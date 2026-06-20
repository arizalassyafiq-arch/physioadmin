# Deployment PhysioAdmin

Dokumen ini adalah checklist deploy production untuk server/domain klien. Jalankan dari root project, kecuali hosting panel meminta path tertentu.

## 1. Kebutuhan Server

- PHP 8.2 atau lebih baru.
- MySQL atau MariaDB.
- Composer 2.
- Node.js dan npm untuk build asset.
- Web server mengarah ke folder `public/`, bukan root project.
- SSL aktif untuk domain production.

## 2. Environment Production

Salin `.env.example` menjadi `.env`, lalu isi nilai production:

```bash
cp .env.example .env
php artisan key:generate
```

Nilai yang wajib dicek:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://domain-klien.com
SESSION_SECURE_COOKIE=true
LOG_LEVEL=error
```

Isi kredensial database, SMTP, dan admin:

```env
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=

MAIL_HOST=
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_FROM_ADDRESS=no-reply@domain-klien.com

ADMIN_EMAIL=
ADMIN_PASSWORD=
```

Gunakan password admin yang kuat dan unik. Setelah admin pertama dibuat, password dapat diganti dari database atau flow operasional yang disepakati.

## 3. Install Dependency

```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build
```

Jika hosting tidak menyediakan Node.js, build asset di mesin lokal/CI lalu upload folder `public/build`.

## 4. Database

Pastikan database sudah dibuat, lalu jalankan migration:

```bash
php artisan migrate --force
```

Seed admin hanya saat setup awal:

```bash
php artisan db:seed --class=AdminSeeder --force
```

Jangan menjalankan seeder admin dengan `ADMIN_PASSWORD` kosong.

## 5. Permission Folder

Folder berikut harus bisa ditulis oleh user web server:

```text
storage/
bootstrap/cache/
```

Untuk Linux hosting, contoh:

```bash
chmod -R ug+rw storage bootstrap/cache
```

File medis disimpan private di `storage/app/medical` dan diakses lewat route terautentikasi. Jangan membuat folder ini public.

## 6. Cache Production

Setelah environment final:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Jika `.env` berubah, jalankan ulang:

```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 7. Backup

Sebelum go-live dan sebelum update besar, backup:

- Database MySQL.
- Folder `storage/app/medical`.
- File `.env` production.

Lihat juga `docs/backup-rutin.md` untuk backup rutin Windows.

## 8. Smoke Test Setelah Deploy

Lakukan pengecekan ini di domain production:

- Buka `/login`.
- Login admin berhasil.
- Buat pasien baru.
- Isi rekam medis.
- Upload file penunjang/paraf bila dipakai.
- Download file dan export PDF.
- Tambah jadwal kontrol.
- Logout.
- Pastikan `APP_DEBUG=false` dengan mencoba URL yang tidak ada dan memastikan tidak muncul stack trace.

## 9. Perintah Ringkas Go-Live

```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build
php artisan migrate --force
php artisan db:seed --class=AdminSeeder --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Untuk update berikutnya, jangan seed admin lagi kecuali memang ingin membuat/memperbarui admin dari env.
