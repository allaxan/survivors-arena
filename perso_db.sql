-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : jeu. 12 fév. 2026 à 08:12
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
-- Base de données : `perso_db`
--

-- --------------------------------------------------------

--
-- Structure de la table `personnages`
--

DROP TABLE IF EXISTS `personnages`;
CREATE TABLE IF NOT EXISTS `personnages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) NOT NULL,
  `type` varchar(20) NOT NULL,
  `atk` int NOT NULL,
  `defense` int DEFAULT '0',
  `pv` int NOT NULL,
  `energie` int DEFAULT '0',
  `rarete` varchar(20) DEFAULT 'commun',
  `faction` varchar(30) DEFAULT 'indépendant',
  `description` text,
  `image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `personnages`
--

INSERT INTO `personnages` (`id`, `nom`, `type`, `atk`, `defense`, `pv`, `energie`, `rarete`, `faction`, `description`, `image`) VALUES
(1, 'Joel Miller', 'survivant', 85, 70, 30, 50, 'légendaire', 'Jackson', 'Survivant endurci, prêt à tout pour protéger ceux qu’il aime.', './assets/image/joel.png'),
(2, 'Ellie Williams', 'survivante', 75, 55, 47, 61, 'légendaire', 'Jackson', 'Immunisée, déterminée et redoutable malgré son jeune âge.', './assets/image/ellie.png'),
(3, 'Abby Anderson', 'soldate', 90, 80, 100, 40, 'épique', 'WLF', 'Combattante puissante animée par la vengeance.', './assets/image/abby.webp'),
(4, 'Tommy Miller', 'sniper', 80, 60, 10, 45, 'rare', 'Jackson', 'Ancien Firefly, expert en armes à longue portée.', './assets/image/tommy.webp'),
(5, 'Tess', 'contrebandière', 70, 65, 95, 40, 'rare', 'Boston QZ', 'Femme forte et pragmatique, survivante jusqu’au bout.', './assets/image/tess.webp'),
(6, 'Dina', 'éclaireuse', 65, 50, 100, 60, 'rare', 'Jackson', 'Intelligente et loyale, elle soutient Ellie dans son périple.', './assets/image/dina.webp'),
(7, 'Marlene', 'cheffe', 60, 55, 0, 63, 'épique', 'Fireflies', 'Leader déterminée des Lucioles, prête à tout pour l’humanité.', './assets/image/marlene.webp'),
(8, 'Clicker', 'infecté', 70, 40, 100, 0, 'commun', 'infectés', 'Infecté aveugle utilisant l’écholocalisation.', './assets/image/claqueur.jpg'),
(9, 'Bloater', 'infecté', 95, 90, 100, 0, 'légendaire', 'infectés', 'Créature massive, extrêmement résistante.', './assets/image/colosse.webp'),
(10, 'Runner', 'infecté', 40, 20, 100, 0, 'commun', 'infectés', 'Infecté récemment, rapide mais fragile.', './assets/image/coureur.webp'),
(11, 'Stalker', 'infecté', 55, 30, 100, 0, 'rare', 'infectés', 'Infecté plus avancé, plus furtif et dangereux.', './assets/image/stalker.webp'),
(12, 'Shambler', 'infecté', 90, 70, 100, 0, 'légendaire', 'infectés', 'Infecté avancé libérant de l’acide brûlant, très dangereux.', './assets/image/shambler.webp'),
(13, 'Manny', 'guerrier', 75, 55, 100, 45, 'rare', 'WLF', 'Membre robuste et courageux du WLF, souvent en première ligne.', './assets/image/manny.webp'),
(14, 'Nora', 'sniper', 80, 50, 100, 60, 'épique', 'WLF', 'Sniper du WLF, mortelle à longue distance et stratégique.', './assets/image/nora.webp'),
(15, 'Mel', 'infiltrateur', 40, 45, 85, 70, 'rare', 'WLF', 'Spécialiste des missions discrètes et de la reconnaissance.', './assets/image/mel.webp'),
(16, 'Jordan', 'soldat', 70, 65, 100, 40, 'rare', 'WLF', 'Membre du WLF, solide au corps à corps et loyal à ses alliés.', './assets/image/jordan.webp');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
