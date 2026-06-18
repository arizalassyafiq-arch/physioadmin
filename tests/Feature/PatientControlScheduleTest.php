<?php

namespace Tests\Feature;

use App\Models\Patient;
use App\Models\PatientControlSchedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
                'scheduled_date' => '2026-06-25',
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
}
