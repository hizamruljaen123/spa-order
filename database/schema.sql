-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.11.0.7065
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping structure for table data_spa.add_on
CREATE TABLE IF NOT EXISTS `add_on` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `category` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `name` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `price` decimal(10,2) NOT NULL,
  `currency` varchar(10) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'RM',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table data_spa.admin_user
CREATE TABLE IF NOT EXISTS `admin_user` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `role` varchar(20) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'admin',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table data_spa.ads
CREATE TABLE IF NOT EXISTS `ads` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `image_url` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `link_url` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `display_order` int unsigned NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_ads_active` (`is_active`),
  KEY `idx_ads_order` (`display_order`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table data_spa.products
CREATE TABLE IF NOT EXISTS `products` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `price` decimal(10,2) NOT NULL,
  `currency` varchar(10) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'RM',
  `image_url` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `display_order` int unsigned NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_products_active` (`is_active`),
  KEY `idx_products_order` (`display_order`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Sample product data for testing
INSERT INTO products (name, description, price, currency, image_url, is_active, display_order) VALUES
('Essential Oil - Lavender', 'Premium lavender essential oil for relaxation and aromatherapy. Helps reduce stress and promote better sleep.', 45.00, 'RM', NULL, 1, 1),
('Massage Oil Blend', 'Special blend of natural oils for professional massage therapy. Contains jojoba, almond, and essential oils.', 35.00, 'RM', NULL, 1, 2),
('Aromatherapy Diffuser', 'Electric aromatherapy diffuser with LED lights. Creates a relaxing atmosphere with essential oils.', 89.00, 'RM', NULL, 1, 3),
('Spa Towels Set', 'High-quality spa towels set with 6 pieces. Soft, absorbent, and perfect for professional spa use.', 120.00, 'RM', NULL, 1, 4),
('Herbal Compress', 'Traditional Malay herbal compress filled with aromatic herbs. Perfect for hot stone therapy.', 25.00, 'RM', NULL, 1, 5),
('Body Scrub - Coffee', 'Natural coffee body scrub for exfoliation and skin rejuvenation. Made with fresh ground coffee and natural oils.', 55.00, 'RM', NULL, 1, 6),
('Face Mask Pack', 'Professional grade face masks for various skin types. Includes hydrating, cleansing, and anti-aging formulas.', 75.00, 'RM', NULL, 1, 7),
('Spa Candles Set', 'Scented spa candles in relaxing fragrances. Creates ambient lighting and aromatherapy atmosphere.', 65.00, 'RM', NULL, 1, 8);

-- Data exporting was unselected.

-- Dumping structure for table data_spa.app_settings
CREATE TABLE IF NOT EXISTS `app_settings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `value` text COLLATE utf8mb4_general_ci,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table data_spa.booking
CREATE TABLE IF NOT EXISTS `booking` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `customer_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `address` text COLLATE utf8mb4_general_ci,
  `therapist_id` int unsigned DEFAULT NULL,
  `package_id` int unsigned NOT NULL,
  `call_type` enum('IN','OUT') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'IN',
  `date` date NOT NULL,
  `time` time NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('pending','accepted','working','rejected','confirmed','completed','canceled') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'pending',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_booking_date` (`date`),
  KEY `idx_booking_status` (`status`),
  KEY `idx_booking_therapist` (`therapist_id`),
  KEY `idx_booking_package` (`package_id`),
  CONSTRAINT `fk_booking_package` FOREIGN KEY (`package_id`) REFERENCES `package` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_booking_therapist` FOREIGN KEY (`therapist_id`) REFERENCES `therapist` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table data_spa.booking_addon
CREATE TABLE IF NOT EXISTS `booking_addon` (
  `booking_id` int unsigned NOT NULL,
  `add_on_id` int unsigned NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `qty` int unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`booking_id`,`add_on_id`),
  KEY `fk_booking_addon_addon` (`add_on_id`),
  CONSTRAINT `fk_booking_addon_addon` FOREIGN KEY (`add_on_id`) REFERENCES `add_on` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_booking_addon_booking` FOREIGN KEY (`booking_id`) REFERENCES `booking` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table data_spa.ci_sessions
CREATE TABLE IF NOT EXISTS `ci_sessions` (
  `id` varchar(128) COLLATE utf8mb4_general_ci NOT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_general_ci NOT NULL,
  `timestamp` int unsigned NOT NULL DEFAULT '0',
  `data` blob NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ci_sessions_timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table data_spa.invoice
CREATE TABLE IF NOT EXISTS `invoice` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `booking_id` int unsigned NOT NULL,
  `invoice_number` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `payment_status` enum('DP','Lunas') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'DP',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `invoice_number` (`invoice_number`),
  KEY `idx_invoice_booking` (`booking_id`),
  CONSTRAINT `fk_invoice_booking` FOREIGN KEY (`booking_id`) REFERENCES `booking` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table data_spa.package
CREATE TABLE IF NOT EXISTS `package` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `category` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `hands` tinyint unsigned NOT NULL DEFAULT '1' COMMENT 'number of therapists (1 or 2)',
  `duration` int NOT NULL COMMENT 'minutes',
  `price_in_call` decimal(10,2) NOT NULL,
  `price_out_call` decimal(10,2) NOT NULL,
  `currency` varchar(10) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'RM',
  `description` text COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table data_spa.therapist
CREATE TABLE IF NOT EXISTS `therapist` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('available','busy','off') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'available',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `photo` text COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`id`),
  KEY `idx_therapist_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
