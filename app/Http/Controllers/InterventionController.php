<?php

namespace App\Http\Controllers;

use App\Actions\Interventions\DeleteInterventionAction;
use App\Actions\Interventions\StoreInterventionAction;
use App\Actions\Interventions\UpdateInterventionAction;
use App\Models\Intervention;
use App\Models\MedicalRecord;
use App\Support\DateInput;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class InterventionController extends Controller
{
    public function store(Request $request, MedicalRecord $record, StoreInterventionAction $storeIntervention): RedirectResponse
    {
        Gate::authorize('create', Intervention::class);

        $this->normalizeDate($request);

        $validated = $request->validate([
            'tgl' => ['required', 'date'],
            'intervensi' => ['required', 'string'],
            'keluhan' => ['nullable', 'string'],
            'hasil_evaluasi' => ['nullable', 'string'],
            'paraf' => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        $storeIntervention->execute($request, $record, $validated);

        return back()->with('success', 'Intervensi berhasil ditambahkan.');
    }

    public function update(Request $request, Intervention $intervention, UpdateInterventionAction $updateIntervention): RedirectResponse
    {
        Gate::authorize('update', $intervention);

        $this->normalizeDate($request);

        $validated = $request->validate([
            'tgl' => ['required', 'date'],
            'intervensi' => ['required', 'string'],
            'keluhan' => ['nullable', 'string'],
            'hasil_evaluasi' => ['nullable', 'string'],
            'paraf' => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        $updateIntervention->execute($request, $intervention, $validated);

        return back()->with('success', 'Intervensi berhasil diperbarui.');
    }

    public function destroy(Intervention $intervention, DeleteInterventionAction $deleteIntervention): RedirectResponse
    {
        Gate::authorize('delete', $intervention);

        $deleteIntervention->execute($intervention);

        return back()->with('success', 'Intervensi berhasil diarsipkan.');
    }

    protected function normalizeDate(Request $request): void
    {
        $request->merge([
            'tgl' => DateInput::normalize($request->input('tgl')),
        ]);
    }
}
