-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 29 Jun 2025 pada 17.28
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
-- Database: `silandik_semarang`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `berita`
--

CREATE TABLE `berita` (
  `id` int(11) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `isi` text DEFAULT NULL,
  `kategori` enum('dinas','sekolah') NOT NULL,
  `penulis` varchar(100) DEFAULT 'Admin',
  `created_at` datetime DEFAULT current_timestamp(),
  `views` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `berita`
--

INSERT INTO `berita` (`id`, `judul`, `gambar`, `isi`, `kategori`, `penulis`, `created_at`, `views`) VALUES
(8, 'Berita Sekolah Pertama', '1751082775_685f671778934.png', 'berita pertama hari ini\r\nberita pertama hari ini\r\nberita pertama hari ini\r\nberita pertama hari ini', 'sekolah', 'Admin', '2025-06-28 10:52:55', 0);

-- --------------------------------------------------------

--
-- Struktur dari tabel `dasar_hukum`
--

CREATE TABLE `dasar_hukum` (
  `id` int(11) NOT NULL,
  `nomor_regulasi` varchar(255) NOT NULL,
  `tentang` text NOT NULL,
  `draft_hukum` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `dasar_hukum`
--

INSERT INTO `dasar_hukum` (`id`, `nomor_regulasi`, `tentang`, `draft_hukum`) VALUES
(1, '123', 'hukum 1', 'regulasi_685e83b9858d39.89427320.pdf');

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
  `deskripsi` text DEFAULT NULL,
  `jenjang_sekolah` enum('SD','SMP','SMA','SMK') NOT NULL DEFAULT 'SD'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `data_sekolah_inklusi`
--

INSERT INTO `data_sekolah_inklusi` (`id`, `npsn`, `nama_sekolah`, `alamat`, `kepala_sekolah`, `telepon`, `tanggal_berdiri`, `website`, `logo_sekolah`, `deskripsi`, `jenjang_sekolah`) VALUES
(19, '123', 'SD negeri 1', 'jl disini', 'kuman', '0808-1515-4848', '1986-07-27', 'https://github.com/', '1751012549_Screenshot 2025-06-18 161130.png', 'vagdsfhbgjnkfghjh', 'SD'),
(20, '123', 'SMK 2 Semarang Kota', 'Jl. Lodan Raya No. 1, Kel. Bandarharjo, Kec. Semarang Utara, Kota Semarang, Provinsi Jawa Tengah.', 'awan', '0243563228', '2025-06-27', 'https://github.com/', '1751082816_logo_dinas.png', 'dfnsbdgndfosadpfmsgd', 'SMK');

-- --------------------------------------------------------

--
-- Struktur dari tabel `data_siswa`
--

CREATE TABLE `data_siswa` (
  `id` int(11) NOT NULL,
  `nisn` varchar(20) NOT NULL,
  `nama_siswa` varchar(100) NOT NULL,
  `jenis_kelamin` enum('L','P') NOT NULL,
  `kelas` varchar(10) NOT NULL,
  `jenis_inklusi` varchar(255) NOT NULL,
  `sekolah_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `data_siswa`
--

INSERT INTO `data_siswa` (`id`, `nisn`, `nama_siswa`, `jenis_kelamin`, `kelas`, `jenis_inklusi`, `sekolah_id`) VALUES
(1, '23131', 'Wawan', 'L', '5', 'jenis 1', 19),
(2, '5445', 'siti', 'P', '6', '', 19),
(4, '100012', 'Wawan', 'L', '1', '', 20),
(5, '3456i', 'Anugrah Ridho Afriadi', 'L', '2', 'jenis 1', 20);

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

--
-- Dumping data untuk tabel `galeri`
--

INSERT INTO `galeri` (`id`, `sekolah_id`, `path_gambar`, `judul`) VALUES
(5, 19, '1751013097_Screenshot 2025-05-18 205239.png', 'gambar 1'),
(6, 19, '1751018432_Screenshot 2025-05-31 024555.png', 'gambar 2');

-- --------------------------------------------------------

--
-- Struktur dari tabel `info_sekolah_inklusi`
--

CREATE TABLE `info_sekolah_inklusi` (
  `id` int(11) NOT NULL,
  `sekolah_id` int(11) DEFAULT NULL,
  `nama_sekolah` varchar(255) NOT NULL,
  `nama_kegiatan` varchar(255) NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `tanggal` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `info_sekolah_inklusi`
--

INSERT INTO `info_sekolah_inklusi` (`id`, `sekolah_id`, `nama_sekolah`, `nama_kegiatan`, `foto`, `tanggal`, `created_at`) VALUES
(8, 20, '', 'kegiatan pertama', '686154079ccfa_1751208967.png', '2025-06-29', '2025-06-29 14:56:07');

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

--
-- Dumping data untuk tabel `prasarana`
--

INSERT INTO `prasarana` (`id`, `sekolah_id`, `jenis_prasarana`, `jumlah`) VALUES
(17, 19, 'ruang kelas', 14),
(18, 19, 'lab komputer', 2),
(22, 20, 'ruang kelas', 20);

-- --------------------------------------------------------

--
-- Struktur dari tabel `rekap`
--

CREATE TABLE `rekap` (
  `id` int(11) NOT NULL,
  `sekolah_id` int(11) NOT NULL,
  `jumlah_pegawai` int(11) NOT NULL,
  `jumlah_rombel` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `rekap`
--

INSERT INTO `rekap` (`id`, `sekolah_id`, `jumlah_pegawai`, `jumlah_rombel`) VALUES
(4, 19, 30, 24),
(5, 20, 39, 41);

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','umum','pengurus') NOT NULL DEFAULT 'umum',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `request_pengurus` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `role`, `created_at`, `request_pengurus`) VALUES
(1, 'nizar@gmail.com', '1234567890', 'admin', '2025-04-21 07:04:26', 0),
(2, 'silandik@gmail.com', 'silandiksemarang123', 'admin', '2025-04-21 11:37:02', 0),
(6, 'akunpengurus@gmail.com', '$2y$10$PWPGQgg.EmY0GeRBa0iT2epvyiREp61zux1z4PwpzaOrkopPe1Hbq', 'pengurus', '2025-06-27 23:45:46', 0),
(8, 'akunbaru@gmail.com', '$2y$10$qGjC9paUd4vsnpgYYpHs7On6r0eQM0WYEqgkAhZG20grFaWovWKF6', '', '2025-06-29 15:18:46', 0);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `berita`
--
ALTER TABLE `berita`
  ADD PRIMARY KEY (`id`);

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
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nisn` (`nisn`),
  ADD KEY `sekolah_id` (`sekolah_id`);

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
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_sekolah` (`sekolah_id`);

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
-- AUTO_INCREMENT untuk tabel `berita`
--
ALTER TABLE `berita`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `dasar_hukum`
--
ALTER TABLE `dasar_hukum`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `data_sekolah_inklusi`
--
ALTER TABLE `data_sekolah_inklusi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT untuk tabel `data_siswa`
--
ALTER TABLE `data_siswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `galeri`
--
ALTER TABLE `galeri`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `info_sekolah_inklusi`
--
ALTER TABLE `info_sekolah_inklusi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `prasarana`
--
ALTER TABLE `prasarana`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT untuk tabel `rekap`
--
ALTER TABLE `rekap`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `data_siswa`
--
ALTER TABLE `data_siswa`
  ADD CONSTRAINT `data_siswa_ibfk_1` FOREIGN KEY (`sekolah_id`) REFERENCES `data_sekolah_inklusi` (`id`);

--
-- Ketidakleluasaan untuk tabel `galeri`
--
ALTER TABLE `galeri`
  ADD CONSTRAINT `galeri_ibfk_1` FOREIGN KEY (`sekolah_id`) REFERENCES `data_sekolah_inklusi` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `info_sekolah_inklusi`
--
ALTER TABLE `info_sekolah_inklusi`
  ADD CONSTRAINT `fk_sekolah` FOREIGN KEY (`sekolah_id`) REFERENCES `data_sekolah_inklusi` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

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
