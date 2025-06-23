-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 23 Jun 2025 pada 02.07
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
-- Database: `silandik-semarang`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `dasar_hukum`
--

CREATE TABLE `dasar_hukum` (
  `id` int(11) NOT NULL,
  `draft_hukum` varchar(250) NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `dasar_hukum`
--

INSERT INTO `dasar_hukum` (`id`, `draft_hukum`) VALUES
(1, 'SR Apriansyah Wibowo.pdf');

-- --------------------------------------------------------

--
-- Struktur dari tabel `data_sekolah_inklusi`
--

CREATE TABLE `data_sekolah_inklusi` (
  `id` int(11) NOT NULL,
  `npsn` varchar(20) NOT NULL,
  `nama_sekolah` varchar(255) NOT NULL,
  `alamat` varchar(255) NOT NULL,
  `kepala_sekolah` varchar(100) NOT NULL,
  `telepon` varchar(20) DEFAULT NULL,
  `tanggal_berdiri` date DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `logo_sekolah` varchar(255) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `data_sekolah_inklusi`
--

INSERT INTO `data_sekolah_inklusi` (`id`, `npsn`, `nama_sekolah`, `alamat`, `kepala_sekolah`, `telepon`, `tanggal_berdiri`, `website`, `logo_sekolah`, `deskripsi`) VALUES
(10, '2', 'SMK BINUS', 'Alamat ini', 'WAWAN', '0814472558', '2025-06-23', 'https://github.com/', 'Acer_Wallpaper_02_5000x2813.jpg', 'ertyuio'),
(11, '33', 'SMA 2 Semarang', 'Alamat itu', 'wanto', '0214587963', '1995-02-08', 'https://github.com/', 'sdn bandarharjo 3.jpg', 'aesrdftghjkj'),
(12, '44', 'SMA 2 Semarang', 'alamat mana', 'wanto', '0214587963', '1980-11-12', 'https://github.com/', 'Screenshot 2025-06-23 050420.png', 'dfuytg');

-- --------------------------------------------------------

--
-- Struktur dari tabel `data_siswa`
--

CREATE TABLE `data_siswa` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `asal_sekolah` varchar(100) DEFAULT NULL,
  `kelas` varchar(20) DEFAULT NULL,
  `alamat` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `data_siswa`
--

INSERT INTO `data_siswa` (`id`, `nama`, `asal_sekolah`, `kelas`, `alamat`) VALUES
(28, 'fizi dwi saputra', 'sd sekaran', '5', 'sekaran'),
(29, 'asd', 'asf', 'f', 'asf'),
(30, 'komeng', 'sekolah disini', 'kelas sekarang', 'ada');

-- --------------------------------------------------------

--
-- Struktur dari tabel `dokumen_kurikulum_inklusi`
--

CREATE TABLE `dokumen_kurikulum_inklusi` (
  `id` int(11) NOT NULL,
  `draft_kurikulum` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `dokumen_kurikulum_inklusi`
--

INSERT INTO `dokumen_kurikulum_inklusi` (`id`, `draft_kurikulum`) VALUES
(1, 'Pengganti+Article+2_Apriansyah+Wibowo_Juni+2025.pdf');

-- --------------------------------------------------------

--
-- Struktur dari tabel `galeri`
--

CREATE TABLE `galeri` (
  `id` int(11) NOT NULL,
  `sekolah_id` int(11) NOT NULL,
  `path_gambar` varchar(255) NOT NULL,
  `judul` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `info_sekolah_inklusi`
--

CREATE TABLE `info_sekolah_inklusi` (
  `id` int(11) NOT NULL,
  `draft_sekolah` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `info_sekolah_inklusi`
--

INSERT INTO `info_sekolah_inklusi` (`id`, `draft_sekolah`) VALUES
(1, 'Kota Salatiga_Penjelasan_Raperwali_Satu Data - pphukumsetda.pdf');

-- --------------------------------------------------------

--
-- Struktur dari tabel `prasarana`
--

CREATE TABLE `prasarana` (
  `id` int(11) NOT NULL,
  `sekolah_id` int(11) NOT NULL,
  `jenis_prasarana` varchar(100) NOT NULL,
  `jumlah` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `rekap`
--

CREATE TABLE `rekap` (
  `id` int(11) NOT NULL,
  `sekolah_id` int(11) NOT NULL,
  `jumlah_pegawai` int(11) NOT NULL,
  `jumlah_siswa` int(11) NOT NULL,
  `jumlah_rombel` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','umum') NOT NULL DEFAULT 'umum',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'nizar@gmail.com', '1234567890', 'admin', '2025-04-21 07:04:26'),
(2, 'silandik@gmail.com', 'silandiksemarang123', 'admin', '2025-04-21 11:37:02');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `dasar_hukum`
--
ALTER TABLE `dasar_hukum`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `data_sekolah_inklusi`
--
ALTER TABLE `data_sekolah_inklusi`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `data_siswa`
--
ALTER TABLE `data_siswa`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `dokumen_kurikulum_inklusi`
--
ALTER TABLE `dokumen_kurikulum_inklusi`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `galeri`
--
ALTER TABLE `galeri`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sekolah_id` (`sekolah_id`);

--
-- Indeks untuk tabel `info_sekolah_inklusi`
--
ALTER TABLE `info_sekolah_inklusi`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `prasarana`
--
ALTER TABLE `prasarana`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sekolah_id` (`sekolah_id`);

--
-- Indeks untuk tabel `rekap`
--
ALTER TABLE `rekap`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sekolah_id` (`sekolah_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `data_sekolah_inklusi`
--
ALTER TABLE `data_sekolah_inklusi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `data_siswa`
--
ALTER TABLE `data_siswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT untuk tabel `galeri`
--
ALTER TABLE `galeri`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `prasarana`
--
ALTER TABLE `prasarana`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `rekap`
--
ALTER TABLE `rekap`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `galeri`
--
ALTER TABLE `galeri`
  ADD CONSTRAINT `galeri_ibfk_1` FOREIGN KEY (`sekolah_id`) REFERENCES `data_sekolah_inklusi` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `prasarana`
--
ALTER TABLE `prasarana`
  ADD CONSTRAINT `prasarana_ibfk_1` FOREIGN KEY (`sekolah_id`) REFERENCES `data_sekolah_inklusi` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `rekap`
--
ALTER TABLE `rekap`
  ADD CONSTRAINT `rekap_ibfk_1` FOREIGN KEY (`sekolah_id`) REFERENCES `data_sekolah_inklusi` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
