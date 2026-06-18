<?php

namespace App\Services\Pdf;

use App\Contracts\MedicalRecordPdfExporter;
use Barryvdh\DomPDF\Facade\Pdf;

class MedicalRecordPdfExporterFactory
{
    public function make(): MedicalRecordPdfExporter
    {
        if (class_exists(Pdf::class)) {
            return app(DomPdfMedicalRecordExporter::class);
        }

        return app(SimplePdfMedicalRecordExporter::class);
    }
}
