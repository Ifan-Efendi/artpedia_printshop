-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: 127.0.0.1    Database: artpedia_db
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `artpedia_db`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `artpedia_db` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;

USE `artpedia_db`;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jenis_kertas`
--

DROP TABLE IF EXISTS `jenis_kertas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jenis_kertas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `harga_tambahan` decimal(12,2) NOT NULL DEFAULT 0.00,
  `aktif` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jenis_kertas`
--

LOCK TABLES `jenis_kertas` WRITE;
/*!40000 ALTER TABLE `jenis_kertas` DISABLE KEYS */;
INSERT INTO `jenis_kertas` VALUES (1,'Vinyl',NULL,0.00,1,'2026-02-01 04:00:12','2026-02-01 04:00:12'),(2,'Vinyl Transparan',NULL,0.00,1,'2026-02-01 04:00:12','2026-02-01 04:00:12'),(3,'Chromo Glossy',NULL,0.00,1,'2026-02-01 04:00:12','2026-02-01 04:00:12'),(4,'Chromo HVS',NULL,0.00,1,'2026-02-01 04:00:12','2026-02-01 04:00:12'),(5,'Artpaper 150 gsm',NULL,0.00,1,'2026-02-01 04:00:12','2026-02-01 04:00:12'),(6,'Artpaper 260 gsm',NULL,0.00,1,'2026-02-01 04:00:12','2026-02-01 04:00:12'),(7,'Artpaper 120 gsm',NULL,0.00,1,'2026-02-01 04:00:12','2026-02-01 04:00:12'),(8,'Linen',NULL,0.00,1,'2026-02-01 04:00:12','2026-02-01 04:00:12'),(9,'Concord',NULL,0.00,1,'2026-02-01 04:00:12','2026-02-01 04:00:12'),(10,'Jasmine',NULL,0.00,1,'2026-02-01 04:00:12','2026-02-01 04:00:12');
/*!40000 ALTER TABLE `jenis_kertas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kategori_produk`
--

DROP TABLE IF EXISTS `kategori_produk`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kategori_produk` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `aktif` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kategori_produk_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kategori_produk`
--

LOCK TABLES `kategori_produk` WRITE;
/*!40000 ALTER TABLE `kategori_produk` DISABLE KEYS */;
INSERT INTO `kategori_produk` VALUES (1,'Sticker','sticker','Cetak stiker berkualitas dengan berbagai pilihan bahan dan potong.',NULL,1,'2026-02-01 04:00:11','2026-02-01 04:00:11'),(2,'Poster','poster','Poster kualitas HD untuk kebutuhan promosi atau dekorasi.',NULL,1,'2026-02-01 04:00:11','2026-02-01 04:00:11'),(3,'Kartu Nama','kartu-nama','Kartu nama profesional dengan bahan premium.',NULL,1,'2026-02-01 04:00:11','2026-02-01 04:00:11'),(4,'Kartu Ucapan','kartu-ucapan','Cetak kartu ucapan personal atau bisnis.',NULL,1,'2026-02-01 04:00:11','2026-02-01 04:00:11'),(5,'Brosur','brosur','Media promosi brosur dengan berbagai ukuran.',NULL,1,'2026-02-01 04:00:11','2026-02-01 04:00:11');
/*!40000 ALTER TABLE `kategori_produk` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2014_10_12_000000_create_users_table',1),(2,'2014_10_12_100000_create_password_resets_table',1),(3,'2019_08_19_000000_create_failed_jobs_table',1),(4,'2019_12_14_000001_create_personal_access_tokens_table',1),(5,'2026_01_22_000001_create_kategori_produk_table',1),(6,'2026_01_22_000002_create_ukuran_kertas_table',1),(7,'2026_01_22_000003_create_jenis_kertas_table',1),(8,'2026_01_22_000004_create_produk_table',1),(9,'2026_01_22_000005_create_pesanan_table',1),(10,'2026_01_22_083439_create_transaksis_table',1),(11,'2026_01_22_083503_add_transaksi_id_to_pesanan_table',1),(12,'2026_01_22_162356_add_details_to_pesanan_table',1),(13,'2026_04_21_000001_add_dibatalkan_status_to_pesanan_enum',2);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_resets`
--

LOCK TABLES `password_resets` WRITE;
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
INSERT INTO `password_resets` VALUES ('kasir@gmail.com','$2y$10$1TTpv9pj9TxP4yiN4Hy9uup02t91OChxxJJOZKVlt2VQXIUKpm5fq','2026-02-17 08:58:48'),('ifan@gmail.com','$2y$10$081oIcz6oqkXOdzZZWSzGeWpXg73vsPbtQOOleP7BrpuZCzH9u.xS','2026-02-17 09:06:43'),('ifanefendi666@gmail.com','$2y$10$PgXE1sEXHgxC664GrgDLF.ykWUFeVqHgjSCo5V6.0AhuwrXhgNwQW','2026-02-17 09:10:07');
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pesanan`
--

DROP TABLE IF EXISTS `pesanan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pesanan` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nomor_pesanan` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `transaksi_id` bigint(20) unsigned DEFAULT NULL,
  `produk_id` bigint(20) unsigned NOT NULL,
  `ukuran_kertas_id` bigint(20) unsigned NOT NULL,
  `jenis_kertas_id` bigint(20) unsigned NOT NULL,
  `jumlah` int(11) NOT NULL,
  `file_desain` varchar(255) NOT NULL,
  `catatan` text DEFAULT NULL,
  `finishing` varchar(255) DEFAULT NULL,
  `opsi_potong` varchar(255) DEFAULT NULL,
  `harga_satuan` decimal(12,2) NOT NULL,
  `total_harga` decimal(12,2) NOT NULL,
  `bukti_pembayaran` varchar(255) DEFAULT NULL,
  `estimasi_waktu` int(11) NOT NULL,
  `status` enum('pending','ditolak','dibatalkan','dalam_antrian','diproses','selesai') DEFAULT 'pending',
  `alasan_penolakan` text DEFAULT NULL,
  `dikonfirmasi_oleh` bigint(20) unsigned DEFAULT NULL,
  `dikonfirmasi_at` timestamp NULL DEFAULT NULL,
  `diproses_oleh` bigint(20) unsigned DEFAULT NULL,
  `mulai_produksi_at` timestamp NULL DEFAULT NULL,
  `selesai_produksi_at` timestamp NULL DEFAULT NULL,
  `diambil_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pesanan_nomor_pesanan_unique` (`nomor_pesanan`),
  KEY `pesanan_user_id_foreign` (`user_id`),
  KEY `pesanan_produk_id_foreign` (`produk_id`),
  KEY `pesanan_ukuran_kertas_id_foreign` (`ukuran_kertas_id`),
  KEY `pesanan_jenis_kertas_id_foreign` (`jenis_kertas_id`),
  KEY `pesanan_dikonfirmasi_oleh_foreign` (`dikonfirmasi_oleh`),
  KEY `pesanan_diproses_oleh_foreign` (`diproses_oleh`),
  KEY `pesanan_transaksi_id_foreign` (`transaksi_id`),
  CONSTRAINT `pesanan_dikonfirmasi_oleh_foreign` FOREIGN KEY (`dikonfirmasi_oleh`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `pesanan_diproses_oleh_foreign` FOREIGN KEY (`diproses_oleh`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `pesanan_jenis_kertas_id_foreign` FOREIGN KEY (`jenis_kertas_id`) REFERENCES `jenis_kertas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pesanan_produk_id_foreign` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pesanan_transaksi_id_foreign` FOREIGN KEY (`transaksi_id`) REFERENCES `transaksis` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pesanan_ukuran_kertas_id_foreign` FOREIGN KEY (`ukuran_kertas_id`) REFERENCES `ukuran_kertas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pesanan_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pesanan`
--

LOCK TABLES `pesanan` WRITE;
/*!40000 ALTER TABLE `pesanan` DISABLE KEYS */;
INSERT INTO `pesanan` VALUES (4,'ART-20260421-001',3,2,2,1,1,1,'desain/UD6E2BGKarFsf4fWXZ9dX4IGuZHxAoiVCRGDzkUm.png',NULL,'Glossy','Die Cut',23000.00,23000.00,'bukti_bayar/iHsHep2Fo8DHkEyxM1V7i79yPg3SfC9GdtofQDpH.png',5,'dibatalkan',NULL,1,'2026-04-21 09:57:25',NULL,NULL,NULL,NULL,'2026-04-21 09:30:41','2026-04-21 09:57:25'),(5,'ART-20260421-002',3,2,4,1,5,1,'desain/kuXpHj40AJmRaoD7DQyezP79qfOXD1hsmcdcFbWt.png',NULL,'Glossy','Potong Kotak',11000.00,11000.00,'bukti_bayar/iHsHep2Fo8DHkEyxM1V7i79yPg3SfC9GdtofQDpH.png',3,'dibatalkan',NULL,1,'2026-04-21 09:57:27',NULL,NULL,NULL,NULL,'2026-04-21 09:30:41','2026-04-21 09:57:27'),(6,'ART-20260421-003',5,NULL,6,5,5,1,'desain/2LGHRnqvKJa57NFZG2K4vPiyDYiqK7fAIgfJYCHX.png',NULL,'Tidak Pakai','Potong Kotak',0.00,0.00,'Pesanan Langsung',60,'dibatalkan',NULL,1,'2026-04-21 09:44:06',NULL,NULL,NULL,NULL,'2026-04-21 09:35:59','2026-04-21 09:44:06'),(7,'ART-20260421-004',5,NULL,5,1,5,1,'desain/SEfDTqPYB8fjROS43Tf3joRtawXxvjqY0pWYpzaT.png',NULL,'Glossy','Potong Kotak',4000.00,4000.00,'Pesanan Langsung',3,'dibatalkan',NULL,1,'2026-04-21 09:44:14',NULL,NULL,NULL,NULL,'2026-04-21 09:36:19','2026-04-21 09:44:14'),(11,'ART-20260421-006',3,6,3,1,5,1,'desain/cNMBBaiU8LFjLRLdwsOJGfI8WqEKWnZ5qM24Mdyn.jpg',NULL,'Glossy','Potong Kotak',11000.00,11000.00,'bukti_bayar/GuZMTNJ6ttgOWH0plHC6xSDiUCudyoFuYqDATIO0.png',3,'dibatalkan',NULL,1,'2026-04-21 12:00:04',NULL,NULL,NULL,NULL,'2026-04-21 11:35:59','2026-04-21 12:00:04'),(12,'ART-20260421-007',6,NULL,1,1,2,1,'desain/91dFqTfZ6BspuFgKJxIb9umWNk4RrvJFONmuKBz1.jpg',NULL,'Tidak Pakai','Potong Kotak',0.00,0.00,'Pesanan Langsung',5,'selesai',NULL,1,'2026-04-21 11:59:59',2,'2026-04-23 06:41:31','2026-04-23 06:42:04',NULL,'2026-04-21 11:59:59','2026-04-23 06:42:04'),(13,'ART-20260421-008',3,7,4,1,5,1,'desain/8IJ3bsKNvC9DOF0GLNpA5sbZhrBs8O5ruvQRgQiS.png',NULL,'Glossy','Potong Kotak',11000.00,11000.00,'bukti_bayar/EWP3Tl9QLlGCM7LrHyurQSZmOKwpKGVJFWgtTsyp.png',3,'ditolak','no',1,'2026-04-21 12:36:25',NULL,NULL,NULL,NULL,'2026-04-21 12:27:16','2026-04-21 12:36:25'),(14,'ART-20260421-009',4,NULL,2,1,1,1,'desain/7ebdm4l2o2Os6NZ6Ko6JvhXAwTh72Lqt4YFl1OZE.png',NULL,'Tidak Pakai','Potong Kotak',11000.00,11000.00,'Pesanan Langsung',5,'selesai',NULL,1,'2026-04-21 12:48:10',2,'2026-04-23 06:42:07','2026-04-23 06:42:09',NULL,'2026-04-21 12:48:10','2026-04-23 06:42:09'),(15,'ART-20260423-001',5,NULL,1,6,2,10,'NANTI_DIKIRIM',NULL,'Glossy','Kiss Cut',19000.00,190000.00,'Pesanan Langsung',50,'selesai',NULL,1,'2026-04-23 06:40:18',2,'2026-04-23 06:42:11','2026-04-23 06:42:13',NULL,'2026-04-23 06:40:18','2026-04-23 06:42:13'),(16,'ART-20260423-002',3,8,1,1,1,1,'desain/npNTTrvw3cWOilKgpxGa04X7DbjzDOGwdPbkDFWw.png','ukuran 5x2','Glossy','Kiss Cut',19000.00,19000.00,'bukti_bayar/6VLTsdoJRQiFggELw21uaaI3FmoGZGXFuDrP4JL6.jpg',5,'selesai',NULL,1,'2026-04-23 07:02:03',2,'2026-04-23 07:14:01','2026-04-23 07:14:21',NULL,'2026-04-23 06:44:59','2026-04-23 07:14:21'),(18,'ART-20260423-003',7,NULL,1,1,2,1,'desain/o42yLw63koKFnNVslnS4Pg69smOW6KZUEp5x7acH.png',NULL,'Glossy','Kiss Cut',19000.00,19000.00,'Pesanan Langsung',5,'selesai',NULL,1,'2026-04-23 07:17:52',2,'2026-04-23 07:20:07','2026-04-23 07:20:09',NULL,'2026-04-23 07:17:52','2026-04-23 07:20:09'),(19,'ART-20260423-004',8,NULL,6,5,6,1,'desain/GrYFs8VIw12jvcVNNviwiyULX6YwkhfpsZOidSos.jpg',NULL,'Glossy','Potong Kotak',4480.00,4480.00,'Pesanan Langsung',60,'selesai',NULL,1,'2026-04-23 07:19:05',2,'2026-04-23 07:19:27','2026-04-23 07:20:00',NULL,'2026-04-23 07:19:05','2026-04-23 07:20:00'),(20,'ART-20260423-005',3,9,2,1,3,1,'desain/VFeZuaU2IglONLcZ6rka7PyEMjaiQr0khpHOknD7.pdf',NULL,'Glossy','Die Cut',20000.00,20000.00,'bukti_bayar/Y2AWuYj9WHs9XDnWtl3w8dMTGPRvWIDnYvniIhco.jpg',5,'selesai',NULL,1,'2026-04-23 07:36:37',2,'2026-04-23 08:00:07','2026-04-23 08:00:27',NULL,'2026-04-23 07:34:18','2026-04-23 08:00:27'),(21,'ART-20260423-006',3,10,8,1,5,1,'desain/lOKf0kM2YxEgHmaN4eaPeyZSw1Y1nUXyV7WlDDCv.jpg',NULL,'Tidak Pakai','Potong Kotak',5000.00,5000.00,'bukti_bayar/LcdZImjy49T16MaCOCEsj1maJTFaKY5yW6Sb0WPW.jpg',1,'selesai',NULL,1,'2026-04-23 07:59:29',2,'2026-04-23 07:59:40','2026-04-23 08:00:01',NULL,'2026-04-23 07:49:09','2026-04-23 08:00:01');
/*!40000 ALTER TABLE `pesanan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `produk`
--

DROP TABLE IF EXISTS `produk`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `produk` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `kategori_id` bigint(20) unsigned NOT NULL,
  `nama` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `harga_satuan` decimal(12,2) NOT NULL,
  `min_order` int(11) NOT NULL DEFAULT 1,
  `estimasi_waktu_per_unit` int(11) NOT NULL,
  `aktif` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `produk_slug_unique` (`slug`),
  KEY `produk_kategori_id_foreign` (`kategori_id`),
  CONSTRAINT `produk_kategori_id_foreign` FOREIGN KEY (`kategori_id`) REFERENCES `kategori_produk` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `produk`
--

LOCK TABLES `produk` WRITE;
/*!40000 ALTER TABLE `produk` DISABLE KEYS */;
INSERT INTO `produk` VALUES (1,1,'Sticker Vinyl A3','sticker-vinyl-a3','Cetak stiker bahan Vinyl tahan air ukuran A3 (32x48cm). Termasuk potong.','produk/sticker-vinyl-a3.png',11000.00,1,5,1,'2026-02-01 04:00:12','2026-04-21 12:11:28'),(2,1,'Sticker Chromo A3','sticker-chromo-a3','Cetak stiker bahan kertas Chromo mengkilap ukuran A3 (32x48cm).','produk/sticker-chromo-a3.png',8000.00,1,5,1,'2026-02-01 04:00:12','2026-04-21 12:10:37'),(3,2,'Poster Artpaper 260 A3','poster-ap260-a3','Poster dinding kertas tebal Artpaper 260gsm ukuran A3.','produk/poster-a3.png',7000.00,1,3,1,'2026-02-01 04:00:12','2026-04-21 12:12:25'),(4,2,'Poster Artpaper 260 A4','poster-ap260-a4','Poster dinding kertas tebal Artpaper 260gsm ukuran A4.','produk/poster-a4.png',4000.00,1,3,1,'2026-02-01 04:00:12','2026-04-21 12:13:09'),(5,2,'Poster Artpaper 150 A3','poster-ap150-a3','Poster ekonomis kertas Artpaper 150gsm ukuran A3.','produk/poster-a3.png',5000.00,1,3,1,'2026-02-01 04:00:12','2026-04-21 12:12:25'),(6,3,'Kartu Nama Standard','kartu-nama-standard','Kartu nama 1 muka bahan Art Carton 260gsm (Box isi 100).','produk/kartu-nama.png',40000.00,1,60,1,'2026-02-01 04:00:12','2026-04-21 12:16:10'),(7,4,'Kartu Ucapan A3','kartu-ucapan-a3','Cetak kartu ucapan custom bahan BW / Carton per lembar A3.','produk/kartu-ucapan.png',7000.00,1,2,1,'2026-02-01 04:00:12','2026-04-21 12:14:24'),(8,5,'Brosur A4','brosur-a4','Cetak brosur promosi ukuran A4 bahan Artpaper 120gsm.','produk/brosur-a4.png',4000.00,1,1,1,'2026-02-01 04:00:12','2026-04-21 12:08:20'),(9,5,'Brosur A5','brosur-a5','Cetak brosur promosi hemat ukuran A5 bahan Artpaper 120gsm.','produk/brosur-a5.png',2500.00,2,1,1,'2026-02-01 04:00:12','2026-04-21 12:07:40');
/*!40000 ALTER TABLE `produk` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transaksis`
--

DROP TABLE IF EXISTS `transaksis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transaksis` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nomor_transaksi` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `total_harga` decimal(12,2) NOT NULL,
  `bukti_pembayaran` varchar(255) DEFAULT NULL,
  `status` enum('pending','valid','ditolak') NOT NULL DEFAULT 'pending',
  `alasan_penolakan` text DEFAULT NULL,
  `dikonfirmasi_oleh` bigint(20) unsigned DEFAULT NULL,
  `dikonfirmasi_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `transaksis_nomor_transaksi_unique` (`nomor_transaksi`),
  KEY `transaksis_user_id_foreign` (`user_id`),
  KEY `transaksis_dikonfirmasi_oleh_foreign` (`dikonfirmasi_oleh`),
  CONSTRAINT `transaksis_dikonfirmasi_oleh_foreign` FOREIGN KEY (`dikonfirmasi_oleh`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `transaksis_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transaksis`
--

LOCK TABLES `transaksis` WRITE;
/*!40000 ALTER TABLE `transaksis` DISABLE KEYS */;
INSERT INTO `transaksis` VALUES (1,'TRX-20260421-001',3,42000.00,'bukti_bayar/SXFW8W1Ted5eOCYOVobPk5smU2mb5HmFy9A0EhpM.png','pending',NULL,NULL,NULL,'2026-04-21 09:14:23','2026-04-21 09:14:23'),(2,'TRX-20260421-002',3,34000.00,'bukti_bayar/iHsHep2Fo8DHkEyxM1V7i79yPg3SfC9GdtofQDpH.png','pending',NULL,NULL,NULL,'2026-04-21 09:30:41','2026-04-21 09:30:41'),(3,'TRX-20260421-003',3,11000.00,'bukti_bayar/1ABLynAnSFq5EdqDyu66tvJ4RNTVNYCvlBKhhtli.png','pending',NULL,NULL,NULL,'2026-04-21 09:58:31','2026-04-21 09:58:31'),(4,'TRX-20260421-004',3,11000.00,'bukti_bayar/4K2LHlclToPS3tNeUzhjTjMElWCW8OhUeB9L4LTP.png','pending',NULL,NULL,NULL,'2026-04-21 11:05:29','2026-04-21 11:05:29'),(5,'TRX-20260421-005',3,228000.00,'bukti_bayar/66lLZqHDq1zOBEp7V2pBUCTCl74FyJ8c09dZe4ms.jpg','pending',NULL,NULL,NULL,'2026-04-21 11:24:04','2026-04-21 11:24:04'),(6,'TRX-20260421-006',3,11000.00,'bukti_bayar/GuZMTNJ6ttgOWH0plHC6xSDiUCudyoFuYqDATIO0.png','pending',NULL,NULL,NULL,'2026-04-21 11:35:59','2026-04-21 11:35:59'),(7,'TRX-20260421-007',3,11000.00,'bukti_bayar/EWP3Tl9QLlGCM7LrHyurQSZmOKwpKGVJFWgtTsyp.png','pending',NULL,NULL,NULL,'2026-04-21 12:27:16','2026-04-21 12:27:16'),(8,'TRX-20260423-001',3,38000.00,'bukti_bayar/6VLTsdoJRQiFggELw21uaaI3FmoGZGXFuDrP4JL6.jpg','pending',NULL,NULL,NULL,'2026-04-23 06:44:59','2026-04-23 06:44:59'),(9,'TRX-20260423-002',3,20000.00,'bukti_bayar/Y2AWuYj9WHs9XDnWtl3w8dMTGPRvWIDnYvniIhco.jpg','pending',NULL,NULL,NULL,'2026-04-23 07:34:18','2026-04-23 07:34:18'),(10,'TRX-20260423-003',3,5000.00,'bukti_bayar/LcdZImjy49T16MaCOCEsj1maJTFaKY5yW6Sb0WPW.jpg','pending',NULL,NULL,NULL,'2026-04-23 07:49:09','2026-04-23 07:49:09');
/*!40000 ALTER TABLE `transaksis` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ukuran_kertas`
--

DROP TABLE IF EXISTS `ukuran_kertas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ukuran_kertas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) NOT NULL,
  `dimensi` varchar(255) NOT NULL,
  `faktor_harga` decimal(5,2) NOT NULL DEFAULT 1.00,
  `faktor_waktu` decimal(5,2) NOT NULL DEFAULT 1.00,
  `aktif` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ukuran_kertas`
--

LOCK TABLES `ukuran_kertas` WRITE;
/*!40000 ALTER TABLE `ukuran_kertas` DISABLE KEYS */;
INSERT INTO `ukuran_kertas` VALUES (1,'A3','297x420mm',1.00,1.00,1,'2026-02-01 04:00:11','2026-02-01 04:00:11'),(2,'A4','210x297mm',1.00,1.00,1,'2026-02-01 04:00:11','2026-02-01 04:00:11'),(3,'A5','148x210mm',1.00,1.00,1,'2026-02-01 04:00:12','2026-02-01 04:00:12'),(4,'A6','105x148mm',1.00,1.00,1,'2026-02-01 04:00:12','2026-02-01 04:00:12'),(5,'Standard (54x86 mm)','54x86mm',1.00,1.00,1,'2026-02-01 04:00:12','2026-02-01 04:00:12'),(6,'Custom','Custom',1.00,1.00,1,'2026-02-01 04:00:12','2026-02-01 04:00:12');
/*!40000 ALTER TABLE `ukuran_kertas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('pelanggan','kasir','operator_produksi') NOT NULL DEFAULT 'pelanggan',
  `telepon` varchar(255) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Kasir Artpedia','kasir@gmail.com',NULL,'$2y$10$Jv8pF9A4QtfzvMisXhlM..b6nicGL2WmpT1ZkM9.be54tI3T.fJO6','kasir','081234567890','SYC4wWjFHez000f4sLKtKIUqe6pcO9AKAIxMIGXSr6yYsFZkxFrnP9q5dkUi','2026-02-01 04:00:11','2026-02-01 04:00:11'),(2,'Operator Produksi','produksi@gmail.com',NULL,'$2y$10$j/p2sfqC3VmJ9g12dGKTwOzpc5rtGVGtjdnBXytihnoxu3.9tuqD6','operator_produksi','081234567891','tPmue2AFW3JQnG2GeHytQkEql12mfH8mBXZc06XnMTTsNRzG2LcofZAkROCu','2026-02-01 04:00:11','2026-02-01 04:00:11'),(3,'Ifan','ifan@gmail.com',NULL,'$2y$10$.doCbAYDTWymcpHDaNOAOOlAlxf1foy9tK9chgN6DtacqmJsjU9GG','pelanggan','081234567892','IFq3QRabIb55xJjIUjSzIftPKb4Mne5wQgydgymsrwEruSeopBy2Eab0FXuK','2026-02-01 04:00:11','2026-02-01 04:00:11'),(4,'Rika Anggareni','rikaanggareni@gmail.com',NULL,'$2y$10$l/XiWeGd3v/ST5dlmBw3uucN.K9ckkIK4Rcjub0lLBCysSWa7rN6O','pelanggan','089652663982',NULL,'2026-02-03 09:19:41','2026-02-03 09:19:41'),(5,'Ifan Efendi','ifanefendi666@gmail.com',NULL,'$2y$10$UDjHxnC86aaCkkLv5MLag.ymbzN79TNSdzPKXsJHUiwpYMa06k4xS','pelanggan','082321238305','EMF0qnC1HjvwylCI62dmO4a6KXWCF1Uz8oAZDUeMl9LpXcjKvPV06qDjbiuJ','2026-02-17 09:08:19','2026-02-17 09:08:19'),(6,'Ifan','0823212385305@artpedia.com',NULL,'$2y$10$lzJvIX/fLCURW72HlkqEEeBFhSZ9vkBNhHIFtmHo.9tfs8DW9rdjC','pelanggan','0823212385305',NULL,'2026-04-21 11:59:59','2026-04-21 11:59:59'),(7,'ifan','0855565@artpedia.com',NULL,'$2y$10$qzY.Cd4lx2UsWAgfKSjw/Oc1nUbqtlAHDzKThNupDN3UZnFHnq0My','pelanggan','0855565',NULL,'2026-04-23 07:17:52','2026-04-23 07:17:52'),(8,'uhuy','08565464+45656+@artpedia.com',NULL,'$2y$10$PzCSkg5EJU4PBByY1QD4iOgV5AovcjuFBr28SeKm14aG9OQbN9Ggm','pelanggan','08565464+45656+',NULL,'2026-04-23 07:19:05','2026-04-23 07:19:05');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-04-30  5:43:57
