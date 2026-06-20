<?php

namespace Tests\Feature;

use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PatientSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_search_patients_by_address_and_job(): void
    {
        $admin = User::factory()->create();

        Patient::create([
            'nama' => 'Ari Bandung',
            'no_rm' => 'RM-SEARCH-1',
            'jenis_kelamin' => 'L',
            'tanggal_lahir' => '1990-01-01',
            'umur' => 36,
            'pekerjaan' => 'Arsitek',
            'alamat' => 'Cicendo Bandung',
        ]);
        Patient::create([
            'nama' => 'Bima Jakarta',
            'no_rm' => 'RM-SEARCH-2',
            'jenis_kelamin' => 'L',
            'tanggal_lahir' => '1988-01-01',
            'umur' => 38,
            'pekerjaan' => 'Guru',
            'alamat' => 'Kemang Jakarta',
        ]);

        $this->actingAs($admin)
            ->withoutVite()
            ->get(route('patients.index', ['search' => 'cicendo']))
            ->assertOk()
            ->assertSee('Ari Bandung')
            ->assertDontSee('Bima Jakarta');

        $this->actingAs($admin)
            ->withoutVite()
            ->get(route('patients.index', ['search' => 'guru']))
            ->assertOk()
            ->assertSee('Bima Jakarta')
            ->assertDontSee('Ari Bandung');
    }

    public function test_admin_can_filter_patients_by_gender_age_and_visit_month_year(): void
    {
        $admin = User::factory()->create();

        $matchedPatient = Patient::create([
            'nama' => 'Dewi Cocok',
            'no_rm' => 'RM-FILTER-1',
            'jenis_kelamin' => 'P',
            'tanggal_lahir' => '1994-01-01',
            'umur' => 32,
            'pekerjaan' => 'Pegawai',
            'alamat' => 'Depok',
        ]);
        MedicalRecord::create([
            'patient_id' => $matchedPatient->id,
            'examined_at' => '2026-02-12',
            'patient_age_at_visit' => 32,
            'keluhan_utama' => 'Nyeri bahu',
        ]);
        MedicalRecord::create([
            'patient_id' => $matchedPatient->id,
            'examined_at' => '2026-03-15',
            'patient_age_at_visit' => 32,
            'keluhan_utama' => 'Kontrol',
        ]);

        $wrongGenderPatient = Patient::create([
            'nama' => 'Doni Salah Gender',
            'no_rm' => 'RM-FILTER-2',
            'jenis_kelamin' => 'L',
            'tanggal_lahir' => '1993-01-01',
            'umur' => 33,
            'pekerjaan' => 'Pegawai',
            'alamat' => 'Depok',
        ]);
        MedicalRecord::create([
            'patient_id' => $wrongGenderPatient->id,
            'examined_at' => '2026-03-10',
            'patient_age_at_visit' => 33,
            'keluhan_utama' => 'Nyeri lutut',
        ]);

        $wrongAgePatient = Patient::create([
            'nama' => 'Rani Salah Umur',
            'no_rm' => 'RM-FILTER-3',
            'jenis_kelamin' => 'P',
            'tanggal_lahir' => '1976-01-01',
            'umur' => 50,
            'pekerjaan' => 'Pegawai',
            'alamat' => 'Depok',
        ]);
        MedicalRecord::create([
            'patient_id' => $wrongAgePatient->id,
            'examined_at' => '2026-03-12',
            'patient_age_at_visit' => 50,
            'keluhan_utama' => 'Nyeri pinggang',
        ]);

        $wrongVisitPatient = Patient::create([
            'nama' => 'Sari Salah Kunjungan',
            'no_rm' => 'RM-FILTER-4',
            'jenis_kelamin' => 'P',
            'tanggal_lahir' => '1995-01-01',
            'umur' => 31,
            'pekerjaan' => 'Pegawai',
            'alamat' => 'Depok',
        ]);
        MedicalRecord::create([
            'patient_id' => $wrongVisitPatient->id,
            'examined_at' => '2026-01-20',
            'patient_age_at_visit' => 31,
            'keluhan_utama' => 'Nyeri tangan',
        ]);

        $this->actingAs($admin)
            ->withoutVite()
            ->get(route('patients.index', [
                'search' => 'depok',
                'jenis_kelamin' => 'P',
                'min_age' => 30,
                'max_age' => 40,
                'visit_month' => 3,
                'visit_year' => 2026,
            ]))
            ->assertOk()
            ->assertSee('Dewi Cocok')
            ->assertDontSee('Doni Salah Gender')
            ->assertDontSee('Rani Salah Umur')
            ->assertDontSee('Sari Salah Kunjungan');
    }

    public function test_admin_can_bulk_archive_patients_by_filtered_visit_month_year(): void
    {
        $admin = User::factory()->create();

        $firstJunePatient = Patient::create([
            'nama' => 'Juni Satu',
            'no_rm' => 'RM-BULK-1',
            'jenis_kelamin' => 'L',
            'tanggal_lahir' => '1990-01-01',
            'umur' => 36,
            'pekerjaan' => 'Guru',
            'alamat' => 'Bandung',
        ]);
        MedicalRecord::create([
            'patient_id' => $firstJunePatient->id,
            'examined_at' => '2026-06-01',
            'patient_age_at_visit' => 36,
            'keluhan_utama' => 'Kontrol Juni',
        ]);

        $secondJunePatient = Patient::create([
            'nama' => 'Juni Dua',
            'no_rm' => 'RM-BULK-2',
            'jenis_kelamin' => 'P',
            'tanggal_lahir' => '1992-01-01',
            'umur' => 34,
            'pekerjaan' => 'Pegawai',
            'alamat' => 'Jakarta',
        ]);
        MedicalRecord::create([
            'patient_id' => $secondJunePatient->id,
            'examined_at' => '2026-06-20',
            'patient_age_at_visit' => 34,
            'keluhan_utama' => 'Terapi Juni',
        ]);

        $mayPatient = Patient::create([
            'nama' => 'Mei Aman',
            'no_rm' => 'RM-BULK-3',
            'jenis_kelamin' => 'L',
            'tanggal_lahir' => '1991-01-01',
            'umur' => 35,
            'pekerjaan' => 'Dosen',
            'alamat' => 'Depok',
        ]);
        MedicalRecord::create([
            'patient_id' => $mayPatient->id,
            'examined_at' => '2026-05-20',
            'patient_age_at_visit' => 35,
            'keluhan_utama' => 'Kontrol Mei',
        ]);

        $this->actingAs($admin)
            ->withoutVite()
            ->delete(route('patients.bulk-destroy.visit-period'), [
                'visit_month' => 6,
                'visit_year' => 2026,
            ])
            ->assertRedirect(route('patients.index'))
            ->assertSessionHas('success', '2 pasien pada periode kunjungan terpilih berhasil diarsipkan.');

        $this->assertSoftDeleted('patients', ['id' => $firstJunePatient->id]);
        $this->assertSoftDeleted('patients', ['id' => $secondJunePatient->id]);
        $this->assertSoftDeleted('medical_records', ['patient_id' => $firstJunePatient->id]);
        $this->assertSoftDeleted('medical_records', ['patient_id' => $secondJunePatient->id]);
        $this->assertDatabaseHas('patients', [
            'id' => $mayPatient->id,
            'deleted_at' => null,
        ]);
        $this->assertDatabaseHas('medical_records', [
            'patient_id' => $mayPatient->id,
            'deleted_at' => null,
        ]);
    }

    public function test_bulk_archive_requires_visit_month_and_year(): void
    {
        $admin = User::factory()->create();
        $patient = Patient::create([
            'nama' => 'Tidak Terhapus',
            'no_rm' => 'RM-BULK-4',
            'jenis_kelamin' => 'L',
            'tanggal_lahir' => '1990-01-01',
            'umur' => 36,
            'pekerjaan' => 'Guru',
            'alamat' => 'Bandung',
        ]);

        $this->actingAs($admin)
            ->withoutVite()
            ->from(route('patients.index'))
            ->delete(route('patients.bulk-destroy.visit-period'), [
                'visit_month' => 6,
            ])
            ->assertRedirect(route('patients.index'))
            ->assertSessionHasErrors('visit_year');

        $this->assertDatabaseHas('patients', [
            'id' => $patient->id,
            'deleted_at' => null,
        ]);
    }
}
