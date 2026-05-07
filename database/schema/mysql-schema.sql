/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `activities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activities` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `crop_stage_id` bigint unsigned DEFAULT NULL,
  `activity_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `cost` decimal(10,2) DEFAULT NULL,
  `labor_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activity_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `staff_id` bigint unsigned DEFAULT NULL,
  `crop_cycle_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `activities_staff_id_foreign` (`staff_id`),
  KEY `activities_crop_stage_id_foreign` (`crop_stage_id`),
  KEY `activities_crop_cycle_id_foreign` (`crop_cycle_id`),
  CONSTRAINT `activities_crop_cycle_id_foreign` FOREIGN KEY (`crop_cycle_id`) REFERENCES `crop_cycles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `activities_crop_stage_id_foreign` FOREIGN KEY (`crop_stage_id`) REFERENCES `crop_stages` (`id`) ON DELETE CASCADE,
  CONSTRAINT `activities_staff_id_foreign` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `alerts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `alerts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sensor_reading_id` bigint unsigned NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `severity` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `parameter` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` decimal(10,2) DEFAULT NULL,
  `threshold` decimal(10,2) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `alerts_sensor_reading_id_foreign` (`sensor_reading_id`),
  KEY `alerts_is_read_index` (`is_read`),
  KEY `alerts_created_at_index` (`created_at`),
  CONSTRAINT `alerts_sensor_reading_id_foreign` FOREIGN KEY (`sensor_reading_id`) REFERENCES `sensor_readings` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `automation_rules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `automation_rules` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `field_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sensor_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `operator` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `threshold` decimal(10,2) NOT NULL,
  `action` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `cooldown_minutes` int NOT NULL DEFAULT '30',
  `last_triggered` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `automation_rules_field_id_foreign` (`field_id`),
  CONSTRAINT `automation_rules_field_id_foreign` FOREIGN KEY (`field_id`) REFERENCES `fields` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `buyers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `buyers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `farm_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `buyer_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'individual',
  `credit_limit` decimal(10,2) DEFAULT '0.00',
  `rating` decimal(3,1) DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `buyers_farm_id_is_active_index` (`farm_id`,`is_active`),
  CONSTRAINT `buyers_farm_id_foreign` FOREIGN KEY (`farm_id`) REFERENCES `farms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `crop_analyses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `crop_analyses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `field_id` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned NOT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `diagnosis` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `severity` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `recommendation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `detected_issues` json DEFAULT NULL,
  `confidence_score` decimal(5,2) DEFAULT NULL,
  `status` enum('pending','analyzed','reviewed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `crop_cycle_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `crop_analyses_field_id_foreign` (`field_id`),
  KEY `crop_analyses_user_id_foreign` (`user_id`),
  KEY `crop_analyses_crop_cycle_id_foreign` (`crop_cycle_id`),
  CONSTRAINT `crop_analyses_crop_cycle_id_foreign` FOREIGN KEY (`crop_cycle_id`) REFERENCES `crop_cycles` (`id`) ON DELETE SET NULL,
  CONSTRAINT `crop_analyses_field_id_foreign` FOREIGN KEY (`field_id`) REFERENCES `fields` (`id`) ON DELETE SET NULL,
  CONSTRAINT `crop_analyses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `crop_analysis_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `crop_analysis_images` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `crop_analysis_id` bigint unsigned NOT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `order` int NOT NULL DEFAULT '0',
  `label` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `crop_analysis_images_crop_analysis_id_foreign` (`crop_analysis_id`),
  CONSTRAINT `crop_analysis_images_crop_analysis_id_foreign` FOREIGN KEY (`crop_analysis_id`) REFERENCES `crop_analyses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `crop_cycles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `crop_cycles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `field_id` bigint unsigned NOT NULL,
  `crop_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `variety` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `season` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `crop_id` bigint unsigned NOT NULL,
  `start_date` date NOT NULL,
  `irrigation_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `irrigation_schedule` text COLLATE utf8mb4_unicode_ci,
  `drainage` text COLLATE utf8mb4_unicode_ci,
  `ph_level` decimal(4,2) DEFAULT NULL,
  `previous_crop_cycle_id` bigint unsigned DEFAULT NULL,
  `soil_type_override` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `water_source_override` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `current_stage` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expected_harvest_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `farm_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `crop_cycles_field_id_foreign` (`field_id`),
  KEY `crop_cycles_crop_id_foreign` (`crop_id`),
  KEY `crop_cycles_farm_id_foreign` (`farm_id`),
  KEY `crop_cycles_previous_crop_cycle_id_foreign` (`previous_crop_cycle_id`),
  CONSTRAINT `crop_cycles_crop_id_foreign` FOREIGN KEY (`crop_id`) REFERENCES `crops` (`id`) ON DELETE CASCADE,
  CONSTRAINT `crop_cycles_farm_id_foreign` FOREIGN KEY (`farm_id`) REFERENCES `farms` (`id`) ON DELETE SET NULL,
  CONSTRAINT `crop_cycles_field_id_foreign` FOREIGN KEY (`field_id`) REFERENCES `fields` (`id`) ON DELETE CASCADE,
  CONSTRAINT `crop_cycles_previous_crop_cycle_id_foreign` FOREIGN KEY (`previous_crop_cycle_id`) REFERENCES `crop_cycles` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `crop_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `crop_history` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `farmer_id` bigint unsigned NOT NULL,
  `crop_id` bigint unsigned DEFAULT NULL,
  `crop_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `year` year NOT NULL,
  ` hectares` decimal(10,2) DEFAULT NULL,
  `expected_yield` decimal(10,2) DEFAULT NULL,
  `actual_yield` decimal(10,2) DEFAULT NULL,
  `yield_unit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'kg',
  `income` decimal(12,2) DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `crop_history_crop_id_foreign` (`crop_id`),
  KEY `crop_history_farmer_id_index` (`farmer_id`),
  KEY `crop_history_year_crop_name_index` (`year`,`crop_name`),
  CONSTRAINT `crop_history_crop_id_foreign` FOREIGN KEY (`crop_id`) REFERENCES `crops` (`id`) ON DELETE SET NULL,
  CONSTRAINT `crop_history_farmer_id_foreign` FOREIGN KEY (`farmer_id`) REFERENCES `farmers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `crop_rotations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `crop_rotations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `crop_id` bigint unsigned NOT NULL,
  `previous_crop_id` bigint unsigned NOT NULL,
  `sequence_order` int NOT NULL,
  `yield_benefit_percentage` decimal(5,2) DEFAULT NULL,
  `benefits` text COLLATE utf8mb4_unicode_ci,
  `risks` text COLLATE utf8mb4_unicode_ci,
  `recommendations` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `crop_rotations_previous_crop_id_foreign` (`previous_crop_id`),
  KEY `crop_rotations_crop_id_previous_crop_id_index` (`crop_id`,`previous_crop_id`),
  CONSTRAINT `crop_rotations_crop_id_foreign` FOREIGN KEY (`crop_id`) REFERENCES `crops` (`id`) ON DELETE CASCADE,
  CONSTRAINT `crop_rotations_previous_crop_id_foreign` FOREIGN KEY (`previous_crop_id`) REFERENCES `crops` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `crop_seasons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `crop_seasons` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `crop_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `season_period` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `planting_start_date` date NOT NULL,
  `planting_end_date` date NOT NULL,
  `expected_harvest_start` date NOT NULL,
  `expected_harvest_end` date NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `is_optimal` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `crop_seasons_crop_id_index` (`crop_id`),
  KEY `crop_seasons_season_period_index` (`season_period`),
  CONSTRAINT `crop_seasons_crop_id_foreign` FOREIGN KEY (`crop_id`) REFERENCES `crops` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `crop_stages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `crop_stages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `crop_cycle_id` bigint unsigned NOT NULL,
  `stage_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `health_status` enum('excellent','good','fair','poor') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `germination_rate` decimal(5,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `crop_stages_crop_cycle_id_foreign` (`crop_cycle_id`),
  CONSTRAINT `crop_stages_crop_cycle_id_foreign` FOREIGN KEY (`crop_cycle_id`) REFERENCES `crop_cycles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `crops`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `crops` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `field_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `variety` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `days_to_maturity` int DEFAULT NULL,
  `average_yield_per_hectare` decimal(10,2) DEFAULT NULL,
  `yield_unit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'kg',
  `season_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `planting_date` date DEFAULT NULL,
  `expected_harvest_date` date DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `growth_stages` json DEFAULT NULL,
  `soil_requirements` json DEFAULT NULL,
  `water_requirements` json DEFAULT NULL,
  `pest_vulnerabilities` json DEFAULT NULL,
  `min_temperature` decimal(5,2) DEFAULT NULL,
  `max_temperature` decimal(5,2) DEFAULT NULL,
  `optimal_ph_min` decimal(4,2) DEFAULT NULL,
  `optimal_ph_max` decimal(4,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `crops_field_name_variety_unique` (`field_id`,`name`,`variety`),
  KEY `crops_category_index` (`category`),
  KEY `crops_season_type_index` (`season_type`),
  KEY `crops_user_id_foreign` (`user_id`),
  CONSTRAINT `crops_field_id_foreign` FOREIGN KEY (`field_id`) REFERENCES `fields` (`id`) ON DELETE CASCADE,
  CONSTRAINT `crops_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `expenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `expenses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `farm_id` bigint unsigned NOT NULL,
  `crop_id` bigint unsigned DEFAULT NULL,
  `expense_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `expense_date` date NOT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `receipt_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `staff_id` bigint unsigned DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `expenses_crop_id_foreign` (`crop_id`),
  KEY `expenses_staff_id_foreign` (`staff_id`),
  KEY `expenses_farm_id_expense_date_index` (`farm_id`,`expense_date`),
  KEY `expenses_expense_type_expense_date_index` (`expense_type`,`expense_date`),
  CONSTRAINT `expenses_crop_id_foreign` FOREIGN KEY (`crop_id`) REFERENCES `crops` (`id`) ON DELETE SET NULL,
  CONSTRAINT `expenses_farm_id_foreign` FOREIGN KEY (`farm_id`) REFERENCES `farms` (`id`) ON DELETE CASCADE,
  CONSTRAINT `expenses_staff_id_foreign` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
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
DROP TABLE IF EXISTS `farm_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `farm_documents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `farm_id` bigint unsigned NOT NULL,
  `document_type` enum('ownership','title_deed','tax_compliance','insurance','certification','other') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'other',
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `original_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_size` bigint unsigned DEFAULT NULL,
  `mime_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `uploaded_by` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `farm_documents_farm_id_foreign` (`farm_id`),
  KEY `farm_documents_uploaded_by_foreign` (`uploaded_by`),
  CONSTRAINT `farm_documents_farm_id_foreign` FOREIGN KEY (`farm_id`) REFERENCES `farms` (`id`) ON DELETE CASCADE,
  CONSTRAINT `farm_documents_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `farm_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `farm_images` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `farm_id` bigint unsigned NOT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `caption` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `farm_images_farm_id_foreign` (`farm_id`),
  CONSTRAINT `farm_images_farm_id_foreign` FOREIGN KEY (`farm_id`) REFERENCES `farms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `farmer_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `farmer_documents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `farmer_id` bigint unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `document_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_size` int NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_verified` tinyint(1) NOT NULL DEFAULT '0',
  `verified_by` bigint unsigned DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `verification_notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `farmer_documents_verified_by_foreign` (`verified_by`),
  KEY `farmer_documents_farmer_id_index` (`farmer_id`),
  KEY `farmer_documents_document_type_index` (`document_type`),
  CONSTRAINT `farmer_documents_farmer_id_foreign` FOREIGN KEY (`farmer_id`) REFERENCES `farmers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `farmer_documents_verified_by_foreign` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `farmers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `farmers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `national_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('male','female','other') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `village` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ward` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `district` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `region` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `farm_size_hectares` decimal(10,2) DEFAULT NULL,
  `farm_type` enum('smallholder','medium','large') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'smallholder',
  `crop_history` json DEFAULT NULL,
  `farming_methods` json DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `farmers_national_id_unique` (`national_id`),
  KEY `farmers_user_id_index` (`user_id`),
  KEY `farmers_district_region_index` (`district`,`region`),
  CONSTRAINT `farmers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `farms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `farms` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subcounty` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `physical_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `farm_type` enum('crop','livestock','mixed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'crop',
  `ownership_type` enum('owned','leased','community') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'owned',
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `gps_latitude` decimal(10,8) DEFAULT NULL,
  `gps_longitude` decimal(11,8) DEFAULT NULL,
  `size_hectares` decimal(10,2) DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `storage_facilities` tinyint(1) NOT NULL DEFAULT '0',
  `estimated_budget` decimal(15,2) DEFAULT NULL,
  `main_purpose` enum('commercial','subsistence') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `farm_operation_details` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `main_image_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `farms_user_id_foreign` (`user_id`),
  KEY `farms_main_image_id_foreign` (`main_image_id`),
  CONSTRAINT `farms_main_image_id_foreign` FOREIGN KEY (`main_image_id`) REFERENCES `farm_images` (`id`) ON DELETE SET NULL,
  CONSTRAINT `farms_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `feed_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `feed_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `default_unit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `min_threshold` decimal(12,2) NOT NULL DEFAULT '10.00',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `feed_types_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `feed_usages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `feed_usages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `feed_type_id` bigint unsigned NOT NULL,
  `livestock_id` bigint unsigned NOT NULL,
  `quantity` decimal(12,2) NOT NULL,
  `unit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `usage_date` date NOT NULL,
  `usage_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `feed_usages_livestock_id_foreign` (`livestock_id`),
  KEY `feed_usages_feed_type_id_livestock_id_index` (`feed_type_id`,`livestock_id`),
  KEY `feed_usages_usage_date_index` (`usage_date`),
  CONSTRAINT `feed_usages_feed_type_id_foreign` FOREIGN KEY (`feed_type_id`) REFERENCES `feed_types` (`id`) ON DELETE CASCADE,
  CONSTRAINT `feed_usages_livestock_id_foreign` FOREIGN KEY (`livestock_id`) REFERENCES `livestock` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `fertilizer_applications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fertilizer_applications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `fertilizer_id` bigint unsigned NOT NULL,
  `field_id` bigint unsigned NOT NULL,
  `quantity_used` decimal(12,2) NOT NULL,
  `unit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `application_date` date NOT NULL,
  `growth_stage` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `application_method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fertilizer_applications_field_id_foreign` (`field_id`),
  KEY `fertilizer_applications_fertilizer_id_field_id_index` (`fertilizer_id`,`field_id`),
  KEY `fertilizer_applications_application_date_index` (`application_date`),
  CONSTRAINT `fertilizer_applications_fertilizer_id_foreign` FOREIGN KEY (`fertilizer_id`) REFERENCES `fertilizers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fertilizer_applications_field_id_foreign` FOREIGN KEY (`field_id`) REFERENCES `fields` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `fertilizer_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fertilizer_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `default_unit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'kg',
  `min_threshold` decimal(12,2) NOT NULL DEFAULT '10.00',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `fertilizer_types_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `fertilizers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fertilizers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `fertilizer_type_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` decimal(12,2) NOT NULL,
  `unit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `unit_cost` decimal(12,2) DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `supplier_id` bigint unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fertilizers_fertilizer_type_id_is_active_index` (`fertilizer_type_id`,`is_active`),
  CONSTRAINT `fertilizers_fertilizer_type_id_foreign` FOREIGN KEY (`fertilizer_type_id`) REFERENCES `fertilizer_types` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `fields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fields` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `farm_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `size_hectares` decimal(10,2) DEFAULT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rainfall_zone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `topography` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `water_source` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `seed_quantity_estimate` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fertilizer_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fertilizer_amount` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pest_control_recommendations` text COLLATE utf8mb4_unicode_ci,
  `feed_requirements` text COLLATE utf8mb4_unicode_ci,
  `vaccination_schedule` text COLLATE utf8mb4_unicode_ci,
  `housing_requirements` text COLLATE utf8mb4_unicode_ci,
  `soil_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gps_latitude` decimal(10,8) DEFAULT NULL,
  `gps_longitude` decimal(11,8) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fields_farm_id_foreign` (`farm_id`),
  KEY `fields_user_id_foreign` (`user_id`),
  CONSTRAINT `fields_farm_id_foreign` FOREIGN KEY (`farm_id`) REFERENCES `farms` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fields_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `food_stocks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `food_stocks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `feed_type_id` bigint unsigned NOT NULL,
  `quantity` decimal(12,2) NOT NULL,
  `unit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `unit_cost` decimal(12,2) DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `supplier_id` bigint unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `food_stocks_user_id_is_active_index` (`user_id`,`is_active`),
  KEY `food_stocks_feed_type_id_is_active_index` (`feed_type_id`,`is_active`),
  KEY `food_stocks_supplier_id_foreign` (`supplier_id`),
  KEY `food_stocks_expiry_date_index` (`expiry_date`),
  CONSTRAINT `food_stocks_feed_type_id_foreign` FOREIGN KEY (`feed_type_id`) REFERENCES `feed_types` (`id`) ON DELETE CASCADE,
  CONSTRAINT `food_stocks_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `food_stocks_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `growth_measurements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `growth_measurements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `crop_stage_id` bigint unsigned NOT NULL,
  `measurement_date` date DEFAULT NULL,
  `height_cm` decimal(6,2) DEFAULT NULL,
  `leaf_count` int DEFAULT NULL,
  `fruit_count` int DEFAULT NULL,
  `health_notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `growth_measurements_crop_stage_id_measurement_date_index` (`crop_stage_id`,`measurement_date`),
  CONSTRAINT `growth_measurements_crop_stage_id_foreign` FOREIGN KEY (`crop_stage_id`) REFERENCES `crop_stages` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `harvests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `harvests` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `crop_id` bigint unsigned NOT NULL,
  `field_id` bigint unsigned NOT NULL,
  `farm_id` bigint unsigned NOT NULL,
  `harvest_date` date NOT NULL,
  `harvest_batch` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity_harvested` decimal(12,2) NOT NULL DEFAULT '0.00',
  `unit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'kg',
  `quality_grade` enum('grade_a','grade_b','grade_c','reject') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'grade_b',
  `quality_percentage` decimal(5,2) NOT NULL DEFAULT '100.00',
  `loss_quantity` decimal(12,2) NOT NULL DEFAULT '0.00',
  `loss_reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `loss_percentage` decimal(5,2) NOT NULL DEFAULT '0.00',
  `storage_location` enum('field','barn','warehouse','cold_storage','sold_immediately') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'warehouse',
  `storage_details` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `moisture_content` decimal(5,2) DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `crop_cycle_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `harvests_farm_id_foreign` (`farm_id`),
  KEY `harvests_crop_id_harvest_date_index` (`crop_id`,`harvest_date`),
  KEY `harvests_field_id_harvest_date_index` (`field_id`,`harvest_date`),
  KEY `harvests_harvest_date_index` (`harvest_date`),
  KEY `harvests_crop_cycle_id_foreign` (`crop_cycle_id`),
  CONSTRAINT `harvests_crop_cycle_id_foreign` FOREIGN KEY (`crop_cycle_id`) REFERENCES `crop_cycles` (`id`) ON DELETE SET NULL,
  CONSTRAINT `harvests_crop_id_foreign` FOREIGN KEY (`crop_id`) REFERENCES `crops` (`id`) ON DELETE CASCADE,
  CONSTRAINT `harvests_farm_id_foreign` FOREIGN KEY (`farm_id`) REFERENCES `farms` (`id`) ON DELETE CASCADE,
  CONSTRAINT `harvests_field_id_foreign` FOREIGN KEY (`field_id`) REFERENCES `fields` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `inputs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inputs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `activity_id` bigint unsigned DEFAULT NULL,
  `input_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int NOT NULL,
  `unit` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cost` decimal(10,2) DEFAULT NULL,
  `application_date` date DEFAULT NULL,
  `application_method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `crop_cycle_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `inputs_activity_id_foreign` (`activity_id`),
  KEY `inputs_crop_cycle_id_foreign` (`crop_cycle_id`),
  CONSTRAINT `inputs_activity_id_foreign` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`id`) ON DELETE CASCADE,
  CONSTRAINT `inputs_crop_cycle_id_foreign` FOREIGN KEY (`crop_cycle_id`) REFERENCES `crop_cycles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `irrigation_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `irrigation_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `irrigation_zone_id` bigint unsigned NOT NULL,
  `triggered_by` bigint unsigned DEFAULT NULL,
  `event_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `started_at` timestamp NOT NULL,
  `ended_at` timestamp NULL DEFAULT NULL,
  `duration_minutes` int DEFAULT NULL,
  `water_used_liters` decimal(10,2) DEFAULT NULL,
  `soil_moisture_before` decimal(5,2) DEFAULT NULL,
  `soil_moisture_after` decimal(5,2) DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'running',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `irrigation_logs_triggered_by_foreign` (`triggered_by`),
  KEY `irrigation_logs_irrigation_zone_id_started_at_index` (`irrigation_zone_id`,`started_at`),
  CONSTRAINT `irrigation_logs_irrigation_zone_id_foreign` FOREIGN KEY (`irrigation_zone_id`) REFERENCES `irrigation_zones` (`id`) ON DELETE CASCADE,
  CONSTRAINT `irrigation_logs_triggered_by_foreign` FOREIGN KEY (`triggered_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `irrigation_zones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `irrigation_zones` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `field_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `controller_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `valve_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','inactive','maintenance') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `flow_rate_lph` decimal(10,2) DEFAULT NULL,
  `duration_minutes` int NOT NULL DEFAULT '30',
  `start_time` time DEFAULT NULL,
  `schedule_type` enum('manual','scheduled','smart','weekdays') COLLATE utf8mb4_unicode_ci DEFAULT 'manual',
  `schedule_days` json DEFAULT NULL,
  `soil_moisture_threshold` decimal(5,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `notes` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `irrigation_zones_field_id_foreign` (`field_id`),
  CONSTRAINT `irrigation_zones_field_id_foreign` FOREIGN KEY (`field_id`) REFERENCES `fields` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
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
  KEY `jobs_queue_reserved_at_available_at_index` (`queue`,`reserved_at`,`available_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `livestock`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `livestock` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `livestock_type_id` bigint unsigned NOT NULL,
  `farm_id` bigint unsigned DEFAULT NULL,
  `tag_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tracking_id` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_acquired` date DEFAULT NULL,
  `weight` decimal(8,2) DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `gender` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_id` bigint unsigned DEFAULT NULL,
  `status` enum('healthy','sick','sold','dead') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'healthy',
  `purchase_price` decimal(12,2) DEFAULT NULL,
  `sale_price` decimal(12,2) DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `livestock_tag_number_unique` (`tag_number`),
  UNIQUE KEY `livestock_tracking_id_unique` (`tracking_id`),
  KEY `livestock_farm_id_foreign` (`farm_id`),
  KEY `livestock_parent_id_foreign` (`parent_id`),
  KEY `livestock_user_id_status_index` (`user_id`,`status`),
  KEY `livestock_livestock_type_id_status_index` (`livestock_type_id`,`status`),
  KEY `livestock_tag_number_index` (`tag_number`),
  KEY `livestock_tracking_id_index` (`tracking_id`),
  CONSTRAINT `livestock_farm_id_foreign` FOREIGN KEY (`farm_id`) REFERENCES `farms` (`id`) ON DELETE SET NULL,
  CONSTRAINT `livestock_livestock_type_id_foreign` FOREIGN KEY (`livestock_type_id`) REFERENCES `livestock_types` (`id`) ON DELETE CASCADE,
  CONSTRAINT `livestock_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `livestock` (`id`) ON DELETE SET NULL,
  CONSTRAINT `livestock_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `livestock_analyses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `livestock_analyses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `livestock_id` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned NOT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `diagnosis` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `severity` enum('low','medium','high') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `recommendation` text COLLATE utf8mb4_unicode_ci,
  `detected_issues` json DEFAULT NULL,
  `confidence_score` decimal(5,2) DEFAULT NULL,
  `status` enum('pending','analyzed','reviewed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `livestock_analyses_livestock_id_index` (`livestock_id`),
  KEY `livestock_analyses_user_id_index` (`user_id`),
  KEY `livestock_analyses_status_index` (`status`),
  KEY `livestock_analyses_created_at_index` (`created_at`),
  CONSTRAINT `livestock_analyses_livestock_id_foreign` FOREIGN KEY (`livestock_id`) REFERENCES `livestock` (`id`) ON DELETE CASCADE,
  CONSTRAINT `livestock_analyses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `livestock_locations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `livestock_locations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `livestock_id` bigint unsigned NOT NULL,
  `field_id` bigint unsigned DEFAULT NULL,
  `farm_id` bigint unsigned DEFAULT NULL,
  `gps_latitude` decimal(10,8) DEFAULT NULL,
  `gps_longitude` decimal(11,8) DEFAULT NULL,
  `location_type` enum('field','farm','pasture','barn','transport','sick_bay','other') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'field',
  `movement_type` enum('grazing','resting','feeding','transport','treatment','inspection','birth','other') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'grazing',
  `entered_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `left_at` timestamp NULL DEFAULT NULL,
  `duration_minutes` int DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `livestock_locations_livestock_id_entered_at_index` (`livestock_id`,`entered_at`),
  KEY `livestock_locations_field_id_entered_at_index` (`field_id`,`entered_at`),
  KEY `livestock_locations_farm_id_entered_at_index` (`farm_id`,`entered_at`),
  KEY `livestock_locations_entered_at_index` (`entered_at`),
  CONSTRAINT `livestock_locations_farm_id_foreign` FOREIGN KEY (`farm_id`) REFERENCES `farms` (`id`) ON DELETE SET NULL,
  CONSTRAINT `livestock_locations_field_id_foreign` FOREIGN KEY (`field_id`) REFERENCES `fields` (`id`) ON DELETE SET NULL,
  CONSTRAINT `livestock_locations_livestock_id_foreign` FOREIGN KEY (`livestock_id`) REFERENCES `livestock` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `livestock_movements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `livestock_movements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `livestock_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `movement_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `from_farm_id` bigint unsigned DEFAULT NULL,
  `to_farm_id` bigint unsigned DEFAULT NULL,
  `from_field_id` bigint unsigned DEFAULT NULL,
  `to_field_id` bigint unsigned DEFAULT NULL,
  `movement_date` datetime NOT NULL,
  `reason` text COLLATE utf8mb4_unicode_ci,
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `livestock_movements_user_id_foreign` (`user_id`),
  KEY `livestock_movements_to_farm_id_foreign` (`to_farm_id`),
  KEY `livestock_movements_from_field_id_foreign` (`from_field_id`),
  KEY `livestock_movements_to_field_id_foreign` (`to_field_id`),
  KEY `livestock_movements_livestock_id_movement_date_index` (`livestock_id`,`movement_date`),
  KEY `livestock_movements_from_farm_id_to_farm_id_index` (`from_farm_id`,`to_farm_id`),
  KEY `livestock_movements_movement_date_index` (`movement_date`),
  CONSTRAINT `livestock_movements_from_farm_id_foreign` FOREIGN KEY (`from_farm_id`) REFERENCES `farms` (`id`) ON DELETE SET NULL,
  CONSTRAINT `livestock_movements_from_field_id_foreign` FOREIGN KEY (`from_field_id`) REFERENCES `fields` (`id`) ON DELETE SET NULL,
  CONSTRAINT `livestock_movements_livestock_id_foreign` FOREIGN KEY (`livestock_id`) REFERENCES `livestock` (`id`) ON DELETE CASCADE,
  CONSTRAINT `livestock_movements_to_farm_id_foreign` FOREIGN KEY (`to_farm_id`) REFERENCES `farms` (`id`) ON DELETE SET NULL,
  CONSTRAINT `livestock_movements_to_field_id_foreign` FOREIGN KEY (`to_field_id`) REFERENCES `fields` (`id`) ON DELETE SET NULL,
  CONSTRAINT `livestock_movements_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `livestock_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `livestock_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `requires_individual_tracking` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `livestock_types_name_unique` (`name`),
  UNIQUE KEY `livestock_types_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `livestock_wound_analyses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `livestock_wound_analyses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `priority` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'normal',
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `read_at` timestamp NULL DEFAULT NULL,
  `data` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_user_id_is_read_index` (`user_id`,`is_read`),
  CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `farm_id` bigint unsigned NOT NULL,
  `buyer_id` bigint unsigned NOT NULL,
  `crop_id` bigint unsigned DEFAULT NULL,
  `order_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_date` date NOT NULL,
  `delivery_date` date DEFAULT NULL,
  `quantity` decimal(12,2) NOT NULL,
  `unit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'kg',
  `price_per_unit` decimal(10,2) NOT NULL,
  `total_amount` decimal(12,2) NOT NULL,
  `status` enum('pending','confirmed','processing','shipped','delivered','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `payment_status` enum('unpaid','partial','paid') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unpaid',
  `paid_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `payment_due_date` date DEFAULT NULL,
  `delivery_address` text COLLATE utf8mb4_unicode_ci,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `orders_order_number_unique` (`order_number`),
  KEY `orders_farm_id_foreign` (`farm_id`),
  KEY `orders_crop_id_foreign` (`crop_id`),
  KEY `orders_buyer_id_status_index` (`buyer_id`,`status`),
  KEY `orders_order_date_status_index` (`order_date`,`status`),
  CONSTRAINT `orders_buyer_id_foreign` FOREIGN KEY (`buyer_id`) REFERENCES `buyers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `orders_crop_id_foreign` FOREIGN KEY (`crop_id`) REFERENCES `crops` (`id`) ON DELETE SET NULL,
  CONSTRAINT `orders_farm_id_foreign` FOREIGN KEY (`farm_id`) REFERENCES `farms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
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
DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
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
DROP TABLE IF EXISTS `pest_disease_treatments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pest_disease_treatments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `crop_cycle_id` bigint unsigned NOT NULL,
  `issue_type` enum('pest','disease','other') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `severity` enum('low','medium','high','critical') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `affected_area` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `treatment_date` date DEFAULT NULL,
  `treatment_method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `chemicals_used` text COLLATE utf8mb4_unicode_ci,
  `outcome` enum('resolved','ongoing','unresolved') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cost` decimal(10,2) DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pest_disease_treatments_crop_cycle_id_treatment_date_index` (`crop_cycle_id`,`treatment_date`),
  CONSTRAINT `pest_disease_treatments_crop_cycle_id_foreign` FOREIGN KEY (`crop_cycle_id`) REFERENCES `crop_cycles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `planting_schedules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `planting_schedules` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `crop_id` bigint unsigned NOT NULL,
  `crop_cycle_id` bigint unsigned DEFAULT NULL,
  `field_id` bigint unsigned DEFAULT NULL,
  `farm_id` bigint unsigned DEFAULT NULL,
  `planting_date` date NOT NULL,
  `planting_window_start` date DEFAULT NULL COMMENT 'Earliest recommended planting',
  `planting_window_end` date DEFAULT NULL COMMENT 'Latest recommended planting',
  `expected_harvest_date` date DEFAULT NULL,
  `actual_harvest_date` date DEFAULT NULL,
  `estimated_quantity` decimal(10,2) DEFAULT NULL COMMENT 'Estimated yield',
  `quantity_unit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'kg',
  `actual_quantity` decimal(10,2) DEFAULT NULL COMMENT 'Actual harvested',
  `actual_quantity_unit` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('planned','planted','growing','ready_for_harvest','harvested','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'planned',
  `season` enum('spring','summer','fall','winter','year_round') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'spring',
  `variety` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `completion_percentage` int NOT NULL DEFAULT '0',
  `current_stage` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `planting_schedules_crop_cycle_id_foreign` (`crop_cycle_id`),
  KEY `planting_schedules_user_id_index` (`user_id`),
  KEY `planting_schedules_field_id_status_index` (`field_id`,`status`),
  KEY `planting_schedules_farm_id_planting_date_index` (`farm_id`,`planting_date`),
  KEY `planting_schedules_crop_id_season_index` (`crop_id`,`season`),
  KEY `planting_schedules_status_index` (`status`),
  KEY `planting_schedules_planting_date_index` (`planting_date`),
  KEY `planting_schedules_expected_harvest_date_index` (`expected_harvest_date`),
  KEY `planting_schedules_crop_id_field_id_index` (`crop_id`,`field_id`),
  CONSTRAINT `planting_schedules_crop_cycle_id_foreign` FOREIGN KEY (`crop_cycle_id`) REFERENCES `crop_cycles` (`id`) ON DELETE SET NULL,
  CONSTRAINT `planting_schedules_crop_id_foreign` FOREIGN KEY (`crop_id`) REFERENCES `crops` (`id`) ON DELETE CASCADE,
  CONSTRAINT `planting_schedules_farm_id_foreign` FOREIGN KEY (`farm_id`) REFERENCES `farms` (`id`) ON DELETE CASCADE,
  CONSTRAINT `planting_schedules_field_id_foreign` FOREIGN KEY (`field_id`) REFERENCES `fields` (`id`) ON DELETE CASCADE,
  CONSTRAINT `planting_schedules_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reports` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `farm_id` bigint unsigned NOT NULL,
  `generated_by` bigint unsigned NOT NULL,
  `report_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `report_period_start` datetime NOT NULL,
  `report_period_end` datetime NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data` json DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reports_generated_by_foreign` (`generated_by`),
  KEY `reports_farm_id_report_type_index` (`farm_id`,`report_type`),
  CONSTRAINT `reports_farm_id_foreign` FOREIGN KEY (`farm_id`) REFERENCES `farms` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reports_generated_by_foreign` FOREIGN KEY (`generated_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `revenues`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `revenues` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `farm_id` bigint unsigned NOT NULL,
  `crop_id` bigint unsigned DEFAULT NULL,
  `buyer_id` bigint unsigned DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL,
  `sale_date` date NOT NULL,
  `quantity_sold` decimal(12,2) DEFAULT NULL,
  `unit` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price_per_unit` decimal(10,2) DEFAULT NULL,
  `payment_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `payment_date` date DEFAULT NULL,
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invoice_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `crop_cycle_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `revenues_crop_id_foreign` (`crop_id`),
  KEY `revenues_buyer_id_foreign` (`buyer_id`),
  KEY `revenues_farm_id_sale_date_index` (`farm_id`,`sale_date`),
  KEY `revenues_payment_status_index` (`payment_status`),
  KEY `revenues_crop_cycle_id_foreign` (`crop_cycle_id`),
  CONSTRAINT `revenues_buyer_id_foreign` FOREIGN KEY (`buyer_id`) REFERENCES `buyers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `revenues_crop_cycle_id_foreign` FOREIGN KEY (`crop_cycle_id`) REFERENCES `crop_cycles` (`id`) ON DELETE SET NULL,
  CONSTRAINT `revenues_crop_id_foreign` FOREIGN KEY (`crop_id`) REFERENCES `crops` (`id`) ON DELETE SET NULL,
  CONSTRAINT `revenues_farm_id_foreign` FOREIGN KEY (`farm_id`) REFERENCES `farms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sensor_readings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sensor_readings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sensor_id` bigint unsigned NOT NULL,
  `soil_moisture` decimal(5,2) DEFAULT NULL,
  `temperature` decimal(5,2) DEFAULT NULL,
  `humidity` decimal(5,2) DEFAULT NULL,
  `soil_ph` decimal(3,2) DEFAULT NULL,
  `light_intensity` decimal(10,2) DEFAULT NULL,
  `nitrogen_level` decimal(6,2) DEFAULT NULL,
  `phosphorus_level` decimal(6,2) DEFAULT NULL,
  `potassium_level` decimal(6,2) DEFAULT NULL,
  `rain_detected` tinyint(1) NOT NULL DEFAULT '0',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sensor_readings_sensor_id_foreign` (`sensor_id`),
  CONSTRAINT `sensor_readings_sensor_id_foreign` FOREIGN KEY (`sensor_id`) REFERENCES `sensors` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sensors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sensors` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `field_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `serial_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sensors_serial_number_unique` (`serial_number`),
  KEY `sensors_field_id_foreign` (`field_id`),
  CONSTRAINT `sensors_field_id_foreign` FOREIGN KEY (`field_id`) REFERENCES `fields` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
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
DROP TABLE IF EXISTS `staff`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `staff` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `farm_id` bigint unsigned NOT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `national_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('male','female','other') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general_worker',
  `daily_wage` decimal(10,2) NOT NULL DEFAULT '0.00',
  `payment_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'daily',
  `address` text COLLATE utf8mb4_unicode_ci,
  `emergency_contact` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emergency_phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hire_date` date NOT NULL,
  `termination_date` date DEFAULT NULL,
  `status` enum('active','inactive','terminated') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `available_equipment` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `staff_farm_id_status_index` (`farm_id`,`status`),
  KEY `staff_national_id_index` (`national_id`),
  CONSTRAINT `staff_farm_id_foreign` FOREIGN KEY (`farm_id`) REFERENCES `farms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `staff_attendances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `staff_attendances` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `staff_id` bigint unsigned NOT NULL,
  `date` date NOT NULL,
  `clock_in` time DEFAULT NULL,
  `clock_out` time DEFAULT NULL,
  `hours_worked` decimal(5,2) NOT NULL DEFAULT '0.00',
  `status` enum('present','absent','late','half_day','on_leave') COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `staff_attendances_staff_id_date_unique` (`staff_id`,`date`),
  KEY `staff_attendances_staff_id_date_index` (`staff_id`,`date`),
  CONSTRAINT `staff_attendances_staff_id_foreign` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `staff_wages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `staff_wages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `staff_id` bigint unsigned NOT NULL,
  `farm_id` bigint unsigned NOT NULL,
  `period_start` date NOT NULL,
  `period_end` date NOT NULL,
  `days_worked` int NOT NULL,
  `hours_worked` decimal(8,2) NOT NULL DEFAULT '0.00',
  `daily_rate` decimal(10,2) NOT NULL DEFAULT '0.00',
  `gross_wage` decimal(10,2) NOT NULL DEFAULT '0.00',
  `deductions` decimal(10,2) NOT NULL DEFAULT '0.00',
  `net_wage` decimal(10,2) NOT NULL DEFAULT '0.00',
  `status` enum('pending','approved','paid','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `payment_date` date DEFAULT NULL,
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `staff_wages_farm_id_foreign` (`farm_id`),
  KEY `staff_wages_staff_id_period_start_period_end_index` (`staff_id`,`period_start`,`period_end`),
  KEY `staff_wages_status_index` (`status`),
  CONSTRAINT `staff_wages_farm_id_foreign` FOREIGN KEY (`farm_id`) REFERENCES `farms` (`id`) ON DELETE CASCADE,
  CONSTRAINT `staff_wages_staff_id_foreign` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `suppliers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `suppliers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_person` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `suppliers_user_id_foreign` (`user_id`),
  CONSTRAINT `suppliers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tasks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `field_id` bigint unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `task_type` enum('planting','harvesting','irrigation','fertilizing','pesticide','maintenance','inspection','other') COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','in_progress','completed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `scheduled_date` date NOT NULL,
  `completed_date` date DEFAULT NULL,
  `assigned_to` bigint unsigned DEFAULT NULL,
  `estimated_cost` decimal(10,2) DEFAULT NULL,
  `actual_cost` decimal(10,2) DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `crop_id` bigint unsigned DEFAULT NULL,
  `priority` enum('low','normal','high','urgent') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'normal',
  `estimated_hours` decimal(5,2) DEFAULT NULL,
  `actual_hours` decimal(5,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tasks_field_id_foreign` (`field_id`),
  KEY `tasks_assigned_to_foreign` (`assigned_to`),
  KEY `tasks_crop_id_foreign` (`crop_id`),
  CONSTRAINT `tasks_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tasks_crop_id_foreign` FOREIGN KEY (`crop_id`) REFERENCES `crops` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tasks_field_id_foreign` FOREIGN KEY (`field_id`) REFERENCES `fields` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `preferred_language` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT 'en',
  `notification_preferences` json DEFAULT NULL,
  `weather_alerts` tinyint(1) NOT NULL DEFAULT '0',
  `ai_recommendations` tinyint(1) NOT NULL DEFAULT '1',
  `role` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('active','inactive','suspended') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `last_seen_at` timestamp NULL DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `weather_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `weather_data` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `farm_id` bigint unsigned NOT NULL,
  `recorded_at` datetime NOT NULL,
  `temperature` decimal(5,2) DEFAULT NULL,
  `humidity` decimal(5,2) DEFAULT NULL,
  `precipitation` decimal(6,2) DEFAULT NULL,
  `wind_speed` decimal(6,2) DEFAULT NULL,
  `wind_direction` decimal(5,2) DEFAULT NULL,
  `pressure` decimal(7,2) DEFAULT NULL,
  `uv_index` decimal(4,2) DEFAULT NULL,
  `cloud_cover` decimal(5,2) DEFAULT NULL,
  `dew_point` decimal(5,2) DEFAULT NULL,
  `weather_condition` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `feels_like` decimal(5,2) DEFAULT NULL,
  `visibility` decimal(6,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `crop_cycle_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `weather_data_farm_id_recorded_at_index` (`farm_id`,`recorded_at`),
  KEY `weather_data_crop_cycle_id_foreign` (`crop_cycle_id`),
  CONSTRAINT `weather_data_crop_cycle_id_foreign` FOREIGN KEY (`crop_cycle_id`) REFERENCES `crop_cycles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `weather_data_farm_id_foreign` FOREIGN KEY (`farm_id`) REFERENCES `farms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `yield_estimations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `yield_estimations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `crop_id` bigint unsigned NOT NULL,
  `farmer_id` bigint unsigned NOT NULL,
  `field_id` bigint unsigned DEFAULT NULL,
  `hectares` decimal(10,2) DEFAULT NULL,
  `season` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `year` int NOT NULL,
  `estimated_yield` decimal(10,2) DEFAULT NULL,
  `actual_yield` decimal(10,2) DEFAULT NULL,
  `yield_per_hectare` decimal(10,2) DEFAULT NULL,
  `yield_unit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'kg',
  `estimated_income` decimal(12,2) DEFAULT NULL,
  `actual_income` decimal(12,2) DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `status` enum('planned','in_progress','harvested','failed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'planned',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `yield_estimations_field_id_foreign` (`field_id`),
  KEY `yield_estimations_farmer_id_index` (`farmer_id`),
  KEY `yield_estimations_crop_id_season_index` (`crop_id`,`season`),
  KEY `yield_estimations_farmer_id_year_index` (`farmer_id`,`year`),
  CONSTRAINT `yield_estimations_crop_id_foreign` FOREIGN KEY (`crop_id`) REFERENCES `crops` (`id`) ON DELETE CASCADE,
  CONSTRAINT `yield_estimations_farmer_id_foreign` FOREIGN KEY (`farmer_id`) REFERENCES `farmers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `yield_estimations_field_id_foreign` FOREIGN KEY (`field_id`) REFERENCES `fields` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (1,'0001_01_01_000000_create_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (2,'0001_01_01_000001_create_cache_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (3,'0001_01_01_000002_create_jobs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (4,'2024_01_01_000001_create_farms_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (5,'2024_01_01_000002_create_fields_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (6,'2024_01_01_000003_add_role_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (7,'2024_01_01_000003_create_crops_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (8,'2024_01_01_000004_create_sensors_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (9,'2024_01_01_000005_create_sensor_readings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (10,'2024_01_01_000006_add_light_rain_to_sensor_readings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (11,'2024_01_01_000006_create_alerts_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (12,'2024_01_01_000007_create_automation_rules_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (13,'2024_01_01_000008_create_crop_analyses_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (14,'2024_01_01_000009_create_tasks_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (15,'2024_01_01_000010_create_irrigation_zones_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (16,'2024_01_01_000011_create_weather_data_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (17,'2024_01_01_000012_create_notifications_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (18,'2024_01_01_000013_create_irrigation_logs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (19,'2024_01_01_000014_create_reports_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (20,'2024_01_01_000015_add_columns_to_tasks_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (21,'2024_01_01_000016_add_columns_to_irrigation_zones_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (22,'2024_01_01_000017_create_farmers_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (23,'2024_01_01_000018_create_farmer_documents_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (24,'2024_01_01_000019_create_crop_history_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (25,'2024_01_01_000021_create_crop_seasons_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (26,'2024_01_01_000022_create_crop_rotations_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (27,'2024_01_01_000023_create_yield_estimations_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (28,'2026_04_18_174916_create_personal_access_tokens_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (29,'2026_04_19_131041_add_user_id_to_fields_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (30,'2026_04_20_000001_update_schedule_type_enum',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (31,'2026_04_20_081053_add_field_id_to_crops_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (32,'2026_04_20_081222_add_cultivation_fields_to_crops_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (33,'2026_04_20_081700_add_user_id_to_crops_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (34,'2026_04_20_083701_add_soil_type_and_gps_to_fields_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (35,'2026_04_20_120001_create_staff_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (36,'2026_04_20_120002_create_staff_attendances_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (37,'2026_04_20_120003_create_staff_wages_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (38,'2026_04_20_120004_create_harvests_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (39,'2026_04_20_130001_create_expenses_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (40,'2026_04_20_130002_create_buyers_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (41,'2026_04_20_130003_create_revenues_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (42,'2026_04_20_130003_make_buyers_credit_limit_nullable',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (43,'2026_04_20_130004_create_orders_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (44,'2026_04_20_140001_add_npk_to_sensor_readings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (45,'2026_04_20_180001_create_livestock_types_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (46,'2026_04_20_180002_create_livestock_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (47,'2026_04_20_180003_create_feed_types_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (48,'2026_04_20_180004_create_food_stocks_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (49,'2026_04_20_180005_create_diseases_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (50,'2026_04_20_180006_create_livestock_diseases_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (51,'2026_04_22_120000_fix_crops_unique_constraint',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (52,'2026_04_23_103202_add_missing_livestock_disease_fields',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (53,'2026_04_23_120000_create_livestock_analyses_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (54,'2026_04_23_122044_update_user_roles_for_filament',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (55,'2026_04_24_110615_create_livestock_wound_analyses_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (56,'2026_04_24_130647_create_suppliers_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (57,'2026_04_24_130828_add_foreign_key_to_food_stocks_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (58,'2026_04_24_164537_create_permission_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (59,'2026_04_28_000000_create_livestock_locations_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (60,'2026_04_28_000100_create_livestock_movements_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (61,'2026_04_28_000200_add_gps_to_farms_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (62,'2026_04_28_000300_drop_disease_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (63,'2026_04_28_090352_add_tracking_id_to_livestock_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (64,'2026_04_28_091255_create_crop_cycles_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (65,'2026_04_28_101022_create_fertilizer_types_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (66,'2026_04_28_101111_create_fertilizers_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (67,'2026_04_28_101145_create_fertilizer_applications_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (68,'2026_04_28_101219_create_feed_usages_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (69,'2026_04_28_151621_create_crop_stages_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (70,'2026_04_28_151642_create_activities_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (71,'2026_04_28_151727_create_inputs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (72,'2026_04_30_000002_add_farm_type_and_ownership_type_to_farms_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (73,'2026_04_30_000003_add_gps_coordinates_to_farms_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (74,'2026_04_30_000004_add_farm_operation_details_to_farms_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (75,'2026_04_30_064235_add_indexes_to_alerts_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (76,'2026_04_30_064842_add_expiry_date_index_to_food_stocks_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (77,'2026_04_30_101603_add_environmental_fields_to_fields_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (78,'2026_04_30_101700_add_management_details_to_fields_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (79,'2026_04_30_122545_create_planting_schedules_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (80,'2026_04_30_132101_add_crop_cycle_id_to_crop_analyses_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (81,'2026_04_30_132143_create_crop_analysis_images_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (82,'2026_04_30_180000_add_crop_management_enhancements',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (83,'2026_05_04_073643_add_subcounty_and_physical_address_to_farms_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (84,'2026_05_04_083226_add_available_equipment_to_staff_table',3);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (85,'2026_05_04_090625_add_storage_facilities_to_farms_table',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (86,'2026_05_04_094722_add_financial_operational_fields_to_farms_table',5);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (87,'2026_05_04_100036_add_user_preferences_to_users_table',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (88,'2026_05_04_102039_add_images_and_documents_to_farms_table',7);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (89,'2026_05_04_103558_create_farm_images_table',7);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (90,'2026_05_04_103559_create_farm_documents_table',7);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (91,'2026_05_04_104448_add_main_image_to_farms_table',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (92,'2026_05_04_141150_add_status_to_users_table',9);
