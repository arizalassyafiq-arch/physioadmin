<?php

namespace App\Http\Controllers;

use App\Models\Intervention;
use App\Models\MedicalRecord;
use App\Models\Patient;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        return view('pages.reports.index', $this->reportData($request));
    }

    public function exportPdf(Request $request): Response
    {
        $data = $this->reportData($request);

        return Pdf::loadView('pages.reports.pdf', $data)
            ->setPaper('a4', 'portrait')
            ->download("laporan-klinik-{$data['monthValue']}.pdf");
    }

    protected function reportData(Request $request): array
    {
        $validated = $request->validate([
            'month' => ['nullable', 'date_format:Y-m'],
        ]);

        $monthValue = $validated['month'] ?? now()->format('Y-m');
        $month = Carbon::createFromFormat('Y-m-d', "{$monthValue}-01")->startOfMonth();
        $start = $month->copy()->startOfDay();
        $end = $month->copy()->endOfMonth()->endOfDay();

        $recentRecords = MedicalRecord::query()
            ->with('patient')
            ->whereBetween('examined_at', [$start->toDateString(), $end->toDateString()])
            ->latest('examined_at')
            ->latest('created_at')
            ->take(10)
            ->get();

        return [
            'monthValue' => $monthValue,
            'monthLabel' => $month->copy()->locale('id')->translatedFormat('F Y'),
            'summary' => [
                'totalPatients' => Patient::count(),
                'newPatientsThisMonth' => Patient::query()->whereBetween('created_at', [$start, $end])->count(),
                'medicalRecordsThisMonth' => MedicalRecord::query()->whereBetween('examined_at', [$start->toDateString(), $end->toDateString()])->count(),
                'interventionsThisMonth' => Intervention::query()->whereBetween('tgl', [$start->toDateString(), $end->toDateString()])->count(),
            ],
            'dailyVisits' => $this->dailyVisits($start, $end),
            'recentRecords' => $recentRecords,
        ];
    }

    protected function dailyVisits(Carbon $start, Carbon $end): array
    {
        $counts = MedicalRecord::query()
            ->whereBetween('examined_at', [$start->toDateString(), $end->toDateString()])
            ->get(['examined_at'])
            ->groupBy(fn (MedicalRecord $record) => $record->examined_at?->toDateString())
            ->map->count();

        $days = [];
        $cursor = $start->copy();

        while ($cursor->lte($end)) {
            $date = $cursor->toDateString();
            $days[] = [
                'date' => $date,
                'day' => $cursor->format('d'),
                'label' => $cursor->copy()->locale('id')->translatedFormat('d M'),
                'count' => (int) ($counts[$date] ?? 0),
            ];
            $cursor->addDay();
        }

        return $days;
    }
}
