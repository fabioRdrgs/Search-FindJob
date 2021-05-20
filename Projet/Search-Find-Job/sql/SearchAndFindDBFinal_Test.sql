-- phpMyAdmin SQL Dump
-- version 4.9.7
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : jeu. 20 mai 2021 à 16:04
-- Version du serveur :  10.3.27-MariaDB-0+deb10u1
-- Version de PHP : 7.3.27-1~deb10u1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `SearchAndFindDBFinal_Test`
--
CREATE DATABASE IF NOT EXISTS `SearchAndFindDBFinal_Test` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `SearchAndFindDBFinal_Test`;

-- --------------------------------------------------------

--
-- Structure de la table `annonces`
--

CREATE TABLE `annonces` (
  `id` int(10) UNSIGNED NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `date_publication` datetime NOT NULL DEFAULT current_timestamp(),
  `titre` varchar(255) NOT NULL,
  `description` longtext NOT NULL,
  `media_path` varchar(255) DEFAULT NULL,
  `media_nom` varchar(45) DEFAULT NULL,
  `media_type` enum('png','pdf','jpg','jpeg','bmp') DEFAULT NULL,
  `utilisateurs_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `annonces`
--

INSERT INTO `annonces` (`id`, `date_debut`, `date_fin`, `date_publication`, `titre`, `description`, `media_path`, `media_nom`, `media_type`, `utilisateurs_id`) VALUES
(18, '2021-05-10', '2021-06-30', '2021-05-18 13:26:04', 'Annonce1', 'C&#39;est la première annonce, 1', './tmp/', '60a66b775bcbe', 'png', 2),
(22, '2021-05-17', '2021-07-25', '2021-05-20 15:57:52', 'Annonce2', 'Ceci est une annonce, version 2', NULL, NULL, NULL, 2),
(23, '2021-05-20', '2021-06-30', '2021-05-20 15:59:09', 'Annonce3', 'C&#39;est la troisième annonce créée', './tmp/', '60a66b2de4c87', 'png', 2),
(24, '2021-05-20', '2021-06-20', '2021-05-20 15:59:47', 'Annonce4', 'C&#39;est la 4ème annonce', './tmp/', '60a66b53917bd', 'pdf', 2),
(25, '2021-05-20', '2021-06-20', '2021-05-20 16:00:54', 'Annonce5', 'C&#39;est une annonce cachée', NULL, NULL, NULL, 2),
(26, '2021-05-03', '2021-05-16', '2021-05-20 16:01:21', 'Annonce non disponible', 'Cette annonce n&#39;est pas visible', NULL, NULL, NULL, 2);

-- --------------------------------------------------------

--
-- Structure de la table `annonces_has_keywords`
--

CREATE TABLE `annonces_has_keywords` (
  `annonces_id` int(10) UNSIGNED NOT NULL,
  `keywords_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `annonces_has_keywords`
--

INSERT INTO `annonces_has_keywords` (`annonces_id`, `keywords_id`) VALUES
(18, 9),
(18, 15),
(22, 8),
(22, 15),
(23, 8),
(23, 9),
(23, 11),
(24, 10),
(24, 15),
(25, 9),
(25, 10),
(26, 9);

-- --------------------------------------------------------

--
-- Structure de la table `keywords`
--

CREATE TABLE `keywords` (
  `id` int(10) UNSIGNED NOT NULL,
  `label` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `keywords`
--

INSERT INTO `keywords` (`id`, `label`) VALUES
(8, 'PHP'),
(9, 'C#'),
(10, 'HTML'),
(11, 'CSS'),
(15, 'COBOL');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id` int(10) UNSIGNED NOT NULL,
  `login` varchar(45) NOT NULL,
  `password` varchar(60) NOT NULL,
  `type` enum('Admin','Chercheur','Annonceur') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `login`, `password`, `type`) VALUES
(1, '1.1@gmail.com', '$2y$10$jbgH7IAzhvtdDBtNJWJIte2YMIXZNLCOK8KU7Pz1lJaUy.y1Wx0bm', 'Chercheur'),
(2, '2.2@gmail.com', '$2y$10$Am4ieaUTwZSfY1K2UWZrDeYIU4yyQBSvlP2/PcBWcYYj6frsFtlyG', 'Annonceur'),
(3, '3.3@gmail.com', '$2y$10$89s5Nq4q0NovYdLtQv8Au.uBWk.WUc8wT8FvWe0gKv52pnIfgwKwS', 'Admin'),
(4, '4.4@gmail.com', '$2y$10$391oDVgnj8zywMjB6R/Yy.S41q.LaC9WUrIa2CpIyKP0awEkamjKe', 'Annonceur'),
(5, '5.5@gmail.com', '$2y$10$c7tMoru0yc7v7TMeb3H4LOtwbyMOVu0CgBMmOpRG597/iA/7eWN3K', 'Chercheur'),
(6, '6.6@gmail.com', '$2y$10$dcm8Y8SRqWZn6EUZaW500uuzUBwFrAO0FDJt9mGURB6H9irclJ9wi', 'Annonceur');

-- --------------------------------------------------------

--
-- Structure de la table `wishlists`
--

CREATE TABLE `wishlists` (
  `annonces_id` int(10) UNSIGNED NOT NULL,
  `utilisateurs_id` int(10) UNSIGNED NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `wishlists`
--

INSERT INTO `wishlists` (`annonces_id`, `utilisateurs_id`, `date`) VALUES
(18, 1, '2021-05-20 13:17:26'),
(25, 1, '2021-05-20 16:01:42'),
(23, 1, '2021-05-20 16:01:44');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `annonces`
--
ALTER TABLE `annonces`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FOREIGN` (`utilisateurs_id`) USING BTREE;

--
-- Index pour la table `annonces_has_keywords`
--
ALTER TABLE `annonces_has_keywords`
  ADD KEY `FOREIGN` (`annonces_id`,`keywords_id`) USING BTREE,
  ADD KEY `ahk_keywords_id` (`keywords_id`);

--
-- Index pour la table `keywords`
--
ALTER TABLE `keywords`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `wishlists`
--
ALTER TABLE `wishlists`
  ADD KEY `FOREIGN` (`annonces_id`,`utilisateurs_id`) USING BTREE,
  ADD KEY `wishlists_utilisateurs_id` (`utilisateurs_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `annonces`
--
ALTER TABLE `annonces`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT pour la table `keywords`
--
ALTER TABLE `keywords`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `annonces`
--
ALTER TABLE `annonces`
  ADD CONSTRAINT `annonces_utilisateurs_id` FOREIGN KEY (`utilisateurs_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `annonces_has_keywords`
--
ALTER TABLE `annonces_has_keywords`
  ADD CONSTRAINT `ahk_annonces_id` FOREIGN KEY (`annonces_id`) REFERENCES `annonces` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ahk_keywords_id` FOREIGN KEY (`keywords_id`) REFERENCES `keywords` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `wishlists`
--
ALTER TABLE `wishlists`
  ADD CONSTRAINT `wishlists_annonces_id` FOREIGN KEY (`annonces_id`) REFERENCES `annonces` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `wishlists_utilisateurs_id` FOREIGN KEY (`utilisateurs_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
