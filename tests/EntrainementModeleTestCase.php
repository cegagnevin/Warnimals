<?php
require_once('./../../lib/simpletest/autorun.php');
require_once '../../lib/smarty/Smarty.class.php';
require_once './../metiers/Animal.php';
require_once './../metiers/Joueur.php';
require_once './../modeles/EntrainementModele.php';
require_once './../mapping/DAO.php';


/**
 *
 * Classe de test de la classe EntrainementModele (modeles).
 *
 */
class EntrainementModeleTestCase extends UnitTestCase
{
	/** DAO */
	private $_dao;
	
	/** Modele entrainement à tester */
	private $_mdlEntrainement;
	
	/**
	 * Constructeur par défaut. Instancie le DAO et l'EntrainementModele à tester.
	 */
	public function __construct()
	{
		$this->_dao = new DAO();
		$this->_mdlEntrainement = EntrainementModele::getInstance();
	}
	
	/**
	 * Test du constructeur de EntrainementModeleTestCase.
	 */
	function test_constructeur_EntrainementModeleTestCase()
	{
		//Asserts
		$this->assertNotNull($this->_dao);
		$this->assertNotNull($this->_mdlEntrainement);
		$this->assertTrue($this->_dao instanceof DAO);
		$this->assertTrue($this->_mdlEntrainement instanceof EntrainementModele);
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
	private function addAnimal($idAnimal, $proprietaire, $nomAnimal, $vie, $defense, $attaque, $niveau = 0)
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
	
	
	/**
	 *  Permet d'ajouter un entrainement de test dans la base de données et d'être sur que l'ajout ait eu lieu.
	 * @param String $idEntrainement
	 * @param String $joueur
	 * @param int $duree
	 * @param int $prix
	 * @param timestamp $date
	 * @param String $idOffre
	 */
	private function addTraining($idEntrainement, $duree, $prix, $date, $type, $idOffre, $niveauMax=0, $nbParticipantsMin=0, $nbParticipantsMax=0, $annule=0)
	{
		try
		{
			//Insertion
			$requete_prepare = $this->_dao->getConnexion()->prepare("INSERT INTO t_entrainement (idEntrainement, duree, prix, dateDebut, type, niveauMax, nbParticipantsMin, nbParticipantsMax, annule, OffreEntrainement_idOffre) VALUES (:id, :duree, :prix, :date, :type, :niveauMax, :nbParticipantsMin, :nbParticipantsMax, :annule, :offre)");
			$requete_prepare->bindParam(':id', $idEntrainement, PDO::PARAM_STR);
			$requete_prepare->bindParam(':duree', $duree, PDO::PARAM_INT);
			$requete_prepare->bindParam(':prix', $prix, PDO::PARAM_INT);
			$requete_prepare->bindParam(':date', $date, PDO::PARAM_INT);
			$requete_prepare->bindParam(':type', $type, PDO::PARAM_INT);
			$requete_prepare->bindParam(':niveauMax', $niveauMax, PDO::PARAM_INT);
			$requete_prepare->bindParam(':nbParticipantsMin', $nbParticipantsMin, PDO::PARAM_INT);
			$requete_prepare->bindParam(':nbParticipantsMax', $nbParticipantsMax, PDO::PARAM_INT);
			$requete_prepare->bindParam(':annule', $annule, PDO::PARAM_INT);
			$requete_prepare->bindParam(':offre', $idOffre, PDO::PARAM_STR);
			$bool = $requete_prepare->execute();
	
			//Select pour vérification
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT * FROM t_entrainement WHERE idEntrainement = :idEntrainement");
			$requete_prepare->bindParam(':idEntrainement', $idEntrainement, PDO::PARAM_STR);
			$requete_prepare->execute();
			$donnees = $requete_prepare->fetch(PDO::FETCH_OBJ);
			$dureeReceived = $donnees->duree;
			$prixReceived = $donnees->prix;
			$typeReceived = $donnees->type;
			$niveauMaxReceived = $donnees->niveauMax;
			$nbParticipantsMinReceived = $donnees->nbParticipantsMin;
			$nbParticipantsMaxReceived = $donnees->nbParticipantsMax;
			$annuleReceived = $donnees->annule;
			$dateSouscriptionReceived = $donnees->dateDebut;
			$offreReceived = $donnees->OffreEntrainement_idOffre;
			
			//Asserts
			$this->assertTrue($bool);
			$this->assertEqual($duree, $dureeReceived);
			$this->assertEqual($prix, $prixReceived);
			$this->assertEqual($type, $typeReceived);
			$this->assertEqual($niveauMax, $niveauMaxReceived);
			$this->assertEqual($nbParticipantsMin, $nbParticipantsMinReceived);
			$this->assertEqual($nbParticipantsMax, $nbParticipantsMaxReceived);
			$this->assertEqual($annule, $annuleReceived);
			$this->assertEqual($date, $dateSouscriptionReceived);
			$this->assertEqual($idOffre, $offreReceived);
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : '.$e->getTraceAsString().'<br/>';
		}
	}
	
	/**
	 * Permet de supprimer un entrainement de test de la base de données et d'être sur que la suppression ait eu lieu.
	 * @param String $idTrain
	 */
	private function deleteTraining($idTrain)
	{
		try
		{
			//Suppression
			$requete_prepare = $this->_dao->getConnexion()->prepare("DELETE FROM t_entrainement WHERE idEntrainement = :idEntrainement");
			$requete_prepare->bindParam(':idEntrainement', $idTrain, PDO::PARAM_STR);
			$bool = $requete_prepare->execute();
	
			//Select pour vérification
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT COUNT(*) as COUNT FROM t_entrainement WHERE idEntrainement = :idEntrainement");
			$requete_prepare->bindParam(':idEntrainement', $idTrain, PDO::PARAM_STR);
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
	
	
	// -------------------------------- TESTS DE LA CLASSE ENTRAINEMENTMODELE ---------------------------------------
	
	/**
	 * Test du constructeur du Modele Entrainement.
	 */
	function test_constructeur()
	{
		//Création d'un modele Entrainement
		$mdl = EntrainementModele::getInstance();
		$daoEntrainement = $mdl->getDAOEntrainement();
		$daoAnimal = $mdl->getDAOAnimal();
		
		//Asserts
		$this->assertNotNull($mdl);
		$this->assertNotNull($daoEntrainement);
		$this->assertNotNull($daoAnimal);
		$this->assertTrue($daoEntrainement instanceof EntrainementDAO);
		$this->assertTrue($daoAnimal instanceof AnimalDAO);
	}

	
	/** 
	 * Test de la méthode gererEntrainementsCollectifs() de EntrainementModele
	 */
	 function test_getEntrainementIndividuelByNiveau()
	 {
	 	try
	 	{
	 		//On remplit le fichier
	 		$this->_mdlEntrainement->gererOffresEntrainementsIndividuels();
	 		
	 		//Exécution de la méthode
	 		$niveauAnimal = 15;
	 		$entrainementReceived = $this->_mdlEntrainement->getEntrainementIndividuelByNiveau($niveauAnimal);		
	 		$offreReceived = $entrainementReceived->getOffre();
	 		
	 		//Récupération de l'offre correspondante
	 		$path = WARNIMALS_PATH.'/files/offres_entrainements_individuels.txt';
	 		$file = fopen($path, 'r');
	 		if($file != null)
	 		{
				$dateOffre = trim(fgets($file));
				$tab_offres = unserialize(fgets($file));
				fclose($file);
	 		}
	 		$offreExpected = $tab_offres[ 'offre2' ];
	 	
	 		//Calcul des valeurs attendues
	 		$prixExpected = ceil(COEF_MULTI_PRIX_ENTRAINEMENT_IND * ((20 * $niveauAnimal) + ($niveauAnimal * $offreExpected->getSommePoints()) + COUT_SUPP_ENTRAINEMENT));
	 		$dureeExpected = COEF_MULTI_DUREE_ENTRAINEMENT_IND * $prixExpected;
	 		
	 	
	 		//Asserts
	 		$this->assertNotNull($entrainementReceived);
	 		$this->assertEqual($entrainementReceived->getPrix(), $prixExpected);
	 		$this->assertEqual($entrainementReceived->getDuree(), $dureeExpected);
	 		$this->assertTrue($offreReceived->getSommePoints() > 0 && $offreReceived->getSommePoints() <= 4);
	 		$this->assertTrue($offreReceived->getAttaqueOffre() >= 0 && $offreReceived->getAttaqueOffre() <= 4);
	 		$this->assertTrue($offreReceived->getDefenseOffre() >= 0 && $offreReceived->getDefenseOffre() <= 4);
	 		$this->assertTrue($offreReceived->getVieOffre() >= 0 && $offreReceived->getVieOffre() <= 4);
	 		$this->assertTrue(in_array($offreReceived->getLevelUp(), array(0,1)));	
	 	
	 	}
	 	catch(Exception $e)
	 	{
	 		echo 'Exception reçue : '.$e->getMessage().'<br/>';
	 		echo 'Trace : <pre>'.$e->getTraceAsString().'</pre><br/>';
	 	}
	 }
	 
	 
	 /**
	  * Test de la méthode assignEntrainementIndividuel() de EntrainementModele
	  */
	 function test_assignEntrainementIndividuel()
	 { 
	 	$smarty = new Smarty();
	 	$currentTime = time();
	 	$offre = new OffreEntrainement("O1", 3, 1, 1, 0);
	 	$entrainementExpected = new EntrainementIndividuel("ETEST003", 500, 1000, $currentTime, $offre, null);
	 	//Exécution de la méthode à tester
	 	$smarty = $this->_mdlEntrainement->assignEntrainementIndividuel($entrainementExpected, $smarty);
	 	
	 	//Récupération de la liste assignée de l'entrainement assigné
	 	$entrainementIndividuel = $smarty->getTemplateVars("entrainementIndividuel");
	 	
	 	//Asserts
	 	$this->assertNotNull($smarty);
	 	$this->assertNotNull($entrainementExpected);
	 	$this->assertNotNull($entrainementIndividuel);
	 	
	 	$this->assertEqual($entrainementIndividuel[ 'idEntrainementInd' ], $entrainementExpected->getIdEntrainement());
	 	$this->assertEqual($entrainementIndividuel[ 'dureeEntrainementInd' ], date("G\hi",$entrainementExpected->getDuree()));
	 	$this->assertEqual($entrainementIndividuel[ 'prixEntrainementInd' ], $entrainementExpected->getPrix());
	 	$this->assertEqual($entrainementIndividuel[ 'idOffreInd' ], $entrainementExpected->getOffre()->getIdOffre());
	 	$this->assertEqual($entrainementIndividuel[ 'vieOffreInd' ], $entrainementExpected->getOffre()->getVieOffre());
	 	$this->assertEqual($entrainementIndividuel[ 'attaqueOffreInd' ], $entrainementExpected->getOffre()->getAttaqueOffre());
	 	$this->assertEqual($entrainementIndividuel[ 'defenseOffreInd' ], $entrainementExpected->getOffre()->getDefenseOffre());
	 	$this->assertEqual($entrainementIndividuel[ 'levelUpOffreInd' ], $entrainementExpected->getOffre()->getLevelUp());
	 	 
	 	$smarty->__destruct();
	 }
	 
	 
	 /**
	  * Test de la méthode getOffreAleatoireByNiveau() de EntrainementModele
	  */
	 function test_getOffreAleatoireByNiveau()
	 {
	 	try
	 	{
	 		//Exécution de la méthode avec un niveau compris entre 0 et 10
	 		$offreReceived = $this->_mdlEntrainement->getOffreAleatoireByNiveau(1);
	 
	 		//Asserts
	 		$this->assertNotNull($offreReceived);
	 		$this->assertNotNull($offreReceived->getIdOffre());
	 		$this->assertTrue($offreReceived->getAttaqueOffre() >= 0 && $offreReceived->getAttaqueOffre() <= 6);
	 		$this->assertTrue($offreReceived->getDefenseOffre() >= 0 && $offreReceived->getDefenseOffre() <= 6);
	 		$this->assertTrue($offreReceived->getVieOffre() >= 0 && $offreReceived->getVieOffre() <= 6);
	 		$this->assertTrue(in_array($offreReceived->getLevelUp(), array(0,1)));
	 			
	 		$sommePoints = $offreReceived->getSommePoints();
	 		$this->assertTrue($sommePoints > 0 && $sommePoints <= 6);
	 
	 			
	 		//Exécution de la méthode avec un niveau compris entre 11 et 20
	 		$offreReceived = $this->_mdlEntrainement->getOffreAleatoireByNiveau(15);
	 			
	 		//Asserts
	 		$this->assertNotNull($offreReceived);
	 		$this->assertNotNull($offreReceived->getIdOffre());
	 		$this->assertTrue($offreReceived->getAttaqueOffre() >= 0 && $offreReceived->getAttaqueOffre() <= 4);
	 		$this->assertTrue($offreReceived->getDefenseOffre() >= 0 && $offreReceived->getDefenseOffre() <= 4);
	 		$this->assertTrue($offreReceived->getVieOffre() >= 0 && $offreReceived->getVieOffre() <= 4);
	 		$this->assertTrue(in_array($offreReceived->getLevelUp(), array(0,1)));
	 
	 		$sommePoints = $offreReceived->getSommePoints();
	 		$this->assertTrue($sommePoints > 0 && $sommePoints <= 4);
	 
	 			
	 		//Exécution de la méthode avec un niveau compris entre 21 et 30
	 		$offreReceived = $this->_mdlEntrainement->getOffreAleatoireByNiveau(23);
	 
	 		//Asserts
	 		$this->assertNotNull($offreReceived);
	 		$this->assertNotNull($offreReceived->getIdOffre());
	 		$this->assertTrue($offreReceived->getAttaqueOffre() >= 0 && $offreReceived->getAttaqueOffre() <= 2);
	 		$this->assertTrue($offreReceived->getDefenseOffre() >= 0 && $offreReceived->getDefenseOffre() <= 2);
	 		$this->assertTrue($offreReceived->getVieOffre() >= 0 && $offreReceived->getVieOffre() <= 2);
	 		$this->assertTrue(in_array($offreReceived->getLevelUp(), array(0,1)));
	 			
	 		$sommePoints = $offreReceived->getSommePoints();
	 		$this->assertTrue($sommePoints > 0 && $sommePoints <= 2);
	 	}
	 	catch(Exception $e)
	 	{
	 		echo 'Exception reçue : '.$e->getMessage().'<br/>';
	 		echo 'Trace : <pre>'.$e->getTraceAsString().'</pre><br/>';
	 	}
	 }
	 
	 
	 /** 
	  * Test de la méthode souscrireEntrainementIndividuel() de EntrainementModele
	  */
	 function test_souscrireEntrainementIndividuel()
	 {
	 	try
	 	{
		 	//Création des occurrences de test
		 	$idJoueur = "JTEST010";
		 	$this->addPlayer($idJoueur, 3000, time());
		 	@session_start();
		 	$_SESSION[ 'joueur' ] = new Joueur($idJoueur, "Joueur test", array(), 3000, time());
		 	
		 	$idAnimal = "ATEST010";
		 	$this->addAnimal($idAnimal, $idJoueur, "Lapin de test", 10, 30, 20);
		 	
		 	//Execution de la méthode à tester
		 	$entrainementExpected = $this->_mdlEntrainement->souscrireEntrainementIndividuel();
		 	
		 	//Select pour verifications
		 	$idEntrainementExpected = $entrainementExpected->getIdEntrainement();
		 	$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT * FROM t_entrainement WHERE idEntrainement = :idEntrainement");
		 	$requete_prepare->bindParam(':idEntrainement', $idEntrainementExpected, PDO::PARAM_STR);
		 	$bool = $requete_prepare->execute();
		 	$donnees = $requete_prepare->fetch(PDO::FETCH_OBJ);
		 	$idEntrainementReceived = $donnees->idEntrainement;
		 	$dureeReceived = $donnees->duree;
		 	$prixReceived = $donnees->prix;
		 	$dateDebutReceived = $donnees->dateDebut;
		 	$typeReceived = $donnees->type;
		 	$idOffreReceived = $donnees->OffreEntrainement_idOffre;
		 	
		 	$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT credit FROM t_joueur WHERE idFacebook = :idFacebook");
		 	$requete_prepare->bindParam(':idFacebook', $idJoueur, PDO::PARAM_STR);
		 	$bool = $requete_prepare->execute();
		 	$donnees = $requete_prepare->fetch(PDO::FETCH_OBJ);
		 	$creditReceived = $donnees->credit;
	
		 	$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT COUNT(*) as COUNT FROM t_entrainement_animal WHERE Entrainement_idEntrainement = :idEntrainement AND Animal_idAnimal = :idAnimal");
		 	$requete_prepare->bindParam(':idEntrainement', $idEntrainementExpected, PDO::PARAM_STR);
		 	$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
		 	$bool = $requete_prepare->execute();
		 	$count = $requete_prepare->fetch(PDO::FETCH_OBJ)->COUNT;
		 	
		 	
		 	//Asserts
		 	$this->assertEqual($entrainementExpected->getIdEntrainement(), $idEntrainementReceived);
		 	$this->assertEqual($entrainementExpected->getDuree(), $dureeReceived);
		 	$this->assertEqual($entrainementExpected->getPrix(), $prixReceived);
		 	$this->assertEqual($entrainementExpected->getDateDebut(), $dateDebutReceived);
		 	$this->assertEqual($entrainementExpected->getType(), $typeReceived);
		 	$this->assertEqual($entrainementExpected->getOffre()->getIdOffre(), $idOffreReceived);
		 	
		 	$this->assertEqual($creditReceived, 3000 - $entrainementExpected->getPrix());
		 	
		 	$this->assertEqual($count, 1);
		 	
		 	//Suppressions
		 	$this->deleteAnimal($idAnimal);
			$this->deleteTraining($entrainementExpected->getIdEntrainement());
			$this->deletePlayer($idJoueur);
			
			$requete_prepare = $this->_dao->getConnexion()->prepare("DELETE FROM t_entrainement_animal WHERE Entrainement_idEntrainement = :idEntrainement AND Animal_idAnimal = :idAnimal");
		 	$requete_prepare->bindParam(':idEntrainement', $idEntrainementExpected, PDO::PARAM_STR);
		 	$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
		 	$bool = $requete_prepare->execute();
		 	$this->assertTrue($bool);
	 	}
	 	catch(Exception $e)
	 	{
	 		echo 'Exception reçue : '.$e->getMessage().'<br/>';
	 		echo 'Trace : <pre>'.$e->getTraceAsString().'</pre><br/>';
	 	}
		
	 }
	 

	 /**
	  * Test de la methode gererOffresEntrainementsIndividuels() de Cron.
	  */
	 function test_gererOffresEntrainementsIndividuels()
	 {
	 	try
	 	{
		 	$path = WARNIMALS_PATH.'/files/offres_entrainements_individuels.txt';
		 
		 	//Lecture du contenu du fichier des offres d'entrainements individuels
		 	$file = fopen($path, 'r+');
		 	$this->assertNotNull($file);
		 	if($file != null)
		 	{
		 		//On lit la date à laquelle le fichier a été édité pour la dernière fois
		 		$dateOffres = trim(fgets($file));
		 		$tab_offres = unserialize(fgets($file));
		 		fclose($file); //Fermeture du fichier
		 	}
		 
		 
		 	//On vide le fichier pour forcer un changement des offres lors de l'exécution de la méthode
		 	file_put_contents($path,'');
		 
		 
		 	//On execute la méthode à tester
		 	EntrainementModele::getInstance()->gererOffresEntrainementsIndividuels();
		 
		 	//On lit ce qui a été inscrit dans le fichier
		 	$file = fopen($path, 'r+');
		 	$this->assertNotNull($file);
		 	if($file != null)
		 	{
		 		//On lit la date à laquelle le fichier a été édité pour la dernière fois
		 		$dateOffresReceived = trim(fgets($file));
		 		$tab_offresReceived = unserialize(fgets($file));
		 		fclose($file); //Fermeture du fichier
		 	}
		 
		 	//Asserts
		 	$this->assertNotNull($tab_offres);
		 	$this->assertNotNull($dateOffres);
		 	$this->assertNotNull($dateOffresReceived);
		 	$this->assertNotNull($tab_offresReceived);
		 	$this->assertEqual($dateOffresReceived, date("d/m/Y"));
		 	$this->assertNotEqual($tab_offres, $tab_offresReceived);
		 	$this->assertTrue($tab_offresReceived[ 'offre1' ]->getSommePoints() > 0);
		 	$this->assertTrue($tab_offresReceived[ 'offre1' ]->getSommePoints() <= 6);
		 	$this->assertTrue($tab_offresReceived[ 'offre2' ]->getSommePoints() > 0);
		 	$this->assertTrue($tab_offresReceived[ 'offre2' ]->getSommePoints() <= 4);
		 	$this->assertTrue($tab_offresReceived[ 'offre3' ]->getSommePoints() > 0);
		 	$this->assertTrue($tab_offresReceived[ 'offre3' ]->getSommePoints() <= 2);
		 
		 	//On restaure le fichier d'origine
		 	$file = fopen($path, 'r+');
		 	$this->assertNotNull($file);
		 	if($file != null)
		 	{
		 		fwrite($file, $dateOffres."\n");
		 		fwrite($file, serialize($tab_offres));
		 		fclose($file);
		 	}
		 
		 	$this->assertTrue(filesize($path) > 0);
	 	}
	 	catch(Exception $e)
	 	{
	 		echo 'Exception reçue : '.$e->getMessage().'<br/>';
	 		echo 'Trace : <pre>'.$e->getTraceAsString().'</pre><br/>';
	 	}
	 }
	 
	 /**
	  * Test de la methode entrainerAnimaux() de EntrainementModele.
	  */
	 function test_entrainerAnimaux()
	 {
	 	try
	 	{
			//--------------- Création des occurrences pour le test --------------- 
			
	 		//Entrainement de test
			$idEntrainement = 'ETEST001';
			$duree = 5;
			$prix = 1000;
			$dateDebut = time() - 1000;
			$type = 'collectif';
			$idOffre = 'O3';
			$this->addTraining($idEntrainement, $duree, $prix, $dateDebut, $type, $idOffre);
			
			//Animaux de test
			$idAnimal1 = 'ATEST001';
			$nomAnimal1 = 'Lapin de test';
			$vie1 = 3;
			$defense1 = 15;
			$attaque1 = 35;
			$niveau1 = 2;
			$race1 = 'R0002';	
			$idAnimal2 = 'ATEST002';
			$nomAnimal2 = 'T-Rex de test';
			$vie2 = 20;
			$defense2 = 10;
			$attaque2 = 20;
			$niveau2 = 2;
			$race2 = 'R0001';
			$requete_prepare = $this->_dao->getConnexion()->prepare("INSERT INTO t_animal (idAnimal, nomAnimal, vie, defense, attaque, niveau, RaceAnimal_race) VALUES (:id1, :nom1, :vie1, :def1, :att1, :niveau1, :race1), (:id2, :nom2, :vie2, :def2, :att2, :niveau2, :race2)");
			$requete_prepare->bindParam(':id1', $idAnimal1, PDO::PARAM_STR);
			$requete_prepare->bindParam(':nom1', $nomAnimal1, PDO::PARAM_STR);
			$requete_prepare->bindParam(':vie1', $vie1, PDO::PARAM_INT);
			$requete_prepare->bindParam(':def1', $defense1, PDO::PARAM_INT);
			$requete_prepare->bindParam(':att1', $attaque1, PDO::PARAM_INT);
			$requete_prepare->bindParam(':niveau1', $niveau1, PDO::PARAM_INT);
			$requete_prepare->bindParam(':race1', $race1, PDO::PARAM_STR);
			$requete_prepare->bindParam(':id2', $idAnimal2, PDO::PARAM_STR);
			$requete_prepare->bindParam(':nom2', $nomAnimal2, PDO::PARAM_STR);
			$requete_prepare->bindParam(':vie2', $vie2, PDO::PARAM_INT);
			$requete_prepare->bindParam(':def2', $defense2, PDO::PARAM_INT);
			$requete_prepare->bindParam(':att2', $attaque2, PDO::PARAM_INT);
			$requete_prepare->bindParam(':niveau2', $niveau2, PDO::PARAM_INT);
			$requete_prepare->bindParam(':race2', $race2, PDO::PARAM_STR);
			$boolInsert = $requete_prepare->execute();
			$this->assertTrue($boolInsert);
			
			//Inscription des animaux à l'entrainement
			$requete_prepare = $this->_dao->getConnexion()->prepare("INSERT INTO t_entrainement_animal (Entrainement_idEntrainement, Animal_idAnimal, dateSouscription, valide) VALUES (:idEntrainement, :idAnimal1, :dateSouscription, 0), (:idEntrainement, :idAnimal2, :dateSouscription, 0)");
			$requete_prepare->bindParam(':idEntrainement', $idEntrainement, PDO::PARAM_STR);
			$requete_prepare->bindParam(':idAnimal1', $idAnimal1, PDO::PARAM_STR);
			$requete_prepare->bindParam(':idAnimal2', $idAnimal2, PDO::PARAM_STR);
			$requete_prepare->bindParam(':dateSouscription', $dateDebut, PDO::PARAM_INT);
			$boolInsert = $requete_prepare->execute();
			$this->assertTrue($boolInsert);
			
			//Exécution de la méthode à tester
			$this->_mdlEntrainement->entrainerAnimaux();
			
			//Vérifications de l'amélioration des animaux
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT * FROM t_animal  WHERE idAnimal = :idAnimal1");
			$requete_prepare->bindParam(':idAnimal1', $idAnimal1, PDO::PARAM_STR);
			$requete_prepare->execute();
			$donnees = $requete_prepare->fetch(PDO::FETCH_ASSOC);
			$vie1Received = $donnees[ 'vie' ];
			$defense1Received = $donnees[ 'defense' ];
			$attaque1Received = $donnees[ 'attaque' ];
			$niveau1Received = $donnees[ 'niveau' ];
			
			//NB -- Offre 3 : attaque=+1 ; defense=+1 ; vie=+1 ; levelUp=+1
			$this->assertEqual($attaque1Received, $attaque1 + 1);
			$this->assertEqual($defense1Received, $defense1 + 1);
			$this->assertEqual($vie1Received, $vie1 + 1);
			$this->assertEqual($niveau1Received, $niveau1 + 1);
			
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT * FROM t_animal  WHERE idAnimal = :idAnimal2");
			$requete_prepare->bindParam(':idAnimal2', $idAnimal2, PDO::PARAM_STR);
			$requete_prepare->execute();
			$donnees = $requete_prepare->fetch(PDO::FETCH_ASSOC);
			$vie2Received = $donnees[ 'vie' ];
			$defense2Received = $donnees[ 'defense' ];
			$attaque2Received = $donnees[ 'attaque' ];
			$niveau2Received = $donnees[ 'niveau' ];
				
			$this->assertEqual($attaque2Received, $attaque2 + 1);
			$this->assertEqual($defense2Received, $defense2 + 1);
			$this->assertEqual($vie2Received, $vie2 + 1);
			$this->assertEqual($niveau2Received, $niveau2 + 1);
			
			//Vérification de l'apprentissage des compétences
			$idCompetence = 'C0005';					
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT COUNT(*) as COUNT FROM t_competence_animal  WHERE Animal_idAnimal = :idAnimal1 AND Competence_idCompetence = :idCompetence");
			$requete_prepare->bindParam(':idAnimal1', $idAnimal1, PDO::PARAM_STR);
			$requete_prepare->bindParam(':idCompetence', $idCompetence, PDO::PARAM_STR);
			$requete_prepare->execute();
			$count1 = $requete_prepare->fetch(PDO::FETCH_OBJ)->COUNT;
			$this->assertEqual($count1, 1);
			
			$idCompetence = 'C0005';
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT COUNT(*) as COUNT FROM t_competence_animal  WHERE Animal_idAnimal = :idAnimal2 AND Competence_idCompetence = :idCompetence");
			$requete_prepare->bindParam(':idAnimal2', $idAnimal2, PDO::PARAM_STR);
			$requete_prepare->bindParam(':idCompetence', $idCompetence, PDO::PARAM_STR);
			$requete_prepare->execute();
			$count1 = $requete_prepare->fetch(PDO::FETCH_OBJ)->COUNT;
			$this->assertEqual($count1, 1);
			
			//Vérification de la validation de l'entrainement
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT valide FROM t_entrainement_animal  WHERE Entrainement_idEntrainement = :idEntrainement");
			$requete_prepare->bindParam(':idEntrainement', $idEntrainement, PDO::PARAM_STR);
			$requete_prepare->execute();
			
			while($donnees = $requete_prepare->fetch(PDO::FETCH_ASSOC))
			{
				$this->assertEqual($donnees[ 'valide' ], 1);
			}
			
			//Vérification que l'entrainement et ses dépendances ont bien été supprimés
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT COUNT(*) as COUNT FROM t_entrainement_animal  WHERE Entrainement_idEntrainement = :idEntrainement");
			$requete_prepare->bindParam(':idEntrainement', $idEntrainement, PDO::PARAM_STR);
			$requete_prepare->execute();
			$count1 = $requete_prepare->fetch(PDO::FETCH_OBJ)->COUNT;
			$this->assertEqual($count1, 0);
			
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT COUNT(*) as COUNT FROM t_entrainement  WHERE idEntrainement = :idEntrainement");
			$requete_prepare->bindParam(':idEntrainement', $idEntrainement, PDO::PARAM_STR);
			$requete_prepare->execute();
			$count1 = $requete_prepare->fetch(PDO::FETCH_OBJ)->COUNT;
			$this->assertEqual($count1, 0);
			
	
 		}
 		catch(Exception $e)
 		{
 			echo 'Exception reçue : '.$e->getMessage().'<br/>';
 			echo 'Trace : <pre>'.$e->getTraceAsString().'</pre><br/>';
 		}
 		
 		//Suppressions
 		$this->deleteAnimal($idAnimal1);
 		$this->deleteAnimal($idAnimal2);
 		$this->deleteTraining($idEntrainement);
 			
 		$requete_prepare = $this->_dao->getConnexion()->prepare("DELETE FROM t_competence_animal WHERE Animal_idAnimal IN (:idAnimal1, :idAnimal2)");
 		$requete_prepare->bindParam(':idAnimal1', $idAnimal1, PDO::PARAM_STR);
 		$requete_prepare->bindParam(':idAnimal2', $idAnimal2, PDO::PARAM_STR);
 		$bool = $requete_prepare->execute();
 		$this->assertTrue($bool);
 		
 		$requete_prepare = $this->_dao->getConnexion()->prepare("DELETE FROM t_entrainement_animal WHERE Entrainement_idEntrainement = :idEntrainement");
 		$requete_prepare->bindParam(':idEntrainement', $idEntrainement, PDO::PARAM_STR);
 		$bool = $requete_prepare->execute();
 		$this->assertTrue($bool);
	 }
	 
	 /**
	  * Test de la methode getAleatoryTimeForCollectiveTraining() de EntrainementModele.
	  */
	 function test_getAleatoryTimeForCollectiveTraining()
	 {
	 	try
	 	{
	 		//On regarde s'il y a des entrainements en base
	 		$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT COUNT(*) as COUNT FROM t_entrainement  WHERE type = 'collectif'");
	 		$requete_prepare->execute();
	 		$count = $requete_prepare->fetch(PDO::FETCH_OBJ)->COUNT;
	 		if($count == 0)
	 		{
		 		//On créé 2 entrainements collectifs en base
		 		$dateDebut1 = mktime(11, 0, 0, date("n"), date("j"), date("Y")); //Aujourd'hui à 11h
		 		$this->addTraining('ETEST0014', 360, 500, $dateDebut1, 'collectif', 'O1');
		 		
		 		$dateDebut2 = mktime(18, 0, 0, date("n"), date("j"), date("Y")); //Aujourd'hui à 18h
		 		$this->addTraining('ETEST0042', 360, 500, $dateDebut2, 'collectif', 'O2');
		 		
		 		$ajd_9h = mktime(9, 0, 0, date("n"), date("j"), date("Y"));
		 		$ajd_13h = mktime(13, 0, 0, date("n"), date("j"), date("Y"));
		 		$ajd_16h = mktime(16, 0, 0, date("n"), date("j"), date("Y"));
		 		$ajd_20h = mktime(20, 0, 0, date("n"), date("j"), date("Y"));
		 		//On teste 10 fois la méthode
		 		for($i=0 ; $i < 10 ; $i++)
		 		{
		 			$dateReceived = $this->_mdlEntrainement->getAleatoryTimeForCollectiveTraining();
		 			if(time() > DATE_MAX) //S'il est plus de 22h -> Exception
		 			{
		 				$this->assertEqual($dateReceived, -1);
		 			}
		 			else 
		 			{
			 			$this->assertFalse($dateReceived < DATE_MIN); //Ne doit pas etre avant 8h
			 			$this->assertFalse($dateReceived > DATE_MAX); //Ne doit pas etre apres 22h
			 			$this->assertTrue($dateReceived >= time()); //Doit etre superieure a la date actuelle 
			 			$this->assertFalse($dateReceived > $ajd_9h && $dateReceived < $ajd_13h); //Ne doit pas etre entre 9h et 13h
			 			$this->assertFalse($dateReceived > $ajd_16h && $dateReceived < $ajd_20h); //Ne doit pas etre entre 16h et 20h
		 			}
		 		}
	 		}
	 	
 		}
 		catch(Exception $e)
 		{
 			echo 'Exception reçue : '.$e->getMessage().'<br/>';
 			echo 'Trace : <pre>'.$e->getTraceAsString().'</pre><br/>';
 		}
 		
 		//Suppressions
 		$this->deleteTraining('ETEST0014');
 		$this->deleteTraining('ETEST0042');
	 }
	 
	 /**
	  * Test de la méthode createAleatoryCollectiveTraining() de EntrainementModele.
	  * Remarque : Si vous lancer ce test après 21h, vous risquez d'avoir une boucle infinie (à partir de 21h30 exactement).
	  * Solution : Modifiez temporairement la valeur de DATE_MAX dans Configuration.
	  */
	  function test_createAleatoryCollectiveTraining()
	  {
		    try
		    {
		    	if(time() <= mktime(22, 0, 0, date("n"), date("j"), date("Y")))
		    	{
			    	//On compte le nombre d'entrainements collectifs avant exécution de la méthode
			    	$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT COUNT(*) as COUNT FROM t_entrainement  WHERE type = 'collectif'");
			    	$requete_prepare->execute();
			    	$countBefore = $requete_prepare->fetch(PDO::FETCH_OBJ)->COUNT;
	
			    	//On exécute la méthode à tester
			    	$idEntrainementCree = $this->_mdlEntrainement->createAleatoryCollectiveTraining();
			    	
			    	//On compte le nombre d'entrainements collectifs apres exécution de la méthode
			    	$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT COUNT(*) as COUNT FROM t_entrainement  WHERE type = 'collectif'");
			    	$requete_prepare->execute();
			    	$countAfter = $requete_prepare->fetch(PDO::FETCH_OBJ)->COUNT;
			    	
			    	//Asserts
			    	$this->assertEqual($countBefore + 1, $countAfter);
			    	$this->assertNotNull($idEntrainementCree);
			    	
			    	//On récupere l'entrainement créé
			    	$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT * FROM t_entrainement, t_entrainementoffre  WHERE t_entrainement.OffreEntrainement_idOffre = t_entrainementoffre.idOffre AND idEntrainement = :idEntrainement");
			    	$requete_prepare->bindParam(':idEntrainement', $idEntrainementCree, PDO::PARAM_STR);
			    	$requete_prepare->execute();
			    	$donnees = $requete_prepare->fetch(PDO::FETCH_ASSOC);
			    	$dureeReceived = $donnees[ 'duree' ];
			    	$prixReceived = $donnees[ 'prix' ];
			    	$dateDebutReceived = $donnees[ 'dateDebut' ];
			    	$typeReceived = $donnees[ 'type' ];
			    	$niveauMaxReceived = $donnees[ 'niveauMax' ];
			    	$annuleReceived = $donnees[ 'annule' ];
			    	$nbParticipantsMinReceived = $donnees[ 'nbParticipantsMin' ];
			    	$nbParticipantsMaxReceived = $donnees[ 'nbParticipantsMax' ];
			    	$idOffreReceived = $donnees[ 'OffreEntrainement_idOffre' ];
			    	$attaqueOffreReceived = $donnees[ 'attaque_offre' ];
			    	$defenseOffreReceived = $donnees[ 'defense_offre' ];
			    	$vieOffreReceived = $donnees[ 'vie_offre' ];
			    	$levelUpReceived = $donnees[ 'levelUp' ];
			    	
			    	$offreReceived = new OffreEntrainement($idOffreReceived, $attaqueOffreReceived, $defenseOffreReceived, $vieOffreReceived, $levelUpReceived);
			    	
			    	//On verifie le niveau Max recu
			    	if(in_array( $offreReceived->getSommePoints(), array(5, 6)))
			    		$this->assertTrue($niveauMaxReceived >= 1 && $niveauMaxReceived <= 10);
			    	elseif(in_array( $offreReceived->getSommePoints(), array(3, 4)))
			    		$this->assertTrue($niveauMaxReceived >= 11 && $niveauMaxReceived <= 20);
			    	else 
			    		$this->assertTrue($niveauMaxReceived >= 21 && $niveauMaxReceived <= 30);
			    	
			    	$this->assertTrue($offreReceived->getSommePoints() > 0 && $offreReceived->getSommePoints() <= 6);
	
			    	
			    	//On calcule les donnees attendues
			    	$prixExpected = ceil( COEF_MULTI_PRIX_ENTRAINEMENT_CO * (( $niveauMaxReceived * 20 ) + ( $niveauMaxReceived * $offreReceived->getSommePoints() ) + COUT_SUPP_ENTRAINEMENT) );
			    	$dureeExpected = $prixExpected * COEF_MULTI_DUREE_ENTRAINEMENT_CO;
			    	
			    	$this->assertEqual($prixReceived, $prixExpected);
			    	$this->assertEqual($dureeReceived, $dureeExpected);
			    	$this->assertEqual($annuleReceived, 0);
			    	$this->assertEqual($typeReceived, 'collectif');
			   		$this->assertEqual($nbParticipantsMinReceived, NB_PARTICIPANTS_MIN);
			   		$this->assertEqual($nbParticipantsMaxReceived, NB_PARTICIPANTS_MAX);
			    	
			    	//Suppressions
			    	$this->deleteTraining($idEntrainementCree);
		    	} 	
		  	}
		  	catch(Exception $e)
		  	{
		  		echo 'Exception reçue : '.$e->getMessage().'<br/>';
		  		echo 'Trace : <pre>'.$e->getTraceAsString().'</pre><br/>';
		  	}
	  }
	  
		/**
         * Test de la méthode gererEntrainementsCollectifs() de EntrainementModele
         */
        function test_gererEntrainementsCollectifs()
        {
                try
                {
                		//On compte le nombre d'entrainements co' avant d'executer la requete
	                	$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT COUNT(*) as COUNT FROM t_entrainement WHERE type = 'collectif'");
	                	$requete_prepare->bindParam(':currentTime', $currentTime, PDO::PARAM_INT);
	                	$requete_prepare->execute();
	                	$count1 = $requete_prepare->fetch(PDO::FETCH_OBJ)->COUNT;
                	
                       	//Execution de la méthode à tester
                        $this->_mdlEntrainement->gererEntrainementsCollectifs();
                        
                        //On vérifie qu'il y a toujours 1 à 3 entrainements collectifs disponibles
                        $currentTime = time();
                        $requete_prepare = $this->_dao->getConnexion()->prepare("SELECT COUNT(*) as COUNT FROM t_entrainement WHERE type = 'collectif'");
                        $requete_prepare->bindParam(':currentTime', $currentTime, PDO::PARAM_INT);
                        $requete_prepare->execute();
                        $nbEntrainementsDispo = $requete_prepare->fetch(PDO::FETCH_OBJ)->COUNT;
                        
                        //Asserts
                        if($count1 == 0 && 
                           time() <= mktime(22, 0, 0, date("n"), date("j"), date("Y")))
                        {
                        	$this->assertTrue($nbEntrainementsDispo <= 3 && $nbEntrainementsDispo >= 1);
                        }
                        else 
                        {
                        	$this->assertEqual($nbEntrainementsDispo, $count1);
                        }
                        
                }
                catch(Exception $e)
                {
                        echo 'Exception reçue : '.$e->getMessage().'<br/>';
                        echo 'Trace : '.$e->getTraceAsString().'<br/>';
                }
                
                //Suppressions
                if($count1 == 0)  	
                {
                	$requete_prepare = $this->_dao->getConnexion()->prepare("DELETE FROM t_entrainement WHERE type = 'collectif'");
                	$requete_prepare->execute();
                }
	
        }
        
        
        /**
         * Test de la méthode assignEntrainementsCollectifs() de EntrainementModele
         */
        function test_assignEntrainementsCollectifs()
        {
        	try
        	{     		
        		//On ajoute 2 entrainements de test
        		$idEntrainement1 = "ETEST0010";
        		$this->addTraining($idEntrainement1, 500, 1000, time(), 'collectif', 'O3');
        		$idEntrainement2 = "ETEST0012";
        		$this->addTraining($idEntrainement2, 503, 1030, time(), 'collectif', 'O5');
        		
        		//Execution de la méthode à tester
        		$smarty = new Smarty();
        		$smarty = $this->_mdlEntrainement->assignEntrainementsCollectifs($smarty);
        		 
        		//Récupération des entrainements collectifs assignés
        		$entrainementsCollectifs = $smarty->getTemplateVars("entrainementsCollectifs");
        		
        		//Récupération des entrainements collectifs
        		$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT * FROM t_entrainement, t_entrainementoffre WHERE t_entrainement.OffreEntrainement_idOffre = t_entrainementoffre.idOffre AND type = 'collectif'");
        		$requete_prepare->execute();
        		$i=0;
        		while($donnees = $requete_prepare->fetch(PDO::FETCH_ASSOC))
        		{
        			$i++;
        			$this->assertEqual($entrainementsCollectifs["idEntrainementCo$i"], $donnees[ 'idEntrainement' ]);
        			$this->assertEqual($entrainementsCollectifs["dureeEntrainementCo$i"], date("G\hi",$donnees[ 'duree' ]));
        			$this->assertEqual($entrainementsCollectifs["dateDebutEntrainementCo$i"], date("j/m/Y \à H\hi",$donnees[ 'dateDebut' ]));
        			$this->assertEqual($entrainementsCollectifs["prixEntrainementCo$i"], $donnees[ 'prix' ]);
        			$this->assertEqual($entrainementsCollectifs["vieOffreCo$i"], $donnees[ 'vie_offre' ]);
        			$this->assertEqual($entrainementsCollectifs["attaqueOffreCo$i"], $donnees[ 'attaque_offre' ]);
        			$this->assertEqual($entrainementsCollectifs["defenseOffreCo$i"], $donnees[ 'defense_offre' ]);
        			$this->assertEqual($entrainementsCollectifs["levelUpOffreCo$i"], $donnees[ 'levelUp' ]);
        			$this->assertEqual($entrainementsCollectifs["niveauMaxCo$i"], $donnees[ 'niveauMax' ]);
        			$this->assertEqual($entrainementsCollectifs["nbParticipantsMinCo$i"], $donnees[ 'nbParticipantsMin' ]);	
        			$this->assertEqual($entrainementsCollectifs["nbParticipantsMaxCo$i"], $donnees[ 'nbParticipantsMax' ]);
        		}
        		
        		//Asserts
        		$this->assertNotNull($smarty);
        		$this->assertNotNull($entrainementsCollectifs);
        		
        		//On supprime les entrainements de test
        		$this->deleteTraining($idEntrainement1);
        		$this->deleteTraining($idEntrainement2);
        	}
        	catch(Exception $e)
        	{
        		echo 'Exception reçue : '.$e->getMessage().'<br/>';
        		echo 'Trace : '.$e->getTraceAsString().'<br/>';
        	}
        		
        }
        
        
        
        /**
         * Test de la méthode souscrireEntrainementCollectif() de EntrainementModele
         */
        function test_souscrireEntrainementCollectif_OK()
        {
        	try
        	{
        		//------------------------ Inscription qui se déroule bien -------------------------------
        		//Création des occurrences de test
        		$idJoueur = "JTEST012";
        		$this->addPlayer($idJoueur, 3000, time());
        		@session_start();
        		$_SESSION[ 'joueur' ] = new Joueur($idJoueur, "Joueur test", array(), 3000, time());
        
        		$idAnimal = "ATEST012";
        		$this->addAnimal($idAnimal, $idJoueur, "Lapin de test", 10, 30, 20);
        		
        		$idEntrainementExpected = 'ETEST00078';
        		$dureeExpected = 800;
        		$prixExpected = 567;
        		$dateExpected = time() + 800;
        		$typeExpected = 'collectif';
        		$nbParticipantsMin = 2;
        		$nbParticipantsMax = 3;
        		$niveauMax = 19;
        		$annule = 0;
        		$offreExpected = new OffreEntrainement('O5', 2, 2, 2, 0);
        		$this->addTraining($idEntrainementExpected, $dureeExpected, $prixExpected, $dateExpected, $typeExpected, 'O5', $niveauMax, $nbParticipantsMin, $nbParticipantsMax, $annule);
        
        		//Execution de la méthode à tester
        		$entrainementReceived = $this->_mdlEntrainement->souscrireEntrainementCollectif($idEntrainementExpected);
        
        		//Select pour verifications
        		$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT credit FROM t_joueur WHERE idFacebook = :idFacebook");
        		$requete_prepare->bindParam(':idFacebook', $idJoueur, PDO::PARAM_STR);
        		$bool = $requete_prepare->execute();
        		$donnees = $requete_prepare->fetch(PDO::FETCH_OBJ);
        		$creditReceived = $donnees->credit;
        
        		$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT COUNT(*) as COUNT FROM t_entrainement_animal WHERE Entrainement_idEntrainement = :idEntrainement AND Animal_idAnimal = :idAnimal");
        		$requete_prepare->bindParam(':idEntrainement', $idEntrainementExpected, PDO::PARAM_STR);
        		$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
        		$bool = $requete_prepare->execute();
        		$count = $requete_prepare->fetch(PDO::FETCH_OBJ)->COUNT;
        
        
        		//Asserts
        		$this->assertEqual($entrainementReceived->getIdEntrainement(), $idEntrainementExpected);
        		$this->assertEqual($entrainementReceived->getDuree(), $dureeExpected);
        		$this->assertEqual($entrainementReceived->getPrix(), $prixExpected);
        		$this->assertEqual($entrainementReceived->getDateDebut(), $dateExpected);
        		$this->assertEqual($entrainementReceived->getType(), $typeExpected);
        		$this->assertEqual($entrainementReceived->getOffre(), $offreExpected);
        
        		$this->assertEqual($creditReceived, 3000 - $prixExpected);
        
        		$this->assertEqual($count, 1);
        
        		//Suppressions
        		$this->deleteAnimal($idAnimal);
        		$this->deleteTraining($idEntrainementExpected);
        		$this->deletePlayer($idJoueur);
        			
        		$requete_prepare = $this->_dao->getConnexion()->prepare("DELETE FROM t_entrainement_animal WHERE Entrainement_idEntrainement = :idEntrainement AND Animal_idAnimal = :idAnimal");
        		$requete_prepare->bindParam(':idEntrainement', $idEntrainementExpected, PDO::PARAM_STR);
        		$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
        		$bool = $requete_prepare->execute();
        		$this->assertTrue($bool);
        		
        	}
        	catch(Exception $e)
        	{
        		echo 'Exception reçue : '.$e->getMessage().'<br/>';
        		echo 'Trace : <pre>'.$e->getTraceAsString().'</pre><br/>';
        		//Suppressions
        		$this->deleteAnimal($idAnimal);
        		$this->deleteTraining($idEntrainementExpected);
        		$this->deletePlayer($idJoueur);
        	}
        
        }
        
        /**
         * Test de la méthode souscrireEntrainementCollectif() de EntrainementModele
         * qui doit générer une exception car entrainement annulé
         */
        function test_souscrireEntrainementCollectif_ANNULE()
        {
        	try
        	{
        		//------------------------ Probleme : Entrainement annulé -------------------------------
        		//Création des occurrences de test
        		$idJoueur = "JTEST012";
        		$this->addPlayer($idJoueur, 3000, time());
        		@session_start();
        		$_SESSION[ 'joueur' ] = new Joueur($idJoueur, "Joueur test", array(), 3000, time());
        
        		$idAnimal = "ATEST012";
        		$this->addAnimal($idAnimal, $idJoueur, "Lapin de test", 10, 30, 20);
        
        		$idEntrainementExpected = 'ETEST00078';
        		$dureeExpected = 800;
        		$prixExpected = 567;
        		$dateExpected = time() + 800;
        		$typeExpected = 'collectif';
        		$nbParticipantsMin = 2;
        		$nbParticipantsMax = 3;
        		$niveauMax = 19;
        		$annule = 1;
        		$offreExpected = new OffreEntrainement('O5', 2, 2, 2, 0);
        		$this->addTraining($idEntrainementExpected, $dureeExpected, $prixExpected, $dateExpected, $typeExpected, 'O5', $niveauMax, $nbParticipantsMin,$nbParticipantsMax, $annule);
        
        		//Execution de la méthode à tester
        		$entrainementReceived = $this->_mdlEntrainement->souscrireEntrainementCollectif($idEntrainementExpected);
        		$this->expectException();
        
        		//Select pour verifications
        		$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT credit FROM t_joueur WHERE idFacebook = :idFacebook");
        		$requete_prepare->bindParam(':idFacebook', $idJoueur, PDO::PARAM_STR);
        		$bool = $requete_prepare->execute();
        		$donnees = $requete_prepare->fetch(PDO::FETCH_OBJ);
        		$creditReceived = $donnees->credit;
        
        		$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT COUNT(*) as COUNT FROM t_entrainement_animal WHERE Entrainement_idEntrainement = :idEntrainement AND Animal_idAnimal = :idAnimal");
        		$requete_prepare->bindParam(':idEntrainement', $idEntrainementExpected, PDO::PARAM_STR);
        		$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
        		$bool = $requete_prepare->execute();
        		$count = $requete_prepare->fetch(PDO::FETCH_OBJ)->COUNT;
        
        
        		//Asserts
        		$this->assertNull($entrainementReceived);
        
        		$this->assertEqual($creditReceived, 3000);
        
        		$this->assertEqual($count, 0);
        
        		//Suppressions
        		$this->deleteAnimal($idAnimal);
        		$this->deleteTraining($idEntrainementExpected);
        		$this->deletePlayer($idJoueur);
        		 
        		$requete_prepare = $this->_dao->getConnexion()->prepare("DELETE FROM t_entrainement_animal WHERE Entrainement_idEntrainement = :idEntrainement AND Animal_idAnimal = :idAnimal");
        		$requete_prepare->bindParam(':idEntrainement', $idEntrainementExpected, PDO::PARAM_STR);
        		$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
        		$bool = $requete_prepare->execute();
        		$this->assertTrue($bool);
        	}
        	catch(LogicException $e)
        	{
        		//Suppressions
        		$this->deleteAnimal($idAnimal);
        		$this->deleteTraining($idEntrainementExpected);
        		$this->deletePlayer($idJoueur);
        	}
        	catch(Exception $e)
        	{
        		echo 'Exception reçue : '.$e->getMessage().'<br/>';
        		echo 'Trace : <pre>'.$e->getTraceAsString().'</pre><br/>';
        		//Suppressions
        		$this->deleteAnimal($idAnimal);
        		$this->deleteTraining($idEntrainementExpected);
        		$this->deletePlayer($idJoueur);
        	}
        
        }
        
        /**
        * Test de la méthode souscrireEntrainementCollectif() de EntrainementModele
        * qui doit générer une exception car entrainement deja commencé
        */
        function test_souscrireEntrainementCollectif_COMMENCE()
        {
        	try
        	{
        		 //------------------------ Probleme : Entrainement deja commencé -------------------------------
        		//Création des occurrences de test
        		$idJoueur = "JTEST012";
        		$this->addPlayer($idJoueur, 3000, time());
        		@session_start();
        		$_SESSION[ 'joueur' ] = new Joueur($idJoueur, "Joueur test", array(), 3000, time());
        		
        		$idAnimal = "ATEST012";
        		$this->addAnimal($idAnimal, $idJoueur, "Lapin de test", 10, 30, 20);
        		
        		$idEntrainementExpected = 'ETEST00078';
        		$dureeExpected = 800;
        		$prixExpected = 567;
        		$dateExpected = time() - 800;
        		$typeExpected = 'collectif';
        		$nbParticipantsMin = 2;
        		$nbParticipantsMax = 3;
        		$niveauMax = 19;
        		$annule = 0;
        		$offreExpected = new OffreEntrainement('O5', 2, 2, 2, 0);
        		$this->addTraining($idEntrainementExpected, $dureeExpected, $prixExpected, $dateExpected, $typeExpected, 'O5', $niveauMax, $nbParticipantsMin, $nbParticipantsMax, $annule);
        		
        		//Execution de la méthode à tester
        		$entrainementReceived = $this->_mdlEntrainement->souscrireEntrainementCollectif($idEntrainementExpected);
        		$this->expectException();
        		
        		//Select pour verifications
        		$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT credit FROM t_joueur WHERE idFacebook = :idFacebook");
        		$requete_prepare->bindParam(':idFacebook', $idJoueur, PDO::PARAM_STR);
        		$bool = $requete_prepare->execute();
        		$donnees = $requete_prepare->fetch(PDO::FETCH_OBJ);
        		$creditReceived = $donnees->credit;
        		
        		$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT COUNT(*) as COUNT FROM t_entrainement_animal WHERE Entrainement_idEntrainement = :idEntrainement AND Animal_idAnimal = :idAnimal");
        		$requete_prepare->bindParam(':idEntrainement', $idEntrainementExpected, PDO::PARAM_STR);
        		$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
        		$bool = $requete_prepare->execute();
        		$count = $requete_prepare->fetch(PDO::FETCH_OBJ)->COUNT;
        		
        		
        		//Asserts
        		$this->assertNull($entrainementReceived);
        		
        		$this->assertEqual($creditReceived, 3000);
        		
        		$this->assertEqual($count, 0);
        		
        		//Suppressions
        		$this->deleteAnimal($idAnimal);
        		$this->deleteTraining($idEntrainementExpected);
        		$this->deletePlayer($idJoueur);
        		 
        		$requete_prepare = $this->_dao->getConnexion()->prepare("DELETE FROM t_entrainement_animal WHERE Entrainement_idEntrainement = :idEntrainement AND Animal_idAnimal = :idAnimal");
        		$requete_prepare->bindParam(':idEntrainement', $idEntrainementExpected, PDO::PARAM_STR);
        		$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
        		$bool = $requete_prepare->execute();
        		$this->assertTrue($bool);
        	}
        	catch(LogicException $e)
        	{
        		//Suppressions
        		$this->deleteAnimal($idAnimal);
        		$this->deleteTraining($idEntrainementExpected);
        		$this->deletePlayer($idJoueur);
        	}
        	catch(Exception $e)
        	{
        		echo 'Exception reçue : '.$e->getMessage().'<br/>';
        		echo 'Trace : <pre>'.$e->getTraceAsString().'</pre><br/>';
        		//Suppressions
        		$this->deleteAnimal($idAnimal);
        		$this->deleteTraining($idEntrainementExpected);
        		$this->deletePlayer($idJoueur);
        	}
        
        }
        
        
        /**
         * Test de la méthode souscrireEntrainementCollectif() de EntrainementModele
         * qui doit générer une exception car le niveau de l'animal est supérieur au niveau max autorisé.
         */
        function test_souscrireEntrainementCollectif_NIVEAU_MAX()
        {
        	try
        	{
        		//------------------------ Probleme : Niveau de l'animal trop élevé -------------------------------
        		//Création des occurrences de test
        		$idJoueur = "JTEST012";
        		$this->addPlayer($idJoueur, 3000, time());
        		@session_start();
        		$_SESSION[ 'joueur' ] = new Joueur($idJoueur, "Joueur test", array(), 3000, time());
        
        		$idAnimal = "ATEST012";
        		$this->addAnimal($idAnimal, $idJoueur, "Lapin de test", 10, 30, 20, 30);
        
        		$idEntrainementExpected = 'ETEST00078';
        		$dureeExpected = 800;
        		$prixExpected = 567;
        		$dateExpected = time() + 800;
        		$typeExpected = 'collectif';
        		$nbParticipantsMin = 2;
        		$nbParticipantsMax = 3;
        		$niveauMax = 19;
        		$annule = 0;
        		$offreExpected = new OffreEntrainement('O5', 2, 2, 2, 0);
        		$this->addTraining($idEntrainementExpected, $dureeExpected, $prixExpected, $dateExpected, $typeExpected, 'O5', $niveauMax, $nbParticipantsMin, $nbParticipantsMax, $annule);
        
        		//Execution de la méthode à tester
        		$entrainementReceived = $this->_mdlEntrainement->souscrireEntrainementCollectif($idEntrainementExpected);
        		$this->expectException();
        
        		//Select pour verifications
        		$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT credit FROM t_joueur WHERE idFacebook = :idFacebook");
        		$requete_prepare->bindParam(':idFacebook', $idJoueur, PDO::PARAM_STR);
        		$bool = $requete_prepare->execute();
        		$donnees = $requete_prepare->fetch(PDO::FETCH_OBJ);
        		$creditReceived = $donnees->credit;
        
        		$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT COUNT(*) as COUNT FROM t_entrainement_animal WHERE Entrainement_idEntrainement = :idEntrainement AND Animal_idAnimal = :idAnimal");
        		$requete_prepare->bindParam(':idEntrainement', $idEntrainementExpected, PDO::PARAM_STR);
        		$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
        		$bool = $requete_prepare->execute();
        		$count = $requete_prepare->fetch(PDO::FETCH_OBJ)->COUNT;
        
        
        		//Asserts
        		$this->assertNull($entrainementReceived);
        
        		$this->assertEqual($creditReceived, 3000);
        
        		$this->assertEqual($count, 0);
        
        		//Suppressions
        		$this->deleteAnimal($idAnimal);
        		$this->deleteTraining($idEntrainementExpected);
        		$this->deletePlayer($idJoueur);
        		 
        		$requete_prepare = $this->_dao->getConnexion()->prepare("DELETE FROM t_entrainement_animal WHERE Entrainement_idEntrainement = :idEntrainement AND Animal_idAnimal = :idAnimal");
        		$requete_prepare->bindParam(':idEntrainement', $idEntrainementExpected, PDO::PARAM_STR);
        		$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
        		$bool = $requete_prepare->execute();
        		$this->assertTrue($bool);
        	}
        	catch(LogicException $e)
        	{
        		//Suppressions
        		$this->deleteAnimal($idAnimal);
        		$this->deleteTraining($idEntrainementExpected);
        		$this->deletePlayer($idJoueur);
        	}
        	catch(Exception $e)
        	{
        		echo 'Exception reçue : '.$e->getMessage().'<br/>';
        		echo 'Trace : <pre>'.$e->getTraceAsString().'</pre><br/>';
        		//Suppressions
        		$this->deleteAnimal($idAnimal);
        		$this->deleteTraining($idEntrainementExpected);
        		$this->deletePlayer($idJoueur);
        	}
        
        }
        
        /**
         * Test de la méthode cancelIncompleteCollectiveTrainings() de EntrainementModele
         */
        function test_cancelIncompleteCollectiveTrainings()
        {
        	try
        	{
        		//On créé un entrainement de test
				$idEntrainement = 'ETEST0012';
				$duree = 3000;
				$prix = 500;
				$date = time();
				$type = 'collectif';
				$idOffre = 'O1';
				$nbParticipantsMin = 5;
				$nbParticipantsMax = 6;
				$niveauMax = 10;
				$annule = 0;
				$entrainement = new EntrainementCollectif($idEntrainement, $duree, $prix, $date, new OffreEntrainement('O1', 3, 1, 1, 0), array());
				$this->addTraining($idEntrainement, $duree, $prix, $date, $type, $idOffre, $niveauMax, $nbParticipantsMin, $nbParticipantsMax, $annule);
		
				//On créé 2 animaux de test
				$idAnimal1 = "ATEST0010";
				$nomAnimal1 = "Lapin test 1";
				$vie1 = 10;
				$defense1 = 15;
				$attaque1 = 20;
				$niveau1 = 3;
				$proprietaire1 = "JTEST0010";
				$animal1 = new Animal($idAnimal1, $proprietaire1, $nomAnimal1, $vie1, $defense1, $attaque1, $niveau1);
				$this->addAnimal($idAnimal1, $proprietaire1, $nomAnimal1, $vie1, $defense1, $attaque1, $niveau1);
				
				$idAnimal2 = "ATEST0012";
				$nomAnimal2 = "Lapin test 2";
				$vie2 = 12;
				$defense2 = 25;
				$attaque2 = 22;
				$niveau2 = 5;
				$proprietaire2 = "JTEST0012";
				$animal2 = new Animal($idAnimal2, $proprietaire2, $nomAnimal2, $vie2, $defense2, $attaque2, $niveau2);
				$this->addAnimal($idAnimal2, $proprietaire2, $nomAnimal2, $vie2, $defense2, $attaque2, $niveau2);
				
				$idJoueur1 = "JTEST0010";
				$dateInscription1 = time() -10000;
				$credit1 = 1000;
				$this->addPlayer($idJoueur1, $credit1, $dateInscription1);
				
				$idJoueur2 = "JTEST0012";
				$dateInscription2 = time() -20000;
				$credit2 = 2000;
				$this->addPlayer($idJoueur2, $credit2, $dateInscription2);
				
				//Et on inscrit les 2 animaux à cet entrainement
				$requete_prepare = $this->_dao->getConnexion()->prepare("INSERT INTO t_entrainement_animal (Entrainement_idEntrainement, Animal_idAnimal, dateSouscription, valide)
						VALUES (:idEntrainement, :idAnimal1, :dateSouscription, 0),
						(:idEntrainement, :idAnimal2, :dateSouscription, 0)");
				$requete_prepare->bindParam(':idEntrainement', $idEntrainement, PDO::PARAM_STR);
				$requete_prepare->bindParam(':idAnimal1', $idAnimal1, PDO::PARAM_STR);
				$requete_prepare->bindParam(':idAnimal2', $idAnimal2, PDO::PARAM_STR);
				$requete_prepare->bindParam(':dateSouscription', $date, PDO::PARAM_INT);
				$bool = $requete_prepare->execute();
				$this->assertTrue($bool);
				
				//On exécute la méthode à tester
				$this->_mdlEntrainement->cancelIncompleteCollectiveTrainings();
		
				//Vérifications
				$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT COUNT(*) AS COUNT FROM t_entrainement_animal
																		 WHERE Entrainement_idEntrainement = :idEntrainement");
				$requete_prepare->bindParam(':idEntrainement', $idEntrainement, PDO::PARAM_STR);
				$requete_prepare->execute();
				$count = $requete_prepare->fetch(PDO::FETCH_OBJ)->COUNT;
				$this->assertEqual($count, 0);
				
				$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT COUNT(*) AS COUNT FROM t_entrainement
																		 WHERE idEntrainement = :idEntrainement");
				$requete_prepare->bindParam(':idEntrainement', $idEntrainement, PDO::PARAM_STR);
				$requete_prepare->execute();
				$count = $requete_prepare->fetch(PDO::FETCH_OBJ)->COUNT;
				$this->assertEqual($count, 0);
				
				$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT credit FROM t_joueur
																	     WHERE idFacebook = :idFacebook");
				$requete_prepare->bindParam(':idFacebook', $idJoueur1, PDO::PARAM_STR);
				$requete_prepare->execute();
				$creditReceived = $requete_prepare->fetch(PDO::FETCH_OBJ)->credit;
				$this->assertEqual($creditReceived, $credit1 + $prix);
				
				$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT credit FROM t_joueur
																		 WHERE idFacebook = :idFacebook");
				$requete_prepare->bindParam(':idFacebook', $idJoueur2, PDO::PARAM_STR);
				$requete_prepare->execute();
				$creditReceived = $requete_prepare->fetch(PDO::FETCH_OBJ)->credit;
				$this->assertEqual($creditReceived, $credit2 + $prix);

				//Suppressions
				$this->deleteTraining($idEntrainement);
				$this->deleteAnimal($idAnimal1);
				$this->deleteAnimal($idAnimal2);
				$this->deletePlayer($idJoueur1);
				$this->deletePlayer($idJoueur2);
				
				$requete_prepare = $this->_dao->getConnexion()->prepare("DELETE FROM t_entrainement_animal 
																		 WHERE Entrainement_idEntrainement = :idEntrainement");
				$requete_prepare->bindParam(':idEntrainement', $idEntrainement, PDO::PARAM_STR);
				$bool = $requete_prepare->execute();
				$this->assertTrue($bool);
        	}
        	catch(Exception $e)
        	{
        		echo 'Exception reçue : '.$e->getMessage().'<br/>';
        		echo 'Trace : <pre>'.$e->getTraceAsString().'</pre><br/>';
        	}
        
        }
}

?>