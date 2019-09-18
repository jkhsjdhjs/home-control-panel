-- MariaDB dump 10.17  Distrib 10.4.7-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: hcp
-- ------------------------------------------------------
-- Server version	10.4.7-MariaDB

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
-- Table structure for table `change_email`
--

DROP TABLE IF EXISTS `change_email`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `change_email` (
  `userid` int(11) NOT NULL,
  `email` text NOT NULL,
  `challenge_id` text NOT NULL,
  `valid` tinyint(4) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `changed_pw`
--

DROP TABLE IF EXISTS `changed_pw`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `changed_pw` (
  `userid` int(11) NOT NULL,
  `old_pw` text NOT NULL,
  `challenge_id` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `general`
--

DROP TABLE IF EXISTS `general`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `general` (
  `website_charset` text NOT NULL,
  `website_title` text NOT NULL,
  `name_socket1` text NOT NULL,
  `name_socket2` text NOT NULL,
  `name_socket3` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pending_users`
--

DROP TABLE IF EXISTS `pending_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pending_users` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `validationID` text NOT NULL,
  `email` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `admin` tinyint(1) NOT NULL,
  `socket1` tinyint(1) NOT NULL,
  `socket2` tinyint(1) NOT NULL,
  `socket3` tinyint(1) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=147 DEFAULT CHARSET=utf8 COMMENT='Beinhaltet neu hinzugefügte Nutzer, die sich noch nicht registriert haben.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permissions` (
  `userid` int(11) NOT NULL,
  `admin` tinyint(1) NOT NULL COMMENT 'Ist der User Admin?',
  `socket1` tinyint(1) NOT NULL COMMENT 'Darf der User Steckdose 1 an- und ausschalten?',
  `socket2` tinyint(1) NOT NULL COMMENT 'Darf der User Steckdose 2 an- und ausschalten?',
  `socket3` tinyint(1) NOT NULL COMMENT 'Darf der User Steckdose 3 an- und ausschalten?',
  PRIMARY KEY (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Beinhaltet die Rechte der einzelnen Nutzer.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reset_pw`
--

DROP TABLE IF EXISTS `reset_pw`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reset_pw` (
  `userid` int(11) NOT NULL,
  `challenge_id` text NOT NULL COMMENT 'Beinhaltet die Challenge ID.',
  `valid` tinyint(1) NOT NULL COMMENT 'Ist die Challenge-ID noch gültig?',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Wann wurde der PW-Reset ausgelöst?',
  PRIMARY KEY (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Beinhaltet Informationen der "Passwort vergessen" Funktion.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `userid` int(11) NOT NULL AUTO_INCREMENT,
  `username` text NOT NULL COMMENT 'Beinhaltet die Nutzernamen.',
  `password` text NOT NULL COMMENT 'Beinhaltet die zugehörigen Passwörter in md5 gehasht.',
  `email` text NOT NULL COMMENT 'Beinhaltet die E-Mail Adressen.',
  `NumberOfLogins` int(11) NOT NULL COMMENT 'Beinhaltet die Anzahl der bisherigen Logins.',
  `LastLogin` datetime NOT NULL COMMENT 'Beinhaltet den Zeitpunkt des letzten Logins.',
  `LastLogin2` datetime NOT NULL COMMENT 'Beinhaltet den Zeitpunkt des vorletzten Logins.',
  `AccountCreation` datetime NOT NULL COMMENT 'Beinhaltet den Zeitpunkt der Erstellung des Accounts.',
  PRIMARY KEY (`userid`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='Beinhaltet die Lister aller Accounts mit Zusatzinfos.';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-09-18 23:26:59
