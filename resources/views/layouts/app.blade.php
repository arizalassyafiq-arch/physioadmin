<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'PhysioAdmin' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-800">
    <div class="flex min-h-screen">
        <aside class="hidden w-72 flex-col bg-[#1e3a5f] text-white lg:flex">
            <div class="border-b border-white/10 px-8 py-8">
                <p class="text-2xl font-bold tracking-tight">PhysioAdmin</p>
                <p class="mt-1 text-sm text-blue-100">Klinik Fisioterapi</p>
            </div>
            <nav class="flex-1 space-y-1 px-5 py-6">
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'bg-white/10 text-white' : 'text-blue-100 hover:bg-white/5 hover:text-white' }} flex items-center rounded-xl px-4 py-3 text-sm font-medium">
                    Dashboard
                </a>
                <a href="{{ route('patients.create') }}" class="{{ request()->routeIs('patients.create') || request()->routeIs('records.create') ? 'bg-white/10 text-white' : 'text-blue-100 hover:bg-white/5 hover:text-white' }} flex items-center rounded-xl px-4 py-3 text-sm font-medium">
                    Pasien Baru
                </a>
                <a href="{{ route('patients.index') }}" class="{{ request()->routeIs('patients.index') || request()->routeIs('patients.show') || request()->routeIs('patients.edit') || request()->routeIs('records.show') || request()->routeIs('records.edit') ? 'bg-white/10 text-white' : 'text-blue-100 hover:bg-white/5 hover:text-white' }} flex items-center rounded-xl px-4 py-3 text-sm font-medium">
                    Riwayat Rekam
                </a>
                <a href="{{ route('schedule') }}" class="{{ request()->routeIs('schedule') || request()->routeIs('schedule.*') ? 'bg-white/10 text-white' : 'text-blue-100 hover:bg-white/5 hover:text-white' }} flex items-center rounded-xl px-4 py-3 text-sm font-medium">
                    Jadwal Pasien
                </a>
                <a href="{{ route('reports') }}" class="{{ request()->routeIs('reports') ? 'bg-white/10 text-white' : 'text-blue-100 hover:bg-white/5 hover:text-white' }} flex items-center rounded-xl px-4 py-3 text-sm font-medium">
                    Laporan
                </a>
                <a href="{{ route('settings') }}" class="{{ request()->routeIs('settings') ? 'bg-white/10 text-white' : 'text-blue-100 hover:bg-white/5 hover:text-white' }} flex items-center rounded-xl px-4 py-3 text-sm font-medium">
                    Pengaturan
                </a>
            </nav>
            <div class="border-t border-white/10 p-5">
                <form method="POST" action="{{ route('logout', [], false) }}">
                    @csrf
                    <button type="submit" class="flex w-full items-center rounded-xl px-4 py-3 text-left text-sm font-medium text-red-200 transition hover:bg-red-500/10 hover:text-white">
                        Keluar
                    </button>
                </form>
            </div>
        </aside>

        <div class="flex min-h-screen flex-1 flex-col">
            <header class="border-b border-slate-200 bg-white">
                <div class="flex items-center justify-between gap-4 px-5 py-4 sm:px-8">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.28em] text-blue-700">Sistem Manajemen Rekam Medis</p>
                        <h1 class="mt-1 text-lg font-semibold text-slate-900">{{ $header ?? 'PhysioAdmin' }}</h1>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium text-slate-900">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-slate-500">{{ auth()->user()->email }}</p>
                    </div>
                </div>
            </header>

            <main class="flex-1 px-5 py-6 sm:px-8">
                <x-flash-message />
                {{ $slot }}
            </main>
        </div>
    </div>

    <script>
        window.formatDateFromIso = (value) => {
            const match = String(value || '').match(/^(\d{4})-(\d{2})-(\d{2})$/);
            return match ? `${match[2]}/${match[3]}/${match[1]}` : '';
        };

        window.toIsoDate = (value) => {
            const displayMatch = String(value || '').trim().match(/^(\d{2})\/(\d{2})\/(\d{4})$/);
            if (!displayMatch) {
                return String(value || '').match(/^\d{4}-\d{2}-\d{2}$/) ? value : '';
            }

            const [, month, day, year] = displayMatch;
            const date = new Date(Number(year), Number(month) - 1, Number(day));

            if (
                date.getFullYear() !== Number(year)
                || date.getMonth() !== Number(month) - 1
                || date.getDate() !== Number(day)
            ) {
                return '';
            }

            return `${year}-${month}-${day}`;
        };

        window.dateInputField = (initialValue = '') => ({
            display: initialValue || '',
            nativeValue: '',
            init() {
                this.nativeValue = window.toIsoDate(this.display);
            },
            syncNative() {
                this.nativeValue = window.toIsoDate(this.display);
                this.$dispatch('date-input-changed', { value: this.display });
            },
            fromNative() {
                this.display = window.formatDateFromIso(this.nativeValue);
                this.$dispatch('date-input-changed', { value: this.display });
            },
            openPicker() {
                this.nativeValue = window.toIsoDate(this.display);
                this.$refs.native.value = this.nativeValue;

                if (this.$refs.native.showPicker) {
                    this.$refs.native.showPicker();
                    return;
                }

                this.$refs.native.click();
            }
        });

        window.patientForm = (initialBirthDate = '', fallbackAge = '') => ({
            umur: '',
            init() {
                this.syncAge(initialBirthDate, fallbackAge);
            },
            get ageLabel() {
                return this.umur === '' ? 'Pilih tanggal lahir' : `${this.umur} tahun`;
            },
            syncAge(value, fallbackAge = '') {
                if (!value) {
                    this.umur = fallbackAge;
                    return;
                }

                const birthDate = this.parseLocalDate(value);

                if (Number.isNaN(birthDate.getTime())) {
                    this.umur = fallbackAge;
                    return;
                }

                const today = new Date();
                let age = today.getFullYear() - birthDate.getFullYear();
                const monthDiff = today.getMonth() - birthDate.getMonth();

                if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                    age--;
                }

                this.umur = age >= 0 ? age : '';
            },
            parseLocalDate(value) {
                const match = String(value).trim().match(/^(\d{2})\/(\d{2})\/(\d{4})$/);

                if (!match) {
                    return new Date(value);
                }

                return new Date(Number(match[3]), Number(match[1]) - 1, Number(match[2]));
            }
        });

        window.recordForm = (rows) => ({
            rows: (rows.length ? rows : [
                { id: null, tgl: '', intervensi: '', keluhan: '', hasil_evaluasi: '', paraf_url: null },
                { id: null, tgl: '', intervensi: '', keluhan: '', hasil_evaluasi: '', paraf_url: null },
                { id: null, tgl: '', intervensi: '', keluhan: '', hasil_evaluasi: '', paraf_url: null },
            ]).map((row, index) => ({
                uid: row.id ? `saved-${row.id}` : `new-${Date.now()}-${index}`,
                ...row,
            })),
            addRow() {
                this.rows.push({
                    uid: `new-${Date.now()}-${Math.random().toString(16).slice(2)}`,
                    id: null,
                    tgl: '',
                    intervensi: '',
                    keluhan: '',
                    hasil_evaluasi: '',
                    paraf_url: null,
                });
            },
            removeRow(index) {
                this.rows.splice(index, 1);
            }
        });
    </script>
</body>
</html>
