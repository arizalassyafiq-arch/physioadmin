@props([
    'title' => 'PhysioAdmin',
    'header' => 'PhysioAdmin',
])

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --app-bg: #f6f8fb;
            --surface: #ffffff;
            --surface-soft: #fbfcfe;
            --surface-muted: #eef3f8;
            --sidebar-bg: #111827;
            --sidebar-bg-soft: #172235;
            --sidebar-border: #253044;
            --sidebar-text: #dbe4f0;
            --sidebar-muted: #94a3b8;
            --primary: #2f66e8;
            --primary-hover: #2557cc;
            --primary-soft: #e8f0ff;
            --focus-ring: rgba(47, 102, 232, 0.22);
            --text: #172033;
            --text-soft: #334155;
            --muted: #6b7a90;
            --border: #d9e2ec;
            --success-bg: #edf8f3;
            --success-border: #b9e7d4;
            --success-text: #147a55;
            --warning-bg: #fff8e6;
            --warning-border: #f5dfa7;
            --warning-text: #946200;
            --danger-bg: #fff1f2;
            --danger-border: #fecdd3;
            --danger-text: #be123c;
            --shadow: 0 14px 30px rgba(23, 32, 51, 0.055);
            --shadow-soft: 0 8px 18px rgba(23, 32, 51, 0.045);
        }

        * {
            box-sizing: border-box;
        }

        [x-cloak] {
            display: none !important;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background: var(--app-bg);
            color: var(--text);
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        a {
            color: inherit;
        }

        .app-shell {
            min-height: 100vh;
            display: flex;
            background: var(--app-bg);
        }

        .app-sidebar {
            position: fixed;
            inset: 0 auto 0 0;
            z-index: 50;
            width: min(82vw, 252px);
            max-width: 252px;
            flex: 0 0 auto;
            background: var(--sidebar-bg);
            color: #fff;
            display: flex;
            flex-direction: column;
            transform: translateX(-100%);
            box-shadow: 24px 0 60px rgba(17, 24, 39, 0.2);
            transition: width .2s ease, flex-basis .2s ease, transform .2s ease;
        }

        .is-sidebar-open .app-sidebar {
            transform: translateX(0);
        }

        .app-brand {
            min-height: 64px;
            padding: 12px;
            border-bottom: 1px solid rgba(148, 163, 184, 0.16);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .app-brand-row {
            display: flex;
            min-width: 0;
            flex: 1;
            align-items: center;
            gap: 10px;
        }

        .app-brand-mark,
        .app-sidebar-toggle {
            display: inline-flex;
            width: 40px;
            height: 40px;
            flex: 0 0 40px;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            border: 1px solid rgba(148, 163, 184, 0.34);
            background: rgba(255, 255, 255, 0.035);
            color: var(--sidebar-text);
        }

        .app-brand-copy {
            min-width: 0;
        }

        .app-sidebar-close,
        .app-mobile-menu {
            width: 40px;
            height: 40px;
            border: 0;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background-color .2s ease, color .2s ease, border-color .2s ease;
        }

        .app-sidebar-toggle:hover,
        .app-sidebar-close:hover {
            background: rgba(255, 255, 255, 0.16);
            color: #fff;
        }

        .app-sidebar-close {
            display: inline-flex;
        }

        .app-brand-title {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
            line-height: 1.1;
        }

        .app-brand-subtitle {
            margin: 3px 0 0;
            color: var(--sidebar-muted);
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .app-nav {
            padding: 18px 10px;
            display: grid;
            gap: 8px;
        }

        .app-nav-divider {
            height: 1px;
            margin: 8px 0 14px;
            background: rgba(148, 163, 184, 0.16);
        }

        .app-nav-link,
        .app-logout-button {
            display: flex;
            align-items: center;
            gap: 12px;
            width: 100%;
            text-align: left;
            text-decoration: none;
            border: 0;
            border-radius: 9px;
            padding: 11px 12px;
            background: transparent;
            color: var(--sidebar-text);
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: background-color .2s ease, color .2s ease;
        }

        .app-nav-icon {
            display: inline-flex;
            width: 22px;
            height: 22px;
            flex: 0 0 22px;
            align-items: center;
            justify-content: center;
            color: var(--sidebar-muted);
        }

        .app-nav-icon svg,
        .app-sidebar-toggle svg,
        .app-mobile-menu svg {
            width: 20px;
            height: 20px;
            stroke-width: 2;
        }

        .app-nav-label {
            min-width: 0;
            white-space: nowrap;
        }

        .app-nav-link:hover,
        .app-logout-button:hover {
            background: rgba(148, 163, 184, 0.09);
            color: #fff;
        }

        .app-nav-link.is-active {
            background: var(--primary);
            box-shadow: 0 10px 20px rgba(47, 102, 232, 0.28);
            color: #fff;
        }

        .app-nav-link.is-active .app-nav-icon {
            color: #fff;
        }

        .app-sidebar-footer {
            margin-top: auto;
            padding: 14px 10px 16px;
            border-top: 1px solid rgba(148, 163, 184, 0.16);
        }

        .app-logout-button {
            color: #fb7185;
        }

        .app-logout-button .app-nav-icon {
            color: #fb7185;
        }

        .app-main {
            flex: 1;
            width: 100%;
            min-width: 0;
            display: flex;
            flex-direction: column;
        }

        .app-header {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            padding: 20px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .app-header-left {
            min-width: 0;
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .app-mobile-menu {
            display: inline-flex;
            width: 50px;
            height: 50px;
            flex: 0 0 50px;
            border: 1px solid var(--border);
            border-radius: 16px;
            background: #fff;
            color: var(--text-soft);
        }

        .app-mobile-menu:hover {
            border-color: #c9d7ea;
            color: var(--primary);
        }

        .app-header-copy {
            min-width: 0;
        }

        .app-header-eyebrow {
            margin: 0;
            color: var(--primary);
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.22em;
            text-transform: uppercase;
        }

        .app-header-title {
            margin: 8px 0 0;
            font-size: 22px;
            font-weight: 700;
            color: var(--text);
        }

        .app-user {
            text-align: right;
        }

        .app-user-name {
            margin: 0;
            font-size: 14px;
            font-weight: 600;
            color: var(--text);
        }

        .app-user-email {
            margin: 6px 0 0;
            color: var(--muted);
            font-size: 12px;
        }

        .app-content {
            flex: 1;
            padding: 28px 32px;
            min-width: 0;
        }

        .app-sidebar-backdrop {
            position: fixed;
            inset: 0;
            z-index: 40;
            border: 0;
            background: rgba(15, 23, 42, 0.44);
        }

        .flash-box {
            margin-bottom: 20px;
            border-radius: 16px;
            padding: 14px 16px;
            border: 1px solid;
            font-size: 14px;
            line-height: 1.6;
        }

        .flash-box.success {
            background: var(--success-bg);
            border-color: var(--success-border);
            color: var(--success-text);
        }

        .flash-box.error {
            background: var(--danger-bg);
            border-color: var(--danger-border);
            color: var(--danger-text);
        }

        .flash-box ul {
            margin: 10px 0 0;
            padding-left: 18px;
        }

        .app-content .bg-white {
            background-color: var(--surface);
        }

        .app-content .bg-slate-50 {
            background-color: var(--surface-soft);
        }

        .app-content .border-slate-200,
        .app-content .border-slate-300,
        .app-content .ring-slate-200 {
            border-color: var(--border);
            --tw-ring-color: var(--border);
        }

        .app-content .shadow-sm {
            --tw-shadow: var(--shadow-soft);
            --tw-shadow-colored: var(--shadow-soft);
            box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow);
        }

        .app-content .text-slate-900,
        .app-content .text-blue-900 {
            color: var(--text);
        }

        .app-content .text-slate-700 {
            color: var(--text-soft);
        }

        .app-content .text-slate-600,
        .app-content .text-slate-500,
        .app-content .text-slate-400 {
            color: var(--muted);
        }

        .app-content .text-blue-700,
        .app-content .text-blue-600 {
            color: var(--primary);
        }

        .app-content .bg-blue-50 {
            background-color: var(--primary-soft);
        }

        .app-content .border-blue-100,
        .app-content .border-blue-200 {
            border-color: #c9d7ea;
        }

        .app-content .bg-emerald-50,
        .app-content .bg-green-50 {
            background-color: var(--success-bg);
        }

        .app-content .text-emerald-700,
        .app-content .text-green-700 {
            color: var(--success-text);
        }

        .app-content .bg-amber-50 {
            background-color: var(--warning-bg);
        }

        .app-content .border-amber-200 {
            border-color: var(--warning-border);
        }

        .app-content .text-amber-700,
        .app-content .text-amber-900 {
            color: var(--warning-text);
        }

        .app-content .bg-red-50 {
            background-color: var(--danger-bg);
        }

        .app-content .border-red-100,
        .app-content .border-red-200 {
            border-color: var(--danger-border);
        }

        .app-content .text-red-600 {
            color: var(--danger-text);
        }

        .app-content input,
        .app-content select,
        .app-content textarea {
            background-color: #fff;
            border-color: #cfd9e6;
            color: var(--text);
        }

        .app-content input:focus,
        .app-content select:focus,
        .app-content textarea:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--focus-ring);
        }

        .app-content [class*="bg-[#2563eb]"],
        .app-content .bg-blue-600 {
            background-color: var(--primary);
        }

        .app-content [class*="bg-[#2563eb]"]:hover,
        .app-content .hover\:bg-blue-700:hover {
            background-color: var(--primary-hover);
        }

        .app-content .bg-emerald-600 {
            background-color: #2f8f6a;
        }

        .app-content .hover\:bg-emerald-700:hover {
            background-color: #27785a;
        }

        .app-content .sticky.bottom-0 {
            box-shadow: 0 -10px 24px rgba(23, 32, 51, 0.06);
        }

        @media (min-width: 961px) {
            .app-mobile-menu {
                display: none;
            }

            .app-sidebar {
                position: sticky;
                top: 0;
                width: 252px;
                max-width: 252px;
                height: 100vh;
                flex: 0 0 252px;
                transform: translateX(0);
                box-shadow: none;
            }

            .app-sidebar-backdrop {
                display: none !important;
            }

            .app-sidebar-close {
                display: none;
            }

            .is-sidebar-collapsed .app-sidebar {
                width: 68px;
                flex-basis: 68px;
            }

            .is-sidebar-collapsed .app-brand {
                justify-content: center;
                padding-left: 10px;
                padding-right: 10px;
            }

            .is-sidebar-collapsed .app-brand-row {
                flex: 0 0 auto;
            }

            .is-sidebar-collapsed .app-brand-copy,
            .is-sidebar-collapsed .app-nav-label {
                display: none;
            }

            .is-sidebar-collapsed .app-nav {
                padding-left: 10px;
                padding-right: 10px;
                justify-items: center;
            }

            .is-sidebar-collapsed .app-nav-link,
            .is-sidebar-collapsed .app-logout-button {
                width: 44px;
                height: 40px;
                justify-content: center;
                gap: 0;
                padding: 0;
            }

            .is-sidebar-collapsed .app-nav-link.is-active {
                border-radius: 8px;
            }

            .is-sidebar-collapsed .app-sidebar-footer {
                padding-left: 10px;
                padding-right: 10px;
            }

            .is-sidebar-collapsed .app-logout-button {
                margin: 0 auto;
            }

            .is-sidebar-collapsed .app-nav-divider {
                width: 44px;
            }

            .app-header {
                min-height: 108px;
            }
        }

        @media (max-width: 960px) {
            .app-sidebar-toggle {
                display: none;
            }

            .app-header {
                align-items: flex-start;
                padding: 16px 20px;
            }

            .app-header-title {
                font-size: 20px;
            }

            .app-header-eyebrow {
                letter-spacing: 0.14em;
                line-height: 1.5;
            }

            .app-content {
                padding-left: 20px;
                padding-right: 20px;
            }
        }

        @media (max-width: 640px) {
            .app-header {
                gap: 12px;
            }

            .app-user {
                display: none;
            }

            .app-content {
                padding: 18px 14px 24px;
            }
        }
    </style>
</head>
<body>
    <div
        class="app-shell"
        x-data="appShell()"
        x-init="init()"
        :class="{ 'is-sidebar-open': mobileSidebarOpen, 'is-sidebar-collapsed': sidebarCollapsed }"
    >
        <button
            type="button"
            class="app-sidebar-backdrop"
            x-cloak
            x-show="mobileSidebarOpen"
            x-transition.opacity
            @click="closeMobileSidebar()"
            aria-label="Tutup menu"
        ></button>

        <aside class="app-sidebar">
            <div class="app-brand">
                <div class="app-brand-row">
                    <button type="button" class="app-sidebar-toggle" @click="toggleSidebar()" :aria-label="sidebarCollapsed ? 'Perbesar sidebar' : 'Perkecil sidebar'">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <div class="app-brand-copy">
                        <p class="app-brand-title">PhysioAdmin</p>
                        <p class="app-brand-subtitle">Klinik Fisioterapi</p>
                    </div>
                </div>
            </div>
            <nav class="app-nav">
                <a href="{{ route('dashboard') }}" class="app-nav-link {{ request()->routeIs('dashboard') ? 'is-active' : '' }}" title="Dashboard" @click="closeMobileSidebar()">
                    <span class="app-nav-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                            <rect x="4" y="4" width="6" height="6" rx="1" />
                            <rect x="14" y="4" width="6" height="6" rx="1" />
                            <rect x="4" y="14" width="6" height="6" rx="1" />
                            <rect x="14" y="14" width="6" height="6" rx="1" />
                        </svg>
                    </span>
                    <span class="app-nav-label">Dashboard</span>
                </a>
                <a href="{{ route('patients.create') }}" class="app-nav-link {{ request()->routeIs('patients.create') || request()->routeIs('records.create') ? 'is-active' : '' }}" title="Pasien Baru" @click="closeMobileSidebar()">
                    <span class="app-nav-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                            <circle cx="10" cy="7" r="4" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 8v6M22 11h-6" />
                        </svg>
                    </span>
                    <span class="app-nav-label">Pasien Baru</span>
                </a>
                <a href="{{ route('patients.index') }}" class="app-nav-link {{ request()->routeIs('patients.index') || request()->routeIs('patients.show') || request()->routeIs('patients.edit') || request()->routeIs('records.show') || request()->routeIs('records.edit') ? 'is-active' : '' }}" title="Riwayat Rekam" @click="closeMobileSidebar()">
                    <span class="app-nav-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 4h8l2 2v14H6V6l2-2Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 9h6M9 13h6M9 17h4" />
                        </svg>
                    </span>
                    <span class="app-nav-label">Riwayat Rekam</span>
                </a>
                <a href="{{ route('schedule') }}" class="app-nav-link {{ request()->routeIs('schedule') ? 'is-active' : '' }}" title="Jadwal Terapis" @click="closeMobileSidebar()">
                    <span class="app-nav-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                            <rect x="4" y="5" width="16" height="15" rx="2" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 3v4M16 3v4M4 10h16M8 14h.01M12 14h.01M16 14h.01M8 17h.01M12 17h.01" />
                        </svg>
                    </span>
                    <span class="app-nav-label">Jadwal Terapis</span>
                </a>
                <a href="{{ route('reports') }}" class="app-nav-link {{ request()->routeIs('reports') ? 'is-active' : '' }}" title="Laporan" @click="closeMobileSidebar()">
                    <span class="app-nav-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 1 1-9-9v9h9Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3a9 9 0 0 1 9 9" />
                        </svg>
                    </span>
                    <span class="app-nav-label">Laporan</span>
                </a>
                <div class="app-nav-divider" aria-hidden="true"></div>
                <a href="{{ route('settings') }}" class="app-nav-link {{ request()->routeIs('settings') ? 'is-active' : '' }}" title="Pengaturan" @click="closeMobileSidebar()">
                    <span class="app-nav-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.4 15a1.7 1.7 0 0 0 .34 1.88l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06A1.7 1.7 0 0 0 15 19.4a1.7 1.7 0 0 0-1 1.55V21a2 2 0 0 1-4 0v-.05A1.7 1.7 0 0 0 9 19.4a1.7 1.7 0 0 0-1.88.34l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.7 1.7 0 0 0 4.6 15a1.7 1.7 0 0 0-1.55-1H3a2 2 0 0 1 0-4h.05A1.7 1.7 0 0 0 4.6 9a1.7 1.7 0 0 0-.34-1.88l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.7 1.7 0 0 0 9 4.6a1.7 1.7 0 0 0 1-1.55V3a2 2 0 0 1 4 0v.05A1.7 1.7 0 0 0 15 4.6a1.7 1.7 0 0 0 1.88-.34l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.7 1.7 0 0 0 19.4 9a1.7 1.7 0 0 0 1.55 1H21a2 2 0 0 1 0 4h-.05A1.7 1.7 0 0 0 19.4 15Z" />
                        </svg>
                    </span>
                    <span class="app-nav-label">Pengaturan</span>
                </a>
            </nav>
            <div class="app-sidebar-footer">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="app-logout-button" title="Keluar">
                        <span class="app-nav-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 17l5-5-5-5M21 12H9" />
                            </svg>
                        </span>
                        <span class="app-nav-label">Keluar</span>
                    </button>
                </form>
            </div>
        </aside>

        <div class="app-main">
            <header class="app-header">
                <div class="app-header-left">
                    <button type="button" class="app-mobile-menu" @click="openMobileSidebar()" aria-label="Buka menu">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <div class="app-header-copy">
                        <p class="app-header-eyebrow">Sistem Manajemen Rekam Medis</p>
                        <h1 class="app-header-title">{{ $header }}</h1>
                    </div>
                </div>
                <div class="app-user">
                    <p class="app-user-name">{{ auth()->user()->name }}</p>
                    <p class="app-user-email">{{ auth()->user()->email }}</p>
                </div>
            </header>

            <main class="app-content">
                <x-flash-message />
                {{ $slot }}
            </main>
        </div>
    </div>

    <script>
        window.appShell = () => ({
            sidebarCollapsed: false,
            mobileSidebarOpen: false,
            init() {
                this.sidebarCollapsed = localStorage.getItem('physioadmin.sidebarCollapsed.v2') === 'true';
            },
            toggleSidebar() {
                if (window.matchMedia('(max-width: 960px)').matches) {
                    this.mobileSidebarOpen = false;
                    return;
                }

                this.sidebarCollapsed = !this.sidebarCollapsed;
                localStorage.setItem('physioadmin.sidebarCollapsed.v2', this.sidebarCollapsed ? 'true' : 'false');
            },
            openMobileSidebar() {
                this.mobileSidebarOpen = true;
            },
            closeMobileSidebar() {
                this.mobileSidebarOpen = false;
            }
        });

        window.patientForm = (initialAge = '') => ({
            umur: initialAge,
            syncAge(event) {
                const value = event.target.value;
                if (!value) {
                    this.umur = '';
                    return;
                }

                const birthDate = new Date(value);
                const today = new Date();
                let age = today.getFullYear() - birthDate.getFullYear();
                const monthDiff = today.getMonth() - birthDate.getMonth();

                if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                    age--;
                }

                this.umur = age >= 0 ? age : '';
            }
        });

        window.recordForm = (rows) => ({
            rows: (rows.length ? rows : [
                { id: null, tgl: '', intervensi: '', hasil_evaluasi: '', paraf_url: null },
                { id: null, tgl: '', intervensi: '', hasil_evaluasi: '', paraf_url: null },
                { id: null, tgl: '', intervensi: '', hasil_evaluasi: '', paraf_url: null },
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
