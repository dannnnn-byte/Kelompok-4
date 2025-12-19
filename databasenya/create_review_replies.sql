-- Tabel untuk menyimpan reply admin terhadap review user
CREATE TABLE IF NOT EXISTS `review_replies` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `review_id` INT(11) NOT NULL,
  `admin_id` INT(11) NOT NULL,
  `reply_text` TEXT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_review` (`review_id`),
  CONSTRAINT `fk_reply_review` FOREIGN KEY (`review_id`) REFERENCES `paket_reviews` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
