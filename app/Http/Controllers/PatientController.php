<?php

namespace App\Http\Controllers;

use App\Actions\Patients\DeletePatientAction;
use App\Actions\Patients\BulkDeletePatientsByVisitPeriodAction;
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

        $filters = $this->validatedIndexFilters($request);
        $search = trim((string) $filters['search']);

        $patients = $this->patientIndexQuery($filters)
            ->with(['latestMedicalRecord'])
            ->withCount('medicalRecords')
            ->withMax('medicalRecords as latest_visit_at', 'examined_at')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('pages.patients.index', compact('patients', 'filters', 'search'));
    }

    public function bulkDestroyByVisitPeriod(
        Request $request,
        BulkDeletePatientsByVisitPeriodAction $bulkDeletePatients,
    ): RedirectResponse {
        Gate::authorize('deleteAny', Patient::class);

        $filters = $this->validatedIndexFilters($request, requireVisitPeriod: true);
        $deletedCount = $bulkDeletePatients->execute($this->patientIndexQuery($filters));

        return redirect()
            ->route('patients.index')
            ->with('success', "{$deletedCount} pasien pada periode kunjungan terpilih berhasil diarsipkan.");
    }

    protected function validatedIndexFilters(Request $request, bool $requireVisitPeriod = false): array
    {
        $visitPeriodRule = $requireVisitPeriod ? 'required' : 'nullable';

        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'jenis_kelamin' => ['nullable', 'in:L,P'],
            'min_age' => ['nullable', 'integer', 'min:0', 'max:150'],
            'max_age' => ['nullable', 'integer', 'min:0', 'max:150', 'gte:min_age'],
            'visit_month' => [$visitPeriodRule, 'integer', 'min:1', 'max:12'],
            'visit_year' => [$visitPeriodRule, 'integer', 'min:1900', 'max:2100'],
        ]);

        $filters = array_merge([
            'search' => '',
            'jenis_kelamin' => '',
            'min_age' => '',
            'max_age' => '',
            'visit_month' => '',
            'visit_year' => '',
        ], $filters);

        return array_map(fn ($value) => $value ?? '', $filters);
    }

    protected function patientIndexQuery(array $filters)
    {
        $search = trim((string) $filters['search']);

        return Patient::query()
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
            ->when($filters['visit_month'] !== '' || $filters['visit_year'] !== '', function ($query) use ($filters) {
                $query->whereHas('medicalRecords', function ($recordQuery) use ($filters) {
                    if ($filters['visit_month'] !== '') {
                        $recordQuery->whereMonth('examined_at', (int) $filters['visit_month']);
                    }

                    if ($filters['visit_year'] !== '') {
                        $recordQuery->whereYear('examined_at', (int) $filters['visit_year']);
                    }
                });
            });
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

        $patient->load([
            'medicalRecords' => fn ($query) => $query
                ->orderBy('examined_at')
                ->orderBy('created_at'),
        ]);

        $currentYear = now()->year;
        $visitSummary = [
            'current_year' => $currentYear,
            'current_year_count' => $patient->medicalRecords()
                ->whereYear('examined_at', $currentYear)
                ->count(),
            'first_visit_at' => $patient->medicalRecords()
                ->oldest('examined_at')
                ->value('examined_at'),
        ];

        return view('pages.patients.show', compact('patient', 'visitSummary'));
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
