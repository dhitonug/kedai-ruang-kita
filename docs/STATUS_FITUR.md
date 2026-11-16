# Status Fitur Kedai Ruang Kita

Status ini membedakan kode yang sudah tersedia di prototype, bagian yang masih parsial, dan pekerjaan yang belum dibuat. **Sudah tersedia** tidak berarti sudah lolos UAT atau siap untuk transaksi produksi.

## Sudah tersedia di prototype

- **Katalog pelanggan:** daftar menu aktif dengan foto, kategori, deskripsi, harga, dan filter kategori.
- **Keranjang:** tambah/kurangi jumlah, hitung subtotal dan total pada tampilan, input nama/catatan, lalu kirim pesanan.
- **QR meja:** URL `/meja/{nomor}` menanam nomor meja ke pesanan; halaman admin menyiapkan QR untuk meja 1-12.
- **Penerimaan pesanan meja:** backend memeriksa produk tersedia, menghitung harga dari database, dan menyimpan order serta item secara atomik.
- **Konfirmasi order:** pelanggan melihat kode dan ringkasan order setelah submit.
- **Antrean operasional:** pesanan baru, diproses, dan siap disajikan; satu tombol untuk melanjutkan status; order tertua ditampilkan lebih dulu; refresh otomatis tiap 15 detik.
- **Riwayat:** pesanan selesai dan dibatalkan dengan filter status dan pagination.
- **Pengelolaan menu:** tambah, edit, hapus, dan tandai tersedia/habis.
- **Kanal marketplace manual:** kasir dapat memasukkan sumber dan total GoFood/GrabFood/ShopeeFood ke antrean pusat.
- **Dashboard dasar:** ringkasan pendapatan/pesanan/menu, grafik 7 hari, dan menu terlaris untuk periode hari, minggu, atau bulan.
- **Responsive UI:** halaman pelanggan dan panel operasional memiliki breakpoint ponsel/tablet.

## Sudah ada, tetapi masih parsial/perlu dipastikan

- **QR meja:** jumlah meja masih hard-coded 1-12; QR gambar diambil dari layanan pihak ketiga, bukan dibuat/dikelola dari data meja sendiri. Pemindaian URL meja berfungsi pada alamat yang benar.
- **Marketplace:** yang tersedia adalah input manual ringkas, bukan sinkronisasi otomatis dan bukan rincian item aktual dari order platform.
- **Laporan:** angka dihitung dari data internal dan diperbarui saat halaman dimuat; belum ada ekspor, rekonsiliasi settlement, atau validasi terhadap laporan platform.
- **Pendapatan:** query dashboard saat ini memasukkan status `siap` dan `selesai`; definisi transaksi yang dihitung harus disepakati dengan pemilik.
- **Akun admin:** satu login berbasis konfigurasi/session untuk prototype; belum ada manajemen user/role dan kredensial demo terlihat di halaman login.
- **Stok:** hanya status tersedia/habis; belum ada jumlah stok atau bahan baku.
- **Pembaruan antrean:** polling 15 detik, belum push realtime.
- **Database:** SQLite untuk pengembangan lokal, belum dikonfigurasi untuk deployment multi-user.
- **Pengujian:** test bawaan Laravel tersedia, tetapi test fitur bisnis dan UAT belum dibuat/dijalankan.
- **Dokumentasi:** panduan preview dan arahan backend tersedia; panduan operasional lengkap untuk staf belum selesai.

## Belum dibuat

- Integrasi API/webhook otomatis GoFood.
- Integrasi API/webhook otomatis GrabFood.
- Integrasi API/webhook otomatis ShopeeFood.
- Penyimpanan ID eksternal, idempotensi, retry, rekonsiliasi, dan pemetaan status untuk order marketplace.
- Database meja yang dapat dikelola dari panel, bukan angka QR hard-coded.
- Deployment produksi dengan database persisten, domain/HTTPS, backup, logging, dan kredensial produksi.
- Uji end-to-end/UAT bersama kasir dan pemilik, pengukuran target pengurangan waktu minimal 30%, serta sign-off mitra.

## Di luar scope PRD

- Payment gateway otomatis.
- Aplikasi native Android/iOS.
- Integrasi langsung dengan POS pihak ketiga.
