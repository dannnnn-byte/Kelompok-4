-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 17 Des 2025 pada 04.23
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
-- Struktur dari tabel `booking_log`
--

CREATE TABLE `booking_log` (
  `id_log` int(11) NOT NULL,
  `id_pemesanan` varchar(50) DEFAULT NULL,
  `aktivitas` varchar(100) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `booking_log`
--

INSERT INTO `booking_log` (`id_log`, `id_pemesanan`, `aktivitas`, `keterangan`, `created_at`) VALUES
(1, 'PMS1765740615', 'Pemesanan Dibuat', 'Booking baru dengan kode: JWT20251214936C', '2025-12-14 19:30:15'),
(2, 'PMS1765740688', 'Pemesanan Dibuat', 'Booking baru dengan kode: JWT2025121434BA', '2025-12-14 19:31:28'),
(3, 'PMS1765786370', 'Pemesanan Dibuat', 'Booking baru dengan kode: JWT20251215CABD', '2025-12-15 08:12:50'),
(4, 'PMS1765787348', 'Pemesanan Dibuat', 'Booking baru dengan kode: JWT202512151371', '2025-12-15 08:29:08'),
(5, 'PMS1765795232', 'Pemesanan Dibuat', 'Booking baru dengan kode: JWT202512154FFC', '2025-12-15 10:40:32');

-- --------------------------------------------------------

--
-- Struktur dari tabel `chat_messages`
--

CREATE TABLE `chat_messages` (
  `id` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `message` text NOT NULL,
  `sender` enum('user','admin') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `destinasi`
--

CREATE TABLE `destinasi` (
  `id` int(11) NOT NULL,
  `kota` varchar(100) DEFAULT NULL,
  `gambar` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `destinasi`
--

INSERT INTO `destinasi` (`id`, `kota`, `gambar`) VALUES
(5, 'Sumenep', 'img/Sumenep1.jpeg');

-- --------------------------------------------------------

--
-- Struktur dari tabel `destinasi_populer`
--

CREATE TABLE `destinasi_populer` (
  `id` int(11) NOT NULL,
  `nama` varchar(150) DEFAULT NULL,
  `slug` varchar(150) DEFAULT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `aktif` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `destinasi_populer`
--

INSERT INTO `destinasi_populer` (`id`, `nama`, `slug`, `gambar`, `aktif`, `created_at`) VALUES
(1, 'pantai lombang', 'Pantai Lombang - Sampang', '1765883832-Pantai Lombang - Sampang.png', 0, '2025-12-16 11:17:12'),
(2, 'Pantai Lombang', 'Pantai Lombang - Sumenep', '1765884846-Pantai Lombang - Sumenep.jpg', 0, '2025-12-16 11:34:06'),
(3, 'Pantai Lombang', 'Pantai Lombang - Sumenep', '1765885037-Pantai Lombang - Sumenep.jpg', 0, '2025-12-16 11:37:17'),
(4, 'Bromo - Lumajang', 'bromo', 'img/bromo4.jpg', 1, '2025-12-17 02:37:50'),
(5, 'Tumpak Sewu - Lumajang', 'tumpaksewu', 'img/tumpaksewu.jpg', 1, '2025-12-17 02:37:50'),
(6, 'Kawah Ijen - Banyuwangi', 'kawahijen', 'img/ijen2.jpg', 1, '2025-12-17 02:37:50'),
(7, 'Museum Angkut - Malang', 'museumangkut', 'img/angkut.webp', 1, '2025-12-17 02:37:50'),
(8, 'Wisata Bahari Lamongan - Lamongan', 'wbl', 'img/wbl.jpg', 1, '2025-12-17 02:37:50'),
(9, 'Pantai Lombang', 'Pantai Lombang - Sumenep', '1765941247-Pantai Lombang - Sumenep.jpg', 0, '2025-12-17 03:14:07');

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
-- Struktur dari tabel `kota`
--

CREATE TABLE `kota` (
  `id_kota` int(11) NOT NULL,
  `nama_kota` varchar(100) NOT NULL,
  `gambar_kota` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kota`
--

INSERT INTO `kota` (`id_kota`, `nama_kota`, `gambar_kota`) VALUES
(1, 'Batu', 'batu.webp'),
(2, 'Mojokerto', 'mojokerto.jpeg'),
(3, 'Sumenep', 'sumenep.jpg'),
(4, 'Banyuwangi', 'banyuwangi.jpeg');

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

--
-- Dumping data untuk tabel `master_hotel`
--

INSERT INTO `master_hotel` (`id_hotel`, `nama_hotel`, `bintang`, `lokasi`, `gambar_hotel`) VALUES
(1, 'Jiwa Jawa Resort', 4, 'Bromo, Probolinggo', 'jiwajawa.jpg'),
(2, 'Ketapang Indah Hotel', 4, 'Banyuwangi', 'ketapang.jpg'),
(3, 'Pohon Inn Hotel', 3, 'Batu, Malang', 'pohoninn.jpg'),
(4, 'Hotel Tugu Malang', 5, 'Kota Malang', 'hoteltugu.jpg');

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

--
-- Dumping data untuk tabel `master_transport`
--

INSERT INTO `master_transport` (`id_transport`, `jenis_kendaraan`, `kapasitas_kursi`, `fasilitas_mobil`, `gambar_transport`) VALUES
(1, 'Toyota Hiace Commuter', 14, 'AC, Reclining Seat, Musik', 'hiace.jpg'),
(2, 'Jeep Hardtop 4x4', 6, 'Offroad Bromo, Driver Pro', 'jeep.jpg'),
(3, 'Bus Medium Pariwisata', 30, 'AC, Karaoke, TV, Selimut', 'bus_medium.jpg'),
(4, 'Toyota Avanza', 6, 'AC, Private Tour', 'avanza.jpg');

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
-- Struktur dari tabel `paket_fasilitas`
--

CREATE TABLE `paket_fasilitas` (
  `id_fasilitas` int(11) NOT NULL,
  `id_paket` int(11) NOT NULL,
  `jenis` enum('include','exclude') NOT NULL,
  `item` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `paket_fasilitas`
--

INSERT INTO `paket_fasilitas` (`id_fasilitas`, `id_paket`, `jenis`, `item`) VALUES
(1, 1, 'include', 'Transportasi Selama Tour'),
(2, 1, 'include', 'Tiket Masuk Wisata'),
(3, 1, 'include', 'Masker Gas'),
(4, 1, 'exclude', 'Pengeluaran Pribadi'),
(5, 1, 'exclude', 'Surat Keterangan Sehat');

-- --------------------------------------------------------

--
-- Struktur dari tabel `paket_itinerary`
--

CREATE TABLE `paket_itinerary` (
  `id_itinerary` int(11) NOT NULL,
  `id_paket` int(11) NOT NULL,
  `hari_ke` int(11) DEFAULT 1,
  `jam` varchar(20) DEFAULT NULL,
  `kegiatan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `paket_itinerary`
--

INSERT INTO `paket_itinerary` (`id_itinerary`, `id_paket`, `hari_ke`, `jam`, `kegiatan`) VALUES
(1, 1, 1, '00:10', 'Penjemputan Stasiun / Hotel sekitar Banyuwangi Kota'),
(2, 1, 1, '00:30', 'Berangkat menuju Kawah Ijen'),
(3, 1, 1, '01:30', 'Sampai Paltuding, kemudian konfirmasi tiket online'),
(4, 1, 1, '04:00', 'Sampai area Puncak, melihat Blue Fire dan Sunrise Point');

-- --------------------------------------------------------

--
-- Struktur dari tabel `paket_reviews`
--

CREATE TABLE `paket_reviews` (
  `id` int(11) NOT NULL,
  `id_paket` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `rating` tinyint(1) NOT NULL,
  `review_text` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `paket_wisata`
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
-- Dumping data untuk tabel `paket_wisata`
--

INSERT INTO `paket_wisata` (`id_paket`, `nama_paket`, `harga_per_pax`, `durasi`, `id_hotel`, `id_transport`, `fasilitas_lain`, `gambar_paket`, `id_kota`, `deskripsi_wisata`) VALUES
(1, 'Batu 2 Hari 1 Malam', 350000.00, '2 Hari 1 Malam', 1, 2, '<li>Tiket Masuk Jatim Park 1</li><li>Dokumentasi</li><li>2x Makan</li>', 'jtp1.png', 1, 'Penat dengan hiruk pikuk kota? Saatnya short escape ke Kota Batu yang sejuk! Paket 2D1N ini dirancang khusus buat kamu yang butuh healing singkat tapi berkualitas. Nikmati udara segar pegunungan, sunset yang memukau, dan kuliner malam yang lezat di Batu. Dari wisata alam yang menenangkan hingga spot foto kekinian di Museum Angkut, semuanya bisa kamu dapatkan di sini. Recharge energimu sekarang bersama JawaTrip!'),
(2, 'Batu 3 Hari 2 Malam', 450000.00, '3 Hari 2 Malam', 2, 1, '<li>Jatim Park 2</li>Dokumentasi<li></li><li>Makan 2x</li>', 'bsz.jpg', 1, NULL),
(3, 'Batu City Tour', 250000.00, '1 Hari 1 Malam', 4, 4, '<li>Tiket Terusan Wisata Kota</li><li>Makan 2x</li><li>Souvenir</li>', 'pelangi.webp', 1, NULL),
(4, 'Wisata Alam Batu', 300000.00, '1 Hari 1 Malam', 4, 4, '<li>Tiket Pantai</li><li>Dinner Romantis</li><li>Guide Sejarah</li>', 'alambatu1.jpg', 1, NULL),
(5, 'Gili Labak', 750000.00, NULL, NULL, NULL, NULL, 'sumenep1.jpeg', 3, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id_pembayaran` varchar(50) NOT NULL,
  `id_pemesanan` varchar(20) NOT NULL,
  `kode_booking` varchar(50) DEFAULT NULL,
  `metode_bayar` varchar(50) DEFAULT NULL,
  `tanggal_bayar` datetime NOT NULL DEFAULT current_timestamp(),
  `jumlah_bayar` decimal(15,2) NOT NULL,
  `no_va` varchar(50) DEFAULT NULL,
  `bank` varchar(50) DEFAULT NULL,
  `qr_code` text DEFAULT NULL,
  `bukti_bayar` varchar(255) DEFAULT NULL,
  `bank_asal` varchar(50) DEFAULT NULL,
  `nama_pengirim` varchar(100) DEFAULT NULL,
  `status_bayar` enum('pending','menunggu_verifikasi','lunas','dibatalkan') DEFAULT 'pending',
  `catatan` text DEFAULT NULL,
  `tanggal_konfirmasi` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pembayaran`
--

INSERT INTO `pembayaran` (`id_pembayaran`, `id_pemesanan`, `kode_booking`, `metode_bayar`, `tanggal_bayar`, `jumlah_bayar`, `no_va`, `bank`, `qr_code`, `bukti_bayar`, `bank_asal`, `nama_pengirim`, `status_bayar`, `catatan`, `tanggal_konfirmasi`) VALUES
('1', 'PMS1765787348', 'JWT202512151371', 'qris', '2025-12-15 15:50:32', 450000.00, NULL, NULL, 'QRIS_JWT202512151371_450000.00', NULL, NULL, NULL, 'pending', NULL, NULL),
('2', 'PMS1765787348', 'JWT202512151371', 'va_bca', '2025-12-15 15:50:37', 450000.00, '0143968597817', 'BCA', NULL, NULL, NULL, NULL, 'pending', NULL, NULL),
('3', 'PMS1765787348', 'JWT202512151371', 'va_bni', '2025-12-15 15:50:37', 450000.00, '0093559988202', 'BNI', NULL, NULL, NULL, NULL, 'pending', NULL, NULL),
('4', 'PMS1765787348', 'JWT202512151371', 'va_bri', '2025-12-15 15:50:37', 450000.00, '0020142663713', 'BRI', NULL, NULL, NULL, NULL, 'pending', NULL, NULL),
('5', 'PMS1765787348', 'JWT202512151371', 'va_mandiri', '2025-12-15 15:50:37', 450000.00, '0080665575093', 'Mandiri', NULL, NULL, NULL, NULL, 'pending', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `pembayaran_bromo`
--

CREATE TABLE `pembayaran_bromo` (
  `id` int(11) NOT NULL,
  `bromo_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `bukti_bayar` varchar(255) NOT NULL,
  `status_bayar` enum('pending','menunggu_verifikasi','lunas') DEFAULT 'pending',
  `tanggal_bayar` datetime DEFAULT current_timestamp(),
  `tanggal_konfirmasi` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pembayaran_bromo`
--

INSERT INTO `pembayaran_bromo` (`id`, `bromo_id`, `user_id`, `bukti_bayar`, `status_bayar`, `tanggal_bayar`, `tanggal_konfirmasi`, `created_at`, `updated_at`) VALUES
(1, 27, NULL, 'bukti_27_1765874535.png', 'pending', '2025-12-16 15:42:15', NULL, '2025-12-16 08:42:15', '2025-12-16 08:42:15'),
(2, 28, NULL, 'bukti_28_1765875396.png', 'pending', '2025-12-16 15:56:36', NULL, '2025-12-16 08:56:36', '2025-12-16 08:56:36'),
(3, 29, NULL, '1765876944_form java.png', 'menunggu_verifikasi', '2025-12-16 16:22:24', NULL, '2025-12-16 09:22:24', '2025-12-16 09:22:24'),
(4, 30, NULL, '1765877390_ERD.png', 'menunggu_verifikasi', '2025-12-16 16:29:50', NULL, '2025-12-16 09:29:50', '2025-12-16 09:29:50'),
(5, 32, NULL, '1765941157_form java.png', 'menunggu_verifikasi', '2025-12-17 10:12:37', NULL, '2025-12-17 03:12:37', '2025-12-17 03:12:37');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pemesanan`
--

CREATE TABLE `pemesanan` (
  `id_pemesanan` varchar(20) NOT NULL,
  `kode_booking` varchar(30) DEFAULT NULL,
  `id_user` int(11) DEFAULT NULL,
  `id_paket` int(11) DEFAULT NULL,
  `tgl_tour` date DEFAULT NULL,
  `tanggal_keberangkatan` date DEFAULT NULL,
  `jumlah_peserta` int(11) DEFAULT NULL,
  `jumlah_dewasa` int(11) DEFAULT NULL,
  `jumlah_anak` int(11) DEFAULT NULL,
  `total_bayar` decimal(15,2) DEFAULT NULL,
  `total_harga` decimal(15,2) DEFAULT NULL,
  `status_bayar` enum('pending','lunas','batal') DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `tanggal_pesan` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pemesanan`
--

INSERT INTO `pemesanan` (`id_pemesanan`, `kode_booking`, `id_user`, `id_paket`, `tgl_tour`, `tanggal_keberangkatan`, `jumlah_peserta`, `jumlah_dewasa`, `jumlah_anak`, `total_bayar`, `total_harga`, `status_bayar`, `status`, `tanggal_pesan`) VALUES
('PMS1765740615', 'JWT20251214936C', NULL, 2, '2025-12-24', NULL, 1, 1, 0, 450000.00, 450000.00, '', 'pending', '2025-12-14 20:30:15'),
('PMS1765740688', 'JWT2025121434BA', NULL, 3, '2025-12-24', NULL, 1, 1, 0, 250000.00, 250000.00, '', 'pending', '2025-12-14 20:31:28'),
('PMS1765786370', 'JWT20251215CABD', NULL, 1, '2025-12-16', NULL, 1, 1, 0, 350000.00, 350000.00, 'pending', 'pending', '2025-12-15 09:12:50'),
('PMS1765787348', 'JWT202512151371', NULL, 2, '2025-12-25', NULL, 1, 1, 0, 450000.00, 450000.00, 'pending', 'pending', '2025-12-15 09:29:08'),
('PMS1765795232', 'JWT202512154FFC', NULL, 1, '2025-12-16', NULL, 1, 1, 0, 350000.00, 350000.00, 'pending', 'pending', '2025-12-15 11:40:32');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pemesanan_bromo`
--

CREATE TABLE `pemesanan_bromo` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `tanggal_kunjungan` date DEFAULT NULL,
  `jumlah_orang` int(11) DEFAULT NULL,
  `sewa_jeep` varchar(5) DEFAULT NULL,
  `sewa_trail` varchar(5) DEFAULT NULL,
  `jumlah_trail` int(11) DEFAULT NULL,
  `total_harga` int(11) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `bukti_transfer` varchar(255) DEFAULT NULL,
  `waktu_bayar` datetime DEFAULT NULL,
  `alasan_batal` text DEFAULT NULL,
  `waktu_batal` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `penumpang`
--

CREATE TABLE `penumpang` (
  `id_penumpang` int(11) NOT NULL,
  `id_pemesanan` varchar(50) DEFAULT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `no_telepon` varchar(20) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `no_identitas` varchar(50) DEFAULT NULL,
  `tipe_penumpang` enum('Dewasa','Anak') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `penumpang`
--

INSERT INTO `penumpang` (`id_penumpang`, `id_pemesanan`, `nama_lengkap`, `email`, `no_telepon`, `alamat`, `no_identitas`, `tipe_penumpang`, `created_at`) VALUES
(1, 'PMS1765740615', 'huhu', 'asda@gyadyagdabda.com', '0812836178236', 'ajbsndjabdjas', '12312321312312321', 'Dewasa', '2025-12-14 19:30:15'),
(2, 'PMS1765740688', 'riko tampati', 'Rikoboy@gmail.com', '0812836178236', 'smnuad', '1234567890123456', 'Dewasa', '2025-12-14 19:31:28'),
(3, 'PMS1765786370', 'akska', 'alifbai@gmail.com', '0808012122323', 'jl.wownokoko', '242422121212121', 'Dewasa', '2025-12-15 08:12:50'),
(4, 'PMS1765787348', 'alif bai', 'alifbai@gmail.com', '08080808232323', 'alskalkslakslkals', '24242424242422323', 'Dewasa', '2025-12-15 08:29:08'),
(5, 'PMS1765795232', 'Budi Ganteng', 'budi@gmail.com', '0811234567893', 'blega,jawa timut', '1234566', 'Dewasa', '2025-12-15 10:40:32');

-- --------------------------------------------------------

--
-- Struktur dari tabel `promo_diskon`
--

CREATE TABLE `promo_diskon` (
  `id` int(11) NOT NULL,
  `kode_promo` varchar(50) NOT NULL,
  `nama_promo` varchar(200) NOT NULL,
  `jenis_diskon` enum('percentage','fixed') NOT NULL DEFAULT 'percentage',
  `nilai_diskon` decimal(15,2) NOT NULL,
  `min_transaksi` decimal(15,2) DEFAULT 0.00,
  `max_diskon` decimal(15,2) DEFAULT 0.00,
  `kuota` int(11) NOT NULL DEFAULT 0,
  `terpakai` int(11) DEFAULT 0,
  `tanggal_mulai` datetime NOT NULL,
  `tanggal_selesai` datetime NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `promo_diskon`
--

INSERT INTO `promo_diskon` (`id`, `kode_promo`, `nama_promo`, `jenis_diskon`, `nilai_diskon`, `min_transaksi`, `max_diskon`, `kuota`, `terpakai`, `tanggal_mulai`, `tanggal_selesai`, `status`, `created_at`) VALUES
(1, 'TAHUNBARU2026', 'Diskon Tahun Baru 2026', 'percentage', 20.00, 500000.00, 200000.00, 100, 0, '2025-12-20 00:00:00', '2026-01-10 23:59:59', 'active', '2025-12-17 03:04:32'),
(2, 'HEMAT50K', 'Potongan Langsung 50K', 'fixed', 50000.00, 300000.00, 0.00, 50, 0, '2025-12-16 00:00:00', '2025-12-31 23:59:59', 'active', '2025-12-17 03:04:32'),
(3, 'LIBURAN15', 'Diskon Liburan 15%', 'percentage', 15.00, 750000.00, 150000.00, 75, 0, '2025-12-16 00:00:00', '2026-01-31 23:59:59', 'active', '2025-12-17 03:04:32');

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
  `tempat` varchar(100) NOT NULL,
  `balasan_admin` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `reviews`
--

INSERT INTO `reviews` (`id`, `kota`, `nama`, `rating`, `komentar`, `created_at`, `tempat`, `balasan_admin`) VALUES
(4, '', 'Dani ', 5, 'Mantap', '2025-12-05 02:02:00', 'bromo', 'terimakasi kak'),
(5, '', 'dani', 4, 'mantap', '2025-12-05 02:05:52', 'tumpak_sewu', NULL),
(6, '', 'M. ARIF', 5, 'manatp\r\n', '2025-12-05 07:31:13', 'Museum Angkut', NULL),
(7, '', 'Arif', 4, 'seruuuu!!!!!', '2025-12-05 08:50:34', 'bromo', 'acc'),
(10, '', 'Budi Santoso', 5, 'Mantap Broomoo', '2025-12-16 12:40:39', 'tumpak sewu', NULL),
(11, '', 'Budi Santoso', 5, 'mangaoo', '2025-12-17 02:27:16', 'bromo', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `no_telepon` int(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id_user`, `nama_lengkap`, `email`, `alamat`, `password`, `role`, `no_telepon`) VALUES
(1, 'Admin JawaTrip', 'admin@jawatrip.id', NULL, '12345', 'admin', 2147483647),
(2, 'Budi Santoso', 'budi@gmail.com', NULL, '12345', 'user', 2147483647),
(3, 'Siti Aminah', 'siti@yahoo.com', NULL, '12345', 'user', 2147483647),
(4, 'Andi Pratama', 'andi@outlook.com', NULL, '12345', 'user', 2147483647),
(6, 'sasa', 'sasa@gmail.com', NULL, '$2y$10$RyjnqfocF3TvdgRW7m5m4O4WgoIu3.k4VPG9dKZnmpJHIiDntKQYC', 'user', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_paket` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
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
-- Indeks untuk tabel `booking_log`
--
ALTER TABLE `booking_log`
  ADD PRIMARY KEY (`id_log`);

--
-- Indeks untuk tabel `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_user` (`id_user`);

--
-- Indeks untuk tabel `destinasi`
--
ALTER TABLE `destinasi`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `destinasi_populer`
--
ALTER TABLE `destinasi_populer`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `hotel`
--
ALTER TABLE `hotel`
  ADD PRIMARY KEY (`id_hotel`);

--
-- Indeks untuk tabel `kota`
--
ALTER TABLE `kota`
  ADD PRIMARY KEY (`id_kota`);

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
-- Indeks untuk tabel `paket_fasilitas`
--
ALTER TABLE `paket_fasilitas`
  ADD PRIMARY KEY (`id_fasilitas`),
  ADD KEY `id_paket` (`id_paket`);

--
-- Indeks untuk tabel `paket_itinerary`
--
ALTER TABLE `paket_itinerary`
  ADD PRIMARY KEY (`id_itinerary`),
  ADD KEY `id_paket` (`id_paket`);

--
-- Indeks untuk tabel `paket_reviews`
--
ALTER TABLE `paket_reviews`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_paket_review` (`id_paket`,`id_user`),
  ADD KEY `idx_paket_reviews_paket` (`id_paket`),
  ADD KEY `idx_paket_reviews_user` (`id_user`);

--
-- Indeks untuk tabel `paket_wisata`
--
ALTER TABLE `paket_wisata`
  ADD PRIMARY KEY (`id_paket`),
  ADD KEY `id_hotel` (`id_hotel`),
  ADD KEY `id_transport` (`id_transport`),
  ADD KEY `fk_kota_paket` (`id_kota`);

--
-- Indeks untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id_pembayaran`),
  ADD KEY `id_pemesanan` (`id_pemesanan`),
  ADD KEY `idx_kode_booking` (`kode_booking`),
  ADD KEY `idx_status` (`status_bayar`);

--
-- Indeks untuk tabel `pembayaran_bromo`
--
ALTER TABLE `pembayaran_bromo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bromo_id` (`bromo_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `pemesanan`
--
ALTER TABLE `pemesanan`
  ADD PRIMARY KEY (`id_pemesanan`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_paket` (`id_paket`);

--
-- Indeks untuk tabel `pemesanan_bromo`
--
ALTER TABLE `pemesanan_bromo`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `penumpang`
--
ALTER TABLE `penumpang`
  ADD PRIMARY KEY (`id_penumpang`);

--
-- Indeks untuk tabel `promo_diskon`
--
ALTER TABLE `promo_diskon`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_promo` (`kode_promo`);

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
-- Indeks untuk tabel `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_wishlist` (`id_user`,`id_paket`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_paket` (`id_paket`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `booking_log`
--
ALTER TABLE `booking_log`
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `destinasi`
--
ALTER TABLE `destinasi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `destinasi_populer`
--
ALTER TABLE `destinasi_populer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `hotel`
--
ALTER TABLE `hotel`
  MODIFY `id_hotel` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `kota`
--
ALTER TABLE `kota`
  MODIFY `id_kota` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `master_hotel`
--
ALTER TABLE `master_hotel`
  MODIFY `id_hotel` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `master_transport`
--
ALTER TABLE `master_transport`
  MODIFY `id_transport` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `paket_fasilitas`
--
ALTER TABLE `paket_fasilitas`
  MODIFY `id_fasilitas` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `paket_itinerary`
--
ALTER TABLE `paket_itinerary`
  MODIFY `id_itinerary` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `paket_reviews`
--
ALTER TABLE `paket_reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `paket_wisata`
--
ALTER TABLE `paket_wisata`
  MODIFY `id_paket` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `pembayaran_bromo`
--
ALTER TABLE `pembayaran_bromo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `pemesanan_bromo`
--
ALTER TABLE `pemesanan_bromo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT untuk tabel `penumpang`
--
ALTER TABLE `penumpang`
  MODIFY `id_penumpang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `promo_diskon`
--
ALTER TABLE `promo_diskon`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `paket_fasilitas`
--
ALTER TABLE `paket_fasilitas`
  ADD CONSTRAINT `paket_fasilitas_ibfk_1` FOREIGN KEY (`id_paket`) REFERENCES `paket_wisata` (`id_paket`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `paket_itinerary`
--
ALTER TABLE `paket_itinerary`
  ADD CONSTRAINT `paket_itinerary_ibfk_1` FOREIGN KEY (`id_paket`) REFERENCES `paket_wisata` (`id_paket`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `paket_wisata`
--
ALTER TABLE `paket_wisata`
  ADD CONSTRAINT `fk_kota_paket` FOREIGN KEY (`id_kota`) REFERENCES `kota` (`id_kota`) ON DELETE SET NULL,
  ADD CONSTRAINT `paket_wisata_ibfk_1` FOREIGN KEY (`id_hotel`) REFERENCES `master_hotel` (`id_hotel`) ON DELETE SET NULL,
  ADD CONSTRAINT `paket_wisata_ibfk_2` FOREIGN KEY (`id_transport`) REFERENCES `master_transport` (`id_transport`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`id_pemesanan`) REFERENCES `pemesanan` (`id_pemesanan`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pembayaran_bromo`
--
ALTER TABLE `pembayaran_bromo`
  ADD CONSTRAINT `pembayaran_bromo_ibfk_1` FOREIGN KEY (`bromo_id`) REFERENCES `pemesanan_bromo` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pembayaran_bromo_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id_user`) ON DELETE SET NULL;

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
