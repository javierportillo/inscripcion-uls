-- MySQL dump 10.15  Distrib 10.0.25-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: inscripcion_uls
-- ------------------------------------------------------
-- Server version	10.0.25-MariaDB-0ubuntu0.16.04.1

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
-- Table structure for table `carrera`
--

DROP TABLE IF EXISTS `carrera`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `carrera` (
  `codigo_carrera` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`codigo_carrera`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carrera`
--

LOCK TABLES `carrera` WRITE;
/*!40000 ALTER TABLE `carrera` DISABLE KEYS */;
INSERT INTO `carrera` VALUES ('LCC','Licenciatura en Ciencias de la Computación');
/*!40000 ALTER TABLE `carrera` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `carrera_alumno`
--

DROP TABLE IF EXISTS `carrera_alumno`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `carrera_alumno` (
  `fecha_ingreso` date NOT NULL,
  `carnet` varchar(12) COLLATE utf8mb4_unicode_ci NOT NULL,
  `codigo_carrera` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`fecha_ingreso`,`carnet`,`codigo_carrera`),
  KEY `carnet` (`carnet`),
  KEY `codigo_carrera` (`codigo_carrera`),
  CONSTRAINT `carrera_alumno_ibfk_1` FOREIGN KEY (`carnet`) REFERENCES `matricula` (`carnet`) ON UPDATE CASCADE,
  CONSTRAINT `carrera_alumno_ibfk_2` FOREIGN KEY (`codigo_carrera`) REFERENCES `carrera` (`codigo_carrera`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carrera_alumno`
--

LOCK TABLES `carrera_alumno` WRITE;
/*!40000 ALTER TABLE `carrera_alumno` DISABLE KEYS */;
INSERT INTO `carrera_alumno` VALUES ('2016-05-19','CH01133374','LCC'),('2016-05-19','HL01133112','LCC'),('2016-05-19','MP01133315','LCC'),('2016-05-19','VO01132924','LCC');
/*!40000 ALTER TABLE `carrera_alumno` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `grupo`
--

DROP TABLE IF EXISTS `grupo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `grupo` (
  `id_grupo` int(11) NOT NULL AUTO_INCREMENT,
  `ciclo` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dia` int(1) NOT NULL,
  `codigo_materia` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_horario` int(11) NOT NULL,
  PRIMARY KEY (`id_grupo`),
  UNIQUE KEY `ciclo` (`ciclo`,`dia`,`codigo_materia`,`id_horario`),
  KEY `codigo_materia` (`codigo_materia`),
  KEY `id_horario` (`id_horario`),
  CONSTRAINT `grupo_ibfk_1` FOREIGN KEY (`codigo_materia`) REFERENCES `pensum` (`codigo_materia`) ON UPDATE CASCADE,
  CONSTRAINT `grupo_ibfk_2` FOREIGN KEY (`id_horario`) REFERENCES `horarios` (`id_horario`)
) ENGINE=InnoDB AUTO_INCREMENT=75 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `grupo`
--

LOCK TABLES `grupo` WRITE;
/*!40000 ALTER TABLE `grupo` DISABLE KEYS */;
INSERT INTO `grupo` VALUES (65,'1-2015',3,'FIGE1',4),(64,'1-2015',3,'MA1',1),(66,'1-2015',6,'INTE1',2),(67,'1-2015',6,'TEAD1',4),(3,'1-2016',1,'ES1',1),(1,'1-2016',1,'LOCO1',1),(2,'1-2016',1,'MA1',4),(6,'1-2016',2,'ADPE1',2),(4,'1-2016',2,'FIGE1',1),(8,'1-2016',2,'LELA1',1),(7,'1-2016',2,'REOR1',4),(5,'1-2016',2,'TEAD1',2),(21,'1-2016',3,'AL2',3),(27,'1-2016',3,'ANSI1',2),(25,'1-2016',3,'AUSI1',1),(23,'1-2016',3,'BADA1',2),(13,'1-2016',3,'CO1',1),(15,'1-2016',3,'ES1',2),(22,'1-2016',3,'IN2',1),(9,'1-2016',3,'ININ1',3),(18,'1-2016',3,'INTE1',2),(14,'1-2016',3,'LELA1',4),(12,'1-2016',3,'LOCO1',2),(17,'1-2016',3,'ME1',1),(16,'1-2016',3,'METEIN1',3),(24,'1-2016',3,'PR3',3),(10,'1-2016',3,'PRGEEC1',1),(28,'1-2016',3,'PRGEEC1',4),(29,'1-2016',3,'RE1',3),(20,'1-2016',3,'REOR1',3),(11,'1-2016',3,'TEAD1',2),(33,'1-2016',4,'CO1',2),(32,'1-2016',4,'ININ1',1),(35,'1-2016',4,'INTE1',4),(36,'1-2016',4,'METEIN1',4),(38,'1-2016',5,'ININ1',4),(37,'1-2016',5,'MA1',1),(48,'1-2016',6,'AL2',2),(53,'1-2016',6,'AUSI1',3),(50,'1-2016',6,'BADA1',2),(41,'1-2016',6,'COPR1',3),(52,'1-2016',6,'DISI1',2),(40,'1-2016',6,'FOEVPR1',2),(49,'1-2016',6,'IN2',1),(42,'1-2016',6,'INTE1',2),(47,'1-2016',6,'LOCO1',3),(43,'1-2016',6,'MA1',2),(46,'1-2016',6,'PR3',3),(44,'1-2016',6,'PRGEEC1',1),(51,'1-2016',6,'RE1',1),(45,'1-2016',6,'REOR1',1),(54,'1-2016',7,'FIGE1',2),(56,'1-2016',7,'MA1',2),(55,'1-2016',7,'PRGEEC1',1),(59,'2-2014',3,'ININ1',1),(60,'2-2014',3,'PRGEEC1',2),(61,'2-2014',6,'EC1',2),(62,'2-2014',6,'LOCO1',3),(68,'2-2015',3,'MA2',2),(69,'2-2015',3,'PROROB1',3),(70,'2-2015',6,'AL1',1),(71,'2-2015',6,'INSOLI1',2),(57,'2-2016',1,'MA1',2),(63,'3-2014',6,'REOR1',2),(73,'3-2015',3,'CO1',2),(72,'3-2015',3,'ETPR1',1),(74,'3-2015',6,'LELA1',2);
/*!40000 ALTER TABLE `grupo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `horarios`
--

DROP TABLE IF EXISTS `horarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `horarios` (
  `id_horario` int(11) NOT NULL AUTO_INCREMENT,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  PRIMARY KEY (`id_horario`),
  UNIQUE KEY `hora_inicio` (`hora_inicio`,`hora_fin`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `horarios`
--

LOCK TABLES `horarios` WRITE;
/*!40000 ALTER TABLE `horarios` DISABLE KEYS */;
INSERT INTO `horarios` VALUES (1,'07:00:00','09:30:00'),(2,'09:40:00','12:10:00'),(3,'13:00:00','15:50:00'),(4,'16:00:00','19:20:00');
/*!40000 ALTER TABLE `horarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inscripcion`
--

DROP TABLE IF EXISTS `inscripcion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inscripcion` (
  `id_inscripcion` int(11) NOT NULL AUTO_INCREMENT,
  `estado` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `carnet` varchar(12) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_grupo` int(11) NOT NULL,
  `id_notas` int(11) NOT NULL,
  PRIMARY KEY (`id_inscripcion`),
  UNIQUE KEY `carnet` (`carnet`,`id_grupo`,`id_notas`),
  KEY `id_grupo` (`id_grupo`),
  KEY `id_notas` (`id_notas`),
  CONSTRAINT `inscripcion_ibfk_1` FOREIGN KEY (`carnet`) REFERENCES `matricula` (`carnet`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `inscripcion_ibfk_2` FOREIGN KEY (`id_grupo`) REFERENCES `grupo` (`id_grupo`),
  CONSTRAINT `inscripcion_ibfk_3` FOREIGN KEY (`id_notas`) REFERENCES `notas` (`id_notas`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inscripcion`
--

LOCK TABLES `inscripcion` WRITE;
/*!40000 ALTER TABLE `inscripcion` DISABLE KEYS */;
INSERT INTO `inscripcion` VALUES (1,'aprobado','MP01133315',59,1),(2,'aprobado','MP01133315',60,2),(3,'aprobado','MP01133315',61,3),(4,'aprobado','MP01133315',62,4),(5,'aprobado','MP01133315',63,5),(6,'aprobado','MP01133315',64,6),(7,'aprobado','MP01133315',65,7),(8,'aprobado','MP01133315',66,8),(9,'aprobado','MP01133315',67,9),(10,'aprobado','MP01133315',68,10),(11,'aprobado','MP01133315',69,11),(12,'aprobado','MP01133315',70,12),(13,'aprobado','MP01133315',71,13),(14,'aprobado','MP01133315',72,14),(15,'aprobado','MP01133315',73,15),(18,'aprobado','MP01133315',74,20),(28,'','MP01133315',17,30),(29,'','MP01133315',15,31),(30,'','MP01133315',21,32),(31,'','MP01133315',50,33);
/*!40000 ALTER TABLE `inscripcion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `matricula`
--

DROP TABLE IF EXISTS `matricula`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `matricula` (
  `carnet` varchar(12) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `anio_pensum` year(4) NOT NULL,
  PRIMARY KEY (`carnet`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `matricula`
--

LOCK TABLES `matricula` WRITE;
/*!40000 ALTER TABLE `matricula` DISABLE KEYS */;
INSERT INTO `matricula` VALUES ('CH01133374','Cristian Antonio Cerón Henriquez','activo','$2y$10$3OKsRNoOFhAenFRarnTEDunj8LjFlq5hiCQi9O.RQUeiCwAMhfdJm',2014),('HL01133112','Xavier Edenilson Hernández Lovos','activo','$2y$10$av2eaIlo4JdfDMgzJX.33eXWQ9EttuVnrEMhaiRLPopEuDrfBbPd.',2014),('MP01133315','Javier Marquez Portillo','activo','$2y$10$109766PthoYfOgOD5qWY6eD/35.4aQa1GuYSQ/baHskVNblGxVpYu',2014),('VO01132924','Denis Alexi Valencia Olmedo','activo','$2y$10$MsFMcexcFgB3a7w3oAlkhOW28SDb4NP36Df0XUfOWmVnwYUIzthQm',2014);
/*!40000 ALTER TABLE `matricula` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notas`
--

DROP TABLE IF EXISTS `notas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notas` (
  `id_notas` int(11) NOT NULL AUTO_INCREMENT,
  `nota_final` decimal(4,2) DEFAULT NULL,
  PRIMARY KEY (`id_notas`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notas`
--

LOCK TABLES `notas` WRITE;
/*!40000 ALTER TABLE `notas` DISABLE KEYS */;
INSERT INTO `notas` VALUES (1,10.00),(2,7.24),(3,8.12),(4,10.00),(5,9.88),(6,10.00),(7,10.00),(8,10.00),(9,10.00),(10,10.00),(11,10.00),(12,10.00),(13,10.00),(14,10.00),(15,10.00),(20,10.00),(30,0.00),(31,0.00),(32,0.00),(33,0.00);
/*!40000 ALTER TABLE `notas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pagos`
--

DROP TABLE IF EXISTS `pagos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pagos` (
  `id_pago` int(11) NOT NULL AUTO_INCREMENT,
  `referencia_pago` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cantidad_pago` decimal(25,2) NOT NULL,
  `fecha_pago` date NOT NULL,
  `ciclo` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `carnet` varchar(12) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id_pago`),
  KEY `carnet` (`carnet`),
  CONSTRAINT `pagos_ibfk_1` FOREIGN KEY (`carnet`) REFERENCES `matricula` (`carnet`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pagos`
--

LOCK TABLES `pagos` WRITE;
/*!40000 ALTER TABLE `pagos` DISABLE KEYS */;
INSERT INTO `pagos` VALUES (1,'071218011333151201601',40.00,'2016-05-19','1-2016','MP01133315'),(2,'071218011333151201603',40.00,'2016-05-19','1-2016','MP01133315'),(3,'071218011333151201613',40.00,'2016-05-19','2-2016','MP01133315'),(4,'071218011333151201603',40.00,'2016-05-19','1-2016','HL01133112'),(5,'071218011333151201600',40.00,'2016-05-22','1-2016','CH01133374'),(6,'071218011333151201600',40.00,'2016-05-22','1-2016','MP01133315');
/*!40000 ALTER TABLE `pagos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pensum`
--

DROP TABLE IF EXISTS `pensum`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pensum` (
  `codigo_materia` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `codigo_referencia` int(1) NOT NULL,
  `codigo_prerrequisito` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `anio_pensum` year(4) NOT NULL,
  `codigo_carrera` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`codigo_materia`),
  UNIQUE KEY `codigo_materia` (`codigo_materia`,`anio_pensum`),
  KEY `codigo_carrera` (`codigo_carrera`),
  CONSTRAINT `pensum_ibfk_1` FOREIGN KEY (`codigo_carrera`) REFERENCES `carrera` (`codigo_carrera`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pensum`
--

LOCK TABLES `pensum` WRITE;
/*!40000 ALTER TABLE `pensum` DISABLE KEYS */;
INSERT INTO `pensum` VALUES ('ADPE1','Administración de Personal',33,'21',2014,'LCC'),('ADSIIN1','Administración de Sistemas Informáticos',39,'37',2014,'LCC'),('AL1','Algoritmo I',7,'2',2014,'LCC'),('AL2','Algoritmo II',14,'7',2014,'LCC'),('ANSI1','Análisis de Sistemas',37,'29',2014,'LCC'),('AUSI1','Auditoria de Sistemas',35,'29',2014,'LCC'),('BADA1','Base de Datos I',23,'9',2014,'LCC'),('BADA2','Bases de Datos II',26,'23',2014,'LCC'),('CO1','Contabilidad',17,'3',2014,'LCC'),('COPR1','Consultoría Profesional',38,'31',2014,'LCC'),('DISI1','Diseño de Sistemas',41,'37',2014,'LCC'),('EC1','Ecología',8,'0',2014,'LCC'),('EMCOSO1','Empresas y Cooperativas de Software',40,'37',2014,'LCC'),('ES1','Estadística',11,'6',2014,'LCC'),('ETPR1','Ética Profesional',16,'4',2014,'LCC'),('FIGE1','Filosofía General',4,'0',2014,'LCC'),('FOEVPR1','Formulación y Evaluación de Proyectos',30,'25',2014,'LCC'),('GEMIRI1','Gestión y Mitigación de Riesgos',27,'8',2014,'LCC'),('IN1','Internet I',18,'14',2014,'LCC'),('IN2','Internet II',22,'18',2014,'LCC'),('INHA1','Introducción al Hardware',20,'10',2014,'LCC'),('ININ1','Introducción a la Informática',5,'0',2014,'LCC'),('INSOLI1','Introducción al Software Libre',10,'5',2014,'LCC'),('INTE1','Inglés Técnico',15,'0',2014,'LCC'),('LELA1','Legislación Laboral',31,'17',2014,'LCC'),('LOCO1','Lógica Computacional',2,'0',2014,'LCC'),('MA1','Matemática I',1,'0',2014,'LCC'),('MA2','Matemática II',6,'1',2014,'LCC'),('ME1','Mercadotecnia',21,'12',2014,'LCC'),('METEIN1','Métodos y Técnicas de Investigación',25,'0',2014,'LCC'),('NUTEPR1','Nuevas Tendencias de Programación',36,'29',2014,'LCC'),('PR1','Programación I',19,'14',2014,'LCC'),('PR2','Programación II',24,'19',2014,'LCC'),('PR3','Programación III',29,'24',2014,'LCC'),('PRGEEC1','Principios Generales de Economía',3,'0',2014,'LCC'),('PROROB1','Programación Orientada a Objetos',9,'2',2014,'LCC'),('PRPRIN1','Práctica Profesional de Informática',43,'39',2014,'LCC'),('PRSOLI1','Proyectos de Software Libre',42,'41',2014,'LCC'),('RE1','Redes I',28,'22',2014,'LCC'),('RE2','Redes II',34,'28',2014,'LCC'),('REOR1','Redacción y Ortografía',13,'0',2014,'LCC'),('SEGR1','Seminario de Graduación',44,'90%',2014,'LCC'),('SIOPRE1','Sistemas Operativos de Redes',32,'28',2014,'LCC'),('TEAD1','Teoría Administrativa',12,'3',2014,'LCC');
/*!40000 ALTER TABLE `pensum` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-06-22 17:44:26
