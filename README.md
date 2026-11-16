# Kedai Ruang Kita

Web app Laravel 12 untuk katalog menu, pemesanan QR meja, panel operasional, dan rekap penjualan.

## Menjalankan lokal

1. Pastikan PHP 8.2+, Composer, dan ekstensi `pdo_sqlite` tersedia.
2. Salin `.env.example` menjadi `.env`, lalu atur `APP_URL`, `ADMIN_EMAIL`, dan `ADMIN_PASSWORD`.
3. Jalankan `composer install`.
4. Jalankan `php artisan key:generate`.
5. Jalankan `php artisan migrate --seed`.
6. Jalankan `php artisan serve` lalu buka URL yang ditampilkan.

Login awal lokal: `admin@ruangkita.id` / `ruangkita123`. Ganti sebelum deployment.

## Halaman

- `/` katalog pelanggan.
- `/meja/1` katalog dan pemesanan meja dengan nomor meja tertanam pada URL.
- `/admin/login` panel admin, `/admin` dashboard, `/admin/pesanan`, `/admin/menu`, `/admin/meja`, dan `/admin/kanal`.
- `/admin/meja` menampilkan lembar QR untuk 12 meja. Set `APP_URL` ke alamat situs publik sebelum mencetak QR.

## Alur antrean pesanan

Halaman `/admin/pesanan` adalah papan antrean dapur. Pesanan dikelompokkan menjadi **Pesanan baru**, **Sedang dibuat**, dan **Siap disajikan**, dengan pesanan tertua berada di urutan atas. Staf memajukan tiap pesanan dengan satu tombol. Antrean memuat ulang otomatis setiap 15 detik; halaman `/admin/pesanan/riwayat` menyimpan pesanan selesai dan dibatalkan.

## Integrasi marketplace

Kanal GoFood, GrabFood, dan ShopeeFood disiapkan pada dashboard. API otomatis memerlukan kredensial merchant dan detail webhook resmi yang belum tercantum di PRD. Selama akses belum tersedia, admin dapat memasukkan order marketplace melalui halaman Kanal Online agar pesanan tetap tercatat terpusat.

## Dokumentasi tim

- [Panduan preview di komputer sendiri atau perangkat teman](docs/PANDUAN_PREVIEW.md)
- [Arahan pekerjaan backend](docs/ARAHAN_BACKEND.md)
- [Status fitur prototype](docs/STATUS_FITUR.md)
