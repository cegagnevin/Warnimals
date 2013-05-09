<?php
require_once('./../../lib/simpletest/autorun.php');
require_once '../../lib/smarty/Smarty.class.php';
require_once './../metiers/Animal.php';
require_once './../metiers/Joueur.php';
require_once './../modeles/CombatModele.php';
require_once './../mapping/DAO.php';


/**
 *
 * Classe de test de la classe ombatModele (modeles).
 *
 */
class CombatModeleTestCase extends UnitTestCase
{
	/** DAO */
	private $_dao;
	
	/** Modele Combat à tester */
	private $_mdlCombat;
	
	/**
	 * Constructeur par défaut. Instancie le DAO et le CombatModele à tester.
	 */
	public function __construct()
	{
		$this->_dao = new DAO();
		$this->_mdlCombat = CombatModele::getInstance();
	}
	
	/**
	 * Test du constructeur de AnimalModeleTestCase.
	 */
	function test_constructeur_AnimalModeleTestCase()
	{
		//Asserts
		$this->assertNotNull($this->_dao);
		$this->assertNotNull($this->_mdlCombat);
		$this->assertTrue($this->_dao instanceof DAO);
		$this->assertTrue($this->_mdlCombat instanceof CombatModele);
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
			$this->assertEqual($idAnimal, $idAnimalReceived);
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
	// -------------------------------- TESTS DE LA CLASSE COMBATMODELE ---------------------------------------
	
	
	/**
	 * Test de l'inscritpion à un combat.
	 * Fait appel au inscriptionCombat de CombatDAO.
	 *
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
			$boolReceived = $this->_mdlCombat->inscriptionCombat($idCombat, $idAnimal);
			
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
		
	}*/
	
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
			$idAnimal = 'ATEST001';
			$date = time();
			$money = 50;
			
			//Exécution de la méthode testée
			$idCombat = $this->_mdlCombat->ajouterCombat($idAnimal, $date, $money);

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
			$this->assertTrue($dateReceived <= time()); //On test si le changement a bien été fait
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
                        $listeCombats = $this->_mdlCombat->listerCombatsParNiveau($level);
        
                        $listeCombatsExpected = array();
                        $listeCombatsExpected[0]['Combat_idCombat'] = 'CA1';
                        $listeCombatsExpected[0]['nomAnimal'] = 'Lapin test';
                        $listeCombatsExpected[0]['vie'] = 15;
                        $listeCombatsExpected[0]['attaque'] = 15;
                        $listeCombatsExpected[0]['defense'] = 15;
                        $listeCombatsExpected[0]['niveau'] = 13;
                                
                        $listeCombatsExpected[1]['Combat_idCombat'] = 'CA2';
                        $listeCombatsExpected[1]['nomAnimal'] = 'T-Rex test';
                        $listeCombatsExpected[1]['vie'] = 10;
                        $listeCombatsExpected[1]['attaque'] = 10;
                        $listeCombatsExpected[1]['defense'] = 10;
                        $listeCombatsExpected[1]['niveau'] = 11;
                                
                        //Assert
                        $this->assertEqual($listeCombatsExpected[0]['Combat_idCombat'],$listeCombats[0]['Combat_idCombat']);
                        $this->assertEqual($listeCombatsExpected[0]['nomAnimal'],$listeCombats[0]['nomAnimal']);
                        $this->assertEqual($listeCombatsExpected[0]['vie'],$listeCombats[0]['vie']);
                        $this->assertEqual($listeCombatsExpected[0]['attaque'],$listeCombats[0]['attaque']);
                        $this->assertEqual($listeCombatsExpected[0]['defense'],$listeCombats[0]['defense']);
                        $this->assertEqual($listeCombatsExpected[0]['niveau'],$listeCombats[0]['niveau']);
                        $this->assertEqual($listeCombatsExpected[1]['Combat_idCombat'],$listeCombats[1]['Combat_idCombat']);
                        $this->assertEqual($listeCombatsExpected[1]['nomAnimal'],$listeCombats[1]['nomAnimal']);
                        $this->assertEqual($listeCombatsExpected[1]['vie'],$listeCombats[1]['vie']);
                        $this->assertEqual($listeCombatsExpected[1]['attaque'],$listeCombats[1]['attaque']);
                        $this->assertEqual($listeCombatsExpected[1]['defense'],$listeCombats[1]['defense']);
                        $this->assertEqual($listeCombatsExpected[1]['niveau'],$listeCombats[1]['niveau']);
                                
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
			$idAction = $this->_mdlCombat->ajouterAction($idCombat, $idAnimal, $idCompetence, $degats, $date);
	
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
	 * Test de getMyTurn de CombatDAO.
	 */
	public function test_getMyTurn()
	{
		//Création d'un combat de test
		$idCombat = 'CTEST001';
		$dateCombat = time();
		$estCommence = true;
		$this->addCombat($idCombat, $dateCombat, $estCommence);
		$idAnimal = 'ATEST001';
		$this->addCombatAnimal($idCombat, $idAnimal);
		$idAnimal2 = 'ATEST002';
		$this->addCombatAnimal($idCombat, $idAnimal2);
		
		//Creation d'actions pour le test
		$idAction = 'ATEST001';
		$idCompetence = 'C0001'; //Attaque
		$degats = 10;
		$date = time();
		$this->addAction($idAction, $idCombat, $idAnimal, $idCompetence, $degats, $date);
	
		$idAction2 = 'ATEST002';
		$idCompetence2 = 'C0002'; //Defense
		$degats2 = 0;
		$date2 = time()+10;
		$this->addAction($idAction2, $idCombat, $idAnimal2, $idCompetence2, $degats2, $date2);
	
		//Execution
		$turnReceived = $this->_mdlCombat->getMyTurn($idCombat, $idAnimal2);
		
		//Asserts
		$this->assertEqual($turnReceived, 'attaque');
		
		//--------------------------------------------------------------------------------------
	
		//Execution
		$turnReceived = $this->_mdlCombat->getMyTurn($idCombat, $idAnimal);
		
		//Asserts
		$this->assertEqual($turnReceived, 'wait');
		
		//--------------------------------------------------------------------------------------
		$idAction3 = 'ATEST003';
		$idCompetence3 = 'C0001'; //Attaque
		$degats3 = 20;
		$date3 = time()+20;
		$this->addAction($idAction3, $idCombat, $idAnimal2, $idCompetence3, $degats3, $date3);
		
		//Execution
		$turnReceived = $this->_mdlCombat->getMyTurn($idCombat, $idAnimal);
		
		//Asserts
		$this->assertEqual($turnReceived, 'defense');
	
		//Suppressions
		$this->deleteAction($idAction);
		$this->deleteAction($idAction2);
		$this->deleteAction($idAction3);
		$this->deleteCombat($idCombat);
	}
	
	/**
	 * Test de la méthode getLastAction() de CombatModele
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
			$action = $this->_mdlCombat->getLastAction($idCombat);
				
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
	 * Test de la méthode calculerDegats() de CombatModele
	 */
	public function test_calculerDegats()
	{
		try
		{
			//Ajout d'animaux de test
			$this->addAnimal('ATEST1', 'JTEST1', 'T-Rex de test', 12, 7, 25, 1);
			$this->addAnimal('ATEST2', 'JTEST2', 'Lapin de test', 20, 13, 16, 1);
			
			//Ajout d'actions de test
			$idCombat = 'CTEST0001';
			
			$actionAttaque['idAction'] = 'ATEST0001';
			$actionAttaque['Combat_idCombat'] = $idCombat;
			$actionAttaque['Animal_idAnimal'] = 'ATEST1';
			$actionAttaque['Competence_idCompetence'] = 'C0005';
			$actionAttaque['degatsProvoques'] = 0;
			$actionAttaque['dateAction'] = 1362649861;
			
			$this->addAction($actionAttaque['idAction'],
							 $actionAttaque['Combat_idCombat'],
					 		 $actionAttaque['Animal_idAnimal'],
					 		 $actionAttaque['Competence_idCompetence'],
							 $actionAttaque['degatsProvoques'], 
							 $actionAttaque['dateAction']);
	
			$actionDefense['idAction'] = 'ATEST0002';
			$actionDefense['Combat_idCombat'] = $idCombat;
			$actionDefense['Animal_idAnimal'] = 'ATEST2';
			$actionDefense['Competence_idCompetence'] = 'C0012';
			$actionDefense['degatsProvoques'] = 0;
			$actionDefense['dateAction'] = 1362649961;
			
			$this->addAction($actionDefense['idAction'],
							 $actionDefense['Combat_idCombat'],
					 		 $actionDefense['Animal_idAnimal'],
					 		 $actionDefense['Competence_idCompetence'],
							 $actionDefense['degatsProvoques'], 
							 $actionDefense['dateAction']);
	
			

			//Execution de la méthode à tester
			$degatsReceived = $this->_mdlCombat->calculerDegats($idCombat, $actionAttaque);
	
			//Asserts
			$this->assertEqual($degatsReceived, 9);
	
			//Suppression
			$this->deleteAnimal('ATEST1');
			$this->deleteAnimal('ATEST2');
			$this->deleteAction($actionAttaque['idAction']);
			$this->deleteAction($actionDefense['idAction']);
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : '.$e->getTraceAsString().'<br/>';
		}
	}
	
	/**
	 * Test de la méthode updateDegatsProvoques() de CombatModele
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
			$this->_mdlCombat->updateDegatsProvoques($idAction, $degatsExpected);
	
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
			$degatsReceived = $this->_mdlCombat->getDegatsProvoques($idAction);
				
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
			$action = $this->_mdlCombat->getAction($idAction);
	
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
	 * Test de la méthode recompenserVainqueurCombat() de CombatDAO
	 */
	public function test_recompenserVainqueurCombat()
	{
		try
		{
			//Variables de test
			$idCombat = "CTEST001";
			
			$idJoueur1 = "JTEST001";
			$credit1 = 1000;
			$dateInscription1 = time() - 10000;
			$sommeEngagee1 = 300;
			$this->addPlayer($idJoueur1, $credit1, $dateInscription1);
			
			$idJoueur2 = "JTEST002";
			$credit2 = 2000;
			$dateInscription2 = time() - 20000;
			$sommeEngagee2 = 100;
			$this->addPlayer($idJoueur2, $credit2, $dateInscription2);
			
			$idAnimal= "ATEST001";
			$proprietaire=$idJoueur1;
			$nomAnimal= "Lapin de test";
			$vie = 10;
			$defense = 20;
			$attaque = 30;
			$niveau = 3;
			$animal1 = new Animal($idAnimal, $proprietaire, $nomAnimal, $vie, $defense, $attaque, $niveau);
			$this->addAnimal($idAnimal, $proprietaire, $nomAnimal, $vie, $defense, $attaque, $niveau);
			
			$idAnimal2= "ATEST002";
			$proprietaire2=$idJoueur2;
			$nomAnimal2= "Lapin de test2";
			$vie2 = 40;
			$defense2 = 50;
			$attaque2 = 60;
			$niveau2 = 12;
			$animal2 = new Animal($idAnimal2, $proprietaire2, $nomAnimal2, $vie2, $defense2, $attaque2, $niveau2);
			$this->addAnimal($idAnimal2, $proprietaire2, $nomAnimal2, $vie2, $defense2, $attaque2, $niveau2);
			
			$this->addCombatAnimal($idCombat, $idAnimal, $sommeEngagee1);
			$this->addCombatAnimal($idCombat, $idAnimal2, $sommeEngagee2);
			
			//Initialisation des variables de session
			@session_start();
			$_SESSION [ 'combat' ][ 'idCombat' ] = $idCombat;
			$_SESSION [ 'combat' ][ 'animal1' ] = $animal1;
			$_SESSION [ 'combat' ][ 'animal2' ] = $animal2;
			
			//Execution de la méthode à tester
			$gainsReceived = $this->_mdlCombat->recompenserVainqueurCombat($idAnimal);
	
			//Asserts
			$this->assertEqual($gainsReceived, 700);
			
			//Vérification de la mise à jour des credits du vainqueur
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT credit FROM t_joueur WHERE idFacebook = :idFb");
			$requete_prepare->bindParam(':idFb', $idJoueur1, PDO::PARAM_STR);
			$requete_prepare->execute();
			$donnees = $requete_prepare->fetch(PDO::FETCH_ASSOC);
			$creditReceived= $donnees[ 'credit' ];

			$this->assertEqual($creditReceived, $credit1+$gainsReceived);
			
			//Vérification du nombre de victoires
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT nbVictoires FROM t_joueur WHERE idFacebook = :idFb");
			$requete_prepare->bindParam(':idFb', $idJoueur1, PDO::PARAM_STR);
			$requete_prepare->execute();
			$donnees = $requete_prepare->fetch(PDO::FETCH_ASSOC);
			$nbVictoiresReceived= $donnees[ 'nbVictoires' ];
			
			$this->assertEqual($nbVictoiresReceived, 1);
			
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT nbVictoires FROM t_animal WHERE idAnimal = :idAnimal");
			$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
			$requete_prepare->execute();
			$donnees = $requete_prepare->fetch(PDO::FETCH_ASSOC);
			$nbVictoiresReceived= $donnees[ 'nbVictoires' ];
				
			$this->assertEqual($nbVictoiresReceived, 1);
			
			//Vérification du nombre de défaites
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT nbDefaites FROM t_joueur WHERE idFacebook = :idFb");
			$requete_prepare->bindParam(':idFb', $idJoueur2, PDO::PARAM_STR);
			$requete_prepare->execute();
			$donnees = $requete_prepare->fetch(PDO::FETCH_ASSOC);
			$nbDefaitesReceived= $donnees[ 'nbDefaites' ];
				
			$this->assertEqual($nbDefaitesReceived, 1);
				
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT nbDefaites FROM t_animal WHERE idAnimal = :idAnimal");
			$requete_prepare->bindParam(':idAnimal', $idAnimal2, PDO::PARAM_STR);
			$requete_prepare->execute();
			$donnees = $requete_prepare->fetch(PDO::FETCH_ASSOC);
			$nbDefaitesReceived= $donnees[ 'nbDefaites' ];
			
			$this->assertEqual($nbDefaitesReceived, 1);
			
			//----------------------------------------------------------------------------------
			
			//Execution de la méthode à tester
			$gainsReceived = $this->_mdlCombat->recompenserVainqueurCombat($idAnimal2);
			
			//Asserts
			$this->assertEqual($gainsReceived, 300);
				
			//Vérification de la mise à jour des credits du vainqueur
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT credit FROM t_joueur WHERE idFacebook = :idFb");
			$requete_prepare->bindParam(':idFb', $idJoueur2, PDO::PARAM_STR);
			$requete_prepare->execute();
			$donnees = $requete_prepare->fetch(PDO::FETCH_ASSOC);
			$creditReceived= $donnees[ 'credit' ];
			
			$this->assertEqual($creditReceived, $credit2+$gainsReceived);

			//Suppression
			
			$this->deletePlayer($idJoueur1);
			$this->deletePlayer($idJoueur2);
			$this->deleteAnimal($idAnimal);
			$this->deleteAnimal($idAnimal2);
			$this->deleteCombat($idCombat);
			
			$_SESSION [ 'combat' ] = array();

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
	public function test_getCombatsParNiveau()
	{
		try
		{
			//Variables de test
			$idCombat1 = "CTEST001";
			$dateCombat1 = time();
			$idCombat2 = "CTEST002";
			$dateCombat2 = time();	
			
			$idJoueur1 = "JTEST002";
			$credit1 = 1000;
			$dateInscription1 = time() - 10000;
			$sommeEngagee1 = 200;
			$this->addPlayer($idJoueur1, $credit1, $dateInscription1);
	
			$idJoueur2 = "JTEST003";
			$credit2 = 2000;
			$dateInscription2 = time() - 20000;
			$sommeEngagee2 = 100;
			$this->addPlayer($idJoueur2, $credit2, $dateInscription2);
	
			$idAnimal1= "ATEST002";
			$proprietaire1=$idJoueur1;
			$nomAnimal1= "Lapin de test";
			$vie1 = 10;
			$defense1 = 20;
			$attaque1 = 30;
			$niveau1 = 3;
			$animal1 = new Animal($idAnimal1, $proprietaire1, $nomAnimal1, $vie1, $defense1, $attaque1, $niveau1);
			$this->addAnimal($idAnimal1, $proprietaire1, $nomAnimal1, $vie1, $defense1, $attaque1, $niveau1);
				
			$idAnimal2= "ATEST003";
			$proprietaire2=$idJoueur2;
			$nomAnimal2= "Lapin de test2";
			$vie2 = 20;
			$defense2 = 15;
			$attaque2 = 40;
			$niveau2 = 4;
			$animal2 = new Animal($idAnimal2, $proprietaire2, $nomAnimal2, $vie2, $defense2, $attaque2, $niveau2);
			$this->addAnimal($idAnimal2, $proprietaire2, $nomAnimal2, $vie2, $defense2, $attaque2, $niveau2);
				
			$this->addCombat($idCombat1, $dateCombat1);
			$this->addCombat($idCombat2, $dateCombat2);
			$this->addCombatAnimal($idCombat1, $idAnimal1, $sommeEngagee1);
			$this->addCombatAnimal($idCombat2, $idAnimal2, $sommeEngagee2);
				
			//Initialisation des variables de session
			@session_start();
			$_SESSION [ 'combat' ][ 'idCombat' ] = $idCombat1;
			$_SESSION [ 'combat' ][ 'animal1' ] = $animal1;
			$_SESSION [ 'combat' ][ 'animal2' ] = $animal2;
				
			//Execution de la méthode à tester
			$combatsReceived = $this->_mdlCombat->getCombatsParNiveau($niveau1);

			//Asserts
			$this->assertEqual($combatsReceived[0]['idAnimal'], $idAnimal1);
			$this->assertEqual($combatsReceived[0]['idCombat'], $idCombat1);
			$this->assertEqual($combatsReceived[0]['idJoueur'], $idJoueur1);
			$this->assertEqual($combatsReceived[0]['nom_animal'], $nomAnimal1);
			$this->assertEqual($combatsReceived[0]['vie_animal'], $vie1);
			$this->assertEqual($combatsReceived[0]['attaque_animal'], $attaque1);
			$this->assertEqual($combatsReceived[0]['defense_animal'], $defense1);
			$this->assertEqual($combatsReceived[0]['niveau_animal'], $niveau1);
			
			$this->assertEqual($combatsReceived[1]['idAnimal'], $idAnimal2);
			$this->assertEqual($combatsReceived[1]['idCombat'], $idCombat2);
			$this->assertEqual($combatsReceived[1]['idJoueur'], $idJoueur2);
			$this->assertEqual($combatsReceived[1]['nom_animal'], $nomAnimal2);
			$this->assertEqual($combatsReceived[1]['vie_animal'], $vie2);
			$this->assertEqual($combatsReceived[1]['attaque_animal'], $attaque2);
			$this->assertEqual($combatsReceived[1]['defense_animal'], $defense2);
			$this->assertEqual($combatsReceived[1]['niveau_animal'], $niveau2);
			//Suppression
			
			$this->deleteCombat($idCombat1);
			$this->deleteCombat($idCombat2);
			$this->deleteAnimal($idAnimal1);
			$this->deleteAnimal($idAnimal2);
			$this->deletePlayer($idJoueur1);
			$this->deletePlayer($idJoueur2);
			
			$_SESSION [ 'combat' ] = array();
		
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
			//Création d'une ligne de test
			$idCombat = "CTEST100";
			$idAnimal = 'ATEST005';
			$sommeEngagee= 100;
			$dateCombat = time();
			
			$this->addCombat($idCombat, $dateCombat);
			$this->addCombatAnimal($idCombat, $idAnimal, $sommeEngagee);
		
			//Exécution de la méthode testée
			$levelReceived = $this->_mdlCombat->deleteCombatByIdCombat($idCombat);
			
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT * FROM t_combat_animal WHERE Combat_idCombat = :idCombat");
			$requete_prepare->bindParam(':idCombat', $idCombat, PDO::PARAM_STR);
			$requete_prepare->execute();
			$donnees = $requete_prepare->fetch(PDO::FETCH_ASSOC);
			
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT * FROM t_combat WHERE idCombat = :idCombat");
			$requete_prepare->bindParam(':idCombat', $idCombat, PDO::PARAM_STR);
			$requete_prepare->execute();
			$donnees2 = $requete_prepare->fetch(PDO::FETCH_ASSOC);
			
			//Assert
			$this->assertNull($donnees[ 'Combat_idCombat' ]);
			$this->assertNull($donnees[ 'Animal_idAnimal' ]);
			$this->assertNull($donnees[ 'sommeEngagee' ]);
			
			$this->assertNull($donnees2[ 'idCombat' ]);
			$this->assertNull($donnees2[ 'dateCombat' ]);
			$this->assertNull($donnees2[ 'estCommence' ]);
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
			$this->_mdlCombat->rejoindreCombat($idCombat, $idAnimal, $money);
	
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
			$idCombat = "CTEST001";
			$estCommence = 1;
	
			//Execution de la méthode à tester
			$this->addCombat($idCombat, $dateCombat);
			$this->_mdlCombat->startCombat($idCombat);
	
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
	public function test_getAllAboutCombat()
	{
		try
		{
			//Ajout de tout ce dont on a besoin
			$dateCombat = 1360698377;
			$idCombat = "CTEST001";
			$estCommence = 1;
			
			$idAnimal1 = 'ATEST100';
			$proprietaire1 = 'JTEST100';
			$nom1 = 'Lapin test';
			$vie1 = 15;
			$defense1 = 15;
			$attaque1 = 15;
			$niveau1 = 13;
			$this->addAnimal($idAnimal1, $proprietaire1, $nom1, $vie1, $defense1, $attaque1, $niveau1);
			
			$idAnimal2 = 'ATEST102';
			$proprietaire2 = 'JTEST102';
			$nom2 = 'T-Rex test';
			$vie2 = 15;
			$defense2 = 14;
			$attaque2 = 15;
			$niveau2 = 12;
			$this->addAnimal($idAnimal2, $proprietaire2, $nom2, $vie2, $defense2, $attaque2, $niveau2);
		
			$this->addCombat($idCombat, $dateCombat);
			$this->addCombatAnimal($idCombat, $idAnimal1);
			$this->addCombatAnimal($idCombat, $idAnimal2);
			
			//Execution de la méthode à tester
			$donnees = $this->_mdlCombat->getAllAboutCombat($idCombat);
			
			$idCombatReceived = $donnees['idCombat'];
			$animal1Received = $donnees['animal1'];
			$animal2Received = $donnees['animal2'];
		
			//Asserts
			$this->assertEqual($idCombat, $idCombatReceived);
			$this->assertEqual($idAnimal1, $animal1Received->getIdAnimal());
			$this->assertEqual($proprietaire1, $animal1Received->getProprietaire());
			$this->assertEqual($nom1, $animal1Received->getNomAnimal());
			$this->assertEqual($vie1, $animal1Received->getVie());
			$this->assertEqual($defense1, $animal1Received->getDefense());
			$this->assertEqual($attaque1, $animal1Received->getAttaque());
			$this->assertEqual($niveau1, $animal1Received->getNiveau());
			
			$this->assertEqual($idAnimal2, $animal2Received->getIdAnimal());
			$this->assertEqual($proprietaire2, $animal2Received->getProprietaire());
			$this->assertEqual($nom2, $animal2Received->getNomAnimal());
			$this->assertEqual($vie2, $animal2Received->getVie());
			$this->assertEqual($defense2, $animal2Received->getDefense());
			$this->assertEqual($attaque2, $animal2Received->getAttaque());
			$this->assertEqual($niveau2, $animal2Received->getNiveau());
		
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : '.$e->getTraceAsString().'<br/>';
		}
		//On enlève la ligne insérée pour le test
		$this->deleteCombat($idCombat);
		$this->deleteAnimal($idAnimal1);
		$this->deleteAnimal($idAnimal2);
	}
	
	/**
	 * 
	 */
	public function test_isThereAnOpponent()
	{
		try
		{
			//Ajout d'une action de test
			$dateCombat = 1360698377;
			$idCombat = "CTEST105";
			$estCommence = 1;
			$idAnimal1 = "ATEST005";
			$idAnimal2 = "ATEST006";
		
			//Execution de la méthode à tester
			$this->addCombat($idCombat, $dateCombat);
			$this->addCombatAnimal($idCombat, $idAnimal1);
			$this->addCombatAnimal($idCombat, $idAnimal2);
				
			$isOpponnentReceived = $this->_mdlCombat->isThereAnOpponent($idCombat);
		
			//Verification
			$isOpponentExpected = true;
			
			//Asserts
			$this->assertEqual($isOpponnentReceived, $isOpponentExpected);

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