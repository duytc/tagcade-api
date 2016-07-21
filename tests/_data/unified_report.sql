-- MySQL dump 10.15  Distrib 10.0.24-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: tagcade_api
-- ------------------------------------------------------
-- Server version	10.0.24-MariaDB-1~wheezy

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
-- Table structure for table `action_log`
--

DROP TABLE IF EXISTS `action_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `ip` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `server_ip` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `action` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `data` longtext COLLATE utf8_unicode_ci COMMENT '(DC2Type:json_array)',
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_B2C5F685A76ED395` (`user_id`),
  CONSTRAINT `FK_B2C5F685A76ED395` FOREIGN KEY (`user_id`) REFERENCES `core_user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log`
--

LOCK TABLES `action_log` WRITE;
/*!40000 ALTER TABLE `action_log` DISABLE KEYS */;
INSERT INTO `action_log` VALUES (1,1,'192.168.55.1','192.168.55.100','LOGIN',NULL,'2016-01-15 10:44:49'),(2,1,'192.168.55.1','192.168.55.100','CREATE/UPDATE','{\"action\":\"CREATE\\/UPDATE\",\"entity\":{\"className\":\"Publisher\",\"id\":null,\"name\":\"usatoday\"},\"affectedEntities\":[],\"changedFields\":[{\"name\":\"username\",\"oldVal\":null,\"newVal\":\"usatoday\",\"startDate\":null,\"endDate\":null},{\"name\":\"usernameCanonical\",\"oldVal\":null,\"newVal\":\"usatoday\",\"startDate\":null,\"endDate\":null},{\"name\":\"enabled\",\"oldVal\":\"false\",\"newVal\":\"true\",\"startDate\":null,\"endDate\":null},{\"name\":\"password\",\"oldVal\":\"***\",\"newVal\":\"***\",\"startDate\":null,\"endDate\":null},{\"name\":\"roles\",\"oldVal\":\"a:1:{i:0;s:9:\\\"ROLE_USER\\\";}\",\"newVal\":\"a:4:{i:0;s:14:\\\"MODULE_DISPLAY\\\";i:1;s:16:\\\"MODULE_ANALYTICS\\\";i:2;s:14:\\\"ROLE_PUBLISHER\\\";i:3;s:9:\\\"ROLE_USER\\\";}\",\"startDate\":null,\"endDate\":null},{\"name\":\"id\",\"oldVal\":null,\"newVal\":2,\"startDate\":null,\"endDate\":null},{\"name\":\"email\",\"oldVal\":null,\"newVal\":\"admin@usatoday.com\",\"startDate\":null,\"endDate\":null},{\"name\":\"emailCanonical\",\"oldVal\":null,\"newVal\":\"admin@usatoday.com\",\"startDate\":null,\"endDate\":null},{\"name\":\"uuid\",\"oldVal\":null,\"newVal\":\"076c22ee-c585-5e86-ba2f-a7ee2241540f\",\"startDate\":null,\"endDate\":null},{\"name\":\"firstName\",\"oldVal\":null,\"newVal\":\"USA\",\"startDate\":null,\"endDate\":null},{\"name\":\"company\",\"oldVal\":null,\"newVal\":\"USA Today Inc\",\"startDate\":null,\"endDate\":null},{\"name\":\"billingRate\",\"oldVal\":null,\"newVal\":\"1.1\",\"startDate\":null,\"endDate\":null}]}','2016-01-15 10:45:40'),(3,2,'192.168.55.1','192.168.55.100','CREATE/UPDATE','{\"action\":\"CREATE\\/UPDATE\",\"entity\":{\"className\":\"Site\",\"id\":null,\"name\":1},\"affectedEntities\":[],\"changedFields\":[{\"name\":\"id\",\"oldVal\":null,\"newVal\":1,\"startDate\":null,\"endDate\":null},{\"name\":\"name\",\"oldVal\":null,\"newVal\":\"USA Today\",\"startDate\":null,\"endDate\":null},{\"name\":\"domain\",\"oldVal\":null,\"newVal\":\"usatoday.com\",\"startDate\":null,\"endDate\":null},{\"name\":\"siteToken\",\"oldVal\":null,\"newVal\":\"857a43603dd1c2325bea18bbf83925b1\",\"startDate\":null,\"endDate\":null}]}','2016-01-15 10:50:52'),(4,2,'192.168.55.1','192.168.55.100','CREATE/UPDATE','{\"action\":\"CREATE\\/UPDATE\",\"entity\":{\"className\":\"AdNetwork\",\"id\":null,\"name\":1},\"affectedEntities\":[],\"changedFields\":[{\"name\":\"id\",\"oldVal\":null,\"newVal\":1,\"startDate\":null,\"endDate\":null},{\"name\":\"name\",\"oldVal\":null,\"newVal\":\"Local Ad Network\",\"startDate\":null,\"endDate\":null},{\"name\":\"url\",\"oldVal\":null,\"newVal\":\"http:\\/\\/localhost\\/adnetwork\",\"startDate\":null,\"endDate\":null}]}','2016-01-15 10:51:02'),(5,2,'192.168.55.1','192.168.55.100','CREATE/UPDATE','{\"action\":\"CREATE\\/UPDATE\",\"entity\":{\"className\":\"LibraryDisplayAdSlot\",\"id\":null,\"name\":null},\"affectedEntities\":[],\"changedFields\":[{\"name\":\"id\",\"oldVal\":null,\"newVal\":1,\"startDate\":null,\"endDate\":null},{\"name\":\"name\",\"oldVal\":null,\"newVal\":\"Display AdSlot 1\",\"startDate\":null,\"endDate\":null},{\"name\":\"visible\",\"oldVal\":null,\"newVal\":\"true\",\"startDate\":null,\"endDate\":null},{\"name\":\"width\",\"oldVal\":null,\"newVal\":34,\"startDate\":null,\"endDate\":null},{\"name\":\"height\",\"oldVal\":null,\"newVal\":34,\"startDate\":null,\"endDate\":null},{\"name\":\"passbackMode\",\"oldVal\":null,\"newVal\":\"position\",\"startDate\":null,\"endDate\":null}]}','2016-01-15 10:51:24'),(6,2,'192.168.55.1','192.168.55.100','CREATE/UPDATE','{\"action\":\"CREATE\\/UPDATE\",\"entity\":{\"className\":\"LibrarySlotTag\",\"id\":null,\"name\":null},\"affectedEntities\":[],\"changedFields\":[{\"name\":\"id\",\"oldVal\":null,\"newVal\":1,\"startDate\":null,\"endDate\":null},{\"name\":\"position\",\"oldVal\":null,\"newVal\":1,\"startDate\":null,\"endDate\":null},{\"name\":\"refId\",\"oldVal\":null,\"newVal\":\"56986cd2821167.11237342\",\"startDate\":null,\"endDate\":null}]}','2016-01-15 10:51:47'),(7,2,'192.168.55.1','192.168.55.100','CREATE/UPDATE','{\"action\":\"CREATE\\/UPDATE\",\"entity\":{\"className\":\"LibrarySlotTag\",\"id\":null,\"name\":null},\"affectedEntities\":[],\"changedFields\":[{\"name\":\"id\",\"oldVal\":null,\"newVal\":2,\"startDate\":null,\"endDate\":null},{\"name\":\"position\",\"oldVal\":null,\"newVal\":2,\"startDate\":null,\"endDate\":null},{\"name\":\"refId\",\"oldVal\":null,\"newVal\":\"56986ce4e595d8.11924247\",\"startDate\":null,\"endDate\":null}]}','2016-01-15 10:52:05'),(8,2,'192.168.55.1','192.168.55.100','CREATE/UPDATE','{\"action\":\"CREATE\\/UPDATE\",\"entity\":{\"className\":\"LibraryDisplayAdSlot\",\"id\":null,\"name\":null},\"affectedEntities\":[],\"changedFields\":[{\"name\":\"id\",\"oldVal\":null,\"newVal\":2,\"startDate\":null,\"endDate\":null},{\"name\":\"name\",\"oldVal\":null,\"newVal\":\"Display AdSlot 2\",\"startDate\":null,\"endDate\":null},{\"name\":\"visible\",\"oldVal\":null,\"newVal\":\"true\",\"startDate\":null,\"endDate\":null},{\"name\":\"width\",\"oldVal\":null,\"newVal\":34,\"startDate\":null,\"endDate\":null},{\"name\":\"height\",\"oldVal\":null,\"newVal\":34,\"startDate\":null,\"endDate\":null},{\"name\":\"passbackMode\",\"oldVal\":null,\"newVal\":\"position\",\"startDate\":null,\"endDate\":null}]}','2016-01-15 10:52:20'),(9,2,'192.168.55.1','192.168.55.100','CREATE/UPDATE','{\"action\":\"CREATE\\/UPDATE\",\"entity\":{\"className\":\"LibrarySlotTag\",\"id\":null,\"name\":null},\"affectedEntities\":[],\"changedFields\":[{\"name\":\"id\",\"oldVal\":null,\"newVal\":3,\"startDate\":null,\"endDate\":null},{\"name\":\"position\",\"oldVal\":null,\"newVal\":1,\"startDate\":null,\"endDate\":null},{\"name\":\"refId\",\"oldVal\":null,\"newVal\":\"56986d09c351b8.47232662\",\"startDate\":null,\"endDate\":null}]}','2016-01-15 10:52:42'),(10,2,'192.168.55.1','192.168.55.100','CREATE/UPDATE','{\"action\":\"CREATE\\/UPDATE\",\"entity\":{\"className\":\"LibrarySlotTag\",\"id\":null,\"name\":null},\"affectedEntities\":[],\"changedFields\":[{\"name\":\"id\",\"oldVal\":null,\"newVal\":4,\"startDate\":null,\"endDate\":null},{\"name\":\"position\",\"oldVal\":null,\"newVal\":2,\"startDate\":null,\"endDate\":null},{\"name\":\"refId\",\"oldVal\":null,\"newVal\":\"56986d1d64b068.58616459\",\"startDate\":null,\"endDate\":null}]}','2016-01-15 10:53:02'),(11,2,'192.168.55.1','192.168.55.100','CREATE/UPDATE','{\"action\":\"CREATE\\/UPDATE\",\"entity\":{\"className\":\"LibrarySlotTag\",\"id\":null,\"name\":null},\"affectedEntities\":[],\"changedFields\":[{\"name\":\"id\",\"oldVal\":null,\"newVal\":5,\"startDate\":null,\"endDate\":null},{\"name\":\"position\",\"oldVal\":null,\"newVal\":3,\"startDate\":null,\"endDate\":null},{\"name\":\"refId\",\"oldVal\":null,\"newVal\":\"56986d2f8361d4.33474707\",\"startDate\":null,\"endDate\":null}]}','2016-01-15 10:53:20'),(12,2,'192.168.55.1','192.168.55.100','CREATE/UPDATE','{\"action\":\"CREATE\\/UPDATE\",\"entity\":{\"className\":\"LibraryDisplayAdSlot\",\"id\":null,\"name\":null},\"affectedEntities\":[],\"changedFields\":[{\"name\":\"id\",\"oldVal\":null,\"newVal\":3,\"startDate\":null,\"endDate\":null},{\"name\":\"name\",\"oldVal\":null,\"newVal\":\"Display AdSlot 3\",\"startDate\":null,\"endDate\":null},{\"name\":\"visible\",\"oldVal\":null,\"newVal\":\"true\",\"startDate\":null,\"endDate\":null},{\"name\":\"width\",\"oldVal\":null,\"newVal\":33,\"startDate\":null,\"endDate\":null},{\"name\":\"height\",\"oldVal\":null,\"newVal\":343,\"startDate\":null,\"endDate\":null},{\"name\":\"passbackMode\",\"oldVal\":null,\"newVal\":\"position\",\"startDate\":null,\"endDate\":null}]}','2016-01-15 10:55:38'),(13,2,'192.168.55.1','192.168.55.100','CREATE/UPDATE','{\"action\":\"CREATE\\/UPDATE\",\"entity\":{\"className\":\"LibrarySlotTag\",\"id\":null,\"name\":null},\"affectedEntities\":[],\"changedFields\":[{\"name\":\"id\",\"oldVal\":null,\"newVal\":6,\"startDate\":null,\"endDate\":null},{\"name\":\"position\",\"oldVal\":null,\"newVal\":1,\"startDate\":null,\"endDate\":null},{\"name\":\"refId\",\"oldVal\":null,\"newVal\":\"56986e0e021a20.53611356\",\"startDate\":null,\"endDate\":null}]}','2016-01-15 10:57:02'),(14,2,'192.168.55.1','192.168.55.100','CREATE/UPDATE','{\"action\":\"CREATE\\/UPDATE\",\"entity\":{\"className\":\"LibrarySlotTag\",\"id\":null,\"name\":null},\"affectedEntities\":[],\"changedFields\":[{\"name\":\"id\",\"oldVal\":null,\"newVal\":7,\"startDate\":null,\"endDate\":null},{\"name\":\"position\",\"oldVal\":null,\"newVal\":2,\"startDate\":null,\"endDate\":null},{\"name\":\"refId\",\"oldVal\":null,\"newVal\":\"56986e225358d1.19820599\",\"startDate\":null,\"endDate\":null}]}','2016-01-15 10:57:23'),(15,2,'192.168.55.1','192.168.55.100','CREATE/UPDATE','{\"action\":\"CREATE\\/UPDATE\",\"entity\":{\"className\":\"LibrarySlotTag\",\"id\":null,\"name\":null},\"affectedEntities\":[],\"changedFields\":[{\"name\":\"id\",\"oldVal\":null,\"newVal\":8,\"startDate\":null,\"endDate\":null},{\"name\":\"position\",\"oldVal\":null,\"newVal\":3,\"startDate\":null,\"endDate\":null},{\"name\":\"refId\",\"oldVal\":null,\"newVal\":\"56986e36a95780.71207988\",\"startDate\":null,\"endDate\":null}]}','2016-01-15 10:57:43'),(16,2,'192.168.55.1','192.168.55.100','CREATE/UPDATE','{\"action\":\"CREATE\\/UPDATE\",\"entity\":{\"className\":\"LibraryDynamicAdSlot\",\"id\":null,\"name\":null},\"affectedEntities\":[],\"changedFields\":[{\"name\":\"id\",\"oldVal\":null,\"newVal\":4,\"startDate\":null,\"endDate\":null},{\"name\":\"name\",\"oldVal\":null,\"newVal\":\"Dynamic  AdSlot 1\",\"startDate\":null,\"endDate\":null},{\"name\":\"visible\",\"oldVal\":null,\"newVal\":\"true\",\"startDate\":null,\"endDate\":null}]}','2016-01-15 10:58:18'),(17,2,'192.168.55.1','192.168.55.100','CREATE/UPDATE','{\"action\":\"CREATE\\/UPDATE\",\"entity\":{\"className\":\"LibraryDisplayAdSlot\",\"id\":null,\"name\":null},\"affectedEntities\":[],\"changedFields\":[{\"name\":\"id\",\"oldVal\":null,\"newVal\":5,\"startDate\":null,\"endDate\":null},{\"name\":\"name\",\"oldVal\":null,\"newVal\":\"Display AdSlot 4\",\"startDate\":null,\"endDate\":null},{\"name\":\"visible\",\"oldVal\":null,\"newVal\":\"true\",\"startDate\":null,\"endDate\":null},{\"name\":\"width\",\"oldVal\":null,\"newVal\":34,\"startDate\":null,\"endDate\":null},{\"name\":\"height\",\"oldVal\":null,\"newVal\":34,\"startDate\":null,\"endDate\":null},{\"name\":\"passbackMode\",\"oldVal\":null,\"newVal\":\"position\",\"startDate\":null,\"endDate\":null}]}','2016-01-15 10:58:35'),(18,2,'192.168.55.1','192.168.55.100','CREATE/UPDATE','{\"action\":\"CREATE\\/UPDATE\",\"entity\":{\"className\":\"LibrarySlotTag\",\"id\":null,\"name\":null},\"affectedEntities\":[],\"changedFields\":[{\"name\":\"id\",\"oldVal\":null,\"newVal\":9,\"startDate\":null,\"endDate\":null},{\"name\":\"position\",\"oldVal\":null,\"newVal\":1,\"startDate\":null,\"endDate\":null},{\"name\":\"refId\",\"oldVal\":null,\"newVal\":\"56986e941ee7a2.59349775\",\"startDate\":null,\"endDate\":null}]}','2016-01-15 10:59:16'),(19,2,'192.168.55.1','192.168.55.100','CREATE/UPDATE','{\"action\":\"CREATE\\/UPDATE\",\"entity\":{\"className\":\"LibrarySlotTag\",\"id\":null,\"name\":null},\"affectedEntities\":[],\"changedFields\":[{\"name\":\"id\",\"oldVal\":null,\"newVal\":10,\"startDate\":null,\"endDate\":null},{\"name\":\"position\",\"oldVal\":null,\"newVal\":2,\"startDate\":null,\"endDate\":null},{\"name\":\"refId\",\"oldVal\":null,\"newVal\":\"56986ea6018566.28682516\",\"startDate\":null,\"endDate\":null}]}','2016-01-15 10:59:34'),(20,2,'192.168.55.1','192.168.55.100','CREATE/UPDATE','{\"action\":\"CREATE\\/UPDATE\",\"entity\":{\"className\":\"LibraryDynamicAdSlot\",\"id\":null,\"name\":null},\"affectedEntities\":[],\"changedFields\":[{\"name\":\"id\",\"oldVal\":null,\"newVal\":6,\"startDate\":null,\"endDate\":null},{\"name\":\"name\",\"oldVal\":null,\"newVal\":\"Display AdSlot 2\",\"startDate\":null,\"endDate\":null},{\"name\":\"visible\",\"oldVal\":null,\"newVal\":\"true\",\"startDate\":null,\"endDate\":null}]}','2016-01-15 11:04:15'),(21,2,'192.168.55.1','192.168.55.100','UPDATE','{\"action\":\"UPDATE\",\"entity\":{\"className\":\"LibraryDynamicAdSlot\",\"id\":6,\"name\":null},\"affectedEntities\":[],\"changedFields\":[{\"name\":\"name\",\"oldVal\":\"Display AdSlot 2\",\"newVal\":\"Dynamic  AdSlot 2\",\"startDate\":null,\"endDate\":null}]}','2016-01-15 11:04:38'),(22,2,'192.168.55.1','192.168.55.100','CREATE/UPDATE','{\"action\":\"CREATE\\/UPDATE\",\"entity\":{\"className\":\"LibraryDynamicAdSlot\",\"id\":null,\"name\":null},\"affectedEntities\":[],\"changedFields\":[{\"name\":\"id\",\"oldVal\":null,\"newVal\":7,\"startDate\":null,\"endDate\":null},{\"name\":\"name\",\"oldVal\":null,\"newVal\":\"Dynamic  AdSlot 3\",\"startDate\":null,\"endDate\":null},{\"name\":\"visible\",\"oldVal\":null,\"newVal\":\"true\",\"startDate\":null,\"endDate\":null}]}','2016-01-15 11:05:41'),(23,2,'192.168.55.1','192.168.55.100','CREATE/UPDATE','{\"action\":\"CREATE\\/UPDATE\",\"entity\":{\"className\":\"Site\",\"id\":null,\"name\":2},\"affectedEntities\":[],\"changedFields\":[{\"name\":\"id\",\"oldVal\":null,\"newVal\":2,\"startDate\":null,\"endDate\":null},{\"name\":\"name\",\"oldVal\":null,\"newVal\":\"site1\",\"startDate\":null,\"endDate\":null},{\"name\":\"domain\",\"oldVal\":null,\"newVal\":\"site1.com\",\"startDate\":null,\"endDate\":null},{\"name\":\"siteToken\",\"oldVal\":null,\"newVal\":\"cd095d73be0b78b932b2851f2c1250d9\",\"startDate\":null,\"endDate\":null}]}','2016-01-15 11:13:09'),(24,2,'192.168.55.1','192.168.55.100','CREATE/UPDATE','{\"action\":\"CREATE\\/UPDATE\",\"entity\":{\"className\":\"LibraryDisplayAdSlot\",\"id\":null,\"name\":null},\"affectedEntities\":[],\"changedFields\":[{\"name\":\"id\",\"oldVal\":null,\"newVal\":8,\"startDate\":null,\"endDate\":null},{\"name\":\"name\",\"oldVal\":null,\"newVal\":\"Display AdSlot 5\",\"startDate\":null,\"endDate\":null},{\"name\":\"visible\",\"oldVal\":null,\"newVal\":\"true\",\"startDate\":null,\"endDate\":null},{\"name\":\"width\",\"oldVal\":null,\"newVal\":34,\"startDate\":null,\"endDate\":null},{\"name\":\"height\",\"oldVal\":null,\"newVal\":34,\"startDate\":null,\"endDate\":null},{\"name\":\"passbackMode\",\"oldVal\":null,\"newVal\":\"position\",\"startDate\":null,\"endDate\":null}]}','2016-01-15 11:18:25'),(25,2,'192.168.55.1','192.168.55.100','CREATE/UPDATE','{\"action\":\"CREATE\\/UPDATE\",\"entity\":{\"className\":\"LibrarySlotTag\",\"id\":null,\"name\":null},\"affectedEntities\":[],\"changedFields\":[{\"name\":\"id\",\"oldVal\":null,\"newVal\":11,\"startDate\":null,\"endDate\":null},{\"name\":\"position\",\"oldVal\":null,\"newVal\":1,\"startDate\":null,\"endDate\":null},{\"name\":\"refId\",\"oldVal\":null,\"newVal\":\"5698731fa48e59.61889897\",\"startDate\":null,\"endDate\":null}]}','2016-01-15 11:18:39'),(26,2,'192.168.55.1','192.168.55.100','CREATE/UPDATE','{\"action\":\"CREATE\\/UPDATE\",\"entity\":{\"className\":\"LibrarySlotTag\",\"id\":null,\"name\":null},\"affectedEntities\":[],\"changedFields\":[{\"name\":\"id\",\"oldVal\":null,\"newVal\":12,\"startDate\":null,\"endDate\":null},{\"name\":\"position\",\"oldVal\":null,\"newVal\":2,\"startDate\":null,\"endDate\":null},{\"name\":\"refId\",\"oldVal\":null,\"newVal\":\"56987329e3a805.60121712\",\"startDate\":null,\"endDate\":null}]}','2016-01-15 11:18:50'),(27,1,'127.0.0.1','127.0.0.1','LOGIN',NULL,'2016-07-20 17:32:40'),(28,1,'127.0.0.1','127.0.0.1','LOGIN',NULL,'2016-07-20 17:33:10'),(29,1,'192.168.55.1','192.168.55.100','LOGIN',NULL,'2016-07-20 17:35:51'),(30,2,'192.168.55.1','192.168.55.100','DELETE','{\"action\":\"DELETE\",\"entity\":{\"className\":\"Site\",\"id\":1,\"name\":1},\"affectedEntities\":[],\"changedFields\":[]}','2016-07-20 17:36:10'),(31,2,'192.168.55.1','192.168.55.100','DELETE','{\"action\":\"DELETE\",\"entity\":{\"className\":\"Site\",\"id\":2,\"name\":2},\"affectedEntities\":[],\"changedFields\":[]}','2016-07-20 17:36:14'),(32,1,'192.168.55.1','192.168.55.100','UPDATE','{\"action\":\"UPDATE\",\"entity\":{\"className\":\"Publisher\",\"id\":2,\"name\":\"usatoday\"},\"affectedEntities\":[],\"changedFields\":[{\"name\":\"roles\",\"oldVal\":\"a:4:{i:0;s:14:\\\"MODULE_DISPLAY\\\";i:1;s:16:\\\"MODULE_ANALYTICS\\\";i:2;s:14:\\\"ROLE_PUBLISHER\\\";i:3;s:9:\\\"ROLE_USER\\\";}\",\"newVal\":\"a:6:{i:0;s:14:\\\"ROLE_PUBLISHER\\\";i:1;s:14:\\\"MODULE_DISPLAY\\\";i:2;s:16:\\\"MODULE_ANALYTICS\\\";i:3;s:21:\\\"MODULE_UNIFIED_REPORT\\\";i:4;s:20:\\\"MODULE_SUB_PUBLISHER\\\";i:5;s:9:\\\"ROLE_USER\\\";}\",\"startDate\":null,\"endDate\":null}]}','2016-07-20 17:36:43'),(33,1,'127.0.0.1','127.0.0.1','LOGIN',NULL,'2016-07-20 17:36:57'),(34,2,'192.168.55.1','192.168.55.100','CREATE/UPDATE','{\"action\":\"CREATE\\/UPDATE\",\"entity\":{\"className\":\"AdNetwork\",\"id\":null,\"name\":2},\"affectedEntities\":[],\"changedFields\":[{\"name\":\"id\",\"oldVal\":null,\"newVal\":2,\"startDate\":null,\"endDate\":null},{\"name\":\"name\",\"oldVal\":null,\"newVal\":\"Komoona\",\"startDate\":null,\"endDate\":null}]}','2016-07-20 17:37:04'),(35,2,'192.168.55.1','192.168.55.100','CREATE/UPDATE','{\"action\":\"CREATE\\/UPDATE\",\"entity\":{\"className\":\"Site\",\"id\":null,\"name\":3},\"affectedEntities\":[],\"changedFields\":[{\"name\":\"id\",\"oldVal\":null,\"newVal\":3,\"startDate\":null,\"endDate\":null},{\"name\":\"name\",\"oldVal\":null,\"newVal\":\"Gravity Tales\",\"startDate\":null,\"endDate\":null},{\"name\":\"domain\",\"oldVal\":null,\"newVal\":\"gravitytales.com\",\"startDate\":null,\"endDate\":null},{\"name\":\"siteToken\",\"oldVal\":null,\"newVal\":\"d2f11f17d04ddafd376e167a450af746\",\"startDate\":null,\"endDate\":null}]}','2016-07-20 17:37:12'),(36,2,'192.168.55.1','192.168.55.100','CREATE/UPDATE','{\"action\":\"CREATE\\/UPDATE\",\"entity\":{\"className\":\"DisplayAdSlot\",\"id\":null,\"name\":9},\"affectedEntities\":[],\"changedFields\":[{\"name\":\"id\",\"oldVal\":null,\"newVal\":9,\"startDate\":null,\"endDate\":null},{\"name\":\"deleteToken\",\"oldVal\":null,\"newVal\":\"0\",\"startDate\":null,\"endDate\":null}]}','2016-07-20 17:37:25'),(37,2,'192.168.55.1','192.168.55.100','CREATE/UPDATE','{\"action\":\"CREATE\\/UPDATE\",\"entity\":{\"className\":\"AdTag\",\"id\":null,\"name\":12},\"affectedEntities\":[],\"changedFields\":[{\"name\":\"id\",\"oldVal\":null,\"newVal\":12,\"startDate\":null,\"endDate\":null},{\"name\":\"position\",\"oldVal\":null,\"newVal\":1,\"startDate\":null,\"endDate\":null},{\"name\":\"active\",\"oldVal\":null,\"newVal\":1,\"startDate\":null,\"endDate\":null},{\"name\":\"refId\",\"oldVal\":null,\"newVal\":\"578f547b79e867.89963055\",\"startDate\":null,\"endDate\":null}]}','2016-07-20 17:37:47'),(38,2,'192.168.55.1','192.168.55.100','CREATE/UPDATE','{\"action\":\"CREATE\\/UPDATE\",\"entity\":{\"className\":\"AdTag\",\"id\":null,\"name\":13},\"affectedEntities\":[],\"changedFields\":[{\"name\":\"id\",\"oldVal\":null,\"newVal\":13,\"startDate\":null,\"endDate\":null},{\"name\":\"position\",\"oldVal\":null,\"newVal\":2,\"startDate\":null,\"endDate\":null},{\"name\":\"active\",\"oldVal\":null,\"newVal\":1,\"startDate\":null,\"endDate\":null},{\"name\":\"refId\",\"oldVal\":null,\"newVal\":\"578f548b20ead5.29613298\",\"startDate\":null,\"endDate\":null}]}','2016-07-20 17:38:03'),(39,2,'192.168.55.1','192.168.55.100','CREATE/UPDATE','{\"action\":\"CREATE\\/UPDATE\",\"entity\":{\"className\":\"AdTag\",\"id\":null,\"name\":14},\"affectedEntities\":[],\"changedFields\":[{\"name\":\"id\",\"oldVal\":null,\"newVal\":14,\"startDate\":null,\"endDate\":null},{\"name\":\"position\",\"oldVal\":null,\"newVal\":3,\"startDate\":null,\"endDate\":null},{\"name\":\"active\",\"oldVal\":null,\"newVal\":1,\"startDate\":null,\"endDate\":null},{\"name\":\"refId\",\"oldVal\":null,\"newVal\":\"578f54980d7971.43063407\",\"startDate\":null,\"endDate\":null}]}','2016-07-20 17:38:16'),(40,2,'192.168.55.1','192.168.55.100','CREATE/UPDATE','{\"action\":\"CREATE\\/UPDATE\",\"entity\":{\"className\":\"AdTag\",\"id\":null,\"name\":15},\"affectedEntities\":[],\"changedFields\":[{\"name\":\"id\",\"oldVal\":null,\"newVal\":15,\"startDate\":null,\"endDate\":null},{\"name\":\"position\",\"oldVal\":null,\"newVal\":4,\"startDate\":null,\"endDate\":null},{\"name\":\"active\",\"oldVal\":null,\"newVal\":1,\"startDate\":null,\"endDate\":null},{\"name\":\"refId\",\"oldVal\":null,\"newVal\":\"578f54a75c8a68.23825025\",\"startDate\":null,\"endDate\":null}]}','2016-07-20 17:38:31'),(41,2,'192.168.55.1','192.168.55.100','CREATE/UPDATE','{\"action\":\"CREATE\\/UPDATE\",\"entity\":{\"className\":\"AdTag\",\"id\":null,\"name\":16},\"affectedEntities\":[],\"changedFields\":[{\"name\":\"id\",\"oldVal\":null,\"newVal\":16,\"startDate\":null,\"endDate\":null},{\"name\":\"position\",\"oldVal\":null,\"newVal\":5,\"startDate\":null,\"endDate\":null},{\"name\":\"active\",\"oldVal\":null,\"newVal\":1,\"startDate\":null,\"endDate\":null},{\"name\":\"refId\",\"oldVal\":null,\"newVal\":\"578f54b95c7825.03028659\",\"startDate\":null,\"endDate\":null}]}','2016-07-20 17:38:49'),(42,2,'192.168.55.1','192.168.55.100','CREATE/UPDATE','{\"action\":\"CREATE\\/UPDATE\",\"entity\":{\"className\":\"Publisher\",\"id\":null,\"name\":\"sub1\"},\"affectedEntities\":[],\"changedFields\":[{\"name\":\"username\",\"oldVal\":null,\"newVal\":\"sub1\",\"startDate\":null,\"endDate\":null},{\"name\":\"usernameCanonical\",\"oldVal\":null,\"newVal\":\"sub1\",\"startDate\":null,\"endDate\":null},{\"name\":\"enabled\",\"oldVal\":\"false\",\"newVal\":\"true\",\"startDate\":null,\"endDate\":null},{\"name\":\"password\",\"oldVal\":\"***\",\"newVal\":\"***\",\"startDate\":null,\"endDate\":null},{\"name\":\"roles\",\"oldVal\":\"a:5:{i:0;s:14:\\\"MODULE_DISPLAY\\\";i:1;s:16:\\\"MODULE_ANALYTICS\\\";i:2;s:21:\\\"MODULE_UNIFIED_REPORT\\\";i:3;s:20:\\\"MODULE_SUB_PUBLISHER\\\";i:4;s:9:\\\"ROLE_USER\\\";}\",\"newVal\":\"a:6:{i:0;s:18:\\\"ROLE_SUB_PUBLISHER\\\";i:1;s:14:\\\"MODULE_DISPLAY\\\";i:2;s:16:\\\"MODULE_ANALYTICS\\\";i:3;s:21:\\\"MODULE_UNIFIED_REPORT\\\";i:4;s:20:\\\"MODULE_SUB_PUBLISHER\\\";i:5;s:9:\\\"ROLE_USER\\\";}\",\"startDate\":null,\"endDate\":null},{\"name\":\"id\",\"oldVal\":null,\"newVal\":3,\"startDate\":null,\"endDate\":null},{\"name\":\"email\",\"oldVal\":null,\"newVal\":\"sub1@tagcade.com\",\"startDate\":null,\"endDate\":null},{\"name\":\"emailCanonical\",\"oldVal\":null,\"newVal\":\"sub1@tagcade.com\",\"startDate\":null,\"endDate\":null},{\"name\":\"demandSourceTransparency\",\"oldVal\":\"false\",\"newVal\":\"true\",\"startDate\":null,\"endDate\":null},{\"name\":\"enableViewTagcadeReport\",\"oldVal\":\"false\",\"newVal\":\"true\",\"startDate\":null,\"endDate\":null}]}','2016-07-20 17:39:11');
/*!40000 ALTER TABLE `action_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `core_ad_network`
--

DROP TABLE IF EXISTS `core_ad_network`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `core_ad_network` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `publisher_id` int(11) DEFAULT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `default_cpm_rate` decimal(10,4) DEFAULT NULL,
  `active_ad_tags_count` int(11) NOT NULL DEFAULT '0',
  `paused_ad_tags_count` int(11) NOT NULL DEFAULT '0',
  `network_partner_id` int(11) DEFAULT NULL,
  `username` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `encrypted_password` longblob,
  `impression_cap` int(11) DEFAULT NULL,
  `network_opportunity_cap` int(11) DEFAULT NULL,
  `email_hook_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `publisher_network_partner_unique_key` (`publisher_id`,`network_partner_id`),
  KEY `IDX_9182EAD940C86FCE` (`publisher_id`),
  KEY `IDX_9182EAD94AC20742` (`network_partner_id`),
  CONSTRAINT `FK_9182EAD940C86FCE` FOREIGN KEY (`publisher_id`) REFERENCES `core_user` (`id`),
  CONSTRAINT `FK_9182EAD94AC20742` FOREIGN KEY (`network_partner_id`) REFERENCES `core_partner` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `core_ad_network`
--

LOCK TABLES `core_ad_network` WRITE;
/*!40000 ALTER TABLE `core_ad_network` DISABLE KEYS */;
INSERT INTO `core_ad_network` VALUES (1,2,'Local Ad Network','http://localhost/adnetwork',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL),(2,2,'Komoona',NULL,NULL,5,0,1,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `core_ad_network` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `core_ad_slot`
--

DROP TABLE IF EXISTS `core_ad_slot`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `core_ad_slot` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) DEFAULT NULL,
  `library_ad_slot_id` int(11) DEFAULT NULL,
  `auto_create` tinyint(1) NOT NULL DEFAULT '0',
  `deleted_at` date DEFAULT NULL,
  `delete_token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `slot_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ad_slot_by_site_and_library_key` (`site_id`,`library_ad_slot_id`,`delete_token`),
  KEY `IDX_6D6C731F6BD1646` (`site_id`),
  KEY `IDX_6D6C73170BBCB64` (`library_ad_slot_id`),
  CONSTRAINT `FK_6D6C73170BBCB64` FOREIGN KEY (`library_ad_slot_id`) REFERENCES `library_ad_slot` (`id`),
  CONSTRAINT `FK_6D6C731F6BD1646` FOREIGN KEY (`site_id`) REFERENCES `core_site` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `core_ad_slot`
--

LOCK TABLES `core_ad_slot` WRITE;
/*!40000 ALTER TABLE `core_ad_slot` DISABLE KEYS */;
INSERT INTO `core_ad_slot` VALUES (1,1,1,0,'2016-07-20','578f541a351c73.09514653','display'),(2,1,3,0,'2016-07-20','0','display'),(3,1,4,0,'2016-07-20','578f541a362e09.83772238','dynamic'),(4,1,5,0,'2016-07-20','0','display'),(5,1,6,0,'2016-07-20','578f541a373d44.18681904','dynamic'),(6,2,5,0,'2016-07-20','0','display'),(7,2,6,0,'2016-07-20','578f541e7e4f44.88869048','dynamic'),(9,3,9,0,NULL,'0','display');
/*!40000 ALTER TABLE `core_ad_slot` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `core_ad_tag`
--

DROP TABLE IF EXISTS `core_ad_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `core_ad_tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ad_slot_id` int(11) DEFAULT NULL,
  `library_ad_tag_id` int(11) DEFAULT NULL,
  `position` int(11) NOT NULL DEFAULT '1',
  `active` smallint(6) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` date DEFAULT NULL,
  `frequency_cap` int(11) DEFAULT NULL,
  `rotation` int(11) DEFAULT NULL,
  `ref_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `impression_cap` int(11) DEFAULT NULL,
  `network_opportunity_cap` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_D122BEED94AFF818` (`ad_slot_id`),
  KEY `IDX_D122BEED3DC10368` (`library_ad_tag_id`),
  CONSTRAINT `FK_D122BEED3DC10368` FOREIGN KEY (`library_ad_tag_id`) REFERENCES `library_ad_tag` (`id`),
  CONSTRAINT `FK_D122BEED94AFF818` FOREIGN KEY (`ad_slot_id`) REFERENCES `core_ad_slot` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `core_ad_tag`
--

LOCK TABLES `core_ad_tag` WRITE;
/*!40000 ALTER TABLE `core_ad_tag` DISABLE KEYS */;
INSERT INTO `core_ad_tag` VALUES (1,1,1,1,1,'2016-01-15 10:53:56','2016-07-20 17:36:10','2016-07-20',NULL,NULL,'56986cd2821167.11237342',NULL,NULL),(2,1,2,1,1,'2016-01-15 10:53:56','2016-07-20 17:36:10','2016-07-20',NULL,NULL,'56986ce4e595d8.11924247',NULL,NULL),(3,2,6,1,1,'2016-01-15 11:00:13','2016-07-20 17:36:10','2016-07-20',NULL,NULL,'56986e0e021a20.53611356',NULL,NULL),(4,2,7,1,1,'2016-01-15 11:00:13','2016-07-20 17:36:10','2016-07-20',NULL,NULL,'56986e225358d1.19820599',NULL,NULL),(5,2,8,1,1,'2016-01-15 11:00:13','2016-07-20 17:36:10','2016-07-20',NULL,NULL,'56986e36a95780.71207988',NULL,NULL),(6,4,9,1,1,'2016-01-15 11:05:52','2016-07-20 17:36:10','2016-07-20',NULL,NULL,'56986e941ee7a2.59349775',NULL,NULL),(7,4,10,1,1,'2016-01-15 11:05:52','2016-07-20 17:36:10','2016-07-20',NULL,NULL,'56986ea6018566.28682516',NULL,NULL),(8,6,9,1,1,'2016-01-15 11:13:25','2016-07-20 17:36:14','2016-07-20',NULL,NULL,'56986e941ee7a2.59349775',NULL,NULL),(9,6,10,1,1,'2016-01-15 11:13:25','2016-07-20 17:36:14','2016-07-20',NULL,NULL,'56986ea6018566.28682516',NULL,NULL),(12,9,13,1,1,'2016-07-20 17:37:47','2016-07-20 17:37:47',NULL,NULL,NULL,'578f547b79e867.89963055',NULL,NULL),(13,9,14,2,1,'2016-07-20 17:38:03','2016-07-20 17:38:03',NULL,NULL,NULL,'578f548b20ead5.29613298',NULL,NULL),(14,9,15,3,1,'2016-07-20 17:38:16','2016-07-20 17:38:16',NULL,NULL,NULL,'578f54980d7971.43063407',NULL,NULL),(15,9,16,4,1,'2016-07-20 17:38:31','2016-07-20 17:38:31',NULL,NULL,NULL,'578f54a75c8a68.23825025',NULL,NULL),(16,9,17,5,1,'2016-07-20 17:38:49','2016-07-20 17:38:49',NULL,NULL,NULL,'578f54b95c7825.03028659',NULL,NULL);
/*!40000 ALTER TABLE `core_ad_tag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `core_billing_configuration`
--

DROP TABLE IF EXISTS `core_billing_configuration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `core_billing_configuration` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `publisher_id` int(11) DEFAULT NULL,
  `module` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `tiers` longtext COLLATE utf8_unicode_ci COMMENT '(DC2Type:json_array)',
  `billing_factor` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `default_config` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `publisher_billing_configuration_unique_key` (`publisher_id`,`module`),
  KEY `IDX_8B11973240C86FCE` (`publisher_id`),
  CONSTRAINT `FK_8B11973240C86FCE` FOREIGN KEY (`publisher_id`) REFERENCES `core_user_publisher` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `core_billing_configuration`
--

LOCK TABLES `core_billing_configuration` WRITE;
/*!40000 ALTER TABLE `core_billing_configuration` DISABLE KEYS */;
INSERT INTO `core_billing_configuration` VALUES (1,2,'MODULE_DISPLAY','[]','SLOT_OPPORTUNITY',1);
/*!40000 ALTER TABLE `core_billing_configuration` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `core_channel`
--

DROP TABLE IF EXISTS `core_channel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `core_channel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `publisher_id` int(11) DEFAULT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `deleted_at` date DEFAULT NULL,
  `rtb_status` smallint(6) NOT NULL DEFAULT '2',
  PRIMARY KEY (`id`),
  KEY `IDX_B0EE3B7440C86FCE` (`publisher_id`),
  CONSTRAINT `FK_B0EE3B7440C86FCE` FOREIGN KEY (`publisher_id`) REFERENCES `core_user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `core_channel`
--

LOCK TABLES `core_channel` WRITE;
/*!40000 ALTER TABLE `core_channel` DISABLE KEYS */;
/*!40000 ALTER TABLE `core_channel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `core_channel_site`
--

DROP TABLE IF EXISTS `core_channel_site`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `core_channel_site` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `channel_id` int(11) DEFAULT NULL,
  `site_id` int(11) DEFAULT NULL,
  `deleted_at` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_58A7134272F5A1AA` (`channel_id`),
  KEY `IDX_58A71342F6BD1646` (`site_id`),
  CONSTRAINT `FK_58A7134272F5A1AA` FOREIGN KEY (`channel_id`) REFERENCES `core_channel` (`id`),
  CONSTRAINT `FK_58A71342F6BD1646` FOREIGN KEY (`site_id`) REFERENCES `core_site` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `core_channel_site`
--

LOCK TABLES `core_channel_site` WRITE;
/*!40000 ALTER TABLE `core_channel_site` DISABLE KEYS */;
/*!40000 ALTER TABLE `core_channel_site` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `core_display_ad_slot`
--

DROP TABLE IF EXISTS `core_display_ad_slot`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `core_display_ad_slot` (
  `id` int(11) NOT NULL,
  `floor_price` double DEFAULT NULL,
  `rtb_status` smallint(6) NOT NULL DEFAULT '2',
  `hb_bid_price` double DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `FK_5ED252C1BF396750` FOREIGN KEY (`id`) REFERENCES `core_ad_slot` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `core_display_ad_slot`
--

LOCK TABLES `core_display_ad_slot` WRITE;
/*!40000 ALTER TABLE `core_display_ad_slot` DISABLE KEYS */;
INSERT INTO `core_display_ad_slot` VALUES (1,NULL,2,NULL),(2,NULL,2,NULL),(4,NULL,2,NULL),(6,NULL,2,NULL),(9,NULL,0,NULL);
/*!40000 ALTER TABLE `core_display_ad_slot` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `core_dynamic_ad_slot`
--

DROP TABLE IF EXISTS `core_dynamic_ad_slot`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `core_dynamic_ad_slot` (
  `id` int(11) NOT NULL,
  `default_ad_slot_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_B7415E41DC8CAC7B` (`default_ad_slot_id`),
  CONSTRAINT `FK_B7415E41BF396750` FOREIGN KEY (`id`) REFERENCES `core_ad_slot` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_B7415E41DC8CAC7B` FOREIGN KEY (`default_ad_slot_id`) REFERENCES `core_ad_slot` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `core_dynamic_ad_slot`
--

LOCK TABLES `core_dynamic_ad_slot` WRITE;
/*!40000 ALTER TABLE `core_dynamic_ad_slot` DISABLE KEYS */;
INSERT INTO `core_dynamic_ad_slot` VALUES (3,NULL),(5,NULL),(7,NULL);
/*!40000 ALTER TABLE `core_dynamic_ad_slot` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `core_expression`
--

DROP TABLE IF EXISTS `core_expression`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `core_expression` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `library_expression_id` int(11) DEFAULT NULL,
  `expect_ad_slot_id` int(11) DEFAULT NULL,
  `dynamic_ad_slot_id` int(11) DEFAULT NULL,
  `expression_in_js` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT '(DC2Type:json_array)',
  `deleted_at` date DEFAULT NULL,
  `hb_bid_price` double DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_E47CD2E01EE1453C` (`library_expression_id`),
  KEY `IDX_E47CD2E0E4E5E816` (`expect_ad_slot_id`),
  KEY `IDX_E47CD2E01D925722` (`dynamic_ad_slot_id`),
  CONSTRAINT `FK_E47CD2E01D925722` FOREIGN KEY (`dynamic_ad_slot_id`) REFERENCES `core_dynamic_ad_slot` (`id`),
  CONSTRAINT `FK_E47CD2E01EE1453C` FOREIGN KEY (`library_expression_id`) REFERENCES `library_expression` (`id`),
  CONSTRAINT `FK_E47CD2E0E4E5E816` FOREIGN KEY (`expect_ad_slot_id`) REFERENCES `core_ad_slot` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `core_expression`
--

LOCK TABLES `core_expression` WRITE;
/*!40000 ALTER TABLE `core_expression` DISABLE KEYS */;
INSERT INTO `core_expression` VALUES (1,1,2,3,'{\"vars\":[{\"name\":\"${COUNTRY}\",\"type\":\"string\"}],\"expression\":\"(${COUNTRY}==\\\"AW\\\")\",\"expectedAdSlot\":2,\"cpm\":null,\"startingPosition\":3}','2016-07-20',NULL),(2,2,4,5,'{\"vars\":[{\"name\":\"${PAGE_URL}\",\"type\":\"string\"}],\"expression\":\"(${PAGE_URL}==\\\"dfdf\\\")\",\"expectedAdSlot\":4,\"cpm\":null,\"startingPosition\":1}','2016-07-20',NULL),(3,3,4,5,'{\"vars\":[{\"name\":\"${COUNTRY}\",\"type\":\"string\"}],\"expression\":\"(${COUNTRY}==\\\"AR\\\")\",\"expectedAdSlot\":4,\"cpm\":null,\"startingPosition\":2}','2016-07-20',NULL),(4,4,4,5,'{\"vars\":[{\"name\":\"${DOMAIN}\",\"type\":\"string\"}],\"expression\":\"(${DOMAIN}==\\\"dfdf\\\")\",\"expectedAdSlot\":4,\"cpm\":null,\"startingPosition\":1}','2016-07-20',NULL),(5,2,6,7,'{\"vars\":[{\"name\":\"${PAGE_URL}\",\"type\":\"string\"}],\"expression\":\"(${PAGE_URL}==\\\"dfdf\\\")\",\"expectedAdSlot\":6,\"cpm\":null,\"startingPosition\":1}','2016-07-20',NULL),(6,3,6,7,'{\"vars\":[{\"name\":\"${COUNTRY}\",\"type\":\"string\"}],\"expression\":\"(${COUNTRY}==\\\"AR\\\")\",\"expectedAdSlot\":6,\"cpm\":null,\"startingPosition\":2}','2016-07-20',NULL),(7,4,6,7,'{\"vars\":[{\"name\":\"${DOMAIN}\",\"type\":\"string\"}],\"expression\":\"(${DOMAIN}==\\\"dfdf\\\")\",\"expectedAdSlot\":6,\"cpm\":null,\"startingPosition\":1}','2016-07-20',NULL);
/*!40000 ALTER TABLE `core_expression` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `core_native_ad_slot`
--

DROP TABLE IF EXISTS `core_native_ad_slot`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `core_native_ad_slot` (
  `id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `FK_5A19262EBF396750` FOREIGN KEY (`id`) REFERENCES `core_ad_slot` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `core_native_ad_slot`
--

LOCK TABLES `core_native_ad_slot` WRITE;
/*!40000 ALTER TABLE `core_native_ad_slot` DISABLE KEYS */;
/*!40000 ALTER TABLE `core_native_ad_slot` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `core_partner`
--

DROP TABLE IF EXISTS `core_partner`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `core_partner` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name_canonical` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `report_types` longtext COLLATE utf8_unicode_ci COMMENT '(DC2Type:json_array)',
  `tag_id_regex` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tag_size_regex` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ad_network_partner_name_unique_key` (`name_canonical`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `core_partner`
--

LOCK TABLES `core_partner` WRITE;
/*!40000 ALTER TABLE `core_partner` DISABLE KEYS */;
INSERT INTO `core_partner` VALUES (1,'Komoona',NULL,'komoona',NULL,NULL,NULL);
/*!40000 ALTER TABLE `core_partner` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `core_partner_imported_file`
--

DROP TABLE IF EXISTS `core_partner_imported_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `core_partner_imported_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `publisher_id` int(11) NOT NULL,
  `file_path` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `hash` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `hash_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'sha1',
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `core_partner_imported_file`
--

LOCK TABLES `core_partner_imported_file` WRITE;
/*!40000 ALTER TABLE `core_partner_imported_file` DISABLE KEYS */;
INSERT INTO `core_partner_imported_file` VALUES (1,2,'data/komoona/20160705-20160609-20160615/BluTonic_jun9.csv','04395c1d872d95a38ef9d2098163863794ea95a5','sha1','2016-07-20 17:32:40'),(2,2,'data/komoona/20160705-20160609-20160615/BluTonic_jun9.csv','04395c1d872d95a38ef9d2098163863794ea95a5','sha1','2016-07-20 17:33:11');
/*!40000 ALTER TABLE `core_partner_imported_file` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `core_publisher_partner`
--

DROP TABLE IF EXISTS `core_publisher_partner`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `core_publisher_partner` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ad_network_partner_id` int(11) DEFAULT NULL,
  `publisher_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `publisher_partner_unique_key` (`publisher_id`,`ad_network_partner_id`),
  KEY `IDX_BA836221EC12BC9B` (`ad_network_partner_id`),
  CONSTRAINT `FK_BA836221EC12BC9B` FOREIGN KEY (`ad_network_partner_id`) REFERENCES `core_partner` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `core_publisher_partner`
--

LOCK TABLES `core_publisher_partner` WRITE;
/*!40000 ALTER TABLE `core_publisher_partner` DISABLE KEYS */;
/*!40000 ALTER TABLE `core_publisher_partner` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `core_site`
--

DROP TABLE IF EXISTS `core_site`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `core_site` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `publisher_id` int(11) DEFAULT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `domain` varchar(240) COLLATE utf8_unicode_ci NOT NULL,
  `auto_create` tinyint(1) NOT NULL DEFAULT '0',
  `deleted_at` datetime DEFAULT NULL,
  `site_token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `enable_source_report` tinyint(1) NOT NULL DEFAULT '0',
  `players` longtext COLLATE utf8_unicode_ci COMMENT '(DC2Type:json_array)',
  `sub_publisher_id` int(11) DEFAULT NULL,
  `rtb_status` smallint(6) NOT NULL DEFAULT '2',
  PRIMARY KEY (`id`),
  UNIQUE KEY `site_token_key` (`site_token`),
  KEY `IDX_5BA6CAD140C86FCE` (`publisher_id`),
  KEY `IDX_5BA6CAD1579A28F8` (`sub_publisher_id`),
  CONSTRAINT `FK_5BA6CAD140C86FCE` FOREIGN KEY (`publisher_id`) REFERENCES `core_user` (`id`),
  CONSTRAINT `FK_5BA6CAD1579A28F8` FOREIGN KEY (`sub_publisher_id`) REFERENCES `core_user_sub_publisher` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `core_site`
--

LOCK TABLES `core_site` WRITE;
/*!40000 ALTER TABLE `core_site` DISABLE KEYS */;
INSERT INTO `core_site` VALUES (1,2,'USA Today','usatoday.com',0,'2016-07-20 17:36:10','578f541a376d29.43307704',0,NULL,NULL,2),(2,2,'site1','site1.com',0,'2016-07-20 17:36:14','578f541e7e80c6.39072887',0,NULL,NULL,2),(3,2,'Gravity Tales','gravitytales.com',0,NULL,'d2f11f17d04ddafd376e167a450af746',0,'[]',3,0);
/*!40000 ALTER TABLE `core_site` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `core_sub_publisher_partner_revenue_config`
--

DROP TABLE IF EXISTS `core_sub_publisher_partner_revenue_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `core_sub_publisher_partner_revenue_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sub_publisher_id` int(11) DEFAULT NULL,
  `ad_network_partner_id` int(11) DEFAULT NULL,
  `revenue_option` smallint(6) NOT NULL DEFAULT '0',
  `revenue_value` double NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_9DB7E597579A28F8` (`sub_publisher_id`),
  KEY `IDX_9DB7E597EC12BC9B` (`ad_network_partner_id`),
  CONSTRAINT `FK_9DB7E597579A28F8` FOREIGN KEY (`sub_publisher_id`) REFERENCES `core_user` (`id`),
  CONSTRAINT `FK_9DB7E597EC12BC9B` FOREIGN KEY (`ad_network_partner_id`) REFERENCES `core_partner` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `core_sub_publisher_partner_revenue_config`
--

LOCK TABLES `core_sub_publisher_partner_revenue_config` WRITE;
/*!40000 ALTER TABLE `core_sub_publisher_partner_revenue_config` DISABLE KEYS */;
INSERT INTO `core_sub_publisher_partner_revenue_config` VALUES (1,3,1,0,0);
/*!40000 ALTER TABLE `core_sub_publisher_partner_revenue_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `core_user`
--

DROP TABLE IF EXISTS `core_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `core_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `username_canonical` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `salt` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `locked` tinyint(1) NOT NULL,
  `expired` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  `confirmation_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password_requested_at` datetime DEFAULT NULL,
  `roles` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT '(DC2Type:array)',
  `credentials_expired` tinyint(1) NOT NULL,
  `credentials_expire_at` datetime DEFAULT NULL,
  `join_date` date NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email_canonical` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `test_account` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_BF76157C92FC23A8` (`username_canonical`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `core_user`
--

LOCK TABLES `core_user` WRITE;
/*!40000 ALTER TABLE `core_user` DISABLE KEYS */;
INSERT INTO `core_user` VALUES (1,'admin','admin',1,'iou8l2nyblkw08cws48kgs0sg80c0gs','9ueBuQc4FMaSHady4cQC1DuTNoWDtNkoJJxxu3X8833TsK6AP6N5KYtpLNrhIXjG6Zd+7oorolV5+kkrOS5Pww==','2016-07-20 17:36:57',0,0,NULL,NULL,NULL,'a:1:{i:0;s:10:\"ROLE_ADMIN\";}',0,NULL,'2016-01-15','admin@tagcade.com','admin@tagcade.com','admin',0),(2,'usatoday','usatoday',1,'4zhru5a0qzk0ow0k44o80g48wwoksgs','59UyCqAnlkL9EIcF/HoTlV8A+iHfGrhG21xRY1NAud6ZfB4GtXO72cz229pcTCEjt7pHajtE+zvbvNztw5SbXw==',NULL,0,0,NULL,NULL,NULL,'a:5:{i:0;s:14:\"ROLE_PUBLISHER\";i:1;s:14:\"MODULE_DISPLAY\";i:2;s:16:\"MODULE_ANALYTICS\";i:3;s:21:\"MODULE_UNIFIED_REPORT\";i:4;s:20:\"MODULE_SUB_PUBLISHER\";}',0,NULL,'2016-01-15','admin@usatoday.com','admin@usatoday.com','publisher',0),(3,'sub1','sub1',1,'irmuwx2e9u04ccwggw8s88sgg8g8wko','zASvQwNX85OoUvhUyXf6QSbH5u0BGuc2AkZvbZj4Cx6NSMMi/sh0jzZElkTXrAOwZGf740/Q3i8D+epck4PZvQ==',NULL,0,0,NULL,NULL,NULL,'a:1:{i:0;s:18:\"ROLE_SUB_PUBLISHER\";}',0,NULL,'2016-07-20','sub1@tagcade.com','sub1@tagcade.com','subPublisher',0);
/*!40000 ALTER TABLE `core_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `core_user_admin`
--

DROP TABLE IF EXISTS `core_user_admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `core_user_admin` (
  `id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `FK_568072CFBF396750` FOREIGN KEY (`id`) REFERENCES `core_user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `core_user_admin`
--

LOCK TABLES `core_user_admin` WRITE;
/*!40000 ALTER TABLE `core_user_admin` DISABLE KEYS */;
INSERT INTO `core_user_admin` VALUES (1);
/*!40000 ALTER TABLE `core_user_admin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `core_user_publisher`
--

DROP TABLE IF EXISTS `core_user_publisher`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `core_user_publisher` (
  `id` int(11) NOT NULL,
  `uuid` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `first_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `last_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `company` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `billing_rate` decimal(10,4) DEFAULT NULL,
  `city` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `state` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `postal_code` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `country` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `settings` longtext COLLATE utf8_unicode_ci COMMENT '(DC2Type:json_array)',
  `tag_domain` longtext COLLATE utf8_unicode_ci COMMENT '(DC2Type:json_array)',
  `exchanges` longtext COLLATE utf8_unicode_ci COMMENT '(DC2Type:json_array)',
  `bidders` longtext COLLATE utf8_unicode_ci COMMENT '(DC2Type:json_array)',
  PRIMARY KEY (`id`),
  CONSTRAINT `FK_6754B12DBF396750` FOREIGN KEY (`id`) REFERENCES `core_user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `core_user_publisher`
--

LOCK TABLES `core_user_publisher` WRITE;
/*!40000 ALTER TABLE `core_user_publisher` DISABLE KEYS */;
INSERT INTO `core_user_publisher` VALUES (2,'076c22ee-c585-5e86-ba2f-a7ee2241540f','USA',NULL,'USA Today Inc',NULL,1.1000,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `core_user_publisher` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `core_user_sub_publisher`
--

DROP TABLE IF EXISTS `core_user_sub_publisher`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `core_user_sub_publisher` (
  `id` int(11) NOT NULL,
  `publisher_id` int(11) DEFAULT NULL,
  `demand_source_transparency` tinyint(1) NOT NULL DEFAULT '0',
  `enable_view_tagcade_report` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `IDX_4F034A2640C86FCE` (`publisher_id`),
  CONSTRAINT `FK_4F034A2640C86FCE` FOREIGN KEY (`publisher_id`) REFERENCES `core_user_publisher` (`id`),
  CONSTRAINT `FK_4F034A26BF396750` FOREIGN KEY (`id`) REFERENCES `core_user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `core_user_sub_publisher`
--

LOCK TABLES `core_user_sub_publisher` WRITE;
/*!40000 ALTER TABLE `core_user_sub_publisher` DISABLE KEYS */;
INSERT INTO `core_user_sub_publisher` VALUES (3,2,1,1);
/*!40000 ALTER TABLE `core_user_sub_publisher` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `library_ad_slot`
--

DROP TABLE IF EXISTS `library_ad_slot`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `library_ad_slot` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `publisher_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `visible` tinyint(1) NOT NULL,
  `deleted_at` date DEFAULT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_6E00CA3240C86FCE` (`publisher_id`),
  CONSTRAINT `FK_6E00CA3240C86FCE` FOREIGN KEY (`publisher_id`) REFERENCES `core_user_publisher` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `library_ad_slot`
--

LOCK TABLES `library_ad_slot` WRITE;
/*!40000 ALTER TABLE `library_ad_slot` DISABLE KEYS */;
INSERT INTO `library_ad_slot` VALUES (1,2,'Display AdSlot 1',1,NULL,'display'),(2,2,'Display AdSlot 2',1,NULL,'display'),(3,2,'Display AdSlot 3',1,NULL,'display'),(4,2,'Dynamic  AdSlot 1',1,NULL,'dynamic'),(5,2,'Display AdSlot 4',1,NULL,'display'),(6,2,'Dynamic  AdSlot 2',1,NULL,'dynamic'),(7,2,'Dynamic  AdSlot 3',1,NULL,'dynamic'),(8,2,'Display AdSlot 5',1,NULL,'display'),(9,2,'slot 1',0,NULL,'display');
/*!40000 ALTER TABLE `library_ad_slot` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `library_ad_slot_ad_tag`
--

DROP TABLE IF EXISTS `library_ad_slot_ad_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `library_ad_slot_ad_tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `library_ad_tag_id` int(11) DEFAULT NULL,
  `library_ad_slot_id` int(11) DEFAULT NULL,
  `position` int(11) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `frequency_cap` int(11) DEFAULT NULL,
  `rotation` int(11) DEFAULT NULL,
  `ref_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` date DEFAULT NULL,
  `impression_cap` int(11) DEFAULT NULL,
  `network_opportunity_cap` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slot_tag_compound_primary_key` (`library_ad_tag_id`,`library_ad_slot_id`,`ref_id`),
  KEY `IDX_DC3B33AE3DC10368` (`library_ad_tag_id`),
  KEY `IDX_DC3B33AE70BBCB64` (`library_ad_slot_id`),
  CONSTRAINT `FK_DC3B33AE3DC10368` FOREIGN KEY (`library_ad_tag_id`) REFERENCES `library_ad_tag` (`id`),
  CONSTRAINT `FK_DC3B33AE70BBCB64` FOREIGN KEY (`library_ad_slot_id`) REFERENCES `library_ad_slot` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `library_ad_slot_ad_tag`
--

LOCK TABLES `library_ad_slot_ad_tag` WRITE;
/*!40000 ALTER TABLE `library_ad_slot_ad_tag` DISABLE KEYS */;
INSERT INTO `library_ad_slot_ad_tag` VALUES (1,1,1,1,1,NULL,NULL,'56986cd2821167.11237342','2016-01-15 10:51:47','2016-01-15 10:51:47',NULL,NULL,NULL),(2,2,1,2,1,NULL,NULL,'56986ce4e595d8.11924247','2016-01-15 10:52:05','2016-01-15 10:52:05',NULL,NULL,NULL),(3,3,2,1,1,NULL,NULL,'56986d09c351b8.47232662','2016-01-15 10:52:42','2016-01-15 10:52:42',NULL,NULL,NULL),(4,4,2,2,1,NULL,NULL,'56986d1d64b068.58616459','2016-01-15 10:53:02','2016-01-15 10:53:02',NULL,NULL,NULL),(5,5,2,3,1,NULL,NULL,'56986d2f8361d4.33474707','2016-01-15 10:53:20','2016-01-15 10:53:20',NULL,NULL,NULL),(6,6,3,1,1,NULL,NULL,'56986e0e021a20.53611356','2016-01-15 10:57:02','2016-01-15 10:57:02',NULL,NULL,NULL),(7,7,3,2,1,NULL,NULL,'56986e225358d1.19820599','2016-01-15 10:57:22','2016-01-15 10:57:22',NULL,NULL,NULL),(8,8,3,3,1,NULL,NULL,'56986e36a95780.71207988','2016-01-15 10:57:43','2016-01-15 10:57:43',NULL,NULL,NULL),(9,9,5,1,1,NULL,NULL,'56986e941ee7a2.59349775','2016-01-15 10:59:16','2016-01-15 10:59:16',NULL,NULL,NULL),(10,10,5,2,1,NULL,NULL,'56986ea6018566.28682516','2016-01-15 10:59:34','2016-01-15 10:59:34',NULL,NULL,NULL),(11,11,8,1,1,NULL,NULL,'5698731fa48e59.61889897','2016-01-15 11:18:39','2016-01-15 11:18:39',NULL,NULL,NULL),(12,12,8,2,1,NULL,NULL,'56987329e3a805.60121712','2016-01-15 11:18:50','2016-01-15 11:18:50',NULL,NULL,NULL);
/*!40000 ALTER TABLE `library_ad_slot_ad_tag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `library_ad_tag`
--

DROP TABLE IF EXISTS `library_ad_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `library_ad_tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ad_network_id` int(11) DEFAULT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `html` longtext COLLATE utf8_unicode_ci,
  `visible` tinyint(1) NOT NULL DEFAULT '0',
  `ad_type` int(11) NOT NULL DEFAULT '0',
  `descriptor` longtext COLLATE utf8_unicode_ci COMMENT '(DC2Type:json_array)',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` date DEFAULT NULL,
  `partner_tag_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_DA9C453FCB9BD82B` (`ad_network_id`),
  CONSTRAINT `FK_DA9C453FCB9BD82B` FOREIGN KEY (`ad_network_id`) REFERENCES `core_ad_network` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `library_ad_tag`
--

LOCK TABLES `library_ad_tag` WRITE;
/*!40000 ALTER TABLE `library_ad_tag` DISABLE KEYS */;
INSERT INTO `library_ad_tag` VALUES (1,1,'Ad Tag 1','Ad Tag 1',1,0,NULL,'2016-01-15 10:51:47','2016-01-15 10:51:47',NULL,NULL),(2,1,'Ad Tag 2','Ad Tag 2',1,0,NULL,'2016-01-15 10:52:05','2016-01-15 10:52:05',NULL,NULL),(3,1,'Ad Tag 3','Ad Tag 3',1,0,NULL,'2016-01-15 10:52:42','2016-01-15 10:52:42',NULL,NULL),(4,1,'Ad Tag 4','Ad Tag 4',1,0,NULL,'2016-01-15 10:53:02','2016-01-15 10:53:02',NULL,NULL),(5,1,'Ad Tag 5','Ad Tag 5',1,0,NULL,'2016-01-15 10:53:20','2016-01-15 10:53:20',NULL,NULL),(6,1,'Ad Tag 6','Ad Tag 6',1,0,NULL,'2016-01-15 10:57:02','2016-01-15 10:57:02',NULL,NULL),(7,1,'Ad Tag 7','Ad Tag 7',1,0,NULL,'2016-01-15 10:57:23','2016-01-15 10:57:23',NULL,NULL),(8,1,'Ad Tag 8','Ad Tag 8',1,0,NULL,'2016-01-15 10:57:43','2016-01-15 10:57:43',NULL,NULL),(9,1,'Ad Tag 9','Ad Tag 9',1,0,NULL,'2016-01-15 10:59:16','2016-01-15 10:59:16',NULL,NULL),(10,1,'Ad Tag 10','Ad Tag 10',1,0,NULL,'2016-01-15 10:59:34','2016-01-15 10:59:34',NULL,NULL),(11,1,'test 1','test 1',1,0,NULL,'2016-01-15 11:18:39','2016-01-15 11:18:39',NULL,NULL),(12,1,'test 2','test 2',1,0,NULL,'2016-01-15 11:18:50','2016-01-15 11:18:50',NULL,NULL),(13,2,'Komoona 1','adsf',0,0,NULL,'2016-07-20 17:37:47','2016-07-20 17:37:47',NULL,'300x250'),(14,2,'Komoona 2','asdf',0,0,NULL,'2016-07-20 17:38:03','2016-07-20 17:38:03',NULL,'300x600'),(15,2,'Komoona 3','asdf',0,0,NULL,'2016-07-20 17:38:16','2016-07-20 17:38:16',NULL,'728x90'),(16,2,'Komoona 4','sdf',0,0,NULL,'2016-07-20 17:38:31','2016-07-20 17:38:31',NULL,'Shiroyukitranslations.com 300x250'),(17,2,'Komoona 5','asdf',0,0,NULL,'2016-07-20 17:38:49','2016-07-20 17:38:49',NULL,'Dailyvoice.com 728x90');
/*!40000 ALTER TABLE `library_ad_tag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `library_display_ad_slot`
--

DROP TABLE IF EXISTS `library_display_ad_slot`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `library_display_ad_slot` (
  `id` int(11) NOT NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `passback_mode` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `auto_fit` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  CONSTRAINT `FK_DCAFF75CBF396750` FOREIGN KEY (`id`) REFERENCES `library_ad_slot` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `library_display_ad_slot`
--

LOCK TABLES `library_display_ad_slot` WRITE;
/*!40000 ALTER TABLE `library_display_ad_slot` DISABLE KEYS */;
INSERT INTO `library_display_ad_slot` VALUES (1,34,34,'position',0),(2,34,34,'position',0),(3,33,343,'position',0),(5,34,34,'position',0),(8,34,34,'position',0),(9,44,44,'position',0);
/*!40000 ALTER TABLE `library_display_ad_slot` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `library_dynamic_ad_slot`
--

DROP TABLE IF EXISTS `library_dynamic_ad_slot`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `library_dynamic_ad_slot` (
  `id` int(11) NOT NULL,
  `default_library_ad_slot_id` int(11) DEFAULT NULL,
  `native` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `IDX_353CFBDC10FEC588` (`default_library_ad_slot_id`),
  CONSTRAINT `FK_353CFBDC10FEC588` FOREIGN KEY (`default_library_ad_slot_id`) REFERENCES `library_ad_slot` (`id`),
  CONSTRAINT `FK_353CFBDCBF396750` FOREIGN KEY (`id`) REFERENCES `library_ad_slot` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `library_dynamic_ad_slot`
--

LOCK TABLES `library_dynamic_ad_slot` WRITE;
/*!40000 ALTER TABLE `library_dynamic_ad_slot` DISABLE KEYS */;
INSERT INTO `library_dynamic_ad_slot` VALUES (4,NULL,0),(6,NULL,0),(7,NULL,0);
/*!40000 ALTER TABLE `library_dynamic_ad_slot` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `library_expression`
--

DROP TABLE IF EXISTS `library_expression`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `library_expression` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `library_dynamic_ad_slot_id` int(11) NOT NULL,
  `expect_library_ad_slot_id` int(11) DEFAULT NULL,
  `expression_in_js` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT '(DC2Type:json_array)',
  `expression_descriptor` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT '(DC2Type:json_array)',
  `starting_position` int(11) DEFAULT '1',
  `deleted_at` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_3C24657D3AF54DA0` (`library_dynamic_ad_slot_id`),
  KEY `IDX_3C24657D55FE8C5D` (`expect_library_ad_slot_id`),
  CONSTRAINT `FK_3C24657D3AF54DA0` FOREIGN KEY (`library_dynamic_ad_slot_id`) REFERENCES `library_dynamic_ad_slot` (`id`),
  CONSTRAINT `FK_3C24657D55FE8C5D` FOREIGN KEY (`expect_library_ad_slot_id`) REFERENCES `library_ad_slot` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `library_expression`
--

LOCK TABLES `library_expression` WRITE;
/*!40000 ALTER TABLE `library_expression` DISABLE KEYS */;
INSERT INTO `library_expression` VALUES (1,4,3,'{\"vars\":[{\"name\":\"${COUNTRY}\",\"type\":\"string\"}],\"expression\":\"(${COUNTRY}==\\\"AW\\\")\",\"expectedLibraryAdSlot\":3,\"startingPosition\":3}','{\"groupType\":\"AND\",\"groupVal\":[{\"var\":\"${COUNTRY}\",\"cmp\":\"==\",\"val\":\"AW\",\"type\":\"string\"}]}',3,NULL),(2,6,5,'{\"vars\":[{\"name\":\"${PAGE_URL}\",\"type\":\"string\"}],\"expression\":\"(${PAGE_URL}==\\\"dfdf\\\")\",\"expectedLibraryAdSlot\":5,\"startingPosition\":1}','{\"groupType\":\"AND\",\"groupVal\":[{\"var\":\"${PAGE_URL}\",\"cmp\":\"==\",\"val\":\"dfdf\",\"type\":\"string\"}]}',1,NULL),(3,6,5,'{\"vars\":[{\"name\":\"${COUNTRY}\",\"type\":\"string\"}],\"expression\":\"(${COUNTRY}==\\\"AR\\\")\",\"expectedLibraryAdSlot\":5,\"startingPosition\":2}','{\"groupType\":\"AND\",\"groupVal\":[{\"var\":\"${COUNTRY}\",\"cmp\":\"==\",\"val\":\"AR\",\"type\":\"string\"}]}',2,NULL),(4,6,5,'{\"vars\":[{\"name\":\"${DOMAIN}\",\"type\":\"string\"}],\"expression\":\"(${DOMAIN}==\\\"dfdf\\\")\",\"expectedLibraryAdSlot\":5,\"startingPosition\":1}','{\"groupType\":\"AND\",\"groupVal\":[{\"var\":\"${DOMAIN}\",\"cmp\":\"==\",\"val\":\"dfdf\",\"type\":\"string\"}]}',1,NULL),(5,7,5,'{\"vars\":[{\"name\":\"${SCREEN_HEIGHT}\",\"type\":\"numeric\"}],\"expression\":\"(top.screen.height==34)\",\"expectedLibraryAdSlot\":5,\"startingPosition\":1}','{\"groupType\":\"AND\",\"groupVal\":[{\"var\":\"${SCREEN_HEIGHT}\",\"cmp\":\"==\",\"val\":\"34\",\"type\":\"numeric\"}]}',1,NULL),(6,7,5,'{\"vars\":[{\"name\":\"${WINDOW_WIDTH}\",\"type\":\"numeric\"}],\"expression\":\"(top.outerWidth==34)\",\"expectedLibraryAdSlot\":5,\"startingPosition\":2}','{\"groupType\":\"AND\",\"groupVal\":[{\"var\":\"${WINDOW_WIDTH}\",\"cmp\":\"==\",\"val\":\"34\",\"type\":\"numeric\"}]}',2,NULL);
/*!40000 ALTER TABLE `library_expression` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `library_native_ad_slot`
--

DROP TABLE IF EXISTS `library_native_ad_slot`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `library_native_ad_slot` (
  `id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `FK_2F487A78BF396750` FOREIGN KEY (`id`) REFERENCES `library_ad_slot` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `library_native_ad_slot`
--

LOCK TABLES `library_native_ad_slot` WRITE;
/*!40000 ALTER TABLE `library_native_ad_slot` DISABLE KEYS */;
/*!40000 ALTER TABLE `library_native_ad_slot` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `report_performance_display_hierarchy_ad_network`
--

DROP TABLE IF EXISTS `report_performance_display_hierarchy_ad_network`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `report_performance_display_hierarchy_ad_network` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ad_network_id` int(11) DEFAULT NULL,
  `date` date NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `total_opportunities` int(11) NOT NULL,
  `impressions` int(11) NOT NULL,
  `passbacks` int(11) NOT NULL,
  `fill_rate` decimal(10,4) NOT NULL,
  `est_revenue` decimal(10,4) DEFAULT NULL,
  `est_cpm` decimal(10,4) DEFAULT NULL,
  `first_opportunities` int(11) NOT NULL,
  `verified_impressions` int(11) NOT NULL,
  `unverified_impressions` int(11) NOT NULL,
  `blank_impressions` int(11) NOT NULL,
  `void_impressions` int(11) NOT NULL,
  `clicks` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_ad_network_report_idx` (`date`,`ad_network_id`),
  KEY `IDX_88AB3042CB9BD82B` (`ad_network_id`),
  CONSTRAINT `FK_88AB3042CB9BD82B` FOREIGN KEY (`ad_network_id`) REFERENCES `core_ad_network` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report_performance_display_hierarchy_ad_network`
--

LOCK TABLES `report_performance_display_hierarchy_ad_network` WRITE;
/*!40000 ALTER TABLE `report_performance_display_hierarchy_ad_network` DISABLE KEYS */;
/*!40000 ALTER TABLE `report_performance_display_hierarchy_ad_network` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `report_performance_display_hierarchy_ad_network_ad_tag`
--

DROP TABLE IF EXISTS `report_performance_display_hierarchy_ad_network_ad_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `report_performance_display_hierarchy_ad_network_ad_tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ad_tag_id` int(11) DEFAULT NULL,
  `super_report_id` int(11) DEFAULT NULL,
  `date` date NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `total_opportunities` int(11) NOT NULL,
  `impressions` int(11) NOT NULL,
  `passbacks` int(11) DEFAULT NULL,
  `fill_rate` decimal(10,4) NOT NULL,
  `est_revenue` decimal(10,4) DEFAULT NULL,
  `est_cpm` decimal(10,4) DEFAULT NULL,
  `first_opportunities` int(11) NOT NULL,
  `verified_impressions` int(11) NOT NULL,
  `unverified_impressions` int(11) DEFAULT NULL,
  `blank_impressions` int(11) DEFAULT NULL,
  `void_impressions` int(11) DEFAULT NULL,
  `clicks` int(11) DEFAULT NULL,
  `sub_publisher_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_ad_network_adtag_report_idx` (`date`,`ad_tag_id`),
  KEY `IDX_70B96999273D74E4` (`ad_tag_id`),
  KEY `IDX_70B96999E7B18F1F` (`super_report_id`),
  KEY `IDX_70B96999579A28F8` (`sub_publisher_id`),
  CONSTRAINT `FK_70B96999273D74E4` FOREIGN KEY (`ad_tag_id`) REFERENCES `core_ad_tag` (`id`),
  CONSTRAINT `FK_70B96999579A28F8` FOREIGN KEY (`sub_publisher_id`) REFERENCES `core_user_sub_publisher` (`id`),
  CONSTRAINT `FK_70B96999E7B18F1F` FOREIGN KEY (`super_report_id`) REFERENCES `report_performance_display_hierarchy_ad_network_site` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report_performance_display_hierarchy_ad_network_ad_tag`
--

LOCK TABLES `report_performance_display_hierarchy_ad_network_ad_tag` WRITE;
/*!40000 ALTER TABLE `report_performance_display_hierarchy_ad_network_ad_tag` DISABLE KEYS */;
/*!40000 ALTER TABLE `report_performance_display_hierarchy_ad_network_ad_tag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `report_performance_display_hierarchy_ad_network_site`
--

DROP TABLE IF EXISTS `report_performance_display_hierarchy_ad_network_site`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `report_performance_display_hierarchy_ad_network_site` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) DEFAULT NULL,
  `super_report_id` int(11) DEFAULT NULL,
  `date` date NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `total_opportunities` int(11) NOT NULL,
  `impressions` int(11) NOT NULL,
  `passbacks` int(11) NOT NULL,
  `fill_rate` decimal(10,4) NOT NULL,
  `est_revenue` decimal(10,4) DEFAULT NULL,
  `est_cpm` decimal(10,4) DEFAULT NULL,
  `first_opportunities` int(11) NOT NULL,
  `verified_impressions` int(11) NOT NULL,
  `unverified_impressions` int(11) NOT NULL,
  `blank_impressions` int(11) NOT NULL,
  `void_impressions` int(11) NOT NULL,
  `clicks` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_ad_network_site_report_idx` (`date`,`site_id`,`super_report_id`),
  KEY `IDX_C0092AB5F6BD1646` (`site_id`),
  KEY `IDX_C0092AB5E7B18F1F` (`super_report_id`),
  CONSTRAINT `FK_C0092AB5E7B18F1F` FOREIGN KEY (`super_report_id`) REFERENCES `report_performance_display_hierarchy_ad_network` (`id`),
  CONSTRAINT `FK_C0092AB5F6BD1646` FOREIGN KEY (`site_id`) REFERENCES `core_site` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report_performance_display_hierarchy_ad_network_site`
--

LOCK TABLES `report_performance_display_hierarchy_ad_network_site` WRITE;
/*!40000 ALTER TABLE `report_performance_display_hierarchy_ad_network_site` DISABLE KEYS */;
/*!40000 ALTER TABLE `report_performance_display_hierarchy_ad_network_site` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `report_performance_display_hierarchy_partner_account`
--

DROP TABLE IF EXISTS `report_performance_display_hierarchy_partner_account`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `report_performance_display_hierarchy_partner_account` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `publisher_id` int(11) DEFAULT NULL,
  `date` date NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `total_opportunities` int(11) NOT NULL,
  `impressions` int(11) NOT NULL,
  `passbacks` int(11) NOT NULL,
  `fill_rate` decimal(10,4) NOT NULL,
  `est_revenue` decimal(10,4) DEFAULT NULL,
  `est_cpm` decimal(10,4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_partner_account_report_idx` (`date`,`publisher_id`),
  KEY `IDX_84836C6A40C86FCE` (`publisher_id`),
  CONSTRAINT `FK_84836C6A40C86FCE` FOREIGN KEY (`publisher_id`) REFERENCES `core_user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report_performance_display_hierarchy_partner_account`
--

LOCK TABLES `report_performance_display_hierarchy_partner_account` WRITE;
/*!40000 ALTER TABLE `report_performance_display_hierarchy_partner_account` DISABLE KEYS */;
/*!40000 ALTER TABLE `report_performance_display_hierarchy_partner_account` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `report_performance_display_hierarchy_partner_ad_network_ad_tag`
--

DROP TABLE IF EXISTS `report_performance_display_hierarchy_partner_ad_network_ad_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `report_performance_display_hierarchy_partner_ad_network_ad_tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ad_network_id` int(11) DEFAULT NULL,
  `date` date NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `partner_tag_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `total_opportunities` int(11) NOT NULL,
  `impressions` int(11) NOT NULL,
  `passbacks` int(11) NOT NULL,
  `fill_rate` decimal(10,4) NOT NULL,
  `est_revenue` decimal(10,4) DEFAULT NULL,
  `est_cpm` decimal(10,4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_partner_ad_network_ad_tag_report_idx` (`date`,`ad_network_id`,`partner_tag_id`),
  KEY `IDX_7A82FBDDCB9BD82B` (`ad_network_id`),
  CONSTRAINT `FK_7A82FBDDCB9BD82B` FOREIGN KEY (`ad_network_id`) REFERENCES `core_ad_network` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report_performance_display_hierarchy_partner_ad_network_ad_tag`
--

LOCK TABLES `report_performance_display_hierarchy_partner_ad_network_ad_tag` WRITE;
/*!40000 ALTER TABLE `report_performance_display_hierarchy_partner_ad_network_ad_tag` DISABLE KEYS */;
/*!40000 ALTER TABLE `report_performance_display_hierarchy_partner_ad_network_ad_tag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `report_performance_display_hierarchy_partner_ad_network_domain`
--

DROP TABLE IF EXISTS `report_performance_display_hierarchy_partner_ad_network_domain`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `report_performance_display_hierarchy_partner_ad_network_domain` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ad_network_id` int(11) DEFAULT NULL,
  `date` date NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `domain` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `total_opportunities` int(11) NOT NULL,
  `impressions` int(11) NOT NULL,
  `passbacks` int(11) NOT NULL,
  `fill_rate` decimal(10,4) NOT NULL,
  `est_revenue` decimal(10,4) DEFAULT NULL,
  `est_cpm` decimal(10,4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_partner_ad_network_site_report_idx` (`date`,`ad_network_id`,`domain`),
  KEY `IDX_AC553862CB9BD82B` (`ad_network_id`),
  CONSTRAINT `FK_AC553862CB9BD82B` FOREIGN KEY (`ad_network_id`) REFERENCES `core_ad_network` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report_performance_display_hierarchy_partner_ad_network_domain`
--

LOCK TABLES `report_performance_display_hierarchy_partner_ad_network_domain` WRITE;
/*!40000 ALTER TABLE `report_performance_display_hierarchy_partner_ad_network_domain` DISABLE KEYS */;
/*!40000 ALTER TABLE `report_performance_display_hierarchy_partner_ad_network_domain` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `report_performance_display_hierarchy_partner_ad_network_site_tag`
--

DROP TABLE IF EXISTS `report_performance_display_hierarchy_partner_ad_network_site_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `report_performance_display_hierarchy_partner_ad_network_site_tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ad_network_id` int(11) DEFAULT NULL,
  `date` date NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `domain` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `total_opportunities` int(11) NOT NULL,
  `impressions` int(11) NOT NULL,
  `passbacks` int(11) NOT NULL,
  `fill_rate` decimal(10,4) NOT NULL,
  `est_revenue` decimal(10,4) DEFAULT NULL,
  `est_cpm` decimal(10,4) DEFAULT NULL,
  `partner_tag_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_partner_ad_network_site_ad_tag_report_idx` (`date`,`ad_network_id`,`domain`,`partner_tag_id`),
  KEY `IDX_9126D84CCB9BD82B` (`ad_network_id`),
  CONSTRAINT `FK_9126D84CCB9BD82B` FOREIGN KEY (`ad_network_id`) REFERENCES `core_ad_network` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report_performance_display_hierarchy_partner_ad_network_site_tag`
--

LOCK TABLES `report_performance_display_hierarchy_partner_ad_network_site_tag` WRITE;
/*!40000 ALTER TABLE `report_performance_display_hierarchy_partner_ad_network_site_tag` DISABLE KEYS */;
/*!40000 ALTER TABLE `report_performance_display_hierarchy_partner_ad_network_site_tag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `report_performance_display_hierarchy_partner_adnetworksitetagsub`
--

DROP TABLE IF EXISTS `report_performance_display_hierarchy_partner_adnetworksitetagsub`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `report_performance_display_hierarchy_partner_adnetworksitetagsub` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ad_network_id` int(11) DEFAULT NULL,
  `sub_publisher_id` int(11) DEFAULT NULL,
  `date` date NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `domain` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `total_opportunities` int(11) NOT NULL,
  `impressions` int(11) NOT NULL,
  `passbacks` int(11) NOT NULL,
  `fill_rate` decimal(10,4) NOT NULL,
  `est_revenue` decimal(10,4) DEFAULT NULL,
  `est_cpm` decimal(10,4) DEFAULT NULL,
  `partner_tag_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_partner_ad_network_site_ad_tag_sub_publisher_report_idx` (`date`,`ad_network_id`,`domain`,`partner_tag_id`,`sub_publisher_id`),
  KEY `IDX_62FAC95ACB9BD82B` (`ad_network_id`),
  KEY `IDX_62FAC95A579A28F8` (`sub_publisher_id`),
  CONSTRAINT `FK_62FAC95A579A28F8` FOREIGN KEY (`sub_publisher_id`) REFERENCES `core_user_sub_publisher` (`id`),
  CONSTRAINT `FK_62FAC95ACB9BD82B` FOREIGN KEY (`ad_network_id`) REFERENCES `core_ad_network` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report_performance_display_hierarchy_partner_adnetworksitetagsub`
--

LOCK TABLES `report_performance_display_hierarchy_partner_adnetworksitetagsub` WRITE;
/*!40000 ALTER TABLE `report_performance_display_hierarchy_partner_adnetworksitetagsub` DISABLE KEYS */;
/*!40000 ALTER TABLE `report_performance_display_hierarchy_partner_adnetworksitetagsub` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `report_performance_display_hierarchy_platform`
--

DROP TABLE IF EXISTS `report_performance_display_hierarchy_platform`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `report_performance_display_hierarchy_platform` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `total_opportunities` int(11) NOT NULL,
  `slot_opportunities` int(11) NOT NULL,
  `impressions` int(11) NOT NULL,
  `passbacks` int(11) NOT NULL,
  `fill_rate` decimal(10,4) NOT NULL,
  `est_revenue` decimal(10,4) DEFAULT NULL,
  `est_cpm` decimal(10,4) DEFAULT NULL,
  `billed_rate` decimal(10,4) DEFAULT NULL,
  `billed_amount` decimal(10,4) DEFAULT NULL,
  `rtb_impressions` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_platform_report_idx` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report_performance_display_hierarchy_platform`
--

LOCK TABLES `report_performance_display_hierarchy_platform` WRITE;
/*!40000 ALTER TABLE `report_performance_display_hierarchy_platform` DISABLE KEYS */;
/*!40000 ALTER TABLE `report_performance_display_hierarchy_platform` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `report_performance_display_hierarchy_platform_account`
--

DROP TABLE IF EXISTS `report_performance_display_hierarchy_platform_account`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `report_performance_display_hierarchy_platform_account` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `publisher_id` int(11) DEFAULT NULL,
  `super_report_id` int(11) DEFAULT NULL,
  `date` date NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `total_opportunities` int(11) NOT NULL,
  `slot_opportunities` int(11) NOT NULL,
  `impressions` int(11) NOT NULL,
  `passbacks` int(11) NOT NULL,
  `fill_rate` decimal(10,4) NOT NULL,
  `est_revenue` decimal(10,4) DEFAULT NULL,
  `est_cpm` decimal(10,4) DEFAULT NULL,
  `billed_rate` decimal(10,4) DEFAULT NULL,
  `billed_amount` decimal(10,4) DEFAULT NULL,
  `rtb_impressions` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_platform_account_report_idx` (`date`,`publisher_id`),
  KEY `IDX_77E240C140C86FCE` (`publisher_id`),
  KEY `IDX_77E240C1E7B18F1F` (`super_report_id`),
  CONSTRAINT `FK_77E240C140C86FCE` FOREIGN KEY (`publisher_id`) REFERENCES `core_user` (`id`),
  CONSTRAINT `FK_77E240C1E7B18F1F` FOREIGN KEY (`super_report_id`) REFERENCES `report_performance_display_hierarchy_platform` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report_performance_display_hierarchy_platform_account`
--

LOCK TABLES `report_performance_display_hierarchy_platform_account` WRITE;
/*!40000 ALTER TABLE `report_performance_display_hierarchy_platform_account` DISABLE KEYS */;
/*!40000 ALTER TABLE `report_performance_display_hierarchy_platform_account` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `report_performance_display_hierarchy_platform_ad_slot`
--

DROP TABLE IF EXISTS `report_performance_display_hierarchy_platform_ad_slot`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `report_performance_display_hierarchy_platform_ad_slot` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ad_slot_id` int(11) DEFAULT NULL,
  `super_report_id` int(11) DEFAULT NULL,
  `date` date NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `total_opportunities` int(11) NOT NULL,
  `slot_opportunities` int(11) NOT NULL,
  `impressions` int(11) DEFAULT NULL,
  `passbacks` int(11) DEFAULT NULL,
  `fill_rate` decimal(10,4) DEFAULT NULL,
  `est_revenue` decimal(10,4) DEFAULT NULL,
  `est_cpm` decimal(10,4) DEFAULT NULL,
  `billed_rate` decimal(10,4) DEFAULT NULL,
  `custom_rate` decimal(10,4) DEFAULT NULL,
  `billed_amount` decimal(10,4) DEFAULT NULL,
  `rtb_impressions` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_platform_adslot_report_idx` (`date`,`ad_slot_id`),
  KEY `IDX_1E15646794AFF818` (`ad_slot_id`),
  KEY `IDX_1E156467E7B18F1F` (`super_report_id`),
  CONSTRAINT `FK_1E15646794AFF818` FOREIGN KEY (`ad_slot_id`) REFERENCES `core_ad_slot` (`id`),
  CONSTRAINT `FK_1E156467E7B18F1F` FOREIGN KEY (`super_report_id`) REFERENCES `report_performance_display_hierarchy_platform_site` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report_performance_display_hierarchy_platform_ad_slot`
--

LOCK TABLES `report_performance_display_hierarchy_platform_ad_slot` WRITE;
/*!40000 ALTER TABLE `report_performance_display_hierarchy_platform_ad_slot` DISABLE KEYS */;
/*!40000 ALTER TABLE `report_performance_display_hierarchy_platform_ad_slot` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `report_performance_display_hierarchy_platform_ad_tag`
--

DROP TABLE IF EXISTS `report_performance_display_hierarchy_platform_ad_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `report_performance_display_hierarchy_platform_ad_tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ad_tag_id` int(11) DEFAULT NULL,
  `super_report_id` int(11) DEFAULT NULL,
  `date` date NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `position` int(11) DEFAULT NULL,
  `total_opportunities` int(11) NOT NULL,
  `impressions` int(11) NOT NULL,
  `passbacks` int(11) DEFAULT NULL,
  `fill_rate` decimal(10,4) NOT NULL,
  `relative_fill_rate` decimal(10,4) NOT NULL,
  `est_revenue` decimal(10,4) DEFAULT NULL,
  `est_cpm` decimal(10,4) DEFAULT NULL,
  `first_opportunities` int(11) NOT NULL,
  `verified_impressions` int(11) NOT NULL,
  `unverified_impressions` int(11) DEFAULT NULL,
  `blank_impressions` int(11) DEFAULT NULL,
  `void_impressions` int(11) DEFAULT NULL,
  `clicks` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_platform_adtag_report_idx` (`date`,`ad_tag_id`),
  KEY `IDX_A5C69F3A273D74E4` (`ad_tag_id`),
  KEY `IDX_A5C69F3AE7B18F1F` (`super_report_id`),
  CONSTRAINT `FK_A5C69F3A273D74E4` FOREIGN KEY (`ad_tag_id`) REFERENCES `core_ad_tag` (`id`),
  CONSTRAINT `FK_A5C69F3AE7B18F1F` FOREIGN KEY (`super_report_id`) REFERENCES `report_performance_display_hierarchy_platform_ad_slot` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report_performance_display_hierarchy_platform_ad_tag`
--

LOCK TABLES `report_performance_display_hierarchy_platform_ad_tag` WRITE;
/*!40000 ALTER TABLE `report_performance_display_hierarchy_platform_ad_tag` DISABLE KEYS */;
/*!40000 ALTER TABLE `report_performance_display_hierarchy_platform_ad_tag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `report_performance_display_hierarchy_platform_site`
--

DROP TABLE IF EXISTS `report_performance_display_hierarchy_platform_site`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `report_performance_display_hierarchy_platform_site` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) DEFAULT NULL,
  `super_report_id` int(11) DEFAULT NULL,
  `date` date NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `total_opportunities` int(11) NOT NULL,
  `slot_opportunities` int(11) NOT NULL,
  `impressions` int(11) NOT NULL,
  `passbacks` int(11) NOT NULL,
  `fill_rate` decimal(10,4) NOT NULL,
  `est_revenue` decimal(10,4) DEFAULT NULL,
  `est_cpm` decimal(10,4) DEFAULT NULL,
  `billed_rate` decimal(10,4) DEFAULT NULL,
  `billed_amount` decimal(10,4) DEFAULT NULL,
  `rtb_impressions` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_platform_site_report_idx` (`date`,`site_id`),
  KEY `IDX_ADCD6666F6BD1646` (`site_id`),
  KEY `IDX_ADCD6666E7B18F1F` (`super_report_id`),
  CONSTRAINT `FK_ADCD6666E7B18F1F` FOREIGN KEY (`super_report_id`) REFERENCES `report_performance_display_hierarchy_platform_account` (`id`),
  CONSTRAINT `FK_ADCD6666F6BD1646` FOREIGN KEY (`site_id`) REFERENCES `core_site` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report_performance_display_hierarchy_platform_site`
--

LOCK TABLES `report_performance_display_hierarchy_platform_site` WRITE;
/*!40000 ALTER TABLE `report_performance_display_hierarchy_platform_site` DISABLE KEYS */;
/*!40000 ALTER TABLE `report_performance_display_hierarchy_platform_site` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `report_performance_display_hierarchy_segment_ron_ad_slot`
--

DROP TABLE IF EXISTS `report_performance_display_hierarchy_segment_ron_ad_slot`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `report_performance_display_hierarchy_segment_ron_ad_slot` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ron_ad_slot_id` int(11) DEFAULT NULL,
  `segment_id` int(11) DEFAULT NULL,
  `super_report_id` int(11) DEFAULT NULL,
  `date` date NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `total_opportunities` int(11) NOT NULL,
  `slot_opportunities` int(11) NOT NULL,
  `impressions` int(11) DEFAULT NULL,
  `passbacks` int(11) DEFAULT NULL,
  `fill_rate` decimal(10,4) DEFAULT NULL,
  `est_revenue` decimal(10,4) DEFAULT NULL,
  `est_cpm` decimal(10,4) DEFAULT NULL,
  `billed_rate` decimal(10,4) DEFAULT NULL,
  `custom_rate` decimal(10,4) DEFAULT NULL,
  `billed_amount` decimal(10,4) DEFAULT NULL,
  `rtb_impressions` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_platform_adslot_report_idx` (`date`,`ron_ad_slot_id`,`segment_id`),
  KEY `IDX_DFE6ACE5AE3907DE` (`ron_ad_slot_id`),
  KEY `IDX_DFE6ACE5DB296AAD` (`segment_id`),
  KEY `IDX_DFE6ACE5E7B18F1F` (`super_report_id`),
  CONSTRAINT `FK_DFE6ACE5AE3907DE` FOREIGN KEY (`ron_ad_slot_id`) REFERENCES `ron_ad_slot` (`id`),
  CONSTRAINT `FK_DFE6ACE5DB296AAD` FOREIGN KEY (`segment_id`) REFERENCES `segment` (`id`),
  CONSTRAINT `FK_DFE6ACE5E7B18F1F` FOREIGN KEY (`super_report_id`) REFERENCES `report_performance_display_hierarchy_segment_segment` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report_performance_display_hierarchy_segment_ron_ad_slot`
--

LOCK TABLES `report_performance_display_hierarchy_segment_ron_ad_slot` WRITE;
/*!40000 ALTER TABLE `report_performance_display_hierarchy_segment_ron_ad_slot` DISABLE KEYS */;
/*!40000 ALTER TABLE `report_performance_display_hierarchy_segment_ron_ad_slot` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `report_performance_display_hierarchy_segment_ron_ad_tag`
--

DROP TABLE IF EXISTS `report_performance_display_hierarchy_segment_ron_ad_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `report_performance_display_hierarchy_segment_ron_ad_tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ron_ad_tag_id` int(11) DEFAULT NULL,
  `super_report_id` int(11) DEFAULT NULL,
  `segment_id` int(11) DEFAULT NULL,
  `date` date NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `position` int(11) DEFAULT NULL,
  `total_opportunities` int(11) NOT NULL,
  `impressions` int(11) NOT NULL,
  `passbacks` int(11) DEFAULT NULL,
  `fill_rate` decimal(10,4) NOT NULL,
  `relative_fill_rate` decimal(10,4) NOT NULL,
  `est_revenue` decimal(10,4) DEFAULT NULL,
  `est_cpm` decimal(10,4) DEFAULT NULL,
  `first_opportunities` int(11) NOT NULL,
  `verified_impressions` int(11) NOT NULL,
  `unverified_impressions` int(11) DEFAULT NULL,
  `blank_impressions` int(11) DEFAULT NULL,
  `void_impressions` int(11) DEFAULT NULL,
  `clicks` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_platform_adtag_report_idx` (`date`,`ron_ad_tag_id`,`super_report_id`),
  KEY `IDX_3713B6044937C85` (`ron_ad_tag_id`),
  KEY `IDX_3713B604E7B18F1F` (`super_report_id`),
  KEY `IDX_3713B604DB296AAD` (`segment_id`),
  CONSTRAINT `FK_3713B6044937C85` FOREIGN KEY (`ron_ad_tag_id`) REFERENCES `library_ad_slot_ad_tag` (`id`),
  CONSTRAINT `FK_3713B604DB296AAD` FOREIGN KEY (`segment_id`) REFERENCES `segment` (`id`),
  CONSTRAINT `FK_3713B604E7B18F1F` FOREIGN KEY (`super_report_id`) REFERENCES `report_performance_display_hierarchy_segment_ron_ad_slot` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report_performance_display_hierarchy_segment_ron_ad_tag`
--

LOCK TABLES `report_performance_display_hierarchy_segment_ron_ad_tag` WRITE;
/*!40000 ALTER TABLE `report_performance_display_hierarchy_segment_ron_ad_tag` DISABLE KEYS */;
/*!40000 ALTER TABLE `report_performance_display_hierarchy_segment_ron_ad_tag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `report_performance_display_hierarchy_segment_segment`
--

DROP TABLE IF EXISTS `report_performance_display_hierarchy_segment_segment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `report_performance_display_hierarchy_segment_segment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `segment_id` int(11) DEFAULT NULL,
  `date` date NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `total_opportunities` int(11) NOT NULL,
  `slot_opportunities` int(11) NOT NULL,
  `impressions` int(11) NOT NULL,
  `passbacks` int(11) NOT NULL,
  `fill_rate` decimal(10,4) NOT NULL,
  `est_revenue` decimal(10,4) DEFAULT NULL,
  `est_cpm` decimal(10,4) DEFAULT NULL,
  `billed_rate` decimal(10,4) DEFAULT NULL,
  `billed_amount` decimal(10,4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_platform_site_report_idx` (`date`,`segment_id`),
  KEY `IDX_79A7ED54DB296AAD` (`segment_id`),
  CONSTRAINT `FK_79A7ED54DB296AAD` FOREIGN KEY (`segment_id`) REFERENCES `segment` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report_performance_display_hierarchy_segment_segment`
--

LOCK TABLES `report_performance_display_hierarchy_segment_segment` WRITE;
/*!40000 ALTER TABLE `report_performance_display_hierarchy_segment_segment` DISABLE KEYS */;
/*!40000 ALTER TABLE `report_performance_display_hierarchy_segment_segment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `report_performance_display_hierarchy_sub_publisher`
--

DROP TABLE IF EXISTS `report_performance_display_hierarchy_sub_publisher`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `report_performance_display_hierarchy_sub_publisher` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sub_publisher_id` int(11) DEFAULT NULL,
  `date` date NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `total_opportunities` int(11) NOT NULL,
  `impressions` int(11) NOT NULL,
  `passbacks` int(11) NOT NULL,
  `fill_rate` decimal(10,4) NOT NULL,
  `est_revenue` decimal(10,4) DEFAULT NULL,
  `est_cpm` decimal(10,4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_performance_sub_publisher_report_idx` (`date`,`sub_publisher_id`),
  KEY `IDX_CE639B56579A28F8` (`sub_publisher_id`),
  CONSTRAINT `FK_CE639B56579A28F8` FOREIGN KEY (`sub_publisher_id`) REFERENCES `core_user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report_performance_display_hierarchy_sub_publisher`
--

LOCK TABLES `report_performance_display_hierarchy_sub_publisher` WRITE;
/*!40000 ALTER TABLE `report_performance_display_hierarchy_sub_publisher` DISABLE KEYS */;
/*!40000 ALTER TABLE `report_performance_display_hierarchy_sub_publisher` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `report_performance_display_hierarchy_sub_publisher_ad_network`
--

DROP TABLE IF EXISTS `report_performance_display_hierarchy_sub_publisher_ad_network`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `report_performance_display_hierarchy_sub_publisher_ad_network` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sub_publisher_id` int(11) DEFAULT NULL,
  `ad_network_id` int(11) DEFAULT NULL,
  `date` date NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `total_opportunities` int(11) NOT NULL,
  `impressions` int(11) NOT NULL,
  `passbacks` int(11) NOT NULL,
  `fill_rate` decimal(10,4) NOT NULL,
  `est_revenue` decimal(10,4) DEFAULT NULL,
  `est_cpm` decimal(10,4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_performance_sub_publisher_ad_network_report_idx` (`date`,`sub_publisher_id`,`ad_network_id`),
  KEY `IDX_6D07125D579A28F8` (`sub_publisher_id`),
  KEY `IDX_6D07125DCB9BD82B` (`ad_network_id`),
  CONSTRAINT `FK_6D07125D579A28F8` FOREIGN KEY (`sub_publisher_id`) REFERENCES `core_user` (`id`),
  CONSTRAINT `FK_6D07125DCB9BD82B` FOREIGN KEY (`ad_network_id`) REFERENCES `core_ad_network` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report_performance_display_hierarchy_sub_publisher_ad_network`
--

LOCK TABLES `report_performance_display_hierarchy_sub_publisher_ad_network` WRITE;
/*!40000 ALTER TABLE `report_performance_display_hierarchy_sub_publisher_ad_network` DISABLE KEYS */;
/*!40000 ALTER TABLE `report_performance_display_hierarchy_sub_publisher_ad_network` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `report_rtb_hierarchy_account`
--

DROP TABLE IF EXISTS `report_rtb_hierarchy_account`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `report_rtb_hierarchy_account` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `publisher_id` int(11) DEFAULT NULL,
  `super_report_id` int(11) DEFAULT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date` date NOT NULL,
  `opportunities` int(11) NOT NULL,
  `impressions` int(11) NOT NULL,
  `fill_rate` decimal(10,4) NOT NULL,
  `earned_amount` decimal(10,4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_6088ABA40C86FCE` (`publisher_id`),
  KEY `IDX_6088ABAE7B18F1F` (`super_report_id`),
  CONSTRAINT `FK_6088ABA40C86FCE` FOREIGN KEY (`publisher_id`) REFERENCES `core_user` (`id`),
  CONSTRAINT `FK_6088ABAE7B18F1F` FOREIGN KEY (`super_report_id`) REFERENCES `report_rtb_hierarchy_platform` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report_rtb_hierarchy_account`
--

LOCK TABLES `report_rtb_hierarchy_account` WRITE;
/*!40000 ALTER TABLE `report_rtb_hierarchy_account` DISABLE KEYS */;
/*!40000 ALTER TABLE `report_rtb_hierarchy_account` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `report_rtb_hierarchy_ad_slot`
--

DROP TABLE IF EXISTS `report_rtb_hierarchy_ad_slot`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `report_rtb_hierarchy_ad_slot` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `super_report_id` int(11) DEFAULT NULL,
  `ad_slot_id` int(11) DEFAULT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date` date NOT NULL,
  `opportunities` int(11) NOT NULL,
  `impressions` int(11) NOT NULL,
  `fill_rate` decimal(10,4) NOT NULL,
  `earned_amount` decimal(10,4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_6FFFAE1CE7B18F1F` (`super_report_id`),
  KEY `IDX_6FFFAE1C94AFF818` (`ad_slot_id`),
  CONSTRAINT `FK_6FFFAE1C94AFF818` FOREIGN KEY (`ad_slot_id`) REFERENCES `core_ad_slot` (`id`),
  CONSTRAINT `FK_6FFFAE1CE7B18F1F` FOREIGN KEY (`super_report_id`) REFERENCES `report_rtb_hierarchy_site` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report_rtb_hierarchy_ad_slot`
--

LOCK TABLES `report_rtb_hierarchy_ad_slot` WRITE;
/*!40000 ALTER TABLE `report_rtb_hierarchy_ad_slot` DISABLE KEYS */;
/*!40000 ALTER TABLE `report_rtb_hierarchy_ad_slot` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `report_rtb_hierarchy_platform`
--

DROP TABLE IF EXISTS `report_rtb_hierarchy_platform`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `report_rtb_hierarchy_platform` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `opportunities` int(11) NOT NULL,
  `impressions` int(11) NOT NULL,
  `fill_rate` decimal(10,4) NOT NULL,
  `earned_amount` decimal(10,4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report_rtb_hierarchy_platform`
--

LOCK TABLES `report_rtb_hierarchy_platform` WRITE;
/*!40000 ALTER TABLE `report_rtb_hierarchy_platform` DISABLE KEYS */;
/*!40000 ALTER TABLE `report_rtb_hierarchy_platform` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `report_rtb_hierarchy_ron_ad_slot`
--

DROP TABLE IF EXISTS `report_rtb_hierarchy_ron_ad_slot`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `report_rtb_hierarchy_ron_ad_slot` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ron_ad_slot_id` int(11) DEFAULT NULL,
  `segment_id` int(11) DEFAULT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date` date NOT NULL,
  `opportunities` int(11) NOT NULL,
  `impressions` int(11) NOT NULL,
  `fill_rate` decimal(10,4) NOT NULL,
  `earned_amount` decimal(10,4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_CE684C32AE3907DE` (`ron_ad_slot_id`),
  KEY `IDX_CE684C32DB296AAD` (`segment_id`),
  CONSTRAINT `FK_CE684C32AE3907DE` FOREIGN KEY (`ron_ad_slot_id`) REFERENCES `ron_ad_slot` (`id`),
  CONSTRAINT `FK_CE684C32DB296AAD` FOREIGN KEY (`segment_id`) REFERENCES `segment` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report_rtb_hierarchy_ron_ad_slot`
--

LOCK TABLES `report_rtb_hierarchy_ron_ad_slot` WRITE;
/*!40000 ALTER TABLE `report_rtb_hierarchy_ron_ad_slot` DISABLE KEYS */;
/*!40000 ALTER TABLE `report_rtb_hierarchy_ron_ad_slot` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `report_rtb_hierarchy_site`
--

DROP TABLE IF EXISTS `report_rtb_hierarchy_site`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `report_rtb_hierarchy_site` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) DEFAULT NULL,
  `super_report_id` int(11) DEFAULT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date` date NOT NULL,
  `opportunities` int(11) NOT NULL,
  `impressions` int(11) NOT NULL,
  `fill_rate` decimal(10,4) NOT NULL,
  `earned_amount` decimal(10,4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_95F84E28F6BD1646` (`site_id`),
  KEY `IDX_95F84E28E7B18F1F` (`super_report_id`),
  CONSTRAINT `FK_95F84E28E7B18F1F` FOREIGN KEY (`super_report_id`) REFERENCES `report_rtb_hierarchy_account` (`id`),
  CONSTRAINT `FK_95F84E28F6BD1646` FOREIGN KEY (`site_id`) REFERENCES `core_site` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report_rtb_hierarchy_site`
--

LOCK TABLES `report_rtb_hierarchy_site` WRITE;
/*!40000 ALTER TABLE `report_rtb_hierarchy_site` DISABLE KEYS */;
/*!40000 ALTER TABLE `report_rtb_hierarchy_site` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `report_source_report`
--

DROP TABLE IF EXISTS `report_source_report`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `report_source_report` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) DEFAULT NULL,
  `date` date NOT NULL,
  `display_opportunities` int(11) DEFAULT NULL,
  `display_impressions` int(11) DEFAULT NULL,
  `display_fill_rate` decimal(10,4) DEFAULT NULL,
  `display_clicks` int(11) DEFAULT NULL,
  `display_ctr` decimal(10,4) DEFAULT NULL,
  `display_ipv` decimal(10,4) DEFAULT NULL,
  `video_player_ready` int(11) DEFAULT NULL,
  `video_ad_plays` int(11) DEFAULT NULL,
  `video_ad_impressions` int(11) DEFAULT NULL,
  `video_ad_completions` int(11) DEFAULT NULL,
  `video_ad_completion_rate` decimal(10,4) DEFAULT NULL,
  `video_ipv` decimal(10,4) DEFAULT NULL,
  `video_ad_clicks` int(11) DEFAULT NULL,
  `video_starts` int(11) DEFAULT NULL,
  `video_ends` int(11) DEFAULT NULL,
  `visits` int(11) DEFAULT NULL,
  `page_views` int(11) DEFAULT NULL,
  `qtos` int(11) DEFAULT NULL,
  `qtos_percentage` decimal(10,4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_report_idx` (`date`,`site_id`),
  KEY `IDX_FB0D1312F6BD1646` (`site_id`),
  CONSTRAINT `FK_FB0D1312F6BD1646` FOREIGN KEY (`site_id`) REFERENCES `core_site` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report_source_report`
--

LOCK TABLES `report_source_report` WRITE;
/*!40000 ALTER TABLE `report_source_report` DISABLE KEYS */;
/*!40000 ALTER TABLE `report_source_report` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `report_source_report_record`
--

DROP TABLE IF EXISTS `report_source_report_record`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `report_source_report_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `source_report_id` int(11) DEFAULT NULL,
  `embedded_tracking_keys` longtext COLLATE utf8_unicode_ci COMMENT '(DC2Type:json_array)',
  `display_opportunities` int(11) DEFAULT NULL,
  `display_impressions` int(11) DEFAULT NULL,
  `display_fill_rate` decimal(10,4) DEFAULT NULL,
  `display_clicks` int(11) DEFAULT NULL,
  `display_ctr` decimal(10,4) DEFAULT NULL,
  `display_ipv` decimal(10,4) DEFAULT NULL,
  `video_player_ready` int(11) DEFAULT NULL,
  `video_ad_plays` int(11) DEFAULT NULL,
  `video_ad_impressions` int(11) DEFAULT NULL,
  `video_ad_completions` int(11) DEFAULT NULL,
  `video_ad_completion_rate` decimal(10,4) DEFAULT NULL,
  `video_ipv` decimal(10,4) DEFAULT NULL,
  `video_ad_clicks` int(11) DEFAULT NULL,
  `video_starts` int(11) DEFAULT NULL,
  `video_ends` int(11) DEFAULT NULL,
  `visits` int(11) DEFAULT NULL,
  `page_views` int(11) DEFAULT NULL,
  `qtos` int(11) DEFAULT NULL,
  `qtos_percentage` decimal(10,4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_BEF3D0D7234C370E` (`source_report_id`),
  CONSTRAINT `FK_BEF3D0D7234C370E` FOREIGN KEY (`source_report_id`) REFERENCES `report_source_report` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report_source_report_record`
--

LOCK TABLES `report_source_report_record` WRITE;
/*!40000 ALTER TABLE `report_source_report_record` DISABLE KEYS */;
/*!40000 ALTER TABLE `report_source_report_record` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `report_source_report_record_x_tracking_key`
--

DROP TABLE IF EXISTS `report_source_report_record_x_tracking_key`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `report_source_report_record_x_tracking_key` (
  `record_id` int(11) NOT NULL,
  `tracking_key_id` int(11) NOT NULL,
  PRIMARY KEY (`record_id`,`tracking_key_id`),
  KEY `IDX_89D775414DFD750C` (`record_id`),
  KEY `IDX_89D77541A598D67F` (`tracking_key_id`),
  CONSTRAINT `FK_89D775414DFD750C` FOREIGN KEY (`record_id`) REFERENCES `report_source_report_record` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_89D77541A598D67F` FOREIGN KEY (`tracking_key_id`) REFERENCES `report_source_tracking_key` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report_source_report_record_x_tracking_key`
--

LOCK TABLES `report_source_report_record_x_tracking_key` WRITE;
/*!40000 ALTER TABLE `report_source_report_record_x_tracking_key` DISABLE KEYS */;
/*!40000 ALTER TABLE `report_source_report_record_x_tracking_key` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `report_source_tracking_key`
--

DROP TABLE IF EXISTS `report_source_tracking_key`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `report_source_tracking_key` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tracking_term_id` int(11) DEFAULT NULL,
  `value` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_4F61B9D171EEB4C4` (`tracking_term_id`),
  CONSTRAINT `FK_4F61B9D171EEB4C4` FOREIGN KEY (`tracking_term_id`) REFERENCES `report_source_tracking_term` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report_source_tracking_key`
--

LOCK TABLES `report_source_tracking_key` WRITE;
/*!40000 ALTER TABLE `report_source_tracking_key` DISABLE KEYS */;
/*!40000 ALTER TABLE `report_source_tracking_key` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `report_source_tracking_term`
--

DROP TABLE IF EXISTS `report_source_tracking_term`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `report_source_tracking_term` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `term` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report_source_tracking_term`
--

LOCK TABLES `report_source_tracking_term` WRITE;
/*!40000 ALTER TABLE `report_source_tracking_term` DISABLE KEYS */;
/*!40000 ALTER TABLE `report_source_tracking_term` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ron_ad_slot`
--

DROP TABLE IF EXISTS `ron_ad_slot`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ron_ad_slot` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `library_ad_slot_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `floor_price` double DEFAULT NULL,
  `rtb_status` smallint(6) NOT NULL DEFAULT '2',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_library_ad_slot_id_constraint` (`library_ad_slot_id`,`deleted_at`),
  KEY `IDX_7C2F130370BBCB64` (`library_ad_slot_id`),
  CONSTRAINT `FK_7C2F130370BBCB64` FOREIGN KEY (`library_ad_slot_id`) REFERENCES `library_ad_slot` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ron_ad_slot`
--

LOCK TABLES `ron_ad_slot` WRITE;
/*!40000 ALTER TABLE `ron_ad_slot` DISABLE KEYS */;
/*!40000 ALTER TABLE `ron_ad_slot` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ron_ad_slot_segment`
--

DROP TABLE IF EXISTS `ron_ad_slot_segment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ron_ad_slot_segment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `segment_id` int(11) DEFAULT NULL,
  `ron_ad_slot_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `deleted_at` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_7EA82F85DB296AAD` (`segment_id`),
  KEY `IDX_7EA82F85AE3907DE` (`ron_ad_slot_id`),
  CONSTRAINT `FK_7EA82F85AE3907DE` FOREIGN KEY (`ron_ad_slot_id`) REFERENCES `ron_ad_slot` (`id`),
  CONSTRAINT `FK_7EA82F85DB296AAD` FOREIGN KEY (`segment_id`) REFERENCES `segment` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ron_ad_slot_segment`
--

LOCK TABLES `ron_ad_slot_segment` WRITE;
/*!40000 ALTER TABLE `ron_ad_slot_segment` DISABLE KEYS */;
/*!40000 ALTER TABLE `ron_ad_slot_segment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `segment`
--

DROP TABLE IF EXISTS `segment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `segment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `publisher_id` int(11) DEFAULT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `deleted_at` date DEFAULT NULL,
  `sub_publisher_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_1881F56540C86FCE` (`publisher_id`),
  KEY `IDX_1881F565579A28F8` (`sub_publisher_id`),
  CONSTRAINT `FK_1881F56540C86FCE` FOREIGN KEY (`publisher_id`) REFERENCES `core_user` (`id`),
  CONSTRAINT `FK_1881F565579A28F8` FOREIGN KEY (`sub_publisher_id`) REFERENCES `core_user_sub_publisher` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `segment`
--

LOCK TABLES `segment` WRITE;
/*!40000 ALTER TABLE `segment` DISABLE KEYS */;
INSERT INTO `segment` VALUES (1,2,'sub1','2016-07-20 17:39:11',NULL,3);
/*!40000 ALTER TABLE `segment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `source_report_email_config`
--

DROP TABLE IF EXISTS `source_report_email_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `source_report_email_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `included_all` tinyint(1) NOT NULL DEFAULT '0',
  `included_all_sites_of_publishers` longtext COLLATE utf8_unicode_ci COMMENT '(DC2Type:json_array)',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `deleted_at` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_B04788BDE7927C74` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `source_report_email_config`
--

LOCK TABLES `source_report_email_config` WRITE;
/*!40000 ALTER TABLE `source_report_email_config` DISABLE KEYS */;
/*!40000 ALTER TABLE `source_report_email_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `source_report_site_config`
--

DROP TABLE IF EXISTS `source_report_site_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `source_report_site_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) DEFAULT NULL,
  `source_report_email_config_id` int(11) DEFAULT NULL,
  `deleted_at` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_source_report_site_config_idx` (`source_report_email_config_id`,`site_id`),
  KEY `IDX_9F66C1B5F6BD1646` (`site_id`),
  KEY `IDX_9F66C1B5B0256EB3` (`source_report_email_config_id`),
  CONSTRAINT `FK_9F66C1B5B0256EB3` FOREIGN KEY (`source_report_email_config_id`) REFERENCES `source_report_email_config` (`id`),
  CONSTRAINT `FK_9F66C1B5F6BD1646` FOREIGN KEY (`site_id`) REFERENCES `core_site` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `source_report_site_config`
--

LOCK TABLES `source_report_site_config` WRITE;
/*!40000 ALTER TABLE `source_report_site_config` DISABLE KEYS */;
/*!40000 ALTER TABLE `source_report_site_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `unified_report_comparison_account`
--

DROP TABLE IF EXISTS `unified_report_comparison_account`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `unified_report_comparison_account` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `performance_account_report_id` int(11) DEFAULT NULL,
  `unified_account_report_id` int(11) DEFAULT NULL,
  `publisher_id` int(11) DEFAULT NULL,
  `date` date NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_CAEF9A726181E349` (`performance_account_report_id`),
  UNIQUE KEY `UNIQ_CAEF9A726F6F4BE5` (`unified_account_report_id`),
  UNIQUE KEY `unified_report_comparison_account_report_unique_key` (`date`,`performance_account_report_id`,`unified_account_report_id`,`publisher_id`),
  KEY `IDX_CAEF9A7240C86FCE` (`publisher_id`),
  CONSTRAINT `FK_CAEF9A7240C86FCE` FOREIGN KEY (`publisher_id`) REFERENCES `core_user` (`id`),
  CONSTRAINT `FK_CAEF9A726181E349` FOREIGN KEY (`performance_account_report_id`) REFERENCES `report_performance_display_hierarchy_partner_account` (`id`),
  CONSTRAINT `FK_CAEF9A726F6F4BE5` FOREIGN KEY (`unified_account_report_id`) REFERENCES `unified_report_publisher` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `unified_report_comparison_account`
--

LOCK TABLES `unified_report_comparison_account` WRITE;
/*!40000 ALTER TABLE `unified_report_comparison_account` DISABLE KEYS */;
/*!40000 ALTER TABLE `unified_report_comparison_account` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `unified_report_comparison_ad_network`
--

DROP TABLE IF EXISTS `unified_report_comparison_ad_network`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `unified_report_comparison_ad_network` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `performance_ad_network_report_id` int(11) DEFAULT NULL,
  `unified_ad_network_report_id` int(11) DEFAULT NULL,
  `ad_network_id` int(11) DEFAULT NULL,
  `date` date NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_BC8AD53F9AFD9563` (`performance_ad_network_report_id`),
  UNIQUE KEY `UNIQ_BC8AD53F6BF3C8F7` (`unified_ad_network_report_id`),
  UNIQUE KEY `unified_report_comparison_ad_network_report_unique_key` (`date`,`performance_ad_network_report_id`,`unified_ad_network_report_id`,`ad_network_id`),
  KEY `IDX_BC8AD53FCB9BD82B` (`ad_network_id`),
  CONSTRAINT `FK_BC8AD53F6BF3C8F7` FOREIGN KEY (`unified_ad_network_report_id`) REFERENCES `unified_report_network` (`id`),
  CONSTRAINT `FK_BC8AD53F9AFD9563` FOREIGN KEY (`performance_ad_network_report_id`) REFERENCES `report_performance_display_hierarchy_ad_network` (`id`),
  CONSTRAINT `FK_BC8AD53FCB9BD82B` FOREIGN KEY (`ad_network_id`) REFERENCES `core_ad_network` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `unified_report_comparison_ad_network`
--

LOCK TABLES `unified_report_comparison_ad_network` WRITE;
/*!40000 ALTER TABLE `unified_report_comparison_ad_network` DISABLE KEYS */;
/*!40000 ALTER TABLE `unified_report_comparison_ad_network` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `unified_report_comparison_ad_network_ad_tag`
--

DROP TABLE IF EXISTS `unified_report_comparison_ad_network_ad_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `unified_report_comparison_ad_network_ad_tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `performance_ad_network_ad_tag_report_id` int(11) DEFAULT NULL,
  `unified_ad_network_ad_tag_report_id` int(11) DEFAULT NULL,
  `ad_network_id` int(11) DEFAULT NULL,
  `date` date NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `partner_tag_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_38673B01F3C2AB1F` (`performance_ad_network_ad_tag_report_id`),
  UNIQUE KEY `UNIQ_38673B01D9BF2DBA` (`unified_ad_network_ad_tag_report_id`),
  UNIQUE KEY `unified_report_comparison_ad_network_ad_tag_report_unique_key` (`date`,`performance_ad_network_ad_tag_report_id`,`unified_ad_network_ad_tag_report_id`,`partner_tag_id`,`ad_network_id`),
  KEY `IDX_38673B01CB9BD82B` (`ad_network_id`),
  CONSTRAINT `FK_38673B01CB9BD82B` FOREIGN KEY (`ad_network_id`) REFERENCES `core_ad_network` (`id`),
  CONSTRAINT `FK_38673B01D9BF2DBA` FOREIGN KEY (`unified_ad_network_ad_tag_report_id`) REFERENCES `unified_report_network_ad_tag` (`id`),
  CONSTRAINT `FK_38673B01F3C2AB1F` FOREIGN KEY (`performance_ad_network_ad_tag_report_id`) REFERENCES `report_performance_display_hierarchy_partner_ad_network_ad_tag` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `unified_report_comparison_ad_network_ad_tag`
--

LOCK TABLES `unified_report_comparison_ad_network_ad_tag` WRITE;
/*!40000 ALTER TABLE `unified_report_comparison_ad_network_ad_tag` DISABLE KEYS */;
/*!40000 ALTER TABLE `unified_report_comparison_ad_network_ad_tag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `unified_report_comparison_ad_network_domain`
--

DROP TABLE IF EXISTS `unified_report_comparison_ad_network_domain`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `unified_report_comparison_ad_network_domain` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `performance_ad_network_domain_report_id` int(11) DEFAULT NULL,
  `unified_network_site_report_id` int(11) DEFAULT NULL,
  `ad_network_id` int(11) DEFAULT NULL,
  `date` date NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `domain` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_EEB0F8BEC9BE0D5C` (`performance_ad_network_domain_report_id`),
  UNIQUE KEY `UNIQ_EEB0F8BE71EFA8EA` (`unified_network_site_report_id`),
  UNIQUE KEY `unified_report_comparison_ad_network_domain_report_unique_key` (`date`,`performance_ad_network_domain_report_id`,`unified_network_site_report_id`,`domain`,`ad_network_id`),
  KEY `IDX_EEB0F8BECB9BD82B` (`ad_network_id`),
  CONSTRAINT `FK_EEB0F8BE71EFA8EA` FOREIGN KEY (`unified_network_site_report_id`) REFERENCES `unified_report_network_site` (`id`),
  CONSTRAINT `FK_EEB0F8BEC9BE0D5C` FOREIGN KEY (`performance_ad_network_domain_report_id`) REFERENCES `report_performance_display_hierarchy_partner_ad_network_domain` (`id`),
  CONSTRAINT `FK_EEB0F8BECB9BD82B` FOREIGN KEY (`ad_network_id`) REFERENCES `core_ad_network` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `unified_report_comparison_ad_network_domain`
--

LOCK TABLES `unified_report_comparison_ad_network_domain` WRITE;
/*!40000 ALTER TABLE `unified_report_comparison_ad_network_domain` DISABLE KEYS */;
/*!40000 ALTER TABLE `unified_report_comparison_ad_network_domain` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `unified_report_comparison_ad_network_domain_ad_tag`
--

DROP TABLE IF EXISTS `unified_report_comparison_ad_network_domain_ad_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `unified_report_comparison_ad_network_domain_ad_tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `performance_ad_network_domain_ad_tag_report_id` int(11) DEFAULT NULL,
  `unified_ad_network_domain_ad_tag_report_id` int(11) DEFAULT NULL,
  `ad_network_id` int(11) DEFAULT NULL,
  `date` date NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `partner_tag_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `domain` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_27A1ABA19A37685F` (`performance_ad_network_domain_ad_tag_report_id`),
  UNIQUE KEY `UNIQ_27A1ABA189800877` (`unified_ad_network_domain_ad_tag_report_id`),
  UNIQUE KEY `unified_report_comparison_ad_network_domain_ad_tag_unique_key` (`date`,`domain`,`partner_tag_id`,`performance_ad_network_domain_ad_tag_report_id`,`unified_ad_network_domain_ad_tag_report_id`,`ad_network_id`),
  KEY `IDX_27A1ABA1CB9BD82B` (`ad_network_id`),
  CONSTRAINT `FK_27A1ABA189800877` FOREIGN KEY (`unified_ad_network_domain_ad_tag_report_id`) REFERENCES `unified_report_network_domain_ad_tag` (`id`),
  CONSTRAINT `FK_27A1ABA19A37685F` FOREIGN KEY (`performance_ad_network_domain_ad_tag_report_id`) REFERENCES `report_performance_display_hierarchy_partner_ad_network_site_tag` (`id`),
  CONSTRAINT `FK_27A1ABA1CB9BD82B` FOREIGN KEY (`ad_network_id`) REFERENCES `core_ad_network` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `unified_report_comparison_ad_network_domain_ad_tag`
--

LOCK TABLES `unified_report_comparison_ad_network_domain_ad_tag` WRITE;
/*!40000 ALTER TABLE `unified_report_comparison_ad_network_domain_ad_tag` DISABLE KEYS */;
/*!40000 ALTER TABLE `unified_report_comparison_ad_network_domain_ad_tag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `unified_report_comparison_ad_network_domain_ad_tag_sub_publisher`
--

DROP TABLE IF EXISTS `unified_report_comparison_ad_network_domain_ad_tag_sub_publisher`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `unified_report_comparison_ad_network_domain_ad_tag_sub_publisher` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `performance_ad_network_domain_ad_tag_sub_publisher_report_id` int(11) DEFAULT NULL,
  `unified_ad_network_domain_ad_tag_sub_publisher_report_id` int(11) DEFAULT NULL,
  `ad_network_id` int(11) DEFAULT NULL,
  `sub_publisher_id` int(11) DEFAULT NULL,
  `date` date NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `partner_tag_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `domain` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_4A8416F8CF3B245D` (`performance_ad_network_domain_ad_tag_sub_publisher_report_id`),
  UNIQUE KEY `UNIQ_4A8416F88978A4C` (`unified_ad_network_domain_ad_tag_sub_publisher_report_id`),
  UNIQUE KEY `unified_report_comparison_network_domain_tag_subpub_unique_key` (`date`,`domain`,`partner_tag_id`,`performance_ad_network_domain_ad_tag_sub_publisher_report_id`,`unified_ad_network_domain_ad_tag_sub_publisher_report_id`,`ad_network_id`,`sub_publisher_id`),
  KEY `IDX_4A8416F8CB9BD82B` (`ad_network_id`),
  KEY `IDX_4A8416F8579A28F8` (`sub_publisher_id`),
  CONSTRAINT `FK_4A8416F8579A28F8` FOREIGN KEY (`sub_publisher_id`) REFERENCES `core_user_sub_publisher` (`id`),
  CONSTRAINT `FK_4A8416F88978A4C` FOREIGN KEY (`unified_ad_network_domain_ad_tag_sub_publisher_report_id`) REFERENCES `unified_report_network_domain_ad_tag_sub_publisher` (`id`),
  CONSTRAINT `FK_4A8416F8CB9BD82B` FOREIGN KEY (`ad_network_id`) REFERENCES `core_ad_network` (`id`),
  CONSTRAINT `FK_4A8416F8CF3B245D` FOREIGN KEY (`performance_ad_network_domain_ad_tag_sub_publisher_report_id`) REFERENCES `report_performance_display_hierarchy_partner_adnetworksitetagsub` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `unified_report_comparison_ad_network_domain_ad_tag_sub_publisher`
--

LOCK TABLES `unified_report_comparison_ad_network_domain_ad_tag_sub_publisher` WRITE;
/*!40000 ALTER TABLE `unified_report_comparison_ad_network_domain_ad_tag_sub_publisher` DISABLE KEYS */;
/*!40000 ALTER TABLE `unified_report_comparison_ad_network_domain_ad_tag_sub_publisher` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `unified_report_comparison_sub_publisher`
--

DROP TABLE IF EXISTS `unified_report_comparison_sub_publisher`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `unified_report_comparison_sub_publisher` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `performance_sub_publisher_report_id` int(11) DEFAULT NULL,
  `unified_sub_publisher_report_id` int(11) DEFAULT NULL,
  `sub_publisher_id` int(11) DEFAULT NULL,
  `date` date NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_FD2A77963A5DB108` (`performance_sub_publisher_report_id`),
  UNIQUE KEY `UNIQ_FD2A779628EAAFCB` (`unified_sub_publisher_report_id`),
  UNIQUE KEY `unified_report_comparison_sub_publisher_report_unique_key` (`date`,`performance_sub_publisher_report_id`,`unified_sub_publisher_report_id`,`sub_publisher_id`),
  KEY `IDX_FD2A7796579A28F8` (`sub_publisher_id`),
  CONSTRAINT `FK_FD2A779628EAAFCB` FOREIGN KEY (`unified_sub_publisher_report_id`) REFERENCES `unified_report_publisher_sub_publisher` (`id`),
  CONSTRAINT `FK_FD2A77963A5DB108` FOREIGN KEY (`performance_sub_publisher_report_id`) REFERENCES `report_performance_display_hierarchy_sub_publisher` (`id`),
  CONSTRAINT `FK_FD2A7796579A28F8` FOREIGN KEY (`sub_publisher_id`) REFERENCES `core_user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `unified_report_comparison_sub_publisher`
--

LOCK TABLES `unified_report_comparison_sub_publisher` WRITE;
/*!40000 ALTER TABLE `unified_report_comparison_sub_publisher` DISABLE KEYS */;
/*!40000 ALTER TABLE `unified_report_comparison_sub_publisher` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `unified_report_comparison_sub_publisher_ad_network`
--

DROP TABLE IF EXISTS `unified_report_comparison_sub_publisher_ad_network`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `unified_report_comparison_sub_publisher_ad_network` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `performance_sub_publisher_ad_network_report_id` int(11) DEFAULT NULL,
  `unified_sub_publisher_ad_network_report_id` int(11) DEFAULT NULL,
  `sub_publisher_id` int(11) DEFAULT NULL,
  `ad_network_id` int(11) DEFAULT NULL,
  `date` date NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_98033210A360A385` (`performance_sub_publisher_ad_network_report_id`),
  UNIQUE KEY `UNIQ_98033210B0D7C3AD` (`unified_sub_publisher_ad_network_report_id`),
  UNIQUE KEY `unified_report_comparison_sub_publisher_ad_network_unique_key` (`date`,`performance_sub_publisher_ad_network_report_id`,`unified_sub_publisher_ad_network_report_id`,`sub_publisher_id`,`ad_network_id`),
  KEY `IDX_98033210579A28F8` (`sub_publisher_id`),
  KEY `IDX_98033210CB9BD82B` (`ad_network_id`),
  CONSTRAINT `FK_98033210579A28F8` FOREIGN KEY (`sub_publisher_id`) REFERENCES `core_user` (`id`),
  CONSTRAINT `FK_98033210A360A385` FOREIGN KEY (`performance_sub_publisher_ad_network_report_id`) REFERENCES `report_performance_display_hierarchy_sub_publisher_ad_network` (`id`),
  CONSTRAINT `FK_98033210B0D7C3AD` FOREIGN KEY (`unified_sub_publisher_ad_network_report_id`) REFERENCES `unified_report_publisher_sub_publisher_network` (`id`),
  CONSTRAINT `FK_98033210CB9BD82B` FOREIGN KEY (`ad_network_id`) REFERENCES `core_ad_network` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `unified_report_comparison_sub_publisher_ad_network`
--

LOCK TABLES `unified_report_comparison_sub_publisher_ad_network` WRITE;
/*!40000 ALTER TABLE `unified_report_comparison_sub_publisher_ad_network` DISABLE KEYS */;
/*!40000 ALTER TABLE `unified_report_comparison_sub_publisher_ad_network` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `unified_report_network`
--

DROP TABLE IF EXISTS `unified_report_network`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `unified_report_network` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ad_network_id` int(11) DEFAULT NULL,
  `date` date NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `fill_rate` double DEFAULT NULL,
  `impressions` int(11) DEFAULT NULL,
  `total_opportunities` int(11) DEFAULT '0',
  `passbacks` int(11) DEFAULT NULL,
  `est_cpm` double DEFAULT NULL,
  `est_revenue` double DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `network_report_unique_key` (`ad_network_id`,`date`),
  KEY `IDX_E7CC5BD1CB9BD82B` (`ad_network_id`),
  CONSTRAINT `FK_E7CC5BD1CB9BD82B` FOREIGN KEY (`ad_network_id`) REFERENCES `core_ad_network` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `unified_report_network`
--

LOCK TABLES `unified_report_network` WRITE;
/*!40000 ALTER TABLE `unified_report_network` DISABLE KEYS */;
/*!40000 ALTER TABLE `unified_report_network` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `unified_report_network_ad_tag`
--

DROP TABLE IF EXISTS `unified_report_network_ad_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `unified_report_network_ad_tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ad_network_id` int(11) DEFAULT NULL,
  `date` date NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fill_rate` double DEFAULT NULL,
  `impressions` int(11) DEFAULT NULL,
  `total_opportunities` int(11) DEFAULT '0',
  `passbacks` int(11) DEFAULT NULL,
  `est_cpm` double DEFAULT NULL,
  `est_revenue` double DEFAULT NULL,
  `partner_tag_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `network_ad_tag_report_unique_key` (`ad_network_id`,`partner_tag_id`,`date`),
  KEY `IDX_84FDB6A9CB9BD82B` (`ad_network_id`),
  CONSTRAINT `FK_84FDB6A9CB9BD82B` FOREIGN KEY (`ad_network_id`) REFERENCES `core_ad_network` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `unified_report_network_ad_tag`
--

LOCK TABLES `unified_report_network_ad_tag` WRITE;
/*!40000 ALTER TABLE `unified_report_network_ad_tag` DISABLE KEYS */;
/*!40000 ALTER TABLE `unified_report_network_ad_tag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `unified_report_network_ad_tag_sub_publisher`
--

DROP TABLE IF EXISTS `unified_report_network_ad_tag_sub_publisher`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `unified_report_network_ad_tag_sub_publisher` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ad_network_id` int(11) DEFAULT NULL,
  `sub_publisher_id` int(11) DEFAULT NULL,
  `date` date NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fill_rate` double DEFAULT NULL,
  `impressions` int(11) DEFAULT NULL,
  `total_opportunities` int(11) DEFAULT '0',
  `passbacks` int(11) DEFAULT NULL,
  `est_cpm` double DEFAULT NULL,
  `est_revenue` double DEFAULT NULL,
  `partner_tag_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `network_ad_tag_sub_publisher_report_unique_key` (`ad_network_id`,`partner_tag_id`,`date`,`sub_publisher_id`),
  KEY `IDX_DB1805AECB9BD82B` (`ad_network_id`),
  KEY `IDX_DB1805AE579A28F8` (`sub_publisher_id`),
  CONSTRAINT `FK_DB1805AE579A28F8` FOREIGN KEY (`sub_publisher_id`) REFERENCES `core_user_sub_publisher` (`id`),
  CONSTRAINT `FK_DB1805AECB9BD82B` FOREIGN KEY (`ad_network_id`) REFERENCES `core_ad_network` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `unified_report_network_ad_tag_sub_publisher`
--

LOCK TABLES `unified_report_network_ad_tag_sub_publisher` WRITE;
/*!40000 ALTER TABLE `unified_report_network_ad_tag_sub_publisher` DISABLE KEYS */;
/*!40000 ALTER TABLE `unified_report_network_ad_tag_sub_publisher` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `unified_report_network_domain_ad_tag`
--

DROP TABLE IF EXISTS `unified_report_network_domain_ad_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `unified_report_network_domain_ad_tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ad_network_id` int(11) DEFAULT NULL,
  `date` date NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fill_rate` double DEFAULT NULL,
  `impressions` int(11) DEFAULT NULL,
  `total_opportunities` int(11) DEFAULT '0',
  `passbacks` int(11) DEFAULT NULL,
  `est_cpm` double DEFAULT NULL,
  `est_revenue` double DEFAULT NULL,
  `partner_tag_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `domain` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `network_ad_tag_report_unique_key` (`ad_network_id`,`domain`,`partner_tag_id`,`date`),
  KEY `IDX_3DD9FF3DCB9BD82B` (`ad_network_id`),
  CONSTRAINT `FK_3DD9FF3DCB9BD82B` FOREIGN KEY (`ad_network_id`) REFERENCES `core_ad_network` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `unified_report_network_domain_ad_tag`
--

LOCK TABLES `unified_report_network_domain_ad_tag` WRITE;
/*!40000 ALTER TABLE `unified_report_network_domain_ad_tag` DISABLE KEYS */;
/*!40000 ALTER TABLE `unified_report_network_domain_ad_tag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `unified_report_network_domain_ad_tag_sub_publisher`
--

DROP TABLE IF EXISTS `unified_report_network_domain_ad_tag_sub_publisher`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `unified_report_network_domain_ad_tag_sub_publisher` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ad_network_id` int(11) DEFAULT NULL,
  `sub_publisher_id` int(11) DEFAULT NULL,
  `date` date NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fill_rate` double DEFAULT NULL,
  `impressions` int(11) DEFAULT NULL,
  `total_opportunities` int(11) DEFAULT '0',
  `passbacks` int(11) DEFAULT NULL,
  `est_cpm` double DEFAULT NULL,
  `est_revenue` double DEFAULT NULL,
  `partner_tag_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `domain` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `network_ad_tag_report_unique_key` (`ad_network_id`,`domain`,`partner_tag_id`,`date`,`sub_publisher_id`),
  KEY `IDX_229C62F9CB9BD82B` (`ad_network_id`),
  KEY `IDX_229C62F9579A28F8` (`sub_publisher_id`),
  CONSTRAINT `FK_229C62F9579A28F8` FOREIGN KEY (`sub_publisher_id`) REFERENCES `core_user_sub_publisher` (`id`),
  CONSTRAINT `FK_229C62F9CB9BD82B` FOREIGN KEY (`ad_network_id`) REFERENCES `core_ad_network` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `unified_report_network_domain_ad_tag_sub_publisher`
--

LOCK TABLES `unified_report_network_domain_ad_tag_sub_publisher` WRITE;
/*!40000 ALTER TABLE `unified_report_network_domain_ad_tag_sub_publisher` DISABLE KEYS */;
/*!40000 ALTER TABLE `unified_report_network_domain_ad_tag_sub_publisher` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `unified_report_network_site`
--

DROP TABLE IF EXISTS `unified_report_network_site`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `unified_report_network_site` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ad_network_id` int(11) DEFAULT NULL,
  `date` date NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `total_opportunities` int(11) DEFAULT '0',
  `fill_rate` double DEFAULT NULL,
  `impressions` int(11) DEFAULT NULL,
  `passbacks` int(11) DEFAULT NULL,
  `est_cpm` double DEFAULT NULL,
  `est_revenue` double DEFAULT NULL,
  `domain` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `network_site_report_unique_key` (`ad_network_id`,`domain`,`date`),
  KEY `IDX_1575ADBACB9BD82B` (`ad_network_id`),
  CONSTRAINT `FK_1575ADBACB9BD82B` FOREIGN KEY (`ad_network_id`) REFERENCES `core_ad_network` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `unified_report_network_site`
--

LOCK TABLES `unified_report_network_site` WRITE;
/*!40000 ALTER TABLE `unified_report_network_site` DISABLE KEYS */;
/*!40000 ALTER TABLE `unified_report_network_site` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `unified_report_network_site_sub_publisher`
--

DROP TABLE IF EXISTS `unified_report_network_site_sub_publisher`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `unified_report_network_site_sub_publisher` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ad_network_id` int(11) DEFAULT NULL,
  `sub_publisher_id` int(11) DEFAULT NULL,
  `date` date NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `total_opportunities` int(11) DEFAULT '0',
  `fill_rate` double DEFAULT NULL,
  `impressions` int(11) DEFAULT NULL,
  `passbacks` int(11) DEFAULT NULL,
  `est_cpm` double DEFAULT NULL,
  `est_revenue` double DEFAULT NULL,
  `domain` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `network_site_sub_publisher_report_unique_key` (`ad_network_id`,`domain`,`date`,`sub_publisher_id`),
  KEY `IDX_EC5732EACB9BD82B` (`ad_network_id`),
  KEY `IDX_EC5732EA579A28F8` (`sub_publisher_id`),
  CONSTRAINT `FK_EC5732EA579A28F8` FOREIGN KEY (`sub_publisher_id`) REFERENCES `core_user_sub_publisher` (`id`),
  CONSTRAINT `FK_EC5732EACB9BD82B` FOREIGN KEY (`ad_network_id`) REFERENCES `core_ad_network` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `unified_report_network_site_sub_publisher`
--

LOCK TABLES `unified_report_network_site_sub_publisher` WRITE;
/*!40000 ALTER TABLE `unified_report_network_site_sub_publisher` DISABLE KEYS */;
/*!40000 ALTER TABLE `unified_report_network_site_sub_publisher` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `unified_report_publisher`
--

DROP TABLE IF EXISTS `unified_report_publisher`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `unified_report_publisher` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `publisher_id` int(11) DEFAULT NULL,
  `date` date NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fill_rate` double DEFAULT NULL,
  `impressions` int(11) DEFAULT NULL,
  `total_opportunities` int(11) DEFAULT '0',
  `passbacks` int(11) DEFAULT NULL,
  `est_cpm` double DEFAULT NULL,
  `est_revenue` double DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `publisher_report_unique_key` (`publisher_id`,`date`),
  KEY `IDX_C3DED91B40C86FCE` (`publisher_id`),
  CONSTRAINT `FK_C3DED91B40C86FCE` FOREIGN KEY (`publisher_id`) REFERENCES `core_user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `unified_report_publisher`
--

LOCK TABLES `unified_report_publisher` WRITE;
/*!40000 ALTER TABLE `unified_report_publisher` DISABLE KEYS */;
/*!40000 ALTER TABLE `unified_report_publisher` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `unified_report_publisher_sub_publisher`
--

DROP TABLE IF EXISTS `unified_report_publisher_sub_publisher`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `unified_report_publisher_sub_publisher` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sub_publisher_id` int(11) DEFAULT NULL,
  `date` date NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fill_rate` double DEFAULT NULL,
  `impressions` int(11) DEFAULT NULL,
  `total_opportunities` int(11) DEFAULT '0',
  `passbacks` int(11) DEFAULT NULL,
  `est_cpm` double DEFAULT NULL,
  `est_revenue` double DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `publisher_sub_publisher_report_unique_key` (`sub_publisher_id`,`date`),
  KEY `IDX_AB8C605A579A28F8` (`sub_publisher_id`),
  CONSTRAINT `FK_AB8C605A579A28F8` FOREIGN KEY (`sub_publisher_id`) REFERENCES `core_user_sub_publisher` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `unified_report_publisher_sub_publisher`
--

LOCK TABLES `unified_report_publisher_sub_publisher` WRITE;
/*!40000 ALTER TABLE `unified_report_publisher_sub_publisher` DISABLE KEYS */;
/*!40000 ALTER TABLE `unified_report_publisher_sub_publisher` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `unified_report_publisher_sub_publisher_network`
--

DROP TABLE IF EXISTS `unified_report_publisher_sub_publisher_network`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `unified_report_publisher_sub_publisher_network` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ad_network_id` int(11) DEFAULT NULL,
  `sub_publisher_id` int(11) DEFAULT NULL,
  `date` date NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fill_rate` double DEFAULT NULL,
  `impressions` int(11) DEFAULT NULL,
  `total_opportunities` int(11) DEFAULT '0',
  `passbacks` int(11) DEFAULT NULL,
  `est_cpm` double DEFAULT NULL,
  `est_revenue` double DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `publisher_sub_publisher_network_report_unique_key` (`sub_publisher_id`,`ad_network_id`,`date`),
  KEY `IDX_3519BF0ECB9BD82B` (`ad_network_id`),
  KEY `IDX_3519BF0E579A28F8` (`sub_publisher_id`),
  CONSTRAINT `FK_3519BF0E579A28F8` FOREIGN KEY (`sub_publisher_id`) REFERENCES `core_user_sub_publisher` (`id`),
  CONSTRAINT `FK_3519BF0ECB9BD82B` FOREIGN KEY (`ad_network_id`) REFERENCES `core_ad_network` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `unified_report_publisher_sub_publisher_network`
--

LOCK TABLES `unified_report_publisher_sub_publisher_network` WRITE;
/*!40000 ALTER TABLE `unified_report_publisher_sub_publisher_network` DISABLE KEYS */;
/*!40000 ALTER TABLE `unified_report_publisher_sub_publisher_network` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `unified_report_sub_publisher_network_domain_ad_tag`
--

DROP TABLE IF EXISTS `unified_report_sub_publisher_network_domain_ad_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `unified_report_sub_publisher_network_domain_ad_tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ad_network_id` int(11) DEFAULT NULL,
  `sub_publisher_id` int(11) DEFAULT NULL,
  `date` date NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fill_rate` double DEFAULT NULL,
  `impressions` int(11) DEFAULT NULL,
  `total_opportunities` int(11) DEFAULT '0',
  `passbacks` int(11) DEFAULT NULL,
  `est_cpm` double DEFAULT NULL,
  `est_revenue` double DEFAULT NULL,
  `partner_tag_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `domain` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `network_ad_tag_report_unique_key` (`ad_network_id`,`domain`,`partner_tag_id`,`date`,`sub_publisher_id`),
  KEY `IDX_176B1FA2CB9BD82B` (`ad_network_id`),
  KEY `IDX_176B1FA2579A28F8` (`sub_publisher_id`),
  CONSTRAINT `FK_176B1FA2579A28F8` FOREIGN KEY (`sub_publisher_id`) REFERENCES `core_user_sub_publisher` (`id`),
  CONSTRAINT `FK_176B1FA2CB9BD82B` FOREIGN KEY (`ad_network_id`) REFERENCES `core_ad_network` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `unified_report_sub_publisher_network_domain_ad_tag`
--

LOCK TABLES `unified_report_sub_publisher_network_domain_ad_tag` WRITE;
/*!40000 ALTER TABLE `unified_report_sub_publisher_network_domain_ad_tag` DISABLE KEYS */;
/*!40000 ALTER TABLE `unified_report_sub_publisher_network_domain_ad_tag` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-07-20 17:39:20
