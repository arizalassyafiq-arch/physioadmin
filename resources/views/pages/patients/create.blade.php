<x-layouts.app :title="'Pasien Baru | PhysioAdmin'" :header="'Pasien Baru'">
    <div class="mb-6 grid gap-3 rounded-2xl border border-blue-100 bg-blue-50 p-4 text-sm text-blue-900 md:grid-cols-3">
        <div class="font-semibold">1. Identitas Pasien</div>
        <div class="text-blue-700">2. Rekam Medis Awal</div>
        <div class="text-blue-700">3. Review & Simpan</div>
    </div>

    <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-8">
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-slate-900">Tambah Data Pasien</h2>
            <p class="mt-1 text-sm text-slate-500">Identitas pasien langsung disimpan permanen setelah tombol Simpan Identitas diklik.</p>
        </div>

        <form method="POST" action="{{ route('patients.store') }}" class="space-y-6">
            @csrf
            @include('pages.patients._form', ['patient' => $patient])

            <div class="flex justify-end">
                <button type="submit" class="rounded-xl bg-[#2563eb] px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-700">
                    Simpan Identitas
                </button>
            </div>
        </form>
    </div>
</x-layouts.app>
