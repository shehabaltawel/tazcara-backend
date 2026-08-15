-- MySQL dump 10.13  Distrib 8.0.46, for Linux (x86_64)
--
-- Host: localhost    Database: tazcara_db
-- ------------------------------------------------------
-- Server version	8.0.46

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
-- Table structure for table `bookings`
--

DROP TABLE IF EXISTS `bookings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bookings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `seat_id` bigint unsigned NOT NULL,
  `from_trip_city_id` bigint unsigned NOT NULL,
  `to_trip_city_id` bigint unsigned NOT NULL,
  `price` decimal(8,2) NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'confirmed',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `bookings_uuid_unique` (`uuid`),
  KEY `bookings_user_id_foreign` (`user_id`),
  KEY `bookings_seat_id_foreign` (`seat_id`),
  KEY `bookings_from_trip_city_id_foreign` (`from_trip_city_id`),
  KEY `bookings_to_trip_city_id_foreign` (`to_trip_city_id`),
  CONSTRAINT `bookings_from_trip_city_id_foreign` FOREIGN KEY (`from_trip_city_id`) REFERENCES `trip_cities` (`id`),
  CONSTRAINT `bookings_seat_id_foreign` FOREIGN KEY (`seat_id`) REFERENCES `seats` (`id`),
  CONSTRAINT `bookings_to_trip_city_id_foreign` FOREIGN KEY (`to_trip_city_id`) REFERENCES `trip_cities` (`id`),
  CONSTRAINT `bookings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bookings`
--

LOCK TABLES `bookings` WRITE;
/*!40000 ALTER TABLE `bookings` DISABLE KEYS */;
/*!40000 ALTER TABLE `bookings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `buses`
--

DROP TABLE IF EXISTS `buses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `buses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `class` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `plate_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `buses_uuid_unique` (`uuid`),
  UNIQUE KEY `buses_plate_number_unique` (`plate_number`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `buses`
--

LOCK TABLES `buses` WRITE;
/*!40000 ALTER TABLE `buses` DISABLE KEYS */;
INSERT INTO `buses` VALUES (1,'01a00636-805c-725e-a6b4-2fd8649cb335','First Class','ABC-1234','2026-08-15 16:17:10','2026-08-15 16:17:10',NULL),(2,'01a00636-806c-7042-aaa6-12c4e36cc562','First Class','ABC-5678','2026-08-15 16:17:10','2026-08-15 16:17:10',NULL),(3,'01a00636-807e-71f1-b594-40645513964a','Business','DEF-1234','2026-08-15 16:17:10','2026-08-15 16:17:10',NULL),(4,'01a00636-8091-721c-b236-bdaa6e1d1868','Business','DEF-5678','2026-08-15 16:17:10','2026-08-15 16:17:10',NULL),(5,'01a00636-80a8-70d8-87de-4c5932df8b7b','Standard','GHI-1234','2026-08-15 16:17:10','2026-08-15 16:17:10',NULL),(6,'01a00636-80bd-70f8-ab49-065583c66e10','Standard','GHI-5678','2026-08-15 16:17:10','2026-08-15 16:17:10',NULL);
/*!40000 ALTER TABLE `buses` ENABLE KEYS */;
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
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
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
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
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
-- Table structure for table `cities`
--

DROP TABLE IF EXISTS `cities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cities` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cities_uuid_unique` (`uuid`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cities`
--

LOCK TABLES `cities` WRITE;
/*!40000 ALTER TABLE `cities` DISABLE KEYS */;
INSERT INTO `cities` VALUES (1,'01a00636-7e50-71dd-bd52-e087501ea0bc','Cairo','CAI','2026-08-15 16:17:09','2026-08-15 16:17:09',NULL),(2,'01a00636-7e60-708b-99c9-0b2ed97ac8ba','Giza','GIZ','2026-08-15 16:17:09','2026-08-15 16:17:09',NULL),(3,'01a00636-7e6e-7169-bc13-08ef137c7c72','Alexandria','ALX','2026-08-15 16:17:09','2026-08-15 16:17:09',NULL),(4,'01a00636-7e80-70fe-bd10-401cfe97591f','AlFayyum','FYM','2026-08-15 16:17:09','2026-08-15 16:17:09',NULL),(5,'01a00636-7e92-73a8-b6a1-d5b702f419dc','AlMinya','MNY','2026-08-15 16:17:09','2026-08-15 16:17:09',NULL),(6,'01a00636-7eaa-70e4-b334-4f7575df74f2','Asyut','ASY','2026-08-15 16:17:09','2026-08-15 16:17:09',NULL),(7,'01a00636-7ec1-70aa-9d26-f97d0ef68a00','Sohag','SOH','2026-08-15 16:17:09','2026-08-15 16:17:09',NULL),(8,'01a00636-7ed5-7315-94fb-22a97a893c8d','Qena','QNA','2026-08-15 16:17:09','2026-08-15 16:17:09',NULL),(9,'01a00636-7ee6-70ba-b6fe-d7e0a98b923c','Luxor','LXR','2026-08-15 16:17:09','2026-08-15 16:17:09',NULL),(10,'01a00636-7f01-7171-9b6b-4a45a46a416a','Aswan','ASW','2026-08-15 16:17:09','2026-08-15 16:17:09',NULL),(11,'01a00636-7f16-7258-ad45-ae77280afc3b','BeniSuef','BNS','2026-08-15 16:17:09','2026-08-15 16:17:09',NULL),(12,'01a00636-7f2b-7321-8019-24ce1465870f','Ismailia','ISM','2026-08-15 16:17:09','2026-08-15 16:17:09',NULL),(13,'01a00636-7f3c-73f1-a761-97beb07539ac','PortSaid','PSD','2026-08-15 16:17:09','2026-08-15 16:17:09',NULL),(14,'01a00636-7f4c-721f-a0a2-1788e2e3898d','Suez','SUZ','2026-08-15 16:17:09','2026-08-15 16:17:09',NULL),(15,'01a00636-7f63-729a-ba00-fde181866961','Mansoura','MNS','2026-08-15 16:17:09','2026-08-15 16:17:09',NULL),(16,'01a00636-7f7c-7315-9c5a-c9437e56673c','Tanta','TNT','2026-08-15 16:17:10','2026-08-15 16:17:10',NULL),(17,'01a00636-7f92-73c3-aaf1-f901924abfc6','Zagazig','ZAG','2026-08-15 16:17:10','2026-08-15 16:17:10',NULL),(18,'01a00636-7fa9-72c5-b13e-b528af3b3371','Damietta','DMT','2026-08-15 16:17:10','2026-08-15 16:17:10',NULL),(19,'01a00636-7fc0-7345-886e-05bf3e7b226d','KafrElSheikh','KFS','2026-08-15 16:17:10','2026-08-15 16:17:10',NULL),(20,'01a00636-7fd5-70a0-a9a3-febf371082d3','Damanhur','DMH','2026-08-15 16:17:10','2026-08-15 16:17:10',NULL),(21,'01a00636-7fea-7109-ae04-0480389e2555','Hurghada','HRG','2026-08-15 16:17:10','2026-08-15 16:17:10',NULL),(22,'01a00636-8004-71c6-8081-85dde0aea027','ElArish','ARS','2026-08-15 16:17:10','2026-08-15 16:17:10',NULL),(23,'01a00636-8024-72ce-9afc-ee3833941cf6','Marsa Matruh','MMH','2026-08-15 16:17:10','2026-08-15 16:17:10',NULL),(24,'01a00636-8041-7041-b7aa-2e5f38c5ddea','Beni Mazar','BMZ','2026-08-15 16:17:10','2026-08-15 16:17:10',NULL);
/*!40000 ALTER TABLE `cities` ENABLE KEYS */;
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
  `connection` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`)
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
  `attempts` smallint unsigned NOT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_08_14_132911_create_cities_table',1),(5,'2026_08_14_133232_create_buses_table',1),(6,'2026_08_14_133250_create_trips_table',1),(7,'2026_08_14_133306_create_trip_cities_table',1),(8,'2026_08_14_133326_create_seats_table',1),(9,'2026_08_14_133338_create_bookings_table',1),(10,'2026_08_14_143724_create_personal_access_tokens_table',1),(11,'2026_08_15_062159_add_is_admin_to_users_table',1),(12,'2026_08_15_073813_add_uuid_to_users_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
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
-- Table structure for table `seats`
--

DROP TABLE IF EXISTS `seats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `seats` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bus_id` bigint unsigned NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `seats_bus_id_code_unique` (`bus_id`,`code`),
  UNIQUE KEY `seats_uuid_unique` (`uuid`),
  CONSTRAINT `seats_bus_id_foreign` FOREIGN KEY (`bus_id`) REFERENCES `buses` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=73 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `seats`
--

LOCK TABLES `seats` WRITE;
/*!40000 ALTER TABLE `seats` DISABLE KEYS */;
INSERT INTO `seats` VALUES (1,'01a00636-80d9-70dd-b8fb-541dba347db6',1,'A1','2026-08-15 16:17:10','2026-08-15 16:17:10',NULL),(2,'01a00636-80f2-73e2-8b38-9813dada0b01',1,'A2','2026-08-15 16:17:10','2026-08-15 16:17:10',NULL),(3,'01a00636-8106-710f-becb-ff26891584bc',1,'A3','2026-08-15 16:17:10','2026-08-15 16:17:10',NULL),(4,'01a00636-8111-71d1-a9c3-47e118cd94f4',1,'A4','2026-08-15 16:17:10','2026-08-15 16:17:10',NULL),(5,'01a00636-811f-70f5-a4ec-a6ce1199a058',1,'A5','2026-08-15 16:17:10','2026-08-15 16:17:10',NULL),(6,'01a00636-812c-7261-8526-039c618b4a81',1,'A6','2026-08-15 16:17:10','2026-08-15 16:17:10',NULL),(7,'01a00636-8138-721f-8a90-45b7f09d0faa',1,'A7','2026-08-15 16:17:10','2026-08-15 16:17:10',NULL),(8,'01a00636-814a-72fd-b002-23e88acf9e26',1,'A8','2026-08-15 16:17:10','2026-08-15 16:17:10',NULL),(9,'01a00636-815f-73e2-b65f-fa9555151ac8',1,'A9','2026-08-15 16:17:10','2026-08-15 16:17:10',NULL),(10,'01a00636-816e-7068-82b1-54f9ebd01392',1,'A10','2026-08-15 16:17:10','2026-08-15 16:17:10',NULL),(11,'01a00636-8180-72f2-af08-0fac02aeedff',1,'A11','2026-08-15 16:17:10','2026-08-15 16:17:10',NULL),(12,'01a00636-8190-7188-a8be-89a7650bf61b',1,'A12','2026-08-15 16:17:10','2026-08-15 16:17:10',NULL),(13,'01a00636-81a1-708b-a3d3-db3503c2b102',2,'A1','2026-08-15 16:17:10','2026-08-15 16:17:10',NULL),(14,'01a00636-81b5-711d-aa1a-77827e20294c',2,'A2','2026-08-15 16:17:10','2026-08-15 16:17:10',NULL),(15,'01a00636-81c5-708a-8c00-06c21b3162aa',2,'A3','2026-08-15 16:17:10','2026-08-15 16:17:10',NULL),(16,'01a00636-81dc-73f2-91a7-9d3a24b65b28',2,'A4','2026-08-15 16:17:10','2026-08-15 16:17:10',NULL),(17,'01a00636-81ec-722d-9556-bd1d912750fa',2,'A5','2026-08-15 16:17:10','2026-08-15 16:17:10',NULL),(18,'01a00636-81f9-73e1-95ba-500ac8fc95a5',2,'A6','2026-08-15 16:17:10','2026-08-15 16:17:10',NULL),(19,'01a00636-820b-726a-a5da-dd2568928432',2,'A7','2026-08-15 16:17:10','2026-08-15 16:17:10',NULL),(20,'01a00636-821f-709f-978a-25368e64a58f',2,'A8','2026-08-15 16:17:10','2026-08-15 16:17:10',NULL),(21,'01a00636-822f-70d5-9539-00c402ee074f',2,'A9','2026-08-15 16:17:10','2026-08-15 16:17:10',NULL),(22,'01a00636-823d-7245-8fc9-2ac91799cfeb',2,'A10','2026-08-15 16:17:10','2026-08-15 16:17:10',NULL),(23,'01a00636-8251-73b1-a0b8-b1d19f0131cb',2,'A11','2026-08-15 16:17:10','2026-08-15 16:17:10',NULL),(24,'01a00636-825f-735f-aa10-a49d46970a75',2,'A12','2026-08-15 16:17:10','2026-08-15 16:17:10',NULL),(25,'01a00636-8271-700f-a1c5-073f21bdbac7',3,'A1','2026-08-15 16:17:10','2026-08-15 16:17:10',NULL),(26,'01a00636-8285-71e7-bea2-63264fcc9e8c',3,'A2','2026-08-15 16:17:10','2026-08-15 16:17:10',NULL),(27,'01a00636-8295-7278-a103-269623946c58',3,'A3','2026-08-15 16:17:10','2026-08-15 16:17:10',NULL),(28,'01a00636-82a6-737e-9467-ac111ac638a5',3,'A4','2026-08-15 16:17:10','2026-08-15 16:17:10',NULL),(29,'01a00636-82b5-700d-87de-6225395e4ab5',3,'A5','2026-08-15 16:17:10','2026-08-15 16:17:10',NULL),(30,'01a00636-82c3-733c-bef6-2dcf9a6f8267',3,'A6','2026-08-15 16:17:10','2026-08-15 16:17:10',NULL),(31,'01a00636-82d7-7320-95aa-f31d454f57cf',3,'A7','2026-08-15 16:17:10','2026-08-15 16:17:10',NULL),(32,'01a00636-82e6-71fd-a6ac-425398a982a5',3,'A8','2026-08-15 16:17:10','2026-08-15 16:17:10',NULL),(33,'01a00636-82f8-73b8-9b1b-5d5a30e60446',3,'A9','2026-08-15 16:17:10','2026-08-15 16:17:10',NULL),(34,'01a00636-8304-7099-8ac1-59a1ecd400b1',3,'A10','2026-08-15 16:17:10','2026-08-15 16:17:10',NULL),(35,'01a00636-8311-72a9-8af7-12cf891d6b92',3,'A11','2026-08-15 16:17:10','2026-08-15 16:17:10',NULL),(36,'01a00636-831c-7157-9d39-775f9a60f69b',3,'A12','2026-08-15 16:17:10','2026-08-15 16:17:10',NULL),(37,'01a00636-832a-73ca-ae11-5d313a72baf7',4,'A1','2026-08-15 16:17:10','2026-08-15 16:17:10',NULL),(38,'01a00636-8336-703d-915d-c0a80e5c77d2',4,'A2','2026-08-15 16:17:10','2026-08-15 16:17:10',NULL),(39,'01a00636-8345-708a-b748-0245cb330eef',4,'A3','2026-08-15 16:17:10','2026-08-15 16:17:10',NULL),(40,'01a00636-8351-7066-b8fb-377504bf69c3',4,'A4','2026-08-15 16:17:10','2026-08-15 16:17:10',NULL),(41,'01a00636-8361-705d-80ba-d90269e55f36',4,'A5','2026-08-15 16:17:11','2026-08-15 16:17:11',NULL),(42,'01a00636-836d-72ce-b504-cfea1a6c58f8',4,'A6','2026-08-15 16:17:11','2026-08-15 16:17:11',NULL),(43,'01a00636-837b-70fe-8a29-c921a3ba79c1',4,'A7','2026-08-15 16:17:11','2026-08-15 16:17:11',NULL),(44,'01a00636-8388-7101-8d4d-7722c170468f',4,'A8','2026-08-15 16:17:11','2026-08-15 16:17:11',NULL),(45,'01a00636-8397-70c5-8fea-47ce19ff9898',4,'A9','2026-08-15 16:17:11','2026-08-15 16:17:11',NULL),(46,'01a00636-83a6-71d3-bbaf-3c70925a3f8c',4,'A10','2026-08-15 16:17:11','2026-08-15 16:17:11',NULL),(47,'01a00636-83b3-73f3-a587-fe56d9f16ddd',4,'A11','2026-08-15 16:17:11','2026-08-15 16:17:11',NULL),(48,'01a00636-83c4-70d5-aeef-853b1072e88b',4,'A12','2026-08-15 16:17:11','2026-08-15 16:17:11',NULL),(49,'01a00636-83df-7140-a782-6cf4454afd9d',5,'A1','2026-08-15 16:17:11','2026-08-15 16:17:11',NULL),(50,'01a00636-83f5-70c7-b34f-a9a355f59e37',5,'A2','2026-08-15 16:17:11','2026-08-15 16:17:11',NULL),(51,'01a00636-8413-7209-94fe-6a05c9a2d897',5,'A3','2026-08-15 16:17:11','2026-08-15 16:17:11',NULL),(52,'01a00636-8427-7120-bb8c-a138bc172e2f',5,'A4','2026-08-15 16:17:11','2026-08-15 16:17:11',NULL),(53,'01a00636-843b-71dc-b3bb-3d278863fdde',5,'A5','2026-08-15 16:17:11','2026-08-15 16:17:11',NULL),(54,'01a00636-8449-73aa-a727-c566af8c2dd0',5,'A6','2026-08-15 16:17:11','2026-08-15 16:17:11',NULL),(55,'01a00636-8458-7224-9048-b94371982dbf',5,'A7','2026-08-15 16:17:11','2026-08-15 16:17:11',NULL),(56,'01a00636-8465-7274-a038-b4fa1348ac30',5,'A8','2026-08-15 16:17:11','2026-08-15 16:17:11',NULL),(57,'01a00636-8475-71b6-8a67-1dc95b33b459',5,'A9','2026-08-15 16:17:11','2026-08-15 16:17:11',NULL),(58,'01a00636-8482-71a1-abd9-3415109dc44f',5,'A10','2026-08-15 16:17:11','2026-08-15 16:17:11',NULL),(59,'01a00636-8493-70f9-8211-2086b1c96077',5,'A11','2026-08-15 16:17:11','2026-08-15 16:17:11',NULL),(60,'01a00636-84a0-7200-8519-b6887185683d',5,'A12','2026-08-15 16:17:11','2026-08-15 16:17:11',NULL),(61,'01a00636-84b5-73c0-9a4a-c699f05dab69',6,'A1','2026-08-15 16:17:11','2026-08-15 16:17:11',NULL),(62,'01a00636-84c2-730a-8f2c-70fd62b72c35',6,'A2','2026-08-15 16:17:11','2026-08-15 16:17:11',NULL),(63,'01a00636-84d2-72ea-b681-357bb0cd90ae',6,'A3','2026-08-15 16:17:11','2026-08-15 16:17:11',NULL),(64,'01a00636-84de-705a-9008-12de1bc0b214',6,'A4','2026-08-15 16:17:11','2026-08-15 16:17:11',NULL),(65,'01a00636-84ec-73c2-be72-2d173690289a',6,'A5','2026-08-15 16:17:11','2026-08-15 16:17:11',NULL),(66,'01a00636-84f8-711d-936c-1b5eb245afd8',6,'A6','2026-08-15 16:17:11','2026-08-15 16:17:11',NULL),(67,'01a00636-8505-717c-ad28-fe2fbdb1af0e',6,'A7','2026-08-15 16:17:11','2026-08-15 16:17:11',NULL),(68,'01a00636-8516-73fc-9485-56a0f3f58fd6',6,'A8','2026-08-15 16:17:11','2026-08-15 16:17:11',NULL),(69,'01a00636-8527-71d6-984c-72b306d7127f',6,'A9','2026-08-15 16:17:11','2026-08-15 16:17:11',NULL),(70,'01a00636-8536-7070-9d70-3bf16f296f6c',6,'A10','2026-08-15 16:17:11','2026-08-15 16:17:11',NULL),(71,'01a00636-8546-7167-985e-475893f7db48',6,'A11','2026-08-15 16:17:11','2026-08-15 16:17:11',NULL),(72,'01a00636-8551-7213-98a5-8b1f4989e469',6,'A12','2026-08-15 16:17:11','2026-08-15 16:17:11',NULL);
/*!40000 ALTER TABLE `seats` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
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
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `trip_cities`
--

DROP TABLE IF EXISTS `trip_cities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `trip_cities` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `trip_id` bigint unsigned NOT NULL,
  `city_id` bigint unsigned NOT NULL,
  `sequence` smallint unsigned NOT NULL,
  `price_from_origin` decimal(8,2) NOT NULL,
  `departure_timestamp` datetime NOT NULL,
  `arrival_timestamp` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `trip_cities_uuid_unique` (`uuid`),
  KEY `trip_cities_trip_id_foreign` (`trip_id`),
  KEY `trip_cities_city_id_foreign` (`city_id`),
  CONSTRAINT `trip_cities_city_id_foreign` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`),
  CONSTRAINT `trip_cities_trip_id_foreign` FOREIGN KEY (`trip_id`) REFERENCES `trips` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `trip_cities`
--

LOCK TABLES `trip_cities` WRITE;
/*!40000 ALTER TABLE `trip_cities` DISABLE KEYS */;
INSERT INTO `trip_cities` VALUES (1,'01a00636-8574-7035-bdb9-d70f16dd44da',1,1,0,0.00,'2026-08-16 07:00:00','2026-08-16 07:00:00','2026-08-15 16:17:11','2026-08-15 16:17:11',NULL),(2,'01a00636-8582-7112-b343-d088a5911c48',1,4,1,50.00,'2026-08-16 09:00:00','2026-08-16 09:00:00','2026-08-15 16:17:11','2026-08-15 16:17:11',NULL),(3,'01a00636-8592-724a-a7b1-2eec1022adca',1,5,2,90.00,'2026-08-16 11:30:00','2026-08-16 11:30:00','2026-08-15 16:17:11','2026-08-15 16:17:11',NULL),(4,'01a00636-85a2-73e7-a03c-c57253126b11',1,6,3,140.00,'2026-08-16 14:00:00','2026-08-16 14:00:00','2026-08-15 16:17:11','2026-08-15 16:17:11',NULL),(5,'01a00636-85c0-7083-92b7-cee85695a352',2,1,0,0.00,'2026-08-16 09:00:00','2026-08-16 09:00:00','2026-08-15 16:17:11','2026-08-15 16:17:11',NULL),(6,'01a00636-85cf-703e-ac23-de77bd59f5da',2,16,1,70.00,'2026-08-16 12:00:00','2026-08-16 12:00:00','2026-08-15 16:17:11','2026-08-15 16:17:11',NULL),(7,'01a00636-85e0-71a6-9cbf-5065edbf2793',2,17,2,120.00,'2026-08-16 15:00:00','2026-08-16 15:00:00','2026-08-15 16:17:11','2026-08-15 16:17:11',NULL),(8,'01a00636-85ed-715c-93e5-25c2a4b6ce5b',2,18,3,180.00,'2026-08-16 18:00:00','2026-08-16 18:00:00','2026-08-15 16:17:11','2026-08-15 16:17:11',NULL),(9,'01a00636-85fa-73e0-9b46-fe365f9ad2f3',2,19,4,240.00,'2026-08-16 21:00:00','2026-08-16 21:00:00','2026-08-15 16:17:11','2026-08-15 16:17:11',NULL),(10,'01a00636-8607-7175-ba19-9c00cd8547d6',2,3,5,300.00,'2026-08-17 00:00:00','2026-08-17 00:00:00','2026-08-15 16:17:11','2026-08-15 16:17:11',NULL);
/*!40000 ALTER TABLE `trip_cities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `trips`
--

DROP TABLE IF EXISTS `trips`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `trips` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bus_id` bigint unsigned NOT NULL,
  `from_city_id` bigint unsigned NOT NULL,
  `to_city_id` bigint unsigned NOT NULL,
  `departure_timestamp` datetime NOT NULL,
  `arrival_timestamp` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `trips_uuid_unique` (`uuid`),
  KEY `trips_bus_id_foreign` (`bus_id`),
  KEY `trips_from_city_id_foreign` (`from_city_id`),
  KEY `trips_to_city_id_foreign` (`to_city_id`),
  CONSTRAINT `trips_bus_id_foreign` FOREIGN KEY (`bus_id`) REFERENCES `buses` (`id`),
  CONSTRAINT `trips_from_city_id_foreign` FOREIGN KEY (`from_city_id`) REFERENCES `cities` (`id`),
  CONSTRAINT `trips_to_city_id_foreign` FOREIGN KEY (`to_city_id`) REFERENCES `cities` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `trips`
--

LOCK TABLES `trips` WRITE;
/*!40000 ALTER TABLE `trips` DISABLE KEYS */;
INSERT INTO `trips` VALUES (1,'01a00636-8566-7303-bda3-7dbf0f5a5123',1,1,6,'2026-08-16 07:00:00','2026-08-16 14:00:00','2026-08-15 16:17:11','2026-08-15 16:17:11',NULL),(2,'01a00636-85b4-7182-98a4-4f19f2170296',2,1,3,'2026-08-16 09:00:00','2026-08-17 00:00:00','2026-08-15 16:17:11','2026-08-15 16:17:11',NULL);
/*!40000 ALTER TABLE `trips` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mobile` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT '0',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_uuid_unique` (`uuid`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'01a00636-7e34-7230-a83c-9224d5dbc279','Test','User','test@example.com',NULL,0,'2026-08-15 16:17:09','$2y$12$w0d5wMVD9IVVM3l9cP0zf.w6KQTU0zpc3lZ1QP6t2m/YrxrSNWJqu',NULL,'2026-08-15 16:17:09','2026-08-15 16:17:09',NULL);
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

-- Dump completed on 2026-08-15 16:27:08
