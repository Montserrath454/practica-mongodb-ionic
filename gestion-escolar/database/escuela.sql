CREATE DATABASE IF NOT EXISTS escuela;
USE escuela;

CREATE TABLE IF NOT EXISTS alumnos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    matricula VARCHAR(20) NOT NULL UNIQUE,
    carrera VARCHAR(100) NOT NULL
);

CREATE TABLE IF NOT EXISTS materias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL
);

CREATE TABLE IF NOT EXISTS calificaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    alumno_id INT NOT NULL,
    materia_id INT NOT NULL,
    calificacion DECIMAL(4,2) NOT NULL,
    FOREIGN KEY (alumno_id) REFERENCES alumnos(id),
    FOREIGN KEY (materia_id) REFERENCES materias(id)
);

INSERT INTO materias (nombre) VALUES
('Base de Datos I'),
('Programación Web'),
('Desarrollo de APIs');