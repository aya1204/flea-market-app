-- MySQL dump 10.13  Distrib 8.0.26, for Linux (x86_64)
--
-- Host: localhost    Database: laravel_db
-- ------------------------------------------------------
-- Server version	8.0.26

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
-- Table structure for table `brands`
--

DROP TABLE IF EXISTS `brands`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `brands` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `brands`
--

LOCK TABLES `brands` WRITE;
/*!40000 ALTER TABLE `brands` DISABLE KEYS */;
/*!40000 ALTER TABLE `brands` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'ファッション',NULL,NULL),(2,'家電',NULL,NULL),(3,'インテリア',NULL,NULL),(4,'レディース',NULL,NULL),(5,'メンズ',NULL,NULL),(6,'コスメ',NULL,NULL),(7,'本',NULL,NULL),(8,'ゲーム',NULL,NULL),(9,'スポーツ',NULL,NULL),(10,'キッチン',NULL,NULL),(11,'ハンドメイド',NULL,NULL),(12,'アクセサリー',NULL,NULL),(13,'おもちゃ',NULL,NULL),(14,'ベビー・キッズ',NULL,NULL);
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `category_item`
--

DROP TABLE IF EXISTS `category_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `category_item` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `item_id` bigint unsigned NOT NULL,
  `category_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `category_item_item_id_foreign` (`item_id`),
  KEY `category_item_category_id_foreign` (`category_id`),
  CONSTRAINT `category_item_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  CONSTRAINT `category_item_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `category_item`
--

LOCK TABLES `category_item` WRITE;
/*!40000 ALTER TABLE `category_item` DISABLE KEYS */;
INSERT INTO `category_item` VALUES (1,1,1,NULL,NULL),(2,1,5,NULL,NULL),(3,1,12,NULL,NULL),(4,2,2,NULL,NULL),(5,2,8,NULL,NULL),(6,3,10,NULL,NULL),(7,4,1,NULL,NULL),(8,4,5,NULL,NULL),(9,5,2,NULL,NULL),(10,5,8,NULL,NULL),(11,6,2,NULL,NULL),(12,7,1,NULL,NULL),(13,7,4,NULL,NULL),(14,7,12,NULL,NULL),(15,8,10,NULL,NULL),(16,9,10,NULL,NULL),(17,10,1,NULL,NULL),(18,10,4,NULL,NULL),(19,10,6,NULL,NULL);
/*!40000 ALTER TABLE `category_item` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `comments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `item_id` bigint unsigned NOT NULL,
  `comment` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `comments_user_id_foreign` (`user_id`),
  KEY `comments_item_id_foreign` (`item_id`),
  CONSTRAINT `comments_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comments`
--

LOCK TABLES `comments` WRITE;
/*!40000 ALTER TABLE `comments` DISABLE KEYS */;
INSERT INTO `comments` VALUES (1,1,4,'傷はありますか？','2025-06-19 04:19:39','2025-06-19 04:19:39');
/*!40000 ALTER TABLE `comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `conditions`
--

DROP TABLE IF EXISTS `conditions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `conditions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `conditions`
--

LOCK TABLES `conditions` WRITE;
/*!40000 ALTER TABLE `conditions` DISABLE KEYS */;
INSERT INTO `conditions` VALUES (1,'良好',NULL,NULL),(2,'目立った傷や汚れなし',NULL,NULL),(3,'やや傷や汚れあり',NULL,NULL),(4,'状態が悪い',NULL,NULL);
/*!40000 ALTER TABLE `conditions` ENABLE KEYS */;
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
-- Table structure for table `favorites`
--

DROP TABLE IF EXISTS `favorites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `favorites` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `item_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `favorites_user_id_item_id_unique` (`user_id`,`item_id`),
  KEY `favorites_item_id_foreign` (`item_id`),
  CONSTRAINT `favorites_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`),
  CONSTRAINT `favorites_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `favorites`
--

LOCK TABLES `favorites` WRITE;
/*!40000 ALTER TABLE `favorites` DISABLE KEYS */;
INSERT INTO `favorites` VALUES (1,1,1,'2025-06-11 15:35:58','2025-06-11 15:35:58'),(7,1,4,'2025-06-13 14:04:42','2025-06-13 14:04:42');
/*!40000 ALTER TABLE `favorites` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `items`
--

DROP TABLE IF EXISTS `items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `seller_user_id` bigint unsigned NOT NULL,
  `purchase_user_id` bigint unsigned DEFAULT NULL,
  `condition_id` bigint unsigned NOT NULL,
  `brand_id` bigint unsigned DEFAULT NULL,
  `paymentmethod_id` bigint unsigned DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` int NOT NULL,
  `is_sold` tinyint(1) NOT NULL DEFAULT '0',
  `postal_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `building` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `items_seller_user_id_foreign` (`seller_user_id`),
  KEY `items_purchase_user_id_foreign` (`purchase_user_id`),
  KEY `items_condition_id_foreign` (`condition_id`),
  KEY `items_brand_id_foreign` (`brand_id`),
  KEY `items_paymentmethod_id_foreign` (`paymentmethod_id`),
  CONSTRAINT `items_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`),
  CONSTRAINT `items_condition_id_foreign` FOREIGN KEY (`condition_id`) REFERENCES `conditions` (`id`),
  CONSTRAINT `items_paymentmethod_id_foreign` FOREIGN KEY (`paymentmethod_id`) REFERENCES `paymentmethods` (`id`),
  CONSTRAINT `items_purchase_user_id_foreign` FOREIGN KEY (`purchase_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `items_seller_user_id_foreign` FOREIGN KEY (`seller_user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `items`
--

LOCK TABLES `items` WRITE;
/*!40000 ALTER TABLE `items` DISABLE KEYS */;
INSERT INTO `items` VALUES (1,2,1,1,NULL,NULL,'images/Armani+Mens+Clock.jpg','腕時計','スタイリッシュなデザインのメンズ腕時計',15000,1,NULL,NULL,NULL,'2025-06-11 11:05:05','2025-06-11 11:05:35'),(2,2,1,2,NULL,NULL,'images/HDD+Hard+Disk.jpg','HDD','高速で信頼性の高いハードディスク',5000,1,NULL,NULL,NULL,'2025-06-11 11:05:05','2025-06-12 14:07:14'),(3,3,1,3,NULL,NULL,'images/iLoveIMG+d.jpg','玉ねぎ3束','新鮮な玉ねぎ3束のセット',300,1,NULL,NULL,NULL,'2025-06-11 11:05:05','2025-06-11 14:47:52'),(4,3,1,4,NULL,NULL,'images/Leather+Shoes+Product+Photo.jpg','革靴','クラシックなデザインの革靴',4000,1,NULL,NULL,NULL,'2025-06-11 11:05:05','2025-06-11 14:37:01'),(5,3,1,1,NULL,NULL,'images/Living+Room+Laptop.jpg','ノートPC','高性能なノートパソコン',45000,1,NULL,NULL,NULL,'2025-06-11 11:05:05','2025-06-20 13:40:17'),(6,2,1,2,NULL,NULL,'images/Music+Mic+4632231.jpg','マイク','高音質のレコーディング用マイク',8000,1,NULL,NULL,NULL,'2025-06-11 11:05:05','2025-06-20 13:43:08'),(7,3,1,3,NULL,NULL,'images/Purse+fashion+pocket.jpg','ショルダーバッグ','おしゃれなショルダーバッグ',3500,0,NULL,NULL,NULL,'2025-06-11 11:05:05','2025-06-20 13:45:28'),(8,3,NULL,4,NULL,NULL,'images/Tumbler+souvenir.jpg','タンブラー','使いやすいタンブラー',500,0,NULL,NULL,NULL,'2025-06-11 11:05:05','2025-06-11 11:05:05'),(9,2,NULL,1,NULL,NULL,'images/Waitress+with+Coffee+Grinder.jpg','コーヒーミル','手動のコーヒーミル',4000,0,NULL,NULL,NULL,'2025-06-11 11:05:05','2025-06-11 11:05:05'),(10,1,NULL,2,NULL,NULL,'images/外出メイクアップセット.jpg','メイクセット','便利なメイクアップセット',2500,0,NULL,NULL,NULL,'2025-06-11 11:05:05','2025-06-11 11:05:05'),(11,1,NULL,1,NULL,NULL,'images/oWZLmN6TodY0EHNFUD5S5BFD3xJrvauFvzwQhF7r.png','スニーカー','赤いスニーカー\r\n新品未使用\r\nサイズが合わなかったため出品しました',2500,0,NULL,NULL,NULL,'2025-06-18 04:52:47','2025-06-18 04:52:47'),(12,1,NULL,3,NULL,NULL,'images/AOUU6rP5v4VmCV985Ftgo8476Ov2gMpp5LJumf4d.png','パーカー','サイズが合わなくなったので出品します',1000,0,NULL,NULL,NULL,'2025-06-18 14:30:13','2025-06-18 14:30:13');
/*!40000 ALTER TABLE `items` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=107 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (14,'2025_05_22_095621_add_is_sold_to_category_item_table',1),(94,'2014_10_12_000000_create_users_table',2),(95,'2014_10_12_100000_create_password_resets_table',2),(96,'2014_10_12_200000_add_two_factor_columns_to_users_table',2),(97,'2019_08_19_000000_create_failed_jobs_table',2),(98,'2019_12_14_000001_create_personal_access_tokens_table',2),(99,'2025_05_13_211339_create_paymentmethods_table',2),(100,'2025_05_13_220957_create_categories_table',2),(101,'2025_05_13_221126_create_conditions_table',2),(102,'2025_05_13_221249_create_brands_table',2),(103,'2025_05_13_221300_create_items_table',2),(104,'2025_05_13_221302_create_favorites_table',2),(105,'2025_05_13_221359_category_item_table',2),(106,'2025_05_13_221428_create_comments_table',2);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_resets`
--

LOCK TABLES `password_resets` WRITE;
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `paymentmethods`
--

DROP TABLE IF EXISTS `paymentmethods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `paymentmethods` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `paymentmethods`
--

LOCK TABLES `paymentmethods` WRITE;
/*!40000 ALTER TABLE `paymentmethods` DISABLE KEYS */;
INSERT INTO `paymentmethods` VALUES (1,'カード払い',NULL,NULL),(2,'コンビニ払い',NULL,NULL);
/*!40000 ALTER TABLE `paymentmethods` ENABLE KEYS */;
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
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
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
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `two_factor_secret` text COLLATE utf8mb4_unicode_ci,
  `two_factor_recovery_codes` text COLLATE utf8mb4_unicode_ci,
  `postal_code` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `building` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'山田 太郎','test@example.com','$2y$10$iimASEmbn4czWx1y3fI.lOLUy0wVyH/k1Er7ZwO/6B4BJRcEbHcZq',NULL,NULL,'000-0000','北海道北見市田端町123',NULL,'user1_icon.png','2025-06-17 04:21:43',NULL,'2025-06-11 11:05:05','2025-06-17 04:21:43'),(2,'佐藤 花子','test2@example.com','$2y$10$8xDlp6HJf9GOpgQPkiG4y.7oX5eCwdTsD3gUv5sk.vx0LdcN4ILIW',NULL,NULL,'123-4567','東京都渋谷区千駄ヶ谷1-2-3',' 千駄ヶ谷マンション102','user2_icon.png',NULL,NULL,'2025-06-11 11:05:05','2025-06-11 11:05:05'),(3,'鈴木 一郎','test3@example.com','$2y$10$A7oIK67KAFnd9EtXugpccOFwN7OzGbhFDEBWlG7R0hbCqIuMJmcUG',NULL,NULL,'123-4567','東京都渋谷区千駄ヶ谷1-2-3',' 千駄ヶ谷マンション103','user3_icon.png',NULL,NULL,'2025-06-11 11:05:05','2025-06-11 11:05:05'),(4,'キウイ','testggggg@example.com','$2y$10$.4WVUgbz752g7ArOk2RNVufzSjfBGUZAEa1V33e56ZODwIed0fDE6',NULL,NULL,NULL,NULL,NULL,NULL,'2025-06-14 14:05:22',NULL,'2025-06-14 14:05:11','2025-06-14 14:05:22'),(5,'山本悟','testabc@example.com','$2y$10$Pu1Gk34sT7U2kNQod08u9.EUKNjqfpnzxQtvHhMEnRkH8PPaiZUoC',NULL,NULL,NULL,NULL,NULL,NULL,'2025-06-14 14:27:08',NULL,'2025-06-14 14:27:00','2025-06-14 14:27:08'),(6,'伊藤はじめ','testddd@example.com','$2y$10$Dzwxbvdpaqo4Wu.7Oynk8OhJiBg4CVfCVQV.r0MrrmNS3EBg0gUgW',NULL,NULL,NULL,NULL,NULL,NULL,'2025-06-14 14:34:53',NULL,'2025-06-14 14:34:46','2025-06-14 14:34:53'),(7,'加藤由華','testfff@example.com','$2y$10$4eQeusFOxZHBEKI3MmDDse2CRIpiqtfOSOuvx.mpQ0vOr2dJ5CWry',NULL,NULL,NULL,NULL,NULL,NULL,'2025-06-14 14:42:48',NULL,'2025-06-14 14:42:38','2025-06-14 14:42:48'),(8,'山下望美','testhhh@example.com','$2y$10$bB44tgamvgsfNkF.DxLrnO4.gSsNX/ognWkb4D3vGEb.wsa50EeJi',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-06-14 15:01:53','2025-06-14 15:01:53'),(9,'佐久間若菜','testiii@example.com','$2y$10$Ai4ghp5b514KwnwwaI/ObOf0EuVNUmv8C64/qj8vbrdSzLtL0RVHa',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-06-14 15:06:17','2025-06-14 15:06:17'),(10,'大竹志乃','testjjj@example.com','$2y$10$LhRPnHwo1MdKbLAH85v1/.a5MswqcGhAOEfdtAKiFBqWsV6h8qCCe',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-06-14 15:07:22','2025-06-14 15:07:22'),(11,'志賀梨菜','testkkk@example.com','$2y$10$u3qGSCoG4sAChHIwh5NETeY2EvQ8Z8NVlHESE8omL0KZARMem.emu',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-06-14 15:11:32','2025-06-14 15:11:32'),(12,'市原真弥','testlll@example.com','$2y$10$.OZGoOf1IAc7kf.oxzRUbu28DPtv/0fE0UcN76rGvGxKNSd7fR7Wq',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-06-14 15:16:45','2025-06-14 15:16:45'),(13,'橋本真紀','testmmm@example.com','$2y$10$XmtndINOKqP01MvtJ9S.1u2N81G9IpUykVRO.8qFvogMLWS7plbEC',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-06-14 15:31:51','2025-06-14 15:31:51'),(14,'三村麻美','testnnn@example.com','$2y$10$ZCIG5gJ36gqVCf/dDN7ROugsdk5MW4rCSPTMqdYhzyHA.ODxMHiRm',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-06-14 15:35:56','2025-06-14 15:35:56'),(15,'山口   未希','testooo@example.com','$2y$10$zEwMkRgRjGQfMfHubDd61.pSY/bdM5j2X0qxZNXC0roFCRXoK87Yi',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-06-14 15:38:17','2025-06-14 15:38:17'),(16,'remoon','testremoon@example.com','$2y$10$PljdIzVl.QSsOxqdihgk2uu340Y3h.Y/e2/gB6WkEsXfRK1wGBW42',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-06-14 15:44:57','2025-06-14 15:44:57'),(17,'peach','testpeach@example.com','$2y$10$OiHC6vycRuOaasyjeA1eGepCm/1hKLQfDTQKE4S661cPnjnd0WW2O',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-06-14 15:47:13','2025-06-14 15:47:13'),(18,'オレンジ','testorange@example.com','$2y$10$0uC9Ugzmb7ULb6ezfW3RQuKofOlqwocy0yGoKIMri0eWkBOlfAQUO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-06-14 15:48:26','2025-06-14 15:48:26'),(19,'木原千草','testppp@example.com','$2y$10$fshFI66yOyC9qqi60JlRZuOVy7zABpAeg2iWj7CAMZZ8SGNMX61GS',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-06-15 10:27:36','2025-06-15 10:27:36'),(20,'いちご','testichigo@example.com','$2y$10$u.yLcKvODiRrwpbbO3dy8Oy7FUZELo6W3ZAsD2rEi1urLMzYs5G3u',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-06-15 10:53:10','2025-06-15 10:53:10'),(21,'ぶどう','testgrape@example.com','$2y$10$ogEQ/u.YmSibjjUocTheYusyaysbiNGX3m9s8VExr2ZK1kOi8mI7a',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-06-15 10:55:30','2025-06-15 10:55:30'),(22,'みかん','testmikan@example.com','$2y$10$TzRWsWc/K0F9ffz9DkTEROLlMl2OexCgByRLEWhW8kEuL7xkssvi.',NULL,NULL,'000-0000','北海道北見市田端町1234','キタミマンション1030','TqmCKqnnXsvU7oQEs7VTdMZeiChcOgvMDrHLy5w6.png',NULL,NULL,'2025-06-15 11:00:03','2025-06-15 11:00:58'),(23,'雨','testrain@example.com','$2y$10$AzfQpuYcY09FThrpKgD5E.lGxkQ.jPOsb49ur4yj7/lJflbFXMuxu',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-06-15 11:18:52','2025-06-15 11:18:52'),(24,'もも','testmomo@example.com','$2y$10$Eo1GH/WsaNzVoVIFmgr6Ye4K5xePCL5nwZ5flIU7.gOQNJ/LLIMPa',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-06-15 11:27:12','2025-06-15 11:27:12'),(25,'きのこ','kinoko@example.com','$2y$10$P8ARKe7rM/G1R3MDIS6Ez.J7h3GB8ws5ASUKlZdFm3MCGaF/rNhz.',NULL,NULL,'000-0000','北海道北見市田端町1234','キタミマンション1000',NULL,'2025-06-16 14:19:02',NULL,'2025-06-16 14:03:28','2025-06-16 14:42:17'),(26,'たけのこ','takenoko@example.com','$2y$10$OyzyzZEOd4qGURVlFm5vgeKK.nUMZrBiaC4b/huxD2uY.Y8jXosLm',NULL,NULL,NULL,NULL,NULL,NULL,'2025-06-17 04:33:45',NULL,'2025-06-17 04:33:38','2025-06-17 04:33:45'),(27,'コーン','corn@example.com','$2y$10$fUTkj/WiXSupD6oy9A3.gOZ201tHqmzao9SknuaQIs78OMmykSgk.',NULL,NULL,'000-0000','北海道北見市田端町1234','キタミマンション12345',NULL,'2025-06-17 13:40:25',NULL,'2025-06-17 13:40:18','2025-06-17 13:40:48'),(28,'あいうえお','aiueo@example.com','$2y$10$DyPZBxTw7NG5i4wJMGn1dOQOmWa/exMrkssErsYonU0UaXwWtz1gy',NULL,NULL,'000-0000','北海道北見市田端町123',NULL,NULL,'2025-06-18 06:42:22',NULL,'2025-06-18 06:42:11','2025-06-18 06:42:27'),(29,'かきくけこ','kakikukeko@example.com','$2y$10$m2oWBzbcrstdxz6aauZjl.DM6UCWhTtRsJlzL7keTBzTJdSPBM2Ya',NULL,NULL,'000-0000','北海道北見市田端町123','キタミマンション23456',NULL,'2025-06-18 09:35:49',NULL,'2025-06-18 09:35:41','2025-06-18 09:36:03'),(30,'たちつてと','tatituteto@example.com','$2y$10$A/WQLxo9XlRn7Od60fb9JugISxH76C5/m0qwHii6wq1hIRfM63kYm',NULL,NULL,'000-0000','北海道北見市田端町1234','キタミマンション101212',NULL,'2025-06-20 04:39:06',NULL,'2025-06-20 04:35:00','2025-06-20 04:39:21'),(31,'なにぬねの','naninuneno@example.com','$2y$10$8P5RpswvseSwlFzW55bI5OFlezkxLD380lL3y6.yXp7Nsgu6YsWWm',NULL,NULL,'000-0000','北海道北見市田端町1234','千駄ヶ谷マンション10222',NULL,'2025-06-20 14:42:04',NULL,'2025-06-20 14:41:57','2025-06-20 14:42:15'),(32,'はひふへほ','hahihuheho@example.com','$2y$10$GYg6EcERHSE8v24Muo7FzeOFHlVvm8lExA.b14uydlllLaWjWySW.',NULL,NULL,'000-0000','北海道北見市田端町1234','キタミマンション101221',NULL,'2025-06-20 15:12:26',NULL,'2025-06-20 15:12:18','2025-06-20 15:12:37');
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

-- Dump completed on 2025-06-22 14:22:55
