<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Rekam Medis {{ $record->patient->no_rm }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #1e293b; font-size: 12px; }
        .header, .section { margin-bottom: 20px; }
        .header-table, .data-table { width: 100%; border-collapse: collapse; }
        .data-table th, .data-table td { border: 1px solid #cbd5e1; padding: 8px; vertical-align: top; }
        .label { width: 26%; font-weight: bold; }
        h1, h2, h3 { margin: 0 0 8px; }
        .muted { color: #64748b; }
        .pill { display: inline-block; padding: 6px 10px; background: #eff6ff; border-radius: 999px; margin: 2px 0; }
    </style>
</head>
<body>
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

    <div class="header">
        <table class="header-table">
            <tr>
                <td>
                    <h1>Klinik Fisioterapi</h1>
                    <p class="muted">Sistem Rekam Medis Pasien</p>
                </td>
                <td style="text-align:right;">
                    <div style="display:inline-block; width:70px; height:70px; border:1px solid #94a3b8; text-align:center; line-height:70px;">LOGO</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h2>Identitas Pasien</h2>
        <table class="data-table">
            <tr><td class="label">Nama</td><td>{{ $record->patient->nama }}</td></tr>
            <tr><td class="label">No. RM</td><td>{{ $record->patient->no_rm }}</td></tr>
            <tr><td class="label">Kategori Pasien</td><td>{{ $record->patient->categoryLabel() }}</td></tr>
            <tr><td class="label">Jenis Kelamin</td><td>{{ $record->patient->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</td></tr>
            <tr><td class="label">Tanggal Lahir</td><td>{{ $record->patient->tanggal_lahir->translatedFormat('d F Y') }}</td></tr>
            <tr><td class="label">Tanggal Pemeriksaan</td><td>{{ optional($record->examined_at)->translatedFormat('d F Y') ?? '-' }}</td></tr>
            <tr><td class="label">Umur Saat Pemeriksaan</td><td>{{ $record->patient_age_at_visit ?? $record->patient->umur }} tahun</td></tr>
            <tr><td class="label">Pekerjaan</td><td>{{ $record->patient->pekerjaan ?: '-' }}</td></tr>
            <tr><td class="label">Alamat</td><td>{{ $record->patient->alamat ?: '-' }}</td></tr>
            @if ($isPediatric)
                <tr><td class="label">Nama Ibu/Ayah</td><td>{{ data_get($pediatricData, 'nama_ibu_ayah', '-') ?: '-' }}</td></tr>
                <tr><td class="label">Umur Ibu</td><td>{{ data_get($pediatricData, 'umur_ibu', '-') ?: '-' }}</td></tr>
                <tr><td class="label">Umur Ayah</td><td>{{ data_get($pediatricData, 'umur_ayah', '-') ?: '-' }}</td></tr>
                <tr><td class="label">Diagnosis Medis</td><td>{{ data_get($pediatricData, 'diagnosis_medis', '-') ?: '-' }}</td></tr>
                <tr><td class="label">ICD</td><td>{{ data_get($pediatricData, 'icd', '-') ?: '-' }}</td></tr>
            @endif
        </table>
    </div>

    @if ($isPediatric)
        @foreach ([
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
                'Lingkar Kepala' => data_get($pediatricData, 'lingkar_kepala'),
                'Tingkat Kesadaran' => $consciousnessLabels[data_get($pediatricData, 'tingkat_kesadaran')] ?? null,
                'Pemeriksaan Khusus' => data_get($pediatricData, 'pemeriksaan_khusus'),
                'Fisioterapis' => data_get($pediatricData, 'fisioterapis'),
            ],
        ] as $title => $items)
            <div class="section">
                <h2>{{ $title }}</h2>
                <table class="data-table">
                    @foreach ($items as $label => $value)
                        <tr>
                            <td class="label">{{ $label }}</td>
                            <td>{{ $value ?: '-' }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
        @endforeach
    @endif

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
        'Vital Sign' => [
            'Nadi' => $record->nadi,
            'Suhu' => $record->suhu,
            'Tensi' => $record->tensi,
            'Frekuensi Nafas' => $record->frekuensi_nafas,
            'Berat Badan' => $record->berat_badan,
            'Tinggi Badan' => $record->tinggi_badan,
        ],
        'Pemeriksaan Nyeri' => [
            'Nyeri Diam' => $record->nyeri_diam,
            'Nyeri Tekan' => $record->nyeri_tekan,
            'Nyeri Gerak' => $record->nyeri_gerak,
            'Faktor Pemberat/Peringan' => $record->faktor_pemberat,
            'Deskripsi Nyeri' => $record->deskripsi_nyeri,
            'Waktu / Onset Nyeri' => $record->waktu_onset_nyeri,
        ],
        'Pemeriksaan Tambahan' => [
            'Hasil Penunjang' => $record->hasil_penunjang,
            'Pemeriksaan Kognitif' => $record->pemeriksaan_kognitif,
            'Pemeriksaan Psikologi' => $record->pemeriksaan_psikologi,
            'Pemeriksaan Khusus Lain' => $record->pemeriksaan_khusus_lain,
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
    ] as $title => $items)
        <div class="section">
            <h2>{{ $title }}</h2>
            <table class="data-table">
                @foreach ($items as $label => $value)
                    <tr>
                        <td class="label">{{ $label }}</td>
                        <td>{{ $value ?: '-' }}</td>
                    </tr>
                @endforeach
            </table>
        </div>
    @endforeach

    <div class="section">
        <h2>Rencana Intervensi</h2>
        @forelse ($record->rencana_intervensi ?? [] as $plan)
            <div class="pill">{{ $plan }}</div>
        @empty
            <p>-</p>
        @endforelse
    </div>

    <div class="section">
        <h2>Pelaksanaan dan Evaluasi Intervensi</h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Intervensi</th>
                    <th>Hasil Evaluasi</th>
                    <th>Paraf</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($record->interventions as $intervention)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ optional($intervention->tgl)->translatedFormat('d M Y') }}</td>
                        <td>{{ $intervention->intervensi }}</td>
                        <td>{{ $intervention->hasil_evaluasi ?: '-' }}</td>
                        <td>{{ $intervention->paraf ? 'Tersedia' : '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">Belum ada intervensi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>
</html>
