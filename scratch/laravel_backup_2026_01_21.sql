-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: laravel
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
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
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
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
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
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `group_key` varchar(30) DEFAULT NULL,
  `unit_type` varchar(255) NOT NULL DEFAULT 'unit',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `parent_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `categories_parent_id_foreign` (`parent_id`),
  CONSTRAINT `categories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (11,'Covered Foam','foam','unit','2026-01-01 12:11:12','2026-01-07 11:28:55',NULL),(12,'Foam Mattressess','foam','unit','2026-01-01 12:11:32','2026-01-07 11:29:15',11),(13,'Spring mattressess','foam','unit','2026-01-01 12:12:33','2026-01-07 11:30:30',NULL),(14,'uncoverd Foam','foam','unit','2026-01-01 15:19:53','2026-01-07 11:30:58',NULL),(15,'jumbolon coverd','foam','unit','2026-01-01 15:20:15','2026-01-07 11:29:35',NULL),(16,'jumbolon Uncoverd','foam','unit','2026-01-01 15:20:35','2026-01-07 11:29:43',NULL),(17,'misc','foam','unit','2026-01-01 15:29:31','2026-01-07 11:30:17',NULL),(18,'Lamination','hardware','sqft','2026-01-03 10:39:15','2026-01-07 11:29:50',19),(19,'Hardware','hardware','unit','2026-01-03 10:39:58','2026-01-07 11:32:29',NULL),(20,'Lasani','hardware','sqft','2026-01-03 10:51:13','2026-01-07 11:29:59',19),(21,'Chip Board','hardware','sqft','2026-01-03 10:51:33','2026-01-07 11:28:39',19),(22,'Comercial','hardware','sqft','2026-01-03 10:51:52','2026-01-07 11:28:48',19),(23,'Shesham','hardware','sqft','2026-01-03 10:54:41','2026-01-07 11:30:08',19);
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `companies`
--

DROP TABLE IF EXISTS `companies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `companies` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `group_key` varchar(40) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `phone_main` varchar(40) DEFAULT NULL,
  `email` varchar(120) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `note` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `companies_group_key_index` (`group_key`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `companies`
--

LOCK TABLES `companies` WRITE;
/*!40000 ALTER TABLE `companies` DISABLE KEYS */;
INSERT INTO `companies` VALUES (1,'Master Foam','hardware',1,'2026-01-01 15:15:01','2026-01-07 12:20:22',NULL,NULL,NULL,NULL),(2,'Dura Foam','foam',1,'2026-01-01 15:21:58','2026-01-07 12:19:25',NULL,NULL,NULL,NULL),(3,'Diamond Foam','foam',1,'2026-01-01 15:22:10','2026-01-07 12:19:34',NULL,NULL,NULL,NULL),(4,'Citi Foam','foam',1,'2026-01-01 15:22:19','2026-01-07 12:19:41',NULL,NULL,NULL,NULL),(5,'Style Foam','foam',1,'2026-01-01 15:22:29','2026-01-07 12:19:50',NULL,NULL,NULL,NULL),(6,'KMI lamination','hardware',1,'2026-01-03 10:36:57','2026-01-07 12:20:54',NULL,NULL,NULL,NULL),(7,'AL-Noor lamination','hardware',1,'2026-01-03 10:37:08','2026-01-07 12:20:29',NULL,NULL,NULL,NULL),(8,'Shaheen Board','hardware',1,'2026-01-03 10:38:34','2026-01-07 12:21:03',NULL,NULL,NULL,NULL),(9,'ZRK Lamination','hardware',1,'2026-01-03 10:38:48','2026-01-07 12:21:10',NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `companies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `company_contacts`
--

DROP TABLE IF EXISTS `company_contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `company_contacts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint(20) unsigned NOT NULL,
  `name` varchar(120) NOT NULL,
  `role_title` varchar(120) DEFAULT NULL,
  `phone_primary` varchar(40) DEFAULT NULL,
  `phone_alt` varchar(40) DEFAULT NULL,
  `email` varchar(120) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `reports_to_contact_id` bigint(20) unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(10) unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `company_contacts_reports_to_contact_id_foreign` (`reports_to_contact_id`),
  KEY `company_contacts_company_id_is_active_index` (`company_id`,`is_active`),
  KEY `company_contacts_company_id_sort_order_index` (`company_id`,`sort_order`),
  CONSTRAINT `company_contacts_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `company_contacts_reports_to_contact_id_foreign` FOREIGN KEY (`reports_to_contact_id`) REFERENCES `company_contacts` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `company_contacts`
--

LOCK TABLES `company_contacts` WRITE;
/*!40000 ALTER TABLE `company_contacts` DISABLE KEYS */;
/*!40000 ALTER TABLE `company_contacts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `company_groups`
--

DROP TABLE IF EXISTS `company_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `company_groups` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(40) NOT NULL,
  `name` varchar(80) NOT NULL,
  `sort_order` int(10) unsigned NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `company_groups_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `company_groups`
--

LOCK TABLES `company_groups` WRITE;
/*!40000 ALTER TABLE `company_groups` DISABLE KEYS */;
INSERT INTO `company_groups` VALUES (1,'foam','Foam',1,1,'2026-01-07 12:49:10','2026-01-07 12:49:10'),(2,'hardware','Hardware',2,1,'2026-01-07 12:49:10','2026-01-07 12:49:10'),(3,'fabric','Fabric',3,1,'2026-01-07 12:49:10','2026-01-07 12:49:10'),(4,'spring','Spring',4,1,'2026-01-07 12:49:10','2026-01-07 12:49:10'),(5,'accessories','Accessories',5,1,'2026-01-07 12:49:10','2026-01-07 12:49:10'),(6,'other','Other',6,1,'2026-01-07 12:49:10','2026-01-07 12:49:10');
/*!40000 ALTER TABLE `company_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `company_ledger_entries`
--

DROP TABLE IF EXISTS `company_ledger_entries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `company_ledger_entries` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint(20) unsigned NOT NULL,
  `entry_date` date NOT NULL,
  `entry_type` varchar(40) NOT NULL DEFAULT 'adjustment',
  `direction` enum('debit','credit') NOT NULL DEFAULT 'debit',
  `amount` decimal(14,2) NOT NULL DEFAULT 0.00,
  `description` varchar(255) DEFAULT NULL,
  `ref_type` varchar(40) DEFAULT NULL,
  `ref_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `company_ledger_entries_user_id_foreign` (`user_id`),
  KEY `company_ledger_entries_company_id_entry_date_index` (`company_id`,`entry_date`),
  KEY `company_ledger_entries_company_id_entry_type_index` (`company_id`,`entry_type`),
  KEY `company_ledger_entries_ref_type_ref_id_index` (`ref_type`,`ref_id`),
  CONSTRAINT `company_ledger_entries_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `company_ledger_entries_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `company_ledger_entries`
--

LOCK TABLES `company_ledger_entries` WRITE;
/*!40000 ALTER TABLE `company_ledger_entries` DISABLE KEYS */;
/*!40000 ALTER TABLE `company_ledger_entries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `company_order_items`
--

DROP TABLE IF EXISTS `company_order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `company_order_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_order_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `qty` decimal(12,3) NOT NULL DEFAULT 0.000,
  `unit_cost` decimal(12,2) NOT NULL DEFAULT 0.00,
  `line_total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `company_order_items_company_order_id_foreign` (`company_order_id`),
  KEY `company_order_items_product_id_foreign` (`product_id`),
  CONSTRAINT `company_order_items_company_order_id_foreign` FOREIGN KEY (`company_order_id`) REFERENCES `company_orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `company_order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `company_order_items`
--

LOCK TABLES `company_order_items` WRITE;
/*!40000 ALTER TABLE `company_order_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `company_order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `company_orders`
--

DROP TABLE IF EXISTS `company_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `company_orders` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint(20) unsigned NOT NULL,
  `order_date` date NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'open',
  `goods_total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `note` text DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `received_purchase_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `company_orders_company_id_foreign` (`company_id`),
  KEY `company_orders_created_by_foreign` (`created_by`),
  KEY `company_orders_received_purchase_id_foreign` (`received_purchase_id`),
  CONSTRAINT `company_orders_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `company_orders_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `company_orders_received_purchase_id_foreign` FOREIGN KEY (`received_purchase_id`) REFERENCES `purchases` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `company_orders`
--

LOCK TABLES `company_orders` WRITE;
/*!40000 ALTER TABLE `company_orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `company_orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `phone_primary` varchar(30) DEFAULT NULL,
  `phone_alt_1` varchar(30) DEFAULT NULL,
  `phone_alt_2` varchar(30) DEFAULT NULL,
  `phone` varchar(30) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `cnic` varchar(30) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `customers_phone_unique` (`phone`),
  KEY `customers_phone_index` (`phone`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customers`
--

LOCK TABLES `customers` WRITE;
/*!40000 ALTER TABLE `customers` DISABLE KEYS */;
INSERT INTO `customers` VALUES (1,NULL,NULL,NULL,'03126233326','ali masood',NULL,NULL,'2026-01-03 14:32:27','2026-01-03 14:32:27'),(2,NULL,NULL,NULL,'03006806503','sami urf Qureshi',NULL,NULL,'2026-01-05 00:45:20','2026-01-05 00:45:20'),(3,'03127817811',NULL,NULL,'03127817811','imran','shadman colony street 4b ahmed pur east',NULL,'2026-01-11 09:34:16','2026-01-11 09:34:16'),(4,'03117817811',NULL,NULL,'03117817811','ibtaisam',NULL,NULL,'2026-01-14 05:36:15','2026-01-14 05:36:15');
/*!40000 ALTER TABLE `customers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `discount_rules`
--

DROP TABLE IF EXISTS `discount_rules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `discount_rules` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `scope_type` varchar(255) NOT NULL,
  `scope_id` bigint(20) unsigned NOT NULL,
  `discount_type_id` bigint(20) unsigned NOT NULL,
  `rule_type` varchar(255) NOT NULL DEFAULT 'percent_once',
  `percent_1` decimal(8,3) DEFAULT NULL,
  `percent_2` decimal(8,3) DEFAULT NULL,
  `fixed_purchase_price` decimal(12,2) DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_rule_per_scope_type` (`scope_type`,`scope_id`,`discount_type_id`),
  KEY `discount_rules_discount_type_id_foreign` (`discount_type_id`),
  KEY `discount_rules_scope_type_scope_id_index` (`scope_type`,`scope_id`),
  CONSTRAINT `discount_rules_discount_type_id_foreign` FOREIGN KEY (`discount_type_id`) REFERENCES `discount_types` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `discount_rules`
--

LOCK TABLES `discount_rules` WRITE;
/*!40000 ALTER TABLE `discount_rules` DISABLE KEYS */;
INSERT INTO `discount_rules` VALUES (2,'company',1,10,'percent_once',12.500,NULL,NULL,NULL,'2026-01-01 15:26:35','2026-01-01 15:26:35'),(3,'company',1,11,'percent_once',14.500,NULL,NULL,NULL,'2026-01-01 15:27:07','2026-01-01 15:27:07'),(4,'company',1,12,'percent_once',12.500,NULL,NULL,NULL,'2026-01-01 15:27:40','2026-01-01 15:27:40'),(5,'company',2,10,'percent_once',17.500,NULL,NULL,NULL,'2026-01-01 15:28:05','2026-01-01 15:28:05'),(6,'company',2,12,'percent_once',17.500,NULL,NULL,NULL,'2026-01-01 15:28:30','2026-01-01 15:28:30'),(7,'company',2,11,'percent_once',17.500,NULL,NULL,NULL,'2026-01-01 15:28:54','2026-01-01 15:28:54'),(8,'company',1,17,'percent_once',10.000,NULL,NULL,NULL,'2026-01-01 15:29:57','2026-01-01 15:29:57'),(9,'company',5,13,'percent_twostep',25.000,5.000,NULL,NULL,'2026-01-01 15:30:22','2026-01-02 07:57:13');
/*!40000 ALTER TABLE `discount_rules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `discount_types`
--

DROP TABLE IF EXISTS `discount_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `discount_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `discount_types_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `discount_types`
--

LOCK TABLES `discount_types` WRITE;
/*!40000 ALTER TABLE `discount_types` DISABLE KEYS */;
INSERT INTO `discount_types` VALUES (10,'coverd',1,'2026-01-01 15:23:41','2026-01-01 15:23:41'),(11,'uncoverd',1,'2026-01-01 15:23:55','2026-01-01 15:23:55'),(12,'spring',1,'2026-01-01 15:24:05','2026-01-01 15:24:05'),(13,'jumboloncoverd',1,'2026-01-01 15:24:35','2026-01-01 15:24:35'),(14,'jumbolonuncoverd',1,'2026-01-01 15:24:49','2026-01-01 15:24:49'),(15,'hardware',1,'2026-01-01 15:24:59','2026-01-01 15:24:59'),(16,'fabric',1,'2026-01-01 15:25:07','2026-01-01 15:25:07'),(17,'misc',1,'2026-01-01 15:25:22','2026-01-01 15:25:22'),(18,'accessories',1,'2026-01-01 15:25:33','2026-01-01 16:58:11'),(19,'shell',1,'2026-01-01 18:20:46','2026-01-01 18:20:46');
/*!40000 ALTER TABLE `discount_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `expenses`
--

DROP TABLE IF EXISTS `expenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `expenses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `expense_date` date NOT NULL,
  `title` varchar(160) NOT NULL,
  `category` varchar(80) DEFAULT NULL,
  `vendor` varchar(160) DEFAULT NULL,
  `amount` decimal(14,2) NOT NULL DEFAULT 0.00,
  `payment_method` varchar(40) DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `expenses_user_id_foreign` (`user_id`),
  KEY `expenses_expense_date_index` (`expense_date`),
  KEY `expenses_category_index` (`category`),
  CONSTRAINT `expenses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `expenses`
--

LOCK TABLES `expenses` WRITE;
/*!40000 ALTER TABLE `expenses` DISABLE KEYS */;
/*!40000 ALTER TABLE `expenses` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
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
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
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
-- Table structure for table `leftover_pieces`
--

DROP TABLE IF EXISTS `leftover_pieces`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `leftover_pieces` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) unsigned NOT NULL,
  `width_ft` decimal(10,4) NOT NULL DEFAULT 0.0000,
  `length_ft` decimal(10,4) NOT NULL DEFAULT 0.0000,
  `qty` decimal(10,4) NOT NULL DEFAULT 1.0000,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `note` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `leftover_pieces_product_id_index` (`product_id`),
  CONSTRAINT `leftover_pieces_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leftover_pieces`
--

LOCK TABLES `leftover_pieces` WRITE;
/*!40000 ALTER TABLE `leftover_pieces` DISABLE KEYS */;
/*!40000 ALTER TABLE `leftover_pieces` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_01_01_134753_create_shops_table',1),(5,'2026_01_01_134754_create_categories_table',1),(6,'2026_01_01_134754_create_companies_table',1),(7,'2026_01_01_134755_create_product_variants_table',1),(8,'2026_01_01_134755_create_products_table',1),(9,'2026_01_01_163849_add_parent_and_unit_to_categories_table',2),(10,'2026_01_01_165718_add_parent_and_unit_to_categories_table',3),(11,'2026_01_01_173832_create_companies_table',4),(12,'2026_01_01_184624_create_discount_types_table',4),(13,'2026_01_01_184659_create_discount_rules_table',4),(14,'2026_01_01_184744_add_pricing_fields_to_products_table',4),(15,'2026_01_01_194318_add_fields_to_companies_table',5),(16,'2026_01_01_204909_add_shell_rate_fields_to_products_table',6),(17,'2026_01_01_220320_add_core_fields_to_products_table',7),(18,'2026_01_03_000000_create_stock_movements_table',8),(19,'2026_01_03_000001_create_sales_table',9),(20,'2026_01_03_000002_create_sale_items_table',9),(21,'2026_01_03_000003_create_sale_payments_table',10),(22,'2026_01_03_000004_add_udhaar_fields_to_sales_table',11),(23,'2026_01_03_161009_add_ref_fields_to_stock_movements_table',12),(24,'2026_01_03_000010_create_customers_table',13),(25,'2026_01_03_000011_add_customer_and_profit_to_sales_table',14),(26,'2026_01_03_000012_create_sale_payments_table',15),(27,'2026_01_04_000000_create_payments_table',15),(28,'2026_01_04_000000_create_customers_table',16),(29,'2026_01_04_000001_add_customer_fk_to_sales_table',17),(30,'2026_01_04_000002_fix_customers_table',17),(31,'2026_01_04_000003_add_customer_fk_to_sales_table_safe',17),(32,'2026_01_04_180518_add_customer_id_to_sale_payments_table',18),(33,'2026_01_04_181142_add_payment_ref_to_sale_payments_table',19),(34,'2026_01_06_000001_add_sheet_fields_to_products_table',20),(35,'2026_01_06_130000_add_customer_extra_fields_to_customers_and_sales',20),(36,'2026_01_07_000001_add_group_key_to_categories_table',21),(37,'2026_01_07_000001_add_group_key_to_companies_table',22),(38,'2026_01_07_000002_create_company_groups_table',23),(39,'2026_01_08_065126_add_role_to_users_table',24),(40,'2026_01_10_152726_add_role_to_users_table',25),(41,'2026_01_11_160000_add_sheet_fields_to_products_table',26),(42,'2026_01_14_000001_create_leftover_pieces_table',27),(43,'2026_01_03_000005_add_customer_id_and_profit_to_sales_table',28),(44,'2026_01_20_000001_add_contact_fields_to_companies_table',28),(45,'2026_01_20_000002_create_company_contacts_table',28),(46,'2026_01_20_000010_create_company_ledger_entries_table',28),(47,'2026_01_20_000011_create_purchases_tables',28),(48,'2026_01_20_000012_create_expenses_and_recurring_expenses_tables',28),(49,'2026_01_20_000020_create_company_orders_tables',28);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
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
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sale_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL,
  `note` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payments_user_id_foreign` (`user_id`),
  KEY `payments_sale_id_created_at_index` (`sale_id`,`created_at`),
  CONSTRAINT `payments_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
INSERT INTO `payments` VALUES (1,9,1,10000.00,NULL,'2026-01-04 11:10:56','2026-01-04 11:10:56'),(2,21,1,18100.00,NULL,'2026-01-05 04:38:46','2026-01-05 04:38:46'),(3,24,1,600.00,NULL,'2026-01-05 05:11:51','2026-01-05 05:11:51');
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_variants`
--

DROP TABLE IF EXISTS `product_variants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_variants` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_variants`
--

LOCK TABLES `product_variants` WRITE;
/*!40000 ALTER TABLE `product_variants` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_variants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `products` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `pricing_mode` varchar(255) NOT NULL DEFAULT 'discount',
  `shell_rate` decimal(10,2) DEFAULT NULL,
  `width_in` decimal(10,2) DEFAULT NULL,
  `length_in` decimal(10,2) DEFAULT NULL,
  `height_in` decimal(10,2) DEFAULT NULL,
  `sheet_full_w` decimal(10,2) DEFAULT NULL,
  `sheet_full_l` decimal(10,2) DEFAULT NULL,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  `category_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `is_variant_parent` tinyint(1) NOT NULL DEFAULT 0,
  `mrp` decimal(12,2) DEFAULT NULL,
  `purchase_price_manual` decimal(12,2) DEFAULT NULL,
  `sale_price_default` decimal(12,2) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `stock_qty` decimal(12,2) NOT NULL DEFAULT 0.00,
  `sku` varchar(255) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `selling_price_default` decimal(12,2) DEFAULT NULL,
  `is_split_parent` tinyint(1) NOT NULL DEFAULT 0,
  `split_total_parts` int(10) unsigned DEFAULT NULL,
  `low_stock_alert_qty` int(11) NOT NULL DEFAULT 5,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `pricing_source` varchar(255) NOT NULL DEFAULT 'manual',
  `discount_type_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `products_company_id_foreign` (`company_id`),
  KEY `products_category_id_foreign` (`category_id`),
  KEY `products_discount_type_id_foreign` (`discount_type_id`),
  CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  CONSTRAINT `products_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  CONSTRAINT `products_discount_type_id_foreign` FOREIGN KEY (`discount_type_id`) REFERENCES `discount_types` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,'manual',NULL,NULL,NULL,NULL,NULL,NULL,1,11,'molty',0,33000.00,NULL,33000.00,1,9.00,NULL,NULL,33000.00,0,NULL,5,'2026-01-01 17:13:44','2026-01-03 10:21:39','discount_rule',10),(2,'shell',27.50,22.00,22.00,3.50,NULL,NULL,5,16,'jumbolon Uncoverd',0,400.00,NULL,NULL,1,115.00,NULL,NULL,400.00,0,NULL,20,'2026-01-02 08:03:02','2026-01-11 08:34:01','manual',NULL),(3,'manual',NULL,NULL,NULL,NULL,NULL,NULL,2,11,'Dura Luxry',0,18100.00,NULL,NULL,1,20.00,NULL,NULL,18100.00,0,NULL,4,'2026-01-03 02:21:49','2026-01-06 03:29:20','discount_rule',10),(4,'manual',NULL,48.00,96.00,NULL,NULL,NULL,9,18,'Lamination',0,3600.00,3450.00,NULL,1,45.00,'1',NULL,3600.00,1,8,0,'2026-01-03 10:58:28','2026-01-14 05:37:40','manual',NULL),(5,'manual',NULL,48.00,96.00,NULL,4.00,8.00,9,20,'lasnani 3/4',0,3300.00,3200.00,NULL,1,48.00,NULL,NULL,3300.00,1,8,20,'2026-01-05 05:09:09','2026-01-07 03:51:49','manual',15),(6,'manual',NULL,48.00,96.00,NULL,4.00,8.00,7,19,'shesham',0,3300.00,3200.00,NULL,1,99.00,NULL,NULL,3300.00,1,8,20,'2026-01-05 06:56:28','2026-01-11 09:58:05','manual',15),(7,'manual',NULL,36.00,72.00,1.50,NULL,NULL,1,14,'molty 1.5',0,2720.00,NULL,NULL,1,17.50,NULL,NULL,2720.00,1,6,5,'2026-01-07 03:58:22','2026-01-21 08:08:45','manual',11);
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchase_items`
--

DROP TABLE IF EXISTS `purchase_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `purchase_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `purchase_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `qty` decimal(12,2) NOT NULL DEFAULT 0.00,
  `unit_cost` decimal(14,2) NOT NULL DEFAULT 0.00,
  `line_total` decimal(14,2) NOT NULL DEFAULT 0.00,
  `note` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_items_product_id_foreign` (`product_id`),
  KEY `purchase_items_purchase_id_product_id_index` (`purchase_id`,`product_id`),
  CONSTRAINT `purchase_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `purchase_items_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_items`
--

LOCK TABLES `purchase_items` WRITE;
/*!40000 ALTER TABLE `purchase_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `purchase_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchases`
--

DROP TABLE IF EXISTS `purchases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `purchases` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  `supplier_name` varchar(255) DEFAULT NULL,
  `invoice_no` varchar(80) DEFAULT NULL,
  `purchase_date` date NOT NULL,
  `goods_total` decimal(14,2) NOT NULL DEFAULT 0.00,
  `transport_charges` decimal(14,2) NOT NULL DEFAULT 0.00,
  `payment_made` decimal(14,2) NOT NULL DEFAULT 0.00,
  `note` text DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchases_user_id_foreign` (`user_id`),
  KEY `purchases_company_id_purchase_date_index` (`company_id`,`purchase_date`),
  CONSTRAINT `purchases_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL,
  CONSTRAINT `purchases_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchases`
--

LOCK TABLES `purchases` WRITE;
/*!40000 ALTER TABLE `purchases` DISABLE KEYS */;
/*!40000 ALTER TABLE `purchases` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `recurring_expenses`
--

DROP TABLE IF EXISTS `recurring_expenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `recurring_expenses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(160) NOT NULL,
  `category` varchar(80) DEFAULT NULL,
  `vendor` varchar(160) DEFAULT NULL,
  `amount` decimal(14,2) NOT NULL DEFAULT 0.00,
  `day_of_month` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_generated_month` varchar(7) DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `recurring_expenses_user_id_foreign` (`user_id`),
  KEY `recurring_expenses_is_active_day_of_month_index` (`is_active`,`day_of_month`),
  CONSTRAINT `recurring_expenses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recurring_expenses`
--

LOCK TABLES `recurring_expenses` WRITE;
/*!40000 ALTER TABLE `recurring_expenses` DISABLE KEYS */;
/*!40000 ALTER TABLE `recurring_expenses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sale_items`
--

DROP TABLE IF EXISTS `sale_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sale_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sale_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned DEFAULT NULL,
  `product_name` varchar(255) NOT NULL,
  `qty` decimal(12,2) NOT NULL DEFAULT 0.00,
  `price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `purchase_price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `line_total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sale_items_product_id_foreign` (`product_id`),
  KEY `sale_items_sale_id_index` (`sale_id`),
  CONSTRAINT `sale_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  CONSTRAINT `sale_items_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sale_items`
--

LOCK TABLES `sale_items` WRITE;
/*!40000 ALTER TABLE `sale_items` DISABLE KEYS */;
INSERT INTO `sale_items` VALUES (1,1,3,'Dura Luxry',1.00,18100.00,14932.50,18100.00,'2026-01-03 08:50:47','2026-01-03 08:50:47'),(2,2,3,'Dura Luxry',1.00,18100.00,14932.50,18100.00,'2026-01-03 10:17:50','2026-01-03 10:17:50'),(3,3,3,'Dura Luxry',1.00,18100.00,14932.50,18100.00,'2026-01-03 10:18:00','2026-01-03 10:18:00'),(4,4,3,'Dura Luxry',1.00,18100.00,14932.50,18100.00,'2026-01-03 10:21:39','2026-01-03 10:21:39'),(5,4,2,'jumbolon Uncoverd',1.00,400.00,323.51,400.00,'2026-01-03 10:21:39','2026-01-03 10:21:39'),(6,4,1,'molty',1.00,33000.00,28875.00,33000.00,'2026-01-03 10:21:39','2026-01-03 10:21:39'),(12,8,3,'Dura Luxry',1.00,18100.00,14932.50,18100.00,'2026-01-04 11:02:38','2026-01-04 11:02:38'),(13,9,3,'Dura Luxry',1.00,18100.00,14932.50,18100.00,'2026-01-04 11:07:12','2026-01-04 11:07:12'),(14,10,2,'jumbolon Uncoverd',1.00,400.00,323.51,400.00,'2026-01-04 11:41:39','2026-01-04 11:41:39'),(15,11,3,'Dura Luxry',1.00,18100.00,14932.50,18100.00,'2026-01-04 11:42:16','2026-01-04 11:42:16'),(16,12,3,'Dura Luxry',1.00,18100.00,14932.50,18100.00,'2026-01-04 11:43:36','2026-01-04 11:43:36'),(17,13,3,'Dura Luxry',1.00,18100.00,14932.50,18100.00,'2026-01-04 11:47:14','2026-01-04 11:47:14'),(18,14,4,'Lamination',1.00,3600.00,3450.00,3600.00,'2026-01-04 12:43:45','2026-01-04 12:43:45'),(19,15,3,'Dura Luxry',1.00,18100.00,14932.50,18100.00,'2026-01-04 12:50:52','2026-01-04 12:50:52'),(20,16,3,'Dura Luxry',1.00,18100.00,14932.50,18100.00,'2026-01-04 12:59:52','2026-01-04 12:59:52'),(21,17,3,'Dura Luxry',1.00,18100.00,14932.50,18100.00,'2026-01-04 13:02:42','2026-01-04 13:02:42'),(22,18,2,'jumbolon Uncoverd',1.00,400.00,323.51,400.00,'2026-01-04 13:03:06','2026-01-04 13:03:06'),(23,19,4,'Lamination',10.00,3600.00,3450.00,36000.00,'2026-01-05 00:52:10','2026-01-05 00:52:10'),(24,20,3,'Dura Luxry',1.00,18100.00,0.00,18100.00,'2026-01-05 04:12:06','2026-01-05 04:12:06'),(25,21,3,'Dura Luxry',1.00,18100.00,0.00,18100.00,'2026-01-05 04:12:50','2026-01-05 04:12:50'),(26,22,3,'Dura Luxry',1.00,18100.00,0.00,18100.00,'2026-01-05 04:35:47','2026-01-05 04:35:47'),(27,23,3,'Dura Luxry',2.00,19000.00,0.00,38000.00,'2026-01-05 04:41:21','2026-01-05 04:41:21'),(28,24,5,'lasnani 3/4',1.00,3600.00,3200.00,3600.00,'2026-01-05 05:11:05','2026-01-05 05:11:05'),(29,25,4,'Lamination',15.00,3600.00,3450.00,54000.00,'2026-01-05 05:23:44','2026-01-05 05:23:44'),(30,26,4,'Lamination',10.00,3600.00,3450.00,36000.00,'2026-01-05 05:24:38','2026-01-05 05:24:38'),(31,27,4,'Lamination',10.00,3600.00,3450.00,36000.00,'2026-01-05 06:50:48','2026-01-05 06:50:48'),(32,28,3,'Dura Luxry',2.00,18100.00,0.00,36200.00,'2026-01-05 08:09:39','2026-01-05 08:09:39'),(33,28,5,'lasnani 3/4',1.00,3300.00,3200.00,3300.00,'2026-01-05 08:09:39','2026-01-05 08:09:39'),(34,28,2,'jumbolon Uncoverd',1.00,400.00,0.00,400.00,'2026-01-05 08:09:39','2026-01-05 08:09:39'),(35,28,6,'shesham',1.00,3300.00,3200.00,3300.00,'2026-01-05 08:09:39','2026-01-05 08:09:39'),(36,29,2,'jumbolon Uncoverd',1.00,400.00,0.00,400.00,'2026-01-06 03:20:54','2026-01-06 03:20:54'),(37,29,3,'Dura Luxry',1.00,18100.00,0.00,18100.00,'2026-01-06 03:20:54','2026-01-06 03:20:54'),(38,30,7,'molty 1/5\"',2.50,2720.00,2325.60,6800.00,'2026-01-11 09:34:16','2026-01-11 09:34:16'),(39,31,4,'Lamination',4.00,3600.00,3450.00,14400.00,'2026-01-14 05:36:15','2026-01-14 05:36:15'),(40,31,4,'Lamination',1.00,1350.00,1293.75,1350.00,'2026-01-14 05:36:15','2026-01-14 05:36:15'),(41,32,4,'Lamination',3.00,3600.00,3450.00,10800.00,'2026-01-14 05:37:40','2026-01-14 05:37:40'),(42,32,4,'Lamination',1.00,450.00,431.25,450.00,'2026-01-14 05:37:40','2026-01-14 05:37:40');
/*!40000 ALTER TABLE `sale_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sale_payments`
--

DROP TABLE IF EXISTS `sale_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sale_payments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sale_id` bigint(20) unsigned NOT NULL,
  `payment_ref` varchar(64) DEFAULT NULL,
  `customer_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL,
  `method` varchar(255) DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sale_payments_sale_id_foreign` (`sale_id`),
  KEY `sale_payments_user_id_foreign` (`user_id`),
  KEY `sale_payments_customer_id_foreign` (`customer_id`),
  KEY `sale_payments_payment_ref_index` (`payment_ref`),
  CONSTRAINT `sale_payments_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `sale_payments_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sale_payments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sale_payments`
--

LOCK TABLES `sale_payments` WRITE;
/*!40000 ALTER TABLE `sale_payments` DISABLE KEYS */;
INSERT INTO `sale_payments` VALUES (1,18,NULL,1,1,150.00,NULL,NULL,'2026-01-04 13:06:39','2026-01-04 13:06:39'),(2,17,NULL,1,1,8000.00,'cash',NULL,'2026-01-04 13:06:52','2026-01-04 13:06:52'),(3,17,NULL,1,1,10000.00,NULL,NULL,'2026-01-04 13:07:02','2026-01-04 13:07:02'),(4,17,NULL,1,1,100.00,NULL,NULL,'2026-01-04 13:16:18','2026-01-04 13:16:18'),(5,16,NULL,1,1,1100.00,'cash',NULL,'2026-01-04 13:16:33','2026-01-04 13:16:33'),(6,8,'85339c43-5870-4285-a7c4-b11b1748bd05',NULL,1,18100.00,NULL,NULL,'2026-01-04 13:23:19','2026-01-04 13:23:19'),(7,9,'85339c43-5870-4285-a7c4-b11b1748bd05',NULL,1,8100.00,NULL,NULL,'2026-01-04 13:23:19','2026-01-04 13:23:19'),(8,10,'85339c43-5870-4285-a7c4-b11b1748bd05',NULL,1,400.00,NULL,NULL,'2026-01-04 13:23:19','2026-01-04 13:23:19'),(9,11,'85339c43-5870-4285-a7c4-b11b1748bd05',NULL,1,18100.00,NULL,NULL,'2026-01-04 13:23:19','2026-01-04 13:23:19'),(10,12,'85339c43-5870-4285-a7c4-b11b1748bd05',NULL,1,18100.00,NULL,NULL,'2026-01-04 13:23:19','2026-01-04 13:23:19'),(11,13,'85339c43-5870-4285-a7c4-b11b1748bd05',NULL,1,18100.00,NULL,NULL,'2026-01-04 13:23:19','2026-01-04 13:23:19'),(12,14,'85339c43-5870-4285-a7c4-b11b1748bd05',NULL,1,3600.00,NULL,NULL,'2026-01-04 13:23:19','2026-01-04 13:23:19'),(13,15,'85339c43-5870-4285-a7c4-b11b1748bd05',NULL,1,15500.00,NULL,NULL,'2026-01-04 13:23:19','2026-01-04 13:23:19'),(14,21,NULL,NULL,1,18100.00,NULL,NULL,'2026-01-05 04:39:21','2026-01-05 04:39:21'),(15,15,NULL,NULL,1,2600.00,NULL,NULL,'2026-01-05 05:13:07','2026-01-05 05:13:07'),(16,20,NULL,NULL,1,3100.00,NULL,NULL,'2026-01-05 05:13:07','2026-01-05 05:13:07'),(17,22,NULL,NULL,1,3100.00,NULL,NULL,'2026-01-05 05:13:07','2026-01-05 05:13:07'),(18,24,NULL,NULL,1,600.00,NULL,NULL,'2026-01-05 05:13:07','2026-01-05 05:13:07'),(19,23,NULL,NULL,1,2000.00,NULL,NULL,'2026-01-06 07:40:07','2026-01-06 07:40:07'),(20,23,NULL,NULL,1,400.00,NULL,'jazz cahs','2026-01-07 05:24:36','2026-01-07 05:24:36');
/*!40000 ALTER TABLE `sale_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sales`
--

DROP TABLE IF EXISTS `sales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sales` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `customer_id` bigint(20) unsigned DEFAULT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `customer_phone` varchar(255) DEFAULT NULL,
  `customer_phone2` varchar(30) DEFAULT NULL,
  `customer_phone3` varchar(30) DEFAULT NULL,
  `customer_address` varchar(255) DEFAULT NULL,
  `customer_cnic` varchar(30) DEFAULT NULL,
  `total_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `paid_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `balance_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `profit_total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `profit_realized` decimal(12,2) NOT NULL DEFAULT 0.00,
  `allow_negative_stock` tinyint(1) NOT NULL DEFAULT 0,
  `note` text DEFAULT NULL,
  `sale_type` varchar(255) NOT NULL DEFAULT 'cash',
  `status` varchar(255) NOT NULL DEFAULT 'paid',
  `due_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sales_user_id_foreign` (`user_id`),
  KEY `sales_created_at_index` (`created_at`),
  KEY `sales_customer_id_foreign` (`customer_id`),
  CONSTRAINT `sales_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `sales_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sales`
--

LOCK TABLES `sales` WRITE;
/*!40000 ALTER TABLE `sales` DISABLE KEYS */;
INSERT INTO `sales` VALUES (1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,51500.00,51500.00,0.00,0.00,0.00,0,NULL,'cash','paid',NULL,'2026-01-03 08:50:47','2026-01-03 08:50:47'),(2,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,51500.00,51500.00,0.00,0.00,0.00,0,NULL,'cash','paid',NULL,'2026-01-03 10:17:50','2026-01-03 10:17:50'),(3,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,51500.00,51500.00,0.00,0.00,0.00,0,NULL,'cash','paid',NULL,'2026-01-03 10:17:59','2026-01-03 10:17:59'),(4,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,51500.00,51500.00,0.00,0.00,0.00,0,NULL,'cash','paid',NULL,'2026-01-03 10:21:39','2026-01-03 10:21:39'),(8,1,NULL,'ali masood','03126233326',NULL,NULL,NULL,NULL,18100.00,18100.00,0.00,3167.50,3167.50,0,NULL,'udhar','paid','2026-05-12','2026-01-04 11:02:38','2026-01-04 13:23:19'),(9,1,NULL,'ali masood','03126233326',NULL,NULL,NULL,NULL,18100.00,18100.00,0.00,3167.50,3167.50,0,NULL,'udhar','paid','2026-04-01','2026-01-04 11:07:12','2026-01-04 13:23:19'),(10,1,NULL,'ali masood','03126233326',NULL,NULL,NULL,NULL,400.00,400.00,0.00,76.49,76.49,0,NULL,'udhar','paid',NULL,'2026-01-04 11:41:38','2026-01-04 13:23:19'),(11,1,NULL,'ali masood','03126233326',NULL,NULL,NULL,NULL,18100.00,18100.00,0.00,3167.50,3167.50,0,NULL,'udhar','paid','2026-01-04','2026-01-04 11:42:16','2026-01-04 13:23:19'),(12,1,NULL,'ali masood','03126233326',NULL,NULL,NULL,NULL,18100.00,18100.00,0.00,3167.50,3167.50,0,NULL,'udhar','paid','2026-01-04','2026-01-04 11:43:36','2026-01-04 13:23:19'),(13,1,NULL,'ali masood','03126233326',NULL,NULL,NULL,NULL,18100.00,18100.00,0.00,3167.50,3167.50,0,NULL,'udhar','paid',NULL,'2026-01-04 11:47:14','2026-01-04 13:23:19'),(14,1,1,NULL,'03126233326',NULL,NULL,NULL,NULL,3600.00,3600.00,0.00,150.00,150.00,0,NULL,'udhar','paid',NULL,'2026-01-04 12:43:45','2026-01-04 13:23:19'),(15,1,1,NULL,'03126233326',NULL,NULL,NULL,NULL,18100.00,18100.00,0.00,3167.50,3167.50,0,NULL,'cash','paid',NULL,'2026-01-04 12:50:52','2026-01-05 05:13:07'),(16,1,1,NULL,'03126233326',NULL,NULL,NULL,NULL,18100.00,18100.00,0.00,3167.50,3167.50,0,NULL,'udhar','paid',NULL,'2026-01-04 12:59:52','2026-01-04 13:16:33'),(17,1,1,NULL,'03126233326',NULL,NULL,NULL,NULL,18100.00,18100.00,0.00,3167.50,3167.50,0,NULL,'udhar','paid',NULL,'2026-01-04 13:02:42','2026-01-04 13:16:18'),(18,1,1,NULL,'03126233326',NULL,NULL,NULL,NULL,400.00,400.00,0.00,76.49,76.49,0,NULL,'udhar','paid',NULL,'2026-01-04 13:03:06','2026-01-04 13:06:39'),(19,1,NULL,'ali masood',NULL,NULL,NULL,NULL,NULL,36000.00,36000.00,0.00,1500.00,1500.00,0,NULL,'cash','paid',NULL,'2026-01-05 00:52:10','2026-01-05 00:52:10'),(20,1,NULL,'ali masood','03126233326',NULL,NULL,NULL,NULL,18100.00,18100.00,0.00,18100.00,18100.00,0,NULL,'cash','paid',NULL,'2026-01-05 04:12:06','2026-01-05 05:13:07'),(21,1,NULL,'sami urf Qureshi','03006806503',NULL,NULL,NULL,NULL,18100.00,18100.00,0.00,18100.00,18100.00,0,NULL,'cash','paid',NULL,'2026-01-05 04:12:50','2026-01-05 04:39:21'),(22,1,NULL,'ali masood','03126233326',NULL,NULL,NULL,NULL,18100.00,18100.00,0.00,18100.00,18100.00,0,NULL,'cash','paid',NULL,'2026-01-05 04:35:47','2026-01-05 05:13:07'),(23,1,NULL,'sami urf Qureshi','03006806503',NULL,NULL,NULL,NULL,38000.00,37400.00,600.00,38000.00,0.00,0,NULL,'udhar','partial',NULL,'2026-01-05 04:41:21','2026-01-07 05:25:05'),(24,1,NULL,'ali masood','03126233326',NULL,NULL,NULL,NULL,3600.00,3600.00,0.00,400.00,400.00,0,'wapsi 08/01/2026','cash','paid',NULL,'2026-01-05 05:11:05','2026-01-05 05:13:07'),(25,1,NULL,'sami urf Qureshi','03126233326',NULL,NULL,NULL,NULL,54000.00,54000.00,0.00,2250.00,2250.00,0,NULL,'cash','paid',NULL,'2026-01-05 05:23:44','2026-01-05 05:23:44'),(26,1,NULL,'ali masood','03126233326',NULL,NULL,NULL,NULL,36000.00,36000.00,0.00,1500.00,1500.00,0,NULL,'cash','paid',NULL,'2026-01-05 05:24:38','2026-01-05 05:24:38'),(27,1,NULL,'ali masood','03006806503',NULL,NULL,NULL,NULL,36000.00,35000.00,1000.00,1500.00,0.00,0,NULL,'udhar','partial',NULL,'2026-01-05 06:50:48','2026-01-05 06:50:49'),(28,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,43200.00,32000.00,11200.00,36800.00,0.00,0,NULL,'udhar','partial',NULL,'2026-01-05 08:09:39','2026-01-05 08:09:39'),(29,1,NULL,'amir','03068865736',NULL,NULL,NULL,NULL,18500.00,18500.00,0.00,18500.00,18500.00,0,NULL,'cash','paid',NULL,'2026-01-06 03:20:54','2026-01-06 03:20:54'),(30,1,3,'imran','03127817811',NULL,NULL,'shadman colony street 4b ahmed pur east',NULL,6800.00,6000.00,800.00,986.00,0.00,0,NULL,'udhar','partial',NULL,'2026-01-11 09:34:16','2026-01-11 09:34:16'),(31,1,4,'ibtaisam','03117817811',NULL,NULL,NULL,NULL,15750.00,15750.00,0.00,656.25,656.25,0,NULL,'cash','paid',NULL,'2026-01-14 05:36:15','2026-01-14 05:36:15'),(32,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,11250.00,11250.00,0.00,468.75,468.75,0,NULL,'cash','paid',NULL,'2026-01-14 05:37:40','2026-01-14 05:37:40');
/*!40000 ALTER TABLE `sales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
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
INSERT INTO `sessions` VALUES ('1aYuAhvfd5DK9ieTRLa8f5rlr6LL90uyzrvISQDR',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','YToyOntzOjY6Il90b2tlbiI7czo0MDoiT2dyRXBDdnZGUjg2WnNOQzllSWFXRGdiQkQxYmZDRDVVMlEzRDduTSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1768471369),('mARFuo2Amn549H7LHNRBJ19V3d05lUsurBb4yRR0',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','YTo1OntzOjY6Il90b2tlbiI7czo0MDoiTGE4cUdoSU1FaTkycHVJRTd1NU85ZEo0cEpIWWdBckhodndhNmpsQyI7czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTtzOjc6Im10X2NhcnQiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjM2OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvcG9zP2c9aGFyZHdhcmUiO3M6NToicm91dGUiO3M6MTI6Im10LnBvcy5pbmRleCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1768999358),('PaLesfbpngLv2rNMCtdAV9Rayz1sC7cxpcsZGYrr',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','YTo1OntzOjY6Il90b2tlbiI7czo0MDoiWTVIcVpwTEdLYnJjMzFsSHpUU1dQS0dQQzdYc3hENDJxT0ZZcDRIUCI7czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTtzOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czoyODoiaHR0cDovLzEyNy4wLjAuMTo4MDAwL3Bvcz9nPSI7czo1OiJyb3V0ZSI7czoxMjoibXQucG9zLmluZGV4Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo3OiJtdF9jYXJ0IjthOjM6e3M6MjoicDMiO2E6Njp7czo2OiJyb3dfaWQiO3M6MjoicDMiO3M6MTA6InByb2R1Y3RfaWQiO2k6MztzOjQ6Im5hbWUiO3M6MTA6IkR1cmEgTHV4cnkiO3M6MzoicXR5IjtkOjE7czo1OiJwcmljZSI7ZDoxODEwMDtzOjk6InVuaXRfdHlwZSI7czo2OiJub3JtYWwiO31zOjI6InAyIjthOjY6e3M6Njoicm93X2lkIjtzOjI6InAyIjtzOjEwOiJwcm9kdWN0X2lkIjtpOjI7czo0OiJuYW1lIjtzOjE3OiJqdW1ib2xvbiBVbmNvdmVyZCI7czozOiJxdHkiO2Q6MTtzOjU6InByaWNlIjtkOjQwMDtzOjk6InVuaXRfdHlwZSI7czo2OiJub3JtYWwiO31zOjI6InA0IjthOjY6e3M6Njoicm93X2lkIjtzOjI6InA0IjtzOjEwOiJwcm9kdWN0X2lkIjtpOjQ7czo0OiJuYW1lIjtzOjEwOiJMYW1pbmF0aW9uIjtzOjM6InF0eSI7ZDoxO3M6NToicHJpY2UiO2Q6MzYwMDtzOjk6InVuaXRfdHlwZSI7czo2OiJub3JtYWwiO319fQ==',1769002959),('WacQSoBrebEchg4T36ifnffWq1RD7RoYu2kiXcDB',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','YTo1OntzOjY6Il90b2tlbiI7czo0MDoiRENYbEJPZzFCRjFrOTZvRmowNk43YjAxRXQyQVZLMHhyZFdsbmszTSI7czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTtzOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozNjoiaHR0cDovLzEyNy4wLjAuMTo4MDAwL3Bvcz9nPWhhcmR3YXJlIjtzOjU6InJvdXRlIjtzOjEyOiJtdC5wb3MuaW5kZXgiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjc6Im10X2NhcnQiO2E6Nzp7czoyOiJwMyI7YToxMjp7czo2OiJyb3dfaWQiO3M6MjoicDMiO3M6MTA6InByb2R1Y3RfaWQiO2k6MztzOjQ6Im5hbWUiO3M6MTA6IkR1cmEgTHV4cnkiO3M6MzoicXR5IjtkOjI7czo1OiJwcmljZSI7ZDoxODEwMDtzOjk6ImZ1bGxfd19mdCI7TjtzOjk6ImZ1bGxfbF9mdCI7TjtzOjg6ImN1dF93X2Z0IjtOO3M6ODoiY3V0X2xfZnQiO047czo2OiJzb3VyY2UiO3M6NToic3RvY2siO3M6MTE6ImxlZnRvdmVyX2lkIjtOO3M6OToidW5pdF90eXBlIjtzOjY6Im5vcm1hbCI7fXM6MjoicDIiO2E6MTI6e3M6Njoicm93X2lkIjtzOjI6InAyIjtzOjEwOiJwcm9kdWN0X2lkIjtpOjI7czo0OiJuYW1lIjtzOjE3OiJqdW1ib2xvbiBVbmNvdmVyZCI7czozOiJxdHkiO2Q6MTtzOjU6InByaWNlIjtkOjQwMDtzOjk6ImZ1bGxfd19mdCI7TjtzOjk6ImZ1bGxfbF9mdCI7TjtzOjg6ImN1dF93X2Z0IjtOO3M6ODoiY3V0X2xfZnQiO047czo2OiJzb3VyY2UiO3M6NToic3RvY2siO3M6MTE6ImxlZnRvdmVyX2lkIjtOO3M6OToidW5pdF90eXBlIjtzOjY6Im5vcm1hbCI7fXM6MjoicDQiO2E6MTI6e3M6Njoicm93X2lkIjtzOjI6InA0IjtzOjEwOiJwcm9kdWN0X2lkIjtpOjQ7czo0OiJuYW1lIjtzOjEwOiJMYW1pbmF0aW9uIjtzOjM6InF0eSI7ZDoxO3M6NToicHJpY2UiO2Q6MzYwMDtzOjk6ImZ1bGxfd19mdCI7TjtzOjk6ImZ1bGxfbF9mdCI7TjtzOjg6ImN1dF93X2Z0IjtOO3M6ODoiY3V0X2xfZnQiO047czo2OiJzb3VyY2UiO3M6NToic3RvY2siO3M6MTE6ImxlZnRvdmVyX2lkIjtOO3M6OToidW5pdF90eXBlIjtzOjY6Im5vcm1hbCI7fXM6MjoicDUiO2E6MTI6e3M6Njoicm93X2lkIjtzOjI6InA1IjtzOjEwOiJwcm9kdWN0X2lkIjtpOjU7czo0OiJuYW1lIjtzOjExOiJsYXNuYW5pIDMvNCI7czozOiJxdHkiO2Q6MTtzOjU6InByaWNlIjtkOjMzMDA7czo5OiJmdWxsX3dfZnQiO047czo5OiJmdWxsX2xfZnQiO047czo4OiJjdXRfd19mdCI7TjtzOjg6ImN1dF9sX2Z0IjtOO3M6Njoic291cmNlIjtzOjU6InN0b2NrIjtzOjExOiJsZWZ0b3Zlcl9pZCI7TjtzOjk6InVuaXRfdHlwZSI7czo2OiJub3JtYWwiO31zOjI6InAxIjthOjEyOntzOjY6InJvd19pZCI7czoyOiJwMSI7czoxMDoicHJvZHVjdF9pZCI7aToxO3M6NDoibmFtZSI7czo1OiJtb2x0eSI7czozOiJxdHkiO2Q6MTtzOjU6InByaWNlIjtkOjMzMDAwO3M6OToiZnVsbF93X2Z0IjtOO3M6OToiZnVsbF9sX2Z0IjtOO3M6ODoiY3V0X3dfZnQiO047czo4OiJjdXRfbF9mdCI7TjtzOjY6InNvdXJjZSI7czo1OiJzdG9jayI7czoxMToibGVmdG92ZXJfaWQiO047czo5OiJ1bml0X3R5cGUiO3M6Njoibm9ybWFsIjt9czoyOiJwNyI7YToxMjp7czo2OiJyb3dfaWQiO3M6MjoicDciO3M6MTA6InByb2R1Y3RfaWQiO2k6NztzOjQ6Im5hbWUiO3M6MTA6Im1vbHR5IDEvNSIiO3M6MzoicXR5IjtkOjE7czo1OiJwcmljZSI7ZDoyNzIwO3M6OToiZnVsbF93X2Z0IjtOO3M6OToiZnVsbF9sX2Z0IjtOO3M6ODoiY3V0X3dfZnQiO047czo4OiJjdXRfbF9mdCI7TjtzOjY6InNvdXJjZSI7czo1OiJzdG9jayI7czoxMToibGVmdG92ZXJfaWQiO047czo5OiJ1bml0X3R5cGUiO3M6Njoibm9ybWFsIjt9czoyOiJwNiI7YToxMjp7czo2OiJyb3dfaWQiO3M6MjoicDYiO3M6MTA6InByb2R1Y3RfaWQiO2k6NjtzOjQ6Im5hbWUiO3M6Nzoic2hlc2hhbSI7czozOiJxdHkiO2Q6MTtzOjU6InByaWNlIjtkOjMzMDA7czo5OiJmdWxsX3dfZnQiO047czo5OiJmdWxsX2xfZnQiO047czo4OiJjdXRfd19mdCI7TjtzOjg6ImN1dF9sX2Z0IjtOO3M6Njoic291cmNlIjtzOjU6InN0b2NrIjtzOjExOiJsZWZ0b3Zlcl9pZCI7TjtzOjk6InVuaXRfdHlwZSI7czo2OiJub3JtYWwiO319fQ==',1768471997);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shops`
--

DROP TABLE IF EXISTS `shops`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shops` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shops`
--

LOCK TABLES `shops` WRITE;
/*!40000 ALTER TABLE `shops` DISABLE KEYS */;
/*!40000 ALTER TABLE `shops` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stock_movements`
--

DROP TABLE IF EXISTS `stock_movements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stock_movements` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `type` varchar(255) NOT NULL,
  `qty` decimal(12,2) NOT NULL,
  `note` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stock_movements_user_id_foreign` (`user_id`),
  KEY `stock_movements_product_id_created_at_index` (`product_id`,`created_at`),
  CONSTRAINT `stock_movements_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `stock_movements_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_movements`
--

LOCK TABLES `stock_movements` WRITE;
/*!40000 ALTER TABLE `stock_movements` DISABLE KEYS */;
INSERT INTO `stock_movements` VALUES (1,3,1,'in',10.00,'purchase','2026-01-03 07:10:18','2026-01-03 07:10:18'),(2,3,1,'sale',-1.00,'POS checkout','2026-01-03 10:21:39','2026-01-03 10:21:39'),(3,2,1,'sale',-1.00,'POS checkout','2026-01-03 10:21:39','2026-01-03 10:21:39'),(4,1,1,'sale',-1.00,'POS checkout','2026-01-03 10:21:39','2026-01-03 10:21:39'),(9,3,1,'out',-1.00,'POS checkout (Sale #8)','2026-01-04 11:02:38','2026-01-04 11:02:38'),(10,3,1,'out',-1.00,'POS checkout (Sale #9)','2026-01-04 11:07:12','2026-01-04 11:07:12'),(11,2,1,'out',-1.00,'POS checkout (Sale #10)','2026-01-04 11:41:39','2026-01-04 11:41:39'),(12,3,1,'out',-1.00,'POS checkout (Sale #11)','2026-01-04 11:42:16','2026-01-04 11:42:16'),(13,3,1,'out',-1.00,'POS checkout (Sale #12)','2026-01-04 11:43:36','2026-01-04 11:43:36'),(14,3,1,'out',-1.00,'POS checkout (Sale #13)','2026-01-04 11:47:14','2026-01-04 11:47:14'),(15,4,1,'sale',-1.00,'POS checkout','2026-01-04 12:43:45','2026-01-04 12:43:45'),(16,3,1,'sale',-1.00,'POS checkout','2026-01-04 12:50:52','2026-01-04 12:50:52'),(17,3,1,'sale',-1.00,'POS checkout','2026-01-04 12:59:52','2026-01-04 12:59:52'),(18,3,1,'sale',-1.00,'POS checkout','2026-01-04 13:02:42','2026-01-04 13:02:42'),(19,2,1,'sale',-1.00,'POS checkout','2026-01-04 13:03:06','2026-01-04 13:03:06'),(20,4,1,'sale',-10.00,'POS checkout','2026-01-05 00:52:10','2026-01-05 00:52:10');
/*!40000 ALTER TABLE `stock_movements` ENABLE KEYS */;
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
  `role` varchar(20) NOT NULL DEFAULT 'cashier',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Mian Ali','ww.alimasood@gmail.com','admin',1,NULL,'$2y$12$WsosD7lGcSsMmkF6CUDBRO2lY/p2EorEiL1qMhZJohxFAbR.fIGEy','YE9WvfIcBKXEBBsSuoNs3kY5H7LgAbmnZnOR3kySWFnpeV9ZGONcl1OHfJyP','2026-01-01 11:28:59','2026-01-11 07:24:39'),(2,'Babar','miancashier1@gmail.com','cashier',1,NULL,'$2y$12$A/SN/UbhAh3pZlqqdexQAuPtVT.eLECd3VfVvoVB9POb264ZsH3Iy',NULL,'2026-01-11 07:25:14','2026-01-11 07:25:14');
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

-- Dump completed on 2026-06-05 22:29:33
