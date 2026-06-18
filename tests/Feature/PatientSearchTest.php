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

    public function test_admin_can_filter_patients_by_gender_age_and_latest_visit_date(): void
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
                'latest_visit_from' => '2026-03-01',
                'latest_visit_to' => '2026-03-31',
            ]))
            ->assertOk()
            ->assertSee('Dewi Cocok')
            ->assertDontSee('Doni Salah Gender')
            ->assertDontSee('Rani Salah Umur')
            ->assertDontSee('Sari Salah Kunjungan');
    }
}
