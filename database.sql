CREATE DATABASE IF NOT EXISTS activa_el_turismo;
USE activa_el_turismo;

CREATE TABLE IF NOT EXISTS usuarios (
    id_usu       INT AUTO_INCREMENT PRIMARY KEY,
    nombre_usu   VARCHAR(100)  NOT NULL,
    correo_usu   VARCHAR(150)  NOT NULL UNIQUE,
    password_usu VARCHAR(255)  NOT NULL,
    rol_usu      ENUM('admin','editor','colaborador') DEFAULT 'editor'
);

CREATE TABLE IF NOT EXISTS categorias (
    id_cat     INT AUTO_INCREMENT PRIMARY KEY,
    nombre_cat VARCHAR(100) NOT NULL,
    slug_cat   VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS etiquetas (
    id_eti   INT AUTO_INCREMENT PRIMARY KEY,
    nom_eti  VARCHAR(100) NOT NULL,
    slug_eti VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS noticias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fecha DATE NOT NULL,
    titulo VARCHAR(255) NOT NULL,
    subtitulo VARCHAR(255),
    autor VARCHAR(100) NOT NULL,
    cuerpo TEXT NOT NULL,
    imagen_path VARCHAR(255),
    pie_imagen VARCHAR(255),
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    slug_not VARCHAR(255) NOT NULL UNIQUE,
    id_cat INT, 
    FOREIGN KEY (id_cat) REFERENCES categorias(id_cat) ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS noticias_etiquetadas (
    id INT NOT NULL,
    id_eti INT NOT NULL,
    PRIMARY KEY (id, id_eti)
);

INSERT INTO categorias (nombre_cat, slug_cat) VALUES
('Destinos',     'destinos'),
('Gastronomía',  'gastronomia'),
('Cultura',      'cultura'),
('Minería',      'mineria'),
('Eventos',      'eventos');

INSERT INTO etiquetas (nom_eti, slug_eti) VALUES
('Atacama', 'atacama'),
('Desierto', 'desierto'),
('San Pedro', 'san-pedro'),
('Calama', 'calama'),
('Antofagasta', 'antofagasta'),
('Taltal', 'taltal');
