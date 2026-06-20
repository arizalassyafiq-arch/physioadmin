<x-layouts.app :title="'Detail Pasien | PhysioAdmin'" :header="'Detail Pasien'">
    <div class="space-y-6">
        <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <div class="flex flex-col gap-5 xl:flex-row xl:items-start xl:justify-between">
                <div class="grid flex-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
                    <div>
                        <p class="text-xs uppercase tracking-[0.22em] text-slate-400">Nama</p>
                        <p class="mt-2 font-semibold text-slate-900">{{ $patient->nama }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.22em] text-slate-400">No. RM</p>
                        <p class="mt-2 font-semibold text-slate-900">{{ $patient->no_rm }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.22em] text-slate-400">Kategori Pasien</p>
                        <p class="mt-2 font-semibold text-slate-900">{{ $patient->categoryLabel() }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.22em] text-slate-400">Jenis Kelamin</p>
                        <p class="mt-2 font-semibold text-slate-900">{{ $patient->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.22em] text-slate-400">Tanggal Lahir</p>
                        <p class="mt-2 font-semibold text-slate-900">{{ $patient->tanggal_lahir->translatedFormat('d F Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.22em] text-slate-400">Umur</p>
                        <p class="mt-2 font-semibold text-slate-900">{{ $patient->umur }} tahun</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.22em] text-slate-400">Pekerjaan</p>
                        <p class="mt-2 font-semibold text-slate-900">{{ $patient->pekerjaan ?: '-' }}</p>
                    </div>
                    <div class="md:col-span-2 xl:col-span-3">
                        <p class="text-xs uppercase tracking-[0.22em] text-slate-400">Alamat</p>
                        <p class="mt-2 font-semibold text-slate-900">{{ $patient->alamat ?: '-' }}</p>
                    </div>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('records.create', $patient) }}" class="rounded-xl border border-blue-200 bg-blue-50 px-5 py-3 text-sm font-semibold text-blue-700 transition hover:bg-blue-100">Tambah Rekam Medis</a>
                    <a href="{{ route('patients.edit', $patient) }}" class="rounded-xl border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Edit Pasien</a>
                    <form method="POST" action="{{ route('patients.destroy', $patient, false) }}" onsubmit="return confirm('Hapus data pasien ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="rounded-xl border border-red-200 px-5 py-3 text-sm font-semibold text-red-600 transition hover:bg-red-50">Hapus</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Daftar Rekam Medis</h2>
                    <p class="text-sm text-slate-500">Seluruh histori assessment dan intervensi pasien.</p>
                </div>
                <a href="{{ route('records.create', $patient) }}" class="rounded-xl border border-blue-200 bg-blue-50 px-4 py-2 text-sm font-semibold text-blue-700 transition hover:bg-blue-100">Tambah RM</a>
            </div>

            <div class="mt-5 grid gap-3 md:grid-cols-2">
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs uppercase tracking-[0.22em] text-slate-400">Kunjungan Tahun Ini</p>
                    <p class="mt-2 text-lg font-semibold text-slate-900">
                        {{ $visitSummary['current_year_count'] }} kali
                    </p>
                    <p class="mt-1 text-xs text-slate-500">Tahun {{ $visitSummary['current_year'] }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs uppercase tracking-[0.22em] text-slate-400">Tanggal Kedatangan Pertama</p>
                    <p class="mt-2 text-lg font-semibold text-slate-900">
                        {{ $visitSummary['first_visit_at'] ? \Illuminate\Support\Carbon::parse($visitSummary['first_visit_at'])->translatedFormat('d M Y') : '-' }}
                    </p>
                </div>
            </div>

            <div class="mt-5 overflow-x-auto rounded-2xl border border-slate-200">
                <table class="min-w-[860px] divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-slate-500">
                        <tr>
                            <th class="px-4 py-3 font-medium">No</th>
                            <th class="px-4 py-3 font-medium">Tanggal</th>
                            <th class="px-4 py-3 font-medium">Keluhan Utama</th>
                            <th class="px-4 py-3 font-medium">Intervensi</th>
                            <th class="px-4 py-3 font-medium">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @forelse ($patient->medicalRecords as $record)
                            <tr>
                                <td class="px-4 py-3 font-semibold text-slate-900">{{ $loop->iteration }}.</td>
                                <td class="px-4 py-3">{{ optional($record->examined_at)->translatedFormat('d M Y') ?? $record->created_at->translatedFormat('d M Y') }}</td>
                                <td class="px-4 py-3">{{ \Illuminate\Support\Str::limit($record->keluhan_utama, 80) }}</td>
                                <td class="px-4 py-3">Intervensi {{ $loop->iteration }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap gap-3">
                                        <a href="{{ route('records.show', $record) }}" class="font-semibold text-blue-600 hover:text-blue-700">Lihat</a>
                                        <a href="{{ route('records.edit', $record) }}" class="font-semibold text-slate-600 hover:text-slate-900">Edit</a>
                                        <a href="{{ route('records.pdf', $record) }}" class="font-semibold text-emerald-600 hover:text-emerald-700">PDF</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-12 text-center">
                                    <p class="font-semibold text-slate-800">Belum ada rekam medis untuk pasien ini.</p>
                                    <p class="mt-1 text-sm text-slate-500">Tambahkan assessment pertama agar histori klinis mulai tercatat.</p>
                                    <a href="{{ route('records.create', $patient) }}" class="mt-4 inline-flex rounded-xl bg-[#2563eb] px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700">Isi Rekam Medis</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>
