<?php

namespace App\Actions\MedicalRecords;

use App\Http\Requests\MedicalRecordRequest;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Services\AuditLogger;
use App\Services\MedicalFileStorage;
use Illuminate\Support\Facades\DB;
use Throwable;

class CreateMedicalRecordAction
{
    public function __construct(
        protected BuildMedicalRecordPayload $payloadBuilder,
        protected SyncInterventionsAction $syncInterventions,
        protected MedicalFileStorage $files,
        protected AuditLogger $auditLogger,
    ) {}

    public function execute(MedicalRecordRequest $request, Patient $patient): MedicalRecord
    {
        $storedFiles = [];

        try {
            $record = DB::transaction(function () use ($request, $patient, &$storedFiles): MedicalRecord {
                $payload = $this->payloadBuilder->execute($request, $patient);
                $payload['patient_id'] = $patient->id;

                if ($request->hasFile('file_penunjang')) {
                    $payload['file_penunjang'] = $this->files->storeSupportingDocument($request->file('file_penunjang'));
                    $storedFiles[] = $payload['file_penunjang'];
                }

                $record = MedicalRecord::create($payload);
                $replacedFiles = [];
                $this->syncInterventions->execute($request, $record, $storedFiles, $replacedFiles);

                return $record;
            });
        } catch (Throwable $exception) {
            foreach ($storedFiles as $path) {
                $this->files->delete($path);
            }

            throw $exception;
        }

        $this->auditLogger->record('medical_record.created', $record);

        return $record;
    }
}
