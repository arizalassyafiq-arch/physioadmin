<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\PatientControlSchedule;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PatientControlScheduleController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', Patient::class);

        $patients = Patient::query()
            ->orderBy('nama')
            ->get(['id', 'nama', 'no_rm']);

        $schedules = PatientControlSchedule::query()
            ->with('patient')
            ->orderByRaw("case when status = 'scheduled' then 0 else 1 end")
            ->orderBy('scheduled_date')
            ->latest('created_at')
            ->paginate(12);

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

        return view('pages.schedule', compact('patients', 'schedules', 'summary'));
    }

    public function store(Request $request, AuditLogger $auditLogger): RedirectResponse
    {
        Gate::authorize('create', Patient::class);

        $payload = $request->validate([
            'patient_id' => ['required', Rule::exists('patients', 'id')->whereNull('deleted_at')],
            'control_number' => ['required', 'integer', 'min:2', 'max:99'],
            'scheduled_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ], [], [
            'patient_id' => 'pasien',
            'control_number' => 'kontrol ke',
            'scheduled_date' => 'tanggal kontrol',
            'notes' => 'catatan',
        ]);

        $schedule = PatientControlSchedule::create([
            ...$payload,
            'status' => 'scheduled',
        ]);

        $auditLogger->record('patient_control_schedule.created', $schedule);

        return redirect()
            ->route('schedule')
            ->with('success', 'Jadwal kontrol pasien berhasil ditambahkan.');
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
}
