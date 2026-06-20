<x-layouts.app :title="'Edit Pasien | PhysioAdmin'" :header="'Edit Pasien'">
    <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-8">
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-slate-900">Perbarui Data Pasien</h2>
            <p class="mt-1 text-sm text-slate-500">Sesuaikan identitas pasien bila ada perubahan.</p>
        </div>

        <form method="POST" action="{{ route('patients.update', $patient, false) }}" class="space-y-6">
            @csrf
            @method('PUT')
            @include('pages.patients._form', ['patient' => $patient])

            <div class="flex justify-end">
                <button type="submit" class="rounded-xl bg-[#2563eb] px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-700">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</x-layouts.app>
