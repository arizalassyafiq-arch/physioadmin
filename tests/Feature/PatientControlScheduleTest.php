<?php

namespace Tests\Feature;

use App\Models\Patient;
use App\Models\PatientControlSchedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class PatientControlScheduleTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_and_create_patient_control_schedule(): void
    {
        $admin = User::factory()->create();
        $patient = Patient::create([
            'nama' => 'Pasien Kontrol',
            'no_rm' => 'RM-KTRL-001',
            'kategori_pasien' => 'dewasa',
            'jenis_kelamin' => 'L',
            'tanggal_lahir' => '1990-01-01',
            'umur' => 36,
            'pekerjaan' => 'Karyawan',
            'alamat' => 'Jakarta',
        ]);

        $this->actingAs($admin)
            ->withoutVite()
            ->get(route('schedule'))
            ->assertOk()
            ->assertSee('Jadwal Kontrol Pasien')
            ->assertSee('Tambah Jadwal Kontrol');

        $this->actingAs($admin)
            ->withoutVite()
            ->post(route('schedule.store'), [
                'patient_id' => $patient->id,
                'control_number' => 2,
                'scheduled_date' => '06/25/2026',
                'notes' => 'Evaluasi nyeri',
            ])
            ->assertRedirect(route('schedule'));

        $schedule = PatientControlSchedule::query()->firstOrFail();

        $this->assertSame($patient->id, $schedule->patient_id);
        $this->assertSame(2, $schedule->control_number);
        $this->assertSame('2026-06-25', $schedule->scheduled_date->toDateString());
        $this->assertSame('scheduled', $schedule->status);
        $this->assertSame('Evaluasi nyeri', $schedule->notes);
    }

    public function test_admin_can_complete_and_delete_patient_control_schedule(): void
    {
        $admin = User::factory()->create();
        $patient = Patient::create([
            'nama' => 'Pasien Lanjutan',
            'no_rm' => 'RM-KTRL-002',
            'kategori_pasien' => 'anak',
            'jenis_kelamin' => 'P',
            'tanggal_lahir' => '2020-01-01',
            'umur' => 6,
            'pekerjaan' => null,
            'alamat' => 'Bandung',
        ]);
        $schedule = PatientControlSchedule::create([
            'patient_id' => $patient->id,
            'control_number' => 3,
            'scheduled_date' => '2026-06-30',
            'status' => 'scheduled',
        ]);

        $this->actingAs($admin)
            ->withoutVite()
            ->patch(route('schedule.complete', $schedule))
            ->assertRedirect(route('schedule'));

        $this->assertDatabaseHas('patient_control_schedules', [
            'id' => $schedule->id,
            'status' => 'completed',
        ]);

        $this->actingAs($admin)
            ->withoutVite()
            ->delete(route('schedule.destroy', $schedule))
            ->assertRedirect(route('schedule'));

        $this->assertSoftDeleted('patient_control_schedules', [
            'id' => $schedule->id,
        ]);
    }

    public function test_admin_can_filter_patient_control_schedules(): void
    {
        Carbon::setTestNow('2026-06-18 10:00:00');

        $admin = User::factory()->create();
        $targetPatient = Patient::create([
            'nama' => 'Budi Kontrol',
            'no_rm' => 'RM-FILTER-001',
            'kategori_pasien' => 'dewasa',
            'jenis_kelamin' => 'L',
            'tanggal_lahir' => '1990-01-01',
            'umur' => 36,
            'pekerjaan' => 'Karyawan',
            'alamat' => 'Jakarta',
        ]);
        $otherPatient = Patient::create([
            'nama' => 'Siti Lain',
            'no_rm' => 'RM-FILTER-002',
            'kategori_pasien' => 'anak',
            'jenis_kelamin' => 'P',
            'tanggal_lahir' => '2020-01-01',
            'umur' => 6,
            'pekerjaan' => null,
            'alamat' => 'Bandung',
        ]);

        PatientControlSchedule::create([
            'patient_id' => $targetPatient->id,
            'control_number' => 2,
            'scheduled_date' => '2026-06-20',
            'status' => 'scheduled',
        ]);
        PatientControlSchedule::create([
            'patient_id' => $otherPatient->id,
            'control_number' => 2,
            'scheduled_date' => '2026-06-10',
            'status' => 'scheduled',
        ]);

        $response = $this->actingAs($admin)
            ->withoutVite()
            ->get(route('schedule', [
                'search' => 'Budi',
                'date_from' => '06/19/2026',
                'date_to' => '06/21/2026',
                'status' => 'upcoming',
            ]));

        $response
            ->assertOk()
            ->assertSee('Budi Kontrol');

        $schedules = $response->viewData('schedules');

        $this->assertCount(1, $schedules->items());
        $this->assertSame($targetPatient->id, $schedules->first()->patient_id);

        Carbon::setTestNow();
    }

    public function test_admin_can_edit_patient_control_schedule(): void
    {
        $admin = User::factory()->create();
        $patient = Patient::create([
            'nama' => 'Pasien Reschedule',
            'no_rm' => 'RM-EDIT-001',
            'kategori_pasien' => 'dewasa',
            'jenis_kelamin' => 'L',
            'tanggal_lahir' => '1990-01-01',
            'umur' => 36,
            'pekerjaan' => 'Karyawan',
            'alamat' => 'Jakarta',
        ]);
        $schedule = PatientControlSchedule::create([
            'patient_id' => $patient->id,
            'control_number' => 2,
            'scheduled_date' => '2026-06-20',
            'status' => 'scheduled',
            'notes' => 'Awal',
        ]);

        $this->actingAs($admin)
            ->withoutVite()
            ->get(route('schedule.edit', $schedule))
            ->assertOk()
            ->assertSee('Edit Jadwal Kontrol')
            ->assertSee('Pasien Reschedule');

        $this->actingAs($admin)
            ->withoutVite()
            ->put(route('schedule.update', $schedule), [
                'patient_id' => $patient->id,
                'control_number' => 3,
                'scheduled_date' => '06/27/2026',
                'status' => 'completed',
                'notes' => 'Reschedule selesai',
            ])
            ->assertRedirect(route('schedule'));

        $schedule->refresh();

        $this->assertSame(3, $schedule->control_number);
        $this->assertSame('2026-06-27', $schedule->scheduled_date->toDateString());
        $this->assertSame('completed', $schedule->status);
        $this->assertSame('Reschedule selesai', $schedule->notes);
    }
}
