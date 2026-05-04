CREATE DATABASE IF NOT EXISTS cardstudio;
USE cardstudio;

CREATE TABLE IF NOT EXISTS `categorias` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS `diseños` (
  `id_diseño` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre_diseño` VARCHAR(255) NOT NULL,
  `id_categoria` INT NOT NULL,
  `imagen_fondo_url` VARCHAR(255) NOT NULL,
  `miniatura_url` VARCHAR(255) NOT NULL,
  `configuracion_textos_json` TEXT NOT NULL,
  FOREIGN KEY (`id_categoria`) REFERENCES `categorias`(`id`)
);

CREATE TABLE IF NOT EXISTS `usuarios` (
  `id_usuario` INT AUTO_INCREMENT PRIMARY KEY,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `fecha_registro` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS `compras` (
  `id_compra` INT AUTO_INCREMENT PRIMARY KEY,
  `id_usuario` INT NOT NULL,
  `id_diseño` INT NOT NULL,
  `estado_pago` ENUM('pendiente', 'completado') DEFAULT 'pendiente',
  `fecha_compra` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`id_usuario`) REFERENCES `usuarios`(`id_usuario`),
  FOREIGN KEY (`id_diseño`) REFERENCES `diseños`(`id_diseño`)
);

-- Insertar datos si la tabla de categorias está vacía
INSERT INTO `categorias` (`nombre`) 
SELECT * FROM (SELECT 'Cumpleaños') AS tmp 
WHERE NOT EXISTS (SELECT nombre FROM `categorias` WHERE nombre = 'Cumpleaños') LIMIT 1;

INSERT INTO `categorias` (`nombre`) 
SELECT * FROM (SELECT 'Boda') AS tmp 
WHERE NOT EXISTS (SELECT nombre FROM `categorias` WHERE nombre = 'Boda') LIMIT 1;

INSERT INTO `categorias` (`nombre`) 
SELECT * FROM (SELECT 'Bautizo') AS tmp 
WHERE NOT EXISTS (SELECT nombre FROM `categorias` WHERE nombre = 'Bautizo') LIMIT 1;

INSERT INTO `categorias` (`nombre`) 
SELECT * FROM (SELECT 'Agradecimiento') AS tmp 
WHERE NOT EXISTS (SELECT nombre FROM `categorias` WHERE nombre = 'Agradecimiento') LIMIT 1;
