<x-layouts.app :title="'Detail Rekam Medis | PhysioAdmin'" :header="'Detail Rekam Medis'">
    @php
        $isPediatric = ($record->patient->kategori_pasien ?? 'dewasa') === 'anak';
        $pediatricData = $record->pediatric_data ?? [];
        $consciousnessLabels = [
            'compos_mentis' => 'Compos mentis',
            'apatis' => 'Apatis',
            'somnolen' => 'Somnolen',
            'sopor' => 'Sopor',
            'sopor_coma' => 'Sopor Coma',
            'coma' => 'Coma',
        ];
    @endphp

    <div class="space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-3xl font-bold text-slate-900">{{ $record->patient->nama }}</h2>
                <p class="mt-2 text-sm text-slate-500">No. RM {{ $record->patient->no_rm }} - Pemeriksaan {{ optional($record->examined_at)->translatedFormat('d F Y') ?? $record->created_at->translatedFormat('d F Y') }}</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('records.edit', $record) }}" class="rounded-xl border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Edit</a>
                <a href="{{ route('records.pdf', $record) }}" class="rounded-xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700">Unduh PDF</a>
                <form method="POST" action="{{ route('records.destroy', $record) }}" onsubmit="return confirm('Hapus rekam medis ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="rounded-xl border border-red-200 px-5 py-3 text-sm font-semibold text-red-600 transition hover:bg-red-50">Hapus</button>
                </form>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-2">
            <section class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <h3 class="text-lg font-semibold text-blue-900">Identitas Pasien</h3>
                <div class="mt-5 grid gap-4 sm:grid-cols-2">
                    <div><p class="text-xs uppercase tracking-[0.2em] text-slate-400">Nama</p><p class="mt-2 font-semibold text-slate-900">{{ $record->patient->nama }}</p></div>
                    <div><p class="text-xs uppercase tracking-[0.2em] text-slate-400">No. RM</p><p class="mt-2 font-semibold text-slate-900">{{ $record->patient->no_rm }}</p></div>
                    <div><p class="text-xs uppercase tracking-[0.2em] text-slate-400">Kategori Pasien</p><p class="mt-2 font-semibold text-slate-900">{{ $record->patient->categoryLabel() }}</p></div>
                    <div><p class="text-xs uppercase tracking-[0.2em] text-slate-400">Jenis Kelamin</p><p class="mt-2 font-semibold text-slate-900">{{ $record->patient->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</p></div>
                    <div><p class="text-xs uppercase tracking-[0.2em] text-slate-400">Umur Saat Pemeriksaan</p><p class="mt-2 font-semibold text-slate-900">{{ $record->patient_age_at_visit ?? $record->patient->umur }} tahun</p></div>
                    <div><p class="text-xs uppercase tracking-[0.2em] text-slate-400">Jadwal Terapis</p><p class="mt-2 font-semibold text-slate-900">{{ $record->jadwal_terapis ?: '-' }}</p></div>
                    <div class="sm:col-span-2"><p class="text-xs uppercase tracking-[0.2em] text-slate-400">Alamat</p><p class="mt-2 font-semibold text-slate-900">{{ $record->patient->alamat ?: '-' }}</p></div>
                </div>
            </section>

            <section class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <h3 class="text-lg font-semibold text-blue-900">Vital Sign dan Nyeri</h3>
                <div class="mt-5 grid gap-4 sm:grid-cols-2">
                    <div><p class="text-xs uppercase tracking-[0.2em] text-slate-400">Nadi</p><p class="mt-2 font-semibold text-slate-900">{{ $record->nadi ?: '-' }}</p></div>
                    <div><p class="text-xs uppercase tracking-[0.2em] text-slate-400">Suhu</p><p class="mt-2 font-semibold text-slate-900">{{ $record->suhu ?: '-' }}</p></div>
                    <div><p class="text-xs uppercase tracking-[0.2em] text-slate-400">Tensi</p><p class="mt-2 font-semibold text-slate-900">{{ $record->tensi ?: '-' }}</p></div>
                    <div><p class="text-xs uppercase tracking-[0.2em] text-slate-400">Frekuensi Nafas</p><p class="mt-2 font-semibold text-slate-900">{{ $record->frekuensi_nafas ?: '-' }}</p></div>
                    <div><p class="text-xs uppercase tracking-[0.2em] text-slate-400">Nyeri Diam</p><p class="mt-2 font-semibold text-slate-900">{{ $record->nyeri_diam ?? '-' }}</p></div>
                    <div><p class="text-xs uppercase tracking-[0.2em] text-slate-400">Nyeri Gerak</p><p class="mt-2 font-semibold text-slate-900">{{ $record->nyeri_gerak ?? '-' }}</p></div>
                    @if ($isPediatric)
                        <div><p class="text-xs uppercase tracking-[0.2em] text-slate-400">Berat Badan</p><p class="mt-2 font-semibold text-slate-900">{{ $record->berat_badan ?: '-' }}</p></div>
                        <div><p class="text-xs uppercase tracking-[0.2em] text-slate-400">Tinggi Badan</p><p class="mt-2 font-semibold text-slate-900">{{ $record->tinggi_badan ?: '-' }}</p></div>
                        <div><p class="text-xs uppercase tracking-[0.2em] text-slate-400">Lingkar Kepala</p><p class="mt-2 font-semibold text-slate-900">{{ data_get($pediatricData, 'lingkar_kepala', '-') }}</p></div>
                        <div><p class="text-xs uppercase tracking-[0.2em] text-slate-400">Tingkat Kesadaran</p><p class="mt-2 font-semibold text-slate-900">{{ $consciousnessLabels[data_get($pediatricData, 'tingkat_kesadaran')] ?? '-' }}</p></div>
                    @endif
                </div>
            </section>
        </div>

        @if ($isPediatric)
            <div class="grid gap-6 xl:grid-cols-2">
                @foreach ([
                    'Data Pediatri' => [
                        'Nama Ibu/Ayah' => data_get($pediatricData, 'nama_ibu_ayah'),
                        'Umur Ibu' => data_get($pediatricData, 'umur_ibu'),
                        'Umur Ayah' => data_get($pediatricData, 'umur_ayah'),
                        'Diagnosis Medis' => data_get($pediatricData, 'diagnosis_medis'),
                        'ICD' => data_get($pediatricData, 'icd'),
                    ],
                    'Anamnesis Pediatri' => [
                        'Riwayat Pre Natal' => data_get($pediatricData, 'riwayat_prenatal'),
                        'Riwayat Natal' => data_get($pediatricData, 'riwayat_natal'),
                        'Riwayat Post Natal' => data_get($pediatricData, 'riwayat_postnatal'),
                        'Riwayat NICU/PICU' => data_get($pediatricData, 'riwayat_nicu_picu'),
                        'Riwayat Penyerta' => data_get($pediatricData, 'riwayat_penyerta'),
                        'Riwayat Imunisasi' => data_get($pediatricData, 'riwayat_imunisasi'),
                    ],
                    'Pemeriksaan Pediatri' => [
                        'Inspeksi Kesadaran Umum' => data_get($pediatricData, 'inspeksi_kesadaran_umum'),
                        'Pemeriksaan Gerak Dasar' => data_get($pediatricData, 'pemeriksaan_gerak_dasar'),
                        'Pemeriksaan Khusus' => data_get($pediatricData, 'pemeriksaan_khusus'),
                        'Fisioterapis' => data_get($pediatricData, 'fisioterapis'),
                    ],
                ] as $section => $items)
                    <section class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                        <h3 class="text-lg font-semibold text-blue-900">{{ $section }}</h3>
                        <div class="mt-5 space-y-4">
                            @foreach ($items as $label => $value)
                                <div>
                                    <p class="text-xs uppercase tracking-[0.2em] text-slate-400">{{ $label }}</p>
                                    <p class="mt-2 whitespace-pre-line font-semibold text-slate-900">{{ $value ?: '-' }}</p>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endforeach
            </div>
        @endif

        <div class="grid gap-6 xl:grid-cols-2">
            @foreach ([
                'Anamnesis' => [
                    'Keluhan Utama' => $record->keluhan_utama,
                    'Riwayat Penyakit Sekarang' => $record->riwayat_penyakit_sekarang,
                    'Riwayat Penyakit Dahulu' => $record->riwayat_penyakit_dahulu,
                    'Riwayat Penyakit Keluarga' => $record->riwayat_penyakit_keluarga,
                    'Riwayat Pengobatan' => $record->riwayat_penggunaan_obat,
                    'Riwayat Alergi' => $record->riwayat_alergi,
                ],
                'Pemeriksaan Fisik' => [
                    'Inspeksi Statis' => $record->inspeksi_statis,
                    'Inspeksi Dinamis' => $record->inspeksi_dinamis,
                    'Palpasi' => $record->palpasi,
                    'Perkusi' => $record->perkusi,
                    'Auskultasi' => $record->auskultasi,
                    'MMT' => $record->mmt,
                    'Lingkup Gerak Sendi' => $record->lingkup_gerak_sendi,
                    'Antropometri' => $record->antropometri,
                ],
                'Pemeriksaan Penunjang' => [
                    'Hasil Penunjang' => $record->hasil_penunjang,
                    'File Penunjang' => $record->file_penunjang ? 'Lihat file penunjang' : '-',
                ],
                'ICF dan Diagnosa' => [
                    'Body Structures' => $record->icf_body_structures,
                    'Body Functions' => $record->icf_body_functions,
                    'Activities & Participation' => $record->icf_activities_participation,
                    'Environmental Factors' => $record->icf_environmental_factors,
                    'Impairment' => $record->diagnosa_impairment,
                    'Functional Limitation' => $record->diagnosa_functional_limitation,
                    'Participation Restriction' => $record->diagnosa_participation_restriction,
                ],
            ] as $section => $items)
                <section class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <h3 class="text-lg font-semibold text-blue-900">{{ $section }}</h3>
                    <div class="mt-5 space-y-4">
                        @foreach ($items as $label => $value)
                            <div>
                                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">{{ $label }}</p>
                                @if ($label === 'File Penunjang' && $record->file_penunjang)
                                    <a href="{{ route('records.file', $record) }}" target="_blank" rel="noopener noreferrer" class="mt-2 inline-block font-semibold text-blue-600 hover:text-blue-700">{{ $value }}</a>
                                @else
                                    <p class="mt-2 whitespace-pre-line font-semibold text-slate-900">{{ $value ?: '-' }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </section>
            @endforeach
        </div>

        <section class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <h3 class="text-lg font-semibold text-blue-900">Rencana Intervensi</h3>
            <div class="mt-5 grid gap-4 md:grid-cols-2">
                @forelse ($record->rencana_intervensi ?? [] as $index => $plan)
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-xs uppercase tracking-[0.22em] text-slate-400">Rencana {{ $index + 1 }}</p>
                        <p class="mt-2 font-semibold text-slate-900">{{ $plan }}</p>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Belum ada rencana intervensi.</p>
                @endforelse
            </div>
        </section>

        <section class="overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-slate-200">
            <div class="border-b border-slate-200 px-6 py-5">
                <h3 class="text-lg font-semibold text-blue-900">Pelaksanaan & Evaluasi Intervensi</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-slate-500">
                        <tr>
                            <th class="px-4 py-3 font-medium">No</th>
                            <th class="px-4 py-3 font-medium">Tanggal</th>
                            <th class="px-4 py-3 font-medium">Intervensi</th>
                            <th class="px-4 py-3 font-medium">Hasil Evaluasi</th>
                            <th class="px-4 py-3 font-medium">Paraf</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @forelse ($record->interventions as $intervention)
                            <tr>
                                <td class="px-4 py-4">{{ $loop->iteration }}</td>
                                <td class="px-4 py-4">{{ optional($intervention->tgl)->translatedFormat('d M Y') }}</td>
                                <td class="px-4 py-4">{{ $intervention->intervensi }}</td>
                                <td class="px-4 py-4">{{ $intervention->hasil_evaluasi ?: '-' }}</td>
                                <td class="px-4 py-4">
                                    @if ($intervention->paraf)
                                        <a href="{{ route('interventions.signature', $intervention) }}" target="_blank" rel="noopener noreferrer" class="font-semibold text-blue-600 hover:text-blue-700">Lihat</a>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-10 text-center text-slate-500">Belum ada data intervensi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</x-layouts.app>
