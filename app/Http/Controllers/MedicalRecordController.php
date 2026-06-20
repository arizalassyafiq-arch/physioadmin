<?php

namespace App\Http\Controllers;

use App\Actions\MedicalRecords\CreateMedicalRecordAction;
use App\Actions\MedicalRecords\DeleteMedicalRecordAction;
use App\Actions\MedicalRecords\UpdateMedicalRecordAction;
use App\Http\Requests\MedicalRecordRequest;
use App\Models\Intervention;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Services\Pdf\MedicalRecordPdfExporterFactory;
use App\Support\DateInput;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class MedicalRecordController extends Controller
{
    public function create(Patient $patient): View
    {
        Gate::authorize('create', MedicalRecord::class);

        return view('pages.records.create', [
            'patient' => $patient,
            'record' => new MedicalRecord([
                'examined_at' => now()->toDateString(),
                'patient_age_at_visit' => $patient->ageAt(),
                'rencana_intervensi' => [''],
            ]),
            'interventionRows' => $this->defaultInterventionRows(),
            'formAction' => route('records.store', $patient, false),
            'formMethod' => 'POST',
            'pageTitle' => 'Rekam Medis: Pasien Baru',
        ]);
    }

    public function store(
        MedicalRecordRequest $request,
        Patient $patient,
        CreateMedicalRecordAction $createMedicalRecord,
    ): RedirectResponse {
        Gate::authorize('create', MedicalRecord::class);

        $record = $createMedicalRecord->execute($request, $patient);

        return redirect()
            ->route('records.show', $record)
            ->with('success', 'Rekam medis berhasil disimpan.');
    }

    public function show(MedicalRecord $record): View
    {
        Gate::authorize('view', $record);

        $record->load(['patient', 'interventions']);

        return view('pages.records.show', compact('record'));
    }

    public function edit(MedicalRecord $record): View
    {
        Gate::authorize('update', $record);

        $record->load(['patient', 'interventions']);

        return view('pages.records.edit', [
            'patient' => $record->patient,
            'record' => $record,
            'interventionRows' => $this->interventionRowsFromRecord($record),
            'formAction' => route('records.update', $record, false),
            'formMethod' => 'PUT',
            'pageTitle' => 'Edit Rekam Medis',
        ]);
    }

    public function update(
        MedicalRecordRequest $request,
        MedicalRecord $record,
        UpdateMedicalRecordAction $updateMedicalRecord,
    ): RedirectResponse {
        Gate::authorize('update', $record);

        $updateMedicalRecord->execute($request, $record);

        return redirect()
            ->route('records.show', $record)
            ->with('success', 'Rekam medis berhasil diperbarui.');
    }

    public function destroy(MedicalRecord $record, DeleteMedicalRecordAction $deleteMedicalRecord): RedirectResponse
    {
        Gate::authorize('delete', $record);

        $patient = $record->patient;
        $deleteMedicalRecord->execute($record);

        return redirect()
            ->route('patients.show', $patient)
            ->with('success', 'Rekam medis berhasil diarsipkan.');
    }

    public function exportPdf(MedicalRecord $record, MedicalRecordPdfExporterFactory $exporters): Response
    {
        Gate::authorize('export', $record);

        $record->load(['patient', 'interventions']);

        return $exporters->make()->download($record);
    }

    protected function defaultInterventionRows(): array
    {
        return collect(range(1, 3))->map(fn () => [
            'id' => null,
            'tgl' => '',
            'intervensi' => '',
            'keluhan' => '',
            'hasil_evaluasi' => '',
            'paraf' => null,
            'paraf_url' => null,
        ])->all();
    }

    protected function interventionRowsFromRecord(MedicalRecord $record): array
    {
        $rows = $record->interventions->map(function (Intervention $intervention) {
            return [
                'id' => $intervention->id,
                'tgl' => DateInput::display($intervention->tgl),
                'intervensi' => $intervention->intervensi,
                'keluhan' => $intervention->keluhan,
                'hasil_evaluasi' => $intervention->hasil_evaluasi,
                'paraf' => $intervention->paraf,
                'paraf_url' => $intervention->paraf ? route('interventions.signature', $intervention) : null,
            ];
        })->values();

        while ($rows->count() < 3) {
            $rows->push([
                'id' => null,
                'tgl' => '',
                'intervensi' => '',
                'keluhan' => '',
                'hasil_evaluasi' => '',
                'paraf' => null,
                'paraf_url' => null,
            ]);
        }

        return $rows->all();
    }
}
