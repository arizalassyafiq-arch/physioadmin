<?php

namespace App\Actions\Patients;

use App\Models\Patient;
use App\Services\AuditLogger;
use App\Support\AgeCalculator;

class StorePatientAction
{
    public function __construct(
        protected AgeCalculator $ageCalculator,
        protected AuditLogger $auditLogger,
    ) {}

    public function execute(array $payload): Patient
    {
        $payload['umur'] = $this->ageCalculator->yearsAt($payload['tanggal_lahir']);

        $patient = Patient::create($payload);

        $this->auditLogger->record('patient.created', $patient);

        return $patient;
    }
}
