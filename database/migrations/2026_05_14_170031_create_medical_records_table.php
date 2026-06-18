<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('medical_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->text('keluhan_utama')->nullable();
            $table->text('riwayat_penyakit_sekarang')->nullable();
            $table->text('riwayat_penyakit_dahulu')->nullable();
            $table->text('riwayat_penyakit_keluarga')->nullable();
            $table->text('riwayat_penggunaan_obat')->nullable();
            $table->text('riwayat_alergi')->nullable();
            $table->text('inspeksi_statis')->nullable();
            $table->text('inspeksi_dinamis')->nullable();
            $table->text('palpasi')->nullable();
            $table->text('perkusi')->nullable();
            $table->text('auskultasi')->nullable();
            $table->text('mmt')->nullable();
            $table->text('lingkup_gerak_sendi')->nullable();
            $table->text('antropometri')->nullable();
            $table->string('nadi')->nullable();
            $table->string('suhu')->nullable();
            $table->string('tensi')->nullable();
            $table->string('frekuensi_nafas')->nullable();
            $table->string('berat_badan')->nullable();
            $table->string('tinggi_badan')->nullable();
            $table->unsignedTinyInteger('nyeri_diam')->nullable();
            $table->unsignedTinyInteger('nyeri_tekan')->nullable();
            $table->unsignedTinyInteger('nyeri_gerak')->nullable();
            $table->text('faktor_pemberat')->nullable();
            $table->text('deskripsi_nyeri')->nullable();
            $table->string('waktu_onset_nyeri')->nullable();
            $table->text('hasil_penunjang')->nullable();
            $table->string('file_penunjang')->nullable();
            $table->text('pemeriksaan_kognitif')->nullable();
            $table->text('pemeriksaan_psikologi')->nullable();
            $table->text('pemeriksaan_khusus_lain')->nullable();
            $table->text('icf_body_structures')->nullable();
            $table->text('icf_body_functions')->nullable();
            $table->text('icf_activities_participation')->nullable();
            $table->text('icf_environmental_factors')->nullable();
            $table->text('diagnosa_impairment')->nullable();
            $table->text('diagnosa_functional_limitation')->nullable();
            $table->text('diagnosa_participation_restriction')->nullable();
            $table->json('rencana_intervensi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_records');
    }
};
