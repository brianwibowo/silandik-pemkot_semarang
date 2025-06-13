-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jun 13, 2025 at 07:08 PM
-- Server version: 10.11.10-MariaDB
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u655368359_silandik`
--

-- --------------------------------------------------------

--
-- Table structure for table `dasar_hukum`
--

CREATE TABLE `dasar_hukum` (
  `id` int(11) NOT NULL,
  `draft_hukum` varchar(250) NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dasar_hukum`
--

INSERT INTO `dasar_hukum` (`id`, `draft_hukum`) VALUES
(1, 'SR Apriansyah Wibowo.pdf');

-- --------------------------------------------------------

--
-- Table structure for table `data_sekolah_inklusi`
--

CREATE TABLE `data_sekolah_inklusi` (
  `id` int(11) NOT NULL,
  `nama_sekolah` varchar(255) NOT NULL,
  `logo_sekolah` varchar(255) NOT NULL,
  `deskripsi` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `data_sekolah_inklusi`
--

INSERT INTO `data_sekolah_inklusi` (`id`, `nama_sekolah`, `logo_sekolah`, `deskripsi`, `created_at`) VALUES
(6, 'SMA NEGERI 01 CILACAP', 'IMG_8203.JPG', 'BAGUS SEKOLAHNYA', '2025-04-21 06:28:01'),
(10, 'sd sekiaran', 'l-images20221012205835.jpeg', 'sekaran', '2025-04-25 06:40:11');

-- --------------------------------------------------------

--
-- Table structure for table `data_siswa`
--

CREATE TABLE `data_siswa` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `asal_sekolah` varchar(100) DEFAULT NULL,
  `kelas` varchar(20) DEFAULT NULL,
  `alamat` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `data_siswa`
--

INSERT INTO `data_siswa` (`id`, `nama`, `asal_sekolah`, `kelas`, `alamat`) VALUES
(28, 'fizi dwi saputra', 'sd sekaream', '5', 'ssekAARAN'),
(29, 'asd', 'asf', 'f', 'asf');

-- --------------------------------------------------------

--
-- Table structure for table `dokumen_kurikulum_inklusi`
--

CREATE TABLE `dokumen_kurikulum_inklusi` (
  `id` int(11) NOT NULL,
  `draft_kurikulum` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dokumen_kurikulum_inklusi`
--

INSERT INTO `dokumen_kurikulum_inklusi` (`id`, `draft_kurikulum`) VALUES
(1, 'Pengganti+Article+2_Apriansyah+Wibowo_Juni+2025.pdf');

-- --------------------------------------------------------

--
-- Table structure for table `info_sekolah_inklusi`
--

CREATE TABLE `info_sekolah_inklusi` (
  `id` int(11) NOT NULL,
  `draft_sekolah` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `info_sekolah_inklusi`
--

INSERT INTO `info_sekolah_inklusi` (`id`, `draft_sekolah`) VALUES
(1, 'Kota Salatiga_Penjelasan_Raperwali_Satu Data - pphukumsetda.pdf');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','umum') NOT NULL DEFAULT 'umum',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'nizar@gmail.com', '1234567890', 'admin', '2025-04-21 07:04:26'),
(2, 'silandik@gmail.com', 'silandiksemarang123', 'admin', '2025-04-21 11:37:02');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dasar_hukum`
--
ALTER TABLE `dasar_hukum`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `data_sekolah_inklusi`
--
ALTER TABLE `data_sekolah_inklusi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `data_siswa`
--
ALTER TABLE `data_siswa`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dokumen_kurikulum_inklusi`
--
ALTER TABLE `dokumen_kurikulum_inklusi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `info_sekolah_inklusi`
--
ALTER TABLE `info_sekolah_inklusi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `data_sekolah_inklusi`
--
ALTER TABLE `data_sekolah_inklusi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `data_siswa`
--
ALTER TABLE `data_siswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
