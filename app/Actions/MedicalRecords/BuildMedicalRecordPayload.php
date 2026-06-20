<?php

namespace App\Actions\MedicalRecords;

use App\Http\Requests\MedicalRecordRequest;
use App\Models\Patient;

class BuildMedicalRecordPayload
{
    private const PEDIATRIC_FIELDS = [
        'nama_ibu_ayah',
        'umur_ibu',
        'umur_ayah',
        'diagnosis_medis',
        'icd',
        'riwayat_prenatal',
        'riwayat_natal',
        'riwayat_postnatal',
        'riwayat_nicu_picu',
        'riwayat_penyerta',
        'riwayat_imunisasi',
        'pemeriksaan_gerak_dasar',
        'lingkar_kepala',
        'tingkat_kesadaran',
        'pemeriksaan_khusus',
        'fisioterapis',
    ];

    public function execute(MedicalRecordRequest $request, Patient $patient): array
    {
        $payload = $request->safe()->except(['file_penunjang', 'interventions']);
        $payload['patient_age_at_visit'] = $patient->ageAt($payload['examined_at']);
        $payload['rencana_intervensi'] = collect($request->input('rencana_intervensi', []))
            ->map(fn ($value) => trim((string) $value))
            ->filter()
            ->values()
            ->all();
        $payload['pediatric_data'] = $patient->kategori_pasien === 'anak'
            ? $this->cleanPediatricData($request->input('pediatric_data', []))
            : null;

        return $payload;
    }

    /**
     * Keep JSON compact so empty pediatric form fields do not look like saved clinical data.
     */
    private function cleanPediatricData(array $data): ?array
    {
        $cleaned = collect($data)
            ->only(self::PEDIATRIC_FIELDS)
            ->map(fn ($value) => is_string($value) ? trim($value) : $value)
            ->filter(fn ($value) => $value !== null && $value !== '')
            ->all();

        return $cleaned === [] ? null : $cleaned;
    }
}
