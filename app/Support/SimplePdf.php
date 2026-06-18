<?php

namespace App\Support;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class SimplePdf implements Responsable
{
    public function __construct(
        protected string $html,
        protected string $filename = 'document.pdf',
    ) {}

    public static function loadView(string $view, array $data = []): self
    {
        return new self(View::make($view, $data)->render());
    }

    public function download(string $filename): Response
    {
        $this->filename = $filename;

        return $this->toResponse(request());
    }

    public function toResponse($request): Response
    {
        return response($this->buildPdf(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$this->filename.'"',
        ]);
    }

    protected function buildPdf(): string
    {
        $text = html_entity_decode(strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $this->html)));
        $text = preg_replace("/\r\n|\r/", "\n", $text) ?? '';
        $text = preg_replace("/[ \t]+/", ' ', $text) ?? '';
        $text = preg_replace("/\n{2,}/", "\n", $text) ?? '';

        $lines = collect(explode("\n", trim($text)))
            ->map(fn ($line) => trim($line))
            ->filter()
            ->flatMap(function ($line) {
                return collect(Str::of(wordwrap($line, 95, "\n", true))->explode("\n"));
            })
            ->values()
            ->all();

        $pages = array_chunk($lines, 42);
        if ($pages === []) {
            $pages = [['Dokumen kosong']];
        }

        $objects = [];
        $pageObjectIds = [];
        $contentObjectIds = [];
        $fontObjectId = 3;
        $nextId = 4;

        foreach ($pages as $pageLines) {
            $content = "BT\n/F1 10 Tf\n50 790 Td\n14 TL\n";

            foreach ($pageLines as $index => $line) {
                if ($index > 0) {
                    $content .= "T*\n";
                }

                $escaped = str_replace(['\\', '(', ')'], ['\\\\', '\(', '\)'], $line);
                $content .= "({$escaped}) Tj\n";
            }

            $content .= 'ET';
            $contentObjectId = $nextId;
            $contentObjectIds[] = $contentObjectId;
            $objects[$contentObjectId] = '<< /Length '.strlen($content)." >>\nstream\n{$content}\nendstream";
            $nextId++;

            $pageObjectId = $nextId;
            $pageObjectIds[] = $pageObjectId;
            $objects[$pageObjectId] = "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Contents {$contentObjectId} 0 R /Resources << /Font << /F1 {$fontObjectId} 0 R >> >> >>";
            $nextId++;
        }

        $kids = implode(' ', array_map(fn ($id) => "{$id} 0 R", $pageObjectIds));
        $objects[1] = '<< /Type /Catalog /Pages 2 0 R >>';
        $objects[2] = "<< /Type /Pages /Kids [ {$kids} ] /Count ".count($pageObjectIds).' >>';
        $objects[$fontObjectId] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>';
        ksort($objects);

        $pdf = "%PDF-1.4\n";
        $offsets = [0];

        foreach ($objects as $id => $content) {
            $offsets[$id] = strlen($pdf);
            $pdf .= "{$id} 0 obj\n{$content}\nendobj\n";
        }

        $xrefOffset = strlen($pdf);
        $pdf .= "xref\n0 ".(count($objects) + 1)."\n";
        $pdf .= "0000000000 65535 f \n";

        for ($id = 1; $id <= count($objects); $id++) {
            $pdf .= str_pad((string) $offsets[$id], 10, '0', STR_PAD_LEFT)." 00000 n \n";
        }

        $pdf .= "trailer\n<< /Size ".(count($objects) + 1)." /Root 1 0 R >>\nstartxref\n{$xrefOffset}\n%%EOF";

        return $pdf;
    }
}
