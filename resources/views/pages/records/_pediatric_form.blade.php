@php
    $pediatric = old('pediatric_data', $record->pediatric_data ?? []);
    $currentPlan = old('rencana_intervensi', $record->rencana_intervensi ?: ['', '', '', '']);
    $recordSections = [
        'identitas' => 'Identitas',
        'anamnesis' => 'Anamnesis',
        'dasar' => 'Dasar FT',
        'vital' => 'Vital',
        'khusus' => 'Khusus',
        'icf' => 'ICF',
        'intervensi' => 'Intervensi',
    ];
    $pediatricTextareas = [
        'riwayat_prenatal' => 'Riwayat Pre Natal',
        'riwayat_natal' => 'Riwayat Natal',
        'riwayat_postnatal' => 'Riwayat Post Natal',
        'riwayat_nicu_picu' => 'Riwayat NICU/PICU',
        'riwayat_penyerta' => 'Riwayat Penyerta',
        'riwayat_imunisasi' => 'Riwayat Imunisasi',
    ];
    $basicExamFields = [
        'inspeksi_statis' => 'Inspeksi Statis',
        'inspeksi_dinamis' => 'Inspeksi Dinamis',
        'palpasi' => 'Palpasi',
        'perkusi' => 'Perkusi',
        'auskultasi' => 'Auskultasi',
    ];
    $vitalSignFields = [
        'nadi' => ['label' => 'Nadi (bpm)', 'type' => 'number', 'inputmode' => 'numeric', 'min' => '20', 'max' => '250', 'step' => '1', 'placeholder' => '100'],
        'suhu' => ['label' => 'Suhu (&deg;C)', 'type' => 'number', 'inputmode' => 'decimal', 'min' => '30', 'max' => '45', 'step' => '0.1', 'placeholder' => '36.5'],
        'tensi' => ['label' => 'Tensi (mmHg)', 'type' => 'text', 'inputmode' => 'numeric', 'pattern' => '[0-9]{2,3}/[0-9]{2,3}', 'placeholder' => '100/70', 'title' => 'Format: 100/70'],
        'frekuensi_nafas' => ['label' => 'Frekuensi Nafas (x/mnt)', 'type' => 'number', 'inputmode' => 'numeric', 'min' => '5', 'max' => '80', 'step' => '1', 'placeholder' => '24'],
        'berat_badan' => ['label' => 'Berat Badan (kg)', 'type' => 'number', 'inputmode' => 'decimal', 'min' => '1', 'max' => '500', 'step' => '0.1', 'placeholder' => '18'],
        'tinggi_badan' => ['label' => 'Tinggi Badan (cm)', 'type' => 'number', 'inputmode' => 'decimal', 'min' => '30', 'max' => '250', 'step' => '0.1', 'placeholder' => '105'],
    ];
    $consciousnessOptions = [
        [
            'compos_mentis' => 'Compos mentis',
            'apatis' => 'Apatis',
            'somnolen' => 'Somnolen',
        ],
        [
            'sopor' => 'Sopor',
            'sopor_coma' => 'Sopor Coma',
            'coma' => 'Coma',
        ],
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
            <div class="font-semibold text-blue-950">2. Rekam Medis Pediatri</div>
            <div class="text-blue-700">3. Review & Simpan</div>
        </div>
    </div>

    <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-blue-600">Blangko Pediatri</p>
            <h2 class="mt-2 text-2xl font-bold text-slate-900 sm:text-3xl">{{ $pageTitle }}</h2>
            <p class="mt-2 text-sm text-slate-500">Form ini mengikuti blangko penatalaksanaan fisioterapi pediatri.</p>
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

    <section id="section-identitas" class="scroll-mt-24 rounded-3xl bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:p-6">
        <h3 class="text-lg font-semibold text-blue-900">Identitas</h3>
        <div class="mt-5 grid gap-4 md:grid-cols-2">
            <div>
                <label for="examined_at" class="mb-2 block text-sm font-medium text-slate-700">Tanggal Pemeriksaan</label>
                <input id="examined_at" name="examined_at" type="date" value="{{ old('examined_at', optional($record->examined_at)->format('Y-m-d') ?? now()->toDateString()) }}" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200" required>
                <x-field-error :messages="$errors->get('examined_at')" />
            </div>
            <div>
                <label for="jadwal_terapis" class="mb-2 block text-sm font-medium text-slate-700">Jadwal Terapis</label>
                <input id="jadwal_terapis" name="jadwal_terapis" type="text" value="{{ old('jadwal_terapis', $record->jadwal_terapis ?? '') }}" placeholder="Opsional, contoh: Senin 14.00" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                <x-field-error :messages="$errors->get('jadwal_terapis')" />
            </div>
            <div>
                <label for="pediatric_data_nama_ibu_ayah" class="mb-2 block text-sm font-medium text-slate-700">Nama Ibu/Ayah</label>
                <input id="pediatric_data_nama_ibu_ayah" name="pediatric_data[nama_ibu_ayah]" type="text" value="{{ old('pediatric_data.nama_ibu_ayah', data_get($pediatric, 'nama_ibu_ayah', '')) }}" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                <x-field-error :messages="$errors->get('pediatric_data.nama_ibu_ayah')" />
            </div>

            @foreach ([
                'Nama Anak' => $patient->nama,
                'No. RM' => $patient->no_rm,
                'Tanggal Lahir Anak' => $patient->tanggal_lahir->translatedFormat('d F Y'),
                'Umur Anak' => ($record->patient_age_at_visit ?? $patient->umur).' tahun',
            ] as $label => $value)
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-slate-400">{{ $label }}</p>
                    <p class="mt-2 font-semibold text-slate-900">{{ $value }}</p>
                </div>
            @endforeach

            <div>
                <label for="pediatric_data_umur_ibu" class="mb-2 block text-sm font-medium text-slate-700">Umur Ibu</label>
                <input id="pediatric_data_umur_ibu" name="pediatric_data[umur_ibu]" type="number" min="0" max="120" value="{{ old('pediatric_data.umur_ibu', data_get($pediatric, 'umur_ibu', '')) }}" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                <x-field-error :messages="$errors->get('pediatric_data.umur_ibu')" />
            </div>
            <div>
                <label for="pediatric_data_umur_ayah" class="mb-2 block text-sm font-medium text-slate-700">Umur Ayah</label>
                <input id="pediatric_data_umur_ayah" name="pediatric_data[umur_ayah]" type="number" min="0" max="120" value="{{ old('pediatric_data.umur_ayah', data_get($pediatric, 'umur_ayah', '')) }}" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                <x-field-error :messages="$errors->get('pediatric_data.umur_ayah')" />
            </div>
            <div>
                <label for="pediatric_data_diagnosis_medis" class="mb-2 block text-sm font-medium text-slate-700">Diagnosis Medis</label>
                <input id="pediatric_data_diagnosis_medis" name="pediatric_data[diagnosis_medis]" type="text" value="{{ old('pediatric_data.diagnosis_medis', data_get($pediatric, 'diagnosis_medis', '')) }}" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                <x-field-error :messages="$errors->get('pediatric_data.diagnosis_medis')" />
            </div>
            <div>
                <label for="pediatric_data_icd" class="mb-2 block text-sm font-medium text-slate-700">ICD</label>
                <input id="pediatric_data_icd" name="pediatric_data[icd]" type="text" value="{{ old('pediatric_data.icd', data_get($pediatric, 'icd', '')) }}" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                <x-field-error :messages="$errors->get('pediatric_data.icd')" />
            </div>
            <div class="md:col-span-2">
                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Alamat</p>
                <p class="mt-2 font-semibold text-slate-900">{{ $patient->alamat ?: '-' }}</p>
            </div>
        </div>
    </section>

    <section id="section-anamnesis" class="scroll-mt-24 rounded-3xl bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:p-6">
        <h3 class="text-lg font-semibold text-blue-900">Anamnesis</h3>
        <div class="mt-5 grid gap-4 lg:grid-cols-2">
            <div class="lg:col-span-2">
                <label for="keluhan_utama" class="mb-2 block text-sm font-medium text-slate-700">Keluhan Utama</label>
                <textarea id="keluhan_utama" name="keluhan_utama" rows="3" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200" required>{{ old('keluhan_utama', $record->keluhan_utama ?? '') }}</textarea>
                <x-field-error :messages="$errors->get('keluhan_utama')" />
            </div>
            @foreach ($pediatricTextareas as $field => $label)
                <div>
                    <label for="pediatric_data_{{ $field }}" class="mb-2 block text-sm font-medium text-slate-700">{{ $label }}</label>
                    <textarea id="pediatric_data_{{ $field }}" name="pediatric_data[{{ $field }}]" rows="3" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200">{{ old("pediatric_data.$field", data_get($pediatric, $field, '')) }}</textarea>
                    <x-field-error :messages="$errors->get("pediatric_data.$field")" />
                </div>
            @endforeach
            @foreach ([
                'riwayat_penyakit_keluarga' => 'Riwayat Keluarga',
                'riwayat_alergi' => 'Riwayat Alergi',
                'riwayat_penggunaan_obat' => 'Riwayat Penggunaan Obat',
            ] as $field => $label)
                <div>
                    <label for="{{ $field }}" class="mb-2 block text-sm font-medium text-slate-700">{{ $label }}</label>
                    <textarea id="{{ $field }}" name="{{ $field }}" rows="3" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200">{{ old($field, $record->{$field} ?? '') }}</textarea>
                    <x-field-error :messages="$errors->get($field)" />
                </div>
            @endforeach
        </div>
    </section>

    <section id="section-dasar" class="scroll-mt-24 rounded-3xl bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:p-6">
        <h3 class="text-lg font-semibold text-blue-900">Pemeriksaan Dasar FT</h3>
        <div class="mt-5 grid gap-4 lg:grid-cols-2">
            <div>
                <label for="pediatric_data_inspeksi_kesadaran_umum" class="mb-2 block text-sm font-medium text-slate-700">Inspeksi Kesadaran Umum</label>
                <input id="pediatric_data_inspeksi_kesadaran_umum" name="pediatric_data[inspeksi_kesadaran_umum]" type="text" value="{{ old('pediatric_data.inspeksi_kesadaran_umum', data_get($pediatric, 'inspeksi_kesadaran_umum', '')) }}" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                <x-field-error :messages="$errors->get('pediatric_data.inspeksi_kesadaran_umum')" />
            </div>
            @foreach ($basicExamFields as $field => $label)
                <div>
                    <label for="{{ $field }}" class="mb-2 block text-sm font-medium text-slate-700">{{ $label }}</label>
                    <input id="{{ $field }}" name="{{ $field }}" type="text" value="{{ old($field, $record->{$field} ?? '') }}" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                    <x-field-error :messages="$errors->get($field)" />
                </div>
            @endforeach
            <div>
                <label for="pediatric_data_pemeriksaan_gerak_dasar" class="mb-2 block text-sm font-medium text-slate-700">Pemeriksaan Gerak Dasar</label>
                <textarea id="pediatric_data_pemeriksaan_gerak_dasar" name="pediatric_data[pemeriksaan_gerak_dasar]" rows="3" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200">{{ old('pediatric_data.pemeriksaan_gerak_dasar', data_get($pediatric, 'pemeriksaan_gerak_dasar', '')) }}</textarea>
                <x-field-error :messages="$errors->get('pediatric_data.pemeriksaan_gerak_dasar')" />
            </div>
            <div>
                <label for="pemeriksaan_kognitif" class="mb-2 block text-sm font-medium text-slate-700">Pemeriksaan Kognitif</label>
                <textarea id="pemeriksaan_kognitif" name="pemeriksaan_kognitif" rows="3" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200">{{ old('pemeriksaan_kognitif', $record->pemeriksaan_kognitif ?? '') }}</textarea>
                <x-field-error :messages="$errors->get('pemeriksaan_kognitif')" />
            </div>
            <div class="lg:col-span-2">
                <label for="hasil_penunjang" class="mb-2 block text-sm font-medium text-slate-700">Hasil Pemeriksaan Penunjang (Rontgen/MRI/EMG/CT-Scan/Lab)</label>
                <textarea id="hasil_penunjang" name="hasil_penunjang" rows="3" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200">{{ old('hasil_penunjang', $record->hasil_penunjang ?? '') }}</textarea>
                <x-field-error :messages="$errors->get('hasil_penunjang')" />
            </div>
            <div class="lg:col-span-2">
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

    <section id="section-vital" class="scroll-mt-24 rounded-3xl bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:p-6">
        <h3 class="text-lg font-semibold text-blue-900">Vital Sign dan Tingkat Kesadaran</h3>
        <div class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
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
            <div>
                <label for="pediatric_data_lingkar_kepala" class="mb-2 block text-sm font-medium text-slate-700">Lingkar Kepala (cm)</label>
                <input id="pediatric_data_lingkar_kepala" name="pediatric_data[lingkar_kepala]" type="number" min="20" max="80" step="0.1" inputmode="decimal" value="{{ old('pediatric_data.lingkar_kepala', data_get($pediatric, 'lingkar_kepala', '')) }}" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                <x-field-error :messages="$errors->get('pediatric_data.lingkar_kepala')" />
            </div>
        </div>
        <div class="mt-6 rounded-2xl border border-slate-200 bg-white p-4">
            <p class="text-sm font-bold uppercase tracking-wide text-slate-900">Tingkat Kesadaran <span class="normal-case tracking-normal text-slate-500">(centang salah satu)</span></p>
            <div class="mt-4 grid gap-x-8 gap-y-3 sm:grid-cols-2">
                @foreach ($consciousnessOptions as $column)
                    <div class="space-y-3">
                        @foreach ($column as $value => $label)
                            <label class="flex min-h-11 items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700 transition has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50 has-[:checked]:text-blue-900">
                                <input name="pediatric_data[tingkat_kesadaran]" type="radio" value="{{ $value }}" @checked(old('pediatric_data.tingkat_kesadaran', data_get($pediatric, 'tingkat_kesadaran')) === $value) class="h-4 w-4 border-slate-300 text-blue-600 focus:ring-blue-500">
                                <span>{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                @endforeach
            </div>
            <x-field-error :messages="$errors->get('pediatric_data.tingkat_kesadaran')" />
        </div>
    </section>

    <section id="section-khusus" class="scroll-mt-24 rounded-3xl bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:p-6">
        <h3 class="text-lg font-semibold text-blue-900">Pemeriksaan Khusus</h3>
        <div class="mt-5 grid gap-4 lg:grid-cols-2">
            <div>
                <label for="pediatric_data_pemeriksaan_khusus" class="mb-2 block text-sm font-medium text-slate-700">Pemeriksaan Khusus (neuro, muskulo, kardiopulmo, kardiorespirasi, dll)</label>
                <textarea id="pediatric_data_pemeriksaan_khusus" name="pediatric_data[pemeriksaan_khusus]" rows="5" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200">{{ old('pediatric_data.pemeriksaan_khusus', data_get($pediatric, 'pemeriksaan_khusus', '')) }}</textarea>
                <x-field-error :messages="$errors->get('pediatric_data.pemeriksaan_khusus')" />
            </div>
            <div>
                <label for="pemeriksaan_khusus_lain" class="mb-2 block text-sm font-medium text-slate-700">Pemeriksaan Khusus Lain (DDST, GMFM, Asworth Scale, dll)</label>
                <textarea id="pemeriksaan_khusus_lain" name="pemeriksaan_khusus_lain" rows="5" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200">{{ old('pemeriksaan_khusus_lain', $record->pemeriksaan_khusus_lain ?? '') }}</textarea>
                <x-field-error :messages="$errors->get('pemeriksaan_khusus_lain')" />
            </div>
        </div>
    </section>

    <div class="grid gap-6 lg:grid-cols-2">
        <section id="section-icf" class="scroll-mt-24 rounded-3xl bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:p-6">
            <h3 class="text-lg font-semibold text-blue-900">Kode dan Keterangan Pemeriksaan ICF</h3>
            <div class="mt-5 space-y-4">
                @foreach ([
                    'icf_body_functions' => 'Body Functions',
                    'icf_activities_participation' => 'Activities and Participation',
                    'icf_environmental_factors' => 'Environmental Factors',
                    'icf_body_structures' => 'Body Structures',
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
        <h3 class="text-lg font-semibold text-blue-900">Rencana Intervensi Fisioterapi</h3>
        <div class="mt-5 grid gap-4 md:grid-cols-2">
            @foreach (range(0, 3) as $index)
                <div>
                    <label for="rencana_intervensi_{{ $index }}" class="mb-2 block text-sm font-medium text-slate-700">Rencana {{ $index + 1 }}</label>
                    <input id="rencana_intervensi_{{ $index }}" name="rencana_intervensi[]" type="text" value="{{ $currentPlan[$index] ?? '' }}" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                    <x-field-error :messages="$errors->get('rencana_intervensi.'.$index)" />
                </div>
            @endforeach
        </div>
    </section>

    <section id="section-intervensi" class="scroll-mt-24 overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-slate-200">
        <div class="flex flex-col gap-4 border-b border-slate-200 px-4 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div>
                <h3 class="text-lg font-semibold text-blue-900">Pelaksanaan & Evaluasi Intervensi Fisioterapi</h3>
                <p class="mt-1 text-sm text-slate-500">Catat tanggal, intervensi, hasil evaluasi, dan paraf fisioterapis.</p>
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

    <section class="scroll-mt-24 rounded-3xl bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:p-6">
        <h3 class="text-lg font-semibold text-blue-900">Fisioterapis</h3>
        <div class="mt-5">
            <label for="pediatric_data_fisioterapis" class="mb-2 block text-sm font-medium text-slate-700">Nama Fisioterapis</label>
            <input id="pediatric_data_fisioterapis" name="pediatric_data[fisioterapis]" type="text" value="{{ old('pediatric_data.fisioterapis', data_get($pediatric, 'fisioterapis', '')) }}" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
            <x-field-error :messages="$errors->get('pediatric_data.fisioterapis')" />
        </div>
    </section>

    <div class="sticky bottom-0 z-20 -mx-5 border-t border-slate-200 bg-white/95 px-5 py-4 shadow-[0_-12px_30px_rgba(15,23,42,0.08)] backdrop-blur sm:-mx-8 sm:px-8">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-sm text-slate-600">Pastikan data pediatri sudah sesuai sebelum disimpan.</p>
            <div class="grid gap-3 sm:flex sm:flex-wrap">
                <button type="submit" class="rounded-xl bg-[#2563eb] px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-700">
                    Simpan Rekam Medis
                </button>
            </div>
        </div>
    </div>
</form>
