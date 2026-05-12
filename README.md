# FinEdu Backend — Panduan Deploy

## Struktur File
```
finedu-backend/
├── .htaccess          ← Konfigurasi Apache & CORS
├── vercel.json        ← Konfigurasi Vercel
├── Dockerfile         ← Konfigurasi Railway (Docker)
├── db.php             ← Koneksi database (jangan diakses langsung)
├── index.php          ← Endpoint root / health check
├── login.php
├── register.php
├── get_profile.php
├── edit_profile.php
├── get_modul.php
├── get_progress.php
├── selesai_modul.php
└── simpan_slide.php
```

---

## Deploy ke Railway (Database + Backend)

### 1. Database MySQL di Railway
1. Buka [railway.app](https://railway.app) → **New Project** → **Add MySQL**
2. Setelah MySQL aktif, buka tab **Variables** — Railway otomatis mengisi:
   - `MYSQLHOST`, `MYSQLUSER`, `MYSQLPASSWORD`, `MYSQLDATABASE`, `MYSQLPORT`
3. Import skema database kamu lewat tab **Query** atau koneksi eksternal

### 2. Deploy Backend PHP di Railway
1. Di project yang sama → **New Service** → **GitHub Repo** (pilih repo ini)
2. Railway otomatis deteksi `Dockerfile` dan build
3. Variabel env MySQL sudah otomatis terbaca (tidak perlu setting manual)
4. Setelah deploy, klik **Generate Domain** di tab Settings

---

## Deploy ke Vercel (Frontend / Backend PHP)

### Catatan Penting
Vercel **tidak** menyediakan MySQL. Database tetap harus di Railway.
Vercel hanya dipakai untuk hosting file PHP-nya saja.

### Langkah-langkah
1. Install Vercel CLI:
   ```bash
   npm i -g vercel
   ```
2. Login:
   ```bash
   vercel login
   ```
3. Deploy dari folder project:
   ```bash
   vercel --prod
   ```
4. Set Environment Variables di dashboard Vercel:
   - Buka **Project Settings** → **Environment Variables**
   - Tambahkan variabel berikut (ambil nilai dari Railway MySQL):

   | Key         | Nilai (dari Railway)         |
   |-------------|------------------------------|
   | DB_HOST     | nilai MYSQLHOST dari Railway |
   | DB_USER     | nilai MYSQLUSER              |
   | DB_PASSWORD | nilai MYSQLPASSWORD          |
   | DB_NAME     | nilai MYSQLDATABASE          |
   | DB_PORT     | nilai MYSQLPORT              |

5. Redeploy setelah menambah env:
   ```bash
   vercel --prod
   ```

---

## Rekomendasi Arsitektur

```
Flutter App
    │
    ├──▶ Vercel (PHP API)  ──▶  Railway MySQL
    │         atau
    └──▶ Railway (PHP + MySQL, satu project)
```

Gunakan **Railway** saja jika ingin servis dalam satu platform.
Gunakan **Vercel + Railway** jika ingin PHP di edge Vercel dengan DB di Railway.

---

## Daftar Endpoint API

| Method | Endpoint             | Parameter Body / Query        |
|--------|----------------------|-------------------------------|
| GET    | `/index.php`         | —                             |
| POST   | `/login.php`         | `email`, `password`           |
| POST   | `/register.php`      | `name`, `email`, `phone`, `password` |
| GET    | `/get_profile.php`   | `?id=`                        |
| POST   | `/edit_profile.php`  | `id`, `full_name`, `phone`    |
| GET    | `/get_modul.php`     | `?user_id=`                   |
| GET    | `/get_progress.php`  | `?user_id=`                   |
| POST   | `/selesai_modul.php` | `user_id`, `modul_id`         |
| POST   | `/simpan_slide.php`  | `user_id`, `modul_id`, `slide_id` |
