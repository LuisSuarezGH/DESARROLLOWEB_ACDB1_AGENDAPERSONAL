
CREATE DATABASE IF NOT EXISTS agenda_personal
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE agenda_personal;

-- Tabla de usuarios
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL,
    correo VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabla de contactos
CREATE TABLE contactos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    nombre VARCHAR(120) NOT NULL,
    telefono VARCHAR(25) NOT NULL,
    correo VARCHAR(150) NOT NULL,
    ciudad VARCHAR(100) NOT NULL,

    CONSTRAINT fk_contactos_usuarios
    FOREIGN KEY (id_usuario)
    REFERENCES usuarios(id)
    ON DELETE CASCADE
) ENGINE=InnoDB;