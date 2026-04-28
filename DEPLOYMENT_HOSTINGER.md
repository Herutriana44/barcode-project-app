# Deploy ke Shared Hosting (Hostinger / File Manager)

Dokumen ini menjelaskan cara deploy proyek **Laravel 10 (PHP 8.1+)** ini ke shared hosting (contoh: **Hostinger**) menggunakan **File Manager**, dengan opsi tambahan jika Anda punya akses **SSH/Terminal**.

> Catatan penting: folder `vendor/` dan output Vite `public/build/` wajib ada di server. Jika tidak, aplikasi akan error (mis. `vendor/autoload.php` tidak ditemukan atau asset Vite 404).

---

## Prasyarat

- **Hosting**: Shared hosting yang mendukung **PHP 8.1+** dan **MySQL/MariaDB**.
- **Domain/Subdomain**: Sudah terhubung ke hosting.
- **Akses**:
  - Minimal: **File Manager** (hPanel).
  - Lebih baik: **SSH/Terminal** (untuk `composer install`, migrasi, dan cache).
- **Dari lokal (komputer Anda)**:
  - Composer (untuk build `vendor/` bila tanpa SSH)
  - Node.js + npm (untuk `npm run build` menghasilkan `public/build/`)

---

## Struktur folder yang benar di hosting

Laravel idealnya memakai document root ke folder `public/`.

- **Opsi A (Recommended)**: Set document root domain/subdomain → `.../barcode-project-app/public`
- **Opsi B (Paling umum di shared hosting)**: Domain root adalah `public_html/`
  - Simpan project di luar `public_html/` (mis. `barcode-project-app/`)
  - Arahkan domain/subdomain ke `barcode-project-app/public` jika panel hosting mendukung
  - Jika tidak bisa mengubah document root, gunakan workaround “public_html sebagai `public/`” (lihat bagian “Jika document root tidak bisa diarahkan”).

---

## Langkah 1 — Siapkan build production di lokal

### 1A. Build asset Vite (wajib)

Di komputer lokal, dari root project:

```bash
npm install
npm run build
```

Pastikan folder ini ada setelah build:

- `public/build/` (berisi `manifest.json` dan file asset hasil build)

### 1B. Siapkan dependensi PHP

Jika Anda **punya SSH/Terminal di hosting**, Anda bisa install dependensi langsung di server (lebih ideal) dan **tidak perlu** upload `vendor/` dari lokal.

Jika Anda **tanpa SSH**, Anda harus menyiapkan `vendor/` di lokal:

```bash
composer install --no-dev --optimize-autoloader
```

---

## Langkah 2 — Buat Database dan user (MySQL)

Di hPanel Hostinger:

- Buat database (mis. `barcode_db`)
- Buat user database dan password
- Catat:
  - **DB_HOST** (biasanya `localhost`)
  - **DB_DATABASE**
  - **DB_USERNAME**
  - **DB_PASSWORD**

---

## Langkah 3 — Upload project ke hosting (File Manager)

### 3A. Upload file project

Upload seluruh isi project ke folder tujuan, contoh:

- `/home/<user>/barcode-project-app/` (nama folder bebas)

**Jangan upload** hal berikut (opsional, untuk menghemat space):

- `node_modules/` (tidak diperlukan di server)
- file kerja lokal lain yang tidak dipakai

**Yang wajib ada di server:**

- `app/`, `bootstrap/`, `config/`, `database/`, `public/`, `resources/`, `routes/`, `storage/`, `artisan`, `composer.json`, `composer.lock`
- `vendor/` (jika tidak menjalankan Composer di server)
- `public/build/` (hasil `npm run build`)

### 3B. Atur lokasi document root

Di Hostinger biasanya bisa set “Document Root” untuk domain/subdomain.

- Set ke: `barcode-project-app/public`

Jika Anda memakai domain root default `public_html`, Anda bisa:

- Menjadikan `public_html` sebagai isi `public/` (lihat bagian “Jika document root tidak bisa diarahkan”).

---

## Langkah 4 — Buat dan konfigurasi `.env` di server

Di server, buat file `.env` dari `.env.example`, lalu sesuaikan minimal ini:

```dotenv
APP_NAME="TASM MIS"
APP_ENV=production
APP_KEY=base64:... (isi nanti dari key:generate)
APP_DEBUG=false
APP_URL=https://domain-anda.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=... (sesuai hosting)
DB_USERNAME=... (sesuai hosting)
DB_PASSWORD=... (sesuai hosting)
```

**Penting:**

- Pastikan `APP_URL` sesuai domain (https jika memakai SSL).
- Set `APP_DEBUG=false` di production.

---

## Langkah 5 — Jalankan perintah deployment (SSH/Terminal jika ada)

Jika Hostinger menyediakan **Terminal/SSH**, jalankan dari folder root project (yang berisi `artisan`):

### 5A. Install dependensi (jika belum upload `vendor/`)

```bash
composer install --no-dev --optimize-autoloader
```

### 5B. Generate APP_KEY

```bash
php artisan key:generate --force
```

### 5C. Migrasi database

```bash
php artisan migrate --force
```

Jika ada seeder yang memang dibutuhkan (opsional, hanya jika proyek Anda memakainya):

```bash
php artisan db:seed --force
```

### 5D. Cache untuk production (recommended)

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Jika suatu saat mengubah `.env` / routes / views dan perubahan tidak muncul, jalankan:

```bash
php artisan optimize:clear
```

---

## Langkah 6 — Permission folder penting

Shared hosting biasanya tidak butuh `chown`, tapi perlu permission yang benar.

Pastikan folder ini **writable** oleh PHP:

- `storage/`
- `bootstrap/cache/`

Jika ada menu permission di File Manager:

- Set folder ke **775** (atau **755** jika 775 tidak memungkinkan)
- File biasanya **644**

---

## Langkah 7 — Storage symlink (jika aplikasi pakai file public)

Laravel biasanya butuh symlink agar `storage/app/public` bisa diakses lewat `public/storage`.

Jika SSH tersedia:

```bash
php artisan storage:link
```

Jika **symlink tidak diizinkan** di shared hosting:

- Buat folder `public/storage/` lalu **copy** isi `storage/app/public/` ke sana (manual via File Manager).
  - Ini workaround; idealnya symlink.

---

## Jika document root tidak bisa diarahkan ke `public/`

Beberapa shared hosting “memaksa” web root berada di `public_html/` dan tidak bisa diubah.

Workaround yang paling aman:

1. Upload **seluruh project** ke folder misalnya `barcode-project-app/` (di luar `public_html` jika memungkinkan).
2. Isi `public_html/` hanya berisi file dari folder `public/`:
   - Copy semua isi `barcode-project-app/public/*` ke `public_html/`
3. Edit `public_html/index.php` (yang asalnya dari `public/index.php`) agar path tetap mengarah ke folder project yang benar:
   - Sesuaikan `__DIR__.'/../vendor/autoload.php'` dan `__DIR__.'/../bootstrap/app.php'`
   - Contoh jika project ada di `../barcode-project-app/`:
     - `__DIR__.'/../barcode-project-app/vendor/autoload.php'`
     - `__DIR__.'/../barcode-project-app/bootstrap/app.php'`

> Catatan: ini rawan salah path. Kalau Anda bisa mengubah document root ke `public/`, itu jauh lebih baik.

---

## Troubleshooting cepat

- **500 / blank page**
  - Cek `storage/logs/laravel.log`
  - Pastikan `APP_DEBUG=false` tapi saat debugging sementara bisa set `true` (ingat kembalikan ke `false`)
  - Pastikan permission `storage/` dan `bootstrap/cache/` benar
- **Error “Composer dependencies are missing”**
  - `vendor/` belum ada → jalankan `composer install` di server atau upload `vendor/` dari lokal
- **Asset tidak muncul / `@vite` error / 404 build**
  - `public/build/manifest.json` belum ada → jalankan `npm run build` di lokal dan upload `public/build/`
- **Perubahan `.env` tidak terbaca**
  - Jalankan `php artisan optimize:clear` lalu (opsional) `php artisan config:cache`
- **Migrasi gagal**
  - Pastikan kredensial DB di `.env` benar dan database/user punya privilege

---

## Checklist deploy (ringkas)

- [ ] `npm run build` → upload `public/build/`
- [ ] `.env` production (APP_URL, DB_*, APP_DEBUG=false)
- [ ] `composer install --no-dev` (atau upload `vendor/`)
- [ ] `php artisan key:generate --force`
- [ ] `php artisan migrate --force`
- [ ] Permission `storage/` + `bootstrap/cache/`
- [ ] `php artisan config:cache` + `route:cache` + `view:cache`

