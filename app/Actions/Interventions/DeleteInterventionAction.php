<?php

namespace App\Actions\Interventions;

use App\Models\Intervention;
use App\Services\AuditLogger;

class DeleteInterventionAction
{
    public function __construct(
        protected AuditLogger $auditLogger,
    ) {}

    public function execute(Intervention $intervention): void
    {
        $intervention->delete();

        $this->auditLogger->record('intervention.deleted', $intervention);
    }
}
