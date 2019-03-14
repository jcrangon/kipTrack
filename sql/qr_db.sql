-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le :  jeu. 14 mars 2019 à 01:05
-- Version du serveur :  5.7.23
-- Version de PHP :  7.2.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `qr_db`
--

-- --------------------------------------------------------

--
-- Structure de la table `account`
--

DROP TABLE IF EXISTS `account`;
CREATE TABLE IF NOT EXISTS `account` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_membre` int(10) NOT NULL,
  `nom_account` varchar(21) NOT NULL,
  `solde` float NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `account`
--

INSERT INTO `account` (`id`, `id_membre`, `nom_account`, `solde`) VALUES
(17, 1, 'cash1', 187.5);

-- --------------------------------------------------------

--
-- Structure de la table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `nom_categorie` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=59 DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `categories`
--

INSERT INTO `categories` (`id`, `id_user`, `nom_categorie`) VALUES
(50, 1, 'sorties'),
(49, 1, 'chaussures'),
(48, 1, 'vêtements'),
(43, 1, 'courses'),
(45, 1, 'restau'),
(40, 1, 'aucune'),
(39, 1, 'versement');

-- --------------------------------------------------------

--
-- Structure de la table `qr_user`
--

DROP TABLE IF EXISTS `qr_user`;
CREATE TABLE IF NOT EXISTS `qr_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pseudo` varchar(30) NOT NULL,
  `nom` varchar(30) NOT NULL,
  `prenom` varchar(30) NOT NULL,
  `email` varchar(60) NOT NULL,
  `date_de_naissance` date NOT NULL,
  `password` varchar(100) NOT NULL,
  `photo` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `status` int(2) NOT NULL,
  `role` int(2) NOT NULL,
  `hcode` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `validite` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `qr_user`
--

INSERT INTO `qr_user` (`id`, `pseudo`, `nom`, `prenom`, `email`, `date_de_naissance`, `password`, `photo`, `status`, `role`, `hcode`, `validite`) VALUES
(1, 'jcr972', 'Rangon', 'Jean-Christophe', 'jc.rangon@gmail.com', '1974-07-17', '$2y$12$kZ00Kt3fdgAppSK9yj4v0OUUt8xMCx8kyLeDU1pMFY75A8oXTg916', 'default.jpg', 1, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `transactions`
--

DROP TABLE IF EXISTS `transactions`;
CREATE TABLE IF NOT EXISTS `transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `date` date NOT NULL,
  `montant` float NOT NULL,
  `categorie` int(11) NOT NULL,
  `compte` int(11) NOT NULL,
  `moypay` enum('cash','cb') NOT NULL,
  `memo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `transactions`
--

INSERT INTO `transactions` (`id`, `id_user`, `date`, `montant`, `categorie`, `compte`, `moypay`, `memo`) VALUES
(57, 1, '2019-02-09', 12.5, 45, 17, 'cash', ''),
(58, 1, '2019-02-09', 100, 39, 17, 'cash', NULL),
(60, 1, '2019-02-11', 100, 39, 17, 'cash', NULL);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
