CREATE DATABASE IF NOT EXISTS activa_el_turismo;
USE activa_el_turismo;

CREATE TABLE IF NOT EXISTS noticias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fecha DATE NOT NULL,
    titulo VARCHAR(255) NOT NULL,
    subtitulo VARCHAR(255),
    autor VARCHAR(100) NOT NULL,
    cuerpo TEXT NOT NULL,
    imagen_path VARCHAR(255),
    pie_imagen VARCHAR(255),
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
