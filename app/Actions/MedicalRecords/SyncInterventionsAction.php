<?php

namespace App\Actions\MedicalRecords;

use App\Http\Requests\MedicalRecordRequest;
use App\Models\MedicalRecord;
use App\Services\MedicalFileStorage;

class SyncInterventionsAction
{
    public function __construct(
        protected MedicalFileStorage $files,
    ) {}

    public function execute(MedicalRecordRequest $request, MedicalRecord $record, array &$storedFiles, array &$replacedFiles): void
    {
        $rows = collect($request->input('interventions', []));
        $touchedIds = [];

        foreach ($rows as $index => $row) {
            $hasContent = filled($row['tgl'] ?? null)
                || filled($row['intervensi'] ?? null)
                || filled($row['hasil_evaluasi'] ?? null)
                || $request->hasFile("interventions.{$index}.paraf");

            if (! $hasContent) {
                continue;
            }

            $payload = [
                'tgl' => $row['tgl'] ?? now()->toDateString(),
                'intervensi' => $row['intervensi'] ?? '-',
                'hasil_evaluasi' => $row['hasil_evaluasi'] ?? null,
            ];

            $intervention = isset($row['id'])
                ? $record->interventions()->whereKey($row['id'])->first()
                : null;

            $intervention = $intervention
                ? tap($intervention)->update($payload)
                : $record->interventions()->create($payload);

            if ($request->hasFile("interventions.{$index}.paraf")) {
                $newPath = $this->files->storeSignature($request->file("interventions.{$index}.paraf"));
                $storedFiles[] = $newPath;

                if ($intervention->paraf) {
                    $replacedFiles[] = $intervention->paraf;
                }

                $intervention->update(['paraf' => $newPath]);
            }

            $touchedIds[] = $intervention->id;
        }

        if ($record->exists) {
            $record->interventions()
                ->whereNotIn('id', $touchedIds)
                ->delete();
        }
    }
}
