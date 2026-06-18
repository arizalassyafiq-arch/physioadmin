<?php

namespace App\Observers;

use App\Models\Intervention;
use App\Services\MedicalFileStorage;

class InterventionObserver
{
    public function __construct(
        protected MedicalFileStorage $files,
    ) {}

    public function forceDeleted(Intervention $intervention): void
    {
        $this->files->delete($intervention->paraf);
    }
}
