<?php

namespace App\Services\Pdf;

use App\Contracts\MedicalRecordPdfExporter;
use App\Models\MedicalRecord;
use App\Support\SimplePdf;
use Illuminate\Http\Response;

class SimplePdfMedicalRecordExporter implements MedicalRecordPdfExporter
{
    public function download(MedicalRecord $record): Response
    {
        return SimplePdf::loadView('pages.records.pdf', compact('record'))
            ->download($this->filename($record));
    }

    protected function filename(MedicalRecord $record): string
    {
        return "rekam-medis-{$record->patient->no_rm}-{$record->id}.pdf";
    }
}
