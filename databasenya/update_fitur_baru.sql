-- =====================================================
-- SQL UNTUK FITUR-FITUR BARU JAWATRIP
-- Jalankan query ini di phpMyAdmin
-- =====================================================

-- Ganti: gunakan tabel baru khusus paket: `paket_reviews`
CREATE TABLE IF NOT EXISTS `paket_reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_paket` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `rating` tinyint(1) NOT NULL,
  `review_text` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_paket_review` (`id_paket`, `id_user`),
  KEY `idx_paket_reviews_paket` (`id_paket`),
  KEY `idx_paket_reviews_user` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 2. TABEL WISHLIST (Sistem Favorit)
CREATE TABLE IF NOT EXISTS `wishlist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `id_paket` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_wishlist` (`id_user`, `id_paket`),
  KEY `id_user` (`id_user`),
  KEY `id_paket` (`id_paket`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 3. TABEL PROMO_DISKON (Manajemen Promo)
CREATE TABLE IF NOT EXISTS `promo_diskon` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kode_promo` varchar(50) NOT NULL,
  `nama_promo` varchar(200) NOT NULL,
  `jenis_diskon` enum('percentage','fixed') NOT NULL DEFAULT 'percentage',
  `nilai_diskon` decimal(15,2) NOT NULL,
  `min_transaksi` decimal(15,2) DEFAULT 0,
  `max_diskon` decimal(15,2) DEFAULT 0,
  `kuota` int(11) NOT NULL DEFAULT 0,
  `terpakai` int(11) DEFAULT 0,
  `tanggal_mulai` datetime NOT NULL,
  `tanggal_selesai` datetime NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode_promo` (`kode_promo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 4. TABEL CHAT_MESSAGES (Opsional - untuk save chat history)
CREATE TABLE IF NOT EXISTS `chat_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `message` text NOT NULL,
  `sender` enum('user','admin') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `id_user` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 5. INSERT SAMPLE DATA

-- Sample Promo
INSERT INTO `promo_diskon` (`kode_promo`, `nama_promo`, `jenis_diskon`, `nilai_diskon`, `min_transaksi`, `max_diskon`, `kuota`, `tanggal_mulai`, `tanggal_selesai`, `status`) VALUES
('TAHUNBARU2026', 'Diskon Tahun Baru 2026', 'percentage', 20.00, 500000.00, 200000.00, 100, '2025-12-20 00:00:00', '2026-01-10 23:59:59', 'active'),
('HEMAT50K', 'Potongan Langsung 50K', 'fixed', 50000.00, 300000.00, 0.00, 50, '2025-12-16 00:00:00', '2025-12-31 23:59:59', 'active'),
('LIBURAN15', 'Diskon Liburan 15%', 'percentage', 15.00, 750000.00, 150000.00, 75, '2025-12-16 00:00:00', '2026-01-31 23:59:59', 'active');

-- =====================================================
-- SELESAI! Refresh phpMyAdmin untuk melihat tabel baru
-- =====================================================
