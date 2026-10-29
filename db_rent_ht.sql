-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 29, 2026 at 02:03 AM
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
-- Database: `db_rent_ht`
--

-- --------------------------------------------------------

--
-- Table structure for table `ht_laporan`
--

CREATE TABLE `ht_laporan` (
  `id_laporan` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `judul_kendala` varchar(150) NOT NULL,
  `deskripsi` text NOT NULL,
  `tgl_laporan` datetime NOT NULL,
  `status` enum('Pending','Proses','Selesai') NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ht_pembayaran`
--

CREATE TABLE `ht_pembayaran` (
  `id_pembayaran` int(11) NOT NULL,
  `id_rental` int(11) NOT NULL,
  `invoice` varchar(50) NOT NULL,
  `nama_rekening` varchar(100) NOT NULL,
  `metode_pembayaran` varchar(50) NOT NULL,
  `tgl_bayar` datetime NOT NULL,
  `status` enum('Verifikasi','Paid','Rejected') NOT NULL DEFAULT 'Verifikasi'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ht_pembayaran`
--

INSERT INTO `ht_pembayaran` (`id_pembayaran`, `id_rental`, `invoice`, `nama_rekening`, `metode_pembayaran`, `tgl_bayar`, `status`) VALUES
(1, 1, 'INV-HT-20261027-1', 'Budi Santoso', 'Transfer Bank', '2026-10-27 12:52:46', 'Paid');

-- --------------------------------------------------------

--
-- Table structure for table `ht_perangkat`
--

CREATE TABLE `ht_perangkat` (
  `id_perangkat` int(11) NOT NULL,
  `merk_tipe` varchar(100) NOT NULL,
  `spesifikasi` varchar(255) NOT NULL,
  `harga_sewa_per_hari` int(11) NOT NULL,
  `stok_tersedia` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ht_perangkat`
--

INSERT INTO `ht_perangkat` (`id_perangkat`, `merk_tipe`, `spesifikasi`, `harga_sewa_per_hari`, `stok_tersedia`) VALUES
(1, 'Baofeng UV-5R', 'Dual Band VHF/UHF, 5 Watt, Baterai 1800mAh', 15000, 25),
(2, 'ICOM V80-T', 'VHF Transceiver, Heavy Duty, Audio Jernih', 25000, 15),
(3, 'Firstcom FC-27', 'UHF Professional Radio, Jangkauan Luas', 20000, 20);

-- --------------------------------------------------------

--
-- Table structure for table `ht_rental`
--

CREATE TABLE `ht_rental` (
  `id_rental` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_perangkat` int(11) NOT NULL,
  `jumlah_unit` int(11) NOT NULL,
  `tgl_mulai` date NOT NULL,
  `tgl_selesai` date NOT NULL,
  `total_hari` int(11) NOT NULL,
  `total_biaya` int(11) NOT NULL,
  `status_rental` enum('Menunggu Pembayaran','Aktif','Selesai','Batal') NOT NULL DEFAULT 'Menunggu Pembayaran'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ht_rental`
--

INSERT INTO `ht_rental` (`id_rental`, `id_user`, `id_perangkat`, `jumlah_unit`, `tgl_mulai`, `tgl_selesai`, `total_hari`, `total_biaya`, `status_rental`) VALUES
(1, 2, 2, 25, '2026-10-27', '2026-11-01', 5, 3125000, 'Aktif');

-- --------------------------------------------------------

--
-- Table structure for table `ht_user`
--

CREATE TABLE `ht_user` (
  `id_user` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `nohp` varchar(20) NOT NULL,
  `level` enum('Admin','User') NOT NULL DEFAULT 'User'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ht_user`
--

INSERT INTO `ht_user` (`id_user`, `username`, `password`, `nama`, `nohp`, `level`) VALUES
(1, 'admin', 'admin123', 'Administrator RENT-HT', '081234567890', 'Admin'),
(2, 'budi', 'user123', 'Budi Santoso', '089876543210', 'User');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ht_laporan`
--
ALTER TABLE `ht_laporan`
  ADD PRIMARY KEY (`id_laporan`);

--
-- Indexes for table `ht_pembayaran`
--
ALTER TABLE `ht_pembayaran`
  ADD PRIMARY KEY (`id_pembayaran`);

--
-- Indexes for table `ht_perangkat`
--
ALTER TABLE `ht_perangkat`
  ADD PRIMARY KEY (`id_perangkat`);

--
-- Indexes for table `ht_rental`
--
ALTER TABLE `ht_rental`
  ADD PRIMARY KEY (`id_rental`);

--
-- Indexes for table `ht_user`
--
ALTER TABLE `ht_user`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ht_laporan`
--
ALTER TABLE `ht_laporan`
  MODIFY `id_laporan` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ht_pembayaran`
--
ALTER TABLE `ht_pembayaran`
  MODIFY `id_pembayaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ht_perangkat`
--
ALTER TABLE `ht_perangkat`
  MODIFY `id_perangkat` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `ht_rental`
--
ALTER TABLE `ht_rental`
  MODIFY `id_rental` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ht_user`
--
ALTER TABLE `ht_user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
