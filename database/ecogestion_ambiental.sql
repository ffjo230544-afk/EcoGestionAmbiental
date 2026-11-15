CREATE DATABASE IF NOT EXISTS `ecogestion_ambiental` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `ecogestion_ambiental`;

-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: 127.0.0.1    Database: ecogestion_ambiental
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

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
-- Table structure for table `actividades_gamificacion`
--

DROP TABLE IF EXISTS `actividades_gamificacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `actividades_gamificacion` (
  `id_actividad` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(120) NOT NULL,
  `tipo_actividad` enum('juego_reciclaje','trivia','reto_reutilizacion','memorama','otro') NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `nivel` enum('basico','intermedio','avanzado') NOT NULL DEFAULT 'basico',
  `puntos_maximos` int(10) unsigned NOT NULL DEFAULT 0,
  `instrucciones` text DEFAULT NULL,
  `estado` enum('activa','inactiva') NOT NULL DEFAULT 'activa',
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_actividad`),
  KEY `idx_actividades_tipo` (`tipo_actividad`),
  KEY `idx_actividades_estado` (`estado`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `actividades_gamificacion`
--

LOCK TABLES `actividades_gamificacion` WRITE;
/*!40000 ALTER TABLE `actividades_gamificacion` DISABLE KEYS */;
INSERT INTO `actividades_gamificacion` VALUES (1,'Clasifica los residuos','juego_reciclaje','Arrastra cada residuo al contenedor correcto antes de terminar.','intermedio',100,'Aprende a separar residuos reciclables, orgánicos, especiales y peligrosos.','activa','2026-08-25 11:37:00'),(2,'Trivia ambiental','trivia','Responde preguntas y descubre datos útiles para cuidar el ambiente.','basico',100,'Refuerza conocimientos sobre agua, energía, reciclaje y consumo responsable.','activa','2026-08-25 11:37:00'),(3,'Reto de reutilización','reto_reutilizacion','Actividad para aprender nuevas formas de reutilizar materiales.','intermedio',150,'Completa el reto propuesto y registra tu resultado.','activa','2026-08-25 11:37:00'),(4,'Memorama ecológico','memorama','Encuentra parejas entre residuos y su destino correcto.','intermedio',100,'Relaciona cada material con su forma adecuada de manejo.','activa','2026-09-15 17:26:36'),(5,'Misión: limpia el parque','otro','Recoge los residuos y recupera un espacio natural.','intermedio',100,'Identifica residuos mientras observas el impacto de mantener limpios los espacios públicos.','activa','2026-09-15 17:26:36'),(6,'La ruta del reciclaje','otro','Juego para ordenar las etapas de recuperación del plástico, vidrio y papel.','intermedio',100,'Ordena cada proceso desde la separación hasta la fabricación de un producto nuevo.','activa','2026-09-15 19:29:44'),(7,'Decisiones ecológicas en casa','reto_reutilizacion','Toma decisiones cotidianas y descubre su impacto ambiental.','avanzado',100,'Practica hábitos responsables relacionados con agua, energía y consumo.','activa','2026-09-15 19:29:44');
/*!40000 ALTER TABLE `actividades_gamificacion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `archivos_sistema`
--

DROP TABLE IF EXISTS `archivos_sistema`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `archivos_sistema` (
  `id_archivo` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_usuario` int(10) unsigned NOT NULL,
  `tipo_archivo` enum('respaldo_bd','restauracion_bd','reporte_pdf') NOT NULL,
  `modulo_origen` varchar(80) NOT NULL,
  `nombre_archivo` varchar(180) NOT NULL,
  `ruta_archivo` varchar(255) DEFAULT NULL,
  `extension` varchar(10) NOT NULL,
  `tamanio_bytes` bigint(20) unsigned DEFAULT NULL,
  `tipo_reporte` enum('citas','cotizaciones','usuarios','centros_acopio','encuestas','actividades','no_aplica') NOT NULL DEFAULT 'no_aplica',
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `estado` enum('generado','descargado','restaurado','error','eliminado') NOT NULL DEFAULT 'generado',
  `observaciones` varchar(300) DEFAULT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_archivo`),
  KEY `idx_archivos_usuario` (`id_usuario`),
  KEY `idx_archivos_tipo` (`tipo_archivo`),
  KEY `idx_archivos_reporte` (`tipo_reporte`),
  KEY `idx_archivos_fecha` (`fecha_creacion`),
  KEY `idx_archivos_estado` (`estado`),
  CONSTRAINT `fk_archivos_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON UPDATE CASCADE,
  CONSTRAINT `CONSTRAINT_1` CHECK (`extension` in ('sql','pdf')),
  CONSTRAINT `CONSTRAINT_2` CHECK (`fecha_inicio` is null or `fecha_fin` is null or `fecha_inicio` <= `fecha_fin`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `archivos_sistema`
--

LOCK TABLES `archivos_sistema` WRITE;
/*!40000 ALTER TABLE `archivos_sistema` DISABLE KEYS */;
INSERT INTO `archivos_sistema` VALUES (1,5,'reporte_pdf','reportes','reporte_usuarios_20260825_125022_914d30.pdf','storage/reports/reporte_usuarios_20260825_125022_914d30.pdf','pdf',3829,'usuarios','2026-08-01','2026-08-25','descargado','5 registros incluidos','2026-08-25 12:50:22');
/*!40000 ALTER TABLE `archivos_sistema` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categorias_residuos`
--

DROP TABLE IF EXISTS `categorias_residuos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categorias_residuos` (
  `id_categoria` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(80) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `tipo_residuo` enum('reciclable','reutilizable','peligroso','especial','organico','otro') NOT NULL,
  `icono` varchar(80) DEFAULT NULL,
  `color_hex` char(7) DEFAULT NULL,
  `disponible_cotizacion` tinyint(1) NOT NULL DEFAULT 1,
  `visible_base_conocimiento` tinyint(1) NOT NULL DEFAULT 1,
  `estado` enum('activo','inactivo') NOT NULL DEFAULT 'activo',
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_categoria`),
  UNIQUE KEY `nombre` (`nombre`),
  KEY `idx_categorias_tipo` (`tipo_residuo`),
  KEY `idx_categorias_estado` (`estado`),
  CONSTRAINT `CONSTRAINT_1` CHECK (`color_hex` is null or `color_hex` regexp '^#[0-9A-Fa-f]{6}$')
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categorias_residuos`
--

LOCK TABLES `categorias_residuos` WRITE;
/*!40000 ALTER TABLE `categorias_residuos` DISABLE KEYS */;
INSERT INTO `categorias_residuos` VALUES (1,'Vidrio','Residuos de botellas, frascos y envases de vidrio.','reciclable','bi-cup-straw','#16a34a',1,1,'activo','2026-08-25 11:37:00'),(2,'Cartón','Material de cajas, empaques y papel grueso.','reciclable','bi-box-seam','#A16207',1,1,'activo','2026-08-25 11:37:00'),(3,'Plástico','Botellas, envases y empaques plásticos reciclables.','reciclable','bi-droplet-half','#0284c7',1,1,'activo','2026-08-25 11:37:00'),(4,'Metal','Latas, aluminio y otros metales reciclables.','reciclable','bi-nut','#64748b',1,1,'activo','2026-08-25 11:37:00'),(5,'Electrónicos','Equipos electrónicos, cables, celulares y componentes.','especial','bi-cpu','#7c3aed',1,1,'activo','2026-08-25 11:37:00'),(6,'Residuos tóxicos','Materiales peligrosos que requieren manejo especial.','peligroso','bi-exclamation-triangle','#dc2626',0,1,'activo','2026-08-25 11:37:00'),(7,'Envases multicapa','Envases fabricados con capas de cartón, plástico y aluminio, como los utilizados para leche, jugos, sopas y otros alimentos.','reciclable','bi-recycle','#BB9F3A',1,1,'activo','2026-11-04 00:29:51'),(8,'Papel','Periódicos, revistas, hojas, cuadernos y documentos sin plastificar que pueden procesarse para fabricar nuevos productos de papel.','reciclable','bi-recycle','#23B4C7',1,1,'activo','2026-11-04 00:36:34');
/*!40000 ALTER TABLE `categorias_residuos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `citas`
--

DROP TABLE IF EXISTS `citas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `citas` (
  `id_cita` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_usuario` int(10) unsigned NOT NULL,
  `id_punto` int(10) unsigned NOT NULL,
  `fecha_cita` date NOT NULL,
  `hora_cita` time NOT NULL,
  `motivo` enum('entrega_reciclables','entrega_electronicos','consulta_residuos_toxicos','informacion_servicios','otro') NOT NULL,
  `observaciones` varchar(300) DEFAULT NULL,
  `estado` enum('pendiente','confirmada','atendida','cancelada') NOT NULL DEFAULT 'pendiente',
  `fecha_registro` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_cita`),
  UNIQUE KEY `uk_cita_punto_fecha_hora` (`id_punto`,`fecha_cita`,`hora_cita`),
  KEY `idx_citas_usuario` (`id_usuario`),
  KEY `idx_citas_punto` (`id_punto`),
  KEY `idx_citas_fecha` (`fecha_cita`),
  KEY `idx_citas_estado` (`estado`),
  CONSTRAINT `fk_citas_punto` FOREIGN KEY (`id_punto`) REFERENCES `puntos_atencion` (`id_punto`) ON UPDATE CASCADE,
  CONSTRAINT `fk_citas_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `citas`
--

LOCK TABLES `citas` WRITE;
/*!40000 ALTER TABLE `citas` DISABLE KEYS */;
INSERT INTO `citas` VALUES (1,3,1,'2026-08-26','10:00:00','entrega_reciclables','Cita demostrativa para conocer el funcionamiento del sistema.','atendida','2026-08-25 11:37:00','2026-11-06 14:10:53'),(2,6,3,'2026-10-30','12:00:00','informacion_servicios','Quiero saber que mas servicios tienen','atendida','2026-10-02 19:48:44','2026-11-06 14:09:56'),(3,6,3,'2026-10-31','09:30:00','entrega_reciclables',NULL,'atendida','2026-10-09 17:28:57','2026-11-06 14:10:36'),(4,7,3,'2026-11-19','15:00:00','informacion_servicios',NULL,'confirmada','2026-10-24 09:54:36','2026-10-24 09:58:34'),(5,6,4,'2026-11-06','09:00:00','entrega_reciclables',NULL,'confirmada','2026-11-04 22:28:40','2026-11-04 22:29:46');
/*!40000 ALTER TABLE `citas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cotizaciones`
--

DROP TABLE IF EXISTS `cotizaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cotizaciones` (
  `id_cotizacion` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_usuario` int(10) unsigned NOT NULL,
  `id_categoria` int(10) unsigned NOT NULL,
  `id_punto` int(10) unsigned DEFAULT NULL,
  `peso_kg` decimal(10,2) DEFAULT NULL,
  `cantidad_piezas` int(10) unsigned DEFAULT NULL,
  `precio_unitario` decimal(10,2) NOT NULL DEFAULT 0.00,
  `monto_estimado` decimal(10,2) NOT NULL,
  `observaciones` varchar(300) DEFAULT NULL,
  `estado` enum('pendiente','revisada','aprobada','rechazada','cancelada') NOT NULL DEFAULT 'pendiente',
  `fecha_cotizacion` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_cotizacion`),
  KEY `idx_cotizaciones_usuario` (`id_usuario`),
  KEY `idx_cotizaciones_categoria` (`id_categoria`),
  KEY `idx_cotizaciones_punto` (`id_punto`),
  KEY `idx_cotizaciones_estado` (`estado`),
  KEY `idx_cotizaciones_fecha` (`fecha_cotizacion`),
  CONSTRAINT `fk_cotizaciones_categoria` FOREIGN KEY (`id_categoria`) REFERENCES `categorias_residuos` (`id_categoria`) ON UPDATE CASCADE,
  CONSTRAINT `fk_cotizaciones_punto` FOREIGN KEY (`id_punto`) REFERENCES `puntos_atencion` (`id_punto`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_cotizaciones_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON UPDATE CASCADE,
  CONSTRAINT `CONSTRAINT_1` CHECK (`peso_kg` is null or `peso_kg` >= 0),
  CONSTRAINT `CONSTRAINT_2` CHECK (`cantidad_piezas` is null or `cantidad_piezas` >= 0),
  CONSTRAINT `CONSTRAINT_3` CHECK (`precio_unitario` >= 0),
  CONSTRAINT `CONSTRAINT_4` CHECK (`monto_estimado` >= 0)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cotizaciones`
--

LOCK TABLES `cotizaciones` WRITE;
/*!40000 ALTER TABLE `cotizaciones` DISABLE KEYS */;
INSERT INTO `cotizaciones` VALUES (1,3,3,2,5.50,NULL,6.50,35.75,'Registro demostrativo de botellas PET limpias.','aprobada','2026-08-25 11:37:00','2026-10-01 09:30:03'),(2,6,2,2,10.00,NULL,3.80,38.00,NULL,'rechazada','2026-09-10 21:22:11','2026-09-21 10:58:44'),(3,6,4,2,50.00,NULL,13.00,650.00,NULL,'cancelada','2026-10-01 09:31:37','2026-11-05 06:07:59'),(4,7,3,2,200.00,NULL,6.50,1300.00,'Plastico en buen estado','aprobada','2026-10-24 18:34:23','2026-10-24 18:36:49'),(5,7,5,2,12.00,NULL,8.75,105.00,NULL,'aprobada','2026-10-24 18:54:23','2026-10-24 18:55:43'),(6,6,8,4,50.00,NULL,10.00,500.00,'Papel en buen estado','aprobada','2026-11-05 06:06:04','2026-11-05 06:07:16'),(7,6,2,4,50.00,NULL,5.00,250.00,NULL,'aprobada','2026-11-06 20:22:27','2026-11-06 20:23:58'),(8,6,2,4,70.00,NULL,5.00,350.00,NULL,'revisada','2026-11-06 20:33:26','2026-11-06 20:34:27'),(9,7,8,4,50.00,NULL,10.00,500.00,NULL,'aprobada','2026-11-06 20:36:15','2026-11-06 20:39:56');
/*!40000 ALTER TABLE `cotizaciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `detalle_respuestas_encuesta`
--

DROP TABLE IF EXISTS `detalle_respuestas_encuesta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `detalle_respuestas_encuesta` (
  `id_detalle` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_respuesta` bigint(20) unsigned NOT NULL,
  `id_pregunta` int(10) unsigned NOT NULL,
  `id_opcion` int(10) unsigned DEFAULT NULL,
  `respuesta_texto` varchar(500) DEFAULT NULL,
  `respuesta_valor` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`id_detalle`),
  KEY `fk_detalle_opcion` (`id_opcion`),
  KEY `idx_detalle_respuesta` (`id_respuesta`),
  KEY `idx_detalle_pregunta` (`id_pregunta`),
  CONSTRAINT `fk_detalle_opcion` FOREIGN KEY (`id_opcion`) REFERENCES `opciones_pregunta` (`id_opcion`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_detalle_pregunta` FOREIGN KEY (`id_pregunta`) REFERENCES `preguntas_encuesta` (`id_pregunta`) ON UPDATE CASCADE,
  CONSTRAINT `fk_detalle_respuesta` FOREIGN KEY (`id_respuesta`) REFERENCES `respuestas_encuesta` (`id_respuesta`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detalle_respuestas_encuesta`
--

LOCK TABLES `detalle_respuestas_encuesta` WRITE;
/*!40000 ALTER TABLE `detalle_respuestas_encuesta` DISABLE KEYS */;
INSERT INTO `detalle_respuestas_encuesta` VALUES (1,1,1,2,NULL,NULL),(2,2,2,NULL,'hola',NULL),(3,3,1,3,NULL,NULL),(4,4,3,7,NULL,NULL),(5,4,4,10,NULL,NULL),(6,4,5,NULL,NULL,1),(7,4,6,18,NULL,NULL),(8,4,7,23,NULL,NULL);
/*!40000 ALTER TABLE `detalle_respuestas_encuesta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `encuestas`
--

DROP TABLE IF EXISTS `encuestas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `encuestas` (
  `id_encuesta` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_creador` int(10) unsigned NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `objetivo` varchar(255) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_cierre` date DEFAULT NULL,
  `estado` enum('borrador','publicada','cerrada','inactiva') NOT NULL DEFAULT 'borrador',
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_encuesta`),
  KEY `fk_encuestas_creador` (`id_creador`),
  KEY `idx_encuestas_estado` (`estado`),
  KEY `idx_encuestas_fechas` (`fecha_inicio`,`fecha_cierre`),
  CONSTRAINT `fk_encuestas_creador` FOREIGN KEY (`id_creador`) REFERENCES `usuarios` (`id_usuario`) ON UPDATE CASCADE,
  CONSTRAINT `CONSTRAINT_1` CHECK (`fecha_inicio` is null or `fecha_cierre` is null or `fecha_inicio` <= `fecha_cierre`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `encuestas`
--

LOCK TABLES `encuestas` WRITE;
/*!40000 ALTER TABLE `encuestas` DISABLE KEYS */;
INSERT INTO `encuestas` VALUES (1,1,'Hábitos de reciclaje en Jiutepec','Conocer cómo separa sus residuos la comunidad.','Encuesta demostrativa para preparar el módulo de participación ciudadana.','2026-08-25','2026-11-25','publicada','2026-08-25 11:37:00'),(2,4,'Encuesta de habitos y conciencia ambiental','Conocer las practicas y percepciones de la comunidad sobre el cuidado del medio ambiente para identificar oportunidades de mejora','Esta encuesta busca comprender sus habitos diarios y su perspectiva del medio ambiente, sus respuestas son confidenciales y nos ayudaran a mejorar nuestras acciones comunitarias','2026-09-03','2026-12-31','publicada','2026-09-03 12:17:28'),(3,4,'Hábitos de separación y reciclaje de residuos','Conocer los hábitos de la ciudadanía relacionados con la separación, reutilización y entrega de residuos reciclables.','Esta encuesta tiene como propósito conocer cómo separan y manejan sus residuos los usuarios de EcoGestión Ambiental. Las respuestas permitirán identificar áreas de mejora y proponer nuevas acciones, campañas y servicios ambientales. La información será utilizada únicamente con fines de evaluación y mejora del sistema.','2026-11-04','2026-12-31','publicada','2026-11-04 08:00:19');
/*!40000 ALTER TABLE `encuestas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `horarios_atencion`
--

DROP TABLE IF EXISTS `horarios_atencion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `horarios_atencion` (
  `id_horario` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_punto` int(10) unsigned NOT NULL,
  `dia_semana` enum('lunes','martes','miercoles','jueves','viernes','sabado','domingo') NOT NULL,
  `hora_apertura` time DEFAULT NULL,
  `hora_cierre` time DEFAULT NULL,
  `cerrado` tinyint(1) NOT NULL DEFAULT 0,
  `observaciones` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`id_horario`),
  UNIQUE KEY `uk_horario_punto_dia` (`id_punto`,`dia_semana`),
  CONSTRAINT `fk_horarios_punto` FOREIGN KEY (`id_punto`) REFERENCES `puntos_atencion` (`id_punto`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `CONSTRAINT_1` CHECK (`cerrado` = 1 or `hora_apertura` < `hora_cierre`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `horarios_atencion`
--

LOCK TABLES `horarios_atencion` WRITE;
/*!40000 ALTER TABLE `horarios_atencion` DISABLE KEYS */;
INSERT INTO `horarios_atencion` VALUES (1,1,'lunes','09:00:00','17:00:00',0,'Horario demostrativo sujeto a confirmación'),(2,1,'martes','09:00:00','17:00:00',0,'Horario demostrativo sujeto a confirmación'),(3,1,'miercoles','09:00:00','17:00:00',0,'Horario demostrativo sujeto a confirmación'),(4,1,'jueves','09:00:00','17:00:00',0,'Horario demostrativo sujeto a confirmación'),(5,1,'viernes',NULL,NULL,1,'Horario demostrativo sujeto a confirmación'),(6,1,'sabado',NULL,NULL,1,'Sin atención al público'),(7,1,'domingo',NULL,NULL,1,'Sin atención al público'),(8,2,'lunes','09:00:00','17:00:00',0,'Horario demostrativo sujeto a confirmación'),(9,2,'martes','09:00:00','17:00:00',0,'Horario demostrativo sujeto a confirmación'),(10,2,'miercoles','09:00:00','17:00:00',0,'Horario demostrativo sujeto a confirmación'),(11,2,'jueves','09:00:00','17:00:00',0,'Horario demostrativo sujeto a confirmación'),(12,2,'viernes','09:00:00','17:00:00',0,'Horario demostrativo sujeto a confirmación'),(13,2,'sabado','09:00:00','14:00:00',0,'Horario demostrativo sujeto a confirmación'),(14,2,'domingo',NULL,NULL,1,'Sin atención al público'),(15,3,'lunes','09:00:00','17:00:00',0,'Horario demostrativo sujeto a confirmación'),(16,3,'martes','09:00:00','17:00:00',0,'Horario demostrativo sujeto a confirmación'),(17,3,'miercoles','09:00:00','17:00:00',0,'Horario demostrativo sujeto a confirmación'),(18,3,'jueves','09:00:00','17:00:00',0,'Horario demostrativo sujeto a confirmación'),(19,3,'viernes','09:00:00','17:00:00',0,'Horario demostrativo sujeto a confirmación'),(20,3,'sabado','09:00:00','14:00:00',0,'Horario demostrativo sujeto a confirmación'),(21,3,'domingo',NULL,NULL,1,'Sin atención al público'),(36,4,'lunes',NULL,NULL,1,NULL),(37,4,'martes',NULL,NULL,1,NULL),(38,4,'miercoles',NULL,NULL,1,NULL),(39,4,'jueves',NULL,NULL,1,NULL),(40,4,'viernes','09:00:00','17:00:00',0,NULL),(41,4,'sabado','09:00:00','14:00:00',0,NULL),(42,4,'domingo',NULL,NULL,1,NULL),(43,5,'lunes','09:00:00','17:00:00',0,NULL),(44,5,'martes','09:00:00','17:00:00',0,NULL),(45,5,'miercoles','09:00:00','17:00:00',0,NULL),(46,5,'jueves','09:00:00','17:00:00',0,NULL),(47,5,'viernes',NULL,NULL,1,NULL),(48,5,'sabado',NULL,NULL,1,NULL),(49,5,'domingo',NULL,NULL,1,NULL);
/*!40000 ALTER TABLE `horarios_atencion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notificaciones`
--

DROP TABLE IF EXISTS `notificaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notificaciones` (
  `id_notificacion` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_usuario` int(10) unsigned NOT NULL,
  `id_cita` bigint(20) unsigned DEFAULT NULL,
  `id_cotizacion` bigint(20) unsigned DEFAULT NULL,
  `id_publicacion` int(10) unsigned DEFAULT NULL,
  `tipo_notificacion` enum('recuperacion_contrasena','nuevo_aviso','confirmacion_cita','cambio_cita','cancelacion_cita','actualizacion_cotizacion','sistema') NOT NULL,
  `titulo` varchar(120) NOT NULL,
  `mensaje` varchar(500) NOT NULL,
  `medio` enum('interna','correo','interna_correo') NOT NULL DEFAULT 'interna',
  `estado` enum('no_leida','leida') NOT NULL DEFAULT 'no_leida',
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_lectura` datetime DEFAULT NULL,
  PRIMARY KEY (`id_notificacion`),
  KEY `fk_notificaciones_cita` (`id_cita`),
  KEY `fk_notificaciones_cotizacion` (`id_cotizacion`),
  KEY `fk_notificaciones_publicacion` (`id_publicacion`),
  KEY `idx_notificaciones_usuario` (`id_usuario`),
  KEY `idx_notificaciones_estado` (`estado`),
  KEY `idx_notificaciones_tipo` (`tipo_notificacion`),
  KEY `idx_notificaciones_fecha` (`fecha_creacion`),
  CONSTRAINT `fk_notificaciones_cita` FOREIGN KEY (`id_cita`) REFERENCES `citas` (`id_cita`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_notificaciones_cotizacion` FOREIGN KEY (`id_cotizacion`) REFERENCES `cotizaciones` (`id_cotizacion`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_notificaciones_publicacion` FOREIGN KEY (`id_publicacion`) REFERENCES `publicaciones` (`id_publicacion`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_notificaciones_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=114 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notificaciones`
--

LOCK TABLES `notificaciones` WRITE;
/*!40000 ALTER TABLE `notificaciones` DISABLE KEYS */;
INSERT INTO `notificaciones` VALUES (1,1,NULL,NULL,NULL,'sistema','Bienvenido a EcoGestión Ambiental','Explora los centros de acopio, los materiales educativos y los servicios disponibles en Jiutepec.','interna','no_leida','2026-08-25 11:37:00',NULL),(2,2,NULL,NULL,NULL,'sistema','Bienvenido a EcoGestión Ambiental','Explora los centros de acopio, los materiales educativos y los servicios disponibles en Jiutepec.','interna','no_leida','2026-08-25 11:37:00',NULL),(3,3,NULL,NULL,NULL,'sistema','Bienvenido a EcoGestión Ambiental','Explora los centros de acopio, los materiales educativos y los servicios disponibles en Jiutepec.','interna','no_leida','2026-08-25 11:37:00',NULL),(4,3,NULL,1,NULL,'actualizacion_cotizacion','Tu cotización cambió de estado','La cotización #1 ahora se encuentra en estado: Pendiente.','interna','no_leida','2026-08-25 11:39:16',NULL),(5,3,NULL,1,NULL,'actualizacion_cotizacion','Tu cotización cambió de estado','La cotización #1 ahora se encuentra en estado: Revisada.','interna','no_leida','2026-08-25 11:39:19',NULL),(6,3,NULL,1,NULL,'actualizacion_cotizacion','Tu cotización cambió de estado','La cotización #1 ahora se encuentra en estado: Pendiente.','interna','no_leida','2026-08-25 11:39:22',NULL),(7,4,NULL,NULL,NULL,'recuperacion_contrasena','Solicitud de recuperación de contraseña','Se generó un enlace temporal de recuperación para tu cuenta.','interna','no_leida','2026-08-25 12:58:17',NULL),(8,4,NULL,NULL,NULL,'recuperacion_contrasena','Solicitud de recuperación de contraseña','Se generó un enlace temporal de recuperación para tu cuenta.','interna','no_leida','2026-08-25 12:58:52',NULL),(9,4,NULL,NULL,NULL,'recuperacion_contrasena','Solicitud de recuperación de contraseña','Se generó un enlace temporal de recuperación para tu cuenta.','interna','no_leida','2026-08-25 13:00:38',NULL),(10,6,3,NULL,NULL,'cambio_cita','Solicitud de cita recibida','La cita #3 fue registrada y está pendiente de confirmación.','interna','leida','2026-10-09 17:28:57','2026-10-09 17:39:22'),(11,1,3,NULL,NULL,'cambio_cita','Nueva solicitud de cita','Se registró la cita #3 para tu centro.','interna','no_leida','2026-10-09 17:28:57',NULL),(12,4,3,NULL,NULL,'cambio_cita','Nueva solicitud de cita','Se registró la cita #3 para tu centro.','interna','no_leida','2026-10-09 17:28:57',NULL),(13,5,3,NULL,NULL,'cambio_cita','Nueva solicitud de cita','Se registró la cita #3 para tu centro.','interna','leida','2026-10-09 17:28:57','2026-10-09 17:38:57'),(14,6,2,NULL,NULL,'confirmacion_cita','Tu cita cambió de estado','La cita #2 ahora se encuentra en estado: Confirmada.','interna','leida','2026-10-09 17:40:15','2026-11-06 20:25:17'),(15,6,NULL,NULL,NULL,'recuperacion_contrasena','Contraseña restablecida','La contraseña de tu cuenta fue restablecida mediante el proceso de recuperación.','interna','leida','2026-10-24 08:06:19','2026-11-06 20:25:17'),(16,7,4,NULL,NULL,'cambio_cita','Solicitud de cita recibida','La cita #4 fue registrada y está pendiente de confirmación.','interna_correo','leida','2026-10-24 09:54:38','2026-11-06 20:41:29'),(17,1,4,NULL,NULL,'cambio_cita','Nueva solicitud de cita','Se registró la cita #4 para el centro correspondiente.','interna','no_leida','2026-10-24 09:54:38',NULL),(18,4,4,NULL,NULL,'cambio_cita','Nueva solicitud de cita','Se registró la cita #4 para el centro correspondiente.','interna','leida','2026-10-24 09:54:38','2026-10-24 18:42:59'),(19,5,4,NULL,NULL,'cambio_cita','Nueva solicitud de cita','Se registró la cita #4 para el centro correspondiente.','interna','leida','2026-10-24 09:54:38','2026-10-24 09:58:21'),(20,7,4,NULL,NULL,'confirmacion_cita','Tu cita cambió de estado','La cita #4 ahora se encuentra en estado: Confirmada.','interna_correo','leida','2026-10-24 09:58:37','2026-11-06 20:41:29'),(21,7,NULL,4,NULL,'actualizacion_cotizacion','Cotización registrada','La cotización #4 fue registrada con un importe estimado de $1,300.00.','interna_correo','leida','2026-10-24 18:34:26','2026-11-06 20:41:29'),(22,1,NULL,4,NULL,'actualizacion_cotizacion','Nueva cotización recibida','Se registró la cotización #4 y necesita revisión.','interna','no_leida','2026-10-24 18:34:26',NULL),(23,2,NULL,4,NULL,'actualizacion_cotizacion','Nueva cotización recibida','Se registró la cotización #4 y necesita revisión.','interna','no_leida','2026-10-24 18:34:26',NULL),(24,4,NULL,4,NULL,'actualizacion_cotizacion','Nueva cotización recibida','Se registró la cotización #4 y necesita revisión.','interna','leida','2026-10-24 18:34:26','2026-10-24 18:36:43'),(25,7,NULL,5,NULL,'actualizacion_cotizacion','Cotización registrada','La cotización #5 fue registrada con un importe estimado de $105.00.','interna_correo','leida','2026-10-24 18:54:26','2026-11-06 20:41:29'),(26,1,NULL,5,NULL,'actualizacion_cotizacion','Nueva cotización recibida','Se registró la cotización #5 y necesita revisión.','interna','no_leida','2026-10-24 18:54:26',NULL),(27,2,NULL,5,NULL,'actualizacion_cotizacion','Nueva cotización recibida','Se registró la cotización #5 y necesita revisión.','interna','no_leida','2026-10-24 18:54:26',NULL),(28,4,NULL,5,NULL,'actualizacion_cotizacion','Nueva cotización recibida','Se registró la cotización #5 y necesita revisión.','interna','leida','2026-10-24 18:54:26','2026-10-24 18:54:59'),(29,7,NULL,5,NULL,'actualizacion_cotizacion','Tu cotización cambió de estado','La cotización #5 ahora se encuentra en estado: Revisada.','interna_correo','leida','2026-10-24 18:55:09','2026-11-06 20:41:29'),(30,7,NULL,5,NULL,'actualizacion_cotizacion','Tu cotización cambió de estado','La cotización #5 ahora se encuentra en estado: Aprobada.','interna_correo','leida','2026-10-24 18:55:46','2026-11-06 20:41:29'),(31,1,NULL,NULL,7,'nuevo_aviso','Nuevo contenido ambiental','Se publicó: Suspensión temporal de recepción de vidrio.','interna','no_leida','2026-11-04 07:40:58',NULL),(32,2,NULL,NULL,7,'nuevo_aviso','Nuevo contenido ambiental','Se publicó: Suspensión temporal de recepción de vidrio.','interna','no_leida','2026-11-04 07:40:58',NULL),(33,5,NULL,NULL,7,'nuevo_aviso','Nuevo contenido ambiental','Se publicó: Suspensión temporal de recepción de vidrio.','interna','no_leida','2026-11-04 07:40:58',NULL),(34,9,NULL,NULL,7,'nuevo_aviso','Nuevo contenido ambiental','Se publicó: Suspensión temporal de recepción de vidrio.','interna','no_leida','2026-11-04 07:40:58',NULL),(35,10,NULL,NULL,7,'nuevo_aviso','Nuevo contenido ambiental','Se publicó: Suspensión temporal de recepción de vidrio.','interna','no_leida','2026-11-04 07:40:58',NULL),(36,3,NULL,NULL,7,'nuevo_aviso','Nuevo contenido ambiental','Se publicó: Suspensión temporal de recepción de vidrio.','interna','no_leida','2026-11-04 07:40:58',NULL),(37,6,NULL,NULL,7,'nuevo_aviso','Nuevo contenido ambiental','Se publicó: Suspensión temporal de recepción de vidrio.','interna','leida','2026-11-04 07:40:58','2026-11-06 20:25:17'),(38,7,NULL,NULL,7,'nuevo_aviso','Nuevo contenido ambiental','Se publicó: Suspensión temporal de recepción de vidrio.','interna','leida','2026-11-04 07:40:58','2026-11-06 20:41:29'),(39,8,NULL,NULL,7,'nuevo_aviso','Nuevo contenido ambiental','Se publicó: Suspensión temporal de recepción de vidrio.','interna','no_leida','2026-11-04 07:40:58',NULL),(40,1,NULL,NULL,7,'nuevo_aviso','Nuevo contenido ambiental','Se publicó: Suspensión temporal de recepción de vidrio.','interna','no_leida','2026-11-04 07:41:14',NULL),(41,2,NULL,NULL,7,'nuevo_aviso','Nuevo contenido ambiental','Se publicó: Suspensión temporal de recepción de vidrio.','interna','no_leida','2026-11-04 07:41:14',NULL),(42,5,NULL,NULL,7,'nuevo_aviso','Nuevo contenido ambiental','Se publicó: Suspensión temporal de recepción de vidrio.','interna','no_leida','2026-11-04 07:41:14',NULL),(43,9,NULL,NULL,7,'nuevo_aviso','Nuevo contenido ambiental','Se publicó: Suspensión temporal de recepción de vidrio.','interna','no_leida','2026-11-04 07:41:14',NULL),(44,10,NULL,NULL,7,'nuevo_aviso','Nuevo contenido ambiental','Se publicó: Suspensión temporal de recepción de vidrio.','interna','no_leida','2026-11-04 07:41:14',NULL),(45,3,NULL,NULL,7,'nuevo_aviso','Nuevo contenido ambiental','Se publicó: Suspensión temporal de recepción de vidrio.','interna','no_leida','2026-11-04 07:41:14',NULL),(46,6,NULL,NULL,7,'nuevo_aviso','Nuevo contenido ambiental','Se publicó: Suspensión temporal de recepción de vidrio.','interna','leida','2026-11-04 07:41:14','2026-11-06 20:25:17'),(47,7,NULL,NULL,7,'nuevo_aviso','Nuevo contenido ambiental','Se publicó: Suspensión temporal de recepción de vidrio.','interna','leida','2026-11-04 07:41:14','2026-11-06 20:41:29'),(48,8,NULL,NULL,7,'nuevo_aviso','Nuevo contenido ambiental','Se publicó: Suspensión temporal de recepción de vidrio.','interna','no_leida','2026-11-04 07:41:14',NULL),(49,1,NULL,NULL,7,'nuevo_aviso','Nuevo contenido ambiental','Se publicó: Suspensión temporal de recepción de vidrio.','interna','no_leida','2026-11-04 07:48:58',NULL),(50,2,NULL,NULL,7,'nuevo_aviso','Nuevo contenido ambiental','Se publicó: Suspensión temporal de recepción de vidrio.','interna','no_leida','2026-11-04 07:48:58',NULL),(51,5,NULL,NULL,7,'nuevo_aviso','Nuevo contenido ambiental','Se publicó: Suspensión temporal de recepción de vidrio.','interna','no_leida','2026-11-04 07:48:58',NULL),(52,9,NULL,NULL,7,'nuevo_aviso','Nuevo contenido ambiental','Se publicó: Suspensión temporal de recepción de vidrio.','interna','no_leida','2026-11-04 07:48:58',NULL),(53,10,NULL,NULL,7,'nuevo_aviso','Nuevo contenido ambiental','Se publicó: Suspensión temporal de recepción de vidrio.','interna','no_leida','2026-11-04 07:48:58',NULL),(54,3,NULL,NULL,7,'nuevo_aviso','Nuevo contenido ambiental','Se publicó: Suspensión temporal de recepción de vidrio.','interna','no_leida','2026-11-04 07:48:58',NULL),(55,6,NULL,NULL,7,'nuevo_aviso','Nuevo contenido ambiental','Se publicó: Suspensión temporal de recepción de vidrio.','interna','leida','2026-11-04 07:48:58','2026-11-06 20:25:17'),(56,7,NULL,NULL,7,'nuevo_aviso','Nuevo contenido ambiental','Se publicó: Suspensión temporal de recepción de vidrio.','interna','leida','2026-11-04 07:48:58','2026-11-06 20:41:29'),(57,8,NULL,NULL,7,'nuevo_aviso','Nuevo contenido ambiental','Se publicó: Suspensión temporal de recepción de vidrio.','interna','no_leida','2026-11-04 07:48:58',NULL),(58,1,NULL,NULL,8,'nuevo_aviso','Nuevo contenido ambiental','Se publicó: Jornada de recolección de residuos electrónicos en Punto Verde CIVAC.','interna','no_leida','2026-11-04 07:53:11',NULL),(59,2,NULL,NULL,8,'nuevo_aviso','Nuevo contenido ambiental','Se publicó: Jornada de recolección de residuos electrónicos en Punto Verde CIVAC.','interna','no_leida','2026-11-04 07:53:11',NULL),(60,5,NULL,NULL,8,'nuevo_aviso','Nuevo contenido ambiental','Se publicó: Jornada de recolección de residuos electrónicos en Punto Verde CIVAC.','interna','no_leida','2026-11-04 07:53:11',NULL),(61,9,NULL,NULL,8,'nuevo_aviso','Nuevo contenido ambiental','Se publicó: Jornada de recolección de residuos electrónicos en Punto Verde CIVAC.','interna','no_leida','2026-11-04 07:53:11',NULL),(62,10,NULL,NULL,8,'nuevo_aviso','Nuevo contenido ambiental','Se publicó: Jornada de recolección de residuos electrónicos en Punto Verde CIVAC.','interna','no_leida','2026-11-04 07:53:11',NULL),(63,3,NULL,NULL,8,'nuevo_aviso','Nuevo contenido ambiental','Se publicó: Jornada de recolección de residuos electrónicos en Punto Verde CIVAC.','interna','no_leida','2026-11-04 07:53:11',NULL),(64,6,NULL,NULL,8,'nuevo_aviso','Nuevo contenido ambiental','Se publicó: Jornada de recolección de residuos electrónicos en Punto Verde CIVAC.','interna','leida','2026-11-04 07:53:11','2026-11-06 20:25:17'),(65,7,NULL,NULL,8,'nuevo_aviso','Nuevo contenido ambiental','Se publicó: Jornada de recolección de residuos electrónicos en Punto Verde CIVAC.','interna','leida','2026-11-04 07:53:11','2026-11-06 20:41:29'),(66,8,NULL,NULL,8,'nuevo_aviso','Nuevo contenido ambiental','Se publicó: Jornada de recolección de residuos electrónicos en Punto Verde CIVAC.','interna','no_leida','2026-11-04 07:53:11',NULL),(67,1,NULL,NULL,8,'nuevo_aviso','Nuevo contenido ambiental','Se publicó: Jornada de recolección de residuos electrónicos en Punto Verde Civac.','interna','no_leida','2026-11-04 07:55:13',NULL),(68,2,NULL,NULL,8,'nuevo_aviso','Nuevo contenido ambiental','Se publicó: Jornada de recolección de residuos electrónicos en Punto Verde Civac.','interna','no_leida','2026-11-04 07:55:13',NULL),(69,5,NULL,NULL,8,'nuevo_aviso','Nuevo contenido ambiental','Se publicó: Jornada de recolección de residuos electrónicos en Punto Verde Civac.','interna','no_leida','2026-11-04 07:55:13',NULL),(70,9,NULL,NULL,8,'nuevo_aviso','Nuevo contenido ambiental','Se publicó: Jornada de recolección de residuos electrónicos en Punto Verde Civac.','interna','no_leida','2026-11-04 07:55:13',NULL),(71,10,NULL,NULL,8,'nuevo_aviso','Nuevo contenido ambiental','Se publicó: Jornada de recolección de residuos electrónicos en Punto Verde Civac.','interna','no_leida','2026-11-04 07:55:13',NULL),(72,3,NULL,NULL,8,'nuevo_aviso','Nuevo contenido ambiental','Se publicó: Jornada de recolección de residuos electrónicos en Punto Verde Civac.','interna','no_leida','2026-11-04 07:55:13',NULL),(73,6,NULL,NULL,8,'nuevo_aviso','Nuevo contenido ambiental','Se publicó: Jornada de recolección de residuos electrónicos en Punto Verde Civac.','interna','leida','2026-11-04 07:55:13','2026-11-06 20:25:17'),(74,7,NULL,NULL,8,'nuevo_aviso','Nuevo contenido ambiental','Se publicó: Jornada de recolección de residuos electrónicos en Punto Verde Civac.','interna','leida','2026-11-04 07:55:13','2026-11-06 20:41:29'),(75,8,NULL,NULL,8,'nuevo_aviso','Nuevo contenido ambiental','Se publicó: Jornada de recolección de residuos electrónicos en Punto Verde Civac.','interna','no_leida','2026-11-04 07:55:13',NULL),(76,6,5,NULL,NULL,'cambio_cita','Solicitud de cita recibida','La cita #5 fue registrada y está pendiente de confirmación.','interna_correo','leida','2026-11-04 22:28:44','2026-11-06 20:25:17'),(77,1,5,NULL,NULL,'cambio_cita','Nueva solicitud de cita','Se registró la cita #5 para el centro correspondiente.','interna','no_leida','2026-11-04 22:28:44',NULL),(78,4,5,NULL,NULL,'cambio_cita','Nueva solicitud de cita','Se registró la cita #5 para el centro correspondiente.','interna','no_leida','2026-11-04 22:28:44',NULL),(79,9,5,NULL,NULL,'cambio_cita','Nueva solicitud de cita','Se registró la cita #5 para el centro correspondiente.','interna','no_leida','2026-11-04 22:28:44',NULL),(80,6,5,NULL,NULL,'confirmacion_cita','Tu cita cambió de estado','La cita #5 ahora se encuentra en estado: Confirmada.','interna_correo','leida','2026-11-04 22:29:49','2026-11-06 20:25:17'),(81,6,NULL,6,NULL,'actualizacion_cotizacion','Cotización registrada','La cotización #6 fue registrada con un importe estimado de $500.00.','interna_correo','leida','2026-11-05 06:06:10','2026-11-06 20:25:17'),(82,1,NULL,6,NULL,'actualizacion_cotizacion','Nueva cotización recibida','Se registró la cotización #6 y necesita revisión.','interna','no_leida','2026-11-05 06:06:10',NULL),(83,4,NULL,6,NULL,'actualizacion_cotizacion','Nueva cotización recibida','Se registró la cotización #6 y necesita revisión.','interna','no_leida','2026-11-05 06:06:10',NULL),(84,9,NULL,6,NULL,'actualizacion_cotizacion','Nueva cotización recibida','Se registró la cotización #6 y necesita revisión.','interna','no_leida','2026-11-05 06:06:10',NULL),(85,6,NULL,6,NULL,'actualizacion_cotizacion','Tu cotización cambió de estado','La cotización #6 ahora se encuentra en estado: Revisada.','interna_correo','leida','2026-11-05 06:07:07','2026-11-06 20:25:17'),(86,6,NULL,6,NULL,'actualizacion_cotizacion','Tu cotización cambió de estado','La cotización #6 ahora se encuentra en estado: Aprobada.','interna_correo','leida','2026-11-05 06:07:19','2026-11-06 20:25:17'),(87,6,NULL,3,NULL,'actualizacion_cotizacion','Cotización cancelada','La cotización #3 fue cancelada correctamente.','interna_correo','leida','2026-11-05 06:08:02','2026-11-06 20:25:17'),(88,1,NULL,3,NULL,'actualizacion_cotizacion','Cotización cancelada','El ciudadano canceló la cotización #3.','interna','no_leida','2026-11-05 06:08:02',NULL),(89,4,NULL,3,NULL,'actualizacion_cotizacion','Cotización cancelada','El ciudadano canceló la cotización #3.','interna','no_leida','2026-11-05 06:08:02',NULL),(90,2,NULL,3,NULL,'actualizacion_cotizacion','Cotización cancelada','El ciudadano canceló la cotización #3.','interna','no_leida','2026-11-05 06:08:02',NULL),(91,10,NULL,3,NULL,'actualizacion_cotizacion','Cotización cancelada','El ciudadano canceló la cotización #3.','interna','no_leida','2026-11-05 06:08:02',NULL),(92,6,2,NULL,NULL,'cambio_cita','Tu cita cambió de estado','La cita #2 ahora se encuentra en estado: Atendida.','interna_correo','leida','2026-11-06 14:09:58','2026-11-06 20:25:17'),(93,6,3,NULL,NULL,'confirmacion_cita','Tu cita cambió de estado','La cita #3 ahora se encuentra en estado: Confirmada.','interna_correo','leida','2026-11-06 14:10:32','2026-11-06 20:25:17'),(94,6,3,NULL,NULL,'cambio_cita','Tu cita cambió de estado','La cita #3 ahora se encuentra en estado: Atendida.','interna_correo','leida','2026-11-06 14:10:38','2026-11-06 20:25:17'),(95,3,1,NULL,NULL,'confirmacion_cita','Tu cita cambió de estado','La cita #1 ahora se encuentra en estado: Confirmada.','interna_correo','no_leida','2026-11-06 14:10:50',NULL),(96,3,1,NULL,NULL,'cambio_cita','Tu cita cambió de estado','La cita #1 ahora se encuentra en estado: Atendida.','interna_correo','no_leida','2026-11-06 14:10:55',NULL),(97,6,NULL,7,NULL,'actualizacion_cotizacion','Cotización registrada','La cotización #7 fue registrada con un importe estimado de $250.00.','interna_correo','leida','2026-11-06 20:22:30','2026-11-06 20:22:43'),(98,1,NULL,7,NULL,'actualizacion_cotizacion','Nueva cotización recibida','Se registró la cotización #7 y necesita revisión.','interna','no_leida','2026-11-06 20:22:30',NULL),(99,4,NULL,7,NULL,'actualizacion_cotizacion','Nueva cotización recibida','Se registró la cotización #7 y necesita revisión.','interna','no_leida','2026-11-06 20:22:30',NULL),(100,9,NULL,7,NULL,'actualizacion_cotizacion','Nueva cotización recibida','Se registró la cotización #7 y necesita revisión.','interna','leida','2026-11-06 20:22:30','2026-11-06 20:23:29'),(101,6,NULL,7,NULL,'actualizacion_cotizacion','Tu cotización cambió de estado','La cotización #7 ahora se encuentra en estado: Revisada.','interna_correo','leida','2026-11-06 20:23:48','2026-11-06 20:25:17'),(102,6,NULL,7,NULL,'actualizacion_cotizacion','Tu cotización cambió de estado','La cotización #7 ahora se encuentra en estado: Aprobada.','interna_correo','leida','2026-11-06 20:24:01','2026-11-06 20:24:32'),(103,6,NULL,8,NULL,'actualizacion_cotizacion','Cotización registrada','La cotización #8 fue registrada con un importe estimado de $350.00.','interna_correo','leida','2026-11-06 20:33:29','2026-11-06 20:33:44'),(104,1,NULL,8,NULL,'actualizacion_cotizacion','Nueva cotización recibida','Se registró la cotización #8 y necesita revisión.','interna','no_leida','2026-11-06 20:33:29',NULL),(105,4,NULL,8,NULL,'actualizacion_cotizacion','Nueva cotización recibida','Se registró la cotización #8 y necesita revisión.','interna','no_leida','2026-11-06 20:33:29',NULL),(106,9,NULL,8,NULL,'actualizacion_cotizacion','Nueva cotización recibida','Se registró la cotización #8 y necesita revisión.','interna','leida','2026-11-06 20:33:29','2026-11-06 20:34:16'),(107,6,NULL,8,NULL,'actualizacion_cotizacion','Tu cotización cambió de estado','La cotización #8 ahora se encuentra en estado: Revisada.','interna_correo','no_leida','2026-11-06 20:34:30',NULL),(108,7,NULL,9,NULL,'actualizacion_cotizacion','Cotización registrada','La cotización #9 fue registrada con un importe estimado de $500.00.','interna_correo','leida','2026-11-06 20:36:17','2026-11-06 20:41:29'),(109,1,NULL,9,NULL,'actualizacion_cotizacion','Nueva cotización recibida','Se registró la cotización #9 y necesita revisión.','interna','no_leida','2026-11-06 20:36:17',NULL),(110,4,NULL,9,NULL,'actualizacion_cotizacion','Nueva cotización recibida','Se registró la cotización #9 y necesita revisión.','interna','no_leida','2026-11-06 20:36:17',NULL),(111,9,NULL,9,NULL,'actualizacion_cotizacion','Nueva cotización recibida','Se registró la cotización #9 y necesita revisión.','interna','leida','2026-11-06 20:36:17','2026-11-06 20:38:52'),(112,7,NULL,9,NULL,'actualizacion_cotizacion','Tu cotización cambió de estado','La cotización #9 ahora se encuentra en estado: Revisada.','interna_correo','leida','2026-11-06 20:39:06','2026-11-06 20:41:29'),(113,7,NULL,9,NULL,'actualizacion_cotizacion','Tu cotización cambió de estado','La cotización #9 ahora se encuentra en estado: Aprobada.','interna_correo','leida','2026-11-06 20:39:58','2026-11-06 20:40:53');
/*!40000 ALTER TABLE `notificaciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `opciones_pregunta`
--

DROP TABLE IF EXISTS `opciones_pregunta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `opciones_pregunta` (
  `id_opcion` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_pregunta` int(10) unsigned NOT NULL,
  `texto_opcion` varchar(150) NOT NULL,
  `valor` smallint(6) DEFAULT NULL,
  `orden` smallint(5) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_opcion`),
  KEY `idx_opciones_pregunta` (`id_pregunta`),
  CONSTRAINT `fk_opciones_pregunta` FOREIGN KEY (`id_pregunta`) REFERENCES `preguntas_encuesta` (`id_pregunta`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `opciones_pregunta`
--

LOCK TABLES `opciones_pregunta` WRITE;
/*!40000 ALTER TABLE `opciones_pregunta` DISABLE KEYS */;
INSERT INTO `opciones_pregunta` VALUES (1,1,'Siempre',NULL,1),(2,1,'Algunas veces',NULL,2),(3,1,'Casi nunca',NULL,3),(4,1,'Nunca',NULL,4),(5,3,'Siempre',1,1),(6,3,'Casi siempre',2,2),(7,3,'Algunas veces',3,3),(8,3,'Nunca',4,4),(9,4,'Papel y cartón',1,1),(10,4,'Plástico',2,2),(11,4,'Vidrio',3,3),(12,4,'Metal',4,4),(13,4,'Residuos electrónicos',5,5),(14,4,'Residuos orgánicos',6,6),(15,4,'No separo mis residuos',7,7),(16,6,'Sí, frecuentemente',1,1),(17,6,'Sí, una o dos veces',2,2),(18,6,'No, pero me gustaría hacerlo',3,3),(19,6,'No conozco ningún centro de acopio',4,4),(20,7,'Consulta de centros de acopio',1,1),(21,7,'Mapa interactivo',2,2),(22,7,'Cotización de materiales',3,3),(23,7,'Programación de citas',4,4),(24,7,'Noticias y campañas',5,5),(25,7,'Base de conocimiento',6,6),(26,7,'Juegos y actividades',7,7);
/*!40000 ALTER TABLE `opciones_pregunta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `preguntas_encuesta`
--

DROP TABLE IF EXISTS `preguntas_encuesta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `preguntas_encuesta` (
  `id_pregunta` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_encuesta` int(10) unsigned NOT NULL,
  `texto_pregunta` varchar(255) NOT NULL,
  `tipo_pregunta` enum('opcion_multiple','respuesta_corta','escala') NOT NULL,
  `obligatoria` tinyint(1) NOT NULL DEFAULT 1,
  `orden` smallint(5) unsigned NOT NULL DEFAULT 1,
  `estado` enum('activa','inactiva') NOT NULL DEFAULT 'activa',
  PRIMARY KEY (`id_pregunta`),
  KEY `idx_preguntas_encuesta` (`id_encuesta`),
  KEY `idx_preguntas_orden` (`orden`),
  CONSTRAINT `fk_preguntas_encuesta` FOREIGN KEY (`id_encuesta`) REFERENCES `encuestas` (`id_encuesta`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `preguntas_encuesta`
--

LOCK TABLES `preguntas_encuesta` WRITE;
/*!40000 ALTER TABLE `preguntas_encuesta` DISABLE KEYS */;
INSERT INTO `preguntas_encuesta` VALUES (1,1,'¿Con qué frecuencia separas los residuos en tu hogar?','opcion_multiple',1,1,'activa'),(2,2,'holaaa','respuesta_corta',1,1,'activa'),(3,3,'¿Con qué frecuencia separas los residuos generados en tu hogar?','opcion_multiple',0,1,'activa'),(4,3,'¿Qué tipo de residuo separas con mayor frecuencia?','opcion_multiple',1,2,'activa'),(5,3,'¿Qué tan fácil te resulta encontrar un centro de acopio cercano?','escala',1,3,'activa'),(6,3,'¿Has utilizado alguna vez un centro de acopio para entregar tus residuos?','opcion_multiple',1,4,'activa'),(7,3,'¿Qué funcionalidad de EcoGestión Ambiental consideras más útil?','opcion_multiple',1,5,'activa');
/*!40000 ALTER TABLE `preguntas_encuesta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `publicacion_categoria`
--

DROP TABLE IF EXISTS `publicacion_categoria`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `publicacion_categoria` (
  `id_publicacion` int(10) unsigned NOT NULL,
  `id_categoria` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_publicacion`,`id_categoria`),
  KEY `fk_publicacion_categoria_categoria` (`id_categoria`),
  CONSTRAINT `fk_publicacion_categoria_categoria` FOREIGN KEY (`id_categoria`) REFERENCES `categorias_residuos` (`id_categoria`) ON UPDATE CASCADE,
  CONSTRAINT `fk_publicacion_categoria_publicacion` FOREIGN KEY (`id_publicacion`) REFERENCES `publicaciones` (`id_publicacion`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `publicacion_categoria`
--

LOCK TABLES `publicacion_categoria` WRITE;
/*!40000 ALTER TABLE `publicacion_categoria` DISABLE KEYS */;
INSERT INTO `publicacion_categoria` VALUES (1,2),(1,3),(6,1),(6,2),(6,3),(6,6),(6,7),(6,8),(7,2),(7,4),(7,5),(7,7),(8,3),(8,4),(8,5),(8,6);
/*!40000 ALTER TABLE `publicacion_categoria` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `publicaciones`
--

DROP TABLE IF EXISTS `publicaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `publicaciones` (
  `id_publicacion` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_autor` int(10) unsigned NOT NULL,
  `tipo_publicacion` enum('noticia','aviso','campaña','evento','material_educativo','ficha_informativa') NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `resumen` varchar(255) DEFAULT NULL,
  `contenido` text NOT NULL,
  `imagen_portada` varchar(255) DEFAULT NULL,
  `fecha_publicacion` datetime DEFAULT NULL,
  `estado` enum('borrador','publicado','inactivo') NOT NULL DEFAULT 'borrador',
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_publicacion`),
  KEY `fk_publicaciones_autor` (`id_autor`),
  KEY `idx_publicaciones_tipo` (`tipo_publicacion`),
  KEY `idx_publicaciones_estado` (`estado`),
  KEY `idx_publicaciones_fecha` (`fecha_publicacion`),
  CONSTRAINT `fk_publicaciones_autor` FOREIGN KEY (`id_autor`) REFERENCES `usuarios` (`id_usuario`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `publicaciones`
--

LOCK TABLES `publicaciones` WRITE;
/*!40000 ALTER TABLE `publicaciones` DISABLE KEYS */;
INSERT INTO `publicaciones` VALUES (1,1,'material_educativo','Cómo separar correctamente los residuos en casa','Identifica los principales grupos de residuos y evita la contaminación cruzada.','Separa vidrio, cartón, plástico y metal. Lava los envases cuando sea posible, mantén seco el cartón y deposita los residuos peligrosos únicamente en centros autorizados.','uploads/publicaciones/publication-bc7c160189ebcc88a41162b357f25aef.jpg','2026-08-25 11:37:00','publicado','2026-08-25 11:37:00','2026-08-31 20:12:29'),(2,1,'ficha_informativa','Manejo responsable de residuos electrónicos','Celulares, baterías, cables y dispositivos requieren un manejo diferenciado.','No deseches baterías ni aparatos electrónicos junto con los residuos domésticos. Consulta un punto de recepción especializado y elimina la información personal de tus dispositivos.',NULL,'2026-08-25 11:37:00','publicado','2026-08-25 11:37:00',NULL),(3,1,'noticia','Jiutepec fortalece su cultura de reciclaje','La comunidad universitaria y los vecinos se suman a nuevas acciones ambientales.','Este contenido demostrativo presenta iniciativas de separación de residuos, educación ambiental y participación comunitaria en Jiutepec, Morelos.',NULL,'2026-08-25 11:37:00','publicado','2026-08-25 11:37:00',NULL),(4,1,'campaña','Campaña universitaria: trae tus electrónicos','Consulta los puntos y horarios disponibles para entregar aparatos electrónicos.','Los ciudadanos podrán localizar centros de acopio, revisar horarios y solicitar una cita desde la plataforma EcoGestión Ambiental.',NULL,'2026-08-25 11:37:00','publicado','2026-08-25 11:37:00',NULL),(5,1,'aviso','Recomendaciones para entregar cartón y plástico','Recuerda llevar tus materiales limpios, secos y separados.','Se recomienda compactar el cartón, separar tapas y envases, y consultar los materiales aceptados por cada punto de atención.',NULL,'2026-08-25 11:37:00','publicado','2026-08-25 11:37:00',NULL),(6,4,'ficha_informativa','Errores comunes al depositar residuos en los contenedores','Depositar un residuo en el contenedor equivocado puede contaminar otros materiales e impedir su reciclaje. Conoce los errores más comunes y aprende a evitarlos.','Utilizar contenedores separados es una buena práctica, pero también es necesario preparar correctamente los residuos. Un material reciclable puede dejar de aprovecharse cuando está mojado, sucio o mezclado con sustancias peligrosas.\r\n\r\n1. Depositar envases con restos de comida\r\n\r\nLas botellas, latas y recipientes con restos de alimentos pueden ensuciar el papel, el cartón y otros materiales. Antes de separarlos, deben vaciarse y enjuagarse ligeramente cuando sea necesario.\r\n\r\n2. Colocar papel o cartón mojado\r\n\r\nEl papel y el cartón pierden calidad cuando absorben agua, aceite o grasa. Las cajas de pizza con restos de comida y las servilletas usadas no deben mezclarse con el papel limpio.\r\n\r\n3. Mezclar residuos peligrosos\r\n\r\nLas pilas, medicamentos, pinturas, solventes y productos químicos requieren un manejo especial. No deben depositarse en los mismos contenedores que el plástico, cartón, vidrio o metal.\r\n\r\n4. Colocar vidrio roto sin protección\r\n\r\nEl vidrio roto puede causar lesiones a quienes recolectan y clasifican los residuos. Debe protegerse, marcarse y entregarse únicamente en centros que acepten este tipo de material.\r\n\r\n5. No revisar el tipo de plástico\r\n\r\nNo todos los plásticos se reciclan de la misma manera. Es importante revisar el símbolo del envase y consultar cuáles son aceptados por el centro de acopio.\r\n\r\n6. Mezclar residuos sanitarios\r\n\r\nCubrebocas, pañuelos, papel sanitario, pañales y otros residuos de higiene no deben colocarse con los materiales reciclables.\r\n\r\n7. Depositar objetos completos sin reducir su volumen\r\n\r\nLas botellas de plástico, cajas y latas pueden compactarse para ocupar menos espacio. Antes de hacerlo, deben estar vacías y secas.\r\n\r\nUna separación correcta mantiene los materiales en mejores condiciones, protege al personal encargado de su manejo y aumenta la posibilidad de que sean reciclados.','uploads/publicaciones/publication-27ddb7f42d71a8bb56a669ecc776d744.jpg','2026-11-04 07:48:49','publicado','2026-11-04 00:51:35','2026-11-04 07:48:49'),(7,4,'aviso','Suspensión temporal de recepción de vidrio','El Centro Circular Tejalpa suspenderá temporalmente la recepción de vidrio debido a trabajos de mantenimiento.','Se informa a la ciudadanía que el Centro Circular Tejalpa suspenderá temporalmente la recepción de botellas, frascos y otros residuos de vidrio del 6 al 8 de noviembre de 2026.\r\n\r\nLa suspensión se debe a trabajos de mantenimiento y reorganización del área destinada al almacenamiento seguro de este material.\r\n\r\nDurante este periodo, el centro continuará recibiendo los siguientes residuos:\r\n- Papel y cartón.\r\n- Botellas y envases de plástico.\r\n- Latas y objetos metálicos pequeños.\r\n- Aparatos electrónicos de tamaño reducido.\r\n- Envases multicapa limpios y secos.\r\nSe solicita a los usuarios conservar temporalmente los residuos de vidrio en un lugar seguro. Las botellas y frascos deben permanecer vacíos, limpios y separados del vidrio roto.\r\n\r\nLa recepción de vidrio se reanudará el 9 de noviembre de 2026, dentro del horario habitual del centro.\r\n\r\nAgradecemos la comprensión y colaboración de la ciudadanía.',NULL,'2026-11-04 07:40:58','publicado','2026-11-04 01:01:54','2026-11-04 07:48:58'),(8,4,'evento','Jornada de recolección de residuos electrónicos en Punto Verde Civac','Participa en la jornada de recolección de celulares, computadoras, cables, baterías y otros aparatos electrónicos en desuso.','EcoGestión Ambiental invita a la ciudadanía a participar en la Jornada de Recolección de Residuos Electrónicos, que se realizará el sábado 14 de noviembre de 2026, de 09:00 a 14:00 horas, en el centro de acopio Punto Verde CIVAC.\r\n\r\nDurante la jornada se recibirán celulares, tabletas, computadoras, teclados, cables, cargadores, bocinas, pequeños electrodomésticos y baterías usadas.\r\n\r\nAntes de entregar computadoras, celulares o dispositivos de almacenamiento, se recomienda eliminar toda la información personal y retirar las cuentas vinculadas.\r\n\r\nLos materiales deberán entregarse limpios, secos y, cuando sea posible, separados por tipo. No se recibirán equipos contaminados con líquidos, residuos orgánicos o sustancias desconocidas.\r\n\r\nEsta actividad busca evitar que los residuos electrónicos terminen mezclados con la basura doméstica, ya que algunos de sus componentes pueden contaminar el suelo y el agua.\r\n\r\nLa participación es gratuita. Para conocer la ubicación exacta, consulta el módulo de Centros de acopio o el Mapa interactivo de EcoGestión Ambiental.',NULL,'2026-11-04 07:53:11','publicado','2026-11-04 07:53:11','2026-11-04 07:55:13');
/*!40000 ALTER TABLE `publicaciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `punto_categoria`
--

DROP TABLE IF EXISTS `punto_categoria`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `punto_categoria` (
  `id_punto` int(10) unsigned NOT NULL,
  `id_categoria` int(10) unsigned NOT NULL,
  `acepta_compra` tinyint(1) NOT NULL DEFAULT 0,
  `acepta_recepcion` tinyint(1) NOT NULL DEFAULT 1,
  `observaciones` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`id_punto`,`id_categoria`),
  KEY `fk_punto_categoria_categoria` (`id_categoria`),
  CONSTRAINT `fk_punto_categoria_categoria` FOREIGN KEY (`id_categoria`) REFERENCES `categorias_residuos` (`id_categoria`) ON UPDATE CASCADE,
  CONSTRAINT `fk_punto_categoria_punto` FOREIGN KEY (`id_punto`) REFERENCES `puntos_atencion` (`id_punto`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `punto_categoria`
--

LOCK TABLES `punto_categoria` WRITE;
/*!40000 ALTER TABLE `punto_categoria` DISABLE KEYS */;
INSERT INTO `punto_categoria` VALUES (1,1,0,1,NULL),(1,2,0,1,NULL),(1,4,0,1,NULL),(1,5,0,1,NULL),(2,1,1,1,NULL),(2,2,1,1,NULL),(2,3,1,1,NULL),(2,4,1,1,NULL),(2,5,1,1,NULL),(2,6,1,1,NULL),(3,1,0,1,NULL),(3,2,0,1,NULL),(3,3,0,1,NULL),(3,4,0,1,NULL),(3,5,0,1,NULL),(3,6,0,1,NULL),(4,1,1,1,NULL),(4,2,1,1,NULL),(4,3,1,1,NULL),(4,4,1,1,NULL),(4,6,1,1,NULL),(4,8,1,1,NULL),(5,1,1,1,NULL),(5,2,1,1,NULL),(5,4,1,1,NULL);
/*!40000 ALTER TABLE `punto_categoria` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `puntos_atencion`
--

DROP TABLE IF EXISTS `puntos_atencion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `puntos_atencion` (
  `id_punto` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(120) NOT NULL,
  `tipo_punto` enum('empresa','centro_acopio','punto_servicio') NOT NULL,
  `direccion` varchar(200) NOT NULL,
  `municipio` varchar(100) DEFAULT NULL,
  `estado_region` varchar(100) DEFAULT 'Morelos',
  `codigo_postal` varchar(10) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `correo_contacto` varchar(120) DEFAULT NULL,
  `tipo_servicio` enum('compra_residuos','recepcion_reciclables','residuos_electronicos','materiales_toxicos','servicio_general') NOT NULL DEFAULT 'servicio_general',
  `latitud` decimal(10,7) DEFAULT NULL,
  `longitud` decimal(10,7) DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `estado` enum('activo','inactivo') NOT NULL DEFAULT 'activo',
  `fecha_registro` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_punto`),
  KEY `idx_puntos_tipo` (`tipo_punto`),
  KEY `idx_puntos_servicio` (`tipo_servicio`),
  KEY `idx_puntos_estado` (`estado`),
  KEY `idx_puntos_ubicacion` (`latitud`,`longitud`),
  CONSTRAINT `CONSTRAINT_1` CHECK (`latitud` is null or `latitud` between -90 and 90),
  CONSTRAINT `CONSTRAINT_2` CHECK (`longitud` is null or `longitud` between -180 and 180)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `puntos_atencion`
--

LOCK TABLES `puntos_atencion` WRITE;
/*!40000 ALTER TABLE `puntos_atencion` DISABLE KEYS */;
INSERT INTO `puntos_atencion` VALUES (1,'Centro Ambiental UPEMOR','centro_acopio','Bulevar Cuauhnáhuac 566, Lomas del Texcal','Jiutepec','Morelos',NULL,'7772293500',NULL,'recepcion_reciclables',18.8914200,-99.1631400,'Punto demostrativo de recepción para la comunidad universitaria.','activo','2026-08-25 11:37:00','2026-08-31 08:31:29'),(2,'Recicla Jiutepec Centro','empresa','Avenida Insurgentes 24, Centro','Jiutepec','Morelos',NULL,'7771002020',NULL,'compra_residuos',18.8850768,-99.1737649,'Centro demostrativo para compra y recepción de residuos reciclables.','activo','2026-08-25 11:37:00','2026-11-04 22:18:30'),(3,'Punto Verde CIVAC','punto_servicio','Avenida Centenario 108, CIVAC','Jiutepec','Morelos',NULL,'7771003030',NULL,'residuos_electronicos',18.9056200,-99.1788100,'Punto demostrativo para electrónicos y materiales de manejo especial.','activo','2026-08-25 11:37:00','2026-09-24 09:02:19'),(4,'EcoPunto Las Fuentes Jiutepec','punto_servicio','Avenida de las Fuentes 125, Colonia Centro','Jiutepec','Morelos','62550','7774567890','contacto@gmail.com','compra_residuos',18.8837600,-99.1769100,'Centro dedicado a la recepción y compra de residuos reciclables generados por hogares y pequeños comercios.','activo','2026-11-04 00:06:24','2026-11-04 08:24:48'),(5,'Centro Circular','punto_servicio','Avenida Insurgentes 48, Colonia Tejalpa','Jiutepec','Morelos','62570','7773251840','circular@gmail.com','compra_residuos',18.8992000,-99.1605000,'Punto ambiental dedicado a recibir materiales reciclables y orientar a la ciudadanía sobre su correcta separación.','activo','2026-11-04 00:18:50','2026-11-04 00:22:58');
/*!40000 ALTER TABLE `puntos_atencion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reglas_cotizacion`
--

DROP TABLE IF EXISTS `reglas_cotizacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reglas_cotizacion` (
  `id_regla` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_punto` int(10) unsigned NOT NULL,
  `id_categoria` int(10) unsigned NOT NULL,
  `precio_por_kg` decimal(10,2) NOT NULL DEFAULT 0.00,
  `precio_por_pieza` decimal(10,2) NOT NULL DEFAULT 0.00,
  `minimo_kg` decimal(10,2) DEFAULT NULL,
  `maximo_kg` decimal(10,2) DEFAULT NULL,
  `vigencia_inicio` date NOT NULL,
  `vigencia_fin` date DEFAULT NULL,
  `estado` enum('activa','inactiva') NOT NULL DEFAULT 'activa',
  PRIMARY KEY (`id_regla`),
  UNIQUE KEY `uk_reglas_punto_categoria_inicio` (`id_punto`,`id_categoria`,`vigencia_inicio`),
  KEY `idx_reglas_categoria` (`id_categoria`),
  KEY `idx_reglas_vigencia` (`vigencia_inicio`,`vigencia_fin`),
  KEY `idx_reglas_estado` (`estado`),
  KEY `idx_reglas_punto_categoria_estado` (`id_punto`,`id_categoria`,`estado`),
  CONSTRAINT `fk_reglas_categoria` FOREIGN KEY (`id_categoria`) REFERENCES `categorias_residuos` (`id_categoria`) ON UPDATE CASCADE,
  CONSTRAINT `fk_reglas_punto` FOREIGN KEY (`id_punto`) REFERENCES `puntos_atencion` (`id_punto`) ON UPDATE CASCADE,
  CONSTRAINT `CONSTRAINT_1` CHECK (`precio_por_kg` >= 0),
  CONSTRAINT `CONSTRAINT_2` CHECK (`precio_por_pieza` >= 0),
  CONSTRAINT `CONSTRAINT_3` CHECK (`minimo_kg` is null or `minimo_kg` >= 0),
  CONSTRAINT `CONSTRAINT_4` CHECK (`maximo_kg` is null or `maximo_kg` >= 0),
  CONSTRAINT `CONSTRAINT_5` CHECK (`vigencia_fin` is null or `vigencia_inicio` <= `vigencia_fin`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reglas_cotizacion`
--

LOCK TABLES `reglas_cotizacion` WRITE;
/*!40000 ALTER TABLE `reglas_cotizacion` DISABLE KEYS */;
INSERT INTO `reglas_cotizacion` VALUES (1,2,1,2.20,0.00,0.10,1000.00,'2026-01-01',NULL,'activa'),(2,2,2,3.80,0.00,0.10,1000.00,'2026-01-01',NULL,'inactiva'),(3,2,3,6.50,0.00,0.10,1000.00,'2026-01-01',NULL,'activa'),(4,2,4,13.00,0.00,0.10,1000.00,'2026-01-01',NULL,'activa'),(5,2,5,8.75,0.00,0.10,1000.00,'2026-01-01',NULL,'activa'),(6,4,8,10.00,0.30,0.50,200.00,'2026-11-05','2027-10-19','activa'),(7,4,2,5.00,0.50,0.50,200.00,'2026-11-05',NULL,'activa');
/*!40000 ALTER TABLE `reglas_cotizacion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `respuestas_encuesta`
--

DROP TABLE IF EXISTS `respuestas_encuesta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `respuestas_encuesta` (
  `id_respuesta` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_encuesta` int(10) unsigned NOT NULL,
  `id_usuario` int(10) unsigned DEFAULT NULL,
  `fecha_respuesta` datetime NOT NULL DEFAULT current_timestamp(),
  `ip_respuesta` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id_respuesta`),
  KEY `idx_respuestas_encuesta` (`id_encuesta`),
  KEY `idx_respuestas_usuario` (`id_usuario`),
  KEY `idx_respuestas_fecha` (`fecha_respuesta`),
  CONSTRAINT `fk_respuestas_encuesta` FOREIGN KEY (`id_encuesta`) REFERENCES `encuestas` (`id_encuesta`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_respuestas_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `respuestas_encuesta`
--

LOCK TABLES `respuestas_encuesta` WRITE;
/*!40000 ALTER TABLE `respuestas_encuesta` DISABLE KEYS */;
INSERT INTO `respuestas_encuesta` VALUES (1,1,4,'2026-09-03 12:08:40','::1'),(2,2,6,'2026-09-07 11:31:32','::1'),(3,1,6,'2026-09-07 11:31:45','::1'),(4,3,6,'2026-11-04 08:08:14','::1');
/*!40000 ALTER TABLE `respuestas_encuesta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `resultados_gamificacion`
--

DROP TABLE IF EXISTS `resultados_gamificacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `resultados_gamificacion` (
  `id_resultado` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_usuario` int(10) unsigned NOT NULL,
  `id_actividad` int(10) unsigned NOT NULL,
  `puntaje_obtenido` int(10) unsigned NOT NULL DEFAULT 0,
  `aciertos` smallint(5) unsigned NOT NULL DEFAULT 0,
  `errores` smallint(5) unsigned NOT NULL DEFAULT 0,
  `porcentaje_acierto` decimal(5,2) DEFAULT NULL,
  `fecha_resultado` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_resultado`),
  KEY `idx_resultados_usuario` (`id_usuario`),
  KEY `idx_resultados_actividad` (`id_actividad`),
  KEY `idx_resultados_fecha` (`fecha_resultado`),
  CONSTRAINT `fk_resultados_actividad` FOREIGN KEY (`id_actividad`) REFERENCES `actividades_gamificacion` (`id_actividad`) ON UPDATE CASCADE,
  CONSTRAINT `fk_resultados_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `CONSTRAINT_1` CHECK (`porcentaje_acierto` is null or `porcentaje_acierto` between 0 and 100)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `resultados_gamificacion`
--

LOCK TABLES `resultados_gamificacion` WRITE;
/*!40000 ALTER TABLE `resultados_gamificacion` DISABLE KEYS */;
INSERT INTO `resultados_gamificacion` VALUES (1,6,2,90,9,1,90.00,'2026-09-15 15:15:25'),(2,6,1,100,12,0,100.00,'2026-09-15 15:18:12'),(3,4,4,28,6,9,28.00,'2026-09-15 17:40:55'),(4,4,5,80,10,2,80.00,'2026-09-15 17:42:07'),(5,4,1,100,12,0,100.00,'2026-09-15 17:51:54'),(6,4,4,28,6,9,28.00,'2026-09-15 17:54:00'),(7,4,5,100,10,0,100.00,'2026-09-15 17:55:16'),(8,4,2,80,8,2,80.00,'2026-09-15 17:58:06'),(9,4,1,100,12,0,100.00,'2026-09-21 10:29:52'),(10,4,2,100,10,0,100.00,'2026-09-21 10:32:22'),(11,4,4,28,6,9,28.00,'2026-09-21 10:33:35'),(12,4,5,100,10,0,100.00,'2026-09-21 10:34:05'),(13,4,6,53,8,7,53.33,'2026-09-21 10:40:48'),(14,4,7,100,8,0,100.00,'2026-09-21 10:43:29'),(15,4,6,20,3,12,20.00,'2026-10-31 08:35:05'),(16,6,1,69,9,4,69.23,'2026-11-04 22:04:24'),(17,6,2,70,7,3,70.00,'2026-11-04 22:05:23'),(18,6,5,100,10,0,100.00,'2026-11-04 22:06:35'),(19,6,6,13,2,13,13.33,'2026-11-04 22:07:01'),(20,6,7,38,2,6,38.00,'2026-11-04 22:07:31');
/*!40000 ALTER TABLE `resultados_gamificacion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id_rol` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `nombre_rol` varchar(30) NOT NULL,
  `descripcion` varchar(150) DEFAULT NULL,
  `estado` enum('activo','inactivo') NOT NULL DEFAULT 'activo',
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_rol`),
  UNIQUE KEY `nombre_rol` (`nombre_rol`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Administrador','Usuario con acceso completo al sistema','activo','2026-08-25 11:37:00'),(2,'Coordinador','Usuario encargado de gestionar información operativa','activo','2026-08-25 11:37:00'),(3,'Ciudadano','Usuario que consulta servicios, agenda citas y usa módulos ciudadanos','activo','2026-08-25 11:37:00');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sesiones_usuario`
--

DROP TABLE IF EXISTS `sesiones_usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sesiones_usuario` (
  `id_sesion` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_usuario` int(10) unsigned NOT NULL,
  `token_sesion` varchar(255) NOT NULL,
  `ip_acceso` varchar(45) DEFAULT NULL,
  `navegador` varchar(180) DEFAULT NULL,
  `fecha_inicio` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_ultima_actividad` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_cierre` datetime DEFAULT NULL,
  `motivo_cierre` enum('manual','inactividad','expirada','seguridad') DEFAULT NULL,
  `estado` enum('activa','cerrada') NOT NULL DEFAULT 'activa',
  PRIMARY KEY (`id_sesion`),
  UNIQUE KEY `token_sesion` (`token_sesion`),
  KEY `idx_sesiones_usuario` (`id_usuario`),
  KEY `idx_sesiones_estado` (`estado`),
  CONSTRAINT `fk_sesiones_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=262 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sesiones_usuario`
--

LOCK TABLES `sesiones_usuario` WRITE;
/*!40000 ALTER TABLE `sesiones_usuario` DISABLE KEYS */;
INSERT INTO `sesiones_usuario` VALUES (1,1,'94c6693d495cf8a2b73438da0d9ae688298e91d814fc8fdf34cf1dcfe640362f','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36','2026-08-25 11:37:20','2026-08-25 11:37:20','2026-08-25 11:41:07','manual','cerrada'),(2,4,'65cef5d528b06db49a82d7a3c318de5d86b2855ace6dba3c4fd1c9ae90086581','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36','2026-08-25 11:41:13','2026-08-25 11:41:13','2026-08-25 11:56:14','manual','cerrada'),(3,4,'b446fbff7e90d4cb4876bf9cbf3bf44cc2bf4a69ff1d2cecbfe97313a42037e8','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36','2026-08-25 12:28:15','2026-08-25 12:28:15','2026-08-25 12:29:21','manual','cerrada'),(4,5,'5bf117d9caedd9d606ed36fb10886d4704bcbbcc575b2d1e42405f98492124b7','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36','2026-08-25 12:29:28','2026-08-25 12:29:28','2026-08-25 12:29:42','manual','cerrada'),(5,5,'66f910da92338fe94823cbc8f1076abbd4e7b13cd105f3cae356fdd74dbd5390','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36','2026-08-25 12:29:55','2026-08-25 12:50:49','2026-08-25 12:51:23','manual','cerrada'),(6,4,'383b04fc5dae76df2eaa26baa0dd9ccaf5fee846ecb29329027d012bca14a625','192.168.1.65','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Mobile Safari/537.36','2026-08-25 12:33:24','2026-08-25 12:33:24',NULL,NULL,'activa'),(7,4,'723f16155c4a93d4f112447b1dcedb4b24d5873887435a320fe2303432a01818','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36','2026-08-25 12:58:27','2026-08-25 12:58:27','2026-08-25 12:58:45','manual','cerrada'),(8,4,'180412d884e4bd905de3185f5748ef27f2411f26d8e2fbb93a9030cbb7447cf6','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36','2026-08-26 11:25:45','2026-08-26 11:25:45','2026-08-26 11:25:49','manual','cerrada'),(9,3,'61abea3f58a9704e955e158b3e434f00fcdb2e8ddbd80c7085ca5da1eee3b000','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36','2026-08-26 11:26:08','2026-08-26 11:26:08','2026-08-26 11:26:16','manual','cerrada'),(10,4,'bcc4acf46d6fbc423ffb13d892c3a8e658204887ec059fd8979a8080bf75719f','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36','2026-08-26 11:27:03','2026-08-26 11:27:03','2026-08-26 11:27:08','manual','cerrada'),(11,4,'4eec443b676e88b43985f42ee8abde1680f7410a5bd718decc65e36ab2337149','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','2026-08-28 19:13:59','2026-08-28 19:15:37','2026-08-28 19:15:37','manual','cerrada'),(12,3,'d1c28f36b88bd8dbb545261eb27d134896aa50918ff6f371e72ebdce6bf74d74','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','2026-08-28 19:15:56','2026-08-28 19:17:42','2026-08-28 19:17:42','manual','cerrada'),(13,5,'3db457329ac765daf5d65243464fdbd8b006799d4905897562da540b50e2f242','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','2026-08-28 19:22:21','2026-08-28 19:22:21',NULL,NULL,'activa'),(14,4,'1bd770832f8bb240a3778ad69f867c28337f7d24efc54fd3832b7eb8456cd747','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','2026-08-29 09:08:56','2026-08-29 09:16:57','2026-08-29 09:16:57','manual','cerrada'),(15,4,'eade9a4045405822bd22039785216bc98e8acd116fd40656997313f9d9162166','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 Edg/151.0.0.0','2026-08-29 09:09:50','2026-08-29 09:11:57',NULL,NULL,'activa'),(16,4,'14d5ce316841b896fa7f6ac5ee4394a17e79bbafcab57eb5f75d6e7cb28d1ea2','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','2026-08-29 09:17:14','2026-08-29 09:17:14','2026-08-29 09:17:30','manual','cerrada'),(17,4,'5f47b8b560bec1b5a9d371b3d0f4e68320ce7dfe583471c4bad8b70b5a4dd3ff','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','2026-08-29 09:17:49','2026-08-29 09:19:04','2026-08-29 09:19:44','manual','cerrada'),(18,6,'fbde2aa0f6d154bd5ded7c33ee691f4522793b4537c876870fbc4e7cdad77117','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','2026-08-29 09:19:56','2026-08-29 09:19:56','2026-08-29 09:20:01','manual','cerrada'),(19,4,'7b0ff0c4895c1c43fa33eca8e855717e609516e425504d0ef610734e8ed9abcf','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','2026-08-29 09:20:08','2026-08-29 09:21:35','2026-08-29 09:21:35','manual','cerrada'),(20,4,'d73ac14e89871902a75230f5a5f8374e5d87effb4661f3bdc7bab881bc4907cb','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','2026-08-29 09:22:35','2026-08-29 09:26:10','2026-08-29 09:26:19','manual','cerrada'),(21,5,'a1da2b989501603042af9784ae6143fd09cc048c24c803380717666f5af8f5c9','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','2026-08-29 09:26:43','2026-08-29 09:26:43','2026-08-29 09:27:19','manual','cerrada'),(22,4,'31ee7f1bf43f82d80f8f80d9e230e5e6ae012101a1efcc88df87cbbfbade4eab','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','2026-08-29 09:27:35','2026-08-29 09:27:35','2026-08-29 09:27:50','manual','cerrada'),(23,4,'5be5a99a8a68857d8a66a71a44846d96ad6f622ef91b863664cdc67b1f1c394c','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','2026-08-29 09:29:04','2026-08-29 09:29:04','2026-08-29 09:29:30','manual','cerrada'),(24,5,'257d87b285e615d2249dd6fa74b0d6797ac4e3334dea474f69195a97bf3542fd','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','2026-08-29 09:29:37','2026-08-29 09:29:37','2026-08-29 09:29:43','manual','cerrada'),(25,4,'cc3df116244fe64a6558e6cfe9dc9e7b42516aea4a97741d0165a2324c7e90eb','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','2026-08-29 09:29:50','2026-08-29 09:30:52','2026-08-29 09:30:52','manual','cerrada'),(26,4,'ea763146826f00ae50f9096847c189d332e1ad1c95fc06c78a7ec9547be22ac0','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','2026-08-29 11:36:32','2026-08-29 12:04:19',NULL,NULL,'activa'),(27,4,'b6e89849847e960937af861e106be8afa54493167fe9b00ff911562f67604a21','192.168.1.74','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Mobile Safari/537.36','2026-08-29 11:41:46','2026-08-29 12:02:04',NULL,NULL,'activa'),(28,4,'9c98f0cbeb0bc6a817f4676948dd0164d5766055ccb357949fcf9d547a437042','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','2026-08-29 15:20:44','2026-08-29 15:50:49',NULL,NULL,'activa'),(29,4,'c67481725008d21d928fadc1888f2f10eaaaff64928af843cbdf179ce72cf06c','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 Edg/151.0.0.0','2026-08-29 15:26:53','2026-08-29 15:29:07',NULL,NULL,'activa'),(30,4,'0226fe755573d4de5b3b52a2fd7681cba25deacd94ba1d2e8314a0e9b179ddb7','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','2026-08-29 18:36:16','2026-08-29 18:36:16',NULL,NULL,'activa'),(31,4,'d985bf013c0cfacfeac7bd907e46385d5c2eaaa7c38d4da09e1b4e81b14f3382','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','2026-08-31 08:27:30','2026-08-31 08:59:48','2026-08-31 08:59:51','manual','cerrada'),(32,6,'a943e639983799636c82d417f303c68c3b5ade9f6fb3bbf933361a87cf8daf0d','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','2026-08-31 09:00:01','2026-08-31 09:01:02','2026-08-31 09:01:02','manual','cerrada'),(33,4,'b13a8f4733cecaeb98390264e73a8cdfffc51f4685c60b2755fbd954207a1e25','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','2026-08-31 09:01:07','2026-08-31 09:39:19','2026-08-31 09:39:29','manual','cerrada'),(34,6,'f132edaa7484c6a8ac69ed9999cf3383865b5947e7e0303b151044983d9a2298','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','2026-08-31 09:39:36','2026-08-31 09:39:36','2026-08-31 09:39:51','manual','cerrada'),(35,4,'a3e9bf7e589efb838f7849305af8b4d12bf3619b0895e8b95d88375e2eb4ec84','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','2026-08-31 09:40:26','2026-08-31 09:40:26','2026-08-31 12:19:13','inactividad','cerrada'),(36,4,'3cf65874de28dcf73a74d8bd3fb7708bfe765832b3f3af31a76d675a0bd41bdf','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','2026-08-31 12:19:21','2026-08-31 12:20:25',NULL,NULL,'activa'),(37,4,'6caf16697212974c09e9955d161f4b497bcd501371bfc5ba81743745a3ce5aa1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','2026-08-31 13:45:23','2026-08-31 14:20:29','2026-08-31 19:29:38','inactividad','cerrada'),(38,4,'b3a33f46a93a46f9013e3d23ff17ba5f67024c2831cb86dc8eae36160fd23820','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','2026-08-31 19:29:43','2026-08-31 20:12:15',NULL,NULL,'activa'),(39,4,'64d41836bd470214b25a17587ad7e0193ea7bf9864274eb3c244d96c88779f25','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0','2026-08-31 19:56:08','2026-08-31 19:56:08',NULL,NULL,'activa'),(40,4,'e6fce9ff9ab1ed328b9e5e9387c17bd32f2853eb135ee1750e765f665adfe406','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','2026-09-01 18:05:57','2026-09-01 18:48:21','2026-09-01 18:48:21','manual','cerrada'),(41,4,'14f4e8fad156dcb960415d39d4463c96019101744952535cba51ab011a4432e9','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0','2026-09-01 18:47:58','2026-09-01 18:47:58',NULL,NULL,'activa'),(42,5,'9e245975233f9e3bef6303e51050e5626082b15111c1be7072acf23e8e0cd43d','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','2026-09-01 18:48:33','2026-09-01 18:48:33','2026-09-01 19:19:03','inactividad','cerrada'),(43,4,'a0f88fae679031e481c08e7676f33c93c1d5dbaac7e97e48a481d535c916a926','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','2026-09-01 19:19:14','2026-09-01 19:26:40','2026-09-01 19:26:56','manual','cerrada'),(44,4,'72729341ff4405f0cd131385704077fa2875573f7098374413e97e50d7654c96','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','2026-09-01 19:28:07','2026-09-01 19:34:21',NULL,NULL,'activa'),(45,4,'3a125a9976895fb216da61adfaf488d83559b5007bfb4308a808b9f36d0c9958','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','2026-09-01 19:33:57','2026-09-01 19:33:57',NULL,NULL,'activa'),(46,4,'695d35b89abbc3b1bee97e938d81044a5c296b1340b3927fa3d27406087d5195','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','2026-09-01 19:38:32','2026-09-01 20:08:33',NULL,NULL,'activa'),(47,4,'d1750f5458431e081108db39bd7ca9c21be358e79b94260a99568fad40f4b4b3','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','2026-09-03 10:59:59','2026-09-03 10:59:59','2026-09-03 11:00:19','manual','cerrada'),(48,6,'01bacd2b204652c605ee619e4b6b6c4203bfe41625daae5edd40b6f80e3362d0','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','2026-09-03 11:02:23','2026-09-03 11:02:23','2026-09-03 11:03:21','manual','cerrada'),(49,4,'cefc202b97f2a08804de8c6b81a370b0783be7ad7e4eb17ac9745057896afd9c','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','2026-09-03 11:58:51','2026-09-03 12:19:39','2026-09-03 12:20:32','manual','cerrada'),(50,6,'e08be51c229c13ca82f5389b4fe47ca4c50a6ad31ebc398bca29fee2b0ffcb70','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','2026-09-03 12:20:49','2026-09-03 12:27:28','2026-09-03 12:27:32','manual','cerrada'),(51,4,'63e97f7c923a71bb26e8cc28adf33833c95eae65f4963518df9029b3c9df79b2','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','2026-09-03 12:27:36','2026-09-03 12:29:33','2026-09-03 12:29:46','manual','cerrada'),(52,6,'47f4408271f3aa4092352ad8880c8c0405fa7b4567c103d3d26d56b9e80038af','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','2026-09-03 12:29:54','2026-09-03 12:46:59','2026-09-03 12:46:59','manual','cerrada'),(53,4,'d505584ccc28d1532093512595b7dfe0dc26f8ab6cecd6a9f43e7c6da996e18c','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','2026-09-03 12:47:04','2026-09-03 12:47:04',NULL,NULL,'activa'),(54,4,'ad1fd8ab96830838ee89c2ce363f43af0aa18b7a63f3f58fae32fb944b94827e','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','2026-09-05 19:35:53','2026-09-05 19:41:37','2026-09-05 20:14:50','inactividad','cerrada'),(55,4,'badc518f4ea8ac42b81228dfbf64d7282e9829d5d6fd7327da067b9604f90758','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','2026-09-05 20:14:56','2026-09-05 20:21:03','2026-09-05 21:03:04','inactividad','cerrada'),(56,4,'0e0a8ea0cce8fea8f344e38bd4f995bc126a043ad6fac9bfc54da87c5972804e','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','2026-09-05 21:03:11','2026-09-05 21:03:11',NULL,NULL,'activa'),(57,4,'83948fe618cf7fc0ef11d3308c958ec0e61b70bdcad79daafdd97e9d84d7b501','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','2026-09-07 10:42:18','2026-09-07 10:42:18','2026-09-07 11:24:35','inactividad','cerrada'),(58,4,'7e021a97ef54947244cec8561e55bd4043562e5c8b0da3ee0968a7fd7a831b75','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','2026-09-07 11:24:58','2026-09-07 11:27:00','2026-09-07 11:27:13','manual','cerrada'),(59,6,'5692953f18f8962aad581a2f07080dc516856e33b2c8ce13496d5ebbe101685e','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','2026-09-07 11:27:24','2026-09-07 11:27:24','2026-09-07 11:27:35','manual','cerrada'),(60,4,'29c9bef28132f178e3f28700bfad50864f048f07b843bb16e1c1602f61af7e1a','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','2026-09-07 11:27:40','2026-09-07 11:28:54','2026-09-07 11:29:01','manual','cerrada'),(61,6,'42c367995e05b6da27bd8128b5319bc55a9a606740f2b7efbf6e425fdaeb7285','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','2026-09-07 11:29:11','2026-09-07 11:31:11','2026-09-07 11:31:51','manual','cerrada'),(62,4,'80ff3459adbd10a0a6083b75e00c4fc58355e046d81c21d7df7b1f48e2eb3756','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','2026-09-07 11:31:57','2026-09-07 11:34:07','2026-09-07 11:34:18','manual','cerrada'),(63,4,'5c585726a95f17705dbfb63f3dd9da3bdb53a70632fec99e55a838bb1c8d3acb','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','2026-09-08 17:09:16','2026-09-08 17:52:24','2026-09-08 20:17:03','inactividad','cerrada'),(64,4,'baddae88f03f67e71f725d24ceea6c1ed2a536fe89cd7c9e2171b2eabbf1d6ee','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','2026-09-10 10:29:08','2026-09-10 10:47:13',NULL,NULL,'activa'),(65,4,'0b9984306140b58bd50add17becc145ab032d2d425badfdc0a3a05d177df2b10','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','2026-09-10 21:21:02','2026-09-10 21:21:02','2026-09-10 21:21:25','manual','cerrada'),(66,6,'abfd105d5507873cae0ccabe128deef0df587be4a461ed4f792d6057ab217312','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','2026-09-10 21:21:32','2026-09-10 21:21:32','2026-09-10 21:22:18','manual','cerrada'),(67,4,'38310959d738381b9d5c2ae8ea4c7a8e0f04b3f5c28806a961e001833350a175','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','2026-09-10 21:22:23','2026-09-10 21:22:23','2026-09-10 21:22:38','manual','cerrada'),(68,4,'850050e886d8ce821510ba625ac11b5afd17e46828667d70f9577aeb419c098f','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','2026-09-14 10:17:09','2026-09-14 10:20:57','2026-09-14 10:21:34','manual','cerrada'),(69,5,'ddaa1fb8d07837bb5b01defa3bb86741a2a918d3a75a3fddd4f4e63d53ebf611','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','2026-09-14 10:21:43','2026-09-14 10:28:09','2026-09-14 10:28:49','manual','cerrada'),(70,6,'8c1d60d58229fdfa4d64fbfab09c539f690911ce10f9505d023bfc38af615d2f','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','2026-09-14 10:28:58','2026-09-14 10:31:00','2026-09-14 10:31:00','manual','cerrada'),(71,4,'43da0b9bee50fed050870bca8e2fe42d883d7e16e29a246dddbd99d945964564','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','2026-09-14 10:31:26','2026-09-14 10:32:27','2026-09-14 10:32:30','manual','cerrada'),(72,6,'c325730cc657c8bf6340e9b9f33efa4a6e574c435a89e7ed52b31202d1ee7a0f','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','2026-09-14 10:32:37','2026-09-14 10:32:37','2026-10-24 08:06:19','seguridad','cerrada'),(73,4,'88f5c26a72037afa60eca775ac70b9dea7cbaa88224c4c455d0ad81780127d2f','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0','2026-09-14 10:35:42','2026-09-14 11:06:57',NULL,NULL,'activa'),(74,4,'4532efaa065e3277ba727faf6848b8e52638b000924050a847dfdeb2c715ff5e','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','2026-09-15 09:37:31','2026-09-15 09:37:31','2026-09-15 09:38:00','manual','cerrada'),(75,6,'4367fcdae8be619a56a23b7286dc5ca7f1eb332e0d33aad5641e788d244903c0','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','2026-09-15 09:38:13','2026-09-15 09:38:13','2026-09-15 09:38:53','manual','cerrada'),(76,4,'db9fffe685c06f16c6eb627a20acf20eb9d809a57a87f9abd1ad9c90926c87ee','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','2026-09-15 09:38:59','2026-09-15 09:41:56',NULL,NULL,'activa'),(77,4,'08aa0f08f53ac3b0c8a7cd8a999d34d26eeaaa5cb4e49d7cf64c93e191aa1731','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','2026-09-15 15:06:38','2026-09-15 15:10:23','2026-09-15 15:10:32','manual','cerrada'),(78,6,'7221163267fe4c2c58383eeb6987846c5f492368fd2a4363043e35c0c65e35fc','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','2026-09-15 15:10:39','2026-09-15 15:19:28','2026-10-24 08:06:19','seguridad','cerrada'),(79,4,'bb6fe0903837248a7e6f06feadbf6848702991dbdcf7ac381c56fd6a561bacc5','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','2026-09-15 17:22:33','2026-09-15 18:07:46','2026-09-16 08:02:43','inactividad','cerrada'),(80,4,'b2a0aaa80c49b4b8c1ba1357e39686163969dabf122e9606a9673b052fc3766d','192.168.1.66','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Mobile Safari/537.36','2026-09-15 17:44:41','2026-09-15 17:59:43',NULL,NULL,'activa'),(81,4,'b43d33c90d504026abf690f4aa1258d9d1a6d71238d236f8b2b207c2b77a2e18','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36','2026-09-16 08:02:48','2026-09-16 08:05:26',NULL,NULL,'activa'),(82,4,'fc6a92540b2112275839f903c614c292837ab3d4018a23a4e8c0b9c92d36899c','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-21 10:23:06','2026-09-21 11:31:46',NULL,NULL,'activa'),(83,4,'bcf3b4e801c18f3b2f982227530b6974cb2c25d681d9e57f3dbbf8572c35f225','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-22 08:52:56','2026-09-22 08:52:56','2026-09-22 11:37:21','inactividad','cerrada'),(84,4,'03ed9bc76c3c04db4d41e1ba11f26faabff101f822243c205c916033bc181d40','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-22 11:37:25','2026-09-22 11:37:25','2026-09-22 12:33:40','inactividad','cerrada'),(85,4,'62710ec808fe99e9df3883ab28aaede2cb0fa363f0208ee6ffbe1c6548b03f60','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-22 12:33:46','2026-09-22 12:33:46','2026-09-22 18:57:03','inactividad','cerrada'),(86,4,'90d4cbf3b4f2554e7a648fbda0419300e5d8d9c024d3a657785b6c15c9ab0896','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-22 18:57:07','2026-09-22 19:09:32','2026-09-22 19:10:21','manual','cerrada'),(87,5,'d1ce9036e33f60bd3f3be3362f72b7a3c1874638f5a134cd1bf9fb686bf4d3f6','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-22 19:10:34','2026-09-22 19:10:34','2026-09-22 19:10:57','manual','cerrada'),(88,4,'92215b4c79f7975ef5260b21c75db7c451e268449acf5145a0c34efbcedd72ae','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36','2026-09-22 19:11:02','2026-09-22 19:11:02','2026-09-22 19:11:20','manual','cerrada'),(89,4,'879049a10b8e984bfce64fd95ff806019ad36dacb7583cd9c89c9d2753597204','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-09-24 09:01:55','2026-09-24 09:01:55','2026-09-24 09:01:59','manual','cerrada'),(90,4,'5d0b0f2185ac6c28e36ffc3a735b7b34760c2e31b4b9cbab37571f2a843893cd','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-09-24 09:02:12','2026-09-24 09:02:12','2026-09-24 09:02:20','manual','cerrada'),(91,5,'1358ae497fa52311887b0d43b2131c9ec64db9ed7f140698148de0d591edad0c','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-09-24 09:02:25','2026-09-24 09:02:25','2026-09-24 09:03:05','manual','cerrada'),(92,4,'9d8c4b2bd5d8c4d97fdf6a9108ecc62c1e9dbe905c53582f5024c3fec6d54bdd','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-09-24 09:03:11','2026-09-24 09:04:18','2026-09-24 09:04:18','manual','cerrada'),(93,5,'22b11b4a1ec170d3d228bc0f289ba0badc57697e2989b5b053f6d39d05e3d837','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-09-24 09:04:23','2026-09-24 09:05:38',NULL,NULL,'activa'),(94,4,'d4cd7020c28ff7996c56d8ee45d146e6d5cf0b99f2028fb9389d9e9267c7d11b','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-01 09:25:36','2026-10-01 09:29:12','2026-10-01 09:29:12','manual','cerrada'),(95,5,'2d31a41488d0c745522cda4aa442f078c583bde4071c7eb47ae215d99e1561b7','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-01 09:29:19','2026-10-01 09:29:19','2026-10-01 09:30:07','manual','cerrada'),(96,4,'19d37e9118e5d2d6384e5b2cfebc4eebe6d0ed2c7e0341946489e6b68fb3d80a','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-01 09:30:15','2026-10-01 09:30:15','2026-10-01 09:30:36','manual','cerrada'),(97,6,'34890e714d8adfa45e193b18ee33fa2f7d80e3b14e91a0333a3fbfc2bc6d2e9a','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-01 09:30:43','2026-10-01 09:30:43','2026-10-01 09:31:41','manual','cerrada'),(98,5,'936c60ad225ebbd56b0437ebe0e6e84520e18e2f10a554b74a120e7b979c24b9','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-01 09:31:46','2026-10-01 09:34:54','2026-10-01 09:35:14','manual','cerrada'),(99,6,'0e10db45a43aa5633b32abe961c2bf14903100e136ac4623eab3021c7b77d48b','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-01 09:35:23','2026-10-01 09:36:44','2026-10-01 09:36:44','manual','cerrada'),(100,4,'5799b3a0e78cc6c77f080ebc56d076b4f7a8a4c67b589342a83e9daee379f25f','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-01 09:36:49','2026-10-01 09:37:52','2026-10-01 09:37:52','manual','cerrada'),(101,5,'fc70b299138850bd735b8e63c0980fa6fe84cb9b33491752f3e0ddcb7b831f9e','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-01 13:02:30','2026-10-01 13:02:30','2026-10-01 13:03:21','manual','cerrada'),(102,4,'f0396e3e9d7b8d7155e53e801905e3d7822b71ddeb3cb11729ba1b52f4a50e42','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-01 13:03:44','2026-10-01 13:03:44','2026-10-01 13:04:14','manual','cerrada'),(103,2,'edbb345b1fcb2975d15c265e6ca937d717c652a7ed191974b49982560f5127be','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-01 13:04:32','2026-10-01 13:04:32','2026-10-01 13:04:49','manual','cerrada'),(104,5,'54690625f35bfdd7f73ef864f1c03c2bbf8d6cbab6b33862fd6b6f0464db1302','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-01 13:05:11','2026-10-01 13:05:11',NULL,NULL,'activa'),(105,4,'537c016db3984393b3e3f04fc4daf8300cd6dcc3427317cb4b408f3ea42537d9','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-02 19:12:16','2026-10-02 19:17:20','2026-10-02 19:18:01','manual','cerrada'),(106,6,'60015ebeb3571306cd4c473a56a202119a3fe7f9636f76a38d7e700b7cc5a78b','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-02 19:18:07','2026-10-02 19:46:49','2026-10-02 19:46:49','manual','cerrada'),(107,4,'9ede273d75f793930897f022fac2a9098fbe8d6e9fb6c6513d1068783005e023','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-02 19:46:54','2026-10-02 19:46:54','2026-10-02 19:47:07','manual','cerrada'),(108,6,'543946c70e3317ad416eb3409f3caf39b08281bd8659ea22be8a72b26c7773e1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-02 19:47:15','2026-10-02 19:48:44','2026-10-02 19:48:55','manual','cerrada'),(109,4,'5d174baf372ab74a03b0cc36d257bf820771e756372fbb8efc6b29f22bfb5079','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-02 19:49:00','2026-10-02 19:49:00','2026-10-02 19:49:08','manual','cerrada'),(110,3,'d025bd3f92cf21d3e43122b5593c200871d7b4553df350a8f6095fef509fa054','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-02 19:49:20','2026-10-02 19:51:19','2026-10-02 19:51:24','manual','cerrada'),(111,4,'eaeba5199fc91b574a1166ef2ad84a36eaaf8dfc7c4cfed7b96fad934405eb6d','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-02 19:51:38','2026-10-02 19:53:33','2026-10-02 19:53:35','manual','cerrada'),(112,2,'e0cf6e4cf7cc1f676c2bb46140475aac52cd8a203e431ee394e8eee7a21f071f','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-02 19:53:56','2026-10-02 19:53:56','2026-10-02 19:54:18','manual','cerrada'),(113,5,'b4349e863c3e309dc516b6ceacb2d7a3ee597844e5cb3982a3fb7823470e2c0c','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','2026-10-02 19:54:24','2026-10-02 19:54:24',NULL,NULL,'activa'),(114,4,'9b9af4fbbbce9ed6bb49b031340cd3ae90ab8cd39bf02fde5e9d41b6e2c37836','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','2026-10-09 13:28:17','2026-10-09 13:30:29','2026-10-09 13:31:12','manual','cerrada'),(115,6,'7172bbda1d8b8b97b2f30ac2e70cfa5effa3e6bd59eb9e192791c2bac578dd09','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','2026-10-09 13:31:24','2026-10-09 13:31:24','2026-10-24 08:06:19','seguridad','cerrada'),(116,5,'e6e6ec6f49bb2eb122cf3534fbdc26da429df016bebbba1b7d0a0e4e822995d3','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','2026-10-09 17:02:00','2026-10-09 17:02:00','2026-10-09 17:02:31','manual','cerrada'),(117,6,'e2e4aaffb22ffbe7b73cda2eb728a23960ae8f4af37f73abef7a29685f2f2391','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','2026-10-09 17:28:28','2026-10-09 17:28:28','2026-10-09 17:29:05','manual','cerrada'),(118,5,'a517d6c247ee0674ee8cc4c5ad87af602d8a0bce09cedb56b517ee0f3065d6d0','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','2026-10-09 17:29:10','2026-10-09 17:38:46','2026-10-09 17:39:03','manual','cerrada'),(119,6,'37abe8ce5646cd5370176224c4a4991c5de737713cefeb93b45d8a8f97469ab4','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','2026-10-09 17:39:12','2026-10-09 17:39:12','2026-10-09 17:39:30','manual','cerrada'),(120,5,'febdafb61619ea52e483567b24b19ef6ed3b0214dd271b1675277528effb5d85','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','2026-10-09 17:39:53','2026-10-09 17:39:53','2026-10-09 17:40:20','manual','cerrada'),(121,6,'d2b0f3beb5e6a51e0c358c5049e5b2a1d4d40d100d390c045a7112219a2bc0b7','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','2026-10-09 17:40:26','2026-10-09 17:43:31','2026-10-09 17:43:36','manual','cerrada'),(122,4,'b4b18638a015642af2e0b6f6ef0986d66ca1987917d9ccc2ab7d0bd02c113eff','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','2026-10-09 17:43:44','2026-10-09 17:46:23','2026-10-09 17:46:48','manual','cerrada'),(123,4,'3575e4fa2f85bf3fa3af4cdea88ed98ab88537dd0942c5cf2b928213aa533367','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','2026-10-09 20:23:26','2026-10-09 20:24:59',NULL,NULL,'activa'),(124,4,'5642847742120f8b7ba2473618d7ca6364f9e97434f215c63b5eac4b4b1d46a7','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','2026-10-10 20:06:20','2026-10-10 20:35:12',NULL,NULL,'activa'),(125,4,'425a8e8a9650d9183f9e6dcfd2e004d66cc6b419eae20c7e695c16d3debeb9db','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','2026-10-16 10:30:20','2026-10-16 10:45:13','2026-10-16 12:01:45','inactividad','cerrada'),(126,4,'f389e202dce3b525c298672e2180a48eed7f00acf34572704a30d6e8d416a51e','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','2026-10-16 12:07:26','2026-10-16 12:07:26','2026-10-17 07:31:52','inactividad','cerrada'),(127,4,'052ce70cd3293cbff73c068ba0269f894841db980cc7b4d28c343acbb880cf99','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','2026-10-17 07:31:58','2026-10-17 07:31:58','2026-10-17 07:32:33','manual','cerrada'),(128,5,'c09e3712c602cc59d73037de7578d1a7f9d59518d2a7eb5ffc294c4e8610176a','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','2026-10-17 07:32:38','2026-10-17 07:32:38','2026-10-17 07:33:25','manual','cerrada'),(129,4,'d1b38a74e77112a8c591b69d325652baa30427a200b5419e5961f59deed26bd2','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','2026-10-17 07:33:31','2026-10-17 07:33:31','2026-10-17 09:13:03','inactividad','cerrada'),(130,4,'0bc834206495559226cd7142991195fa98a2232354c64e9657e1d25371e0b56a','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','2026-10-17 09:13:10','2026-10-17 09:14:39','2026-10-17 09:14:39','manual','cerrada'),(131,5,'135b2e3911fa8c2bebe117ec61d5562a5f31e5b33db6fc541a8aa088e778b9d2','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','2026-10-17 09:14:46','2026-10-17 09:16:15','2026-10-17 09:16:19','manual','cerrada'),(132,4,'98918379ce787bd748fef314b85d7234b5696e89215466f7b2a48ebbd50fbd3d','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','2026-10-17 09:16:31','2026-10-17 09:16:31','2026-10-17 09:16:59','manual','cerrada'),(133,6,'2a87f067e8224fd4d27b5cd4c17715abff28ec348aab4c1382e79c1e12b7b654','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','2026-10-17 09:17:06','2026-10-17 09:17:06','2026-10-17 09:17:22','manual','cerrada'),(134,5,'6263ef063da86f793c4bf32f02fffa8be8bad946355c01afa987bc3ff1d25de7','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','2026-10-17 09:17:52','2026-10-17 09:23:10',NULL,NULL,'activa'),(135,6,'04c18b2dc09ce5bd0206225ebcf58be484deea9dcc4211079d55bdd0413484f0','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','2026-10-24 08:05:24','2026-10-24 08:05:24','2026-10-24 08:05:28','manual','cerrada'),(136,6,'6911c71979fd455260b1d58047f84fe4b5ce6fcb8ab126b461c5271d323e37f6','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','2026-10-24 08:06:41','2026-10-24 08:42:21','2026-10-24 08:42:21','manual','cerrada'),(137,4,'6b977944c06a15e781299e99c0765f0a763888cf7aa549da81f78f3f3f7cc980','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','2026-10-24 09:45:55','2026-10-24 09:53:25','2026-10-24 09:53:25','manual','cerrada'),(138,7,'69065c2a7c85eee5513aa9f7271ae4ba83dcec32ec4b0549dbbd31f8a5f964c0','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','2026-10-24 09:53:52','2026-10-24 09:53:52','2026-10-24 09:54:45','manual','cerrada'),(139,5,'6f4378eb75e37b7d6fd2effcb7ed4f08e3cf816a194497999738656df2714967','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36','2026-10-24 09:58:05','2026-10-24 09:58:05',NULL,NULL,'activa'),(140,7,'1bfb734f004295cbea2e04317d4ec2c9723240d0ebe56aa3bfb8d57b17dbf0ae','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-10-24 18:33:26','2026-10-24 18:34:26','2026-10-24 18:34:33','manual','cerrada'),(141,5,'0c354ac3d1a63f31b8ed0dc55009b7ca199502c7abbfe1a85383262d3836441a','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-10-24 18:35:36','2026-10-24 18:35:36','2026-10-24 18:36:13','manual','cerrada'),(142,4,'50e15d351b4a459cc5c4a26aab8b85e434d7e550a1ce35b61fe1d575ac8d43e8','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-10-24 18:36:39','2026-10-24 18:53:26','2026-10-24 18:53:37','manual','cerrada'),(143,7,'632ca8a8e02d72cb86719732a7db5007baac010eba17e1846c48efcbf4adefff','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-10-24 18:54:01','2026-10-24 18:54:01','2026-10-24 18:54:29','manual','cerrada'),(144,4,'a13d4fb38db3633034642e2a3d9ca55481590c9c928f8446722520607155aebe','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-10-24 18:54:44','2026-10-24 18:55:46','2026-10-24 20:17:12','inactividad','cerrada'),(145,4,'7248a2526d7fd9249cd47cf4eed4faad3224d4f7b986e22831596b121a319efc','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-10-24 20:17:27','2026-10-24 20:17:27',NULL,NULL,'activa'),(146,4,'fe66d772928f729617075e7a6c5011282bd2394a560df2cc8f5219ee91b81d24','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-10-30 19:27:14','2026-10-30 19:27:14',NULL,NULL,'activa'),(147,4,'a96175cae2174e7ed4c29e6aaed172f4f7c45e059149a22b7eab2e6450339188','192.168.1.71','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Mobile Safari/537.36','2026-10-30 22:03:36','2026-10-30 22:08:48',NULL,NULL,'activa'),(148,4,'6a5cefc680778c2e15e2a4deb7f319e9f23bd41aa7a07d2e39c34ca5f32d189a','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-10-31 08:34:33','2026-10-31 08:48:45','2026-10-31 12:16:59','inactividad','cerrada'),(149,4,'42282593c227ce664429e9a966dd6a71b326c53a50e76eacae1811c7d6a89466','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-10-31 19:20:02','2026-10-31 19:20:02','2026-10-31 20:26:22','inactividad','cerrada'),(150,4,'26a567151f6cef96c6e1e61a7af6421231d3c3317eb6b92df275dc8daf8cbb2f','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-10-31 20:26:42','2026-10-31 20:28:26','2026-10-31 20:28:52','manual','cerrada'),(151,6,'e5c937ebb33dc71b98e046a63d6941712f989df17fb6705f6b173dfe97bb303a','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-10-31 20:31:34','2026-10-31 20:32:39','2026-10-31 20:32:52','manual','cerrada'),(152,4,'1bdd2f0caa991425d5c446b88ce6cff23d61ae192c370082f99ea2db127c4fdc','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-03 23:26:40','2026-11-03 23:30:35','2026-11-03 23:31:15','manual','cerrada'),(153,4,'bbf141f59e87f3ebdd9d46ee7b55ccaf57227c17497c015a8fbed5284f5b8ac1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-03 23:31:47','2026-11-03 23:33:41','2026-11-03 23:33:41','manual','cerrada'),(154,4,'1f5b221abafdf2e0e808416ac2780cbc3b39a069e2a23d4aa713dae7aa39d255','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-03 23:35:02','2026-11-03 23:36:34','2026-11-03 23:36:34','manual','cerrada'),(155,4,'97c8fa1c2dd84ca8059d52b3dfa9b8c912a2aab79350c7af5ac711d95e3fb2a2','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-03 23:40:01','2026-11-03 23:43:25','2026-11-03 23:43:46','manual','cerrada'),(156,9,'2fbe7c69cfbd190c8db0d1e1e40bff80c60a08bff79fe903a17359fe30813267','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-03 23:44:05','2026-11-03 23:44:05','2026-11-03 23:44:15','manual','cerrada'),(157,4,'e18b3a4d978acbe8200cf4a5e119b734dd8fe7188d30d7eb899fc9d27752ea55','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-03 23:45:53','2026-11-03 23:45:53','2026-11-03 23:46:06','manual','cerrada'),(158,4,'136743e652042eabb3ec56f11111aec099b18babc6a326cf33440c8da492c3eb','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-03 23:46:46','2026-11-03 23:50:07','2026-11-03 23:50:31','manual','cerrada'),(159,10,'0ad1b7cf334469328db2c44642ec5b2c5f9fab00c03e2d23821fbe9d30c3cceb','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-03 23:50:48','2026-11-03 23:50:48','2026-11-03 23:51:00','manual','cerrada'),(160,4,'f2f094149fbd01efd088f38bf2c598911b5a3e1070eeecc0aac8ad0bcdfccbc4','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-03 23:51:07','2026-11-03 23:51:07','2026-11-03 23:51:19','manual','cerrada'),(161,4,'44b88f985c07e57aeb7b772ab39db95012f7f845b7a2ffa1967717c679810698','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-03 23:51:48','2026-11-03 23:51:48','2026-11-03 23:51:54','manual','cerrada'),(162,10,'20db4a58bb9a6edaf8f16e0ea1d3db24b203782ded9e1c7dc34bdff9380a6977','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-03 23:52:07','2026-11-03 23:52:07','2026-11-03 23:52:09','manual','cerrada'),(163,4,'a6c5baf53e84ec55802df312f760c4fdebeac2c0d668a2ce154708a7ea0bb1d2','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-03 23:52:15','2026-11-04 00:02:15','2026-11-04 00:02:15','manual','cerrada'),(164,4,'ea7a8bc0b0ac079c23deca463d86390c1d355139c802810b919261fa2c847b9d','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-04 00:02:45','2026-11-04 00:45:32','2026-11-04 00:45:32','manual','cerrada'),(165,5,'7d8b0aabf96707aff617df271c941f1ce08cc4fd72e2cb5b96dc58adeb459a4c','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-04 00:45:45','2026-11-04 00:45:45','2026-11-04 00:45:52','manual','cerrada'),(166,4,'06bbc3a39032e76a71edfe863e0c9d6fc3f204e2e246cdf95eb1ab241aab0462','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-04 00:45:59','2026-11-04 00:51:35','2026-11-04 00:52:18','manual','cerrada'),(167,6,'da1c68288fa71369dc0101cfcd78c123f4cd9745f1c6a2f8e0f15cce0d945e54','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-04 00:52:28','2026-11-04 00:52:28','2026-11-04 00:52:49','manual','cerrada'),(168,4,'0e306a10c35febb477848f7b524a197af63d14e305d4abf5b4284514220b122d','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-04 00:52:58','2026-11-04 00:52:58','2026-11-04 00:53:27','manual','cerrada'),(169,6,'00bcaed1d3ccf393de7b3dff62320e3329d1cb230b2f0bff518d4d367c23f560','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-04 00:53:35','2026-11-04 00:53:35','2026-11-04 00:53:52','manual','cerrada'),(170,4,'239262c9748e086a7fc49f3daf87ae46ba5698ac6ad8acdda321cef7ea0b36b7','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-04 00:53:58','2026-11-04 01:30:02',NULL,NULL,'activa'),(171,4,'1682fe2d801d3f0998ff5e705a66abeb76b2a8712dce8064637a658672f271cb','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-04 07:40:16','2026-11-04 07:53:11','2026-11-04 07:53:57','manual','cerrada'),(172,6,'4b426fa1b9c4e80248b3f2b098b05371a6a14b0e69576dc1f5ca06d0b0fad616','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-04 07:54:04','2026-11-04 07:54:04','2026-11-04 07:54:22','manual','cerrada'),(173,4,'aa458e9bf753cb687cedcef3bae2dee61d12537603b2bc3ab985520feb17523d','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-04 07:54:27','2026-11-04 07:54:27','2026-11-04 07:54:37','manual','cerrada'),(174,6,'f02b41661f9d2b9fcf610ae5f8bc4a9ffd9b7c9a0da147424871c7c572d81f1b','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-04 07:54:44','2026-11-04 07:54:44','2026-11-04 07:54:55','manual','cerrada'),(175,4,'4f1e206287887b09cada2695cdfb027d4b2cfc9fa930b9ea170e18d5b2c03078','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-04 07:55:01','2026-11-04 07:55:01','2026-11-04 07:55:18','manual','cerrada'),(176,6,'ff1bf196d2f5e2fbf4682b9b3c9bb7c430684b14785157b4bc6dd455681ae0da','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-04 07:55:26','2026-11-04 07:55:26','2026-11-04 07:55:37','manual','cerrada'),(177,6,'61558e861b8c90d7fc1b49995f077c39d7d4e78eff475b3804e680b7bbe8dd37','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-04 07:56:01','2026-11-04 07:58:01','2026-11-04 07:58:19','manual','cerrada'),(178,4,'92c83cec9a56792d1aaee4d6d6c7f2b5f6492b410e0a900b3b37af28bbd104d1','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-04 07:58:24','2026-11-04 08:06:01','2026-11-04 08:06:29','manual','cerrada'),(179,6,'447cfab4cb5d8ac7c17582b942aba33412d57f7174d1acae1bc34525daf27e27','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-04 08:06:36','2026-11-04 08:06:36','2026-11-04 08:06:43','manual','cerrada'),(180,4,'75688fb06d84d4d610bdfea8b547ae513434a469d6045c38b399599d2c96267e','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-04 08:06:47','2026-11-04 08:06:47','2026-11-04 08:07:16','manual','cerrada'),(181,6,'c3d395c95c9dfd70e2dea2a616e34158533f118d55541579992a20b5c7e7adc7','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-04 08:07:21','2026-11-04 08:08:25','2026-11-04 08:08:25','manual','cerrada'),(182,4,'8879e4dc6f582bfe1d697aeed7a95873afa8183397d8159acd50fe0e3fa7c430','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-04 08:08:30','2026-11-04 08:23:16','2026-11-04 08:23:42','manual','cerrada'),(183,4,'5667d3cdc7e98606dd12692e0fda27eb1ca28fe6b94b7b2ef60aec4f80b5ae6b','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-04 08:23:47','2026-11-04 08:28:33','2026-11-04 08:29:01','manual','cerrada'),(184,9,'ee0006ae32e79b19d9eb8229c3c30859c2c55d7bb64c997f0150a986e5c3fc76','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-04 08:29:19','2026-11-04 08:29:19','2026-11-04 08:30:17','manual','cerrada'),(185,4,'cf8d382d83dfbb4db46ece232a9568b8189f17933c120e5fa997a5dcfdf10876','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-04 08:30:24','2026-11-04 08:31:25','2026-11-04 08:32:00','manual','cerrada'),(186,9,'370dd5ad6e94fc50eeef60f86007c4383b81de6c244573e9a6f2fa7a97f7956e','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-04 08:32:08','2026-11-04 08:32:08','2026-11-04 08:33:04','manual','cerrada'),(187,6,'913a48a53fcaf8609e616b8a10353239afd961df82867a7f8fff8e32e62edb78','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-04 08:33:34','2026-11-04 08:33:34','2026-11-04 08:33:45','manual','cerrada'),(188,9,'6748d9b3c6cac20736edca7ec1154cd79c9b6e41edd15703f1550e6c8b6c7a4b','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-04 08:33:55','2026-11-04 08:33:55','2026-11-04 08:34:04','manual','cerrada'),(189,4,'1fe8f7936d01ce8a461cff8dfebe8b16abafdb374450f3a251c0874f1b2696d2','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-04 08:34:10','2026-11-04 08:34:10','2026-11-04 08:34:16','manual','cerrada'),(190,4,'c2be6684182af820358242c4729ad5fc42b75f3c9728a4a839160e33b28539e2','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-04 08:36:11','2026-11-04 08:39:55','2026-11-04 08:39:55','manual','cerrada'),(191,5,'1e071695db1c4342d3ae580c76516b15f5bd8c8b2f26f62f8ab9659d8aae4a94','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-04 08:40:00','2026-11-04 08:40:00','2026-11-04 08:40:17','manual','cerrada'),(192,5,'d5ab135b32e9516be210ee976009cd2c357f21e424c80dc1e08ba18cb056f92d','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-04 08:40:40','2026-11-04 08:43:22','2026-11-04 08:43:23','manual','cerrada'),(193,9,'4ec898457abde6c71d46846200620a91c55ede85273980313bf83a33b21c447c','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-04 08:57:22','2026-11-04 08:57:22','2026-11-04 09:33:07','inactividad','cerrada'),(194,9,'e54b2cdc933df8d08e1af8bd79879c68b99bf3e35706bf42297c077130d8c8ff','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-04 09:33:29','2026-11-04 09:33:29',NULL,NULL,'activa'),(195,4,'9abdf235fbacc579d08bb1fd8e959a68b4cdddcdd8126c74eeca372b02ce8743','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-04 21:50:04','2026-11-04 21:50:04','2026-11-04 21:50:46','manual','cerrada'),(196,6,'56d6b85307df8ea0c728f3f95ee3a480e5a9049adbbfc776df3ddea52c5830b0','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-04 21:50:54','2026-11-04 21:52:26','2026-11-04 21:52:26','manual','cerrada'),(197,5,'f6ff6561c449ccf034fa5ef50dfd39773ff7b035dc9590149707ae63bdf59a03','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-04 21:53:30','2026-11-04 21:53:30','2026-11-04 21:53:40','manual','cerrada'),(198,4,'9c03fde7188419c45652e5c667fd8ca5751145638171c25f96a493294dba8a42','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-04 21:53:51','2026-11-04 21:53:51','2026-11-04 21:54:12','manual','cerrada'),(199,10,'aec8499993dad460c72057f1e98dffb516401d5b1b1bc9cf601f3fb3fcb12f18','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-04 21:54:36','2026-11-04 21:54:36','2026-11-04 21:55:03','manual','cerrada'),(200,4,'3c82fd9dda9d5ed6252bb70e23c4499b52c69fe6f1b03a36d140b7af55fdb399','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-04 21:55:10','2026-11-04 21:55:10','2026-11-04 21:55:39','manual','cerrada'),(201,6,'cf8114b09bd61ec16ea0ef4d0317837498222955f8a2e2eca8424b944a1ce596','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-04 21:55:57','2026-11-04 21:55:57','2026-11-04 21:56:27','manual','cerrada'),(202,9,'8176a537c1325f902f7ab56356bc95eeabda2604184610490746f26ea2404a8a','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-04 21:56:51','2026-11-04 22:01:29','2026-11-04 22:01:29','manual','cerrada'),(203,6,'1d1bda9da07faf88409027e7476c169df2412cabc9c5403462b8ff1983443f03','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-04 22:02:23','2026-11-04 22:07:44','2026-11-04 22:07:54','manual','cerrada'),(204,4,'da3c7dac3f43958093336f668296d894d76020eb1500edc608a55d50091cbf38','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-04 22:07:59','2026-11-04 22:20:56','2026-11-04 22:20:56','manual','cerrada'),(205,6,'580db5b469e4b910b85bfe73f622440ff4ba0c64f7616307eb3166d96a501ba3','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-04 22:21:03','2026-11-04 22:28:40','2026-11-04 22:29:22','manual','cerrada'),(206,9,'b18f1e819c313931bbf0e082e26fc5453ccbf9ed94f62817d3a56e1e0587d28b','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-04 22:29:30','2026-11-04 22:29:30','2026-11-04 22:30:00','manual','cerrada'),(207,6,'9ec74c19035f0448dc5f9b15246ae1069c8b0b6cca02d9d85140137d6dd12dd0','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-04 22:30:06','2026-11-04 22:30:06','2026-11-04 22:30:20','manual','cerrada'),(208,9,'1b6122ae90fb7036535b6077622c42243657481c55e8a1ceec4491802be52a8f','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-04 22:30:30','2026-11-04 22:30:30','2026-11-04 22:31:05','manual','cerrada'),(209,6,'29639d19ea2abdfcfb0461bcae6faaa2a660dd33759eac8f9d0e1326af8bfbc5','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-04 22:31:12','2026-11-04 22:31:12','2026-11-04 22:32:01','manual','cerrada'),(210,9,'66a33d0bf9ec1056bb7ee666ba6aa0ff06b73d7261f8675c6e63e7bbd2fd6689','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-04 22:32:13','2026-11-04 22:33:20','2026-11-04 22:33:34','manual','cerrada'),(211,5,'e67b857790cdbe1e86ae029b0aa47ebdaa9d560dc842b38b2899a54fa8792ca6','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-04 22:33:40','2026-11-04 22:33:40','2026-11-04 22:34:09','manual','cerrada'),(212,4,'3229f0cf9f64373e347e9f0f55d8be04ef6c15136bc59425154b989e19147798','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-04 22:34:14','2026-11-04 22:37:20','2026-11-05 05:49:19','inactividad','cerrada'),(213,4,'cd6d9bb6fbd364507a302fe6341a915ca87d693f123ce444c90adc559cbfd844','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-05 05:49:26','2026-11-05 05:49:26','2026-11-05 05:50:01','manual','cerrada'),(214,5,'a9fe28a1bd9097890be4f6fd3d368093d564a5aa03b76d054b21837149c5bdd7','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-05 05:51:39','2026-11-05 05:51:39','2026-11-05 05:52:03','manual','cerrada'),(215,9,'2f253b2fed3461d73e1be8414d7186e6b8f77018e73c9708d2e372d274ebfde7','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-05 05:52:17','2026-11-05 05:52:17','2026-11-05 05:52:30','manual','cerrada'),(216,6,'09852d4c730573bc8f185a98cd599cda29d5dbcec99aea7bb4ac32f7b4d96d9f','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-05 05:52:41','2026-11-05 05:52:41','2026-11-05 05:52:58','manual','cerrada'),(217,4,'e0742520e2a108d768f53b46651a6ee8dfcf4e062ede37a55e2defd16afd26a9','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-05 05:53:10','2026-11-05 05:55:15','2026-11-05 05:55:37','manual','cerrada'),(218,5,'437d8bd673c720969c015f131633206e4771815ce72ea673ce644d89375b672a','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-05 05:55:44','2026-11-05 05:55:44','2026-11-05 05:56:01','manual','cerrada'),(219,9,'8f5a43cd641523fcfc9504b71ec384cc675edb584afbdbf075bc62bed407f96d','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-05 05:56:13','2026-11-05 05:57:17','2026-11-05 05:57:17','manual','cerrada'),(220,6,'7c8c11dc82a7cd14943dc0bfe753c8dcaee92c0c84e2cda5d3f6ea58a6947e83','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-05 05:57:24','2026-11-05 05:57:24','2026-11-05 05:57:43','manual','cerrada'),(221,9,'cbaae1dd93f5cd2af0325f6a62596b9e2dcbd61f926c5bda713a20df5cdc9b23','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-05 05:58:03','2026-11-05 05:58:03','2026-11-05 05:58:16','manual','cerrada'),(222,6,'2cdcc10248349879adab30b793ee0bbdbaf5a9aef54af7fde929dca0bae98138','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-05 05:58:25','2026-11-05 05:58:25','2026-11-05 05:58:40','manual','cerrada'),(223,4,'56e562b3771fff6aeb2d6cd42636ee4a08bb463fd1c3a0fbd1ad866b1682a091','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-05 05:58:46','2026-11-05 06:03:10','2026-11-05 06:03:23','manual','cerrada'),(224,6,'162042d40a57f39027e3da9b2b6cb56dde7d3f1a73cf0f31ca861df679eee6eb','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-05 06:03:41','2026-11-05 06:04:45','2026-11-05 06:04:45','manual','cerrada'),(225,6,'bfdb453911ee93aab728cfc07efa592f8cbfc8a476d481f8820bdd4c2e624d8e','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-05 06:04:57','2026-11-05 06:06:04','2026-11-05 06:06:27','manual','cerrada'),(226,9,'7212309cd437d276a0eb72aaadb1f8eeb6d258acc3fe257f3aa27711a61e60e9','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-05 06:06:36','2026-11-05 06:06:36','2026-11-05 06:07:30','manual','cerrada'),(227,6,'91709e53b482721bc9749c8b726184f99cc80715ddbdb7e921457f89b7767fb7','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-05 06:07:45','2026-11-05 06:07:45','2026-11-05 06:08:17','manual','cerrada'),(228,4,'3c14fc6e76085da49171eaa8631e9b96b9f60d5e1d42eeafdb03f51f59874f4b','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-05 06:08:24','2026-11-05 06:08:24',NULL,NULL,'activa'),(229,4,'9285d49fa61d09118eb1fe4d5c76543661e572800630a2c1cd9629371ebd3f44','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-06 12:29:54','2026-11-06 12:29:54','2026-11-06 12:30:24','manual','cerrada'),(230,6,'79dbfe31738b7988d23e5aae49cff1eb5fbc6fb18458fe6bd07da513d266a653','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-06 12:30:31','2026-11-06 12:30:31','2026-11-06 12:30:34','manual','cerrada'),(231,4,'e99b7317699d8796abbee9d9be810ad2bce46bc5bcdcc1c9b80ebdcfa6d44232','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-06 12:30:48','2026-11-06 12:48:46','2026-11-06 12:48:51','manual','cerrada'),(232,6,'4d5b5f94a92b6b2f58278f1f73e3e6649d02380d58ae08c1e855287dc0f61a2d','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-06 12:49:10','2026-11-06 12:49:10','2026-11-06 12:49:17','manual','cerrada'),(233,5,'f2b5af4912eec9bc8f81c9733efccb2d3d88eb5e79b32ec4d9114400dcd48a34','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-06 12:49:22','2026-11-06 12:49:22','2026-11-06 12:49:27','manual','cerrada'),(234,4,'cc60c46ddba9edf3cc3e6006fa17f421d3a709b04f0a006899994786d473eadf','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-06 13:00:39','2026-11-06 13:00:39','2026-11-06 13:00:59','manual','cerrada'),(235,5,'341e64460bbd4db7c2f228c6ceb2a638db241ddc9fe9dfc8a4ca21fce908217a','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-06 13:01:03','2026-11-06 13:01:03','2026-11-06 13:01:08','manual','cerrada'),(236,6,'010ca0f60a1fc87a48265bb12508d18419ea43aa691b00b02760b4fd4512ff65','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-06 13:01:20','2026-11-06 13:01:20','2026-11-06 13:47:15','inactividad','cerrada'),(237,4,'b1dd368308c6b92980e1977cab32dba396715562ef8da66d9b0966a332147aff','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-06 13:47:21','2026-11-06 13:47:21','2026-11-06 13:47:30','manual','cerrada'),(238,6,'c515518666dd6050bcc1ffe79a3fea12375d524d7ba6494052d5e1acdb54ed6e','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-06 13:47:36','2026-11-06 13:54:17','2026-11-06 13:54:23','manual','cerrada'),(239,4,'0e18a87921d01113154dde1ce86e5d6ccdfe1027554dcb6509dd960253a855b0','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-06 13:54:27','2026-11-06 13:54:27','2026-11-06 13:55:18','manual','cerrada'),(240,6,'7ab2751ed7a4e5e9b826eab06f5ea435ef278de38f8ea10478bc4c2890c3504b','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-06 13:55:24','2026-11-06 13:55:24','2026-11-06 13:55:46','manual','cerrada'),(241,5,'fdc184807cb5b7304a08bae96fc093b4dca6cae3535773dfb151e1e36b2630d3','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-06 13:55:53','2026-11-06 13:57:07','2026-11-06 13:57:41','manual','cerrada'),(242,4,'19a8f14cafeee87081b34b329e3b5a07914bb322a692309de9db3a518c220296','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-06 13:57:48','2026-11-06 14:12:22','2026-11-06 14:12:52','manual','cerrada'),(243,6,'ea88fe6fa2e505e04de1b4e68b2cf715f64c750110bc5d8168f43a4c0a27d622','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-06 14:13:00','2026-11-06 14:14:01','2026-11-06 14:14:32','manual','cerrada'),(244,5,'bac2861e3ddbd1024f43710884a60b2ce90ddd9af55b44aab315b65bbce74a86','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-06 14:14:36','2026-11-06 14:15:46','2026-11-06 14:16:09','manual','cerrada'),(245,6,'590020341ff4f1ae57e341401ecb109c0df65ab751600541222bff5e5faf03dc','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-06 14:20:09','2026-11-06 14:20:09','2026-11-06 20:20:12','inactividad','cerrada'),(246,6,'2caba2f95e5d0b536c81e4768df256d057bbdf36ef87749b44f9743ee1b38905','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-06 20:20:19','2026-11-06 20:22:38','2026-11-06 20:23:05','manual','cerrada'),(247,9,'4fd0f57609297f6907110124f836534c3616674f1fa9a89c84aee4a9f074d1e6','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-06 20:23:13','2026-11-06 20:23:13','2026-11-06 20:24:06','manual','cerrada'),(248,6,'f50e491dcb35797b49b87e62eee8c73151a583bdd73b72622114c9ded475302b','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-06 20:24:13','2026-11-06 20:25:17','2026-11-06 20:25:32','manual','cerrada'),(249,6,'c5c7c0bbd7b468a8e7eed22230294a2aeecc7ef129f23adab8800415da8be351','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-06 20:33:02','2026-11-06 20:33:02','2026-11-06 20:33:52','manual','cerrada'),(250,9,'b610f4f28ec84fa764f702b905ca96dbd87f6469f89739f6ccf9c5f59a6002fb','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-06 20:34:00','2026-11-06 20:34:00','2026-11-06 20:34:53','manual','cerrada'),(251,4,'bbe220f0980f371377e1335b08856e99a40f1c520d1633161dcd00b27efaa3bf','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-06 20:35:07','2026-11-06 20:35:07','2026-11-06 20:35:18','manual','cerrada'),(252,7,'a313509562f4d3a08000d242d0b7d8affc40650397b65b247aa0ec6d29aea132','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-06 20:35:31','2026-11-06 20:38:23','2026-11-06 20:38:23','manual','cerrada'),(253,7,'b4b90d3119014735a89b616b5a729ae25f7b80881c70e1e40fc8a3433573a070','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36','2026-11-06 20:38:02','2026-11-06 20:38:02',NULL,NULL,'activa'),(254,9,'875298ed14a1f8760d75c153ad0bcb4ed7fa7b9de2a79e6b448ba9f14995cf3b','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-06 20:38:41','2026-11-06 20:39:56','2026-11-06 20:40:31','manual','cerrada'),(255,7,'7171f69fedc5dc8d8575c8011a348cec8c96c628006e4038230a83e783817e83','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-06 20:40:44','2026-11-06 20:40:44','2026-11-06 21:22:32','inactividad','cerrada'),(256,4,'c5226bc3b7e25b461a59fb03bb799e9748d5639b1869e8245bf173cf648df115','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-06 21:22:37','2026-11-06 21:24:30','2026-11-06 21:24:30','manual','cerrada'),(257,9,'d630e1a4ccfb9ba12248e6dbcf7d87948d25cac99b082a7250c7f03f342120a7','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-06 21:24:38','2026-11-06 21:24:38','2026-11-06 21:24:55','manual','cerrada'),(258,4,'02fb29aadc9adaa8c5482e23c49cfc795cd4e2b5af3e9a79cfaae9d5ba7c1a82','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-06 21:25:39','2026-11-06 21:31:10','2026-11-06 21:31:36','manual','cerrada'),(259,9,'35f5ba0e5991213289fc6c9cba4985bea4d6eca2398f0c18fd949423607e0416','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-06 21:31:45','2026-11-06 21:32:55','2026-11-06 21:33:47','manual','cerrada'),(260,6,'f1fab14744d4f491f0aed64d12f0b0cce3d9a20e9ffd61f711fb44550a81eff0','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-06 21:33:53','2026-11-06 21:33:53','2026-11-06 21:34:26','manual','cerrada'),(261,4,'96301e5c71470eac1228a00a94de97bc39a5977e78897e990c59c88d4cc5c2c6','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36','2026-11-06 21:34:32','2026-11-06 21:36:53',NULL,NULL,'activa');
/*!40000 ALTER TABLE `sesiones_usuario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tokens_recuperacion`
--

DROP TABLE IF EXISTS `tokens_recuperacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tokens_recuperacion` (
  `id_token` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_usuario` int(10) unsigned NOT NULL,
  `token` varchar(255) NOT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_expiracion` datetime NOT NULL,
  `usado` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_token`),
  UNIQUE KEY `token` (`token`),
  KEY `idx_tokens_usuario` (`id_usuario`),
  KEY `idx_tokens_expiracion` (`fecha_expiracion`),
  CONSTRAINT `fk_tokens_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tokens_recuperacion`
--

LOCK TABLES `tokens_recuperacion` WRITE;
/*!40000 ALTER TABLE `tokens_recuperacion` DISABLE KEYS */;
INSERT INTO `tokens_recuperacion` VALUES (1,4,'9ea4b69ad60655a586e285a7820dc976fed60e997ab2b8f362166f3dcb513814','2026-08-25 12:58:17','2026-08-25 13:28:16',1),(2,4,'5b63d66fba575ea84da1e139e7217b69c63414f8bd8586d5f99b6420c8ae12ee','2026-08-25 12:58:52','2026-08-25 13:28:52',1),(3,4,'717be75f4b659e47e13fd11b8c13bb85f2f3c70af0d33cc2cf91693c40faf64e','2026-08-25 13:00:38','2026-08-25 13:30:38',1),(4,4,'727af7feb310559bdce9724cbf826042350639dd7f9b169c7e8d3aabe79fc2cb','2026-10-24 08:02:15','2026-10-24 08:32:15',1),(5,6,'c138841ef1a26f5d8159e91a3c056ee6e29a5c299ccf5e3c5e2f13740a20f3fe','2026-10-24 08:05:38','2026-10-24 08:35:38',1),(6,4,'80744e2bcf268fa31d8cc5d227c0367c5bbe429683ec8ca2008377c8f6667d01','2026-10-24 08:42:30','2026-10-24 09:12:30',0);
/*!40000 ALTER TABLE `tokens_recuperacion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuarios` (
  `id_usuario` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_rol` tinyint(3) unsigned NOT NULL,
  `id_punto_asignado` int(10) unsigned DEFAULT NULL,
  `nombre_completo` varchar(120) NOT NULL,
  `correo` varchar(120) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `estado` enum('activo','inactivo','bloqueado') NOT NULL DEFAULT 'activo',
  `fecha_registro` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `correo` (`correo`),
  KEY `idx_usuarios_rol` (`id_rol`),
  KEY `idx_usuarios_estado` (`estado`),
  KEY `idx_usuarios_correo` (`correo`),
  KEY `idx_usuarios_punto_asignado` (`id_punto_asignado`),
  CONSTRAINT `fk_usuarios_punto_asignado` FOREIGN KEY (`id_punto_asignado`) REFERENCES `puntos_atencion` (`id_punto`) ON UPDATE CASCADE,
  CONSTRAINT `fk_usuarios_roles` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`) ON UPDATE CASCADE,
  CONSTRAINT `chk_usuarios_centro_por_rol` CHECK (`id_rol` = 2 and `id_punto_asignado` is not null or `id_rol` <> 2 and `id_punto_asignado` is null)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,1,NULL,'Administrador EcoGestión','admin@gmail.com','$2y$10$GCOcn8WkpgJ/z89apO6s0.Z1ctJiAxFeca7Wz9AGqlzuPGb6qpYQO','7771000001','activo','2026-08-25 11:37:00','2026-11-06 13:59:09'),(2,2,2,'Sandra Elizabeth Leon Sosa','sandra@gmail.com','$2y$10$SFwD3lR1Jm.HoMci1kmBc.uEVOTImcozJY4Ufevkkyiy4dZAVb8pO','7771000002','activo','2026-08-25 11:37:00','2026-11-06 14:03:42'),(3,3,NULL,'Sergio Perez','checo@gmail.com','$2y$10$4x50eVJUwg.yd6YjYfePcO4VmDq4ovzxVWxnR.zU7IoIfaN81gJNW','7771000003','activo','2026-08-25 11:37:00','2026-11-06 14:03:07'),(4,1,NULL,'Joel Figueroa Figueroa','fjoel4588@gmail.com','$2y$10$ZBwhkbvnVTZBrXCqKFTLK.XWsBJZkJwSTmXAaahJ7d2sQWAXz0Bkm','7771430773','activo','2026-08-25 11:41:00','2026-08-25 12:58:42'),(5,2,3,'Jana Aixa Sanabria Quintana','jana@gmail.com','$2y$10$2sLS7EDm9LNO1RvmqZ/DAuzmxeSQGKjBOohWYvIXsYBEWOl0Oyntq','7771231253','activo','2026-08-25 12:29:18','2026-09-22 19:09:48'),(6,3,NULL,'Asiel Vazquez Rodriguez','asiel@gmail.com','$2y$10$nLHNKrcg8U.qzplDHLkSR.su278PZGH4WT/ckROz5q.A3IAyEawMG','1234567890','activo','2026-08-29 09:19:41','2026-10-24 08:06:19'),(7,3,NULL,'Joel Figueroa','joelfidiz@gmail.com','$2y$10$9ePBF/ioTqmA2b9jhY9qbeTtZlyAQwPK7WD5ILul3EMUhkow97N4.','77714312121','activo','2026-10-24 09:47:49',NULL),(8,3,NULL,'Elsy Orihuela Flores','ofeo230519@upemor.edu.mx','$2y$10$.lSDlIzk5e9ob57HJCfIue3EUyz9IxLWsVHWl7scVQ2mUBHACx1A.','1234567890','activo','2026-10-24 09:52:24',NULL),(9,2,4,'Enrique Aranda','kike@gmail.com','$2y$10$vVJjqFO4MA5aeCiZ7EtTyOqjhxqwK1.x9ZnukO94u6VTmM1NzObBC','7771231253','activo','2026-11-03 23:42:25','2026-11-04 08:28:58'),(10,2,2,'Kevin Gasca','kevin@gmail.com','$2y$10$LU0YDg99YiB.1QDwGzmHNeDtGMN15KgVGg4TQ9LGXTdr3zuSL7x8G','7771231253','activo','2026-11-03 23:49:03','2026-11-03 23:51:53');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-11-06 21:37:21
