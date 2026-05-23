<<<<<<< HEAD
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

=======
-- Création de la base de données
CREATE DATABASE IF NOT EXISTS agenceimmo;
USE agenceimmo;

-- Table des utilisateurs
CREATE TABLE IF NOT EXISTS utilisateurs (
    id_utilisateur INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL,
    prenom VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    telephone VARCHAR(15),
    date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP,
    niveau_acces INT DEFAULT 1 -- 1: utilisateur normal, 2: agent, 3: admin
);

-- Table des biens immobiliers
CREATE TABLE IF NOT EXISTS biens (
    id_bien INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    type VARCHAR(50) NOT NULL, -- maison, appartement, terrain, etc.
    statut VARCHAR(20) NOT NULL, -- à vendre, à louer, vendu, loué
    prix DECIMAL(10, 2) NOT NULL,
    surface DECIMAL(8, 2) NOT NULL,
    nb_pieces INT NOT NULL,
    adresse VARCHAR(255) NOT NULL,
    ville VARCHAR(100) NOT NULL,
    code_postal VARCHAR(10) NOT NULL,
    date_publication DATETIME DEFAULT CURRENT_TIMESTAMP,
    id_agent INT,
    FOREIGN KEY (id_agent) REFERENCES utilisateurs(id_utilisateur)
);

-- Table des images des biens
CREATE TABLE IF NOT EXISTS images_bien (
    id_image INT AUTO_INCREMENT PRIMARY KEY,
    id_bien INT NOT NULL,
    url_image VARCHAR(255) NOT NULL,
    est_principale BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (id_bien) REFERENCES biens(id_bien) ON DELETE CASCADE
);

-- Table des messages entre utilisateurs
CREATE TABLE IF NOT EXISTS messages (
    id_message INT AUTO_INCREMENT PRIMARY KEY,
    id_expediteur INT NOT NULL,
    id_destinataire INT NOT NULL,
    sujet VARCHAR(100) NOT NULL,
    contenu TEXT NOT NULL,
    date_envoi DATETIME DEFAULT CURRENT_TIMESTAMP,
    lu BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (id_expediteur) REFERENCES utilisateurs(id_utilisateur),
    FOREIGN KEY (id_destinataire) REFERENCES utilisateurs(id_utilisateur)
);

-- Insertion de données de test
-- Utilisateurs
INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, telephone, niveau_acces) VALUES
('Admin', 'System', 'admin@altimmo.fr', SHA1('admin123'), '0123456789', 3),
('Dupont', 'Jean', 'jean.dupont@altimmo.fr', SHA1('agent123'), '0123456789', 2),
('Martin', 'Sophie', 'sophie.martin@altimmo.fr', SHA1('agent123'), '0123456789', 2),
('Durand', 'Pierre', 'pierre@exemple.fr', SHA1('user123'), '0123456789', 1),
('Lefebvre', 'Marie', 'marie@exemple.fr', SHA1('user123'), '0123456789', 1);

-- Biens immobiliers
INSERT INTO biens (titre, description, type, statut, prix, surface, nb_pieces, adresse, ville, code_postal, id_agent) VALUES
('Villa de luxe avec piscine', 'Magnifique villa avec piscine et jardin paysager. Vue imprenable sur la mer.', 'Maison', 'À vendre', 850000.00, 220.00, 6, '123 Avenue du Paradis', 'Nice', '06000', 2),
('Appartement moderne en centre-ville', 'Bel appartement rénové avec goût, proche de toutes commodités.', 'Appartement', 'À vendre', 320000.00, 85.00, 3, '45 Rue de la République', 'Lyon', '69002', 2),
('Studio étudiant', 'Studio idéal pour étudiant, proche des universités et des transports.', 'Appartement', 'À louer', 550.00, 25.00, 1, '12 Rue des Étudiants', 'Paris', '75005', 3),
('Maison familiale avec jardin', 'Grande maison familiale avec jardin, garage et dépendance.', 'Maison', 'À vendre', 420000.00, 160.00, 5, '78 Rue des Cerisiers', 'Bordeaux', '33000', 3),
('Appartement avec terrasse', 'Bel appartement avec grande terrasse ensoleillée, vue dégagée.', 'Appartement', 'À louer', 980.00, 65.00, 2, '34 Avenue Victor Hugo', 'Marseille', '13008', 2);

-- Images des biens
INSERT INTO images_bien (id_bien, url_image, est_principale) VALUES
(1, 'img/biens/villa1.jpg', TRUE),
(1, 'img/biens/villa2.jpg', FALSE),
(1, 'img/biens/villa3.jpg', FALSE),
(2, 'img/biens/appart1.jpg', TRUE),
(2, 'img/biens/appart2.jpg', FALSE),
(3, 'img/biens/studio1.jpg', TRUE),
(4, 'img/biens/maison1.jpg', TRUE),
(4, 'img/biens/maison2.jpg', FALSE),
(5, 'img/biens/appart3.jpg', TRUE);

-- Messages
INSERT INTO messages (id_expediteur, id_destinataire, sujet, contenu, date_envoi, lu) VALUES
(4, 2, 'Demande de visite', 'Bonjour, je souhaiterais visiter la villa de luxe avec piscine. Quelles sont vos disponibilités ?', NOW(), FALSE),
(5, 3, 'Renseignements', 'Bonjour, je souhaiterais avoir plus d\'informations sur l\'appartement avec terrasse à Marseille.', NOW(), TRUE),
(2, 4, 'RE: Demande de visite', 'Bonjour, je suis disponible ce week-end pour vous faire visiter la villa. Cordialement, Jean Dupont', NOW(), FALSE);
>>>>>>> a3a6479586a2984f840440d0b07222f8debfd793
