-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.4.3 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for qlkhodh
CREATE DATABASE IF NOT EXISTS `qlkhodh` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;
USE `qlkhodh`;


-- Dumping structure for table qlkhodh.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('warehouse_staff','qc_staff','manager','admin') NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table qlkhodh.users: ~5 rows (approximately)
INSERT INTO `users` (`id`, `username`, `password_hash`, `role`, `is_active`, `created_at`) VALUES
	(1, 'admin', '$2a$12$Yqv1/qpIdwE9PPGH6AxWbuk7OmV49Gq7KhRgAPR2X/EeaOAtjUXB.', 'admin', 1, '2026-03-19 16:53:08'),
	(2, 'kho', '$2a$12$Yqv1/qpIdwE9PPGH6AxWbuk7OmV49Gq7KhRgAPR2X/EeaOAtjUXB.', 'warehouse_staff', 1, '2026-03-22 04:15:29'),
	(3, 'qc', '$2a$12$Yqv1/qpIdwE9PPGH6AxWbuk7OmV49Gq7KhRgAPR2X/EeaOAtjUXB.', 'qc_staff', 1, '2026-03-22 04:15:29'),
	(4, 'quanly', '$2y$12$RuqHguij3.lw7OQNHld5aehmFUpkjNz7Vq6mvLJyx0wTKHxALWZJW', 'manager', 1, '2026-03-22 04:15:29'),
	(5, 'kho2', '$2y$12$vWcsuJ70NSyAou8kGrGyguMteqjn5iN6wRstAg2fYG6WYh8JkVDGa', 'warehouse_staff', 1, '2026-04-09 00:20:27');


-- Dumping structure for table qlkhodh.suppliers
CREATE TABLE IF NOT EXISTS `suppliers` (
  `supplier_id` int unsigned NOT NULL AUTO_INCREMENT,
  `supplier_code` varchar(20) NOT NULL,
  `supplier_name` varchar(100) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`supplier_id`),
  UNIQUE KEY `supplier_code` (`supplier_code`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table qlkhodh.suppliers: ~2 rows (approximately)
INSERT INTO `suppliers` (`supplier_id`, `supplier_code`, `supplier_name`, `is_active`, `created_at`, `deleted_at`) VALUES
	(1, 'VSD', 'Công ty VSD', 1, '2026-03-21 02:39:14', NULL),
	(2, 'HDB', 'Công ty HDB', 1, '2026-03-21 02:39:14', NULL);


-- Dumping structure for table qlkhodh.defect_types
CREATE TABLE IF NOT EXISTS `defect_types` (
  `defect_type_id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `description` text,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`defect_type_id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table qlkhodh.defect_types: ~3 rows (approximately)
INSERT INTO `defect_types` (`defect_type_id`, `name`, `description`, `is_active`, `created_at`, `deleted_at`) VALUES
	(1, 'Lỗi xước mặt đồng hồ', 'Xước, trầy mặt đồng hồ', 1, '2026-03-21 02:39:14', NULL),
	(2, 'Lỗi quai đeo bị đứt', 'Đứt lìa', 1, '2026-03-21 02:39:14', NULL),
	(3, 'Lỗi quai đeo có vết hằn', 'Vết hằn/lỗi trên bề mặt', 1, '2026-03-21 02:39:14', NULL);


-- Dumping structure for table qlkhodh.product_types
CREATE TABLE IF NOT EXISTS `product_types` (
  `product_type_id` int unsigned NOT NULL AUTO_INCREMENT,
  `product_code` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`product_type_id`) USING BTREE,
  UNIQUE KEY `product_code` (`product_code`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- Dumping data for table qlkhodh.product_types: ~2 rows (approximately)
INSERT INTO `product_types` (`product_type_id`, `product_code`, `product_name`, `is_active`, `created_at`, `deleted_at`) VALUES
	(1, 'watch_strap', 'Quai đeo đồng hồ', 1, '2026-03-21 02:51:17', NULL),
	(2, 'watch_face', 'Mặt đồng hồ', 1, '2026-03-21 02:51:17', NULL);


-- Dumping structure for table qlkhodh.batches
CREATE TABLE IF NOT EXISTS `batches` (
  `batch_id` int unsigned NOT NULL AUTO_INCREMENT,
  `batch_code` varchar(50) NOT NULL,
  `supplier_code` enum('VSD','HDB') NOT NULL,
  `product_type` enum('watch_strap','watch_face') NOT NULL,
  `import_date` date NOT NULL,
  `status` enum('new','pending_qc','in_progress','completed','rejected') NOT NULL DEFAULT 'new',
  `created_by` int unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`batch_id`),
  UNIQUE KEY `batch_code` (`batch_code`),
  KEY `fk_batches_created_by` (`created_by`),
  KEY `idx_batches_import_date` (`import_date`),
  KEY `idx_batches_status` (`status`),
  KEY `idx_batches_supplier_code` (`supplier_code`),
  CONSTRAINT `fk_batches_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table qlkhodh.batches: ~11 rows (approximately)
INSERT INTO `batches` (`batch_id`, `batch_code`, `supplier_code`, `product_type`, `import_date`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
	(1, 'Q1-20260322-HDB', 'HDB', 'watch_face', '2026-03-22', 'rejected', NULL, '2026-03-22 04:19:17', '2026-03-22 05:38:12'),
	(2, 'Q2-20260322-HDB', 'HDB', 'watch_strap', '2026-03-22', 'completed', NULL, '2026-03-22 05:02:06', '2026-03-22 05:37:00'),
	(3, 'Q3-20260322-VSD', 'VSD', 'watch_strap', '2026-03-22', 'rejected', NULL, '2026-03-22 05:38:36', '2026-04-09 05:57:04'),
	(4, 'Q4-20260322-VSD', 'VSD', 'watch_face', '2026-03-22', 'in_progress', NULL, '2026-03-22 05:38:58', '2026-04-05 19:08:24'),
	(5, 'Q5-20260322-VSD', 'VSD', 'watch_face', '2026-03-22', 'in_progress', NULL, '2026-03-22 05:39:33', '2026-04-08 08:41:09'),
	(6, 'Q1-20260324-VSD', 'VSD', 'watch_strap', '2026-03-24', 'pending_qc', NULL, '2026-03-24 20:55:20', '2026-04-08 08:34:42'),
	(7, 'Q2-20260324-VSD', 'VSD', 'watch_strap', '2026-03-24', 'pending_qc', NULL, '2026-03-24 21:23:50', '2026-04-08 08:34:40'),
	(8, 'Q1-20260408-HDB', 'HDB', 'watch_strap', '2026-04-08', 'pending_qc', NULL, '2026-04-08 07:51:30', '2026-04-08 08:34:06'),
	(9, 'Q2-20260408-HDB', 'HDB', 'watch_face', '2026-04-08', 'new', NULL, '2026-04-08 09:24:18', NULL),
	(10, 'Q3-20260408-HDB', 'HDB', 'watch_face', '2026-04-08', 'pending_qc', NULL, '2026-04-08 09:25:23', '2026-04-08 09:26:15'),
	(11, 'Q4-20260408-VSD', 'VSD', 'watch_face', '2026-04-08', 'pending_qc', NULL, '2026-04-08 09:25:50', '2026-04-08 09:26:13');


-- Dumping structure for table qlkhodh.boxes
CREATE TABLE IF NOT EXISTS `boxes` (
  `box_id` int unsigned NOT NULL AUTO_INCREMENT,
  `batch_code` varchar(50) NOT NULL,
  `box_code` varchar(100) NOT NULL,
  `tray_count` int unsigned DEFAULT NULL,
  `unit_per_tray` int unsigned DEFAULT NULL,
  `total_units` int unsigned NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`box_id`),
  UNIQUE KEY `box_code` (`box_code`),
  KEY `idx_boxes_batch_code` (`batch_code`),
  CONSTRAINT `fk_boxes_batch_code` FOREIGN KEY (`batch_code`) REFERENCES `batches` (`batch_code`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table qlkhodh.boxes: ~15 rows (approximately)
INSERT INTO `boxes` (`box_id`, `batch_code`, `box_code`, `tray_count`, `unit_per_tray`, `total_units`, `created_at`, `updated_at`) VALUES
	(1, 'Q1-20260322-HDB', 'HDB1', 20, NULL, 400, '2026-03-22 05:00:50', NULL),
	(2, 'Q1-20260322-HDB', 'HDB2', NULL, NULL, 500, '2026-03-22 05:00:50', NULL),
	(3, 'Q1-20260322-HDB', 'HDB3', 25, 10, 250, '2026-03-22 05:00:50', NULL),
	(4, 'Q2-20260322-HDB', 'HDB4', 20, NULL, 400, '2026-03-22 05:28:48', NULL),
	(5, 'Q2-20260322-HDB', 'HDB5', 20, 20, 400, '2026-03-22 05:28:48', NULL),
	(6, 'Q3-20260322-VSD', 'VSD1', NULL, NULL, 400, '2026-03-22 05:38:45', NULL),
	(7, 'Q4-20260322-VSD', 'VSD2', NULL, NULL, 200, '2026-03-22 05:39:08', NULL),
	(8, 'Q5-20260322-VSD', 'VSD3', NULL, NULL, 600, '2026-03-22 05:39:40', NULL),
	(9, 'Q1-20260408-HDB', 'DST1', NULL, NULL, 300, '2026-04-08 07:53:26', '2026-04-08 08:33:45'),
	(10, 'Q1-20260408-HDB', 'DST2', NULL, NULL, 200, '2026-04-08 07:53:26', NULL),
	(11, 'Q2-20260324-VSD', 'DST3', NULL, NULL, 350, '2026-04-08 08:34:26', NULL),
	(12, 'Q1-20260324-VSD', 'DST4', NULL, NULL, 400, '2026-04-08 08:34:38', NULL),
	(13, 'Q2-20260408-HDB', 'DST5', NULL, NULL, 500, '2026-04-08 09:24:26', NULL),
	(14, 'Q3-20260408-HDB', 'DST6', NULL, NULL, 300, '2026-04-08 09:25:35', NULL),
	(15, 'Q4-20260408-VSD', 'DST7', NULL, NULL, 600, '2026-04-08 09:26:01', '2026-04-09 04:04:35');


-- Dumping structure for table qlkhodh.defect_records
CREATE TABLE IF NOT EXISTS `defect_records` (
  `defect_id` int NOT NULL AUTO_INCREMENT,
  `batch_code` varchar(50) NOT NULL,
  `defect_type_id` int unsigned NOT NULL,
  `qty_units` int NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`defect_id`),
  KEY `fk_defect_records_batch_code` (`batch_code`),
  KEY `fk_defect_records_defect_type` (`defect_type_id`),
  CONSTRAINT `fk_defect_records_batch_code` FOREIGN KEY (`batch_code`) REFERENCES `batches` (`batch_code`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_defect_records_defect_type` FOREIGN KEY (`defect_type_id`) REFERENCES `defect_types` (`defect_type_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `chk_defect_records_qty_units` CHECK ((`qty_units` > 0))
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table qlkhodh.defect_records: ~4 rows (approximately)
INSERT INTO `defect_records` (`defect_id`, `batch_code`, `defect_type_id`, `qty_units`, `created_at`) VALUES
	(1, 'Q1-20260322-HDB', 2, 500, '2026-03-22 05:38:12'),
	(11, 'Q3-20260322-VSD', 2, 100, '2026-04-09 05:57:04'),
	(12, 'Q3-20260322-VSD', 3, 50, '2026-04-09 05:57:04');


-- Dumping structure for table qlkhodh.qc_results
CREATE TABLE IF NOT EXISTS `qc_results` (
  `result_id` int unsigned NOT NULL AUTO_INCREMENT,
  `batch_code` varchar(50) NOT NULL DEFAULT '',
  `ok_units` int unsigned NOT NULL DEFAULT '0',
  `ng_units` int unsigned NOT NULL DEFAULT '0',
  `inspected_by` int unsigned DEFAULT NULL,
  `inspected_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`result_id`),
  KEY `idx_qc_results_inspected_by` (`inspected_by`),
  KEY `idx_qc_results_inspected_at` (`inspected_at`),
  KEY `idx_qc_results_batch_id` (`batch_code`) USING BTREE,
  CONSTRAINT `fk_qc_results_batch` FOREIGN KEY (`batch_code`) REFERENCES `batches` (`batch_code`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_qc_results_user` FOREIGN KEY (`inspected_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table qlkhodh.qc_results: ~3 rows (approximately)
INSERT INTO `qc_results` (`result_id`, `batch_code`, `ok_units`, `ng_units`, `inspected_by`, `inspected_at`) VALUES
	(1, 'Q2-20260322-HDB', 800, 0, 1, '2026-03-22 05:37:00'),
	(2, 'Q1-20260322-HDB', 650, 500, 1, '2026-03-22 05:38:12'),
	(3, 'Q3-20260322-VSD', 250, 150, 1, '2026-04-08 09:17:00');


/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;