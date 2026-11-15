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
INSERT INTO `puntos_atencion` VALUES (1,'Centro Ambiental UPEMOR','centro_acopio','Bulevar Cuauhnáhuac 566, Lomas del Texcal','Jiutepec','Morelos',NULL,'7772293500',NULL,'recepcion_reciclables',18.8914200,-99.1631400,'Punto demostrativo de recepción para la comunidad universitaria.','activo','2026-08-25 11:37:00','2026-08-31 08:31:29'),(2,'Recicla Jiutepec Centro','empresa','Avenida Insurgentes 24, Centro','Jiutepec','Morelos',NULL,'7771002020',NULL,'compra_residuos',18.8850768,-99.1737649,'Centro demostrativo para compra y recepción de residuos reciclables.','activo','2026-08-25 11:37:00','2026-11-04 22:18:30'),(3,'Punto Verde CIVAC','punto_servicio','Avenida Centenario 108, CIVAC','Jiutepec','Morelos',NULL,'7771003030',NULL,'residuos_electronicos',18.9056200,-99.1788100,'Punto demostrativo para electrónicos y materiales de manejo especial.','activo','2026-08-25 11:37:00','2026-09-24 09:02:19'),(4,'EcoPunto Las Fuentes Jiutepec','punto_servicio','Avenida de las Fuentes 125, Colonia Centro','Jiutepec','Morelos','62550','7774567890','contacto@ejemplo.test','compra_residuos',18.8837600,-99.1769100,'Centro dedicado a la recepción y compra de residuos reciclables generados por hogares y pequeños comercios.','activo','2026-11-04 00:06:24','2026-11-04 08:24:48'),(5,'Centro Circular','punto_servicio','Avenida Insurgentes 48, Colonia Tejalpa','Jiutepec','Morelos','62570','7773251840','circular@ejemplo.test','compra_residuos',18.8992000,-99.1605000,'Punto ambiental dedicado a recibir materiales reciclables y orientar a la ciudadanía sobre su correcta separación.','activo','2026-11-04 00:18:50','2026-11-04 00:22:58');
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
INSERT INTO `usuarios` VALUES (1,1,NULL,'Administrador de demostración','admin@ecogestion.local','$2b$12$Bhps4X5zkxBU2uvKJHgC6OPggyIV7pXvOySMbl10tgPDhZ3LUJ65S','7770000001','activo','2026-08-25 11:37:00',NULL),(2,2,2,'Coordinador de demostración 1','coordinador1@ecogestion.local','$2b$12$Bhps4X5zkxBU2uvKJHgC6OPggyIV7pXvOySMbl10tgPDhZ3LUJ65S','7770000002','activo','2026-08-25 11:37:00',NULL),(3,3,NULL,'Ciudadano de demostración 1','ciudadano1@ecogestion.local','$2b$12$Bhps4X5zkxBU2uvKJHgC6OPggyIV7pXvOySMbl10tgPDhZ3LUJ65S','7770000003','activo','2026-08-25 11:37:00',NULL),(4,1,NULL,'Administrador de demostración 2','admin2@ecogestion.local','$2b$12$Bhps4X5zkxBU2uvKJHgC6OPggyIV7pXvOySMbl10tgPDhZ3LUJ65S','7770000004','activo','2026-08-25 11:41:00',NULL),(5,2,3,'Coordinador de demostración 2','coordinador2@ecogestion.local','$2b$12$Bhps4X5zkxBU2uvKJHgC6OPggyIV7pXvOySMbl10tgPDhZ3LUJ65S','7770000005','activo','2026-08-25 12:29:18',NULL),(6,3,NULL,'Ciudadano de demostración 2','ciudadano2@ecogestion.local','$2b$12$Bhps4X5zkxBU2uvKJHgC6OPggyIV7pXvOySMbl10tgPDhZ3LUJ65S','7770000006','activo','2026-08-29 09:19:41',NULL),(7,3,NULL,'Ciudadano de demostración 3','ciudadano3@ecogestion.local','$2b$12$Bhps4X5zkxBU2uvKJHgC6OPggyIV7pXvOySMbl10tgPDhZ3LUJ65S','7770000007','activo','2026-10-24 09:47:49',NULL),(8,3,NULL,'Ciudadano de demostración 4','ciudadano4@ecogestion.local','$2b$12$Bhps4X5zkxBU2uvKJHgC6OPggyIV7pXvOySMbl10tgPDhZ3LUJ65S','7770000008','activo','2026-10-24 09:52:24',NULL),(9,2,4,'Coordinador de demostración 3','coordinador3@ecogestion.local','$2b$12$Bhps4X5zkxBU2uvKJHgC6OPggyIV7pXvOySMbl10tgPDhZ3LUJ65S','7770000009','activo','2026-11-03 23:42:25',NULL),(10,2,2,'Coordinador de demostración 4','coordinador4@ecogestion.local','$2b$12$Bhps4X5zkxBU2uvKJHgC6OPggyIV7pXvOySMbl10tgPDhZ3LUJ65S','7770000010','activo','2026-11-03 23:49:03',NULL);
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
