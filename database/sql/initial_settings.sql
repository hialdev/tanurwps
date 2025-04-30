-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping data for table tanur_wps.settings: ~15 rows (approximately)
REPLACE INTO `settings` (`id`, `group`, `group_key`, `name`, `key`, `description`, `input_type`, `value`, `is_urgent`, `created_at`, `updated_at`) VALUES
	('07180d50-193d-42b7-87a2-b4f5c23bd6f8', 'Site', 'site', 'Mobile Menu Logo', 'mobile-logo', 'Logo untuk mobile menu', 'image', 'settings/P2cWSztZq5qwmnJu3jWBC1PLJAbyLQ2UaDAaURoV.webp', 1, '2025-04-29 17:21:59', '2025-04-29 17:22:13'),
	('0dce868d-08f8-4c65-8904-3636ae2af53d', 'Site', 'site', 'Logo', 'logo', 'Logo untuk website', 'image', 'settings/6TxMsmR4PS8vO8RVt4CU8XXD0WvyApN2LPYXESny.webp', 1, '2024-12-25 21:05:21', '2025-04-28 16:45:53'),
	('1626205a-3961-4cb4-a1aa-ad3ab5720eeb', 'Site', 'site', 'Favicon', 'favicon', 'Upload gambar dengan rasio 1:1', 'image', 'settings/tvDWkKcLua02oGVGnYhBUCpJJBzmeqHmwVFsnSKM.webp', 1, '2024-12-25 21:05:54', '2025-04-28 16:45:44'),
	('239f1c78-7d44-42c2-8f10-f678396c5e9d', 'Theme', 'theme', 'Button Border Color', 'btn-border', 'Nilai Default \'#926e38\'', 'text', '#027673', 1, '2024-12-26 05:17:30', '2025-01-17 13:11:57'),
	('3313cff0-3ae7-429a-88b7-2ade63680ddd', 'Company', 'company', 'Company City', 'city', 'Alamat Kota Perusahaan untuk keperluan sistem lebih lanjut', 'text', 'Jakarta Selatan', 1, '2025-01-23 17:15:17', '2025-01-23 17:15:26'),
	('40aa2835-2e30-451f-bcb8-6fc4f5a29559', 'Theme', 'theme', 'Background Subtle', 'bg-subtle', 'Nilai default \'#926e380c\'', 'text', '#f8f4ee', 1, '2024-12-26 05:16:10', '2025-01-17 13:12:50'),
	('6d6de589-6acd-437b-b86a-7f8074ae667d', 'Theme', 'theme', 'Button Hover Background', 'btn-hover-bg', 'Nilai default \'#a47d42\'', 'text', '#027673', 1, '2024-12-26 05:18:02', '2025-01-17 17:39:50'),
	('7fcfe7da-a8cf-42aa-af87-d0c3114443ad', 'Company', 'company', 'Company Phone', 'phone', 'Telp yang mewakili Perusahaan untuk keperluan sistem lebih lanjut', 'number', '089671052052', 1, '2025-01-23 17:04:50', '2025-01-23 17:11:06'),
	('8e5d7803-d425-4e13-a645-dea51a9362f9', 'Theme', 'theme', 'Button Background', 'btn-bg', 'Nilai Default #926e38', 'text', '#027673', 1, '2024-12-26 05:16:58', '2025-01-19 12:43:37'),
	('930d264f-66b0-4b7e-b0fa-8cecac392c7d', 'Theme', 'theme', 'Primary RGBA', 'primary-rgba', 'Nilai default \'163, 118, 78\'', 'text', '2, 118, 115', 1, '2024-12-26 05:15:33', '2025-01-17 17:38:19'),
	('a4ac5a3e-a71b-4f3a-8364-6bf5e1a46872', 'Company', 'company', 'Company Legal Name', 'legal-name', 'Nama Perusahaan untuk keperluan sistem lebih lanjut', 'text', 'PT Rizq Sahara Multindo', 1, '2025-01-23 17:10:32', '2025-01-23 17:10:47'),
	('a888e029-7e36-497a-b27b-27085de77d0f', 'Company', 'company', 'Company Address', 'address', 'Alamat Company untuk keperluan sistem lebih lanjut', 'textarea', 'Rukan Tanjung Mas Raya Blok B1/42 Lt.3, Tanjung Barat, Jagakarsa, Jakarta Selatan, Indonesia. 12530', 1, '2025-01-23 17:04:07', '2025-01-23 17:13:21'),
	('cbe5cd7b-c416-4818-aa54-907d75415104', 'Theme', 'theme', 'Button Hover Border', 'btn-hover-border', 'Nilai default \'#a47d42\'', 'text', '#027673', 1, '2024-12-26 05:18:28', '2025-01-17 13:20:57'),
	('ed186bd9-6cad-471e-97c6-6c8660c51375', 'Theme', 'theme', 'Primary Color', 'primary-color', 'Nilai default #926e38', 'text', '#027673', 1, '2024-12-26 05:15:02', '2025-01-19 12:43:46'),
	('f15ac8c8-1070-46ca-afd3-695cc23eb557', 'Company', 'company', 'Company Postal Code', 'postal-code', 'Kode Pos Perusahaan untuk keperluan sistem lebih lanjut (surat menyurat dan lainnya)', 'text', '12530', 1, '2025-01-23 17:16:05', '2025-01-23 17:16:21');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
