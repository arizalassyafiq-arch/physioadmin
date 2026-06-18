<?php

namespace App\Observers;

use App\Models\Patient;

class PatientObserver
{
    public function forceDeleted(Patient $patient): void
    {
        $patient->controlSchedules()->withTrashed()->get()->each->forceDelete();
        $patient->medicalRecords()->withTrashed()->get()->each->forceDelete();
    }
}
