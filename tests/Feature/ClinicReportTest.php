<?php

namespace Tests\Feature;

use App\Models\Intervention;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ClinicReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_monthly_clinic_report(): void
    {
        Carbon::setTestNow('2026-06-16 10:00:00');

        $admin = User::factory()->create();
        $junePatient = $this->patient('RM-JUN', 'Pasien Juni', '2026-06-02 09:00:00');
        $mayPatient = $this->patient('RM-MAY', 'Pasien Mei', '2026-05-20 09:00:00');

        $juneRecord = MedicalRecord::create([
            'patient_id' => $junePatient->id,
            'examined_at' => '2026-06-12',
            'patient_age_at_visit' => 30,
            'keluhan_utama' => 'Nyeri bahu',
        ]);
        MedicalRecord::create([
            'patient_id' => $mayPatient->id,
            'examined_at' => '2026-05-21',
            'patient_age_at_visit' => 30,
            'keluhan_utama' => 'Nyeri lutut',
        ]);
        Intervention::create([
            'medical_record_id' => $juneRecord->id,
            'tgl' => '2026-06-12',
            'intervensi' => 'Latihan ROM',
        ]);

        $this->actingAs($admin)
            ->withoutVite()
            ->get(route('reports', ['month' => '2026-06']))
            ->assertOk()
            ->assertSee('Laporan Bulanan Klinik')
            ->assertSee('Juni 2026')
            ->assertSee('Total Pasien')
            ->assertSee('Pasien Baru Bulan Ini')
            ->assertSee('Rekam Medis Bulan Ini')
            ->assertSee('Intervensi Bulan Ini')
            ->assertSee('Nyeri bahu')
            ->assertDontSee('Nyeri lutut');

        Carbon::setTestNow();
    }

    public function test_admin_can_export_monthly_clinic_report_pdf(): void
    {
        $admin = User::factory()->create();

        $this->actingAs($admin)
            ->withoutVite()
            ->get(route('reports.pdf', ['month' => '2026-06']))
            ->assertOk()
            ->assertHeader('content-disposition', 'attachment; filename=laporan-klinik-2026-06.pdf');
    }

    protected function patient(string $medicalNumber, string $name, string $createdAt): Patient
    {
        $patient = Patient::create([
            'nama' => $name,
            'no_rm' => $medicalNumber,
            'jenis_kelamin' => 'L',
            'tanggal_lahir' => '1996-01-01',
            'umur' => 30,
            'pekerjaan' => 'Karyawan',
            'alamat' => 'Jakarta',
        ]);
        $patient->forceFill([
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ])->save();

        return $patient;
    }
}
