@php
    $maxVisits = max(1, collect($dailyVisits)->max('count') ?? 0);
    $summaryCards = [
        ['label' => 'Total Pasien', 'value' => $summary['totalPatients'], 'note' => 'Seluruh pasien terdaftar'],
        ['label' => 'Pasien Baru Bulan Ini', 'value' => $summary['newPatientsThisMonth'], 'note' => $monthLabel],
        ['label' => 'Rekam Medis Bulan Ini', 'value' => $summary['medicalRecordsThisMonth'], 'note' => 'Berdasarkan tanggal pemeriksaan'],
        ['label' => 'Intervensi Bulan Ini', 'value' => $summary['interventionsThisMonth'], 'note' => 'Berdasarkan tanggal intervensi'],
    ];
@endphp

<x-layouts.app :title="'Laporan | PhysioAdmin'" :header="'Laporan'">
    <div class="space-y-6">
        <section class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.22em] text-blue-600">Laporan Bulanan Klinik</p>
                    <h2 class="mt-3 text-2xl font-bold text-slate-900">{{ $monthLabel }}</h2>
                    <p class="mt-2 max-w-2xl text-sm leading-7 text-slate-600">
                        Ringkasan pasien, rekam medis, intervensi, dan kunjungan klinik berdasarkan bulan laporan.
                    </p>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row">
                    <form method="GET" action="{{ route('reports', [], false) }}" class="flex gap-3">
                        <input
                            type="month"
                            name="month"
                            value="{{ $monthValue }}"
                            class="rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
                        >
                        <button type="submit" class="rounded-xl border border-slate-300 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                            Tampilkan
                        </button>
                    </form>

                    <a href="{{ route('reports.pdf', ['month' => $monthValue]) }}" class="inline-flex items-center justify-center rounded-xl bg-[#2563eb] px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-700">
                        Export PDF
                    </a>
                </div>
            </div>
        </section>

        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            @foreach ($summaryCards as $card)
                <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                    <p class="text-sm font-medium text-slate-500">{{ $card['label'] }}</p>
                    <p class="mt-3 text-3xl font-bold text-slate-950">{{ number_format($card['value'], 0, ',', '.') }}</p>
                    <p class="mt-2 text-xs text-slate-500">{{ $card['note'] }}</p>
                </div>
            @endforeach
        </section>

        <section class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <div class="flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-blue-900">Grafik Kunjungan per Tanggal</h3>
                    <p class="mt-1 text-sm text-slate-500">Jumlah rekam medis berdasarkan tanggal pemeriksaan.</p>
                </div>
                <p class="text-sm font-semibold text-slate-600">Total {{ number_format($summary['medicalRecordsThisMonth'], 0, ',', '.') }} kunjungan</p>
            </div>

            <div class="mt-6 overflow-x-auto">
                <div class="flex min-w-[760px] items-end gap-2 border-b border-slate-200 pb-4">
                    @foreach ($dailyVisits as $visit)
                        @php
                            $height = 18 + (($visit['count'] / $maxVisits) * 132);
                        @endphp

                        <div class="flex flex-1 flex-col items-center gap-2">
                            <div class="flex h-40 w-full items-end justify-center">
                                <div
                                    class="w-full max-w-5 rounded-t-lg {{ $visit['count'] > 0 ? 'bg-blue-600' : 'bg-slate-200' }}"
                                    style="height: {{ $height }}px"
                                    title="{{ $visit['label'] }}: {{ $visit['count'] }} kunjungan"
                                ></div>
                            </div>
                            <p class="text-[11px] font-semibold text-slate-500">{{ $visit['day'] }}</p>
                            <p class="text-[11px] text-slate-400">{{ $visit['count'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-slate-200">
            <div class="flex flex-col gap-1 border-b border-slate-200 px-6 py-5 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-blue-900">Rekam Medis Terbaru</h3>
                    <p class="mt-1 text-sm text-slate-500">Maksimal 10 rekam medis terbaru pada {{ $monthLabel }}.</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-[860px] divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-slate-500">
                        <tr>
                            <th class="px-5 py-3 font-medium">Tanggal</th>
                            <th class="px-5 py-3 font-medium">No. RM</th>
                            <th class="px-5 py-3 font-medium">Pasien</th>
                            <th class="px-5 py-3 font-medium">Keluhan Utama</th>
                            <th class="px-5 py-3 font-medium">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @forelse ($recentRecords as $record)
                            <tr>
                                <td class="px-5 py-4 text-slate-700">{{ optional($record->examined_at)->translatedFormat('d M Y') ?? '-' }}</td>
                                <td class="px-5 py-4 font-semibold text-slate-900">{{ $record->patient->no_rm }}</td>
                                <td class="px-5 py-4 text-slate-700">{{ $record->patient->nama }}</td>
                                <td class="px-5 py-4 text-slate-700">{{ $record->keluhan_utama ?: '-' }}</td>
                                <td class="px-5 py-4">
                                    <a href="{{ route('records.show', $record) }}" class="rounded-lg bg-[#2563eb] px-3 py-2 text-xs font-semibold text-white transition hover:bg-blue-700">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-10 text-center text-sm text-slate-500">
                                    Belum ada rekam medis pada bulan ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</x-layouts.app>
