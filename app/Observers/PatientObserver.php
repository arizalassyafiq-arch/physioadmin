<?php

namespace App\Observers;

use App\Models\Patient;

class PatientObserver
{
    public function forceDeleted(Patient $patient): void
    {
        $patient->medicalRecords()->withTrashed()->get()->each->forceDelete();
    }
}
