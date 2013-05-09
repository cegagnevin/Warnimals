-- ----------------------
-- dump de la base warnimals_db1 au 08-Feb-2013
-- ----------------------


DROP TABLE t_animal;
-- -----------------------------
-- Structure de la table t_animal
-- -----------------------------
CREATE TABLE `t_animal` (
  `idAnimal` varchar(30) NOT NULL,
  `nomAnimal` varchar(50) NOT NULL,
  `vie` int(11) NOT NULL,
  `defense` int(11) NOT NULL,
  `attaque` int(11) NOT NULL,
  `niveau` int(2) NOT NULL DEFAULT '1',
  `RaceAnimal_race` varchar(30) NOT NULL,
  PRIMARY KEY (`idAnimal`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE t_combat;
-- -----------------------------
-- Structure de la table t_combat
-- -----------------------------
CREATE TABLE `t_combat` (
  `idCombat` varchar(30) NOT NULL,
  `dateCombat` int(11) NOT NULL,
  PRIMARY KEY (`idCombat`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE t_competence;
-- -----------------------------
-- Structure de la table t_competence
-- -----------------------------
CREATE TABLE `t_competence` (
  `idCompetence` varchar(30) NOT NULL,
  `nomCompetence` varchar(50) NOT NULL,
  `degats` float NOT NULL,
  PRIMARY KEY (`idCompetence`),
  UNIQUE KEY `nomCompetence` (`nomCompetence`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE t_competence_animal;
-- -----------------------------
-- Structure de la table t_competence_animal
-- -----------------------------
CREATE TABLE `t_competence_animal` (
  `Competence_idCompetence` varchar(30) NOT NULL,
  `Animal_idAnimal` varchar(30) NOT NULL,
  PRIMARY KEY (`Competence_idCompetence`,`Animal_idAnimal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE t_competence_raceanimal;
-- -----------------------------
-- Structure de la table t_competence_raceanimal
-- -----------------------------
CREATE TABLE `t_competence_raceanimal` (
  `Competence_idCompetence` varchar(30) NOT NULL,
  `RaceAnimal_idRace` varchar(30) NOT NULL,
  `niveauRequis` int(11) NOT NULL,
  PRIMARY KEY (`Competence_idCompetence`,`RaceAnimal_idRace`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE t_entrainement;
-- -----------------------------
-- Structure de la table t_entrainement
-- -----------------------------
CREATE TABLE `t_entrainement` (
  `idEntrainement` varchar(30) NOT NULL,
  `duree` int(11) NOT NULL,
  `prix` int(11) NOT NULL,
  `dateDebut` int(11) NOT NULL,
  `type` varchar(20) NOT NULL,
  `niveauMax` int(11) NOT NULL,
  `nbParticipantsMin` int(11) NOT NULL,
  `annule` tinyint(1) NOT NULL DEFAULT '0',
  `OffreEntrainement_idOffre` varchar(30) NOT NULL,
  PRIMARY KEY (`idEntrainement`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE t_entrainement_animal;
-- -----------------------------
-- Structure de la table t_entrainement_animal
-- -----------------------------
CREATE TABLE `t_entrainement_animal` (
  `Entrainement_idEntrainement` varchar(30) NOT NULL,
  `Animal_idAnimal` varchar(30) NOT NULL,
  `dateSouscription` int(11) NOT NULL,
  `valide` tinyint(1) NOT NULL,
  PRIMARY KEY (`Entrainement_idEntrainement`,`Animal_idAnimal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE t_entrainementoffre;
-- -----------------------------
-- Structure de la table t_entrainementoffre
-- -----------------------------
CREATE TABLE `t_entrainementoffre` (
  `idOffre` varchar(30) NOT NULL,
  `attaque_offre` int(11) NOT NULL,
  `defense_offre` int(11) NOT NULL,
  `vie_offre` int(11) NOT NULL,
  `levelUp` tinyint(1) NOT NULL,
  PRIMARY KEY (`idOffre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE t_joueur;
-- -----------------------------
-- Structure de la table t_joueur
-- -----------------------------
CREATE TABLE `t_joueur` (
  `idFacebook` varchar(100) NOT NULL,
  `credit` int(11) NOT NULL,
  `dateInscription` int(11) NOT NULL,
  PRIMARY KEY (`idFacebook`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE t_joueur_animal;
-- -----------------------------
-- Structure de la table t_joueur_animal
-- -----------------------------
CREATE TABLE `t_joueur_animal` (
  `Joueur_idFacebook` varchar(100) NOT NULL,
  `Animal_idAnimal` varchar(30) NOT NULL,
  PRIMARY KEY (`Joueur_idFacebook`,`Animal_idAnimal`),
  UNIQUE KEY `Animal_idAnimal` (`Animal_idAnimal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE t_pari;
-- -----------------------------
-- Structure de la table t_pari
-- -----------------------------
CREATE TABLE `t_pari` (
  `idPari` varchar(30) NOT NULL,
  `Combat_idCombat` varchar(30) NOT NULL,
  `Joueur_idFacebook` varchar(30) NOT NULL,
  `montantPari` int(11) NOT NULL,
  `datePari` int(11) NOT NULL,
  PRIMARY KEY (`idPari`),
  UNIQUE KEY `Combat_idCombat` (`Combat_idCombat`,`Joueur_idFacebook`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE t_raceanimal;
-- -----------------------------
-- Structure de la table t_raceanimal
-- -----------------------------
CREATE TABLE `t_raceanimal` (
  `idRace` varchar(30) NOT NULL,
  `nomRace` varchar(100) NOT NULL,
  `vie_defaut` int(11) NOT NULL,
  `defense_defaut` int(11) NOT NULL,
  `attaque_defaut` int(11) NOT NULL,
  PRIMARY KEY (`idRace`),
  UNIQUE KEY `nomRace` (`nomRace`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE t_transaction;
-- -----------------------------
-- Structure de la table t_transaction
-- -----------------------------
CREATE TABLE `t_transaction` (
  `idTransaction` varchar(30) NOT NULL,
  `Animal_idAnimal` varchar(30) NOT NULL,
  `dateTransaction` int(11) NOT NULL,
  `montantDepart` int(11) NOT NULL,
  `montantFinal` int(11) NOT NULL,
  `etat` int(11) NOT NULL,
  PRIMARY KEY (`idTransaction`),
  UNIQUE KEY `Animal_idAnimal` (`Animal_idAnimal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



-- -----------------------------
-- Contenu de la table t_animal
-- -----------------------------
INSERT INTO t_animal VALUES('A3', 'Herisson', 8, 20, 30, 1, 'R0003');
INSERT INTO t_animal VALUES('A1', 'T-Rex', 20, 10, 20, 1, 'R0001');
INSERT INTO t_animal VALUES('A2', 'Lapin', 12, 15, 18, 1, 'R0002');

-- -----------------------------
-- Contenu de la table t_combat
-- -----------------------------

-- -----------------------------
-- Contenu de la table t_competence
-- -----------------------------
INSERT INTO t_competence VALUES('C0001', 'Coup Assommant', 5);
INSERT INTO t_competence VALUES('C0002', 'Carapace Miroir', 7);
INSERT INTO t_competence VALUES('C0003', 'Machoire d\'Acier', 10);

-- -----------------------------
-- Contenu de la table t_competence_animal
-- -----------------------------

-- -----------------------------
-- Contenu de la table t_competence_raceanimal
-- -----------------------------
INSERT INTO t_competence_raceanimal VALUES('C0001', 'R0002', 2);
INSERT INTO t_competence_raceanimal VALUES('C0002', 'R0003', 2);
INSERT INTO t_competence_raceanimal VALUES('C0003', 'R0001', 2);

-- -----------------------------
-- Contenu de la table t_entrainement
-- -----------------------------
INSERT INTO t_entrainement VALUES('E2', 62250, 1245, 1360259108, 'collectif', 30, 2, 0, 'O10');
INSERT INTO t_entrainement VALUES('E3', 62850, 1257, 1360266538, 'collectif', 29, 2, 0, 'O17');

-- -----------------------------
-- Contenu de la table t_entrainement_animal
-- -----------------------------

-- -----------------------------
-- Contenu de la table t_entrainementoffre
-- -----------------------------
INSERT INTO t_entrainementoffre VALUES('O1', 3, 1, 1, 0);
INSERT INTO t_entrainementoffre VALUES('O10', 0, 1, 0, 0);
INSERT INTO t_entrainementoffre VALUES('O11', 0, 0, 1, 0);
INSERT INTO t_entrainementoffre VALUES('O12', 0, 0, 0, 1);
INSERT INTO t_entrainementoffre VALUES('O13', 0, 1, 1, 0);
INSERT INTO t_entrainementoffre VALUES('O14', 0, 0, 1, 1);
INSERT INTO t_entrainementoffre VALUES('O15', 1, 0, 0, 1);
INSERT INTO t_entrainementoffre VALUES('O16', 2, 0, 0, 0);
INSERT INTO t_entrainementoffre VALUES('O17', 0, 2, 0, 0);
INSERT INTO t_entrainementoffre VALUES('O18', 0, 0, 2, 0);
INSERT INTO t_entrainementoffre VALUES('O19', 0, 1, 1, 1);
INSERT INTO t_entrainementoffre VALUES('O2', 0, 2, 4, 0);
INSERT INTO t_entrainementoffre VALUES('O20', 1, 1, 1, 0);
INSERT INTO t_entrainementoffre VALUES('O21', 2, 1, 0, 0);
INSERT INTO t_entrainementoffre VALUES('O22', 0, 2, 1, 0);
INSERT INTO t_entrainementoffre VALUES('O23', 0, 0, 2, 1);
INSERT INTO t_entrainementoffre VALUES('O24', 2, 0, 0, 1);
INSERT INTO t_entrainementoffre VALUES('O25', 1, 1, 2, 0);
INSERT INTO t_entrainementoffre VALUES('O26', 2, 1, 1, 0);
INSERT INTO t_entrainementoffre VALUES('O27', 1, 2, 1, 0);
INSERT INTO t_entrainementoffre VALUES('O28', 2, 1, 0, 1);
INSERT INTO t_entrainementoffre VALUES('O29', 1, 2, 0, 1);
INSERT INTO t_entrainementoffre VALUES('O3', 1, 1, 1, 1);
INSERT INTO t_entrainementoffre VALUES('O30', 4, 0, 0, 0);
INSERT INTO t_entrainementoffre VALUES('O31', 0, 4, 0, 0);
INSERT INTO t_entrainementoffre VALUES('O32', 0, 0, 4, 0);
INSERT INTO t_entrainementoffre VALUES('O33', 3, 2, 1, 0);
INSERT INTO t_entrainementoffre VALUES('O34', 2, 1, 3, 0);
INSERT INTO t_entrainementoffre VALUES('O35', 1, 1, 2, 1);
INSERT INTO t_entrainementoffre VALUES('O36', 1, 2, 2, 0);
INSERT INTO t_entrainementoffre VALUES('O37', 0, 0, 5, 0);
INSERT INTO t_entrainementoffre VALUES('O38', 2, 1, 1, 1);
INSERT INTO t_entrainementoffre VALUES('O4', 0, 5, 0, 0);
INSERT INTO t_entrainementoffre VALUES('O5', 2, 2, 2, 0);
INSERT INTO t_entrainementoffre VALUES('O6', 1, 2, 2, 1);
INSERT INTO t_entrainementoffre VALUES('O7', 1, 0, 1, 0);
INSERT INTO t_entrainementoffre VALUES('O8', 1, 1, 0, 0);
INSERT INTO t_entrainementoffre VALUES('O9', 1, 0, 0, 0);

-- -----------------------------
-- Contenu de la table t_joueur
-- -----------------------------
INSERT INTO t_joueur VALUES('9f20d53fab1470b02a217242c4eb1cf59303aa7c', 1000, 1358547171);

-- -----------------------------
-- Contenu de la table t_joueur_animal
-- -----------------------------
INSERT INTO t_joueur_animal VALUES('', 'A1');
INSERT INTO t_joueur_animal VALUES('9f20d53fab1470b02a217242c4eb1cf59303aa7c', 'A2');

-- -----------------------------
-- Contenu de la table t_pari
-- -----------------------------

-- -----------------------------
-- Contenu de la table t_raceanimal
-- -----------------------------

-- -----------------------------
-- Contenu de la table t_transaction
-- -----------------------------

