<?php

namespace App\Actions\Patients;

use App\Models\Patient;
use App\Services\AuditLogger;
use App\Support\AgeCalculator;

class UpdatePatientAction
{
    public function __construct(
        protected AgeCalculator $ageCalculator,
        protected AuditLogger $auditLogger,
    ) {}

    public function execute(Patient $patient, array $payload): Patient
    {
        $payload['umur'] = $this->ageCalculator->yearsAt($payload['tanggal_lahir']);

        $patient->update($payload);

        $this->auditLogger->record('patient.updated', $patient);

        return $patient;
    }
}
