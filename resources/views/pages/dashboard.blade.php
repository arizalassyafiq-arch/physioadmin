<x-layouts.app :title="'Dashboard | PhysioAdmin'" :header="'Dashboard'">
    <style>
        .dashboard-stack {
            display: grid;
            gap: 24px;
        }

        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 20px;
        }

        .dashboard-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 24px;
            box-shadow: var(--shadow-soft);
        }

        .dashboard-card.highlight {
            background: var(--primary-soft);
            color: var(--text);
            border-color: #c9d7ea;
        }

        .dashboard-label {
            margin: 0;
            color: var(--muted);
            font-size: 14px;
            font-weight: 600;
        }

        .dashboard-card.highlight .dashboard-label {
            color: var(--primary);
        }

        .dashboard-value {
            margin: 16px 0 0;
            font-size: 42px;
            font-weight: 700;
            color: var(--text);
            line-height: 1;
        }

        .dashboard-card.highlight .dashboard-value,
        .dashboard-card.highlight .dashboard-note {
            color: var(--text);
        }

        .dashboard-note {
            margin: 16px 0 0;
            max-width: 280px;
            color: var(--text-soft);
            font-size: 18px;
            font-weight: 600;
            line-height: 1.6;
        }

        .dashboard-panel {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 24px;
            box-shadow: var(--shadow-soft);
        }

        .dashboard-panel-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 18px;
        }

        .dashboard-panel-title {
            margin: 0;
            font-size: 22px;
            font-weight: 700;
            color: var(--text);
        }

        .dashboard-panel-subtitle {
            margin: 6px 0 0;
            font-size: 14px;
            color: var(--muted);
        }

        .quick-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .quick-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 44px;
            border-radius: 12px;
            padding: 10px 16px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 700;
            border: 1px solid var(--border);
            color: var(--text);
            background: var(--surface);
        }

        .quick-action.primary {
            border-color: transparent;
            background: var(--primary);
            color: #fff;
        }

        .dashboard-link {
            color: var(--primary);
            text-decoration: none;
            font-size: 14px;
            font-weight: 700;
        }

        .dashboard-table-wrap {
            border: 1px solid var(--border);
            border-radius: 18px;
            overflow-x: auto;
        }

        .dashboard-table {
            width: 100%;
            min-width: 720px;
            border-collapse: collapse;
            font-size: 14px;
        }

        .dashboard-table thead {
            background: var(--surface-soft);
        }

        .dashboard-table th,
        .dashboard-table td {
            padding: 14px 16px;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }

        .dashboard-table th {
            color: var(--muted);
            font-weight: 700;
        }

        .dashboard-table td {
            color: var(--text);
        }

        .dashboard-table tbody tr:last-child td {
            border-bottom: 0;
        }

        .dashboard-empty {
            text-align: center;
            padding: 38px 16px;
            color: var(--muted);
        }

        @media (max-width: 1100px) {
            .dashboard-stats {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 720px) {
            .dashboard-stack {
                gap: 18px;
            }

            .dashboard-panel,
            .dashboard-card {
                border-radius: 18px;
                padding: 18px;
            }

            .dashboard-panel-header {
                display: block;
            }

            .dashboard-panel-title {
                font-size: 18px;
            }

            .dashboard-value {
                font-size: 34px;
            }

            .quick-actions {
                display: grid;
                grid-template-columns: 1fr;
            }

            .quick-action {
                width: 100%;
            }

            .dashboard-link {
                display: inline-block;
                margin-top: 12px;
            }
        }
    </style>

    <div class="dashboard-stack">
        <div class="dashboard-panel">
            <div class="dashboard-panel-header">
                <div>
                    <h2 class="dashboard-panel-title">Aksi Cepat</h2>
                    <p class="dashboard-panel-subtitle">Mulai input pasien atau buka riwayat tanpa melewati menu samping.</p>
                </div>
            </div>
            <div class="quick-actions">
                <a href="{{ route('patients.create') }}" class="quick-action primary">Pasien Baru</a>
                <a href="{{ route('patients.index') }}" class="quick-action">Cari Pasien</a>
                <a href="{{ route('reports') }}" class="quick-action">Laporan</a>
            </div>
        </div>

        <div class="dashboard-stats">
            <div class="dashboard-card">
                <p class="dashboard-label">Total Pasien</p>
                <p class="dashboard-value">{{ $totalPatients }}</p>
            </div>
            <div class="dashboard-card">
                <p class="dashboard-label">Total Rekam Medis</p>
                <p class="dashboard-value">{{ $totalRecords }}</p>
            </div>
            <div class="dashboard-card highlight">
                <p class="dashboard-label">Ringkasan Laporan</p>
                <p class="dashboard-note">Pantau pasien baru dan histori rekam medis dari satu dashboard.</p>
            </div>
        </div>

        <div class="dashboard-panel">
            <div class="dashboard-panel-header">
                <div>
                    <h2 class="dashboard-panel-title">Pasien Terbaru</h2>
                    <p class="dashboard-panel-subtitle">5 data pasien terakhir yang masuk ke sistem.</p>
                </div>
                <a href="{{ route('patients.index') }}" class="dashboard-link">Lihat semua</a>
            </div>

            <div class="dashboard-table-wrap">
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th>No. RM</th>
                            <th>Nama</th>
                            <th>Jenis Kelamin</th>
                            <th>Umur</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentPatients as $patient)
                            <tr>
                                <td><strong>{{ $patient->no_rm }}</strong></td>
                                <td>{{ $patient->nama }}</td>
                                <td>{{ $patient->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                                <td>{{ $patient->umur }} tahun</td>
                                <td>
                                    <a href="{{ route('patients.show', $patient) }}" class="dashboard-link">Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="dashboard-empty">Belum ada data pasien.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>
