<?php

require_once './../../lib/simpletest/autorun.php';
require_once './../mapping/CombatDAO.php';
require_once './../modeles/CombatModele.php';

/**
 *
 * Classe de test de la classe AnimalDAO (package mapping)
 *
 */
class CombatDAOTestCase extends UnitTestCase
{
	/** DAO */
	private $_dao;
	
	/** DAO Animal à tester */
	private $_daoCombat;

	/**
	 * Constructeur par défaut. Instancie le DAO.
	 */
	public function __construct()
	{
		$this->_dao = new DAO();
		$this->_daoCombat = new CombatDAO();
	}
	
	public function __destruct()
	{
		$this->_dao->__destruct();
		$this->_daoCombat->__destruct();
	}
	
	/**
	 * Test du constructeur de AnimalDAOTestCase.
	 */
	function test_constructeur_CombatDAOTestCase()
	{
		//Asserts
		$this->assertNotNull($this->_dao);
		$this->assertNotNull($this->_daoCombat);
		$this->assertTrue($this->_dao instanceof DAO);
		$this->assertTrue($this->_daoCombat instanceof CombatDAO);
	}
	
	/**
	 * Permet d'ajouter un combat de test dans la base de données et d'être sur que l'ajout ait eu lieu.
	 * @param String $idAnimal
	 * @param String $proprietaire
	 * @param String $nomAnimal
	 * @param int $vie
	 * @param int $defense
	 * @param int $attaque
	 */
	private function addCombat($idCombat, $dateCombat, $estCommence=0)
	{
		try
		{
			//Insertion
			$requete_prepare = $this->_dao->getConnexion()->prepare("INSERT INTO t_combat (idCombat, dateCombat, estCommence) VALUES (:idCombat, :dateCombat, :estCommence)");
			$requete_prepare->bindParam(':idCombat', $idCombat, PDO::PARAM_STR); 
			$requete_prepare->bindParam(':dateCombat', $dateCombat, PDO::PARAM_INT);
			$requete_prepare->bindParam(':estCommence', $estCommence, PDO::PARAM_INT);
			
			$boolInsert = $requete_prepare->execute();
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT * FROM t_combat WHERE idCombat = :idCombat");
			$requete_prepare->bindParam(':idCombat', $idCombat, PDO::PARAM_STR);
			$requete_prepare->execute();
			$donnees = $requete_prepare->fetch(PDO::FETCH_ASSOC);
			$idCombatReceived = $donnees['idCombat'];
			$dateReceived = $donnees['dateCombat'];
			$estCommenceReceived = $donnees['estCommence'];
		
			//Asserts
			$this->assertTrue($boolInsert);
			$this->assertEqual($idCombat, $idCombatReceived);
			$this->assertEqual($dateCombat, $dateReceived);
			$this->assertEqual($estCommence, $estCommenceReceived);
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : '.$e->getTraceAsString().'<br/>';
		}
	}
	
	/**
	 * Permet d'ajouter une action de test dans la base de données et d'être sur que l'ajout ait eu lieu.
	 * @param String $idAction
	 * @param String $idCombat
	 * @param String $idAnimal
	 * @param String $idCompetence
	 * @param int $degats
	 * @param int $date
	 */
	private function addAction($idAction, $idCombat, $idAnimal, $idCompetence, $degats, $date)
	{		
		try
		{
			//Insertion
			$requete_prepare = $this->_dao->getConnexion()->prepare("INSERT INTO t_combat_action (idAction, Combat_idCombat, Animal_idAnimal, Competence_idCompetence, degatsProvoques, dateAction) VALUES (:idAction, :idCombat, :idAnimal, :idCompetence, :degats, :date)");
			$requete_prepare->bindParam(':idAction', $idAction, PDO::PARAM_STR);
			$requete_prepare->bindParam(':idCombat', $idCombat, PDO::PARAM_STR);
			$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
			$requete_prepare->bindParam(':idCompetence', $idCompetence, PDO::PARAM_STR);
			$requete_prepare->bindParam(':degats', $degats, PDO::PARAM_INT);
			$requete_prepare->bindParam(':date', $date, PDO::PARAM_INT);
			$boolInsert = $requete_prepare->execute();
			
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT * FROM t_combat_action WHERE idAction = :idAction");
			$requete_prepare->bindParam(':idAction', $idAction, PDO::PARAM_STR);
			$requete_prepare->execute();
			$donnees = $requete_prepare->fetch(PDO::FETCH_ASSOC);
			$idActionReceived= $donnees[ 'idAction' ];
			$idCombatReceived = $donnees[ 'Combat_idCombat' ];
			$idAnimalReceived= $donnees[ 'Animal_idAnimal' ];
			$idCompetenceReceived= $donnees[ 'Competence_idCompetence' ];
			$degatsReceived= $donnees[ 'degatsProvoques' ];
			$dateReceived= $donnees[ 'dateAction' ];
		
			//Asserts
			$this->assertTrue($boolInsert);
			$this->assertEqual($idAction, $idActionReceived); 
			$this->assertEqual($idCombat, $idCombatReceived);
			$this->assertEqual($idAnimal, $idAnimalReceived);
			$this->assertEqual($idCompetence, $idCompetenceReceived);
			$this->assertEqual($degats, $degatsReceived);
			$this->assertEqual($date, $dateReceived);
	
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : '.$e->getTraceAsString().'<br/>';
		}
	}
	
	/**
	 * Permet de supprimer un combat de test de la base de données et d'être sur que la suppression ait eu lieu.
	 * @param String $idCombat
	 */
	private function deleteCombat($idCombat)
	{
		try
		{
			//Suppression
		 	$requete_prepare = $this->_dao->getConnexion()->prepare("DELETE FROM t_combat WHERE idCombat = :idCombat");
		 	$requete_prepare->bindParam(':idCombat', $idCombat, PDO::PARAM_STR);
		 	$bool = $requete_prepare->execute();
		 	
		 	$requete_prepare = $this->_dao->getConnexion()->prepare("DELETE FROM t_combat_animal WHERE Combat_idCombat = :idCombat");
		 	$requete_prepare->bindParam(':idCombat', $idCombat, PDO::PARAM_STR);
		 	$bool = $requete_prepare->execute();
		
			//Select pour vérification
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT COUNT(*) as COUNT FROM t_combat WHERE idCombat = :idCombat");
			$requete_prepare->bindParam(':idCombat', $idCombat, PDO::PARAM_STR);
			$requete_prepare->execute();
			$count = $requete_prepare->fetch(PDO::FETCH_OBJ)->COUNT;
		
			//Asserts
			$this->assertTrue($bool);
			$this->assertEqual($count, 0);
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : '.$e->getTraceAsString().'<br/>';
		}
	}
	
	/**
	 * Permet de supprimer une action d'un combat dans la base de données et d'être sur que la suppression ait eu lieu.
	 * @param String $idCombat
	 */
	private function deleteAction($idAction)
	{
		try
		{
			//Suppression
			$requete_prepare = $this->_dao->getConnexion()->prepare("DELETE FROM t_combat_action WHERE idAction = :idAction");
			$requete_prepare->bindParam(':idAction', $idAction, PDO::PARAM_STR);
			$bool = $requete_prepare->execute();
	
			//Select pour vérification
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT COUNT(*) as COUNT FROM t_combat_action WHERE idAction = :idAction");
			$requete_prepare->bindParam(':idAction', $idAction, PDO::PARAM_STR);
			$requete_prepare->execute();
			$count = $requete_prepare->fetch(PDO::FETCH_OBJ)->COUNT;
	
			//Asserts
			$this->assertTrue($bool);
			$this->assertEqual($count, 0);
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : '.$e->getTraceAsString().'<br/>';
		}
	}
	
	/**
	 * Permet d'ajouter un combat_animal de test dans la base de données et d'être sur que l'ajout ait eu lieu.
	 * @param String $idCombat
	 * @param String $idAnimal
	 * @param int $sommeEngagee
	 */
	private function addCombatAnimal($idCombat, $idAnimal, $sommeEngagee=0)
	{
		try
		{
			//Insertion
			$requete_prepare = $this->_dao->getConnexion()->prepare("INSERT INTO t_combat_animal (Combat_idCombat, Animal_idAnimal, sommeEngagee) VALUES (:idCombat, :idAnimal, :sommeEngagee)");
			$requete_prepare->bindParam(':idCombat', $idCombat, PDO::PARAM_STR);
			$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
			$requete_prepare->bindParam(':sommeEngagee', $sommeEngagee, PDO::PARAM_INT);
			$boolInsert = $requete_prepare->execute();
			
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT * FROM t_combat_animal WHERE Combat_idCombat = :idCombat and Animal_idAnimal = :idAnimal");
			$requete_prepare->bindParam(':idCombat', $idCombat, PDO::PARAM_STR);
			$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
			$requete_prepare->execute();
			$donnees = $requete_prepare->fetch(PDO::FETCH_ASSOC);
			$idCombatReceived = $donnees['Combat_idCombat'];
			$idAnimalReceived= $donnees['Animal_idAnimal'];
			$sommeEngageeReceived= $donnees['sommeEngagee'];
	
			//Asserts
			$this->assertTrue($boolInsert);
			$this->assertEqual($idCombat, $idCombatReceived);
			$this->assertEqual($sommeEngagee, $sommeEngageeReceived);
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : '.$e->getTraceAsString().'<br/>';
		}
	}

	/**
	 * Permet d'ajouter un animal de test dans la base de données et d'être sur que l'ajout ait eu lieu.
	 * @param String $idAnimal
	 * @param String $proprietaire
	 * @param String $nomAnimal
	 * @param int $vie
	 * @param int $defense
	 * @param int $attaque
	 */
	private function addAnimal($idAnimal, $proprietaire, $nomAnimal, $vie, $defense, $attaque, $niveau)
	{
		try
		{
			//Insertion
			$requete_prepare = $this->_dao->getConnexion()->prepare("INSERT INTO t_animal (idAnimal, nomAnimal, vie, defense, attaque, niveau) VALUES (:id, :nom, :vie, :def, :att, :niveau)");
			$requete_prepare->bindParam(':id', $idAnimal, PDO::PARAM_STR);
			$requete_prepare->bindParam(':nom', $nomAnimal, PDO::PARAM_STR);
			$requete_prepare->bindParam(':vie', $vie, PDO::PARAM_INT);
			$requete_prepare->bindParam(':def', $defense, PDO::PARAM_INT);
			$requete_prepare->bindParam(':att', $attaque, PDO::PARAM_INT);
			$requete_prepare->bindParam(':niveau', $niveau, PDO::PARAM_INT);
			$boolInsert1 = $requete_prepare->execute();
	
			$requete_prepare = $this->_dao->getConnexion()->prepare("INSERT INTO t_joueur_animal (Joueur_idFacebook, Animal_idAnimal) VALUES (:idJ, :idA)");
			$requete_prepare->bindParam(':idJ', $proprietaire, PDO::PARAM_STR);
			$requete_prepare->bindParam(':idA', $idAnimal, PDO::PARAM_STR);
			$boolInsert2 = $requete_prepare->execute();
	
			//Select pour vérification
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT * FROM t_joueur_animal WHERE Animal_idAnimal = :idAnimal AND Joueur_idFacebook = :idFacebook");
			$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
			$requete_prepare->bindParam(':idFacebook', $proprietaire, PDO::PARAM_STR);
			$requete_prepare->execute();
			$donnees = $requete_prepare->fetch(PDO::FETCH_OBJ);
			$proprietaireReceived = $donnees->Joueur_idFacebook;
	
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT * FROM t_animal WHERE idAnimal = :idAnimal");
			$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
			$requete_prepare->execute();
			$donnees = $requete_prepare->fetch(PDO::FETCH_OBJ);
			$idAnimalReceived = $donnees->idAnimal;
			$nomReceived = $donnees->nomAnimal;
			$vieReceived = $donnees->vie;
			$defenseReceived = $donnees->defense;
			$attaqueReceived = $donnees->attaque;
			$niveauReceived = $donnees->niveau;
	
			//Asserts
			$this->assertTrue($boolInsert1);
			$this->assertTrue($boolInsert2);
			$this->assertEqual($idAnimal, $idAnimalReceived);
			$this->assertEqual($proprietaire, $proprietaireReceived);
			$this->assertEqual($nomAnimal, $nomReceived);
			$this->assertEqual($vie, $vieReceived);
			$this->assertEqual($defense, $defenseReceived);
			$this->assertEqual($attaque, $attaqueReceived);
			$this->assertEqual($niveau, $niveauReceived);
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : '.$e->getTraceAsString().'<br/>';
		}
	}	
	
	/**
	 * Permet de supprimer un animal de test de la base de données et d'être sur que la suppression ait eu lieu.
	 * @param String $idAnimal
	 */
	private function deleteAnimal($idAnimal)
	{
		try
		{
			//Suppression
			$requete_prepare = $this->_dao->getConnexion()->prepare("DELETE FROM t_animal WHERE idAnimal = :idAnimal");
			$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
			$boolDelete1 = $requete_prepare->execute();
				
			$requete_prepare = $this->_dao->getConnexion()->prepare("DELETE FROM t_joueur_animal WHERE Animal_idAnimal = :idAnimal");
			$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
			$boolDelete2 = $requete_prepare->execute();
				
			//Select pour vérification
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT COUNT(*) as COUNT1 FROM t_animal WHERE idAnimal = :id");
			$requete_prepare->bindParam(':id', $idAnimal, PDO::PARAM_STR);
			$requete_prepare->execute();
			$count1 = $requete_prepare->fetch(PDO::FETCH_OBJ)->COUNT1;
				
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT COUNT(*) as COUNT2 FROM t_joueur_animal WHERE Animal_idAnimal = :id");
			$requete_prepare->bindParam(':id', $idAnimal, PDO::PARAM_STR);
			$requete_prepare->execute();
			$count2 = $requete_prepare->fetch(PDO::FETCH_OBJ)->COUNT2;
				
			//Asserts
			$this->assertTrue($boolDelete1);
			$this->assertTrue($boolDelete2);
			$this->assertEqual($count1, 0);
			$this->assertEqual($count2, 0);
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : '.$e->getTraceAsString().'<br/>';
		}
	}
	
	// -------------------------------- TESTS DE LA CLASSE COMBATAO ---------------------------------------
	
	
	/**
	 * Test de l'initialisation d'une connexion à la base de données dans le constructeur.
	 * Fait appel au constructeur de la classe abstraite mère DAO.
	 */
	function test_constructeur()
	{
		$dao = new CombatDAO();
		/* Asserts */
		$this->assertNotNull($dao);
		$this->assertTrue($dao instanceof CombatDAO);
		$this->assertNotNull($dao->getConnexion());
	}
	
	/**
	 * Test de la fermeture de la connexion à la base de données dans le constructeur.
	 * Fait appel au destructeur de la classe abstraite mère DAO.
	 */
	function test_destructeur()
	{
		$dao = new CombatDAO();
		$dao->__destruct(); /* Fermeture de la connexion à la base */
		/* Assert */
		$this->assertNull($dao->getConnexion());
	}
	
	/**
	 * Test de l'inscritpion à un combat.
	 * Fait appel au inscriptionCombat de CombatDAO.
	 */
	function test_inscriptionCombat()
	{
		try
		{
			//Création d'une ligne de test dans la table t_animal
			$idAnimal = 'ATEST001';
			$proprietaire = 'JTEST001';
			$nom = 'Lapin test';
			$vie = 5;
			$defense = 10;
			$attaque = 15;
			$niveau = 1;
			$this->addAnimal($idAnimal, $proprietaire, $nom, $vie, $defense, $attaque, $niveau);
			
			//Création d'une ligne dans la table combat
			$idCombat = "CTEST001";
			$dateCombat = time();
			$estCommence = false;
			$this->addCombat($idCombat, $dateCombat, $estCommence);
			
			//Exécution de la méthode testée
			$boolReceived = $this->_daoCombat->inscriptionCombat($idCombat, $idAnimal);
			
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT COUNT(*) as COUNT FROM t_combat_animal WHERE Combat_idCombat = :idCombat AND Animal_idAnimal = :idAnimal");
			$requete_prepare->bindParam(':idCombat', $idCombat, PDO::PARAM_STR);
			$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
			$requete_prepare->execute();
			$donnees = $requete_prepare->fetch(PDO::FETCH_ASSOC);
			$countReceived = $donnees['COUNT'];			
			
			//Assert
			$this->assertTrue($boolReceived); //On teste si la méthode revoie true.
			$this->assertEqual($countReceived, 1); //On test si le changement a bien été fait
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : '.$e->getTraceAsString().'<br/>';
		}
		//On enlève la ligne insérée pour le test
		$this->deleteCombat($idCombat);
		$this->deleteAnimal($idAnimal);
		
		//Select pour vérification
		$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT COUNT(*) as COUNT FROM t_combat WHERE idCombat = :idCombat");
		$requete_prepare->bindParam(':idCombat', $idCombat, PDO::PARAM_STR);
		$requete_prepare->execute();
		$count = $requete_prepare->fetch(PDO::FETCH_OBJ)->COUNT;
		
		//Asserts
		$this->assertEqual($count, 0);
		
	}

	/**
	 * Test de l'ajout d'un combat.
	 * Fait appel a ajouterCombat de CombatDAO.
	 */
	function test_ajouterCombat()
	{
		$idCombat=null;
		try
		{
			//Création d'une ligne de test dans la table t_combat
			$dateCombat = 1360698377;
			$idAnimal = 'ATEST001';
			$money = 50;
			
			//Exécution de la méthode testée
			$idCombat = $this->_daoCombat->ajouterCombat($idAnimal, $dateCombat, $money);

			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT * FROM t_combat WHERE idCombat = :idCombat");
			$requete_prepare->bindParam(':idCombat', $idCombat, PDO::PARAM_STR);
			$requete_prepare->execute();
			$donnees = $requete_prepare->fetch(PDO::FETCH_ASSOC);
			$idCombatReceived = $donnees[ 'idCombat' ];
			$dateReceived= $donnees[ 'dateCombat' ];
			$estCommenceReceived= $donnees[ 'estCommence' ];
			
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT * FROM t_combat_animal WHERE Combat_idCombat = :idCombat AND Animal_idAnimal = :idAnimal");
			$requete_prepare->bindParam(':idCombat', $idCombat, PDO::PARAM_STR);
			$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
			$requete_prepare->execute();
			$donnees = $requete_prepare->fetch(PDO::FETCH_ASSOC);
			$idCombat2Received = $donnees[ 'Combat_idCombat' ];
			$idAnimalReceived= $donnees[ 'Animal_idAnimal' ];
			$sommeReceived= $donnees[ 'sommeEngagee' ];
			
			//Assert
			$this->assertEqual($idCombatReceived, $idCombat); //On test si le changement a bien été fait
			$this->assertEqual($dateCombat, $dateReceived); //On test si le changement a bien été fait
			$this->assertEqual(0, $estCommenceReceived); //On test si le changement a bien été fait
			
			$this->assertEqual($idCombat2Received, $idCombat); //On test si le changement a bien été fait
			$this->assertEqual($idAnimalReceived, $idAnimal); //On test si le changement a bien été fait
			$this->assertEqual($sommeReceived, $money); //On test si le changement a bien été fait
			
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : '.$e->getTraceAsString().'<br/>';
		}
		//On enlève la ligne insérée pour le test
		$this->deleteCombat($idCombat);
	}
	
	/**
	 * Test si un combat est disponible.
	 * Fait appel a isFightAvailable de CombatDAO.
	 */
	function test_isFightAvailable()
	{
		$idCombat=null;
		try
		{
			//Création d'une ligne de test dans la table t_combat
			$idCombat = 'CTEST001';
			$date = time();
			$estCommence = true;
			$this->addCombat($idCombat, $date, $estCommence);
			
			$this->addCombatAnimal($idCombat, 'ATEST001');
			$this->addCombatAnimal($idCombat, 'ATEST002');
			
			//Exécution de la méthode testée
			$boolReceived = $this->_daoCombat->isFightAvailable($idCombat);
	
			//Assert
			$this->assertEqual($boolReceived, false); //On test si le changement a bien été fait
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : '.$e->getTraceAsString().'<br/>';
		}
		//On enlève la ligne insérée pour le test
		$this->deleteCombat($idCombat);
	
		//Select pour vérification
		$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT COUNT(*) as COUNT FROM t_combat WHERE idCombat = :idCombat");
		$requete_prepare->bindParam(':idCombat', $idCombat, PDO::PARAM_STR);
		$requete_prepare->execute();
		$count = $requete_prepare->fetch(PDO::FETCH_OBJ)->COUNT;
	
		//Asserts
		$this->assertEqual($count, 0);
	
	}
	
	/**
	 * Test la liste des combats retournées suivant un niveau d'animal.
	 * Fait appel a listerCombats de CombatDAO.
	 *
	function test_listerCombats()
	{
		$idCombat1 = null;
		$idCombat2 = null;
		$idAnimal1 = null;
		$idAnimal2 = null;
		try
		{
			//Création d'une ligne de test dans la table t_animal
			$idJoueurTest = 'TEST';
			$idAnimal1 = 'ATEST001';
			$proprietaire = 'JTEST001';
			$nom = 'Lapin test';
			$vie = 15;
			$defense = 15;
			$attaque = 15;
			$niveau = 13;
			$this->addAnimal($idAnimal1, $proprietaire, $nom, $vie, $defense, $attaque, $niveau);
			
			$idAnimal2 = 'ATEST002';
			$proprietaire = 'JTEST001';
			$nom = 'T-Rex test';
			$vie = 10;
			$defense = 10;
			$attaque = 10;
			$niveau = 11;
			$this->addAnimal($idAnimal2, $proprietaire, $nom, $vie, $defense, $attaque, $niveau);
			
			//Création d'une ligne de test dans la table t_combat_animal
			$idCombat1 = 'CA1';
			$idCombat2 = 'CA2';
			$level = 12;
			$this->addCombatAnimal($idCombat1, $idAnimal1);
			$this->addCombatAnimal($idCombat2, $idAnimal2);
			
			//Exécution de la méthode testée
			$listeCombats = $this->_daoCombat->listerCombats($level);
			$listeCombatsBis = $this->_daoCombat->listerCombats($level, $proprietaire);
			
			$listeCombatsExpected = array();
			$listeCombatsExpected[0]['idCombat'] = 'CA1';
			$listeCombatsExpected[0]['Animal_idAnimal1'] = 'ATEST001';
			
			$listeCombatsExpected[1]['idCombat'] = 'CA2';
			$listeCombatsExpected[1]['Animal_idAnimal1'] = 'ATEST002';
		
			//Assert
			$this->assertEqual($listeCombatsExpected[0]['idCombat'],$listeCombats[0]['idCombat']);
			$this->assertEqual($listeCombatsExpected[0]['Animal_idAnimal1'],$listeCombats[0]['Animal_idAnimal1']);
			$this->assertEqual($listeCombatsExpected[1]['idCombat'],$listeCombats[1]['idCombat']);
			$this->assertEqual($listeCombatsExpected[1]['Animal_idAnimal1'],$listeCombats[1]['Animal_idAnimal1']);
			
			$this->assertEqual(0,count($listeCombatsBis));
				
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : '.$e->getTraceAsString().'<br/>';
		}
		//On enlève la ligne insérée pour le test
		$this->deleteCombatAnimal($idCombat1, $idAnimal1);
		$this->deleteAnimal($idAnimal1);
		
		$this->deleteCombatAnimal($idCombat2, $idAnimal2);
		$this->deleteAnimal($idAnimal2);
		
		//Select pour vérification
		$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT COUNT(*) as COUNT FROM t_combat WHERE idCombat = :idCombat AND Animal_idAnimal1 = :idAnimal");
		$requete_prepare->bindParam(':idCombat', $idCombat, PDO::PARAM_STR);
		$requete_prepare->bindParam(':idAnimal', $idAnimal1, PDO::PARAM_STR);
		$requete_prepare->execute();
		$count1 = $requete_prepare->fetch(PDO::FETCH_OBJ)->COUNT;
		
		$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT COUNT(*) as COUNT FROM t_combat WHERE idCombat = :idCombat AND Animal_idAnimal1 = :idAnimal");
		$requete_prepare->bindParam(':idCombat', $idCombat, PDO::PARAM_STR);
		$requete_prepare->bindParam(':idAnimal', $idAnimal2, PDO::PARAM_STR);
		$requete_prepare->execute();
		$count2 = $requete_prepare->fetch(PDO::FETCH_OBJ)->COUNT;
		
		//Asserts
		$this->assertEqual($count1, 0);
		$this->assertEqual($count2, 0);
	}*/
	
	/**
	 * Test de l'ajout d'une action d'un combat.
	 */
	function test_ajouterAction()
	{
		$idCombat=null;
		try
		{
			//Exécution de la méthode testée
			$idCombat = 'CTEST001';
			$idAnimal = 'ATEST001';
			$idCompetence = 'CTEST001';
			$degats = 10;
			$date = time();
			$idAction = $this->_daoCombat->ajouterAction($idCombat, $idAnimal, $idCompetence, $degats, $date);
	
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT * FROM t_combat_action WHERE idAction = :idAction");
			$requete_prepare->bindParam(':idAction', $idAction, PDO::PARAM_STR);
			$requete_prepare->execute();
			$donnees = $requete_prepare->fetch(PDO::FETCH_ASSOC);
			$idActionReceived= $donnees[ 'idAction' ];
			$idCombatReceived = $donnees[ 'Combat_idCombat' ];
			$idAnimalReceived= $donnees[ 'Animal_idAnimal' ];
			$idCompetenceReceived= $donnees[ 'Competence_idCompetence' ];
			$degatsReceived= $donnees[ 'degatsProvoques' ];
			$dateReceived= $donnees[ 'dateAction' ];
				
			//Assert
			$this->assertEqual($idAction, $idActionReceived); 
			$this->assertEqual($idCombat, $idCombatReceived);
			$this->assertEqual($idAnimal, $idAnimalReceived);
			$this->assertEqual($idCompetence, $idCompetenceReceived);
			$this->assertEqual($degats, $degatsReceived);
			$this->assertEqual($date, $dateReceived);
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : '.$e->getTraceAsString().'<br/>';
		}
		//On enlève la ligne insérée pour le test
		$this->deleteAction($idAction);
	}
	
	/**
	 * Test de la méthode getLastAction() de CombatDAO
	 */
	public function test_getLastAction()
	{
		try
		{
			//Ajout d'actions de test
			$this->addAction('ACTTEST002', "CTEST001", "ANITEST002", "CTEST002", 3, time()-10);
			
			$idAction = "ACTTEST001";
			$idCombat = "CTEST001";
			$idAnimal = "ANITEST001";
			$idCompetence = "CTEST001";
			$degats = 10;
			$date = time();
			$this->addAction($idAction, $idCombat, $idAnimal, $idCompetence, $degats, $date);
			
			//Execution de la méthode à tester
			$action = $this->_daoCombat->getLastAction($idCombat);
			
			//Asserts
			$this->assertEqual($idAction, $action['idAction']);
			$this->assertEqual($idCombat, $action['Combat_idCombat']);
			$this->assertEqual($idAnimal, $action['Animal_idAnimal']);
			$this->assertEqual($idCompetence, $action['Competence_idCompetence']);
			$this->assertEqual($degats, $action['degatsProvoques']);
			$this->assertEqual($date, $action['dateAction']);
			
			//Suppression
			$this->deleteAction("ACTTEST002");
			$this->deleteAction($idAction);
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : '.$e->getTraceAsString().'<br/>';
		}
	}
	
	/**
	 * Test de la méthode getAction() de CombatDAO
	 */
	public function test_getAction()
	{
		try
		{
			//Ajout d'actions de test
			$idAction = "ACTTEST001";
			$idCombat = "CTEST001";
			$idAnimal = "ANITEST001";
			$idCompetence = "CTEST001";
			$degats = 10;
			$date = time();
			$this->addAction($idAction, $idCombat, $idAnimal, $idCompetence, $degats, $date);
				
			//Execution de la méthode à tester
			$action = $this->_daoCombat->getAction($idAction);
				
			//Asserts
			$this->assertEqual($idAction, $action['idAction']);
			$this->assertEqual($idCombat, $action['Combat_idCombat']);
			$this->assertEqual($idAnimal, $action['Animal_idAnimal']);
			$this->assertEqual($idCompetence, $action['Competence_idCompetence']);
			$this->assertEqual($degats, $action['degatsProvoques']);
			$this->assertEqual($date, $action['dateAction']);
				
			//Suppression
			$this->deleteAction($idAction);
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : '.$e->getTraceAsString().'<br/>';
		}
	}
	
	/**
	 * Test de la méthode getCombat() de CombatDAO
	 */
	public function test_getCombat()
	{
		try
		{
			//Ajout une ligne de test dans la table t_combat
			$idCombat = 'CTEST001';
			$idAnimal1 = 'ATEST001';
			$idAnimal2 = 'ATEST002';
			$dateCombat = time();
			$estCommence = true;
			$this->addCombat($idCombat, $dateCombat, $estCommence);
			$this->addCombatAnimal($idCombat, $idAnimal1);
			$this->addCombatAnimal($idCombat, $idAnimal2);
	
			//Execution de la méthode à tester
			$combat = $this->_daoCombat->getCombat($idCombat);
	
			//Asserts
			$this->assertEqual($idCombat, $combat->getIdCombat());
			$this->assertEqual($idAnimal1, $combat->getAnimal1());
			$this->assertEqual($idAnimal2, $combat->getAnimal2());
			$this->assertEqual($dateCombat, $combat->getDate());
			$this->assertEqual($estCommence, $combat->getEstCommence());
	
			//Suppression
			$this->deleteCombat($idCombat);
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : '.$e->getTraceAsString().'<br/>';
		}
	}
	
	
	/**
	 * Test de la méthode getAbsorption() de CombatDAO
	 */
	public function test_getAbsorption()
	{
		try
		{
			//Ajout d'actions de test
			$idAbsorption = 'ABSTEST001';
			$idCompetence = 'CTEST001';
			$absorptionAttaque = 0.68;
			$requete_prepare = $this->_dao->getConnexion()->prepare("INSERT INTO t_absorption (idAbsorption, Competence_defense, absorptionAttaque3) VALUES (:idAbsorption, :idCompetence, :absorptionAttaque)");
			$requete_prepare->bindParam(':idAbsorption', $idAbsorption, PDO::PARAM_STR);
			$requete_prepare->bindParam(':idCompetence', $idCompetence, PDO::PARAM_STR);
			$requete_prepare->bindValue(':absorptionAttaque', $absorptionAttaque);
			$requete_prepare->execute();
		
			//Execution de la méthode à tester
			$absorptionReceived = $this->_daoCombat->getAbsorption($idCompetence, 3);
		
			//Asserts
			$this->assertEqual($absorptionReceived, $absorptionAttaque);
		
			//Suppression
			$requete_prepare = $this->_dao->getConnexion()->prepare("DELETE FROM t_absorption WHERE idAbsorption = :idAbsorption");
			$requete_prepare->bindParam(':idAbsorption', $idAbsorption, PDO::PARAM_STR);
			$requete_prepare->execute();
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : '.$e->getTraceAsString().'<br/>';
		}
	}
	
	/**
	 * Test de la méthode updateDegatsProvoques() de CombatDAO
	 */
	public function test_updateDegatsProvoques()
	{
		try
		{
			//Ajout d'une action de test
			$idAction = "ACTTEST001";
			$idCombat = "CTEST001";
			$idAnimal = "ANITEST001";
			$idCompetence = "CTEST001";
			$degats = 0;
			$date = time();
			$this->addAction($idAction, $idCombat, $idAnimal, $idCompetence, $degats, $date);
			
			//Execution de la méthode à tester
			$degatsExpected = 10;
			$this->_daoCombat->updateDegatsProvoques($idAction, $degatsExpected);
	
			//Verification
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT degatsProvoques FROM t_combat_action WHERE idAction = :idAction");
			$requete_prepare->bindParam(':idAction', $idAction, PDO::PARAM_STR);
			$requete_prepare->execute();
			$donnees = $requete_prepare->fetch(PDO::FETCH_ASSOC);
			$degatsReceived= $donnees[ 'degatsProvoques' ];
			
			//Asserts
			$this->assertEqual($degatsExpected, $degatsReceived);
	
			//Suppression
			$this->deleteAction($idAction);
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : '.$e->getTraceAsString().'<br/>';
		}
	}
	
	/**
	 * Test de la méthode getDegatsProvoques() de CombatDAO
	 */
	public function test_getDegatsProvoques()
	{
		try
		{
			//Ajout d'une action de test
			$idAction = "ACTTEST001";
			$idCombat = "CTEST001";
			$idAnimal = "ANITEST001";
			$idCompetence = "CTEST001";
			$degats = 10;
			$date = time();
			$this->addAction($idAction, $idCombat, $idAnimal, $idCompetence, $degats, $date);
				
			//Execution de la méthode à tester
			$degatsReceived = $this->_daoCombat->getDegatsProvoques($idAction);
			
			//Asserts
			$this->assertEqual($degats, $degatsReceived);
	
			//Suppression
			$this->deleteAction($idAction);
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : '.$e->getTraceAsString().'<br/>';
		}
	}
	
	/**
	 * Test de la méthode getSommeEngagee() de CombatDAO
	 */
	public function test_getSommeEngagee()
	{
		try
		{
			//Ajout d'un combat de test
			$idAnimal = "ATEST001";
			$idCombat = "CTEST001";
			$sommeEngagee = 200;
			$this->addCombatAnimal($idCombat, $idAnimal, $sommeEngagee);
				
			//Execution de la méthode à tester
			$sommeReceived = $this->_daoCombat->getSommeEngagee($idCombat, $idAnimal);
				
			//Asserts
			$this->assertEqual($sommeEngagee, $sommeReceived);
	
			//Suppression
			$this->deleteCombat($idCombat);
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : '.$e->getTraceAsString().'<br/>';
		}
	}
	
	/**
	 * 
	 */
	public function test_deleteCombatByIdCombat()
	{
		try
		{
			//Ajout une ligne de test dans la table t_combat
			$idCombat = 'CTEST001';
			$idAnimal1 = 'ATEST001';
			$idAnimal2 = 'ATEST002';
			$dateCombat = time();
			$estCommence = true;
			$this->addCombat($idCombat, $dateCombat, $estCommence);
			$this->addCombatAnimal($idCombat, $idAnimal1);
			$this->addCombatAnimal($idCombat, $idAnimal2);
			
			//Appel de la méthode concerné
			$this->_daoCombat->deleteCombatByIdCombat($idCombat);
			
			//Select pour vérification
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT COUNT(*) as COUNT FROM t_combat WHERE idCombat = :idCombat");
			$requete_prepare->bindParam(':idCombat', $idCombat, PDO::PARAM_STR);
			$requete_prepare->execute();
			$count = $requete_prepare->fetch(PDO::FETCH_OBJ)->COUNT;
		
			$requete_prepare2 = $this->_dao->getConnexion()->prepare("SELECT COUNT(*) as COUNT FROM t_combat_animal WHERE Combat_idCombat = :idCombat");
			$requete_prepare2->bindParam(':idCombat', $idCombat, PDO::PARAM_STR);
			$requete_prepare2->execute();
			$count2 = $requete_prepare2->fetch(PDO::FETCH_OBJ)->COUNT;
			
			//Asserts
			$this->assertEqual($count, 0);
			$this->assertEqual($count2, 0);
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : '.$e->getTraceAsString().'<br/>';
		}
	}
	
	/**
	 * 
	 */
	public function test_rejoindreCombat()
	{
		$idCombat=null;
		try
		{
			//Création d'une ligne de test dans la table t_combat
			$dateCombat = 1360698377;
			$idAnimal = 'ATEST001';
			$idCombat = 'CTEST555';
			$money = 50;
				
			//Exécution de la méthode testée
			$this->addCombat($idCombat, $dateCombat);
			$this->_daoCombat->rejoindreCombat($idCombat, $idAnimal, $money);
				
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT * FROM t_combat_animal WHERE Combat_idCombat = :idCombat AND Animal_idAnimal = :idAnimal");
			$requete_prepare->bindParam(':idCombat', $idCombat, PDO::PARAM_STR);
			$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
			$requete_prepare->execute();
			$donnees = $requete_prepare->fetch(PDO::FETCH_ASSOC);
			$idCombatReceived = $donnees[ 'Combat_idCombat' ];
			$idAnimalReceived= $donnees[ 'Animal_idAnimal' ];
			$sommeReceived= $donnees[ 'sommeEngagee' ];
				
			//Assert				
			$this->assertEqual($idCombatReceived, $idCombat); //On test si le changement a bien été fait
			$this->assertEqual($idAnimalReceived, $idAnimal); //On test si le changement a bien été fait
			$this->assertEqual($sommeReceived, $money); //On test si le changement a bien été fait
				
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : '.$e->getTraceAsString().'<br/>';
		}
		//On enlève la ligne insérée pour le test
		$this->deleteCombat($idCombat);
	}
	
	/**
	 * 
	 */
	public function test_startCombat()
	{
		try
		{
			//Ajout d'une action de test
			$dateCombat = 1360698377;
			$idCombat = "CTEST100";
			$estCommence = 1;
				
			//Execution de la méthode à tester
			$this->addCombat($idCombat, $dateCombat);
			$this->_daoCombat->startCombat($idCombat);
	
			//Verification
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT estCommence FROM t_combat WHERE idCombat = :idCombat");
			$requete_prepare->bindParam(':idCombat', $idCombat, PDO::PARAM_STR);
			$requete_prepare->execute();
			$donnees = $requete_prepare->fetch(PDO::FETCH_ASSOC);
			$estCommenceReceived= $donnees[ 'estCommence' ];
				
			//Asserts
			$this->assertEqual($estCommence, $estCommenceReceived);
	
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : '.$e->getTraceAsString().'<br/>';
		}
		//On enlève la ligne insérée pour le test
		$this->deleteCombat($idCombat);
	}
	
	/**
	 * 
	 */
	public function test_getAnimalsByCombat()
	{
		try
		{
			//Ajout d'une action de test
			$dateCombat = 1360698377;
			$idCombat = "CTEST100";
			$estCommence = 1;
			$idAnimal1 = "ATEST001";
			$idAnimal2 = "ATEST002";
		
			//Execution de la méthode à tester
			$this->addCombat($idCombat, $dateCombat);
			$this->addCombatAnimal($idCombat, $idAnimal1);
			$this->addCombatAnimal($idCombat, $idAnimal2);
			
			$combatReceived = $this->_daoCombat->getAnimalsByCombat($idCombat);

			//Verification
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT * FROM t_combat_animal WHERE Combat_idCombat = :idCombat");
			$requete_prepare->bindParam(':idCombat', $idCombat, PDO::PARAM_STR);
			$requete_prepare->execute();
			$donnees = $requete_prepare->fetchAll(PDO::FETCH_ASSOC);
			
			$idCombatReceived= $donnees[0][ 'Combat_idCombat' ];
			$idAnimal1Received= $donnees[0][ 'Animal_idAnimal' ];
			$sommeEngagee1Received= $donnees[0][ 'sommeEngagee' ];
			
			$idAnimal2Received = $donnees[1][ 'Animal_idAnimal' ];
			$sommeEngagee2Received = $donnees[1][ 'sommeEngagee' ];
			
			//Asserts
			$this->assertEqual($idCombat, $idCombatReceived);
			$this->assertEqual($idAnimal1, $idAnimal1Received);
			$this->assertEqual(0, $sommeEngagee1Received);
			$this->assertEqual($idAnimal2, $idAnimal2Received);
			$this->assertEqual(0, $sommeEngagee2Received);
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : '.$e->getTraceAsString().'<br/>';
		}
		//On enlève la ligne insérée pour le test
		$this->deleteCombat($idCombat);
	}
	
	/**
	 * 
	 */
	public function test_countPlayersByCombat()
	{
		try
		{
			//Ajout d'une action de test
			$dateCombat = 1360698377;
			$idCombat = "CTEST100";
			$estCommence = 1;
			$idAnimal1 = "ATEST001";
			$idAnimal2 = "ATEST002";
		
			//Execution de la méthode à tester
			$this->addCombat($idCombat, $dateCombat);
			$this->addCombatAnimal($idCombat, $idAnimal1);
			$this->addCombatAnimal($idCombat, $idAnimal2);
				
			$combatReceived = $this->_daoCombat->countPlayersByCombat($idCombat);
		
			//Verification
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT COUNT(*) as nbPlayers FROM t_combat_animal WHERE Combat_idCombat = :idCombat");
			$requete_prepare->bindParam(':idCombat', $idCombat, PDO::PARAM_STR);
			$requete_prepare->execute();
			$donnees = $requete_prepare->fetch(PDO::FETCH_ASSOC);
				
			$nbPlayersExpected =  $donnees[ 'nbPlayers' ] ;

			//Asserts
			$this->assertEqual($nbPlayersExpected, $combatReceived);

		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : '.$e->getTraceAsString().'<br/>';
		}
		//On enlève la ligne insérée pour le test
		$this->deleteCombat($idCombat);
	}
}

?>