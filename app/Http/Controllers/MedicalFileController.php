<?php

namespace App\Http\Controllers;

use App\Models\Intervention;
use App\Models\MedicalRecord;
use App\Services\AuditLogger;
use App\Services\MedicalFileStorage;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MedicalFileController extends Controller
{
    public function supportingDocument(
        MedicalRecord $record,
        MedicalFileStorage $files,
        AuditLogger $auditLogger,
    ): StreamedResponse {
        Gate::authorize('view', $record);

        abort_unless($record->file_penunjang, 404);

        $auditLogger->record('medical_record.file_downloaded', $record);

        return $files->download($record->file_penunjang, basename($record->file_penunjang));
    }

    public function signature(
        Intervention $intervention,
        MedicalFileStorage $files,
        AuditLogger $auditLogger,
    ): StreamedResponse {
        Gate::authorize('view', $intervention);

        abort_unless($intervention->paraf, 404);

        $auditLogger->record('intervention.signature_downloaded', $intervention);

        return $files->download($intervention->paraf, basename($intervention->paraf));
    }
}
