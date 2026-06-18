<?php

namespace App\Http\Controllers;

use App\Actions\Patients\DeletePatientAction;
use App\Actions\Patients\StorePatientAction;
use App\Actions\Patients\UpdatePatientAction;
use App\Http\Requests\PatientRequest;
use App\Models\Patient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PatientController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Patient::class);

        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'jenis_kelamin' => ['nullable', 'in:L,P'],
            'min_age' => ['nullable', 'integer', 'min:0', 'max:150'],
            'max_age' => ['nullable', 'integer', 'min:0', 'max:150', 'gte:min_age'],
            'latest_visit_from' => ['nullable', 'date'],
            'latest_visit_to' => ['nullable', 'date', 'after_or_equal:latest_visit_from'],
        ]);

        $filters = array_merge([
            'search' => '',
            'jenis_kelamin' => '',
            'min_age' => '',
            'max_age' => '',
            'latest_visit_from' => '',
            'latest_visit_to' => '',
        ], $filters);
        $filters = array_map(fn ($value) => $value ?? '', $filters);

        $search = trim((string) $filters['search']);

        $patients = Patient::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery
                        ->where('nama', 'like', "%{$search}%")
                        ->orWhere('no_rm', 'like', "%{$search}%")
                        ->orWhere('alamat', 'like', "%{$search}%")
                        ->orWhere('pekerjaan', 'like', "%{$search}%");
                });
            })
            ->when($filters['jenis_kelamin'] !== '', fn ($query) => $query->where('jenis_kelamin', $filters['jenis_kelamin']))
            ->when($filters['min_age'] !== '', fn ($query) => $query->where('umur', '>=', $filters['min_age']))
            ->when($filters['max_age'] !== '', fn ($query) => $query->where('umur', '<=', $filters['max_age']))
            ->when($filters['latest_visit_from'] !== '', function ($query) use ($filters) {
                $query->where($this->latestVisitDateSubquery(), '>=', $filters['latest_visit_from']);
            })
            ->when($filters['latest_visit_to'] !== '', function ($query) use ($filters) {
                $query->where($this->latestVisitDateSubquery(), '<=', $filters['latest_visit_to']);
            })
            ->with(['latestMedicalRecord'])
            ->withCount('medicalRecords')
            ->withMax('medicalRecords as latest_visit_at', 'examined_at')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('pages.patients.index', compact('patients', 'filters', 'search'));
    }

    protected function latestVisitDateSubquery(): \Closure
    {
        return function ($query): void {
            $query
                ->selectRaw('max(examined_at)')
                ->from('medical_records')
                ->whereColumn('medical_records.patient_id', 'patients.id')
                ->whereNull('medical_records.deleted_at');
        };
    }

    public function create(): View
    {
        Gate::authorize('create', Patient::class);

        $patient = new Patient(['kategori_pasien' => 'dewasa']);

        return view('pages.patients.create', [
            'patient' => $patient,
        ]);
    }

    public function store(PatientRequest $request, StorePatientAction $storePatient): RedirectResponse
    {
        Gate::authorize('create', Patient::class);

        $patient = $storePatient->execute($request->validated());

        return redirect()
            ->route('records.create', $patient)
            ->with('success', 'Identitas pasien berhasil disimpan permanen. Silakan lanjutkan rekam medis awal.');
    }

    public function show(Patient $patient): View
    {
        Gate::authorize('view', $patient);

        $patient->load(['medicalRecords' => fn ($query) => $query->latest()->withCount('interventions')]);

        return view('pages.patients.show', compact('patient'));
    }

    public function edit(Patient $patient): View
    {
        Gate::authorize('update', $patient);

        return view('pages.patients.edit', compact('patient'));
    }

    public function update(PatientRequest $request, Patient $patient, UpdatePatientAction $updatePatient): RedirectResponse
    {
        Gate::authorize('update', $patient);

        $updatePatient->execute($patient, $request->validated());

        return redirect()
            ->route('patients.show', $patient)
            ->with('success', 'Data pasien berhasil diperbarui.');
    }

    public function destroy(Patient $patient, DeletePatientAction $deletePatient): RedirectResponse
    {
        Gate::authorize('delete', $patient);

        $deletePatient->execute($patient);

        return redirect()
            ->route('patients.index')
            ->with('success', 'Data pasien berhasil diarsipkan.');
    }
}
