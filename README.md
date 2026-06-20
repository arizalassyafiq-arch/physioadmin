# PhysioAdmin

Sistem manajemen rekam medis pasien fisioterapi berbasis Laravel 12, Blade, Alpine.js, Tailwind CSS, Vite, dan DomPDF.

## Fitur

- Login admin dengan rate limiting.
- Dashboard statistik pasien dan rekam medis.
- CRUD data pasien.
- Form rekam medis fisioterapi lengkap.
- Snapshot tanggal pemeriksaan dan umur pasien saat kunjungan.
- Manajemen intervensi dan evaluasi dengan baris dinamis.
- Upload file penunjang dan paraf ke private medical storage.
- Download file medis melalui route terautentikasi.
- Export PDF rekam medis via DomPDF dengan fallback internal.
- Soft delete dan activity log untuk perubahan data utama.

## Setup Lokal

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Isi kredensial admin di `.env` sebelum menjalankan seeder:

```env
ADMIN_NAME="Admin Fisioterapi"
ADMIN_EMAIL=admin@physio.com
ADMIN_PASSWORD=ubah-password-ini
```

Lalu jalankan:

```bash
php artisan migrate --seed
npm install
npm run build
php artisan serve
```

Jika ingin server langsung membuka Microsoft Edge di Windows:

```powershell
powershell -ExecutionPolicy Bypass -File .\serve-edge.ps1
```

## Database

Default development memakai SQLite:

```env
DB_CONNECTION=sqlite
```

Untuk MySQL, buat database lebih dulu lalu ubah `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=fisioterapi
DB_USERNAME=root
DB_PASSWORD=
```

## Struktur Halaman Utama

- `/login` untuk autentikasi admin.
- `/dashboard` untuk ringkasan statistik.
- `/patients` untuk riwayat dan pencarian pasien.
- `/patients/create` untuk input identitas pasien baru.
- `/patients/{patient}/records/create` untuk input rekam medis.

## Verifikasi

```bash
composer audit
npm audit --audit-level=moderate
php artisan route:list
php artisan migrate:status
php artisan test
php artisan view:cache
npm run build
```

## Deployment

Checklist deploy production ada di [docs/deployment.md](docs/deployment.md).
