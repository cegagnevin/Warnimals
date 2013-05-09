<?php
require_once './../../lib/simpletest/autorun.php';
require_once './../modeles/JoueurModele.php';
require_once './../mapping/DAO.php';

/**
 *
 * Classe de test de la classe JoueurModele (modeles).
 *
 */
class JoueurModeleTestCase extends UnitTestCase 
{
	/** DAO */
	private $_dao;
	
	/** Modele joueur à tester */
	private $_mdlJoueur;
	
	/**
	 * Constructeur par défaut. Instancie le DAO et le JoueurModele à tester.
	 */
	public function __construct()
	{
		$this->_dao = new DAO();
		$this->_mdlJoueur = JoueurModele::getInstance();
	}
	
	/**
	 * Test du constructeur de JoueurModeleTestCase.
	 */
	function test_constructeur_JoueurModeleTestCase()
	{
		//Asserts
		$this->assertNotNull($this->_dao);
		$this->assertNotNull($this->_mdlJoueur);
		$this->assertTrue($this->_dao instanceof DAO);
		$this->assertTrue($this->_mdlJoueur instanceof JoueurModele);
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
	
	
	// -------------------------------- TESTS DE LA CLASSE JOUEURMODELE ---------------------------------------
	
	
	/**
	 * Test de la méthode debiterCredit() de JoueurModele.
	 */
	function test_debiterCredit()
	{
		try 
		{
			//Joueur de test
			$idAmi1 = "100000932826921";
			$idAmi2 = "100000932826922";
			$idAmi3 = "100000932826923";
			$idFacebook= "100000932826926";
			$nomJoueur = "Alexis";
			$credit = 1000;
			$dateInscription = time();
			$amis = array($idAmi1, $idAmi2, $idAmi3);	
			$joueur = new Joueur($idFacebook , $nomJoueur, $amis, $credit, $dateInscription);
			
			//Création du Joueur de test en base
			$this->addPlayer($idFacebook, $credit, $dateInscription);
		 	
			//Exécution de la méthode testée
		 	$this->_mdlJoueur->debiterCredit($idFacebook, 200);
		 	
		 	//select pour vérification
		 	$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT * FROM t_joueur WHERE idFacebook = :idFacebook");
		 	$requete_prepare->bindParam(':idFacebook', $idFacebook, PDO::PARAM_STR);
		 	$requete_prepare->execute();
		 	$donnees = $requete_prepare->fetch(PDO::FETCH_OBJ);
	
		 	$creditReceived = $donnees->credit;
		 	$creditExpected = 800;
		 	
		 	//Assert
		 	$this->assertEqual($creditReceived, $creditExpected);
	 	}
	 	catch(Exception $e)
	 	{
	 		echo 'Exception reçue : '.$e->getMessage().'<br/>';
	 		echo 'Trace : '.$e->getTraceAsString().'<br/>';
	 	}	 	 
	 	//Suppression de l'occurrence en base
	 	$this->deletePlayer($idFacebook);
	}
	
	
	/**
	 * Test de la méthode acheter() de JoueurModele.
	 */
	function test_achete()
	{
		try
		{
			//Joueur de test
			$idAmi1 = "100000932826921";
			$idAmi2 = "100000932826922";
			$idAmi3 = "100000932826923";
			$idFacebook= "100000932826926";
			$nomJoueur = "Alexis";
			$credit = 1000;
			$dateInscription = time();
			$amis = array($idAmi1, $idAmi2, $idAmi3);
			$joueur = new Joueur($idFacebook , $nomJoueur, $amis, $credit, $dateInscription);
			
			//Animal de test
			$idAnimal = "ATEST1";
			$nomAnimal ="Lapin";
			$vie = $def = $att = 30;	
			$niveau = 1;
			$animal = new Animal($idAnimal, '', $nomAnimal, $vie, $def, $att, $niveau);
		
			//Création des occurrences de test en base
			$this->addPlayer($idFacebook, $credit, $dateInscription);
			$this->addAnimal($idAnimal, '', $nomAnimal, $vie, $def, $att);
			
			//Exécution de la méthode à tester
			$this->_mdlJoueur->achete($idFacebook, $idAnimal, 100);
			 
			//Select pour vérification
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT * FROM t_joueur WHERE idFacebook = :idFacebook");
			$requete_prepare->bindParam(':idFacebook', $idFacebook, PDO::PARAM_STR);
			$requete_prepare->execute();
			$donnees = $requete_prepare->fetch(PDO::FETCH_OBJ);
			$creditReceived = $donnees->credit;
			$creditExpected = 900;
			
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT * FROM t_joueur_animal WHERE Animal_idAnimal = :idAnimal");
			$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
			$requete_prepare->execute();
			$donnees = $requete_prepare->fetch(PDO::FETCH_OBJ);
			$proprietaireReceived = $donnees->Joueur_idFacebook;
			$proprietaireExpected = $idFacebook;
			
			$this->assertEqual($creditReceived, $creditExpected);
			$this->assertEqual($proprietaireReceived, $proprietaireExpected);
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : '.$e->getTraceAsString().'<br/>';
		}	
		//Suppression des occurrences de test de la base
		$this->deleteAnimal($idAnimal);
		$this->deletePlayer($idFacebook);
	}
	
	/**
	 * Test de la méthode isPlayerExist() de JoueurModele.
	 */
	function test_isPlayerExist()
	{
		try
		{
			if(MODE_TEST) //Mode test
			{	
				//Exécution de la méthode à tester
				$arrayReceived = $this->_mdlJoueur->isPlayerExist();
				
				//Select du resultat à obtenir
				$userID = '15603901792'; //ID par defaut 
				$userID_hash = sha1($userID.SEL_HASH);
				$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT COUNT(*) as COUNT FROM t_joueur WHERE idFacebook = :idJoueur");
				$requete_prepare->bindParam(':idJoueur', $userID_hash, PDO::PARAM_STR);
				$requete_prepare->execute();
				$count = $requete_prepare->fetch(PDO::FETCH_OBJ)->COUNT;
				$bool = ($count == 0) ? false : true;
				
				//Asserts
				$this->assertEqual($bool, $arrayReceived[ 'isPlayerExist' ]);
				$this->assertEqual($userID, $arrayReceived[ 'idPlayer' ]);
			}
			else //Mode production
			{
				//Exécution de la méthode à tester
				$arrayReceived = $this->_mdlJoueur->isPlayerExist();
				
				//Asserts
				$this->assertTrue(in_array($arrayReceived[ 'isPlayerExist' ], array(true,false)));
				$this->assertNotNull($arrayReceived[ 'idPlayer' ]);
				$this->assertTrue(count($arrayReceived[ 'idPlayer' ]) > 0);
			}

		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : '.$e->getTraceAsString().'<br/>';
		}	 
	}
	
	/**
	 * Test de la méthode test_createJoueur() de JoueurModele.
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
		 	$idJoueurExpected = "100000932826935";
		 	$creditExpected = 1000;
		 	$dateExpected = time();
		 	$joueurExpected = new Joueur($idJoueurExpected, null, null, $creditExpected, $dateExpected);
		 	$boolReceived = $this->_mdlJoueur->createJoueur($idJoueurExpected, null, null, $creditExpected, $dateExpected);
		 	
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
	 * Test de la méthode getJoueur() de JoueurModele.
	 */
	function test_getJoueur()
	{
		try
		{
			//Création d'un joueur test
			$idFacebookExpected = "100000932826951";
			$creditExpected = 1000;
			$dateInscriptionExpected = time();
			$this->addPlayer($idFacebookExpected, $creditExpected, $dateInscriptionExpected);
			 
			//Execution de la méthode à tester
			$joueur = $this->_mdlJoueur->getJoueur($idFacebookExpected);
			 
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
	 * Test de la méthode getAnimalByJoueur() de JoueurModele.
	 */
	function test_getAnimalByJoueur()
	{
		try
		{
			//Création d'un animal test + joueur test
			$idJoueur = "JTEST001";
			$this->addPlayer("JTEST001", 1000, time());
			$this->addAnimal("ATEST001", $idJoueur, "Herisson test", 10, 30, 20, 'R0003');
	
			//Execution de la méthode à tester
			$animalReceived = $this->_mdlJoueur->getAnimalByJoueur($idJoueur);
	
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
			$this->deleteAnimal("ATEST001");
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : '.$e->getTraceAsString().'<br/>';
		}
	}
	
	/**
	 * Test de la méthode getInfosFacebook() de JoueurModele.
	 */
	function test_getInfosFacebook()
	{
		try
		{
			if(!MODE_TEST)
			{
				//Exécution de la méthode à tester
				$arrayReceived = $this->_mdlJoueur->getInfosFacebook();
				
				//Asserts
				$this->assertNotNull($arrayReceived[ 'id' ]);
				$this->assertNotNull($arrayReceived[ 'nom' ]);
				$this->assertNotNull($arrayReceived[ 'amis' ]);
			}
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : '.$e->getTraceAsString().'<br/>';
		}
	}
	
	/**
	 * Test de la méthode initialisation() de JoueurModele.
	 */
	function test_initialisation()
	{
		try
		{
			if(MODE_TEST)//Test
			{
				//------------ Avec un joueur qui n'existe pas--------------------
				$idJoueur = "1560390179333322";
				$idJoueurHash = sha1($idJoueur.SEL_HASH);//On hash l'identifiant du joueur
				$amis = array('100000932826926');
				$nom = 'Cédric Gagnevin';
				
				//Verification en base de l'existence du joueur
				$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT COUNT(*) as COUNT FROM t_joueur WHERE idFacebook = :idFb");
				$requete_prepare->bindParam(':idFb', $idJoueurHash, PDO::PARAM_STR);
				$requete_prepare->execute();
				$count = $requete_prepare->fetch(PDO::FETCH_OBJ)->COUNT;
				
				//Exécution de la méthode à tester
				$this->_mdlJoueur->initialisation($idJoueur, false);
				
				//Asserts
				@session_start();
				//On recupere le joueur en session
				$joueurReceived = $_SESSION [ 'joueur' ];
				
				$this->assertEqual($joueurReceived->getIdFacebook(), $idJoueurHash);
				$this->assertEqual($joueurReceived->getAmis(), $amis);
				$this->assertEqual($joueurReceived->getNomJoueur(), $nom);
				
				//Suppressions
				$this->deletePlayer($idJoueurHash);
			}
			else //Production
			{
				//------------ Avec un joueur qui n'existe pas--------------------
				//Verification en base de l'existence du joueur
				$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT COUNT(*) as COUNT FROM t_joueur");
				$requete_prepare->execute();
				$count = $requete_prepare->fetch(PDO::FETCH_OBJ)->COUNT;
				
				//Exécution de la méthode à tester
				$this->_mdlJoueur->initialisation($idJoueur, false);
				
				//Verification si un joueur a été ajouté
				$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT COUNT(*) as COUNT FROM t_joueur");
				$requete_prepare->execute();
				$count2 = $requete_prepare->fetch(PDO::FETCH_OBJ)->COUNT;
				
				//Asserts
				@session_start();
				//On recupere le joueur en session
				$joueurReceived = $_SESSION [ 'joueur' ];
				
				$this->assertNotNull($joueurReceived->getIdFacebook());
				$this->assertNotNull($joueurReceived->getAmis());
				$this->assertNotNull($joueurReceived->getNomJoueur());
				
				if($count =! $count2)//Nouveau joueur
				{
					$idJ = $joueurReceived->getIdFacebook();
					$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT COUNT(*) as COUNT FROM t_joueur WHERE idFacebook = :idFb");
					$requete_prepare->bindParam(':idFb', $idJ, PDO::PARAM_STR);
					$requete_prepare->execute();
					$count2 = $requete_prepare->fetch(PDO::FETCH_OBJ)->COUNT;
					$this->assertEqual($count2, 1);
				}

				//------------ Avec un joueur qui existe --------------------
				//Verification en base de l'existence du joueur
				$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT COUNT(*) as COUNT FROM t_joueur");
				$requete_prepare->execute();
				$count = $requete_prepare->fetch(PDO::FETCH_OBJ)->COUNT;
				
				//Exécution de la méthode à tester
				$this->_mdlJoueur->initialisation($idJoueur, false);
				
				//Verification si un joueur a été ajouté
				$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT COUNT(*) as COUNT FROM t_joueur");
				$requete_prepare->execute();
				$count2 = $requete_prepare->fetch(PDO::FETCH_OBJ)->COUNT;
				$this->assertEqual($count, $count2);
				
				//Asserts
				@session_start();
				//On recupere le joueur en session
				$joueurReceived = $_SESSION [ 'joueur' ];
				
				$this->assertNotNull($joueurReceived->getIdFacebook());
				$this->assertNotNull($joueurReceived->getAmis());
				$this->assertNotNull($joueurReceived->getNomJoueur());
			}
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : '.$e->getTraceAsString().'<br/>';
		}
	}
	
	/**
	 * Test de la méthode augmenterCredit() de JoueurModele.
	 */
	function test_augmenterCredit()
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
			$this->_mdlJoueur->augmenterCredit($idJoueur, 150);
	
			//Select pour vérification
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT * FROM t_joueur WHERE idFacebook = :idFacebook");
			$requete_prepare->bindParam(':idFacebook', $idJoueur, PDO::PARAM_STR);
			$requete_prepare->execute();
			$donnees = $requete_prepare->fetch(PDO::FETCH_OBJ);
				
			//Assert
			$creditReceived = $donnees->credit;
			$creditExpected = 300;
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
			$joueurReceived = $this->_mdlJoueur->getJoueurByAnimal($idA);
	
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
	 * Test de la méthode addVictoire() de JoueurModele.
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
			$boolReceived = $this->_mdlJoueur->addVictoire($idA);
				
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
	 * Test de la méthode addDefaite() de JoueurModele.
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
			$boolReceived = $this->_mdlJoueur->addDefaite($idA);
	
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
	 * Test de la méthode addAbandon() de JoueurModele.
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
			$boolReceived = $this->_mdlJoueur->addAbandon($idA);
	
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