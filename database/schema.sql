-- SPA Booking System Schema (MySQL)
-- Import this script into your database (create a DB and set application/config/database.php accordingly)
-- Ensure your MySQL supports utf8mb4

SET NAMES utf8mb4;
SET time_zone = '+07:00';

-- Drop tables if exist (optional)
-- SET FOREIGN_KEY_CHECKS=0;
-- DROP TABLE IF EXISTS invoice;
-- DROP TABLE IF EXISTS booking;
-- DROP TABLE IF EXISTS package;
-- DROP TABLE IF EXISTS therapist;
-- SET FOREIGN_KEY_CHECKS=1;

CREATE TABLE IF NOT EXISTS therapist (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  phone VARCHAR(20) NULL,
  status ENUM('available','busy','off') NOT NULL DEFAULT 'available',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_therapist_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS package (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  category VARCHAR(100) NOT NULL,
  hands TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT 'number of therapists (1 or 2)',
  duration INT NOT NULL COMMENT 'minutes',
  price_in_call DECIMAL(10,2) NOT NULL,
  price_out_call DECIMAL(10,2) NOT NULL,
  currency VARCHAR(10) NOT NULL DEFAULT 'RM',
  description TEXT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS booking (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  customer_name VARCHAR(100) NOT NULL,
  address TEXT NULL,
  therapist_id INT UNSIGNED NULL,
  package_id INT UNSIGNED NOT NULL,
  call_type ENUM('IN','OUT') NOT NULL DEFAULT 'IN',
  date DATE NOT NULL,
  time TIME NOT NULL,
  total_price DECIMAL(10,2) NOT NULL,
  status ENUM('pending','accepted','working','rejected','confirmed','completed','canceled') NOT NULL DEFAULT 'pending',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_booking_date (date),
  INDEX idx_booking_status (status),
  INDEX idx_booking_therapist (therapist_id),
  INDEX idx_booking_package (package_id),
  CONSTRAINT fk_booking_therapist
    FOREIGN KEY (therapist_id) REFERENCES therapist(id)
    ON UPDATE CASCADE ON DELETE SET NULL,
  CONSTRAINT fk_booking_package
    FOREIGN KEY (package_id) REFERENCES package(id)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS invoice (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  booking_id INT UNSIGNED NOT NULL,
  invoice_number VARCHAR(50) NOT NULL UNIQUE,
  total DECIMAL(10,2) NOT NULL,
  payment_status ENUM('DP','Lunas') NOT NULL DEFAULT 'DP',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_invoice_booking (booking_id),
  CONSTRAINT fk_invoice_booking
    FOREIGN KEY (booking_id) REFERENCES booking(id)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Sample seed data (optional)
INSERT INTO therapist (name, phone, status) VALUES
('Therapist A', '081234567890', 'available'),
('Therapist B', '081298765432', 'available');

-- APITT Menu seed data
INSERT INTO package (name, category, hands, duration, price_in_call, price_out_call, currency, description) VALUES
('Solo Package A', 'Solo Oil Relaxing Massage', 1, 60, 89.00, 150.00, 'RM', 'Full Body Massage'),
('Solo Package B', 'Solo Oil Relaxing Massage', 1, 75, 139.00, 180.00, 'RM', 'Full Body Massage + Manhood'),
('Solo Package C', 'Solo Oil Relaxing Massage', 1, 80, 159.00, 200.00, 'RM', 'Full Body Massage + Body Manipulation Therapy'),
('Solo Package D', 'Solo Oil Relaxing Massage', 1, 100, 199.00, 250.00, 'RM', 'Full Body Massage + Manhood + Body Manipulation Therapy'),

('4 Hand Package A', '4 Hand Oil Relaxing Massage', 2, 60, 160.00, 230.00, 'RM', 'Full Body Massage'),
('4 Hand Package B', '4 Hand Oil Relaxing Massage', 2, 75, 190.00, 260.00, 'RM', 'Full Body Massage + Manhood'),
('4 Hand Package C', '4 Hand Oil Relaxing Massage', 2, 80, 210.00, 280.00, 'RM', 'Full Body Massage + Body Manipulation Therapy'),
('4 Hand Package D', '4 Hand Oil Relaxing Massage', 2, 100, 260.00, 340.00, 'RM', 'Full Body Massage + Manhood + Body Manipulation Therapy');

-- Example booking pending
INSERT INTO booking (customer_name, address, therapist_id, package_id, call_type, date, time, total_price, status)
VALUES ('Cindi Rahayu', 'Jl. Mawar No. 5, Bandung', 1, 1, 'IN', DATE(NOW()), '15:00:00', 89.00, 'pending');

-- Example invoice for confirmed booking (adjust status to confirmed in app flow)
-- UPDATE booking SET status='confirmed' WHERE id=1;
INSERT INTO invoice (booking_id, invoice_number, total, payment_status)
VALUES (1, CONCAT('INV-', DATE_FORMAT(NOW(), '%Y%m%d'), '-0001'), 350000.00, 'DP');

-- Sessions table for CodeIgniter 3 database session driver
-- Matches CI3's expected schema used by [CI_Session_database_driver](system/libraries/Session/drivers/Session_database_driver.php:50)
CREATE TABLE IF NOT EXISTS ci_sessions (
  id varchar(128) NOT NULL,
  ip_address varchar(45) NOT NULL,
  timestamp int(10) unsigned NOT NULL DEFAULT 0,
  data blob NOT NULL,
  PRIMARY KEY (id),
  KEY ci_sessions_timestamp (timestamp)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Application settings for storing configuration such as Telegram Bot
CREATE TABLE IF NOT EXISTS app_settings (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `key` VARCHAR(100) NOT NULL UNIQUE,
  `value` TEXT NULL,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;