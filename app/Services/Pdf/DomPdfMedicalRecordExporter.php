<?php

namespace App\Services\Pdf;

use App\Contracts\MedicalRecordPdfExporter;
use App\Models\MedicalRecord;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class DomPdfMedicalRecordExporter implements MedicalRecordPdfExporter
{
    public function download(MedicalRecord $record): Response
    {
        return Pdf::loadView('pages.records.pdf', compact('record'))
            ->download($this->filename($record));
    }

    protected function filename(MedicalRecord $record): string
    {
        return "rekam-medis-{$record->patient->no_rm}-{$record->id}.pdf";
    }
}
