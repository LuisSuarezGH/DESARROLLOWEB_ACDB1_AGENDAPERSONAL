
-- BASE DE DATOS: AGENDA PERSONAL

CREATE DATABASE IF NOT EXISTS agenda_personal
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE agenda_personal;

-- TABLA: USUARIOS

CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL,
    correo VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- TABLA: CATEGORIAS

CREATE TABLE IF NOT EXISTS categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB;

INSERT IGNORE INTO categorias (nombre) VALUES
('Familia'),
('Amigos'),
('Trabajo'),
('Universidad'),
('Otro');

-- TABLA: CONTACTOS

CREATE TABLE IF NOT EXISTS contactos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    nombre VARCHAR(120) NOT NULL,
    telefono VARCHAR(25) NOT NULL,
    correo VARCHAR(150) NOT NULL,
    ciudad VARCHAR(100) NOT NULL,
    id_categoria INT NOT NULL,
    favorito TINYINT(1) NOT NULL DEFAULT 0,

    CONSTRAINT fk_contactos_usuarios
        FOREIGN KEY (id_usuario)
        REFERENCES usuarios(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_contactos_categorias
        FOREIGN KEY (id_categoria)
        REFERENCES categorias(id)
) ENGINE=InnoDB;

-- CONSULTAS DE VERIFICACION

SHOW TABLES;

SELECT * FROM categorias;
SELECT * FROM usuarios;
SELECT * FROM contactos;
