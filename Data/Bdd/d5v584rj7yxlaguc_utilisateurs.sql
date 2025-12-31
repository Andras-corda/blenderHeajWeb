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
-- Table structure for table `utilisateurs`
--

DROP TABLE IF EXISTS `utilisateurs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `utilisateurs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'user',
  `date_inscription` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `discord_id` varchar(64) NOT NULL,
  `discord_username` varchar(100) NOT NULL,
  `discord_discriminator` varchar(10) NOT NULL,
  `discord_avatar` varchar(255) NOT NULL,
  `discord_access_token` varchar(2000) NOT NULL,
  `discord_refresh_token` varchar(2000) NOT NULL,
  `discord_token_expires_at` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `discord_id` (`discord_id`),
  KEY `idx_discord_id` (`discord_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `utilisateurs`
--

LOCK TABLES `utilisateurs` WRITE;
/*!40000 ALTER TABLE `utilisateurs` DISABLE KEYS */;
INSERT INTO `utilisateurs` VALUES (1,'R3tr0___','admin','2025-10-24 08:28:56','411170239607472128','buildcraft_ak','0','966e1ab0fc414c70e30c99c5ce6f7ece','MTQzMTE3NzE2NzYwNTIwNzA3MA.GLfLsYPuqav4y1p9OwTRzZ9pmaynrh','7UhxtgSM0iEjMCEnys7lAPuBYHPvGn',1762789737),(2,'Hugobema','user','2025-10-24 09:04:24','546421974646128652','hugobema','0','97757e7f7be3f2aafd62051a648fe496','MTQzMTE3NzE2NzYwNTIwNzA3MA.2e6aMdKC2LfR4nu6gVoinZjiA7Jty1','3QeBLKmAQbZvlbtaijwLwrlLaN68jf',1761901464),(3,'Flo_','admin','2025-10-24 11:36:31','691647665905074226','fl0_gbx','0','f4a24dc6e92d475a4165947c8a4bd7ed','MTQzMTE3NzE2NzYwNTIwNzA3MA.jLsiyiKwnEjOeEbtF5fFTveGIxuQWj','YlrGavCIQxEtolxNy9qp63iBpVd6tG',1761910591);
/*!40000 ALTER TABLE `utilisateurs` ENABLE KEYS */;
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

-- Dump completed on 2025-11-07 15:36:05
