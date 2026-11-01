# Deploy SIAP AUM ke Railway (Docker + MySQL)

Project ini sudah berisi `railway.json` + `Dockerfile.railway`. Railway otomatis build pakai Dockerfile tersebut.

## 1. Upload ke GitHub
```bash
git init && git add . && git commit -m "SIAP AUM"
git branch -M main
git remote add origin https://github.com/USERNAME/siap-aum.git
git push -u origin main
```

## 2. Buat project di Railway
1. Buka https://railway.com/new → **Deploy from GitHub repo** → pilih repo `siap-aum`.
2. Di project yang sama klik **+ Create** → **Database** → **MySQL**. Nama service-nya biasanya `MySQL`.
3. Buka service aplikasi (siap-aum) → tab **Variables** → **Raw Editor**, tempel ini:
   ```
   APP_NAME="SIAP AUM"
   APP_ENV=production
   APP_DEBUG=false
   APP_KEY=ISI_DARI_FILE_.env_LOKAL
   APP_URL=https://${{RAILWAY_PUBLIC_DOMAIN}}
   DB_CONNECTION=mysql
   DB_HOST=${{MySQL.MYSQLHOST}}
   DB_PORT=${{MySQL.MYSQLPORT}}
   DB_DATABASE=${{MySQL.MYSQLDATABASE}}
   DB_USERNAME=${{MySQL.MYSQLUSER}}
   DB_PASSWORD=${{MySQL.MYSQLPASSWORD}}
   LOG_CHANNEL=stderr
   LOG_LEVEL=warning
   CACHE_DRIVER=file
   SESSION_DRIVER=file
   QUEUE_CONNECTION=sync
   FILESYSTEM_DISK=local
   MAIL_MAILER=log
   MAIL_FROM_ADDRESS=noreply@example.com
   MAIL_FROM_NAME="SIAP AUM"
   RECAPTCHA_ENABLED=false
   TELESCOPE_ENABLED=false
   ```
   - `APP_KEY`: salin nilai `APP_KEY=base64:...` dari file `.env` hasil `setup.sh`
     (atau jalankan `php artisan key:generate --show`).
   - Kalau nama service database bukan `MySQL`, ganti `MySQL.` sesuai namanya.
4. Tab **Settings** → **Networking** → **Generate Domain** (port `8080`).
5. Klik **Deploy**. Build pertama ±10–15 menit (memasang PHP, LibreOffice, build asset Vite).

Saat container menyala, `docker/railway/entrypoint.sh` otomatis menjalankan `migrate --force`, seeder
(kalau tabel user masih kosong), cache config/view, lalu Apache di port `$PORT`.

## Akun awal (ganti password setelah login)
| Email | Peran | Password |
|---|---|---|
| admin@example.com | Admin | P@ssw0rd |
| verifikator@example.com | Verifikator | P@ssw0rd |
| user@example.com | Pemohon | P@ssw0rd |

## Opsional
- **File upload permanen**: service aplikasi → klik kanan → **Attach Volume**, mount path
  `/var/www/html/storage/app`. Tanpa volume, berkas upload hilang setiap redeploy.
- **Email OTP**: ganti `MAIL_MAILER=smtp` dan isi `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_ENCRYPTION=tls`.
- **reCAPTCHA**: isi `RECAPTCHA_SITE_KEY`, `RECAPTCHA_SECRET_KEY`, lalu `RECAPTCHA_ENABLED=true`.
- **Seeder ulang**: `RUN_SEED=true` lalu redeploy (kembalikan ke `false`).
- Railway memakai kredit (trial $5); pantau penggunaan di menu Usage.

## Deploy lama / "Waiting for build to start..."
- Itu artinya build belum dimulai (masih antre di Railway), bukan error kode. Batalkan deployment (titik tiga > Cancel / Abort), lalu klik **Redeploy**.
- Cek https://status.railway.com apakah sedang ada gangguan builder.
- Cek **Usage / Billing**: kalau kredit trial/plan habis, build tidak akan pernah mulai.
- Pastikan hanya ada 1 deployment yang berjalan; deployment lain yang masih "Building" membuat yang baru ikut antre.
