-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : sam. 23 mai 2026 à 12:00
-- Version du serveur : 9.1.0
-- Version de PHP : 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `altimmo`
--

-- --------------------------------------------------------

--
-- Structure de la table `biens`
--

DROP TABLE IF EXISTS `biens`;
CREATE TABLE IF NOT EXISTS `biens` (
  `id_b` int NOT NULL AUTO_INCREMENT,
  `ville` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cp` varchar(12) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prix` decimal(12,2) NOT NULL,
  `superficie` decimal(8,2) NOT NULL,
  `pieces` int NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `cat_id` int DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `u_id` int DEFAULT NULL,
  `vendu` tinyint(1) NOT NULL DEFAULT '0',
  `date_v` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_b`),
  KEY `fk_biens_categories` (`cat_id`),
  KEY `fk_biens_users` (`u_id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `biens`
--

INSERT INTO `biens` (`id_b`, `ville`, `cp`, `prix`, `superficie`, `pieces`, `description`, `cat_id`, `image`, `u_id`, `vendu`, `date_v`) VALUES
(1, 'Paris', '75015', 650000.00, 72.00, 3, 'Appartement lumineux avec balcon proche des commerces et transports.', 1, 'assets/img/biens/bien_20260523_113458_e63132c0.jpg', 2, 0, '2025-01-08 10:15:00'),
(2, 'Lyon', '69003', 420000.00, 85.00, 4, 'Appartement familial situé dans un quartier dynamique.', 1, 'assets/img/biens/bien_20260523_113532_e7d5d881.png', 2, 0, '2025-01-18 14:30:00'),
(3, 'Marseille', '13008', 780000.00, 140.00, 5, 'Villa avec jardin et terrasse proche de la mer.', 3, 'assets/img/biens/bien_20260523_113623_c36fd1a6.webp', 2, 0, '2025-02-03 09:45:00'),
(4, 'Toulouse', '31000', 295000.00, 58.00, 2, 'Deux pièces agréable en centre-ville avec belle exposition.', 1, 'assets/img/biens/bien_20260523_113653_ebd34262.jpg', 2, 0, '2025-02-14 16:20:00'),
(5, 'Nice', '06000', 520000.00, 76.00, 3, 'Appartement avec vue dégagée à proximité de la promenade.', 1, 'assets/img/biens/bien_20260523_113728_3b819ca1.jpg', 2, 0, '2025-02-27 11:10:00'),
(6, 'Nantes', '44000', 360000.00, 95.00, 4, 'Maison de ville rénovée avec petite cour intérieure.', 2, 'assets/img/biens/bien_20260523_113839_35dbe0c2.jpg', 2, 0, '2025-03-05 13:25:00'),
(7, 'Bordeaux', '33000', 890000.00, 160.00, 6, 'Maison en pierre avec jardin et prestations haut de gamme.', 2, 'assets/img/biens/bien_20260523_113920_10d32286.jpg', 2, 0, '2025-03-16 15:40:00'),
(8, 'Lille', '59000', 210000.00, 42.00, 2, 'Studio moderne idéal pour étudiant ou investissement locatif.', 4, 'assets/img/biens/bien_20260523_114508_13862550.jpg', 2, 1, '2025-03-29 08:55:00'),
(9, 'Strasbourg', '67000', 470000.00, 110.00, 5, 'Duplex spacieux dans un secteur calme et recherché.', 8, 'assets/img/biens/bien_20260523_114424_f89dd43a.jpg', 2, 0, '2025-04-07 12:05:00'),
(10, 'Montpellier', '34000', 315000.00, 67.00, 3, 'Appartement récent avec terrasse et place de parking.', 1, 'assets/img/biens/bien_20260523_114354_858cb88a.jpg', 2, 0, '2025-04-19 17:35:00'),
(11, 'Rennes', '35000', 150000.00, 28.00, 1, 'Studio proche université et transports.', 4, 'assets/img/biens/bien_20260523_114241_ef2123c7.jpg', 2, 0, '2025-05-02 09:20:00'),
(12, 'Grenoble', '38000', 250000.00, 62.00, 3, 'Appartement traversant avec cave dans résidence calme.', 1, 'assets/img/biens/bien_20260523_114204_4d4bd96d.jpg', 2, 0, '2025-05-13 10:50:00'),
(13, 'Dijon', '21000', 580000.00, 130.00, 5, 'Maison familiale avec jardin et garage.', 2, 'assets/img/biens/bien_20260523_114132_c0a3c880.webp', 2, 0, '2025-05-24 14:15:00'),
(14, 'Annecy', '74000', 720000.00, 98.00, 4, 'Appartement proche du lac avec grande terrasse.', 1, 'assets/img/biens/bien_20260523_114056_dcb48331.jpg', 2, 0, '2025-06-04 11:45:00'),
(15, 'Tours', '37000', 99000.00, 18.00, 1, 'Résidence étudiante proche du centre et des écoles.', 15, 'assets/img/biens/bien_20260523_113218_c2a4e1fe.jpg', 2, 1, '2025-06-18 16:30:00'),
(16, 'Versailles', '78000', 735000.00, 92.00, 4, 'Appartement élégant proche du château avec séjour lumineux.', 1, 'assets/img/biens/bien_20260523_113146_5b130069.jpg', 2, 0, '2025-07-01 09:10:00'),
(17, 'Cergy', '95000', 310000.00, 84.00, 4, 'Maison familiale avec jardin dans un quartier calme.', 2, 'assets/img/biens/bien_20260523_113109_60f3bb8a.webp', 2, 0, '2025-07-12 13:55:00'),
(18, 'Biarritz', '64200', 980000.00, 145.00, 6, 'Villa proche plage avec terrasse et belles prestations.', 3, 'assets/img/biens/bien_20260523_113025_a71045cf.webp', 2, 0, '2025-07-26 15:25:00'),
(19, 'Nancy', '54000', 155000.00, 32.00, 1, 'Studio lumineux idéal pour étudiant.', 4, 'assets/img/biens/bien_20260523_112932_e5b79382.jpg', 2, 0, '2025-08-06 10:35:00'),
(20, 'Lyon', '69007', 420000.00, 90.00, 3, 'Local commercial bien placé avec vitrine sur rue passante.', 5, 'assets/img/biens/bien_20260523_112847_ff970d81.jpg', 2, 0, '2025-08-20 17:00:00'),
(21, 'Orléans', '45000', 180000.00, 650.00, 1, 'Terrain constructible situé dans un secteur résidentiel.', 6, 'assets/img/biens/bien_20260523_112609_38d88f35.webp', 2, 0, '2025-09-03 08:40:00'),
(22, 'Roubaix', '59100', 275000.00, 118.00, 4, 'Loft rénové avec grands volumes et belle hauteur sous plafond.', 7, 'assets/img/biens/bien_20260523_112525_d5972148.jpg', 2, 0, '2025-09-15 12:30:00'),
(23, 'Saint-Denis', '93200', 39000.00, 12.00, 1, 'Place de parking sécurisée en sous-sol.', 9, 'assets/img/biens/bien_20260523_112453_62fac3e3.jpg', 2, 0, '2025-09-27 14:50:00'),
(24, 'Nantes', '44000', 295000.00, 85.00, 4, 'Bureau lumineux proche centre-ville avec salle de réunion.', 10, 'assets/img/biens/bien_20260523_112358_5ae36349.jpg', 2, 0, '2025-10-08 09:25:00'),
(25, 'Lille', '59000', 1150000.00, 420.00, 12, 'Immeuble de rapport composé de plusieurs lots.', 11, 'assets/img/biens/bien_20260523_112325_35b4241f.jpg', 2, 0, '2025-10-21 16:10:00'),
(26, 'Chamonix-Mont-Blanc', '74400', 890000.00, 135.00, 5, 'Chalet avec vue montagne, terrasse et cheminée.', 12, 'assets/img/biens/bien_20260523_112128_779be73d.jpg', 2, 0, '2025-11-02 11:15:00'),
(27, 'Dijon', '21000', 520000.00, 190.00, 7, 'Ferme rénovée avec dépendances et grand terrain.', 13, 'assets/img/biens/bien_20260523_112054_50f91722.webp', 2, 0, '2025-11-11 15:45:00'),
(28, 'Paris', '75016', 680000.00, 75.00, 3, 'Péniche aménagée avec terrasse sur la Seine.', 14, 'assets/img/biens/bien_20260523_112024_21d9d74c.jpg', 2, 0, '2025-11-19 10:05:00'),
(29, 'Poitiers', '86000', 105000.00, 24.00, 1, 'Studio proche université, parfait pour investissement.', 4, 'assets/img/biens/bien_20260523_111856_f8576142.webp', 2, 0, '2025-11-28 13:20:00'),
(30, 'Angers', '49000', 210000.00, 720.00, 1, 'Terrain viabilisé idéal pour projet de construction.', 6, 'assets/img/biens/bien_20260523_111820_4cc7f9b5.jpg', 2, 0, '2025-12-03 09:50:00'),
(31, 'Saint-Étienne', '42000', 185000.00, 95.00, 3, 'Loft industriel rénové avec espace ouvert.', 7, 'assets/img/biens/bien_20260523_111725_5ed0976d.jpg', 2, 0, '2025-12-06 14:35:00'),
(32, 'Montpellier', '34000', 455000.00, 105.00, 4, 'Duplex avec terrasse et parking dans résidence récente.', 8, 'assets/img/biens/bien_20260523_111559_ed6ac318.webp', 2, 0, '2025-12-09 16:25:00'),
(33, 'Nice', '06000', 42000.00, 11.00, 1, 'Parking fermé proche centre-ville et tramway.', 9, 'assets/img/biens/bien_20260523_111455_17d9ea66.jpg', 2, 0, '2025-12-12 10:40:00'),
(34, 'Grenoble', '38000', 245000.00, 70.00, 3, 'Bureau bien situé avec accès transports.', 10, 'assets/img/biens/bien_20260523_111356_eddd2f95.webp', 2, 0, '2025-12-15 12:10:00'),
(35, 'Metz', '57000', 980000.00, 360.00, 10, 'Immeuble ancien rénové avec plusieurs appartements.', 11, 'assets/img/biens/bien_20260523_111323_8dffd8a2.webp', 2, 0, '2025-12-18 15:30:00'),
(36, 'Annecy', '74000', 990000.00, 145.00, 6, 'Chalet moderne proche lac et montagnes.', 12, 'assets/img/biens/bien_20260523_111225_56ef5ed9.webp', 2, 0, '2025-12-20 09:15:00'),
(37, 'Limoges', '87000', 430000.00, 210.00, 8, 'Ferme avec grange, jardin et grand espace extérieur.', 13, 'assets/img/biens/bien_20260523_111133_721f7ac2.jpg', 2, 1, '2025-12-22 11:55:00'),
(38, 'Lyon', '69002', 590000.00, 62.00, 2, 'Péniche rénovée avec espace de vie atypique.', 14, 'assets/img/biens/bien_20260523_111103_6c234595.jpg', 2, 0, '2025-12-24 14:05:00'),
(39, 'Caen', '14000', 255000.00, 65.00, 3, 'Appartement rénové avec balcon proche centre-ville.', 1, 'assets/img/biens/bien_20260523_111010_94ba9268.jpg', 2, 0, '2025-12-27 16:45:00'),
(40, 'Arras', '62000', 310000.00, 88.00, 3, 'Local commercial avec bonne visibilité en centre-ville.', 5, 'assets/img/biens/bien_20260523_110837_7fe990b2.jpg', 2, 0, '2025-12-30 10:30:00');

-- --------------------------------------------------------

--
-- Structure de la table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id_cat` int NOT NULL AUTO_INCREMENT,
  `nom_cat` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id_cat`),
  UNIQUE KEY `nom_cat` (`nom_cat`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `categories`
--

INSERT INTO `categories` (`id_cat`, `nom_cat`) VALUES
(1, 'Appartement'),
(10, 'Bureau'),
(12, 'Chalet'),
(8, 'Duplex'),
(13, 'Ferme'),
(11, 'Immeuble'),
(5, 'Local commercial'),
(7, 'Loft'),
(2, 'Maison'),
(9, 'Parking'),
(14, 'Péniche'),
(15, 'Résidence étudiante'),
(4, 'Studio'),
(6, 'Terrain'),
(3, 'Villa');

-- --------------------------------------------------------

--
-- Structure de la table `envoyer`
--

DROP TABLE IF EXISTS `envoyer`;
CREATE TABLE IF NOT EXISTS `envoyer` (
  `id_env` int NOT NULL AUTO_INCREMENT,
  `id_exp` int DEFAULT NULL,
  `id_recept` int DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_env` datetime DEFAULT CURRENT_TIMESTAMP,
  `lu` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_env`),
  KEY `fk_envoyer_exp` (`id_exp`),
  KEY `fk_envoyer_recept` (`id_recept`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `envoyer`
--

INSERT INTO `envoyer` (`id_env`, `id_exp`, `id_recept`, `message`, `date_env`, `lu`) VALUES
(1, 3, 2, 'Bonjour, je suis intéressé par l?appartement à Paris. Est-il encore disponible ?', '2026-05-23 12:46:39', 1),
(2, 4, 2, 'Pouvez-vous me donner plus d?informations sur le bien situé à Lyon ?', '2026-05-23 12:46:39', 1),
(3, 5, 2, 'Bonjour, je souhaite organiser une visite pour la villa à Marseille.', '2026-05-23 12:46:39', 1),
(4, 6, 2, 'Le studio à Lille est-il toujours en vente ?', '2026-05-23 12:46:39', 1),
(5, 7, 2, 'Bonjour, le prix du bien à Bordeaux est-il négociable ?', '2026-05-23 12:46:39', 1),
(6, 8, 2, 'Je voudrais visiter le duplex à Strasbourg cette semaine.', '2026-05-23 12:46:39', 1),
(7, 9, 2, 'Est-ce que l?appartement à Nice dispose d?un ascenseur ?', '2026-05-23 12:46:39', 1),
(8, 10, 2, 'Bonjour, avez-vous des photos supplémentaires du bien à Montpellier ?', '2026-05-23 12:46:39', 1),
(9, 11, 2, 'Je suis intéressé par la maison à Dijon.', '2026-05-23 12:46:39', 1),
(10, 12, 2, 'Le bien à Annecy est-il proche des transports ?', '2026-05-23 12:46:39', 1),
(11, 13, 2, 'Bonjour, je cherche un studio pour un étudiant à Rennes.', '2026-05-23 12:46:39', 1),
(12, 14, 2, 'Pouvez-vous me rappeler concernant le logement à Tours ?', '2026-05-23 12:46:39', 1),
(13, 15, 2, 'Merci pour votre retour, je suis disponible demain après-midi.', '2026-05-23 12:46:39', 1),
(14, 3, 1, 'Je souhaite recevoir le dossier complet du bien à Nantes.', '2026-05-23 12:46:39', 0),
(15, 4, 1, 'Bonjour, y a-t-il des frais de copropriété pour l?appartement à Grenoble ?', '2026-05-23 12:46:39', 0);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id_u` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `email` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_u`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id_u`, `nom`, `prenom`, `email`, `password`, `ip`, `role`, `created_at`) VALUES
(1, 'saadi', 'aylan', 'aylan@gmail.com', '$2y$10$3Ve/j0A6gi4oD9Cc5dUO.Opi/XXLznpo4fK8OpcNpO8Zinx4qIma2', '::1', 2, '2026-05-23 10:37:29'),
(2, 'saadi', 'ATF', 'atef@gmail.com', '$2y$10$LVxcemkgNDtD4xK8hH1/leXx2wZxKoGxNJ3XnpJgYgILiWlhJOOkG', '::1', 3, '2026-05-23 10:38:52'),
(3, 'Benali', 'Yanis', 'yanis.benali@altimmo.local', '$2y$10$Wrc8lMOOpeIw0bFJ5/jJG.1t94BcWdjwlhPDFEkTu1fGJ25pmr.t.', NULL, 1, '2026-05-23 10:46:38'),
(4, 'Dubois', 'Emma', 'emma.dubois@altimmo.local', '$2y$10$Wrc8lMOOpeIw0bFJ5/jJG.1t94BcWdjwlhPDFEkTu1fGJ25pmr.t.', NULL, 1, '2026-05-23 10:46:38'),
(5, 'Nguyen', 'Lina', 'lina.nguyen@altimmo.local', '$2y$10$Wrc8lMOOpeIw0bFJ5/jJG.1t94BcWdjwlhPDFEkTu1fGJ25pmr.t.', NULL, 1, '2026-05-23 10:46:38'),
(6, 'Garcia', 'Lucas', 'lucas.garcia@altimmo.local', '$2y$10$Wrc8lMOOpeIw0bFJ5/jJG.1t94BcWdjwlhPDFEkTu1fGJ25pmr.t.', NULL, 1, '2026-05-23 10:46:38'),
(7, 'Diop', 'Aminata', 'aminata.diop@altimmo.local', '$2y$10$Wrc8lMOOpeIw0bFJ5/jJG.1t94BcWdjwlhPDFEkTu1fGJ25pmr.t.', NULL, 1, '2026-05-23 10:46:38'),
(8, 'Bernard', 'Hugo', 'hugo.bernard@altimmo.local', '$2y$10$Wrc8lMOOpeIw0bFJ5/jJG.1t94BcWdjwlhPDFEkTu1fGJ25pmr.t.', NULL, 1, '2026-05-23 10:46:38'),
(9, 'Rossi', 'Sofia', 'sofia.rossi@altimmo.local', '$2y$10$Wrc8lMOOpeIw0bFJ5/jJG.1t94BcWdjwlhPDFEkTu1fGJ25pmr.t.', NULL, 1, '2026-05-23 10:46:38'),
(10, 'Moreau', 'Nicolas', 'nicolas.moreau@altimmo.local', '$2y$10$Wrc8lMOOpeIw0bFJ5/jJG.1t94BcWdjwlhPDFEkTu1fGJ25pmr.t.', NULL, 1, '2026-05-23 10:46:38'),
(11, 'Khaldi', 'Sara', 'sara.khaldi@altimmo.local', '$2y$10$Wrc8lMOOpeIw0bFJ5/jJG.1t94BcWdjwlhPDFEkTu1fGJ25pmr.t.', NULL, 1, '2026-05-23 10:46:38'),
(12, 'Petit', 'Antoine', 'antoine.petit@altimmo.local', '$2y$10$Wrc8lMOOpeIw0bFJ5/jJG.1t94BcWdjwlhPDFEkTu1fGJ25pmr.t.', NULL, 1, '2026-05-23 10:46:38'),
(13, 'Santos', 'Inès', 'ines.santos@altimmo.local', '$2y$10$Wrc8lMOOpeIw0bFJ5/jJG.1t94BcWdjwlhPDFEkTu1fGJ25pmr.t.', NULL, 1, '2026-05-23 10:46:38'),
(14, 'Leroy', 'Mehdi', 'mehdi.leroy@altimmo.local', '$2y$10$Wrc8lMOOpeIw0bFJ5/jJG.1t94BcWdjwlhPDFEkTu1fGJ25pmr.t.', NULL, 1, '2026-05-23 10:46:38'),
(15, 'Chen', 'Julie', 'julie.chen@altimmo.local', '$2y$10$Wrc8lMOOpeIw0bFJ5/jJG.1t94BcWdjwlhPDFEkTu1fGJ25pmr.t.', NULL, 1, '2026-05-23 10:46:38');

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `biens`
--
ALTER TABLE `biens`
  ADD CONSTRAINT `fk_biens_categories` FOREIGN KEY (`cat_id`) REFERENCES `categories` (`id_cat`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_biens_users` FOREIGN KEY (`u_id`) REFERENCES `users` (`id_u`) ON DELETE SET NULL;

--
-- Contraintes pour la table `envoyer`
--
ALTER TABLE `envoyer`
  ADD CONSTRAINT `fk_envoyer_exp` FOREIGN KEY (`id_exp`) REFERENCES `users` (`id_u`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_envoyer_recept` FOREIGN KEY (`id_recept`) REFERENCES `users` (`id_u`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
