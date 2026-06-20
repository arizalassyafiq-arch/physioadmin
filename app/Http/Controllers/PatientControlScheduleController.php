<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\PatientControlSchedule;
use App\Services\AuditLogger;
use App\Support\DateInput;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PatientControlScheduleController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Patient::class);

        $this->normalizeDates($request, ['date_from', 'date_to']);

        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'status' => ['nullable', Rule::in(['scheduled', 'today', 'upcoming', 'overdue', 'completed'])],
        ]);
        $filters = array_merge([
            'search' => '',
            'date_from' => '',
            'date_to' => '',
            'status' => '',
        ], $filters);
        $filters = array_map(fn ($value) => $value ?? '', $filters);
        $search = trim((string) $filters['search']);

        $patients = Patient::query()
            ->orderBy('nama')
            ->get(['id', 'nama', 'no_rm']);

        $schedules = PatientControlSchedule::query()
            ->with('patient')
            ->when($search !== '', function ($query) use ($search) {
                $query->whereHas('patient', function ($patientQuery) use ($search) {
                    $patientQuery
                        ->where('nama', 'like', "%{$search}%")
                        ->orWhere('no_rm', 'like', "%{$search}%");
                });
            })
            ->when($filters['date_from'] !== '', fn ($query) => $query->whereDate('scheduled_date', '>=', $filters['date_from']))
            ->when($filters['date_to'] !== '', fn ($query) => $query->whereDate('scheduled_date', '<=', $filters['date_to']))
            ->when($filters['status'] !== '', fn ($query) => $this->applyStatusFilter($query, $filters['status']))
            ->orderByRaw("case when status = 'scheduled' then 0 else 1 end")
            ->orderBy('scheduled_date')
            ->latest('created_at')
            ->paginate(12)
            ->withQueryString();

        $summary = [
            'today' => PatientControlSchedule::query()
                ->where('status', 'scheduled')
                ->whereDate('scheduled_date', today())
                ->count(),
            'upcoming' => PatientControlSchedule::query()
                ->where('status', 'scheduled')
                ->whereDate('scheduled_date', '>', today())
                ->count(),
            'overdue' => PatientControlSchedule::query()
                ->where('status', 'scheduled')
                ->whereDate('scheduled_date', '<', today())
                ->count(),
        ];

        return view('pages.schedule', compact('patients', 'schedules', 'summary', 'filters', 'search'));
    }

    public function store(Request $request, AuditLogger $auditLogger): RedirectResponse
    {
        Gate::authorize('create', Patient::class);

        $payload = $this->validatedPayload($request);

        $schedule = PatientControlSchedule::create([
            ...$payload,
            'status' => 'scheduled',
        ]);

        $auditLogger->record('patient_control_schedule.created', $schedule);

        return redirect()
            ->route('schedule')
            ->with('success', 'Jadwal kontrol pasien berhasil ditambahkan.');
    }

    public function edit(PatientControlSchedule $patientControlSchedule): View
    {
        Gate::authorize('update', $patientControlSchedule->patient);

        $patients = Patient::query()
            ->orderBy('nama')
            ->get(['id', 'nama', 'no_rm']);

        return view('pages.schedule-edit', [
            'schedule' => $patientControlSchedule->load('patient'),
            'patients' => $patients,
        ]);
    }

    public function update(
        Request $request,
        PatientControlSchedule $patientControlSchedule,
        AuditLogger $auditLogger,
    ): RedirectResponse {
        Gate::authorize('update', $patientControlSchedule->patient);

        $payload = $this->validatedPayload($request, includeStatus: true);

        $patientControlSchedule->update($payload);
        $auditLogger->record('patient_control_schedule.updated', $patientControlSchedule);

        return redirect()
            ->route('schedule')
            ->with('success', 'Jadwal kontrol pasien berhasil diperbarui.');
    }

    public function complete(PatientControlSchedule $patientControlSchedule, AuditLogger $auditLogger): RedirectResponse
    {
        Gate::authorize('update', $patientControlSchedule->patient);

        $patientControlSchedule->update(['status' => 'completed']);
        $auditLogger->record('patient_control_schedule.completed', $patientControlSchedule);

        return redirect()
            ->route('schedule')
            ->with('success', 'Jadwal kontrol ditandai selesai.');
    }

    public function destroy(PatientControlSchedule $patientControlSchedule, AuditLogger $auditLogger): RedirectResponse
    {
        Gate::authorize('delete', $patientControlSchedule->patient);

        $patientControlSchedule->delete();
        $auditLogger->record('patient_control_schedule.deleted', $patientControlSchedule);

        return redirect()
            ->route('schedule')
            ->with('success', 'Jadwal kontrol berhasil dihapus.');
    }

    protected function validatedPayload(Request $request, bool $includeStatus = false): array
    {
        $this->normalizeDates($request, ['scheduled_date']);

        $rules = [
            'patient_id' => ['required', Rule::exists('patients', 'id')->whereNull('deleted_at')],
            'control_number' => ['required', 'integer', 'min:2', 'max:99'],
            'scheduled_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];

        if ($includeStatus) {
            $rules['status'] = ['required', Rule::in(['scheduled', 'completed'])];
        }

        return $request->validate($rules, [], [
            'patient_id' => 'pasien',
            'control_number' => 'kontrol ke',
            'scheduled_date' => 'tanggal kontrol',
            'status' => 'status',
            'notes' => 'catatan',
        ]);
    }

    protected function normalizeDates(Request $request, array $fields): void
    {
        $normalized = [];

        foreach ($fields as $field) {
            $normalized[$field] = DateInput::normalize($request->input($field));
        }

        $request->merge($normalized);
    }

    protected function applyStatusFilter($query, string $status)
    {
        return match ($status) {
            'today' => $query->where('status', 'scheduled')->whereDate('scheduled_date', today()),
            'upcoming' => $query->where('status', 'scheduled')->whereDate('scheduled_date', '>', today()),
            'overdue' => $query->where('status', 'scheduled')->whereDate('scheduled_date', '<', today()),
            default => $query->where('status', $status),
        };
    }
}
