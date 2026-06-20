# Backup Rutin PhysioAdmin

Backup otomatis dibuat oleh Windows Task Scheduler dengan nama:

`PhysioAdmin Daily Backup`

Jadwal default: setiap hari pukul `21:00`.

Lokasi backup default:

`C:\laragon\backups\physioadmin`

Isi setiap folder backup:

- `physioadmin.sql` untuk database MySQL.
- `medical-files.zip` untuk file penunjang/paraf di `storage/app/medical`.
- `backup-info.txt` untuk informasi waktu, database, dan status file medis.

Menjalankan backup manual:

```powershell
powershell -ExecutionPolicy Bypass -File scripts\backup-full.ps1
```

Mengubah jadwal backup:

```powershell
powershell -ExecutionPolicy Bypass -File scripts\install-backup-task.ps1 -At 21:00
```

Catatan operasional:

- Komputer harus menyala pada jam backup.
- Dengan mode task saat ini, user Windows perlu sedang login.
- Salin folder backup ke flashdisk/external drive secara berkala.
