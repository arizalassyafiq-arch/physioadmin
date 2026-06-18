<?php

namespace App\Actions\Interventions;

use App\Models\Intervention;
use App\Models\MedicalRecord;
use App\Services\AuditLogger;
use App\Services\MedicalFileStorage;
use Illuminate\Http\Request;
use Throwable;

class StoreInterventionAction
{
    public function __construct(
        protected MedicalFileStorage $files,
        protected AuditLogger $auditLogger,
    ) {}

    public function execute(Request $request, MedicalRecord $record, array $payload): Intervention
    {
        $storedPath = null;

        try {
            if ($request->hasFile('paraf')) {
                $storedPath = $this->files->storeSignature($request->file('paraf'));
                $payload['paraf'] = $storedPath;
            }

            $intervention = $record->interventions()->create($payload);
        } catch (Throwable $exception) {
            $this->files->delete($storedPath);

            throw $exception;
        }

        $this->auditLogger->record('intervention.created', $intervention);

        return $intervention;
    }
}
