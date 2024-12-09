-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 09, 2024 at 07:25 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `inventory`
--

-- --------------------------------------------------------

--
-- Table structure for table `barang`
--

CREATE TABLE `barang` (
  `id` int(11) NOT NULL,
  `nama_barang` varchar(100) NOT NULL,
  `merk` varchar(50) NOT NULL,
  `id_kategori` int(15) NOT NULL,
  `id_ruangan` int(15) NOT NULL,
  `stok` int(11) NOT NULL DEFAULT 0,
  `asal_perolehan` varchar(200) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `barang`
--

INSERT INTO `barang` (`id`, `nama_barang`, `merk`, `id_kategori`, `id_ruangan`, `stok`, `asal_perolehan`, `created_at`, `updated_at`) VALUES
(5, 'mouse', 'r-one', 1, 1, 0, 'pt indobismar', '2024-11-21 10:42:31', '2024-12-09 06:13:56'),
(6, 'wifi adapter', 'd-link', 1, 1, 20, 'pt indobismar', '2024-11-21 10:43:08', '2024-12-08 13:00:51'),
(7, 'handphone', 'iphone 14 pro max', 1, 2, 20, 'pt indobismar', '2024-11-22 10:02:32', '2024-12-08 13:00:42'),
(8, 'kipas angin', 'nova', 1, 3, 20, 'instansi komdigi', '2024-11-23 13:11:50', '2024-12-08 13:00:35'),
(9, 'obeng', 'visipro', 4, 3, 20, 'axioo', '2024-11-24 02:36:59', '2024-12-08 13:00:15'),
(14, 'bola basket', 'molten', 2, 2, 20, 'instansi komdigi', '2024-11-24 13:56:09', '2024-12-08 13:00:27'),
(15, 'jam tangan', 'rolex', 1, 3, 20, 'pt di belanda', '2024-12-08 13:54:10', '2024-12-08 14:03:31');

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id_kategori` int(20) NOT NULL,
  `kategori` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id_kategori`, `kategori`) VALUES
(1, 'elektronik'),
(2, 'peralatan'),
(4, 'alat');

-- --------------------------------------------------------

--
-- Table structure for table `kondisi`
--

CREATE TABLE `kondisi` (
  `id_kondisi` int(20) NOT NULL,
  `kondisi` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kondisi`
--

INSERT INTO `kondisi` (`id_kondisi`, `kondisi`) VALUES
(1, 'baru'),
(2, 'bekas'),
(5, 'rusak');

-- --------------------------------------------------------

--
-- Table structure for table `ruang`
--

CREATE TABLE `ruang` (
  `id_ruangan` int(15) NOT NULL,
  `ruangan` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ruang`
--

INSERT INTO `ruang` (`id_ruangan`, `ruangan`) VALUES
(1, 'gudang elektronika'),
(2, 'divisi gudang'),
(3, 'gudang peralatan');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id` int(11) NOT NULL,
  `id_barang` int(11) NOT NULL,
  `jenis` enum('masuk','keluar') NOT NULL,
  `jumlah` int(11) NOT NULL,
  `id_kondisi` int(15) NOT NULL,
  `nomer_surat_jalan` varchar(100) NOT NULL,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`id`, `id_barang`, `jenis`, `jumlah`, `id_kondisi`, `nomer_surat_jalan`, `tanggal`) VALUES
(7, 5, 'keluar', 5, 1, 'miso1983829923', '2024-12-08 13:12:00'),
(8, 5, 'keluar', 15, 2, 'iosn320985829050', '2024-12-08 13:42:00'),
(13, 5, 'masuk', 5, 1, 'iosn2032099433435', '2024-12-09 06:13:00'),
(14, 5, 'keluar', 5, 2, 'iosn2032099433435', '2024-12-09 06:14:00');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','manager','user') NOT NULL,
  `gambar` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `gambar`) VALUES
(10, 'admin', 'admin1@gmail.com', '$2y$10$rU9csrLAC1DMrbVpnBM55e0Xt9ivCX04cPfzxsfrp7FX78jARFQLq', 'admin', '43f928178f4efc826a8069d1e0bafa56.png'),
(11, 'manager', 'manager1@gmail.com', '$2y$10$IQMyZCeIqfb9Emf9iTjBWucXPuX5krZoKv3xNT58cXTLsTVIh/EAS', 'manager', '0e9b8c965c64cc91d827372ad86e4431.jpg'),
(12, 'user', 'user1@gmail.com', '$2y$10$NSIXVDsqah7wRReM0KKeUOFP1SV.BaKq3ICPaDFtELGbmQF87bwIq', 'user', 'fd4c39ded59d3ae4379bcc2a8cac7ca7.jpeg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `barang`
--
ALTER TABLE `barang`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indexes for table `kondisi`
--
ALTER TABLE `kondisi`
  ADD PRIMARY KEY (`id_kondisi`);

--
-- Indexes for table `ruang`
--
ALTER TABLE `ruang`
  ADD PRIMARY KEY (`id_ruangan`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_barang` (`id_barang`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `barang`
--
ALTER TABLE `barang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id_kategori` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `kondisi`
--
ALTER TABLE `kondisi`
  MODIFY `id_kondisi` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `ruang`
--
ALTER TABLE `ruang`
  MODIFY `id_ruangan` int(15) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
