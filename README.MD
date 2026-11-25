# Insightly

**Insightly** adalah website untuk membantu perusahaan/brand mengelola feedback pelanggan, melihat rating, dan memahami masukan pelanggan melalui data yang tersimpan.

Website ini ditujukan untuk penggunaan internal perusahaan/brand, sehingga pelanggan tidak perlu memiliki akun atau role tersendiri.

## Fitur

- **Manajemen Feedback** — tambah, edit, hapus, dan lihat feedback pelanggan.
- **Rating Pelanggan** — menyimpan rating pelanggan sebagai bahan evaluasi.
- **Kategori Feedback** — mengelompokkan feedback berdasarkan kategori.
- **Pencarian dan Filter** — mencari feedback berdasarkan nama pelanggan atau komentar, serta memfilter berdasarkan tanggal, kategori, dan rating.
- **Insights** — menampilkan ringkasan dan analisis feedback, seperti rating, kategori, tren feedback, dan sentimen.
- **Export CSV** — mengekspor data feedback untuk kebutuhan pengolahan lebih lanjut.

## Teknologi

- PHP
- MySQL
- HTML
- CSS
- JavaScript
- XAMPP (untuk pengembangan lokal)

## Instalasi dan Menjalankan

1. Pastikan **XAMPP** sudah terinstal.
2. Jalankan **Apache** dan **MySQL** melalui XAMPP Control Panel.
3. Salin folder project `insightly` ke folder `htdocs`.
4. Buka phpMyAdmin melalui `http://localhost/phpmyadmin`.
5. Buat database sesuai nama database yang digunakan pada project.
6. Import file SQL database project melalui phpMyAdmin.
7. Sesuaikan konfigurasi koneksi database pada file `config/database.php`.
8. Buka website melalui browser:

   ```text
   http://localhost/insightly/
   ```

Link Website:
```text
https://insightly-web.site.je/
```

## Penggunaan

Insightly digunakan oleh pihak perusahaan/brand untuk mencatat dan mengelola feedback pelanggan. Data feedback yang terkumpul dapat dilihat melalui halaman Feedback dan dianalisis melalui halaman Insights.

## Struktur Halaman

- **Beranda** — halaman pengenalan Insightly dan akses ke fitur utama.
- **Feedback** — mengelola feedback pelanggan dan melakukan pencarian/filter data.
- **Insights** — melihat ringkasan dan analisis data feedback.

## Catatan

- Project ini dikembangkan sebagai website pengelolaan feedback pelanggan.
- Nama database, struktur tabel, dan konfigurasi koneksi mengikuti file database project.
- Jika dijalankan di lingkungan lain, sesuaikan konfigurasi database dan server PHP.

---

*Insightly — Customer Feedback Management*
