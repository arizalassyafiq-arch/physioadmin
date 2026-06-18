<?php

namespace App\Actions\MedicalRecords;

use App\Http\Requests\MedicalRecordRequest;
use App\Models\MedicalRecord;
use App\Services\AuditLogger;
use App\Services\MedicalFileStorage;
use Illuminate\Support\Facades\DB;
use Throwable;

class UpdateMedicalRecordAction
{
    public function __construct(
        protected BuildMedicalRecordPayload $payloadBuilder,
        protected SyncInterventionsAction $syncInterventions,
        protected MedicalFileStorage $files,
        protected AuditLogger $auditLogger,
    ) {}

    public function execute(MedicalRecordRequest $request, MedicalRecord $record): MedicalRecord
    {
        $storedFiles = [];
        $replacedFiles = [];

        try {
            DB::transaction(function () use ($request, $record, &$storedFiles, &$replacedFiles): void {
                $payload = $this->payloadBuilder->execute($request, $record->patient);

                if ($request->hasFile('file_penunjang')) {
                    $payload['file_penunjang'] = $this->files->storeSupportingDocument($request->file('file_penunjang'));
                    $storedFiles[] = $payload['file_penunjang'];

                    if ($record->file_penunjang) {
                        $replacedFiles[] = $record->file_penunjang;
                    }
                }

                $record->update($payload);
                $this->syncInterventions->execute($request, $record, $storedFiles, $replacedFiles);
            });
        } catch (Throwable $exception) {
            foreach ($storedFiles as $path) {
                $this->files->delete($path);
            }

            throw $exception;
        }

        foreach ($replacedFiles as $path) {
            $this->files->delete($path);
        }

        $this->auditLogger->record('medical_record.updated', $record);

        return $record->refresh();
    }
}
