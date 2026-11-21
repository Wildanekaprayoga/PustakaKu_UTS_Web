# Panduan Demo UTS PustakaKu

Jalankan server dari folder proyek sebelum presentasi.

```bash
php -S localhost:8000 -t public public/router.php
```

Buka [localhost:8000](http://localhost:8000). Siapkan satu e-book reguler dan satu e-book premium yang tersedia supaya perbedaan tenggatnya terlihat saat demo.

## Urutan demo

1. Mulai dari **Dashboard**. Tunjukkan jumlah e-book, anggota, dan pinjaman aktif.
2. Masuk ke **Katalog E-Book**. Cari `PHP`, lalu pilih kategori **Teknologi**. Setelah itu, klik **Tambah E-Book** dan isi formulir. Ubah salah satu isian yang baru disimpan untuk menunjukkan fitur edit.
3. Buka **Data Anggota**. Tambah satu anggota dan ubah emailnya. Coba tambahkan anggota lain memakai email yang sama, lalu tunjukkan pesan yang muncul.
4. Kembali ke katalog. Klik **Pinjam Buku**, pilih anggota tadi, lalu simpan. Lakukan untuk satu e-book reguler dan satu e-book premium.
5. Di **Transaksi**, bandingkan tanggal pinjam dan tenggatnya. Masa pinjam e-book reguler adalah 7 hari, sedangkan premium 14 hari.
6. Klik **Kembalikan** pada salah satu transaksi. Tunjukkan perubahan statusnya, lalu lihat kembali di katalog bahwa e-book itu sudah tersedia.
7. Muat ulang halaman untuk melihat bahwa perubahan masih tersimpan. Buka `storage/database.json` di VS Code dan tunjukkan catatan pinjamannya.

## Menjelaskan OOP dari kode

| Konsep | Bagian yang dibuka |
| --- | --- |
| Class dan object | `Book.php` mendefinisikan class abstrak `Book`. Di `BookFactory.php`, `new $class(...)` membuat objek `EBook` atau `PremiumEBook`. Saat buku dipinjam, `LoanService.php` membuat objek transaksi dengan `new Loan(...)`. |
| Encapsulation | Properti seperti `$title` dan `$author` di `Book.php` bersifat `private`. Bagian lain membaca nilainya lewat method seperti `getTitle()`. |
| Inheritance | `EBook` mewarisi `Book`. `PremiumEBook` mewarisi `EBook` dan mengubah implementasi method yang menentukan masa pinjam. |
| Polymorphism | Di `LoanService::borrow()`, kedua jenis buku memakai panggilan `$book->getLoanPeriodDays()`. Objek reguler mengembalikan 7 hari dan objek premium mengembalikan 14 hari. |
| Namespace | Buka awal file di `src/Models/` atau `src/Services/` untuk melihat `App\Models` dan `App\Services`. `src/autoload.php` memakai namespace untuk mencari dan memuat file class. |

Setelah menunjukkan dua tenggat di halaman **Transaksi**, buka `src/Services/LoanService.php` pada bagian yang menghitung tanggal jatuh tempo. Dari situ, lanjutkan ke implementasi method di `EBook.php` dan `PremiumEBook.php` dalam `src/Models/`.

## Menjelaskan tampilan, validasi, dan data

Buka `templates/catalog.php` untuk menunjukkan label dan atribut validasi seperti `required`, `minlength`, `maxlength`, serta batas tahun. Form anggota memakai `type="email"`. Pencarian, filter, dialog, dan konfirmasi ada di `public/assets/app.js`. Kecilkan jendela browser untuk memperlihatkan perubahan tata letak yang diatur lewat media query di `public/assets/styles.css`.

Di server, `src/Support/Validation.php` memeriksa ulang isian. Class layanan mengecek aturan seperti email ganda dan batas pinjaman. Form yang mengubah data dikirim lewat POST dan diperiksa dengan token CSRF. Teks dari pengguna ditampilkan lewat fungsi `e()` agar dibaca sebagai teks biasa.

Buka `src/Repositories/JsonStore.php` untuk melihat cara aplikasi membaca dan menyimpan `books`, `members`, `loans`, dan `meta`. `SeedData.php` menyiapkan data contoh jika file JSON belum ada. File data berada di luar `public/`. Saat menyimpan perubahan, aplikasi memakai file lock dan mengganti file data setelah penulisan selesai.

## Tes sebelum presentasi

```bash
php tests/test_app.php
```

Tes mencakup penolakan email duplikat, batas tiga pinjaman aktif, pengembalian, dan perbedaan tenggat e-book reguler dan premium.
