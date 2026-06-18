@php
    $currentPlan = old('rencana_intervensi', $record->rencana_intervensi ?: ['']);
    $recordSections = [
        'identitas' => 'Identitas',
        'anamnesis' => 'Anamnesis',
        'fisik' => 'Fisik',
        'nyeri' => 'Nyeri',
        'penunjang' => 'Penunjang',
        'kognitif' => 'Kognitif',
        'icf' => 'ICF',
        'intervensi' => 'Intervensi',
    ];
    $vitalSignFields = [
        'nadi' => ['label' => 'Nadi (bpm)', 'type' => 'number', 'inputmode' => 'numeric', 'min' => '20', 'max' => '250', 'step' => '1', 'placeholder' => '80'],
        'suhu' => ['label' => 'Suhu (&deg;C)', 'type' => 'number', 'inputmode' => 'decimal', 'min' => '30', 'max' => '45', 'step' => '0.1', 'placeholder' => '36.5'],
        'tensi' => ['label' => 'Tensi (mmHg)', 'type' => 'text', 'inputmode' => 'numeric', 'pattern' => '[0-9]{2,3}/[0-9]{2,3}', 'placeholder' => '120/80', 'title' => 'Format: 120/80'],
        'frekuensi_nafas' => ['label' => 'Frekuensi Nafas (x/mnt)', 'type' => 'number', 'inputmode' => 'numeric', 'min' => '5', 'max' => '80', 'step' => '1', 'placeholder' => '20'],
        'berat_badan' => ['label' => 'Berat Badan (kg)', 'type' => 'number', 'inputmode' => 'decimal', 'min' => '1', 'max' => '500', 'step' => '0.1', 'placeholder' => '65'],
        'tinggi_badan' => ['label' => 'Tinggi Badan (cm)', 'type' => 'number', 'inputmode' => 'decimal', 'min' => '30', 'max' => '250', 'step' => '0.1', 'placeholder' => '170'],
    ];
@endphp

<form method="POST" action="{{ $formAction }}" enctype="multipart/form-data" x-data="recordForm(@js(old('interventions', $interventionRows)))" class="space-y-6">
    @csrf
    @if ($formMethod !== 'POST')
        @method($formMethod)
    @endif

    <div class="rounded-2xl border border-blue-100 bg-blue-50 p-4">
        <div class="grid gap-3 text-sm md:grid-cols-3">
            <div class="text-blue-700">1. Identitas Pasien</div>
            <div class="font-semibold text-blue-950">2. Rekam Medis Awal</div>
            <div class="text-blue-700">3. Review & Simpan</div>
        </div>
    </div>

    <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 sm:text-3xl">{{ $pageTitle }}</h2>
            <p class="mt-2 text-sm text-slate-500">Lengkapi formulir pemeriksaan fisioterapi di bawah ini dengan teliti.</p>
        </div>
        <div class="grid gap-3 sm:flex sm:flex-wrap">
            <button type="submit" class="rounded-xl bg-[#2563eb] px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-700">
                Simpan Rekam Medis
            </button>
        </div>
    </div>

    <nav class="sticky top-0 z-10 -mx-1 flex gap-2 overflow-x-auto border-y border-slate-200 bg-slate-50/95 px-1 py-3 backdrop-blur">
        @foreach ($recordSections as $target => $label)
            <a href="#section-{{ $target }}" class="shrink-0 rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-700 shadow-sm transition hover:border-blue-200 hover:text-blue-700">
                {{ $label }}
            </a>
        @endforeach
    </nav>

    <div class="grid gap-6 xl:grid-cols-[0.95fr_1.05fr]">
        <div class="space-y-6">
            <section id="section-identitas" class="scroll-mt-24 rounded-3xl bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:p-6">
                <h3 class="text-lg font-semibold text-blue-900">Identitas Pasien</h3>
                <div class="mt-5 grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="examined_at" class="mb-2 block text-sm font-medium text-slate-700">Tanggal Pemeriksaan</label>
                        <input id="examined_at" name="examined_at" type="date" value="{{ old('examined_at', optional($record->examined_at)->format('Y-m-d') ?? now()->toDateString()) }}" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200" required>
                        <x-field-error :messages="$errors->get('examined_at')" />
                    </div>
                    <div class="sm:col-span-2">
                        <label for="jadwal_terapis" class="mb-2 block text-sm font-medium text-slate-700">Jadwal Terapis</label>
                        <input id="jadwal_terapis" name="jadwal_terapis" type="text" value="{{ old('jadwal_terapis', $record->jadwal_terapis ?? '') }}" placeholder="Opsional, contoh: Senin 14.00" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                        <x-field-error :messages="$errors->get('jadwal_terapis')" />
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Nama</p>
                        <p class="mt-2 font-semibold text-slate-900">{{ $patient->nama }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">No. RM</p>
                        <p class="mt-2 font-semibold text-slate-900">{{ $patient->no_rm }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Kategori Pasien</p>
                        <p class="mt-2 font-semibold text-slate-900">{{ $patient->categoryLabel() }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Jenis Kelamin</p>
                        <p class="mt-2 font-semibold text-slate-900">{{ $patient->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Tanggal Lahir</p>
                        <p class="mt-2 font-semibold text-slate-900">{{ $patient->tanggal_lahir->translatedFormat('d F Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Umur</p>
                        <p class="mt-2 font-semibold text-slate-900">{{ $patient->umur }} tahun</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Pekerjaan</p>
                        <p class="mt-2 font-semibold text-slate-900">{{ $patient->pekerjaan ?: '-' }}</p>
                    </div>
                    <div class="sm:col-span-2">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Alamat</p>
                        <p class="mt-2 font-semibold text-slate-900">{{ $patient->alamat ?: '-' }}</p>
                    </div>
                </div>
            </section>

            <section id="section-anamnesis" class="scroll-mt-24 rounded-3xl bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:p-6">
                <h3 class="text-lg font-semibold text-blue-900">Anamnesis</h3>
                <div class="mt-5 space-y-4">
                    @foreach ([
                        'keluhan_utama' => 'Keluhan Utama',
                        'riwayat_penyakit_sekarang' => 'Riwayat Penyakit Sekarang',
                        'riwayat_penyakit_dahulu' => 'Riwayat Penyakit Dahulu',
                        'riwayat_penyakit_keluarga' => 'Riwayat Penyakit Keluarga',
                        'riwayat_penggunaan_obat' => 'Riwayat Pengobatan',
                        'riwayat_alergi' => 'Riwayat Alergi',
                    ] as $field => $label)
                        <div>
                            <label for="{{ $field }}" class="mb-2 block text-sm font-medium text-slate-700">{{ $label }}</label>
                            <textarea id="{{ $field }}" name="{{ $field }}" rows="3" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200">{{ old($field, $record->{$field} ?? '') }}</textarea>
                            <x-field-error :messages="$errors->get($field)" />
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="scroll-mt-24 rounded-3xl bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:p-6">
                <h3 class="text-lg font-semibold text-blue-900">Vital Sign</h3>
                <div class="mt-5 grid gap-4 sm:grid-cols-2">
                    @foreach ($vitalSignFields as $field => $config)
                        <div>
                            <label for="{{ $field }}" class="mb-2 block text-sm font-medium text-slate-700">{!! $config['label'] !!}</label>
                            <input
                                id="{{ $field }}"
                                name="{{ $field }}"
                                type="{{ $config['type'] }}"
                                value="{{ old($field, $record->{$field} ?? '') }}"
                                inputmode="{{ $config['inputmode'] }}"
                                placeholder="{{ $config['placeholder'] }}"
                                @if (isset($config['min'])) min="{{ $config['min'] }}" @endif
                                @if (isset($config['max'])) max="{{ $config['max'] }}" @endif
                                @if (isset($config['step'])) step="{{ $config['step'] }}" @endif
                                @if (isset($config['pattern'])) pattern="{{ $config['pattern'] }}" @endif
                                @if (isset($config['title'])) title="{{ $config['title'] }}" @endif
                                class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
                            >
                            <x-field-error :messages="$errors->get($field)" />
                        </div>
                    @endforeach
                </div>
            </section>
        </div>

        <div class="space-y-6">
            <section id="section-fisik" class="scroll-mt-24 rounded-3xl bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:p-6">
                <h3 class="text-lg font-semibold text-blue-900">Pemeriksaan Fisik</h3>
                <div class="mt-5 grid gap-4 md:grid-cols-2">
                    @foreach ([
                        'inspeksi_statis' => 'Inspeksi Statis',
                        'inspeksi_dinamis' => 'Inspeksi Dinamis',
                        'palpasi' => 'Palpasi',
                        'perkusi' => 'Perkusi',
                        'auskultasi' => 'Auskultasi',
                        'mmt' => 'MMT',
                        'lingkup_gerak_sendi' => 'Lingkup Gerak Sendi',
                        'antropometri' => 'Antropometri',
                    ] as $field => $label)
                        <div class="{{ in_array($field, ['lingkup_gerak_sendi', 'antropometri'], true) ? 'md:col-span-2' : '' }}">
                            <label for="{{ $field }}" class="mb-2 block text-sm font-medium text-slate-700">{{ $label }}</label>
                            <input id="{{ $field }}" name="{{ $field }}" type="text" value="{{ old($field, $record->{$field} ?? '') }}" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                            <x-field-error :messages="$errors->get($field)" />
                        </div>
                    @endforeach
                </div>
            </section>

            <section id="section-nyeri" class="scroll-mt-24 rounded-3xl bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:p-6">
                <h3 class="text-lg font-semibold text-blue-900">Pemeriksaan Nyeri (VAS/NPRS)</h3>
                <div class="mt-5 grid gap-4 sm:grid-cols-3">
                    @foreach ([
                        'nyeri_diam' => 'Nyeri Diam',
                        'nyeri_tekan' => 'Nyeri Tekan',
                        'nyeri_gerak' => 'Nyeri Gerak',
                    ] as $field => $label)
                        <div>
                            <label for="{{ $field }}" class="mb-2 block text-sm font-medium text-slate-700">{{ $label }}</label>
                            <input id="{{ $field }}" name="{{ $field }}" type="number" min="0" max="10" value="{{ old($field, $record->{$field} ?? '') }}" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                            <x-field-error :messages="$errors->get($field)" />
                        </div>
                    @endforeach
                </div>
                <div class="mt-4 space-y-4">
                    <div>
                        <label for="faktor_pemberat" class="mb-2 block text-sm font-medium text-slate-700">Faktor Pemberat/Peringan</label>
                        <textarea id="faktor_pemberat" name="faktor_pemberat" rows="3" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200">{{ old('faktor_pemberat', $record->faktor_pemberat ?? '') }}</textarea>
                        <x-field-error :messages="$errors->get('faktor_pemberat')" />
                    </div>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label for="deskripsi_nyeri" class="mb-2 block text-sm font-medium text-slate-700">Deskripsi Nyeri</label>
                            <textarea id="deskripsi_nyeri" name="deskripsi_nyeri" rows="3" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200">{{ old('deskripsi_nyeri', $record->deskripsi_nyeri ?? '') }}</textarea>
                            <x-field-error :messages="$errors->get('deskripsi_nyeri')" />
                        </div>
                        <div>
                            <label for="waktu_onset_nyeri" class="mb-2 block text-sm font-medium text-slate-700">Waktu / Onset Nyeri</label>
                            <input id="waktu_onset_nyeri" name="waktu_onset_nyeri" type="text" value="{{ old('waktu_onset_nyeri', $record->waktu_onset_nyeri ?? '') }}" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                            <x-field-error :messages="$errors->get('waktu_onset_nyeri')" />
                        </div>
                    </div>
                </div>
            </section>

            <section id="section-penunjang" class="scroll-mt-24 rounded-3xl bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:p-6">
                <h3 class="text-lg font-semibold text-blue-900">Hasil Pemeriksaan Penunjang</h3>
                <div class="mt-5 space-y-4">
                    <div>
                        <label for="hasil_penunjang" class="mb-2 block text-sm font-medium text-slate-700">Hasil Pemeriksaan Penunjang (Rontgen/MRI/EMG/CT-Scan/Lab)</label>
                        <textarea id="hasil_penunjang" name="hasil_penunjang" rows="4" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200">{{ old('hasil_penunjang', $record->hasil_penunjang ?? '') }}</textarea>
                        <x-field-error :messages="$errors->get('hasil_penunjang')" />
                    </div>
                    <div>
                        <label for="file_penunjang" class="mb-2 block text-sm font-medium text-slate-700">Upload File Penunjang</label>
                        <input id="file_penunjang" name="file_penunjang" type="file" class="block w-full rounded-xl border border-dashed border-slate-300 bg-slate-50 px-4 py-3 text-sm text-slate-600">
                        <x-field-error :messages="$errors->get('file_penunjang')" />
                        @if (!empty($record->file_penunjang))
                            <a href="{{ route('records.file', $record) }}" target="_blank" rel="noopener noreferrer" class="mt-2 inline-block text-sm font-semibold text-blue-600 hover:text-blue-700">
                                Lihat file saat ini
                            </a>
                        @endif
                    </div>
                </div>
            </section>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <section id="section-kognitif" class="scroll-mt-24 rounded-3xl bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:p-6">
            <h3 class="text-lg font-semibold text-blue-900">Pemeriksaan Kognitif dan Psikologi</h3>
            <div class="mt-5 grid gap-4">
                <div>
                    <label for="pemeriksaan_kognitif" class="mb-2 block text-sm font-medium text-slate-700">Pemeriksaan Kognitif</label>
                    <textarea id="pemeriksaan_kognitif" name="pemeriksaan_kognitif" rows="4" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200">{{ old('pemeriksaan_kognitif', $record->pemeriksaan_kognitif ?? '') }}</textarea>
                    <x-field-error :messages="$errors->get('pemeriksaan_kognitif')" />
                </div>
                <div>
                    <label for="pemeriksaan_psikologi" class="mb-2 block text-sm font-medium text-slate-700">Pemeriksaan Psikologi</label>
                    <textarea id="pemeriksaan_psikologi" name="pemeriksaan_psikologi" rows="4" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200">{{ old('pemeriksaan_psikologi', $record->pemeriksaan_psikologi ?? '') }}</textarea>
                    <x-field-error :messages="$errors->get('pemeriksaan_psikologi')" />
                </div>
            </div>
        </section>

        <section class="scroll-mt-24 rounded-3xl bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:p-6">
            <h3 class="text-lg font-semibold text-blue-900">Pemeriksaan Khusus Lain</h3>
            <div class="mt-5">
                <textarea id="pemeriksaan_khusus_lain" name="pemeriksaan_khusus_lain" rows="9" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200">{{ old('pemeriksaan_khusus_lain', $record->pemeriksaan_khusus_lain ?? '') }}</textarea>
                <x-field-error :messages="$errors->get('pemeriksaan_khusus_lain')" />
            </div>
        </section>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <section id="section-icf" class="scroll-mt-24 rounded-3xl bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:p-6">
            <h3 class="text-lg font-semibold text-blue-900">Kode dan Keterangan ICF</h3>
            <div class="mt-5 space-y-4">
                @foreach ([
                    'icf_body_structures' => 'Body Structures',
                    'icf_body_functions' => 'Body Functions',
                    'icf_activities_participation' => 'Activities & Participation',
                    'icf_environmental_factors' => 'Environmental Factors',
                ] as $field => $label)
                    <div>
                        <label for="{{ $field }}" class="mb-2 block text-sm font-medium text-slate-700">{{ $label }}</label>
                        <textarea id="{{ $field }}" name="{{ $field }}" rows="2" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200">{{ old($field, $record->{$field} ?? '') }}</textarea>
                        <x-field-error :messages="$errors->get($field)" />
                    </div>
                @endforeach
            </div>
        </section>

        <section class="scroll-mt-24 rounded-3xl bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:p-6">
            <h3 class="text-lg font-semibold text-blue-900">Diagnosa Fisioterapi</h3>
            <div class="mt-5 space-y-4">
                @foreach ([
                    'diagnosa_impairment' => 'Impairment',
                    'diagnosa_functional_limitation' => 'Functional Limitation',
                    'diagnosa_participation_restriction' => 'Participation Restriction',
                ] as $field => $label)
                    <div>
                        <label for="{{ $field }}" class="mb-2 block text-sm font-medium text-slate-700">{{ $label }}</label>
                        <textarea id="{{ $field }}" name="{{ $field }}" rows="3" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200">{{ old($field, $record->{$field} ?? '') }}</textarea>
                        <x-field-error :messages="$errors->get($field)" />
                    </div>
                @endforeach
            </div>
        </section>
    </div>

    <section class="scroll-mt-24 rounded-3xl bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:p-6">
        <h3 class="text-lg font-semibold text-blue-900">Rencana Intervensi</h3>
        <div class="mt-5">
            <label for="rencana_intervensi_0" class="mb-2 block text-sm font-medium text-slate-700">Rencana</label>
            <input id="rencana_intervensi_0" name="rencana_intervensi[]" type="text" value="{{ $currentPlan[0] ?? '' }}" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
            <x-field-error :messages="$errors->get('rencana_intervensi.0')" />
        </div>
    </section>

    <section id="section-intervensi" class="scroll-mt-24 overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-slate-200">
        <div class="flex flex-col gap-4 border-b border-slate-200 px-4 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div>
                <h3 class="text-lg font-semibold text-blue-900">Pelaksanaan & Evaluasi Intervensi</h3>
                <p class="mt-1 text-sm text-slate-500">Tambahkan catatan intervensi dan hasil evaluasi secara dinamis.</p>
            </div>
            <button type="button" @click="addRow" class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                Tambah Baris
            </button>
        </div>
        @php
            $interventionErrors = collect($errors->getMessages())
                ->filter(fn ($messages, $key) => str_starts_with($key, 'interventions'))
                ->flatten()
                ->all();
        @endphp
        @if ($interventionErrors !== [])
            <div class="border-b border-red-100 bg-red-50 px-6 py-4">
                <x-field-error :messages="$interventionErrors" />
            </div>
        @endif
        <div class="overflow-x-auto">
            <table class="min-w-[980px] divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-slate-500">
                    <tr>
                        <th class="px-4 py-3 font-medium">No</th>
                        <th class="px-4 py-3 font-medium">TGL</th>
                        <th class="px-4 py-3 font-medium">Intervensi</th>
                        <th class="px-4 py-3 font-medium">Hasil Evaluasi</th>
                        <th class="px-4 py-3 font-medium">Paraf</th>
                        <th class="px-4 py-3 font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white">
                    <template x-for="(row, index) in rows" :key="row.uid">
                        <tr>
                            <td class="px-4 py-4 align-top font-semibold text-slate-900" x-text="index + 1"></td>
                            <td class="px-4 py-4 align-top">
                                <input type="hidden" :name="`interventions[${index}][id]`" x-model="row.id">
                                <input type="date" :name="`interventions[${index}][tgl]`" x-model="row.tgl" class="w-40 rounded-xl border border-slate-300 px-3 py-2 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                            </td>
                            <td class="px-4 py-4 align-top">
                                <input type="text" :name="`interventions[${index}][intervensi]`" x-model="row.intervensi" class="w-full min-w-56 rounded-xl border border-slate-300 px-3 py-2 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                            </td>
                            <td class="px-4 py-4 align-top">
                                <input type="text" :name="`interventions[${index}][hasil_evaluasi]`" x-model="row.hasil_evaluasi" class="w-full min-w-56 rounded-xl border border-slate-300 px-3 py-2 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                            </td>
                            <td class="px-4 py-4 align-top">
                                <div class="space-y-2">
                                    <input type="file" :name="`interventions[${index}][paraf]`" class="block w-44 rounded-xl border border-dashed border-slate-300 bg-slate-50 px-3 py-2 text-xs text-slate-600">
                                    <template x-if="row.paraf_url">
                                        <a :href="row.paraf_url" target="_blank" rel="noopener noreferrer" class="inline-block text-xs font-semibold text-blue-600 hover:text-blue-700">Lihat paraf</a>
                                    </template>
                                </div>
                            </td>
                            <td class="px-4 py-4 align-top">
                                <button type="button" @click="removeRow(index)" class="rounded-lg border border-red-200 px-3 py-2 text-xs font-semibold text-red-600 transition hover:bg-red-50">
                                    Hapus
                                </button>
                            </td>
                        </tr>
                    </template>
                    <template x-if="rows.length === 0">
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-sm text-slate-500">
                                Belum ada baris intervensi. Klik Tambah Baris untuk mencatat intervensi.
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </section>

    <div class="sticky bottom-0 z-20 -mx-5 border-t border-slate-200 bg-white/95 px-5 py-4 shadow-[0_-12px_30px_rgba(15,23,42,0.08)] backdrop-blur sm:-mx-8 sm:px-8">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-sm text-slate-600">Pastikan perubahan rekam medis sudah sesuai sebelum disimpan.</p>
            <div class="grid gap-3 sm:flex sm:flex-wrap">
                <button type="submit" class="rounded-xl bg-[#2563eb] px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-700">
                    Simpan Rekam Medis
                </button>
            </div>
        </div>
    </div>
</form>
