<x-layouts.app :title="'Edit Jadwal Pasien | PhysioAdmin'" :header="'Edit Jadwal Pasien'">
    <div class="space-y-6">
        <section class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.22em] text-blue-600">Reschedule Kontrol</p>
                    <h2 class="mt-3 text-2xl font-bold text-slate-900">Edit Jadwal Kontrol</h2>
                    <p class="mt-2 text-sm text-slate-600">
                        Ubah tanggal kontrol, nomor kontrol, status, atau catatan jadwal pasien.
                    </p>
                </div>
                <a href="{{ route('schedule') }}" class="rounded-xl border border-slate-300 px-5 py-3 text-center text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                    Kembali
                </a>
            </div>
        </section>

        <section class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <form method="POST" action="{{ route('schedule.update', $schedule, false) }}" class="grid gap-5 lg:grid-cols-2">
                @csrf
                @method('PUT')

                <div class="lg:col-span-2">
                    <label for="patient_id" class="mb-2 block text-sm font-medium text-slate-700">Pasien</label>
                    <select id="patient_id" name="patient_id" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200" required>
                        <option value="">Pilih pasien</option>
                        @foreach ($patients as $patient)
                            <option value="{{ $patient->id }}" @selected((string) old('patient_id', $schedule->patient_id) === (string) $patient->id)>
                                {{ $patient->nama }} - {{ $patient->no_rm }}
                            </option>
                        @endforeach
                    </select>
                    <x-field-error :messages="$errors->get('patient_id')" />
                </div>

                <div>
                    <label for="control_number" class="mb-2 block text-sm font-medium text-slate-700">Kontrol Ke</label>
                    <input id="control_number" name="control_number" type="number" min="2" max="99" value="{{ old('control_number', $schedule->control_number) }}" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200" required>
                    <x-field-error :messages="$errors->get('control_number')" />
                </div>

                <div>
                    <label for="scheduled_date" class="mb-2 block text-sm font-medium text-slate-700">Tanggal Kontrol</label>
                    <x-date-input id="scheduled_date" name="scheduled_date" :value="old('scheduled_date', $schedule->scheduled_date)" required />
                    <x-field-error :messages="$errors->get('scheduled_date')" />
                </div>

                <div>
                    <label for="status" class="mb-2 block text-sm font-medium text-slate-700">Status</label>
                    <select id="status" name="status" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200" required>
                        <option value="scheduled" @selected(old('status', $schedule->status) === 'scheduled')>Terjadwal</option>
                        <option value="completed" @selected(old('status', $schedule->status) === 'completed')>Selesai</option>
                    </select>
                    <x-field-error :messages="$errors->get('status')" />
                </div>

                <div>
                    <label for="notes" class="mb-2 block text-sm font-medium text-slate-700">Catatan</label>
                    <input id="notes" name="notes" type="text" value="{{ old('notes', $schedule->notes) }}" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                    <x-field-error :messages="$errors->get('notes')" />
                </div>

                <div class="flex justify-end gap-3 lg:col-span-2">
                    <a href="{{ route('schedule') }}" class="rounded-xl border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                        Batal
                    </a>
                    <button type="submit" class="rounded-xl bg-[#2563eb] px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-700">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </section>
    </div>
</x-layouts.app>
