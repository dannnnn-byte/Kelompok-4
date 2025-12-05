-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 05 Des 2025 pada 03.26
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
-- Database: `jawatrip`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`) VALUES
(4, 'kelompok4@gmail.com', '1234'),
(5, 'dani', '12345');

-- --------------------------------------------------------

--
-- Struktur dari tabel `hotel`
--

CREATE TABLE `hotel` (
  `id_hotel` int(11) NOT NULL,
  `nama_hotel` varchar(100) NOT NULL,
  `kota` varchar(50) DEFAULT NULL,
  `bintang` int(1) DEFAULT NULL,
  `harga_per_malam` decimal(15,2) DEFAULT NULL,
  `gambar` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `master_hotel`
--

CREATE TABLE `master_hotel` (
  `id_hotel` int(11) NOT NULL,
  `nama_hotel` varchar(100) NOT NULL,
  `bintang` int(1) DEFAULT NULL COMMENT 'Contoh: 3 untuk Bintang 3',
  `lokasi` varchar(100) DEFAULT NULL,
  `gambar_hotel` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `master_transport`
--

CREATE TABLE `master_transport` (
  `id_transport` int(11) NOT NULL,
  `jenis_kendaraan` varchar(100) NOT NULL COMMENT 'Contoh: Hiace Premiere / Bus HDD',
  `kapasitas_kursi` int(11) DEFAULT NULL,
  `fasilitas_mobil` text DEFAULT NULL COMMENT 'Contoh: AC, Karaoke, Reclining Seat',
  `gambar_transport` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `destinasi` varchar(100) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `paket_wisata`
--

CREATE TABLE `paket_wisata` (
  `id_paket` int(11) NOT NULL,
  `nama_paket` varchar(100) NOT NULL,
  `deskripsi_wisata` text DEFAULT NULL,
  `harga_per_pax` decimal(15,2) NOT NULL,
  `durasi` varchar(50) DEFAULT NULL,
  `id_hotel` int(11) DEFAULT NULL,
  `id_transport` int(11) DEFAULT NULL,
  `fasilitas_lain` text DEFAULT NULL COMMENT 'List html: <li>Makan 3x</li> <li>Tiket Masuk</li>',
  `gambar_paket` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id_pembayaran` int(11) NOT NULL,
  `id_pemesanan` varchar(20) NOT NULL,
  `tgl_bayar` datetime DEFAULT current_timestamp(),
  `jumlah_bayar` decimal(15,2) NOT NULL,
  `bukti_transfer` varchar(255) DEFAULT NULL,
  `bank_asal` varchar(50) DEFAULT NULL,
  `nama_pengirim` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pemesanan`
--

CREATE TABLE `pemesanan` (
  `id_pemesanan` varchar(20) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `id_paket` int(11) DEFAULT NULL,
  `tgl_tour` date DEFAULT NULL,
  `jumlah_peserta` int(11) DEFAULT NULL,
  `total_bayar` decimal(15,2) DEFAULT NULL,
  `status_bayar` enum('pending','lunas','batal') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `kota` varchar(100) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `rating` int(11) NOT NULL,
  `komentar` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `tempat` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `reviews`
--

INSERT INTO `reviews` (`id`, `kota`, `nama`, `rating`, `komentar`, `created_at`, `tempat`) VALUES
(4, '', 'Dani ', 5, 'Mantap', '2025-12-05 02:02:00', 'bromo'),
(5, '', 'dani', 4, 'mantap', '2025-12-05 02:05:52', 'tumpak_sewu');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','user') DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indeks untuk tabel `hotel`
--
ALTER TABLE `hotel`
  ADD PRIMARY KEY (`id_hotel`);

--
-- Indeks untuk tabel `master_hotel`
--
ALTER TABLE `master_hotel`
  ADD PRIMARY KEY (`id_hotel`);

--
-- Indeks untuk tabel `master_transport`
--
ALTER TABLE `master_transport`
  ADD PRIMARY KEY (`id_transport`);

--
-- Indeks untuk tabel `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `paket_wisata`
--
ALTER TABLE `paket_wisata`
  ADD PRIMARY KEY (`id_paket`),
  ADD KEY `id_hotel` (`id_hotel`),
  ADD KEY `id_transport` (`id_transport`);

--
-- Indeks untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id_pembayaran`),
  ADD KEY `id_pemesanan` (`id_pemesanan`);

--
-- Indeks untuk tabel `pemesanan`
--
ALTER TABLE `pemesanan`
  ADD PRIMARY KEY (`id_pemesanan`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_paket` (`id_paket`);

--
-- Indeks untuk tabel `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `hotel`
--
ALTER TABLE `hotel`
  MODIFY `id_hotel` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `master_hotel`
--
ALTER TABLE `master_hotel`
  MODIFY `id_hotel` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `master_transport`
--
ALTER TABLE `master_transport`
  MODIFY `id_transport` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `paket_wisata`
--
ALTER TABLE `paket_wisata`
  MODIFY `id_paket` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id_pembayaran` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `paket_wisata`
--
ALTER TABLE `paket_wisata`
  ADD CONSTRAINT `paket_wisata_ibfk_1` FOREIGN KEY (`id_hotel`) REFERENCES `master_hotel` (`id_hotel`) ON DELETE SET NULL,
  ADD CONSTRAINT `paket_wisata_ibfk_2` FOREIGN KEY (`id_transport`) REFERENCES `master_transport` (`id_transport`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`id_pemesanan`) REFERENCES `pemesanan` (`id_pemesanan`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pemesanan`
--
ALTER TABLE `pemesanan`
  ADD CONSTRAINT `pemesanan_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`),
  ADD CONSTRAINT `pemesanan_ibfk_2` FOREIGN KEY (`id_paket`) REFERENCES `paket_wisata` (`id_paket`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
