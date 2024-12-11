CREATE DATABASE IF NOT EXISTS academia;
USE academia;

DROP TABLE IF EXISTS grades;
DROP TABLE IF EXISTS teacher_subjects;
DROP TABLE IF EXISTS subjects;
DROP TABLE IF EXISTS users;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    surname1 VARCHAR(50) NOT NULL,
    surname2 VARCHAR(50),
    email VARCHAR(100) NOT NULL UNIQUE,
    dni VARCHAR(20) NOT NULL UNIQUE,
    role ENUM('gestor', 'profesor', 'tutor', 'estudiante') NOT NULL,
    first_login BOOLEAN DEFAULT TRUE,
    tutor_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tutor_id) REFERENCES users(id)
);

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `code` varchar(20) NOT NULL,
  `course` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS teacher_subjects (
    teacher_id INT,
    subject_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (teacher_id, subject_id),
    FOREIGN KEY (teacher_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS grades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    subject_id INT NOT NULL,
    grade DECIMAL(4,2) NOT NULL,
    comments TEXT,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
    CHECK (grade >= 0 AND grade <= 10)
);

INSERT INTO users (username, password, name, surname1, surname2, email, dni, role, first_login)
VALUES (
    'admin',
    'admin123',
    'Administrador',
    'Sistema',
    '',
    'admin@academia.com',
    '00000000A',
    'gestor',
    false
);

INSERT INTO users (username, password, name, surname1, surname2, email, dni, role, first_login)
VALUES (
    'profesor1',
    'profesor123',
    'Juan',
    'Pérez',
    'García',
    'profesor1@academia.com',
    '11111111B',
    'profesor',
    true
);

INSERT INTO users (username, password, name, surname1, surname2, email, dni, role, first_login)
VALUES (
    'juan.estudiante',
    'student123',
    'Juan',
    'García',
    'López',
    'juan.estudiante@academia.com',
    '22222222C',
    'estudiante',
    true
);
