# Panduan Preview Kedai Ruang Kita

Panduan ini untuk menjalankan prototype di komputer sendiri atau membukanya dari perangkat lain. Gunakan data demo saja.

## A. Menjalankan project di komputer teman

### Kebutuhan

- PHP 8.2 atau lebih baru, dengan ekstensi `pdo_sqlite`.
- Composer.
- Node.js dan npm.
- Koneksi internet saat memasang dependency dan memuat foto menu dari Unsplash.

### Siapkan source code

Bagikan folder project sebagai ZIP atau lewat repository privat. Jangan sertakan file `.env`, `database/database.sqlite`, `vendor`, atau `node_modules`. Temanmu perlu membuat konfigurasi dan database lokal baru.

Ekstrak ZIP, buka PowerShell pada folder project, lalu jalankan:

```powershell
Copy-Item .env.example .env
if (-not (Test-Path 'database/database.sqlite')) { New-Item -ItemType File 'database/database.sqlite' | Out-Null }
composer install
php artisan key:generate
php artisan migrate --seed
npm ci
npm run build
php artisan serve
```

Buka alamat yang ditampilkan Laravel, biasanya `http://127.0.0.1:8000`.

### Akses admin demo

Buka `/admin/login` dan gunakan akun demo lokal:

- Email: `admin@ruangkita.id`
- Password: `ruangkita123`

Akun itu hanya untuk prototype lokal. Untuk demo melalui internet, ubah `ADMIN_EMAIL` dan `ADMIN_PASSWORD` di `.env` menjadi nilai khusus demo sebelum menjalankan aplikasi. Jangan membagikan file `.env`.

## B. Membuka project dari perangkat lain

### Perangkat berada di Wi-Fi yang sama

1. Pada komputer yang menjalankan project, jalankan:

   ```powershell
   php artisan serve --host=0.0.0.0 --port=8000
   ```

2. Jalankan `ipconfig`, lalu catat alamat IPv4 komputer pada adapter Wi-Fi, misalnya `192.168.1.25`.
3. Pada perangkat teman yang berada di Wi-Fi sama, buka `http://192.168.1.25:8000` dengan mengganti alamat contoh tersebut.
4. Jika Windows Firewall bertanya, izinkan PHP pada jaringan privat.

### Teman berada di jaringan lain

Gunakan Cloudflare Quick Tunnel untuk preview sementara. Instal `cloudflared` dari [panduan unduhan resmi Cloudflare](https://developers.cloudflare.com/cloudflare-one/networks/connectors/cloudflare-tunnel/downloads/), lalu:

1. Pastikan Laravel berjalan pada port `8000`.
2. Buka terminal PowerShell kedua dan jalankan:

   ```powershell
   cloudflared tunnel --url http://127.0.0.1:8000
   ```

3. Bagikan URL `https://....trycloudflare.com` yang dicetak oleh perintah tersebut.

URL Quick Tunnel bersifat sementara dan berubah saat tunnel dibuat ulang. Komputer dan kedua terminal harus tetap aktif. Siapa pun yang memiliki URL dapat membuka preview. Untuk membatasi akses berdasarkan email teman, Quick Tunnel mendukung opsi `--allowed-mail`; lihat [dokumentasi Quick Tunnels](https://developers.cloudflare.com/tunnel/get-started/quick-tunnels/).

Jika URL QR meja pada halaman admin masih mengarah ke `localhost`, set `APP_URL` di `.env` ke URL tunnel yang baru, jalankan `php artisan config:clear`, lalu muat ulang halaman QR.

## URL penting

- Katalog pelanggan: `/`
- Contoh QR meja 1: `/meja/1`
- Login staf: `/admin/login`
- Dashboard: `/admin`
- Antrean pesanan: `/admin/pesanan`
- Pengelolaan menu: `/admin/menu`
- QR meja: `/admin/meja`
- Kanal online: `/admin/kanal`

## Catatan preview

- Setiap teman yang memasang project secara lokal akan memiliki database SQLite sendiri.
- Preview lewat tunnel memakai database di komputer host; pesanan demo teman akan masuk ke database host.
- QR gambar saat ini memakai layanan QR pihak ketiga dan butuh internet.
- Ini prototype untuk review, bukan deployment produksi. Jangan masukkan informasi pelanggan atau transaksi nyata.
