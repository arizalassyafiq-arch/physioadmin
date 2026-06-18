<?php

namespace App\Actions\MedicalRecords;

use App\Http\Requests\MedicalRecordRequest;
use App\Models\Patient;

class BuildMedicalRecordPayload
{
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
            ->map(fn ($value) => is_string($value) ? trim($value) : $value)
            ->filter(fn ($value) => $value !== null && $value !== '')
            ->all();

        return $cleaned === [] ? null : $cleaned;
    }
}
