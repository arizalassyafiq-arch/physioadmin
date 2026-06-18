<x-layouts.app :title="'Daftar Pasien | PhysioAdmin'" :header="'Riwayat Rekam'">
    <div class="space-y-6">
        <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <form
                method="GET"
                action="{{ route('patients.index') }}"
                x-data="{
                    timer: null,
                    debounceSubmit() {
                        clearTimeout(this.timer);
                        this.timer = setTimeout(() => this.$refs.filters.requestSubmit(), 450);
                    },
                }"
                x-ref="filters"
                class="space-y-4"
            >
                <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                    <div>
                    <h2 class="text-xl font-semibold text-slate-900">Daftar Pasien</h2>
                        <p class="mt-1 text-sm text-slate-500">Cari pasien berdasarkan nama, No. RM, alamat, atau pekerjaan.</p>
                    </div>
                    <div class="flex w-full flex-col gap-3 sm:flex-row md:max-w-xl">
                        <input type="text" name="search" value="{{ $filters['search'] }}" placeholder="Cari pasien" @input="debounceSubmit()" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                        <button type="submit" class="rounded-xl bg-[#2563eb] px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-700 sm:w-auto">
                            Cari
                        </button>
                    </div>
                </div>

                <div class="grid gap-3 md:grid-cols-6">
                    <div>
                        <label for="jenis_kelamin" class="mb-2 block text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Jenis Kelamin</label>
                        <select id="jenis_kelamin" name="jenis_kelamin" @change="$refs.filters.requestSubmit()" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                            <option value="">Semua</option>
                            <option value="L" @selected($filters['jenis_kelamin'] === 'L')>Laki-laki</option>
                            <option value="P" @selected($filters['jenis_kelamin'] === 'P')>Perempuan</option>
                        </select>
                    </div>
                    <div>
                        <label for="min_age" class="mb-2 block text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Umur Min</label>
                        <input id="min_age" name="min_age" type="number" min="0" max="150" value="{{ $filters['min_age'] }}" placeholder="0" @input="debounceSubmit()" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                    </div>
                    <div>
                        <label for="max_age" class="mb-2 block text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Umur Max</label>
                        <input id="max_age" name="max_age" type="number" min="0" max="150" value="{{ $filters['max_age'] }}" placeholder="150" @input="debounceSubmit()" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                    </div>
                    <div>
                        <label for="latest_visit_from" class="mb-2 block text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Kunjungan Dari</label>
                        <input id="latest_visit_from" name="latest_visit_from" type="date" value="{{ $filters['latest_visit_from'] }}" @change="$refs.filters.requestSubmit()" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                    </div>
                    <div>
                        <label for="latest_visit_to" class="mb-2 block text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Kunjungan Sampai</label>
                        <input id="latest_visit_to" name="latest_visit_to" type="date" value="{{ $filters['latest_visit_to'] }}" @change="$refs.filters.requestSubmit()" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                    </div>
                    <div class="flex items-end">
                        <a href="{{ route('patients.index') }}" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-center text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto rounded-3xl bg-white shadow-sm ring-1 ring-slate-200">
            <table class="min-w-[1080px] divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-slate-500">
                    <tr>
                        <th class="px-5 py-4 font-medium">No. RM</th>
                        <th class="px-5 py-4 font-medium">Nama</th>
                        <th class="px-5 py-4 font-medium">Kategori</th>
                        <th class="px-5 py-4 font-medium">Jenis Kelamin</th>
                        <th class="px-5 py-4 font-medium">Umur</th>
                        <th class="px-5 py-4 font-medium">Pekerjaan</th>
                        <th class="px-5 py-4 font-medium">Kunjungan Terakhir</th>
                        <th class="px-5 py-4 font-medium">Status RM</th>
                        <th class="px-5 py-4 font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white">
                    @forelse ($patients as $patient)
                        <tr>
                            <td class="px-5 py-4 font-medium text-slate-900">{{ $patient->no_rm }}</td>
                            <td class="px-5 py-4">{{ $patient->nama }}</td>
                            <td class="px-5 py-4">{{ $patient->categoryLabel() }}</td>
                            <td class="px-5 py-4">{{ $patient->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                            <td class="px-5 py-4">{{ $patient->umur }} tahun</td>
                            <td class="px-5 py-4">{{ $patient->pekerjaan ?: '-' }}</td>
                            <td class="px-5 py-4">
                                {{ $patient->latest_visit_at ? \Illuminate\Support\Carbon::parse($patient->latest_visit_at)->translatedFormat('d M Y') : '-' }}
                            </td>
                            <td class="px-5 py-4">
                                @if ($patient->medical_records_count > 0)
                                    <span class="inline-flex rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                                        {{ $patient->medical_records_count }} rekam medis
                                    </span>
                                @else
                                    <span class="inline-flex rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700">
                                        Belum ada RM
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex flex-wrap items-center gap-2">
                                    @if ($patient->latestMedicalRecord)
                                        <a href="{{ route('records.show', $patient->latestMedicalRecord) }}" class="rounded-lg bg-[#2563eb] px-3 py-2 text-xs font-semibold text-white transition hover:bg-blue-700">Buka RM</a>
                                        <a href="{{ route('records.edit', $patient->latestMedicalRecord) }}" class="rounded-lg border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50">Edit RM</a>
                                    @else
                                        <a href="{{ route('records.create', $patient) }}" class="rounded-lg bg-emerald-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-emerald-700">Isi RM</a>
                                    @endif
                                    <a href="{{ route('patients.show', $patient) }}" class="rounded-lg border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50">Detail</a>
                                    <a href="{{ route('patients.edit', $patient) }}" class="rounded-lg border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50">Edit Pasien</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-5 py-12 text-center">
                                <p class="font-semibold text-slate-800">Belum ada data pasien.</p>
                                <p class="mt-1 text-sm text-slate-500">Mulai dari identitas pasien, lalu lengkapi rekam medis awal.</p>
                                <a href="{{ route('patients.create') }}" class="mt-4 inline-flex rounded-xl bg-[#2563eb] px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700">Tambah Pasien Baru</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div>
            {{ $patients->links() }}
        </div>
    </div>
</x-layouts.app>
