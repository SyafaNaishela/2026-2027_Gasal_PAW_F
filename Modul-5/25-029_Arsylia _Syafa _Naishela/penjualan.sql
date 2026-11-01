-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 01, 2026 at 04:39 PM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `penjualan`
--

-- --------------------------------------------------------

--
-- Table structure for table `barang`
--

CREATE TABLE `barang` (
  `id` int NOT NULL,
  `kode_barang` varchar(20) DEFAULT NULL,
  `nama_barang` varchar(100) DEFAULT NULL,
  `harga` decimal(12,2) DEFAULT NULL,
  `stok` int DEFAULT NULL,
  `supplier_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `barang`
--

INSERT INTO `barang` (`id`, `kode_barang`, `nama_barang`, `harga`, `stok`, `supplier_id`) VALUES
(1, 'BRG001', 'Laptop Asus', '7500000.00', 10, 1),
(2, 'BRG002', 'Mouse Logitech', '150000.00', 25, 2),
(3, 'BRG003', 'Keyboard Mechanical', '350000.00', 15, 3),
(4, 'BRG004', 'Monitor Samsung', '1800000.00', 12, 4),
(5, 'BRG005', 'Flashdisk 32GB', '75000.00', 30, 5),
(6, 'BRG006', 'Harddisk 1TB', '850000.00', 8, 6),
(7, 'BRG007', 'Printer Epson', '2200000.00', 7, 7),
(8, 'BRG008', 'Headset Gaming', '275000.00', 20, 8),
(9, 'BRG009', 'Webcam HD', '450000.00', 14, 9),
(10, 'BRG010', 'Speaker Bluetooth', '300000.00', 18, 10);

-- --------------------------------------------------------

--
-- Table structure for table `pelanggan`
--

CREATE TABLE `pelanggan` (
  `id` int NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `jenis_kelamin` char(1) DEFAULT NULL,
  `telp` varchar(15) DEFAULT NULL,
  `alamat` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pelanggan`
--

INSERT INTO `pelanggan` (`id`, `nama`, `jenis_kelamin`, `telp`, `alamat`) VALUES
(1, 'Andi', 'L', '081234567801', 'Surabaya'),
(2, 'Budi', 'L', '081234567802', 'Sidoarjo'),
(3, 'Citra', 'P', '081234567803', 'Gresik'),
(4, 'Dina', 'P', '081234567804', 'Bangkalan'),
(5, 'Eko', 'L', '081234567805', 'Malang'),
(6, 'Fani', 'P', '081234567806', 'Madura'),
(7, 'Gilang', 'L', '081234567807', 'Lamongan'),
(8, 'Hani', 'P', '081234567808', 'Jombang'),
(9, 'Indra', 'L', '081234567809', 'Mojokerto'),
(10, 'Jihan', 'P', '081234567810', 'Pasuruan');

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id` int NOT NULL,
  `waktu_bayar` datetime DEFAULT NULL,
  `total` decimal(12,2) DEFAULT NULL,
  `metode` varchar(20) DEFAULT NULL,
  `transaksi_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pembayaran`
--

INSERT INTO `pembayaran` (`id`, `waktu_bayar`, `total`, `metode`, `transaksi_id`) VALUES
(1, '2026-10-01 09:10:00', '7500000.00', 'Tunai', 1),
(2, '2026-10-02 10:10:00', '150000.00', 'Transfer', 2),
(3, '2026-10-03 11:10:00', '350000.00', 'EDC', 3),
(4, '2026-10-04 12:10:00', '1800000.00', 'Tunai', 4),
(5, '2026-10-05 13:10:00', '150000.00', 'Transfer', 5),
(6, '2026-10-06 14:10:00', '850000.00', 'EDC', 6),
(7, '2026-10-07 15:10:00', '2200000.00', 'Tunai', 7),
(8, '2026-10-08 16:10:00', '550000.00', 'Transfer', 8),
(9, '2026-10-09 17:10:00', '450000.00', 'EDC', 9),
(10, '2026-10-10 18:10:00', '600000.00', 'Tunai', 10);

-- --------------------------------------------------------

--
-- Table structure for table `supplier`
--

CREATE TABLE `supplier` (
  `id` int NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `telp` varchar(15) DEFAULT NULL,
  `alamat` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `supplier`
--

INSERT INTO `supplier` (`id`, `nama`, `telp`, `alamat`) VALUES
(1, 'PT Sumber Makmur', '081111111101', 'Surabaya'),
(2, 'CV Berkah Jaya', '081111111102', 'Sidoarjo'),
(3, 'PT Indo Sejahtera', '081111111103', 'Gresik'),
(4, 'CV Mitra Usaha', '081111111104', 'Malang'),
(5, 'PT Sinar Abadi', '081111111105', 'Mojokerto'),
(6, 'CV Karya Mandiri', '081111111106', 'Bangkalan'),
(7, 'PT Cahaya Baru', '081111111107', 'Lamongan'),
(8, 'CV Jaya Sentosa', '081111111108', 'Jombang'),
(9, 'PT Mega Store', '081111111109', 'Pasuruan'),
(10, 'CV Mitra Niaga', '081111111110', 'Madura');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id` int NOT NULL,
  `waktu_transaksi` datetime DEFAULT NULL,
  `keterangan` text,
  `total` decimal(12,2) DEFAULT NULL,
  `pelanggan_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`id`, `waktu_transaksi`, `keterangan`, `total`, `pelanggan_id`) VALUES
(1, '2026-10-01 09:00:00', 'Pembelian laptop', '7500000.00', 1),
(2, '2026-10-02 10:00:00', 'Pembelian mouse', '150000.00', 2),
(3, '2026-10-03 11:00:00', 'Pembelian keyboard', '350000.00', 3),
(4, '2026-10-04 12:00:00', 'Pembelian monitor', '1800000.00', 4),
(5, '2026-10-05 13:00:00', 'Pembelian flashdisk', '150000.00', 5),
(6, '2026-10-06 14:00:00', 'Pembelian harddisk', '850000.00', 6),
(7, '2026-10-07 15:00:00', 'Pembelian printer', '2200000.00', 7),
(8, '2026-10-08 16:00:00', 'Pembelian headset', '550000.00', 8),
(9, '2026-10-09 17:00:00', 'Pembelian webcam', '450000.00', 9),
(10, '2026-10-10 18:00:00', 'Pembelian speaker', '600000.00', 10);

-- --------------------------------------------------------

--
-- Table structure for table `transaksi_detail`
--

CREATE TABLE `transaksi_detail` (
  `transaksi_id` int NOT NULL,
  `barang_id` int NOT NULL,
  `harga` decimal(12,2) DEFAULT NULL,
  `qty` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `transaksi_detail`
--

INSERT INTO `transaksi_detail` (`transaksi_id`, `barang_id`, `harga`, `qty`) VALUES
(1, 1, '7500000.00', 1),
(2, 2, '150000.00', 1),
(3, 3, '350000.00', 1),
(4, 4, '1800000.00', 1),
(5, 5, '75000.00', 2),
(6, 6, '850000.00', 1),
(7, 7, '2200000.00', 1),
(8, 8, '275000.00', 2),
(9, 9, '450000.00', 1),
(10, 10, '300000.00', 2);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id_user` int NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `alamat` text,
  `hp` varchar(15) DEFAULT NULL,
  `level` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id_user`, `username`, `password`, `nama`, `alamat`, `hp`, `level`) VALUES
(1, 'admin', 'admin123', 'Administrator', 'Surabaya', '082222222201', 'admin'),
(2, 'andi', 'andi123', 'Andi', 'Sidoarjo', '082222222202', 'kasir'),
(3, 'budi', 'budi123', 'Budi', 'Gresik', '082222222203', 'kasir'),
(4, 'citra', 'citra123', 'Citra', 'Malang', '082222222204', 'kasir'),
(5, 'dina', 'dina123', 'Dina', 'Bangkalan', '082222222205', 'kasir'),
(6, 'eko', 'eko123', 'Eko', 'Madura', '082222222206', 'kasir'),
(7, 'fani', 'fani123', 'Fani', 'Lamongan', '082222222207', 'kasir'),
(8, 'gilang', 'gilang123', 'Gilang', 'Jombang', '082222222208', 'kasir'),
(9, 'hani', 'hani123', 'Hani', 'Mojokerto', '082222222209', 'kasir'),
(10, 'indra', 'indra123', 'Indra', 'Pasuruan', '082222222210', 'kasir');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `barang`
--
ALTER TABLE `barang`
  ADD PRIMARY KEY (`id`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indexes for table `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transaksi_id` (`transaksi_id`);

--
-- Indexes for table `supplier`
--
ALTER TABLE `supplier`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pelanggan_id` (`pelanggan_id`);

--
-- Indexes for table `transaksi_detail`
--
ALTER TABLE `transaksi_detail`
  ADD PRIMARY KEY (`transaksi_id`,`barang_id`),
  ADD KEY `barang_id` (`barang_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `barang`
--
ALTER TABLE `barang`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `pelanggan`
--
ALTER TABLE `pelanggan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `supplier`
--
ALTER TABLE `supplier`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `barang`
--
ALTER TABLE `barang`
  ADD CONSTRAINT `barang_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `supplier` (`id`);

--
-- Constraints for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`transaksi_id`) REFERENCES `transaksi` (`id`);

--
-- Constraints for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`pelanggan_id`) REFERENCES `pelanggan` (`id`);

--
-- Constraints for table `transaksi_detail`
--
ALTER TABLE `transaksi_detail`
  ADD CONSTRAINT `transaksi_detail_ibfk_1` FOREIGN KEY (`transaksi_id`) REFERENCES `transaksi` (`id`),
  ADD CONSTRAINT `transaksi_detail_ibfk_2` FOREIGN KEY (`barang_id`) REFERENCES `barang` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
