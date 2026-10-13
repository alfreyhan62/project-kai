-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: arsip_digital
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
-- Table structure for table `arsip`
--

DROP TABLE IF EXISTS `arsip`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `arsip` (
  `arsip_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `unit_id` int(10) unsigned NOT NULL,
  `jenis_surat_id` int(10) unsigned NOT NULL,
  `klasifikasi_id` int(10) unsigned DEFAULT NULL,
  `rak_id` int(10) unsigned DEFAULT NULL,
  `no_registrasi` varchar(100) NOT NULL,
  `no_surat` varchar(100) NOT NULL,
  `perihal` varchar(500) NOT NULL,
  `tanggal_surat` date NOT NULL,
  `masa_simpan_tahun` int(10) unsigned DEFAULT NULL,
  `tanggal_mulai_retensi` date DEFAULT NULL,
  `tanggal_jatuh_tempo` date DEFAULT NULL,
  `status_arsip` enum('Inaktif','Permanen','Musnah') NOT NULL DEFAULT 'Inaktif',
  `penyimpanan_cloud` varchar(500) DEFAULT NULL,
  `detail_arsip` text DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `created_by` int(10) unsigned NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `nomor_spk` varchar(100) DEFAULT NULL,
  `tanggal_spk` date DEFAULT NULL,
  `vendor` varchar(255) DEFAULT NULL,
  `nama_pekerjaan` varchar(500) DEFAULT NULL,
  `nilai_spk` decimal(18,2) DEFAULT NULL,
  `kategori_nilai` varchar(30) DEFAULT NULL,
  `tanggal_mulai_pekerjaan` date DEFAULT NULL,
  `tanggal_selesai_pekerjaan` date DEFAULT NULL,
  `jangka_waktu` int(10) unsigned DEFAULT NULL,
  `penanggung_jawab_unit_id` int(10) unsigned DEFAULT NULL,
  `status_spk` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`arsip_id`),
  UNIQUE KEY `no_registrasi` (`no_registrasi`),
  KEY `fk_arsip_jenis` (`jenis_surat_id`),
  KEY `fk_arsip_klasifikasi` (`klasifikasi_id`),
  KEY `fk_arsip_rak` (`rak_id`),
  KEY `fk_arsip_created_by` (`created_by`),
  KEY `idx_arsip_no_surat` (`no_surat`),
  KEY `idx_arsip_perihal` (`perihal`),
  KEY `idx_arsip_tanggal` (`tanggal_surat`),
  KEY `idx_arsip_status` (`status_arsip`),
  KEY `idx_arsip_unit` (`unit_id`),
  KEY `idx_arsip_jatuh_tempo` (`tanggal_jatuh_tempo`),
  CONSTRAINT `fk_arsip_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_arsip_jenis` FOREIGN KEY (`jenis_surat_id`) REFERENCES `jenis_surat` (`jenis_surat_id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_arsip_klasifikasi` FOREIGN KEY (`klasifikasi_id`) REFERENCES `klasifikasi_arsip` (`klasifikasi_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_arsip_rak` FOREIGN KEY (`rak_id`) REFERENCES `rak` (`rak_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_arsip_unit` FOREIGN KEY (`unit_id`) REFERENCES `unit_kerja` (`unit_id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `arsip`
--

LOCK TABLES `arsip` WRITE;
/*!40000 ALTER TABLE `arsip` DISABLE KEYS */;
INSERT INTO `arsip` VALUES (1,4,12,NULL,3,'99988','999','tes','2026-03-25',1,'2026-08-15','2026-08-22','Inaktif',NULL,'tes','tes',13,'2026-08-15 19:33:36','2026-10-13 19:17:12',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(2,4,12,NULL,3,'99988ee','999cc','tesc','2025-09-16',NULL,NULL,NULL,'Permanen',NULL,'cc','cc',13,'2026-08-15 20:02:47','2026-10-13 19:11:33',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(3,4,12,NULL,NULL,'UAT-KALENDER-001','UAT/KALENDER/001/2026','UAT Kalender ΓÇö Arsip Sudah Kedaluwarsa','2025-08-14',1,'2025-08-14','2026-08-14','Musnah',NULL,'DATA DUMMY UAT KALENDER ΓÇö SUDAH KEDALUWARSA','DUMMY UAT ΓÇö jangan digunakan sebagai data produksi.',13,'2026-08-15 20:32:05','2026-10-13 19:11:33',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(4,4,12,NULL,NULL,'UAT-KALENDER-002','UAT/KALENDER/002/2026','UAT Kalender ΓÇö Jatuh Tempo H-7','2025-08-22',1,'2025-08-22','2026-08-22','Musnah',NULL,'DATA DUMMY UAT KALENDER ΓÇö H-7','DUMMY UAT ΓÇö jangan digunakan sebagai data produksi.',13,'2026-08-15 20:32:05','2026-10-13 19:11:33',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(5,4,12,NULL,NULL,'UAT-KALENDER-003','UAT/KALENDER/003/2026','UAT Kalender ΓÇö Jatuh Tempo H-1 Bulan','2025-09-15',1,'2025-09-15','2026-09-15','Musnah',NULL,'DATA DUMMY UAT KALENDER ΓÇö H-1 BULAN','DUMMY UAT ΓÇö jangan digunakan sebagai data produksi.',13,'2026-08-15 20:32:05','2026-10-13 19:11:33',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(6,4,12,NULL,NULL,'UAT-KALENDER-004','UAT/KALENDER/004/2026','UAT Kalender ΓÇö Jatuh Tempo H-1 Tahun','2026-08-15',1,'2026-08-15','2027-08-15','Musnah',NULL,'DATA DUMMY UAT KALENDER ΓÇö H-1 TAHUN','DUMMY UAT ΓÇö jangan digunakan sebagai data produksi.',13,'2026-08-15 20:32:05','2026-10-13 19:11:33',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(7,4,12,NULL,NULL,'UAT-KALENDER-005','UAT/KALENDER/005/2026','UAT Kalender ΓÇö Arsip Permanen','2026-08-15',NULL,NULL,NULL,'Permanen',NULL,'DATA DUMMY UAT ΓÇö ARSIP PERMANEN','DUMMY UAT ΓÇö jangan digunakan sebagai data produksi.',13,'2026-08-15 20:32:06','2026-10-13 19:11:33',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(8,4,12,NULL,3,'00998','9888','dinas malam','2025-06-24',1,'2026-08-15','2026-08-15','Inaktif',NULL,'ok','tes',13,'2026-08-15 23:13:50','2026-10-13 19:17:12',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(9,19,12,NULL,NULL,'0099899','988888','dinas malam banget','2026-05-01',NULL,NULL,NULL,'Inaktif',NULL,NULL,NULL,20,'2026-08-15 23:23:50','2026-10-13 19:17:12',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(10,21,12,NULL,4,'DUMMY-2026-001','TEST/OPS/001/2026','Dummy dashboard operasional 01','2026-03-05',1,'2026-03-05','2027-03-05','Inaktif',NULL,NULL,'Dummy development testing',1,'2026-03-05 09:00:00','2026-10-13 19:17:12',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(11,21,12,NULL,4,'DUMMY-2026-002','TEST/KEU/002/2026','Dummy dashboard keuangan 02','2026-03-18',1,'2026-03-18','2027-03-18','Permanen',NULL,NULL,'Dummy development testing',1,'2026-03-18 09:00:00','2026-10-13 19:11:33',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(12,21,12,NULL,4,'DUMMY-2026-003','TEST/SDM/003/2026','Dummy dashboard SDM 03','2026-04-03',1,'2026-04-03','2027-04-03','Inaktif',NULL,NULL,'Dummy development testing',1,'2026-04-03 09:00:00','2026-10-13 19:17:12',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(13,21,12,NULL,4,'DUMMY-2026-004','TEST/TEK/004/2026','Dummy dashboard teknis 04','2026-04-22',1,'2026-04-22','2027-04-22','Musnah',NULL,NULL,'Dummy development testing',1,'2026-04-22 09:00:00','2026-10-13 19:11:33',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(14,22,12,NULL,5,'DUMMY-2026-005','TEST/UMM/005/2026','Dummy dashboard umum 05','2026-04-30',1,'2026-04-30','2027-04-30','Inaktif',NULL,NULL,'Dummy development testing',1,'2026-04-30 09:00:00','2026-10-13 19:17:12',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(15,22,12,NULL,5,'DUMMY-2026-006','TEST/OPS/006/2026','Dummy dashboard operasional 06','2026-05-07',1,'2026-05-07','2027-05-07','Permanen',NULL,NULL,'Dummy development testing',1,'2026-05-07 09:00:00','2026-10-13 19:11:33',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(16,22,12,NULL,5,'DUMMY-2026-007','TEST/KEU/007/2026','Dummy dashboard keuangan 07','2026-05-26',1,'2026-05-26','2027-05-26','Inaktif',NULL,NULL,'Dummy development testing',1,'2026-05-26 09:00:00','2026-10-13 19:17:12',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(17,23,12,NULL,6,'DUMMY-2026-008','TEST/SDM/008/2026','Dummy dashboard SDM 08','2026-06-04',1,'2026-06-04','2027-06-04','Musnah',NULL,NULL,'Dummy development testing',1,'2026-06-04 09:00:00','2026-10-13 19:11:33',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(18,23,12,NULL,6,'DUMMY-2026-009','TEST/TEK/009/2026','Dummy dashboard teknis 09','2026-06-17',1,'2026-06-17','2027-06-17','Inaktif',NULL,NULL,'Dummy development testing',1,'2026-06-17 09:00:00','2026-10-13 19:17:12',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(19,23,12,NULL,6,'DUMMY-2026-010','TEST/UMM/010/2026','Dummy dashboard umum 10','2026-07-02',1,'2026-07-02','2027-07-02','Permanen',NULL,NULL,'Dummy development testing',1,'2026-07-02 09:00:00','2026-10-13 19:11:33',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(20,24,12,NULL,7,'DUMMY-2026-011','TEST/OPS/011/2026','Dummy dashboard operasional 11','2026-07-16',1,'2026-07-16','2026-08-22','Inaktif',NULL,NULL,'Dummy development testing',1,'2026-07-16 09:00:00','2026-10-13 19:17:12',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(21,24,12,NULL,7,'DUMMY-2026-012','TEST/KEU/012/2026','Dummy dashboard keuangan 12','2026-08-01',1,'2026-08-01','2026-08-31','Musnah',NULL,NULL,'Dummy development testing',1,'2026-08-01 09:00:00','2026-10-13 19:11:33',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(22,24,12,NULL,7,'DUMMY-2026-013','TEST/SDM/013/2026','Dummy dashboard SDM 13','2026-08-08',1,'2026-08-08','2026-12-15','Inaktif',NULL,NULL,'Dummy development testing',1,'2026-08-08 09:00:00','2026-10-13 19:17:12',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(23,25,12,NULL,8,'DUMMY-2026-014','TEST/TEK/014/2026','Dummy dashboard teknis 14','2026-08-12',1,'2026-08-12','2027-01-10','Musnah',NULL,NULL,'Dummy development testing',1,'2026-08-12 09:00:00','2026-10-13 19:11:33',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(24,25,12,NULL,8,'DUMMY-2026-015','TEST/UMM/015/2026','Dummy dashboard umum 15','2026-08-18',1,'2026-08-18','2027-08-18','Inaktif',NULL,NULL,'Dummy development testing',1,'2026-08-18 09:00:00','2026-10-13 19:17:12',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(25,4,12,NULL,3,'33','9888','dinas malam banget ya','2026-08-24',1,'2026-08-24','2026-08-24','Inaktif',NULL,'res','ees',13,'2026-08-24 00:02:52','2026-10-13 19:17:12',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(26,4,12,NULL,3,'0099854','9888654','dinas malam hh','2026-10-08',NULL,NULL,NULL,'Permanen',NULL,'aktif','tes',13,'2026-10-08 16:03:17','2026-10-13 19:11:33',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(27,4,12,NULL,3,'983','0832','dinas malam','2026-10-12',1,'2026-10-12','2026-10-12','Inaktif',NULL,'okee','oeje',13,'2026-10-12 09:28:36','2026-10-13 19:17:12',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(28,4,12,NULL,NULL,'099','9888','dinas luar','2025-01-13',NULL,NULL,NULL,'Permanen',NULL,'tes','tes',13,'2026-10-12 09:56:59','2026-10-13 19:11:33',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(29,4,14,NULL,3,'099xx','xx','xxx','2026-10-13',1,'2026-10-05',NULL,'Inaktif',NULL,NULL,NULL,13,'2026-10-13 19:44:43','2026-10-13 19:44:43',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(30,4,27,397,9,'09933','988844','dinas luar44','2026-10-13',1,NULL,'2027-10-13','Inaktif',NULL,'ds','ss',13,'2026-10-13 22:59:30','2026-10-13 22:59:30',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(31,4,15,278,10,'01','KL-01-002','Masalah Hukum','2026-10-13',1,NULL,'2027-10-20','Inaktif',NULL,'Dugaan Kasus Korupsi','-',13,'2026-10-13 23:13:59','2026-10-13 23:13:59',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `arsip` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `file_arsip`
--

DROP TABLE IF EXISTS `file_arsip`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `file_arsip` (
  `file_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `arsip_id` bigint(20) unsigned NOT NULL,
  `nama_file` varchar(255) NOT NULL,
  `path_file` varchar(500) NOT NULL,
  `ekstensi` varchar(20) DEFAULT NULL,
  `mime_type` varchar(100) DEFAULT NULL,
  `ukuran_file` bigint(20) unsigned NOT NULL DEFAULT 0,
  `checksum` varchar(128) DEFAULT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `uploaded_by` int(10) unsigned NOT NULL,
  `uploaded_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`file_id`),
  KEY `fk_file_user` (`uploaded_by`),
  KEY `idx_file_arsip` (`arsip_id`),
  CONSTRAINT `fk_file_arsip` FOREIGN KEY (`arsip_id`) REFERENCES `arsip` (`arsip_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_file_user` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`user_id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `file_arsip`
--

LOCK TABLES `file_arsip` WRITE;
/*!40000 ALTER TABLE `file_arsip` DISABLE KEYS */;
INSERT INTO `file_arsip` VALUES (1,2,'Annotation 2023-02-06 233624.png','storage/uploads/6f30939b3f2a20d454053e9395ab0916.png','png','image/png',189457,'76df803f6057c21ad0e5a73350f734a713d867883f153bc0d0671161c9c3b521',0,13,'2026-08-15 20:03:10'),(2,8,'Screenshot 2026-08-14 221422.png','storage/uploads/3ae370307533e06a5793d036d9dc5155.png','png','image/png',73018,'00ded27c7b97166686b6dfa33b55a04cc13342a77b385f7d1dea40a8b1cef5ac',0,13,'2026-08-15 23:14:03'),(3,9,'landingpagedasboar.jpg','storage/uploads/409573843dcddd965b3fdf7805e7fb53.jpg','jpg','image/jpeg',153183,'8a1728bc20ec5e1d6c07503ae8bde38e5c6e607e13abb14cf5fda8621b60674e',0,20,'2026-08-15 23:24:03'),(4,26,'Doc1.pdf','storage/uploads/b52d50da17c982f78da4482cd5b46dbf.pdf','pdf','application/pdf',594101,'41bbf95fc8ccc977035ac9ebe0cae107fd0ede354deb11b24c45660f31171b88',0,13,'2026-10-08 16:03:17'),(5,27,'Laporan_Arsip_Pimpinan_2026-10-08_110538.xlsx','storage/uploads/209c29d5e61f16ad8aeff7388af67250.xlsx','xlsx','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',58558,'6e43ae5a293f9a9422d06a1bc57efc02aa8d552759f720b8e7b0a00cb8ff7dfd',0,13,'2026-10-12 09:28:36'),(6,28,'Laporan_Arsip_Pimpinan_2026-10-08_110538.xlsx','storage/uploads/6f968fb4fee96fc6b74363b5ee70802f.xlsx','xlsx','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',58558,'6e43ae5a293f9a9422d06a1bc57efc02aa8d552759f720b8e7b0a00cb8ff7dfd',0,13,'2026-10-12 09:56:59'),(7,30,'Laporan_Arsip_e-ARSIP_KAI_DIVRE_III_2026-10-13.pdf','storage/uploads/564d623023f6ba437ea561c8b2981cfe.pdf','pdf','application/pdf',996978,'1f2380a5d9fe68b9ce1e84ded147cff4a25e7d0e913c9ad1109dea64e30286ef',0,13,'2026-10-13 22:59:30'),(8,31,'Saputra Andreansyah_062430801645_Per2.xlsx','storage/uploads/3a2591c23298e8b4ea7b20c7279b7f5b.xlsx','xlsx','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',10667,'7236040991cbbad182f8bea3b5dabecc90f4414c894e50d07ad6f9773a04280f',0,13,'2026-10-13 23:13:59'),(9,31,'Saputra Andreansyah_062430801645_Per2.xlsx','storage/uploads/767462bfb50ade4e39e82494b3c01233.xlsx','xlsx','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',10667,'7236040991cbbad182f8bea3b5dabecc90f4414c894e50d07ad6f9773a04280f',0,13,'2026-10-13 23:15:38');
/*!40000 ALTER TABLE `file_arsip` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jadwal_pemusnahan`
--

DROP TABLE IF EXISTS `jadwal_pemusnahan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jadwal_pemusnahan` (
  `jadwal_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `arsip_id` bigint(20) unsigned NOT NULL,
  `tanggal_jatuh_tempo` date NOT NULL,
  `status_jadwal` enum('Menunggu','Mendekati','Siap Dimusnahkan','Sudah Dimusnahkan','Dibatalkan') NOT NULL DEFAULT 'Menunggu',
  `keterangan` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`jadwal_id`),
  UNIQUE KEY `arsip_id` (`arsip_id`),
  KEY `idx_jadwal_tempo` (`tanggal_jatuh_tempo`),
  KEY `idx_jadwal_status` (`status_jadwal`),
  CONSTRAINT `fk_jadwal_arsip` FOREIGN KEY (`arsip_id`) REFERENCES `arsip` (`arsip_id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=292 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jadwal_pemusnahan`
--

LOCK TABLES `jadwal_pemusnahan` WRITE;
/*!40000 ALTER TABLE `jadwal_pemusnahan` DISABLE KEYS */;
INSERT INTO `jadwal_pemusnahan` VALUES (1,3,'2026-08-14','Dibatalkan',NULL,'2026-08-15 20:32:06','2026-08-23 00:23:11'),(2,4,'2026-08-22','Dibatalkan',NULL,'2026-08-15 20:32:06','2026-08-23 00:23:11'),(3,5,'2026-09-15','Dibatalkan',NULL,'2026-08-15 20:32:06','2026-08-23 00:23:11'),(4,6,'2027-08-15','Dibatalkan',NULL,'2026-08-15 20:32:06','2026-08-23 00:23:11'),(9,8,'2026-08-15','Menunggu',NULL,'2026-08-23 00:23:11','2026-08-23 00:23:11'),(10,1,'2026-08-22','Menunggu',NULL,'2026-08-23 00:23:11','2026-08-23 00:23:11'),(11,20,'2026-08-22','Menunggu',NULL,'2026-08-23 00:23:11','2026-08-23 00:23:11'),(40,25,'2026-08-24','Menunggu',NULL,'2026-08-24 00:02:52','2026-08-24 00:02:52'),(214,27,'2026-10-12','Menunggu',NULL,'2026-10-12 09:28:36','2026-10-12 09:28:36');
/*!40000 ALTER TABLE `jadwal_pemusnahan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jenis_surat`
--

DROP TABLE IF EXISTS `jenis_surat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jenis_surat` (
  `jenis_surat_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nama_jenis` varchar(100) NOT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`jenis_surat_id`),
  UNIQUE KEY `nama_jenis` (`nama_jenis`)
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jenis_surat`
--

LOCK TABLES `jenis_surat` WRITE;
/*!40000 ALTER TABLE `jenis_surat` DISABLE KEYS */;
INSERT INTO `jenis_surat` VALUES (3,'Surat Dinas','Surat dinas'),(12,'Instruksi Direksi.',NULL),(13,'Instruksi Kepala Daerah.',NULL),(14,'Kajian Perpajakan',NULL),(15,'Laporan.',NULL),(16,'Maklumat Direksi.',NULL),(17,'Maklumat Kepala Daerah.',NULL),(18,'Nota Dinas',NULL),(19,'Nota Dinas (Internal Dekom)',NULL),(20,'Nota Dinas Permintaan Pembayaran',NULL),(21,'Nota Dinas Permohonan Pembatalan NPD/PR/TB',NULL),(22,'Nota Dinas Permohonan Persetujuan NPD',NULL),(23,'Nota Dinas Permohonan Persetujuan NPD/PR Silang',NULL),(24,'Nota Dinas Permohonan Persetujuan PR',NULL),(25,'Nota Dinas Permohonan Persetujuan TB',NULL),(26,'Nota Dinas Pertanggungjawaban UMDS',NULL),(27,'Nota Dinas Undangan',NULL),(28,'Notulen Rapat',NULL),(29,'Penghargaan.',NULL),(30,'Pengumuman.',NULL),(31,'Registrasi Dokumen Masuk Dari Eksternal',NULL),(32,'Sambutan Tertulis Direksi.',NULL),(33,'Siaran Pers.',NULL),(34,'Surat Dinas (Dekom ke Eksternal)',NULL),(35,'Surat Dinas (Dekom ke KAI)',NULL),(36,'Surat Dinas (KAI ke Dekom/Komite)',NULL),(37,'Surat Dinas (KNR ke Dekom/KAI)',NULL),(38,'Surat Dinas (Komite Audit ke Dekom/KAI)',NULL),(39,'Surat Dinas (KPMR Ke Dekom/KAI)',NULL),(40,'Surat Dinas Undangan',NULL),(41,'Surat Dinas Undangan Dekom',NULL),(42,'Surat Edaran Direksi.',NULL),(43,'Surat Edaran Kepala Daerah.',NULL),(44,'Surat Dinas English',NULL),(45,'Surat Keterangan.',NULL),(46,'Surat Keterangan Sertifikasi DJKA ASP',NULL),(47,'Surat Keterangan Sertifikasi DJKA Non ASP',NULL),(48,'Surat Keterangan Sertifikasi DJKA Pengajuan Baru',NULL),(49,'Surat Keterangan Sertifikasi DJKA PJL',NULL),(50,'Surat Penunjukan Rekanan.',NULL),(51,'Surat Perintah Pelaksana Tugas.',NULL),(52,'Surat Perintah Pelaksana Tugas Dekom',NULL),(53,'Surat Pernyataan.',NULL),(54,'Surat Tugas.',NULL),(55,'Surat Tugas Dekom',NULL);
/*!40000 ALTER TABLE `jenis_surat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `klasifikasi_arsip`
--

DROP TABLE IF EXISTS `klasifikasi_arsip`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `klasifikasi_arsip` (
  `klasifikasi_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) unsigned DEFAULT NULL,
  `tingkat` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `urutan` int(10) unsigned NOT NULL DEFAULT 0,
  `sumber_kunci` varchar(100) DEFAULT NULL,
  `kode_klasifikasi` varchar(50) DEFAULT NULL,
  `nama_klasifikasi` varchar(150) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  PRIMARY KEY (`klasifikasi_id`),
  UNIQUE KEY `kode_klasifikasi` (`kode_klasifikasi`),
  UNIQUE KEY `uq_klasifikasi_sumber_kunci` (`sumber_kunci`),
  KEY `idx_klasifikasi_parent_urutan` (`parent_id`,`urutan`),
  CONSTRAINT `fk_klasifikasi_parent` FOREIGN KEY (`parent_id`) REFERENCES `klasifikasi_arsip` (`klasifikasi_id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2433 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `klasifikasi_arsip`
--

LOCK TABLES `klasifikasi_arsip` WRITE;
/*!40000 ALTER TABLE `klasifikasi_arsip` DISABLE KEYS */;
INSERT INTO `klasifikasi_arsip` VALUES (8,NULL,1,1,'KB','KB','KOMERSIALISASI KA',NULL),(9,8,2,2,'KB.100','KB.100','Perencanaan dan Pengembangan Komersialisasi Angkutan dan Non Angkutan',NULL),(10,9,3,3,'KB.101','KB.101','Permintaan Kebutuhan Fasilitas Pendukung dalam Kegiatan Komersialisasi di Perusahaan',NULL),(11,9,3,4,'KB.102','KB.102','Pembuatan Desain Rencana Peningkatan Kegiatan Komersialisasi dan Pelayanan',NULL),(12,9,3,5,'KB.103','KB.103','Perizinan dalam Kegiatan Komersialisasi dan Pelayanan',NULL),(13,8,2,6,'KB.200','KB.200','Pelaksanaan Komersialisasi Angkutan dan Non Angkutan',NULL),(14,13,3,7,'KB.201','KB.201','Proses Rehabilitasi, Perawatan, Perbaikan, Modernisasi, dan Normalisasi yang Berhubungan dengan Sistem atau Prosedur yang digunakan dalam Kegiatan Kom',NULL),(15,13,3,8,'KB.202','KB.202','Persyaratan Angkutan baik Penumpang (Tiket, SAP/SAD, KAD, Railcard, dll) maupun Barang',NULL),(16,13,3,9,'KB.203','KB.203','Komersialisasi Angkutan Penumpang dan Barang (Tarif dan Angkutan)',NULL),(17,13,3,10,'KB.204','KB.204','Sewa atau KSO Kereta/Gerbong/Space Iklan/On Train TV/Designer Shop yang ditangani Unit Komersial',NULL),(18,13,3,11,'KB.205','KB.205','Sewa atau KSO Tanah/Bangunan Dinas/Rumah Dinas/Jalur KA/Sepur Simpang/Space Iklan yang Ditangani Unit Komersial',NULL),(19,13,3,12,'KB.206','KB.206','Asuransi dalam Kegiatan Komersialisasi',NULL),(20,13,3,13,'KB.207','KB.207','Pelayanan Terhadap Pelanggan baik Angkutan maupun Non Angkutan',NULL),(21,8,2,14,'KB.300','KB.300','Pengawasan dan Evaluasi Komersialisasi Angkutan dan Non Angkutan',NULL),(22,21,3,15,'KB.301','KB.301','Pemeriksaan, Pengujian, dan Percobaan Sistem atau Prosedur yang digunakan dalam Kegiatan Komersialisasi',NULL),(23,21,3,16,'KB.302','KB.302','Pengawasan Terhadap Pihak Lain yang berhubungan dengan Pelayanan Komersialisasi',NULL),(24,21,3,17,'KB.303','KB.303','Saran/Pengaduan Pelayanan dari Pelanggan maupun Stakeholder KA Lainnya',NULL),(25,NULL,1,18,'KT','KT','OPERASIONAL KA',NULL),(26,25,2,19,'KT.100','KT.100','Penyusunan Program Kebijakan Operasional KA',NULL),(27,26,3,20,'KT.101','KT.101','Perijinan Kegiatan Operasional KA',NULL),(28,26,3,21,'KT.102','KT.102','Kebutuhan Fasilitas dalam Kegiatan Operasional',NULL),(29,26,3,22,'KT.103','KT.103','Nama, Singkatan, Jenis, dan Klasifikasi Stasiun untuk Kegiatan Pengoperasian KA',NULL),(30,25,2,23,'KT.200','KT.200','Pelaksanaan Operasi KA',NULL),(31,30,3,24,'KT.201','KT.201','Pengoperasian Angkutan Barang',NULL),(32,30,3,25,'KT.202','KT.202','Pengoperasian Angkutan Penumpang',NULL),(33,30,3,26,'KT.203','KT.203','Administrasi yang ada di Stasiun meliputi Kegiatan Kedinasan dan Operasional yang Berkaitan dengan Pengoperasian KA dll',NULL),(34,30,3,27,'KT.204','KT.204','Pedoman Perjalanan KA, WAM, Gapeka, dan Malka (Persilangan, Penyusulan, Jadwal/Time Table/Route/Formasi) yang Terkait dengan Operasi KA)',NULL),(35,30,3,28,'KT.205','KT.205','Peluncuran dan Pembatalan Perjalanan KA yang Berhubungan dengan Pengoperasian KA',NULL),(36,30,3,29,'KT.206','KT.206','Crew yang Ada di KA yang Terkait Langsung dengan Operasional KA',NULL),(37,25,2,30,'KT.300','KT.300','Evaluasi Operasional KA',NULL),(38,37,3,31,'KT.301','KT.301','Aktifitas/Pengawasan Terhadap Pihak Lain yang Berhubungan dengan Operasional KA (Bina Mitra)',NULL),(39,37,3,32,'KT.302','KT.302','Pengaduan/Saran Mengenai Operasional KA',NULL),(40,37,3,33,'KT.303','KT.303','Laporan Gangguan, Kecelakaan, dan PLH yang Terjadi dalam Pengoperasian KA',NULL),(41,NULL,1,34,'KI','KI','PRASARANA KA',NULL),(42,41,2,35,'KI.100','KI.100','Pengembangan Prasarana KA',NULL),(43,42,3,36,'KI.101','KI.101','Kebutuhan Fasilitas dan Suku Cadang Prasarana',NULL),(44,42,3,37,'KI.102','KI.102','Perizinan dalam Penggunaan Prasarana KA (Izin Pembangunan Jalan Rel, Jembatan, Perawatan Sinyal, Telekomunikasi, Listrik Aliran Atas, Stasiun)',NULL),(45,42,3,38,'KI.103','KI.103','Jenis dan Klasifikasi Stasiun yang Berkaitan dengan Bidang Prasarana',NULL),(46,42,3,39,'KI.104','KI.104','Persyaratan Teknis Penggunaan Prasarana KA',NULL),(47,42,3,40,'KI.105','KI.105','Pembuatan Desain Rencana Pemanfaatan Prasarana KA (Perencanaan Perawatan dan Pembangunan Jalan Rel, Jembatan, Perawatan Sinyal, Telekomunikasi, Listri',NULL),(48,42,3,41,'KI.106','KI.106','Gambar/Konstruksi Bidang Prasarana',NULL),(49,41,2,42,'KI.200','KI.200','Pemeliharaan dan pembangunan prasarana KA',NULL),(50,49,3,43,'KI.201','KI.201','Semboyan Prasarana',NULL),(51,49,3,44,'KI.202','KI.202','Semboyan/Tanda Lalu Lintas di Jalan Umum',NULL),(52,49,3,45,'KI.203','KI.203','Jenis Mesin/Perkakas yang digunakan Pada Pekerjaan Bidang Prasarana',NULL),(53,49,3,46,'KI.204','KI.204','Material/suku cadang bidang prasarana',NULL),(54,49,3,47,'KI.205','KI.205','Pengoperasian Rehabilitasi, Perawatan, Perbaikan, Modernisasi, Normalisasi, bandasi dan Standarisasi Prasarana',NULL),(55,49,3,48,'KI.206','KI.206','Gangguan/Kerusakan Prasarana',NULL),(56,49,3,49,'KI.207','KI.207','Perlintasan (Petugas, Fly Over, Underpass, dll), Jalan Biasa yang Berhubungan dengan Jalan Rel KA',NULL),(57,49,3,50,'KI.208','KI.208','Bangunan Resmi/liar Seperti Batas Sungai, Selokan, Got, Pintu Air, Bendungan, Pelabuhan, dll',NULL),(58,49,3,51,'KI.209','KI.209','Sistem Pengairan/Sanitasi',NULL),(59,49,3,52,'KI.210','KI.210','Viaduct, Terowongan, Flyover, dan Underpass',NULL),(60,49,3,53,'KI.211','KI.211','Andas Fondasi/Profil/Konstruksi Bidang Prasarana',NULL),(61,49,3,54,'KI.212','KI.212','Pembangkit, Transformasi, Distribusi Listrik, Laa, Instalasi Listrik dan Sistem Kelistrikan',NULL),(62,49,3,55,'KI.213','KI.213','Sistem Persinyalan/NX',NULL),(63,49,3,56,'KI.214','KI.214','Alat Komunikasi Seperti Telepon, HT, Fax, Telex, Server, Jaringan, dll',NULL),(64,41,2,57,'KI.300','KI.300','Evaluasi Prasarana',NULL),(65,64,3,58,'KI.301','KI.301','Bina Mitra',NULL),(66,64,3,59,'KI.302','KI.302','Pemeriksaan, Pengukuran, Pengujian, Kalibrasi dan Sertifikasi Prasarana KA (Sertifikat Uji Pertama dan Uji Berkala Jalan Rel, Jembatan, Perawatan Siny',NULL),(67,NULL,1,60,'KR','KR','SARANA KA',NULL),(68,67,2,61,'KR.100','KR.100','Pengembangan Pengoperasian Sarana',NULL),(69,68,3,62,'KR.101','KR.101','Kebutuhan Fasilitas, Material Suku Cadang, Suku Cadang, Komponen, Bahan Bakar, Pelumas, dil',NULL),(70,68,3,63,'KR.102','KR.102','Type/Jenis Lok/Kereta/Gerbong',NULL),(71,68,3,64,'KR.103','KR.103','Persyaratan Teknis dalam Penggunaan Sarana',NULL),(72,68,3,65,'KR.104','KR.104','Pembuatan Desain dalam Kegiatan Pekerjaan Sarana KA',NULL),(73,68,3,66,'KR.105','KR.105','Perizinan Pengoperasian Sarana',NULL),(74,68,3,67,'KR.106','KR.106','Gambar/Konstruksi Sarana',NULL),(75,68,3,68,'KR.107','KR.107','Jenis Mesin/Perkakas Sarana',NULL),(76,67,2,69,'KR.200','KR.200','Pemeliharaan dan Pengoperasian Sarana',NULL),(77,76,3,70,'KR.201','KR.201','Gangguan dan Kerusakan Sarana',NULL),(78,76,3,71,'KR.202','KR.202','Suku Cadang Sarana',NULL),(79,76,3,72,'KR.203','KR.203','Rehabilitasi, Perawatan, Perbaikan, Modernisasi, Normalisasi, Standarisasi, Modifikasi dan Penyehatan Sarana',NULL),(80,76,3,73,'KR.204','KR.204','Asuransi Sarana (Lokomotif, Kereta, Gerbong, dll)',NULL),(81,76,3,74,'KR.206','KR.206','Sertifikat/Breveet Sarana',NULL),(82,76,3,75,'KR.207','KR.207','Pengoperasian dan Penggunaan Sarana (Lok/Kereta/Gerbong), Lokrit/Bordesrit',NULL),(83,76,3,76,'KR.209','KR.209','Peralatan Mekanik (HTT, MTT, Kereta Ukur, Crane)',NULL),(84,76,3,77,'KR.211','KR.211','Pemasukan/Penyerahan/Pencabutan/Mutasi/ Penggunaan Sarana',NULL),(85,76,3,78,'KR.212','KR.212','Penomoran Sarana',NULL),(86,76,3,79,'KR.213','KR.213','Bahan Bakar/Pelumas (Jenis, Standar Harga, Pemakaian) Sarana',NULL),(87,67,2,80,'KR.300','KR.300','Pengawasan dan Evaluasi Sarana',NULL),(88,87,3,81,'KR.301','KR.301','Pemeriksaan, Pengujian, Percobaan, dan Kalibrasi Sarana yang digunakan',NULL),(89,87,3,82,'KR.302','KR.302','Bina Mitra yang ada Dibidang Sarana',NULL),(90,NULL,1,83,'KS','KS','KESELAMATAN, KEAMANAN, DAN HYGIENE KA',NULL),(91,90,2,84,'KS.100','KS.100','Kebijakan Keselamatan, Keamanan dan Kesehatan Lingkungan Kerja',NULL),(92,91,3,85,'KS.101','KS.101','Persyaratan Teknis/Survai dan Desain yang Terkait dengan Keselamatan/Keamanan/Kesehatan Lingkungan Kerja',NULL),(93,91,3,86,'KS.102','KS.102','Sistem, Prosedur, SDM, Penjaminan Mutu dan Alat-Alat Keselamatan dan Pengamanan (Taspat) yang Tekait dengan Keselamatan/Keamanan/Kesehatan Lingkungan ',NULL),(94,91,3,87,'KS.103','KS.103','Kebutuhan Fasilitas Pendukung Keselamatan/Keamanan/Kesehatan Lingkungan Kerja',NULL),(95,91,3,88,'KS.104','KS.104','Perijinan yang Terkait dengan Keselamatan/Keamanan/Kesehatan Lingkungan Kerja',NULL),(96,91,3,89,'KS.105','KS.105','Studi Evaluasi Lingkungan/Amdal/Semdal yang Terkait dengan Keselamatan/Keamanan/Kesehatan Lingkungan Kerja',NULL),(97,90,2,90,'KS.200','KS.200','Pelaksanaan Kebijakan Keselamatan, Keamanan dan Kesehatan Lingkungan Kerja',NULL),(98,97,3,91,'KS.201','KS.201','Rehabilitasi/Perawatan/Perbaikan/Modernisasi/ Normalisasi Sistem/Alat Keselamatan/Keamanan/Kesehatan Lingkungan Kerja',NULL),(99,97,3,92,'KS.202','KS.202','Pemeriksaan/Pengujian/Percobaan yang Terkait dengan Keselamatan/Keamanan/Kesehatan Lingkungan Kerja',NULL),(100,90,2,93,'KS.300','KS.300','Evaluasi Kebijakan Keselamatan, Keamanan dan Kesehatan Lingkungan Kerja',NULL),(101,100,3,94,'KS.301','KS.301','Bina Mitra (TNI, Polri, Security Service, Rumah Sakit dll) yang Terkait dengan Keselamatan/Keamanan/Kesehatan Lingkungan Kerja',NULL),(102,NULL,1,95,'FU','FU','NGSI FASILITATIF',NULL),(103,NULL,1,96,'KH','KH','KEPEGAWAIAN',NULL),(104,103,2,97,'KH.100','KH.100','Pengadaan SDM',NULL),(105,104,3,98,'KH.101','KH.101','Kebutuhan pegawai yang diperlukan oleh perusahaan (Usulan dari unit kerja, permintaan formasi, penetapan formasi)',NULL),(106,104,3,99,'KH.102','KH.102','Rekruitmen pegawai (Proses penerimaan, seleksi, dan penetapan kelulusan)',NULL),(107,104,3,100,'KH.103','KH.103','Berkas lamaran yang tidak diterima',NULL),(108,104,3,101,'KH.104','KH.104','Pembinaan karir pegawai (Pola karir, talent mapping, dll)',NULL),(109,103,2,102,'KH.200','KH.200','Administrasi Pengelolaan Pegawai',NULL),(110,109,3,103,'KH.201','KH.201','Daftar hadir dan cuti seluruh pegawai',NULL),(111,109,3,104,'KH.202','KH.202','Perijinan, dan dispensasi seluruh pegawai',NULL),(112,109,3,105,'KH.203','KH.203','Data Pegawai (laporan pajak penghasilan pribadi, keanggotaan organisasi profesi)',NULL),(113,109,3,106,'KH.204','KH.204','Surat perintah dinas/ surat tugas',NULL),(114,103,2,107,'KH.300','KH.300','Mutasi Pegawai',NULL),(115,114,3,108,'KH.301','KH.301','Pengangkatan Menjadi Pegawai',NULL),(116,114,3,109,'KH.302','KH.302','Pelantikan dan Serah Terima Jabatan',NULL),(117,114,3,110,'KH.303','KH.303','Kenaikan Pangkat',NULL),(118,114,3,111,'KH.304','KH.304','Pengangkatan dalam Jabatan',NULL),(119,114,3,112,'KH.305','KH.305','Ujian Dinas Seperti Fit And Proper Test, Assesment dll Beserta Sertifikatnya',NULL),(120,114,3,113,'KH.306','KH.306','Penempatan dan Pemindahan Pegawai',NULL),(121,114,3,114,'KH.307','KH.307','Pengunduran Diri Menjadi Pegawai Perusahaan',NULL),(122,114,3,115,'KH.308','KH.308','Pensiun Pegawai, BT, MPP, Daftar Penerima Calon Pensiun (DPCP), dan Penyesuaian Gaji Pokok',NULL),(123,114,3,116,'KH.309','KH.309','Pemberhentian dengan Hormat',NULL),(124,114,3,117,'KH.310','KH.310','Pemberhentian Tidak dengan Hormat',NULL),(125,114,3,118,'KH.311','KH.311','Pensiun Janda, Duda, Anak',NULL),(126,114,3,119,'KH.312','KH.312','Pensiun Meninggal Dunia',NULL),(127,114,3,120,'KH.313','KH.313','Penetapan Uang Tunggu dan Uang Muka Pensiun',NULL),(128,114,3,121,'KH.314','KH.314','Laporan Mutasi Keluarga',NULL),(129,128,4,122,'KH.314#a',NULL,'Surat izin pernikahan/perceraian',NULL),(130,128,4,123,'KH.314#b',NULL,'Surat penolakan izin pernikahan/perceraian',NULL),(131,128,4,124,'KH.314#c',NULL,'Surat nikah/cerai',NULL),(132,128,4,125,'KH.314#d',NULL,'Akte kelahiran anak',NULL),(133,128,4,126,'KH.314#e',NULL,'Akte keterangan adopsi anak',NULL),(134,128,4,127,'KH.314#f',NULL,'Surat keterangan meninggal dunia',NULL),(135,103,2,128,'KH.400','KH.400','Kesejahteraan Pegawai',NULL),(136,135,3,129,'KH.401','KH.401','Penggajian/kenaikan gaji berkala/tunjangan jabatan/daftar gaji',NULL),(137,135,3,130,'KH.402','KH.402','Uang perangsang/insentif/uang lembur',NULL),(138,135,3,131,'KH.403','KH.403','Reward/penghargaan yang diberikan perusahaan kepada pegawai',NULL),(139,135,3,132,'KH.404','KH.404','Koperasi pegawai, BKRKA, bantuan pegawai, dan SPKA',NULL),(140,135,3,133,'KH.405','KH.405','Jaminan kesehatan pegawai, asuransi, Taspen, Jiwasraya',NULL),(141,135,3,134,'KH.406','KH.406','Pembinaan jasmani dan rohani pegawai',NULL),(142,103,2,135,'KH.500','KH.500','Evaluasi SDM',NULL),(143,142,3,136,'KH.501','KH.501','Peninjauan masa kerja/jabatan pegawai',NULL),(144,142,3,137,'KH.502','KH.502','Impassing/penyesuaian ijasah',NULL),(145,142,3,138,'KH.503','KH.503','DP3/Simak',NULL),(146,142,3,139,'KH.504','KH.504','Pengaduan masalah pegawai',NULL),(147,142,3,140,'KH.505','KH.505','Rehabilitasi pegawai',NULL),(148,142,3,141,'KH.506','KH.506','Hukuman dinas seperti teguran, peringatan, penundaan gaji, dan pangkat',NULL),(149,103,2,142,'KH.600','KH.600','Berkas Perseorangan Pegawai',NULL),(150,149,3,143,'KH.601','KH.601','SK Cuti/Dispensasi',NULL),(151,149,3,144,'KH.602','KH.602','SK Hukuman Jabatan/Hukuman Disiplin Pegawai',NULL),(152,149,3,145,'KH.603','KH.603','SK Kenaikan Pangkat',NULL),(153,149,3,146,'KH.604','KH.604','SK Pemberhentian sebagai Pegawai',NULL),(154,149,3,147,'KH.605','KH.605','SK Pemberhentian dengan Hormat',NULL),(155,149,3,148,'KH.606','KH.606','SK Pemberhentian tidak dengan Hormat',NULL),(156,149,3,149,'KH.607','KH.607','SK Pemberian Uang Tunggu',NULL),(157,149,3,150,'KH.608','KH.608','SK Penempatan dan Pemindahan Pegawai',NULL),(158,149,3,151,'KH.609','KH.609','SK Pengangkatan dalam Jabatan',NULL),(159,149,3,152,'KH.610','KH.610','SK Pengangkatan Pegawai',NULL),(160,149,3,153,'KH.611','KH.611','SK Pengunduran Diri Menjadi Pegawai Perusahaan',NULL),(161,149,3,154,'KH.612','KH.612','SK Pensiun',NULL),(162,149,3,155,'KH.613','KH.613','SK Pensiun Janda, Duda, Anak',NULL),(163,149,3,156,'KH.614','KH.614','SK Pensiun Meninggal Dunia',NULL),(164,149,3,157,'KH.615','KH.615','SK Perpindahan Antar Instansi',NULL),(165,149,3,158,'KH.616','KH.616','SK Rekruitmen Pegawai',NULL),(166,149,3,159,'KH.617','KH.617','Kontrak Penetapan PKM dan Kelengkapannya',NULL),(167,149,3,160,'KH.618','KH.618','Kontrak Penetapan PKWT dan Kelengkapannya',NULL),(168,149,3,161,'KH.619','KH.619','Berkas perorangan lainnya :',NULL),(169,168,4,162,'KH.619#a',NULL,'Surat Pernyataan Melaksanakan Tugas/Menduduki Jabatan dan Surat Pernyataan Pelantikan',NULL),(170,168,4,163,'KH.619#b',NULL,'Surat Pernyataan Pengunduran Diri dari Jabatan Organik karena dicalonkan sebagai Kepala/Wakil Kepala daerah',NULL),(171,168,4,164,'KH.619#c',NULL,'Peninjauan masa kerja/jabatan pegawai',NULL),(172,168,4,165,'KH.619#d',NULL,'Berkas lamaran pegawai yang diterima',NULL),(173,168,4,166,'KH.619#e',NULL,'Hasil Pengujian Kesehatan',NULL),(174,168,4,167,'KH.619#f',NULL,'Berita Acara Pemeriksaan',NULL),(175,168,4,168,'KH.619#g',NULL,'Surat Perbaikan tanggal Tahun Kelahiran',NULL),(176,168,4,169,'KH.619#h',NULL,'Akta Nikah/Cerai',NULL),(177,168,4,170,'KH.619#i',NULL,'Akta Kelahiran',NULL),(178,168,4,171,'KH.619#j',NULL,'Surat Keterangan Mutasi Keluarga',NULL),(179,168,4,172,'KH.619#k',NULL,'Surat Keterangan Meninggal Dunia',NULL),(180,168,4,173,'KH.619#l',NULL,'Surat Keterangan Peningkatan Pendidikan',NULL),(181,168,4,174,'KH.619#m',NULL,'Surat Tugas/Izin Belajar Dalan/Luar Negeri',NULL),(182,168,4,175,'KH.619#n',NULL,'Surat Izin Bepergian ke Luar Negeri',NULL),(183,168,4,176,'KH.619#o',NULL,'Ijazah/Sertifikat',NULL),(184,168,4,177,'KH.619#p',NULL,'Ujian dinas seperti fit and proper Test, assesment dll',NULL),(185,NULL,1,178,'KA','KA','KEPEMILIKAN ASET',NULL),(186,185,2,179,'KA.100','KA.100','Pengelolaan Aset Non Produksi',NULL),(187,186,3,180,'KA.101','KA.101','Kebutuhan Fasilitas Pendukung yang Berkaitan dengan Kepemilikan Aset Tanah dan Bangunan',NULL),(188,186,3,181,'KA.102','KA.102','Persyaratan Teknis dalam Rencana Pengusahaan Aset Tanah dan Bangunan',NULL),(189,186,3,182,'KA.103','KA.103','Survai dan Desain Pengusahaan Aset Tanah dan Bangunan',NULL),(190,186,3,183,'KA.104','KA.104','Perijinan yang Berkaitan dengan Aset',NULL),(191,186,3,184,'KA.105','KA.105','Jenis atau Lokasi Aset',NULL),(192,186,3,185,'KA.106','KA.106','Heritage (Cagar Budaya)',NULL),(193,185,2,186,'KA.200','KA.200','Pemanfaatan Aset Non Produksi',NULL),(194,193,3,187,'KA.201','KA.201','Rehabilitasi, Perawatan, Perbaikan, Modernisasi, dan Normalisasi Aset Tanah dan Bangunan',NULL),(195,193,3,188,'KA.202','KA.202','Penyerahan Tanah atau Bangunan (Pemanfaatan) oleh Pihak Lain Kepada Perusahaan',NULL),(196,193,3,189,'KA.203','KA.203','Pembebasan, Penertiban, Pengosongan, Penutupan dan Pembukaan Aset Perusahaan',NULL),(197,193,3,190,'KA.204','KA.204','Legalitas Kepemilikan Seperti Grondkaart, Sertifikat, dll',NULL),(198,193,3,191,'KA.205','KA.205','Status Kepemilikan, Batas Tanah, Gedung dan Permasalahan Hukum Aset Seperti Sengketa Tanah dil',NULL),(199,185,2,192,'KA.300','KA.300','Pengawasan dan Evaluasi Aset Non Produksi',NULL),(200,199,3,193,'KA.301','KA.301','Pemeriksaan, Pengujian dan Percobaan Sistem atau Prosedur Kepemilikan Aset',NULL),(201,199,3,194,'KA.302','KA.302','Bina Mitra dan Hubungan dengan Pihak Lain dalam Kegiatan Kepemilikan Aset',NULL),(202,NULL,1,195,'KE','KE','DIKLAT',NULL),(203,202,2,196,'KE.100','KE.100','Perencanaan Diklat',NULL),(204,203,3,197,'KE.101','KE.101','Pedoman-Pedoman Kediklatan',NULL),(205,203,3,198,'KE.102','KE.102','Kurikulum/Sylabus/Modul Diklat',NULL),(206,203,3,199,'KE.103','KE.103','Permintaan Pengajar, Dosen atau Widyaswara untuk Proses Diklat',NULL),(207,203,3,200,'KE.104','KE.104','Panduan Fasilitator',NULL),(208,203,3,201,'KE.105','KE.105','Ijin Belajar, PKL, Praktek Kerja atau Magang di Diklat',NULL),(209,203,3,202,'KE.106','KE.106','Saran/Rekomendasi Penyelenggaraan Diklat',NULL),(210,203,3,203,'KE.107','KE.107','Akreditasi Lembaga Diklat',NULL),(211,203,3,204,'KE.108','KE.108','Sertifikasi',NULL),(212,202,2,205,'KE.200','KE.200','Pelaksanaan Diklat',NULL),(213,212,3,206,'KE.201','KE.201','Diklat Fungsional Kereta Api',NULL),(214,212,3,207,'KE.202','KE.202','Diklat Umum (Training, Seminar, Workshop)',NULL),(215,212,3,208,'KE.203','KE.203','Orientasi, Prajabatan dan Penataran',NULL),(216,212,3,209,'KE.204','KE.204','Bea Siswa yang Berprestasi di Perusahaan',NULL),(217,212,3,210,'KE.205','KE.205','Pendidikan Bergelar, Non Gelar, Sertifikasi dan Pelaporannya',NULL),(218,202,2,211,'KE.300','KE.300','Laporan dan Evaluasi Diklat',NULL),(219,218,3,212,'KE.301','KE.301','Laporan Penyelenggaraan Diklat',NULL),(220,218,3,213,'KE.302','KE.302','Evaluasi Penyelenggaraan Diklat',NULL),(221,218,3,214,'KE.303','KE.303','Evaluasi Alumni Pasca Diklat',NULL),(222,218,3,215,'KE.304','KE.304','Wisuda, Penutupan Diklat dan Pelaporan Serta Pengembalian Siswa Diklat',NULL),(223,NULL,1,216,'KL','KL','HUKUM',NULL),(224,223,2,217,'KL.100','KL.100','Rancangan Peraturan/Keputusan Perusahaan',NULL),(225,224,3,218,'KL.101','KL.101','Rancangan Peraturan Perusahaan',NULL),(226,224,3,219,'KL.102','KL.102','Rancangan Keputusan Perusahaan',NULL),(227,224,3,220,'KL.103','KL.103','Rancangan Instruksi/Maklumat/Edaran/Pengumuman',NULL),(228,224,3,221,'KL.104','KL.104','Rancangan Standar / Pedoman / Prosedur Kerja / Petunjuk Pelaksanaan / Petunjuk Teknis / TOR',NULL),(229,223,2,222,'KL.200','KL.200','Peraturan Perusahaan',NULL),(230,229,3,223,'KL.201','KL.201','Komersialisasi',NULL),(231,229,3,224,'KL.202','KL.202','Operasional',NULL),(232,229,3,225,'KL.203','KL.203','Prasarana',NULL),(233,229,3,226,'KL.204','KL.204','Sarana',NULL),(234,229,3,227,'KL.205','KL.205','Keselamatan, Keamanan, dan Hygiene',NULL),(235,229,3,228,'KL.206','KL.206','Kepegawaian',NULL),(236,229,3,229,'KL.207','KL.207','Kepemilikan Aset',NULL),(237,229,3,230,'KL.208','KL.208','Diklat',NULL),(238,229,3,231,'KL.209','KL.209','Hukum',NULL),(239,229,3,232,'KL.210','KL.210','Kerjasama',NULL),(240,229,3,233,'KL.211','KL.211','Organisasi dan Tata Laksana',NULL),(241,229,3,234,'KL.212','KL.212','Dokumen',NULL),(242,229,3,235,'KL.213','KL.213','Perlengkapan dan Pelelangan',NULL),(243,229,3,236,'KL.214','KL.214','Perencanaan',NULL),(244,229,3,237,'KL.215','KL.215','Pengawasan',NULL),(245,229,3,238,'KL.216','KL.216','Umum',NULL),(246,229,3,239,'KL.217','KL.217','Keuangan',NULL),(247,229,3,240,'KL.218','KL.218','Sistem Informasi',NULL),(248,229,3,241,'KL.219','KL.219','Logistik',NULL),(249,223,2,242,'KL.300','KL.300','Keputusan Direksi, Pimpinan Unit, Kepala Daerah',NULL),(250,249,3,243,'KL.301','KL.301','Komersialisasi',NULL),(251,249,3,244,'KL.302','KL.302','Operasional',NULL),(252,249,3,245,'KL.303','KL.303','Prasarana',NULL),(253,249,3,246,'KL.304','KL.304','Sarana',NULL),(254,249,3,247,'KL.305','KL.305','Keselamatan, Keamanan, dan Hygiene',NULL),(255,249,3,248,'KL.306','KL.306','Kepegawaian',NULL),(256,249,3,249,'KL.307','KL.307','Kepemilikan Aset',NULL),(257,249,3,250,'KL.308','KL.308','Diklat',NULL),(258,249,3,251,'KL.309','KL.309','Hukum',NULL),(259,249,3,252,'KL.310','KL.310','Kerjasama',NULL),(260,249,3,253,'KL.311','KL.311','Organisasi dan Tata Laksana',NULL),(261,249,3,254,'KL.312','KL.312','Dokumen',NULL),(262,249,3,255,'KL.313','KL.313','Perlengkapan dan Pelelangan',NULL),(263,249,3,256,'KL.314','KL.314','Perencanaan',NULL),(264,249,3,257,'KL.315','KL.315','Pengawasan',NULL),(265,249,3,258,'KL.316','KL.316','Umum',NULL),(266,249,3,259,'KL.317','KL.317','Keuangan',NULL),(267,249,3,260,'KL.318','KL.318','Sistem Informasi',NULL),(268,249,3,261,'KL.319','KL.319','Logistik',NULL),(269,223,2,262,'KL.400','KL.400','Instruksi/Maklumat/Edaran/Pengumuman',NULL),(270,269,3,263,'KL.401','KL.401','Instruksi',NULL),(271,269,3,264,'KL.402','KL.402','Maklumat',NULL),(272,269,3,265,'KL.403','KL.403','Edaran',NULL),(273,269,3,266,'KL.404','KL.404','Edaran Bersama',NULL),(274,269,3,267,'KL.405','KL.405','Pengumuman',NULL),(275,223,2,268,'KL.500','KL.500','Surat Perintah / Kuasa',NULL),(276,275,3,269,'KL.501','KL.501','Surat Perintah Direksi / Pimpinan Unit / Kepala Daerah',NULL),(277,275,3,270,'KL.502','KL.502','Surat Perintah Pelaksanaan Tugas dan Datasering',NULL),(278,275,3,271,'KL.503','KL.503','Surat Kuasa Direksi / Pimpinan Unit',NULL),(279,275,3,272,'KL.504','KL.504','Surat Pernyataan',NULL),(280,275,3,273,'KL.505','KL.505','Surat Keterangan',NULL),(281,223,2,274,'KL.600','KL.600','Standar / Pedoman / Prosedur Kerja / Petunjuk Pelaksanaan / Petunjuk Teknis / TOR',NULL),(282,281,3,275,'KL.601','KL.601','Komersialisasi',NULL),(283,281,3,276,'KL.602','KL.602','Operasional',NULL),(284,281,3,277,'KL.603','KL.603','Prasarana',NULL),(285,281,3,278,'KL.604','KL.604','Sarana',NULL),(286,281,3,279,'KL.605','KL.605','Keselamatan, Keamanan, dan Hygiene',NULL),(287,281,3,280,'KL.606','KL.606','Kepegawaian',NULL),(288,281,3,281,'KL.607','KL.607','Kepemilikan Aset',NULL),(289,281,3,282,'KL.608','KL.608','Diklat',NULL),(290,281,3,283,'KL.609','KL.609','Hukum',NULL),(291,281,3,284,'KL.610','KL.610','Kerjasama',NULL),(292,281,3,285,'KL.611','KL.611','Organisasi dan Tata Laksana',NULL),(293,281,3,286,'KL.612','KL.612','Dokumen',NULL),(294,281,3,287,'KL.613','KL.613','Perlengkapan dan Pelelangan',NULL),(295,281,3,288,'KL.614','KL.614','Perencanaan',NULL),(296,281,3,289,'KL.615','KL.615','Pengawasan',NULL),(297,281,3,290,'KL.616','KL.616','Umum',NULL),(298,281,3,291,'KL.617','KL.617','Keuangan',NULL),(299,281,3,292,'KL.618','KL.618','Sistem Informasi',NULL),(300,281,3,293,'KL.619','KL.619','Logistik',NULL),(301,223,2,294,'KL.700','KL.700','Nota Kesepahaman/MoU/Kontrak/Perjanjian/ Konsesi/Berita Acara',NULL),(302,301,3,295,'KL.701','KL.701','Kontrak Pendapatan',NULL),(303,301,3,296,'KL.702','KL.702','Kontrak Biaya',NULL),(304,301,3,297,'KL.703','KL.703','Piagam Kesepakatan Bersama (Mou)',NULL),(305,301,3,298,'KL.704','KL.704','Akta',NULL),(306,301,3,299,'KL.705','KL.705','Perjanjian Kerjasama',NULL),(307,301,3,300,'KL.706','KL.706','Pengakhiran Perjanjian Kerjasama',NULL),(308,301,3,301,'KL.707','KL.707','Berita Acara Kontrak dan Perikatan',NULL),(309,301,3,302,'KL.708','KL.708','Konsesi',NULL),(310,301,3,303,'KL.709','KL.709','Perijinan',NULL),(311,301,3,304,'KL.710','KL.710','Dispensasi',NULL),(312,301,3,305,'KL.711','KL.711','Lisensi',NULL),(313,301,3,306,'KL.712','KL.712','Rekomendasi',NULL),(314,301,3,307,'KL.713','KL.713','Wewenang',NULL),(315,301,3,308,'KL.714','KL.714','Recognition',NULL),(316,301,3,309,'KL.715','KL.715','Hak Atas Kekayaan Intelektual',NULL),(317,223,2,310,'KL.800','KL.800','Dokumentasi/Sosialisasi/Bantuan/Konsultasi/ Advokasi Hukum',NULL),(318,317,3,311,'KL.801','KL.801','Undang-Undang, Peraturan Pemerintah, Keputusan Presiden dan Peraturan- Peraturan yang Dijadikan Referensi',NULL),(319,317,3,312,'KL.802','KL.802','Sosialisasi/Penyuluhan/Pembinaan Hukum',NULL),(320,317,3,313,'KL.803','KL.803','Bantuan/Konsultasi/Advokasi Hukum',NULL),(321,317,3,314,'KL.804','KL.804','Panggilan Saksi, Sidang dan Laporan Hasil Sidang',NULL),(322,223,2,315,'KL.900','KL.900','Kasus / Sengketa Hukum',NULL),(323,322,3,316,'KL.901','KL.901','Pidana',NULL),(324,322,3,317,'KL.902','KL.902','Perdata',NULL),(325,322,3,318,'KL.903','KL.903','Tata Usaha Negara',NULL),(326,322,3,319,'KL.904','KL.904','Ketenagakerjaan',NULL),(327,322,3,320,'KL.905','KL.905','Arbitrase',NULL),(328,322,3,321,'KL.906','KL.906','Sengketa Adat',NULL),(329,NULL,1,322,'KJ','KJ','KERJASAMA',NULL),(330,329,2,323,'KJ.100','KJ.100','Perencanaan Program Kerja Sama',NULL),(331,330,3,324,'KJ.101','KJ.101','Penawaran Kerjasama dengan Perusahaan',NULL),(332,330,3,325,'KJ.102','KJ.102','Perijinan Kerja Sama',NULL),(333,329,2,326,'KJ.200','KJ.200','Pelaksanaan Pengawasan dan Evaluasi Program Kerja Sama',NULL),(334,333,3,327,'KJ.201','KJ.201','Bidang Manajemen Seperti Keuangan, Kepegawaian dan IT, Pemasaran dan Aset',NULL),(335,333,3,328,'KJ.203','KJ.203','Kerja Sama Bidang Prasarana Seperti Jalan, Bangunan dan Sintelis',NULL),(336,333,3,329,'KJ.204','KJ.204','Kerja Sama Sarana Seperti Lok dan Kereta',NULL),(337,333,3,330,'KJ.205','KJ.205','Kerja Sama di Bidang Keuangan Seperti Sindikasi Pembiayaan, Kmk dll',NULL),(338,333,3,331,'KJ.206','KJ.206','Kerja Sama Bidang Operasional KA',NULL),(339,333,3,332,'KJ.207','KJ.207','Kerja Sama Bidang Pendidikan',NULL),(340,333,3,333,'KJ.208','KJ.208','Kerja Sama Bidang Keamanan dan Keselamatan',NULL),(341,333,3,334,'KJ.209','KJ.209','Kerjasama dengan Organisasi Pemerintah atau Non Profit',NULL),(342,333,3,335,'KJ.210','KJ.210','Kerjasama dengan Organisasi Bisnis',NULL),(343,333,3,336,'KJ.211','KJ.211','Laporan Hasil Kerjasama dengan Perusahaan',NULL),(344,NULL,1,337,'KO','KO','ORGANISASI DAN TATA LAKSANA',NULL),(345,344,3,338,'KO.101','KO.101','Pembentukan',NULL),(346,344,3,339,'KO.102','KO.102','Pengubahan',NULL),(347,344,3,340,'KO.103','KO.103','Pembubaran',NULL),(348,344,3,341,'KO.104','KO.104','Uraian Jabatan dan Tata Laksana',NULL),(349,344,3,342,'KO.105','KO.105','Evaluasi Organisasi',NULL),(350,344,3,343,'KO.106','KO.106','Perangkat Organisasi (Identitas Perusahaan, Logo, Lagu, Visi dan Misi, dll)',NULL),(351,NULL,1,344,'KD','KD','PENGENDALIAN DOKUMENΓÇ¥',NULL),(352,351,2,345,'KD.100','KD.100','Tata Naskah, Produksi, Reproduksi Dokumen dalam Perusahaan',NULL),(353,352,3,346,'KD.101','KD.101','Penyimpanan dan Pemeliharaan Arsip',NULL),(354,352,3,347,'KD.102','KD.102','Persetujuan Jra',NULL),(355,352,3,348,'KD.103','KD.103','Layanan Dokumen (Penerimaan, Peminjaman, Pengembalian, dan Penggunaan Dokumen)',NULL),(356,352,3,349,'KD.104','KD.104','Penyusutan Arsip',NULL),(357,352,3,350,'KD.105','KD.105','Pembinaan Kearsipan (Sosialisasi, Bimbingan, Supervisi, dan Monitoring)',NULL),(358,352,3,351,'KD.106','KD.106','Perpustakaan (Bibliotik/Bahan Pustaka)',NULL),(359,NULL,1,352,'KM','KM','PERLENGKAPAN DAN PELELANGAN',NULL),(360,359,2,353,'KM.100','KM.100','Proses Pelelangan',NULL),(361,360,3,354,'KM.101','KM.101','Analisa Data (Klasifikasi, Kebutuhan,Penawaran,Harga, Spesifikasi dll) dalam Perlengkapan dan Proses Pelelangan Perusahaan',NULL),(362,360,3,355,'KM.102','KM.102','Perizinan dalam Perlengkapan dan Proses Pelelangan Perusahaan',NULL),(363,360,3,356,'KM.103','KM.103','Persyaratan Teknis dalam Perlengkapan dan Proses Pelelangan Perusahaan',NULL),(364,360,3,357,'KM.104','KM.104','Inventarisasi & Standarisasi Barang Bergerak/Tidak Bergerak dalam Perlengkapan dan Proses Pelelangan Perusahaan',NULL),(365,360,3,358,'KM.105','KM.105','Penghapusan (Usulan, Persetujuan, Pelaksanaan Penjualan, Penghibahan, Pelelangan) dalam Perlengkapan dan Proses Pelelangan Perusahaan',NULL),(366,359,2,359,'KM.200','KM.200','Kegiatan Penggunaan Perlengkapan dan Proses Pelelangan',NULL),(367,366,3,360,'KM.201','KM.201','Tender, Kontrak yang Terkait dalam Perlengkapan dan Proses Pelelangan Perusahaan',NULL),(368,366,3,361,'KM.202','KM.202','Penggunaan Perlengkapan dan Proses Pelelangan Perusahaan',NULL),(369,366,3,362,'KM.203','KM.203','Pergudangan (Distribusi/Penerimaan/Rehabilitasi/Pemeliharaan) dalam Perlengkapan dan Proses Pelelangan Perusahaan',NULL),(370,359,2,363,'KM.300','KM.300','Pengawasan dan Evaluasi Penggunaan Perlengkapan dan Pelelangan',NULL),(371,370,3,364,'KM.301','KM.301','Administrasi/Laporan Kinerja dalam Perlengkapan dan Proses Pelelangan Perusahaan',NULL),(372,370,3,365,'KM.302','KM.302','Pembinaan (Klaim/Komplain) dalam Penggunaan Perlengkapan dan Proses Pelelangan',NULL),(373,NULL,1,366,'KP','KP','PERENCANAAN PERUSAHAAN',NULL),(374,373,2,367,'KP.100','KP.100','Kebijakan dan Strategi Perusahaan',NULL),(375,374,3,368,'KP.101','KP.101','Usulan dan Perubahan Rencana Jangka Panjang Perusahaan (Rjpp)',NULL),(376,374,3,369,'KP.102','KP.102','Rencana Jangka Panjang Perusahaan (Rjpp)',NULL),(377,374,3,370,'KP.103','KP.103','Rencana Proyek dengan Pihak Eksternal',NULL),(378,373,2,371,'KP.200','KP.200','Program Kerja',NULL),(379,378,3,372,'KP.201','KP.201','Usulan Program Kerja Beserta Data Pendukung',NULL),(380,378,3,373,'KP.202','KP.202','Program Kerja Perusahaan',NULL),(381,378,3,374,'KP.203','KP.203','Program Kerja Daerah dan Unit',NULL),(382,373,2,375,'KP.300','KP.300','Penetapan/Kontrak Management',NULL),(383,382,3,376,'KP.301','KP.301','Kontrak Management / BoD dengan Pemegang Saham',NULL),(384,382,3,377,'KP.302','KP.302','Kontrak Management / BoD dengan Pimpinan Unit/Daerah',NULL),(385,382,3,378,'KP.303','KP.303','Kontrak Management / BoD dengan Anak Perusahaan',NULL),(386,373,2,379,'KP.400','KP.400','Laporan',NULL),(387,386,3,380,'KP.401','KP.401','Laporan Berkala (termasuk KPI) Unit Kerja/Direktorat',NULL),(388,386,3,381,'KP.402','KP.402','Laporan /Kinerja Tahunan Perusahaan',NULL),(389,386,3,382,'KP.403','KP.403','Laporan Insidental',NULL),(390,373,2,383,'KP.500','KP.500','Evaluasi Pelaksanaan Program',NULL),(391,390,3,384,'KP.501','KP.501','Evaluasi Pelaksanaan Program Unit Kerja',NULL),(392,390,3,385,'KP.502','KP.502','Evaluasi Pelaksanaan Program Perusahaan',NULL),(393,390,3,386,'KP.503','KP.503','Evaluasi Pelaksanaan Program Insidental',NULL),(394,NULL,1,387,'KC','KC','PENGAWASAN / PEMERIKSAAN',NULL),(395,394,2,388,'KC.100','KC.100','Rencana Pengawasan/Pemeriksaan',NULL),(396,395,3,389,'KC.101','KC.101','Rencana Strategis Pengawasan/Pemeriksaan',NULL),(397,395,3,390,'KC.102','KC.102','Rencana Tahunan Pengawasan/Pemeriksaan',NULL),(398,394,2,391,'KC.200','KC.200','Pelaksanaan Pengawasan/Pemeriksaan',NULL),(399,398,3,392,'KC.201','KC.201','Tindak lanjut atas Laporan Hasil Audit (LHA), Laporan Hasil Pemeriksaan (LHP), Laporan Hasil Pemeriksaan Operasional (LHPO), Laporan Hasil Evaluasi (L',NULL),(400,398,3,393,'KC.202','KC.202','Good Corporate Governance (GCG)',NULL),(401,398,3,394,'KC.203','KC.203','Pengawasan/pemeriksaan/penjaminan mutu kepegawaian',NULL),(402,398,3,395,'KC.204','KC.204','Pengawasan/pemeriksaan/penjaminan mutu sarana',NULL),(403,398,3,396,'KC.205','KC.205','Pengawasan/pemeriksaan/penjaminan mutu prasarana',NULL),(404,398,3,397,'KC.206','KC.206','Pengawasan/pemeriksaan/penjaminan mutu proyek pembangunan',NULL),(405,398,3,398,'KC.207','KC.207','Pengawasan/pemeriksaan/penjaminan mutu keselamatan KA',NULL),(406,398,3,399,'KC.208','KC.208','Pengawasan/pemeriksaan/penjaminan mutu umum/khusus/investigasi',NULL),(407,398,3,400,'KC.209','KC.209','Pengawasan/pemeriksaan/penjaminan mutu operasional KA/PLH',NULL),(408,394,2,401,'KC.300','KC.300','Laporan pengawasan/pemeriksaan',NULL),(409,408,3,402,'KC.301','KC.301','Hasil pengawasan/pemeriksaan/penjaminan mutu (audit, CO, dll)',NULL),(410,409,4,403,'KC.301#a',NULL,'Laporan Hasil Audit (LHA), Laporan Hasil Pemeriksaan (LHP), Laporan Hasil Pemeriksaan Operasional (LHPO), Laporan Hasil Evaluasi (LHE), Laporan Akunta',NULL),(411,409,4,404,'KC.301#b',NULL,'Laporan Hasil Audit (LHA), Laporan Hasil Pemeriksaan (LHP), Laporan Hasil Pemeriksaan Operasional (LHPO), Laporan Hasil Evaluasi (LHE), Laporan Akunta',NULL),(412,409,4,405,'KC.301#c',NULL,'Laporan Hasil Audit Investigasi (LHAI) yang mengandung unsur Tindak Pidana Korupsi (TPK) dan memerlukan tindak lanjut',NULL),(413,409,4,406,'KC.301#d',NULL,'Laporan Perkembangan Penanganan Surat Pengaduan masyarakat',NULL),(414,409,4,407,'KC.301#e',NULL,'Laporan Pemutakhiran Data',NULL),(415,409,4,408,'KC.301#f',NULL,'Laporan Perkembangan Barang Milik Negara',NULL),(416,409,4,409,'KC.301#g',NULL,'Laporan Kegiatan Pendampingan Penyusunan Laporan Keuangan dan Reviu',NULL),(417,408,3,410,'KC.302','KC.302','Laporan tindak lanjut atas Laporan Hasil Audit (LHA), Laporan Hasil Pemeriksaan (LHP), Laporan Hasil Pemeriksaan Operasional (LHPO), Laporan Hasil Eva',NULL),(418,NULL,1,411,'KG','KG','GENERAL AFFAIR DAN HUMAS',NULL),(419,418,2,412,'KG.100','KG.100','General Affair',NULL),(420,419,3,413,'KG.101','KG.101','Administrasi Telekomunikasi Perusahaan',NULL),(421,419,3,414,'KG.102','KG.102','Perjalanan Dinas (Passport, Visa, Fiskal, dll)',NULL),(422,419,3,415,'KG.103','KG.103','Administrasi Penggunaan Fasilitas Perusahaan',NULL),(423,419,3,416,'KG.104','KG.104','Risalah/Notulen Rapat',NULL),(424,419,3,417,'KG.105','KG.105','Risalah/Notulen Rapat Direksi/Komisaris',NULL),(425,419,3,418,'KG.106','KG.106','Administrasi Penyediaan Akomodasi',NULL),(426,419,3,419,'KG.107','KG.107','Kendaraan Dinas',NULL),(427,419,3,420,'KG.108','KG.108','Pemeliharaan Gedung/Perkantoran/Lat/Hospitality (Kebersihan)',NULL),(428,419,3,421,'KG.109','KG.109','Pengelolaan Jaringan Listrik, Air, Telepon, Komputer (Perbaikan, Pemeliharaan dan Pemasangan)',NULL),(429,419,3,422,'KG.110','KG.110','Pakaian Dinas & Atribut',NULL),(430,419,3,423,'KG.111','KG.111','Kunjungan Kerja/Tamu/Survey',NULL),(431,419,3,424,'KG.112','KG.112','Upacara Kedinasan',NULL),(432,419,3,425,'KG.113','KG.113','Acara Ceremonial (Rapat, Workshop, Peresmian, dll)',NULL),(433,419,3,426,'KG.114','KG.114','Permintaan/Pengiriman Alat/Fasilitas Kerja (Atk, Barang Cetakan, dll)',NULL),(434,419,3,427,'KG.115','KG.115','Perumahan Dinas/Mess/Wisma',NULL),(435,418,2,428,'KG.200','KG.200','Hubungan Masyarakat',NULL),(436,435,3,429,'KG.201','KG.201','Keprotokolan',NULL),(437,435,3,430,'KG.202','KG.202','Dokumentasi Kegiatan Perusahaan',NULL),(438,435,3,431,'KG.203','KG.203','Pengumpulan, Pengolahan dan Penyajian Informasi (Buku/Kalender/Agenda/Film/Slide/Foto/Kliping/ Reklame/Iklan/Sponsorship/Sumbangan)',NULL),(439,435,3,432,'KG.204','KG.204','Hubungan Eksternal (Lembaga Pemerintah, Lsm, Perusahaan, Lembaga Pendidikan, Media Masa, dll)',NULL),(440,435,3,433,'KG.205','KG.205','Dengar Pendapat/Hearing DPR',NULL),(441,435,3,434,'KG.206','KG.206','Bahan/materi pidato/sidang MPR, DPR, DPD, kabinet, DPRD, Muspida',NULL),(442,435,3,435,'KG.207','KG.207','Penerbitan Majalah, Buletin, Koran dan Jurnal',NULL),(443,435,3,436,'KG.208','KG.208','Publikasi Melalui Media Cetak maupun Elektronik',NULL),(444,435,3,437,'KG.209','KG.209','Pameran/Sayembara/Lomba, Festival, Pembuatan Spanduk dan Iklan',NULL),(445,435,3,438,'KG.210','KG.210','Penghargaan/Tanda Kenang-Kenangan/Administrasi Pemberian Penghargaan/ Tanda Kenang-Kenangan Kepada Masyarakat yang Memiliki Jasa Prestasi Besar',NULL),(446,435,3,439,'KG.211','KG.211','Ucapan Terima Kasih, Ucapan Selamat, Bela Sungkawa, Permohonan Maaf',NULL),(447,435,3,440,'KG.212','KG.212','Appoinment/Audiensi',NULL),(448,NULL,1,441,'KF','KF','KEUANGAN',NULL),(449,448,2,442,'KF.100','KF.100','Penyusunan Anggaran',NULL),(450,449,3,443,'KF.101','KF.101','Rkap, Rkad (Usulan, Anggaran, Persetujuan, Perubahan, dll) Keuangan Perusahaan',NULL),(451,449,3,444,'KF.102','KF.102','Investasi (Usulan, Anggaran, Persetujuan, Perubahan, Ijin Prinsip, SOT, Pricelist, dil) Keuangan Perusahaan',NULL),(452,448,2,445,'KF.200','KF.200','Pendapatan',NULL),(453,452,3,446,'KF.201','KF.201','Pendapatan dari Kinerja Operasi (Angkutan Barang/Penumpang, Pso, Imo, Pendpt dll) Keuangan Perusahaan',NULL),(454,452,3,447,'KF.202','KF.202','Pendapatan dari Kinerja Non Operasi (Komersialisasi Aset Prasarana/Sarana) Keuangan Perusahaan',NULL),(455,452,3,448,'KF.203','KF.203','Pendapatan Lain-Lain (Rekonsiliasi, Royalty, Tgr, Denda,Deviden,Bunga Deposito, Pajak, Piutang dll) Keuangan Perusahaan',NULL),(456,448,2,449,'KF.300','KF.300','Pembiayaan',NULL),(457,456,3,450,'KF.301','KF.301','Biaya Tetap (Gaji, Listrik, Air, Telepon, Pajak, Emolemen) Pada Keuangan Perusahaan',NULL),(458,456,3,451,'KF.302','KF.302','Biaya Tidak Tetap/Tidak Rutin (PSL, Tagihan Hutang) Pada Kegiatan Keuangan Perusahaan',NULL),(459,456,3,452,'KF.303','KF.303','Pajak Keuangan Perusahaan',NULL),(460,456,3,453,'KF.304','KF.304','Pencairan/Pembayaran Keuangan Perusahaan',NULL),(461,448,2,454,'KF.400','KF.400','Bantuan/Pinjaman/Hibah',NULL),(462,461,3,455,'KF.401','KF.401','Pinjaman Keuangan dalam Negeri',NULL),(463,461,3,456,'KF.402','KF.402','Pinjaman Keuangan Luar Negeri',NULL),(464,461,3,457,'KF.403','KF.403','Penyertaan Modal Pemerintah (Bpybds)',NULL),(465,461,3,458,'KF.404','KF.404','Hibah Pada Proses Keuangan Perusahaan',NULL),(466,448,2,459,'KF.500','KF.500','Pengelolaan Anggaran',NULL),(467,466,3,460,'KF.501','KF.501','Permohonan/Usulan Dana (Npd), Penghitungan Anggaran',NULL),(468,466,3,461,'KF.502','KF.502','Persetujuan (NPD ACC) Pada Proses Keuangan Perusahaan',NULL),(469,466,3,462,'KF.503','KF.503','Perbankan (Pemindahbukuan, Bank Garansi, Valas, Suku Bunga, Lc, Ekspor/Impor)',NULL),(470,466,3,463,'KF.504','KF.504','Bunga dalam Kegiatan Keuangan Perusahaan',NULL),(471,448,2,464,'KF.600','KF.600','Pertanggungjawaban',NULL),(472,471,3,465,'KF.601','KF.601','Laporan Pertanggungjawaban/Penggunaan Dana Keuangan Perusahaan',NULL),(473,471,3,466,'KF.602','KF.602','Laporan Pertanggungjawaban Satker',NULL),(474,NULL,1,467,'KK','KK','SISTEM INFORMASI',NULL),(475,474,2,468,'KK.100','KK.100','Perencanaan',NULL),(476,475,3,469,'KK.101','KK.101','Rencana Peraturan dan Kebijakan Sistem Informasi (IT Policy dan IT Governance)',NULL),(477,475,3,470,'KK.102','KK.102','Rencana Induk Sistem Informasi (IT Master Plan)',NULL),(478,475,3,471,'KK.103','KK.103','Katalog Pelayanan Sistem Informasi (IT Service Catalog)',NULL),(479,475,3,472,'KK.104','KK.104','IT Audit, Compliance, And Risk',NULL),(480,475,3,473,'KK.105','KK.105','Project Management Office (PMO)',NULL),(481,474,2,474,'KK.200','KK.200','Design And Development',NULL),(482,481,3,475,'KK.201','KK.201','Core Application And Support Application Development',NULL),(483,481,3,476,'KK.202','KK.202','Analysis And Design',NULL),(484,474,2,477,'KK.300','KK.300','IT Operation',NULL),(485,484,3,478,'KK.301','KK.301','Aplikasi Inti dan Aplikasi Pendukung (Core And Support Application)',NULL),(486,484,3,479,'KK.302','KK.302','Infrastruktur Sistem Informasi (IT Infrastructure)',NULL),(487,484,3,480,'KK.303','KK.303','Keamanan Sistem Informasi (IT Security)',NULL),(488,484,3,481,'KK.304','KK.304','Pertanyaan, Keluhan, Gangguan, Layanan dan Dukungan Teknis Sistem Informasi (Service Desk And User Support)',NULL),(489,474,2,482,'KK.400','KK.400','IT Enterprise Resource Planning (ERP)',NULL),(490,489,3,483,'KK.401','KK.401','ERP Design And Development',NULL),(491,489,3,484,'KK.402','KK.402','ERP Operation',NULL),(492,489,3,485,'KK.403','KK.403','ERP Project Management Office',NULL);
/*!40000 ALTER TABLE `klasifikasi_arsip` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lemari`
--

DROP TABLE IF EXISTS `lemari`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lemari` (
  `lemari_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ruangan_id` int(10) unsigned NOT NULL,
  `kode_lemari` varchar(50) NOT NULL,
  `nama_lemari` varchar(100) DEFAULT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`lemari_id`),
  UNIQUE KEY `uq_lemari_ruangan` (`ruangan_id`,`kode_lemari`),
  CONSTRAINT `fk_lemari_ruangan` FOREIGN KEY (`ruangan_id`) REFERENCES `ruangan` (`ruangan_id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lemari`
--

LOCK TABLES `lemari` WRITE;
/*!40000 ALTER TABLE `lemari` DISABLE KEYS */;
INSERT INTO `lemari` VALUES (1,1,'L001','Lemari Arsip 01','Lemari arsip utama'),(3,3,'L002','Lemari Arsip Operasi 01','Lemari penyimpanan arsip Unit Operasi'),(4,4,'TEST-LEMARI-01','Lemari Testing 01','Dummy development testing'),(5,5,'TEST-LEMARI-02','Lemari Testing 02','Dummy development testing'),(6,6,'TEST-LEMARI-03','Lemari Testing 03','Dummy development testing'),(7,7,'TEST-LEMARI-04','Lemari Testing 04','Dummy development testing'),(8,8,'TEST-LEMARI-05','Lemari Testing 05','Dummy development testing'),(9,9,'33','33',NULL),(10,10,'Lemari Arsip Operasi 04','Lemari Arsip Operasi 04',NULL);
/*!40000 ALTER TABLE `lemari` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifikasi`
--

DROP TABLE IF EXISTS `notifikasi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notifikasi` (
  `notifikasi_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `arsip_id` bigint(20) unsigned NOT NULL,
  `jenis_notifikasi` enum('1 Tahun','1 Bulan','H-7','Jatuh Tempo') NOT NULL,
  `tanggal_notifikasi` date NOT NULL,
  `sudah_dikirim` tinyint(1) NOT NULL DEFAULT 0,
  `tanggal_dikirim` datetime DEFAULT NULL,
  `read_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`notifikasi_id`),
  UNIQUE KEY `arsip_id` (`arsip_id`,`jenis_notifikasi`),
  UNIQUE KEY `uq_notifikasi_arsip_jenis_tanggal` (`arsip_id`,`jenis_notifikasi`,`tanggal_notifikasi`),
  CONSTRAINT `fk_notifikasi_arsip` FOREIGN KEY (`arsip_id`) REFERENCES `arsip` (`arsip_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=878 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifikasi`
--

LOCK TABLES `notifikasi` WRITE;
/*!40000 ALTER TABLE `notifikasi` DISABLE KEYS */;
INSERT INTO `notifikasi` VALUES (1,3,'1 Tahun','2025-08-14',0,NULL,NULL,'2026-08-15 20:32:06'),(2,4,'1 Tahun','2025-08-22',0,NULL,NULL,'2026-08-15 20:32:06'),(3,5,'1 Tahun','2025-09-15',0,NULL,NULL,'2026-08-15 20:32:06'),(4,6,'1 Tahun','2026-08-15',0,NULL,NULL,'2026-08-15 20:32:06'),(8,3,'1 Bulan','2026-07-14',0,NULL,NULL,'2026-08-15 20:32:06'),(9,4,'1 Bulan','2026-07-22',0,NULL,NULL,'2026-08-15 20:32:06'),(10,5,'1 Bulan','2026-08-15',0,NULL,NULL,'2026-08-15 20:32:06'),(11,3,'H-7','2026-08-07',0,NULL,NULL,'2026-08-15 20:32:06'),(12,4,'H-7','2026-08-15',0,NULL,NULL,'2026-08-15 20:32:06'),(17,1,'H-7','2026-08-15',0,NULL,NULL,'2026-08-23 00:23:11'),(18,8,'H-7','2026-08-08',0,NULL,NULL,'2026-08-23 00:23:11'),(19,20,'H-7','2026-08-15',0,NULL,NULL,'2026-08-23 00:23:11'),(20,1,'1 Bulan','2026-07-22',0,NULL,NULL,'2026-08-23 00:23:11'),(21,8,'1 Bulan','2026-07-15',0,NULL,NULL,'2026-08-23 00:23:11'),(22,20,'1 Bulan','2026-07-22',0,NULL,NULL,'2026-08-23 00:23:11'),(23,1,'1 Tahun','2025-08-22',0,NULL,NULL,'2026-08-23 00:23:11'),(24,8,'1 Tahun','2025-08-15',0,NULL,NULL,'2026-08-23 00:23:11'),(25,10,'1 Tahun','2026-03-05',0,NULL,NULL,'2026-08-23 00:23:11'),(26,12,'1 Tahun','2026-04-03',0,NULL,NULL,'2026-08-23 00:23:11'),(27,14,'1 Tahun','2026-04-30',0,NULL,NULL,'2026-08-23 00:23:11'),(28,16,'1 Tahun','2026-05-26',0,NULL,NULL,'2026-08-23 00:23:11'),(29,18,'1 Tahun','2026-06-17',0,NULL,NULL,'2026-08-23 00:23:11'),(30,20,'1 Tahun','2025-08-22',0,NULL,NULL,'2026-08-23 00:23:11'),(31,22,'1 Tahun','2025-12-15',0,NULL,NULL,'2026-08-23 00:23:11'),(32,24,'1 Tahun','2026-08-18',0,NULL,NULL,'2026-08-23 00:23:11'),(122,25,'H-7','2026-08-17',0,NULL,NULL,'2026-08-24 00:02:53'),(123,25,'1 Bulan','2026-07-24',0,NULL,NULL,'2026-08-24 00:02:53'),(124,25,'1 Tahun','2025-08-24',0,NULL,NULL,'2026-08-24 00:02:53'),(644,27,'H-7','2026-10-05',0,NULL,NULL,'2026-10-12 09:28:36'),(645,27,'1 Bulan','2026-09-12',0,NULL,NULL,'2026-10-12 09:28:36'),(646,27,'1 Tahun','2025-10-12',0,NULL,NULL,'2026-10-12 09:28:36'),(820,30,'1 Tahun','2026-10-13',0,NULL,NULL,'2026-10-13 22:59:30');
/*!40000 ALTER TABLE `notifikasi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pemusnahan`
--

DROP TABLE IF EXISTS `pemusnahan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pemusnahan` (
  `pemusnahan_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `jadwal_id` bigint(20) unsigned NOT NULL,
  `tanggal_pemusnahan` date NOT NULL,
  `metode_pemusnahan` varchar(100) NOT NULL,
  `nomor_berita_acara` varchar(100) DEFAULT NULL,
  `file_berita_acara` varchar(500) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `dilakukan_oleh` int(10) unsigned NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`pemusnahan_id`),
  UNIQUE KEY `jadwal_id` (`jadwal_id`),
  UNIQUE KEY `nomor_berita_acara` (`nomor_berita_acara`),
  KEY `fk_pemusnahan_user` (`dilakukan_oleh`),
  CONSTRAINT `fk_pemusnahan_jadwal` FOREIGN KEY (`jadwal_id`) REFERENCES `jadwal_pemusnahan` (`jadwal_id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_pemusnahan_user` FOREIGN KEY (`dilakukan_oleh`) REFERENCES `users` (`user_id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pemusnahan`
--

LOCK TABLES `pemusnahan` WRITE;
/*!40000 ALTER TABLE `pemusnahan` DISABLE KEYS */;
/*!40000 ALTER TABLE `pemusnahan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `profile_photos`
--

DROP TABLE IF EXISTS `profile_photos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `profile_photos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_profile_photos_user_id` (`user_id`),
  CONSTRAINT `fk_profile_photos_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `profile_photos`
--

LOCK TABLES `profile_photos` WRITE;
/*!40000 ALTER TABLE `profile_photos` DISABLE KEYS */;
INSERT INTO `profile_photos` VALUES (1,13,'flowchat ok.jpg','storage/profile-photos/aca3ec58d99ee21883b945b46eef76e2.jpg','2026-09-06 01:52:50','2026-09-06 03:20:36'),(4,14,'fefeed3e-9721-49be-b138-f5a488948daf.png','storage/profile-photos/dde8e827c97027756f24605272897df6.png','2026-09-06 02:08:46','2026-09-07 12:17:22'),(5,21,'mentahan_ilustrasi_arsip_kai_divreg3.png','storage/profile-photos/42921d1e39110afdc2217685ea525b8d.png','2026-09-06 02:24:07','2026-10-02 23:46:01'),(7,15,'55fa2eed-e24d-42ee-9ba9-2019c70e8d67.png','storage/profile-photos/7ad5cf2d105b5842df35902475db5573.png','2026-09-06 02:37:57','2026-10-02 22:36:46'),(12,1,'fefeed3e-9721-49be-b138-f5a488948daf.png','storage/profile-photos/43c35e2158f46035d395c77adcbb93f9.png','2026-09-06 03:03:46','2026-09-10 23:09:53');
/*!40000 ALTER TABLE `profile_photos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rak`
--

DROP TABLE IF EXISTS `rak`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rak` (
  `rak_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `lemari_id` int(10) unsigned NOT NULL,
  `kode_rak` varchar(50) NOT NULL,
  `nama_rak` varchar(100) DEFAULT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`rak_id`),
  UNIQUE KEY `uq_rak_lemari` (`lemari_id`,`kode_rak`),
  CONSTRAINT `fk_rak_lemari` FOREIGN KEY (`lemari_id`) REFERENCES `lemari` (`lemari_id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rak`
--

LOCK TABLES `rak` WRITE;
/*!40000 ALTER TABLE `rak` DISABLE KEYS */;
INSERT INTO `rak` VALUES (1,1,'RK001','Rak 01','Rak penyimpanan arsip'),(3,3,'lp001','Rak Operasi 01','tesing'),(4,4,'TEST-RAK-01','Rak Testing 01','Dummy development testing'),(5,5,'TEST-RAK-02','Rak Testing 02','Dummy development testing'),(6,6,'TEST-RAK-03','Rak Testing 03','Dummy development testing'),(7,7,'TEST-RAK-04','Rak Testing 04','Dummy development testing'),(8,8,'TEST-RAK-05','Rak Testing 05','Dummy development testing'),(9,9,'33','33',NULL),(10,10,'KL-002','KL-002',NULL);
/*!40000 ALTER TABLE `rak` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `riwayat_arsip`
--

DROP TABLE IF EXISTS `riwayat_arsip`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `riwayat_arsip` (
  `riwayat_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `arsip_id` bigint(20) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `aksi` enum('CREATE','UPDATE','DELETE','UPLOAD','DOWNLOAD','PINDAH_LOKASI','UBAH_STATUS') NOT NULL,
  `data_lama` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`data_lama`)),
  `data_baru` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`data_baru`)),
  `keterangan` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`riwayat_id`),
  KEY `idx_riwayat_arsip` (`arsip_id`),
  KEY `idx_riwayat_user` (`user_id`),
  KEY `idx_riwayat_tanggal` (`created_at`),
  CONSTRAINT `fk_riwayat_arsip` FOREIGN KEY (`arsip_id`) REFERENCES `arsip` (`arsip_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_riwayat_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `riwayat_arsip`
--

LOCK TABLES `riwayat_arsip` WRITE;
/*!40000 ALTER TABLE `riwayat_arsip` DISABLE KEYS */;
INSERT INTO `riwayat_arsip` VALUES (1,1,13,'CREATE',NULL,NULL,'Arsip dibuat','2026-08-15 19:33:36'),(2,2,13,'CREATE',NULL,NULL,'Arsip dibuat','2026-08-15 20:02:47'),(3,2,13,'UPLOAD',NULL,NULL,'Dokumen diunggah','2026-08-15 20:03:10'),(4,3,13,'CREATE',NULL,NULL,'Arsip dibuat','2026-08-15 20:32:05'),(5,4,13,'CREATE',NULL,NULL,'Arsip dibuat','2026-08-15 20:32:05'),(6,5,13,'CREATE',NULL,NULL,'Arsip dibuat','2026-08-15 20:32:05'),(7,6,13,'CREATE',NULL,NULL,'Arsip dibuat','2026-08-15 20:32:05'),(8,7,13,'CREATE',NULL,NULL,'Arsip dibuat','2026-08-15 20:32:06'),(9,8,13,'CREATE',NULL,NULL,'Arsip dibuat','2026-08-15 23:13:50'),(10,8,13,'UPLOAD',NULL,NULL,'Dokumen diunggah','2026-08-15 23:14:03'),(11,9,20,'CREATE',NULL,NULL,'Arsip dibuat','2026-08-15 23:23:50'),(12,9,20,'UPLOAD',NULL,NULL,'Dokumen diunggah','2026-08-15 23:24:03'),(13,25,13,'CREATE',NULL,NULL,'Arsip dibuat','2026-08-24 00:02:52'),(14,26,13,'CREATE',NULL,NULL,'Arsip dibuat','2026-10-08 16:03:17'),(15,27,13,'CREATE',NULL,NULL,'Arsip dibuat','2026-10-12 09:28:36'),(16,28,13,'CREATE',NULL,NULL,'Arsip dibuat','2026-10-12 09:56:59'),(17,29,13,'CREATE',NULL,NULL,'Arsip dibuat','2026-10-13 19:44:43'),(18,30,13,'CREATE',NULL,NULL,'Arsip dibuat','2026-10-13 22:59:30'),(19,31,13,'CREATE',NULL,NULL,'Arsip dibuat','2026-10-13 23:13:59'),(20,31,13,'UPLOAD',NULL,NULL,'Dokumen diunggah','2026-10-13 23:15:38');
/*!40000 ALTER TABLE `riwayat_arsip` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `role_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`role_id`),
  UNIQUE KEY `role_name` (`role_name`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Admin','Mengelola pengguna, hak akses, master data, dan seluruh sistem'),(2,'Petugas','Menginput, mengelola, mencari, dan memproses arsip'),(3,'Visitor','Melihat dan mencari arsip serta mengakses informasi yang diizinkan'),(4,'Pimpinan','Melihat dashboard, laporan, dan monitoring arsip');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ruangan`
--

DROP TABLE IF EXISTS `ruangan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ruangan` (
  `ruangan_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `unit_id` int(10) unsigned NOT NULL,
  `kode_ruangan` varchar(50) NOT NULL,
  `nama_ruangan` varchar(100) NOT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ruangan_id`),
  UNIQUE KEY `uq_ruangan_unit` (`unit_id`,`kode_ruangan`),
  CONSTRAINT `fk_ruangan_unit` FOREIGN KEY (`unit_id`) REFERENCES `unit_kerja` (`unit_id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ruangan`
--

LOCK TABLES `ruangan` WRITE;
/*!40000 ALTER TABLE `ruangan` DISABLE KEYS */;
INSERT INTO `ruangan` VALUES (1,1,'R001','Ruangan Arsip Utama','Ruang penyimpanan arsip'),(3,4,'R002','Ruangan Arsip Operasi','Ruang penyimpanan arsip Unit Operasi'),(4,21,'TEST-RUANG-01','Ruangan Testing 01','Dummy development testing'),(5,22,'TEST-RUANG-02','Ruangan Testing 02','Dummy development testing'),(6,23,'TEST-RUANG-03','Ruangan Testing 03','Dummy development testing'),(7,24,'TEST-RUANG-04','Ruangan Testing 04','Dummy development testing'),(8,25,'TEST-RUANG-05','Ruangan Testing 05','Dummy development testing'),(9,4,'23','23',NULL),(10,4,'Ruangan Arsip Operasi','Ruangan Arsip Operasi',NULL);
/*!40000 ALTER TABLE `ruangan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `unit_kerja`
--

DROP TABLE IF EXISTS `unit_kerja`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `unit_kerja` (
  `unit_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `kode_unit` varchar(30) NOT NULL,
  `nama_unit` varchar(150) NOT NULL,
  `parent_id` int(10) unsigned DEFAULT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`unit_id`),
  UNIQUE KEY `kode_unit` (`kode_unit`),
  KEY `fk_unit_parent` (`parent_id`),
  CONSTRAINT `fk_unit_parent` FOREIGN KEY (`parent_id`) REFERENCES `unit_kerja` (`unit_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `unit_kerja`
--

LOCK TABLES `unit_kerja` WRITE;
/*!40000 ALTER TABLE `unit_kerja` DISABLE KEYS */;
INSERT INTO `unit_kerja` VALUES (1,'DIVRE3','Kantor KAI Divre III',NULL,'Unit utama'),(2,'SDM','Unit SDM & Umum',NULL,'Unit SDM dan Umum'),(3,'KEU','Unit Keuangan',NULL,'Unit Keuangan'),(4,'OPS','Unit Operasi',NULL,'Unit Operasi'),(5,'SAR','Unit Sarana',NULL,'Unit Sarana'),(6,'PRA','Unit Prasarana',NULL,'Unit Prasarana'),(7,'LAIN','Unit Lainnya',NULL,'Unit kerja lainnya'),(10,'SI','Sistem Informasi',1,NULL),(11,'AFP','Angkutan dan Fasilitas Penumpang',1,NULL),(12,'BANG','Bangunan',1,NULL),(13,'HUMAS','Hubungan Masyarakat Daerah',1,NULL),(14,'HKM','Hukum',1,NULL),(15,'JRJ','Jalan Rel dan Jembatan',1,NULL),(16,'KES','Kesehatan',1,NULL),(17,'MTC','Maintenance',1,NULL),(18,'OC','Operation and Commercial',1,NULL),(19,'STL','Sinyal, Telekomunikasi dan Listrik',1,NULL),(21,'TEST-UNIT-01','Unit Testing 01',NULL,'Dummy development testing'),(22,'TEST-UNIT-02','Unit Testing 02',NULL,'Dummy development testing'),(23,'TEST-UNIT-03','Unit Testing 03',NULL,'Dummy development testing'),(24,'TEST-UNIT-04','Unit Testing 04',NULL,'Dummy development testing'),(25,'TEST-UNIT-05','Unit Testing 05',NULL,'Dummy development testing');
/*!40000 ALTER TABLE `unit_kerja` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `password_changed_at` datetime DEFAULT NULL,
  `nama_lengkap` varchar(150) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `role_id` int(10) unsigned NOT NULL,
  `unit_id` int(10) unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_login` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `fk_user_role` (`role_id`),
  KEY `fk_user_unit` (`unit_id`),
  CONSTRAINT `fk_user_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_user_unit` FOREIGN KEY (`unit_id`) REFERENCES `unit_kerja` (`unit_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'admin','$2y$10$.dKTHiV0TZNFVKASEAUh4.FJd23m5duhStjeb0rdvhwjMV4243.ZK','2026-08-11 17:26:21','Administrator Sistem','admin@example.com',1,1,1,'2026-10-13 23:00:19','2026-08-11 17:26:21','2026-10-13 23:00:19'),(2,'din','$2y$10$bMD7N2RHJV7KCR7yUfamWOY0kw2R5ACV8gnvBdhkG2IPYIOKBvucW','2026-08-11 19:36:22','dinnuy','dinnias@gmail.com',3,NULL,0,'2026-08-11 19:36:36','2026-08-11 19:36:22','2026-09-06 00:05:12'),(3,'Saput','$2y$10$I0S.jDaIyCGl6EkXIT6y4OynP6EMVeh6HPrAZkH22N323RlotYNI.','2026-08-11 23:34:20','Saputra','ssssylla5@gmail.com',3,NULL,0,'2026-08-11 23:34:54','2026-08-11 23:34:20','2026-09-06 00:05:12'),(10,'admin2','HASH_PASSWORD_ADMIN','2026-08-12 04:05:56','Administrator 2','admin2@arsip-kai.local',1,NULL,1,NULL,'2026-08-12 04:05:56','2026-09-10 23:12:19'),(11,'petugas2','HASH_PASSWORD_PETUGAS','2026-08-12 04:05:56','Petugas 2','petugas2@arsip-kai.local',2,2,1,NULL,'2026-08-12 04:05:56','2026-09-10 23:12:32'),(12,'pimpinan2','HASH_PASSWORD_PIMPINAN','2026-08-12 04:05:56','Pimpinan 2','pimpinan2@arsip-kai.local',4,NULL,1,NULL,'2026-08-12 04:05:56','2026-09-06 00:05:12'),(13,'petugas','$2y$10$MBMU4X/h9mE4hSWyylMjxe8OXaJOSUHl24kXSH5kU6u3vR.UEwKOy','2026-08-12 04:42:46','Petugas Arsip','petugas@arsip-kai.local',2,4,1,'2026-10-13 23:10:28','2026-08-12 04:42:46','2026-10-13 23:10:28'),(14,'pimpinan','$2y$10$IkfTIEC2LupdO63QorA4Y.4kn0ep.MZSF.RLEwAsKg2D.3cq7pZCa','2026-08-12 04:42:46','Pimpinan','pimpinan@arsip-kai.local',4,NULL,1,'2026-10-13 23:18:03','2026-08-12 04:42:46','2026-10-13 23:18:03'),(15,'davin77','$2y$10$P.9JUqH3/8uMxAO5TuNtg.GmsDOmdQtSmkyfeM.fJsFi0zlNHNCbS','2026-08-13 10:03:20','davin77','davin77@gmail.com',3,NULL,1,'2026-10-08 16:10:48','2026-08-13 10:03:20','2026-10-08 16:10:48'),(16,'putra123','$2y$10$gSZf81E9ge42jJ6uIcSZTOC26G4SHii/p6S5XwHIwZdIHnMtvbwAW','2026-08-13 14:45:02','pura','alfttreyhan6@gmail.com',3,NULL,1,'2026-08-13 14:45:22','2026-08-13 14:45:02','2026-09-06 00:05:12'),(18,'visit1','$2y$10$4dAIPQeZXZFaqNg.6qw2feKiGXNGN3868PguDgVVcSd87LAgYy9Dq','2026-08-15 20:38:58','tessvisit1','visit1@gmail.com',3,12,1,'2026-08-15 20:39:10','2026-08-15 20:38:58','2026-09-06 00:05:12'),(19,'tesvisit51','$2y$10$r2z/U1aDgeT52hJFuD.SaebWKmodRxLE119csfv6flZfzj72XWfP.','2026-08-15 22:24:07','tesvisit5','alfreyhan6@gmail.com',3,NULL,1,'2026-08-16 22:29:28','2026-08-15 22:24:07','2026-09-06 00:05:12'),(20,'petugaskaibaru','$2y$10$4P0dkd48NJCU1bSw25GMPe8cO3YmA1O6mhyqtq8BA.MMFZyk0JLXy','2026-08-15 23:21:53','petugaskaibaru','petugaskaibaru@gmail.com',2,19,1,'2026-08-15 23:22:11','2026-08-15 23:21:53','2026-09-06 00:05:12'),(21,'aryagap34','$2y$10$QvZL85vDTVF8CT.hbxKSy.RoHS4Du.EbnYcc9etjBY/OxFdbvkhCW','2026-08-20 00:08:46','aryagap','aryagap@gmail.com',3,NULL,1,'2026-10-13 23:08:47','2026-08-20 00:08:46','2026-10-13 23:08:47'),(22,'alfreyhan16@gmail.com','$2y$10$AbK/omX23lNzFDPtnqUueOLn0Zi3Y.MH1XMR/BQD.z.Kfuq1MzhQu','2026-08-27 10:54:09','iyan','alfreyhan16@gmail.com',3,NULL,1,'2026-08-27 10:54:20','2026-08-27 10:54:09','2026-09-06 00:05:12'),(23,'reyhanvit2','$2y$10$i0Rm0P2JCAmQz98Fy5SOmuw8BTZZ2rV0kqFUTljNFuTubTqgwZqHm','2026-09-06 00:17:08','reyhanvit',NULL,3,NULL,1,'2026-09-06 00:18:23','2026-09-06 00:16:22','2026-09-06 00:18:23'),(24,'PutraAi001','$2y$10$hRb/fPhfVHp5FCXC621b5eRNaKc1kDn2C9TjpcNMnviO1ULbNpit.','2026-10-13 22:57:43','Satria baja hitam',NULL,3,NULL,1,'2026-10-13 22:57:48','2026-10-13 22:57:43','2026-10-13 22:57:48');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `view_arsip_lengkap`
--

DROP TABLE IF EXISTS `view_arsip_lengkap`;
/*!50001 DROP VIEW IF EXISTS `view_arsip_lengkap`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `view_arsip_lengkap` AS SELECT
 1 AS `arsip_id`,
  1 AS `no_registrasi`,
  1 AS `no_surat`,
  1 AS `perihal`,
  1 AS `tanggal_surat`,
  1 AS `status_arsip`,
  1 AS `masa_simpan_tahun`,
  1 AS `tanggal_mulai_retensi`,
  1 AS `tanggal_jatuh_tempo`,
  1 AS `penyimpanan_cloud`,
  1 AS `detail_arsip`,
  1 AS `kode_unit`,
  1 AS `nama_unit`,
  1 AS `nama_jenis`,
  1 AS `kode_klasifikasi`,
  1 AS `nama_klasifikasi`,
  1 AS `kode_ruangan`,
  1 AS `nama_ruangan`,
  1 AS `kode_lemari`,
  1 AS `nama_lemari`,
  1 AS `kode_rak`,
  1 AS `nama_rak`,
  1 AS `created_at`,
  1 AS `updated_at` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `view_jadwal_pemusnahan`
--

DROP TABLE IF EXISTS `view_jadwal_pemusnahan`;
/*!50001 DROP VIEW IF EXISTS `view_jadwal_pemusnahan`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `view_jadwal_pemusnahan` AS SELECT
 1 AS `jadwal_id`,
  1 AS `arsip_id`,
  1 AS `no_registrasi`,
  1 AS `no_surat`,
  1 AS `perihal`,
  1 AS `nama_unit`,
  1 AS `status_arsip`,
  1 AS `tanggal_jatuh_tempo`,
  1 AS `status_jadwal`,
  1 AS `sisa_hari` */;
SET character_set_client = @saved_cs_client;

--
-- Dumping routines for database 'arsip_digital'
--

--
-- Final view structure for view `view_arsip_lengkap`
--

/*!50001 DROP VIEW IF EXISTS `view_arsip_lengkap`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `view_arsip_lengkap` AS select `a`.`arsip_id` AS `arsip_id`,`a`.`no_registrasi` AS `no_registrasi`,`a`.`no_surat` AS `no_surat`,`a`.`perihal` AS `perihal`,`a`.`tanggal_surat` AS `tanggal_surat`,`a`.`status_arsip` AS `status_arsip`,`a`.`masa_simpan_tahun` AS `masa_simpan_tahun`,`a`.`tanggal_mulai_retensi` AS `tanggal_mulai_retensi`,`a`.`tanggal_jatuh_tempo` AS `tanggal_jatuh_tempo`,`a`.`penyimpanan_cloud` AS `penyimpanan_cloud`,`a`.`detail_arsip` AS `detail_arsip`,`u`.`kode_unit` AS `kode_unit`,`u`.`nama_unit` AS `nama_unit`,`js`.`nama_jenis` AS `nama_jenis`,`k`.`kode_klasifikasi` AS `kode_klasifikasi`,`k`.`nama_klasifikasi` AS `nama_klasifikasi`,`ru`.`kode_ruangan` AS `kode_ruangan`,`ru`.`nama_ruangan` AS `nama_ruangan`,`l`.`kode_lemari` AS `kode_lemari`,`l`.`nama_lemari` AS `nama_lemari`,`r`.`kode_rak` AS `kode_rak`,`r`.`nama_rak` AS `nama_rak`,`a`.`created_at` AS `created_at`,`a`.`updated_at` AS `updated_at` from ((((((`arsip` `a` join `unit_kerja` `u` on(`a`.`unit_id` = `u`.`unit_id`)) join `jenis_surat` `js` on(`a`.`jenis_surat_id` = `js`.`jenis_surat_id`)) left join `klasifikasi_arsip` `k` on(`a`.`klasifikasi_id` = `k`.`klasifikasi_id`)) left join `rak` `r` on(`a`.`rak_id` = `r`.`rak_id`)) left join `lemari` `l` on(`r`.`lemari_id` = `l`.`lemari_id`)) left join `ruangan` `ru` on(`l`.`ruangan_id` = `ru`.`ruangan_id`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `view_jadwal_pemusnahan`
--

/*!50001 DROP VIEW IF EXISTS `view_jadwal_pemusnahan`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `view_jadwal_pemusnahan` AS select `jp`.`jadwal_id` AS `jadwal_id`,`a`.`arsip_id` AS `arsip_id`,`a`.`no_registrasi` AS `no_registrasi`,`a`.`no_surat` AS `no_surat`,`a`.`perihal` AS `perihal`,`u`.`nama_unit` AS `nama_unit`,`a`.`status_arsip` AS `status_arsip`,`jp`.`tanggal_jatuh_tempo` AS `tanggal_jatuh_tempo`,`jp`.`status_jadwal` AS `status_jadwal`,to_days(`jp`.`tanggal_jatuh_tempo`) - to_days(curdate()) AS `sisa_hari` from ((`jadwal_pemusnahan` `jp` join `arsip` `a` on(`jp`.`arsip_id` = `a`.`arsip_id`)) join `unit_kerja` `u` on(`a`.`unit_id` = `u`.`unit_id`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-10-13 23:19:54
