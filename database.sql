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
