<x-layouts.app :title="'Jadwal Pasien | PhysioAdmin'" :header="'Jadwal Pasien'">
    <div class="space-y-6">
        <section class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.22em] text-blue-600">Kontrol Pasien</p>
                    <h2 class="mt-3 text-2xl font-bold text-slate-900">Jadwal Kontrol Pasien</h2>
                    <p class="mt-2 max-w-2xl text-sm leading-7 text-slate-600">
                        Catat jadwal kontrol lanjutan seperti kontrol kedua, ketiga, dan tanggal kontrol berikutnya.
                    </p>
                </div>

                <div class="grid gap-3 sm:grid-cols-3">
                    @foreach ([
                        ['label' => 'Hari Ini', 'value' => $summary['today'], 'tone' => 'bg-blue-50 text-blue-800 ring-blue-100'],
                        ['label' => 'Akan Datang', 'value' => $summary['upcoming'], 'tone' => 'bg-amber-50 text-amber-800 ring-amber-100'],
                        ['label' => 'Terlambat', 'value' => $summary['overdue'], 'tone' => 'bg-red-50 text-red-800 ring-red-100'],
                    ] as $card)
                        <div class="rounded-2xl px-5 py-4 ring-1 {{ $card['tone'] }}">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em]">{{ $card['label'] }}</p>
                            <p class="mt-2 text-2xl font-bold">{{ $card['value'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <h3 class="text-lg font-semibold text-blue-900">Tambah Jadwal Kontrol</h3>
            <form method="POST" action="{{ route('schedule.store') }}" class="mt-5 grid gap-4 lg:grid-cols-[1.5fr_0.7fr_0.9fr_1.2fr_auto] lg:items-end">
                @csrf
                <div>
                    <label for="patient_id" class="mb-2 block text-sm font-medium text-slate-700">Pasien</label>
                    <select id="patient_id" name="patient_id" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200" required>
                        <option value="">Pilih pasien</option>
                        @foreach ($patients as $patient)
                            <option value="{{ $patient->id }}" @selected((string) old('patient_id') === (string) $patient->id)>
                                {{ $patient->nama }} - {{ $patient->no_rm }}
                            </option>
                        @endforeach
                    </select>
                    <x-field-error :messages="$errors->get('patient_id')" />
                </div>
                <div>
                    <label for="control_number" class="mb-2 block text-sm font-medium text-slate-700">Kontrol Ke</label>
                    <input id="control_number" name="control_number" type="number" min="2" max="99" value="{{ old('control_number', 2) }}" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200" required>
                    <x-field-error :messages="$errors->get('control_number')" />
                </div>
                <div>
                    <label for="scheduled_date" class="mb-2 block text-sm font-medium text-slate-700">Tanggal Kontrol</label>
                    <x-date-input id="scheduled_date" name="scheduled_date" :value="old('scheduled_date')" required />
                    <x-field-error :messages="$errors->get('scheduled_date')" />
                </div>
                <div>
                    <label for="notes" class="mb-2 block text-sm font-medium text-slate-700">Catatan</label>
                    <input id="notes" name="notes" type="text" value="{{ old('notes') }}" placeholder="Contoh: evaluasi nyeri, latihan ulang" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                    <x-field-error :messages="$errors->get('notes')" />
                </div>
                <button type="submit" class="rounded-xl bg-[#2563eb] px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-700">
                    Simpan
                </button>
            </form>
        </section>

        <section class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <h3 class="text-lg font-semibold text-blue-900">Cari Jadwal Kontrol</h3>
            <form method="GET" action="{{ route('schedule') }}" class="mt-5 grid gap-4 lg:grid-cols-[1.3fr_0.9fr_0.9fr_0.9fr_auto_auto] lg:items-end">
                <div>
                    <label for="search" class="mb-2 block text-sm font-medium text-slate-700">Nama / No. RM</label>
                    <input id="search" name="search" type="search" value="{{ $search }}" placeholder="Cari pasien" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                </div>
                <div>
                    <label for="date_from" class="mb-2 block text-sm font-medium text-slate-700">Tanggal Dari</label>
                    <x-date-input id="date_from" name="date_from" :value="$filters['date_from']" />
                    <x-field-error :messages="$errors->get('date_from')" />
                </div>
                <div>
                    <label for="date_to" class="mb-2 block text-sm font-medium text-slate-700">Tanggal Sampai</label>
                    <x-date-input id="date_to" name="date_to" :value="$filters['date_to']" />
                    <x-field-error :messages="$errors->get('date_to')" />
                </div>
                <div>
                    <label for="status" class="mb-2 block text-sm font-medium text-slate-700">Status</label>
                    <select id="status" name="status" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                        <option value="">Semua</option>
                        @foreach ([
                            'scheduled' => 'Terjadwal',
                            'today' => 'Hari ini',
                            'upcoming' => 'Akan datang',
                            'overdue' => 'Terlambat',
                            'completed' => 'Selesai',
                        ] as $value => $label)
                            <option value="{{ $value }}" @selected($filters['status'] === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <x-field-error :messages="$errors->get('status')" />
                </div>
                <button type="submit" class="rounded-xl bg-[#2563eb] px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-700">
                    Cari
                </button>
                <a href="{{ route('schedule') }}" class="rounded-xl border border-slate-300 px-5 py-3 text-center text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                    Reset
                </a>
            </form>
        </section>

        <section class="overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-slate-200">
            <div class="border-b border-slate-200 px-6 py-5">
                <h3 class="text-lg font-semibold text-blue-900">Daftar Jadwal Kontrol</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-slate-500">
                        <tr>
                            <th class="px-5 py-3 font-medium">Tanggal</th>
                            <th class="px-5 py-3 font-medium">Pasien</th>
                            <th class="px-5 py-3 font-medium">No. RM</th>
                            <th class="px-5 py-3 font-medium">Kontrol</th>
                            <th class="px-5 py-3 font-medium">Status</th>
                            <th class="px-5 py-3 font-medium">Catatan</th>
                            <th class="px-5 py-3 font-medium">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @forelse ($schedules as $schedule)
                            <tr>
                                <td class="px-5 py-4 font-semibold text-slate-900">{{ $schedule->scheduled_date->translatedFormat('d M Y') }}</td>
                                <td class="px-5 py-4">{{ $schedule->patient->nama }}</td>
                                <td class="px-5 py-4">{{ $schedule->patient->no_rm }}</td>
                                <td class="px-5 py-4">Kontrol ke-{{ $schedule->control_number }}</td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $schedule->statusTone() }}">
                                        {{ $schedule->statusLabel() }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">{{ $schedule->notes ?: '-' }}</td>
                                <td class="px-5 py-4">
                                    <div class="flex flex-wrap gap-2">
                                        <a href="{{ route('patients.show', $schedule->patient) }}" class="rounded-lg border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50">
                                            Detail
                                        </a>
                                        <a href="{{ route('schedule.edit', $schedule) }}" class="rounded-lg border border-blue-200 px-3 py-2 text-xs font-semibold text-blue-700 transition hover:bg-blue-50">
                                            Edit
                                        </a>
                                        @if ($schedule->status !== 'completed')
                                            <form method="POST" action="{{ route('schedule.complete', $schedule) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="rounded-lg bg-emerald-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-emerald-700">
                                                    Selesai
                                                </button>
                                            </form>
                                        @endif
                                        <form method="POST" action="{{ route('schedule.destroy', $schedule) }}" onsubmit="return confirm('Hapus jadwal kontrol ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-lg border border-red-200 px-3 py-2 text-xs font-semibold text-red-600 transition hover:bg-red-50">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-12 text-center text-slate-500">
                                    Belum ada jadwal kontrol pasien.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($schedules->hasPages())
                <div class="border-t border-slate-200 px-6 py-4">
                    {{ $schedules->links() }}
                </div>
            @endif
        </section>
    </div>
</x-layouts.app>
