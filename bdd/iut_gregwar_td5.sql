-- phpMyAdmin SQL Dump
-- version 4.1.4
-- http://www.phpmyadmin.net
--
-- Client :  127.0.0.1
-- Généré le :  Sam 17 Janvier 2015 à 00:16
-- Version du serveur :  5.6.15-log
-- Version de PHP :  5.5.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données :  `iut_gregwar_td5`
--

-- --------------------------------------------------------

--
-- Structure de la table `answers`
--

CREATE TABLE IF NOT EXISTS `answers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `poll_id` int(11) NOT NULL,
  `answer` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Contenu de la table `answers`
--

INSERT INTO `answers` (`id`, `user_id`, `poll_id`, `answer`) VALUES
(1, 3, 1, 2),
(2, 1, 1, 1),
(3, 2, 1, 2),
(4, 4, 2, 1),
(5, 4, 3, 4),
(6, 1, 3, 3);

-- --------------------------------------------------------

--
-- Structure de la table `polls`
--

CREATE TABLE IF NOT EXISTS `polls` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question` text NOT NULL,
  `auteur_sondage` text NOT NULL,
  `answers` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Contenu de la table `polls`
--

INSERT INTO `polls` (`id`, `question`, `auteur_sondage`, `answers`) VALUES
(1, 'Pain au chocolat ou chocolatine ?', 'greg', 'Pain au chocolat|Chocolatine'),
(2, 'Série préférée ?', 'apluie', 'Supernatural|X-files|Arrow|Simpsons|Malcolm'),
(3, 'J''ai faim', 'apluie', 'M''en fou|moi je mange|tournevis|Un maxi big mac frite coca a apporter stp');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `login` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Contenu de la table `users`
--

INSERT INTO `users` (`login`, `password`, `id`) VALUES
('a', '0cc175b9c0f1b6a831c399e269772661', 1),
('b', '92eb5ffee6ae2fec3ad71c777531578f', 2),
('greg', '4ca9d3dcd2b6843e62d75eb191887cf2', 3),
('apluie', '39fad1ca852fc5c908ed42783204c437', 4);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
