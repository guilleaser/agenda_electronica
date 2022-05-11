/* Crear la BBDD 'agenda' */
DROP DATABASE IF EXISTS agenda;
CREATE DATABASE agenda;
USE agenda;

/* Crear tabla 'agenda' */
DROP TABLE IF EXISTS `agenda`;
CREATE TABLE `agenda` (
  `codigo` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(30) NOT NULL,
  `telefono` int(9) DEFAULT NULL,
  `correo` varchar(50) DEFAULT NULL,
  `fechaNac` date DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/* Crear tabla 'usuarios' */
CREATE TABLE `usuarios` (
  `usuario` varchar(30) NOT NULL,
  `password` varchar(30) NOT NULL,
  PRIMARY KEY (`usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/* Crear usuario con todos los privilegios en edición de la base de datos*/
/*DROP USER 'agenda'@'localhost';*/
CREATE USER 'agenda'@'localhost' IDENTIFIED BY '2DAWdwes';
GRANT ALL PRIVILEGES ON * . * TO 'agenda'@'localhost';
FLUSH PRIVILEGES; 


