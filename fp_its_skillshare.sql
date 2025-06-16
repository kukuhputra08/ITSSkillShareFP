-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 16, 2025 at 09:02 PM
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
-- Database: `fp_its_skillshare`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_laporan_order` ()   BEGIN
    SELECT orderId, tanggalOrder, statusOrder, TotalBayar, User_user_id, Service_serviceId
    FROM `order`;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `feedbackId` varchar(3) NOT NULL,
  `rating` int(11) DEFAULT NULL,
  `tanggalFeedback` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Order_orderId` varchar(32) NOT NULL,
  `Order_User_user_id` varchar(32) NOT NULL,
  `Order_Service_serviceId` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `feedback`
--
DELIMITER $$
CREATE TRIGGER `update_rating_provider` AFTER INSERT ON `feedback` FOR EACH ROW BEGIN
    UPDATE provider p
    JOIN service_provider sp ON p.providerID = sp.Provider_providerID
    JOIN service s ON sp.Service_serviceId = s.serviceId
    JOIN `order` o ON s.serviceId = o.Service_serviceId
    SET p.ratingRata = (
        SELECT AVG(f.rating)
        FROM feedback f
        JOIN `order` o2 ON f.Order_orderId = o2.orderId
        WHERE o2.Service_serviceId = s.serviceId
    )
    WHERE o.orderId = NEW.Order_orderId;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `log_admin`
--

CREATE TABLE `log_admin` (
  `id` int(11) NOT NULL,
  `admin_id` varchar(20) DEFAULT NULL,
  `providerID` varchar(20) DEFAULT NULL,
  `aksi` varchar(100) DEFAULT NULL,
  `waktu` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order`
--

CREATE TABLE `order` (
  `orderId` varchar(32) NOT NULL,
  `tanggalOrder` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `statusOrder` varchar(25) NOT NULL,
  `TotalBayar` float(10,2) NOT NULL,
  `User_user_id` varchar(3) NOT NULL,
  `Service_serviceId` varchar(3) NOT NULL,
  `statusPembayaran` enum('menunggu','berhasil','batal') NOT NULL DEFAULT 'menunggu',
  `waktuBayar` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order`
--

INSERT INTO `order` (`orderId`, `tanggalOrder`, `statusOrder`, `TotalBayar`, `User_user_id`, `Service_serviceId`, `statusPembayaran`, `waktuBayar`) VALUES
('O01', '2025-06-16 18:48:52', 'Selesai', 250000.00, 'U03', 'S01', 'berhasil', '2025-06-17 01:48:52'),
('O02', '2025-05-01 01:42:59', 'Batal', 180000.00, 'U04', 'S02', 'menunggu', NULL),
('O03', '2025-06-06 01:42:59', 'Batal', 200000.00, 'U05', 'S03', 'menunggu', NULL),
('O04', '2025-05-26 01:42:59', 'Selesai', 170000.00, 'U06', 'S04', 'menunggu', NULL),
('O05', '2025-05-04 01:42:59', 'Menunggu', 170000.00, 'U07', 'S05', 'menunggu', NULL),
('O06', '2025-06-08 01:42:59', 'Batal', 200000.00, 'U08', 'S06', 'menunggu', NULL),
('O07', '2025-05-04 01:42:59', 'Menunggu', 250000.00, 'U09', 'S07', 'menunggu', NULL),
('O08', '2025-06-16 16:51:12', 'selesai', 150000.00, 'U10', 'S08', 'menunggu', NULL),
('O09', '2025-06-01 01:42:59', 'Menunggu', 150000.00, 'U11', 'S09', 'menunggu', NULL),
('O10', '2025-05-27 01:42:59', 'Menunggu', 250000.00, 'U12', 'S10', 'menunggu', NULL),
('O11', '2025-05-11 01:42:59', 'Batal', 170000.00, 'U13', 'S11', 'menunggu', NULL),
('O12', '2025-05-01 01:42:59', 'Selesai', 220000.00, 'U14', 'S12', 'menunggu', NULL),
('O13', '2025-06-16 17:38:59', 'Batal', 170000.00, 'U15', 'S13', 'batal', NULL),
('O14', '2025-05-17 01:42:59', 'Selesai', 180000.00, 'U16', 'S14', 'menunggu', NULL),
('O15', '2025-06-16 18:55:14', 'Batal', 170000.00, 'U17', 'S15', 'berhasil', '2025-06-17 01:55:14'),
('O16', '2025-05-26 01:42:59', 'Selesai', 220000.00, 'U18', 'S16', 'menunggu', NULL),
('O17', '2025-05-22 01:42:59', 'Selesai', 220000.00, 'U19', 'S17', 'menunggu', NULL),
('O18', '2025-05-08 01:42:59', 'Menunggu', 250000.00, 'U20', 'S18', 'menunggu', NULL),
('O19', '2025-05-15 01:42:59', 'Menunggu', 180000.00, 'U21', 'S19', 'menunggu', NULL),
('O20', '2025-05-18 01:42:59', 'Batal', 220000.00, 'U22', 'S20', 'menunggu', NULL),
('O21', '2025-06-09 01:42:59', 'Selesai', 120000.00, 'U23', 'S01', 'menunggu', NULL),
('O22', '2025-06-13 08:50:31', 'menunggu', 180000.00, 'U24', 'S02', 'menunggu', NULL),
('O23', '2025-06-16 16:51:20', 'batal', 120000.00, 'U25', 'S03', 'menunggu', NULL),
('O24', '2025-05-29 01:42:59', 'Menunggu', 170000.00, 'U26', 'S04', 'menunggu', NULL),
('O25', '2025-05-25 01:42:59', 'Batal', 220000.00, 'U27', 'S05', 'menunggu', NULL),
('O26', '2025-05-02 01:42:59', 'Selesai', 250000.00, 'U28', 'S06', 'menunggu', NULL),
('O27', '2025-05-25 01:42:59', 'Batal', 150000.00, 'U29', 'S07', 'menunggu', NULL),
('O28', '2025-05-25 01:42:59', 'Menunggu', 150000.00, 'U30', 'S08', 'menunggu', NULL),
('O29', '2025-05-01 01:42:59', 'Selesai', 150000.00, 'U31', 'S09', 'menunggu', NULL),
('O30', '2025-06-15 01:49:02', 'selesai', 120000.00, 'U32', 'S10', 'menunggu', NULL),
('O31', '2025-05-02 01:42:59', 'Batal', 120000.00, 'U33', 'S11', 'menunggu', NULL),
('O32', '2025-05-04 01:42:59', 'Menunggu', 180000.00, 'U34', 'S12', 'menunggu', NULL),
('O33', '2025-05-15 01:42:59', 'Menunggu', 180000.00, 'U35', 'S13', 'menunggu', NULL),
('O34', '2025-05-11 01:42:59', 'Menunggu', 180000.00, 'U36', 'S14', 'menunggu', NULL),
('O35', '2025-05-26 01:42:59', 'Batal', 220000.00, 'U37', 'S15', 'menunggu', NULL),
('O36', '2025-05-03 01:42:59', 'Selesai', 250000.00, 'U38', 'S16', 'menunggu', NULL),
('O37', '2025-05-15 01:42:59', 'Selesai', 150000.00, 'U39', 'S17', 'menunggu', NULL),
('O38', '2025-05-10 01:42:59', 'Selesai', 180000.00, 'U40', 'S18', 'menunggu', NULL),
('O39', '2025-05-22 01:42:59', 'Batal', 170000.00, 'U41', 'S19', 'menunggu', NULL),
('O40', '2025-05-13 01:42:59', 'Selesai', 120000.00, 'U42', 'S20', 'menunggu', NULL),
('O41', '2025-05-12 01:42:59', 'Menunggu', 250000.00, 'U43', 'S01', 'menunggu', NULL),
('O42', '2025-06-05 01:42:59', 'Menunggu', 250000.00, 'U44', 'S02', 'menunggu', NULL),
('O43', '2025-05-16 01:42:59', 'Menunggu', 250000.00, 'U45', 'S03', 'menunggu', NULL),
('O44', '2025-05-13 01:42:59', 'Menunggu', 150000.00, 'U46', 'S04', 'menunggu', NULL),
('O45', '2025-05-21 01:42:59', 'Menunggu', 220000.00, 'U47', 'S05', 'menunggu', NULL),
('O46', '2025-05-30 01:42:59', 'Batal', 200000.00, 'U48', 'S06', 'menunggu', NULL),
('O47', '2025-05-10 01:42:59', 'Menunggu', 170000.00, 'U49', 'S07', 'menunggu', NULL),
('O48', '2025-05-21 01:42:59', 'Menunggu', 150000.00, 'U50', 'S08', 'menunggu', NULL),
('O49', '2025-05-03 01:42:59', 'Batal', 170000.00, 'U51', 'S09', 'menunggu', NULL),
('O50', '2025-05-22 01:42:59', 'Batal', 180000.00, 'U52', 'S10', 'menunggu', NULL),
('O51', '2025-05-07 01:42:59', 'Menunggu', 220000.00, 'U53', 'S11', 'menunggu', NULL),
('O52', '2025-05-05 01:42:59', 'Batal', 250000.00, 'U54', 'S12', 'menunggu', NULL),
('O53', '2025-06-16 16:51:26', 'proses', 250000.00, 'U55', 'S13', 'menunggu', NULL),
('O54', '2025-05-27 01:42:59', 'Selesai', 150000.00, 'U56', 'S14', 'menunggu', NULL),
('O55', '2025-05-07 01:42:59', 'Batal', 180000.00, 'U57', 'S15', 'menunggu', NULL),
('O56', '2025-05-06 01:42:59', 'Batal', 170000.00, 'U58', 'S16', 'menunggu', NULL),
('O57', '2025-05-13 01:42:59', 'Menunggu', 180000.00, 'U59', 'S17', 'menunggu', NULL),
('O58', '2025-06-02 01:42:59', 'Batal', 250000.00, 'U60', 'S18', 'menunggu', NULL),
('O59', '2025-05-31 01:42:59', 'Menunggu', 250000.00, 'U61', 'S19', 'menunggu', NULL),
('O60', '2025-05-15 01:42:59', 'Menunggu', 120000.00, 'U62', 'S20', 'menunggu', NULL),
('O68', '2025-06-13 07:48:05', 'batal', 150000.00, '', 'A01', 'menunggu', NULL),
('O68', '2025-06-13 07:48:01', 'selesai', 1222222.00, '', 'S93', 'menunggu', NULL),
('O68', '2025-06-13 07:47:48', 'dalam pengerjaan', 100000000.00, '', 'Sb7', 'menunggu', NULL),
('O68', '2025-06-13 04:31:27', 'menunggu', 175000.00, 'U11', 'S09', 'menunggu', NULL),
('O68', '2025-06-13 04:32:21', 'menunggu', 300000.00, 'U11', 'S12', 'menunggu', NULL),
('O68', '2025-06-16 18:03:24', 'menunggu', 175000.00, 'U15', 'S09', 'menunggu', NULL),
('O68', '2025-06-16 18:08:51', 'menunggu', 130000.00, 'U15', 'S15', 'menunggu', NULL),
('O68505f7d37109', '2025-06-16 18:16:29', 'menunggu', 130000.00, 'U15', 'S15', 'menunggu', NULL),
('O685061e6cf672', '2025-06-16 18:26:46', 'menunggu', 130000.00, 'U15', 'S15', 'menunggu', NULL),
('O685064404927d', '2025-06-16 18:48:34', 'menunggu', 160000.00, 'U03', 'S19', 'berhasil', '2025-06-17 01:48:34'),
('O6850672030f29', '2025-06-16 18:49:13', 'menunggu', 100000.00, 'U03', 'S04', 'berhasil', '2025-06-17 01:49:13');

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `paymentId` varchar(3) NOT NULL,
  `jumlahPembayaran` float(10,2) NOT NULL,
  `metodePembayaran` varchar(25) NOT NULL,
  `tanggalPembayaran` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `statusPembayaran` varchar(25) NOT NULL,
  `Order_orderId` varchar(32) NOT NULL,
  `Order_User_user_id` varchar(3) NOT NULL,
  `Order_Service_serviceId` varchar(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`paymentId`, `jumlahPembayaran`, `metodePembayaran`, `tanggalPembayaran`, `statusPembayaran`, `Order_orderId`, `Order_User_user_id`, `Order_Service_serviceId`) VALUES
('P8a', 150000.00, 'E-Wallet', '2025-06-12 16:43:27', 'paid', 'O68', '', 'A01'),
('PAY', 250000.00, 'Transfer Bank', '2025-06-16 18:48:52', 'berhasil', 'O01', 'U03', 'S01'),
('PAY', 160000.00, 'E-wallet', '2025-06-16 18:48:34', 'berhasil', 'O685064404927d', 'U03', 'S19'),
('PAY', 200000.00, 'E-wallet', '2025-06-16 18:49:13', 'berhasil', 'O6850672030f29', 'U03', 'S04'),
('PAY', 170000.00, 'E-wallet', '2025-06-16 18:55:14', 'berhasil', 'O15', 'U17', 'S15'),
('PM0', 250000.00, 'Transfer Bank', '2025-06-13 09:09:52', 'Lunas', 'O01', 'U03', 'S01'),
('PM0', 180000.00, 'E-Wallet', '2025-06-13 09:09:52', 'Belum Dibayar', 'O02', 'U04', 'S02'),
('PM0', 200000.00, 'E-Wallet', '2025-06-13 09:09:52', 'Belum Dibayar', 'O03', 'U05', 'S03'),
('PM0', 170000.00, 'Transfer Bank', '2025-06-13 09:09:52', 'Lunas', 'O04', 'U06', 'S04'),
('PM0', 170000.00, 'E-Wallet', '2025-06-13 09:09:52', 'Lunas', 'O05', 'U07', 'S05'),
('PM0', 200000.00, 'Kartu Kredit', '2025-06-13 09:09:52', 'Belum Dibayar', 'O06', 'U08', 'S06'),
('PM0', 250000.00, 'Transfer Bank', '2025-06-13 09:09:52', 'Lunas', 'O07', 'U09', 'S07'),
('PM0', 150000.00, 'E-Wallet', '2025-06-13 09:09:52', 'Lunas', 'O08', 'U10', 'S08'),
('PM0', 150000.00, 'Transfer Bank', '2025-06-13 09:09:52', 'Belum Dibayar', 'O09', 'U11', 'S09'),
('PM1', 250000.00, 'E-Wallet', '2025-06-13 09:09:52', 'Lunas', 'O10', 'U12', 'S10'),
('PM1', 170000.00, 'Kartu Kredit', '2025-06-13 09:09:52', 'Belum Dibayar', 'O11', 'U13', 'S11'),
('PM1', 220000.00, 'Transfer Bank', '2025-06-13 09:09:52', 'Lunas', 'O12', 'U14', 'S12'),
('PM1', 170000.00, 'E-Wallet', '2025-06-13 09:09:52', 'Lunas', 'O13', 'U15', 'S13'),
('PM1', 180000.00, 'Transfer Bank', '2025-06-13 09:09:52', 'Lunas', 'O14', 'U16', 'S14'),
('PM1', 170000.00, 'Kartu Kredit', '2025-06-13 09:09:52', 'Belum Dibayar', 'O15', 'U17', 'S15'),
('PM1', 220000.00, 'E-Wallet', '2025-06-13 09:09:52', 'Lunas', 'O16', 'U18', 'S16'),
('PM1', 220000.00, 'Transfer Bank', '2025-06-13 09:09:52', 'Lunas', 'O17', 'U19', 'S17'),
('PM1', 250000.00, 'E-Wallet', '2025-06-13 09:09:52', 'Belum Dibayar', 'O18', 'U20', 'S18'),
('PM1', 180000.00, 'Transfer Bank', '2025-06-13 09:09:52', 'Lunas', 'O19', 'U21', 'S19'),
('PM2', 220000.00, 'Kartu Kredit', '2025-06-13 09:09:52', 'Belum Dibayar', 'O20', 'U22', 'S20'),
('PM2', 120000.00, 'Transfer Bank', '2025-06-13 09:09:52', 'Lunas', 'O21', 'U23', 'S01'),
('PM2', 180000.00, 'E-Wallet', '2025-06-13 09:09:52', 'Belum Dibayar', 'O22', 'U24', 'S02'),
('PM2', 120000.00, 'E-Wallet', '2025-06-13 09:09:52', 'Lunas', 'O23', 'U25', 'S03'),
('PM2', 170000.00, 'Transfer Bank', '2025-06-13 09:09:52', 'Belum Dibayar', 'O24', 'U26', 'S04'),
('PM2', 220000.00, 'Kartu Kredit', '2025-06-13 09:09:52', 'Belum Dibayar', 'O25', 'U27', 'S05'),
('PM2', 250000.00, 'E-Wallet', '2025-06-13 09:09:52', 'Lunas', 'O26', 'U28', 'S06'),
('PM2', 150000.00, 'Transfer Bank', '2025-06-13 09:09:52', 'Belum Dibayar', 'O27', 'U29', 'S07'),
('PM2', 150000.00, 'E-Wallet', '2025-06-13 09:09:52', 'Belum Dibayar', 'O28', 'U30', 'S08'),
('PM2', 150000.00, 'Kartu Kredit', '2025-06-13 09:09:52', 'Lunas', 'O29', 'U31', 'S09'),
('PM3', 120000.00, 'Transfer Bank', '2025-06-13 09:09:52', 'Belum Dibayar', 'O30', 'U32', 'S10'),
('PM3', 120000.00, 'E-Wallet', '2025-06-13 09:09:52', 'Belum Dibayar', 'O31', 'U33', 'S11'),
('PM3', 180000.00, 'Transfer Bank', '2025-06-13 09:09:52', 'Lunas', 'O32', 'U34', 'S12'),
('PM3', 180000.00, 'E-Wallet', '2025-06-13 09:09:52', 'Belum Dibayar', 'O33', 'U35', 'S13'),
('PM3', 180000.00, 'Kartu Kredit', '2025-06-13 09:09:52', 'Belum Dibayar', 'O34', 'U36', 'S14'),
('PM3', 220000.00, 'Transfer Bank', '2025-06-13 09:09:52', 'Lunas', 'O35', 'U37', 'S15'),
('PM3', 250000.00, 'E-Wallet', '2025-06-13 09:09:52', 'Belum Dibayar', 'O36', 'U38', 'S16'),
('PM3', 150000.00, 'E-Wallet', '2025-06-13 09:09:52', 'Lunas', 'O37', 'U39', 'S17'),
('PM3', 180000.00, 'Transfer Bank', '2025-06-13 09:09:52', 'Belum Dibayar', 'O38', 'U40', 'S18'),
('PM3', 170000.00, 'Kartu Kredit', '2025-06-13 09:09:52', 'Belum Dibayar', 'O39', 'U41', 'S19'),
('PM4', 120000.00, 'Transfer Bank', '2025-06-13 09:09:52', 'Lunas', 'O40', 'U42', 'S20'),
('PM4', 250000.00, 'E-Wallet', '2025-06-13 09:09:52', 'Lunas', 'O41', 'U43', 'S01'),
('PM4', 250000.00, 'Transfer Bank', '2025-06-13 09:09:52', 'Lunas', 'O42', 'U44', 'S02'),
('PM4', 250000.00, 'E-Wallet', '2025-06-13 09:09:52', 'Lunas', 'O43', 'U45', 'S03'),
('PM4', 150000.00, 'Transfer Bank', '2025-06-13 09:09:52', 'Lunas', 'O44', 'U46', 'S04'),
('PM4', 220000.00, 'E-Wallet', '2025-06-13 09:09:52', 'Lunas', 'O45', 'U47', 'S05'),
('PM4', 200000.00, 'Transfer Bank', '2025-06-13 09:09:52', 'Lunas', 'O46', 'U48', 'S06'),
('PM4', 170000.00, 'E-Wallet', '2025-06-13 09:09:52', 'Belum Dibayar', 'O47', 'U49', 'S07'),
('PM4', 150000.00, 'Transfer Bank', '2025-06-13 09:09:52', 'Belum Dibayar', 'O48', 'U50', 'S08'),
('PM4', 170000.00, 'E-Wallet', '2025-06-13 09:09:52', 'Belum Dibayar', 'O49', 'U51', 'S09'),
('PM5', 180000.00, 'Kartu Kredit', '2025-06-13 09:09:52', 'Belum Dibayar', 'O50', 'U52', 'S10'),
('PM5', 220000.00, 'Transfer Bank', '2025-06-13 09:09:52', 'Lunas', 'O51', 'U53', 'S11'),
('PM5', 250000.00, 'E-Wallet', '2025-06-13 09:09:52', 'Belum Dibayar', 'O52', 'U54', 'S12'),
('PM5', 250000.00, 'E-Wallet', '2025-06-13 09:09:52', 'Lunas', 'O53', 'U55', 'S13'),
('PM5', 150000.00, 'Transfer Bank', '2025-06-13 09:09:52', 'Lunas', 'O54', 'U56', 'S14'),
('PM5', 180000.00, 'Transfer Bank', '2025-06-13 09:09:52', 'Belum Dibayar', 'O55', 'U57', 'S15'),
('PM5', 170000.00, 'E-Wallet', '2025-06-13 09:09:52', 'Belum Dibayar', 'O56', 'U58', 'S16'),
('PM5', 180000.00, 'Transfer Bank', '2025-06-13 09:09:52', 'Belum Dibayar', 'O57', 'U59', 'S17'),
('PM5', 250000.00, 'E-Wallet', '2025-06-13 09:09:52', 'Belum Dibayar', 'O58', 'U60', 'S18'),
('PM5', 250000.00, 'Transfer Bank', '2025-06-13 09:09:52', 'Belum Dibayar', 'O59', 'U61', 'S19'),
('PM6', 120000.00, 'E-Wallet', '2025-06-13 09:09:52', 'Lunas', 'O60', 'U62', 'S20');

--
-- Triggers `payment`
--
DELIMITER $$
CREATE TRIGGER `auto_expire_order` AFTER UPDATE ON `payment` FOR EACH ROW BEGIN
    IF NEW.statusPembayaran = 'expired' THEN
        UPDATE `order`
        SET statusOrder = 'Batal'
        WHERE orderId = NEW.Order_orderId;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `provider`
--

CREATE TABLE `provider` (
  `providerID` varchar(3) NOT NULL,
  `namaUsaha` varchar(25) NOT NULL,
  `deskripsiUsaha` varchar(255) NOT NULL,
  `ratingRata` int(11) DEFAULT NULL,
  `User_user_id` varchar(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `provider`
--

INSERT INTO `provider` (`providerID`, `namaUsaha`, `deskripsiUsaha`, `ratingRata`, `User_user_id`) VALUES
('P01', 'Konsultan UI/UX Akademik', 'Jasa desain antarmuka untuk proyek tugas akhir dan aplikasi kampus', 5, 'U06'),
('P02', 'Visual Kampus Pro', 'Desain grafis akademik untuk presentasi dan laporan mahasiswa', 5, 'U08'),
('P03', 'Kursus Academic English', 'Pelatihan bahasa Inggris akademik dan TOEFL untuk mahasiswa', 5, 'U10'),
('P04', 'Konsultasi Legal Mahasisw', 'Konsultasi hukum terkait kegiatan dan organisasi kampus', 5, 'U12'),
('P05', 'Penulisan Ilmiah Profesio', 'Jasa penulisan dan proofreading skripsi, tesis, dan jurnal', 5, 'U14'),
('P06', 'Solusi IT Akademik', 'Konsultasi teknologi dan pengembangan sistem kampus', 5, 'U16'),
('P07', 'Tutor Mata Kuliah Umum', 'Bimbingan belajar untuk mata kuliah dasar mahasiswa', 4, 'U18'),
('P08', 'Desain Poster Seminar', 'Desain media visual untuk seminar dan lomba kampus', 4, 'U20'),
('P09', 'Web Kampus Dev', 'Jasa pembuatan website untuk organisasi dan proyek kampus', 4, 'U21'),
('P10', 'Penyedia Media Pembelajar', 'Pembuatan alat peraga digital dan interaktif', 4, 'U22'),
('P11', 'Digitalisasi Dokumen Akad', 'Jasa scan dan arsip dokumen untuk keperluan kampus', 4, 'U23'),
('P12', 'Manajemen Event Akademik', 'Penyelenggaraan event seminar dan workshop kampus', 4, 'U24'),
('P13', 'Sewa Ruang Presentasi', 'Penyewaan ruang dengan fasilitas akademik kampus', 4, 'U25'),
('P14', 'Desain Ruang Belajar', 'Renovasi dan tata ruang kelas atau laboratorium kampus', 4, 'U26'),
('P15', 'Event Organizer Akademik', 'Penyelenggara acara ilmiah, seminar, dan expo kampus', NULL, 'U27'),
('P16', 'Catering Seminar Kampus', 'Layanan konsumsi untuk acara akademik kampus', NULL, 'U28'),
('P17', 'Editing Video Presentasi', 'Jasa editing video untuk tugas, seminar, dan lomba', NULL, 'U29'),
('P18', 'Transportasi Akademik', 'Penyewaan kendaraan untuk kegiatan riset atau kunjungan kampus', NULL, 'U30'),
('P19', 'Asistensi Orang Tua Mahas', 'Layanan penitipan anak selama mahasiswa mengikuti kuliah', NULL, 'U31'),
('P20', 'Fotografi Wisuda Kampus', 'Dokumentasi foto dan video untuk acara kelulusan', NULL, 'U32'),
('P68', 'asdadsdas', 'asdasdadas', 0, 'A02'),
('P68', 'Desain', 'Desain apapun yang berkaitan dengan seni.', 0, 'A12');

--
-- Triggers `provider`
--
DELIMITER $$
CREATE TRIGGER `log_status_layanan_provider` AFTER UPDATE ON `provider` FOR EACH ROW BEGIN
    IF OLD.ratingRata != NEW.ratingRata THEN
        INSERT INTO log_admin (admin_id, providerID, aksi)
        VALUES (CURRENT_USER(), NEW.providerID, CONCAT('Ubah rating menjadi ', NEW.ratingRata));
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `service`
--

CREATE TABLE `service` (
  `serviceId` varchar(3) NOT NULL,
  `namaServive` varchar(25) NOT NULL,
  `deskripsi` text NOT NULL,
  `hargaDasar` float(10,2) NOT NULL,
  `durasi` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `service`
--

INSERT INTO `service` (`serviceId`, `namaServive`, `deskripsi`, `hargaDasar`, `durasi`) VALUES
('S01', 'Desain UI/UX Website', 'Desain antarmuka pengguna untuk web kampus atau tugas akhir', 150000.00, '2024-12-31 19:00:00'),
('S02', 'Pembuatan Landing Page', 'Membuat halaman depan website statis responsif', 200000.00, '2024-12-31 21:00:00'),
('S03', 'Desain Poster Akademik', 'Poster seminar, lomba, atau kegiatan kampus', 75000.00, '2024-12-31 18:00:00'),
('S04', 'Proofreading Skripsi', 'Pemeriksaan tata bahasa dan format penulisan ilmiah', 100000.00, '2024-12-31 20:00:00'),
('S05', 'Editing Video Presentasi', 'Editing video presentasi tugas kuliah atau lomba', 120000.00, '2024-12-31 19:30:00'),
('S06', 'Pelatihan Academic Writin', 'Sesi pelatihan penulisan akademik dalam bahasa Inggris', 180000.00, '2024-12-31 20:00:00'),
('S07', 'Pembuatan Infografis', 'Visualisasi data atau materi kuliah dalam bentuk infografis', 90000.00, '2024-12-31 19:00:00'),
('S08', 'Pembuatan CV Mahasiswa', 'Pembuatan curriculum vitae profesional untuk mahasiswa', 50000.00, '2024-12-31 18:30:00'),
('S09', 'Kursus UI/UX Dasar', 'Pelatihan dasar desain UI/UX untuk pemula', 175000.00, '2024-12-31 21:00:00'),
('S10', 'Translate Abstrak Inggris', 'Penerjemahan abstrak skripsi dari Bahasa Indonesia ke Inggris', 60000.00, '2024-12-31 18:00:00'),
('S11', 'Desain PowerPoint', 'Pembuatan desain slide presentasi akademik', 85000.00, '2024-12-31 18:30:00'),
('S12', 'Pembuatan Website Organis', 'Website untuk UKM atau BEM kampus', 300000.00, '2024-12-31 23:00:00'),
('S13', 'Pembuatan Logo Kampus', 'Desain logo organisasi atau acara kampus', 70000.00, '2024-12-31 18:00:00'),
('S14', 'Simulasi TOEFL', 'Tes simulasi TOEFL ITP untuk persiapan akademik', 100000.00, '2024-12-31 19:00:00'),
('S15', 'Konsultasi Proposal', 'Pendampingan penyusunan proposal penelitian mahasiswa', 130000.00, '2024-12-31 19:30:00'),
('S16', 'Data Entry Penelitian', 'Input dan pengolahan data hasil survey atau eksperimen', 110000.00, '2024-12-31 19:00:00'),
('S17', 'Layout Jurnal Ilmiah', 'Penyusunan layout artikel sesuai format jurnal terakreditasi', 125000.00, '2024-12-31 19:30:00'),
('S18', 'Pelatihan LaTeX Dasar', 'Belajar penulisan dokumen ilmiah menggunakan LaTeX', 95000.00, '2024-12-31 20:00:00'),
('S19', 'Analisis Statistik SPSS', 'Layanan analisis data kuantitatif untuk tugas akhir', 160000.00, '2024-12-31 20:30:00'),
('S20', 'Konsultasi Desain Penelit', 'Bimbingan dalam penyusunan metodologi riset', 140000.00, '2024-12-31 19:30:00'),
('S97', 'Joki Mobile Legends', 'Kita merupakan tim emobile terbaik.', 10000000.00, '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `service_provider`
--

CREATE TABLE `service_provider` (
  `Service_serviceId` varchar(3) NOT NULL,
  `Provider_providerID` varchar(3) NOT NULL,
  `Provider_User_user_id` varchar(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `service_provider`
--

INSERT INTO `service_provider` (`Service_serviceId`, `Provider_providerID`, `Provider_User_user_id`) VALUES
('S01', 'P01', 'U06'),
('S02', 'P09', 'U21'),
('S03', 'P02', 'U08'),
('S04', 'P05', 'U14'),
('S05', 'P17', 'U29'),
('S06', 'P03', 'U10'),
('S07', 'P02', 'U08'),
('S08', 'P02', 'U08'),
('S09', 'P01', 'U06'),
('S10', 'P05', 'U14'),
('S11', 'P02', 'U08'),
('S12', 'P09', 'U21'),
('S13', 'P02', 'U08'),
('S14', 'P03', 'U10'),
('S15', 'P05', 'U14'),
('S16', 'P11', 'U23'),
('S17', 'P05', 'U14'),
('S18', 'P06', 'U16'),
('S19', 'P06', 'U16'),
('S20', 'P05', 'U14'),
('S97', 'P68', 'A02');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` varchar(3) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(25) NOT NULL,
  `role` varchar(10) DEFAULT NULL,
  `TanggalRegis` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `nama`, `email`, `password`, `role`, `TanggalRegis`) VALUES
('', 'Putra', 'kukuhputra0802@gmail.com', 'admin1234', 'customer', '2025-06-16 15:13:59'),
('A02', 'Ikhwan', 'akucustomer@its.skill', 'admin1234', 'provider', '2025-06-12 18:00:07'),
('A12', 'izud', 'izudjoki@aa', 'haloizud', 'provider', '2025-06-12 18:42:13'),
('U01', 'Alya', 'alya@admin.its.ac.id', 'password1', 'admin', '2025-06-13 08:02:03'),
('U02', 'Izud', 'izud@admin.its.ac.id', 'password2', 'admin', '2025-06-13 08:02:03'),
('U03', 'Kelly', '5025231317@student.its.ac.id', 'password3', 'customer', '2025-06-13 08:02:03'),
('U04', 'Kukuh', 'kukuh@admin.its.ac.id', 'password4', 'admin', '2025-06-13 08:02:03'),
('U05', 'Jua', 'jua@admin.its.ac.id', 'password5', 'admin', '2025-06-13 08:02:03'),
('U06', 'Riko', '5025231320@student.its.ac.id', 'password6', 'provider', '2025-06-13 08:02:03'),
('U07', 'Alya', '5025231321@student.its.ac.id', 'password7', 'customer', '2025-06-13 08:02:03'),
('U08', 'Tio', '5025231322@student.its.ac.id', 'password8', 'provider', '2025-06-13 08:02:03'),
('U09', 'Dina', '5025231323@student.its.ac.id', 'password9', 'customer', '2025-06-13 08:02:03'),
('U10', 'Fira', '5025231324@student.its.ac.id', 'password10', 'provider', '2025-06-13 08:02:03'),
('U11', 'Budi', '5025231325@student.its.ac.id', 'password11', 'customer', '2025-06-13 08:02:03'),
('U12', 'Rina', '5025231326@student.its.ac.id', 'password12', 'provider', '2025-06-13 08:02:03'),
('U13', 'Joko', '5025231327@student.its.ac.id', 'password13', 'customer', '2025-06-13 08:02:03'),
('U14', 'Siti', '5025231328@student.its.ac.id', 'password14', 'provider', '2025-06-13 08:02:03'),
('U15', 'Rian', '5025231329@student.its.ac.id', 'password15', 'customer', '2025-06-13 08:02:03'),
('U16', 'Vera', '5025231330@student.its.ac.id', 'password16', 'provider', '2025-06-13 08:02:03'),
('U17', 'Hani', '5025231331@student.its.ac.id', 'password17', 'customer', '2025-06-13 08:02:03'),
('U18', 'Wira', '5025231332@student.its.ac.id', 'password18', 'provider', '2025-06-13 08:02:03'),
('U19', 'Yudi', '5025231333@student.its.ac.id', 'password19', 'customer', '2025-06-13 08:02:03'),
('U20', 'Fadli', '5025231334@student.its.ac.id', 'password20', 'provider', '2025-06-13 08:02:03'),
('U21', 'Zara', '5025231335@student.its.ac.id', 'password21', 'provider', '2025-06-13 08:02:03'),
('U22', 'Gio', '5025231336@student.its.ac.id', 'password22', 'provider', '2025-06-13 08:02:03'),
('U23', 'Nina', '5025231337@student.its.ac.id', 'password23', 'provider', '2025-06-13 08:02:03'),
('U24', 'Luna', '5025231338@student.its.ac.id', 'password24', 'provider', '2025-06-13 08:02:03'),
('U25', 'Mila', '5025231339@student.its.ac.id', 'password25', 'provider', '2025-06-13 08:02:03'),
('U26', 'Kris', '5025231340@student.its.ac.id', 'password26', 'provider', '2025-06-13 08:02:03'),
('U27', 'Zayn', '5025231341@student.its.ac.id', 'password27', 'provider', '2025-06-13 08:02:03'),
('U28', 'Vino', '5025231342@student.its.ac.id', 'password28', 'provider', '2025-06-13 08:02:03'),
('U29', 'Tina', '5025231343@student.its.ac.id', 'password29', 'provider', '2025-06-13 08:02:03'),
('U30', 'Yana', '5025231344@student.its.ac.id', 'password30', 'provider', '2025-06-13 08:02:03'),
('U31', 'Elena', '5025231345@student.its.ac.id', 'password31', 'provider', '2025-06-13 08:02:03'),
('U32', 'Dian', '5025231346@student.its.ac.id', 'password32', 'provider', '2025-06-13 08:02:03'),
('U33', 'Arman', '5025231347@student.its.ac.id', 'password33', 'customer', '2025-06-13 08:43:37'),
('U34', 'Bella', '5025231348@student.its.ac.id', 'password34', 'customer', '2025-06-13 08:43:37'),
('U35', 'Cindy', '5025231349@student.its.ac.id', 'password35', 'customer', '2025-06-13 08:43:37'),
('U36', 'Dimas', '5025231350@student.its.ac.id', 'password36', 'customer', '2025-06-13 08:43:37'),
('U37', 'Eka', '5025231351@student.its.ac.id', 'password37', 'customer', '2025-06-13 08:43:37'),
('U38', 'Ferry', '5025231352@student.its.ac.id', 'password38', 'customer', '2025-06-13 08:43:37'),
('U39', 'Gina', '5025231353@student.its.ac.id', 'password39', 'customer', '2025-06-13 08:43:37'),
('U40', 'Hendra', '5025231354@student.its.ac.id', 'password40', 'customer', '2025-06-13 08:43:37'),
('U41', 'Intan', '5025231355@student.its.ac.id', 'password41', 'customer', '2025-06-13 08:43:37'),
('U42', 'Joko', '5025231356@student.its.ac.id', 'password42', 'customer', '2025-06-13 08:43:37'),
('U43', 'Kiki', '5025231357@student.its.ac.id', 'password43', 'customer', '2025-06-13 08:43:37'),
('U44', 'Lina', '5025231358@student.its.ac.id', 'password44', 'customer', '2025-06-13 08:43:37'),
('U45', 'Mario', '5025231359@student.its.ac.id', 'password45', 'customer', '2025-06-13 08:43:37'),
('U46', 'Nina', '5025231360@student.its.ac.id', 'password46', 'customer', '2025-06-13 08:43:37'),
('U47', 'Oki', '5025231361@student.its.ac.id', 'password47', 'customer', '2025-06-13 08:43:37'),
('U48', 'Putri', '5025231362@student.its.ac.id', 'password48', 'customer', '2025-06-13 08:43:37'),
('U49', 'Qori', '5025231363@student.its.ac.id', 'password49', 'customer', '2025-06-13 08:43:37'),
('U50', 'Rafi', '5025231364@student.its.ac.id', 'password50', 'customer', '2025-06-13 08:43:37'),
('U51', 'Salsa', '5025231365@student.its.ac.id', 'password51', 'customer', '2025-06-13 08:43:37'),
('U52', 'Teguh', '5025231366@student.its.ac.id', 'password52', 'customer', '2025-06-13 08:43:37'),
('U53', 'Uli', '5025231367@student.its.ac.id', 'password53', 'customer', '2025-06-13 08:43:37'),
('U54', 'Vino', '5025231368@student.its.ac.id', 'password54', 'customer', '2025-06-13 08:43:37'),
('U55', 'Wulan', '5025231369@student.its.ac.id', 'password55', 'customer', '2025-06-13 08:43:37'),
('U56', 'Xena', '5025231370@student.its.ac.id', 'password56', 'customer', '2025-06-13 08:43:37'),
('U57', 'Yoga', '5025231371@student.its.ac.id', 'password57', 'customer', '2025-06-13 08:43:37'),
('U58', 'Zahra', '5025231372@student.its.ac.id', 'password58', 'customer', '2025-06-13 08:43:37'),
('U59', 'Aditya', '5025231373@student.its.ac.id', 'password59', 'customer', '2025-06-13 08:43:37'),
('U60', 'Bunga', '5025231374@student.its.ac.id', 'password60', 'customer', '2025-06-13 08:43:37'),
('U61', 'Chandra', '5025231375@student.its.ac.id', 'password61', 'customer', '2025-06-13 08:43:37'),
('U62', 'Dewi', '5025231376@student.its.ac.id', 'password62', 'customer', '2025-06-13 08:43:37'),
('U63', 'Erlangga', '5025231377@student.its.ac.id', 'password63', 'customer', '2025-06-13 08:43:37'),
('U64', 'Farah', '5025231378@student.its.ac.id', 'password64', 'customer', '2025-06-13 08:43:37'),
('U65', 'Galih', '5025231379@student.its.ac.id', 'password65', 'customer', '2025-06-13 08:43:37'),
('U66', 'Hana', '5025231380@student.its.ac.id', 'password66', 'customer', '2025-06-13 08:43:37'),
('U67', 'Irfan', '5025231381@student.its.ac.id', 'password67', 'customer', '2025-06-13 08:43:37'),
('U68', 'Juliana', '5025231382@student.its.ac.id', 'password68', 'customer', '2025-06-13 08:43:37'),
('U69', 'Kevin', '5025231383@student.its.ac.id', 'password69', 'customer', '2025-06-13 08:43:37'),
('U70', 'Lesti', '5025231384@student.its.ac.id', 'password70', 'customer', '2025-06-13 08:43:37'),
('U71', 'Miko', '5025231385@student.its.ac.id', 'password71', 'customer', '2025-06-13 08:43:37'),
('U72', 'Nadia', '5025231386@student.its.ac.id', 'password72', 'customer', '2025-06-13 08:43:37'),
('U73', 'Oscar', '5025231387@student.its.ac.id', 'password73', 'customer', '2025-06-13 08:43:37'),
('U74', 'Prita', '5025231388@student.its.ac.id', 'password74', 'customer', '2025-06-13 08:43:37'),
('U75', 'Qomar', '5025231389@student.its.ac.id', 'password75', 'customer', '2025-06-13 08:43:37'),
('U76', 'Rina', '5025231390@student.its.ac.id', 'password76', 'customer', '2025-06-13 08:43:37'),
('U77', 'Syifa', '5025231391@student.its.ac.id', 'password77', 'customer', '2025-06-13 08:43:37'),
('U78', 'Tomi', '5025231392@student.its.ac.id', 'password78', 'customer', '2025-06-13 08:43:37'),
('U79', 'Ulfa', '5025231393@student.its.ac.id', 'password79', 'customer', '2025-06-13 08:43:37'),
('U80', 'Vera', '5025231394@student.its.ac.id', 'password80', 'customer', '2025-06-13 08:43:37'),
('U81', 'Wahyu', '5025231395@student.its.ac.id', 'password81', 'customer', '2025-06-13 08:43:37'),
('U82', 'Xavier', '5025231396@student.its.ac.id', 'password82', 'customer', '2025-06-13 08:43:37'),
('U83', 'Yuni', '5025231397@student.its.ac.id', 'password83', 'customer', '2025-06-13 08:43:37'),
('U84', 'Zaki', '5025231398@student.its.ac.id', 'password84', 'customer', '2025-06-13 08:43:37');

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_jasa_filter_harga`
-- (See below for the actual view)
--
CREATE TABLE `view_jasa_filter_harga` (
`serviceId` varchar(3)
,`namaServive` varchar(25)
,`deskripsi` text
,`hargaDasar` float(10,2)
,`durasi` timestamp
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_jasa_provider`
-- (See below for the actual view)
--
CREATE TABLE `view_jasa_provider` (
`serviceId` varchar(3)
,`namaServive` varchar(25)
,`providerID` varchar(3)
,`jumlah_order` bigint(21)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_jasa_terpopuler`
-- (See below for the actual view)
--
CREATE TABLE `view_jasa_terpopuler` (
`serviceId` varchar(3)
,`namaServive` varchar(25)
,`jumlah_order` bigint(21)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_provider_rating_tinggi`
-- (See below for the actual view)
--
CREATE TABLE `view_provider_rating_tinggi` (
`providerID` varchar(3)
,`namaUsaha` varchar(25)
,`ratingRata` int(11)
);

-- --------------------------------------------------------

--
-- Structure for view `view_jasa_filter_harga`
--
DROP TABLE IF EXISTS `view_jasa_filter_harga`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_jasa_filter_harga`  AS SELECT `s`.`serviceId` AS `serviceId`, `s`.`namaServive` AS `namaServive`, `s`.`deskripsi` AS `deskripsi`, `s`.`hargaDasar` AS `hargaDasar`, `s`.`durasi` AS `durasi` FROM `service` AS `s` ORDER BY `s`.`hargaDasar` ASC ;

-- --------------------------------------------------------

--
-- Structure for view `view_jasa_provider`
--
DROP TABLE IF EXISTS `view_jasa_provider`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_jasa_provider`  AS SELECT `s`.`serviceId` AS `serviceId`, `s`.`namaServive` AS `namaServive`, `sp`.`Provider_providerID` AS `providerID`, count(`o`.`orderId`) AS `jumlah_order` FROM ((`service` `s` join `service_provider` `sp` on(`s`.`serviceId` = `sp`.`Service_serviceId`)) left join `order` `o` on(`s`.`serviceId` = `o`.`Service_serviceId`)) GROUP BY `s`.`serviceId`, `s`.`namaServive`, `sp`.`Provider_providerID` ;

-- --------------------------------------------------------

--
-- Structure for view `view_jasa_terpopuler`
--
DROP TABLE IF EXISTS `view_jasa_terpopuler`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_jasa_terpopuler`  AS SELECT `s`.`serviceId` AS `serviceId`, `s`.`namaServive` AS `namaServive`, count(`o`.`orderId`) AS `jumlah_order` FROM (`service` `s` left join `order` `o` on(`s`.`serviceId` = `o`.`Service_serviceId`)) GROUP BY `s`.`serviceId`, `s`.`namaServive` ORDER BY count(`o`.`orderId`) DESC ;

-- --------------------------------------------------------

--
-- Structure for view `view_provider_rating_tinggi`
--
DROP TABLE IF EXISTS `view_provider_rating_tinggi`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_provider_rating_tinggi`  AS SELECT `provider`.`providerID` AS `providerID`, `provider`.`namaUsaha` AS `namaUsaha`, `provider`.`ratingRata` AS `ratingRata` FROM `provider` WHERE `provider`.`ratingRata` >= 4.5 ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`feedbackId`,`Order_User_user_id`,`Order_orderId`,`Order_Service_serviceId`),
  ADD KEY `Feedback_Order` (`Order_orderId`,`Order_User_user_id`,`Order_Service_serviceId`);

--
-- Indexes for table `log_admin`
--
ALTER TABLE `log_admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order`
--
ALTER TABLE `order`
  ADD PRIMARY KEY (`orderId`,`User_user_id`,`Service_serviceId`),
  ADD KEY `Order_Service` (`Service_serviceId`),
  ADD KEY `Order_User` (`User_user_id`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`paymentId`,`Order_User_user_id`,`Order_orderId`,`Order_Service_serviceId`),
  ADD KEY `Payment_Order` (`Order_orderId`,`Order_User_user_id`,`Order_Service_serviceId`);

--
-- Indexes for table `provider`
--
ALTER TABLE `provider`
  ADD PRIMARY KEY (`providerID`,`User_user_id`),
  ADD KEY `Provider_User` (`User_user_id`);

--
-- Indexes for table `service`
--
ALTER TABLE `service`
  ADD PRIMARY KEY (`serviceId`);

--
-- Indexes for table `service_provider`
--
ALTER TABLE `service_provider`
  ADD PRIMARY KEY (`Service_serviceId`,`Provider_providerID`,`Provider_User_user_id`),
  ADD KEY `Service_Provider_Provider` (`Provider_providerID`,`Provider_User_user_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `log_admin`
--
ALTER TABLE `log_admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `order`
--
ALTER TABLE `order`
  ADD CONSTRAINT `Order_Service` FOREIGN KEY (`Service_serviceId`) REFERENCES `service` (`serviceId`),
  ADD CONSTRAINT `Order_User` FOREIGN KEY (`User_user_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `Payment_Order` FOREIGN KEY (`Order_orderId`) REFERENCES `order` (`orderId`);

--
-- Constraints for table `provider`
--
ALTER TABLE `provider`
  ADD CONSTRAINT `Provider_User` FOREIGN KEY (`User_user_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `service_provider`
--
ALTER TABLE `service_provider`
  ADD CONSTRAINT `Service_Provider_Provider` FOREIGN KEY (`Provider_providerID`,`Provider_User_user_id`) REFERENCES `provider` (`providerID`, `User_user_id`),
  ADD CONSTRAINT `Service_Provider_Service` FOREIGN KEY (`Service_serviceId`) REFERENCES `service` (`serviceId`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
