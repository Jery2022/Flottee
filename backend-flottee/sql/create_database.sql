CREATE DATABASE IF NOT EXISTS flottee_vehicles;

USE flottee_vehicles;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    pseudo VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    status ENUM('active', 'inactive') DEFAULT 'active',
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS vehicles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    make VARCHAR(100) NOT NULL,
    model VARCHAR(100) NOT NULL,
    type VARCHAR(100) NOT NULL DEFAULT 'Sedan',
    year INT NOT NULL,
    license_plate VARCHAR(50) NOT NULL UNIQUE,
    status ENUM('disponible', 'en utilisation', 'en maintenance') DEFAULT 'disponible',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL
);

CREATE TABLE IF NOT EXISTS assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    vehicle_id INT NOT NULL,
    description TEXT NOT NULL DEFAULT 'A renseigner',
    start_date DATE NOT NULL,
    end_date DATE,
    status ENUM('active', 'completed', 'cancelled') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id)
);

CREATE TABLE IF NOT EXISTS maintenance_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vehicle_id INT NOT NULL,
    description TEXT NOT NULL,
    date DATE NOT NULL,
    cost DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id)
);


CREATE TABLE IF NOT EXISTS reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    vehicle_id INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE
);


-- jeu de données d'exemple pour la table users

INSERT INTO users (first_name, last_name, pseudo, email, password, role, status, last_login)
VALUES
('Admin', 'Paulin', 'admin01', 'admin@example.com', '$2y$10$JlzLZ1zAW0.MXej4nUljPO4Qq3CVQq5fgWQG.f4FCbKRwS5g4vFWq', 'admin', 'active', '2025-06-01 14:00:00'),
('Alice', 'Ngoma', 'alice01', 'alice@example.com', '$2y$10$Nc1SlPoT6PBaIpwYkDsPUe.o5rq/WGchinYbpJt8HR7f2RxnOJTv2', 'admin', 'active', '2025-08-01 10:00:00'),
('Bruno', 'Mouele', 'bruno02', 'bruno@example.com', '$2y$10$Nc1SlPoT6PBaIpwYkDsPUe.o5rq/WGchinYbpJt8HR7f2RxnOJTv2', 'user', 'active', '2025-08-02 09:30:00'),
('Chantal', 'Obiang', 'chantal03', 'chantal@example.com', '$2y$10$Nc1SlPoT6PBaIpwYkDsPUe.o5rq/WGchinYbpJt8HR7f2RxnOJTv2', 'user', 'inactive', NULL),
('David', 'Essono', 'david04', 'david@example.com', '$2y$10$Nc1SlPoT6PBaIpwYkDsPUe.o5rq/WGchinYbpJt8HR7f2RxnOJTv2', 'user', 'active', '2025-08-03 11:15:00'),
('Esther', 'Mba', 'esther05', 'esther@example.com', '$2y$10$Nc1SlPoT6PBaIpwYkDsPUe.o5rq/WGchinYbpJt8HR7f2RxnOJTv2', 'admin', 'active', '2025-08-04 08:45:00'),
('Fabrice', 'Ndong', 'fabrice06', 'fabrice@example.com', '$2y$10$Nc1SlPoT6PBaIpwYkDsPUe.o5rq/WGchinYbpJt8HR7f2RxnOJTv2', 'user', 'active', '2025-08-05 14:20:00'),
('Grace', 'Boussamba', 'grace07', 'grace@example.com', '$2y$10$Nc1SlPoT6PBaIpwYkDsPUe.o5rq/WGchinYbpJt8HR7f2RxnOJTv2', 'user', 'inactive', NULL),
('Henri', 'Makaya', 'henri08', 'henri@example.com', '$2y$10$Nc1SlPoT6PBaIpwYkDsPUe.o5rq/WGchinYbpJt8HR7f2RxnOJTv2', 'user', 'active', '2025-08-06 16:00:00'),
('Ines', 'Koumba', 'ines09', 'ines@example.com', '$2y$10$Nc1SlPoT6PBaIpwYkDsPUe.o5rq/WGchinYbpJt8HR7f2RxnOJTv2', 'user', 'active', '2025-08-07 12:10:00'),
('Jean', 'Minko', 'jean10', 'jean@example.com', '$2y$10$Nc1SlPoT6PBaIpwYkDsPUe.o5rq/WGchinYbpJt8HR7f2RxnOJTv2', 'admin', 'active', '2025-08-08 07:50:00');


-- jeu de données d'exemple pour la table vehicles

INSERT INTO vehicles (make, model, year, license_plate, status)
VALUES
('Toyota', 'Corolla', 2020, 'GA-123-AA', 'disponible'),
('Hyundai', 'Tucson', 2021, 'GA-456-BB', 'en utilisation'),
('Ford', 'Focus', 2019, 'GA-789-CC', 'en maintenance'),
('Renault', 'Clio', 2022, 'GA-321-DD', 'disponible'),
('Peugeot', '208', 2020, 'GA-654-EE', 'disponible'),
('Kia', 'Sportage', 2021, 'GA-987-FF', 'en utilisation'),
('Nissan', 'Juke', 2018, 'GA-147-GG', 'en maintenance'),
('Volkswagen', 'Golf', 2022, 'GA-258-HH', 'disponible'),
('Mazda', 'CX-5', 2023, 'GA-369-II', 'disponible'),
('Honda', 'Civic', 2020, 'GA-741-JJ', 'en utilisation');


-- jeu de données d'exemple pour la table assignments

INSERT INTO assignments (user_id, vehicle_id, start_date, end_date, status)
VALUES
(2, 1, '2025-08-01', NULL, 'active'),
(4, 2, '2025-07-25', '2025-08-05', 'completed'),
(6, 3, '2025-07-20', '2025-07-30', 'cancelled'),
(8, 4, '2025-08-03', NULL, 'active'),
(5, 5, '2025-08-02', '2025-08-10', 'completed'),
(9, 6, '2025-08-04', NULL, 'active'),
(3, 7, '2025-07-15', '2025-07-25', 'completed'),
(10, 8, '2025-08-05', NULL, 'active'),
(1, 9, '2025-08-06', NULL, 'active'),
(7, 10, '2025-08-07', NULL, 'active');


-- jeu de données d'exemple pour la table maintenance_records

INSERT INTO maintenance_records (vehicle_id, description, date, cost)
VALUES
(3, 'Changement de pneus', '2025-07-28', 250.00),
(7, 'Révision moteur', '2025-07-20', 480.00),
(3, 'Vidange et filtres', '2025-08-01', 120.00),
(7, 'Remplacement batterie', '2025-08-03', 180.00),
(2, 'Contrôle technique', '2025-07-30', 90.00),
(6, 'Réparation climatisation', '2025-08-04', 300.00),
(10, 'Remplacement freins', '2025-08-05', 220.00),
(5, 'Révision générale', '2025-08-06', 400.00),
(1, 'Diagnostic électronique', '2025-08-07', 150.00),
(9, 'Nettoyage intérieur', '2025-08-08', 60.00);


-- jeu de données d'exemple pour la table reservations

INSERT INTO reservations (user_id, vehicle_id, start_date, end_date)
VALUES
(2, 1, '2025-08-01', '2025-08-05'),
(4, 2, '2025-08-02', '2025-08-06'),
(6, 3, '2025-08-03', NULL),
(8, 4, '2025-08-04', '2025-08-10'),
(5, 5, '2025-08-05', NULL),
(9, 6, '2025-08-06', '2025-08-12'),
(3, 7, '2025-08-07', NULL),
(10, 8, '2025-08-08', '2025-08-15'),
(1, 9, '2025-08-09', NULL),
(7, 10, '2025-08-10', '2025-08-14');
