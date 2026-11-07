-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 06, 2026 at 06:58 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_ecommerce_sepatu`
--
CREATE DATABASE IF NOT EXISTS `db_ecommerce_sepatu` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `db_ecommerce_sepatu`;

-- --------------------------------------------------------

--
-- Table structure for table `brand`
--

CREATE TABLE `brand` (
  `kd_brand` int(11) NOT NULL,
  `nm_brand` varchar(225) NOT NULL,
  `keterangan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `brand`
--

INSERT INTO `brand` (`kd_brand`, `nm_brand`, `keterangan`) VALUES
(1, 'Adidas', 'Brand sepatu olahraga dan lifestyle'),
(2, 'Nike', 'Brand sepatu olahraga dan lifestyle'),
(3, 'New Balance', 'Brand sepatu olahraga dan lifestyle'),
(4, 'Puma', 'Brand sepatu olahraga'),
(5, 'Salomon', 'Brand sepatu outdoor dan trail'),
(6, 'Converse', 'Brand sepatu kasual'),
(9, 'Vans', 'Brand sepatu kasual');

-- --------------------------------------------------------

--
-- Table structure for table `detail_pembayaran`
--

CREATE TABLE `detail_pembayaran` (
  `kd_detail_pembayaran` int(11) NOT NULL,
  `kd_pembayaran` int(11) NOT NULL,
  `kd_produk` int(11) NOT NULL,
  `harga_jual` decimal(12,2) NOT NULL,
  `harga_beli` decimal(12,2) NOT NULL DEFAULT 0.00,
  `qty` int(11) NOT NULL
) ;

--
-- Dumping data for table `detail_pembayaran`
--

INSERT INTO `detail_pembayaran` (`kd_detail_pembayaran`, `kd_pembayaran`, `kd_produk`, `harga_jual`, `harga_beli`, `qty`) VALUES
(1, 1, 1, 1699000.00, 1100000.00, 2),
(2, 2, 2, 1899000.00, 1250000.00, 1),
(3, 2, 3, 2049000.00, 1400000.00, 1),
(4, 3, 3, 2049000.00, 1400000.00, 1),
(5, 3, 5, 3999000.00, 2800000.00, 1),
(6, 3, 6, 1899000.00, 1250000.00, 1),
(7, 3, 4, 1799000.00, 1200000.00, 1),
(8, 4, 3, 2049000.00, 1400000.00, 1),
(9, 4, 8, 1117658.00, 1699000.00, 1),
(10, 5, 8, 1117658.00, 1699000.00, 1),
(11, 6, 9, 5000000.00, 4000000.00, 1),
(12, 6, 4, 1799000.00, 1200000.00, 1),
(13, 6, 3, 2049000.00, 1400000.00, 1);

-- --------------------------------------------------------

--
-- Table structure for table `distributor`
--

CREATE TABLE `distributor` (
  `kd_distributor` int(11) NOT NULL,
  `nm_distributor` varchar(225) NOT NULL,
  `alamat` text DEFAULT NULL,
  `no_telp` varchar(20) DEFAULT NULL,
  `email` varchar(225) DEFAULT NULL,
  `nm_sales` varchar(225) DEFAULT NULL,
  `hp_sales` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `distributor`
--

INSERT INTO `distributor` (`kd_distributor`, `nm_distributor`, `alamat`, `no_telp`, `email`, `nm_sales`, `hp_sales`) VALUES
(1, 'Distributor Sepatu Nusantara', 'Bandung, Jawa Barat', '0221234567', 'distributor@example.com', 'Andi', '081234567890');

-- --------------------------------------------------------

--
-- Table structure for table `foto`
--

CREATE TABLE `foto` (
  `kd_foto` int(11) NOT NULL,
  `kd_produk` int(11) NOT NULL,
  `nm_foto` varchar(225) DEFAULT NULL,
  `path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `foto`
--

INSERT INTO `foto` (`kd_foto`, `kd_produk`, `nm_foto`, `path`) VALUES
(21, 5, 'jackson-0659-6445825-1.webp', 'uploads/produk/produk_6adefc6f1181d.webp'),
(23, 2, 'new-balance-9617-1863605-1.webp', 'uploads/produk/produk_6adefc8bcfbea.webp'),
(26, 1, 'adidas-4371-9734232-1.webp', 'uploads/produk/produk_6adf2fea67c19.webp'),
(29, 8, 'Vans Classics Old Skool Mens  Shoes.jpeg', 'uploads/produk/produk_6ae05dd2ea8a3.jpeg'),
(30, 7, 'Brands A-Z_ Dress Clothes for Men _ Simons Canada.jpeg', 'uploads/produk/produk_6ae05ddd88bed.jpeg'),
(32, 6, 'adidas Originals Samba OG in Black, White, & Gum _ FWRD.jpeg', 'uploads/produk/produk_6ae05e6f4be15.jpeg'),
(34, 3, 'Deals on Men\'s Speedcat OG \'Black White\' on Sneakers_com.jpeg', 'uploads/produk/produk_6ae05efab75ff.jpeg'),
(35, 4, 'WhatsApp Image 2026-10-06 at 19.51.56.jpeg', 'uploads/produk/produk_6ae05f1413d75.jpeg'),
(36, 9, 'Nike Air Force 1 \'07 Men\'s Shoes_ Nike IN.jpeg', 'uploads/produk/produk_6ae5943014637.jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `kd_kategori` int(11) NOT NULL,
  `nm_kategori` varchar(225) NOT NULL,
  `keterangan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`kd_kategori`, `nm_kategori`, `keterangan`) VALUES
(1, 'Lifestyle', 'Sepatu untuk kegiatan sehari-hari'),
(2, 'Running', 'Sepatu untuk berlari'),
(3, 'Training', 'Sepatu untuk latihan olahraga'),
(4, 'Outdoor', 'Sepatu untuk kegiatan luar ruangan'),
(5, 'Basketball', 'Sepatu untuk bermain basket');

-- --------------------------------------------------------

--
-- Table structure for table `keranjang_belanja`
--

CREATE TABLE `keranjang_belanja` (
  `kd_keranjang_belanja` int(11) NOT NULL,
  `kd_user` int(11) NOT NULL,
  `tgl_keranjang` timestamp NOT NULL DEFAULT current_timestamp(),
  `kd_produk` int(11) NOT NULL,
  `qty` int(11) NOT NULL DEFAULT 1
) ;

--
-- Dumping data for table `keranjang_belanja`
--

INSERT INTO `keranjang_belanja` (`kd_keranjang_belanja`, `kd_user`, `tgl_keranjang`, `kd_produk`, `qty`) VALUES
(13, 7, '2026-10-31 10:29:56', 8, 1);

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran`
--

CREATE TABLE `pembayaran` (
  `kd_pembayaran` int(11) NOT NULL,
  `kd_user` int(11) NOT NULL,
  `invoice` varchar(30) NOT NULL,
  `tgl_bayar` datetime DEFAULT current_timestamp(),
  `status` enum('menunggu','diproses','dikirim','selesai','dibatalkan') NOT NULL DEFAULT 'menunggu'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pembayaran`
--

INSERT INTO `pembayaran` (`kd_pembayaran`, `kd_user`, `invoice`, `tgl_bayar`, `status`) VALUES
(1, 1, 'INV-0001', '2026-10-19 18:11:59', 'selesai'),
(2, 1, 'INV-0002', '2026-10-19 18:11:59', 'selesai'),
(3, 3, 'INV-20261025-F4CECA', '2026-10-25 23:02:14', 'selesai'),
(4, 5, 'INV-20261026-3C346C', '2026-10-26 18:01:28', 'selesai'),
(5, 6, 'INV-20261031-25FEE5', '2026-10-31 11:33:49', 'selesai'),
(6, 7, 'INV-20261031-58DFBA', '2026-10-31 14:12:51', 'selesai');

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `kd_produk` int(11) NOT NULL,
  `kd_ukuran` int(11) NOT NULL,
  `kd_brand` int(11) NOT NULL,
  `kd_kategori` int(11) NOT NULL,
  `kd_warna` int(11) NOT NULL,
  `kd_distributor` int(11) DEFAULT NULL,
  `nama_produk` varchar(225) NOT NULL,
  `harga_jual` decimal(12,2) NOT NULL DEFAULT 0.00,
  `harga_beli` decimal(12,2) NOT NULL DEFAULT 0.00,
  `deskripsi` text DEFAULT NULL,
  `stok` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`kd_produk`, `kd_ukuran`, `kd_brand`, `kd_kategori`, `kd_warna`, `kd_distributor`, `nama_produk`, `harga_jual`, `harga_beli`, `deskripsi`, `stok`, `created_at`) VALUES
(1, 2, 1, 1, 3, 1, 'Adidas Samba OG Cloud White Black', 1699000.00, 1100000.00, 'Sepatu lifestyle klasik Adidas Samba.', 10, '2026-10-19 07:39:16'),
(2, 3, 3, 1, 4, 1, 'New Balance 530 White Silver Navy', 1899000.00, 1250000.00, 'Sepatu lifestyle dengan desain retro running.', 8, '2026-10-19 07:39:16'),
(3, 2, 4, 1, 1, 1, 'Puma Speedcat OG Black White', 2049000.00, 1400000.00, 'Sepatu lifestyle bergaya motorsport.', 4, '2026-10-19 07:39:16'),
(4, 2, 4, 1, 5, 1, 'Puma Speedcat OG Pink White', 1799000.00, 1200000.00, 'Sepatu lifestyle dengan warna pink.', 3, '2026-10-19 07:39:16'),
(5, 4, 5, 4, 1, 1, 'Salomon XT-6 Triple Black', 3999000.00, 2800000.00, 'Sepatu outdoor dan trail running.', 3, '2026-10-19 07:39:16'),
(6, 3, 1, 1, 3, 1, 'Adidas Samba OG Black White Gum', 1899000.00, 1250000.00, 'Sepatu lifestyle Adidas dengan outsole gum.', 5, '2026-10-19 07:39:16'),
(7, 3, 6, 1, 3, 1, 'Converse Chuck Taylor All-Star 70\'s High Black', 1899000.00, 1250000.00, 'Sejak 1917, sepatu Chuck Taylor All Star High Top telah menjadi ikon dari barang harian yang keren. Anda sudah tau detailnya yaitu upper kanvas, outsole diamond, dan tentu saja patch pergelangan kaki Chuck Taylor. Insole OrthoLite menjadikannya tetap nyaman. Tidak pernah rumit. Selalu asli.\r\n\r\nFitur dan Benefit\r\nSneaker high-top dengan upper kanvas\r\nSiluet ikonik\r\nPatch pergelangan kaki bintang dengan brand\r\nInsole OrthoLite untuk kenyamanan\r\nJahitan outsole diamond', 12, '2026-10-20 17:31:34'),
(8, 3, 9, 1, 3, 1, 'Vans old skool', 1117658.00, 1699000.00, 'sepatu sneaker low-top ikonik yang pertama kali dirilis pada tahun 1977 dan terkenal sebagai desain pertama Vans yang menampilkan garis samping khas (Sidestripe atau Jazz Stripe).', 98, '2026-10-26 10:57:27'),
(9, 3, 2, 1, 2, 1, 'nike air force 1', 5000000.00, 4000000.00, 'GONDRONG KONTOL', 11999, '2026-10-31 07:09:36');

-- --------------------------------------------------------

--
-- Table structure for table `stok_produk`
--

CREATE TABLE `stok_produk` (
  `kd_stok` int(11) NOT NULL,
  `tgl_masuk` date NOT NULL,
  `kd_barang` int(11) NOT NULL,
  `harga_beli` decimal(12,2) NOT NULL DEFAULT 0.00,
  `harga_jual` decimal(12,2) NOT NULL DEFAULT 0.00,
  `qty` int(11) NOT NULL DEFAULT 0
) ;

--
-- Dumping data for table `stok_produk`
--

INSERT INTO `stok_produk` (`kd_stok`, `tgl_masuk`, `kd_barang`, `harga_beli`, `harga_jual`, `qty`) VALUES
(1, '2026-10-19', 1, 1100000.00, 1699000.00, 10),
(2, '2026-10-19', 2, 1250000.00, 1899000.00, 8),
(3, '2026-10-19', 3, 1400000.00, 2049000.00, 7),
(4, '2026-10-19', 4, 1200000.00, 1799000.00, 5),
(5, '2026-10-19', 5, 2800000.00, 3999000.00, 4),
(6, '2026-10-19', 6, 1250000.00, 1899000.00, 6),
(7, '2026-10-21', 7, 600.00, 768.00, 12),
(8, '2026-10-26', 8, 1699000.00, 1117658.00, 100),
(9, '2026-10-31', 9, 4000000.00, 5000000.00, 12000);

-- --------------------------------------------------------

--
-- Table structure for table `ukuran`
--

CREATE TABLE `ukuran` (
  `kd_ukuran` int(11) NOT NULL,
  `nm_ukuran` varchar(20) NOT NULL,
  `keterangan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ukuran`
--

INSERT INTO `ukuran` (`kd_ukuran`, `nm_ukuran`, `keterangan`) VALUES
(1, '39', 'EU 39'),
(2, '40', 'EU 40'),
(3, '41', 'EU 41'),
(4, '42', 'EU 42'),
(5, '43', 'EU 43'),
(6, '44', 'EU 44'),
(7, '45', 'EU 45');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `kd_user` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(225) NOT NULL,
  `no_telpon` varchar(20) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `foto_ktp` varchar(255) DEFAULT NULL,
  `nik` varchar(16) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `role` enum('admin','customer') NOT NULL DEFAULT 'customer'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`kd_user`, `username`, `password`, `email`, `no_telpon`, `alamat`, `foto_ktp`, `nik`, `created_at`, `role`) VALUES
(1, 'alam_demo', 'GANTI_DENGAN_HASH_PASSWORD', 'alam@example.com', '081234567890', 'Rajeg, Tangerang', NULL, NULL, '2026-10-19 07:39:16', 'customer'),
(2, 'admin', '$2y$10$wzKrxy5/z56F5XkWL5j4Wu2xJ7b/SmXq5YIltpXCjxeAqrQsWSguS', 'admin@tokosepatu.test', NULL, NULL, NULL, NULL, '2026-10-19 10:49:04', 'admin'),
(3, 'mimiw', '$2y$10$NlMPY9Xq71wmhIkiff4qeOX597XEbPsoA/LWzGHKrjEqStxsUHlSq', 'feliciathegoat230@gmail.com', '081283781237', 'kampung durian runtuh', NULL, NULL, '2026-10-19 12:32:53', 'customer'),
(5, 'gondrongdekil', '$2y$10$QlXSncLHA/v9cl3lCf6oKO1VQ.V/X64tMh.qBq/pb0kPlJ4uyUxRi', 'gondrong22@gmail.com', '0867236746', 'gacoan bumi indah', NULL, NULL, '2026-10-26 11:00:28', 'customer'),
(6, 'gondrong', '$2y$10$cfczTh9O8pyFtCJV8UiRSO7F.XsJ8be1OyTGzq.E86tz5CFHCwe.C', 'gondrong12@gmail.com', '0829288494', 'kampus', NULL, NULL, '2026-10-31 04:32:49', 'customer'),
(7, 'gimon', '$2y$10$Fv3VQVvP6OVZi33R8p1cquPraJHcCzL43uGh6Zf3SBXDN7hxxROiG', 'gimonsukamutiara12@gmail.com', '0812787957894', 'depan qodar', NULL, NULL, '2026-10-31 07:11:52', 'customer');

-- --------------------------------------------------------

--
-- Table structure for table `warna`
--

CREATE TABLE `warna` (
  `kd_warna` int(11) NOT NULL,
  `nm_warna` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `warna`
--

INSERT INTO `warna` (`kd_warna`, `nm_warna`) VALUES
(1, 'Black'),
(3, 'Black White'),
(7, 'Brown'),
(8, 'Green'),
(4, 'Grey'),
(6, 'Navy'),
(5, 'Pink'),
(2, 'White');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `brand`
--
ALTER TABLE `brand`
  ADD PRIMARY KEY (`kd_brand`),
  ADD UNIQUE KEY `nm_brand` (`nm_brand`);

--
-- Indexes for table `detail_pembayaran`
--
ALTER TABLE `detail_pembayaran`
  ADD PRIMARY KEY (`kd_detail_pembayaran`),
  ADD KEY `fk_detail_pembayaran` (`kd_pembayaran`),
  ADD KEY `fk_detail_produk` (`kd_produk`);

--
-- Indexes for table `distributor`
--
ALTER TABLE `distributor`
  ADD PRIMARY KEY (`kd_distributor`);

--
-- Indexes for table `foto`
--
ALTER TABLE `foto`
  ADD PRIMARY KEY (`kd_foto`),
  ADD KEY `fk_foto_produk` (`kd_produk`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`kd_kategori`),
  ADD UNIQUE KEY `nm_kategori` (`nm_kategori`);

--
-- Indexes for table `keranjang_belanja`
--
ALTER TABLE `keranjang_belanja`
  ADD PRIMARY KEY (`kd_keranjang_belanja`),
  ADD KEY `fk_keranjang_user` (`kd_user`),
  ADD KEY `fk_keranjang_produk` (`kd_produk`);

--
-- Indexes for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`kd_pembayaran`),
  ADD UNIQUE KEY `invoice` (`invoice`),
  ADD KEY `fk_pembayaran_user` (`kd_user`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`kd_produk`),
  ADD KEY `fk_produk_ukuran` (`kd_ukuran`),
  ADD KEY `fk_produk_brand` (`kd_brand`),
  ADD KEY `fk_produk_kategori` (`kd_kategori`),
  ADD KEY `fk_produk_warna` (`kd_warna`),
  ADD KEY `fk_produk_distributor` (`kd_distributor`);

--
-- Indexes for table `stok_produk`
--
ALTER TABLE `stok_produk`
  ADD PRIMARY KEY (`kd_stok`),
  ADD KEY `fk_stok_produk` (`kd_barang`);

--
-- Indexes for table `ukuran`
--
ALTER TABLE `ukuran`
  ADD PRIMARY KEY (`kd_ukuran`),
  ADD UNIQUE KEY `nm_ukuran` (`nm_ukuran`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`kd_user`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `nik` (`nik`);

--
-- Indexes for table `warna`
--
ALTER TABLE `warna`
  ADD PRIMARY KEY (`kd_warna`),
  ADD UNIQUE KEY `nm_warna` (`nm_warna`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `brand`
--
ALTER TABLE `brand`
  MODIFY `kd_brand` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `detail_pembayaran`
--
ALTER TABLE `detail_pembayaran`
  MODIFY `kd_detail_pembayaran` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `distributor`
--
ALTER TABLE `distributor`
  MODIFY `kd_distributor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `foto`
--
ALTER TABLE `foto`
  MODIFY `kd_foto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `kd_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `keranjang_belanja`
--
ALTER TABLE `keranjang_belanja`
  MODIFY `kd_keranjang_belanja` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `kd_pembayaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `kd_produk` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stok_produk`
--
ALTER TABLE `stok_produk`
  MODIFY `kd_stok` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ukuran`
--
ALTER TABLE `ukuran`
  MODIFY `kd_ukuran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `kd_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `warna`
--
ALTER TABLE `warna`
  MODIFY `kd_warna` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `detail_pembayaran`
--
ALTER TABLE `detail_pembayaran`
  ADD CONSTRAINT `fk_detail_pembayaran` FOREIGN KEY (`kd_pembayaran`) REFERENCES `pembayaran` (`kd_pembayaran`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_detail_produk` FOREIGN KEY (`kd_produk`) REFERENCES `produk` (`kd_produk`) ON UPDATE CASCADE;

--
-- Constraints for table `foto`
--
ALTER TABLE `foto`
  ADD CONSTRAINT `fk_foto_produk` FOREIGN KEY (`kd_produk`) REFERENCES `produk` (`kd_produk`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `keranjang_belanja`
--
ALTER TABLE `keranjang_belanja`
  ADD CONSTRAINT `fk_keranjang_produk` FOREIGN KEY (`kd_produk`) REFERENCES `produk` (`kd_produk`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_keranjang_user` FOREIGN KEY (`kd_user`) REFERENCES `user` (`kd_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `fk_pembayaran_user` FOREIGN KEY (`kd_user`) REFERENCES `user` (`kd_user`) ON UPDATE CASCADE;

--
-- Constraints for table `produk`
--
ALTER TABLE `produk`
  ADD CONSTRAINT `fk_produk_brand` FOREIGN KEY (`kd_brand`) REFERENCES `brand` (`kd_brand`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_produk_distributor` FOREIGN KEY (`kd_distributor`) REFERENCES `distributor` (`kd_distributor`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_produk_kategori` FOREIGN KEY (`kd_kategori`) REFERENCES `kategori` (`kd_kategori`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_produk_ukuran` FOREIGN KEY (`kd_ukuran`) REFERENCES `ukuran` (`kd_ukuran`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_produk_warna` FOREIGN KEY (`kd_warna`) REFERENCES `warna` (`kd_warna`) ON UPDATE CASCADE;

--
-- Constraints for table `stok_produk`
--
ALTER TABLE `stok_produk`
  ADD CONSTRAINT `fk_stok_produk` FOREIGN KEY (`kd_barang`) REFERENCES `produk` (`kd_produk`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
