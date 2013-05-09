<?php
require_once('./../../lib/simpletest/autorun.php');
require_once './../controleurs/ControleurEntrainement.php';
require_once './../metiers/Joueur.php';
require_once './../modeles/EntrainementModele.php';
require_once './../settings/Configuration.php';

/**
 *
 * Classe de test de la classe ControleurEntrainement (controleurs).
 *
 */
class ControleurEntrainementTestCase extends UnitTestCase
{
	/** DAO */
	private $_dao;
	
	
	/**
	 * Constructeur par défaut. Instancie le DAO 
	 */
	public function __construct()
	{
		$this->_dao = new DAO();
	}
	
	/**
	 * Test du constructeur de ControleurEntrainementTestCase.
	 */
	function test_constructeur_ControleurEntrainementTestCase()
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
	
	/**
	 *  Permet d'ajouter un entrainement de test dans la base de données et d'être sur que l'ajout ait eu lieu.
	 * @param String $idEntrainement
	 * @param String $joueur
	 * @param int $duree
	 * @param int $prix
	 * @param timestamp $date
	 * @param String $idOffre
	 */
	private function addTraining($idEntrainement, $duree, $prix, $date, $type, $idOffre, $niveauMax=0, $nbParticipantsMin=0, $annule=0)
	{
		try
		{
			//Insertion
			$requete_prepare = $this->_dao->getConnexion()->prepare("INSERT INTO t_entrainement (idEntrainement, duree, prix, dateDebut, type, niveauMax, nbParticipantsMin, annule, OffreEntrainement_idOffre) VALUES (:id, :duree, :prix, :date, :type, :niveauMax, :nbParticipantsMin, :annule, :offre)");
			$requete_prepare->bindParam(':id', $idEntrainement, PDO::PARAM_STR);
			$requete_prepare->bindParam(':duree', $duree, PDO::PARAM_INT);
			$requete_prepare->bindParam(':prix', $prix, PDO::PARAM_INT);
			$requete_prepare->bindParam(':date', $date, PDO::PARAM_INT);
			$requete_prepare->bindParam(':type', $type, PDO::PARAM_INT);
			$requete_prepare->bindParam(':niveauMax', $niveauMax, PDO::PARAM_INT);
			$requete_prepare->bindParam(':nbParticipantsMin', $nbParticipantsMin, PDO::PARAM_INT);
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
	
	// -------------------------------- TESTS DE LA CLASSE CONTROLEURENTRAINEMENT ---------------------------------------
	
	
	/**
	 * Test du contructeur de la classe
	 */
	function test_constructeur()
	{
		//Création d'une instance de ControleurEntrainement
		$controleur = ControleurEntrainement::getInstance();
	
		//Asserts
		$this->assertNotNull($controleur);
		$this->assertNotNull($controleur->getEntrainementModele());
		$this->assertNotNull($controleur->getJoueurModele());
	}
	
	/**
	 * Test de la méthode afficherEntrainements() du ControleurEntrainement
	 */
	function test_afficherEntrainements()
	{
		try
		{
			//Création d'une instance de ControleurEntrainement
			$controleur = ControleurEntrainement::getInstance();
			
			//Préparation des variables
			$idJoueur = "JTEST007";
			$credit = 1000;
			$dateInscription = time();
			$joueur = new Joueur($idJoueur, "Joueur test", array(), $credit, $dateInscription);
			$this->addPlayer($idJoueur, $credit, $dateInscription);
			
			$idAnimal = "ATEST007";
			$nomAnimal = "Lapin test";
			$niveauAnimal = 8;
			$this->addAnimal($idAnimal, $idJoueur, $nomAnimal, 10, 20, 30, $niveauAnimal);
			
			@session_start();
			$_SESSION[ 'joueur' ] = $joueur;
			
			//Mise a jour des offres d'entrainements individuels
			$mdlEntrainement = EntrainementModele::getInstance();
			$mdlEntrainement->gererOffresEntrainementsIndividuels();
			
			//Exécution de la méthode à traiter
			$smarty = new Smarty();
			$smartyReceived = $controleur->afficherEntrainements($smarty);
			
			//Récupération de l'entrainement individuel adapté au niveau de l'animal (8)
			$path = WARNIMALS_PATH.'/files/offres_entrainements_individuels.txt';
			$file = fopen($path, 'r');
			
			if($file != null)
			{
				$dateOffre = trim(fgets($file));
				if($dateOffre == date("d/m/Y")) //Si c'est bien l'offre du jour
				{
					$tab_offres = unserialize(fgets($file));
			
					//On prend l'offre adaptée car niveauAnimal = 8
					$offre = $tab_offres[ 'offre1' ];
			
					$prix = COEF_MULTI_PRIX_ENTRAINEMENT_IND * ceil((20 * $niveauAnimal) + ($niveauAnimal * $offre->getSommePoints()) + COUT_SUPP_ENTRAINEMENT);
					$duree = ceil($prix * COEF_MULTI_DUREE_ENTRAINEMENT_IND);
					$dateDebut = time();
						
					$entrainement =  new EntrainementIndividuel(null, $duree, $prix, $dateDebut, $offre, null);
				}
				fclose($file);
			}
				
		
			//Asserts
			$tabReceived = $smarty->getTemplateVars("entrainementIndividuel");
	
			$this->assertNotNull($tabReceived);
			$this->assertEqual($tabReceived['dureeEntrainementInd'], date("G\hi",$entrainement->getDuree()));
			$this->assertEqual($tabReceived['prixEntrainementInd'], $entrainement->getPrix());
			$this->assertEqual($tabReceived['idOffreInd'], $entrainement->getOffre()->getIdOffre());
			$this->assertEqual($tabReceived['vieOffreInd'], $entrainement->getOffre()->getVieOffre());
			$this->assertEqual($tabReceived['attaqueOffreInd'], $entrainement->getOffre()->getAttaqueOffre());
			$this->assertEqual($tabReceived['defenseOffreInd'], $entrainement->getOffre()->getDefenseOffre());
			$this->assertEqual($tabReceived['levelUpOffreInd'], $entrainement->getOffre()->getLevelUp());
			
			//Suppressions
			$this->deleteAnimal($idAnimal);
			$this->deletePlayer($idJoueur);
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : <pre>'.$e->getTraceAsString().'</pre><br/>';
		}
	}
	
	
	/**
	 * Test du case : EntrainementIndividuel de la méthode traiterAction() du ControleurEntrainement
	 */
	function test_traiterAction_case_EntrainementIndividuel()
	{
		try
		{			
			//Création des occurrences de test
		 	$idJoueur = "JTEST010";
		 	$this->addPlayer($idJoueur, 3000, time());
		 	@session_start();
		 	$_SESSION[ 'joueur' ] = new Joueur($idJoueur, "Joueur test", array(), 3000, time());
		 	
		 	$idAnimal = "ATEST010";
		 	$this->addAnimal($idAnimal, $idJoueur, "Lapin de test", 10, 30, 20, 1);
		 	
		 	//Execution de la méthode à tester
		 	ControleurEntrainement::getInstance()->traiterAction("EntrainementIndividuel");
		 	
		 	//Select pour verifications
		 	$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT * FROM t_entrainement WHERE dateDebut = (SELECT MAX(dateDebut) FROM t_entrainement) AND type = 'individuel' LIMIT 1");
		 	$bool = $requete_prepare->execute();
		 	$donnees = $requete_prepare->fetch(PDO::FETCH_ASSOC);
		 	$idEntrainementReceived = $donnees[ 'idEntrainement' ];
		 	$dureeReceived = $donnees[ 'duree' ];
		 	$prixReceived = $donnees[ 'prix' ];
		 	$dateDebutReceived = $donnees[ 'dateDebut' ];
		 	$typeReceived = $donnees[ 'type' ];
		 	$idOffreReceived = $donnees[ 'OffreEntrainement_idOffre' ];
		 	
		 	$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT credit FROM t_joueur WHERE idFacebook = :idFacebook");
		 	$requete_prepare->bindParam(':idFacebook', $idJoueur, PDO::PARAM_STR);
		 	$bool = $requete_prepare->execute();
		 	$creditReceived = $requete_prepare->fetch(PDO::FETCH_OBJ)->credit;
	
		 	$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT COUNT(*) as COUNT FROM t_entrainement_animal WHERE Entrainement_idEntrainement = :idEntrainement AND Animal_idAnimal = :idAnimal");
		 	$requete_prepare->bindParam(':idEntrainement', $idEntrainementReceived, PDO::PARAM_STR);
		 	$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
		 	$bool = $requete_prepare->execute();
		 	$count = $requete_prepare->fetch(PDO::FETCH_OBJ)->COUNT;
		 	
		 	
		 	//Asserts
		 	$this->assertNotNull($idEntrainementReceived);
		 	$this->assertTrue($dureeReceived > 0);
		 	$this->assertTrue($prixReceived > 0);
		 	$this->assertTrue($dateDebutReceived <= time());
		 	$this->assertEqual($typeReceived, 'individuel');
		 	$this->assertNotNull($idOffreReceived);
		 	
		 	$this->assertEqual($creditReceived, 3000 - $prixReceived);
		 	
		 	$this->assertEqual($count, 1);
		 

		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : <pre>'.$e->getTraceAsString().'</pre><br/>';
		}
		
		//Suppressions
		$this->deleteAnimal($idAnimal);
		$this->deleteTraining($idEntrainementReceived);
		$this->deletePlayer($idJoueur);
			
		$requete_prepare = $this->_dao->getConnexion()->prepare("DELETE FROM t_entrainement_animal WHERE Entrainement_idEntrainement = :idEntrainement AND Animal_idAnimal = :idAnimal");
		$requete_prepare->bindParam(':idEntrainement', $idEntrainementReceived, PDO::PARAM_STR);
		$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
		$bool = $requete_prepare->execute();
		$this->assertTrue($bool);
	}
	
	
	/**
	 * Test du case : EntrainementCollectif de la méthode traiterAction() du ControleurEntrainement
	 */
	function test_traiterAction_case_EntrainementCollectif()
	{
		try
		{
			//Création des occurrences de test
			$idJoueur = "JTEST010";
			$this->addPlayer($idJoueur, 3000, time());
			@session_start();
			$_SESSION[ 'joueur' ] = new Joueur($idJoueur, "Joueur test", array(), 3000, time());
	
			$idAnimal = "ATEST010";
			$this->addAnimal($idAnimal, $idJoueur, "Lapin de test", 10, 30, 20, 1);
			
			$idEntrainement = "ETEST0099";
			$duree = 579;
			$prix = 400;
			$date = time() + 100;
			$type = 'collectif';
			$idOffre = 'O5';
			$niveauMax = 11;
			$nbParticipantsMin = 2;
			$annule = 0;
			$this->addTraining($idEntrainement, $duree, $prix, $date, $type, $idOffre, $niveauMax, $nbParticipantsMin, $annule);
			
	
			//Execution de la méthode à tester
			$_GET[ 'idEntrainementCollectif' ] = $idEntrainement;
			ControleurEntrainement::getInstance()->traiterAction("EntrainementCollectif");
	
			//Select pour verifications
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT credit FROM t_joueur WHERE idFacebook = :idFacebook");
			$requete_prepare->bindParam(':idFacebook', $idJoueur, PDO::PARAM_STR);
			$bool = $requete_prepare->execute();
			$creditReceived = $requete_prepare->fetch(PDO::FETCH_OBJ)->credit;
	
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT COUNT(*) as COUNT FROM t_entrainement_animal WHERE Entrainement_idEntrainement = :idEntrainement AND Animal_idAnimal = :idAnimal");
			$requete_prepare->bindParam(':idEntrainement', $idEntrainement, PDO::PARAM_STR);
			$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
			$bool = $requete_prepare->execute();
			$count = $requete_prepare->fetch(PDO::FETCH_OBJ)->COUNT;
	
	
			//Asserts
			$this->assertEqual($creditReceived, 3000 - $prix);
			$this->assertEqual($count, 1);
				
	
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : <pre>'.$e->getTraceAsString().'</pre><br/>';
		}
	
		//Suppressions
		$this->deleteAnimal($idAnimal);
		$this->deleteTraining($idEntrainement);
		$this->deletePlayer($idJoueur);
			
		$requete_prepare = $this->_dao->getConnexion()->prepare("DELETE FROM t_entrainement_animal WHERE Entrainement_idEntrainement = :idEntrainement AND Animal_idAnimal = :idAnimal");
		$requete_prepare->bindParam(':idEntrainement', $idEntrainement, PDO::PARAM_STR);
		$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
		$bool = $requete_prepare->execute();
		$this->assertTrue($bool);
	}
}

?>