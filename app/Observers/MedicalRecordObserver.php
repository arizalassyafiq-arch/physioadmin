<?php

namespace App\Observers;

use App\Models\MedicalRecord;
use App\Services\MedicalFileStorage;

class MedicalRecordObserver
{
    public function __construct(
        protected MedicalFileStorage $files,
    ) {}

    public function forceDeleted(MedicalRecord $record): void
    {
        $record->interventions()->withTrashed()->get()->each->forceDelete();
        $this->files->delete($record->file_penunjang);
    }
}
