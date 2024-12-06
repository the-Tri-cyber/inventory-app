-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 06 Des 2024 pada 10.09
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

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
-- Struktur dari tabel `barang`
--

CREATE TABLE `barang` (
  `id` int(11) NOT NULL,
  `nama_barang` varchar(100) NOT NULL,
  `merk` varchar(50) NOT NULL,
  `id_kategori` int(15) NOT NULL,
  `id_kondisi` int(15) NOT NULL,
  `id_ruangan` int(15) NOT NULL,
  `stok` int(11) NOT NULL DEFAULT 0,
  `harga_satuan` decimal(10,2) NOT NULL,
  `asal_perolehan` varchar(200) NOT NULL,
  `gambar` varchar(200) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `barang`
--

INSERT INTO `barang` (`id`, `nama_barang`, `merk`, `id_kategori`, `id_kondisi`, `id_ruangan`, `stok`, `harga_satuan`, `asal_perolehan`, `gambar`, `created_at`, `updated_at`) VALUES
(5, 'mouse', 'r-one', 1, 2, 1, 40, 180000.00, 'pt indobismar', 'd764e59c2d09fbdba950d1b18e603715.jpg', '2024-11-21 10:42:31', '2024-12-06 09:05:20'),
(6, 'wifi adapter', 'd-link', 1, 1, 1, 100, 100000.00, 'pt indobismar', '83f63a6587445cf1d35b310fb823ff01.jpg', '2024-11-21 10:43:08', '2024-11-24 13:08:10'),
(7, 'handphone', 'iphone 14 pro max', 1, 1, 2, 28, 2000000.00, 'pt indobismar', '948b81b5432b70d32b8ff6efa7b6aed7.jpg', '2024-11-22 10:02:32', '2024-11-24 13:08:35'),
(8, 'kipas angin', 'nova', 1, 1, 3, 12, 200000.00, 'instansi komdigi', 'a3112ee5dfc615f840f62a5a09d2003c.jpg', '2024-11-23 13:11:50', '2024-11-24 13:10:29'),
(9, 'obeng', 'visipro', 4, 4, 3, 11, 200000.00, 'axioo', '93a2673d9b87912c3bdcc86d8f57d6c0.jpg', '2024-11-24 02:36:59', '2024-11-25 03:20:22'),
(14, 'bola basket', 'molten', 2, 2, 2, 10, 300000.00, 'instansi komdigi', '218b0b47854d1103b20012903efcc7d1.jpg', '2024-11-24 13:56:09', '2024-12-06 09:05:40');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kategori`
--

CREATE TABLE `kategori` (
  `id_kategori` int(20) NOT NULL,
  `kategori` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kategori`
--

INSERT INTO `kategori` (`id_kategori`, `kategori`) VALUES
(1, 'elektronik'),
(2, 'peralatan'),
(4, 'alat');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kondisi`
--

CREATE TABLE `kondisi` (
  `id_kondisi` int(20) NOT NULL,
  `kondisi` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kondisi`
--

INSERT INTO `kondisi` (`id_kondisi`, `kondisi`) VALUES
(1, 'sangat baik'),
(2, 'second tapi baik'),
(4, 'cukup baik');

-- --------------------------------------------------------

--
-- Struktur dari tabel `ruang`
--

CREATE TABLE `ruang` (
  `id_ruangan` int(15) NOT NULL,
  `ruangan` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `ruang`
--

INSERT INTO `ruang` (`id_ruangan`, `ruangan`) VALUES
(1, 'gudang elektronika'),
(2, 'divisi gudang'),
(3, 'gudang peralatan');

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaksi`
--

CREATE TABLE `transaksi` (
  `id` int(11) NOT NULL,
  `id_barang` int(11) NOT NULL,
  `jenis` enum('masuk','keluar') NOT NULL,
  `jumlah` int(11) NOT NULL,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `transaksi`
--

INSERT INTO `transaksi` (`id`, `id_barang`, `jenis`, `jumlah`, `tanggal`) VALUES
(2, 5, 'masuk', 20, '2024-11-21 02:06:00'),
(3, 5, 'keluar', 4, '2024-11-21 02:07:00'),
(4, 7, 'keluar', 2, '2024-11-22 02:02:00'),
(5, 9, 'masuk', 6, '2024-11-25 03:20:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
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
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `gambar`) VALUES
(10, 'admin', 'admin1@gmail.com', '$2y$10$rU9csrLAC1DMrbVpnBM55e0Xt9ivCX04cPfzxsfrp7FX78jARFQLq', 'admin', '43f928178f4efc826a8069d1e0bafa56.png'),
(11, 'manager', 'manager1@gmail.com', '$2y$10$IQMyZCeIqfb9Emf9iTjBWucXPuX5krZoKv3xNT58cXTLsTVIh/EAS', 'manager', '0e9b8c965c64cc91d827372ad86e4431.jpg'),
(12, 'user', 'user1@gmail.com', '$2y$10$NSIXVDsqah7wRReM0KKeUOFP1SV.BaKq3ICPaDFtELGbmQF87bwIq', 'user', 'fd4c39ded59d3ae4379bcc2a8cac7ca7.jpeg');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `barang`
--
ALTER TABLE `barang`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indeks untuk tabel `kondisi`
--
ALTER TABLE `kondisi`
  ADD PRIMARY KEY (`id_kondisi`);

--
-- Indeks untuk tabel `ruang`
--
ALTER TABLE `ruang`
  ADD PRIMARY KEY (`id_ruangan`);

--
-- Indeks untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_barang` (`id_barang`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_email` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `barang`
--
ALTER TABLE `barang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT untuk tabel `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id_kategori` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `kondisi`
--
ALTER TABLE `kondisi`
  MODIFY `id_kondisi` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `ruang`
--
ALTER TABLE `ruang`
  MODIFY `id_ruangan` int(15) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
