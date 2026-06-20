<?php

namespace Tests\Feature;

use App\Models\Intervention;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MedicalRecordWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_patient_is_persisted_when_identity_is_saved_before_medical_record(): void
    {
        $admin = User::factory()->create();

        $this->actingAs($admin)
            ->withoutVite()
            ->post(route('patients.store'), [
                'nama' => 'Joko Widodo',
                'no_rm' => 'RM-NEW',
                'kategori_pasien' => 'dewasa',
                'jenis_kelamin' => 'L',
                'tanggal_lahir' => '11/12/1959',
                'pekerjaan' => 'Wiraswasta',
                'alamat' => 'Solo',
            ])
            ->assertRedirect();

        $patient = Patient::query()->where('no_rm', 'RM-NEW')->firstOrFail();

        $this->assertSame('dewasa', $patient->kategori_pasien);
        $this->assertSame(66, $patient->umur);
        $this->assertDatabaseCount('medical_records', 0);

        $this->actingAs($admin)
            ->withoutVite()
            ->post(route('records.store', $patient), [
                'examined_at' => '06/11/2026',
                'keluhan_utama' => 'Nyeri bahu',
                'rencana_intervensi' => ['Latihan ROM'],
            ])
            ->assertRedirect(route('records.show', MedicalRecord::query()->firstOrFail()));

        $this->assertDatabaseHas('patients', [
            'no_rm' => 'RM-NEW',
            'kategori_pasien' => 'dewasa',
            'tanggal_lahir' => '1959-11-12 00:00:00',
        ]);
        $this->assertDatabaseHas('medical_records', [
            'examined_at' => '2026-06-11 00:00:00',
            'keluhan_utama' => 'Nyeri bahu',
        ]);
    }

    public function test_patient_create_form_uses_relative_action_for_csp_self(): void
    {
        $admin = User::factory()->create();

        $this->actingAs($admin)
            ->withoutVite()
            ->get(route('patients.create'))
            ->assertOk()
            ->assertSee('action="/patients"', false)
            ->assertDontSee('action="http', false);
    }

    public function test_admin_can_create_patients_with_same_name_and_medical_record_number(): void
    {
        $admin = User::factory()->create();

        foreach (['1990-01-01', '1992-02-02'] as $birthDate) {
            $this->actingAs($admin)
                ->withoutVite()
                ->post(route('patients.store'), [
                    'nama' => 'Pasien Sama',
                    'no_rm' => 'RM-DUPLICATE',
                    'kategori_pasien' => 'dewasa',
                    'jenis_kelamin' => 'L',
                    'tanggal_lahir' => $birthDate,
                    'pekerjaan' => 'Karyawan',
                    'alamat' => 'Jakarta',
                ])
                ->assertRedirect();
        }

        $this->assertSame(2, Patient::query()
            ->where('nama', 'Pasien Sama')
            ->where('no_rm', 'RM-DUPLICATE')
            ->count());
    }

    public function test_patient_detail_shows_visit_summary_and_sequential_interventions(): void
    {
        Carbon::setTestNow('2026-06-18 10:00:00');

        $admin = User::factory()->create();
        $patient = Patient::create([
            'nama' => 'Pasien Riwayat',
            'no_rm' => 'RM-HISTORY-001',
            'kategori_pasien' => 'dewasa',
            'jenis_kelamin' => 'L',
            'tanggal_lahir' => '1990-01-01',
            'umur' => 36,
            'pekerjaan' => 'Guru',
            'alamat' => 'Bandung',
        ]);

        MedicalRecord::create([
            'patient_id' => $patient->id,
            'examined_at' => '2026-06-28',
            'patient_age_at_visit' => 36,
            'keluhan_utama' => 'paha',
        ]);
        MedicalRecord::create([
            'patient_id' => $patient->id,
            'examined_at' => '2026-06-18',
            'patient_age_at_visit' => 36,
            'keluhan_utama' => 'sasasa',
        ]);
        MedicalRecord::create([
            'patient_id' => $patient->id,
            'examined_at' => '2025-12-20',
            'patient_age_at_visit' => 35,
            'keluhan_utama' => 'tahun lalu',
        ]);

        $response = $this->actingAs($admin)
            ->withoutVite()
            ->get(route('patients.show', $patient))
            ->assertOk()
            ->assertSee('Kunjungan Tahun Ini')
            ->assertSee('2 kali')
            ->assertSee('Tanggal Kedatangan Pertama')
            ->assertSee('20 Des 2025')
            ->assertSee('Intervensi 1')
            ->assertSee('Intervensi 2')
            ->assertSee('Intervensi 3');

        $response->assertSeeInOrder([
            '20 Des 2025',
            'tahun lalu',
            'Intervensi 1',
            '18 Jun 2026',
            'sasasa',
            'Intervensi 2',
            '28 Jun 2026',
            'paha',
            'Intervensi 3',
        ]);

        Carbon::setTestNow();
    }

    public function test_admin_can_create_download_export_and_archive_medical_record(): void
    {
        Storage::fake('medical');
        Storage::fake('public');

        $admin = User::factory()->create();
        $patient = Patient::create([
            'nama' => 'Budi Santoso',
            'no_rm' => 'RM-001',
            'jenis_kelamin' => 'L',
            'tanggal_lahir' => '2000-06-01',
            'umur' => 25,
            'pekerjaan' => 'Guru',
            'alamat' => 'Jakarta',
        ]);

        $response = $this->actingAs($admin)
            ->withoutVite()
            ->post(route('records.store', $patient), [
                'examined_at' => '2026-01-15',
                'jadwal_terapis' => 'Senin 14.00',
                'keluhan_utama' => 'Nyeri punggung bawah',
                'rencana_intervensi' => ['Latihan stabilisasi'],
                'file_penunjang' => UploadedFile::fake()->create('hasil-lab.pdf', 64, 'application/pdf'),
                'interventions' => [
                    [
                        'tgl' => '01/15/2026',
                        'intervensi' => 'Latihan core ringan',
                        'keluhan' => 'Nyeri saat fleksi',
                        'hasil_evaluasi' => 'Nyeri menurun',
                        'paraf' => UploadedFile::fake()->image('paraf.png'),
                    ],
                ],
            ]);

        $record = MedicalRecord::query()->firstOrFail();
        $intervention = Intervention::query()->firstOrFail();

        $response->assertRedirect(route('records.show', $record));
        $this->assertSame(25, $record->patient_age_at_visit);
        $this->assertSame('Senin 14.00', $record->jadwal_terapis);
        $this->assertSame('Nyeri saat fleksi', $intervention->keluhan);
        Storage::disk('medical')->assertExists($record->file_penunjang);
        Storage::disk('medical')->assertExists($intervention->paraf);
        Storage::disk('public')->assertMissing($record->file_penunjang);

        $this->actingAs($admin)
            ->withoutVite()
            ->get(route('records.file', $record))
            ->assertOk();

        $this->actingAs($admin)
            ->withoutVite()
            ->get(route('records.pdf', $record))
            ->assertOk();

        $this->actingAs($admin)
            ->withoutVite()
            ->delete(route('records.destroy', $record))
            ->assertRedirect(route('patients.show', $patient));

        $this->assertSoftDeleted('medical_records', ['id' => $record->id]);
        $this->assertSoftDeleted('interventions', ['id' => $intervention->id]);
        Storage::disk('medical')->assertExists($record->file_penunjang);
    }

    public function test_admin_can_save_pediatric_medical_record_using_pediatric_blanko_fields(): void
    {
        $admin = User::factory()->create();
        $patient = Patient::create([
            'nama' => 'Alya Pediatri',
            'no_rm' => 'RM-PED-001',
            'kategori_pasien' => 'anak',
            'jenis_kelamin' => 'P',
            'tanggal_lahir' => '2020-04-10',
            'umur' => 6,
            'pekerjaan' => null,
            'alamat' => 'Bandung',
        ]);

        $this->actingAs($admin)
            ->withoutVite()
            ->post(route('records.store', $patient), [
                'examined_at' => '2026-01-10',
                'keluhan_utama' => 'Keterlambatan motorik kasar',
                'nadi' => '96',
                'suhu' => '36.7',
                'tensi' => '100/70',
                'frekuensi_nafas' => '24',
                'berat_badan' => '18.5',
                'tinggi_badan' => '106.5',
                'pediatric_data' => [
                    'nama_ibu_ayah' => 'Ibu Rani',
                    'umur_ibu' => '32',
                    'umur_ayah' => '35',
                    'diagnosis_medis' => 'Developmental delay',
                    'icd' => 'F82',
                    'riwayat_prenatal' => 'Kontrol rutin',
                    'riwayat_natal' => 'Lahir spontan',
                    'riwayat_postnatal' => 'Tidak ada komplikasi',
                    'riwayat_nicu_picu' => 'Tidak pernah',
                    'riwayat_penyerta' => 'Tidak ada',
                    'riwayat_imunisasi' => 'Lengkap',
                    'pemeriksaan_gerak_dasar' => 'Duduk mandiri, berjalan dibantu',
                    'lingkar_kepala' => '49.5',
                    'tingkat_kesadaran' => 'compos_mentis',
                    'pemeriksaan_khusus' => 'GMFM awal',
                    'fisioterapis' => 'Ft. Dini',
                ],
                'rencana_intervensi' => [
                    'Latihan kontrol trunk',
                ],
            ])
            ->assertRedirect();

        $record = MedicalRecord::query()->firstOrFail();

        $this->assertSame('Ibu Rani', $record->pediatric_data['nama_ibu_ayah']);
        $this->assertSame('GMFM awal', $record->pediatric_data['pemeriksaan_khusus']);
        $this->assertSame('compos_mentis', $record->pediatric_data['tingkat_kesadaran']);
        $this->assertSame(['Latihan kontrol trunk'], $record->rencana_intervensi);
    }

    public function test_adult_medical_record_does_not_store_pediatric_payload(): void
    {
        $admin = User::factory()->create();
        $patient = Patient::create([
            'nama' => 'Dewasa Tetap',
            'no_rm' => 'RM-ADULT-001',
            'kategori_pasien' => 'dewasa',
            'jenis_kelamin' => 'L',
            'tanggal_lahir' => '1990-04-10',
            'umur' => 36,
            'pekerjaan' => 'Pegawai',
            'alamat' => 'Jakarta',
        ]);

        $this->actingAs($admin)
            ->withoutVite()
            ->post(route('records.store', $patient), [
                'examined_at' => '2026-01-10',
                'keluhan_utama' => 'Nyeri pinggang',
                'pediatric_data' => [
                    'nama_ibu_ayah' => 'Tidak relevan',
                ],
            ])
            ->assertRedirect();

        $record = MedicalRecord::query()->firstOrFail();

        $this->assertNull($record->pediatric_data);
    }

    public function test_admin_can_remove_all_interventions_when_updating_record(): void
    {
        $admin = User::factory()->create();
        $patient = Patient::create([
            'nama' => 'Siti Aminah',
            'no_rm' => 'RM-002',
            'jenis_kelamin' => 'P',
            'tanggal_lahir' => '1990-02-10',
            'umur' => 36,
            'pekerjaan' => 'Pegawai',
            'alamat' => 'Bandung',
        ]);
        $record = MedicalRecord::create([
            'patient_id' => $patient->id,
            'examined_at' => '2026-01-10',
            'patient_age_at_visit' => 35,
            'keluhan_utama' => 'Nyeri lutut',
        ]);
        $intervention = Intervention::create([
            'medical_record_id' => $record->id,
            'tgl' => '2026-01-10',
            'intervensi' => 'Latihan penguatan',
            'hasil_evaluasi' => 'Membaik',
        ]);

        $this->actingAs($admin)
            ->withoutVite()
            ->put(route('records.update', $record), [
                'examined_at' => '2026-01-10',
                'keluhan_utama' => 'Nyeri lutut berkurang',
                'interventions' => [],
            ])
            ->assertRedirect(route('records.show', $record));

        $this->assertSoftDeleted('interventions', ['id' => $intervention->id]);
    }

    public function test_admin_can_save_future_intervention_schedule_dates(): void
    {
        $admin = User::factory()->create();
        $patient = Patient::create([
            'nama' => 'Pasien Jadwal Intervensi',
            'no_rm' => 'RM-FUTURE-INTERVENTION',
            'kategori_pasien' => 'dewasa',
            'jenis_kelamin' => 'L',
            'tanggal_lahir' => '1990-01-01',
            'umur' => 36,
            'pekerjaan' => 'Karyawan',
            'alamat' => 'Jakarta',
        ]);

        $this->actingAs($admin)
            ->withoutVite()
            ->post(route('records.store', $patient), [
                'examined_at' => '06/18/2026',
                'keluhan_utama' => 'Nyeri pinggul',
                'interventions' => [
                    [
                        'tgl' => '06/25/2026',
                        'intervensi' => 'Latihan lanjutan',
                        'keluhan' => 'Pinggul',
                        'hasil_evaluasi' => 'Jadwal kontrol',
                    ],
                ],
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('interventions', [
            'tgl' => '2026-06-25 00:00:00',
            'intervensi' => 'Latihan lanjutan',
            'keluhan' => 'Pinggul',
        ]);
    }

    public function test_medical_record_date_cannot_be_before_patient_birth_date(): void
    {
        $admin = User::factory()->create();
        $patient = Patient::create([
            'nama' => 'Anak Pasien',
            'no_rm' => 'RM-003',
            'jenis_kelamin' => 'L',
            'tanggal_lahir' => '2020-05-10',
            'umur' => 6,
            'pekerjaan' => null,
            'alamat' => 'Bogor',
        ]);

        $this->actingAs($admin)
            ->withoutVite()
            ->post(route('records.store', $patient), [
                'examined_at' => '2019-05-10',
                'keluhan_utama' => 'Nyeri kaki',
            ])
            ->assertSessionHasErrors('examined_at');

        $this->assertDatabaseMissing('medical_records', ['keluhan_utama' => 'Nyeri kaki']);
    }

    public function test_medical_vital_signs_must_use_valid_ranges_and_formats(): void
    {
        $admin = User::factory()->create();
        $patient = Patient::create([
            'nama' => 'Pasien Vital',
            'no_rm' => 'RM-004',
            'jenis_kelamin' => 'P',
            'tanggal_lahir' => '1992-03-12',
            'umur' => 34,
            'pekerjaan' => 'Karyawan',
            'alamat' => 'Jakarta',
        ]);

        $this->actingAs($admin)
            ->withoutVite()
            ->post(route('records.store', $patient), [
                'examined_at' => '2026-01-10',
                'keluhan_utama' => 'Kontrol nyeri',
                'nadi' => '12',
                'suhu' => '36.55',
                'tensi' => '80/120',
                'frekuensi_nafas' => '100',
                'berat_badan' => '501',
                'tinggi_badan' => '20',
            ])
            ->assertSessionHasErrors([
                'nadi',
                'suhu',
                'tensi',
                'frekuensi_nafas',
                'berat_badan',
                'tinggi_badan',
            ]);

        $this->assertDatabaseMissing('medical_records', ['keluhan_utama' => 'Kontrol nyeri']);
    }

    public function test_admin_can_save_medical_record_with_valid_vital_signs(): void
    {
        $admin = User::factory()->create();
        $patient = Patient::create([
            'nama' => 'Pasien Valid Vital',
            'no_rm' => 'RM-005',
            'jenis_kelamin' => 'L',
            'tanggal_lahir' => '1991-03-12',
            'umur' => 35,
            'pekerjaan' => 'Karyawan',
            'alamat' => 'Jakarta',
        ]);

        $this->actingAs($admin)
            ->withoutVite()
            ->post(route('records.store', $patient), [
                'examined_at' => '2026-01-10',
                'keluhan_utama' => 'Kontrol pasca cedera',
                'nadi' => '80',
                'suhu' => '36.5',
                'tensi' => '120/80',
                'frekuensi_nafas' => '20',
                'berat_badan' => '65.25',
                'tinggi_badan' => '170.5',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('medical_records', [
            'keluhan_utama' => 'Kontrol pasca cedera',
            'nadi' => '80',
            'suhu' => '36.5',
            'tensi' => '120/80',
            'frekuensi_nafas' => '20',
            'berat_badan' => '65.25',
            'tinggi_badan' => '170.5',
        ]);
    }

}
