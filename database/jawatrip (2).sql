-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 06, 2025 at 04:48 PM
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
-- Database: `jawatrip`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`) VALUES
(4, 'kelompok4@gmail.com', '1234'),
(5, 'dani', '12345');

-- --------------------------------------------------------

--
-- Table structure for table `hotel`
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
-- Table structure for table `kota`
--

CREATE TABLE `kota` (
  `id_kota` int(11) NOT NULL,
  `nama_kota` varchar(100) NOT NULL,
  `gambar_kota` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kota`
--

INSERT INTO `kota` (`id_kota`, `nama_kota`, `gambar_kota`) VALUES
(1, 'Batu', 'batu.webp'),
(2, 'Mojokerto', 'mojokerto.jpeg'),
(3, 'Sumenep', 'sumenep.jpg'),
(4, 'Banyuwangi', 'banyuwangi.jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `master_hotel`
--

CREATE TABLE `master_hotel` (
  `id_hotel` int(11) NOT NULL,
  `nama_hotel` varchar(100) NOT NULL,
  `bintang` int(1) DEFAULT NULL COMMENT 'Contoh: 3 untuk Bintang 3',
  `lokasi` varchar(100) DEFAULT NULL,
  `gambar_hotel` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `master_hotel`
--

INSERT INTO `master_hotel` (`id_hotel`, `nama_hotel`, `bintang`, `lokasi`, `gambar_hotel`) VALUES
(1, 'Jiwa Jawa Resort', 4, 'Bromo, Probolinggo', 'jiwajawa.jpg'),
(2, 'Ketapang Indah Hotel', 4, 'Banyuwangi', 'ketapang.jpg'),
(3, 'Pohon Inn Hotel', 3, 'Batu, Malang', 'pohoninn.jpg'),
(4, 'Hotel Tugu Malang', 5, 'Kota Malang', 'hoteltugu.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `master_transport`
--

CREATE TABLE `master_transport` (
  `id_transport` int(11) NOT NULL,
  `jenis_kendaraan` varchar(100) NOT NULL COMMENT 'Contoh: Hiace Premiere / Bus HDD',
  `kapasitas_kursi` int(11) DEFAULT NULL,
  `fasilitas_mobil` text DEFAULT NULL COMMENT 'Contoh: AC, Karaoke, Reclining Seat',
  `gambar_transport` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `master_transport`
--

INSERT INTO `master_transport` (`id_transport`, `jenis_kendaraan`, `kapasitas_kursi`, `fasilitas_mobil`, `gambar_transport`) VALUES
(1, 'Toyota Hiace Commuter', 14, 'AC, Reclining Seat, Musik', 'hiace.jpg'),
(2, 'Jeep Hardtop 4x4', 6, 'Offroad Bromo, Driver Pro', 'jeep.jpg'),
(3, 'Bus Medium Pariwisata', 30, 'AC, Karaoke, TV, Selimut', 'bus_medium.jpg'),
(4, 'Toyota Avanza', 6, 'AC, Private Tour', 'avanza.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `destinasi` varchar(100) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `paket_fasilitas`
--

CREATE TABLE `paket_fasilitas` (
  `id_fasilitas` int(11) NOT NULL,
  `id_paket` int(11) NOT NULL,
  `jenis` enum('include','exclude') NOT NULL,
  `item` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `paket_fasilitas`
--

INSERT INTO `paket_fasilitas` (`id_fasilitas`, `id_paket`, `jenis`, `item`) VALUES
(1, 1, 'include', 'Transportasi Selama Tour'),
(2, 1, 'include', 'Tiket Masuk Wisata'),
(3, 1, 'include', 'Masker Gas'),
(4, 1, 'exclude', 'Pengeluaran Pribadi'),
(5, 1, 'exclude', 'Surat Keterangan Sehat');

-- --------------------------------------------------------

--
-- Table structure for table `paket_itinerary`
--

CREATE TABLE `paket_itinerary` (
  `id_itinerary` int(11) NOT NULL,
  `id_paket` int(11) NOT NULL,
  `hari_ke` int(11) DEFAULT 1,
  `jam` varchar(20) DEFAULT NULL,
  `kegiatan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `paket_itinerary`
--

INSERT INTO `paket_itinerary` (`id_itinerary`, `id_paket`, `hari_ke`, `jam`, `kegiatan`) VALUES
(1, 1, 1, '00:10', 'Penjemputan Stasiun / Hotel sekitar Banyuwangi Kota'),
(2, 1, 1, '00:30', 'Berangkat menuju Kawah Ijen'),
(3, 1, 1, '01:30', 'Sampai Paltuding, kemudian konfirmasi tiket online'),
(4, 1, 1, '04:00', 'Sampai area Puncak, melihat Blue Fire dan Sunrise Point');

-- --------------------------------------------------------

--
-- Table structure for table `paket_wisata`
--

CREATE TABLE `paket_wisata` (
  `id_paket` int(11) NOT NULL,
  `nama_paket` varchar(100) NOT NULL,
  `harga_per_pax` decimal(15,2) NOT NULL,
  `durasi` varchar(50) DEFAULT NULL,
  `id_hotel` int(11) DEFAULT NULL,
  `id_transport` int(11) DEFAULT NULL,
  `fasilitas_lain` text DEFAULT NULL COMMENT 'List html: <li>Makan 3x</li> <li>Tiket Masuk</li>',
  `gambar_paket` varchar(255) DEFAULT NULL,
  `id_kota` int(11) DEFAULT NULL,
  `deskripsi_wisata` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `paket_wisata`
--

INSERT INTO `paket_wisata` (`id_paket`, `nama_paket`, `harga_per_pax`, `durasi`, `id_hotel`, `id_transport`, `fasilitas_lain`, `gambar_paket`, `id_kota`, `deskripsi_wisata`) VALUES
(1, 'Batu 2 Hari 1 Malam', 350000.00, '2 Hari 1 Malam', 1, 2, '<li>Tiket Masuk Jatim Park 1</li><li>Dokumentasi</li><li>2x Makan</li>', 'jtp1.png', 1, 'Penat dengan hiruk pikuk kota? Saatnya short escape ke Kota Batu yang sejuk! Paket 2D1N ini dirancang khusus buat kamu yang butuh healing singkat tapi berkualitas. Nikmati udara segar pegunungan, sunset yang memukau, dan kuliner malam yang lezat di Batu. Dari wisata alam yang menenangkan hingga spot foto kekinian di Museum Angkut, semuanya bisa kamu dapatkan di sini. Recharge energimu sekarang bersama JawaTrip!'),
(2, 'Batu 3 Hari 2 Malam', 450000.00, '3 Hari 2 Malam', 2, 1, '<li>Jatim Park 2</li>Dokumentasi<li></li><li>Makan 2x</li>', 'bsz.jpg', 1, NULL),
(3, 'Batu City Tour', 250000.00, '1 Hari 1 Malam', 4, 4, '<li>Tiket Terusan Wisata Kota</li><li>Makan 2x</li><li>Souvenir</li>', 'pelangi.webp', 1, NULL),
(4, 'Wisata Alam Batu', 300000.00, '1 Hari 1 Malam', 4, 4, '<li>Tiket Pantai</li><li>Dinner Romantis</li><li>Guide Sejarah</li>', 'alambatu1.jpg', 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran`
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
-- Table structure for table `pemesanan`
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
-- Table structure for table `reviews`
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
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `kota`, `nama`, `rating`, `komentar`, `created_at`, `tempat`) VALUES
(4, '', 'Dani ', 5, 'Mantap', '2025-12-05 02:02:00', 'bromo'),
(5, '', 'dani', 4, 'mantap', '2025-12-05 02:05:52', 'tumpak_sewu'),
(6, '', 'M. ARIF', 5, 'manatp\r\n', '2025-12-05 07:31:13', 'Museum Angkut'),
(7, '', 'Arif', 4, 'seruuuu!!!!!', '2025-12-05 08:50:34', 'bromo');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `no_telepon` int(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `nama_lengkap`, `email`, `password`, `role`, `no_telepon`) VALUES
(1, 'Admin JawaTrip', 'admin@jawatrip.id', '12345', 'admin', 2147483647),
(2, 'Budi Santoso', 'budi@gmail.com', '12345', 'user', 2147483647),
(3, 'Siti Aminah', 'siti@yahoo.com', '12345', 'user', 2147483647),
(4, 'Andi Pratama', 'andi@outlook.com', '12345', 'user', 2147483647);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `hotel`
--
ALTER TABLE `hotel`
  ADD PRIMARY KEY (`id_hotel`);

--
-- Indexes for table `kota`
--
ALTER TABLE `kota`
  ADD PRIMARY KEY (`id_kota`);

--
-- Indexes for table `master_hotel`
--
ALTER TABLE `master_hotel`
  ADD PRIMARY KEY (`id_hotel`);

--
-- Indexes for table `master_transport`
--
ALTER TABLE `master_transport`
  ADD PRIMARY KEY (`id_transport`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `paket_fasilitas`
--
ALTER TABLE `paket_fasilitas`
  ADD PRIMARY KEY (`id_fasilitas`),
  ADD KEY `id_paket` (`id_paket`);

--
-- Indexes for table `paket_itinerary`
--
ALTER TABLE `paket_itinerary`
  ADD PRIMARY KEY (`id_itinerary`),
  ADD KEY `id_paket` (`id_paket`);

--
-- Indexes for table `paket_wisata`
--
ALTER TABLE `paket_wisata`
  ADD PRIMARY KEY (`id_paket`),
  ADD KEY `id_hotel` (`id_hotel`),
  ADD KEY `id_transport` (`id_transport`),
  ADD KEY `fk_kota_paket` (`id_kota`);

--
-- Indexes for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id_pembayaran`),
  ADD KEY `id_pemesanan` (`id_pemesanan`);

--
-- Indexes for table `pemesanan`
--
ALTER TABLE `pemesanan`
  ADD PRIMARY KEY (`id_pemesanan`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_paket` (`id_paket`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `hotel`
--
ALTER TABLE `hotel`
  MODIFY `id_hotel` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kota`
--
ALTER TABLE `kota`
  MODIFY `id_kota` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `master_hotel`
--
ALTER TABLE `master_hotel`
  MODIFY `id_hotel` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `master_transport`
--
ALTER TABLE `master_transport`
  MODIFY `id_transport` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `paket_fasilitas`
--
ALTER TABLE `paket_fasilitas`
  MODIFY `id_fasilitas` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `paket_itinerary`
--
ALTER TABLE `paket_itinerary`
  MODIFY `id_itinerary` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `paket_wisata`
--
ALTER TABLE `paket_wisata`
  MODIFY `id_paket` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id_pembayaran` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `paket_fasilitas`
--
ALTER TABLE `paket_fasilitas`
  ADD CONSTRAINT `paket_fasilitas_ibfk_1` FOREIGN KEY (`id_paket`) REFERENCES `paket_wisata` (`id_paket`) ON DELETE CASCADE;

--
-- Constraints for table `paket_itinerary`
--
ALTER TABLE `paket_itinerary`
  ADD CONSTRAINT `paket_itinerary_ibfk_1` FOREIGN KEY (`id_paket`) REFERENCES `paket_wisata` (`id_paket`) ON DELETE CASCADE;

--
-- Constraints for table `paket_wisata`
--
ALTER TABLE `paket_wisata`
  ADD CONSTRAINT `fk_kota_paket` FOREIGN KEY (`id_kota`) REFERENCES `kota` (`id_kota`) ON DELETE SET NULL,
  ADD CONSTRAINT `paket_wisata_ibfk_1` FOREIGN KEY (`id_hotel`) REFERENCES `master_hotel` (`id_hotel`) ON DELETE SET NULL,
  ADD CONSTRAINT `paket_wisata_ibfk_2` FOREIGN KEY (`id_transport`) REFERENCES `master_transport` (`id_transport`) ON DELETE SET NULL;

--
-- Constraints for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`id_pemesanan`) REFERENCES `pemesanan` (`id_pemesanan`) ON DELETE CASCADE;

--
-- Constraints for table `pemesanan`
--
ALTER TABLE `pemesanan`
  ADD CONSTRAINT `pemesanan_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`),
  ADD CONSTRAINT `pemesanan_ibfk_2` FOREIGN KEY (`id_paket`) REFERENCES `paket_wisata` (`id_paket`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
