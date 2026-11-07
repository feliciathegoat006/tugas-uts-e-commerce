# Footstep - Panel Admin E-Commerce Sepatu

Database dan panel admin untuk toko sepatu online Footstep.
Dibuat dengan PHP native, MySQL, dan AdminLTE 4.

## Anggota Kelompok
- Muhammad Dafa Alamsyah (2522105057)
- Muhammad Rasyah (2522105031)
- frido reifan hermawan (2522105040)

## Fitur Panel Admin
- Login admin (password di-hash) dan dashboard ringkasan
- CRUD: brand, kategori, ukuran, warna, distributor
- Produk dengan upload banyak foto
- Pesanan: lihat detail dan ubah status
- Daftar user

## Cara Menjalankan
1. Install XAMPP, jalankan Apache dan MySQL.
2. Download repo ini (Code, Download ZIP), extract ke `C:\xampp\htdocs\`,
   lalu **ganti nama foldernya menjadi `toko-sepatu`** (nama harus persis).
3. Buka phpMyAdmin (http://localhost/phpmyadmin), klik Import,
   pilih file `database/db_ecommerce_sepatu.sql`, klik Go.
4. Buka http://localhost/toko-sepatu/login.php

## Akun Demo
- Username: `admin`
- Password: `admin321`

## Teknologi
PHP, MySQL, HTML, CSS, JavaScript, AdminLTE 4 (Bootstrap 5), DataTables

## Database
12 tabel: user, distributor, brand, kategori, warna, ukuran, produk, foto,
stok_produk, keranjang_belanja, pembayaran, detail_pembayaran.
ERD ada di `database/erd.png`.