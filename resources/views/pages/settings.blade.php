<x-layouts.app :title="'Pengaturan | PhysioAdmin'" :header="'Pengaturan'">
    <style>
        .settings-logout-button {
            display: inline-flex;
            width: 100%;
            align-items: center;
            justify-content: center;
            border-radius: 0.75rem;
            background: #dc2626;
            padding: 0.875rem 1.25rem;
            color: #ffffff;
            font-size: 0.875rem;
            font-weight: 700;
            transition: background-color 0.2s ease, box-shadow 0.2s ease;
        }

        .settings-logout-button:hover {
            background: #b91c1c;
            box-shadow: 0 10px 24px rgba(185, 28, 28, 0.18);
        }
    </style>

    <div class="space-y-6">
        <section class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.22em] text-blue-600">Akun Sistem</p>
                    <h2 class="mt-3 text-2xl font-bold text-slate-900">Pengaturan Akun</h2>
                    <p class="mt-2 max-w-2xl text-sm leading-7 text-slate-600">
                        Kelola sesi login aktif dan keluar dari aplikasi dengan aman setelah selesai menggunakan sistem.
                    </p>
                </div>
            </div>
        </section>

        <section class="grid gap-6 lg:grid-cols-[1fr_0.9fr]">
            <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <h3 class="text-lg font-semibold text-blue-900">Informasi Pengguna</h3>
                <div class="mt-5 grid gap-4 sm:grid-cols-2">
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Nama</p>
                        <p class="mt-2 font-semibold text-slate-900">{{ auth()->user()->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Email</p>
                        <p class="mt-2 font-semibold text-slate-900">{{ auth()->user()->email }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Role</p>
                        <p class="mt-2 font-semibold text-slate-900">{{ ucfirst(auth()->user()->role) }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Status Sesi</p>
                        <p class="mt-2 font-semibold text-emerald-700">Aktif</p>
                    </div>
                </div>
            </div>

            <div class="rounded-3xl border border-red-100 bg-red-50 p-6">
                <h3 class="text-lg font-semibold text-red-900">Keluar dari Sistem</h3>
                <p class="mt-3 text-sm leading-7 text-red-800">
                    Gunakan tombol ini untuk mengakhiri sesi login pada perangkat ini. Setelah keluar, Anda perlu login kembali untuk mengakses dashboard.
                </p>

                <form method="POST" action="{{ route('logout') }}" class="mt-6">
                    @csrf
                    <button type="submit" class="settings-logout-button">
                        Keluar dari Sistem
                    </button>
                </form>
            </div>
        </section>
    </div>
</x-layouts.app>
