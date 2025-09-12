-- MySQL dump 10.13  Distrib 5.7.44, for Linux (x86_64)
--
-- Host: localhost    Database: elearning_db
-- ------------------------------------------------------
-- Server version	5.7.44

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `answers`
--

DROP TABLE IF EXISTS `answers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `answers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `question_id` bigint(20) unsigned NOT NULL,
  `text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_correct` tinyint(1) NOT NULL DEFAULT '0',
  `order` int(11) NOT NULL DEFAULT '0',
  `explanation` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `answers_question_id_foreign` (`question_id`),
  CONSTRAINT `answers_question_id_foreign` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `answers`
--

LOCK TABLES `answers` WRITE;
/*!40000 ALTER TABLE `answers` DISABLE KEYS */;
/*!40000 ALTER TABLE `answers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `checkouts`
--

DROP TABLE IF EXISTS `checkouts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `checkouts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cart_data` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `student_id` bigint(20) NOT NULL,
  `txnid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 active, 0 inactive',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `checkouts`
--

LOCK TABLES `checkouts` WRITE;
/*!40000 ALTER TABLE `checkouts` DISABLE KEYS */;
/*!40000 ALTER TABLE `checkouts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `coupons`
--

DROP TABLE IF EXISTS `coupons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `coupons` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `discount` decimal(8,2) NOT NULL,
  `valid_from` date DEFAULT NULL,
  `valid_until` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `coupons_code_unique` (`code`)
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
-- Table structure for table `course_categories`
--

DROP TABLE IF EXISTS `course_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `course_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `category_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=>active 2=>inactive',
  `category_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `course_categories`
--

LOCK TABLES `course_categories` WRITE;
/*!40000 ALTER TABLE `course_categories` DISABLE KEYS */;
INSERT INTO `course_categories` VALUES (1,NULL,1,'category-games.jpg','2025-09-11 06:25:04','2025-09-11 06:25:04',NULL),(2,NULL,1,'category-data.jpg','2025-09-11 06:25:04','2025-09-11 06:25:04',NULL),(3,NULL,1,'category-database.jpg','2025-09-11 06:25:04','2025-09-11 06:25:04',NULL),(4,NULL,1,'category-ai.jpg','2025-09-11 06:25:04','2025-09-11 06:25:04',NULL),(5,NULL,1,'category-ai.jpg','2025-09-11 06:25:04','2025-09-11 06:25:04',NULL),(6,NULL,1,'category-design.jpg','2025-09-11 06:25:04','2025-09-11 06:25:04',NULL),(7,NULL,0,'category-web.jpg','2025-09-11 06:25:04','2025-09-11 06:25:04',NULL),(8,NULL,1,'category-programming.jpg','2025-09-11 06:25:04','2025-09-11 06:25:04',NULL),(9,NULL,0,'category-games.jpg','2025-09-11 06:25:04','2025-09-11 06:25:04',NULL),(10,NULL,0,'category-web.jpg','2025-09-11 06:25:04','2025-09-11 06:25:04',NULL),(11,NULL,0,'https://via.placeholder.com/400x300.png/00bbaa?text=business+fugit','2025-09-11 06:25:04','2025-09-11 06:25:04',NULL);
/*!40000 ALTER TABLE `course_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `course_categories_translations`
--

DROP TABLE IF EXISTS `course_categories_translations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `course_categories_translations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `course_category_id` bigint(20) unsigned NOT NULL,
  `locale` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `course_categories_translations_course_category_id_locale_unique` (`course_category_id`,`locale`),
  CONSTRAINT `course_categories_translations_course_category_id_foreign` FOREIGN KEY (`course_category_id`) REFERENCES `course_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `course_categories_translations`
--

LOCK TABLES `course_categories_translations` WRITE;
/*!40000 ALTER TABLE `course_categories_translations` DISABLE KEYS */;
INSERT INTO `course_categories_translations` VALUES (1,1,'en','Web Development','2025-09-11 06:25:04','2025-09-11 06:25:04'),(2,1,'ru','Веб-разработка','2025-09-11 06:25:04','2025-09-11 06:25:04'),(3,1,'ka','ვებ-განვითარება','2025-09-11 06:25:04','2025-09-11 06:25:04'),(4,2,'en','Mobile Development','2025-09-11 06:25:04','2025-09-11 06:25:04'),(5,2,'ru','Мобильная разработка','2025-09-11 06:25:04','2025-09-11 06:25:04'),(6,2,'ka','მობილური განვითარება','2025-09-11 06:25:04','2025-09-11 06:25:04'),(7,3,'en','Data Science','2025-09-11 06:25:04','2025-09-11 06:25:04'),(8,3,'ru','Наука о данных','2025-09-11 06:25:04','2025-09-11 06:25:04'),(9,3,'ka','მონაცემთა მეცნიერება','2025-09-11 06:25:04','2025-09-11 06:25:04'),(10,4,'en','Programming','2025-09-11 06:25:04','2025-09-11 06:25:04'),(11,4,'ru','Программирование','2025-09-11 06:25:04','2025-09-11 06:25:04'),(12,4,'ka','პროგრამირება','2025-09-11 06:25:04','2025-09-11 06:25:04'),(13,5,'en','Design','2025-09-11 06:25:04','2025-09-11 06:25:04'),(14,5,'ru','Дизайн','2025-09-11 06:25:04','2025-09-11 06:25:04'),(15,5,'ka','დიზაინი','2025-09-11 06:25:04','2025-09-11 06:25:04'),(16,6,'en','Network Administration','2025-09-11 06:25:04','2025-09-11 06:25:04'),(17,6,'ru','Разработка игр','2025-09-11 06:25:04','2025-09-11 06:25:04'),(18,6,'ka','DevOps','2025-09-11 06:25:04','2025-09-11 06:25:04'),(19,7,'en','Cybersecurity','2025-09-11 06:25:04','2025-09-11 06:25:04'),(20,7,'ru','Администрирование сетей','2025-09-11 06:25:04','2025-09-11 06:25:04'),(21,7,'ka','თამაშების განვითარება','2025-09-11 06:25:04','2025-09-11 06:25:04'),(22,8,'en','Machine Learning','2025-09-11 06:25:04','2025-09-11 06:25:04'),(23,8,'ru','Администрирование сетей','2025-09-11 06:25:04','2025-09-11 06:25:04'),(24,8,'ka','მანქანური სწავლა','2025-09-11 06:25:04','2025-09-11 06:25:04'),(25,9,'en','Cloud Computing','2025-09-11 06:25:04','2025-09-11 06:25:04'),(26,9,'ru','Программная инженерия','2025-09-11 06:25:04','2025-09-11 06:25:04'),(27,9,'ka','კიბერუსაფრთხოება','2025-09-11 06:25:04','2025-09-11 06:25:04'),(28,10,'en','UI/UX Design','2025-09-11 06:25:04','2025-09-11 06:25:04'),(29,10,'ru','Машинное обучение','2025-09-11 06:25:04','2025-09-11 06:25:04'),(30,10,'ka','თამაშების განვითარება','2025-09-11 06:25:04','2025-09-11 06:25:04'),(31,11,'en','nihil Category','2025-09-11 06:25:04','2025-09-11 06:25:04'),(32,11,'ru','placeat Категория','2025-09-11 06:25:04','2025-09-11 06:25:04'),(33,11,'ka','vero კატეგორია','2025-09-11 06:25:04','2025-09-11 06:25:04');
/*!40000 ALTER TABLE `course_categories_translations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `course_translations`
--

DROP TABLE IF EXISTS `course_translations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `course_translations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `course_id` bigint(20) unsigned NOT NULL,
  `locale` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `prerequisites` text COLLATE utf8mb4_unicode_ci,
  `keywords` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `course_translations_course_id_locale_unique` (`course_id`,`locale`),
  CONSTRAINT `course_translations_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `course_translations`
--

LOCK TABLES `course_translations` WRITE;
/*!40000 ALTER TABLE `course_translations` DISABLE KEYS */;
INSERT INTO `course_translations` VALUES (1,1,'en','Data Science-ის მასტერკლასი','Voluptas quia officia dolorum maxime ullam accusamus officiis. Repellat odio voluptatum suscipit dolorum. Quis hic magni aut quos omnis quaerat consequuntur. Autem labore officia error aut quod non itaque sit. Et blanditiis est nisi perspiciatis ad.\n\nSit at atque aliquid occaecati. Nihil beatae quod quidem est neque. Et aut sint soluta. Quaerat facilis ut harum et est debitis.\n\nCommodi nobis voluptate qui. Hic magnam repudiandae totam consequatur. Molestias et ut et omnis. Eveniet unde aliquid qui saepe harum. ისწავლეთ ინდუსტრიის ექსპერტებისგან და ააგეთ რეალური პროექტები.','პროგრამირების საბაზისო ცოდნა. გაეცანით veritatis.','კოდირება, ვებ-განვითარება, პროგრამირება','2025-09-11 06:25:04','2025-09-11 06:25:04'),(2,1,'ru','Data Science-ის მასტერკლასი','Molestias doloremque necessitatibus a pariatur aut harum. Minima consectetur vel quam doloribus ut animi officia. Reiciendis iusto sunt commodi perferendis. Corporis ullam dolorem id reiciendis deleniti. Veniam sapiente id enim neque et dolores est.\n\nAdipisci aut velit nisi fugiat. Aut quibusdam id corrupti debitis est officiis ut.\n\nQuisquam voluptas molestiae quo commodi veniam magnam doloribus consequatur. Eos aliquam quis ipsam soluta odit enim delectus. Omnis nisi molestias blanditiis et eveniet. ისწავლეთ ინდუსტრიის ექსპერტებისგან და ააგეთ რეალური პროექტები.','პროგრამირების საბაზისო ცოდნა. გაეცანით alias.','სწავლა, ვებ-განვითარება, ტექნოლოგიები','2025-09-11 06:25:04','2025-09-11 06:25:04'),(3,1,'ka','Основы UX/UI дизайна','Quos voluptates voluptates voluptatem. Voluptas quam pariatur aut omnis hic debitis. Atque voluptatibus id quas praesentium molestias quia est id.\n\nQuae porro ut adipisci deserunt. Qui eos aut dolore ut placeat eos laboriosam est. Aut quis ipsa eveniet suscipit enim at. Rem ipsum eos dolor.\n\nIure nisi odit a autem odit adipisci totam. Voluptatum iusto perspiciatis est vel repellat non voluptas. Enim ut natus explicabo quaerat. Fuga at laborum possimus ipsam. Учитесь у экспертов отрасли и создавайте реальные проекты.','Базовые знания программирования. Знакомство с voluptate.','обучение, программирование, кодирование','2025-09-11 06:25:04','2025-09-11 06:25:04');
/*!40000 ALTER TABLE `course_translations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `courses`
--

DROP TABLE IF EXISTS `courses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `courses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '0 pending, 1 inactive, 2 active',
  `title` json DEFAULT NULL,
  `description` json DEFAULT NULL,
  `prerequisites` json DEFAULT NULL,
  `keywords` json DEFAULT NULL,
  `course_category_id` bigint(20) unsigned NOT NULL,
  `instructor_id` bigint(20) unsigned NOT NULL,
  `courseType` enum('free','paid','subscription') COLLATE utf8mb4_unicode_ci NOT NULL,
  `coursePrice` decimal(10,2) NOT NULL DEFAULT '0.00',
  `courseOldPrice` decimal(10,2) DEFAULT NULL,
  `subscription_price` decimal(10,2) DEFAULT NULL,
  `start_from` date NOT NULL,
  `duration` int(11) NOT NULL,
  `lesson` int(11) NOT NULL,
  `course_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `thumbnail_video_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tag` enum('popular','featured','upcoming') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `thumbnail_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `thumbnail_video_file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `courses_course_category_id_foreign` (`course_category_id`),
  KEY `courses_instructor_id_foreign` (`instructor_id`),
  CONSTRAINT `courses_course_category_id_foreign` FOREIGN KEY (`course_category_id`) REFERENCES `course_categories` (`id`),
  CONSTRAINT `courses_instructor_id_foreign` FOREIGN KEY (`instructor_id`) REFERENCES `instructors` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `courses`
--

LOCK TABLES `courses` WRITE;
/*!40000 ALTER TABLE `courses` DISABLE KEYS */;
INSERT INTO `courses` VALUES (1,1,NULL,NULL,NULL,NULL,11,1,'subscription',483.73,787.77,14.31,'2026-05-23',9,23,'CRS1264','http://zboncak.org/','popular','https://via.placeholder.com/800x600.png/00cc22?text=education+illo','https://via.placeholder.com/400x300.png/004422?text=education+ut','/tmp/faker01prfnpe8672byCDLC9','2025-09-11 06:25:04','2025-09-11 06:25:04',NULL);
/*!40000 ALTER TABLE `courses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `discussions`
--

DROP TABLE IF EXISTS `discussions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `discussions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `course_id` bigint(20) unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `discussions_user_id_index` (`user_id`),
  KEY `discussions_course_id_index` (`course_id`),
  CONSTRAINT `discussions_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `discussions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `discussions`
--

LOCK TABLES `discussions` WRITE;
/*!40000 ALTER TABLE `discussions` DISABLE KEYS */;
/*!40000 ALTER TABLE `discussions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `enrollments`
--

DROP TABLE IF EXISTS `enrollments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `enrollments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) unsigned NOT NULL,
  `course_id` bigint(20) unsigned NOT NULL,
  `enrollment_date` timestamp NOT NULL DEFAULT '2025-09-11 06:24:57',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `enrollments_student_id_index` (`student_id`),
  KEY `enrollments_course_id_index` (`course_id`),
  CONSTRAINT `enrollments_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `enrollments_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `enrollments`
--

LOCK TABLES `enrollments` WRITE;
/*!40000 ALTER TABLE `enrollments` DISABLE KEYS */;
/*!40000 ALTER TABLE `enrollments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `events` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `topic` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `goal` text COLLATE utf8mb4_unicode_ci,
  `hosted_by` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `events`
--

LOCK TABLES `events` WRITE;
/*!40000 ALTER TABLE `events` DISABLE KEYS */;
/*!40000 ALTER TABLE `events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `events_translations`
--

DROP TABLE IF EXISTS `events_translations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `events_translations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` bigint(20) unsigned NOT NULL,
  `locale` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `topic` text COLLATE utf8mb4_unicode_ci,
  `goal` text COLLATE utf8mb4_unicode_ci,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hosted_by` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `events_translations_event_id_locale_unique` (`event_id`,`locale`),
  CONSTRAINT `events_translations_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `events_translations`
--

LOCK TABLES `events_translations` WRITE;
/*!40000 ALTER TABLE `events_translations` DISABLE KEYS */;
/*!40000 ALTER TABLE `events_translations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `instructor_translations`
--

DROP TABLE IF EXISTS `instructor_translations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `instructor_translations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `instructor_id` bigint(20) unsigned NOT NULL,
  `locale` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bio` text COLLATE utf8mb4_unicode_ci,
  `designation` text COLLATE utf8mb4_unicode_ci,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `instructor_translations_instructor_id_foreign` (`instructor_id`),
  CONSTRAINT `instructor_translations_instructor_id_foreign` FOREIGN KEY (`instructor_id`) REFERENCES `instructors` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `instructor_translations`
--

LOCK TABLES `instructor_translations` WRITE;
/*!40000 ALTER TABLE `instructor_translations` DISABLE KEYS */;
INSERT INTO `instructor_translations` VALUES (1,1,'en','Kaley Rice (en)','In necessitatibus eligendi ut consectetur voluptatem fugit veniam. Sunt itaque aut quia eaque. Ut aut est ab omnis.','Bulldozer Operator','numquam veritatis','2025-09-11 06:25:04','2025-09-11 06:25:04'),(2,1,'ru','Mr. Marc Schoen (ru)','Et sit consequatur delectus sunt voluptas aliquam adipisci voluptatem. Numquam id modi expedita perferendis soluta. Dicta labore velit aliquid qui aut sed.','Stonemason','blanditiis placeat','2025-09-11 06:25:04','2025-09-11 06:25:04'),(3,1,'ka','Dr. Keith Spinka DDS (ka)','Delectus explicabo qui explicabo non placeat praesentium cum. Et impedit sunt dolore est. Animi dignissimos illo illum consectetur culpa. Temporibus ad sapiente voluptatibus perspiciatis iusto ipsum.','Marking Machine Operator','sit odit','2025-09-11 06:25:04','2025-09-11 06:25:04');
/*!40000 ALTER TABLE `instructor_translations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `instructors`
--

DROP TABLE IF EXISTS `instructors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `instructors` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` json DEFAULT NULL,
  `contact` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  `bio` json DEFAULT NULL,
  `title` json DEFAULT NULL,
  `designation` json DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 active, 0 inactive',
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `language` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `access_block` text COLLATE utf8mb4_unicode_ci,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `instructors_contact_unique` (`contact`),
  UNIQUE KEY `instructors_email_unique` (`email`),
  KEY `instructors_role_id_index` (`role_id`),
  CONSTRAINT `instructors_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `instructors`
--

LOCK TABLES `instructors` WRITE;
/*!40000 ALTER TABLE `instructors` DISABLE KEYS */;
INSERT INTO `instructors` VALUES (1,NULL,'716-862-4509','delpha89@example.org',3,NULL,NULL,NULL,'instructor_5.jpg',1,'$2y$12$J5XAjkq8RSBkz0Py2gLq8.vEsiKy8E2a2JWG/CUsyTtVnd/A53u4G','en','1',NULL,'2025-09-11 06:25:04','2025-09-11 06:25:04',NULL);
/*!40000 ALTER TABLE `instructors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lessons`
--

DROP TABLE IF EXISTS `lessons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lessons` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `course_id` bigint(20) unsigned NOT NULL,
  `quiz_id` bigint(20) unsigned DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lessons_course_id_index` (`course_id`),
  KEY `lessons_quiz_id_foreign` (`quiz_id`),
  CONSTRAINT `lessons_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lessons_quiz_id_foreign` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lessons`
--

LOCK TABLES `lessons` WRITE;
/*!40000 ALTER TABLE `lessons` DISABLE KEYS */;
INSERT INTO `lessons` VALUES (1,NULL,1,NULL,NULL,NULL,'2025-09-11 06:25:04','2025-09-11 06:25:04',NULL);
/*!40000 ALTER TABLE `lessons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lessons_translations`
--

DROP TABLE IF EXISTS `lessons_translations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lessons_translations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `lesson_id` bigint(20) unsigned NOT NULL,
  `locale` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lessons_translations_lesson_id_locale_unique` (`lesson_id`,`locale`),
  CONSTRAINT `lessons_translations_lesson_id_foreign` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lessons_translations`
--

LOCK TABLES `lessons_translations` WRITE;
/*!40000 ALTER TABLE `lessons_translations` DISABLE KEYS */;
INSERT INTO `lessons_translations` VALUES (1,1,'en','Принципы проектирования баз данных 6','Quibusdam numquam consectetur voluptate non quae sint. Dicta alias odit voluptate necessitatibus. Rem magni qui ullam fugit tenetur vero repudiandae. Eligendi saepe adipisci illum et officia.\n\nVelit quasi atque eius error enim omnis molestiae praesentium. Id beatae pariatur eveniet esse doloribus. Necessitatibus incidunt alias alias inventore doloribus. Velit dolor in itaque distinctio. Изучайте практические навыки и применяйте их в реальных проектах.','Rerum repudiandae et ipsum impedit. Iure at laboriosam omnis minima officiis ex quo. Earum in ullam consequatur ad et.\n• Ключевые моменты для запоминания\n• Дополнительные ресурсы: https://www.rohan.biz/veniam-quo-rerum-optio-laborum-qui','2025-09-11 06:25:04','2025-09-11 06:25:04'),(2,1,'ru','Introduction to Programming 6','Qui nihil voluptas saepe voluptatem et. Pariatur nobis dolorum commodi minus voluptas repudiandae at. Aut occaecati ab enim in nostrum illum velit. Ducimus ipsa non porro fuga. Perspiciatis ut qui eos explicabo exercitationem facere non.\n\nTenetur ut blanditiis adipisci consequuntur inventore. Magni est illum sit facere. Labore architecto dolorem et consequuntur sapiente qui ab illum. Learn practical skills and apply them in real projects.','Commodi voluptas itaque voluptate saepe beatae ut debitis. Rerum placeat ipsa assumenda nam quo dolores. Perferendis sunt commodi esse nihil sed. Dignissimos veritatis aperiam repellat porro harum.\n• Key points to remember\n• Additional resources: http://mann.org/','2025-09-11 06:25:04','2025-09-11 06:25:04'),(3,1,'ka','Backend Development Techniques 9','Nihil blanditiis facere cumque. Minus quae modi aliquid quia vel nisi et. Occaecati similique aut error error quidem sequi.\n\nQuia ratione repudiandae eum ducimus. Quia blanditiis voluptas quod molestiae aut. Et quasi velit et dolor ducimus. Learn practical skills and apply them in real projects.','Sed fuga delectus sed dolore alias nihil aspernatur. Iste voluptate animi magni omnis a sequi. Alias similique adipisci ab ipsa ut. Odio consequatur nostrum odit doloribus accusamus omnis autem.\n• Key points to remember\n• Additional resources: http://larkin.com/autem-dolorem-blanditiis-consequuntur-ipsam.html','2025-09-11 06:25:04','2025-09-11 06:25:04');
/*!40000 ALTER TABLE `lessons_translations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `materials`
--

DROP TABLE IF EXISTS `materials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `materials` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `lesson_id` bigint(20) unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` enum('video','document','quiz') COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content_url` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `materials_lesson_id_index` (`lesson_id`),
  CONSTRAINT `materials_lesson_id_foreign` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `materials`
--

LOCK TABLES `materials` WRITE;
/*!40000 ALTER TABLE `materials` DISABLE KEYS */;
/*!40000 ALTER TABLE `materials` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `materials_translations`
--

DROP TABLE IF EXISTS `materials_translations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `materials_translations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `material_id` bigint(20) unsigned NOT NULL,
  `locale` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content_text` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `materials_translations_material_id_locale_unique` (`material_id`,`locale`),
  CONSTRAINT `materials_translations_material_id_foreign` FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `materials_translations`
--

LOCK TABLES `materials_translations` WRITE;
/*!40000 ALTER TABLE `materials_translations` DISABLE KEYS */;
/*!40000 ALTER TABLE `materials_translations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `messages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sender_id` bigint(20) unsigned NOT NULL,
  `receiver_id` bigint(20) unsigned NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `messages_sender_id_index` (`sender_id`),
  KEY `messages_receiver_id_index` (`receiver_id`),
  CONSTRAINT `messages_receiver_id_foreign` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `messages_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messages`
--

LOCK TABLES `messages` WRITE;
/*!40000 ALTER TABLE `messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2019_12_14_000001_create_personal_access_tokens_table',1),(2,'2023_10_12_031415_create_roles_table',1),(3,'2023_11_12_031401_create_instructors_table',1),(4,'2023_11_12_031402_create_users_table',1),(5,'2023_11_19_053448_create_course_categories_table',1),(6,'2023_11_22_143059_create_permissions_table',1),(7,'2023_11_25_034933_create_students_table',1),(8,'2023_11_26_044558_create_courses_table',1),(9,'2023_11_26_153556_create_enrollments_table',1),(10,'2023_11_26_153557_create_lessons_table',1),(11,'2023_11_26_153620_create_materials_table',1),(12,'2023_11_26_153639_create_quizzes_table',1),(13,'2023_11_26_153735_create_reviews_table',1),(14,'2023_11_26_153754_create_payments_table',1),(15,'2023_11_26_153818_create_subscriptions_table',1),(16,'2023_11_26_153844_create_progress_table',1),(17,'2023_11_26_153902_create_discussions_table',1),(18,'2023_11_26_153916_create_messages_table',1),(19,'2023_12_09_135359_create_coupons_table',1),(20,'2023_12_09_170943_create_checkouts_table',1),(21,'2023_12_20_031354_create_watchlists_table',1),(22,'2024_01_01_121113_add_column_to_user_table',1),(23,'2024_01_03_073449_create_events_table',1),(24,'2025_07_29_084136_create_sessions_table',1),(25,'2025_07_29_092955_add_nullable_to_instructor_id_in_users_table',1),(26,'2025_08_06_104334_create_course_categories_translations_table',1),(27,'2025_08_06_104552_create_instructor_translations_table',1),(28,'2025_08_06_105200_create_lessons_translations_table',1),(29,'2025_08_06_105707_create_events_translations_table',1),(30,'2025_08_06_110144_create_reviews_translations_table',1),(31,'2025_08_06_113852_create_course_translations_table',1),(32,'2025_08_09_075221_create_materials_translations_table',1),(33,'2025_08_11_065153_add_instructor_id_to_users_table',1),(34,'2025_08_24_061128_make_contact_nullable_in_users_table',1),(35,'2025_08_30_034842_add_content_text_to_materials_translations_table',1),(36,'2025_08_30_053540_alter_materials_title_nullable',1),(37,'2025_08_30_095645_add_title_to_instructor_translations_table',1),(38,'2025_09_02_045858_remove_text_fields_from_events_table',1),(39,'2025_09_03_095701_create_questions_table',1),(40,'2025_09_03_095702_create_options_table',1),(41,'2025_09_03_095704_create_quiz_attempts_table',1),(42,'2025_09_03_095705_create_question_answers_table',1),(43,'2025_09_03_101114_create_quizzes_translations_table',1),(44,'2025_09_03_101559_add_quiz_id_to_lessons_table',1),(45,'2025_09_03_115100_create_questions_translations_table',1),(46,'2025_09_03_115101_create_options_translations_table',1),(47,'2025_09_04_052346_make_fields_nullable_in_instructors_table',1),(48,'2025_09_04_054659_migrate_json_to_translations',1),(49,'2025_09_05_045316_add_translation_fields_to_courses_table',1),(50,'2025_09_06_081651_make_lesson_title_nullable',1),(51,'2025_09_07_054957_create_student_lesson_progress_table',1),(52,'2025_09_08_100050_add_status_to_courses_table',1),(53,'2025_09_08_100447_create_answers_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `options`
--

DROP TABLE IF EXISTS `options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `options` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `question_id` bigint(20) unsigned NOT NULL,
  `is_correct` tinyint(1) NOT NULL DEFAULT '0',
  `order` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `options_question_id_index` (`question_id`),
  CONSTRAINT `options_question_id_foreign` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `options`
--

LOCK TABLES `options` WRITE;
/*!40000 ALTER TABLE `options` DISABLE KEYS */;
INSERT INTO `options` VALUES (1,1,1,1,'2025-09-11 06:25:05','2025-09-11 06:25:05'),(2,1,0,2,'2025-09-11 06:25:05','2025-09-11 06:25:05'),(3,1,0,3,'2025-09-11 06:25:05','2025-09-11 06:25:05'),(4,1,0,4,'2025-09-11 06:25:05','2025-09-11 06:25:05'),(5,1,0,5,'2025-09-11 06:25:05','2025-09-11 06:25:05'),(6,2,1,1,'2025-09-11 06:25:05','2025-09-11 06:25:05'),(7,2,0,2,'2025-09-11 06:25:05','2025-09-11 06:25:05'),(8,2,0,3,'2025-09-11 06:25:05','2025-09-11 06:25:05'),(9,2,0,4,'2025-09-11 06:25:05','2025-09-11 06:25:05'),(10,3,1,1,'2025-09-11 06:25:05','2025-09-11 06:25:05'),(11,3,0,2,'2025-09-11 06:25:05','2025-09-11 06:25:05'),(12,3,0,3,'2025-09-11 06:25:05','2025-09-11 06:25:05'),(13,3,0,4,'2025-09-11 06:25:05','2025-09-11 06:25:05'),(14,3,0,5,'2025-09-11 06:25:05','2025-09-11 06:25:05'),(15,5,1,1,'2025-09-11 06:25:05','2025-09-11 06:25:05'),(16,5,1,2,'2025-09-11 06:25:05','2025-09-11 06:25:05'),(17,5,0,3,'2025-09-11 06:25:05','2025-09-11 06:25:05'),(18,6,1,1,'2025-09-11 06:25:05','2025-09-11 06:25:05'),(19,6,0,2,'2025-09-11 06:25:05','2025-09-11 06:25:05'),(20,6,0,3,'2025-09-11 06:25:05','2025-09-11 06:25:05'),(21,6,0,4,'2025-09-11 06:25:05','2025-09-11 06:25:05'),(22,6,0,5,'2025-09-11 06:25:05','2025-09-11 06:25:05'),(23,7,1,1,'2025-09-11 06:25:05','2025-09-11 06:25:05'),(24,7,0,2,'2025-09-11 06:25:05','2025-09-11 06:25:05'),(25,7,0,3,'2025-09-11 06:25:05','2025-09-11 06:25:05'),(26,7,0,4,'2025-09-11 06:25:05','2025-09-11 06:25:05'),(27,8,1,1,'2025-09-11 06:25:05','2025-09-11 06:25:05'),(28,8,0,2,'2025-09-11 06:25:05','2025-09-11 06:25:05'),(29,8,0,3,'2025-09-11 06:25:05','2025-09-11 06:25:05'),(30,8,0,4,'2025-09-11 06:25:05','2025-09-11 06:25:05'),(31,8,0,5,'2025-09-11 06:25:05','2025-09-11 06:25:05');
/*!40000 ALTER TABLE `options` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `options_translations`
--

DROP TABLE IF EXISTS `options_translations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `options_translations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `option_id` bigint(20) unsigned NOT NULL,
  `locale` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `option_text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `options_translations_option_id_locale_unique` (`option_id`,`locale`),
  CONSTRAINT `options_translations_option_id_foreign` FOREIGN KEY (`option_id`) REFERENCES `options` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=94 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `options_translations`
--

LOCK TABLES `options_translations` WRITE;
/*!40000 ALTER TABLE `options_translations` DISABLE KEYS */;
INSERT INTO `options_translations` VALUES (1,1,'en','მონაცემთა მთლიანობა და ურთიერთობები','2025-09-11 06:25:05','2025-09-11 06:25:05'),(2,1,'ru','წაკითხვადობა და მოვლა','2025-09-11 06:25:05','2025-09-11 06:25:05'),(3,1,'ka','Model-View-Controller გაყოფა','2025-09-11 06:25:05','2025-09-11 06:25:05'),(4,2,'en','Уменьшает количество запросов к базе данных','2025-09-11 06:25:05','2025-09-11 06:25:05'),(5,2,'ru','С использованием колбэков и промисов','2025-09-11 06:25:05','2025-09-11 06:25:05'),(6,2,'ka','Reduces database queries','2025-09-11 06:25:05','2025-09-11 06:25:05'),(7,3,'en','Readability and maintainability','2025-09-11 06:25:05','2025-09-11 06:25:05'),(8,3,'ru','Для отслеживания изменений в коде','2025-09-11 06:25:05','2025-09-11 06:25:05'),(9,3,'ka','Model-View-Controller გაყოფა','2025-09-11 06:25:05','2025-09-11 06:25:05'),(10,4,'en','Для отслеживания изменений в коде','2025-09-11 06:25:05','2025-09-11 06:25:05'),(11,4,'ru','დიახ, მხარს უჭერს მრავალ პარადიგმას','2025-09-11 06:25:05','2025-09-11 06:25:05'),(12,4,'ka','Classes and objects','2025-09-11 06:25:05','2025-09-11 06:25:05'),(13,5,'en','Adapts to different screen sizes','2025-09-11 06:25:05','2025-09-11 06:25:05'),(14,5,'ru','კოდში ცვლილებების თვალყურის დევნებისთვის','2025-09-11 06:25:05','2025-09-11 06:25:05'),(15,5,'ka','Разделение Model-View-Controller','2025-09-11 06:25:05','2025-09-11 06:25:05'),(16,6,'en','To track changes in code','2025-09-11 06:25:05','2025-09-11 06:25:05'),(17,6,'ru','To track changes in code','2025-09-11 06:25:05','2025-09-11 06:25:05'),(18,6,'ka','Classes and objects','2025-09-11 06:25:05','2025-09-11 06:25:05'),(19,7,'en','Классы и объекты','2025-09-11 06:25:05','2025-09-11 06:25:05'),(20,7,'ru','Читаемость и поддерживаемость','2025-09-11 06:25:05','2025-09-11 06:25:05'),(21,7,'ka','Classes and objects','2025-09-11 06:25:05','2025-09-11 06:25:05'),(22,8,'en','ეგუოდება სხვადასხვა ეკრანის ზომებს','2025-09-11 06:25:05','2025-09-11 06:25:05'),(23,8,'ru','დიახ, მხარს უჭერს მრავალ პარადიგმას','2025-09-11 06:25:05','2025-09-11 06:25:05'),(24,8,'ka','Разделение Model-View-Controller','2025-09-11 06:25:05','2025-09-11 06:25:05'),(25,9,'en','Model-View-Controller separation','2025-09-11 06:25:05','2025-09-11 06:25:05'),(26,9,'ru','Целостность данных и отношения','2025-09-11 06:25:05','2025-09-11 06:25:05'),(27,9,'ka','კოდში ცვლილებების თვალყურის დევნებისთვის','2025-09-11 06:25:05','2025-09-11 06:25:05'),(28,10,'en','ამცირებს მონაცემთა ბაზის მოთხოვნებს','2025-09-11 06:25:05','2025-09-11 06:25:05'),(29,10,'ru','Уменьшает количество запросов к базе данных','2025-09-11 06:25:05','2025-09-11 06:25:05'),(30,10,'ka','კლასები და ობიექტები','2025-09-11 06:25:05','2025-09-11 06:25:05'),(31,11,'en','Data integrity and relationships','2025-09-11 06:25:05','2025-09-11 06:25:05'),(32,11,'ru','Adapts to different screen sizes','2025-09-11 06:25:05','2025-09-11 06:25:05'),(33,11,'ka','Уменьшает количество запросов к базе данных','2025-09-11 06:25:05','2025-09-11 06:25:05'),(34,12,'en','Reduces database queries','2025-09-11 06:25:05','2025-09-11 06:25:05'),(35,12,'ru','HTTPS უზრუნველყოფს დაშიფვრას','2025-09-11 06:25:05','2025-09-11 06:25:05'),(36,12,'ka','Да, поддерживает несколько парадигм','2025-09-11 06:25:05','2025-09-11 06:25:05'),(37,13,'en','ეგუოდება სხვადასხვა ეკრანის ზომებს','2025-09-11 06:25:05','2025-09-11 06:25:05'),(38,13,'ru','ეგუოდება სხვადასხვა ეკრანის ზომებს','2025-09-11 06:25:05','2025-09-11 06:25:05'),(39,13,'ka','Для отслеживания изменений в коде','2025-09-11 06:25:05','2025-09-11 06:25:05'),(40,14,'en','Model-View-Controller გაყოფა','2025-09-11 06:25:05','2025-09-11 06:25:05'),(41,14,'ru','Да, поддерживает несколько парадигм','2025-09-11 06:25:05','2025-09-11 06:25:05'),(42,14,'ka','Читаемость и поддерживаемость','2025-09-11 06:25:05','2025-09-11 06:25:05'),(43,15,'en','Model-View-Controller separation','2025-09-11 06:25:05','2025-09-11 06:25:05'),(44,15,'ru','ამცირებს მონაცემთა ბაზის მოთხოვნებს','2025-09-11 06:25:05','2025-09-11 06:25:05'),(45,15,'ka','Reduces database queries','2025-09-11 06:25:05','2025-09-11 06:25:05'),(46,16,'en','Readability and maintainability','2025-09-11 06:25:05','2025-09-11 06:25:05'),(47,16,'ru','Adapts to different screen sizes','2025-09-11 06:25:05','2025-09-11 06:25:05'),(48,16,'ka','Readability and maintainability','2025-09-11 06:25:05','2025-09-11 06:25:05'),(49,17,'en','Классы и объекты','2025-09-11 06:25:05','2025-09-11 06:25:05'),(50,17,'ru','Classes and objects','2025-09-11 06:25:05','2025-09-11 06:25:05'),(51,17,'ka','წაკითხვადობა და მოვლა','2025-09-11 06:25:05','2025-09-11 06:25:05'),(52,18,'en','Уменьшает количество запросов к базе данных','2025-09-11 06:25:05','2025-09-11 06:25:05'),(53,18,'ru','Классы и объекты','2025-09-11 06:25:05','2025-09-11 06:25:05'),(54,18,'ka','Model-View-Controller გაყოფა','2025-09-11 06:25:05','2025-09-11 06:25:05'),(55,19,'en','С использованием колбэков и промисов','2025-09-11 06:25:05','2025-09-11 06:25:05'),(56,19,'ru','კოდში ცვლილებების თვალყურის დევნებისთვის','2025-09-11 06:25:05','2025-09-11 06:25:05'),(57,19,'ka','Using callbacks and promises','2025-09-11 06:25:05','2025-09-11 06:25:05'),(58,20,'en','Adapts to different screen sizes','2025-09-11 06:25:05','2025-09-11 06:25:05'),(59,20,'ru','Адаптируется к разным размерам экранов','2025-09-11 06:25:05','2025-09-11 06:25:05'),(60,20,'ka','Для отслеживания изменений в коде','2025-09-11 06:25:05','2025-09-11 06:25:05'),(61,21,'en','Разделение Model-View-Controller','2025-09-11 06:25:05','2025-09-11 06:25:05'),(62,21,'ru','To track changes in code','2025-09-11 06:25:05','2025-09-11 06:25:05'),(63,21,'ka','კოდში ცვლილებების თვალყურის დევნებისთვის','2025-09-11 06:25:05','2025-09-11 06:25:05'),(64,22,'en','Да, поддерживает несколько парадигм','2025-09-11 06:25:05','2025-09-11 06:25:05'),(65,22,'ru','კლასები და ობიექტები','2025-09-11 06:25:05','2025-09-11 06:25:05'),(66,22,'ka','Читаемость и поддерживаемость','2025-09-11 06:25:05','2025-09-11 06:25:05'),(67,23,'en','Yes, it supports multiple paradigms','2025-09-11 06:25:05','2025-09-11 06:25:05'),(68,23,'ru','Классы и объекты','2025-09-11 06:25:05','2025-09-11 06:25:05'),(69,23,'ka','Да, поддерживает несколько парадигм','2025-09-11 06:25:05','2025-09-11 06:25:05'),(70,24,'en','Data integrity and relationships','2025-09-11 06:25:05','2025-09-11 06:25:05'),(71,24,'ru','Data integrity and relationships','2025-09-11 06:25:05','2025-09-11 06:25:05'),(72,24,'ka','Yes, it supports multiple paradigms','2025-09-11 06:25:05','2025-09-11 06:25:05'),(73,25,'en','To track changes in code','2025-09-11 06:25:05','2025-09-11 06:25:05'),(74,25,'ru','Уменьшает количество запросов к базе данных','2025-09-11 06:25:05','2025-09-11 06:25:05'),(75,25,'ka','დიახ, მხარს უჭერს მრავალ პარადიგმას','2025-09-11 06:25:05','2025-09-11 06:25:05'),(76,26,'en','To track changes in code','2025-09-11 06:25:05','2025-09-11 06:25:05'),(77,26,'ru','Adapts to different screen sizes','2025-09-11 06:25:05','2025-09-11 06:25:05'),(78,26,'ka','Читаемость и поддерживаемость','2025-09-11 06:25:05','2025-09-11 06:25:05'),(79,27,'en','Разделение Model-View-Controller','2025-09-11 06:25:05','2025-09-11 06:25:05'),(80,27,'ru','HTTPS обеспечивает шифрование','2025-09-11 06:25:05','2025-09-11 06:25:05'),(81,27,'ka','Классы и объекты','2025-09-11 06:25:05','2025-09-11 06:25:05'),(82,28,'en','Читаемость и поддерживаемость','2025-09-11 06:25:05','2025-09-11 06:25:05'),(83,28,'ru','ეგუოდება სხვადასხვა ეკრანის ზომებს','2025-09-11 06:25:05','2025-09-11 06:25:05'),(84,28,'ka','ეგუოდება სხვადასხვა ეკრანის ზომებს','2025-09-11 06:25:05','2025-09-11 06:25:05'),(85,29,'en','Readability and maintainability','2025-09-11 06:25:05','2025-09-11 06:25:05'),(86,29,'ru','კლასები და ობიექტები','2025-09-11 06:25:05','2025-09-11 06:25:05'),(87,29,'ka','ამცირებს მონაცემთა ბაზის მოთხოვნებს','2025-09-11 06:25:05','2025-09-11 06:25:05'),(88,30,'en','Data integrity and relationships','2025-09-11 06:25:05','2025-09-11 06:25:05'),(89,30,'ru','Readability and maintainability','2025-09-11 06:25:05','2025-09-11 06:25:05'),(90,30,'ka','Читаемость и поддерживаемость','2025-09-11 06:25:05','2025-09-11 06:25:05'),(91,31,'en','კლასები და ობიექტები','2025-09-11 06:25:05','2025-09-11 06:25:05'),(92,31,'ru','To track changes in code','2025-09-11 06:25:05','2025-09-11 06:25:05'),(93,31,'ka','HTTPS უზრუნველყოფს დაშიფვრას','2025-09-11 06:25:05','2025-09-11 06:25:05');
/*!40000 ALTER TABLE `options_translations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) DEFAULT NULL,
  `currency` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `currency_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `currency_value` decimal(10,2) DEFAULT NULL,
  `method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `txnid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '0 pending, 1 successfull, 2 fail',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `permissions_role_id_index` (`role_id`),
  CONSTRAINT `permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
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
-- Table structure for table `progress`
--

DROP TABLE IF EXISTS `progress`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `progress` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) unsigned NOT NULL,
  `course_id` bigint(20) unsigned NOT NULL,
  `progress_percentage` int(11) NOT NULL DEFAULT '0',
  `completed` tinyint(1) NOT NULL DEFAULT '0',
  `last_viewed_material_id` bigint(20) unsigned DEFAULT NULL,
  `last_viewed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `progress_student_id_index` (`student_id`),
  KEY `progress_course_id_index` (`course_id`),
  KEY `progress_last_viewed_material_id_index` (`last_viewed_material_id`),
  CONSTRAINT `progress_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `progress_last_viewed_material_id_foreign` FOREIGN KEY (`last_viewed_material_id`) REFERENCES `materials` (`id`) ON DELETE CASCADE,
  CONSTRAINT `progress_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `progress`
--

LOCK TABLES `progress` WRITE;
/*!40000 ALTER TABLE `progress` DISABLE KEYS */;
/*!40000 ALTER TABLE `progress` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `question_answers`
--

DROP TABLE IF EXISTS `question_answers`; 
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `question_answers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `attempt_id` bigint(20) unsigned NOT NULL,
  `question_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `text_answer` text COLLATE utf8mb4_unicode_ci,
  `rating_answer` int(11) DEFAULT NULL,
  `is_correct` tinyint(1) DEFAULT NULL,
  `points_earned` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `question_answers_attempt_id_index` (`attempt_id`),
  KEY `question_answers_question_id_index` (`question_id`),
  KEY `question_answers_user_id_index` (`user_id`),
  CONSTRAINT `question_answers_attempt_id_foreign` FOREIGN KEY (`attempt_id`) REFERENCES `quiz_attempts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `question_answers_question_id_foreign` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `question_answers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `question_answers`
--

LOCK TABLES `question_answers` WRITE;
/*!40000 ALTER TABLE `question_answers` DISABLE KEYS */;
/*!40000 ALTER TABLE `question_answers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `questions`
--

DROP TABLE IF EXISTS `questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `questions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `quiz_id` bigint(20) unsigned NOT NULL,
  `type` enum('single','multiple','text','rating') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'single',
  `order` int(11) NOT NULL DEFAULT '0',
  `points` int(11) NOT NULL DEFAULT '1',
  `is_required` tinyint(1) NOT NULL DEFAULT '1',
  `max_choices` int(11) DEFAULT NULL,
  `min_rating` int(11) DEFAULT NULL,
  `max_rating` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `questions_quiz_id_index` (`quiz_id`),
  CONSTRAINT `questions_quiz_id_foreign` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `questions`
--

LOCK TABLES `questions` WRITE;
/*!40000 ALTER TABLE `questions` DISABLE KEYS */;
INSERT INTO `questions` VALUES (1,1,'single',9,4,1,NULL,NULL,NULL,'2025-09-11 06:25:05','2025-09-11 06:25:05',NULL),(2,1,'single',13,9,1,NULL,NULL,NULL,'2025-09-11 06:25:05','2025-09-11 06:25:05',NULL),(3,1,'single',4,9,0,NULL,NULL,NULL,'2025-09-11 06:25:05','2025-09-11 06:25:05',NULL),(4,1,'rating',11,1,1,NULL,1,9,'2025-09-11 06:25:05','2025-09-11 06:25:05',NULL),(5,1,'multiple',11,5,1,5,NULL,NULL,'2025-09-11 06:25:05','2025-09-11 06:25:05',NULL),(6,1,'single',2,2,0,NULL,NULL,NULL,'2025-09-11 06:25:05','2025-09-11 06:25:05',NULL),(7,1,'single',10,6,1,NULL,NULL,NULL,'2025-09-11 06:25:05','2025-09-11 06:25:05',NULL),(8,1,'multiple',10,10,0,4,NULL,NULL,'2025-09-11 06:25:05','2025-09-11 06:25:05',NULL),(9,1,'text',20,7,0,NULL,NULL,NULL,'2025-09-11 06:25:05','2025-09-11 06:25:05',NULL);
/*!40000 ALTER TABLE `questions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `questions_translations`
--

DROP TABLE IF EXISTS `questions_translations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `questions_translations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `question_id` bigint(20) unsigned NOT NULL,
  `locale` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `questions_translations_question_id_locale_unique` (`question_id`,`locale`),
  CONSTRAINT `questions_translations_question_id_foreign` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `questions_translations`
--

LOCK TABLES `questions_translations` WRITE;
/*!40000 ALTER TABLE `questions_translations` DISABLE KEYS */;
INSERT INTO `questions_translations` VALUES (1,1,'en','როგორ უმჯობესებს კეშირება აპლიკაციის მუშაობას?','2025-09-11 06:25:05','2025-09-11 06:25:05'),(2,1,'ru','Which programming paradigm does Python support?','2025-09-11 06:25:05','2025-09-11 06:25:05'),(3,1,'ka','როგორ უმჯობესებს კეშირება აპლიკაციის მუშაობას?','2025-09-11 06:25:05','2025-09-11 06:25:05'),(4,2,'en','Опишите архитектурный паттерн MVC.','2025-09-11 06:25:05','2025-09-11 06:25:05'),(5,2,'ru','What are the principles of clean code?','2025-09-11 06:25:05','2025-09-11 06:25:05'),(6,2,'ka','როგორ ამუშავებს JavaScript ასინქრონულ ოპერაციებს?','2025-09-11 06:25:05','2025-09-11 06:25:05'),(7,3,'en','ახსენით რესპონსიული ვებ-დიზაინის კონცეფცია.','2025-09-11 06:25:05','2025-09-11 06:25:05'),(8,3,'ru','What are the advantages of using a relational database?','2025-09-11 06:25:05','2025-09-11 06:25:05'),(9,3,'ka','რა არის სუფთა კოდის პრინციპები?','2025-09-11 06:25:05','2025-09-11 06:25:05'),(10,4,'en','ახსენით ობიექტზე-ორიენტირებული პროგრამირების კონცეფცია.','2025-09-11 06:25:05','2025-09-11 06:25:05'),(11,4,'ru','Каковы преимущества использования реляционной базы данных?','2025-09-11 06:25:05','2025-09-11 06:25:05'),(12,4,'ka','ახსენით რესპონსიული ვებ-დიზაინის კონცეფცია.','2025-09-11 06:25:05','2025-09-11 06:25:05'),(13,5,'en','What are the principles of clean code?','2025-09-11 06:25:05','2025-09-11 06:25:05'),(14,5,'ru','Explain the concept of object-oriented programming.','2025-09-11 06:25:05','2025-09-11 06:25:05'),(15,5,'ka','რომელ პროგრამირების პარადიგმას უჭერს მხარს Python?','2025-09-11 06:25:05','2025-09-11 06:25:05'),(16,6,'en','Which programming paradigm does Python support?','2025-09-11 06:25:05','2025-09-11 06:25:05'),(17,6,'ru','Объясните концепцию адаптивного веб-дизайна.','2025-09-11 06:25:05','2025-09-11 06:25:05'),(18,6,'ka','What are the principles of clean code?','2025-09-11 06:25:05','2025-09-11 06:25:05'),(19,7,'en','Какую парадигму программирования поддерживает Python?','2025-09-11 06:25:05','2025-09-11 06:25:05'),(20,7,'ru','Опишите архитектурный паттерн MVC.','2025-09-11 06:25:05','2025-09-11 06:25:05'),(21,7,'ka','რომელ პროგრამირების პარადიგმას უჭერს მხარს Python?','2025-09-11 06:25:05','2025-09-11 06:25:05'),(22,8,'en','Which programming paradigm does Python support?','2025-09-11 06:25:05','2025-09-11 06:25:05'),(23,8,'ru','Explain the concept of responsive web design.','2025-09-11 06:25:05','2025-09-11 06:25:05'),(24,8,'ka','Which programming paradigm does Python support?','2025-09-11 06:25:05','2025-09-11 06:25:05'),(25,9,'en','რა არის ვერსიების კონტროლის სისტემების მთავარი მიზანი?','2025-09-11 06:25:05','2025-09-11 06:25:05'),(26,9,'ru','What is the difference between HTTP and HTTPS?','2025-09-11 06:25:05','2025-09-11 06:25:05'),(27,9,'ka','Which programming paradigm does Python support?','2025-09-11 06:25:05','2025-09-11 06:25:05');
/*!40000 ALTER TABLE `questions_translations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `quiz_attempts`
--

DROP TABLE IF EXISTS `quiz_attempts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `quiz_attempts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `quiz_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `score` int(11) NOT NULL DEFAULT '0',
  `total_questions` int(11) NOT NULL DEFAULT '0',
  `correct_answers` int(11) NOT NULL DEFAULT '0',
  `started_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `time_taken` int(11) DEFAULT NULL,
  `status` enum('in_progress','completed','expired') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'in_progress',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `quiz_attempts_quiz_id_index` (`quiz_id`),
  KEY `quiz_attempts_user_id_index` (`user_id`),
  CONSTRAINT `quiz_attempts_quiz_id_foreign` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `quiz_attempts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `quiz_attempts`
--

LOCK TABLES `quiz_attempts` WRITE;
/*!40000 ALTER TABLE `quiz_attempts` DISABLE KEYS */;
INSERT INTO `quiz_attempts` VALUES (1,1,6,0,15,0,'2025-08-15 09:20:42',NULL,NULL,'in_progress','2025-08-15 09:20:42','2025-08-15 09:20:42'),(2,1,14,0,7,0,'2025-09-10 12:21:23',NULL,NULL,'in_progress','2025-09-10 12:21:23','2025-09-10 12:21:23'),(3,1,14,15,11,7,'2025-08-28 18:04:13','2025-09-09 05:37:10',511,'completed','2025-08-28 18:04:13','2025-08-28 18:04:13'),(4,1,11,12,20,4,'2025-08-15 00:22:45','2025-09-08 17:11:19',1600,'completed','2025-08-15 00:22:45','2025-08-15 00:22:45'),(5,1,9,85,13,10,'2025-09-07 17:01:30','2025-09-08 16:15:40',525,'completed','2025-09-07 17:01:30','2025-09-07 17:01:30'),(6,1,6,0,20,0,'2025-08-21 07:08:26',NULL,NULL,'in_progress','2025-08-21 07:08:26','2025-08-21 07:08:26'),(7,1,16,77,14,5,'2025-08-15 03:58:50','2025-08-29 09:58:27',145,'completed','2025-08-15 03:58:50','2025-08-15 03:58:50'),(8,1,14,0,15,0,'2025-08-12 13:15:30',NULL,NULL,'in_progress','2025-08-12 13:15:30','2025-08-12 13:15:30'),(9,1,10,0,16,0,'2025-08-18 20:40:35',NULL,NULL,'in_progress','2025-08-18 20:40:35','2025-08-18 20:40:35'),(10,1,10,1,5,1,'2025-08-12 10:34:08','2025-09-06 07:29:41',813,'completed','2025-08-12 10:34:08','2025-08-12 10:34:08'),(11,1,14,0,20,0,'2025-09-07 09:45:56',NULL,NULL,'expired','2025-09-07 09:45:56','2025-09-07 09:45:56'),(12,1,17,0,5,0,'2025-08-19 14:48:57',NULL,NULL,'expired','2025-08-19 14:48:57','2025-08-19 14:48:57'),(13,1,7,65,15,10,'2025-09-01 23:49:42','2025-09-06 21:56:32',434,'completed','2025-09-01 23:49:42','2025-09-01 23:49:42'),(14,1,15,16,19,8,'2025-08-14 10:45:08','2025-08-19 21:09:52',1187,'completed','2025-08-14 10:45:08','2025-08-14 10:45:08'),(15,1,16,33,20,17,'2025-08-23 19:15:28','2025-08-31 08:50:22',394,'completed','2025-08-23 19:15:28','2025-08-23 19:15:28'),(16,1,7,0,20,0,'2025-08-18 17:44:02',NULL,NULL,'in_progress','2025-08-18 17:44:02','2025-08-18 17:44:02'),(17,1,16,0,10,0,'2025-09-10 12:19:04',NULL,NULL,'in_progress','2025-09-10 12:19:04','2025-09-10 12:19:04'),(18,1,17,0,16,0,'2025-08-20 08:04:44',NULL,NULL,'in_progress','2025-08-20 08:04:44','2025-08-20 08:04:44'),(19,1,6,38,6,3,'2025-08-20 11:52:35','2025-08-28 15:53:11',85,'completed','2025-08-20 11:52:35','2025-08-20 11:52:35'),(20,1,5,12,17,0,'2025-08-23 18:19:04','2025-08-23 19:45:29',92,'completed','2025-08-23 18:19:04','2025-08-23 18:19:04'),(21,1,8,64,17,0,'2025-09-07 08:55:47','2025-09-07 12:38:35',1146,'completed','2025-09-07 08:55:47','2025-09-07 08:55:47'),(22,1,13,0,9,0,'2025-08-19 20:38:14',NULL,NULL,'expired','2025-08-19 20:38:14','2025-08-19 20:38:14'),(23,1,15,17,18,13,'2025-08-20 00:17:38','2025-08-24 10:13:39',74,'completed','2025-08-20 00:17:38','2025-08-20 00:17:38'),(24,1,10,65,5,2,'2025-09-05 08:55:58','2025-09-10 11:22:50',544,'completed','2025-09-05 08:55:58','2025-09-05 08:55:58'),(25,1,10,91,12,3,'2025-09-08 01:23:30','2025-09-10 10:03:34',120,'completed','2025-09-08 01:23:30','2025-09-08 01:23:30'),(26,1,6,0,5,0,'2025-09-03 22:53:39',NULL,NULL,'in_progress','2025-09-03 22:53:39','2025-09-03 22:53:39'),(27,1,6,0,15,0,'2025-08-11 08:24:06',NULL,NULL,'in_progress','2025-08-11 08:24:06','2025-08-11 08:24:06'),(28,1,7,0,7,0,'2025-09-10 18:42:34',NULL,NULL,'in_progress','2025-09-10 18:42:34','2025-09-10 18:42:34'),(29,1,8,75,6,4,'2025-08-31 06:05:08','2025-09-02 19:00:53',797,'completed','2025-08-31 06:05:08','2025-08-31 06:05:08'),(30,1,13,42,14,2,'2025-08-29 22:29:57','2025-09-01 03:07:24',949,'completed','2025-08-29 22:29:57','2025-08-29 22:29:57'),(31,1,10,54,13,11,'2025-08-22 12:23:16','2025-08-29 06:12:02',1761,'completed','2025-08-22 12:23:16','2025-08-22 12:23:16'),(32,1,10,0,19,0,'2025-08-29 12:05:34',NULL,NULL,'expired','2025-08-29 12:05:34','2025-08-29 12:05:34'),(33,1,16,0,20,0,'2025-08-17 00:42:04',NULL,NULL,'in_progress','2025-08-17 00:42:04','2025-08-17 00:42:04'),(34,1,8,87,16,11,'2025-08-30 02:43:42','2025-09-07 20:23:02',1708,'completed','2025-08-30 02:43:42','2025-08-30 02:43:42'),(35,1,6,55,9,7,'2025-08-22 19:18:13','2025-08-25 00:27:42',871,'completed','2025-08-22 19:18:13','2025-08-22 19:18:13'),(36,1,7,81,6,3,'2025-09-07 03:55:06','2025-09-10 03:39:17',972,'completed','2025-09-07 03:55:06','2025-09-07 03:55:06'),(37,1,7,71,5,2,'2025-09-08 02:00:21','2025-09-10 18:00:56',824,'completed','2025-09-08 02:00:21','2025-09-08 02:00:21'),(38,1,13,72,20,4,'2025-09-08 04:55:17','2025-09-08 06:25:15',399,'completed','2025-09-08 04:55:17','2025-09-08 04:55:17'),(39,1,6,14,14,13,'2025-09-01 12:13:56','2025-09-06 09:26:34',399,'completed','2025-09-01 12:13:56','2025-09-01 12:13:56'),(40,1,11,36,9,8,'2025-08-23 13:05:08','2025-09-09 12:00:49',840,'completed','2025-08-23 13:05:08','2025-08-23 13:05:08'),(41,1,7,13,11,2,'2025-08-22 06:27:15','2025-09-03 01:02:47',354,'completed','2025-08-22 06:27:15','2025-08-22 06:27:15'),(42,1,6,0,10,0,'2025-08-15 01:31:13',NULL,NULL,'in_progress','2025-08-15 01:31:13','2025-08-15 01:31:13'),(43,1,16,13,14,6,'2025-08-29 13:13:23','2025-09-03 08:24:47',1767,'completed','2025-08-29 13:13:23','2025-08-29 13:13:23'),(44,1,8,76,18,15,'2025-08-19 17:36:02','2025-08-22 12:07:22',930,'completed','2025-08-19 17:36:02','2025-08-19 17:36:02'),(45,1,8,85,20,3,'2025-09-08 11:58:06','2025-09-08 23:57:22',1702,'completed','2025-09-08 11:58:06','2025-09-08 11:58:06'),(46,1,6,0,19,0,'2025-08-11 23:01:31',NULL,NULL,'in_progress','2025-08-11 23:01:31','2025-08-11 23:01:31'),(47,1,15,0,14,0,'2025-08-18 07:50:27',NULL,NULL,'in_progress','2025-08-18 07:50:27','2025-08-18 07:50:27'),(48,1,15,0,9,0,'2025-08-28 13:48:54',NULL,NULL,'in_progress','2025-08-28 13:48:54','2025-08-28 13:48:54'),(49,1,12,92,6,2,'2025-09-02 14:53:18','2025-09-04 14:41:43',371,'completed','2025-09-02 14:53:18','2025-09-02 14:53:18'),(50,1,13,75,18,10,'2025-08-24 19:21:48','2025-09-09 08:36:22',1033,'completed','2025-08-24 19:21:48','2025-08-24 19:21:48');
/*!40000 ALTER TABLE `quiz_attempts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `quizzes`
--

DROP TABLE IF EXISTS `quizzes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `quizzes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `lesson_id` bigint(20) unsigned DEFAULT NULL,
  `order` int(11) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `time_limit` int(11) DEFAULT NULL COMMENT 'Time limit in minutes',
  `passing_score` int(11) NOT NULL DEFAULT '70',
  `max_attempts` int(11) NOT NULL DEFAULT '1',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `quizzes_lesson_id_index` (`lesson_id`),
  CONSTRAINT `quizzes_lesson_id_foreign` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `quizzes`
--

LOCK TABLES `quizzes` WRITE;
/*!40000 ALTER TABLE `quizzes` DISABLE KEYS */;
INSERT INTO `quizzes` VALUES (1,1,7,1,27,87,5,'ut autem ut','2025-09-11 06:25:04','2025-09-11 06:25:04',NULL);
/*!40000 ALTER TABLE `quizzes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `quizzes_translations`
--

DROP TABLE IF EXISTS `quizzes_translations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `quizzes_translations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `quiz_id` bigint(20) unsigned NOT NULL,
  `locale` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `quizzes_translations_quiz_id_locale_unique` (`quiz_id`,`locale`),
  CONSTRAINT `quizzes_translations_quiz_id_foreign` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `quizzes_translations`
--

LOCK TABLES `quizzes_translations` WRITE;
/*!40000 ALTER TABLE `quizzes_translations` DISABLE KEYS */;
INSERT INTO `quizzes_translations` VALUES (1,1,'en','Проверка знаний фронтенд фреймворков','Minus eum itaque enim. Vel a exercitationem doloremque vero voluptas voluptatem. Consequatur sapiente est deleniti sit atque repellendus impedit beatae. Проверьте свои знания и понимание материала курса.','2025-09-11 06:25:04','2025-09-11 06:25:04'),(2,1,'ru','Основы веб-разработки','Consequatur odit sint labore aut autem eius recusandae. Corporis et consequatur veritatis illo. Neque ut quia error recusandae temporibus necessitatibus id esse. Nostrum est porro asperiores voluptate. Проверьте свои знания и понимание материала курса.','2025-09-11 06:25:04','2025-09-11 06:25:04'),(3,1,'ka','Проверка знаний фронтенд фреймворков','Et id ipsa quis soluta. Nihil distinctio atque amet labore itaque. Aliquam aut natus consequatur illum minus nemo et. Recusandae sequi expedita dolores qui itaque. Eum explicabo optio voluptas quo animi nulla quod. Проверьте свои знания и понимание материала курса.','2025-09-11 06:25:04','2025-09-11 06:25:04');
/*!40000 ALTER TABLE `quizzes_translations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reviews` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) unsigned NOT NULL,
  `course_id` bigint(20) unsigned NOT NULL,
  `rating` int(11) NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reviews_student_id_index` (`student_id`),
  KEY `reviews_course_id_index` (`course_id`),
  CONSTRAINT `reviews_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reviews_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
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
-- Table structure for table `reviews_translations`
--

DROP TABLE IF EXISTS `reviews_translations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reviews_translations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `review_id` bigint(20) unsigned NOT NULL,
  `locale` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reviews_translations_review_id_locale_unique` (`review_id`,`locale`),
  CONSTRAINT `reviews_translations_review_id_foreign` FOREIGN KEY (`review_id`) REFERENCES `reviews` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reviews_translations`
--

LOCK TABLES `reviews_translations` WRITE;
/*!40000 ALTER TABLE `reviews_translations` DISABLE KEYS */;
/*!40000 ALTER TABLE `reviews_translations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `identity` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_unique` (`name`),
  UNIQUE KEY `roles_identity_unique` (`identity`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Super Admin','superadmin','2025-09-11 06:24:57',NULL),(2,'Admin','admin','2025-09-11 06:24:57',NULL),(3,'Instructor','instructor','2025-09-11 06:24:57',NULL),(4,'Student','student','2025-09-11 06:24:57',NULL);
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
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
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_lesson_progress`
--

DROP TABLE IF EXISTS `student_lesson_progress`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `student_lesson_progress` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) unsigned NOT NULL,
  `lesson_id` bigint(20) unsigned NOT NULL,
  `course_id` bigint(20) unsigned NOT NULL,
  `progress` int(11) NOT NULL DEFAULT '0',
  `video_position` int(11) NOT NULL DEFAULT '0',
  `video_duration` int(11) NOT NULL DEFAULT '0',
  `is_completed` tinyint(1) NOT NULL DEFAULT '0',
  `completed_at` timestamp NULL DEFAULT NULL,
  `last_accessed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `student_lesson_progress_student_id_lesson_id_unique` (`student_id`,`lesson_id`),
  KEY `student_lesson_progress_lesson_id_foreign` (`lesson_id`),
  KEY `student_lesson_progress_course_id_foreign` (`course_id`),
  CONSTRAINT `student_lesson_progress_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `student_lesson_progress_lesson_id_foreign` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`) ON DELETE CASCADE,
  CONSTRAINT `student_lesson_progress_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_lesson_progress`
--

LOCK TABLES `student_lesson_progress` WRITE;
/*!40000 ALTER TABLE `student_lesson_progress` DISABLE KEYS */;
/*!40000 ALTER TABLE `student_lesson_progress` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `students`
--

DROP TABLE IF EXISTS `students`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `students` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('male','female') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bio` text COLLATE utf8mb4_unicode_ci,
  `profession` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nationality` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'Georgien',
  `address` text COLLATE utf8mb4_unicode_ci,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postcode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 active, 0 inactive',
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `language` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `students_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `students`
--

LOCK TABLES `students` WRITE;
/*!40000 ALTER TABLE `students` DISABLE KEYS */;
/*!40000 ALTER TABLE `students` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subscriptions`
--

DROP TABLE IF EXISTS `subscriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subscriptions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) unsigned NOT NULL,
  `course_id` bigint(20) unsigned NOT NULL,
  `plan` enum('monthly','yearly') COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `end_date` timestamp NULL DEFAULT NULL,
  `status` enum('active','canceled','expired') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subscriptions_student_id_index` (`student_id`),
  KEY `subscriptions_course_id_index` (`course_id`),
  CONSTRAINT `subscriptions_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `subscriptions_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subscriptions`
--

LOCK TABLES `subscriptions` WRITE;
/*!40000 ALTER TABLE `subscriptions` DISABLE KEYS */;
/*!40000 ALTER TABLE `subscriptions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `instructor_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `language` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `full_access` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1=>yes, 0=>no',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=>active 2=>inactive',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_contact_unique` (`contact`),
  KEY `users_role_id_index` (`role_id`),
  CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,NULL,'Super Administrator','superadmin@example.com','+995123456789',1,'$2y$12$5NukyDna9.zQXO8vp0j2/OCEkzYpOHLFKakpQuhJ.9tp5sSLn8/7m','en',NULL,1,1,NULL,'2025-09-11 06:25:00','2025-09-11 06:25:00',NULL),(2,NULL,'Administrator','admin@example.com','+995987654321',2,'$2y$12$NSsn9WssbxvpfLe6enGYReczQlot24a3GBE6sUXdlOTUBRChgR/hO','en',NULL,1,1,NULL,'2025-09-11 06:25:01','2025-09-11 06:25:01',NULL),(3,NULL,'John Instructor','instructor1@example.com','+995555111222',3,'$2y$12$LaLFh2WW3Zod/i3.qH4diuWYjhsxuftf/zj/yTRa8Qohu0ZR03U.q','en',NULL,0,1,NULL,'2025-09-11 06:25:01','2025-09-11 06:25:01',NULL),(4,NULL,'Jane Teacher','instructor2@example.com','+995555333444',3,'$2y$12$Tk35kuYJv0i7a//hWP/K..Xuyo0WcdTOXq6cCOa41DHYPSFUjY1cC','ru',NULL,0,1,NULL,'2025-09-11 06:25:01','2025-09-11 06:25:01',NULL),(5,NULL,'Student One','student1@example.com','+995777111222',4,'$2y$12$rJNk0SHU736oa0rsJcFviOHAyGkkJTRSrat.EwG9RA1Mse8luIhLG','en',NULL,0,1,NULL,'2025-09-11 06:25:01','2025-09-11 06:25:01',NULL),(6,NULL,'Student Two','student2@example.com','+995777333444',4,'$2y$12$sc9JIRRIWKgpvSsTvQKt9OHm8bVck9VO9kLg1767ix3nj/w95HWsW','ru',NULL,0,1,NULL,'2025-09-11 06:25:01','2025-09-11 06:25:01',NULL),(7,NULL,'Student Three','student3@example.com','+995777555666',4,'$2y$12$ZJpCaN3B5m7pX3lzsUdJN.Xf4VZ9cMEUwS0DRB2n73ic1AqsykeAK','ka',NULL,0,1,NULL,'2025-09-11 06:25:02','2025-09-11 06:25:02',NULL),(8,NULL,'Justen Mraz','greenfelder.ryder@example.com',NULL,4,'$2y$12$4/qfaNsFrtwtGktHSzUpYuboovVSQOZtcNsvXqC4FKHwNAzIPfe5e','ru','https://via.placeholder.com/100x100.png/007711?text=people+ut',0,1,'NAcBkZ65uS','2025-09-11 06:25:02','2025-09-11 06:25:02',NULL),(9,NULL,'Prof. Dortha Wyman','xhand@example.net','+1 (828) 630-3413',4,'$2y$12$IbWLLpEuHhVG4k6wBxa2cOzO72Bzb3MKhWWDRmOf5d8mynkndPJFO','ka','https://via.placeholder.com/100x100.png/006699?text=people+est',0,1,'K7qH2RqlyB','2025-09-11 06:25:02','2025-09-11 06:25:02',NULL),(10,NULL,'Prof. Harley Abshire I','jakayla.klocko@example.com',NULL,4,'$2y$12$oi4dv5xx1Qusob1svXCm/OFF9qrhlfVpkQM8MCHKjYR92dD7qDQV.','ru','https://via.placeholder.com/100x100.png/00cc00?text=people+consequatur',0,1,'zQ3lzn9Aak','2025-09-11 06:25:02','2025-09-11 06:25:02',NULL),(11,NULL,'Vicky Stanton','acartwright@example.net',NULL,4,'$2y$12$ysGlRBW/8wiqu7.tS9HDFeXjrm2Z.BWQ5TBpib7ug./G.fSYxaCTO','en','https://via.placeholder.com/100x100.png/007700?text=people+est',0,0,'kjPnVKY0f6','2025-09-11 06:25:03','2025-09-11 06:25:03',NULL),(12,NULL,'Janet Schmeler','elouise09@example.com',NULL,4,'$2y$12$3JA0ykiSL0Z3WrQx7jHD.uw46sxG/UrPyxX7Ww254jq.FxXKKvKq.','ru','https://via.placeholder.com/100x100.png/0011bb?text=people+fugiat',0,1,'XT2MKgunw3','2025-09-11 06:25:03','2025-09-11 06:25:03',NULL),(13,NULL,'Karina Russel','joanne.greenholt@example.org','1-316-580-9072',4,'$2y$12$QYto28Z445AVa.Z1C0XMiuHbFRHhPhxhHVELi3kqx93ouqz47Tq66','ka','https://via.placeholder.com/100x100.png/004433?text=people+aliquam',0,1,'7YTM5mjCMK','2025-09-11 06:25:03','2025-09-11 06:25:03',NULL),(14,NULL,'Haskell Hilpert','rhermann@example.com',NULL,4,'$2y$12$mEIfdYaokL2RzEpVoec97.u4/BdJwhBqtYOg1FKeCgR6NLwP.Awk.','ru','https://via.placeholder.com/100x100.png/0011aa?text=people+soluta',0,1,'hO4avbfSbe','2025-09-11 06:25:03','2025-09-11 06:25:03',NULL),(15,NULL,'Elyssa Hahn Sr.','alison51@example.net',NULL,4,'$2y$12$/4pjC8gIjK.dBOn8xIIWveOlcKNdSAUkDbxOznEdhqfoIdqqiUqQe','ru','https://via.placeholder.com/100x100.png/0077bb?text=people+est',0,1,'y4TLw09bC3','2025-09-11 06:25:03','2025-09-11 06:25:03',NULL),(16,NULL,'Marcellus Wilkinson','julien.abshire@example.net',NULL,4,'$2y$12$QdT8D54oqT1S.FU0HtFoKu9nU.VgFUEZFM6pV76bVWVCuR0PH5mNm','ru',NULL,0,1,'KpWG0zieK3','2025-09-11 06:25:04','2025-09-11 06:25:04',NULL),(17,NULL,'Hal Dibbert','mertz.germaine@example.com',NULL,4,'$2y$12$CxwKO51usuymTS6jxhPB2OPaFSzL38LUWAfHkWHeCeNld/.PWf..a','ru',NULL,0,1,'lBp5SUhJrZ','2025-09-11 06:25:04','2025-09-11 06:25:04',NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `watchlists`
--

DROP TABLE IF EXISTS `watchlists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `watchlists` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) unsigned NOT NULL,
  `course_id` bigint(20) unsigned NOT NULL,
  `lesson_id` bigint(20) unsigned NOT NULL,
  `material_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `watchlists_student_id_index` (`student_id`),
  KEY `watchlists_course_id_index` (`course_id`),
  KEY `watchlists_lesson_id_index` (`lesson_id`),
  KEY `watchlists_material_id_index` (`material_id`),
  CONSTRAINT `watchlists_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `watchlists_lesson_id_foreign` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`) ON DELETE CASCADE,
  CONSTRAINT `watchlists_material_id_foreign` FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`) ON DELETE CASCADE,
  CONSTRAINT `watchlists_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `watchlists`
--

LOCK TABLES `watchlists` WRITE;
/*!40000 ALTER TABLE `watchlists` DISABLE KEYS */;
/*!40000 ALTER TABLE `watchlists` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-09-11  6:28:10
