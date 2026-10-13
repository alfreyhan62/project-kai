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
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `arsip`
--

LOCK TABLES `arsip` WRITE;
/*!40000 ALTER TABLE `arsip` DISABLE KEYS */;
INSERT INTO `arsip` VALUES (1,4,12,3,3,'99988','999','tes','2026-03-25',1,'2026-08-15','2026-08-22','Inaktif',NULL,'tes','tes',13,'2026-08-15 19:33:36','2026-10-13 19:17:12',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(2,4,12,3,3,'99988ee','999cc','tesc','2025-09-16',NULL,NULL,NULL,'Permanen',NULL,'cc','cc',13,'2026-08-15 20:02:47','2026-10-13 19:11:33',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(3,4,12,4,NULL,'UAT-KALENDER-001','UAT/KALENDER/001/2026','UAT Kalender ΓÇö Arsip Sudah Kedaluwarsa','2025-08-14',1,'2025-08-14','2026-08-14','Musnah',NULL,'DATA DUMMY UAT KALENDER ΓÇö SUDAH KEDALUWARSA','DUMMY UAT ΓÇö jangan digunakan sebagai data produksi.',13,'2026-08-15 20:32:05','2026-10-13 19:11:33',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(4,4,12,4,NULL,'UAT-KALENDER-002','UAT/KALENDER/002/2026','UAT Kalender ΓÇö Jatuh Tempo H-7','2025-08-22',1,'2025-08-22','2026-08-22','Musnah',NULL,'DATA DUMMY UAT KALENDER ΓÇö H-7','DUMMY UAT ΓÇö jangan digunakan sebagai data produksi.',13,'2026-08-15 20:32:05','2026-10-13 19:11:33',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(5,4,12,4,NULL,'UAT-KALENDER-003','UAT/KALENDER/003/2026','UAT Kalender ΓÇö Jatuh Tempo H-1 Bulan','2025-09-15',1,'2025-09-15','2026-09-15','Musnah',NULL,'DATA DUMMY UAT KALENDER ΓÇö H-1 BULAN','DUMMY UAT ΓÇö jangan digunakan sebagai data produksi.',13,'2026-08-15 20:32:05','2026-10-13 19:11:33',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(6,4,12,4,NULL,'UAT-KALENDER-004','UAT/KALENDER/004/2026','UAT Kalender ΓÇö Jatuh Tempo H-1 Tahun','2026-08-15',1,'2026-08-15','2027-08-15','Musnah',NULL,'DATA DUMMY UAT KALENDER ΓÇö H-1 TAHUN','DUMMY UAT ΓÇö jangan digunakan sebagai data produksi.',13,'2026-08-15 20:32:05','2026-10-13 19:11:33',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(7,4,12,4,NULL,'UAT-KALENDER-005','UAT/KALENDER/005/2026','UAT Kalender ΓÇö Arsip Permanen','2026-08-15',NULL,NULL,NULL,'Permanen',NULL,'DATA DUMMY UAT ΓÇö ARSIP PERMANEN','DUMMY UAT ΓÇö jangan digunakan sebagai data produksi.',13,'2026-08-15 20:32:06','2026-10-13 19:11:33',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(8,4,12,2,3,'00998','9888','dinas malam','2025-06-24',1,'2026-08-15','2026-08-15','Inaktif',NULL,'ok','tes',13,'2026-08-15 23:13:50','2026-10-13 19:17:12',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(9,19,12,3,NULL,'0099899','988888','dinas malam banget','2026-05-01',NULL,NULL,NULL,'Inaktif',NULL,NULL,NULL,20,'2026-08-15 23:23:50','2026-10-13 19:17:12',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(10,21,12,1,4,'DUMMY-2026-001','TEST/OPS/001/2026','Dummy dashboard operasional 01','2026-03-05',1,'2026-03-05','2027-03-05','Inaktif',NULL,NULL,'Dummy development testing',1,'2026-03-05 09:00:00','2026-10-13 19:17:12',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(11,21,12,2,4,'DUMMY-2026-002','TEST/KEU/002/2026','Dummy dashboard keuangan 02','2026-03-18',1,'2026-03-18','2027-03-18','Permanen',NULL,NULL,'Dummy development testing',1,'2026-03-18 09:00:00','2026-10-13 19:11:33',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(12,21,12,3,4,'DUMMY-2026-003','TEST/SDM/003/2026','Dummy dashboard SDM 03','2026-04-03',1,'2026-04-03','2027-04-03','Inaktif',NULL,NULL,'Dummy development testing',1,'2026-04-03 09:00:00','2026-10-13 19:17:12',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(13,21,12,4,4,'DUMMY-2026-004','TEST/TEK/004/2026','Dummy dashboard teknis 04','2026-04-22',1,'2026-04-22','2027-04-22','Musnah',NULL,NULL,'Dummy development testing',1,'2026-04-22 09:00:00','2026-10-13 19:11:33',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(14,22,12,5,5,'DUMMY-2026-005','TEST/UMM/005/2026','Dummy dashboard umum 05','2026-04-30',1,'2026-04-30','2027-04-30','Inaktif',NULL,NULL,'Dummy development testing',1,'2026-04-30 09:00:00','2026-10-13 19:17:12',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(15,22,12,1,5,'DUMMY-2026-006','TEST/OPS/006/2026','Dummy dashboard operasional 06','2026-05-07',1,'2026-05-07','2027-05-07','Permanen',NULL,NULL,'Dummy development testing',1,'2026-05-07 09:00:00','2026-10-13 19:11:33',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(16,22,12,2,5,'DUMMY-2026-007','TEST/KEU/007/2026','Dummy dashboard keuangan 07','2026-05-26',1,'2026-05-26','2027-05-26','Inaktif',NULL,NULL,'Dummy development testing',1,'2026-05-26 09:00:00','2026-10-13 19:17:12',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(17,23,12,3,6,'DUMMY-2026-008','TEST/SDM/008/2026','Dummy dashboard SDM 08','2026-06-04',1,'2026-06-04','2027-06-04','Musnah',NULL,NULL,'Dummy development testing',1,'2026-06-04 09:00:00','2026-10-13 19:11:33',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(18,23,12,4,6,'DUMMY-2026-009','TEST/TEK/009/2026','Dummy dashboard teknis 09','2026-06-17',1,'2026-06-17','2027-06-17','Inaktif',NULL,NULL,'Dummy development testing',1,'2026-06-17 09:00:00','2026-10-13 19:17:12',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(19,23,12,5,6,'DUMMY-2026-010','TEST/UMM/010/2026','Dummy dashboard umum 10','2026-07-02',1,'2026-07-02','2027-07-02','Permanen',NULL,NULL,'Dummy development testing',1,'2026-07-02 09:00:00','2026-10-13 19:11:33',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(20,24,12,1,7,'DUMMY-2026-011','TEST/OPS/011/2026','Dummy dashboard operasional 11','2026-07-16',1,'2026-07-16','2026-08-22','Inaktif',NULL,NULL,'Dummy development testing',1,'2026-07-16 09:00:00','2026-10-13 19:17:12',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(21,24,12,2,7,'DUMMY-2026-012','TEST/KEU/012/2026','Dummy dashboard keuangan 12','2026-08-01',1,'2026-08-01','2026-08-31','Musnah',NULL,NULL,'Dummy development testing',1,'2026-08-01 09:00:00','2026-10-13 19:11:33',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(22,24,12,3,7,'DUMMY-2026-013','TEST/SDM/013/2026','Dummy dashboard SDM 13','2026-08-08',1,'2026-08-08','2026-12-15','Inaktif',NULL,NULL,'Dummy development testing',1,'2026-08-08 09:00:00','2026-10-13 19:17:12',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(23,25,12,4,8,'DUMMY-2026-014','TEST/TEK/014/2026','Dummy dashboard teknis 14','2026-08-12',1,'2026-08-12','2027-01-10','Musnah',NULL,NULL,'Dummy development testing',1,'2026-08-12 09:00:00','2026-10-13 19:11:33',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(24,25,12,5,8,'DUMMY-2026-015','TEST/UMM/015/2026','Dummy dashboard umum 15','2026-08-18',1,'2026-08-18','2027-08-18','Inaktif',NULL,NULL,'Dummy development testing',1,'2026-08-18 09:00:00','2026-10-13 19:17:12',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(25,4,12,2,3,'33','9888','dinas malam banget ya','2026-08-24',1,'2026-08-24','2026-08-24','Inaktif',NULL,'res','ees',13,'2026-08-24 00:02:52','2026-10-13 19:17:12',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(26,4,12,3,3,'0099854','9888654','dinas malam hh','2026-10-08',NULL,NULL,NULL,'Permanen',NULL,'aktif','tes',13,'2026-10-08 16:03:17','2026-10-13 19:11:33',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(27,4,12,5,3,'983','0832','dinas malam','2026-10-12',1,'2026-10-12','2026-10-12','Inaktif',NULL,'okee','oeje',13,'2026-10-12 09:28:36','2026-10-13 19:17:12',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(28,4,12,NULL,NULL,'099','9888','dinas luar','2025-01-13',NULL,NULL,NULL,'Permanen',NULL,'tes','tes',13,'2026-10-12 09:56:59','2026-10-13 19:11:33',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(29,4,14,3,3,'099xx','xx','xxx','2026-10-13',1,'2026-10-05',NULL,'Inaktif',NULL,NULL,NULL,13,'2026-10-13 19:44:43','2026-10-13 19:44:43',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `file_arsip`
--

LOCK TABLES `file_arsip` WRITE;
/*!40000 ALTER TABLE `file_arsip` DISABLE KEYS */;
INSERT INTO `file_arsip` VALUES (1,2,'Annotation 2023-02-06 233624.png','storage/uploads/6f30939b3f2a20d454053e9395ab0916.png','png','image/png',189457,'76df803f6057c21ad0e5a73350f734a713d867883f153bc0d0671161c9c3b521',0,13,'2026-08-15 20:03:10'),(2,8,'Screenshot 2026-08-14 221422.png','storage/uploads/3ae370307533e06a5793d036d9dc5155.png','png','image/png',73018,'00ded27c7b97166686b6dfa33b55a04cc13342a77b385f7d1dea40a8b1cef5ac',0,13,'2026-08-15 23:14:03'),(3,9,'landingpagedasboar.jpg','storage/uploads/409573843dcddd965b3fdf7805e7fb53.jpg','jpg','image/jpeg',153183,'8a1728bc20ec5e1d6c07503ae8bde38e5c6e607e13abb14cf5fda8621b60674e',0,20,'2026-08-15 23:24:03'),(4,26,'Doc1.pdf','storage/uploads/b52d50da17c982f78da4482cd5b46dbf.pdf','pdf','application/pdf',594101,'41bbf95fc8ccc977035ac9ebe0cae107fd0ede354deb11b24c45660f31171b88',0,13,'2026-10-08 16:03:17'),(5,27,'Laporan_Arsip_Pimpinan_2026-10-08_110538.xlsx','storage/uploads/209c29d5e61f16ad8aeff7388af67250.xlsx','xlsx','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',58558,'6e43ae5a293f9a9422d06a1bc57efc02aa8d552759f720b8e7b0a00cb8ff7dfd',0,13,'2026-10-12 09:28:36'),(6,28,'Laporan_Arsip_Pimpinan_2026-10-08_110538.xlsx','storage/uploads/6f968fb4fee96fc6b74363b5ee70802f.xlsx','xlsx','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',58558,'6e43ae5a293f9a9422d06a1bc57efc02aa8d552759f720b8e7b0a00cb8ff7dfd',0,13,'2026-10-12 09:56:59');
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
) ENGINE=InnoDB AUTO_INCREMENT=248 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
  `kode_klasifikasi` varchar(50) NOT NULL,
  `nama_klasifikasi` varchar(150) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  PRIMARY KEY (`klasifikasi_id`),
  UNIQUE KEY `kode_klasifikasi` (`kode_klasifikasi`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `klasifikasi_arsip`
--

LOCK TABLES `klasifikasi_arsip` WRITE;
/*!40000 ALTER TABLE `klasifikasi_arsip` DISABLE KEYS */;
INSERT INTO `klasifikasi_arsip` VALUES (1,'SM','Surat Masuk','Arsip surat masuk'),(2,'SK','Surat Keluar','Arsip surat keluar'),(3,'KEU','Keuangan','Arsip keuangan'),(4,'PEG','Kepegawaian','Arsip kepegawaian'),(5,'OPS','Operasional','Arsip operasional');
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lemari`
--

LOCK TABLES `lemari` WRITE;
/*!40000 ALTER TABLE `lemari` DISABLE KEYS */;
INSERT INTO `lemari` VALUES (1,1,'L001','Lemari Arsip 01','Lemari arsip utama'),(3,3,'L002','Lemari Arsip Operasi 01','Lemari penyimpanan arsip Unit Operasi'),(4,4,'TEST-LEMARI-01','Lemari Testing 01','Dummy development testing'),(5,5,'TEST-LEMARI-02','Lemari Testing 02','Dummy development testing'),(6,6,'TEST-LEMARI-03','Lemari Testing 03','Dummy development testing'),(7,7,'TEST-LEMARI-04','Lemari Testing 04','Dummy development testing'),(8,8,'TEST-LEMARI-05','Lemari Testing 05','Dummy development testing');
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
) ENGINE=InnoDB AUTO_INCREMENT=746 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifikasi`
--

LOCK TABLES `notifikasi` WRITE;
/*!40000 ALTER TABLE `notifikasi` DISABLE KEYS */;
INSERT INTO `notifikasi` VALUES (1,3,'1 Tahun','2025-08-14',0,NULL,NULL,'2026-08-15 20:32:06'),(2,4,'1 Tahun','2025-08-22',0,NULL,NULL,'2026-08-15 20:32:06'),(3,5,'1 Tahun','2025-09-15',0,NULL,NULL,'2026-08-15 20:32:06'),(4,6,'1 Tahun','2026-08-15',0,NULL,NULL,'2026-08-15 20:32:06'),(8,3,'1 Bulan','2026-07-14',0,NULL,NULL,'2026-08-15 20:32:06'),(9,4,'1 Bulan','2026-07-22',0,NULL,NULL,'2026-08-15 20:32:06'),(10,5,'1 Bulan','2026-08-15',0,NULL,NULL,'2026-08-15 20:32:06'),(11,3,'H-7','2026-08-07',0,NULL,NULL,'2026-08-15 20:32:06'),(12,4,'H-7','2026-08-15',0,NULL,NULL,'2026-08-15 20:32:06'),(17,1,'H-7','2026-08-15',0,NULL,NULL,'2026-08-23 00:23:11'),(18,8,'H-7','2026-08-08',0,NULL,NULL,'2026-08-23 00:23:11'),(19,20,'H-7','2026-08-15',0,NULL,NULL,'2026-08-23 00:23:11'),(20,1,'1 Bulan','2026-07-22',0,NULL,NULL,'2026-08-23 00:23:11'),(21,8,'1 Bulan','2026-07-15',0,NULL,NULL,'2026-08-23 00:23:11'),(22,20,'1 Bulan','2026-07-22',0,NULL,NULL,'2026-08-23 00:23:11'),(23,1,'1 Tahun','2025-08-22',0,NULL,NULL,'2026-08-23 00:23:11'),(24,8,'1 Tahun','2025-08-15',0,NULL,NULL,'2026-08-23 00:23:11'),(25,10,'1 Tahun','2026-03-05',0,NULL,NULL,'2026-08-23 00:23:11'),(26,12,'1 Tahun','2026-04-03',0,NULL,NULL,'2026-08-23 00:23:11'),(27,14,'1 Tahun','2026-04-30',0,NULL,NULL,'2026-08-23 00:23:11'),(28,16,'1 Tahun','2026-05-26',0,NULL,NULL,'2026-08-23 00:23:11'),(29,18,'1 Tahun','2026-06-17',0,NULL,NULL,'2026-08-23 00:23:11'),(30,20,'1 Tahun','2025-08-22',0,NULL,NULL,'2026-08-23 00:23:11'),(31,22,'1 Tahun','2025-12-15',0,NULL,NULL,'2026-08-23 00:23:11'),(32,24,'1 Tahun','2026-08-18',0,NULL,NULL,'2026-08-23 00:23:11'),(122,25,'H-7','2026-08-17',0,NULL,NULL,'2026-08-24 00:02:53'),(123,25,'1 Bulan','2026-07-24',0,NULL,NULL,'2026-08-24 00:02:53'),(124,25,'1 Tahun','2025-08-24',0,NULL,NULL,'2026-08-24 00:02:53'),(644,27,'H-7','2026-10-05',0,NULL,NULL,'2026-10-12 09:28:36'),(645,27,'1 Bulan','2026-09-12',0,NULL,NULL,'2026-10-12 09:28:36'),(646,27,'1 Tahun','2025-10-12',0,NULL,NULL,'2026-10-12 09:28:36');
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rak`
--

LOCK TABLES `rak` WRITE;
/*!40000 ALTER TABLE `rak` DISABLE KEYS */;
INSERT INTO `rak` VALUES (1,1,'RK001','Rak 01','Rak penyimpanan arsip'),(3,3,'lp001','Rak Operasi 01','tesing'),(4,4,'TEST-RAK-01','Rak Testing 01','Dummy development testing'),(5,5,'TEST-RAK-02','Rak Testing 02','Dummy development testing'),(6,6,'TEST-RAK-03','Rak Testing 03','Dummy development testing'),(7,7,'TEST-RAK-04','Rak Testing 04','Dummy development testing'),(8,8,'TEST-RAK-05','Rak Testing 05','Dummy development testing');
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
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `riwayat_arsip`
--

LOCK TABLES `riwayat_arsip` WRITE;
/*!40000 ALTER TABLE `riwayat_arsip` DISABLE KEYS */;
INSERT INTO `riwayat_arsip` VALUES (1,1,13,'CREATE',NULL,NULL,'Arsip dibuat','2026-08-15 19:33:36'),(2,2,13,'CREATE',NULL,NULL,'Arsip dibuat','2026-08-15 20:02:47'),(3,2,13,'UPLOAD',NULL,NULL,'Dokumen diunggah','2026-08-15 20:03:10'),(4,3,13,'CREATE',NULL,NULL,'Arsip dibuat','2026-08-15 20:32:05'),(5,4,13,'CREATE',NULL,NULL,'Arsip dibuat','2026-08-15 20:32:05'),(6,5,13,'CREATE',NULL,NULL,'Arsip dibuat','2026-08-15 20:32:05'),(7,6,13,'CREATE',NULL,NULL,'Arsip dibuat','2026-08-15 20:32:05'),(8,7,13,'CREATE',NULL,NULL,'Arsip dibuat','2026-08-15 20:32:06'),(9,8,13,'CREATE',NULL,NULL,'Arsip dibuat','2026-08-15 23:13:50'),(10,8,13,'UPLOAD',NULL,NULL,'Dokumen diunggah','2026-08-15 23:14:03'),(11,9,20,'CREATE',NULL,NULL,'Arsip dibuat','2026-08-15 23:23:50'),(12,9,20,'UPLOAD',NULL,NULL,'Dokumen diunggah','2026-08-15 23:24:03'),(13,25,13,'CREATE',NULL,NULL,'Arsip dibuat','2026-08-24 00:02:52'),(14,26,13,'CREATE',NULL,NULL,'Arsip dibuat','2026-10-08 16:03:17'),(15,27,13,'CREATE',NULL,NULL,'Arsip dibuat','2026-10-12 09:28:36'),(16,28,13,'CREATE',NULL,NULL,'Arsip dibuat','2026-10-12 09:56:59'),(17,29,13,'CREATE',NULL,NULL,'Arsip dibuat','2026-10-13 19:44:43');
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ruangan`
--

LOCK TABLES `ruangan` WRITE;
/*!40000 ALTER TABLE `ruangan` DISABLE KEYS */;
INSERT INTO `ruangan` VALUES (1,1,'R001','Ruangan Arsip Utama','Ruang penyimpanan arsip'),(3,4,'R002','Ruangan Arsip Operasi','Ruang penyimpanan arsip Unit Operasi'),(4,21,'TEST-RUANG-01','Ruangan Testing 01','Dummy development testing'),(5,22,'TEST-RUANG-02','Ruangan Testing 02','Dummy development testing'),(6,23,'TEST-RUANG-03','Ruangan Testing 03','Dummy development testing'),(7,24,'TEST-RUANG-04','Ruangan Testing 04','Dummy development testing'),(8,25,'TEST-RUANG-05','Ruangan Testing 05','Dummy development testing');
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
INSERT INTO `unit_kerja` VALUES (1,'DIVRE3','Kantor KAI Divre III',NULL,'Unit utama'),(2,'SDM','Unit SDM & Umum',NULL,'Unit SDM dan Umum'),(3,'KEU','Unit Keuangan',NULL,'Unit Keuangan'),(4,'OPS','Unit Operasi',NULL,'Unit Operasi'),(5,'SAR','Unit Sarana',NULL,'Unit Sarana'),(6,'PRA','Unit Prasarana',NULL,'Unit Prasarana'),(7,'LAIN','Unit Lainnya',NULL,'Unit kerja lainnya'),(10,'SI','Sistem Informasi',1,NULL),(11,'AFP','Angkutan dan Fasilitas Penumpang',1,NULL),(12,'BANG','Bangunan',1,NULL),(13,'HUMAS','Hubungan Masyarakat Daerah',1,NULL),(14,'HKM','Hukum',1,NULL),(15,'JRJ','Jalan Rel dan Jembatan',1,NULL),(16,'KES','Kesehatan',1,NULL),(17,'MTC','Maintenance',1,NULL),(18,'OC','Operation and Commercial',1,NULL),(19,'STL','Sinyal, Telekomunikasi dan Listrik',1,NULL),(20,'hms01','humas kai',13,'hubungan masyarakat'),(21,'TEST-UNIT-01','Unit Testing 01',NULL,'Dummy development testing'),(22,'TEST-UNIT-02','Unit Testing 02',NULL,'Dummy development testing'),(23,'TEST-UNIT-03','Unit Testing 03',NULL,'Dummy development testing'),(24,'TEST-UNIT-04','Unit Testing 04',NULL,'Dummy development testing'),(25,'TEST-UNIT-05','Unit Testing 05',NULL,'Dummy development testing');
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
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'admin','$2y$10$.dKTHiV0TZNFVKASEAUh4.FJd23m5duhStjeb0rdvhwjMV4243.ZK','2026-08-11 17:26:21','Administrator Sistem','admin@example.com',1,1,1,'2026-10-13 20:22:32','2026-08-11 17:26:21','2026-10-13 20:22:32'),(2,'din','$2y$10$bMD7N2RHJV7KCR7yUfamWOY0kw2R5ACV8gnvBdhkG2IPYIOKBvucW','2026-08-11 19:36:22','dinnuy','dinnias@gmail.com',3,NULL,0,'2026-08-11 19:36:36','2026-08-11 19:36:22','2026-09-06 00:05:12'),(3,'Saput','$2y$10$I0S.jDaIyCGl6EkXIT6y4OynP6EMVeh6HPrAZkH22N323RlotYNI.','2026-08-11 23:34:20','Saputra','ssssylla5@gmail.com',3,NULL,0,'2026-08-11 23:34:54','2026-08-11 23:34:20','2026-09-06 00:05:12'),(10,'admin2','HASH_PASSWORD_ADMIN','2026-08-12 04:05:56','Administrator 2','admin2@arsip-kai.local',1,NULL,1,NULL,'2026-08-12 04:05:56','2026-09-10 23:12:19'),(11,'petugas2','HASH_PASSWORD_PETUGAS','2026-08-12 04:05:56','Petugas 2','petugas2@arsip-kai.local',2,2,1,NULL,'2026-08-12 04:05:56','2026-09-10 23:12:32'),(12,'pimpinan2','HASH_PASSWORD_PIMPINAN','2026-08-12 04:05:56','Pimpinan 2','pimpinan2@arsip-kai.local',4,NULL,1,NULL,'2026-08-12 04:05:56','2026-09-06 00:05:12'),(13,'petugas','$2y$10$MBMU4X/h9mE4hSWyylMjxe8OXaJOSUHl24kXSH5kU6u3vR.UEwKOy','2026-08-12 04:42:46','Petugas Arsip','petugas@arsip-kai.local',2,4,1,'2026-10-13 20:18:36','2026-08-12 04:42:46','2026-10-13 20:18:36'),(14,'pimpinan','$2y$10$IkfTIEC2LupdO63QorA4Y.4kn0ep.MZSF.RLEwAsKg2D.3cq7pZCa','2026-08-12 04:42:46','Pimpinan','pimpinan@arsip-kai.local',4,NULL,1,'2026-10-13 19:42:44','2026-08-12 04:42:46','2026-10-13 19:42:44'),(15,'davin77','$2y$10$P.9JUqH3/8uMxAO5TuNtg.GmsDOmdQtSmkyfeM.fJsFi0zlNHNCbS','2026-08-13 10:03:20','davin77','davin77@gmail.com',3,NULL,1,'2026-10-08 16:10:48','2026-08-13 10:03:20','2026-10-08 16:10:48'),(16,'putra123','$2y$10$gSZf81E9ge42jJ6uIcSZTOC26G4SHii/p6S5XwHIwZdIHnMtvbwAW','2026-08-13 14:45:02','pura','alfttreyhan6@gmail.com',3,NULL,1,'2026-08-13 14:45:22','2026-08-13 14:45:02','2026-09-06 00:05:12'),(18,'visit1','$2y$10$4dAIPQeZXZFaqNg.6qw2feKiGXNGN3868PguDgVVcSd87LAgYy9Dq','2026-08-15 20:38:58','tessvisit1','visit1@gmail.com',3,12,1,'2026-08-15 20:39:10','2026-08-15 20:38:58','2026-09-06 00:05:12'),(19,'tesvisit51','$2y$10$r2z/U1aDgeT52hJFuD.SaebWKmodRxLE119csfv6flZfzj72XWfP.','2026-08-15 22:24:07','tesvisit5','alfreyhan6@gmail.com',3,NULL,1,'2026-08-16 22:29:28','2026-08-15 22:24:07','2026-09-06 00:05:12'),(20,'petugaskaibaru','$2y$10$4P0dkd48NJCU1bSw25GMPe8cO3YmA1O6mhyqtq8BA.MMFZyk0JLXy','2026-08-15 23:21:53','petugaskaibaru','petugaskaibaru@gmail.com',2,19,1,'2026-08-15 23:22:11','2026-08-15 23:21:53','2026-09-06 00:05:12'),(21,'aryagap34','$2y$10$QvZL85vDTVF8CT.hbxKSy.RoHS4Du.EbnYcc9etjBY/OxFdbvkhCW','2026-08-20 00:08:46','aryagap','aryagap@gmail.com',3,NULL,1,'2026-10-12 09:58:19','2026-08-20 00:08:46','2026-10-12 09:58:19'),(22,'alfreyhan16@gmail.com','$2y$10$AbK/omX23lNzFDPtnqUueOLn0Zi3Y.MH1XMR/BQD.z.Kfuq1MzhQu','2026-08-27 10:54:09','iyan','alfreyhan16@gmail.com',3,NULL,1,'2026-08-27 10:54:20','2026-08-27 10:54:09','2026-09-06 00:05:12'),(23,'reyhanvit2','$2y$10$i0Rm0P2JCAmQz98Fy5SOmuw8BTZZ2rV0kqFUTljNFuTubTqgwZqHm','2026-09-06 00:17:08','reyhanvit',NULL,3,NULL,1,'2026-09-06 00:18:23','2026-09-06 00:16:22','2026-09-06 00:18:23');
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

-- Dump completed on 2026-10-13 21:58:06
