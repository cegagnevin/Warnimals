<?php

require_once './../../lib/simpletest/autorun.php';
require_once './../mapping/TransactionDAO.php';

/**
 *
 * Classe de test de la classe TransactionDAO (package mapping)
 *
 */
class TransactionDAOTestCase extends UnitTestCase
{
	/** DAO */
	private $_dao;
	
	/** DAO Transaction à tester */
	private $_daoTransaction;

	/**
	 * Constructeur par défaut. Instancie le DAO.
	 */
	public function __construct()
	{
		$this->_dao = new DAO();
		$this->_daoTransaction = new TransactionDAO();
	}
	
	public function __destruct()
	{
		$this->_dao->__destruct();
		$this->_daoTransaction->__destruct();
	}
	
	/**
	 * Test du constructeur de TransactionDAOTestCase.
	 */
	function test_constructeur_TransactionDAOTestCase()
	{
		//Asserts
		$this->assertNotNull($this->_dao);
		$this->assertNotNull($this->_daoTransaction);
		$this->assertTrue($this->_dao instanceof DAO);
		$this->assertTrue($this->_daoTransaction instanceof TransactionDAO);
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
	 * Permet d'ajouter une transaction de test dans la base de données et d'être sur que l'ajout ait eu lieu.
	 * @param String $idTransaction
	 * @param String $idAnimal
	 * @param int $dateTransaction
	 * @param int $prix
	 * @param int $vendu
	 */
	private function addTransaction($idTransaction, $idAnimal, $dateTransaction, $prix, $vendu)
	{
		try
		{
			//Insertion
			$requete_prepare = $this->_dao->getConnexion()->prepare("INSERT INTO t_transaction (idTransaction, Animal_idAnimal, dateTransaction, prixVente, vendu) VALUES (:id, :animal, :date, :prix, :vendu)");
			$requete_prepare->bindParam(':id', $idTransaction, PDO::PARAM_STR);
			$requete_prepare->bindParam(':animal', $idAnimal, PDO::PARAM_STR);
			$requete_prepare->bindParam(':date', $dateTransaction, PDO::PARAM_INT);
			$requete_prepare->bindParam(':prix', $prix, PDO::PARAM_INT);
			$requete_prepare->bindParam(':vendu', $vendu, PDO::PARAM_INT);
			$bool = $requete_prepare->execute();
				
			//Select pour vérification
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT * FROM t_transaction WHERE idTransaction = :idTransaction");
			$requete_prepare->bindParam(':idTransaction', $idTransaction, PDO::PARAM_STR);
			$requete_prepare->execute();
			$donnees = $requete_prepare->fetch(PDO::FETCH_ASSOC);
			$idTransactionReceived = $donnees['idTransaction'];
			$idAnimalReceived = $donnees['Animal_idAnimal'];
			$dateTransactionReceived = $donnees['dateTransaction'];
			$prixVenduReceived = $donnees['prixVente'];
			$venduReceived = $donnees['vendu'];
				
			//Asserts
			$this->assertTrue($bool);
			$this->assertEqual($idTransaction, $idTransactionReceived);
			$this->assertEqual($idAnimal, $idAnimalReceived);
			$this->assertEqual($dateTransaction, $dateTransactionReceived);
			$this->assertEqual($prix, $prixVenduReceived);
			$this->assertEqual($vendu, $venduReceived);
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : '.$e->getTraceAsString().'<br/>';
		}
	}
	
	/**
	 * Permet de supprimer une transaction de test de la base de données et d'être sur que la suppression ait eu lieu.
	 * @param String $idTransaction
	 */
	private function deleteTransaction($idTransaction)
	{
		try
		{
			//Suppression
			$requete_prepare = $this->_dao->getConnexion()->prepare("DELETE FROM t_transaction WHERE idTransaction = :idTransaction");
			$requete_prepare->bindParam(':idTransaction', $idTransaction, PDO::PARAM_STR);
			$bool = $requete_prepare->execute();
	
			//Select pour vérification
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT COUNT(*) as COUNT FROM t_transaction WHERE idTransaction = :idTransaction");
			$requete_prepare->bindParam(':idTransaction', $idTransaction, PDO::PARAM_STR);
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
	
	// -------------------------------- TESTS DE LA CLASSE TRANSACTIONDAO ---------------------------------------
	
	
	/**
	 * Test de la méthode : createTransaction().
	 */
	function test_createTransaction()
	{
		try
		{
			//Exécution de la méthode testée
			$idAnimal = "ATEST001";
			$dateTransaction = time();
			$prix = 350;
			$vendu = 0;
			$idTransactionCree = $this->_daoTransaction->createTransaction($idAnimal, $dateTransaction, $prix);
				
			//Test si la requete a été effectuée
			$this->assertNotEqual($idTransactionCree, -1);
				
			//Récupération de l'animal créé
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT * FROM t_transaction WHERE idTransaction = :id");
			$requete_prepare->bindParam(':id', $idTransactionCree, PDO::PARAM_STR);
			$requete_prepare->execute();
			$donnees = $requete_prepare->fetch(PDO::FETCH_ASSOC);
			$idTransactionReceived = $donnees['idTransaction'];
			$idAnimalReceived = $donnees['Animal_idAnimal'];
			$dateTransactionReceived = $donnees['dateTransaction'];
			$prixVenduReceived = $donnees['prixVente'];
			$venduReceived = $donnees['vendu'];
				
			//Assert
			$this->assertEqual($idTransactionCree, $idTransactionReceived);
			$this->assertEqual($idAnimal, $idAnimalReceived);
			$this->assertEqual($dateTransaction, $dateTransactionReceived);
			$this->assertEqual($prix, $prixVenduReceived);
			$this->assertEqual($vendu, $venduReceived);
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : '.$e->getTraceAsString().'<br/>';
		}
		//Suppression de la transaction créée en base pour le test
		$this->deleteTransaction($idTransactionCree);
	}
	
	/**
	 * Test de la méthode : deleteTransaction().
	 */
	public function test_deleteTransaction()
	{
		try
		{
			//Creation d'une transaction de test
			$idTransaction = "TTEST001";
			$idAnimal = "ATEST001";
			$dateTransaction = time();
			$prix = 230;
			$vendu = 0;
			$this->addTransaction($idTransaction, $idAnimal, $dateTransaction, $prix, $vendu);
			
			//Execution de la méthode
			$this->_daoTransaction->deleteTransaction($idTransaction);
	
			//Select pour vérification
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT COUNT(*) as COUNT FROM t_transaction WHERE idTransaction = :idTransaction");
			$requete_prepare->bindParam(':idTransaction', $idTransaction, PDO::PARAM_STR);
			$requete_prepare->execute();
			$count = $requete_prepare->fetch(PDO::FETCH_OBJ)->COUNT;
	
			//Asserts
			$this->assertEqual($count, 0);
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : '.$e->getTraceAsString().'<br/>';
		}
	}
	
	/**
	 * Test de la méthode : getTransactionById().
	 */
	public function test_getTransactionById()
	{
		try
		{
			//Creation d'une transaction de test
			$idTransaction = "TTEST001";
			$idAnimal = "ATEST001";
			$dateTransaction = time();
			$prix = 230;
			$vendu = 0;
			$this->addTransaction($idTransaction, $idAnimal, $dateTransaction, $prix, $vendu);
				
			//Execution de la méthode
			$transactionReceived = $this->_daoTransaction->getTransactionById($idTransaction);
	
			//Asserts
			$this->assertEqual($transactionReceived['idTransaction'], $idTransaction);
			$this->assertEqual($transactionReceived['Animal_idAnimal'], $idAnimal);
			$this->assertEqual($transactionReceived['dateTransaction'], $dateTransaction);
			$this->assertEqual($transactionReceived['prixVente'], $prix);
			$this->assertEqual($transactionReceived['vendu'], $vendu);
			
			//Suppression
			$this->deleteTransaction($idTransaction);
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : '.$e->getTraceAsString().'<br/>';
		}
	}
	
	
	/**
	 * Test de la méthode : updateVendu().
	 */
	public function test_updateVendu()
	{
		try
		{
			//Creation d'une transaction de test
			$idTransaction = "TTEST001";
			$idAnimal = "ATEST001";
			$dateTransaction = time();
			$prix = 230;
			$vendu = 0;
			$this->addTransaction($idTransaction, $idAnimal, $dateTransaction, $prix, $vendu);
				
			//Execution de la méthode
			$vendu = 1;
			$this->_daoTransaction->updateVendu($idTransaction, $vendu);
	
			//Select pour vérification
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT vendu FROM t_transaction WHERE idTransaction = :idTransaction");
			$requete_prepare->bindParam(':idTransaction', $idTransaction, PDO::PARAM_STR);
			$requete_prepare->execute();
			$donnees = $requete_prepare->fetch(PDO::FETCH_ASSOC);
			$venduReceived = $donnees['vendu'];
	
			//Asserts
			$this->assertEqual($venduReceived, $vendu);
			
			//Suppression
			$this->deleteTransaction($idTransaction);
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : '.$e->getTraceAsString().'<br/>';
		}
	}
	
	/**
	 * Test de la méthode : listerTransactionsEnCours().
	 */
	public function test_listerTransactionsEnCours()
	{
		try
		{
			//Creation de 2 transactions de test
			$idTransaction = "TTEST001";
			$idAnimal = "A1";
			$dateTransaction = time()+20;
			$prix = 230;
			$vendu = 0;
			$this->addTransaction($idTransaction, $idAnimal, $dateTransaction, $prix, $vendu);
			
			$idTransaction2 = "TTEST002";
			$idAnimal2 = "A2";
			$dateTransaction2 = time();
			$prix2 = 290;
			$this->addTransaction($idTransaction2, $idAnimal2, $dateTransaction2, $prix2, $vendu);
	
			//Execution de la méthode
			$transactionReceived = $this->_daoTransaction->listerTransactionsEnCours();
	
			//Asserts
			$this->assertEqual(count($transactionReceived), 2);
			
			$this->assertEqual($transactionReceived[0]['idTransaction'], $idTransaction);
			$this->assertEqual($transactionReceived[0]['Animal_idAnimal'], $idAnimal);
			$this->assertEqual($transactionReceived[0]['dateTransaction'], $dateTransaction);
			$this->assertEqual($transactionReceived[0]['vendu'], $vendu);
			$this->assertEqual($transactionReceived[0]['prixVente'], $prix);
			
			$this->assertEqual($transactionReceived[1]['idTransaction'], $idTransaction2);
			$this->assertEqual($transactionReceived[1]['Animal_idAnimal'], $idAnimal2);
			$this->assertEqual($transactionReceived[1]['dateTransaction'], $dateTransaction2);
			$this->assertEqual($transactionReceived[1]['vendu'], $vendu);
			$this->assertEqual($transactionReceived[1]['prixVente'], $prix2);
				
			//Suppression
			$this->deleteTransaction($idTransaction);
			$this->deleteTransaction($idTransaction2);
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : '.$e->getTraceAsString().'<br/>';
		}
	}
	
	/**
	 * Test de la méthode : isSelling().
	 */
	public function test_isSelling()
	{
		try
		{
			//Creation d'une transaction de test
			$idTransaction = "TTEST001";
			$idAnimal = "ATEST001";
			$dateTransaction = time();
			$prix = 230;
			$vendu = 0;
			$this->addTransaction($idTransaction, $idAnimal, $dateTransaction, $prix, $vendu);
	
			//Execution de la méthode
			$boolReceived = $this->_daoTransaction->isSelling($idAnimal);
	
			//Asserts
			$this->assertEqual($boolReceived, true);
				
			//Suppression
			$this->deleteTransaction($idTransaction);
			
			//Execution de la méthode
			$boolReceived = $this->_daoTransaction->isSelling($idAnimal);
			
			//Asserts
			$this->assertEqual($boolReceived, false);
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : '.$e->getTraceAsString().'<br/>';
		}
	}
	
	/**
	 * Test de la méthode : getTransactionEnCoursByAnimal().
	 */
	public function test_getTransactionEnCoursByAnimal()
	{
		try
		{
			//Creation d'une transaction de test
			$idTransaction = "TTEST001";
			$idAnimal = "ATEST001";
			$dateTransaction = time();
			$prix = 230;
			$vendu = 0;
			$this->addTransaction($idTransaction, $idAnimal, $dateTransaction, $prix, $vendu);
	
			//Execution de la méthode
			$transactionReceived = $this->_daoTransaction->getTransactionEnCoursByAnimal($idAnimal);
	
			//Asserts
			$this->assertEqual($transactionReceived['idTransaction'], $idTransaction);
			$this->assertEqual($transactionReceived['Animal_idAnimal'], $idAnimal);
			$this->assertEqual($transactionReceived['dateTransaction'], $dateTransaction);
			$this->assertEqual($transactionReceived['vendu'], $vendu);
			$this->assertEqual($transactionReceived['prixVente'], $prix);
	
			//Suppression
			$this->deleteTransaction($idTransaction);
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : '.$e->getTraceAsString().'<br/>';
		}
	}
}

?>