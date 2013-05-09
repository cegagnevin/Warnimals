<?php

require_once './../../lib/simpletest/autorun.php';
require_once './../mapping/AnimalDAO.php';
require_once './../modeles/AnimalModele.php';
require_once './../metiers/OffreEntrainement.php';
require_once './../metiers/EntrainementIndividuel.php';

/**
 *
 * Classe de test de la classe AnimalDAO (package mapping)
 *
 */
class AnimalDAOTestCase extends UnitTestCase
{
	/** DAO */
	private $_dao;
	
	/** DAO Animal à tester */
	private $_daoAnimal;

	/**
	 * Constructeur par défaut. Instancie le DAO.
	 */
	public function __construct()
	{
		$this->_dao = new DAO();
		$this->_daoAnimal = new AnimalDAO();
	}
	
	public function __destruct()
	{
		$this->_dao->__destruct();
		$this->_daoAnimal->__destruct();
	}
	
	/**
	 * Test du constructeur de AnimalDAOTestCase.
	 */
	function test_constructeur_AnimalDAOTestCase()
	{
		//Asserts
		$this->assertNotNull($this->_dao);
		$this->assertNotNull($this->_daoAnimal);
		$this->assertTrue($this->_dao instanceof DAO);
		$this->assertTrue($this->_daoAnimal instanceof AnimalDAO);
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
	
	// -------------------------------- TESTS DE LA CLASSE ANIMALDAO ---------------------------------------
	
	
	/**
	 * Test de l'initialisation d'une connexion à la base de données dans le constructeur.
	 * Fait appel au constructeur de la classe abstraite mère DAO.
	 */
	function test_constructeur()
	{
		$dao = new AnimalDAO();
		/* Asserts */
		$this->assertNotNull($dao);
		$this->assertTrue($dao instanceof AnimalDAO);
		$this->assertNotNull($dao->getConnexion());
	}
	
	/**
	 * Test de la fermeture de la connexion à la base de données dans le constructeur.
	 * Fait appel au destructeur de la classe abstraite mère DAO.
	 */
	function test_destructeur()
	{
		$dao = new AnimalDAO();
		$dao->__destruct(); /* Fermeture de la connexion à la base */
		/* Assert */
		$this->assertNull($dao->getConnexion());
	}
	
	/**
	 * Test de la méthode : updateProprietaire($idProprietaire, $idAnimal).
	 */
	function test_updateProprietaire()
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
			
			//Exécution de la méthode testée
			$proprietaireExpected = 'JTEST002';
			$boolReceived = $this->_daoAnimal->updateProprietaire($proprietaireExpected, $idAnimal);
			
			//On récupère la nouvelle valeur du propriétaire
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT Joueur_idFacebook FROM t_joueur_animal WHERE Animal_idAnimal = :id");
			$requete_prepare->bindParam(':id', $idAnimal, PDO::PARAM_STR);
			$requete_prepare->execute();
			$proprietaireReceived = $requete_prepare->fetch(PDO::FETCH_OBJ)->Joueur_idFacebook;
			
			//Assert
			$this->assertTrue($boolReceived); //On teste si la méthode revoie true.
			$this->assertEqual($proprietaireExpected, $proprietaireReceived); //On test si le changement a bien été fait
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : '.$e->getTraceAsString().'<br/>';
		}
		//On enlève la ligne insérée pour le test
		$this->deleteAnimal($idAnimal);
		
		//On enleve la ligne dans la table t_joueur_animal
		$requete_prepare = $this->_dao->getConnexion()->prepare("DELETE FROM t_joueur_animal WHERE Animal_idAnimal = :idAnimal AND Joueur_idFacebook = :idJoueur");
		$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
		$requete_prepare->bindParam(':idJoueur', $proprietaireExpected, PDO::PARAM_STR);
		$bool = $requete_prepare->execute();
		//Select pour vérification
		$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT COUNT(*) as COUNT FROM t_joueur_animal WHERE Animal_idAnimal = :idAnimal AND Joueur_idFacebook = :idJoueur");
		$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
		$requete_prepare->bindParam(':idJoueur', $proprietaireExpected, PDO::PARAM_STR);
		$requete_prepare->execute();
		$count = $requete_prepare->fetch(PDO::FETCH_OBJ)->COUNT;
		//Asserts
		$this->assertTrue($bool);
		$this->assertEqual($count, 0);
	}
	
	/**
	 * Test de la méthode : createAnimal($proprietaire, $nom, $vie, $defense, $attaque).
	 */
	function test_createAnimal()
	{
		try 
		{
			//Exécution de la méthode testée
			$idAnimalCree = $this->_daoAnimal->createAnimal('JTEST001', 'Lapin Bleu', 20, 10, 5);
			
			//Test si la requete a été effectuée
			$this->assertNotEqual($idAnimalCree, -1);
			
			//Récupération de l'animal créé
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT * FROM t_animal WHERE idAnimal = :id");
			$requete_prepare->bindParam(':id', $idAnimalCree, PDO::PARAM_STR);
			$requete_prepare->execute();
			$animalCree = $requete_prepare->fetch(PDO::FETCH_OBJ);
			$idAnimalReceived = $animalCree->idAnimal;
			$nomReceived = $animalCree->nomAnimal;
			$vieReceived = $animalCree->vie;
			$defenseReceived = $animalCree->defense;
			$attaqueReceived = $animalCree->attaque;
			
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT * FROM t_joueur_animal WHERE Animal_idAnimal = :id");
			$requete_prepare->bindParam(':id', $idAnimalCree, PDO::PARAM_STR);
			$requete_prepare->execute();	
			$donnees = $requete_prepare->fetch(PDO::FETCH_OBJ);
			$proprietaireReceived = $donnees->Joueur_idFacebook;
			
			//Assert
			$this->assertEqual($idAnimalReceived, $idAnimalCree);
			$this->assertEqual($proprietaireReceived, 'JTEST001');
			$this->assertEqual($nomReceived, 'Lapin Bleu');
			$this->assertEqual($vieReceived, 20);
			$this->assertEqual($defenseReceived, 10);
			$this->assertEqual($attaqueReceived, 5);
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : '.$e->getTraceAsString().'<br/>';
		}	
		//Suppression de l'animal créé en base pour le test
		$this->deleteAnimal($idAnimalCree);
	}
	
	/**
	 * Test de la méthode : getAnimal($idAnimal).
	 */
	function test_getAnimal()
	{
		try 
		{
			//Création d'une ligne de test dans la table t_animal
			$idAnimal = 'ATEST001';
			$proprietaire = 'JTEST001';
			$nom = 'Lapin test';
			$vie = 40;
			$defense = 50;
			$attaque = 60;
			$niveau = 1;
			$this->addAnimal($idAnimal, $proprietaire, $nom, $vie, $defense, $attaque, $niveau);
			
			//Exécution de la méthode testée
			$animalReceived = $this->_daoAnimal->getAnimal($idAnimal);
		
			//Assert
			$this->assertEqual($animalReceived->getIdAnimal(), $idAnimal);
			$this->assertEqual($animalReceived->getProprietaire(), $proprietaire);
			$this->assertEqual($animalReceived->getNomAnimal(), $nom);
			$this->assertEqual($animalReceived->getVie(), $vie);
			$this->assertEqual($animalReceived->getDefense(), $defense);
			$this->assertEqual($animalReceived->getAttaque(), $attaque);
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : '.$e->getTraceAsString().'<br/>';
		}		
		//Suppression de l'animal créé en base pour le test
		$this->deleteAnimal($idAnimal);
	}
	
	/**
	 * Test de la méthode : getAnimauxEnVente().
	 */
	function test_getAnimauxEnVente()
	{
		try
		{
			//Insertion d'un animal en vente (sans proprio)
			$idAnimal = 'ATEST005';
			$proprietaire = '';
			$nom = 'Lapin test';
			$vie = 40;
			$defense = 50;
			$attaque = 60;
			$niveau = 1;
			$this->addAnimal($idAnimal, $proprietaire, $nom, $vie, $defense, $attaque, $niveau);
			
			//Select sur les résultats à obtenir
			$sansProprietaire = '';
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT * FROM t_joueur_animal WHERE Joueur_idFacebook = :id");
			$requete_prepare->bindParam(':id', $sansProprietaire, PDO::PARAM_STR);
			$requete_prepare->execute();
			
			$tabAnimauxExpected = array();
			$nbOcc = 0;
			$mdlAnimal = AnimalModele::getInstance();
			$donnees = $requete_prepare->fetchAll(PDO::FETCH_OBJ);
			foreach ($donnees as $value)
			{
				$animal = $mdlAnimal->getAnimal($value->Animal_idAnimal);
				$animal->setProprietaire($value->Joueur_idFacebook);
				$tabAnimauxExpected[$nbOcc++] = $animal;
			}
			
			//Exécution de la méthode à tester
			$tabAnimauxReceived = $this->_daoAnimal->getAnimauxEnVente();
			
			//Asserts
			$this->assertNotNull($tabAnimauxExpected);
			$this->assertNotNull($tabAnimauxReceived);
			$this->assertEqual($tabAnimauxExpected, $tabAnimauxReceived);
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : '.$e->getTraceAsString().'<br/>';
		}
		//Suppression de l'animal créé en base pour le test
		$this->deleteAnimal($idAnimal);
	}
	
	/**
	 * Test de la méthode : inscriptionEntrainement().
	 */
	function test_inscriptionEntrainement()
	{
		try
	 	{
	 		//Création d'une ligne de test dans la table t_entrainement_animal
	 		$idEntrainement = 'ETEST005';
	 		$idAnimal = 'ATEST005';
			$date = time();
	 		
	 		//Exécution de la méthode testée
	 		//Renvoie la date si ok est -1 si pas bon
	 		$this->_daoAnimal->inscriptionEntrainement($idAnimal, $idEntrainement, $date);
	 		
	 		//Select pour vérification
	 		$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT * FROM t_entrainement_animal WHERE Entrainement_idEntrainement = :idEntrainement AND Animal_idAnimal = :idAnimal");
	 		$requete_prepare->bindParam(':idEntrainement', $idEntrainement, PDO::PARAM_STR);
	 		$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
	 		$requete_prepare->execute();
	 		$donnees = $requete_prepare->fetch(PDO::FETCH_OBJ);
	 		$idEntrainementReceived = $donnees->Entrainement_idEntrainement;
	 		$idAnimalReceived = $donnees->Animal_idAnimal;	 	
	 
	 		//Assert
	 		$this->assertEqual($idEntrainementReceived, $idEntrainement);
	 		$this->assertEqual($idAnimalReceived, $idAnimal);
	 		//$this->assertEqual($dateSousReceived, $date);
	 	}
	 	catch(Exception $e)
	 	{
	 		echo 'Exception reçue : '.$e->getMessage().'<br/>';
	 		echo 'Trace : '.$e->getTraceAsString().'<br/>';
	 	}
	 	//Suppression de l'entrainement créé en base pour le test
		$requete_prepare = $this->_dao->getConnexion()->prepare("DELETE FROM t_entrainement_animal WHERE Entrainement_idEntrainement = :idEntrainement AND Animal_idAnimal = :idAnimal");
	 	$requete_prepare->bindParam(':idEntrainement', $idEntrainement, PDO::PARAM_STR);
	 	$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
		$bool = $requete_prepare->execute();
	
		$this->assertTrue($bool);
	}
	

	/**
	 * Test de la méthode : desinscriptionEntrainement().
	 */
	function test_desinscriptionEntrainement()
	{
		try
		{
			//Création d'une ligne de test dans la table T_ENTRAINEMENT_ANIMAL
			$idEntrainement = 'ETEST005';
			$idAnimal = 'ATEST005';
			$date = time();
	
			//Ajout de la ligne de test dans la table.
			$requete_prepare = $this->_dao->getConnexion()->prepare("INSERT INTO t_entrainement_animal (Entrainement_idEntrainement, Animal_idAnimal, dateSouscription) VALUES (:idEntrainement, :idAnimal, :date)");
			$requete_prepare->bindParam(':idEntrainement', $idEntrainement, PDO::PARAM_STR);
			$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
			$requete_prepare->bindParam(':date', $date, PDO::PARAM_STR);
			$bool = $requete_prepare->execute();
				
			//Exécution de la méthode testée
			$this->_daoAnimal->desinscriptionEntrainement($idAnimal, $idEntrainement);
	
			//Select pour vérification
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT COUNT(*) as COUNT FROM t_entrainement_animal WHERE Entrainement_idEntrainement = :idEntrainement AND Animal_idAnimal = :idAnimal");
			$requete_prepare->bindParam(':idEntrainement', $idEntrainement, PDO::PARAM_STR);
			$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
			$requete_prepare->execute();
			$count = $requete_prepare->fetch(PDO::FETCH_OBJ)->COUNT;
	
			//Assert
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
	 * Test de la méthode : isTraining().
	 */
	function test_isTraining()
	{
		try
		{
			//Création des occurrences de test
			$idEntrainement = 'ETEST008';
			$duree = 3600;
			$prix = 1000;
			$dateDebut = time() - 1000;
			$type = 'individuel';
			$niveauMax = 15;
			$idOffre = 'O1';
			$idAnimal = 'ATEST008';
			$date = time();
			
			$this->addAnimal($idAnimal, "JTEST008", "Lapin test", 30, 20, 10, 1);
			
			$requete_prepare = $this->_dao->getConnexion()->prepare("INSERT INTO t_entrainement (idEntrainement, duree, prix, dateDebut, type, niveauMax, OffreEntrainement_idOffre) VALUES (:idEntrainement, :duree, :prix, :dateDebut, :type, :niveauMax, :idOffre)");
			$requete_prepare->bindParam(':idEntrainement', $idEntrainement, PDO::PARAM_STR);
			$requete_prepare->bindParam(':duree', $duree, PDO::PARAM_INT);
			$requete_prepare->bindParam(':prix', $prix, PDO::PARAM_INT);
			$requete_prepare->bindParam(':dateDebut', $dateDebut, PDO::PARAM_INT);
			$requete_prepare->bindParam(':type', $type, PDO::PARAM_STR);
			$requete_prepare->bindParam(':niveauMax', $niveauMax, PDO::PARAM_INT);
			$requete_prepare->bindParam(':idOffre', $idOffre, PDO::PARAM_STR);
			$bool1 = $requete_prepare->execute();

			$requete_prepare = $this->_dao->getConnexion()->prepare("INSERT INTO t_entrainement_animal (Entrainement_idEntrainement, Animal_idAnimal, dateSouscription) VALUES (:idEntrainement, :idAnimal, :date)");
			$requete_prepare->bindParam(':idEntrainement', $idEntrainement, PDO::PARAM_STR);
			$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
			$requete_prepare->bindParam(':date', $date, PDO::PARAM_INT);
			$bool2 = $requete_prepare->execute();
			
			//Exécution de la méthode testée
			$bool1Received = $this->_daoAnimal->isTraining($idAnimal);

			//Assert
			$this->assertTrue($bool1);
			$this->assertTrue($bool2);
			$this->assertTrue($bool1Received);
				
			// -----------------------------------
			
			
			//On modifie l'entrainement pour qu'il soit terminé
			$requete_prepare = $this->_dao->getConnexion()->prepare("UPDATE t_entrainement SET duree = 100 WHERE idEntrainement = :idEntrainement");
			$requete_prepare->bindParam(':idEntrainement', $idEntrainement, PDO::PARAM_STR);
			$bool1 = $requete_prepare->execute();
			
			//Exécution de la méthode testée
			$bool1Received = $this->_daoAnimal->isTraining($idAnimal);
			
			//Asserts
			$this->assertTrue($bool1);
			$this->assertFalse($bool1Received);
			
			//Suppressions
			$this->deleteAnimal($idAnimal);
			
			$requete_prepare = $this->_dao->getConnexion()->prepare("DELETE FROM t_entrainement WHERE idEntrainement = :idEntrainement");
			$requete_prepare->bindParam(':idEntrainement', $idEntrainement, PDO::PARAM_STR);
			$bool1 = $requete_prepare->execute();
			$this->assertTrue($bool1);
			
			$requete_prepare = $this->_dao->getConnexion()->prepare("DELETE FROM t_entrainement_animal WHERE Entrainement_idEntrainement = :idEntrainement AND Animal_idAnimal = :idAnimal");
			$requete_prepare->bindParam(':idEntrainement', $idEntrainement, PDO::PARAM_STR);
			$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
			$bool2 = $requete_prepare->execute();
			$this->assertTrue($bool2);
			
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : '.$e->getTraceAsString().'<br/>';
		}
	}

	
	/**
	 * Test de la méthode : entrainerAnimal().
	 */
	function test_entrainerAnimal()
	{
		try
		{
			//Création des occurrences de test
			$idOffre = "O36";
			$attaqueOffre = 1;
			$defenseOffre = 2;
			$vieOffre = 2;
			$levelUp = 0;
			$offre = new OffreEntrainement($idOffre, $attaqueOffre, $defenseOffre, $vieOffre, $levelUp);
			
			$idEntrainement = 'ETEST008';
			$duree = 3600;
			$prix = 1000;
			$dateDebut = time() - 1000;
			$entrainement = new EntrainementIndividuel($idEntrainement, $duree, $prix, $dateDebut, $offre, null);
				
			$idAnimal = 'ATEST004';
			$proprietaire = 'JTEST004';
			$nomAnimal = 'Lapin test';
			$attaqueAnimal = 30;
			$defenseAnimal = 20;
			$vieAnimal = 10;
			$niveauAnimal = 1;
			$this->addAnimal($idAnimal, $proprietaire, $nomAnimal, $vieAnimal, $defenseAnimal, $attaqueAnimal, $niveauAnimal);
				
			//Inscription de l'animal à l'entrainement
			$requete_prepare = $this->_dao->getConnexion()->prepare("INSERT INTO t_entrainement_animal (Entrainement_idEntrainement, Animal_idAnimal, dateSouscription, valide)
																	 VALUES (:idEntrainement, :idAnimal, :dateSouscription, 0)");
			$requete_prepare->bindParam(':idEntrainement', $idEntrainement, PDO::PARAM_STR);
			$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
			$requete_prepare->bindParam(':dateSouscription', $dateDebut, PDO::PARAM_INT);
			$bool = $requete_prepare->execute();
			$this->assertTrue($bool);
	
			//Exécution de la méthode testée
			$animalReceived = $this->_daoAnimal->entrainerAnimal($entrainement, $idAnimal);
			
			//Vérification de la validation de l'entrainement
			$idEntrainement = $entrainement->getIdEntrainement();
			$requete_prepare =  $this->_dao->getConnexion()->prepare("SELECT valide FROM t_entrainement_animal WHERE Entrainement_idEntrainement = :idEntrainement AND Animal_idAnimal = :idAnimal");
			$requete_prepare->bindParam(':idEntrainement', $idEntrainement, PDO::PARAM_STR);
			$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
			$requete_prepare->execute();
			$donnees = $requete_prepare->fetch(PDO::FETCH_ASSOC);
			$valide = $donnees[ 'valide' ];
	
			//Assert
			$this->assertNotNull($offre);
			$this->assertNotNull($entrainement);		
			$this->assertEqual($animalReceived->getIdAnimal(), $idAnimal);
			$this->assertEqual($animalReceived->getProprietaire(), $proprietaire);
			$this->assertEqual($animalReceived->getAttaque(), $attaqueAnimal + $attaqueOffre);
			$this->assertEqual($animalReceived->getDefense(), $defenseAnimal + $defenseOffre);
			$this->assertEqual($animalReceived->getVie(), $vieAnimal + $vieOffre);
			$this->assertEqual($animalReceived->getNiveau(), $niveauAnimal);
			$this->assertEqual($valide, 1);
			
			//Suppression
			$this->deleteAnimal($idAnimal);
			
			$requete_prepare = $this->_dao->getConnexion()->prepare("DELETE FROM t_entrainement_animal WHERE Entrainement_idEntrainement = :idEntrainement");
			$requete_prepare->bindParam(':idEntrainement', $idEntrainement, PDO::PARAM_STR);
			$bool = $requete_prepare->execute();
			$this->assertTrue($bool);
		
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : '.$e->getTraceAsString().'<br/>';
		}
	}
	
	/**
	 * Test de la méthode : levelUpAnimal().
	 */
	function test_levelUpAnimal()
	{
		try
		{
			//Création d'un animal de test
			$idAnimal = 'ATEST004';
			$nomAnimal = 'Lapin test';
			$attaqueAnimal = 15;
			$defenseAnimal = 20;
			$vieAnimal = 10;
			$niveauAnimal = 2;
			$raceAnimal = 'R0002';
			
			$requete_prepare = $this->_dao->getConnexion()->prepare("INSERT INTO t_animal (idAnimal, nomAnimal, vie, defense, attaque, niveau, RaceAnimal_race) VALUES(:idAnimal, :nom, :vie, :defense, :attaque, :niveau, :race)");
			$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
			$requete_prepare->bindParam(':nom', $nomAnimal, PDO::PARAM_STR);
			$requete_prepare->bindParam(':vie', $vieAnimal, PDO::PARAM_INT);
			$requete_prepare->bindParam(':defense', $defenseAnimal, PDO::PARAM_INT);
			$requete_prepare->bindParam(':attaque', $attaqueAnimal, PDO::PARAM_INT);
			$requete_prepare->bindParam(':niveau', $niveauAnimal, PDO::PARAM_INT);
			$requete_prepare->bindParam(':race', $raceAnimal, PDO::PARAM_STR);
			$bool = $requete_prepare->execute();
			$this->assertTrue($bool);
	
			//Exécution de la méthode testée
			$competenceReceived = $this->_daoAnimal->levelUpAnimal($idAnimal);
			
			
			//Verification de l'incrémentation du niveau
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT niveau FROM t_animal WHERE idAnimal = :idAnimal");
			$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
			$requete_prepare->execute();
			$niveauReceived = $requete_prepare->fetch(PDO::FETCH_OBJ)->niveau;
	
			//Assert
			$idCompetence = $competenceReceived->getIdCompetence();
			$this->assertNotNull($competenceReceived);
			$this->assertEqual($competenceReceived->getIdCompetence(), 'C0005');
			$this->assertEqual($competenceReceived->getNomCompetence(), 'Morsure');
			$this->assertEqual($competenceReceived->getDegats(), 10);
			$this->assertEqual($niveauReceived, $niveauAnimal + 1);
			
			
			//----------- Dans le cas où il n'y a pas de compétence à apprendre --------------------
			
			$requete_prepare = $this->_dao->getConnexion()->prepare("UPDATE t_animal SET niveau = 0 WHERE idAnimal = :idAnimal");
			$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
			$bool = $requete_prepare->execute();
			$this->assertTrue($bool);
			
			//Exécution de la méthode testée 
			$competenceReceived = $this->_daoAnimal->levelUpAnimal($idAnimal);
			
			//Verification de l'incrémentation du niveau
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT niveau FROM t_animal WHERE idAnimal = :idAnimal");
			$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
			$requete_prepare->execute();
			$niveauReceived = $requete_prepare->fetch(PDO::FETCH_OBJ)->niveau;
			
			//Assert
			$this->assertNull($competenceReceived);
			$this->assertEqual($niveauReceived, 1);
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : '.$e->getTraceAsString().'<br/>';
		}
		
		//Suppression
		$this->deleteAnimal($idAnimal);
			
		$requete_prepare = $this->_dao->getConnexion()->prepare("DELETE FROM t_competence_animal WHERE Competence_idCompetence = :idCompetence AND Animal_idAnimal = :idAnimal");
		$requete_prepare->bindParam(':idCompetence', $idCompetence, PDO::PARAM_STR);
		$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
		$bool = $requete_prepare->execute();
		$this->assertTrue($bool);
	}
	
	/**
	 * Test de la méthode : getLevelById().
	 */
	function test_getLevelById()
	{
		try
		{
			//Création d'un animal de test
			$idAnimal = 'ATEST004';
			$nomAnimal = 'Lapin test';
			$attaqueAnimal = 15;
			$defenseAnimal = 20;
			$vieAnimal = 10;
			$niveauAnimal = 1;
			$raceAnimal = 'R0002';
			
			$requete_prepare = $this->_dao->getConnexion()->prepare("INSERT INTO t_animal (idAnimal, nomAnimal, vie, defense, attaque, niveau, RaceAnimal_race) VALUES(:idAnimal, :nom, :vie, :defense, :attaque, :niveau, :race)");
			$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
			$requete_prepare->bindParam(':nom', $nomAnimal, PDO::PARAM_STR);
			$requete_prepare->bindParam(':vie', $vieAnimal, PDO::PARAM_INT);
			$requete_prepare->bindParam(':defense', $defenseAnimal, PDO::PARAM_INT);
			$requete_prepare->bindParam(':attaque', $attaqueAnimal, PDO::PARAM_INT);
			$requete_prepare->bindParam(':niveau', $niveauAnimal, PDO::PARAM_INT);
			$requete_prepare->bindParam(':race', $raceAnimal, PDO::PARAM_STR);
			$bool = $requete_prepare->execute();
			$this->assertTrue($bool);
	
			//Exécution de la méthode testée
			$niveauReceived = $this->_daoAnimal->getLevelById($idAnimal);
			
			//Verification de l'incrémentation du niveau
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT niveau FROM t_animal WHERE idAnimal = :idAnimal");
			$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
			$requete_prepare->execute();
			$niveauExpected = $requete_prepare->fetch(PDO::FETCH_OBJ)->niveau;
						
			//Assert
			$this->assertNotNull($niveauReceived);
			$this->assertEqual($niveauReceived, $niveauExpected);
			}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : '.$e->getTraceAsString().'<br/>';
		}
		
		//Suppression
		$this->deleteAnimal($idAnimal);		
	}


	/**
	 * Test de la méthode : getCompetencesByAnimal().
	 */
	function test_getCompetencesByAnimal()
	{
		try
		{
			//Création d'un animal de test
			$idAnimal = 'ATEST005';
			$nomAnimal = 'Lapin test';
			$attaqueAnimal = 15;
			$defenseAnimal = 20;
			$vieAnimal = 10;
			$niveauAnimal = 1;
			$raceAnimal = 'R0002';
				
			$requete_prepare = $this->_dao->getConnexion()->prepare("INSERT INTO t_animal (idAnimal, nomAnimal, vie, defense, attaque, niveau, RaceAnimal_race) VALUES(:idAnimal, :nom, :vie, :defense, :attaque, :niveau, :race)");
			$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
			$requete_prepare->bindParam(':nom', $nomAnimal, PDO::PARAM_STR);
			$requete_prepare->bindParam(':vie', $vieAnimal, PDO::PARAM_INT);
			$requete_prepare->bindParam(':defense', $defenseAnimal, PDO::PARAM_INT);
			$requete_prepare->bindParam(':attaque', $attaqueAnimal, PDO::PARAM_INT);
			$requete_prepare->bindParam(':niveau', $niveauAnimal, PDO::PARAM_INT);
			$requete_prepare->bindParam(':race', $raceAnimal, PDO::PARAM_STR);
			$bool = $requete_prepare->execute();
			$this->assertTrue($bool);
			
			//Ajout de compétences
			$idCompetence1 = 'C0001';
			$idCompetence2 = 'C0002';
			$requete_prepare = $this->_dao->getConnexion()->prepare("INSERT INTO t_competence_animal (Competence_idCompetence, Animal_idAnimal) VALUES(:idCompetence1, :idAnimal), (:idCompetence2, :idAnimal)");
			$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
			$requete_prepare->bindParam(':idCompetence1', $idCompetence1, PDO::PARAM_STR);
			$requete_prepare->bindParam(':idCompetence2', $idCompetence2, PDO::PARAM_STR);
			$bool = $requete_prepare->execute();
			$this->assertTrue($bool);
			
			//Execution
			$competencesReceived = $this->_daoAnimal->getCompetencesByAnimal($idAnimal);
				
			//Asserts
			$this->assertEqual(count($competencesReceived), 2);
	
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : '.$e->getTraceAsString().'<br/>';
		}
		
		//Suppression
		$this->deleteAnimal($idAnimal);
			
		$requete_prepare = $this->_dao->getConnexion()->prepare("DELETE FROM t_competence_animal WHERE Animal_idAnimal = :idAnimal");
		$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
		$bool = $requete_prepare->execute();
		$this->assertTrue($bool);
	}
	
	/**
	 * Test de la méthode : getCompetenceById().
	 */
	function test_getCompetenceById()
	{
		try
		{
			$idCompetence = 'C0001';
			//Execution
			$competenceReceived = $this->_daoAnimal->getCompetenceById($idCompetence);
	
			//Asserts
			$this->assertEqual($competenceReceived->getIdCompetence(), 'C0001');
			$this->assertEqual($competenceReceived->getNomCompetence(), 'Coup Assommant');
			$this->assertEqual($competenceReceived->getDegats(), 5);
			$this->assertEqual($competenceReceived->getType(), 'attaque');
			$this->assertEqual($competenceReceived->getCodePuissance(), 1);
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : '.$e->getTraceAsString().'<br/>';
		}
	}
	
	/**
	 * Test de la méthode : updateVie().
	 */
	function test_updateVie()
	{
		try
		{
			//Creation d'un animal de test
			$idAnimal = "ATEST001";
			$nomAnimal = "Lapin test";
			$proprietaire = "JTEST001";
			$vie = 20;
			$defense = 10;
			$attaque = 30;
			$niveau = 1;
			$this->addAnimal($idAnimal, $proprietaire, $nomAnimal, $vie, $defense, $attaque, $niveau);

			//Execution
			$boolReceived = $this->_daoAnimal->updateVie($idAnimal, 0);
			
			//Verification de la modification
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT vie FROM t_animal WHERE idAnimal = :idAnimal");
			$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
			$requete_prepare->execute();
			$donnees = $requete_prepare->fetch(PDO::FETCH_ASSOC);
			$vieReceived = $donnees['vie'];
			
			//Asserts
			$this->assertTrue($boolReceived);
			$this->assertEqual($vieReceived, 0);
			
			//Suppression
			$this->deleteAnimal($idAnimal);
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : '.$e->getTraceAsString().'<br/>';
		}
	}

	
	/**
	 * Test de la méthode : deleteRelationAnimalProprietaire().
	 */
	function test_deleteRelationAnimalProprietaire()
	{
		try
		{
			//Creation d'une relation animal-joueur
			$idAnimal = "ATEST001";
			$idJoueur = "JTEST001";
			$requete_prepare = $this->_dao->getConnexion()->prepare("INSERT INTO t_joueur_animal(Joueur_idFacebook, Animal_idAnimal) VALUES (:idJoueur, :idAnimal)");
			$requete_prepare->bindParam(':idJoueur', $idJoueur, PDO::PARAM_STR);
			$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
			$requete_prepare->execute();
	
			//Execution
			$boolReceived = $this->_daoAnimal->deleteRelationAnimalProprietaire($idAnimal);
				
			//Verification de la modification
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT COUNT(*) as COUNT FROM t_joueur_animal WHERE Joueur_idFacebook = :idJoueur AND Animal_idAnimal = :idAnimal");
			$requete_prepare->bindParam(':idJoueur', $idJoueur, PDO::PARAM_STR);
			$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
			$requete_prepare->execute();
			$donnees = $requete_prepare->fetch(PDO::FETCH_ASSOC);
			$countReceived = $donnees['COUNT'];
				
			//Asserts
			$this->assertTrue($boolReceived);
			$this->assertEqual($countReceived, 0);
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : '.$e->getTraceAsString().'<br/>';
		}
	}
	
	
}

?>