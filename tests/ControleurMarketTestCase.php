<?php
require_once('./../../lib/simpletest/autorun.php');
require_once './../controleurs/ControleurMarket.php';

/**
 *
 * Classe de test de la classe ControleurMarket (controleurs).
 *
 */
class ControleurMarketTestCase extends UnitTestCase
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
	 * Test du constructeur de AnimalDAOTestCase.
	 */
	function test_constructeur_ControleurMarketTestCase()
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
	
	// -------------------------------- TESTS DE LA CLASSE CONTROLEURMARKET ---------------------------------------
	
	/**
	 * Test du contructeur de la classe
	 */
	function test_constructeur()
	{
		//Création d'une instance de ControleurMarket
		$controleur = ControleurMarket::getInstance();
	
		//Asserts
		$this->assertNotNull($controleur);
		$this->assertNotNull($controleur->getAnimalModele());
		$this->assertTrue($controleur->getAnimalModele() instanceof AnimalModele);
		$this->assertNotNull($controleur->getJoueurModele());
		$this->assertTrue($controleur->getJoueurModele() instanceof JoueurModele);
	}
	
	/** 
	 * Test de la méthode traiterAction() du ControleurMarket
	 */
	function test_traiterAction_Acheter()
	{
		try
		{
			//Création d'une instance de ControleurMarket
			$controleur = ControleurMarket::getInstance();
			
			//On créé des instances pour le test
			$currentTime = time();
			@session_start();
			$idJoueur = "JTEST001";
			$_SESSION[ 'joueur' ] = new Joueur($idJoueur, "Joueur de test", array('123456789'), 1000, $currentTime);
			$this->addPlayer($idJoueur, 1000, $currentTime);
			$idAnimal = "ATEST001";
			$this->addAnimal($idAnimal, "", "Lapin test", 30, 10, 20);
			
			//Exécution de la méthode à tester
			$_GET['idAnimal'] = "ATEST001";
			$controleur->traiterAction("Acheter");
			
			//Select pour vérification
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT * FROM t_joueur WHERE idFacebook = :idFacebook");
			$requete_prepare->bindParam(':idFacebook', $idJoueur, PDO::PARAM_STR);
			$requete_prepare->execute();
			$donnees = $requete_prepare->fetch(PDO::FETCH_ASSOC);
			$creditReceived = $donnees['credit'];
			$creditExpected = 900;
			
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT * FROM t_joueur_animal WHERE Animal_idAnimal = :idAnimal");
			$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
			$requete_prepare->execute();
			$donnees = $requete_prepare->fetch(PDO::FETCH_ASSOC);
			$proprietaireReceived = $donnees['Joueur_idFacebook'];
			$proprietaireExpected = $idJoueur;
			
			$this->assertEqual($creditReceived, $creditExpected);
			$this->assertEqual($proprietaireReceived, $proprietaireExpected);
			
			//Suppression des entités de test crées
			$this->deletePlayer($idJoueur);
			$this->deleteAnimal($idAnimal);
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : '.$e->getTraceAsString().'<br/>';
		}
	
	}
	
}

?>