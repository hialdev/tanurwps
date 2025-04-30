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

-- Dumping data for table teuga_sso.settings: ~30 rows (approximately)
REPLACE INTO `settings` (`id`, `group`, `group_key`, `name`, `key`, `description`, `input_type`, `value`, `is_urgent`, `created_at`, `updated_at`) VALUES
	('0dce868d-08f8-4c65-8904-3636ae2af53d', 'Site', 'site', 'Logo', 'logo', 'Logo untuk website', 'image', 'settings/9N6DchS86PIbkPnYtb44K7tKSH6aH1qR0PTs5Izl.png', 1, '2024-12-25 21:05:21', '2025-01-17 13:05:37'),
	('12a3f22f-750c-4e06-8c2e-6cccfb50dcb6', 'Letter', 'letter', 'Background Surat', 'background', 'Kop Surat A4', 'image', 'settings/V3Kg9S8VFkWP5NHTDNut6ijLcKWZ2gEdpfUDd1MH.png', 1, '2025-02-03 04:42:34', '2025-02-03 04:43:24'),
	('1626205a-3961-4cb4-a1aa-ad3ab5720eeb', 'Site', 'site', 'Favicon', 'favicon', 'Upload gambar dengan rasio 1:1', 'image', 'settings/Q8p8PUFdga0weU2JIYqkgDLnLy4us7Z9zKZpDScb.png', 1, '2024-12-25 21:05:54', '2025-01-17 13:04:37'),
	('177999f1-382b-44ee-9205-2bed9b2bf24f', 'Letter', 'letter', 'Purchasing Name', 'purchasing-name', 'Nama penanggung jawab Purchase order / Pembelian ke Principal', 'text', 'Setiadi', 1, '2025-02-03 12:00:54', '2025-02-03 12:01:32'),
	('1c63c36e-64fb-408d-86af-5e4f5d50a1d3', 'Letter', 'letter', 'Director', 'director', 'Nama Direksi yang Digunakan Untuk Surat dan lainnya', 'text', 'Teuku Ria Fahriza', 0, '2025-02-03 04:50:34', '2025-02-03 15:07:28'),
	('22f79ff7-fa6d-4b8d-b34c-fa24bc8fbee9', 'Company', 'company', 'Company Contact Person Email', 'cp-email', 'Company Contact Person Email', 'text', 'teuku.zaldy@gmail.com', 1, '2025-02-03 09:44:57', '2025-02-03 09:45:32'),
	('238e5d40-f530-46a2-968f-dc2eeaafad54', 'Letter', 'letter', 'Administrasi Name', 'administrasi-name', 'Siapa Nama Administrasi yang akan di letakkan pada PIC Surat', 'text', 'Setiadi', 1, '2025-02-04 03:38:48', '2025-02-04 03:39:09'),
	('239f1c78-7d44-42c2-8f10-f678396c5e9d', 'Theme', 'theme', 'Button Border Color', 'btn-border', 'Nilai Default \'#926e38\'', 'text', '#412F73', 1, '2024-12-26 05:17:30', '2025-01-17 13:11:57'),
	('27a91b2a-64fa-4cd4-83b1-465e33e3b812', 'Letter', 'letter', 'Bank Transfer - Rekening', 'rekening', 'Bank Transfer - Rekening | Nomor Rekening yang digunakan untuk pembayaran dan akan disertakan pada surat Invoice', 'number', '1270007938085', 1, '2025-02-03 13:51:22', '2025-02-03 13:54:49'),
	('3313cff0-3ae7-429a-88b7-2ade63680ddd', 'Company', 'company', 'Company City', 'city', 'Alamat Kota Perusahaan untuk keperluan sistem lebih lanjut', 'text', 'Jakarta Selatan', 1, '2025-01-23 17:15:17', '2025-01-23 17:15:26'),
	('40aa2835-2e30-451f-bcb8-6fc4f5a29559', 'Theme', 'theme', 'Background Subtle', 'bg-subtle', 'Nilai default \'#926e380c\'', 'text', '#412F730c', 1, '2024-12-26 05:16:10', '2025-01-17 13:12:50'),
	('4db73672-ad72-4512-83ff-46f02ec607cf', 'Company', 'company', 'Company Contact Person Phone', 'cp-phone', 'Contact Person yang mewakili perusahaan untuk keperluan sistem lebih lanjut', 'text', '089671052050', 1, '2025-01-23 17:06:41', '2025-01-23 17:08:48'),
	('4f1af75f-3cd1-44ea-b7f5-e6d6def166cf', 'Company', 'company', 'Company Contact Person Name', 'cp-name', 'Contact Person yang mewakili perusahaan untuk keperluan sistem lebih lanjut', 'text', 'Teuku Zaldi', 1, '2025-01-23 17:06:03', '2025-01-23 17:14:09'),
	('57613a79-d7c5-4b09-a579-ddd7288599db', 'Letter', 'letter', 'Bank Transfer - Rekening Name', 'rekening-name', 'Bank Transfer - Rekening Name | Nama Rekening yang digunakan pada invoice', 'text', 'PT Rizq Sahara Multindo', 1, '2025-02-03 13:52:31', '2025-02-03 13:53:35'),
	('6d6de589-6acd-437b-b86a-7f8074ae667d', 'Theme', 'theme', 'Button Hover Background', 'btn-hover-bg', 'Nilai default \'#a47d42\'', 'text', 'linear-gradient(101deg, #59A2D0 0%, #412F73 103.07%)', 1, '2024-12-26 05:18:02', '2025-01-17 17:39:50'),
	('7fcfe7da-a8cf-42aa-af87-d0c3114443ad', 'Company', 'company', 'Company Phone', 'phone', 'Telp yang mewakili Perusahaan untuk keperluan sistem lebih lanjut', 'number', '089671052052', 1, '2025-01-23 17:04:50', '2025-01-23 17:11:06'),
	('8e5d7803-d425-4e13-a645-dea51a9362f9', 'Theme', 'theme', 'Button Background', 'btn-bg', 'Nilai Default #926e38', 'text', 'linear-gradient(77deg, #59A2D0 -8.9%, #44528D 25.39%, #E6443A 63.02%, #EC6C35 79.98%, #F18D2D 98.28%)', 1, '2024-12-26 05:16:58', '2025-01-19 12:43:37'),
	('9212cb81-6357-4382-ad09-2f9c6b0b9e2b', 'Site', 'site', 'Dashboard - Paragraf', 'dashboard-paragraf', 'Paragraf pada header dashboard', 'text', 'Lihat etalase kita untuk mengetahui product yang ready stock, atau buat pesanan khusus (Custom Order) sesuai kemauan kamu!', 1, '2024-12-25 21:07:17', '2024-12-25 21:09:24'),
	('930d264f-66b0-4b7e-b0fa-8cecac392c7d', 'Theme', 'theme', 'Primary RGBA', 'primary-rgba', 'Nilai default \'163, 118, 78\'', 'text', '68, 82, 141', 1, '2024-12-26 05:15:33', '2025-01-17 17:38:19'),
	('97de4ca9-788f-445e-8c92-3bcf62374fef', 'Site', 'site', 'Dashboard - Button Link', 'dashboard-button-link', 'Link untuk tombol pada header dashboard', 'text', '/custom-order/offer', 1, '2024-12-25 21:08:00', '2024-12-25 21:09:30'),
	('a4ac5a3e-a71b-4f3a-8364-6bf5e1a46872', 'Company', 'company', 'Company Legal Name', 'legal-name', 'Nama Perusahaan untuk keperluan sistem lebih lanjut', 'text', 'PT Rizq Sahara Multindo', 1, '2025-01-23 17:10:32', '2025-01-23 17:10:47'),
	('a888e029-7e36-497a-b27b-27085de77d0f', 'Company', 'company', 'Company Address', 'address', 'Alamat Company untuk keperluan sistem lebih lanjut', 'textarea', 'Rukan Tanjung Mas Raya Blok B1/42 Lt.3, Tanjung Barat, Jagakarsa, Jakarta Selatan, Indonesia. 12530', 1, '2025-01-23 17:04:07', '2025-01-23 17:13:21'),
	('b2786502-1224-46fd-9062-2de265aa07aa', 'Site', 'site', 'Dashboard - Button Text', 'dashboard-button-text', 'Text untuk tombol pada header dashboard', 'text', 'Custom Order', 1, '2024-12-25 21:07:36', '2024-12-25 21:09:36'),
	('bab1bbf0-31d3-4ceb-b8aa-3c84bd8f2251', 'Letter', 'letter', 'Kop Header', 'kop-header', 'Kop bagian atas pada surat', 'image', 'settings/kALbGmjep5xhEVWUAl51a0etXxd71zjs9wlvYn62.png', 1, '2025-02-28 02:37:14', '2025-02-28 02:38:34'),
	('cbe5cd7b-c416-4818-aa54-907d75415104', 'Theme', 'theme', 'Button Hover Border', 'btn-hover-border', 'Nilai default \'#a47d42\'', 'text', '#EC6C35', 1, '2024-12-26 05:18:28', '2025-01-17 13:20:57'),
	('cf966a94-8ba8-47b6-89d7-f8eac3636e54', 'Site', 'site', 'Dashboard - Greeting', 'dashboard-greeting', 'Title ucapan pada header dashboard', 'text', 'Masukan nilai PPN, contoh 11 untuk PPN 11%', 1, '2024-12-25 21:06:51', '2024-12-25 21:09:55'),
	('db00b99e-884b-4c25-860d-eda0cc1d5cfe', 'Letter', 'letter', 'Kop Footer', 'kop-footer', 'Kop bagian bawah surat', 'image', 'settings/f7rX5J0gXzSxgXDULOa3cL0MdxrGOKwAG8NY5gWR.png', 1, '2025-02-28 02:37:52', '2025-02-28 02:38:54'),
	('e2786502-1344-46fd-7062-2de265ba15aa', 'Site', 'site', 'PPN %', 'ppn', 'Nilai PPN, contoh 11 untuk PPN 11%', 'number', '11', 1, '2024-12-25 21:07:36', '2024-12-25 21:09:36'),
	('eae2fc2d-1231-4390-b458-80a15be81d83', 'Site', 'site', 'Product Limit', 'product-limit', 'Tentukan berapa item yang ditampilkan per halaman saat menampilkan data produk di Permintaan Client atau Pembelian ke Principal', 'number', '6', 1, '2025-02-01 07:57:09', '2025-02-05 04:53:07'),
	('ed186bd9-6cad-471e-97c6-6c8660c51375', 'Theme', 'theme', 'Primary Color', 'primary-color', 'Nilai default #926e38', 'text', 'linear-gradient(77deg, #59A2D0 -8.9%, #44528D 25.39%, #E6443A 63.02%, #EC6C35 79.98%, #F18D2D 98.28%)', 1, '2024-12-26 05:15:02', '2025-01-19 12:43:46'),
	('f15ac8c8-1070-46ca-afd3-695cc23eb557', 'Company', 'company', 'Company Postal Code', 'postal-code', 'Kode Pos Perusahaan untuk keperluan sistem lebih lanjut (surat menyurat dan lainnya)', 'text', '12530', 1, '2025-01-23 17:16:05', '2025-01-23 17:16:21'),
	('f400a8b6-6e43-4bda-8780-015ebfb58004', 'Letter', 'letter', 'Bank Transfer - Bank Name', 'bank-name', 'Bank Transfer - Bank Name | Nama Bank yang digunakan untuk pembayaran dan akan disertakan pada surat Invoice', 'text', 'Mandiri Cabang Jakarta Aneka Tambang', 1, '2025-02-03 13:50:15', '2025-02-03 13:53:18');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
