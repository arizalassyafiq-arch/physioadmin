<?php

namespace App\Actions\MedicalRecords;

use App\Models\MedicalRecord;
use App\Services\AuditLogger;
use Illuminate\Support\Facades\DB;

class DeleteMedicalRecordAction
{
    public function __construct(
        protected AuditLogger $auditLogger,
    ) {}

    public function execute(MedicalRecord $record): void
    {
        DB::transaction(function () use ($record): void {
            $record->interventions()->delete();
            $record->delete();

            $this->auditLogger->record('medical_record.deleted', $record);
        });
    }
}
