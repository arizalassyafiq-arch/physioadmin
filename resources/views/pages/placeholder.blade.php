<x-layouts.app :title="$title . ' | PhysioAdmin'" :header="$title">
    <div class="rounded-3xl bg-white p-8 shadow-sm ring-1 ring-slate-200">
        <p class="text-xs font-bold uppercase tracking-[0.22em] text-amber-600">Fitur belum aktif</p>
        <h2 class="mt-3 text-xl font-semibold text-slate-900">{{ $title }}</h2>
        <p class="mt-3 max-w-2xl text-sm leading-7 text-slate-600">{{ $description }}</p>
        <div class="mt-6 flex flex-wrap gap-3">
            <a href="{{ route('dashboard') }}" class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                Kembali ke Dashboard
            </a>
            <a href="{{ route('patients.index') }}" class="rounded-xl bg-[#2563eb] px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700">
                Buka Riwayat Rekam
            </a>
        </div>
    </div>
</x-layouts.app>
