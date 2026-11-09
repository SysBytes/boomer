-- MySQL dump 10.13  Distrib 8.0.33, for macos13 (x86_64)
--
-- Host: localhost    Database: boomer_elecomm
-- ------------------------------------------------------
-- Server version	8.0.33

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `backup_settings`
--

DROP TABLE IF EXISTS `backup_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `backup_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `backup_type` enum('files','database') COLLATE utf8mb4_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  `frequency` enum('daily','weekly','monthly') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'weekly',
  `time` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '02:00',
  `day_of_week` tinyint DEFAULT NULL,
  `day_of_month` tinyint DEFAULT NULL,
  `retention_days` int unsigned NOT NULL DEFAULT '30',
  `last_run_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `backup_settings`
--

LOCK TABLES `backup_settings` WRITE;
/*!40000 ALTER TABLE `backup_settings` DISABLE KEYS */;
INSERT INTO `backup_settings` VALUES (1,'files',1,'weekly','06:00',0,1,30,'2026-05-31 11:00:06','2026-05-26 01:04:25','2026-05-31 11:00:21'),(2,'database',1,'daily','06:05',0,1,30,'2026-06-03 11:05:03','2026-05-26 01:04:25','2026-06-03 11:05:04');
/*!40000 ALTER TABLE `backup_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clients`
--

DROP TABLE IF EXISTS `clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `clients` (
  `client_id` int unsigned NOT NULL AUTO_INCREMENT,
  `client_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `client_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `client_phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country_id` int DEFAULT NULL,
  `active` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`client_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clients`
--

LOCK TABLES `clients` WRITE;
/*!40000 ALTER TABLE `clients` DISABLE KEYS */;
INSERT INTO `clients` VALUES (1,'FARMATODO CO','info@farmatodo.com','6017469000',1,1,'2026-05-07 01:26:11','2026-05-07 01:26:11'),(2,'BOOMER ELECOMM CO','info@boomer-elecomm.com','3186516672',1,1,'2026-05-08 14:55:28','2026-05-08 14:55:28');
/*!40000 ALTER TABLE `clients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `corporate_content_role`
--

DROP TABLE IF EXISTS `corporate_content_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `corporate_content_role` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `corporate_content_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `corporate_content_role_corporate_content_id_role_id_unique` (`corporate_content_id`,`role_id`),
  KEY `corporate_content_role_role_id_foreign` (`role_id`),
  CONSTRAINT `corporate_content_role_corporate_content_id_foreign` FOREIGN KEY (`corporate_content_id`) REFERENCES `corporate_contents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `corporate_content_role_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `corporate_content_role`
--

LOCK TABLES `corporate_content_role` WRITE;
/*!40000 ALTER TABLE `corporate_content_role` DISABLE KEYS */;
INSERT INTO `corporate_content_role` VALUES (1,1,1,'2026-11-03 22:10:47','2026-11-03 22:10:47'),(2,1,8,'2026-11-03 22:10:47','2026-11-03 22:10:47'),(3,1,6,'2026-11-03 22:10:47','2026-11-03 22:10:47'),(4,1,2,'2026-11-03 22:10:47','2026-11-03 22:10:47'),(5,1,4,'2026-11-03 22:10:47','2026-11-03 22:10:47'),(6,1,5,'2026-11-03 22:10:47','2026-11-03 22:10:47'),(8,1,3,'2026-11-06 14:20:59','2026-11-06 14:20:59');
/*!40000 ALTER TABLE `corporate_content_role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `corporate_contents`
--

DROP TABLE IF EXISTS `corporate_contents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `corporate_contents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `subcategory_id` bigint unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('pdf','video') COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `required_time_seconds` int NOT NULL DEFAULT '0',
  `target_role_id` bigint unsigned DEFAULT NULL,
  `target_user_id` bigint unsigned DEFAULT NULL,
  `is_mandatory` tinyint(1) NOT NULL DEFAULT '1',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `corporate_contents_subcategory_id_foreign` (`subcategory_id`),
  KEY `corporate_contents_target_role_id_foreign` (`target_role_id`),
  KEY `corporate_contents_target_user_id_foreign` (`target_user_id`),
  CONSTRAINT `corporate_contents_subcategory_id_foreign` FOREIGN KEY (`subcategory_id`) REFERENCES `document_subcategories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `corporate_contents_target_role_id_foreign` FOREIGN KEY (`target_role_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL,
  CONSTRAINT `corporate_contents_target_user_id_foreign` FOREIGN KEY (`target_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `corporate_contents`
--

LOCK TABLES `corporate_contents` WRITE;
/*!40000 ALTER TABLE `corporate_contents` DISABLE KEYS */;
INSERT INTO `corporate_contents` VALUES (1,2,'Descripción del Cargo','pdf','induction/pdf/ZsOXzit3rJcvOc7r9qMh6gdiN6dX3kGuC1g6aZxZ.pdf',NULL,0,1,NULL,1,1,'2026-11-03 22:10:47','2026-11-03 22:11:26');
/*!40000 ALTER TABLE `corporate_contents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `countries`
--

DROP TABLE IF EXISTS `countries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `countries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `countries`
--

LOCK TABLES `countries` WRITE;
/*!40000 ALTER TABLE `countries` DISABLE KEYS */;
INSERT INTO `countries` VALUES (1,'Colombia','+57',1,'2026-06-03 20:06:16','2026-06-03 20:06:16'),(2,'Argentina','+54',1,'2026-06-03 20:06:16','2026-06-03 20:06:16');
/*!40000 ALTER TABLE `countries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `country_user`
--

DROP TABLE IF EXISTS `country_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `country_user` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `country_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `country_user_user_id_country_id_unique` (`user_id`,`country_id`),
  KEY `country_user_country_id_foreign` (`country_id`),
  CONSTRAINT `country_user_country_id_foreign` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON DELETE CASCADE,
  CONSTRAINT `country_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `country_user`
--

LOCK TABLES `country_user` WRITE;
/*!40000 ALTER TABLE `country_user` DISABLE KEYS */;
INSERT INTO `country_user` VALUES (1,1,1,'2026-06-03 20:06:16','2026-06-03 20:06:16'),(2,1,2,'2026-06-03 20:06:16','2026-06-03 20:06:16'),(3,2,1,'2026-06-03 20:06:16','2026-06-03 20:06:16'),(4,2,2,'2026-06-03 20:06:16','2026-06-03 20:06:16'),(5,3,1,'2026-06-03 20:06:16','2026-06-03 20:06:16'),(6,3,2,'2026-06-03 20:06:16','2026-06-03 20:06:16'),(7,4,1,'2026-06-03 20:06:16','2026-06-03 20:06:16'),(8,5,1,'2026-06-03 20:06:16','2026-06-03 20:06:16'),(9,5,2,'2026-06-03 20:06:16','2026-06-03 20:06:16'),(10,6,1,'2026-06-03 20:06:16','2026-06-03 20:06:16'),(11,7,1,'2026-06-03 20:06:16','2026-06-03 20:06:16'),(12,8,1,'2026-06-03 20:06:16','2026-06-03 20:06:16'),(13,9,1,'2026-06-03 20:06:16','2026-06-03 20:06:16'),(14,10,1,'2026-06-03 20:06:16','2026-06-03 20:06:16'),(15,11,1,'2026-06-03 20:06:16','2026-06-03 20:06:16'),(16,12,1,'2026-06-03 20:06:16','2026-06-03 20:06:16'),(17,13,1,'2026-06-03 20:06:16','2026-06-03 20:06:16'),(18,14,1,'2026-06-03 20:06:16','2026-06-03 20:06:16'),(19,15,1,'2026-06-03 20:06:16','2026-06-03 20:06:16'),(20,16,1,'2026-06-03 20:06:16','2026-06-03 20:06:16'),(21,17,1,'2026-06-03 20:06:16','2026-06-03 20:06:16'),(22,18,1,'2026-06-03 20:06:16','2026-06-03 20:06:16'),(23,19,1,'2026-06-03 20:06:16','2026-06-03 20:06:16'),(24,20,1,'2026-06-03 20:06:16','2026-06-03 20:06:16');
/*!40000 ALTER TABLE `country_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `document_categories`
--

DROP TABLE IF EXISTS `document_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `document_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `document_categories`
--

LOCK TABLES `document_categories` WRITE;
/*!40000 ALTER TABLE `document_categories` DISABLE KEYS */;
INSERT INTO `document_categories` VALUES (1,'Inducción',NULL,'2026-11-03 21:16:44','2026-11-03 21:16:44');
/*!40000 ALTER TABLE `document_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `document_subcategories`
--

DROP TABLE IF EXISTS `document_subcategories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `document_subcategories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `document_subcategories_category_id_foreign` (`category_id`),
  CONSTRAINT `document_subcategories_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `document_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `document_subcategories`
--

LOCK TABLES `document_subcategories` WRITE;
/*!40000 ALTER TABLE `document_subcategories` DISABLE KEYS */;
INSERT INTO `document_subcategories` VALUES (1,1,'General',NULL,'2026-11-03 21:17:07','2026-11-03 21:17:07'),(2,1,'Documentos',NULL,'2026-11-03 21:17:36','2026-11-03 21:17:36'),(3,1,'Videos',NULL,'2026-11-03 21:17:44','2026-11-03 21:17:44');
/*!40000 ALTER TABLE `document_subcategories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
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
-- Table structure for table `holidays`
--

DROP TABLE IF EXISTS `holidays`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `holidays` (
  `holiday_id` int unsigned NOT NULL AUTO_INCREMENT,
  `holiday_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `holiday_date` date NOT NULL,
  `country_id` int DEFAULT NULL,
  `is_annual` tinyint NOT NULL DEFAULT '1',
  `active` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`holiday_id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `holidays`
--

LOCK TABLES `holidays` WRITE;
/*!40000 ALTER TABLE `holidays` DISABLE KEYS */;
INSERT INTO `holidays` VALUES (1,'Año Nuevo','2026-01-01',1,1,1,NULL,NULL),(2,'Reyes Magos','2026-01-12',1,0,1,NULL,NULL),(3,'Día de San José','2026-03-23',1,0,1,NULL,NULL),(4,'Jueves Santo','2026-04-02',1,0,1,NULL,NULL),(5,'Viernes Santo','2026-04-03',1,0,1,NULL,NULL),(6,'Día del Trabajo','2026-05-01',1,1,1,NULL,NULL),(7,'Ascensión de Jesús','2026-05-18',1,0,1,NULL,NULL),(8,'Corpus Christi','2026-06-08',1,0,1,NULL,NULL),(9,'Sagrado Corazón de Jesús','2026-06-15',1,0,1,NULL,NULL),(10,'San Pedro y San Pablo','2026-06-29',1,0,1,NULL,NULL),(11,'Día de la Independencia','2026-07-20',1,1,1,NULL,NULL),(12,'Batalla de Boyacá','2026-08-07',1,1,1,NULL,NULL),(13,'Asunción de la Virgen','2026-08-17',1,0,1,NULL,NULL),(14,'Día de la Raza','2026-10-12',1,0,1,NULL,NULL),(15,'Todos los Santos','2026-11-02',1,0,1,NULL,NULL),(16,'Independencia de Cartagena','2026-11-16',1,0,1,NULL,NULL),(17,'Inmaculada Concepción','2026-12-08',1,1,1,NULL,NULL),(18,'Navidad','2026-12-25',1,1,1,NULL,NULL),(19,'Año Nuevo','2026-01-01',2,1,1,NULL,NULL),(20,'Carnaval','2026-02-16',2,0,1,NULL,NULL),(21,'Carnaval','2026-02-17',2,0,1,NULL,NULL),(22,'Día Nacional de la Memoria por la Verdad y la Justicia','2026-03-24',2,1,1,NULL,NULL),(23,'Día del Veterano y de los Caídos en la Guerra de Malvinas','2026-04-02',2,1,1,NULL,NULL),(24,'Viernes Santo','2026-04-03',2,0,1,NULL,NULL),(25,'Día del Trabajador','2026-05-01',2,1,1,NULL,NULL),(26,'Día de la Revolución de Mayo','2026-05-25',2,1,1,NULL,NULL),(27,'Paso a la Inmortalidad del Gral. Manuel Belgrano','2026-06-20',2,1,1,NULL,NULL),(28,'Día de la Independencia','2026-07-09',2,1,1,NULL,NULL),(29,'Paso a la Inmortalidad del Gral. José de San Martín','2026-08-17',2,0,1,NULL,NULL),(30,'Día del Respeto a la Diversidad Cultural','2026-10-12',2,0,1,NULL,NULL),(31,'Día de la Soberanía Nacional','2026-11-23',2,0,1,NULL,NULL),(32,'Día de la Inmaculada Concepción de María','2026-12-08',2,1,1,NULL,NULL),(33,'Navidad','2026-12-25',2,1,1,NULL,NULL);
/*!40000 ALTER TABLE `holidays` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `locations`
--

DROP TABLE IF EXISTS `locations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `locations` (
  `location_id` int unsigned NOT NULL AUTO_INCREMENT,
  `client_id` int unsigned NOT NULL,
  `location_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `location_description` text COLLATE utf8mb4_unicode_ci,
  `location_city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location_address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `location_reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location_lat` decimal(10,8) DEFAULT NULL,
  `location_lng` decimal(11,8) DEFAULT NULL,
  `active` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`location_id`)
) ENGINE=InnoDB AUTO_INCREMENT=167 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `locations`
--

LOCK TABLES `locations` WRITE;
/*!40000 ALTER TABLE `locations` DISABLE KEYS */;
INSERT INTO `locations` VALUES (1,1,'FMTD VIVA BARRANQUILLA',NULL,'BARRANQUILLA','Carrera 51B #87-50, Riomar, Barranquilla, Atlántico, Colombia','Lc 333',11.00852270,-74.82083000,1,'2026-05-07 02:44:01','2026-05-14 20:54:31'),(2,1,'FMTD MALECON DEL RIO',NULL,'BARRANQUILLA','VIVE RIO, Riomar, Barranquilla, Atlántico, Colombia','Edificio Vive Rio',11.02511280,-74.79897720,1,'2026-05-07 02:50:52','2026-05-08 14:16:13'),(3,1,'FMTD AUTOPAGOS',NULL,'BOGOTÁ','Calle 73 #11-12, Bogotá, Colombia',NULL,4.65860970,-74.05807010,1,'2026-05-08 14:36:24','2026-05-13 21:09:27'),(4,2,'BOOMER ELECOMM CO',NULL,'BOGOTÁ','Calle 106 #48-46, Bogotá, Colombia',NULL,4.69452420,-74.05916660,1,'2026-05-08 14:57:09','2026-06-01 12:56:35'),(5,1,'FMTD CENTENARIO',NULL,'ARMENIA','Farmatodo, Avenida Centenario, Armenia, Quindío, Colombia',NULL,4.54160390,-75.65935520,1,'2026-05-11 20:33:24','2026-05-11 20:33:24'),(6,1,'FMTD CARRERA 19',NULL,'ARMENIA','Carrera 19 #30n83, Armenia, Quindío, Colombia',NULL,4.56597920,-75.65245860,1,'2026-05-11 20:53:35','2026-05-11 20:53:35'),(7,1,'FMTD VIVA BARRANQUILLA',NULL,'BARRANQUILLA','Carrera 51B #87-50, Riomar, Barranquilla, Atlántico, Colombia',NULL,11.00852270,-74.82083000,1,'2026-05-11 21:03:52','2026-05-11 21:03:52'),(8,1,'FMTD PLAZA DEL PARQUE',NULL,'BARRANQUILLA','Carrera 56 #98-65, Riomar, Barranquilla, Atlántico, Colombia',NULL,11.01518660,-74.82629490,1,'2026-05-11 21:07:25','2026-05-11 21:07:25'),(9,1,'FMTD PLAZA CAMPESTRE',NULL,'BARRANQUILLA','Centro Comercial Plaza Campestre, Cra 30, Sabanilla Montecarmelo, Barranquilla, Atlántico, Colombia',NULL,11.01785180,-74.85974040,1,'2026-05-13 21:15:41','2026-05-13 21:15:41'),(10,1,'FMTD CALLE 80',NULL,'BARRANQUILLA','Carrera 50 #79-207, Barranquilla, Atlántico, Colombia',NULL,11.00127410,-74.81211840,1,'2026-05-13 21:16:59','2026-05-13 21:16:59'),(11,1,'FMTD MALL PLAZA',NULL,'BARRANQUILLA','Carrera 55 #99-51, Riomar, Barranquilla, Atlántico, Colombia',NULL,11.01507070,-74.82774070,1,'2026-05-13 21:17:49','2026-05-13 21:17:49'),(12,1,'FMTD EL GOLF',NULL,'BARRANQUILLA','Carrera 57 #84-10, Riomar, Barranquilla, Atlántico, Colombia',NULL,11.00955740,-74.81331040,1,'2026-05-13 21:18:42','2026-05-13 21:18:42'),(13,1,'FMTD CALLE 84',NULL,'BARRANQUILLA','Farmatodo, Carrera 43, Norte Centro Historico, Barranquilla, Atlántico, Colombia',NULL,10.99829470,-74.82028280,1,'2026-05-13 21:20:08','2026-05-13 21:20:08'),(14,1,'FMTD ALTO DEL PRADO',NULL,'BARRANQUILLA','Farmatodo, Carrera 51B #80-65, Norte Centro Historico, Barranquilla, Atlántico, Colombia',NULL,11.00391070,-74.81343060,1,'2026-05-13 21:26:05','2026-05-13 21:26:05'),(15,1,'FMTD MIRAMAR',NULL,'BARRANQUILLA','Centro empresarial y Comercial Miramar Plaza, Transversal 44, Norte Centro Historico, Barranquilla, Atlántico, Colombia',NULL,11.00685230,-74.84100020,1,'2026-05-13 21:27:32','2026-05-13 21:27:32'),(16,1,'FMTD BERNARDO',NULL,'BARRANQUILLA','Carrera 43 #75-2, Barranquilla, Atlántico, Colombia',NULL,10.99342830,-74.81022950,1,'2026-05-13 21:28:31','2026-05-13 21:28:31'),(17,1,'FMTD VILLA CAROLINA',NULL,'BARRANQUILLA','Carrera 65 # 86 - 125, Riomar, Barranquilla, Atlántico, Colombia',NULL,11.01745370,-74.81358540,1,'2026-05-13 21:29:20','2026-05-13 21:29:20'),(18,1,'FMTD VILLA COUNTRY',NULL,'BARRANQUILLA','Calle 79 #53-51, Barranquilla, Atlántico, Colombia',NULL,11.00493900,-74.80808110,1,'2026-05-13 21:30:12','2026-05-13 21:30:12'),(19,1,'FMTD MALECÓN DEL RÍO',NULL,'BARRANQUILLA','VIVE RIO, Riomar, Barranquilla, Atlántico, Colombia',NULL,11.02511280,-74.79897720,1,'2026-05-13 21:31:26','2026-05-13 21:31:26'),(20,1,'FMTD LA CALLEJA',NULL,'BOGOTÁ','Carrera 19 #127-53, Bogotá, Colombia',NULL,4.70680720,-74.04894220,1,'2026-05-13 21:32:56','2026-05-13 21:32:56'),(21,1,'FMTD AUTONORTE',NULL,'BOGOTÁ','Carrera 45 #104-12, Bogotá, Colombia',NULL,4.69142640,-74.05592160,1,'2026-05-13 21:34:44','2026-05-13 21:34:44'),(22,1,'FMTD CENTRO INTERNACIONAL',NULL,'BOGOTÁ','Carrera 13 #27-8, Bogotá, Colombia',NULL,4.61426170,-74.07077810,1,'2026-05-13 21:35:55','2026-05-13 21:35:55'),(23,1,'FMTD AVENIDA 19',NULL,'BOGOTÁ','Avenida Calle 100 #19-5, Bogotá, Colombia',NULL,4.68562840,-74.05226030,1,'2026-05-13 21:37:05','2026-05-13 21:37:05'),(24,1,'FMTD BELLA SUIZA',NULL,'BOGOTÁ','Avenida Carrera 7 #129-39, Bogotá, Colombia',NULL,4.70810690,-74.02891960,1,'2026-05-13 21:37:54','2026-05-13 21:37:54'),(25,1,'FMTD COSMOS 100',NULL,'BOGOTÁ','Transversal 21 #98-71, Bogotá, Colombia',NULL,4.68581830,-74.05476060,1,'2026-05-13 21:39:53','2026-05-13 21:39:53'),(26,1,'FMTD PLAZA CENTRAL',NULL,'BOGOTÁ','Carrera 65 #11-50, Bogotá, Colombia',NULL,4.63164300,-74.11545920,1,'2026-05-13 21:40:39','2026-05-13 21:40:39'),(27,1,'FMTD ROSALES',NULL,'BOGOTÁ','Diagonal 70a #4-87, Chapinero, Cundinamarca, Colombia',NULL,4.65251690,-74.05439500,1,'2026-05-14 00:48:10','2026-05-14 00:49:06'),(28,1,'FMTD CENTRO ANDINO',NULL,'BOGOTÁ','Carrera 11 #82-71, Bogotá, Colombia',NULL,4.66709340,-74.05174830,1,'2026-05-14 00:52:40','2026-05-14 01:01:48'),(29,1,'FMTD NORMANDIA',NULL,'BOGOTÁ','Avenida Calle 53 #72-49, Bogotá, Colombia',NULL,4.67170980,-74.10716670,1,'2026-05-14 13:11:35','2026-05-14 13:12:27'),(30,1,'FMTD COLINA',NULL,'BOGOTÁ','Farmatodo, Calle 138, Suba, Cundinamarca, Colombia',NULL,4.72984780,-74.06815160,1,'2026-05-14 13:13:32','2026-05-14 13:14:21'),(31,1,'FMTD HAYUELOS',NULL,'BOGOTÁ','Sector Hayuelos, Carrera 90 #23j - 22, Bogotá, Colombia',NULL,4.67290450,-74.12679190,1,'2026-05-14 13:15:16','2026-05-14 13:16:12'),(32,1,'FMTD CEDRITOS 1',NULL,'BOGOTÁ','Cra 19 #139-78, Usaquén, Cundinamarca, Colombia',NULL,4.72296740,-74.04605430,1,'2026-05-14 13:16:27','2026-05-14 13:17:07'),(33,1,'FMTD CALLE 119',NULL,'BOGOTÁ','Calle 119 #13-15, Bogotá, Colombia',NULL,4.69849100,-74.04017920,1,'2026-05-14 13:17:38','2026-05-14 13:18:26'),(34,1,'FMTD CEDRITOS 2',NULL,'BOGOTÁ','Cl. 140 #13 - 27, Bogotá, Colombia',NULL,4.72080630,-74.04045600,1,'2026-05-14 13:18:38','2026-05-14 13:19:19'),(35,1,'FMTD CALLE 74',NULL,'BOGOTÁ','Calle 74 # 5 09, Chapinero, Cundinamarca, Colombia',NULL,4.65593240,-74.05330510,1,'2026-05-14 13:21:27','2026-05-14 13:22:14'),(36,1,'FMTD CALLE 170',NULL,'BOGOTÁ','Calle 170 #65-80, Bogotá, Colombia',NULL,4.75754650,-74.06138320,1,'2026-05-14 13:56:36','2026-05-14 13:57:37'),(37,1,'FMTD CC DIVERPLAZA',NULL,'BOGOTÁ','Calle 71B #99a-11, Bogotá, Colombia',NULL,4.70208840,-74.11685070,1,'2026-05-14 14:00:32','2026-05-14 14:04:46'),(38,1,'FMTD EL ENSUEÑO',NULL,'BOGOTÁ','Centro Comercial Gran Plaza El Ensueño, Avenida Ciudad de Villavicencio, Bogotá, Colombia',NULL,4.58223630,-74.15675450,1,'2026-05-14 14:08:25','2026-05-14 14:09:58'),(39,1,'FMTD PASADENA',NULL,'BOGOTÁ','Cra. 53 #103 B 08, Bogotá, Colombia',NULL,4.69171660,-74.06249940,1,'2026-05-14 14:11:31','2026-05-14 14:12:06'),(40,1,'FMTD CALLE 147',NULL,'BOGOTÁ','Carrera 19 #146 - 81, Usaquén, Cundinamarca, Colombia',NULL,4.72937860,-74.04575590,1,'2026-05-14 14:12:26','2026-05-14 14:13:01'),(41,1,'FMTD SANTA ISABEL',NULL,'BOGOTÁ','Avenida Carrera 30 #2b-17, Bogotá, Colombia',NULL,4.60330510,-74.10125140,1,'2026-05-14 14:13:27','2026-05-14 14:13:58'),(42,1,'FMTD GRAN ESTACIÓN',NULL,'BOGOTÁ','D.C, Ac. 26 # 62 - 49, Teusaquillo, Cundinamarca, Colombia',NULL,4.64765340,-74.10172100,1,'2026-05-14 14:14:17','2026-05-14 14:21:09'),(43,1,'FMTD SALITRE',NULL,'BOGOTÁ','Avenida Calle 24 #59-60, Bogotá, Colombia',NULL,4.64405840,-74.10161880,1,'2026-05-14 14:21:27','2026-05-14 14:30:39'),(44,1,'FMTD CENDIS',NULL,'BOGOTÁ','Celta Trade Park Parque Industrial, Autopista medellin, Costado Occidental, Funza, Cundinamarca, Colombia',NULL,4.76919670,-74.17719870,1,'2026-05-14 14:31:04','2026-05-14 14:33:58'),(45,1,'FMTD PARQUE 93',NULL,'BOGOTÁ','Calle 93a # 12-73, Chapinero, Bogotá, D.C., Colombia',NULL,4.67666750,-74.04916910,1,'2026-05-14 14:34:06','2026-05-14 14:34:49'),(46,1,'FMTD CC SANTA FE',NULL,'BOGOTÁ','Calle 185 #45-3, Bogotá, Colombia',NULL,4.76324740,-74.04525260,1,'2026-05-14 14:35:02','2026-05-14 14:36:12'),(47,1,'FMTD COUNTRY',NULL,'BOGOTÁ','Avenida Carrera 15 #94-72, Bogotá, Colombia',NULL,4.68046500,-74.04993880,1,'2026-05-14 14:36:28','2026-05-14 14:37:11'),(48,1,'FMTD CONNECTA',NULL,'BOGOTÁ','Av. El Dorado #92-32, Bogotá, Colombia',NULL,4.68285460,-74.12110690,1,'2026-05-14 14:37:25','2026-05-14 14:38:27'),(49,1,'FMTD CC UNICENTRO',NULL,'BOGOTÁ','Avenida Carrera 15 #No. 123-30, Usaquén, Cundinamarca, Colombia',NULL,4.70287200,-74.04188900,1,'2026-05-14 14:38:42','2026-05-14 14:39:52'),(50,1,'FMTD USAQUEN',NULL,'BOGOTÁ','Avenida Carrera 7 #No. 116- 50, Bogotá, Colombia',NULL,4.69415880,-74.03249090,1,'2026-05-14 14:40:34','2026-05-14 14:41:17'),(51,1,'FMTD RINCÓN DEL CHICÓ',NULL,'BOGOTÁ','Calle 106 #15-26, Bogotá, Colombia',NULL,4.69078350,-74.04620290,1,'2026-05-14 14:41:27','2026-05-14 14:41:57'),(52,1,'FMTD LA CABRERA',NULL,'BOGOTÁ','Carrera 9 #82-19, Bogotá, Colombia',NULL,4.66483300,-74.05120000,1,'2026-05-14 14:42:10','2026-05-14 14:42:41'),(53,1,'FMTD CARRERA 16',NULL,'BOGOTÁ','Carrera 16 #82-52, Chapinero, Cundinamarca, Colombia',NULL,4.66833940,-74.05666090,1,'2026-05-14 14:42:52','2026-05-14 14:43:31'),(54,1,'FMTD NAVARRA',NULL,'BOGOTÁ','Cra 19 #103-98, Bogotá, Colombia',NULL,4.69014340,-74.05108210,1,'2026-05-14 14:43:41','2026-05-14 14:44:30'),(55,1,'FMTD PEPE SIERRA',NULL,'BOGOTÁ','Avenida Calle 116 #18b-43, Bogotá, Colombia',NULL,4.69815410,-74.04887160,1,'2026-05-14 14:44:35','2026-05-14 14:45:17'),(56,1,'FMTD MAZUREN',NULL,'BOGOTÁ','Avenida Carrera 45 #147 a31, Bogotá, Colombia',NULL,4.72985190,-74.04967270,1,'2026-05-14 14:45:30','2026-05-14 14:46:11'),(57,1,'FMTD SALITRE 2',NULL,'BOGOTÁ','Carrera 68b #24a-30, Fontibón, Cundinamarca, Colombia',NULL,4.65276060,-74.10830840,1,'2026-05-14 14:46:23','2026-05-14 14:51:07'),(58,1,'FMTD CALLE 127',NULL,'BOGOTÁ','Avenida Calle 127 #17A-27, Bogotá, Colombia',NULL,4.70430510,-74.04618530,1,'2026-05-14 14:55:39','2026-05-14 15:03:06'),(59,1,'FMTD ATLANTIS',NULL,'BOGOTÁ','D.C, Calle 81 #13 - 05, Chapinero, Cundinamarca, Colombia',NULL,4.66611960,-74.05497160,1,'2026-05-14 15:07:19','2026-05-14 15:08:29'),(60,1,'FMTD NOGAL',NULL,'BOGOTÁ','Calle 73 #11-12, Bogotá, Colombia',NULL,4.65860970,-74.05807010,1,'2026-05-14 15:09:30','2026-05-14 15:10:00'),(61,1,'FMTD CENTRO MAYOR',NULL,'BOGOTÁ','Farmatodo, Centro Comercial Centro Mayor, Calle 38A Sur, Antonio Nariño, Cundinamarca, Colombia',NULL,4.59023460,-74.12427140,1,'2026-05-14 15:10:10','2026-05-14 15:16:58'),(62,1,'FMTD TITÁN',NULL,'BOGOTÁ','Cra. 72 #80-94, Bogotá, Colombia',NULL,4.69565830,-74.08710160,1,'2026-05-14 15:17:03','2026-05-14 15:18:09'),(63,1,'FMTD CAMPANELLA',NULL,'BOGOTÁ','Calle 154a # 94-91, Suba, Cundinamarca, Colombia',NULL,4.75213510,-74.08448820,1,'2026-05-14 15:19:39','2026-05-14 15:22:42'),(64,1,'FMTD GALERIAS',NULL,'BOGOTÁ','Calle 53 #20-11, Bogotá, Colombia',NULL,4.64112630,-74.07142370,1,'2026-05-14 15:22:56','2026-05-14 15:24:15'),(65,1,'FMTD BRITALIA NORTE',NULL,'BOGOTÁ','Calle 167 #73 – 02, Suba, Cundinamarca, Colombia',NULL,4.75129380,-74.06573770,1,'2026-05-14 15:24:28','2026-05-14 15:25:15'),(66,1,'FMTD NIEVES',NULL,'BOGOTÁ','Carrera 7 # 21-43, Santa Fé, Cundinamarca, Colombia',NULL,4.60736530,-74.07120510,1,'2026-05-14 15:25:37','2026-05-14 15:26:14'),(67,1,'FMTD BELMIRA',NULL,'BOGOTÁ','Avenida Carrera 9 # 139-88, Bogotá, Colombia',NULL,4.71746150,-74.03225740,1,'2026-05-14 15:26:32','2026-05-14 15:27:05'),(68,1,'FMTD CARRERA 13',NULL,'BOGOTÁ','Carrera 13 #55-61, Bogotá, Colombia',NULL,4.64295420,-74.06475680,1,'2026-05-14 15:29:28','2026-05-14 15:31:16'),(69,1,'FMTD CALLE 100',NULL,'BOGOTÁ','Cra. 11 #99-61, Chapinero, Bogotá, Cundinamarca, Colombia',NULL,4.68236300,-74.04380340,1,'2026-05-14 15:32:47','2026-05-14 15:33:40'),(70,1,'FMTD ALTOS DE CABECERA',NULL,'BUCARAMANGA','Cra. 36 #44-2, Cabecera del llano, Bucaramanga, Santander, Colombia',NULL,7.12028070,-73.10938090,1,'2026-05-14 15:34:50','2026-05-14 15:49:22'),(71,1,'FMTD CARRERA 27',NULL,'BUCARAMANGA','Avenida Carrera 27 #45 - 08, Bucaramanga, Santander, Colombia',NULL,7.11754140,-73.11554990,1,'2026-05-14 15:49:37','2026-05-14 15:50:09'),(72,1,'FMTD SOTOMAYOR',NULL,'BUCARAMANGA','Cl. 52 #31-98, Sotomayor, Bucaramanga, Santander, Colombia',NULL,7.11277600,-73.11143640,1,'2026-05-14 15:50:22','2026-05-14 15:52:39'),(73,1,'FMTD CAÑAVERAL',NULL,'BUCARAMANGA','Carrera 20 & Calle 158, Floridablanca, Santander, Colombia',NULL,7.07011470,-73.11394250,1,'2026-05-14 15:53:15','2026-05-14 15:55:36'),(74,1,'FMTD CAJICÁ',NULL,'CAJICÁ','Farmatodo, Kilómetro 1, Cajicá, Cundinamarca, Colombia',NULL,4.90181320,-74.02507920,1,'2026-05-14 15:56:02','2026-05-14 16:00:26'),(75,1,'FMTD PANCE',NULL,'CALI','Carrera 122 #17a-0, Barrio Pance, Cali, Valle del Cauca, Colombia',NULL,3.34311470,-76.53192840,1,'2026-05-14 16:01:01','2026-05-14 16:02:35'),(76,1,'FMTD UNICENTRO CALI',NULL,'CALI','Farmatodo, Carrera 100, Comuna 17, Cali, Valle del Cauca, Colombia',NULL,3.37421110,-76.53877810,1,'2026-05-14 16:04:18','2026-05-14 16:05:24'),(77,1,'FMTD CASTILLO GRANDE',NULL,'CARTAGENA','Farmatodo, Carrera 5, Cartagena de Indias, Bolívar, Colombia',NULL,10.39881930,-75.55594560,1,'2026-05-14 16:06:01','2026-05-14 16:31:35'),(78,1,'FMTD QUINTAS DE DON SIMÓN',NULL,'CALI','Calle 14 #75-60, Comuna 17, Cali, Valle del Cauca, Colombia',NULL,3.38959820,-76.53285240,1,'2026-05-14 16:06:05','2026-05-14 16:07:20'),(79,1,'FMTD CIUDAD JARDÍN',NULL,'CALI','Av. San Joaquín & Calle 15b, Ciudad Jardín, Cali, Valle del Cauca, Colombia',NULL,3.36410790,-76.53315610,1,'2026-05-14 16:07:24','2026-05-14 16:08:42'),(80,1,'FMTD AVENIDA DEL RÍO',NULL,'CALI','Cl. 1 Oe. #1-50, Normandia Sebastian de Belalcazar, Cali, Valle del Cauca, Colombia',NULL,3.45056530,-76.54101370,1,'2026-05-14 16:08:50','2026-05-14 16:13:05'),(81,1,'FMTD MALL PLAZA CALI',NULL,'CALI','Calle 5 #52-140, Cali, Valle del Cauca, Colombia',NULL,3.41278400,-76.54875260,1,'2026-05-14 16:13:10','2026-05-14 16:14:27'),(82,1,'FMTD VALLE DE LILI',NULL,'CALI','Cra. 94 #25-60, Comuna 17, Cali, Valle del Cauca, Colombia',NULL,3.37469870,-76.52396190,1,'2026-05-14 16:14:41','2026-05-14 16:15:28'),(83,1,'FMTD LIMONAR',NULL,'CALI','Calle 10 # 66-22, Limonar, Comuna 17, Cali, Valle del Cauca, Colombia',NULL,3.39788700,-76.54314500,1,'2026-05-14 16:15:32','2026-05-14 16:18:04'),(84,1,'FMTD GRANADA',NULL,'CALI','Av. 8 Nte. #18N-44, Santa Monica Residential, Cali, Valle del Cauca, Colombia',NULL,3.46191500,-76.53218670,1,'2026-05-14 16:18:16','2026-05-14 16:19:42'),(85,1,'FMTD VERSALLES',NULL,'CALI','Avenida 4 # 19 NORTE-35, Versalles, San Vicente, Cali, Valle del Cauca, Colombia',NULL,3.46003600,-76.52908700,1,'2026-05-14 16:19:59','2026-05-14 16:21:32'),(86,1,'FMTD GUADALUPE',NULL,'CALI','Calle 13 #56-40, Cali, Valle del Cauca, Colombia',NULL,3.40454520,-76.53571350,1,'2026-05-14 16:21:44','2026-05-14 16:22:30'),(87,1,'FMTD LA FLORA',NULL,'CALI','Calle 44 Norte #4B-78, Cali, Valle del Cauca, Colombia',NULL,3.48164210,-76.52427030,1,'2026-05-14 16:22:34','2026-05-14 16:23:43'),(88,1,'FMTD CHIPICHAPE',NULL,'CALI','Calle 38 Norte #6N – 45, Cali, Valle del Cauca, Colombia',NULL,3.47595190,-76.52781690,1,'2026-05-14 16:23:53','2026-05-14 16:25:18'),(89,1,'FMTD 3RA NORTE CALI',NULL,'CALI','Av. 3 Nte. #56n25, El Rincon, Cali, Valle del Cauca, Colombia',NULL,3.48671810,-76.51231910,1,'2026-05-14 16:25:22','2026-05-14 16:28:31'),(90,1,'FMTD CHÍA',NULL,'CHÍA','Via A Cajicá #Km 2, Bogotá, Chía, Cundinamarca, Colombia',NULL,4.87785690,-74.03734280,1,'2026-05-14 16:31:45','2026-05-14 16:51:38'),(91,1,'FMTD SAN MARTÍN',NULL,'CARTAGENA','Carrera 2 #10-113, Cartagena de Indias, Bolívar, Colombia',NULL,10.40778060,-75.55155720,1,'2026-05-14 16:31:49','2026-05-14 16:33:51'),(92,1,'FMTD PEDRO DE HEREDIA',NULL,'CARTAGENA','Calle 31d #64-272, Cartagena de Indias, Bolívar, Colombia',NULL,10.40528670,-75.48569910,1,'2026-05-14 16:34:07','2026-05-14 16:38:53'),(93,1,'FMTD MANGA',NULL,'CARTAGENA','Farmatodo, Calle 25, Manga, Cartagena de Indias, Bolívar, Colombia',NULL,10.41675110,-75.54291070,1,'2026-05-14 16:39:00','2026-05-14 16:42:27'),(94,1,'FMTD CIUDAD AMURALLADA',NULL,'CARTAGENA','Cl. 32 # 3 -12, El Centro, Cartagena de Indias, Bolívar, Colombia',NULL,10.42242210,-75.55214310,1,'2026-05-14 16:42:37','2026-05-14 16:45:31'),(95,1,'FMTD VARIANTE CHÍA',NULL,'CHÍA','Farmatodo, Variante Cota, Cota, Cundinamarca, Colombia',NULL,4.80928010,-74.09209350,1,'2026-05-14 16:51:46','2026-05-14 16:55:31'),(96,1,'FMTD AV. CERO',NULL,'CÚCUTA','Av. 0 #11-29, Cúcuta, Norte de Santander, Colombia',NULL,7.88665780,-72.49826440,1,'2026-05-14 16:56:09','2026-05-14 17:00:41'),(97,1,'FMTD COTA',NULL,'COTA','Conjunto Residencial Palo De Agua, Variante de Cota, Cota, Cundinamarca, Colombia',NULL,4.80863220,-74.09148470,1,'2026-05-14 16:56:22','2026-05-14 16:57:24'),(98,1,'FMTD CENTRO CÚCUTA',NULL,'CÚCUTA','Calle 10 #6-2, Cúcuta, Norte de Santander, Colombia',NULL,7.88670840,-72.50494720,1,'2026-05-14 17:00:49','2026-05-14 17:06:22'),(99,1,'FMTD SAN MATEO',NULL,'CÚCUTA','Av. 1 #24-54, Cúcuta, Norte de Santander, Colombia',NULL,7.88167150,-72.48917210,1,'2026-05-14 17:00:53','2026-05-14 17:02:22'),(100,1,'FMTD LA CEIBA 2',NULL,'CÚCUTA','Farmatodo, Calle 5 Norte, Barrio Ceiba 2, Cúcuta, Norte de Santander, Colombia',NULL,7.90303140,-72.49722290,1,'2026-05-14 17:02:27','2026-05-14 17:04:31'),(101,1,'FMTD LIBERTADORES',NULL,'CÚCUTA','Avenida Libertadores #9-68, Cúcuta, Norte de Santander, Colombia',NULL,7.88963850,-72.48906220,1,'2026-05-14 17:04:37','2026-05-14 17:05:26'),(102,1,'FMTD EL VERGEL',NULL,'IBAGUÉ','Farmatodo El Vergel, Carrera 16, Ibagué, Tolima, Colombia',NULL,4.44704040,-75.19135070,1,'2026-05-14 17:06:57','2026-05-14 17:09:45'),(103,1,'FMTD CC COUNTRY MALL',NULL,'JAMUNDÍ','Country Mall plaza comercial, Jamundí, Valle del Cauca, Colombia',NULL,3.28086830,-76.52510660,1,'2026-05-14 17:32:56','2026-05-14 17:58:56'),(104,1,'FMTD LA CALERA',NULL,'LA CALERA','Farmatodo, al lado del peaje, Bogotá - La Calera, La Calera, Cundinamarca, Colombia',NULL,4.66244550,-74.01034900,1,'2026-05-14 17:59:43','2026-05-14 18:01:08'),(105,1,'FMTD EL CABLE',NULL,'MANIZALES','Cra. 23 # 62-96, Manizales, Caldas, Colombia',NULL,5.05823300,-75.48692600,1,'2026-05-14 18:02:03','2026-05-14 18:10:50'),(106,1,'FMTD LA FLORIDA',NULL,'MANIZALES','Calle 71 #1-19, Villamaría, Caldas, Colombia',NULL,5.03144140,-75.48151090,1,'2026-05-14 18:11:09','2026-05-14 18:12:30'),(107,1,'FMTD VISITACIÓN',NULL,'MEDELLIN','Mixy Mall - La Inferior, Carrera 32 #2 Sur-85, El Poblado, Medellín, Antioquia, Colombia',NULL,6.19764430,-75.56565800,1,'2026-05-14 18:13:44','2026-05-14 18:16:27'),(108,1,'FMTD LA CASTELLANA',NULL,'MONTERÍA','Calle 58 # 6-53, Montería, Córdoba, Colombia',NULL,8.76952700,-75.86772900,1,'2026-05-14 18:16:41','2026-05-14 19:23:37'),(109,1,'FMTD SAN LUCAS',NULL,'MEDELLIN','Calle 20 Sur # 25B-210, El Poblado, Medellín, Antioquia, Colombia',NULL,6.18191680,-75.56690070,1,'2026-05-14 18:16:46','2026-05-14 18:17:38'),(110,1,'FMTD SANTAFÉ',NULL,'MEDELLIN','Cra. 43A #7 Sur-170, El Poblado, Medellín, Antioquia, Colombia',NULL,6.19764430,-75.56565800,1,'2026-05-14 18:17:45','2026-05-14 18:19:00'),(111,1,'FMTD TESORO',NULL,'MEDELLIN','Carrera 25a #1a Sur 45, El Poblado, Medellín, Antioquia, Colombia',NULL,6.19741540,-75.55822490,1,'2026-05-14 18:20:39','2026-05-14 18:21:26'),(112,1,'FMTD MILLA DE ORO',NULL,'MEDELLIN','Calle 5 Sur #42-51, El Poblado, Medellín, Antioquia, Colombia',NULL,6.19922810,-75.57277890,1,'2026-05-14 18:21:30','2026-05-14 18:22:14'),(113,1,'FMTD LA SEBASTIANA',NULL,'MEDELLIN','Carrera 27 #36 Sur-199, Bosque El Chuscal, Envigado, Antioquia, Colombia',NULL,6.16600170,-75.56968350,1,'2026-05-14 18:22:25','2026-05-14 18:29:30'),(114,1,'FMTD CIUDAD DEL RÍO',NULL,'MEDELLIN','Torre Medica, Carrera 48 #19A-18, El Poblado, Medellín, Antioquia, Colombia',NULL,6.22300610,-75.57502200,1,'2026-05-14 18:30:45','2026-05-14 18:32:13'),(115,1,'FMTD PROVENZA',NULL,'MEDELLIN','Calle 10 #35-20, Medellín, Antioquia, Colombia',NULL,6.20892080,-75.56563600,1,'2026-05-14 18:32:17','2026-05-14 18:33:58'),(116,1,'FMTD COLORES',NULL,'MEDELLIN','Cl. 53 #73-20, Laureles - Estadio, Medellín, Antioquia, Colombia',NULL,6.26263460,-75.58768130,1,'2026-05-14 18:34:52','2026-05-14 19:06:08'),(117,1,'FMTD ARKADIA',NULL,'MEDELLIN','Cra. 70 #1-141, Medellín, Antioquia, Colombia',NULL,6.21303090,-75.59471800,1,'2026-05-14 18:44:17','2026-05-14 18:45:25'),(118,1,'FMTD LAURELES',NULL,'MEDELLIN','Cra. 76 #33A - 08, Laureles - Estadio, Medellín, Antioquia, Colombia',NULL,6.23963680,-75.59656680,1,'2026-05-14 19:06:56','2026-05-14 19:09:25'),(119,1,'FMTD LOMA DE LOS BERNAL',NULL,'MEDELLIN','Calle 7 # 80-80, Medellín, Antioquia, Colombia',NULL,6.21816100,-75.60086500,1,'2026-05-14 19:09:33','2026-05-14 19:10:41'),(120,1,'FMTD MALL DEL ESTE',NULL,'MEDELLIN','Carrera 25 #3 - 45, El Poblado, Medellín, Antioquia, Colombia',NULL,6.19857880,-75.55678740,1,'2026-05-14 19:10:51','2026-05-14 19:11:33'),(121,1,'FMTD ALTO DE LAS PALMAS',NULL,'MEDELLIN','Avenida Las Palmas, Medellín, Antioquia, Colombia',NULL,6.21447730,-75.55789230,1,'2026-05-14 19:11:43','2026-05-14 19:18:35'),(122,1,'FMTD LOS PARRA',NULL,'MEDELLIN','Calle 1 Sur #36-71, El Poblado, Medellín, Antioquia, Colombia',NULL,6.20137600,-75.56803170,1,'2026-05-14 19:18:41','2026-05-14 19:19:33'),(123,1,'FMTD LAS VEGAS',NULL,'MEDELLIN','Carrera 48 #15 Sur-160, Medellín, Antioquia, Colombia',NULL,6.19158170,-75.58016910,1,'2026-05-14 19:19:50','2026-05-14 19:20:37'),(124,1,'FMTD EL RECREO',NULL,'MONTERÍA','Cra. 5a #62b-59, Montería, Córdoba, Colombia',NULL,8.77412310,-75.86640780,1,'2026-05-14 19:23:52','2026-05-14 19:24:36'),(125,1,'FMTD PINARES',NULL,'PEREIRA','Av. Juan B Gutierrez #18-45, Pereira, Risaralda, Colombia',NULL,4.80275500,-75.68483700,1,'2026-05-14 19:24:56','2026-05-14 19:25:55'),(126,1,'FMTD AVENIDA LAS AMÉRICAS',NULL,'PEREIRA','Farmatodo, Avenida las Américas, Pereira, Risaralda, Colombia',NULL,4.80642070,-75.71457870,1,'2026-05-14 19:26:03','2026-05-14 19:30:15'),(127,1,'FMTD CERRITOS',NULL,'PEREIRA','R55P+29 LA ESPERANZA, Pereira, Risaralda, Colombia',NULL,4.80756250,-75.81406250,1,'2026-05-14 19:26:12','2026-05-14 19:28:32'),(128,1,'FMTD RUITOQUE',NULL,'PIEDECUESTA','Farmatodo, Piedescuesta, Santander, Colombia',NULL,7.03773500,-73.07350600,1,'2026-05-14 19:30:52','2026-05-14 19:32:14'),(129,1,'FMTD LLANOGRANDE',NULL,'RIONEGRO','Farmatodo Llano Grande, Llanogrande-Rionegro, Rionegro, Antioquia, Colombia',NULL,6.12306970,-75.42462900,1,'2026-05-14 19:33:12','2026-05-14 19:34:27'),(130,1,'FMTD SABANETA',NULL,'SABANETA','Calle 77 Sur #46-56, Sabaneta, Antioquia, Colombia',NULL,6.14796540,-75.62047570,1,'2026-05-14 19:34:43','2026-05-14 19:38:56'),(131,1,'FMTD BARROBLANCO',NULL,'RIONEGRO','Farmatodo Barroblanco, Calle 47, Rionegro, Antioquia, Colombia',NULL,6.15191290,-75.39810610,1,'2026-05-14 19:34:49','2026-05-14 19:35:52'),(132,1,'FMTD SAN NICOLÁS',NULL,'RIONEGRO','Calle 43 #54-139, Rionegro, Antioquia, Colombia',NULL,6.14678890,-75.37831700,1,'2026-05-14 19:36:04','2026-05-14 19:37:14'),(133,1,'FMTD MAYORCA',NULL,'SABANETA','Calle 51 Sur #48-57, Sabaneta, Antioquia, Colombia',NULL,6.16181720,-75.60551530,1,'2026-05-14 19:39:04','2026-05-14 19:41:50'),(134,1,'FMTD EL PRADO',NULL,'SANTA MARTA','Carrera 5 #26-40, Comuna 2, Santa Marta, Magdalena, Colombia',NULL,11.23646460,-74.21055550,1,'2026-05-14 19:42:17','2026-05-14 19:43:44'),(135,1,'FMTD SAN PEDRO',NULL,'SANTA MARTA','Avenida Los Estudiantes, Carrera 12, Comuna 4, Santa Marta, Magdalena, Colombia',NULL,11.24109260,-74.20415230,1,'2026-05-14 19:44:15','2026-05-14 19:47:12'),(136,1,'FMTD RODADERO',NULL,'SANTA MARTA','Calle 11 B #2-31, Santa Marta, Magdalena, Colombia',NULL,11.20056680,-74.22627650,1,'2026-05-14 19:44:29','2026-05-14 19:45:06'),(137,1,'FMTD ANTARES',NULL,'SOACHA','Carrera 7 #30 B - 139, Soacha, Cundinamarca, Colombia',NULL,4.58695190,-74.20556290,1,'2026-05-14 19:47:49','2026-05-14 19:48:45'),(138,1,'FMTD PLAZA DEL SOL',NULL,'SOLEDAD','Farmatodo, CC Plaza Del Sol, Carrera 37, Soledad, Atlántico, Colombia',NULL,10.92676440,-74.78034170,1,'2026-05-14 19:49:04','2026-05-14 19:50:46'),(139,1,'FMTD NOVALITO',NULL,'VALLEDUPAR','Cra. 9 #8A - 72, Valledupar, Cesar, Colombia',NULL,10.48466300,-73.25478760,1,'2026-05-14 19:51:00','2026-05-14 19:55:32'),(140,1,'FMTD EL BUQUE',NULL,'VILLAVICENCIO','Cl. 15 / Avenida Circunvalar, Villavicencio, Meta, Colombia',NULL,4.13402720,-73.63730970,1,'2026-05-14 19:55:50','2026-05-14 20:02:59'),(141,1,'FMTD GUATAPURÍ',NULL,'VALLEDUPAR','Stirpe C.C Guatapurí, Centro Comercial Guatapuri Plaza Comercial, Diagonal 10 N #6N - 15, Valledupar, Cesar, Colombia',NULL,10.49499990,-73.26950480,1,'2026-05-14 19:56:03','2026-05-14 19:58:28'),(142,1,'FMTD SIERRA NEVADA',NULL,'VALLEDUPAR','Carrera 23 # 4-86, Valledupar, Cesar, Colombia',NULL,10.48823200,-73.27230900,1,'2026-05-14 19:58:40','2026-05-14 20:01:10'),(143,1,'FMTD PRIMAVERA URBANA',NULL,'VILLAVICENCIO','el, Cl. 15 #40-01, Villavicencio, Meta, Colombia',NULL,4.13438130,-73.64014320,1,'2026-05-14 20:03:07','2026-05-14 20:05:12'),(144,1,'FMTD PUNTO 99',NULL,'BOGOTÁ','Cra. 11a #98-50, Bogotá, Colombia',NULL,4.68171960,-74.04462220,1,'2026-05-14 20:06:47','2026-05-14 20:09:26'),(145,1,'FMTD PASATIEMPO',NULL,'MONTERÍA','Calle 29 #15 84, Montería, Córdoba, Colombia',NULL,8.74977220,-75.87631310,1,'2026-05-14 20:11:07','2026-05-14 20:12:14'),(146,1,'FMTD FERIA',NULL,'BOGOTÁ','Parque de la 93, Carrera 11, Chapinero, Cundinamarca, Colombia',NULL,4.67672190,-74.04827880,1,'2026-05-14 20:17:26','2026-05-15 19:54:58'),(147,1,'FMTD CAJICÁ',NULL,'CAJICÁ','Farmatodo, Kilómetro 1, Cajicá, Cundinamarca, Colombia',NULL,4.90181320,-74.02507920,1,'2026-05-14 20:55:38','2026-05-14 20:56:27'),(148,1,'FMTD PASATIEMPO',NULL,'MONTERÍA','Calle 29 #15 84, Montería, Córdoba, Colombia',NULL,8.74977220,-75.87631310,1,'2026-05-14 20:58:53','2026-05-14 20:59:44'),(149,1,'FMTD UNICENTRO',NULL,'BOGOTÁ','Avenida Carrera 15 #No. 123-30, Usaquén, Cundinamarca, Colombia',NULL,4.70287200,-74.04188900,1,'2026-05-14 21:00:17','2026-05-14 21:00:49'),(150,1,'FMTD SAN PEDRO',NULL,'SANTA MARTA','Avenida Los Estudiantes, Carrera 12, Comuna 4, Santa Marta, Magdalena, Colombia',NULL,11.24109260,-74.20415230,1,'2026-05-14 21:01:13','2026-05-14 21:04:24'),(152,1,'FMTD NAVARRA',NULL,'BOGOTÁ','Cra 19 #103-98, Bogotá, Colombia',NULL,4.69014340,-74.05108210,1,'2026-05-14 21:06:28','2026-05-14 21:06:55'),(153,1,'FMTD ANTARES',NULL,'SOACHA','Carrera 7 #30 B - 139, Soacha, Cundinamarca, Colombia',NULL,4.58695190,-74.20556290,1,'2026-05-14 21:07:19','2026-05-14 21:07:44'),(154,1,'FMTD CEDRITOS 1',NULL,'BOGOTÁ','Cra 19 #139-78, Usaquén, Cundinamarca, Colombia',NULL,4.72296740,-74.04605430,1,'2026-05-15 19:45:48','2026-05-15 19:46:32'),(155,1,'FMTD CENTRO ANDINO',NULL,'BOGOTÁ','Carrera 11 #82-71, Bogotá, Colombia',NULL,4.66709340,-74.05174830,1,'2026-05-15 19:47:33','2026-05-15 19:48:05'),(156,1,'FMTD ABBOTT ROSALES',NULL,'BOGOTÁ','Diagonal 70a #4-87, Chapinero, Cundinamarca, Colombia',NULL,4.65251690,-74.05439500,0,'2026-05-15 20:00:54','2026-05-29 18:07:30'),(157,1,'FMTD ABBOTT CALLE 127',NULL,'BOGOTÁ','Avenida Calle 127 #17A-27, Bogotá, Colombia',NULL,4.70430510,-74.04618530,0,'2026-05-15 20:01:50','2026-05-29 18:07:40'),(158,1,'FMTD ABBOTT CEDRITOS 1',NULL,'BOGOTÁ','Cra 19 #139-78, Usaquén, Cundinamarca, Colombia',NULL,4.72296740,-74.04605430,0,'2026-05-15 20:02:37','2026-05-29 18:07:53'),(159,1,'FMTD ABBOTT SANTA ISABEL',NULL,'BOGOTÁ','Avenida Carrera 30 #2b-17, Bogotá, Colombia',NULL,4.60330510,-74.10125140,0,'2026-05-15 20:03:18','2026-05-29 18:07:58'),(160,1,'FMTD ABBOTT CAJICÁ',NULL,'CAJICÁ','Farmatodo, Kilómetro 1, Cajicá, Cundinamarca, Colombia',NULL,4.90181320,-74.02507920,0,'2026-05-15 20:04:47','2026-05-29 18:08:13'),(161,1,'FMTD ABBOTT LA CALLEJA',NULL,'BOGOTÁ','Carrera 19 #127-53, Bogotá, Colombia',NULL,4.70680720,-74.04894220,0,'2026-05-15 20:05:30','2026-05-29 18:08:17'),(162,1,'FMTD ABBOTT CALLE 170',NULL,'BOGOTÁ','Calle 170 #65-80, Bogotá, Colombia',NULL,4.75754650,-74.06138320,0,'2026-05-15 20:06:16','2026-05-29 18:08:41'),(163,1,'FMTD ABBOTT NAVARRA',NULL,'BOGOTÁ','Cra 19 #103-98, Bogotá, Colombia',NULL,4.69014340,-74.05108210,0,'2026-05-15 20:07:10','2026-05-29 18:08:47'),(164,1,'FMTD ABBOTT CARRERA 13',NULL,'BOGOTÁ','Carrera 13 #55-61, Bogotá, Colombia',NULL,4.64295420,-74.06475680,0,'2026-05-15 20:07:43','2026-05-29 18:08:52'),(165,1,'FMTD ABBOTT CARERRA 16',NULL,'BOGOTÁ','Carrera 16 #82-52, Chapinero, Cundinamarca, Colombia',NULL,4.66833940,-74.05666090,0,'2026-05-15 20:10:17','2026-05-29 18:08:56'),(166,1,'FMTD LAS MERCEDES','CARRERA 28 CON CALLE 54 / PALMIRA, VALLE','PALMIRA','Carrera 28 & Calle 54, Palmira, Valle del Cauca, Colombia',NULL,3.54660160,-76.29551310,1,'2026-06-01 01:17:52','2026-06-01 01:17:52');
/*!40000 ALTER TABLE `locations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `material_categories`
--

DROP TABLE IF EXISTS `material_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `material_categories` (
  `material_category_id` int unsigned NOT NULL AUTO_INCREMENT,
  `material_category_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `material_category_description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`material_category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `material_categories`
--

LOCK TABLES `material_categories` WRITE;
/*!40000 ALTER TABLE `material_categories` DISABLE KEYS */;
INSERT INTO `material_categories` VALUES (1,'TUBERIAS',NULL,1,'2026-05-07 14:40:02','2026-05-07 14:40:02'),(2,'HERRAMIENTA',NULL,1,'2026-05-07 14:40:03','2026-05-07 14:40:03'),(3,'CABLES - ALAMBRES',NULL,1,'2026-05-07 14:40:04','2026-05-07 14:40:04'),(4,'ARTICULOS ELECTRICOS',NULL,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(5,'REDES ESTRUCTURADAS',NULL,1,'2026-05-07 14:40:06','2026-05-07 14:40:06'),(6,'INTERRUPTORES - TOMACORRIENTES',NULL,1,'2026-05-07 14:40:06','2026-05-07 14:40:06'),(7,'HERRAJES ELECTRICOS',NULL,1,'2026-05-07 14:40:06','2026-05-07 14:40:06'),(8,'BREAKER - TABLEROS',NULL,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(9,'ILUMINACION',NULL,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(10,'HERRAMIENTAS',NULL,1,'2026-05-07 14:40:08','2026-05-07 14:40:08');
/*!40000 ALTER TABLE `material_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `material_requisition`
--

DROP TABLE IF EXISTS `material_requisition`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `material_requisition` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `requisition_id` bigint NOT NULL,
  `material_id` bigint NOT NULL,
  `quantity` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `material_requisition`
--

LOCK TABLES `material_requisition` WRITE;
/*!40000 ALTER TABLE `material_requisition` DISABLE KEYS */;
INSERT INTO `material_requisition` VALUES (1,1,2,50,'2026-06-09 22:01:06','2026-06-09 22:01:06');
/*!40000 ALTER TABLE `material_requisition` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `materials`
--

DROP TABLE IF EXISTS `materials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `materials` (
  `material_id` int unsigned NOT NULL AUTO_INCREMENT,
  `material_category_id` bigint NOT NULL,
  `material_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `material_description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `material_unit` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `stock` int NOT NULL DEFAULT '0',
  `active` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`material_id`)
) ENGINE=InnoDB AUTO_INCREMENT=531 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `materials`
--

LOCK TABLES `materials` WRITE;
/*!40000 ALTER TABLE `materials` DISABLE KEYS */;
INSERT INTO `materials` VALUES (1,1,'TUBO EMT 2\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:02','2026-05-07 14:40:02'),(2,1,'TUBO EMT 3\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:02','2026-06-09 22:02:53'),(3,1,'TUBO EMT 1/2\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:02','2026-05-07 14:40:02'),(4,1,'TUBO EMT 1-1/4\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:02','2026-05-07 14:40:02'),(5,1,'TUBO EMT 1/4\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:02','2026-05-07 14:40:02'),(6,1,'TUBO EMT 1-1/2 X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:02','2026-05-07 14:40:02'),(7,1,'TUBO EMT 1\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:02','2026-05-07 14:40:02'),(8,1,'TUBO EMT 3/4\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:02','2026-05-07 14:40:02'),(9,1,'TUBO SCH40 1-1/2\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:02','2026-05-07 14:40:02'),(10,1,'TUBO SCH40 1-1/4\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:02','2026-05-07 14:40:02'),(11,1,'TUBO SCH40 1/2\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:02','2026-05-07 14:40:02'),(12,1,'TUBO SCH40 1\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:02','2026-05-07 14:40:02'),(13,1,'TUBO SCH40 2\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:02','2026-05-07 14:40:02'),(14,1,'TUBO SCH40 3/4\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:02','2026-05-07 14:40:02'),(15,1,'TUBO SCH40 3\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:02','2026-05-07 14:40:02'),(16,1,'TUBO SCH40 1/4\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:02','2026-05-07 14:40:02'),(17,1,'TUBO PVC 1/2\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:02','2026-05-07 14:40:02'),(18,1,'TUBO PVC 1\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:02','2026-05-07 14:40:02'),(19,1,'TUBO PVC 3/4\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:02','2026-05-07 14:40:02'),(20,1,'TUBO PVC 2\'\' X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:03','2026-05-07 14:40:03'),(21,1,'TUBO PVC 1-1/2\'\' X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:03','2026-05-07 14:40:03'),(22,1,'TUBO PVC 1-1/4\'\' X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:03','2026-05-07 14:40:03'),(23,1,'TUBO IMC 2\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:03','2026-05-07 14:40:03'),(24,1,'TUBO IMC 3\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:03','2026-05-07 14:40:03'),(25,1,'TUBO IMC 1/2\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:03','2026-05-07 14:40:03'),(26,1,'TUBO IMC 1-1/4\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:03','2026-05-07 14:40:03'),(27,1,'TUBO IMC 1/4\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:03','2026-05-07 14:40:03'),(28,1,'TUBO IMC 1-1/2 X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:03','2026-05-07 14:40:03'),(29,1,'TUBO IMC 1\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:03','2026-05-07 14:40:03'),(30,1,'TUBO IMC 3/4\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:03','2026-05-07 14:40:03'),(31,2,'CAUCHO DOBLATUBO 3/4\"',NULL,'UNIDAD',0,1,'2026-05-07 14:40:03','2026-05-07 14:40:03'),(32,2,'CAUCHO DOBLATUBO 1/2\"',NULL,'UNIDAD',0,1,'2026-05-07 14:40:03','2026-05-07 14:40:03'),(33,2,'CAUCHO DOBLATUBO 1\"',NULL,'UNIDAD',0,1,'2026-05-07 14:40:03','2026-05-07 14:40:03'),(34,1,'CURVA EMT 2\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:03','2026-05-07 14:40:03'),(35,1,'CURVA EMT 3\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:03','2026-05-07 14:40:03'),(36,1,'CURVA EMT 1/2\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:03','2026-05-07 14:40:03'),(37,1,'CURVA EMT 1-1/4\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:03','2026-05-07 14:40:03'),(38,1,'CURVA EMT 1/4\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:03','2026-05-07 14:40:03'),(39,1,'CURVA EMT 1-1/2 X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:03','2026-05-07 14:40:03'),(40,1,'CURVA EMT 1\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:03','2026-05-07 14:40:03'),(41,1,'CURVA EMT 3/4\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:03','2026-05-07 14:40:03'),(42,1,'CURVA SCH40 1-1/2\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:03','2026-05-07 14:40:03'),(43,1,'CURVA SCH40 1-1/4\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:03','2026-05-07 14:40:03'),(44,1,'CURVA SCH40 1/2\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:03','2026-05-07 14:40:03'),(45,1,'CURVA SCH40 1\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:03','2026-05-07 14:40:03'),(46,1,'CURVA SCH40 2\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:03','2026-05-07 14:40:03'),(47,1,'CURVA SCH40 3/4\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:03','2026-05-07 14:40:03'),(48,1,'CURVA SCH40 3\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:03','2026-05-07 14:40:03'),(49,1,'CURVA SCH40 1/4\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:03','2026-05-07 14:40:03'),(50,1,'CURVA PVC 1/2\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:03','2026-05-07 14:40:03'),(51,1,'CURVA PVC 1\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:03','2026-05-07 14:40:03'),(52,1,'CURVA PVC 3/4\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:03','2026-05-07 14:40:03'),(53,1,'CURVA PVC 2\'\' X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:03','2026-05-07 14:40:03'),(54,1,'CURVA PVC 1-1/2\'\' X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:03','2026-05-07 14:40:03'),(55,1,'CURVA PVC 1-1/4\'\' X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:03','2026-05-07 14:40:03'),(56,1,'CURVA IMC 2\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:03','2026-05-07 14:40:03'),(57,1,'CURVA IMC 3\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:03','2026-05-07 14:40:03'),(58,1,'CURVA IMC 1/2\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:03','2026-05-07 14:40:03'),(59,1,'CURVA IMC 1-1/4\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:03','2026-05-07 14:40:03'),(60,1,'CURVA IMC 1/4\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:03','2026-05-07 14:40:03'),(61,1,'CURVA IMC 1-1/2 X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:03','2026-05-07 14:40:03'),(62,1,'CURVA IMC 1\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:04','2026-05-07 14:40:04'),(63,1,'CURVA IMC 3/4\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:04','2026-05-07 14:40:04'),(64,1,'TERMIANAL EMT 2\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:04','2026-05-07 14:40:04'),(65,1,'TERMINAL EMT 3\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:04','2026-05-07 14:40:04'),(66,1,'TERMINAL EMT 1/2\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:04','2026-05-07 14:40:04'),(67,1,'TERMINAL EMT 1-1/4\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:04','2026-05-07 14:40:04'),(68,1,'TERMINAL EMT 1/4\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:04','2026-05-07 14:40:04'),(69,1,'TERMINAL EMT 1-1/2 X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:04','2026-05-07 14:40:04'),(70,1,'TERMINAL EMT 1\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:04','2026-05-07 14:40:04'),(71,1,'TERMINAL EMT 3/4\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:04','2026-05-07 14:40:04'),(72,1,'TERMINAL SCH40 1-1/2\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:04','2026-05-07 14:40:04'),(73,1,'TERMINAL SCH40 1-1/4\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:04','2026-05-07 14:40:04'),(74,1,'TERMINAL SCH40 1/2\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:04','2026-05-07 14:40:04'),(75,1,'TERMINAL SCH40 1\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:04','2026-05-07 14:40:04'),(76,1,'TERMINAL SCH40 2\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:04','2026-05-07 14:40:04'),(77,1,'TERMINAL SCH40 3/4\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:04','2026-05-07 14:40:04'),(78,1,'TERMINAL SCH40 3\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:04','2026-05-07 14:40:04'),(79,1,'TERMINAL SCH40 1/4\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:04','2026-05-07 14:40:04'),(80,1,'TERMINAL PVC 1/2\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:04','2026-05-07 14:40:04'),(81,1,'TERMINAL PVC 1\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:04','2026-05-07 14:40:04'),(82,1,'TERMINAL PVC 3/4\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:04','2026-05-07 14:40:04'),(83,1,'TERMINAL PVC 2\'\' X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:04','2026-05-07 14:40:04'),(84,1,'TERMINAL PVC 1-1/2\'\' X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:04','2026-05-07 14:40:04'),(85,1,'TERMINAL PVC 1-1/4\'\' X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:04','2026-05-07 14:40:04'),(86,1,'TERMINAL IMC 2\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:04','2026-05-07 14:40:04'),(87,1,'TERMINAL IMC 3\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:04','2026-05-07 14:40:04'),(88,1,'TERMINAL IMC 1/2\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:04','2026-05-07 14:40:04'),(89,1,'TERMINAL IMC 1-1/4\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:04','2026-05-07 14:40:04'),(90,1,'TERMINAL IMC 1/4\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:04','2026-05-07 14:40:04'),(91,1,'TERMINAL IMC 1-1/2 X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:04','2026-05-07 14:40:04'),(92,1,'TERMINAL IMC 1\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:04','2026-05-07 14:40:04'),(93,1,'TERMINAL IMC 3/4\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:04','2026-05-07 14:40:04'),(94,1,'TERMINAL 2 AWG',NULL,'UNIDAD',0,1,'2026-05-07 14:40:04','2026-05-07 14:40:04'),(95,1,'TERMINAL 4 AWG',NULL,'UNIDAD',0,1,'2026-05-07 14:40:04','2026-05-07 14:40:04'),(96,1,'TERMINAL 6 AWG',NULL,'UNIDAD',0,1,'2026-05-07 14:40:04','2026-05-07 14:40:04'),(97,1,'TERMINAL 1/0 AWG',NULL,'UNIDAD',0,1,'2026-05-07 14:40:04','2026-05-07 14:40:04'),(98,1,'TERMINAL 2/0 AWG',NULL,'UNIDAD',0,1,'2026-05-07 14:40:04','2026-05-07 14:40:04'),(99,1,'TERMINAL 8 AWG',NULL,'UNIDAD',0,1,'2026-05-07 14:40:04','2026-05-07 14:40:04'),(100,1,'TERMINAL 250 AWG',NULL,'UNIDAD',0,1,'2026-05-07 14:40:04','2026-05-07 14:40:04'),(101,1,'TERMINAL 12 AWG',NULL,'UNIDAD',0,1,'2026-05-07 14:40:04','2026-05-07 14:40:04'),(102,1,'UNION EMT 2\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:04','2026-05-07 14:40:04'),(103,1,'UNION EMT 3\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:04','2026-05-07 14:40:04'),(104,1,'UNION EMT 1/2\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:04','2026-05-07 14:40:04'),(105,1,'UNION EMT 1-1/4\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:04','2026-05-07 14:40:04'),(106,1,'UNION EMT 1/4\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:04','2026-05-07 14:40:04'),(107,1,'UNION EMT 1-1/2 X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:04','2026-05-07 14:40:04'),(108,1,'UNION EMT 1\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:04','2026-05-07 14:40:04'),(109,1,'UNION EMT 3/4\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:04','2026-05-07 14:40:04'),(110,1,'UNION SCH40 1-1/2\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:04','2026-05-07 14:40:04'),(111,1,'UNION SCH40 1-1/4\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:04','2026-05-07 14:40:04'),(112,1,'UNION SCH40 1/2\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:04','2026-05-07 14:40:04'),(113,1,'UNION SCH40 1\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:04','2026-05-07 14:40:04'),(114,1,'UNION SCH40 2\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:04','2026-05-07 14:40:04'),(115,1,'UNION SCH40 3/4\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:04','2026-05-07 14:40:04'),(116,1,'UNION SCH40 3\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:04','2026-05-07 14:40:04'),(117,1,'UNION SCH40 1/4\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:04','2026-05-07 14:40:04'),(118,1,'UNION PVC 1/2\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:04','2026-05-07 14:40:04'),(119,1,'UNION PVC 1\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:04','2026-05-07 14:40:04'),(120,1,'UNION PVC 3/4\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:04','2026-05-07 14:40:04'),(121,1,'UNION PVC 2\'\' X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:04','2026-05-07 14:40:04'),(122,1,'UNION PVC 1-1/2\'\' X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:04','2026-05-07 14:40:04'),(123,1,'UNION PVC 1-1/4\'\' X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:04','2026-05-07 14:40:04'),(124,1,'UNION IMC 2\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:04','2026-05-07 14:40:04'),(125,1,'UNION IMC 3\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:04','2026-05-07 14:40:04'),(126,1,'UNION IMC 1/2\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:04','2026-05-07 14:40:04'),(127,1,'UNION IMC 1-1/4\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:04','2026-05-07 14:40:04'),(128,1,'UNION IMC 1/4\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:04','2026-05-07 14:40:04'),(129,1,'UNION IMC 1-1/2 X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:04','2026-05-07 14:40:04'),(130,1,'UNION IMC 1\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:04','2026-05-07 14:40:04'),(131,1,'UNION IMC 3/4\" X 3MTS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:04','2026-05-07 14:40:04'),(132,3,'CABLE CU DUPLEX 2X14AWG BLANCO',NULL,'METROS',0,1,'2026-05-07 14:40:04','2026-05-07 14:40:04'),(133,3,'CABLE CU DUPLEX 2X16AWG NEGRO',NULL,'METROS',0,1,'2026-05-07 14:40:04','2026-05-07 14:40:04'),(134,3,'CABLE CU DUPLEX 2X16AWG BLANCO',NULL,'METROS',0,1,'2026-05-07 14:40:04','2026-05-07 14:40:04'),(135,3,'CABLE CU DUPLEX 2X14AWG NEGRO',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(136,3,'CABLE ENCAUCHETADO 3X10AWG',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(137,3,'CABLE ENCAUCHETADO 3X12AWG',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(138,3,'CABLE ENCAUCHETADO 3X14AWG',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(139,3,'CABLE ENCAUCHETADO 3X16AWG',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(140,3,'CABLE ENCAUCHETADO 2X10AWG',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(141,3,'CABLE ENCAUCHETADO 2X12AWG',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(142,3,'CABLE ENCAUCHETADO 2X14AWG',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(143,3,'CABLE ENCAUCHETADO 2X16AWG',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(144,3,'CABLE ENCAUCHETADO 4X10AWG',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(145,3,'CABLE ENCAUCHETADO 4X12AWG',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(146,3,'CABLE ENCAUCHETADO 4X14AWG',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(147,3,'CABLE ENCAUCHETADO 4X16AWG',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(148,3,'CABLE LSZH-FR #10 BLANCO',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(149,3,'CABLE LSZH-FR #10 ROJO',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(150,3,'CABLE LSZH-FR #10 VERDE',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(151,3,'CABLE LSZH-FR #10 AZUL',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(152,3,'CABLE LSZH-FR #10 AMARILLO',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(153,3,'CABLE LSZH-FR #10 NEGRO',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(154,3,'CABLE LSZH-FR #12 BLANCO',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(155,3,'CABLE LSZH-FR #12 ROJO',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(156,3,'CABLE LSZH-FR #12 VERDE',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(157,3,'CABLE LSZH-FR #12 AZUL',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(158,3,'CABLE LSZH-FR #12 AMARILLO',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(159,3,'CABLE LSZH-FR #12 NEGRO',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(160,3,'CABLE LSZH-FR #14 BLANCO',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(161,3,'CABLE LSZH-FR #14 ROJO',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(162,3,'CABLE LSZH-FR #14 VERDE',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(163,3,'CABLE LSZH-FR #14 AZUL',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(164,3,'CABLE LSZH-FR #14 AMARILLO',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(165,3,'CABLE LSZH-FR #14 NEGRO',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(166,3,'CABLE LSZH-FR #8 BLANCO',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(167,3,'CABLE LSZH-FR #8 ROJO',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(168,3,'CABLE LSZH-FR #8 VERDE',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(169,3,'CABLE LSZH-FR #8 AZUL',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(170,3,'CABLE LSZH-FR #8 AMARILLO',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(171,3,'CABLE LSZH-FR #8 NEGRO',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(172,3,'CABLE LSZH-FR #6 BLANCO',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(173,3,'CABLE LSZH-FR #6 ROJO',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(174,3,'CABLE LSZH-FR #6 VERDE',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(175,3,'CABLE LSZH-FR #6 AZUL',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(176,3,'CABLE LSZH-FR #6 AMARILLO',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(177,3,'CABLE LSZH-FR #6 NEGRO',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(178,3,'CABLE LSZH-FR #2 NEGRO',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(179,3,'CABLE CU THHN/THWN-2 #2 NEGRO',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(180,3,'CABLE LSZH-FR #4 NEGRO',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(181,3,'CABLE CU THHN/THWN-2 #4 NEGRO',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(182,3,'CABLE AL THHN/THWN-2 1/0AWG NEGRO',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(183,3,'CABLE AL THHN/THWN-2 2/0AWG NEGRO',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(184,3,'CABLE AL THHN/THWN-2 4/0AWG NEGRO',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(185,3,'CABLE CU THHN/THWN-2 1/0AWG NEGRO',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(186,3,'CABLE CU THHN/THWN-2 2/0AWG NEGRO',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(187,3,'CABLE CU THHN/THWN-2 4/0AWG NEGRO',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(188,3,'CABLE LSZH-FR 1/0AWG NEGRO',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(189,3,'CABLE LSZH-FR 2/0AWG NEGRO',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(190,3,'CABLE LSZH-FR 4/0AWG NEGRO',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(191,3,'CABLE LSZH-FR TRENZADO 3X12 B-V-R',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(192,3,'CABLE THHN/THWN-2 TRENZADO 3X12 B-V-R',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(193,3,'CABLE THHN/THHN-2 TRENZADO 3x12 BL-VD-AZ',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(194,3,'CABLE LSZH-FR TRENZADO 3x12 BL-VD-AZ',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(195,3,'CABLE CU THHN/THWN-2 #10 BLANCO',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(196,3,'CABLE CU THHN/THWN-2 #10 ROJO',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(197,3,'CABLE CU THHN/THWN-2 #10 VERDE',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(198,3,'CABLE CU THHN/THWN-2 #10 AZUL',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(199,3,'CABLE CU THHN/THWN-2 #10 AMARILLO',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(200,3,'CABLE CU THHN/THWN-2 #10 NEGRO',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(201,3,'CABLE CU THHN/THWN-2 #12 BLANCO',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(202,3,'CABLE CU THHN/THWN-2 #12 ROJO',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(203,3,'CABLE CU THHN/THWN-2 #12 VERDE',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(204,3,'CABLE CU THHN/THWN-2 #12 AZUL',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(205,3,'CABLE CU THHN/THWN-2 #12 AMARILLO',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(206,3,'CABLE CU THHN/THWN-2 #12 NEGRO',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(207,3,'CABLE CU THHN/THWN-2 #14 BLANCO',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(208,3,'CABLE CU THHN/THWN-2 #14 ROJO',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(209,3,'CABLE CU THHN/THWN-2 #14 VERDE',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(210,3,'CABLE CU THHN/THWN-2 #14 AZUL',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(211,3,'CABLE CU THHN/THWN-2 #14 AMARILLO',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(212,3,'CABLE CU THHN/THWN-2 #14 NEGRO',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(213,3,'CABLE CU THHN/THWN-2 #8 BLANCO',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(214,3,'CABLE CU THHN/THWN-2 #8 ROJO',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(215,3,'CABLE CU THHN/THWN-2 #8 VERDE',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(216,3,'CABLE CU THHN/THWN-2 #8 AZUL',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(217,3,'CABLE CU THHN/THWN-2 #8 AMARILLO',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(218,3,'CABLE CU THHN/THWN-2 #8 NEGRO',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(219,3,'CABLE CU THHN/THWN-2 #6 BLANCO',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(220,3,'CABLE CU THHN/THWN-2 #6 ROJO',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(221,3,'CABLE CU THHN/THWN-2 #6 VERDE',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(222,3,'CABLE CU THHN/THWN-2 #6 AZUL',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(223,3,'CABLE CU THHN/THWN-2 #6 AMARILLO',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(224,3,'CABLE CU THHN/THWN-2 #6 NEGRO',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(225,3,'CABLE CU THHN/THWN-2 250 NEGRO',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(226,3,'ALAMBRE DESNUDO #12',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(227,3,'ALAMBRE DESNUDO #10',NULL,'METROS',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(228,4,'CINTA 3M TEMPLEX NEGRO',NULL,'UNIDAD',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(229,4,'CINTA 3M TEMPLEX ROJO',NULL,'UNIDAD',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(230,4,'CINTA 3M TEMPLEX BLANCO',NULL,'UNIDAD',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(231,4,'CINTA 3M TEMPLEX AZUL',NULL,'UNIDAD',0,1,'2026-05-07 14:40:05','2026-05-07 14:40:05'),(232,4,'CINTA 3M TEMPLEX VERDE',NULL,'UNIDAD',0,1,'2026-05-07 14:40:06','2026-05-07 14:40:06'),(233,4,'CINTA 3M TEMPLEX MARRON',NULL,'UNIDAD',0,1,'2026-05-07 14:40:06','2026-05-07 14:40:06'),(234,4,'CINTA 3M TEMPLEX NARANJA',NULL,'UNIDAD',0,1,'2026-05-07 14:40:06','2026-05-07 14:40:06'),(235,4,'CINTA 3M TEMPLEX GRIS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:06','2026-05-07 14:40:06'),(236,4,'CINTA 3M TEMPLEX MORADO',NULL,'UNIDAD',0,1,'2026-05-07 14:40:06','2026-05-07 14:40:06'),(237,4,'CINTA 3M TEMPLEX AMARILLO',NULL,'UNIDAD',0,1,'2026-05-07 14:40:06','2026-05-07 14:40:06'),(238,4,'CINTA DOBLE FAZ',NULL,'UNIDAD',0,1,'2026-05-07 14:40:06','2026-05-07 14:40:06'),(239,4,'CINTA 3M 23',NULL,'UNIDAD',0,1,'2026-05-07 14:40:06','2026-05-07 14:40:06'),(240,4,'CINTA 3M SUPER 33+',NULL,'UNIDAD',0,1,'2026-05-07 14:40:06','2026-05-07 14:40:06'),(241,4,'PAPEL CONTACT ROJO',NULL,'METROS',0,1,'2026-05-07 14:40:06','2026-05-07 14:40:06'),(242,4,'PAPEL CONTACT NARANJA',NULL,'METROS',0,1,'2026-05-07 14:40:06','2026-05-07 14:40:06'),(243,4,'PAPEL CONTACT AZUL',NULL,'METROS',0,1,'2026-05-07 14:40:06','2026-05-07 14:40:06'),(244,4,'PAPEL CONTACT AMARILLO',NULL,'METROS',0,1,'2026-05-07 14:40:06','2026-05-07 14:40:06'),(245,3,'CABLE UTP CAT 5E',NULL,'METROS',0,1,'2026-05-07 14:40:06','2026-05-07 14:40:06'),(246,3,'CABLE UTP CAT 6',NULL,'METROS',0,1,'2026-05-07 14:40:06','2026-05-07 14:40:06'),(247,3,'CABLE F/UTP CAT 6A',NULL,'METROS',0,1,'2026-05-07 14:40:06','2026-05-07 14:40:06'),(248,5,'PCORD FUTP 6A AZ 1M',NULL,'UNIDADES',0,1,'2026-05-07 14:40:06','2026-05-07 14:40:06'),(249,5,'PCORD FUTP 6A AZ 2M',NULL,'UNIDADES',0,1,'2026-05-07 14:40:06','2026-05-07 14:40:06'),(250,5,'PCORD FUTP 6A AZ 3M',NULL,'UNIDADES',0,1,'2026-05-07 14:40:06','2026-05-07 14:40:06'),(251,5,'JACK FUTP CAT 6A BL',NULL,'UNIDADES',0,1,'2026-05-07 14:40:06','2026-05-07 14:40:06'),(252,5,'JACK FUTP CAT 6A NG',NULL,'UNIDADES',0,1,'2026-05-07 14:40:06','2026-05-07 14:40:06'),(253,1,'PRENSA ESTOPA 1/2\" 13.5MM PG13',NULL,'UNIDADES',0,1,'2026-05-07 14:40:06','2026-05-07 14:40:06'),(254,1,'PRENSA ESTOPA 3/8\" 11MM PG11',NULL,'UNIDADES',0,1,'2026-05-07 14:40:06','2026-05-07 14:40:06'),(255,4,'CONECTOR AUTODESFORRE 3M 10 - 12 AM',NULL,'UNIDADES',0,1,'2026-05-07 14:40:06','2026-05-07 14:40:06'),(256,1,'SOLDADURA PVC 1/4',NULL,'GALON',0,1,'2026-05-07 14:40:06','2026-05-07 14:40:06'),(257,1,'SOLDADURA PVC 1/2',NULL,'GALON',0,1,'2026-05-07 14:40:06','2026-05-07 14:40:06'),(258,1,'SOLDADURA PVC 1/8',NULL,'GALON',0,1,'2026-05-07 14:40:06','2026-05-07 14:40:06'),(259,1,'SOLDADURA PVC 1 1/16',NULL,'GALON',0,1,'2026-05-07 14:40:06','2026-05-07 14:40:06'),(260,6,'TOMA AEREA CAUCHO POLO A TIERRA 15A 125V',NULL,'UNIDAD',0,1,'2026-05-07 14:40:06','2026-05-07 14:40:06'),(261,6,'TOMA AEREA CAUCHO 15A 125V',NULL,'UNIDAD',0,1,'2026-05-07 14:40:06','2026-05-07 14:40:06'),(262,6,'TOMA AEREA MIRADA CHINA 20A 250V',NULL,'UNIDAD',0,1,'2026-05-07 14:40:06','2026-05-07 14:40:06'),(263,7,'VARILLA ROSCADA ZINCADA 3/8 X 3 MT',NULL,'UNIDAD',0,1,'2026-05-07 14:40:06','2026-05-07 14:40:06'),(264,7,'TUERCA + ARANDELA VARILLA ROSCADA ZINCADA 3/8',NULL,'UNIDAD',0,1,'2026-05-07 14:40:06','2026-05-07 14:40:06'),(265,7,'VARILLA ROSCADA ZINCADA 1/4 X 3 MT',NULL,'UNIDAD',0,1,'2026-05-07 14:40:06','2026-05-07 14:40:06'),(266,7,'TUERCA + ARANDELA VARILLA ROSCADA ZINCADA 1/4',NULL,'UNIDAD',0,1,'2026-05-07 14:40:06','2026-05-07 14:40:06'),(267,7,'VARILLA ROSCADA ZINCADA 1/8 X 3 MT',NULL,'UNIDAD',0,1,'2026-05-07 14:40:06','2026-05-07 14:40:06'),(268,7,'TUERCA + ARANDELA VARILLA ROSCADA ZINCADA 1/8',NULL,'UNIDAD',0,1,'2026-05-07 14:40:06','2026-05-07 14:40:06'),(269,4,'RIEL CHANNEL 2X4 X 3 MT',NULL,'UNIDAD',0,1,'2026-05-07 14:40:06','2026-05-07 14:40:06'),(270,4,'RIEL CHANNEL 4X4 X 3 MT',NULL,'UNIDAD',0,1,'2026-05-07 14:40:06','2026-05-07 14:40:06'),(271,4,'AMARRE DEXSON T8\" 20CM X 100 NG',NULL,'PAQUETE',0,1,'2026-05-07 14:40:06','2026-05-07 14:40:06'),(272,4,'CONECTOR RESORTE 3M',NULL,'UNIDAD',0,1,'2026-05-07 14:40:06','2026-05-07 14:40:06'),(273,7,'ABRAZADERA DOBLE ALA 1/2\"',NULL,'UNIDAD',0,1,'2026-05-07 14:40:06','2026-05-07 14:40:06'),(274,7,'ABRAZADERA DOBLE ALA 3/4\"',NULL,'UNIDAD',0,1,'2026-05-07 14:40:06','2026-05-07 14:40:06'),(275,7,'ABRAZADERA DOBLE ALA 1\"',NULL,'UNIDAD',0,1,'2026-05-07 14:40:06','2026-05-07 14:40:06'),(276,7,'ABRAZADERA DOBLE ALA 1 1/2\"',NULL,'UNIDAD',0,1,'2026-05-07 14:40:06','2026-05-07 14:40:06'),(277,7,'ABRAZADERA DOBLE ALA 1 1/4\"',NULL,'UNIDAD',0,1,'2026-05-07 14:40:06','2026-05-07 14:40:06'),(278,7,'ABRAZADERA DOBLE ALA 2\"',NULL,'UNIDAD',0,1,'2026-05-07 14:40:06','2026-05-07 14:40:06'),(279,7,'ABRAZADERA DOBLE ALA 3\"',NULL,'UNIDAD',0,1,'2026-05-07 14:40:06','2026-05-07 14:40:06'),(280,7,'ABRAZADERA AJUSTABLE 1/2\"',NULL,'UNIDAD',0,1,'2026-05-07 14:40:06','2026-05-07 14:40:06'),(281,7,'ABRAZADERA AJUSTABLE 3/4\"',NULL,'UNIDAD',0,1,'2026-05-07 14:40:06','2026-05-07 14:40:06'),(282,7,'ABRAZADERA AJUSTABLE 1\"',NULL,'UNIDAD',0,1,'2026-05-07 14:40:06','2026-05-07 14:40:06'),(283,7,'ABRAZADERA AJUSTABLE 1 1/2\"',NULL,'UNIDAD',0,1,'2026-05-07 14:40:06','2026-05-07 14:40:06'),(284,7,'ABRAZADERA AJUSTABLE 1 1/4\"',NULL,'UNIDAD',0,1,'2026-05-07 14:40:06','2026-05-07 14:40:06'),(285,7,'ABRAZADERA AJUSTABLE 2\"',NULL,'UNIDAD',0,1,'2026-05-07 14:40:06','2026-05-07 14:40:06'),(286,7,'ABRAZADERA AJUSTABLE 3\"',NULL,'UNIDAD',0,1,'2026-05-07 14:40:06','2026-05-07 14:40:06'),(287,7,'ABRAZADERA TIPO PERA 1/2\"',NULL,'UNIDAD',0,1,'2026-05-07 14:40:06','2026-05-07 14:40:06'),(288,7,'ABRAZADERA TIPO PERA 3/4\"',NULL,'UNIDAD',0,1,'2026-05-07 14:40:06','2026-05-07 14:40:06'),(289,7,'ABRAZADERA TIPO PERA 1\"',NULL,'UNIDAD',0,1,'2026-05-07 14:40:06','2026-05-07 14:40:06'),(290,7,'ABRAZADERA TIPO PERA 1 1/2\"',NULL,'UNIDAD',0,1,'2026-05-07 14:40:06','2026-05-07 14:40:06'),(291,7,'ABRAZADERA TIPO PERA 1 1/4\"',NULL,'UNIDAD',0,1,'2026-05-07 14:40:06','2026-05-07 14:40:06'),(292,7,'ABRAZADERA TIPO PERA 2\"',NULL,'UNIDAD',0,1,'2026-05-07 14:40:06','2026-05-07 14:40:06'),(293,7,'ABRAZADERA TIPO PERA 3\"',NULL,'UNIDAD',0,1,'2026-05-07 14:40:06','2026-05-07 14:40:06'),(294,7,'ABRAZADERA DOBLE ALA 1/2\" EMT',NULL,'UNIDAD',0,1,'2026-05-07 14:40:06','2026-05-07 14:40:06'),(295,7,'ABRAZADERA DOBLE ALA 3/4\" EMT',NULL,'UNIDAD',0,1,'2026-05-07 14:40:06','2026-05-07 14:40:06'),(296,7,'ABRAZADERA DOBLE ALA 1\" EMT',NULL,'UNIDAD',0,1,'2026-05-07 14:40:06','2026-05-07 14:40:06'),(297,7,'ABRAZADERA DOBLE ALA 1 1/2\" EMT',NULL,'UNIDAD',0,1,'2026-05-07 14:40:06','2026-05-07 14:40:06'),(298,7,'ABRAZADERA DOBLE ALA 1 1/4\" EMT',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(299,7,'ABRAZADERA DOBLE ALA 2\" EMT',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(300,7,'ABRAZADERA DOBLE ALA 3\" EMT',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(301,5,'BANDEJA CABLOFIL 30 X 5,4 CM X 3 MT',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(302,5,'SOPORTE CABLOFIL TUBO 1/2 - 3/4',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(303,4,'CAJA METALICA 10X10 DOBLE FONDO',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(304,4,'CAJA SCH40 10X10 DOBLE FONDO',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(305,4,'CAJA METALICA 2400',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(306,4,'CAJA SCH40 2400',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(307,4,'CAJA METALICA 5800',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(308,4,'CAJA SCH40 5800',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(309,4,'TAPA CIEGA SCH40 5800',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(310,4,'TAPA CIEGA METALICA 5800',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(311,4,'TAPA CIEGA SCH40 2400',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(312,4,'TAPA CIEGA METALICA 2400',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(313,4,'SUPLEMENTO METALICO 2400',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(314,4,'SUPLEMENTO SCH40 2400',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(315,4,'SUPLEMENTO METALICO 5800',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(316,4,'SUPLEMENTO SCH40 5800',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(317,6,'TOMA DOBLE 15A 125V BL',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(318,6,'TAPA TOMA DOBLE BL',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(319,6,'TOMA DOBLE 15A 125V NJA',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(320,6,'TAPA TOMA DOBLE NJA',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(321,6,'TOMA GFCI 15A BL',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(322,1,'CORAZA METAL 1/2\"',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(323,1,'CORAZA METAL 3/4\"',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(324,1,'CORAZA METAL 1\"',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(325,1,'CORAZA METAL 2\"',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(326,1,'CORAZA PVC 1/2\"',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(327,1,'CORAZA PVC 3/4\"',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(328,1,'CORAZA PVC 1\"',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(329,1,'CORAZA PVC 2\"',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(330,1,'CONECTOR RECTO PARA CORAZA 1/2\"',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(331,1,'CONECTOR RECTO PARA CORAZA 3/4\"',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(332,1,'CONECTOR RECTO PARA CORAZA 1\"',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(333,1,'CONECTOR RECTO PARA CORAZA 2\"',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(334,1,'CONECTOR CURVO PARA CORAZA 1/2\"',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(335,1,'CONECTOR CURVO PARA CORAZA 3/4\"',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(336,1,'CONECTOR CURVO PARA CORAZA 1\"',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(337,1,'CONECTOR CURVO PARA CORAZA 2\"',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(338,4,'CAJA DEXSON 10X10X5',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(339,4,'CAJA DE PASO 20X20X15 CHAPA',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(340,4,'CAJA PASO 40x40x20 CHAPA',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(341,4,'CAJA METAL 50X50X20',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(342,4,'CAJA RAWELT 2400 3H 1/2\"',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(343,4,'CAJA RAWELT 2400 3H 3/8\"',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(344,4,'CAJA RAWELT 5800 3H 1/2\"',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(345,4,'CAJA RAWELT 5800 3H 3/8\"',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(346,4,'FACEPLATE CU 2PTO BL',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(347,5,'FACEPLATE CU 1PTO BL',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(348,5,'TAPA INSERTO NEGRO',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(349,5,'TAPA INSERTO BLANCO',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(350,5,'ORGANIZADOR HORIZONTAL 2U TAPA',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(351,5,'HERRAJE CAT 6A/TERA 24P PLANO',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(352,5,'PLUG RJ45 CATEGORIA 6',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(353,5,'PLUG RJ45 CATEGORIA 6A',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(354,5,'PLUG RJ45 CATEGORIA 5E',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(355,5,'CANALET DEX 2M 60X40 BL',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(356,5,'CANALETA DE PISO DEX 2M GRIS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(357,5,'CANALETA METALICA 10X4X2 BL',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(358,5,'CANALETA METALICA 12X5X2 BL',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(359,1,'SOLDADURA PVC CONDUIT PAVCO 1/4 GL',NULL,'GALON',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(360,1,'SOLDADURA EXOTERMICA 115G',NULL,'GALON',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(361,1,'SOLDADURA EXOTERMICA 90G',NULL,'GALON',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(362,1,'DUCTO PAVCO TDP 3\'\' X 6 MT CORRUGADO',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(363,4,'CAJA INSPECCION 30X30X30 P.TIERRA CONCRETO',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(364,4,'TORNILLO PARA CHAZO 1/2\"',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(365,4,'TORNILLO PARA CHAZO 1/4\"',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(366,4,'TORNILLO PARA CHAZO 5/16\"',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(367,4,'CHAZO SUPRA 1/2\"',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(368,4,'CHAZO SUPRA 1/4\"',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(369,4,'CHAZO SUPRA 5/16\"',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(370,4,'CHAZO RL 1/2\"',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(371,4,'CHAZO RL 1/4\"',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(372,4,'CHAZO RL 5/16\"',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(373,4,'CHAZO RL 3/8\"',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(374,8,'BREAKER ENCHUFABLE 1X20A',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(375,8,'BREAKER ENCHUFABLE 2X20A',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(376,8,'BREAKER ENCHUFABLE 3X20A',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(377,8,'BREAKER ENCHUFABLE 1X30A',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(378,8,'BREAKER ENCHUFABLE 2X30A',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(379,8,'BREAKER ENCHUFABLE 3X30A',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(380,8,'BREAKER ENCHUFABLE 1X40A',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(381,8,'BREAKER ENCHUFABLE 2X40A',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(382,8,'BREAKER ENCHUFABLE 3X40A',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(383,8,'BREAKER ENCHUFABLE 1X50A',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(384,8,'BREAKER ENCHUFABLE 2X50A',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(385,8,'BREAKER ENCHUFABLE 3X50A',NULL,'UNIDAD',0,1,'2026-05-07 14:40:07','2026-05-07 14:40:07'),(386,8,'BREAKER ENCHUFABLE 1X60A',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(387,8,'BREAKER ENCHUFABLE 2X60A',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(388,8,'BREAKER ENCHUFABLE 3X60A',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(389,8,'BREAKER ENCHUFABLE 1X80A',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(390,8,'BREAKER ENCHUFABLE 2X80A',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(391,8,'BREAKER ENCHUFABLE 3X80A',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(392,8,'BREAKER ENCHUFABLE 1X100A',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(393,8,'BREAKER ENCHUFABLE 2X100A',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(394,8,'BREAKER ENCHUFABLE 3X100A',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(395,8,'BREAKER RIEL DIN 1X20A',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(396,8,'BREAKER RIEL DIN 2X20A',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(397,8,'BREAKER RIEL DIN 3X20A',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(398,8,'BREAKER RIEL DIN 1X30A',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(399,8,'BREAKER RIEL DIN 2X30A',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(400,8,'BREAKER RIEL DIN 3X30A',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(401,8,'BREAKER RIEL DIN 1X40A',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(402,8,'BREAKER RIEL DIN 2X40A',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(403,8,'BREAKER RIEL DIN 3X40A',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(404,8,'BREAKER RIEL DIN 1X50A',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(405,8,'BREAKER RIEL DIN 2X50A',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(406,8,'BREAKER RIEL DIN 3X50A',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(407,8,'BREAKER RIEL DIN 1X60A',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(408,8,'BREAKER RIEL DIN 2X60A',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(409,8,'BREAKER RIEL DIN 3X60A',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(410,8,'BREAKER RIEL DIN 1X80A',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(411,8,'BREAKER RIEL DIN 2X80A',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(412,8,'BREAKER RIEL DIN 3X80A',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(413,8,'BREAKER RIEL DIN 1X100A',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(414,8,'BREAKER RIEL DIN 2X100A',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(415,8,'BREAKER RIEL DIN 3X100A',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(416,8,'BREAKER CAJA MOLDEADA 2X20A',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(417,8,'BREAKER CAJA MOLDEADA 3X20A',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(418,8,'BREAKER CAJA MOLDEADA 2X40A',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(419,8,'BREAKER CAJA MOLDEADA 3X40A',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(420,8,'BREAKER CAJA MOLDEADA 2X63A',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(421,8,'BREAKER CAJA MOLDEADA 3X63A',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(422,8,'BREAKER CAJA MOLDEADA 2X80A',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(423,8,'BREAKER CAJA MOLDEADA 3X80A',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(424,8,'BREAKER CAJA MOLDEADA 2X100A',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(425,8,'BREAKER CAJA MOLDEADA 3X100A',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(426,8,'BREAKER CAJA MOLDEADA 2X125A',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(427,8,'BREAKER CAJA MOLDEADA 3X125A',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(428,8,'BREAKER CAJA MOLDEADA 2X150A',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(429,8,'BREAKER CAJA MOLDEADA 3X150A',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(430,8,'BREAKER CAJA MOLDEADA 2X160A',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(431,8,'BREAKER CAJA MOLDEADA 3X160A',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(432,1,'CONDULETA ROSCADA LR 3/4\"',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(433,1,'CONDULETA ROSCADA LR 1\"',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(434,1,'CONDULETA ROSCADA LR 2\"',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(435,1,'CONDULETA ROSCADA LB 3/4\"',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(436,1,'CONDULETA ROSCADA LB 1\"',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(437,1,'CONDULETA ROSCADA LB 2\"',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(438,3,'CABLE RTD - TERMOCUPLA',NULL,'METRO',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(439,3,'TERMOCUPLA',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(440,3,'POLIWATER - LUBRICANTE PARA CABLE',NULL,'GALON',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(441,4,'VELCRO NEGRO',NULL,'METROS',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(442,4,'FOTOCELDA PARA ALUMBRADO ILUMINACION',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(443,4,'BASE PARA FOTOCELDA PARA ALUMBRADO ILUMINACION',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(444,4,'TEMPORIZADOR',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(445,8,'LOGO!POWER - POWER SUPPLY',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(446,8,'AUXILIARY SWITCH FRONT',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(447,8,'LOGO! ANALOG INPUNT MODILE',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(448,8,'LOGIC MODULE',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(449,8,'CONTACTOR',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(450,8,'LOGO! DIGITAL INPUT/OUTPUT',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(451,8,'DPS DISPOSITIVO DE PROTECCIÓN CONTRA TENSIONES TIPO 1+2',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(452,4,'TORNILLO PUNTA DE BROCA 1/2\"',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(453,4,'TORNILLO PUNTA DE BROCA 3/4\"',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(454,4,'TORNILLO PUNTA DE BROCA 1\"',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(455,4,'TUERCA CANASTILLA',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(456,4,'BARRAS DE TIERRA RACK DE COMUNICACIONES',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(457,4,'TERMINALES DE OJO #12',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(458,4,'EMPALME TUBULAR #12',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(459,4,'TERMINAL DE PIN #12',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(460,4,'TERMINAL DE OJO #8',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(461,4,'EMPALME TUBULAR #8',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(462,4,'TERMINAL DE PIN #8',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(463,4,'BORNA TERMINALES COBRE ESTAÑO 12',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(464,4,'BORNA TERMINALES COBRE ESTAÑO 10',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(465,4,'BORNA TERMINALES COBRE ESTAÑO 8',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(466,4,'BORNA TERMINALES COBRE ESTAÑO 6',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(467,4,'BORNA TERMINALES COBRE ESTAÑO 4',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(468,4,'BORNA TERMINALES COBRE ESTAÑO 2',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(469,4,'BORNA TERMINALES COBRE ESTAÑO 1/0',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(470,4,'BORNA TERMINALES COBRE ESTAÑO 2/0',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(471,4,'BORNA TERMINALESCOBRE ESTAÑO 3/0',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(472,4,'BORNA TERMINALES COBRE ESTAÑO 4/0',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(473,4,'BORNA TERMINALES COBRE ESTAÑO 250 KCMIL',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(474,4,'BORNA TERMINALES COBRE ESTAÑO 300 KCMIL',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(475,4,'BORNA TERMINALES COBRE ESTAÑO 350 KCMIL',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(476,4,'BORNA TERMINALES COBRE ESTAÑO 500 KCMIL',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(477,4,'CONECTOR TUBULAR COBRE ESTAÑO 12',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(478,4,'CONECTOR TUBULAR COBRE ESTAÑO 10',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(479,4,'CONECTOR TUBULAR COBRE ESTAÑO 8',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(480,4,'CONECTOR TUBULAR COBRE ESTAÑO 6',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(481,4,'CONECTOR TUBULAR COBRE ESTAÑO 4',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(482,4,'CONECTOR TUBULAR COBRE ESTAÑO 2',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(483,4,'CONECTOR TUBULAR COBRE ESTAÑO 1/0',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(484,4,'CONECTOR TUBULAR COBRE ESTAÑO 2/0',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(485,4,'CONECTOR TUBULARCOBRE ESTAÑO 3/0',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(486,4,'CONECTOR TUBULAR COBRE ESTAÑO 4/0',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(487,4,'CONECTOR TUBULAR COBRE ESTAÑO 250 KCMIL',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(488,4,'CONECTOR TUBULAR COBRE ESTAÑO 300 KCMIL',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(489,4,'CONECTOR TUBULAR COBRE ESTAÑO 350 KCMIL',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(490,4,'CONECTOR TUBULAR COBRE ESTAÑO 500 KCMIL',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(491,4,'INTERRUPTOR SENCILLO BL',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(492,8,'RACK EN ACERO PISO DOBLE AJUSTABLE 19\"',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(493,8,'BANDEJA SENCILLA VENTILADA 19\" X 15\" 2 RMS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(494,6,'MULTITOMA 19\" X 10 TOMAS 115V',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(495,9,'REFLECTOR LED POTENCIA DE 30 VATIOS  – SYLVANIA',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(496,9,'BOMBILLOS LED T8 120 VATIOS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(497,9,'BOMBILLOS LED 100V SYLVANIA',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(498,9,'LAMPARA UFO 200W',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(499,9,'ROSETA DE CERAMICA E40',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(500,9,'LAMPARA TRY LOOP 50V SYLVANIA',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(501,4,'VIDEO BALUN 8MP',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(502,4,'VIDEO BALUN 5MP',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(503,8,'TABLERO BYPASS',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(504,8,'TABLERO DE AUTOMATIZACION',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(505,4,'BATERIA 12V - 9,0AH',NULL,'UNIDAD',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(506,10,'SONDA 60 MTS',NULL,'',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(507,10,'TALADRO PERCUTOR',NULL,'',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(508,10,'DOBLATUBO 1\"',NULL,'',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(509,10,'DOBLATUBO 3/4\"',NULL,'',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(510,10,'DOBLATUBO 1/2\"',NULL,'',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(511,10,'PULIDORA PEQUEÑA',NULL,'',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(512,10,'PULIDORA GRANDE',NULL,'',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(513,10,'SOPLADORA',NULL,'',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(514,10,'PISTOLA DE CALOR',NULL,'',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(515,10,'TALADRO INHALAMBRICO',NULL,'',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(516,10,'PONCHADORA HIDRAULICA',NULL,'',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(517,10,'MACETA',NULL,'',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(518,10,'PUNTERO',NULL,'',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(519,10,'BOMBA DE AGUA',NULL,'',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(520,10,'PALUSTRE',NULL,'',0,1,'2026-05-07 14:40:08','2026-05-07 14:40:08'),(521,10,'PALA',NULL,'',0,1,'2026-05-07 14:40:09','2026-05-07 14:40:09'),(522,10,'PICO',NULL,'',0,1,'2026-05-07 14:40:09','2026-05-07 14:40:09'),(523,10,'CAJON METALICO PEQUEÑO',NULL,'',0,1,'2026-05-07 14:40:09','2026-05-07 14:40:09'),(524,10,'CAJON METALICO GRANDE',NULL,'',0,1,'2026-05-07 14:40:09','2026-05-07 14:40:09'),(525,10,'CERTIFICADOR',NULL,'',0,1,'2026-05-07 14:40:09','2026-05-07 14:40:09'),(526,10,'MARQUILLADORA',NULL,'',0,1,'2026-05-07 14:40:09','2026-05-07 14:40:09'),(527,10,'PROBADOR DE BATERIAS',NULL,'',0,1,'2026-05-07 14:40:09','2026-05-07 14:40:09'),(528,10,'ARNES',NULL,'',0,1,'2026-05-07 14:40:09','2026-05-07 14:40:09'),(529,10,'ESCALERA 8 PASOS',NULL,'',0,1,'2026-05-07 14:40:09','2026-05-07 14:40:09'),(530,10,'ESCALERA 12 PASOS',NULL,'',0,1,'2026-05-07 14:40:09','2026-05-07 14:40:09');
/*!40000 ALTER TABLE `materials` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (13,'0001_01_01_000000_create_users_table',1),(14,'0001_01_01_000001_create_cache_table',1),(15,'0001_01_01_000002_create_jobs_table',1),(16,'2026_03_07_155100_create_clients_table',1),(17,'2026_03_07_155118_create_works_table',1),(18,'2026_03_07_155134_create_materials_table',1),(19,'2026_03_07_155151_create_material_categories_table',1),(20,'2026_03_07_155212_create_requisitions_table',1),(21,'2026_03_07_155240_create_time_logs_table',1),(22,'2026_03_07_155358_create_work_user_table',1),(23,'2026_03_07_155414_create_material_requisition_table',1),(24,'2026_04_06_194805_create_requisition_comments_table',1),(25,'2026_05_29_000000_create_locations_and_refactor_works',2),(26,'2026_06_03_115102_add_phone_to_users_table',3),(27,'2026_06_03_134656_create_countries_table',4),(28,'2026_06_03_134657_create_country_user_table',4),(29,'2026_04_24_000000_create_roles_table',4),(30,'2026_05_08_155240_add_manual_registration_fields_to_time_logs_table',4),(31,'2026_05_18_110000_create_holidays_table',4),(32,'2026_05_19_120000_create_overtimes_table',4),(33,'2026_05_25_190000_create_backup_settings_table',4),(34,'2026_06_04_172924_create_personal_access_tokens_table',5),(35,'2026_11_02_203001_add_induction_status_to_users_table',5),(36,'2026_11_02_203002_create_document_categories_table',5),(37,'2026_11_02_203003_create_document_subcategories_table',5),(38,'2026_11_02_203004_create_corporate_contents_table',5),(39,'2026_11_02_203005_create_user_content_progress_table',5),(40,'2026_11_02_203006_create_user_induction_evidences_table',5),(41,'2026_11_02_220001_add_url_and_flags_to_corporate_contents_table',6),(42,'2026_11_03_163001_create_corporate_content_role_table',6),(43,'2026_11_03_221500_add_target_user_id_to_corporate_contents_table',7);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `overtimes`
--

DROP TABLE IF EXISTS `overtimes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `overtimes` (
  `overtime_id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint NOT NULL,
  `work_id` bigint NOT NULL,
  `time_log_id` int unsigned NOT NULL,
  `date` date NOT NULL,
  `hours` decimal(8,2) NOT NULL,
  `type` enum('daily_excess','sunday','holiday') COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `approved_by` bigint DEFAULT NULL,
  `supervisor_id` bigint NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`overtime_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `overtimes`
--

LOCK TABLES `overtimes` WRITE;
/*!40000 ALTER TABLE `overtimes` DISABLE KEYS */;
INSERT INTO `overtimes` VALUES (9,6,4,21,'2026-06-01',14.72,'daily_excess','pending',NULL,6,'2026-06-02 14:26:00','2026-06-02 14:26:00'),(10,4,4,22,'2026-06-02',15.78,'daily_excess','pending',NULL,6,'2026-06-03 13:21:38','2026-06-03 13:21:38');
/*!40000 ALTER TABLE `overtimes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_expires_at_index` (`expires_at`)
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
-- Table structure for table `requisition_comments`
--

DROP TABLE IF EXISTS `requisition_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `requisition_comments` (
  `comment_id` int unsigned NOT NULL AUTO_INCREMENT,
  `requisition_id` bigint NOT NULL,
  `user_id` bigint NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`comment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `requisition_comments`
--

LOCK TABLES `requisition_comments` WRITE;
/*!40000 ALTER TABLE `requisition_comments` DISABLE KEYS */;
/*!40000 ALTER TABLE `requisition_comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `requisitions`
--

DROP TABLE IF EXISTS `requisitions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `requisitions` (
  `requisition_id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint NOT NULL,
  `work_id` bigint NOT NULL,
  `status` enum('pending','approved','purchased','delivered','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `reject_reason` text COLLATE utf8mb4_unicode_ci,
  `approved_by` bigint DEFAULT NULL,
  `date_approved` datetime DEFAULT NULL,
  `rejected_by` bigint DEFAULT NULL,
  `date_rejected` datetime DEFAULT NULL,
  `purchased_by` bigint DEFAULT NULL,
  `date_purchased` datetime DEFAULT NULL,
  `delivered_by` bigint DEFAULT NULL,
  `date_delivered` datetime DEFAULT NULL,
  `priority` enum('low','medium','high') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'medium',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`requisition_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `requisitions`
--

LOCK TABLES `requisitions` WRITE;
/*!40000 ALTER TABLE `requisitions` DISABLE KEYS */;
INSERT INTO `requisitions` VALUES (1,2,4,'delivered',NULL,2,'2026-06-09 17:01:45',NULL,NULL,2,'2026-06-09 17:02:33',2,'2026-06-09 17:02:53','medium','2026-06-09 22:01:06','2026-06-09 22:02:53');
/*!40000 ALTER TABLE `requisitions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_user`
--

DROP TABLE IF EXISTS `role_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_user` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `role_user_user_id_foreign` (`user_id`),
  KEY `role_user_role_id_foreign` (`role_id`),
  CONSTRAINT `role_user_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_user`
--

LOCK TABLES `role_user` WRITE;
/*!40000 ALTER TABLE `role_user` DISABLE KEYS */;
INSERT INTO `role_user` VALUES (1,1,3,NULL,NULL),(2,2,1,NULL,NULL),(5,4,2,NULL,NULL),(6,5,1,NULL,NULL),(7,6,4,NULL,NULL),(10,9,5,NULL,NULL),(11,10,5,NULL,NULL),(12,11,5,NULL,NULL),(13,12,5,NULL,NULL),(14,13,5,NULL,NULL),(15,14,5,NULL,NULL),(16,15,6,NULL,NULL),(17,16,6,NULL,NULL),(18,17,6,NULL,NULL),(19,18,6,NULL,NULL),(20,5,4,NULL,NULL),(21,19,4,NULL,NULL),(25,3,8,NULL,NULL),(26,3,1,NULL,NULL),(27,4,1,NULL,NULL),(28,4,8,NULL,NULL),(29,7,5,NULL,NULL),(30,20,6,NULL,NULL),(31,8,2,NULL,NULL),(32,8,8,NULL,NULL);
/*!40000 ALTER TABLE `role_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Administrador','admin','2026-05-05 20:11:36','2026-05-05 20:11:36'),(2,'Compras','purchasing','2026-05-05 20:11:36','2026-05-05 20:11:36'),(3,'Desarrollador','developer','2026-05-05 20:11:36','2026-05-05 20:11:36'),(4,'Supervisor','supervisor','2026-05-05 20:11:36','2026-05-05 20:11:36'),(5,'Técnico','technician','2026-05-05 20:11:36','2026-05-05 20:11:36'),(6,'Auxiliar','assistant','2026-05-05 20:11:36','2026-05-05 20:11:36'),(7,'Externo','external','2026-05-05 20:11:36','2026-05-05 20:11:36'),(8,'Administrativo','staff','2026-05-21 21:02:54','2026-05-21 21:02:54');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('vmyLmi68EQ9BKLS2gj99AJkKFiUCitMJiqc6gHaC',1,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','YTo3OntzOjY6Il90b2tlbiI7czo0MDoiM0p4Z2k2b0VuN04xNjNDbm1aR2k2ejRWUEl3ZTQ1N3BhWnkzcWRMbCI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjY3OiJodHRwOi8vYm9vbWVyLWVsZWNvbW0ubG9jYWxob3N0L2Rhc2hib2FyZC9jb3Jwb3JhdGUtY29udGVudHMvMS92aWV3IjtzOjU6InJvdXRlIjtzOjMzOiJkYXNoYm9hcmQuY29ycG9yYXRlLWNvbnRlbnRzLnZpZXciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO3M6MTc6InBhc3N3b3JkX2hhc2hfd2ViIjtzOjY0OiI2OGIyZTg0Mzc2YzQzZDc3YjE0MTQ2NjkyNmQ5OWQ1NjJjZGIxNmIwYzk3MzYzOTYwOTJmMGE3YTE4YmFiYTMxIjtzOjQ6ImF1dGgiO2E6MTp7czoyMToicGFzc3dvcmRfY29uZmlybWVkX2F0IjtpOjE3OTM5Njk0MTc7fX0=',1793996566);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `time_logs`
--

DROP TABLE IF EXISTS `time_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `time_logs` (
  `time_log_id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint NOT NULL,
  `work_id` bigint NOT NULL,
  `check_in_time` datetime NOT NULL,
  `check_in_photo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `check_in_lat` decimal(10,8) NOT NULL,
  `check_in_lng` decimal(11,8) NOT NULL,
  `check_in_observation` text COLLATE utf8mb4_unicode_ci,
  `check_in_registered_by` bigint DEFAULT NULL,
  `check_out_time` datetime DEFAULT NULL,
  `check_out_photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `check_out_lat` decimal(10,8) DEFAULT NULL,
  `check_out_lng` decimal(11,8) DEFAULT NULL,
  `check_out_observation` text COLLATE utf8mb4_unicode_ci,
  `check_out_registered_by` bigint DEFAULT NULL,
  `hours_worked` decimal(8,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`time_log_id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `time_logs`
--

LOCK TABLES `time_logs` WRITE;
/*!40000 ALTER TABLE `time_logs` DISABLE KEYS */;
INSERT INTO `time_logs` VALUES (18,4,4,'2026-06-01 07:57:00','img/check_in_out/ci_in_18_0004_4_20260601075759.jpg',4.69452540,-74.05928743,NULL,NULL,'2026-06-01 16:57:00','img/check_in_out/ci_out_18_0004_4_20260602073414.jpg',4.69447279,-74.05923486,NULL,NULL,9.00,'2026-06-01 12:57:59','2026-06-03 20:21:31'),(19,5,4,'2026-06-01 08:54:47','img/check_in_out/ci_in_19_0004_5_20260601085447.jpg',-34.52094570,-58.47642270,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-06-01 13:54:47','2026-06-01 13:54:47'),(20,3,4,'2026-06-01 09:17:03','img/check_in_out/ci_in_20_0004_3_20260601091703.jpg',4.72128149,-74.05969669,NULL,NULL,'2026-06-01 10:17:36','img/check_in_out/ci_out_20_0004_3_20260601101736.jpg',4.68027260,-74.10708570,NULL,NULL,1.01,'2026-06-01 14:17:03','2026-06-01 15:17:36'),(21,6,4,'2026-06-01 09:42:57','img/check_in_out/ci_in_21_0004_6_20260601094257.jpg',4.69445905,-74.05919748,NULL,NULL,'2026-06-02 09:26:00','img/check_in_out/ci_out_21_0004_6_20260602092600.jpg',4.69465731,-74.05935045,NULL,NULL,23.72,'2026-06-01 14:42:57','2026-06-02 14:26:00'),(22,4,4,'2026-06-02 07:34:44','img/check_in_out/ci_in_22_0004_4_20260602073444.jpg',4.69446570,-74.05924945,NULL,NULL,'2026-06-03 08:21:38','img/check_in_out/ci_out_22_0004_4_20260603082138.jpg',4.69449246,-74.05930844,NULL,NULL,24.78,'2026-06-02 12:34:44','2026-06-03 13:21:38'),(23,6,4,'2026-06-02 09:27:48','img/check_in_out/ci_in_23_0004_6_20260602092748.jpg',4.69461239,-74.05932504,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-06-02 14:27:48','2026-06-02 14:27:48'),(24,7,146,'2026-06-02 10:58:24','img/check_in_out/ci_in_24_0146_7_20260602105824.jpg',4.67701910,-74.04904350,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-06-02 15:58:24','2026-06-02 15:58:25'),(25,8,4,'2026-06-02 11:34:11','img/check_in_out/ci_in_25_0004_8_20260602113411.jpg',4.69451627,-74.05925432,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-06-02 16:34:11','2026-06-02 16:34:11'),(26,14,148,'2026-06-03 07:14:31','img/check_in_out/ci_in_26_0148_14_20260603071431.jpg',8.74986848,-75.87617126,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-06-03 12:14:31','2026-06-03 12:14:31'),(27,4,4,'2026-06-03 08:22:05','img/check_in_out/ci_in_27_0004_4_20260603082205.jpg',4.69449705,-74.05931502,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-06-03 13:22:05','2026-06-03 13:22:06');
/*!40000 ALTER TABLE `time_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_content_progress`
--

DROP TABLE IF EXISTS `user_content_progress`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_content_progress` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `content_id` bigint unsigned NOT NULL,
  `view_time_seconds` int NOT NULL DEFAULT '0',
  `status` enum('pending','completed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_content_progress_user_id_content_id_unique` (`user_id`,`content_id`),
  KEY `user_content_progress_content_id_foreign` (`content_id`),
  CONSTRAINT `user_content_progress_content_id_foreign` FOREIGN KEY (`content_id`) REFERENCES `corporate_contents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_content_progress_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_content_progress`
--

LOCK TABLES `user_content_progress` WRITE;
/*!40000 ALTER TABLE `user_content_progress` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_content_progress` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_induction_evidences`
--

DROP TABLE IF EXISTS `user_induction_evidences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_induction_evidences` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `evidence_photo_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `accepted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_induction_evidences_user_id_foreign` (`user_id`),
  CONSTRAINT `user_induction_evidences_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_induction_evidences`
--

LOCK TABLES `user_induction_evidences` WRITE;
/*!40000 ALTER TABLE `user_induction_evidences` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_induction_evidences` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country` enum('Colombia','Argentina','Global') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active` tinyint NOT NULL DEFAULT '0',
  `induction_status` enum('pending','completed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `last_login_at` datetime DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Sysadmin','websites@sysbytes.com.co','+57 3225107449',NULL,'$2y$12$VMiGSGyar0qAaTmZNoHuGO6GBv5x1PDVpl42pgSniJyd.67c5RWsa','Global',1,'pending',NULL,NULL,'2026-05-05 20:11:36','2026-06-03 20:06:59'),(2,'Administrador Boomer','admin@boomer-elecomm.com',NULL,NULL,'$2y$12$BN5y.kqSXHJ3gBTbqMMd7OlNyUXovYYSamPyiSLijN/T8w2JPqHwy','Global',1,'pending',NULL,NULL,'2026-05-05 20:13:54','2026-05-05 20:13:54'),(3,'Sandra Cardona','scardona@boomer-elecomm.com','3183624921',NULL,'$2y$12$iKAemIHBNDvHU4G3xm.pr.UcFtf0XjLmyPrMV7dDWw11uYjw3mP1O','Global',1,'pending',NULL,NULL,'2026-05-05 21:17:27','2026-06-03 17:40:54'),(4,'Mónica Agostini','magostini@boomer-elecomm.com',NULL,NULL,'$2y$12$EuUwztpmG9AbNRDDN90ajOpTBFFqqzYbhD3a7qAPZvUv8H2XG6Ky.','Colombia',1,'pending',NULL,'Z0lPGzRQSzupUUbBQhgZAb1KfgAUaBGV7mPxAuBbWLTFBXl9CcqptHjULFBc','2026-05-05 21:20:31','2026-05-05 21:20:31'),(5,'Vladimir Agostini','vagostini@boomer-elecomm.com','+57 3225107449',NULL,'$2y$12$mYKsWVi.pjzCU5DSaJyPi.OtkwbdXgtqz.y87J12u/AXI4UGEMgBe','Global',1,'pending',NULL,'fApwMhhmM22m1DRRRYWNTvdWYwGTII3wqrJiVDf4PliX1V1HyUlJ7biZ97S5','2026-05-05 21:21:34','2026-06-05 17:20:38'),(6,'JUAN CARLOS RAMIREZ','jramirez@boomer-elecomm.com',NULL,NULL,'$2y$12$sCfMKzog7e2iejjcq8Lle.aj6A1wbAjE2qxqWgrAfDFKjI3JJbFZS','Colombia',1,'pending',NULL,NULL,'2026-05-05 21:24:45','2026-05-05 21:24:45'),(7,'Julio Cesar Parra','jparra@boomer-elecomm.com',NULL,NULL,'$2y$12$8sYS59LkA1grSQ4SxPdh3u/qnucsYuWZH41SC/2Zly78jnx6ZSbWi','Colombia',1,'pending',NULL,'vaNbPN0LvHaBnyoo9RcuhuDaMmdvrdaBT5JAOim6MYGqxtiqpz6wpPpG2t2C','2026-05-05 21:25:29','2026-05-29 16:40:03'),(8,'Diana Milena López Rozo','dlopez@boomer-elecomm.com',NULL,NULL,'$2y$12$FBugIJVyqNTApdx5WD4nFel8eHXqvCl4oL7OO/s0vVEnDONgyJN.a','Colombia',1,'pending',NULL,NULL,'2026-05-05 21:26:42','2026-05-29 16:53:05'),(9,'JULIAN CASTIBLANCO','jcastiblanco@boomer-elecomm.com',NULL,NULL,'$2y$12$KUdQi3E7phGPmkKySlyg0.TVuFAGLaYqVhw0sqSGDiO/3M2sabuUi','Colombia',1,'pending',NULL,NULL,'2026-05-05 21:27:33','2026-05-05 21:27:33'),(10,'GIOVANNI SONZA','gsonza@boomer-elecomm.com',NULL,NULL,'$2y$12$qHXgtVmhOiF/CLS7LO/HkOZbUvhtD3PlL16G0REUuWu5CcXmqkO2e','Colombia',1,'pending',NULL,'WXXBiOrDe5uHUsLaUxxxdTqC6yMTyXFkFnZiuFHFp8KoTQsL6Hi1eMBmSi7P','2026-05-05 21:29:13','2026-05-05 21:29:13'),(11,'JOSE WILDER GUTIERREZ','jgutierrez@boomer-elecomm.com',NULL,NULL,'$2y$12$KgARo.omV1wcdmvnEjdikOZilsOrxa.Gr.RqjwH1bSD7cpKc/suBO','Colombia',1,'pending',NULL,'3QhcWWAnpozbsI5VoaJnCkWrbY8qaFhDvO5OW6doAiXFj9J5hcyZ17czpkoa','2026-05-05 21:31:02','2026-05-05 21:31:02'),(12,'WILSON JIMENEZ','wjimenez@boomer-elecomm.com',NULL,NULL,'$2y$12$k1AhKjJkNZbgmnpMnguQ9eMBQ4BoLioZRwGiByLB6XPa0.nKh1sGq','Colombia',1,'pending',NULL,NULL,'2026-05-05 21:31:49','2026-05-05 21:31:49'),(13,'DIEGO QUINTERO','dquintero@boomer-elecomm.com',NULL,NULL,'$2y$12$l.xaNdWAFl221bSF6pmth.yI3LcNH3jW44RBuIQ2Q9gp1v3H6W0xS','Colombia',1,'pending',NULL,NULL,'2026-05-05 21:32:33','2026-05-05 21:32:33'),(14,'EDUWIN CONTRERAS','econtreras@boomer-elecomm.com',NULL,NULL,'$2y$12$/rV7YgJVB6V4QkuONDpqGOLgV8tV8d3ZwqyzJQYRLIMz7/J/AHWri','Colombia',1,'pending',NULL,'NVmtb5m0VMNuKt6MUKbx3aqhXuGo2T3W52Hucu6RFkQcNCx8VPbwUu7RZVRz','2026-05-05 21:33:18','2026-05-05 21:33:18'),(15,'WILLIAM MALAVER','wmalaver@boomer-elecomm.com',NULL,NULL,'$2y$12$jsbx/wVgnDMGwvvhG0MZ/.YqoiW3lhJxT8rzfFoOdEq6y3Vc3U1S2','Colombia',1,'pending',NULL,NULL,'2026-05-05 21:34:01','2026-05-05 21:34:01'),(16,'LUIS CARLOS JIMENEZ','ljimenez@boomer-elecomm.com',NULL,NULL,'$2y$12$EDWnkmCYHaf7D0kf3paureJvyo8Zjy2D00A8skERwE2972on.dWD.','Colombia',1,'pending',NULL,'Avs2WyNmdtPXSAt6MtiOb1z8BGogFxDBgoqcE79IyHnk0JIDUsltvVhkv1jJ','2026-05-05 21:35:14','2026-05-05 21:35:14'),(17,'HELED LUGO','hlugo@boomer-elecomm.com',NULL,NULL,'$2y$12$VlpRgM.Aj1jnUYQulSHVBOQMwq5r16Uy.f6faL2n8C003GaZe20Qu','Colombia',1,'pending',NULL,NULL,'2026-05-05 21:36:01','2026-05-05 21:36:01'),(18,'GILBERTO FORERO','gforero@boomer-elecomm.com',NULL,NULL,'$2y$12$OVf8FtF1WX/k1P3UjQ.mkuQYZOg5LhVcLrNSJXK4e1BO75DC1FbIW','Colombia',1,'pending',NULL,NULL,'2026-05-05 21:36:54','2026-05-05 21:36:54'),(19,'Sin Supervisor Asignado','sinsupervisor@boomer-elecomm.com',NULL,NULL,'$2y$12$aHnVEefiEg5LZtAWOnSod.eJMUQ3jgDQiKpgqOMP1ZYyAQJYPYGLm','Colombia',1,'pending',NULL,NULL,'2026-05-11 20:08:43','2026-05-11 20:08:43'),(20,'Ramiro Antonio Borja','rborja@boomer-elecomm.com',NULL,NULL,'$2y$12$98zY6ENnzXjZ9YabJoQrO.A/.Ewz9obxBYo0wUu86OVSfWSLc3cjO','Colombia',1,'pending',NULL,NULL,'2026-05-29 16:50:18','2026-05-29 16:50:18');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `work_user`
--

DROP TABLE IF EXISTS `work_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `work_user` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `work_id` bigint NOT NULL,
  `user_id` bigint NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `work_user`
--

LOCK TABLES `work_user` WRITE;
/*!40000 ALTER TABLE `work_user` DISABLE KEYS */;
INSERT INTO `work_user` VALUES (8,149,8,NULL,NULL),(9,148,10,NULL,NULL),(10,148,14,NULL,NULL),(11,155,11,NULL,NULL),(12,155,7,NULL,NULL),(13,150,12,NULL,NULL),(14,150,20,NULL,NULL),(17,146,16,NULL,NULL),(20,166,18,NULL,NULL),(21,146,7,NULL,NULL),(22,152,7,NULL,NULL),(23,149,13,NULL,NULL),(24,149,17,NULL,NULL),(25,153,9,NULL,NULL),(26,153,15,NULL,NULL),(27,155,9,NULL,NULL),(28,155,15,NULL,NULL),(29,167,11,NULL,NULL),(30,167,18,NULL,NULL),(31,2,9,NULL,NULL);
/*!40000 ALTER TABLE `work_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `works`
--

DROP TABLE IF EXISTS `works`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `works` (
  `work_id` int unsigned NOT NULL AUTO_INCREMENT,
  `location_id` int unsigned DEFAULT NULL,
  `supervisor_id` bigint NOT NULL,
  `work_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `work_description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active` tinyint NOT NULL DEFAULT '1',
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `work_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`work_id`)
) ENGINE=InnoDB AUTO_INCREMENT=168 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `works`
--

LOCK TABLES `works` WRITE;
/*!40000 ALTER TABLE `works` DISABLE KEYS */;
INSERT INTO `works` VALUES (1,1,6,'REMODELACIÓN TIENDA','REMODELACIÓN TIENDA',1,NULL,NULL,'pending','2026-05-07 02:44:01','2026-05-14 20:54:31'),(2,2,6,'INSTALACIÓN ELÉCTRICA','INSTALACIÓN ELÉCTRICA',1,NULL,NULL,'pending','2026-05-07 02:50:52','2026-05-08 14:16:13'),(3,3,6,'INSTALACION DE AUTOPAGOS TIENDA NOGAL','INSTALACION DE AUTOPAGOS TIENDA NOGAL BOGOTÁ',1,NULL,NULL,'pending','2026-05-08 14:36:24','2026-05-13 21:09:27'),(4,4,6,'OFICINA','OFICINAS COLOMBIA',1,NULL,NULL,'in_progress','2026-05-08 14:57:09','2026-05-29 19:10:54'),(5,5,19,'ARMENIA','FMTD CENTENARIO - ARMENIA',0,NULL,NULL,'pending','2026-05-11 20:33:24','2026-05-11 20:33:24'),(6,6,19,'ARMENIA','FMTD CARRERA 19 - ARMENIA',0,NULL,NULL,'pending','2026-05-11 20:53:35','2026-05-11 20:53:35'),(7,7,19,'BARRANQUILLA','FMTD VIVA BARRANQUILLA - BARRANQUILLA',0,NULL,NULL,'pending','2026-05-11 21:03:52','2026-05-11 21:03:52'),(8,8,19,'BARRANQUILLA','FMTD PLAZA DEL PARQUE - BARRANQUILLA',0,NULL,NULL,'pending','2026-05-11 21:07:25','2026-05-11 21:07:25'),(9,9,19,'BARRANQUILLA','FMTD PLAZA CAMPESTRE - BARRANQUILLA',0,NULL,NULL,'pending','2026-05-13 21:15:41','2026-05-13 21:15:41'),(10,10,19,'BARRANQUILLA','FMTD CALLE 80 - BARRANQUILLA',0,NULL,NULL,'pending','2026-05-13 21:16:59','2026-05-13 21:16:59'),(11,11,19,'BARRANQUILLA','FMTD MALL PLAZA - BARRANQUILLA',0,NULL,NULL,'pending','2026-05-13 21:17:49','2026-05-13 21:17:49'),(12,12,19,'BARRANQUILLA','FMTD EL GOLF - BARRANQUILLA',0,NULL,NULL,'pending','2026-05-13 21:18:42','2026-05-13 21:18:42'),(13,13,19,'BARRANQUILLA','FMTD CALLE 84 - BARRANQUILLA',0,NULL,NULL,'pending','2026-05-13 21:20:08','2026-05-13 21:20:08'),(14,14,19,'BARRANQUILLA','FMTD ALTO DEL PRADO - BARRANQUILLA',0,NULL,NULL,'pending','2026-05-13 21:26:05','2026-05-13 21:26:05'),(15,15,19,'BARRANQUILLA','FMTD MIRAMAR - BARRANQUILLA',0,NULL,NULL,'pending','2026-05-13 21:27:32','2026-05-13 21:27:32'),(16,16,19,'BARRANQUILLA','FMTD BERNARDO - BARRANQUILLA',0,NULL,NULL,'pending','2026-05-13 21:28:31','2026-05-13 21:28:31'),(17,17,19,'BARRANQUILLA','FMTD VILLA CAROLINA - BARRANQUILLA',0,NULL,NULL,'pending','2026-05-13 21:29:20','2026-05-13 21:29:20'),(18,18,19,'BARRANQUILLA','FMTD VILLA COUNTRY - BARRANQUILLA',0,NULL,NULL,'pending','2026-05-13 21:30:12','2026-05-13 21:30:12'),(19,19,19,'BARRANQUILLA','FMTD MALECÓN DEL RÍO - BARRANQUILLA',0,NULL,NULL,'pending','2026-05-13 21:31:26','2026-05-13 21:31:26'),(20,20,19,'BOGOTÁ','FMTD LA CALLEJA - BOGOTÁ',0,NULL,NULL,'pending','2026-05-13 21:32:56','2026-05-13 21:32:56'),(21,21,19,'BOGOTÁ','FMTD AUTONORTE - BOGOTÁ',0,NULL,NULL,'pending','2026-05-13 21:34:44','2026-05-13 21:34:44'),(22,22,19,'BOGOTÁ','FMTD CENTRO INTERNACIONAL - BOGOTÁ',0,NULL,NULL,'pending','2026-05-13 21:35:55','2026-05-13 21:35:55'),(23,23,19,'BOGOTÁ','FMTD AVENIDA 19 - BOGOTÁ',0,NULL,NULL,'pending','2026-05-13 21:37:05','2026-05-13 21:37:05'),(24,24,19,'BOGOTÁ','FMTD BELLA SUIZA - BOGOTÁ',0,NULL,NULL,'pending','2026-05-13 21:37:54','2026-05-13 21:37:54'),(25,25,19,'BOGOTÁ','FMTD COSMOS 100 - BOGOTÁ',0,NULL,NULL,'pending','2026-05-13 21:39:53','2026-05-13 21:39:53'),(26,26,19,'BOGOTÁ','FMTD PLAZA CENTRAL - BOGOTÁ',0,NULL,NULL,'pending','2026-05-13 21:40:39','2026-05-13 21:40:39'),(27,27,19,'BOGOTÁ','FMTD ROSALES - BOGOTÁ',0,NULL,NULL,'pending','2026-05-14 00:48:10','2026-05-14 00:49:06'),(28,28,19,'BOGOTÁ','FMTD CENTRO ANDINO - BOGOTÁ',0,NULL,NULL,'pending','2026-05-14 00:52:40','2026-05-14 01:01:48'),(29,29,19,'BOGOTÁ','FMTD NORMANDIA - BOGOTÁ',0,NULL,NULL,'pending','2026-05-14 13:11:35','2026-05-14 13:12:27'),(30,30,19,'BOGOTÁ','FMTD COLINA - BOGOTÁ',0,NULL,NULL,'pending','2026-05-14 13:13:32','2026-05-14 13:14:21'),(31,31,19,'BOGOTÁ','FMTD HAYUELOS - BOGOTÁ',0,NULL,NULL,'pending','2026-05-14 13:15:16','2026-05-14 13:16:12'),(32,32,19,'BOGOTÁ','FMTD CEDRITOS 1 - BOGOTÁ',0,NULL,NULL,'pending','2026-05-14 13:16:27','2026-05-14 13:17:07'),(33,33,19,'BOGOTÁ','FMTD CALLE 119 - BOGOTÁ',0,NULL,NULL,'pending','2026-05-14 13:17:38','2026-05-14 13:18:26'),(34,34,19,'BOGOTÁ','FMTD CEDRITOS 2 - BOGOTÁ',0,NULL,NULL,'pending','2026-05-14 13:18:38','2026-05-14 13:19:19'),(35,35,19,'BOGOTÁ','FMTD CALLE 74 - BOGOTÁ',0,NULL,NULL,'pending','2026-05-14 13:21:27','2026-05-14 13:22:14'),(36,36,19,'BOGOTÁ','FMTD CALLE 170 - BOGOTÁ',0,NULL,NULL,'pending','2026-05-14 13:56:36','2026-05-14 13:57:37'),(37,37,19,'BOGOTÁ','FMTD CC DIVERPLAZA - BOGOTÁ\r\nLocal 101 y 102',0,NULL,NULL,'pending','2026-05-14 14:00:32','2026-05-14 14:04:46'),(38,38,19,'BOGOTÁ','FMTD EL ENSUEÑO - BOGOTÁ',0,NULL,NULL,'pending','2026-05-14 14:08:25','2026-05-14 14:09:58'),(39,39,19,'BOGOTÁ','FMTD PASADENA - BOGOTÁ',0,NULL,NULL,'pending','2026-05-14 14:11:31','2026-05-14 14:12:06'),(40,40,19,'BOGOTÁ','FMTD CALLE 147 - BOGOTÁ',0,NULL,NULL,'pending','2026-05-14 14:12:26','2026-05-14 14:13:01'),(41,41,19,'BOGOTÁ','FMTD SANTA ISABEL - BOGOTÁ',0,NULL,NULL,'pending','2026-05-14 14:13:27','2026-05-14 14:13:58'),(42,42,19,'BOGOTÁ','FMTD GRAN ESTACIÓN - BOGOTÁ\r\nLocal 1-67',0,NULL,NULL,'pending','2026-05-14 14:14:17','2026-05-14 14:21:09'),(43,43,19,'BOGOTÁ','FMTD SALITRE - BOGOTÁ\r\nLocal 1',0,NULL,NULL,'pending','2026-05-14 14:21:27','2026-05-14 14:30:39'),(44,44,19,'BOGOTÁ','FMTD CENDIS - BOGOTÁ\r\nFunza-Aut Med Km7 Costa Occ Pque Ind Celta Bdga 64',0,NULL,NULL,'pending','2026-05-14 14:31:04','2026-05-14 14:33:58'),(45,45,19,'BOGOTÁ','FMTD PARQUE 93 - BOGOTÁ',0,NULL,NULL,'pending','2026-05-14 14:34:06','2026-05-14 14:34:49'),(46,46,19,'BOGOTÁ','FMTD CC SANTA FE - BOGOTÁ\r\nLocal 3-29 CC Santa Fe',0,NULL,NULL,'pending','2026-05-14 14:35:02','2026-05-14 14:36:12'),(47,47,19,'BOGOTÁ','FMTD COUNTRY - BOGOTÁ',0,NULL,NULL,'pending','2026-05-14 14:36:28','2026-05-14 14:37:11'),(48,48,19,'BOGOTÁ','FMTD CONNECTA - BOGOTÁ\r\nLote L-3',0,NULL,NULL,'pending','2026-05-14 14:37:25','2026-05-14 14:38:27'),(49,49,19,'BOGOTÁ','FMTD CC UNICENTRO - BOGOTÁ\r\nLocal 2-45 Piso 2',0,NULL,NULL,'pending','2026-05-14 14:38:42','2026-05-14 14:39:52'),(50,50,19,'BOGOTÁ','FMTD USAQUEN - BOGOTÁ',0,NULL,NULL,'pending','2026-05-14 14:40:34','2026-05-14 14:41:17'),(51,51,19,'BOGOTÁ','FMTD RINCÓN DEL CHICÓ - BOGOTÁ',0,NULL,NULL,'pending','2026-05-14 14:41:27','2026-05-14 14:41:57'),(52,52,19,'BOGOTÁ','FMTD LA CABRERA - BOGOTÁ',0,NULL,NULL,'pending','2026-05-14 14:42:10','2026-05-14 14:42:41'),(53,53,19,'BOGOTÁ','FMTD CARRERA 16 - BOGOTÁ',0,NULL,NULL,'pending','2026-05-14 14:42:52','2026-05-14 14:43:31'),(54,54,19,'BOGOTÁ','FMTD NAVARRA - BOGOTÁ',0,NULL,NULL,'pending','2026-05-14 14:43:41','2026-05-14 14:44:30'),(55,55,19,'BOGOTÁ','FMTD PEPE SIERRA - BOGOTÁ',0,NULL,NULL,'pending','2026-05-14 14:44:35','2026-05-14 14:45:17'),(56,56,19,'BOGOTÁ','FMTD MAZUREN - BOGOTÁ',0,NULL,NULL,'pending','2026-05-14 14:45:30','2026-05-14 14:46:11'),(57,57,19,'BOGOTÁ','FMTD SALITRE 2 - BOGOTÁ',0,NULL,NULL,'pending','2026-05-14 14:46:23','2026-05-14 14:51:07'),(58,58,19,'BOGOTÁ','FMTD CALLE 127 - BOGOTÁ',0,NULL,NULL,'pending','2026-05-14 14:55:39','2026-05-14 15:03:06'),(59,59,19,'BOGOTÁ','FMTD ATLANTIS - BOGOTÁ\r\nlocal 107',0,NULL,NULL,'pending','2026-05-14 15:07:19','2026-05-14 15:08:29'),(60,60,19,'BOGOTÁ','FMTD NOGAL - BOGOTÁ',0,NULL,NULL,'pending','2026-05-14 15:09:30','2026-05-14 15:10:00'),(61,61,19,'BOGOTÁ','FMTD CENTRO MAYOR - BOGOTÁ\r\nCentro Comercial Centro Mayor\r\nLocal 1-115',0,NULL,NULL,'pending','2026-05-14 15:10:10','2026-05-14 15:16:58'),(62,62,19,'BOGOTÁ','FMTD TITÁN - BOGOTÁ\r\nLOCAL 317',0,NULL,NULL,'pending','2026-05-14 15:17:03','2026-05-14 15:18:09'),(63,63,19,'BOGOTÁ','FMTD CAMPANELLA - BOGOTÁ',0,NULL,NULL,'pending','2026-05-14 15:19:39','2026-05-14 15:22:42'),(64,64,19,'BOGOTÁ','FMTD GALERIAS - BOGOTÁ',0,NULL,NULL,'pending','2026-05-14 15:22:56','2026-05-14 15:24:15'),(65,65,19,'BOGOTÁ','FMTD BRITALIA NORTE - BOGOTÁ',0,NULL,NULL,'pending','2026-05-14 15:24:28','2026-05-14 15:25:15'),(66,66,19,'BOGOTÁ','FMTD NIEVES - BOGOTÁ',0,NULL,NULL,'pending','2026-05-14 15:25:37','2026-05-14 15:26:14'),(67,67,19,'BOGOTÁ','FMTD BELMIRA - BOGOTÁ',0,NULL,NULL,'pending','2026-05-14 15:26:32','2026-05-14 15:27:05'),(68,68,19,'BOGOTÁ','FMTD CARRERA 13 - BOGOTÁ',0,NULL,NULL,'pending','2026-05-14 15:29:28','2026-05-14 15:31:16'),(69,69,19,'BOGOTÁ','FMTD CALLE 100 - BOGOTÁ\r\nCarrera 11 Calle 100 esquina',0,NULL,NULL,'pending','2026-05-14 15:32:47','2026-05-14 15:33:40'),(70,70,19,'BUCARAMANGA','FMTD ALTOS DE CABECERA - BUCARAMANGA\r\nCll 44 entre las Cras 35-36, Altos de Cabecera\r\nCra. 36 #44-2 a 44-136, Cabecera del llano, Bucaramanga, Santander',0,NULL,NULL,'pending','2026-05-14 15:34:50','2026-05-14 15:49:22'),(71,71,19,'BUCARAMANGA','FMTD CARRERA 27 - BUCARAMANGA',0,NULL,NULL,'pending','2026-05-14 15:49:37','2026-05-14 15:50:09'),(72,72,19,'BUCARAMANGA','FMTD SOTOMAYOR - BUCARAMANGA\r\nCra. 30 Cl. 52',0,NULL,NULL,'pending','2026-05-14 15:50:22','2026-05-14 15:52:39'),(73,73,19,'BUCARAMANGA','FMTD CAÑAVERAL - BUCARAMANGA\r\nCra 20 con cll 158, Sector Cañaveral, Floridablanca',0,NULL,NULL,'pending','2026-05-14 15:53:15','2026-05-14 15:55:36'),(74,74,19,'CAJICÁ','FMTD CAJICÁ - CAJICÁ\r\nKM 1, 4 VARIANTE CAJICA-ZIPAQUIRA VEREDA CALAHORRA',0,NULL,NULL,'pending','2026-05-14 15:56:02','2026-05-14 16:00:26'),(75,75,19,'CALI','FMTD PANCE - CALI',0,NULL,NULL,'pending','2026-05-14 16:01:01','2026-05-14 16:02:35'),(76,76,19,'CALI','FMTD UNICENTRO CALI - CALI',0,NULL,NULL,'pending','2026-05-14 16:04:18','2026-05-14 16:05:24'),(77,77,19,'CARTAGENA','FMTD CASTILLO GRANDE - CARTAGENA',0,NULL,NULL,'pending','2026-05-14 16:06:01','2026-05-14 16:31:35'),(78,78,19,'CALI','FMTD QUINTAS DE DON SIMÓN - CALI',0,NULL,NULL,'pending','2026-05-14 16:06:05','2026-05-14 16:07:20'),(79,79,19,'CALI','FMTD CIUDAD JARDÍN - CALI',0,NULL,NULL,'pending','2026-05-14 16:07:24','2026-05-14 16:08:42'),(80,80,19,'CALI','FMTD AVENIDA DEL RÍO - CALI',0,NULL,NULL,'pending','2026-05-14 16:08:50','2026-05-14 16:13:05'),(81,81,19,'CALI','FMTD MALL PLAZA CALI - CALI\r\nCalle 5 # 52 - 140 - Calle 5 # 52 - 253',0,NULL,NULL,'pending','2026-05-14 16:13:10','2026-05-14 16:14:27'),(82,82,19,'CALI','FMTD VALLE DE LILI - CALI',0,NULL,NULL,'pending','2026-05-14 16:14:41','2026-05-14 16:15:28'),(83,83,19,'CALI','FMTD LIMONAR - CALI\r\nBarrio El Limonar, Cali',0,NULL,NULL,'pending','2026-05-14 16:15:32','2026-05-14 16:18:04'),(84,84,19,'CALI','FMTD GRANADA - CALI',0,NULL,NULL,'pending','2026-05-14 16:18:16','2026-05-14 16:19:42'),(85,85,19,'CALI','FMTD VERSALLES - CALI\r\nBarrio Versalles',0,NULL,NULL,'pending','2026-05-14 16:19:59','2026-05-14 16:21:32'),(86,86,19,'CALI','FMTD GUADALUPE - CALI',0,NULL,NULL,'pending','2026-05-14 16:21:44','2026-05-14 16:22:30'),(87,87,19,'CALI','FMTD LA FLORA - CALI',0,NULL,NULL,'pending','2026-05-14 16:22:34','2026-05-14 16:23:43'),(88,88,19,'CALI','FMTD CHIPICHAPE - CALI\r\nLocal 402 - 402A',0,NULL,NULL,'pending','2026-05-14 16:23:53','2026-05-14 16:25:18'),(89,89,19,'CALI','FMTD 3RA NORTE CALI - CALI\r\nAv. 3 Nte. #56n25, El Rincon, Cali, Valle del Cauca',0,NULL,NULL,'pending','2026-05-14 16:25:22','2026-05-14 16:28:31'),(90,90,19,'CHÍA','FMTD CHÍA - CHÍA\r\nKm 2 via Chia Cajica',0,NULL,NULL,'pending','2026-05-14 16:31:45','2026-05-14 16:51:38'),(91,91,19,'CARTAGENA','FMTD SAN MARTÍN - CARTAGENA',0,NULL,NULL,'pending','2026-05-14 16:31:49','2026-05-14 16:33:51'),(92,92,19,'CARTAGENA','FMTD PEDRO DE HEREDIA - CARTAGENA\r\nBARRIO VILLA SANDRA',0,NULL,NULL,'pending','2026-05-14 16:34:07','2026-05-14 16:38:53'),(93,93,19,'CARTAGENA','FMTD MANGA - CARTAGENA\r\nCalle 25 # 15-21',0,NULL,NULL,'pending','2026-05-14 16:39:00','2026-05-14 16:42:27'),(94,94,19,'CARTAGENA','FMTD CIUDAD AMURALLADA - CARTAGENA',0,NULL,NULL,'pending','2026-05-14 16:42:37','2026-05-14 16:45:31'),(95,95,19,'CHÍA','FMTD VARIANTE CHÍA - CHÍA',0,NULL,NULL,'pending','2026-05-14 16:51:46','2026-05-14 16:55:31'),(96,96,19,'CÚCUTA','FMTD AV. CERO - CÚCUTA',0,NULL,NULL,'pending','2026-05-14 16:56:09','2026-05-14 17:00:41'),(97,97,19,'COTA','FMTD COTA - COTA',0,NULL,NULL,'pending','2026-05-14 16:56:22','2026-05-14 16:57:24'),(98,98,19,'CÚCUTA','FMTD CENTRO CÚCUTA - CÚCUTA',0,NULL,NULL,'pending','2026-05-14 17:00:49','2026-05-14 17:06:22'),(99,99,19,'CÚCUTA','FMTD SAN MATEO - CÚCUTA\r\nAv 1 #24-54 conjunto cerrado portobello Lote 2',0,NULL,NULL,'pending','2026-05-14 17:00:53','2026-05-14 17:02:22'),(100,100,19,'CÚCUTA','FMTD LA CEIBA 2 - CÚCUTA\r\nCalle 5 N #1a este 2-10 barrio ceiba 2',0,NULL,NULL,'pending','2026-05-14 17:02:27','2026-05-14 17:04:31'),(101,101,19,'CÚCUTA','FMTD LIBERTADORES - CÚCUTA',0,NULL,NULL,'pending','2026-05-14 17:04:37','2026-05-14 17:05:26'),(102,102,19,'IBAGUÉ','FMTD EL VERGEL - IBAGUÉ\r\nCRA 20 #23-353. Lote 7A-4B',0,NULL,NULL,'pending','2026-05-14 17:06:57','2026-05-14 17:09:45'),(103,103,19,'JAMUNDÍ','FMTD CC COUNTRY MALL - JAMUNDÍ\r\nCC Country Mall\r\nCostado oriental vía panamericana variante',0,NULL,NULL,'pending','2026-05-14 17:32:56','2026-05-14 17:58:56'),(104,104,19,'LA CALERA','FMTD LA CALERA - LA CALERA\r\nFarmatodo, al lado del peaje, Bogotá - La Calera',0,NULL,NULL,'pending','2026-05-14 17:59:43','2026-05-14 18:01:08'),(105,105,19,'MANIZALES','FMTD EL CABLE - MANIZALES',0,NULL,NULL,'pending','2026-05-14 18:02:03','2026-05-14 18:10:50'),(106,106,19,'MANIZALES','FMTD LA FLORIDA - MANIZALES\r\nCalle 71 # 1-19 Villamaria local 09',0,NULL,NULL,'pending','2026-05-14 18:11:09','2026-05-14 18:12:30'),(107,107,19,'MEDELLIN','FMTD VISITACIÓN - MEDELLIN',0,NULL,NULL,'pending','2026-05-14 18:13:44','2026-05-14 18:16:27'),(108,108,19,'MONTERÍA','FMTD LA CASTELLANA - MONTERÍA',0,NULL,NULL,'pending','2026-05-14 18:16:41','2026-05-14 19:23:37'),(109,109,19,'MEDELLIN','FMTD SAN LUCAS - MEDELLIN',0,NULL,NULL,'pending','2026-05-14 18:16:46','2026-05-14 18:17:38'),(110,110,19,'MEDELLIN','FMTD SANTAFÉ - MEDELLIN\r\nCra 43A 7 Sur 170 local 9120 Centro Comercial Santafé',0,NULL,NULL,'pending','2026-05-14 18:17:45','2026-05-14 18:19:00'),(111,111,19,'MEDELLIN','FMTD TESORO - MEDELLIN',0,NULL,NULL,'pending','2026-05-14 18:20:39','2026-05-14 18:21:26'),(112,112,19,'MEDELLIN','FMTD MILLA DE ORO - MEDELLIN',0,NULL,NULL,'pending','2026-05-14 18:21:30','2026-05-14 18:22:14'),(113,113,19,'MEDELLIN','FMTD LA SEBASTIANA - MEDELLIN\r\nENVIGADO',0,NULL,NULL,'pending','2026-05-14 18:22:25','2026-05-14 18:29:30'),(114,114,19,'MEDELLIN','FMTD CIUDAD DEL RÍO - MEDELLIN',0,NULL,NULL,'pending','2026-05-14 18:30:45','2026-05-14 18:32:13'),(115,115,19,'MEDELLIN','FMTD PROVENZA - MEDELLIN',0,NULL,NULL,'pending','2026-05-14 18:32:17','2026-05-14 18:33:58'),(116,116,19,'MEDELLIN','FMTD COLORES - MEDELLIN',0,NULL,NULL,'pending','2026-05-14 18:34:52','2026-05-14 19:06:08'),(117,117,19,'MEDELLIN','FMTD ARKADIA - MEDELLIN\r\nCra 70 # 1-141 local 9824 Centro Comercial Arkadia',0,NULL,NULL,'pending','2026-05-14 18:44:17','2026-05-14 18:45:25'),(118,118,19,'MEDELLIN','FMTD LAURELES - MEDELLIN',0,NULL,NULL,'pending','2026-05-14 19:06:56','2026-05-14 19:09:25'),(119,119,19,'MEDELLIN','FMTD LOMA DE LOS BERNAL - MEDELLIN',0,NULL,NULL,'pending','2026-05-14 19:09:33','2026-05-14 19:10:41'),(120,120,19,'MEDELLIN','FMTD MALL DEL ESTE - MEDELLIN',0,NULL,NULL,'pending','2026-05-14 19:10:51','2026-05-14 19:11:33'),(121,121,19,'MEDELLIN','FMTD ALTO DE LAS PALMAS - MEDELLIN\r\nKILÓMETRO 14 VÍA LAS PALMAS',0,NULL,NULL,'pending','2026-05-14 19:11:43','2026-05-14 19:18:35'),(122,122,19,'MEDELLIN','FMTD LOS PARRA - MEDELLIN',0,NULL,NULL,'pending','2026-05-14 19:18:41','2026-05-14 19:19:33'),(123,123,19,'MEDELLIN','FMTD LAS VEGAS - MEDELLIN',0,NULL,NULL,'pending','2026-05-14 19:19:50','2026-05-14 19:20:37'),(124,124,19,'MONTERÍA','FMTD EL RECREO - MONTERÍA',0,NULL,NULL,'pending','2026-05-14 19:23:52','2026-05-14 19:24:36'),(125,125,19,'PEREIRA','FMTD PINARES - PEREIRA',0,NULL,NULL,'pending','2026-05-14 19:24:56','2026-05-14 19:25:55'),(126,126,19,'PEREIRA','FMTD AVENIDA LAS AMÉRICAS - PEREIRA\r\nAv Las Americas esquina con calle 18 E',0,NULL,NULL,'pending','2026-05-14 19:26:03','2026-05-14 19:30:15'),(127,127,19,'PEREIRA','FMTD CERRITOS - PEREIRA\r\nArteria doble Cerritos Pereira con vía San Jorge Pereira',0,NULL,NULL,'pending','2026-05-14 19:26:12','2026-05-14 19:28:32'),(128,128,19,'PIEDECUESTA','FMTD RUITOQUE - PIEDECUESTA SANTANDER\r\nKM 2.5 VIA PIEDECUESTA, Piedecuesta - Santander',0,NULL,NULL,'pending','2026-05-14 19:30:52','2026-05-14 19:32:14'),(129,129,19,'RIONEGRO','FMTD LLANOGRANDE - RIONEGRO',0,NULL,NULL,'pending','2026-05-14 19:33:12','2026-05-14 19:34:27'),(130,130,19,'SABANETA','FMTD SABANETA - SABANETA',0,NULL,NULL,'pending','2026-05-14 19:34:43','2026-05-14 19:38:56'),(131,131,19,'RIONEGRO','FMTD BARROBLANCO - RIONEGRO',0,NULL,NULL,'pending','2026-05-14 19:34:49','2026-05-14 19:35:52'),(132,132,19,'RIONEGRO','FMTD SAN NICOLÁS - RIONEGRO\r\nCalle 43 # 54 - 139 locales 2325 y 2331',0,NULL,NULL,'pending','2026-05-14 19:36:04','2026-05-14 19:37:14'),(133,133,19,'SABANETA','FMTD MAYORCA - SABANETA\r\nCalle 51 Sur 48-57 Centro Comercial Mayorca',0,NULL,NULL,'pending','2026-05-14 19:39:04','2026-05-14 19:41:50'),(134,134,19,'SANTA MARTA','FMTD EL PRADO - SANTA MARTA\r\nBarrio el Prado',0,NULL,NULL,'pending','2026-05-14 19:42:17','2026-05-14 19:43:44'),(135,135,19,'SANTA MARTA','FMTD SAN PEDRO - SANTA MARTA',0,NULL,NULL,'pending','2026-05-14 19:44:15','2026-05-14 19:47:12'),(136,136,19,'SANTA MARTA','FMTD RODADERO - SANTA MARTA',0,NULL,NULL,'pending','2026-05-14 19:44:29','2026-05-14 19:45:06'),(137,137,19,'SOACHA','FMTD ANTARES - SOACHA',0,NULL,NULL,'pending','2026-05-14 19:47:49','2026-05-14 19:48:45'),(138,138,19,'SOLEDAD','FMTD PLAZA DEL SOL - SOLEDAD\r\nCra 37 # 31 - 33 \r\nCC Plaza Del Sol Local 2-62',0,NULL,NULL,'pending','2026-05-14 19:49:04','2026-05-14 19:50:46'),(139,139,19,'VALLEDUPAR','FMTD NOVALITO - VALLEDUPAR\r\nCARRERA 8A CON CALLE 9',0,NULL,NULL,'pending','2026-05-14 19:51:00','2026-05-14 19:55:32'),(140,140,19,'VILLAVICENCIO','FMTD EL BUQUE - VILLAVICENCIO',0,NULL,NULL,'pending','2026-05-14 19:55:50','2026-05-14 20:02:59'),(141,141,19,'VALLEDUPAR','FMTD GUATAPURÍ - VALLEDUPAR\r\nAvenida Hurtado Diagonal 10 # 6N-15, local 1-039',0,NULL,NULL,'pending','2026-05-14 19:56:03','2026-05-14 19:58:28'),(142,142,19,'VALLEDUPAR','FMTD SIERRA NEVADA - VALLEDUPAR',0,NULL,NULL,'pending','2026-05-14 19:58:40','2026-05-14 20:01:10'),(143,143,19,'VILLAVICENCIO','FMTD PRIMAVERA URBANA - VILLAVICENCIO\r\nSector El Duquel, Cl. 15 #40-01 302-304, Villavicencio, Meta',0,NULL,NULL,'pending','2026-05-14 20:03:07','2026-05-14 20:05:12'),(144,144,19,'BOGOTÁ','FMTD PUNTO 99 - BOGOTÁ',0,NULL,NULL,'pending','2026-05-14 20:06:47','2026-05-14 20:09:26'),(145,145,19,'MONTERÍA','FMTD PASATIEMPO - MONTERÍA\r\nCalle 29 N.15-84 Barrio Costa de Oro',0,NULL,NULL,'pending','2026-05-14 20:11:07','2026-05-14 20:12:14'),(146,146,6,'FERIA DE LA SALUD 2026 PARQUE DE LA 93','FERIA DE LA SALUD 2026 PARQUE DE LA 93',1,NULL,NULL,'pending','2026-05-14 20:17:26','2026-05-15 19:54:58'),(147,147,6,'ANÁLISIS DE CARGAS Y CONSUMO ELÉCTRICO','ANÁLISIS DE CARGAS Y CONSUMO ELÉCTRICO',1,NULL,NULL,'pending','2026-05-14 20:55:38','2026-05-14 20:56:27'),(148,148,6,'INSTALACIÓN ELÉCTRICA / CABLEADO ESTRUCTURADO','INSTALACIÓN ELÉCTRICA / CABLEADO ESTRUCTURADO \r\nMONTERÍA',1,NULL,NULL,'pending','2026-05-14 20:58:53','2026-05-14 20:59:44'),(149,149,6,'REMODELACIÓN TIENDA','REMODELACIÓN TIENDA\r\nLocal 2-45 Piso 2',1,NULL,NULL,'pending','2026-05-14 21:00:17','2026-05-14 21:00:49'),(150,150,6,'REMODELACION TIENDA','REMODELACION TIENDA\r\nSANTA MARTA',1,NULL,NULL,'pending','2026-05-14 21:01:13','2026-05-14 21:04:24'),(151,90,6,'REFRESCAMIENTO TIENDA','REFRESCAMIENTO TIENDA\r\nKm 2 via Chia Cajica',1,NULL,NULL,'pending','2026-05-14 21:05:35','2026-05-29 17:25:58'),(152,152,6,'REMODELACIÓN TIENDA','REMODELACIÓN TIENDA',1,NULL,NULL,'pending','2026-05-14 21:06:28','2026-05-14 21:06:55'),(153,153,6,'REFRESCAMIENTO TIENDA','REFRESCAMIENTO TIENDA',1,NULL,NULL,'pending','2026-05-14 21:07:19','2026-05-14 21:07:44'),(154,154,6,'CABLEADO ESTRUCTURADO','CABLEADO ESTRUCTURADO',1,NULL,NULL,'pending','2026-05-15 19:45:48','2026-05-15 19:46:32'),(155,155,6,'REMODELACIÓN TIENDA','REMODELACIÓN TIENDA',1,NULL,NULL,'pending','2026-05-15 19:47:33','2026-05-15 19:48:05'),(156,27,6,'ABBOTT NEVERAS PUNTERAS GONDOLAS','ABBOTT ROSALES - NEVERAS PUNTERAS GONDOLAS',1,NULL,NULL,'pending','2026-05-15 20:00:54','2026-05-29 17:44:31'),(157,58,6,'ABBOTT NEVERAS PUNTERAS GONDOLAS','ABBOTT CALLE 127 - NEVERAS PUNTERAS GONDOLAS',1,NULL,NULL,'pending','2026-05-15 20:01:50','2026-05-29 17:38:44'),(158,154,6,'ABBOTT NEVERAS PUNTERAS GONDOLAS','ABBOTT CEDRITOS 1 - NEVERAS PUNTERAS GONDOLAS',1,NULL,NULL,'pending','2026-05-15 20:02:37','2026-05-29 17:43:04'),(159,41,6,'ABBOTT NEVERAS PUNTERAS GONDOLAS','ABBOTT SANTA ISABEL - NEVERAS PUNTERAS GONDOLAS',1,NULL,NULL,'pending','2026-05-15 20:03:18','2026-05-29 17:45:19'),(160,147,6,'ABBOTT NEVERAS PUNTERAS GONDOLAS','ABBOTT CAJICÁ - NEVERAS PUNTERAS GONDOLAS',1,NULL,NULL,'pending','2026-05-15 20:04:47','2026-05-29 17:22:29'),(161,20,6,'ABBOTT NEVERAS PUNTERAS GONDOLAS','ABBOTT LA CALLEJA - NEVERAS PUNTERAS GONDOLAS',1,NULL,NULL,'pending','2026-05-15 20:05:30','2026-05-29 17:43:46'),(162,36,6,'ABBOTT NEVERAS PUNTERAS GONDOLAS','ABBOTT CALLE 170 - NEVERAS PUNTERAS GONDOLAS',1,NULL,NULL,'pending','2026-05-15 20:06:16','2026-05-29 17:39:36'),(163,152,6,'ABBOTT NEVERAS PUNTERAS GONDOLAS','ABBOTT NAVARRA - NEVERAS PUNTERAS GONDOLAS',1,NULL,NULL,'pending','2026-05-15 20:07:10','2026-05-29 17:20:36'),(164,68,6,'ABBOTT NEVERAS PUNTERAS GONDOLAS','ABBOTT CARRERA 13 - NEVERAS PUNTERAS GONDOLAS',1,NULL,NULL,'pending','2026-05-15 20:07:43','2026-05-29 17:42:34'),(165,53,6,'ABBOTT  NEVERAS PUNTERAS GONDOLAS','ABBOTT CARERRA 16 - NEVERAS PUNTERAS GONDOLAS',1,NULL,NULL,'pending','2026-05-15 20:10:17','2026-05-29 17:40:30'),(166,91,6,'AUTOPAGOS SAN MARTÍN',NULL,1,NULL,NULL,'pending','2026-05-29 19:01:11','2026-05-29 19:01:11'),(167,166,6,'LAS MERCEDES',NULL,1,NULL,NULL,'in_progress','2026-06-01 01:21:50','2026-06-01 01:21:50');
/*!40000 ALTER TABLE `works` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'boomer_elecomm'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-11-08 20:44:31
