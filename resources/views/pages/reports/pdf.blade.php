@php
    $maxVisits = max(1, collect($dailyVisits)->max('count') ?? 0);
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Klinik {{ $monthLabel }}</title>
    <style>
        body { color: #1e293b; font-family: DejaVu Sans, sans-serif; font-size: 11px; }
        h1, h2, h3, p { margin: 0; }
        .muted { color: #64748b; }
        .header { border-bottom: 2px solid #dbeafe; margin-bottom: 18px; padding-bottom: 14px; }
        .title { color: #1e3a8a; font-size: 22px; font-weight: bold; }
        .subtitle { margin-top: 6px; }
        .grid { display: table; margin-bottom: 18px; width: 100%; }
        .card { border: 1px solid #dbe3ef; border-radius: 8px; display: table-cell; padding: 12px; width: 25%; }
        .card + .card { border-left: 0; }
        .card-label { color: #64748b; font-size: 10px; }
        .card-value { color: #0f172a; font-size: 20px; font-weight: bold; margin-top: 8px; }
        .section { margin-top: 18px; }
        .section-title { color: #1e3a8a; font-size: 15px; font-weight: bold; margin-bottom: 8px; }
        table { border-collapse: collapse; width: 100%; }
        th { background: #f8fafc; color: #475569; font-weight: bold; text-align: left; }
        th, td { border: 1px solid #e2e8f0; padding: 7px; vertical-align: top; }
        .bar-table td { border: 0; padding: 2px; text-align: center; vertical-align: bottom; }
        .bar-wrap { height: 92px; position: relative; }
        .bar { background: #2563eb; border-radius: 3px 3px 0 0; display: inline-block; width: 10px; }
        .bar.empty { background: #cbd5e1; }
        .day { color: #64748b; font-size: 8px; }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="title">Laporan Bulanan Klinik</h1>
        <p class="subtitle muted">Periode {{ $monthLabel }}</p>
    </div>

    <div class="grid">
        <div class="card">
            <p class="card-label">Total Pasien</p>
            <p class="card-value">{{ number_format($summary['totalPatients'], 0, ',', '.') }}</p>
        </div>
        <div class="card">
            <p class="card-label">Pasien Baru</p>
            <p class="card-value">{{ number_format($summary['newPatientsThisMonth'], 0, ',', '.') }}</p>
        </div>
        <div class="card">
            <p class="card-label">Rekam Medis</p>
            <p class="card-value">{{ number_format($summary['medicalRecordsThisMonth'], 0, ',', '.') }}</p>
        </div>
        <div class="card">
            <p class="card-label">Intervensi</p>
            <p class="card-value">{{ number_format($summary['interventionsThisMonth'], 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="section">
        <h2 class="section-title">Grafik Kunjungan per Tanggal</h2>
        <table class="bar-table">
            <tr>
                @foreach ($dailyVisits as $visit)
                    @php
                        $height = 10 + (($visit['count'] / $maxVisits) * 80);
                    @endphp
                    <td>
                        <div class="bar-wrap">
                            <span class="bar {{ $visit['count'] > 0 ? '' : 'empty' }}" style="height: {{ $height }}px;"></span>
                        </div>
                    </td>
                @endforeach
            </tr>
            <tr>
                @foreach ($dailyVisits as $visit)
                    <td class="day">{{ $visit['day'] }}</td>
                @endforeach
            </tr>
            <tr>
                @foreach ($dailyVisits as $visit)
                    <td class="day">{{ $visit['count'] }}</td>
                @endforeach
            </tr>
        </table>
    </div>

    <div class="section">
        <h2 class="section-title">Rekam Medis Terbaru</h2>
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>No. RM</th>
                    <th>Pasien</th>
                    <th>Keluhan Utama</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($recentRecords as $record)
                    <tr>
                        <td>{{ optional($record->examined_at)->translatedFormat('d M Y') ?? '-' }}</td>
                        <td>{{ $record->patient->no_rm }}</td>
                        <td>{{ $record->patient->nama }}</td>
                        <td>{{ $record->keluhan_utama ?: '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">Belum ada rekam medis pada bulan ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>
</html>
