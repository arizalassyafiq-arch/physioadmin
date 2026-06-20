<?php

namespace App\Actions\Patients;

class BulkDeletePatientsByVisitPeriodAction
{
    public function __construct(
        protected DeletePatientAction $deletePatient,
    ) {}

    public function execute($query): int
    {
        $deletedCount = 0;

        $query
            ->select('patients.*')
            ->chunkById(100, function ($patients) use (&$deletedCount): void {
                foreach ($patients as $patient) {
                    $this->deletePatient->execute($patient);
                    $deletedCount++;
                }
            }, 'patients.id', 'id');

        return $deletedCount;
    }
}
