<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InterventionController;
use App\Http\Controllers\MedicalFileController;
use App\Http\Controllers\PatientControlScheduleController;
use App\Http\Controllers\MedicalRecordController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect('/login'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/forgot-password', [LoginController::class, 'showForgotPassword'])->name('password.request');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/patients', [PatientController::class, 'index'])->name('patients.index');
    Route::get('/patients/create', [PatientController::class, 'create'])->name('patients.create');
    Route::post('/patients', [PatientController::class, 'store'])->name('patients.store');
    Route::get('/patients/{patient}', [PatientController::class, 'show'])->name('patients.show');
    Route::get('/patients/{patient}/edit', [PatientController::class, 'edit'])->name('patients.edit');
    Route::put('/patients/{patient}', [PatientController::class, 'update'])->name('patients.update');
    Route::delete('/patients/{patient}', [PatientController::class, 'destroy'])->name('patients.destroy');

    Route::get('/patients/{patient}/records/create', [MedicalRecordController::class, 'create'])->name('records.create');
    Route::post('/patients/{patient}/records', [MedicalRecordController::class, 'store'])->name('records.store');
    Route::get('/records/{record}', [MedicalRecordController::class, 'show'])->name('records.show');
    Route::get('/records/{record}/edit', [MedicalRecordController::class, 'edit'])->name('records.edit');
    Route::put('/records/{record}', [MedicalRecordController::class, 'update'])->name('records.update');
    Route::delete('/records/{record}', [MedicalRecordController::class, 'destroy'])->name('records.destroy');
    Route::get('/records/{record}/pdf', [MedicalRecordController::class, 'exportPdf'])->name('records.pdf');
    Route::get('/records/{record}/file', [MedicalFileController::class, 'supportingDocument'])->name('records.file');

    Route::post('/records/{record}/interventions', [InterventionController::class, 'store'])->name('interventions.store');
    Route::put('/interventions/{intervention}', [InterventionController::class, 'update'])->name('interventions.update');
    Route::delete('/interventions/{intervention}', [InterventionController::class, 'destroy'])->name('interventions.destroy');
    Route::get('/interventions/{intervention}/signature', [MedicalFileController::class, 'signature'])->name('interventions.signature');

    Route::get('/jadwal-pasien', [PatientControlScheduleController::class, 'index'])->name('schedule');
    Route::post('/jadwal-pasien', [PatientControlScheduleController::class, 'store'])->name('schedule.store');
    Route::patch('/jadwal-pasien/{patientControlSchedule}/selesai', [PatientControlScheduleController::class, 'complete'])->name('schedule.complete');
    Route::delete('/jadwal-pasien/{patientControlSchedule}', [PatientControlScheduleController::class, 'destroy'])->name('schedule.destroy');

    Route::get('/laporan', [ReportController::class, 'index'])->name('reports');
    Route::get('/laporan/pdf', [ReportController::class, 'exportPdf'])->name('reports.pdf');

    Route::view('/pengaturan', 'pages.settings')->name('settings');

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});
