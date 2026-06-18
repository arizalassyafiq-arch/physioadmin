<?php

namespace App\Actions\Patients;

use App\Models\Patient;
use App\Services\AuditLogger;
use Illuminate\Support\Facades\DB;

class DeletePatientAction
{
    public function __construct(
        protected AuditLogger $auditLogger,
    ) {}

    public function execute(Patient $patient): void
    {
        DB::transaction(function () use ($patient): void {
            $patient->load('medicalRecords.interventions');

            $patient->controlSchedules()->delete();

            foreach ($patient->medicalRecords as $record) {
                $record->interventions()->delete();
                $record->delete();
            }

            $patient->delete();

            $this->auditLogger->record('patient.deleted', $patient);
        });
    }
}
