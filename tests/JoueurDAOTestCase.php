<?php

require_once('./../../lib/simpletest/autorun.php');
require_once './../mapping/JoueurDAO.php';

/**
 *
 * Classe de test de la classe JoueurDAO (package mapping)
 *
 */
class JoueurDAOTestCase extends UnitTestCase
{
	/** DAO */
	private $_dao;
	
	/** DAO Joueur à tester */
	private $_daoJoueur;
	
	/**
	 * Constructeur par défaut. Instancie le DAO et le DAO Joueur à tester.
	 */
	public function __construct()
	{
		$this->_dao = new DAO();
		$this->_daoJoueur = new JoueurDAO();
	}
	
	/**
	 * Test du constructeur de JoueurDAOTestCase.
	 */
	function test_constructeur_JoueurDAOTestCase()
	{
		//Asserts
		$this->assertNotNull($this->_dao);
		$this->assertNotNull($this->_daoJoueur);
		$this->assertTrue($this->_dao instanceof DAO);
		$this->assertTrue($this->_daoJoueur instanceof JoueurDAO);
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
	private function addAnimal($idAnimal, $proprietaire, $nomAnimal, $vie, $defense, $attaque, $race=null)
	{
		try
		{
			//Insertion
			$requete_prepare = $this->_dao->getConnexion()->prepare("INSERT INTO t_animal (idAnimal, nomAnimal, vie, defense, attaque, RaceAnimal_race) VALUES (:id, :nom, :vie, :def, :att, :race)");
			$requete_prepare->bindParam(':id', $idAnimal, PDO::PARAM_STR);
			$requete_prepare->bindParam(':nom', $nomAnimal, PDO::PARAM_STR);
			$requete_prepare->bindParam(':vie', $vie, PDO::PARAM_INT);
			$requete_prepare->bindParam(':def', $defense, PDO::PARAM_INT);
			$requete_prepare->bindParam(':att', $attaque, PDO::PARAM_INT);
			$requete_prepare->bindParam(':race', $race, PDO::PARAM_STR);
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
			$raceReceived = $donnees->RaceAnimal_race;
			
			//Asserts
			$this->assertTrue($boolInsert1);
			$this->assertTrue($boolInsert2);
			$this->assertEqual($idAnimal, $idAnimalReceived);
			$this->assertEqual($proprietaire, $proprietaireReceived);
			$this->assertEqual($nomAnimal, $nomReceived);
			$this->assertEqual($vie, $vieReceived);
			$this->assertEqual($defense, $defenseReceived);
			$this->assertEqual($attaque, $attaqueReceived);
			$this->assertEqual($race, $raceReceived);
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : '.$e->getTraceAsString().'<br/>';
		}
	}
	
	/**
	 * Permet d'ajouter un joueur de test dans la base de données et d'être sur que l'ajout ait eu lieu.
	 * @param String $idJoueur
	 * @param int $credit
	 * @param int $dateInscription
	 */
	private function addPlayer($idJoueur, $credit, $dateInscription)
	{
		try
		{
			//Insertion
			$requete_prepare = $this->_dao->getConnexion()->prepare("INSERT INTO t_joueur (idFacebook, credit, dateInscription) VALUES (:id, :credit, :date)");
			$requete_prepare->bindParam(':id', $idJoueur, PDO::PARAM_STR);
			$requete_prepare->bindParam(':credit', $credit, PDO::PARAM_INT);
			$requete_prepare->bindParam(':date', $dateInscription, PDO::PARAM_INT);
			$bool = $requete_prepare->execute();
			
			//Select pour vérification
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT * FROM t_joueur WHERE idFacebook = :idJoueur");
			$requete_prepare->bindParam(':idJoueur', $idJoueur, PDO::PARAM_STR);
			$requete_prepare->execute();
			$donnees = $requete_prepare->fetch(PDO::FETCH_OBJ);
			$idJoueurReceived = $donnees->idFacebook;
			$creditReceived = $donnees->credit;
			$dateInscriptionReceived = $donnees->dateInscription;
			
			//Asserts
			$this->assertTrue($bool);
			$this->assertEqual($idJoueur, $idJoueurReceived);
			$this->assertEqual($credit, $creditReceived);
			$this->assertEqual($dateInscription, $dateInscriptionReceived);
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
	
	/**
	 * Permet de supprimer un joueur de test de la base de données et d'être sur que la suppression ait eu lieu.
	 * @param String $idJoueur
	 */
	private function deletePlayer($idJoueur)
	{
		try
		{
			//Suppression
		 	$requete_prepare = $this->_dao->getConnexion()->prepare("DELETE FROM t_joueur WHERE idFacebook = :idFacebook");
		 	$requete_prepare->bindParam(':idFacebook', $idJoueur, PDO::PARAM_STR);
		 	$bool = $requete_prepare->execute();
		
			//Select pour vérification
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT COUNT(*) as COUNT FROM t_joueur WHERE idFacebook = :id");
			$requete_prepare->bindParam(':id', $idJoueur, PDO::PARAM_STR);
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
	
	// -------------------------------- TESTS DE LA CLASSE JOUEURDAO ---------------------------------------
	
	/**
	 * Test de l'initialisation d'une connexion à la base de données dans le constructeur.
	 * Fait appel au constructeur de la classe abstraite mère DAO.
	 */
	function test_constructeur()
	{
		$dao = new JoueurDAO(); /* Initialisation de la connexion à la base lors de la construction */
		/* Asserts */
		$this->assertNotNull($dao);
		$this->assertNotNull($dao->getConnexion());
	}

	/**
	 * Test de la fermeture de la connexion à la base de données dans le constructeur.
	 * Fait appel au destructeur de la classe abstraite mère DAO.
	 */
	function test_destructeur()
	{
		$dao = new JoueurDAO();
		$dao->__destruct(); /* Fermeture de la connexion à la base */
		/* Assert */
		$this->assertNull($dao->getConnexion());
	}

	/**
	 * Test de la fermeture de la connexion à la base de données dans le constructeur.
	 * Fait appel au destructeur de la classe abstraite mère DAO.
	 */
	function test_updateCredit()
	{
		try
		{
			//Joueur de test
		 	$idJoueur = "JTEST0001";
			$credit = 150;
			$dateInscription = time();
		 	$joueur = new Joueur($idJoueur, null, array(), $credit, null);
			$this->addPlayer($idJoueur, $credit, $dateInscription);
		 	
			//Exécution de la méthode à tester
		 	$this->_daoJoueur->updateCredit(150, "+", $idJoueur);
		 	
		 	//Select pour vérification
		 	$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT * FROM t_joueur WHERE idFacebook = :idFacebook");
		 	$requete_prepare->bindParam(':idFacebook', $idJoueur, PDO::PARAM_STR);
		 	$requete_prepare->execute();
		 	$donnees = $requete_prepare->fetch(PDO::FETCH_OBJ);
			
		 	//Assert
		 	$creditReceived = $donnees->credit;
		 	$creditExpected = 300;
		 	$this->assertEqual($creditReceived, $creditExpected);
		 	
		 	//Exécution de la méthode à tester
		 	$this->_daoJoueur->updateCredit(100, "-", $idJoueur);
		 	 
		 	//select pour vérification
		 	$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT * FROM t_joueur WHERE idFacebook = :idFacebook");
		 	$requete_prepare->bindParam(':idFacebook', $idJoueur, PDO::PARAM_STR);
		 	$requete_prepare->execute();
		 	$donnees = $requete_prepare->fetch(PDO::FETCH_OBJ);
		 	
		 	//Assert
		 	$creditReceived = $donnees->credit;
		 	$creditExpected = 200;
		 	$this->assertEqual($creditReceived, $creditExpected);
	 	}
	 	catch(Exception $e)
	 	{
	 		echo 'Exception reçue : '.$e->getMessage().'<br/>';
	 		echo 'Trace : '.$e->getTraceAsString().'<br/>';
	 	}
	 	//Suppression de l'occurrence de test
	 	$this->deletePlayer($idJoueur);
	 }	
	 
	 /**
	  * Test de la méthode isPlayerExist() de JoueurDAO.
	  */
	 function test_isPlayerExist()
	 {
	 	try 
	 	{
		 	//On créé un joueur test
		 	$idFacebook = "100000932826921";
		 	$credit = 1000;
		 	$dateInscription = time();
		 	$this->addPlayer($idFacebook, $credit, $dateInscription);
		 
		 	//On exécute la méthode à tester
		 	$boolReceived = $this->_daoJoueur->isPlayerExist($idFacebook);
		 	
		 	//Select pour vérification
		 	$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT COUNT(*) as COUNT FROM t_joueur WHERE idFacebook = :id");
		 	$requete_prepare->bindParam(':id', $idFacebook, PDO::PARAM_STR);
		 	$requete_prepare->execute();
		 	$count = $requete_prepare->fetch(PDO::FETCH_OBJ)->COUNT;
		 	
		 	//Asserts
		 	$this->assertEqual($boolReceived, true);
		 	$this->assertEqual($count, 1);
	 	}
	 	catch(Exception $e)
	 	{
	 		echo 'Exception reçue : '.$e->getMessage().'<br/>';
	 		echo 'Trace : '.$e->getTraceAsString().'<br/>';
	 	}	
	 	//Suppression du joueur de test créé
	 	$this->deletePlayer($idFacebook);
	 }
	 
	 
/**
	 * Test de la méthode test_createJoueur() de JoueurDAO.
	 */
	function test_createJoueur()
	{
		try 
		{
		 	//Comptage du nombre de joueurs en base AVANT exécution de la méthode
		 	$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT COUNT(*) as COUNT FROM t_joueur");
		 	$requete_prepare->execute();
		 	$countBefore = $requete_prepare->fetch(PDO::FETCH_OBJ)->COUNT;
		 	
		 	//Exécution de la fonction à tester
		 	$idJoueurExpected = "100000932826939";
		 	$creditExpected = 1000;
		 	$dateExpected = time();
		 	$joueurExpected = new Joueur($idJoueurExpected, null, null, $creditExpected, $dateExpected);
		 	$boolReceived = $this->_daoJoueur->createJoueur($joueurExpected);
		 	
		 	//Comptage du nombre de joueurs en base APRES exécution de la méthode
		 	$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT COUNT(*) as COUNT FROM t_joueur");
		 	$requete_prepare->execute();
		 	$countAfter = $requete_prepare->fetch(PDO::FETCH_OBJ)->COUNT;
		 	
		 	//Récupération du joueur ajouté
		 	$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT * FROM t_joueur WHERE idFacebook = :idFb");
		 	$requete_prepare->bindParam(':idFb', $idJoueurExpected, PDO::PARAM_STR);
		 	$requete_prepare->execute();
		 	$donnees = $requete_prepare->fetch(PDO::FETCH_OBJ);
		 	$idJoueurReceived = $donnees->idFacebook;
		 	$creditReceived = $donnees->credit;
		 	$dateReceived = $donnees->dateInscription;
		 	$joueurReceived = new Joueur($idJoueurReceived, null, null, $creditReceived, $dateReceived);
		 	
		 	//Asserts
		 	$this->assertEqual($countBefore+1, $countAfter); 
		 	$this->assertTrue($boolReceived);
		 	$this->assertEqual($idJoueurExpected, $idJoueurReceived);
		 	$this->assertEqual($creditExpected, $creditReceived);
		 	$this->assertEqual($dateExpected, $dateReceived);
		 	$this->assertEqual($joueurExpected, $joueurReceived);
	 	}
	 	catch(Exception $e)
	 	{
	 		echo 'Exception reçue : '.$e->getMessage().'<br/>';
	 		echo 'Trace : '.$e->getTraceAsString().'<br/>';
	 	}
	 	//Suppression du joueur de test créé
	 	$this->deletePlayer($idJoueurExpected);
	}
	
	/**
	 * Test de la méthode getJoueur() de JoueurDAO.
	 */
	function test_getJoueur()
	{
		try 
		{
			//Création d'un joueur test
			$idFacebookExpected = "100000932826955";
			$creditExpected = 1000;
			$dateInscriptionExpected = time();
			$this->addPlayer($idFacebookExpected, $creditExpected, $dateInscriptionExpected);
			 
			//Execution de la méthode à tester
			$joueur = $this->_daoJoueur->getJoueur($idFacebookExpected);
			 
			//Asserts
			$this->assertNotNull($joueur);
			$this->assertEqual($idFacebookExpected, $joueur->getIdFacebook());
			$this->assertEqual($creditExpected, $joueur->getCredit());
			$this->assertEqual($dateInscriptionExpected, $joueur->getDateInscription());
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : '.$e->getTraceAsString().'<br/>';
		} 
		//Suppression du joueur de test créé
		$this->deletePlayer($idFacebookExpected);
	}
	
	/**
	 * Test de la méthode getAnimalByJoueur() de JoueurDAO.
	 */
	function test_getAnimalByJoueur()
	{
		try
		{
			//Création d'un animal test + joueur test
			$idJoueur = "JTEST0017";
			$this->addPlayer($idJoueur, 1000, time());
			$idA = "ATEST0017";
			$this->addAnimal($idA, $idJoueur, "Herisson test", 10, 30, 20, 'R0003');
	
			//Execution de la méthode à tester
			$animalReceived = $this->_daoJoueur->getAnimalByJoueur($idJoueur);

			//Select de l'animal du joueur
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT idAnimal, nomAnimal, vie, defense, attaque, niveau, nomRace FROM t_joueur_animal, t_animal, t_raceanimal WHERE Joueur_idFacebook = :idFb AND Animal_idAnimal = idAnimal AND RaceAnimal_race = idRace");
			$requete_prepare->bindParam(':idFb', $idJoueur, PDO::PARAM_STR);
			$requete_prepare->execute();
			$donnees = $requete_prepare->fetch(PDO::FETCH_ASSOC);
			$idAnimal = $donnees['idAnimal'];
			$nomAnimal = $donnees['nomAnimal'];
			$vie = $donnees['vie'];
			$defense = $donnees['defense'];
			$attaque = $donnees['attaque'];
			$niveau = $donnees['niveau'];
			$race = $donnees['nomRace'];
			$animalExpected = new Animal($idAnimal, $idJoueur, $nomAnimal, $vie, $defense, $attaque, $niveau, $race);
			
			//Asserts
			$this->assertNotNull($animalExpected);
			$this->assertEqual($animalExpected, $animalReceived);
			
			//Suppressions
			$this->deletePlayer($idJoueur);
			$this->deleteAnimal($idA);
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : '.$e->getTraceAsString().'<br/>';
		}
	}
	
	/**
	 * Test de la méthode getJoueurByAnimal() de JoueurDAO.
	 */
	function test_getJoueurByAnimal()
	{
		try
		{
			//Création d'un animal test + joueur test
			$idJoueur = "JTEST0017";
			$dateInscription = time();
			$credit = 1000;
			$this->addPlayer($idJoueur, $credit, $dateInscription);
			$idA = "ATEST0017";
			$this->addAnimal($idA, $idJoueur, "Herisson test", 10, 30, 20, 'R0003');
	
			//Execution de la méthode à tester
			$joueurReceived = $this->_daoJoueur->getJoueurByAnimal($idA);
	
			//Asserts
			$this->assertNotNull($joueurReceived);
			$this->assertEqual($joueurReceived->getNomJoueur(), null);
			$this->assertEqual($joueurReceived->getAmis(), array());
			$this->assertEqual($joueurReceived->getCredit(), $credit);
			$this->assertEqual($joueurReceived->getDateInscription(), $dateInscription);
			$this->assertEqual($joueurReceived->getIdFacebook(), $idJoueur);
				
			//Suppressions
			$this->deletePlayer($idJoueur);
			$this->deleteAnimal($idA);
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : '.$e->getTraceAsString().'<br/>';
		}
	}
	
	/**
	 * Test de la méthode addVictoire() de JoueurDAO.
	 */
	function test_addVictoire()
	{
		try
		{
			//Création d'un animal test + joueur test
			$idJoueur = "JTEST0017";
			$dateInscription = time();
			$credit = 1000;
			$this->addPlayer($idJoueur, $credit, $dateInscription);
			$idA = "ATEST0017";
			$this->addAnimal($idA, $idJoueur, "Herisson test", 10, 30, 20, 'R0003');
	
			//Execution de la méthode à tester
			$boolReceived = $this->_daoJoueur->addVictoire($idA);
			
			//Vérifications
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT * FROM t_joueur WHERE idFacebook = :idFb");
			$requete_prepare->bindParam(':idFb', $idJoueur, PDO::PARAM_STR);
			$requete_prepare->execute();
			$donnees = $requete_prepare->fetch(PDO::FETCH_ASSOC);
			$nbVictoiresJoueur = $donnees['nbVictoires'];
			
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT * FROM t_animal WHERE idAnimal = :idAnimal");
			$requete_prepare->bindParam(':idAnimal', $idA, PDO::PARAM_STR);
			$requete_prepare->execute();
			$donnees = $requete_prepare->fetch(PDO::FETCH_ASSOC);
			$nbVictoiresAnimal = $donnees['nbVictoires'];
	
			//Asserts
			$this->assertTrue($boolReceived);
			$this->assertEqual($nbVictoiresJoueur, 1);
			$this->assertEqual($nbVictoiresAnimal, 1);
	
			//Suppressions
			$this->deletePlayer($idJoueur);
			$this->deleteAnimal($idA);
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : '.$e->getTraceAsString().'<br/>';
		}
	}
	
	/**
	 * Test de la méthode addDefaite() de JoueurDAO.
	 */
	function test_addDefaite()
	{
		try
		{
			//Création d'un animal test + joueur test
			$idJoueur = "JTEST0017";
			$dateInscription = time();
			$credit = 1000;
			$this->addPlayer($idJoueur, $credit, $dateInscription);
			$idA = "ATEST0017";
			$this->addAnimal($idA, $idJoueur, "Herisson test", 10, 30, 20, 'R0003');
	
			//Execution de la méthode à tester
			$boolReceived = $this->_daoJoueur->addDefaite($idA);
				
			//Vérifications
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT * FROM t_joueur WHERE idFacebook = :idFb");
			$requete_prepare->bindParam(':idFb', $idJoueur, PDO::PARAM_STR);
			$requete_prepare->execute();
			$donnees = $requete_prepare->fetch(PDO::FETCH_ASSOC);
			$nbDefaitesJoueur = $donnees['nbDefaites'];
				
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT * FROM t_animal WHERE idAnimal = :idAnimal");
			$requete_prepare->bindParam(':idAnimal', $idA, PDO::PARAM_STR);
			$requete_prepare->execute();
			$donnees = $requete_prepare->fetch(PDO::FETCH_ASSOC);
			$nbDefaitesAnimal = $donnees['nbDefaites'];
	
			//Asserts
			$this->assertTrue($boolReceived);
			$this->assertEqual($nbDefaitesJoueur, 1);
			$this->assertEqual($nbDefaitesAnimal, 1);
	
			//Suppressions
			$this->deletePlayer($idJoueur);
			$this->deleteAnimal($idA);
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : '.$e->getTraceAsString().'<br/>';
		}
	}
	
	/**
	 * Test de la méthode addAbandon() de JoueurDAO.
	 */
	function test_addAbandon()
	{
		try
		{
			//Création d'un animal test + joueur test
			$idJoueur = "JTEST0017";
			$dateInscription = time();
			$credit = 1000;
			$this->addPlayer($idJoueur, $credit, $dateInscription);
			$idA = "ATEST0017";
			$this->addAnimal($idA, $idJoueur, "Herisson test", 10, 30, 20, 'R0003');
	
			//Execution de la méthode à tester
			$boolReceived = $this->_daoJoueur->addAbandon($idA);
	
			//Vérifications
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT * FROM t_joueur WHERE idFacebook = :idFb");
			$requete_prepare->bindParam(':idFb', $idJoueur, PDO::PARAM_STR);
			$requete_prepare->execute();
			$donnees = $requete_prepare->fetch(PDO::FETCH_ASSOC);
			$nbAbandonsJoueur = $donnees['nbAbandons'];
	
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT * FROM t_animal WHERE idAnimal = :idAnimal");
			$requete_prepare->bindParam(':idAnimal', $idA, PDO::PARAM_STR);
			$requete_prepare->execute();
			$donnees = $requete_prepare->fetch(PDO::FETCH_ASSOC);
			$nbAbandonsAnimal = $donnees['nbAbandons'];
	
			//Asserts
			$this->assertTrue($boolReceived);
			$this->assertEqual($nbAbandonsJoueur, 1);
			$this->assertEqual($nbAbandonsAnimal, 1);
	
			//Suppressions
			$this->deletePlayer($idJoueur);
			$this->deleteAnimal($idA);
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : '.$e->getTraceAsString().'<br/>';
		}
	}
}

?>