-- MySQL dump 10.13  Distrib 8.0.43, for Linux (aarch64)
--
-- Host: localhost    Database: board_assignment
-- ------------------------------------------------------
-- Server version	8.0.43

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
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `comments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `post_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `parent_id` bigint unsigned DEFAULT NULL,
  `body` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `comments_post_id_foreign` (`post_id`),
  KEY `comments_user_id_foreign` (`user_id`),
  KEY `comments_parent_id_foreign` (`parent_id`),
  CONSTRAINT `comments_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `comments_post_id_foreign` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comments`
--

LOCK TABLES `comments` WRITE;
/*!40000 ALTER TABLE `comments` DISABLE KEYS */;
INSERT INTO `comments` VALUES (1,22,2,NULL,'정말 유용한 정보네요! 이제 Handler.php를 직접 건드릴 필요가 없겠군요.','2025-07-20 06:59:22','2025-07-20 06:59:22'),(2,23,4,NULL,'HATEOAS는 아직 개념이 좀 어렵네요. 혹시 참고할 만한 자료가 있을까요?','2025-07-23 06:59:22','2025-07-23 06:59:22'),(3,30,10,NULL,'React 컴파일러가 정말 기대됩니다. Vite처럼 빨라질까요?','2025-08-02 06:59:22','2025-08-02 06:59:22'),(4,24,1,NULL,'사진만 봐도 가슴이 뻥 뚫리는 기분입니다! 저도 다음 휴가 때 가봐야겠어요.','2025-07-25 06:59:22','2025-07-25 06:59:22'),(5,29,7,NULL,'실행 계획 분석의 중요성에 대해 다시 한번 깨닫고 갑니다. 좋은 글 감사합니다!','2025-07-31 06:59:22','2025-07-31 06:59:22'),(6,31,2,NULL,'Saga 패턴 외에 Choreography 기반의 접근 방식도 있던데, 두 방식의 장단점이 궁금하네요.','2025-08-02 06:59:22','2025-08-02 06:59:22'),(7,27,5,NULL,'감성적인 글이네요. 저도 오늘 퇴근길에는 주변을 좀 둘러봐야겠어요.','2025-07-28 06:59:22','2025-07-28 06:59:22'),(8,22,1,1,'네 맞아요. 코드가 훨씬 중앙집중적으로 관리되어서 좋은 것 같아요.','2025-07-21 06:59:22','2025-07-21 06:59:22'),(9,30,9,3,'아마도요! 빌드 속도뿐만 아니라 런타임 성능도 개선된다고 하더라고요.','2025-08-03 06:59:22','2025-08-03 06:59:22'),(10,31,10,6,'좋은 질문입니다! Choreography 방식은 각 서비스의 독립성이 높다는 장점이 있지만, 전체 트랜잭션 흐름을 파악하기 어렵다는 단점이 있죠.','2025-08-03 06:59:22','2025-08-03 06:59:22'),(11,23,7,NULL,'오 좋은 글입니다 :)','2025-08-03 07:06:03','2025-08-03 07:34:54');
/*!40000 ALTER TABLE `comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `likes`
--

DROP TABLE IF EXISTS `likes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `likes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `post_id` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `likes_post_id_user_id_unique` (`post_id`,`user_id`),
  KEY `likes_user_id_foreign` (`user_id`),
  CONSTRAINT `likes_post_id_foreign` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `likes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `likes`
--

LOCK TABLES `likes` WRITE;
/*!40000 ALTER TABLE `likes` DISABLE KEYS */;
INSERT INTO `likes` VALUES (1,22,1,'2025-07-21 07:00:42','2025-07-21 07:00:42'),(2,22,2,'2025-07-22 07:00:42','2025-07-22 07:00:42'),(3,22,10,'2025-07-24 07:00:42','2025-07-24 07:00:42'),(4,23,2,'2025-07-23 07:00:42','2025-07-23 07:00:42'),(5,23,4,'2025-07-24 07:00:42','2025-07-24 07:00:42'),(6,23,7,'2025-07-25 07:00:42','2025-07-25 07:00:42'),(7,24,1,'2025-07-25 07:00:42','2025-07-25 07:00:42'),(8,24,3,'2025-07-26 07:00:42','2025-07-26 07:00:42'),(9,26,10,'2025-07-27 07:00:42','2025-07-27 07:00:42'),(10,27,3,'2025-07-28 07:00:42','2025-07-28 07:00:42'),(11,27,5,'2025-07-29 07:00:42','2025-07-29 07:00:42'),(12,27,6,'2025-07-30 07:00:42','2025-07-30 07:00:42'),(13,29,7,'2025-07-31 07:00:42','2025-07-31 07:00:42'),(14,29,8,'2025-08-01 07:00:42','2025-08-01 07:00:42'),(15,30,1,'2025-08-02 07:00:42','2025-08-02 07:00:42'),(16,30,9,'2025-08-02 07:00:42','2025-08-02 07:00:42'),(17,30,10,'2025-08-03 07:00:42','2025-08-03 07:00:42'),(18,31,2,'2025-08-02 07:00:42','2025-08-02 07:00:42'),(19,31,9,'2025-08-03 07:00:42','2025-08-03 07:00:42'),(20,31,10,'2025-08-03 07:00:42','2025-08-03 07:00:42');
/*!40000 ALTER TABLE `likes` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2025_08_02_094745_initial_schema',1),(2,'2025_08_02_095821_create_cache_table',1),(3,'2025_08_02_102304_add_expires_at_to_personal_access_tokens_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
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
  `expires_at` timestamp NULL DEFAULT NULL,
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
-- Table structure for table `posts`
--

DROP TABLE IF EXISTS `posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `posts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `body` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `posts_user_id_foreign` (`user_id`),
  CONSTRAINT `posts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `posts`
--

LOCK TABLES `posts` WRITE;
/*!40000 ALTER TABLE `posts` DISABLE KEYS */;
INSERT INTO `posts` VALUES (22,1,'Laravel 12 예외 처리, 이렇게 바뀌었습니다','Laravel 12부터는 bootstrap/app.php에서 예외를 처리하는 것이 공식적인 방법이 되었습니다. 더 이상 Exception/Handler.php를 직접 수정할 필요가 없습니다. 코드가 훨씬 깔끔해지네요.','2025-07-19 06:53:22','2025-07-20 06:53:22'),(23,2,'RESTful API 설계 원칙 5가지','좋은 API를 만들기 위한 5가지 원칙을 공유합니다. 1. 자원 기반의 URI... 2. HTTP 메소드의 명확한 사용... 3. 적절한 상태 코드 반환... 4. 페이징 처리... 5. HATEOAS... 이 원칙들만 지켜도 훨씬 나은 API를 만들 수 있습니다.','2025-07-22 06:53:22','2025-07-22 06:53:22'),(24,3,'설악산 대청봉 1박 2일 코스 후기','지난 주말, 설악산 대청봉에 다녀왔습니다. 중청대피소에서 1박을 했는데, 밤하늘의 별이 정말 잊히지 않네요. 일출도 장관이었습니다. 등산 좋아하시는 분들께 강력 추천합니다.','2025-07-24 06:53:22','2025-07-24 06:53:22'),(25,4,'알고리즘 문제 풀이: 백준 1001번','오늘은 간단한 A-B 문제로 알고리즘 스터디를 시작했습니다. 간단한 문제라도 꾸준히 푸는 습관이 중요한 것 같아요. 내일은 동적 계획법 문제를 풀어볼 예정입니다.','2025-07-25 06:53:22','2025-07-26 06:53:22'),(26,5,'Figma 신규 기능: 변수(Variables) 사용법','이번에 Figma에 변수 기능이 추가되면서 디자인 시스템 관리가 훨씬 편해졌습니다. 색상, 간격 등을 변수로 관리하니 일관성 유지에 큰 도움이 됩니다.','2025-07-26 06:53:22','2025-07-26 06:53:22'),(27,6,'골목길에서 마주친 계절','오래된 골목길을 걷다 보면, 담벼락에 핀 작은 꽃 한 송이에서도 계절의 변화를 느낄 수 있다. 바쁜 일상이지만 가끔은 하늘을 올려다보는 여유를 가져보는 건 어떨까.','2025-07-27 06:53:22','2025-07-27 06:53:22'),(28,7,'성공적인 프로젝트를 위한 회고(Retrospective) 방법','애자일 방법론에서 회고는 정말 중요합니다. KPT(Keep, Problem, Try) 방법론을 사용하니 팀원들의 의견을 효과적으로 수렴하고 다음 스프린트를 더 잘 계획할 수 있었습니다.','2025-07-28 06:53:22','2025-07-29 06:53:22'),(29,8,'SELECT 쿼리 성능 최적화 팁','N+1 문제를 피하기 위해 Eager Loading을 적극적으로 활용하고, WHERE 절에 들어가는 컬럼에는 인덱스를 생성하는 것이 기본입니다. 실행 계획(Execution Plan)을 분석하는 습관을 들입시다.','2025-07-30 06:53:22','2025-07-30 06:53:22'),(30,9,'React 19, 이것만은 알고 가자!','React 19에서는 새로운 컴파일러와 Actions 기능이 도입될 예정입니다. 개발 생산성이 크게 향상될 것으로 기대됩니다. 미리 공식 문서를 읽어보는 것을 추천합니다.','2025-08-01 06:53:22','2025-08-01 06:53:22'),(31,10,'MSA 환경에서의 데이터 일관성 문제','마이크로서비스 아키텍처에서는 분산 트랜잭션 처리가 가장 큰 숙제 중 하나입니다. Saga 패턴을 적용하여 데이터 일관성을 유지하는 방법에 대해 연구하고 있습니다.','2025-08-02 06:53:22','2025-08-02 06:53:22');
/*!40000 ALTER TABLE `posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_username_unique` (`username`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'kim_laravel','김민준','minjun.kim@example.com','password','2025-07-04 06:52:15','2025-07-04 06:52:15'),(2,'lee_dev','이서연','seoyeon.lee@example.com','password','2025-07-09 06:52:15','2025-07-09 06:52:15'),(3,'park_hiker','박지훈','jihun.park@example.com','password','2025-07-12 06:52:15','2025-07-12 06:52:15'),(4,'choi_coder','최수빈','subin.choi@example.com','password','2025-07-14 06:52:15','2025-07-19 06:52:15'),(5,'jung_design','정은지','eunji.jung@example.com','password','2025-07-16 06:52:15','2025-07-16 06:52:15'),(6,'han_writer','한예슬','yeseul.han@example.com','password','2025-07-20 06:52:15','2025-07-24 06:52:15'),(7,'kang_pm','강현우','hyunwoo.kang@example.com','password','2025-07-24 06:52:15','2025-07-24 06:52:15'),(8,'yoon_db','윤지아','jia.yoon@example.com','password','2025-07-27 06:52:15','2025-07-29 06:52:15'),(9,'song_frontend','송하준','hajun.song@example.com','password','2025-07-31 06:52:15','2025-07-31 06:52:15'),(10,'lim_backend','임도윤','doyun.lim@example.com','password','2025-08-02 06:52:15','2025-08-02 06:52:15');
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

-- Dump completed on 2025-08-03 11:30:54
