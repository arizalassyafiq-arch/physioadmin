<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('medical_records', function (Blueprint $table) {
            $table->date('examined_at')->nullable()->after('patient_id');
            $table->unsignedInteger('patient_age_at_visit')->nullable()->after('examined_at');
            $table->softDeletes();
        });

        Schema::table('interventions', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('interventions', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('medical_records', function (Blueprint $table) {
            $table->dropColumn(['examined_at', 'patient_age_at_visit']);
            $table->dropSoftDeletes();
        });

        Schema::table('patients', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
