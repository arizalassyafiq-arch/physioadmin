<?php

namespace App\Actions\Interventions;

use App\Models\Intervention;
use App\Services\AuditLogger;
use App\Services\MedicalFileStorage;
use Illuminate\Http\Request;
use Throwable;

class UpdateInterventionAction
{
    public function __construct(
        protected MedicalFileStorage $files,
        protected AuditLogger $auditLogger,
    ) {}

    public function execute(Request $request, Intervention $intervention, array $payload): Intervention
    {
        $storedPath = null;
        $replacedPath = null;

        try {
            if ($request->hasFile('paraf')) {
                $storedPath = $this->files->storeSignature($request->file('paraf'));
                $payload['paraf'] = $storedPath;
                $replacedPath = $intervention->paraf;
            }

            $intervention->update($payload);
        } catch (Throwable $exception) {
            $this->files->delete($storedPath);

            throw $exception;
        }

        $this->files->delete($replacedPath);
        $this->auditLogger->record('intervention.updated', $intervention);

        return $intervention->refresh();
    }
}
