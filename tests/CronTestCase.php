<?php

require_once './../../lib/simpletest/autorun.php';
require_once './../controleurs/Cron.php';

/**
 *
 * Classe de test de la classe Cron (package controleurs)
 *
 */
class CronTestCase extends UnitTestCase
{
	
	/** DAO */
	private $_dao;
	
	/**
	 * Constructeur par défaut. Instancie le DAO.
	 */
	public function __construct()
	{
		$this->_dao = new DAO();
	}
	
	/**
	 * Test du constructeur de CronTestCase.
	 */
	function test_constructeur_CronTestCase()
	{
		//Asserts
		$this->assertNotNull($this->_dao);
		$this->assertTrue($this->_dao instanceof DAO);
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
	private function addAnimal($idAnimal, $proprietaire, $nomAnimal, $vie, $defense, $attaque)
	{
		try
		{
			//Insertion
			$requete_prepare = $this->_dao->getConnexion()->prepare("INSERT INTO t_animal (idAnimal, nomAnimal, vie, defense, attaque) VALUES (:id, :nom, :vie, :def, :att)");
			$requete_prepare->bindParam(':id', $idAnimal, PDO::PARAM_STR);
			$requete_prepare->bindParam(':nom', $nomAnimal, PDO::PARAM_STR);
			$requete_prepare->bindParam(':vie', $vie, PDO::PARAM_INT);
			$requete_prepare->bindParam(':def', $defense, PDO::PARAM_INT);
			$requete_prepare->bindParam(':att', $attaque, PDO::PARAM_INT);
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
	
			//Asserts
			$this->assertTrue($boolInsert1);
			$this->assertTrue($boolInsert2);
			$this->assertEqual($idAnimal, $idAnimalReceived);
			$this->assertEqual($proprietaire, $proprietaireReceived);
			$this->assertEqual($nomAnimal, $nomReceived);
			$this->assertEqual($vie, $vieReceived);
			$this->assertEqual($defense, $defenseReceived);
			$this->assertEqual($attaque, $attaqueReceived);
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
	private function addTraining($idEntrainement, $duree, $prix, $date, $type, $idOffre)
	{
		try
		{
			//Insertion
			$requete_prepare = $this->_dao->getConnexion()->prepare("INSERT INTO t_entrainement (idEntrainement, duree, prix, dateDebut, type, OffreEntrainement_idOffre) VALUES (:id, :duree, :prix, :date, :type, :offre)");
			$requete_prepare->bindParam(':id', $idEntrainement, PDO::PARAM_STR);
			$requete_prepare->bindParam(':duree', $duree, PDO::PARAM_INT);
			$requete_prepare->bindParam(':prix', $prix, PDO::PARAM_INT);
			$requete_prepare->bindParam(':date', $date, PDO::PARAM_INT);
			$requete_prepare->bindParam(':type', $type, PDO::PARAM_INT);
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
			$dateSouscriptionReceived = $donnees->dateDebut;
			$offreReceived = $donnees->OffreEntrainement_idOffre;
				
			//Asserts
			$this->assertTrue($bool);
			$this->assertEqual($duree, $dureeReceived);
			$this->assertEqual($prix, $prixReceived);
			$this->assertEqual($type, $typeReceived);
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
	
	
	// -------------------------------- TESTS DE LA CLASSE CRON ---------------------------------------
	
	/**
	 * Test de la methode run() de Cron.
	 */
	function test_run()
	{
		try
	 	{
	 	 //------------------- AVANT EXECUTION------------------------------------
	 		
	 		//-------------------------GererOffreEntrainementIndividuel()-------------------------
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
		 	
		 	//-------------------------EntrainerAnimaux()-------------------------
		 
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
		 	$idAnimal1 = 'ATEST0022';
		 	$nomAnimal1 = 'Lapin de test';
		 	$vie1 = 3;
		 	$defense1 = 15;
		 	$attaque1 = 35;
		 	$niveau1 = 2;
		 	$race1 = 'R0002';
		 	$idAnimal2 = 'ATEST0023';
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
		 	
		 	//----------------GererEntrainementsCollectifs()-----------
		 	
		 	//On compte le nombre d'entrainements co' avant d'executer la requete
		 	$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT COUNT(*) as COUNT FROM t_entrainement WHERE type = 'collectif'");
		 	$requete_prepare->bindParam(':currentTime', $currentTime, PDO::PARAM_INT);
		 	$requete_prepare->execute();
		 	$countEntrainementsCo = $requete_prepare->fetch(PDO::FETCH_OBJ)->COUNT;
		 		
		 
		 	//======================================
			 	//On execute la méthode à tester
			 	Cron::getInstance()->run();
		 	//======================================
		 	
		 //-------------------APRES EXECUTION------------------------------------
		 	
		 	//-------------------------GererOffreEntrainementIndividuel()-------------------------
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
		 	
		 	//-------------------------EntrainerAnimaux()-------------------------
		 	
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
		 	
		 	//-------------------------GererEntrainementsCollectifs()-------------------------
		 	
		 	//On vérifie qu'il y a toujours 1 à 3 entrainements collectifs disponibles
		 	$currentTime = time();
		 	$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT COUNT(*) as COUNT FROM t_entrainement WHERE type = 'collectif'");
		 	$requete_prepare->bindParam(':currentTime', $currentTime, PDO::PARAM_INT);
		 	$requete_prepare->execute();
		 	$nbEntrainementsDispo = $requete_prepare->fetch(PDO::FETCH_OBJ)->COUNT;
		 	
		 	//Asserts
		 	if($countEntrainementsCo == 0)
		 	{
		 		$this->assertTrue($nbEntrainementsDispo <= 3 && $nbEntrainementsDispo >= 1);
		 	}
		 	
		 	//----------- Suppressions--------------------
		 	//Suppressions pour entrainerAnimaux()
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
		 	 
		 	//Suppressions pour gererEntrainementsCollectifs()
	 		$requete_prepare = $this->_dao->getConnexion()->prepare("DELETE FROM t_entrainement WHERE type = 'collectif'");
	 		$requete_prepare->execute();
	 	}
	 	catch(Exception $e)
	 	{
	 		echo 'Exception reçue : '.$e->getMessage().'<br/>';
	 		echo 'Trace : <pre>'.$e->getTraceAsString().'</pre><br/>';
	 	}
	 		
	 	
	 	
	 	
	 	
	 	
	}
	
	
}

?>