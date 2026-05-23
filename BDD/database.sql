CREATE DATABASE IF NOT EXISTS altimmo CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE altimmo;

DROP TABLE IF EXISTS envoyer;
DROP TABLE IF EXISTS biens;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id_u INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) DEFAULT '',
    email VARCHAR(190) NOT NULL UNIQUE,
    password VARCHAR(255) DEFAULT NULL,
    ip VARCHAR(45) DEFAULT NULL,
    role TINYINT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE categories (
    id_cat INT AUTO_INCREMENT PRIMARY KEY,
    nom_cat VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE biens (
    id_b INT AUTO_INCREMENT PRIMARY KEY,
    ville VARCHAR(120) NOT NULL,
    cp VARCHAR(12) NOT NULL,
    prix DECIMAL(12,2) NOT NULL,
    superficie DECIMAL(8,2) NOT NULL,
    pieces INT NOT NULL,
    description TEXT NOT NULL,
    cat_id INT DEFAULT NULL,
    image VARCHAR(255) DEFAULT NULL,
    u_id INT DEFAULT NULL,
    vendu TINYINT(1) NOT NULL DEFAULT 0,
    date_v DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_biens_categories FOREIGN KEY (cat_id) REFERENCES categories(id_cat) ON DELETE SET NULL,
    CONSTRAINT fk_biens_users FOREIGN KEY (u_id) REFERENCES users(id_u) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE envoyer (
    id_env INT AUTO_INCREMENT PRIMARY KEY,
    id_exp INT DEFAULT NULL,
    id_recept INT DEFAULT NULL,
    message TEXT NOT NULL,
    date_env DATETIME DEFAULT CURRENT_TIMESTAMP,
    lu TINYINT(1) NOT NULL DEFAULT 0,
    CONSTRAINT fk_envoyer_exp FOREIGN KEY (id_exp) REFERENCES users(id_u) ON DELETE SET NULL,
    CONSTRAINT fk_envoyer_recept FOREIGN KEY (id_recept) REFERENCES users(id_u) ON DELETE SET NULL
) ENGINE=InnoDB;

INSERT INTO categories (id_cat, nom_cat) VALUES
(1, 'Appartement'),
(2, 'Maison'),
(3, 'Villa'),
(4, 'Studio'),
(5, 'Local commercial');

INSERT INTO users (id_u, nom, prenom, email, password, role) VALUES
(1, 'Admin', 'Altimmo', 'admin@altimmo.local', '$2y$10$hmYozAP/GDF/FxynsmFHouaYcp56tQzDQpIv90MyPLF9HavNaPELS', 3),
(2, 'Martin', 'Claire', 'agent@altimmo.local', '$2y$10$Wrc8lMOOpeIw0bFJ5/jJG.1t94BcWdjwlhPDFEkTu1fGJ25pmr.t.', 2);

INSERT INTO biens (ville, cp, prix, superficie, pieces, description, cat_id, image, u_id, vendu, date_v) VALUES
('Paris', '75008', 980000, 118, 4, 'Appartement lumineux proche des Champs-Elysees avec belles prestations.', 1, 'assets/img/biens/exc1.jpg', 2, 0, NOW()),
('Neuilly-sur-Seine', '92200', 1450000, 185, 6, 'Maison familiale avec terrasse, volumes confortables et emplacement recherche.', 2, 'assets/img/biens/exc2.webp', 2, 0, NOW()),
('Boulogne-Billancourt', '92100', 420000, 48, 2, 'Deux pieces fonctionnel, ideal premier achat ou investissement locatif.', 1, 'assets/img/biens/neuf.jpg', 2, 0, NOW());

