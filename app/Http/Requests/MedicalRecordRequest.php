<?php

namespace App\Http\Requests;

use App\Support\DateInput;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use Throwable;

class MedicalRecordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'examined_at' => ['required', 'date', 'before_or_equal:today'],
            'jadwal_terapis' => ['nullable', 'string', 'max:255'],
            'keluhan_utama' => ['required', 'string'],
            'riwayat_penyakit_sekarang' => ['nullable', 'string'],
            'riwayat_penyakit_dahulu' => ['nullable', 'string'],
            'riwayat_penyakit_keluarga' => ['nullable', 'string'],
            'riwayat_penggunaan_obat' => ['nullable', 'string'],
            'riwayat_alergi' => ['nullable', 'string'],
            'inspeksi_statis' => ['nullable', 'string'],
            'inspeksi_dinamis' => ['nullable', 'string'],
            'palpasi' => ['nullable', 'string'],
            'perkusi' => ['nullable', 'string'],
            'auskultasi' => ['nullable', 'string'],
            'mmt' => ['nullable', 'string'],
            'lingkup_gerak_sendi' => ['nullable', 'string'],
            'antropometri' => ['nullable', 'string'],
            'nadi' => ['bail', 'nullable', 'integer', 'min:20', 'max:250'],
            'suhu' => ['bail', 'nullable', 'numeric', 'between:30,45', 'regex:/^\d{2}(\.\d)?$/'],
            'tensi' => ['bail', 'nullable', 'string', 'max:7', 'regex:/^\d{2,3}\/\d{2,3}$/'],
            'frekuensi_nafas' => ['bail', 'nullable', 'integer', 'min:5', 'max:80'],
            'berat_badan' => ['bail', 'nullable', 'numeric', 'between:1,500', 'regex:/^\d{1,3}(\.\d{1,2})?$/'],
            'tinggi_badan' => ['bail', 'nullable', 'numeric', 'between:30,250', 'regex:/^\d{2,3}(\.\d)?$/'],
            'nyeri_diam' => ['nullable', 'integer', 'min:0', 'max:10'],
            'nyeri_tekan' => ['nullable', 'integer', 'min:0', 'max:10'],
            'nyeri_gerak' => ['nullable', 'integer', 'min:0', 'max:10'],
            'faktor_pemberat' => ['nullable', 'string'],
            'deskripsi_nyeri' => ['nullable', 'string'],
            'waktu_onset_nyeri' => ['nullable', 'string', 'max:255'],
            'hasil_penunjang' => ['nullable', 'string'],
            'file_penunjang' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'pemeriksaan_kognitif' => ['nullable', 'string'],
            'pemeriksaan_psikologi' => ['nullable', 'string'],
            'pemeriksaan_khusus_lain' => ['nullable', 'string'],
            'icf_body_structures' => ['nullable', 'string'],
            'icf_body_functions' => ['nullable', 'string'],
            'icf_activities_participation' => ['nullable', 'string'],
            'icf_environmental_factors' => ['nullable', 'string'],
            'diagnosa_impairment' => ['nullable', 'string'],
            'diagnosa_functional_limitation' => ['nullable', 'string'],
            'diagnosa_participation_restriction' => ['nullable', 'string'],
            'rencana_intervensi' => ['nullable', 'array', 'max:4'],
            'rencana_intervensi.*' => ['nullable', 'string', 'max:255'],
            'pediatric_data' => ['nullable', 'array'],
            'pediatric_data.nama_ibu_ayah' => ['nullable', 'string', 'max:255'],
            'pediatric_data.umur_ibu' => ['nullable', 'integer', 'min:0', 'max:120'],
            'pediatric_data.umur_ayah' => ['nullable', 'integer', 'min:0', 'max:120'],
            'pediatric_data.diagnosis_medis' => ['nullable', 'string', 'max:255'],
            'pediatric_data.icd' => ['nullable', 'string', 'max:50'],
            'pediatric_data.riwayat_prenatal' => ['nullable', 'string'],
            'pediatric_data.riwayat_natal' => ['nullable', 'string'],
            'pediatric_data.riwayat_postnatal' => ['nullable', 'string'],
            'pediatric_data.riwayat_nicu_picu' => ['nullable', 'string'],
            'pediatric_data.riwayat_penyerta' => ['nullable', 'string'],
            'pediatric_data.riwayat_imunisasi' => ['nullable', 'string'],
            'pediatric_data.pemeriksaan_gerak_dasar' => ['nullable', 'string'],
            'pediatric_data.lingkar_kepala' => ['bail', 'nullable', 'numeric', 'between:20,80', 'regex:/^\d{1,2}(\.\d)?$/'],
            'pediatric_data.tingkat_kesadaran' => ['nullable', Rule::in([
                'compos_mentis',
                'apatis',
                'somnolen',
                'sopor',
                'sopor_coma',
                'coma',
            ])],
            'pediatric_data.pemeriksaan_khusus' => ['nullable', 'string'],
            'pediatric_data.fisioterapis' => ['nullable', 'string', 'max:255'],
            'interventions' => ['nullable', 'array'],
            'interventions.*.id' => [
                'nullable',
                'integer',
                Rule::exists('interventions', 'id')->where(
                    'medical_record_id',
                    $this->route('record')?->id ?? 0
                ),
            ],
            'interventions.*.tgl' => ['nullable', 'date', 'after_or_equal:examined_at'],
            'interventions.*.intervensi' => ['nullable', 'string'],
            'interventions.*.keluhan' => ['nullable', 'string'],
            'interventions.*.hasil_evaluasi' => ['nullable', 'string'],
            'interventions.*.paraf' => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $interventions = collect($this->input('interventions', []))
            ->map(function ($row) {
                if (! is_array($row)) {
                    return $row;
                }

                $row['tgl'] = DateInput::normalize($row['tgl'] ?? null);

                return $row;
            })
            ->all();

        $this->merge([
            'examined_at' => DateInput::normalize($this->input('examined_at')),
            'interventions' => $interventions,
        ]);
    }

    public function attributes(): array
    {
        return [
            'examined_at' => 'tanggal pemeriksaan',
            'jadwal_terapis' => 'jadwal terapis',
            'keluhan_utama' => 'keluhan utama',
            'nadi' => 'nadi',
            'suhu' => 'suhu',
            'tensi' => 'tensi',
            'frekuensi_nafas' => 'frekuensi nafas',
            'berat_badan' => 'berat badan',
            'tinggi_badan' => 'tinggi badan',
            'file_penunjang' => 'file penunjang',
            'rencana_intervensi' => 'rencana intervensi',
            'pediatric_data.nama_ibu_ayah' => 'nama ibu/ayah',
            'pediatric_data.umur_ibu' => 'umur ibu',
            'pediatric_data.umur_ayah' => 'umur ayah',
            'pediatric_data.diagnosis_medis' => 'diagnosis medis',
            'pediatric_data.icd' => 'ICD',
            'pediatric_data.lingkar_kepala' => 'lingkar kepala',
            'pediatric_data.tingkat_kesadaran' => 'tingkat kesadaran',
            'interventions.*.tgl' => 'tanggal intervensi',
            'interventions.*.keluhan' => 'keluhan intervensi',
        ];
    }

    public function messages(): array
    {
        return [
            'suhu.regex' => 'Suhu maksimal menggunakan satu angka desimal, contoh 36.5.',
            'tensi.regex' => 'Tensi harus menggunakan format sistolik/diastolik, contoh 120/80.',
            'berat_badan.regex' => 'Berat badan maksimal menggunakan dua angka desimal, contoh 65.25.',
            'tinggi_badan.regex' => 'Tinggi badan maksimal menggunakan satu angka desimal, contoh 170.5.',
            'pediatric_data.lingkar_kepala.regex' => 'Lingkar kepala maksimal menggunakan satu angka desimal, contoh 48.5.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $this->validateExaminedAtNotBeforeBirth($validator);
            $this->validateBloodPressure($validator);
        });
    }

    protected function validateExaminedAtNotBeforeBirth(Validator $validator): void
    {
        $birthDate = $this->patientBirthDate();

        if (! $birthDate || ! $this->filled('examined_at')) {
            return;
        }

        try {
            $examinedAt = CarbonImmutable::parse($this->input('examined_at'))->startOfDay();
            $birthDate = CarbonImmutable::parse($birthDate)->startOfDay();
        } catch (Throwable) {
            return;
        }

        if ($examinedAt->lt($birthDate)) {
            $validator->errors()->add(
                'examined_at',
                'Tanggal pemeriksaan tidak boleh lebih awal dari tanggal lahir pasien.'
            );
        }
    }

    protected function validateBloodPressure(Validator $validator): void
    {
        $value = $this->input('tensi');

        if (! is_string($value) || ! preg_match('/^(\d{2,3})\/(\d{2,3})$/', $value, $matches)) {
            return;
        }

        $systolic = (int) $matches[1];
        $diastolic = (int) $matches[2];

        if ($systolic < 50 || $systolic > 300 || $diastolic < 30 || $diastolic > 200) {
            $validator->errors()->add('tensi', 'Nilai tensi berada di luar rentang medis yang diperbolehkan.');

            return;
        }

        if ($systolic <= $diastolic) {
            $validator->errors()->add('tensi', 'Nilai sistolik harus lebih besar dari diastolik.');
        }
    }

    protected function patientBirthDate(): ?string
    {
        if ($patient = $this->route('patient')) {
            return $patient->tanggal_lahir?->toDateString();
        }

        if ($record = $this->route('record')) {
            return $record->patient?->tanggal_lahir?->toDateString();
        }

        return null;
    }
}
