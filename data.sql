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

-- Dumping data for table data_spa.booking: ~1 rows (approximately)
INSERT INTO `booking` (`id`, `customer_name`, `address`, `therapist_id`, `package_id`, `call_type`, `date`, `time`, `total_price`, `status`, `created_at`) VALUES
	(1, 'Cindi Rahayu', 'Jl. Mawar No. 5, Bandung', 1, 1, 'IN', '2025-11-08', '15:00:00', 89.00, 'pending', '2025-11-08 04:11:23');

-- Dumping data for table data_spa.invoice: ~1 rows (approximately)
INSERT INTO `invoice` (`id`, `booking_id`, `invoice_number`, `total`, `payment_status`, `created_at`) VALUES
	(1, 1, 'INV-20251108-0001', 350000.00, 'DP', '2025-11-08 04:11:23');

-- Dumping data for table data_spa.package: ~8 rows (approximately)
INSERT INTO `package` (`id`, `name`, `category`, `hands`, `duration`, `price_in_call`, `price_out_call`, `currency`, `description`) VALUES
	(1, 'Solo Package A', 'Solo Oil Relaxing Massage', 1, 60, 89.00, 150.00, 'RM', 'Full Body Massage'),
	(2, 'Solo Package B', 'Solo Oil Relaxing Massage', 1, 75, 139.00, 180.00, 'RM', 'Full Body Massage + Manhood'),
	(3, 'Solo Package C', 'Solo Oil Relaxing Massage', 1, 80, 159.00, 200.00, 'RM', 'Full Body Massage + Body Manipulation Therapy'),
	(4, 'Solo Package D', 'Solo Oil Relaxing Massage', 1, 100, 199.00, 250.00, 'RM', 'Full Body Massage + Manhood + Body Manipulation Therapy'),
	(5, '4 Hand Package A', '4 Hand Oil Relaxing Massage', 2, 60, 160.00, 230.00, 'RM', 'Full Body Massage'),
	(6, '4 Hand Package B', '4 Hand Oil Relaxing Massage', 2, 75, 190.00, 260.00, 'RM', 'Full Body Massage + Manhood'),
	(7, '4 Hand Package C', '4 Hand Oil Relaxing Massage', 2, 80, 210.00, 280.00, 'RM', 'Full Body Massage + Body Manipulation Therapy'),
	(8, '4 Hand Package D', '4 Hand Oil Relaxing Massage', 2, 100, 260.00, 340.00, 'RM', 'Full Body Massage + Manhood + Body Manipulation Therapy');

-- Dumping data for table data_spa.therapist: ~2 rows (approximately)
INSERT INTO `therapist` (`id`, `name`, `phone`, `status`, `created_at`) VALUES
	(1, 'Therapist A', '081234567890', 'available', '2025-11-08 04:11:23'),
	(2, 'Therapist B', '081298765432', 'available', '2025-11-08 04:11:23');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
