<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MedicalFileStorage
{
    public function storeSupportingDocument(UploadedFile $file): string
    {
        return $file->store('penunjang', 'medical');
    }

    public function storeSignature(UploadedFile $file): string
    {
        return $file->store('signatures', 'medical');
    }

    public function delete(?string $path): void
    {
        if (! $path) {
            return;
        }

        Storage::disk('medical')->delete($path);
        Storage::disk('public')->delete($path);
    }

    public function download(string $path, string $filename): StreamedResponse
    {
        $disk = Storage::disk('medical')->exists($path) ? 'medical' : 'public';

        return Storage::disk($disk)->download($path, $filename);
    }
}
