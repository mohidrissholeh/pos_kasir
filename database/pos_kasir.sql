-- phpMyAdmin SQL Dump
-- version 4.6.6
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 11, 2025 at 02:16 AM
-- Server version: 5.7.17-log
-- PHP Version: 7.1.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kasir`
--

-- --------------------------------------------------------

--
-- Table structure for table `diskon`
--

CREATE TABLE `diskon` (
  `id_diskon` int(11) NOT NULL,
  `nama_diskon` varchar(100) NOT NULL,
  `jenis_diskon` enum('persentase','nominal') NOT NULL,
  `nilai_diskon` decimal(10,2) NOT NULL,
  `tipe_diskon` enum('manual','otomatis') NOT NULL,
  `kondisi_diskon` enum('produk','kategori','pembayaran','total_belanja') DEFAULT NULL,
  `nilai_kondisi` varchar(255) DEFAULT NULL,
  `tanggal_mulai` date DEFAULT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `status_diskon` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `diskon`
--

INSERT INTO `diskon` (`id_diskon`, `nama_diskon`, `jenis_diskon`, `nilai_diskon`, `tipe_diskon`, `kondisi_diskon`, `nilai_kondisi`, `tanggal_mulai`, `tanggal_selesai`, `status_diskon`, `created_at`, `updated_at`) VALUES
(8, 'Dikon Dessert', 'persentase', '10.00', 'otomatis', 'kategori', '', '2025-05-10', '2025-05-11', 'aktif', '2025-05-10 13:07:48', '2025-05-10 13:07:48'),
(9, 'Diskon 10%', 'persentase', '10.00', 'otomatis', 'total_belanja', '50000', '2025-05-10', '2025-05-11', 'aktif', '2025-05-10 13:11:44', '2025-05-10 13:11:44');

-- --------------------------------------------------------

--
-- Table structure for table `diskon_detail`
--

CREATE TABLE `diskon_detail` (
  `id_diskon_detail` int(11) NOT NULL,
  `id_diskon` int(11) NOT NULL,
  `id_produk` int(11) DEFAULT NULL,
  `id_kategori` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `diskon_detail`
--

INSERT INTO `diskon_detail` (`id_diskon_detail`, `id_diskon`, `id_produk`, `id_kategori`, `created_at`) VALUES
(5, 8, NULL, 4, '2025-05-10 13:07:49');

-- --------------------------------------------------------

--
-- Table structure for table `history_stok`
--

CREATE TABLE `history_stok` (
  `id_history` int(11) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `stok_sebelum` int(11) NOT NULL,
  `stok_sesudah` int(11) NOT NULL,
  `perubahan` int(11) NOT NULL,
  `keterangan` varchar(255) NOT NULL,
  `id_user` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `history_stok`
--

INSERT INTO `history_stok` (`id_history`, `id_produk`, `stok_sebelum`, `stok_sesudah`, `perubahan`, `keterangan`, `id_user`, `created_at`) VALUES
(6, 3, 0, 50, 50, 'Update stok', 1, '2025-05-10 12:59:13'),
(7, 4, 5, 20, 15, 'Update stok', 1, '2025-05-10 12:59:21');

-- --------------------------------------------------------

--
-- Table structure for table `kas_harian`
--

CREATE TABLE `kas_harian` (
  `id_kas` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `kas_awal` decimal(10,2) NOT NULL,
  `total_penjualan` decimal(10,2) NOT NULL DEFAULT '0.00',
  `pemasukan_lain` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_pengeluaran` decimal(10,2) NOT NULL DEFAULT '0.00',
  `kas_akhir` decimal(10,2) NOT NULL,
  `total_laba` decimal(10,2) NOT NULL DEFAULT '0.00',
  `catatan` text,
  `status` enum('draft','selesai') DEFAULT 'draft',
  `id_user` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `kas_harian`
--

INSERT INTO `kas_harian` (`id_kas`, `tanggal`, `kas_awal`, `total_penjualan`, `pemasukan_lain`, `total_pengeluaran`, `kas_akhir`, `total_laba`, `catatan`, `status`, `id_user`, `created_at`, `updated_at`) VALUES
(2, '2025-05-10', '200000.00', '78300.00', '0.00', '100000.00', '178300.00', '-21700.00', '', 'draft', 1, '2025-05-10 13:15:16', '2025-05-10 13:15:16');

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id_kategori` int(11) NOT NULL,
  `nama_kategori` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id_kategori`, `nama_kategori`, `created_at`) VALUES
(1, 'Makanan', '2025-05-07 14:23:27'),
(2, 'Minuman', '2025-05-07 14:23:27'),
(3, 'Snack', '2025-05-07 14:23:27'),
(4, 'Dessert', '2025-05-07 14:23:27');

-- --------------------------------------------------------

--
-- Table structure for table `kategori_pengeluaran`
--

CREATE TABLE `kategori_pengeluaran` (
  `id_kategori_pengeluaran` int(11) NOT NULL,
  `nama_kategori` varchar(100) NOT NULL,
  `deskripsi` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `kategori_pengeluaran`
--

INSERT INTO `kategori_pengeluaran` (`id_kategori_pengeluaran`, `nama_kategori`, `deskripsi`, `created_at`, `updated_at`) VALUES
(1, 'Gaji Karyawan', 'Pembayaran gaji karyawan', '2025-05-09 06:52:51', '2025-05-09 06:52:51'),
(2, 'Listrik', 'Pembayaran tagihan listrik', '2025-05-09 06:52:51', '2025-05-09 06:52:51'),
(3, 'Air', 'Pembayaran tagihan air', '2025-05-09 06:52:51', '2025-05-09 06:52:51'),
(4, 'Internet', 'Pembayaran tagihan internet', '2025-05-09 06:52:51', '2025-05-09 06:52:51'),
(5, 'Belanja Bahan', 'Pembelian bahan baku', '2025-05-09 06:52:51', '2025-05-09 06:52:51'),
(6, 'Sewa Tempat', 'Pembayaran sewa tempat', '2025-05-09 06:52:51', '2025-05-09 06:52:51'),
(7, 'Pajak', 'Pembayaran pajak', '2025-05-09 06:52:51', '2025-05-09 06:52:51'),
(8, 'Lain-lain', 'Pengeluaran lainnya', '2025-05-09 06:52:51', '2025-05-09 06:52:51');

-- --------------------------------------------------------

--
-- Table structure for table `log_laba_harian`
--

CREATE TABLE `log_laba_harian` (
  `id_log` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `laba_bersih` decimal(10,2) NOT NULL,
  `id_sumber` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `modal`
--

CREATE TABLE `modal` (
  `id_modal` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `deskripsi` text NOT NULL,
  `jumlah` decimal(10,2) NOT NULL,
  `status` enum('aktif','nonaktif') DEFAULT 'aktif',
  `id_user` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `modal`
--

INSERT INTO `modal` (`id_modal`, `tanggal`, `deskripsi`, `jumlah`, `status`, `id_user`, `created_at`, `updated_at`) VALUES
(3, '2025-05-10', 'Modal Awal', '1000000.00', 'aktif', 1, '2025-05-10 13:00:24', '2025-05-10 13:00:24');

-- --------------------------------------------------------

--
-- Table structure for table `pemasukan_lain`
--

CREATE TABLE `pemasukan_lain` (
  `id_pemasukan_lain` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `kategori` varchar(100) NOT NULL,
  `keterangan` text NOT NULL,
  `jumlah` decimal(10,2) NOT NULL,
  `id_user` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `pengeluaran`
--

CREATE TABLE `pengeluaran` (
  `id_pengeluaran` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `id_kategori` int(11) NOT NULL,
  `keterangan` text NOT NULL,
  `jumlah` decimal(10,2) NOT NULL,
  `id_user` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `pengeluaran`
--

INSERT INTO `pengeluaran` (`id_pengeluaran`, `tanggal`, `id_kategori`, `keterangan`, `jumlah`, `id_user`, `created_at`, `updated_at`) VALUES
(2, '2025-05-10', 5, 'belanja bahan baku', '100000.00', 1, '2025-05-10 13:01:31', '2025-05-10 13:01:31');

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `id_produk` int(11) NOT NULL,
  `nama_produk` varchar(100) NOT NULL,
  `harga` decimal(10,2) NOT NULL,
  `stok` int(11) NOT NULL DEFAULT '0',
  `gambar` varchar(255) DEFAULT NULL,
  `id_kategori` int(11) DEFAULT NULL,
  `status_produk` enum('y','n') NOT NULL DEFAULT 'y',
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`id_produk`, `nama_produk`, `harga`, `stok`, `gambar`, `id_kategori`, `status_produk`, `created_at`, `updated_at`) VALUES
(1, 'Ayam Goreng', '15000.00', 26, 'uploads/681b6d1d27f0d.jpeg', 1, 'y', '2025-05-07 21:24:29', NULL),
(2, 'Air Mineral', '3500.00', 24, 'uploads/681b6d44a352c.jpeg', 2, 'y', '2025-05-07 21:25:08', NULL),
(3, 'Jus Jeruk', '5000.00', 49, 'uploads/681d753e8cba9.jpeg', 2, 'y', '2025-05-09 10:23:42', NULL),
(4, 'Blackforest', '15000.00', 18, 'uploads/681d76e3a55a3.jpeg', 4, 'y', '2025-05-09 10:30:43', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sumber_laba`
--

CREATE TABLE `sumber_laba` (
  `id_sumber_laba` int(11) NOT NULL,
  `nama_sumber` varchar(100) NOT NULL,
  `deskripsi` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sumber_laba`
--

INSERT INTO `sumber_laba` (`id_sumber_laba`, `nama_sumber`, `deskripsi`, `created_at`, `updated_at`) VALUES
(1, 'Penjualan Langsung', 'Laba dari penjualan di kasir', '2025-05-09 06:52:51', '2025-05-09 06:52:51'),
(2, 'QRIS', 'Laba dari pembayaran QRIS', '2025-05-09 06:52:51', '2025-05-09 06:52:51'),
(3, 'ShopeeFood', 'Laba dari penjualan di ShopeeFood', '2025-05-09 06:52:51', '2025-05-09 06:52:51'),
(4, 'GoFood', 'Laba dari penjualan di GoFood', '2025-05-09 06:52:51', '2025-05-09 06:52:51'),
(5, 'GrabFood', 'Laba dari penjualan di GrabFood', '2025-05-09 06:52:51', '2025-05-09 06:52:51'),
(6, 'Lain-lain', 'Sumber laba lainnya', '2025-05-09 06:52:51', '2025-05-09 06:52:51');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id_transaksi` int(11) NOT NULL,
  `nomor_transaksi` varchar(20) NOT NULL,
  `id_user` int(11) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `metode_pembayaran` enum('cash','qris','transfer') NOT NULL,
  `jumlah_uang` decimal(10,2) DEFAULT NULL,
  `status` enum('selesai','batal') NOT NULL DEFAULT 'selesai',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`id_transaksi`, `nomor_transaksi`, `id_user`, `total`, `metode_pembayaran`, `jumlah_uang`, `status`, `created_at`, `updated_at`) VALUES
(17, 'TRX20250510130839711', 2, '27000.00', 'cash', '30000.00', 'selesai', '2025-05-10 13:08:39', '2025-05-10 13:08:39'),
(18, 'TRX20250510131251101', 2, '51300.00', 'transfer', '0.00', 'selesai', '2025-05-10 13:12:51', '2025-05-10 13:12:51');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi_detail`
--

CREATE TABLE `transaksi_detail` (
  `id_detail` int(11) NOT NULL,
  `id_transaksi` int(11) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `harga` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `transaksi_detail`
--

INSERT INTO `transaksi_detail` (`id_detail`, `id_transaksi`, `id_produk`, `jumlah`, `harga`, `subtotal`, `created_at`) VALUES
(30, 17, 4, 2, '15000.00', '30000.00', '2025-05-10 13:08:39'),
(31, 18, 2, 2, '3500.00', '7000.00', '2025-05-10 13:12:51'),
(32, 18, 1, 3, '15000.00', '45000.00', '2025-05-10 13:12:51'),
(33, 18, 3, 1, '5000.00', '5000.00', '2025-05-10 13:12:51');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi_diskon`
--

CREATE TABLE `transaksi_diskon` (
  `id_transaksi_diskon` int(11) NOT NULL,
  `id_transaksi` int(11) NOT NULL,
  `id_diskon` int(11) NOT NULL,
  `nilai_diskon` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `transaksi_diskon`
--

INSERT INTO `transaksi_diskon` (`id_transaksi_diskon`, `id_transaksi`, `id_diskon`, `nilai_diskon`, `created_at`) VALUES
(9, 17, 8, '3000.00', '2025-05-10 13:08:39'),
(10, 18, 9, '5700.00', '2025-05-10 13:12:51');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id_user` int(11) NOT NULL,
  `nama_user` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(32) NOT NULL,
  `role` enum('admin','kasir') NOT NULL,
  `status_user` enum('y','n') NOT NULL DEFAULT 'y',
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id_user`, `nama_user`, `username`, `password`, `role`, `status_user`, `created_at`, `updated_at`) VALUES
(1, 'Administrator', 'admin', '0192023a7bbd73250516f069df18b500', 'admin', 'y', '2025-05-07 21:23:27', NULL),
(2, 'Kasir', 'kasir', 'f9681ff3b1e808e3bbb6f9d80ae62fd1', 'kasir', 'y', '2025-05-08 15:51:41', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `diskon`
--
ALTER TABLE `diskon`
  ADD PRIMARY KEY (`id_diskon`);

--
-- Indexes for table `diskon_detail`
--
ALTER TABLE `diskon_detail`
  ADD PRIMARY KEY (`id_diskon_detail`),
  ADD KEY `id_diskon` (`id_diskon`),
  ADD KEY `id_produk` (`id_produk`),
  ADD KEY `id_kategori` (`id_kategori`);

--
-- Indexes for table `history_stok`
--
ALTER TABLE `history_stok`
  ADD PRIMARY KEY (`id_history`),
  ADD KEY `id_produk` (`id_produk`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `kas_harian`
--
ALTER TABLE `kas_harian`
  ADD PRIMARY KEY (`id_kas`),
  ADD UNIQUE KEY `tanggal` (`tanggal`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indexes for table `kategori_pengeluaran`
--
ALTER TABLE `kategori_pengeluaran`
  ADD PRIMARY KEY (`id_kategori_pengeluaran`);

--
-- Indexes for table `log_laba_harian`
--
ALTER TABLE `log_laba_harian`
  ADD PRIMARY KEY (`id_log`),
  ADD KEY `id_sumber` (`id_sumber`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `modal`
--
ALTER TABLE `modal`
  ADD PRIMARY KEY (`id_modal`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `pemasukan_lain`
--
ALTER TABLE `pemasukan_lain`
  ADD PRIMARY KEY (`id_pemasukan_lain`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `pengeluaran`
--
ALTER TABLE `pengeluaran`
  ADD PRIMARY KEY (`id_pengeluaran`),
  ADD KEY `id_kategori` (`id_kategori`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id_produk`),
  ADD KEY `id_kategori` (`id_kategori`);

--
-- Indexes for table `sumber_laba`
--
ALTER TABLE `sumber_laba`
  ADD PRIMARY KEY (`id_sumber_laba`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id_transaksi`),
  ADD UNIQUE KEY `nomor_transaksi` (`nomor_transaksi`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `transaksi_detail`
--
ALTER TABLE `transaksi_detail`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `id_transaksi` (`id_transaksi`),
  ADD KEY `id_produk` (`id_produk`);

--
-- Indexes for table `transaksi_diskon`
--
ALTER TABLE `transaksi_diskon`
  ADD PRIMARY KEY (`id_transaksi_diskon`),
  ADD KEY `id_transaksi` (`id_transaksi`),
  ADD KEY `id_diskon` (`id_diskon`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `diskon`
--
ALTER TABLE `diskon`
  MODIFY `id_diskon` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `diskon_detail`
--
ALTER TABLE `diskon_detail`
  MODIFY `id_diskon_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `history_stok`
--
ALTER TABLE `history_stok`
  MODIFY `id_history` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `kas_harian`
--
ALTER TABLE `kas_harian`
  MODIFY `id_kas` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `kategori_pengeluaran`
--
ALTER TABLE `kategori_pengeluaran`
  MODIFY `id_kategori_pengeluaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `log_laba_harian`
--
ALTER TABLE `log_laba_harian`
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `modal`
--
ALTER TABLE `modal`
  MODIFY `id_modal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `pemasukan_lain`
--
ALTER TABLE `pemasukan_lain`
  MODIFY `id_pemasukan_lain` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `pengeluaran`
--
ALTER TABLE `pengeluaran`
  MODIFY `id_pengeluaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `id_produk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `sumber_laba`
--
ALTER TABLE `sumber_laba`
  MODIFY `id_sumber_laba` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id_transaksi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
--
-- AUTO_INCREMENT for table `transaksi_detail`
--
ALTER TABLE `transaksi_detail`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;
--
-- AUTO_INCREMENT for table `transaksi_diskon`
--
ALTER TABLE `transaksi_diskon`
  MODIFY `id_transaksi_diskon` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `diskon_detail`
--
ALTER TABLE `diskon_detail`
  ADD CONSTRAINT `diskon_detail_ibfk_1` FOREIGN KEY (`id_diskon`) REFERENCES `diskon` (`id_diskon`),
  ADD CONSTRAINT `diskon_detail_ibfk_2` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`),
  ADD CONSTRAINT `diskon_detail_ibfk_3` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`);

--
-- Constraints for table `history_stok`
--
ALTER TABLE `history_stok`
  ADD CONSTRAINT `history_stok_ibfk_1` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`),
  ADD CONSTRAINT `history_stok_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`);

--
-- Constraints for table `kas_harian`
--
ALTER TABLE `kas_harian`
  ADD CONSTRAINT `kas_harian_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`);

--
-- Constraints for table `log_laba_harian`
--
ALTER TABLE `log_laba_harian`
  ADD CONSTRAINT `log_laba_harian_ibfk_1` FOREIGN KEY (`id_sumber`) REFERENCES `sumber_laba` (`id_sumber_laba`),
  ADD CONSTRAINT `log_laba_harian_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`);

--
-- Constraints for table `modal`
--
ALTER TABLE `modal`
  ADD CONSTRAINT `modal_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`);

--
-- Constraints for table `pemasukan_lain`
--
ALTER TABLE `pemasukan_lain`
  ADD CONSTRAINT `pemasukan_lain_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`);

--
-- Constraints for table `pengeluaran`
--
ALTER TABLE `pengeluaran`
  ADD CONSTRAINT `pengeluaran_ibfk_1` FOREIGN KEY (`id_kategori`) REFERENCES `kategori_pengeluaran` (`id_kategori_pengeluaran`),
  ADD CONSTRAINT `pengeluaran_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`);

--
-- Constraints for table `produk`
--
ALTER TABLE `produk`
  ADD CONSTRAINT `produk_ibfk_1` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`) ON DELETE SET NULL;

--
-- Constraints for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`);

--
-- Constraints for table `transaksi_detail`
--
ALTER TABLE `transaksi_detail`
  ADD CONSTRAINT `transaksi_detail_ibfk_1` FOREIGN KEY (`id_transaksi`) REFERENCES `transaksi` (`id_transaksi`),
  ADD CONSTRAINT `transaksi_detail_ibfk_2` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`);

--
-- Constraints for table `transaksi_diskon`
--
ALTER TABLE `transaksi_diskon`
  ADD CONSTRAINT `transaksi_diskon_ibfk_1` FOREIGN KEY (`id_transaksi`) REFERENCES `transaksi` (`id_transaksi`),
  ADD CONSTRAINT `transaksi_diskon_ibfk_2` FOREIGN KEY (`id_diskon`) REFERENCES `diskon` (`id_diskon`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
