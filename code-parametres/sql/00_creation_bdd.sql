--
-- Base de donn�es: `libreoffice`
-- version : 1.0 du 26/11/2013 : création de la base de données
--
CREATE DATABASE IF NOT EXISTS `libreoffice` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `libreoffice`;

-- --------------------------------------------------------

--
-- Structure de la table `agents_campagne`
--

CREATE TABLE IF NOT EXISTS `agents_campagne` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ID_AGENT` varchar(20) NOT NULL,
  `ID_CAMPAGNE` int(11) NULL,
  `DATE_CREATION` date NOT NULL,
  `DATE_DEBUT` date DEFAULT NULL,
  `DATE_MIGRATION` date DEFAULT NULL,
  `DATE_RELANCE` date DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `ID_CAMPAGNE` (`ID_CAMPAGNE`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `campagne`
--

CREATE TABLE IF NOT EXISTS `campagne` (
  `NUMERO` int(11) NOT NULL AUTO_INCREMENT,
  `DESCRIPTION` varchar(25) NOT NULL,
  `DATE_DEBUT` date DEFAULT NULL,
  `DATE_BUTOIR` date DEFAULT NULL,
  `DATE_MIGRATION` date DEFAULT NULL,
  `DELAI_AVANT_RELANCE` int(2) NOT NULL,
  PRIMARY KEY (`NUMERO`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `reponse`
--

CREATE TABLE IF NOT EXISTS `reponse` (
  `NUMERO_QUESTION` int(11) NOT NULL,
  `ID_AGENT_CAMPAGNE` int(11) NULL,
  `ID_REPONSE` int(11) NOT NULL,
  `LIBELLE_REPONSE` blob NOT NULL,
  PRIMARY KEY (`NUMERO_QUESTION`, `ID_AGENT_CAMPAGNE`,`ID_REPONSE`),
  KEY `ID_AGENT_CAMPAGNE` (`ID_AGENT_CAMPAGNE`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contraintes pour les tables export�es
--

--
-- Contraintes pour la table `agents_campagne`
--
ALTER TABLE `agents_campagne`
  ADD CONSTRAINT `agents_campagne_ibfk_1` FOREIGN KEY (`ID_CAMPAGNE`) REFERENCES `campagne` (`NUMERO`);

--
-- Contraintes pour la table `reponse`
--
ALTER TABLE `reponse`
  ADD CONSTRAINT `reponse_ibfk_1` FOREIGN KEY (`ID_AGENT_CAMPAGNE`) REFERENCES `agents_campagne` (`ID`);