-- MySQL dump 10.13  Distrib 8.0.27, for Linux (x86_64)
--
-- Host: localhost    Database: pdb
-- ------------------------------------------------------
-- Server version	8.0.27-0ubuntu0.20.04.1

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
-- Table structure for table `author`
--

DROP TABLE IF EXISTS `author`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `author` (
  `idAuthor` int unsigned NOT NULL AUTO_INCREMENT,
  `author` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`idAuthor`),
  FULLTEXT KEY `textAuthor` (`author`)
) DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary view structure for view `authorEntries`
--

DROP TABLE IF EXISTS `authorEntries`;
/*!50001 DROP VIEW IF EXISTS `authorEntries`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `authorEntries` AS SELECT 
 1 AS `author`,
 1 AS `idCode`,
 1 AS `idExpType`,
 1 AS `idCompType`,
 1 AS `header`,
 1 AS `ascessionDate`,
 1 AS `compound`,
 1 AS `resolution`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `author_has_entry`
--

DROP TABLE IF EXISTS `author_has_entry`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `author_has_entry` (
  `idAuthor` int unsigned NOT NULL,
  `idCode` varchar(4) NOT NULL,
  PRIMARY KEY (`idAuthor`,`idCode`),
  KEY `Author_has_Entry_FKIndex1` (`idAuthor`),
  KEY `Author_has_Entry_FKIndex2` (`idCode`),
  CONSTRAINT `fk_author_has_entry_author1` FOREIGN KEY (`idAuthor`) REFERENCES `author` (`idAuthor`),
  CONSTRAINT `fk_author_has_entry_entry1` FOREIGN KEY (`idCode`) REFERENCES `entry` (`idCode`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary view structure for view `authors_view`
--

DROP TABLE IF EXISTS `authors_view`;
/*!50001 DROP VIEW IF EXISTS `authors_view`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `authors_view` AS SELECT 
 1 AS `idCode`,
 1 AS `idExpType`,
 1 AS `idCompType`,
 1 AS `header`,
 1 AS `ascessionDate`,
 1 AS `compound`,
 1 AS `resolution`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `authors_view2`
--

DROP TABLE IF EXISTS `authors_view2`;
/*!50001 DROP VIEW IF EXISTS `authors_view2`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `authors_view2` AS SELECT 
 1 AS `idCode`,
 1 AS `idExpType`,
 1 AS `idCompType`,
 1 AS `header`,
 1 AS `ascessionDate`,
 1 AS `compound`,
 1 AS `resolution`,
 1 AS `idAuthor`,
 1 AS `author`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `comptype`
--

DROP TABLE IF EXISTS `comptype`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `comptype` (
  `idCompType` int unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`idCompType`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `entry`
--

DROP TABLE IF EXISTS `entry`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `entry` (
  `idCode` varchar(4) NOT NULL,
  `idExpType` int unsigned DEFAULT NULL,
  `idCompType` int unsigned DEFAULT NULL,
  `header` varchar(255) DEFAULT NULL,
  `ascessionDate` varchar(20) DEFAULT NULL,
  `compound` varchar(255) DEFAULT NULL,
  `resolution` float DEFAULT NULL,
  PRIMARY KEY (`idCode`),
  KEY `Entry_FKIndex1` (`idCompType`),
  KEY `resolution` (`resolution`),
  KEY `fk_entry_expType1` (`idExpType`),
  FULLTEXT KEY `textheader` (`header`),
  FULLTEXT KEY `textcompound` (`compound`)
) DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `entry_has_source`
--

DROP TABLE IF EXISTS `entry_has_source`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `entry_has_source` (
  `idCode` varchar(4) NOT NULL,
  `idsource` int unsigned NOT NULL,
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  KEY `idCode` (`idCode`,`idsource`)
) ENGINE=InnoDB AUTO_INCREMENT=5678313 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `expClasse`
--

DROP TABLE IF EXISTS `expClasse`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `expClasse` (
  `idExpClasse` int unsigned NOT NULL AUTO_INCREMENT,
  `expClasse` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`idExpClasse`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `expType`
--

DROP TABLE IF EXISTS `expType`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `expType` (
  `idExpType` int unsigned NOT NULL AUTO_INCREMENT,
  `idExpClasse` int unsigned DEFAULT NULL,
  `ExpType` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`idExpType`),
  KEY `ExpType_FKIndex1` (`idExpClasse`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary view structure for view `minmaxres`
--

DROP TABLE IF EXISTS `minmaxres`;
/*!50001 DROP VIEW IF EXISTS `minmaxres`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `minmaxres` AS SELECT 
 1 AS `min(e.resolution)`,
 1 AS `max(e.resolution)`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `sequence`
--

DROP TABLE IF EXISTS `sequence`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sequence` (
  `idCode` varchar(4) NOT NULL,
  `chain` varchar(5) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `sequence` text,
  `header` text,
  PRIMARY KEY (`idCode`,`chain`),
  KEY `idCode` (`idCode`),
  FULLTEXT KEY `header` (`header`)
) DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `source`
--

DROP TABLE IF EXISTS `source`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `source` (
  `idSource` int unsigned NOT NULL AUTO_INCREMENT,
  `source` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`idSource`),
  FULLTEXT KEY `textSource` (`source`)
) DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary view structure for view `stat`
--

DROP TABLE IF EXISTS `stat`;
/*!50001 DROP VIEW IF EXISTS `stat`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `stat` AS SELECT 
 1 AS `COUNT(*)`,
 1 AS `type`*/;
SET character_set_client = @saved_cs_client;

