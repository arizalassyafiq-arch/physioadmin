<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MedicalRecord extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'patient_id',
        'examined_at',
        'patient_age_at_visit',
        'pediatric_data',
        'keluhan_utama',
        'riwayat_penyakit_sekarang',
        'riwayat_penyakit_dahulu',
        'riwayat_penyakit_keluarga',
        'riwayat_penggunaan_obat',
        'riwayat_alergi',
        'inspeksi_statis',
        'inspeksi_dinamis',
        'palpasi',
        'perkusi',
        'auskultasi',
        'mmt',
        'lingkup_gerak_sendi',
        'antropometri',
        'nadi',
        'suhu',
        'tensi',
        'frekuensi_nafas',
        'berat_badan',
        'tinggi_badan',
        'nyeri_diam',
        'nyeri_tekan',
        'nyeri_gerak',
        'faktor_pemberat',
        'deskripsi_nyeri',
        'waktu_onset_nyeri',
        'hasil_penunjang',
        'file_penunjang',
        'pemeriksaan_kognitif',
        'pemeriksaan_psikologi',
        'pemeriksaan_khusus_lain',
        'icf_body_structures',
        'icf_body_functions',
        'icf_activities_participation',
        'icf_environmental_factors',
        'diagnosa_impairment',
        'diagnosa_functional_limitation',
        'diagnosa_participation_restriction',
        'rencana_intervensi',
    ];

    protected function casts(): array
    {
        return [
            'examined_at' => 'date',
            'pediatric_data' => 'array',
            'rencana_intervensi' => 'array',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function interventions(): HasMany
    {
        return $this->hasMany(Intervention::class)->orderBy('tgl');
    }
}
