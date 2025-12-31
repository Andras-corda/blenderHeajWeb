-- MySQL dump 10.13  Distrib 8.0.34, for Win64 (x86_64)
--
-- Host: ko86t9azcob3a2f9.cbetxkdyhwsb.us-east-1.rds.amazonaws.com    Database: d5v584rj7yxlaguc
-- ------------------------------------------------------
-- Server version	8.0.40

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
SET @MYSQLDUMP_TEMP_LOG_BIN = @@SESSION.SQL_LOG_BIN;
SET @@SESSION.SQL_LOG_BIN= 0;

--
-- GTID state at the beginning of the backup 
--

SET @@GLOBAL.GTID_PURGED=/*!80000 '+'*/ '';

--
-- Table structure for table `playlist_videos`
--

DROP TABLE IF EXISTS `playlist_videos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `playlist_videos` (
  `videoId` int NOT NULL AUTO_INCREMENT,
  `playlistId` int NOT NULL,
  `videoUrl` varchar(255) NOT NULL,
  PRIMARY KEY (`videoId`),
  KEY `playlist_id` (`playlistId`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `playlist_videos`
--

LOCK TABLES `playlist_videos` WRITE;
/*!40000 ALTER TABLE `playlist_videos` DISABLE KEYS */;
INSERT INTO `playlist_videos` VALUES (1,1,'https://www.youtube.com/watch?v=dQw4w9WgXcQ'),(2,1,'https://www.youtube.com/watch?v=9bZkp7q19f0'),(3,1,'https://www.youtube.com/watch?v=kJQP7kiw5Fk'),(4,1,'https://www.youtube.com/watch?v=y6120QOlsfU'),(5,1,'https://www.youtube.com/watch?v=fJ9rUzIMcZQ'),(6,2,'https://www.youtube.com/watch?v=3JZ_D3ELwOQ'),(7,2,'https://www.youtube.com/watch?v=hTWKbfoikeg'),(8,2,'https://www.youtube.com/watch?v=OPf0YbXqDm0'),(9,2,'https://www.youtube.com/watch?v=2vjPBrBU-TM'),(10,2,'https://www.youtube.com/watch?v=djV11Xbc914'),(11,3,'https://www.youtube.com/watch?v=ZZ5LpwO-An4'),(12,3,'https://www.youtube.com/watch?v=RBumgq5yVrA'),(13,3,'https://www.youtube.com/watch?v=DyDfgMOUjCI'),(14,3,'https://www.youtube.com/watch?v=L_jWHffIx5E'),(15,3,'https://www.youtube.com/watch?v=o_1aF54DO60'),(16,4,'https://www.youtube.com/watch?v=6n3pFFPSlW4'),(17,4,'https://www.youtube.com/watch?v=LsoLEjrDogU'),(18,4,'https://www.youtube.com/watch?v=nfWlot6h_JM'),(19,4,'https://www.youtube.com/watch?v=TBfWKmRFTjM'),(20,4,'https://www.youtube.com/watch?v=HEXWRTEbj1I'),(21,5,'https://www.youtube.com/watch?v=YQHsXMglC9A'),(22,5,'https://www.youtube.com/watch?v=uelHwf8o7_U'),(23,5,'https://www.youtube.com/watch?v=jNQXAC9IVRw'),(24,5,'https://www.youtube.com/watch?v=xat1GVnl8-k'),(25,5,'https://www.youtube.com/watch?v=eh7lp9umG2I'),(26,6,'https://www.youtube.com/watch?v=wRRsXxE1KVY'),(27,6,'https://www.youtube.com/watch?v=DWcJFNfaw9c'),(28,6,'https://www.youtube.com/watch?v=fC7oUOUEEi4'),(29,6,'https://www.youtube.com/watch?v=UxxajLWwzqY'),(30,6,'https://www.youtube.com/watch?v=eVTXPUF4Oz4'),(31,7,'https://www.youtube.com/watch?v=lXMskKTw3Bc'),(32,7,'https://www.youtube.com/watch?v=8UVNT4wvIGY'),(33,7,'https://www.youtube.com/watch?v=4m1EFMoRFvY'),(34,7,'https://www.youtube.com/watch?v=qeMFqkcPYcg'),(35,7,'https://www.youtube.com/watch?v=ysz5S6PUM-U'),(36,8,'https://www.youtube.com/watch?v=EE-xtCF3T94'),(37,8,'https://www.youtube.com/watch?v=09R8_2nJtjg'),(38,8,'https://www.youtube.com/watch?v=Sv6dMFF_yts'),(39,8,'https://www.youtube.com/watch?v=pAgnJDJN4VA'),(40,8,'https://www.youtube.com/watch?v=Awf45u6zrP0'),(41,9,'https://www.youtube.com/watch?v=1w7OgIMMRc4'),(42,9,'https://www.youtube.com/watch?v=wJnBTPUQS5A'),(43,9,'https://www.youtube.com/watch?v=5pidokakU4I'),(44,9,'https://www.youtube.com/watch?v=BgfcToAjfdc'),(45,9,'https://www.youtube.com/watch?v=dDagv6SA8nw'),(46,10,'https://www.youtube.com/watch?v=G3e-cpL7ofc'),(47,10,'https://www.youtube.com/watch?v=VbfpW0pbvaU'),(48,10,'https://www.youtube.com/watch?v=CD-E-LDc384'),(49,10,'https://www.youtube.com/watch?v=AzlMcvCdhjo'),(50,10,'https://www.youtube.com/watch?v=Gs069dndIYk');
/*!40000 ALTER TABLE `playlist_videos` ENABLE KEYS */;
UNLOCK TABLES;
SET @@SESSION.SQL_LOG_BIN = @MYSQLDUMP_TEMP_LOG_BIN;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-11-07 15:36:02
