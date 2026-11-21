# PustakaKu

PustakaKu adalah prototipe perpustakaan e-book untuk UTS Pemrograman Web 2026/2027. Dari aplikasi ini, petugas bisa mengelola katalog dan data anggota, lalu mencatat peminjaman dan pengembalian.

Tampilannya dibuat dengan HTML, CSS, dan JavaScript. Bagian server memakai PHP 8.x dengan OOP tanpa framework. Data disimpan dalam JSON, jadi aplikasi bisa langsung dicoba tanpa menyiapkan MySQL, Composer, atau npm.

## Cara menjalankan

Buka folder proyek di VS Code. Jalankan perintah ini dari terminal.

```bash
php -v
php -S localhost:8000 -t public public/router.php
```

Pastikan versi PHP yang muncul adalah 8.x. Setelah server berjalan, buka [localhost:8000](http://localhost:8000). Untuk menghentikannya, tekan `Ctrl+C` di terminal.

Kalau memakai Laragon dengan Apache, salin proyek ke `C:\laragon\www\PustakaKu-UTS-PHP-OOP`, jalankan Apache, lalu buka [halaman PustakaKu](http://localhost/PustakaKu-UTS-PHP-OOP/public/).

## Yang bisa dicoba

- **Dashboard** menampilkan jumlah e-book, anggota, pinjaman aktif, buku yang tersedia, dan aktivitas terakhir.
- **Katalog** bisa dicari berdasarkan judul atau penulis dan difilter menurut kategori. Dari sini, petugas bisa menambah, mengubah, menghapus, atau meminjamkan e-book.
- **Data Anggota** bisa ditambah, diedit, dihapus, dan dicari. Satu alamat email hanya bisa dipakai oleh satu anggota.
- **Transaksi** menampilkan tanggal pinjam, tenggat, status keterlambatan, dan riwayat. Pengembalian juga dicatat dari halaman ini.

Setiap anggota boleh punya maksimal tiga pinjaman aktif. Satu e-book hanya bisa dipinjam satu anggota pada satu waktu. Buku atau anggota yang masih terkait pinjaman aktif baru bisa dihapus setelah pengembaliannya dicatat.

## Data contoh

Kalau `storage/database.json` belum ada, aplikasi akan membuatnya saat pertama dibuka. Isinya delapan e-book, tiga anggota, dan dua contoh transaksi. Perubahan yang dilakukan di aplikasi tersimpan di file ini dan tetap ada setelah halaman dimuat ulang. Pastikan PHP punya izin tulis ke folder `storage/`.

Untuk mengulang demo dari awal, hentikan server dan cadangkan data jika masih diperlukan. Hapus `storage/database.json`, lalu jalankan kembali aplikasi. Data contoh akan dibuat lagi saat halaman dibuka.

## Susunan folder

| Folder | Isi |
| --- | --- |
| `public/` | File masuk aplikasi, router, CSS, dan JavaScript |
| `src/` | Model, layanan, penyimpanan, penanganan permintaan, dan validasi PHP |
| `templates/` | Tampilan dashboard, katalog, anggota, dan transaksi |
| `storage/` | Data JSON dan file lock |
| `tests/` | Tes yang dijalankan dari terminal |

## Bagian yang dinilai

- **Front-end (30 poin).** Template memakai elemen seperti `nav`, `main`, `form`, dan `label`. CSS menyesuaikan tampilan dengan lebar layar. JavaScript dipakai untuk pencarian, filter, dialog, dan validasi formulir. PHP memeriksa kembali data yang dikirim.
- **PHP OOP (40 poin).** `BookFactory` membuat objek dari class `EBook` atau `PremiumEBook`. Properti `Book` bersifat privat dan dibaca lewat getter. `EBook` mewarisi `Book`, lalu `PremiumEBook` mewarisi `EBook`. Saat dipanggil oleh `LoanService`, method `getLoanPeriodDays()` mengembalikan 7 atau 14 hari sesuai jenis objeknya. Kode memakai namespace seperti `App\Models` dan `App\Services`.
- **Struktur berkas (30 poin).** Kode dalam `src/` dibagi menurut tugasnya. Template, aset publik, dan data punya folder masing-masing.

## Menjalankan tes

```bash
php tests/test_app.php
```

Tes memakai file JSON sementara sehingga data aplikasi tetap utuh. Untuk urutan presentasi dan bagian kode yang perlu dibuka, lihat [Panduan Demo UTS](PANDUAN_DEMO_UTS.md).
