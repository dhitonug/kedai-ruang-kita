# Arahan Pekerjaan Backend

Dokumen ini membagi pekerjaan backend yang masih perlu diselesaikan berdasarkan PRD dan kondisi kode sekarang. Tujuannya melengkapi backend yang sudah ada, bukan membuat ulang alur menu dan pesanan dari nol.

## Stack dan kode yang sudah tersedia

- Laravel 12, PHP 8.2+, SQLite untuk pengembangan lokal.
- Model dan tabel dasar: `products`, `orders`, `order_items`.
- `OrderController::store` memvalidasi item, mengecek ketersediaan menu, menghitung total dari harga database, dan menyimpan pesanan beserta item dalam transaksi.
- Middleware admin berbasis session sudah melindungi route operasional.
- Backend menu saat ini mencakup tambah, edit, hapus, dan ubah ketersediaan.
- Alur pesanan sudah memiliki status `baru`, `diproses`, `siap`, `selesai`, dan `dibatalkan`.
- Dashboard dasar sudah menghitung ringkasan periode, 7 hari terakhir, dan menu terlaris.
- Order marketplace dapat dicatat manual agar masuk ke antrean yang sama.

Route penting yang sudah ada:

| Method | Route | Kegunaan |
| --- | --- | --- |
| `GET` | `/` dan `/meja/{table}` | Katalog umum dan katalog dengan nomor meja |
| `POST` | `/pesanan` | Validasi dan simpan pesanan pelanggan |
| `GET` | `/pesanan/{order}` | Halaman konfirmasi pesanan |
| `GET` | `/admin/pesanan` | Antrean order aktif |
| `PATCH` | `/admin/pesanan/{order}` | Pindahkan status order |
| `GET` | `/admin/pesanan/riwayat` | Riwayat selesai/dibatalkan |
| `POST/PATCH/DELETE` | `/admin/menu...` | CRUD menu dan ketersediaan |
| `GET` | `/admin/meja` | Lembar QR untuk meja 1-12 |
| `POST` | `/admin/kanal/pesanan` | Input semi-manual order marketplace |

## Prioritas pekerjaan

### 1. Integrasi GoFood, GrabFood, dan ShopeeFood

Ini adalah gap backend terbesar terhadap PRD. Jangan menebak endpoint atau format payload. Minta pemilik/mitra menyediakan kredensial merchant, dokumentasi API resmi, event webhook, sandbox, dan aturan persetujuan tiap platform.

Setelah akses resmi tersedia, implementasikan untuk tiap platform:

- Client/service terpisah per platform; jangan menaruh logika integrasi di controller halaman.
- Penerimaan webhook dengan validasi signature, timestamp, dan sumber request sesuai dokumentasi resmi.
- Pencatatan event masuk sebelum memprosesnya agar request dapat diaudit dan diputar ulang.
- Idempotensi menggunakan ID order/event platform dan batas unik database supaya retry webhook tidak membuat order ganda.
- Normalisasi order menjadi format internal `Order` dan `OrderItem`, termasuk mapping SKU/ID produk, varian, jumlah, harga, biaya, diskon, dan catatan.
- Pemetaan status platform ke status internal tanpa menimpa status yang tidak boleh mundur.
- Queue/job untuk pekerjaan lambat, retry dengan backoff, timeout, pencatatan kegagalan, dan alat rekonsiliasi.
- Lindungi token/kredensial dengan environment/secret store; jangan commit ke Git atau tulis ke log.

**Syarat selesai:** order dari sandbox tiap platform muncul tepat satu kali pada antrean pusat dengan item, total, sumber, ID eksternal, dan status yang cocok; retry atau webhook duplikat tidak membuat transaksi ganda.

### 2. Ganti input marketplace sementara dengan sumber data terhubung

Form `/admin/kanal/pesanan` sekarang hanya membuat satu baris ringkasan `Pesanan {platform}`. Itu membantu demo, tetapi tidak menyinkronkan item sebenarnya. Pertahankan sementara sebagai fallback; simpan ID order platform pada kolom khusus saat skema integrasi dibuat.

### 3. Ketahanan data dan deployment

- Rencanakan migrasi dari SQLite lokal ke MySQL atau PostgreSQL untuk deployment bersama.
- Pindahkan konfigurasi session/cache/queue database ke layanan yang sesuai lingkungan; pastikan worker queue dijalankan jika integrasi memakai job.
- Set `APP_DEBUG=false`, `APP_ENV=production`, `APP_KEY` unik, kredensial admin kuat, HTTPS, backup database, dan kebijakan retensi log.
- Tambahkan health check dan prosedur migrasi/rollback.
- Jangan memakai filesystem ephemeral untuk menyimpan SQLite atau foto yang diunggah.

### 4. Keamanan akun dan akses data

- Login sekarang memakai satu akun konfigurasi dan flag session; belum ada tabel pengguna, role, reset password, atau audit login.
- Kredensial awal demo ditampilkan pada halaman login. Hilangkan kredensial demo yang terlihat sebelum aplikasi dipublikasikan.
- Pertimbangkan hash password dengan sistem autentikasi Laravel, pembatasan percobaan login, dan role pemilik/kasir.
- Route konfirmasi `/pesanan/{order}` memakai ID order. Ganti dengan token publik acak/bertanda tangan agar pesanan orang lain tidak dapat ditebak dan dibuka.
- Catat audit perubahan status: pengguna, status sebelum/sesudah, dan waktu.

### 5. Tabel, stok, dan aturan transaksi

- QR meja saat ini dibuat untuk angka 1-12 hard-coded; belum ada tabel `restaurant_tables` di database.
- Tambahkan data meja yang dapat dikelola (nomor/kode unik, aktif/nonaktif, token QR jika diperlukan) dan pastikan QR menggunakan URL deployment.
- Produk hanya memiliki boolean `available`; belum ada jumlah stok atau bahan baku. Konfirmasi kebutuhan mitra sebelum menambahkan pengurangan inventory otomatis.
- Tetapkan definisi pendapatan: dashboard saat ini menjumlahkan order berstatus `siap` dan `selesai`. Sepakati status yang dianggap transaksi valid, serta perlakuan pembatalan/refund.
- Tentukan strategi untuk dua permintaan terakhir atas menu yang sama ketika stok terbatas, setelah quantity inventory benar-benar dimodelkan.

### 6. Laporan, pembaruan antrean, dan penerimaan

- Dashboard menampilkan periode hari/minggu/bulan, ringkasan, grafik 7 hari, dan best seller dasar; data diperbarui saat halaman dimuat ulang.
- Antrean memuat ulang tiap 15 detik; belum ada push event realtime/WebSocket.
- Tambahkan ekspor laporan bila disetujui pemilik; ini belum ditetapkan secara eksplisit dalam PRD.
- Buat feature tests untuk login/otorisasi, CRUD dan stok menu, pemesanan valid/invalid, perhitungan harga server, batas quantity, status order, laporan periode, idempotensi webhook, serta signature webhook.
- Jalankan UAT dengan kasir dan pemilik; ukur target PRD pengurangan waktu pemesanan minimal 30% dan minta sign-off.

## Batas ruang lingkup PRD

Payment gateway otomatis, aplikasi native Android/iOS, dan integrasi langsung ke POS pihak ketiga tercantum sebagai **out of scope**. Jangan mulai pekerjaan itu tanpa perubahan scope tertulis.
