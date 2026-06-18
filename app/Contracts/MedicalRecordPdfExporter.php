<?php

namespace App\Contracts;

use App\Models\MedicalRecord;
use Illuminate\Http\Response;

interface MedicalRecordPdfExporter
{
    public function download(MedicalRecord $record): Response;
}
