
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
  `idempotency_key` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `bookings_uuid_unique` (`uuid`),
  UNIQUE KEY `bookings_user_id_idempotency_key_seat_id_unique` (`user_id`,`idempotency_key`,`seat_id`),
  KEY `bookings_seat_id_foreign` (`seat_id`),
  KEY `bookings_from_trip_city_id_foreign` (`from_trip_city_id`),
  KEY `bookings_to_trip_city_id_foreign` (`to_trip_city_id`),
  CONSTRAINT `bookings_from_trip_city_id_foreign` FOREIGN KEY (`from_trip_city_id`) REFERENCES `trip_cities` (`id`),
  CONSTRAINT `bookings_seat_id_foreign` FOREIGN KEY (`seat_id`) REFERENCES `seats` (`id`),
  CONSTRAINT `bookings_to_trip_city_id_foreign` FOREIGN KEY (`to_trip_city_id`) REFERENCES `trip_cities` (`id`),
  CONSTRAINT `bookings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `bookings` WRITE;
/*!40000 ALTER TABLE `bookings` DISABLE KEYS */;
/*!40000 ALTER TABLE `bookings` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `buses` WRITE;
/*!40000 ALTER TABLE `buses` DISABLE KEYS */;
INSERT INTO `buses` VALUES (1,'01a0099c-6f03-71a4-9541-b8c8775d5564','First Class','ABC-1234','2026-08-16 08:07:22','2026-08-16 08:07:22',NULL),(2,'01a0099c-6f13-730a-8255-9e13672fbd58','First Class','ABC-5678','2026-08-16 08:07:22','2026-08-16 08:07:22',NULL),(3,'01a0099c-6f1f-73ea-8e0e-cfa52e310233','Business','DEF-1234','2026-08-16 08:07:22','2026-08-16 08:07:22',NULL),(4,'01a0099c-6f2d-70a9-ae93-d24a8f6a002d','Business','DEF-5678','2026-08-16 08:07:22','2026-08-16 08:07:22',NULL),(5,'01a0099c-6f3a-725c-bbc7-31ebfa39b03f','Standard','GHI-1234','2026-08-16 08:07:22','2026-08-16 08:07:22',NULL),(6,'01a0099c-6f4c-72da-834d-f0b0ea39e3da','Standard','GHI-5678','2026-08-16 08:07:22','2026-08-16 08:07:22',NULL);
/*!40000 ALTER TABLE `buses` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;
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
  UNIQUE KEY `cities_uuid_unique` (`uuid`),
  UNIQUE KEY `cities_name_unique` (`name`),
  UNIQUE KEY `cities_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `cities` WRITE;
/*!40000 ALTER TABLE `cities` DISABLE KEYS */;
INSERT INTO `cities` VALUES (1,'01a0099c-6d78-71cf-bb6a-5afc245337d0','Cairo','CAI','2026-08-16 08:07:21','2026-08-16 08:07:21',NULL),(2,'01a0099c-6d83-7019-a308-6e7ef66a1913','Giza','GIZ','2026-08-16 08:07:21','2026-08-16 08:07:21',NULL),(3,'01a0099c-6d8f-71fb-b86c-e27b5e2a71a6','Alexandria','ALX','2026-08-16 08:07:21','2026-08-16 08:07:21',NULL),(4,'01a0099c-6d9f-7355-aac7-870c6b1e812f','AlFayyum','FYM','2026-08-16 08:07:21','2026-08-16 08:07:21',NULL),(5,'01a0099c-6db4-70b8-bc68-9ad764d5c307','AlMinya','MNY','2026-08-16 08:07:21','2026-08-16 08:07:21',NULL),(6,'01a0099c-6dcc-7333-9f1c-4a3ecadf2514','Asyut','ASY','2026-08-16 08:07:21','2026-08-16 08:07:21',NULL),(7,'01a0099c-6dd7-72fa-817d-ffc5bb342273','Sohag','SOH','2026-08-16 08:07:21','2026-08-16 08:07:21',NULL),(8,'01a0099c-6de7-72e9-8ca7-4f51bd752c87','Qena','QNA','2026-08-16 08:07:21','2026-08-16 08:07:21',NULL),(9,'01a0099c-6df6-732d-9296-7ac058c08e3f','Luxor','LXR','2026-08-16 08:07:21','2026-08-16 08:07:21',NULL),(10,'01a0099c-6e0b-726f-a9a6-e804162e10ab','Aswan','ASW','2026-08-16 08:07:21','2026-08-16 08:07:21',NULL),(11,'01a0099c-6e1a-71f2-979a-1e475d4e1db6','BeniSuef','BNS','2026-08-16 08:07:21','2026-08-16 08:07:21',NULL),(12,'01a0099c-6e29-705e-92f9-e8e2fb6d9c83','Ismailia','ISM','2026-08-16 08:07:21','2026-08-16 08:07:21',NULL),(13,'01a0099c-6e37-7353-8606-b40a0a29973e','PortSaid','PSD','2026-08-16 08:07:21','2026-08-16 08:07:21',NULL),(14,'01a0099c-6e49-7270-8de1-c041e375f148','Suez','SUZ','2026-08-16 08:07:21','2026-08-16 08:07:21',NULL),(15,'01a0099c-6e56-73e9-a8dd-3490b65e0fff','Mansoura','MNS','2026-08-16 08:07:21','2026-08-16 08:07:21',NULL),(16,'01a0099c-6e6b-71c6-af49-96a00112d38c','Tanta','TNT','2026-08-16 08:07:21','2026-08-16 08:07:21',NULL),(17,'01a0099c-6e7f-737d-9a94-0516b30de929','Zagazig','ZAG','2026-08-16 08:07:21','2026-08-16 08:07:21',NULL),(18,'01a0099c-6e94-721d-997f-6e99dbb3a886','Damietta','DMT','2026-08-16 08:07:22','2026-08-16 08:07:22',NULL),(19,'01a0099c-6ea4-700f-bd78-d60999bb5501','KafrElSheikh','KFS','2026-08-16 08:07:22','2026-08-16 08:07:22',NULL),(20,'01a0099c-6eb5-7252-9358-603d44866b56','Damanhur','DMH','2026-08-16 08:07:22','2026-08-16 08:07:22',NULL),(21,'01a0099c-6ec8-7027-a3e3-69994ca218f9','Hurghada','HRG','2026-08-16 08:07:22','2026-08-16 08:07:22',NULL),(22,'01a0099c-6ed8-71ea-98c3-ad33a7eb7740','ElArish','ARS','2026-08-16 08:07:22','2026-08-16 08:07:22',NULL),(23,'01a0099c-6ee8-719c-9557-509e6f47e9c5','Marsa Matruh','MMH','2026-08-16 08:07:22','2026-08-16 08:07:22',NULL),(24,'01a0099c-6ef4-71b9-899b-a59cc862dc41','Beni Mazar','BMZ','2026-08-16 08:07:22','2026-08-16 08:07:22',NULL);
/*!40000 ALTER TABLE `cities` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_08_14_132911_create_cities_table',1),(5,'2026_08_14_133232_create_buses_table',1),(6,'2026_08_14_133250_create_trips_table',1),(7,'2026_08_14_133306_create_trip_cities_table',1),(8,'2026_08_14_133326_create_seats_table',1),(9,'2026_08_14_133338_create_bookings_table',1),(10,'2026_08_14_143724_create_personal_access_tokens_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `seats` WRITE;
/*!40000 ALTER TABLE `seats` DISABLE KEYS */;
INSERT INTO `seats` VALUES (1,'01a0099c-6f5f-728a-88bf-0df82675a83d',1,'A1','2026-08-16 08:07:22','2026-08-16 08:07:22',NULL),(2,'01a0099c-6f6c-72b2-8fbb-b44e9317c9f1',1,'A2','2026-08-16 08:07:22','2026-08-16 08:07:22',NULL),(3,'01a0099c-6f79-71c1-bef8-4e4916f17f06',1,'A3','2026-08-16 08:07:22','2026-08-16 08:07:22',NULL),(4,'01a0099c-6f86-729a-beb7-d8b6e70c41d0',1,'A4','2026-08-16 08:07:22','2026-08-16 08:07:22',NULL),(5,'01a0099c-6f90-72b2-8048-4a76c26d9fe7',1,'A5','2026-08-16 08:07:22','2026-08-16 08:07:22',NULL),(6,'01a0099c-6f9d-7090-9d69-ea0e692ba563',1,'A6','2026-08-16 08:07:22','2026-08-16 08:07:22',NULL),(7,'01a0099c-6fb3-70f1-af6f-8abb70bdccce',1,'A7','2026-08-16 08:07:22','2026-08-16 08:07:22',NULL),(8,'01a0099c-6fc4-70d1-9992-a7d60864fd71',1,'A8','2026-08-16 08:07:22','2026-08-16 08:07:22',NULL),(9,'01a0099c-6fd6-73cd-9252-ea4febefe888',1,'A9','2026-08-16 08:07:22','2026-08-16 08:07:22',NULL),(10,'01a0099c-6fe8-73da-bcdf-165b11792352',1,'A10','2026-08-16 08:07:22','2026-08-16 08:07:22',NULL),(11,'01a0099c-6ff8-7155-a725-735b94ea5f1c',1,'A11','2026-08-16 08:07:22','2026-08-16 08:07:22',NULL),(12,'01a0099c-7009-7293-b903-f8090b9efcde',1,'A12','2026-08-16 08:07:22','2026-08-16 08:07:22',NULL),(13,'01a0099c-7019-70b6-afb2-682c86ce0e7e',2,'A1','2026-08-16 08:07:22','2026-08-16 08:07:22',NULL),(14,'01a0099c-702c-73b5-b80e-c6c5febee873',2,'A2','2026-08-16 08:07:22','2026-08-16 08:07:22',NULL),(15,'01a0099c-703f-71a8-8713-3d52cd769b67',2,'A3','2026-08-16 08:07:22','2026-08-16 08:07:22',NULL),(16,'01a0099c-7059-70b3-b986-d56c03a3c9ba',2,'A4','2026-08-16 08:07:22','2026-08-16 08:07:22',NULL),(17,'01a0099c-7071-72c6-aaa7-ff706ce716ce',2,'A5','2026-08-16 08:07:22','2026-08-16 08:07:22',NULL),(18,'01a0099c-7085-7183-853d-4635177f9d37',2,'A6','2026-08-16 08:07:22','2026-08-16 08:07:22',NULL),(19,'01a0099c-7095-7351-b390-b7cc12ff7105',2,'A7','2026-08-16 08:07:22','2026-08-16 08:07:22',NULL),(20,'01a0099c-70a5-73bc-994f-7d8515cd005a',2,'A8','2026-08-16 08:07:22','2026-08-16 08:07:22',NULL),(21,'01a0099c-70b8-721b-87ff-d037e91fc9fa',2,'A9','2026-08-16 08:07:22','2026-08-16 08:07:22',NULL),(22,'01a0099c-70ca-737f-8072-e5dc2759dc2d',2,'A10','2026-08-16 08:07:22','2026-08-16 08:07:22',NULL),(23,'01a0099c-70df-70d4-83ce-7837df464897',2,'A11','2026-08-16 08:07:22','2026-08-16 08:07:22',NULL),(24,'01a0099c-70ee-72cc-9671-224aa02e16b8',2,'A12','2026-08-16 08:07:22','2026-08-16 08:07:22',NULL),(25,'01a0099c-7108-7124-8be9-77feacedc33e',3,'A1','2026-08-16 08:07:22','2026-08-16 08:07:22',NULL),(26,'01a0099c-7121-71f1-b208-e4b398c24322',3,'A2','2026-08-16 08:07:22','2026-08-16 08:07:22',NULL),(27,'01a0099c-7137-7262-b64b-adbd3c2a9f72',3,'A3','2026-08-16 08:07:22','2026-08-16 08:07:22',NULL),(28,'01a0099c-7149-73c9-93e0-7c557b22e906',3,'A4','2026-08-16 08:07:22','2026-08-16 08:07:22',NULL),(29,'01a0099c-715d-7383-a6eb-b5bd8428f369',3,'A5','2026-08-16 08:07:22','2026-08-16 08:07:22',NULL),(30,'01a0099c-716d-7339-81de-07629666b5cb',3,'A6','2026-08-16 08:07:22','2026-08-16 08:07:22',NULL),(31,'01a0099c-7183-72f6-9a03-ac681c006140',3,'A7','2026-08-16 08:07:22','2026-08-16 08:07:22',NULL),(32,'01a0099c-7192-73fb-9fe9-4c2730dd851c',3,'A8','2026-08-16 08:07:22','2026-08-16 08:07:22',NULL),(33,'01a0099c-71a2-717a-a6e4-58f0f38c45b7',3,'A9','2026-08-16 08:07:22','2026-08-16 08:07:22',NULL),(34,'01a0099c-71b0-71c1-9a85-806abe17cf92',3,'A10','2026-08-16 08:07:22','2026-08-16 08:07:22',NULL),(35,'01a0099c-71c0-7381-aada-e7e78083d87b',3,'A11','2026-08-16 08:07:22','2026-08-16 08:07:22',NULL),(36,'01a0099c-71cf-7043-9d27-e12e65602563',3,'A12','2026-08-16 08:07:22','2026-08-16 08:07:22',NULL),(37,'01a0099c-71e1-73bd-9be8-7beba7b7a9a6',4,'A1','2026-08-16 08:07:22','2026-08-16 08:07:22',NULL),(38,'01a0099c-71f4-723d-ab1e-02b2ee32807c',4,'A2','2026-08-16 08:07:22','2026-08-16 08:07:22',NULL),(39,'01a0099c-7209-72fa-9df1-e7f16261edb3',4,'A3','2026-08-16 08:07:22','2026-08-16 08:07:22',NULL),(40,'01a0099c-721f-7211-9ee2-e5f0cbfc7881',4,'A4','2026-08-16 08:07:22','2026-08-16 08:07:22',NULL),(41,'01a0099c-722d-71a0-9536-f8f4185ad0ee',4,'A5','2026-08-16 08:07:22','2026-08-16 08:07:22',NULL),(42,'01a0099c-7245-707a-98d0-60a292e4dde1',4,'A6','2026-08-16 08:07:22','2026-08-16 08:07:22',NULL),(43,'01a0099c-725a-73eb-a3e7-c17303c3f9b2',4,'A7','2026-08-16 08:07:22','2026-08-16 08:07:22',NULL),(44,'01a0099c-7268-7324-ac3c-44b876058e95',4,'A8','2026-08-16 08:07:22','2026-08-16 08:07:22',NULL),(45,'01a0099c-7276-736f-aeb8-a0a847e79e4e',4,'A9','2026-08-16 08:07:22','2026-08-16 08:07:22',NULL),(46,'01a0099c-7281-7302-a650-b69a135c1eaa',4,'A10','2026-08-16 08:07:23','2026-08-16 08:07:23',NULL),(47,'01a0099c-728d-7036-90ae-009f29f7d7a9',4,'A11','2026-08-16 08:07:23','2026-08-16 08:07:23',NULL),(48,'01a0099c-7298-7326-867b-b45c18d636fb',4,'A12','2026-08-16 08:07:23','2026-08-16 08:07:23',NULL),(49,'01a0099c-72a8-72d7-b52c-1b7bc8296e36',5,'A1','2026-08-16 08:07:23','2026-08-16 08:07:23',NULL),(50,'01a0099c-72b8-73a1-9b14-e5ac6565d964',5,'A2','2026-08-16 08:07:23','2026-08-16 08:07:23',NULL),(51,'01a0099c-72c7-70e4-8529-0b3b15d88539',5,'A3','2026-08-16 08:07:23','2026-08-16 08:07:23',NULL),(52,'01a0099c-72d7-7018-88a9-b40cab5b3c45',5,'A4','2026-08-16 08:07:23','2026-08-16 08:07:23',NULL),(53,'01a0099c-72ee-72bb-a49e-6a6553692385',5,'A5','2026-08-16 08:07:23','2026-08-16 08:07:23',NULL),(54,'01a0099c-72fe-73d7-a2a7-1d45eb56973c',5,'A6','2026-08-16 08:07:23','2026-08-16 08:07:23',NULL),(55,'01a0099c-730f-739b-8684-cfb0a235d0ab',5,'A7','2026-08-16 08:07:23','2026-08-16 08:07:23',NULL),(56,'01a0099c-7320-72d0-ba4d-2e64c9b99fc7',5,'A8','2026-08-16 08:07:23','2026-08-16 08:07:23',NULL),(57,'01a0099c-7331-701b-9fa2-ffbd6015280f',5,'A9','2026-08-16 08:07:23','2026-08-16 08:07:23',NULL),(58,'01a0099c-7346-7358-b4f6-9063d39dbb9b',5,'A10','2026-08-16 08:07:23','2026-08-16 08:07:23',NULL),(59,'01a0099c-7358-7205-ace7-5a35d7464fe8',5,'A11','2026-08-16 08:07:23','2026-08-16 08:07:23',NULL),(60,'01a0099c-7368-720e-aa24-ca13ff6f2eb5',5,'A12','2026-08-16 08:07:23','2026-08-16 08:07:23',NULL),(61,'01a0099c-737d-71f2-b012-16c04b4d2747',6,'A1','2026-08-16 08:07:23','2026-08-16 08:07:23',NULL),(62,'01a0099c-738f-7308-9020-8b0eeff75a82',6,'A2','2026-08-16 08:07:23','2026-08-16 08:07:23',NULL),(63,'01a0099c-739e-70d9-b0b9-c452bae2ce6f',6,'A3','2026-08-16 08:07:23','2026-08-16 08:07:23',NULL),(64,'01a0099c-73ad-7322-a810-508241506a58',6,'A4','2026-08-16 08:07:23','2026-08-16 08:07:23',NULL),(65,'01a0099c-73bf-71a4-adda-199bb9632a36',6,'A5','2026-08-16 08:07:23','2026-08-16 08:07:23',NULL),(66,'01a0099c-73cf-73b4-a2b8-e8a33cec639b',6,'A6','2026-08-16 08:07:23','2026-08-16 08:07:23',NULL),(67,'01a0099c-73e0-702f-a9b8-56f06c13daf7',6,'A7','2026-08-16 08:07:23','2026-08-16 08:07:23',NULL),(68,'01a0099c-73f0-7125-a582-50b384a7ce72',6,'A8','2026-08-16 08:07:23','2026-08-16 08:07:23',NULL),(69,'01a0099c-73ff-7361-8af5-04bf93b98575',6,'A9','2026-08-16 08:07:23','2026-08-16 08:07:23',NULL),(70,'01a0099c-740d-73df-91fd-cf57251e6d81',6,'A10','2026-08-16 08:07:23','2026-08-16 08:07:23',NULL),(71,'01a0099c-7420-7130-b84b-272d828d9541',6,'A11','2026-08-16 08:07:23','2026-08-16 08:07:23',NULL),(72,'01a0099c-742e-7330-8b80-8fbd553e4122',6,'A12','2026-08-16 08:07:23','2026-08-16 08:07:23',NULL);
/*!40000 ALTER TABLE `seats` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;
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
  UNIQUE KEY `trip_cities_trip_id_city_id_unique` (`trip_id`,`city_id`),
  UNIQUE KEY `trip_cities_trip_id_sequence_unique` (`trip_id`,`sequence`),
  UNIQUE KEY `trip_cities_uuid_unique` (`uuid`),
  KEY `trip_cities_city_id_foreign` (`city_id`),
  CONSTRAINT `trip_cities_city_id_foreign` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`),
  CONSTRAINT `trip_cities_trip_id_foreign` FOREIGN KEY (`trip_id`) REFERENCES `trips` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `trip_cities` WRITE;
/*!40000 ALTER TABLE `trip_cities` DISABLE KEYS */;
INSERT INTO `trip_cities` VALUES (1,'01a0099c-745d-7090-ba3c-037dd63bd18b',1,1,0,0.00,'2026-08-17 07:00:00','2026-08-17 07:00:00','2026-08-16 08:07:23','2026-08-16 08:07:23',NULL),(2,'01a0099c-746f-7087-84a5-8eee7b95f9cf',1,4,1,50.00,'2026-08-17 09:00:00','2026-08-17 09:00:00','2026-08-16 08:07:23','2026-08-16 08:07:23',NULL),(3,'01a0099c-7482-7236-b392-33ab7db82e51',1,5,2,90.00,'2026-08-17 11:30:00','2026-08-17 11:30:00','2026-08-16 08:07:23','2026-08-16 08:07:23',NULL),(4,'01a0099c-7495-72fd-9bce-172eac363c77',1,6,3,140.00,'2026-08-17 14:00:00','2026-08-17 14:00:00','2026-08-16 08:07:23','2026-08-16 08:07:23',NULL),(5,'01a0099c-74c7-7030-a1c1-8b2e8fbbdc5a',2,1,0,0.00,'2026-08-17 09:00:00','2026-08-17 09:00:00','2026-08-16 08:07:23','2026-08-16 08:07:23',NULL),(6,'01a0099c-74dc-7060-bedf-ea2402200cf5',2,16,1,70.00,'2026-08-17 12:00:00','2026-08-17 12:00:00','2026-08-16 08:07:23','2026-08-16 08:07:23',NULL),(7,'01a0099c-74f4-7352-8aa8-2104a9a7c9f7',2,17,2,120.00,'2026-08-17 15:00:00','2026-08-17 15:00:00','2026-08-16 08:07:23','2026-08-16 08:07:23',NULL),(8,'01a0099c-7510-713f-a42c-c71891f87a2c',2,18,3,180.00,'2026-08-17 18:00:00','2026-08-17 18:00:00','2026-08-16 08:07:23','2026-08-16 08:07:23',NULL),(9,'01a0099c-7523-7161-96d3-35374ceb6771',2,19,4,240.00,'2026-08-17 21:00:00','2026-08-17 21:00:00','2026-08-16 08:07:23','2026-08-16 08:07:23',NULL),(10,'01a0099c-7536-7190-b931-1babfd7a7731',2,3,5,300.00,'2026-08-18 00:00:00','2026-08-18 00:00:00','2026-08-16 08:07:23','2026-08-16 08:07:23',NULL);
/*!40000 ALTER TABLE `trip_cities` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `trips` WRITE;
/*!40000 ALTER TABLE `trips` DISABLE KEYS */;
INSERT INTO `trips` VALUES (1,'01a0099c-744b-733f-8c60-276ce8a80c54',1,1,6,'2026-08-17 07:00:00','2026-08-17 14:00:00','2026-08-16 08:07:23','2026-08-16 08:07:23',NULL),(2,'01a0099c-74af-7320-aaaf-593da30344ac',2,1,3,'2026-08-17 09:00:00','2026-08-18 00:00:00','2026-08-16 08:07:23','2026-08-16 08:07:23',NULL);
/*!40000 ALTER TABLE `trips` ENABLE KEYS */;
UNLOCK TABLES;
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
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT '0',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_uuid_unique` (`uuid`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_mobile_unique` (`mobile`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'01a0099c-6c60-736c-aeda-e2f154dd54e3','Test','User','test@example.com',NULL,'2026-08-16 08:07:21','$2y$12$0tXw2xjQnTu6dSMmk29QMuMgdmyjTOOeQUgBPZwXcrPMW6ww0e77C',0,NULL,'2026-08-16 08:07:21','2026-08-16 08:07:21',NULL),(2,'01a0099c-6d66-7309-893e-d6e5da54c1a8','Admin','User','admin@example.com',NULL,'2026-08-16 08:07:21','$2y$12$YFeQfHTcQ30mruxXZ709X.I/SBbDck5Xgdk3PcqFWH04SpQ6ADQGG',1,NULL,'2026-08-16 08:07:21','2026-08-16 08:07:21',NULL);
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

