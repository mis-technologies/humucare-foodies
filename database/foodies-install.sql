-- =====================================================================
-- Foodies — install / sample database
-- Full schema + seed data (settings, menu, item modifiers, gateways,
-- email templates, one admin). Import into a FRESH, EMPTY database.
--
-- Import:
--   mysql -u <user> -p <your_db_name> < database/foodies-install.sql
--
-- Then point .env at that DB (DB_DATABASE=<your_db_name>) and run:
--   php artisan config:clear
--
-- Admin login:  username "admin"  /  password "password"  (CHANGE IT)
--
-- Sanitised for sharing: mail SMTP username + password are blank — set
-- them in Admin > Email settings (or general_settings.mail_config).
-- AWS / payment gateway keys are NOT in the DB; they live in .env / admin.
-- =====================================================================

SET FOREIGN_KEY_CHECKS=0;
SET NAMES utf8mb4;

-- MySQL dump 10.13  Distrib 8.4.7, for macos15.7 (arm64)
--
-- Host: localhost    Database: humu-foodie
-- ------------------------------------------------------
-- Server version	8.4.7

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
-- Table structure for table `admin_notifications`
--

DROP TABLE IF EXISTS `admin_notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `admin_notifications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL DEFAULT '0',
  `title` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `read_status` tinyint NOT NULL DEFAULT '0',
  `click_url` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_notifications`
--

LOCK TABLES `admin_notifications` WRITE;
/*!40000 ALTER TABLE `admin_notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `admin_notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin_password_resets`
--

DROP TABLE IF EXISTS `admin_password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `admin_password_resets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_password_resets`
--

LOCK TABLES `admin_password_resets` WRITE;
/*!40000 ALTER TABLE `admin_password_resets` DISABLE KEYS */;
/*!40000 ALTER TABLE `admin_password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `admins` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admins`
--

LOCK TABLES `admins` WRITE;
/*!40000 ALTER TABLE `admins` DISABLE KEYS */;
INSERT INTO `admins` (`id`, `name`, `username`, `email`, `image`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES (1,'Administrator','admin','admin@foodies.test',NULL,'$2y$10$OhFF1fPjr7Xr6Giq0kqIUeF5cy8pzQAX2jaA4mkGKTwa5oe.mc/ey',NULL,'2026-07-20 11:45:10','2026-07-20 11:45:10');
/*!40000 ALTER TABLE `admins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `brands`
--

DROP TABLE IF EXISTS `brands`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `brands` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `featured` tinyint NOT NULL DEFAULT '0',
  `web_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `brands`
--

LOCK TABLES `brands` WRITE;
/*!40000 ALTER TABLE `brands` DISABLE KEYS */;
INSERT INTO `brands` (`id`, `name`, `status`, `featured`, `web_url`, `image`, `created_at`, `updated_at`) VALUES (3,'Foodies Kitchen',1,1,'#',NULL,'2026-07-22 17:50:32','2026-07-22 17:50:32');
/*!40000 ALTER TABLE `brands` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `carts`
--

DROP TABLE IF EXISTS `carts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `carts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `product_id` int DEFAULT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `price_per_liter` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `liter` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price_per_milliliter` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `milliliter` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `options` text COLLATE utf8mb4_unicode_ci,
  `options_price` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carts`
--

LOCK TABLES `carts` WRITE;
/*!40000 ALTER TABLE `carts` DISABLE KEYS */;
/*!40000 ALTER TABLE `carts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `featured` tinyint NOT NULL DEFAULT '0',
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` (`id`, `name`, `status`, `featured`, `image`, `created_at`, `updated_at`) VALUES (11,'Starters',1,1,NULL,'2026-07-22 17:50:32','2026-07-22 17:50:32'),(12,'Main Course',1,1,NULL,'2026-07-22 17:50:32','2026-07-22 17:50:32'),(13,'Desserts',1,1,NULL,'2026-07-22 17:50:32','2026-07-22 17:50:32'),(14,'Drinks',1,1,NULL,'2026-07-22 17:50:32','2026-07-22 17:50:32'),(15,'Specials',1,1,NULL,'2026-07-22 17:50:32','2026-07-22 17:50:32');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `coupons`
--

DROP TABLE IF EXISTS `coupons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `coupons` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `discount` decimal(8,2) NOT NULL DEFAULT '0.00',
  `discount_type` tinyint DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `min_order` decimal(8,2) NOT NULL DEFAULT '0.00',
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coupons`
--

LOCK TABLES `coupons` WRITE;
/*!40000 ALTER TABLE `coupons` DISABLE KEYS */;
/*!40000 ALTER TABLE `coupons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `deposits`
--

DROP TABLE IF EXISTS `deposits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `deposits` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `order_id` int DEFAULT NULL,
  `method_code` int DEFAULT NULL,
  `method_id` int DEFAULT NULL,
  `amount` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `method_currency` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `charge` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `rate` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `final_amo` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `detail` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `btc_amo` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `btc_wallet` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `trx` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `try` tinyint NOT NULL DEFAULT '0',
  `status` tinyint NOT NULL DEFAULT '0',
  `admin_feedback` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `deposits_order_id_index` (`order_id`),
  KEY `deposits_method_code_index` (`method_code`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `deposits`
--

LOCK TABLES `deposits` WRITE;
/*!40000 ALTER TABLE `deposits` DISABLE KEYS */;
/*!40000 ALTER TABLE `deposits` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `email_logs`
--

DROP TABLE IF EXISTS `email_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `email_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `mail_sender` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_from` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_to` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `email_logs`
--

LOCK TABLES `email_logs` WRITE;
/*!40000 ALTER TABLE `email_logs` DISABLE KEYS */;
INSERT INTO `email_logs` (`id`, `user_id`, `mail_sender`, `email_from`, `email_to`, `subject`, `message`, `created_at`, `updated_at`) VALUES (1,1,'smtp','Foodies ','tester@t.test','Your Foodies order {{order_no}} is confirmed','<div style=\"font-family:Arial,sans-serif;max-width:600px;margin:auto;border:1px solid #eee;border-radius:12px;overflow:hidden\"><div style=\"background:#14142e;color:#fff;padding:18px 24px;font-size:20px;font-weight:bold\">Foodies</div><div style=\"padding:24px;color:#333;line-height:1.6\"><p>Hi tester,</p><p>Thanks for your order — we are preparing it now.</p><p><b>Order:</b> QG2JD6APGZH7<br><b>Payment:</b> Order successfully done via Cash on delivery.<br><b>Subtotal:</b> GBP10.00<br><b>Delivery:</b> GBP0.00<br><b>Total:</b> GBP10.00</p><p>We will let you know when it is on its way.</p></div><div style=\"background:#f6f6fb;color:#888;padding:14px 24px;font-size:12px\">Foodies &middot; This is an automated message.</div></div>','2026-07-25 13:10:52','2026-07-25 13:10:52');
/*!40000 ALTER TABLE `email_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `email_sms_templates`
--

DROP TABLE IF EXISTS `email_sms_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `email_sms_templates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `act` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subj` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_body` text COLLATE utf8mb4_unicode_ci,
  `sms_body` text COLLATE utf8mb4_unicode_ci,
  `shortcodes` text COLLATE utf8mb4_unicode_ci,
  `email_status` tinyint NOT NULL DEFAULT '1',
  `sms_status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `email_sms_templates_act_index` (`act`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `email_sms_templates`
--

LOCK TABLES `email_sms_templates` WRITE;
/*!40000 ALTER TABLE `email_sms_templates` DISABLE KEYS */;
INSERT INTO `email_sms_templates` (`id`, `act`, `name`, `subj`, `email_body`, `sms_body`, `shortcodes`, `email_status`, `sms_status`, `created_at`, `updated_at`) VALUES (3,'ORDER_COMPLETE','Order Confirmation (customer)','Your Foodies order {{order_no}} is confirmed','<p>Hi {{user_name}},</p><p>Thanks for your order — we are preparing it now.</p><p><b>Order:</b> {{order_no}}<br><b>Payment:</b> {{method_name}}<br><b>Subtotal:</b> {{currency}}{{subtotal}}<br><b>Delivery:</b> {{currency}}{{shipping_charge}}<br><b>Total:</b> {{currency}}{{total}}</p><p>We will let you know when it is on its way.</p>','Order {{order_no}} confirmed. Total {{currency}}{{total}}. Thank you!','{\"user_name\":\"Customer name\",\"order_no\":\"Order number\",\"method_name\":\"Payment method\",\"subtotal\":\"Subtotal\",\"shipping_charge\":\"Delivery fee\",\"total\":\"Order total\",\"currency\":\"Currency\"}',1,1,'2026-07-22 17:50:32','2026-07-22 17:50:32'),(4,'ADMIN_NEW_ORDER','New Order Alert (restaurant)','NEW ORDER {{order_no}} — {{currency}}{{total}}','<p><b>A new order has come in.</b></p><p><b>Order:</b> {{order_no}}<br><b>Customer:</b> {{user_name}}<br><b>Payment:</b> {{method_name}}<br><b>Total:</b> {{currency}}{{total}}</p><p><b>Items</b><br>{{items}}</p><p><b>Deliver to</b><br>{{address}}</p>','NEW ORDER {{order_no}} {{currency}}{{total}}','{\"order_no\":\"Order number\",\"user_name\":\"Customer name\",\"method_name\":\"Payment method\",\"total\":\"Order total\",\"currency\":\"Currency\",\"items\":\"Ordered items\",\"address\":\"Delivery address\"}',1,0,'2026-07-22 17:50:32','2026-07-22 17:50:32');
/*!40000 ALTER TABLE `email_sms_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `extensions`
--

DROP TABLE IF EXISTS `extensions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `extensions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `act` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `script` text COLLATE utf8mb4_unicode_ci,
  `shortcode` text COLLATE utf8mb4_unicode_ci,
  `support` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `extensions`
--

LOCK TABLES `extensions` WRITE;
/*!40000 ALTER TABLE `extensions` DISABLE KEYS */;
/*!40000 ALTER TABLE `extensions` ENABLE KEYS */;
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
-- Table structure for table `frontends`
--

DROP TABLE IF EXISTS `frontends`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `frontends` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `data_keys` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data_values` longtext COLLATE utf8mb4_unicode_ci,
  `seo_content` text COLLATE utf8mb4_unicode_ci,
  `tempname` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `frontends`
--

LOCK TABLES `frontends` WRITE;
/*!40000 ALTER TABLE `frontends` DISABLE KEYS */;
INSERT INTO `frontends` (`id`, `data_keys`, `data_values`, `seo_content`, `tempname`, `created_at`, `updated_at`) VALUES (31,'banner.element','{\"image\":\"hero.png\",\"url\":\"#\"}',NULL,'templates.basic.','2026-07-22 17:50:32','2026-07-22 17:50:32'),(32,'contact_us.content','{\"address\":\"12 Kitchen Street, Birmingham, UK\",\"contact_email\":\"hello@foodies.test\",\"contact_number\":\"+44 7485 705519\",\"short_details\":\"We would love to hear from you.\"}',NULL,'templates.basic.','2026-07-22 17:50:32','2026-07-22 17:50:32'),(33,'footer.content','{\"subscribe_title\":\"Get the latest offers and new dishes straight to your inbox.\",\"connect_title\":\"Follow us for daily specials.\"}',NULL,'templates.basic.','2026-07-22 17:50:32','2026-07-22 17:50:32'),(34,'social_icon.element','{\"title\":\"Facebook\",\"social_icon\":\"<i class=\\\"lab la-facebook-f\\\"><\\/i>\",\"url\":\"#\"}',NULL,'templates.basic.','2026-07-22 17:50:32','2026-07-22 17:50:32'),(35,'social_icon.element','{\"title\":\"Instagram\",\"social_icon\":\"<i class=\\\"lab la-instagram\\\"><\\/i>\",\"url\":\"#\"}',NULL,'templates.basic.','2026-07-22 17:50:32','2026-07-22 17:50:32'),(36,'social_icon.element','{\"title\":\"Twitter\",\"social_icon\":\"<i class=\\\"lab la-twitter\\\"><\\/i>\",\"url\":\"#\"}',NULL,'templates.basic.','2026-07-22 17:50:32','2026-07-22 17:50:32'),(37,'service.element','{\"title\":\"Fast Delivery\",\"short_detail\":\"Hot food within 45 minutes\",\"icon\":\"las la-shipping-fast\",\"image\":\"service1.png\"}',NULL,'templates.basic.','2026-07-22 17:50:32','2026-07-22 17:50:32'),(38,'service.element','{\"title\":\"Fresh Ingredients\",\"short_detail\":\"Sourced fresh every morning\",\"icon\":\"las la-leaf\",\"image\":\"service2.png\"}',NULL,'templates.basic.','2026-07-22 17:50:32','2026-07-22 17:50:32'),(39,'service.element','{\"title\":\"24\\/7 Support\",\"short_detail\":\"We are always here to help\",\"icon\":\"las la-headset\",\"image\":\"service3.png\"}',NULL,'templates.basic.','2026-07-22 17:50:32','2026-07-22 17:50:32'),(40,'service.element','{\"title\":\"Best Prices\",\"short_detail\":\"Great value on every meal\",\"icon\":\"las la-tags\",\"image\":\"service4.png\"}',NULL,'templates.basic.','2026-07-22 17:50:32','2026-07-22 17:50:32'),(41,'policy_pages.element','{\"title\":\"Privacy Policy\",\"details\":\"Your privacy matters to us.\"}',NULL,'templates.basic.','2026-07-22 17:50:32','2026-07-22 17:50:32'),(42,'policy_pages.element','{\"title\":\"Terms of Service\",\"details\":\"Please read our terms carefully.\"}',NULL,'templates.basic.','2026-07-22 17:50:32','2026-07-22 17:50:32'),(43,'cookie.data','{\"status\":0,\"description\":\"We use cookies to improve your experience.\",\"link\":\"#\"}',NULL,'templates.basic.','2026-07-22 17:50:32','2026-07-22 17:50:32'),(44,'seo.data','{\"description\":\"Order freshly cooked, chef-made meals from Foodies and get them delivered hot to your door.\",\"keywords\":[\"food delivery\",\"jollof rice\",\"african food\",\"takeaway\",\"foodies\"],\"social_title\":\"Foodies \\u2014 The best meals for your taste buds\",\"social_description\":\"Freshly cooked, chef-made meals delivered to your door.\",\"image\":\"seo.png\",\"image_size\":\"600x315\"}',NULL,'templates.basic.','2026-07-22 17:50:32','2026-07-22 17:50:32');
/*!40000 ALTER TABLE `frontends` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `gateway_currencies`
--

DROP TABLE IF EXISTS `gateway_currencies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `gateway_currencies` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gateway_id` int DEFAULT NULL,
  `currency` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `symbol` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `method_code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gateway_alias` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `min_amount` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `max_amount` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `percent_charge` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `fixed_charge` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `rate` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gateway_parameter` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gateway_currencies`
--

LOCK TABLES `gateway_currencies` WRITE;
/*!40000 ALTER TABLE `gateway_currencies` DISABLE KEYS */;
INSERT INTO `gateway_currencies` (`id`, `name`, `gateway_id`, `currency`, `symbol`, `method_code`, `gateway_alias`, `min_amount`, `max_amount`, `percent_charge`, `fixed_charge`, `rate`, `image`, `gateway_parameter`, `created_at`, `updated_at`) VALUES (2,'PayPal',NULL,'GBP','£','102','Paypal',0.50000000,1000000.00000000,0.00000000,0.00000000,1.00000000,NULL,'{\"paypal_email\":\"\"}','2026-07-25 12:38:33','2026-07-25 12:41:21'),(3,'Bank Transfer',NULL,'GBP','£','1001','bank_transfer',0.00000000,1000000.00000000,0.00000000,0.00000000,1.00000000,NULL,'{\"sender_name\":{\"field_name\":\"sender_name\",\"field_level\":\"Account Name Used\",\"type\":\"text\",\"validation\":\"required\"},\"reference\":{\"field_name\":\"reference\",\"field_level\":\"Transfer Reference\",\"type\":\"text\",\"validation\":\"required\"}}','2026-07-25 12:38:33','2026-07-25 12:41:21'),(4,'Stripe',NULL,'GBP','£','101','StripeV3',0.50000000,1000000.00000000,0.00000000,0.00000000,1.00000000,NULL,'{\"secret_key\":\"\",\"end_point\":\"\"}','2026-07-25 12:40:49','2026-07-25 12:41:21');
/*!40000 ALTER TABLE `gateway_currencies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `gateways`
--

DROP TABLE IF EXISTS `gateways`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `gateways` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `form_id` int DEFAULT NULL,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alias` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `gateway_parameters` text COLLATE utf8mb4_unicode_ci,
  `supported_currencies` text COLLATE utf8mb4_unicode_ci,
  `crypto` tinyint NOT NULL DEFAULT '0',
  `extra` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `input_form` text COLLATE utf8mb4_unicode_ci,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gateways`
--

LOCK TABLES `gateways` WRITE;
/*!40000 ALTER TABLE `gateways` DISABLE KEYS */;
INSERT INTO `gateways` (`id`, `form_id`, `code`, `name`, `alias`, `status`, `gateway_parameters`, `supported_currencies`, `crypto`, `extra`, `created_at`, `updated_at`, `description`, `input_form`, `image`) VALUES (1,NULL,'101','Stripe','StripeV3',0,'{\"secret_key\":{\"title\":\"API Secret Key\",\"global\":true,\"value\":\"\"},\"end_point\":{\"title\":\"Webhook Signing Secret\",\"global\":true,\"value\":\"\"}}','{\"GBP\":\"GBP\",\"USD\":\"USD\",\"EUR\":\"EUR\"}',0,NULL,'2026-07-25 12:38:33','2026-07-25 12:41:21',NULL,'\"[]\"',NULL),(2,NULL,'102','PayPal','Paypal',0,'{\"paypal_email\":{\"title\":\"PayPal Business Email\",\"global\":true,\"value\":\"\"}}','{\"GBP\":\"GBP\",\"USD\":\"USD\",\"EUR\":\"EUR\"}',0,NULL,'2026-07-25 12:38:33','2026-07-25 12:38:33',NULL,'\"[]\"',NULL),(3,NULL,'1001','Bank Transfer','bank_transfer',1,'[]','[]',0,NULL,'2026-07-25 12:38:33','2026-07-25 12:38:33','Pay by bank transfer to:\n\nAccount name: Foodies Ltd\nSort code: 00-00-00\nAccount number: 00000000\n\nUse your order number as the payment reference, then submit the form below. Your order is confirmed once we verify the transfer.','\"{\\\"sender_name\\\":{\\\"field_name\\\":\\\"sender_name\\\",\\\"field_level\\\":\\\"Account Name Used\\\",\\\"type\\\":\\\"text\\\",\\\"validation\\\":\\\"required\\\"},\\\"reference\\\":{\\\"field_name\\\":\\\"reference\\\",\\\"field_level\\\":\\\"Transfer Reference\\\",\\\"type\\\":\\\"text\\\",\\\"validation\\\":\\\"required\\\"}}\"',NULL);
/*!40000 ALTER TABLE `gateways` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `general_settings`
--

DROP TABLE IF EXISTS `general_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `general_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sitename` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cur_text` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'GBP',
  `cur_sym` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '£',
  `email_from` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_template` text COLLATE utf8mb4_unicode_ci,
  `sms_api` text COLLATE utf8mb4_unicode_ci,
  `sms_body` text COLLATE utf8mb4_unicode_ci,
  `base_color` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'f5a623',
  `seo_description` text COLLATE utf8mb4_unicode_ci,
  `mail_config` text COLLATE utf8mb4_unicode_ci,
  `sms_config` text COLLATE utf8mb4_unicode_ci,
  `ev` tinyint NOT NULL DEFAULT '0',
  `en` tinyint NOT NULL DEFAULT '0',
  `sv` tinyint NOT NULL DEFAULT '0',
  `sn` tinyint NOT NULL DEFAULT '0',
  `force_ssl` tinyint NOT NULL DEFAULT '0',
  `secure_password` tinyint NOT NULL DEFAULT '0',
  `agree` tinyint NOT NULL DEFAULT '0',
  `registration` tinyint NOT NULL DEFAULT '1',
  `display_stock` tinyint NOT NULL DEFAULT '1',
  `active_template` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'basic',
  `discount` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `discount_type` tinyint NOT NULL DEFAULT '2',
  `sys_version` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1.0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `order_config` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `general_settings`
--

LOCK TABLES `general_settings` WRITE;
/*!40000 ALTER TABLE `general_settings` DISABLE KEYS */;
INSERT INTO `general_settings` (`id`, `sitename`, `cur_text`, `cur_sym`, `email_from`, `email_template`, `sms_api`, `sms_body`, `base_color`, `seo_description`, `mail_config`, `sms_config`, `ev`, `en`, `sv`, `sn`, `force_ssl`, `secure_password`, `agree`, `registration`, `display_stock`, `active_template`, `discount`, `discount_type`, `sys_version`, `created_at`, `updated_at`, `order_config`) VALUES (3,'Foodies','GBP','£','','<div style=\"font-family:Arial,sans-serif;max-width:600px;margin:auto;border:1px solid #eee;border-radius:12px;overflow:hidden\"><div style=\"background:#14142e;color:#fff;padding:18px 24px;font-size:20px;font-weight:bold\">Foodies</div><div style=\"padding:24px;color:#333;line-height:1.6\">{{message}}</div><div style=\"background:#f6f6fb;color:#888;padding:14px 24px;font-size:12px\">Foodies &middot; This is an automated message.</div></div>',NULL,NULL,'f5a623','Freshly cooked, chef-made meals delivered to your door.','{\"name\":\"smtp\",\"host\":\"smtp.gmail.com\",\"port\":\"587\",\"enc\":\"tls\",\"username\":\"\",\"password\":\"\"}','{\"name\":\"clickatell\",\"clickatell\":{\"api_key\":\"\"}}',0,1,0,0,0,0,0,1,1,'basic',0.00000000,2,'1.0','2026-07-22 17:50:32','2026-07-25 10:55:56','{\"accepting_orders\":1,\"delivery_enabled\":1,\"collection_enabled\":1,\"hours\":{\"mon\":{\"closed\":0,\"open\":\"09:00\",\"close\":\"22:00\"},\"tue\":{\"closed\":0,\"open\":\"09:00\",\"close\":\"22:00\"},\"wed\":{\"closed\":0,\"open\":\"09:00\",\"close\":\"22:00\"},\"thu\":{\"closed\":0,\"open\":\"09:00\",\"close\":\"22:00\"},\"fri\":{\"closed\":0,\"open\":\"09:00\",\"close\":\"22:00\"},\"sat\":{\"closed\":0,\"open\":\"09:00\",\"close\":\"22:00\"},\"sun\":{\"closed\":0,\"open\":\"09:00\",\"close\":\"22:00\"}}}');
/*!40000 ALTER TABLE `general_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `languages`
--

DROP TABLE IF EXISTS `languages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `languages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `icon` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_default` tinyint NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `languages`
--

LOCK TABLES `languages` WRITE;
/*!40000 ALTER TABLE `languages` DISABLE KEYS */;
INSERT INTO `languages` (`id`, `name`, `code`, `icon`, `is_default`, `created_at`, `updated_at`) VALUES (3,'English','en','',1,'2026-07-22 17:50:32','2026-07-22 17:50:32');
/*!40000 ALTER TABLE `languages` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (1,'2019_12_13_000000_create_core_tables',1),(2,'2019_12_14_000001_create_personal_access_tokens_table',1),(3,'2021_03_15_084721_create_admin_notifications_table',1),(4,'2021_05_08_103925_create_sms_gateways_table',1),(5,'2021_05_23_111859_create_email_logs_table',1),(6,'2021_12_21_152635_create_brands_table',1),(7,'2021_12_21_162332_create_categories_table',1),(8,'2021_12_22_120532_create_sub_categories_table',1),(9,'2021_12_22_161411_create_products_table',1),(10,'2022_03_12_110758_create_coupons_table',1),(11,'2022_03_12_153151_create_shipping_methods_table',1),(12,'2022_03_12_170633_create_orders_table',1),(13,'2022_03_12_171659_create_order_details_table',1),(14,'2022_03_13_143838_create_wishlists_table',1),(15,'2022_03_24_170231_create_reviews_table',1),(16,'2024_08_28_092726_create_product_price_per_liters_table',1),(17,'2024_08_28_133919_add_price_per_liter_to_carts_table',1),(18,'2024_09_01_210947_add_price_per_milliliter_to_carts_table',1),(19,'2024_09_01_213052_create_product_price_per_milliliters_table',1),(20,'2024_09_07_053530_create_volume_ranges_table',1),(21,'2024_09_07_061917_create_product_price_per_volumes_table',1),(22,'2026_07_17_000000_add_missing_product_columns',1),(23,'2026_07_17_000001_add_discount_type_to_products',2),(24,'2026_07_22_000000_add_missing_order_and_product_columns',3),(25,'2026_07_22_000001_create_option_tables',4),(26,'2026_07_22_000002_create_email_sms_templates_table',5),(27,'2026_07_22_000003_add_product_fk_to_option_pivot',6),(28,'2026_07_23_000000_add_order_availability_config',7),(29,'2026_07_23_000001_fix_email_logs_columns',8),(30,'2026_07_24_000000_fix_gateway_columns',9),(31,'2026_07_24_000001_add_image_to_gateways',10),(32,'2026_07_24_000002_add_order_columns_to_deposits',11),(33,'2026_07_24_000003_fix_deposit_method_currency_type',12);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `option_groups`
--

DROP TABLE IF EXISTS `option_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `option_groups` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('single','multi') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'single',
  `is_required` tinyint NOT NULL DEFAULT '0',
  `min_select` tinyint unsigned NOT NULL DEFAULT '0',
  `max_select` tinyint unsigned NOT NULL DEFAULT '1',
  `sort_order` int NOT NULL DEFAULT '0',
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `option_groups`
--

LOCK TABLES `option_groups` WRITE;
/*!40000 ALTER TABLE `option_groups` DISABLE KEYS */;
INSERT INTO `option_groups` (`id`, `name`, `type`, `is_required`, `min_select`, `max_select`, `sort_order`, `status`, `created_at`, `updated_at`) VALUES (1,'Choose your size','single',1,1,1,1,1,'2026-07-21 23:03:34','2026-07-21 23:03:34'),(2,'Add extras','multi',0,0,3,2,1,'2026-07-21 23:03:34','2026-07-21 23:03:34');
/*!40000 ALTER TABLE `option_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `options`
--

DROP TABLE IF EXISTS `options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `options` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `option_group_id` bigint unsigned NOT NULL,
  `name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `sort_order` int NOT NULL DEFAULT '0',
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `options_option_group_id_index` (`option_group_id`),
  CONSTRAINT `options_option_group_id_foreign` FOREIGN KEY (`option_group_id`) REFERENCES `option_groups` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `options`
--

LOCK TABLES `options` WRITE;
/*!40000 ALTER TABLE `options` DISABLE KEYS */;
INSERT INTO `options` (`id`, `option_group_id`, `name`, `price`, `sort_order`, `status`, `created_at`, `updated_at`) VALUES (1,1,'Regular',0.00000000,1,1,'2026-07-21 23:03:34','2026-07-21 23:03:34'),(2,1,'Large',2.50000000,2,1,'2026-07-21 23:03:34','2026-07-21 23:03:34'),(3,2,'Extra plantain',1.50000000,0,1,'2026-07-21 23:03:34','2026-07-21 23:03:34'),(4,2,'Extra sauce',0.75000000,0,1,'2026-07-21 23:03:34','2026-07-21 23:03:34');
/*!40000 ALTER TABLE `options` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_details`
--

DROP TABLE IF EXISTS `order_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_details` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL DEFAULT '0',
  `product_id` int NOT NULL DEFAULT '0',
  `quantity` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `price` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `options` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_details`
--

LOCK TABLES `order_details` WRITE;
/*!40000 ALTER TABLE `order_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `order_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL DEFAULT '0',
  `order_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subtotal` decimal(8,2) NOT NULL DEFAULT '0.00',
  `discount` decimal(8,2) NOT NULL DEFAULT '0.00',
  `shipping_charge` decimal(8,2) NOT NULL DEFAULT '0.00',
  `total` decimal(8,2) NOT NULL DEFAULT '0.00',
  `coupon_id` int NOT NULL DEFAULT '0',
  `shipping_id` int NOT NULL DEFAULT '0',
  `address` text COLLATE utf8mb4_unicode_ci,
  `payment_type` tinyint NOT NULL DEFAULT '2',
  `fulfilment_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'delivery',
  `payment_status` tinyint NOT NULL DEFAULT '0',
  `payment_url` text COLLATE utf8mb4_unicode_ci,
  `order_status` tinyint NOT NULL DEFAULT '0',
  `notification` tinyint NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pages`
--

DROP TABLE IF EXISTS `pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tempname` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `secs` text COLLATE utf8mb4_unicode_ci,
  `is_default` tinyint NOT NULL DEFAULT '0',
  `seo_content` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pages`
--

LOCK TABLES `pages` WRITE;
/*!40000 ALTER TABLE `pages` DISABLE KEYS */;
INSERT INTO `pages` (`id`, `name`, `slug`, `tempname`, `secs`, `is_default`, `seo_content`, `created_at`, `updated_at`) VALUES (5,'Home','home','templates.basic.','[]',1,NULL,'2026-07-22 17:50:32','2026-07-22 17:50:32'),(6,'About Us','about-us','templates.basic.','[]',0,NULL,'2026-07-22 17:50:32','2026-07-22 17:50:32');
/*!40000 ALTER TABLE `pages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_resets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
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
-- Table structure for table `product_galleries`
--

DROP TABLE IF EXISTS `product_galleries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_galleries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int DEFAULT NULL,
  `image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_galleries`
--

LOCK TABLES `product_galleries` WRITE;
/*!40000 ALTER TABLE `product_galleries` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_galleries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_option_group`
--

DROP TABLE IF EXISTS `product_option_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_option_group` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint unsigned NOT NULL,
  `option_group_id` bigint unsigned NOT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_option_group_product_id_option_group_id_unique` (`product_id`,`option_group_id`),
  KEY `product_option_group_product_id_index` (`product_id`),
  KEY `product_option_group_option_group_id_index` (`option_group_id`),
  CONSTRAINT `product_option_group_option_group_id_foreign` FOREIGN KEY (`option_group_id`) REFERENCES `option_groups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_option_group_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_option_group`
--

LOCK TABLES `product_option_group` WRITE;
/*!40000 ALTER TABLE `product_option_group` DISABLE KEYS */;
INSERT INTO `product_option_group` (`id`, `product_id`, `option_group_id`, `sort_order`) VALUES (7,30,1,0),(8,30,2,0);
/*!40000 ALTER TABLE `product_option_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_price_per_liters`
--

DROP TABLE IF EXISTS `product_price_per_liters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_price_per_liters` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `price` decimal(8,2) NOT NULL DEFAULT '0.00',
  `product_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `liter` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_price_per_liters`
--

LOCK TABLES `product_price_per_liters` WRITE;
/*!40000 ALTER TABLE `product_price_per_liters` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_price_per_liters` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_price_per_milliliters`
--

DROP TABLE IF EXISTS `product_price_per_milliliters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_price_per_milliliters` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `price` decimal(8,2) NOT NULL DEFAULT '0.00',
  `product_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `milliliter` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_price_per_milliliters`
--

LOCK TABLES `product_price_per_milliliters` WRITE;
/*!40000 ALTER TABLE `product_price_per_milliliters` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_price_per_milliliters` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_price_per_volumes`
--

DROP TABLE IF EXISTS `product_price_per_volumes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_price_per_volumes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `volume` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(8,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_price_per_volumes`
--

LOCK TABLES `product_price_per_volumes` WRITE;
/*!40000 ALTER TABLE `product_price_per_volumes` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_price_per_volumes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int DEFAULT NULL,
  `subcategory_id` int DEFAULT NULL,
  `brand_id` int DEFAULT NULL,
  `name` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_sku` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(8,2) NOT NULL DEFAULT '0.00',
  `quantity` int NOT NULL DEFAULT '0',
  `club_point` int DEFAULT NULL,
  `discount` decimal(8,2) DEFAULT NULL,
  `discount_type` tinyint NOT NULL DEFAULT '1',
  `min_order` tinyint DEFAULT NULL,
  `unit` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hot_deals` tinyint NOT NULL DEFAULT '0',
  `today_deals` tinyint NOT NULL DEFAULT '0',
  `sale_count` int NOT NULL DEFAULT '0',
  `avg_rate` decimal(8,2) NOT NULL DEFAULT '0.00',
  `featured_product` tinyint NOT NULL DEFAULT '0',
  `status` tinyint NOT NULL DEFAULT '1',
  `digital_item` tinyint NOT NULL DEFAULT '0',
  `file_type` tinyint DEFAULT NULL,
  `digi_file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `digi_link` text COLLATE utf8mb4_unicode_ci,
  `description` text COLLATE utf8mb4_unicode_ci,
  `summary` text COLLATE utf8mb4_unicode_ci,
  `features` text COLLATE utf8mb4_unicode_ci,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `files` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_product_sku_unique` (`product_sku`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` (`id`, `category_id`, `subcategory_id`, `brand_id`, `name`, `slug`, `product_sku`, `product_id`, `price`, `quantity`, `club_point`, `discount`, `discount_type`, `min_order`, `unit`, `hot_deals`, `today_deals`, `sale_count`, `avg_rate`, `featured_product`, `status`, `digital_item`, `file_type`, `digi_file`, `digi_link`, `description`, `summary`, `features`, `image`, `files`, `created_at`, `updated_at`) VALUES (27,11,11,3,'Suya Skewers','suya-skewers',NULL,'DGYHWVNV',8.00,43,NULL,1.50,1,1,'plate',1,0,35,5.00,1,1,0,NULL,NULL,NULL,'Freshly prepared Suya Skewers, cooked to order by our chefs.',NULL,'[]',NULL,'[]','2026-07-22 17:50:32','2026-07-25 12:50:20'),(28,11,11,3,'Puff Puff','puff-puff',NULL,'IQSFMYFE',3.50,50,NULL,0.00,1,1,'plate',0,0,28,4.50,0,1,0,NULL,NULL,NULL,'Freshly prepared Puff Puff, cooked to order by our chefs.',NULL,'[]',NULL,'[]','2026-07-22 17:50:32','2026-07-22 17:50:32'),(29,11,11,3,'Spring Rolls','spring-rolls',NULL,'0LGDGPHR',5.00,50,NULL,1.00,1,1,'plate',1,0,21,4.00,1,1,0,NULL,NULL,NULL,'Freshly prepared Spring Rolls, cooked to order by our chefs.',NULL,'[]',NULL,'[]','2026-07-22 17:50:32','2026-07-22 17:50:32'),(30,12,12,3,'Jollof Rice','jollof-rice',NULL,'HA7UT0I8',10.00,49,NULL,4.00,1,1,'plate',1,0,14,5.00,0,1,0,NULL,NULL,NULL,'Freshly prepared Jollof Rice, cooked to order by our chefs.',NULL,'[]',NULL,'[]','2026-07-22 17:50:32','2026-07-25 13:10:51'),(31,12,12,3,'Egusi & Pounded Yam','egusi-pounded-yam',NULL,'KQ17I7NO',9.50,50,NULL,0.00,1,1,'plate',0,0,7,4.50,1,1,0,NULL,NULL,NULL,'Freshly prepared Egusi & Pounded Yam, cooked to order by our chefs.',NULL,'[]',NULL,'[]','2026-07-22 17:50:32','2026-07-22 17:50:32'),(32,12,12,3,'Grilled Chicken Platter','grilled-chicken-platter',NULL,'5NLH0ZST',13.00,50,NULL,2.00,1,1,'plate',1,0,35,5.00,0,1,0,NULL,NULL,NULL,'Freshly prepared Grilled Chicken Platter, cooked to order by our chefs.',NULL,'[]',NULL,'[]','2026-07-22 17:50:32','2026-07-22 17:50:32'),(33,12,12,3,'Seafood Okra','seafood-okra',NULL,'UQ23BTES',12.50,50,NULL,0.00,1,1,'plate',0,0,28,4.50,1,1,0,NULL,NULL,NULL,'Freshly prepared Seafood Okra, cooked to order by our chefs.',NULL,'[]',NULL,'[]','2026-07-22 17:50:32','2026-07-22 17:50:32'),(34,13,13,3,'Chin Chin','chin-chin',NULL,'SKIJRKR6',3.00,50,NULL,0.00,1,1,'plate',0,0,21,4.00,0,1,0,NULL,NULL,NULL,'Freshly prepared Chin Chin, cooked to order by our chefs.',NULL,'[]',NULL,'[]','2026-07-22 17:50:32','2026-07-22 17:50:32'),(35,13,13,3,'Coconut Cake','coconut-cake',NULL,'S98QOEGI',6.00,50,NULL,1.50,1,1,'plate',1,0,14,5.00,1,1,0,NULL,NULL,NULL,'Freshly prepared Coconut Cake, cooked to order by our chefs.',NULL,'[]',NULL,'[]','2026-07-22 17:50:32','2026-07-22 17:50:32'),(36,14,14,3,'Chapman','chapman',NULL,'NVVAJN2B',3.50,50,NULL,0.00,1,1,'plate',0,0,7,4.50,0,1,0,NULL,NULL,NULL,'Freshly prepared Chapman, cooked to order by our chefs.',NULL,'[]',NULL,'[]','2026-07-22 17:50:32','2026-07-22 17:50:32'),(37,14,14,3,'Zobo Punch','zobo-punch',NULL,'1FZ9SGCE',4.00,50,NULL,1.00,1,1,'plate',1,0,35,5.00,1,1,0,NULL,NULL,NULL,'Freshly prepared Zobo Punch, cooked to order by our chefs.',NULL,'[]',NULL,'[]','2026-07-22 17:50:32','2026-07-22 17:50:32'),(38,15,15,3,'Party Barbecue Platter','party-barbecue-platter',NULL,'SZMTQJDF',30.00,50,NULL,6.00,1,1,'plate',1,0,28,4.50,0,1,0,NULL,NULL,NULL,'Freshly prepared Party Barbecue Platter, cooked to order by our chefs.',NULL,'[]',NULL,'[]','2026-07-22 17:50:32','2026-07-22 17:50:32'),(39,15,15,3,'Weekend Feast Box','weekend-feast-box',NULL,'WXKN7NYP',40.00,50,NULL,8.00,1,1,'plate',1,0,21,4.00,1,1,0,NULL,NULL,NULL,'Freshly prepared Weekend Feast Box, cooked to order by our chefs.',NULL,'[]',NULL,'[]','2026-07-22 17:50:32','2026-07-22 17:50:32');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reviews` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL DEFAULT '0',
  `product_id` int NOT NULL DEFAULT '0',
  `stars` tinyint NOT NULL DEFAULT '0',
  `review_comment` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reviews`
--

LOCK TABLES `reviews` WRITE;
/*!40000 ALTER TABLE `reviews` DISABLE KEYS */;
/*!40000 ALTER TABLE `reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shipping_methods`
--

DROP TABLE IF EXISTS `shipping_methods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `shipping_methods` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(8,2) DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shipping_methods`
--

LOCK TABLES `shipping_methods` WRITE;
/*!40000 ALTER TABLE `shipping_methods` DISABLE KEYS */;
INSERT INTO `shipping_methods` (`id`, `name`, `price`, `status`, `created_at`, `updated_at`) VALUES (5,'Standard Delivery',3.99,1,'2026-07-22 17:50:32','2026-07-22 17:50:32'),(6,'Express Delivery',6.99,1,'2026-07-22 17:50:32','2026-07-22 17:50:32');
/*!40000 ALTER TABLE `shipping_methods` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sms_gateways`
--

DROP TABLE IF EXISTS `sms_gateways`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sms_gateways` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(70) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alias` varchar(70) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `credentials` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sms_gateways`
--

LOCK TABLES `sms_gateways` WRITE;
/*!40000 ALTER TABLE `sms_gateways` DISABLE KEYS */;
/*!40000 ALTER TABLE `sms_gateways` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sub_categories`
--

DROP TABLE IF EXISTS `sub_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sub_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int DEFAULT NULL,
  `name` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `featured` tinyint NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sub_categories`
--

LOCK TABLES `sub_categories` WRITE;
/*!40000 ALTER TABLE `sub_categories` DISABLE KEYS */;
INSERT INTO `sub_categories` (`id`, `category_id`, `name`, `status`, `featured`, `created_at`, `updated_at`) VALUES (11,11,'Starters',1,1,'2026-07-22 17:50:32','2026-07-22 17:50:32'),(12,12,'Main Course',1,1,'2026-07-22 17:50:32','2026-07-22 17:50:32'),(13,13,'Desserts',1,1,'2026-07-22 17:50:32','2026-07-22 17:50:32'),(14,14,'Drinks',1,1,'2026-07-22 17:50:32','2026-07-22 17:50:32'),(15,15,'Specials',1,1,'2026-07-22 17:50:32','2026-07-22 17:50:32');
/*!40000 ALTER TABLE `sub_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subscribers`
--

DROP TABLE IF EXISTS `subscribers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `subscribers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subscribers`
--

LOCK TABLES `subscribers` WRITE;
/*!40000 ALTER TABLE `subscribers` DISABLE KEYS */;
/*!40000 ALTER TABLE `subscribers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `support_attachments`
--

DROP TABLE IF EXISTS `support_attachments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `support_attachments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `support_message_id` int DEFAULT NULL,
  `attachment` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `support_attachments`
--

LOCK TABLES `support_attachments` WRITE;
/*!40000 ALTER TABLE `support_attachments` DISABLE KEYS */;
/*!40000 ALTER TABLE `support_attachments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `support_messages`
--

DROP TABLE IF EXISTS `support_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `support_messages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `support_ticket_id` int DEFAULT NULL,
  `admin_id` int DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `support_messages`
--

LOCK TABLES `support_messages` WRITE;
/*!40000 ALTER TABLE `support_messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `support_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `support_tickets`
--

DROP TABLE IF EXISTS `support_tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `support_tickets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ticket` int DEFAULT NULL,
  `subject` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '0',
  `priority` tinyint NOT NULL DEFAULT '1',
  `last_reply` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `support_tickets`
--

LOCK TABLES `support_tickets` WRITE;
/*!40000 ALTER TABLE `support_tickets` DISABLE KEYS */;
/*!40000 ALTER TABLE `support_tickets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transactions`
--

DROP TABLE IF EXISTS `transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `transactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `amount` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `charge` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `post_balance` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `trx_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `trx` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `details` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remark` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transactions`
--

LOCK TABLES `transactions` WRITE;
/*!40000 ALTER TABLE `transactions` DISABLE KEYS */;
/*!40000 ALTER TABLE `transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_logins`
--

DROP TABLE IF EXISTS `user_logins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_logins` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `user_ip` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country_code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `longitude` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `browser` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `os` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_logins`
--

LOCK TABLES `user_logins` WRITE;
/*!40000 ALTER TABLE `user_logins` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_logins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `firstname` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lastname` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country_code` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `balance` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `ref_by` int NOT NULL DEFAULT '0',
  `ver_code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ver_code_send_at` timestamp NULL DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `ev` tinyint NOT NULL DEFAULT '1',
  `sv` tinyint NOT NULL DEFAULT '1',
  `ts` tinyint NOT NULL DEFAULT '0',
  `tv` tinyint NOT NULL DEFAULT '1',
  `tsc` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` (`id`, `firstname`, `lastname`, `username`, `email`, `country_code`, `mobile`, `password`, `image`, `address`, `balance`, `ref_by`, `ver_code`, `ver_code_send_at`, `status`, `ev`, `sv`, `ts`, `tv`, `tsc`, `email_verified_at`, `remember_token`, `created_at`, `updated_at`) VALUES (1,'Tess','Ter','tester','tester@t.test',NULL,NULL,'$2y$10$OOr6zzEdNFqts3/CP1fCGegUt4dGHkIh32qQeJ4oSyzQiNYayi82K',NULL,NULL,0.00000000,0,NULL,NULL,1,1,1,0,1,NULL,NULL,NULL,'2026-07-25 10:25:45','2026-07-25 10:25:45');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `volume_ranges`
--

DROP TABLE IF EXISTS `volume_ranges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `volume_ranges` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `volume` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `volume_gradiation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'milli-liters',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `volume_ranges`
--

LOCK TABLES `volume_ranges` WRITE;
/*!40000 ALTER TABLE `volume_ranges` DISABLE KEYS */;
/*!40000 ALTER TABLE `volume_ranges` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wishlists`
--

DROP TABLE IF EXISTS `wishlists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wishlists` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL DEFAULT '0',
  `session_id` int unsigned NOT NULL DEFAULT '0',
  `product_id` int unsigned NOT NULL DEFAULT '0',
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wishlists`
--

LOCK TABLES `wishlists` WRITE;
/*!40000 ALTER TABLE `wishlists` DISABLE KEYS */;
/*!40000 ALTER TABLE `wishlists` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `withdraw_methods`
--

DROP TABLE IF EXISTS `withdraw_methods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `withdraw_methods` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `min_limit` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `max_limit` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `fixed_charge` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `rate` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `percent_charge` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `currency` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `status` tinyint NOT NULL DEFAULT '1',
  `user_data` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `withdraw_methods`
--

LOCK TABLES `withdraw_methods` WRITE;
/*!40000 ALTER TABLE `withdraw_methods` DISABLE KEYS */;
/*!40000 ALTER TABLE `withdraw_methods` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `withdrawals`
--

DROP TABLE IF EXISTS `withdrawals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `withdrawals` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `method_id` int DEFAULT NULL,
  `amount` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `currency` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `rate` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `charge` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `trx` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `final_amount` decimal(28,8) NOT NULL DEFAULT '0.00000000',
  `withdraw_information` text COLLATE utf8mb4_unicode_ci,
  `status` tinyint NOT NULL DEFAULT '0',
  `admin_feedback` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `withdrawals`
--

LOCK TABLES `withdrawals` WRITE;
/*!40000 ALTER TABLE `withdrawals` DISABLE KEYS */;
/*!40000 ALTER TABLE `withdrawals` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-07-25 16:31:19
SET FOREIGN_KEY_CHECKS=1;
