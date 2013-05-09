<?php
require_once 'DAO.php';



/**
 *
 * DAO d'une transaction
 *
 */
class TransactionDAO extends DAO
{
	/**
	 * Constructeur. Initialise la connexion à la base de données.
	 * @throws PDOException Lorsqu'un problème survient lors de l'utilisation d'un objet PDO.
	 */
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Destructeur. Ferme la connexion à la base de données.
	 */
	public function __destruct()
	{
		parent::__destruct();
	}
	
	
	/**
	 * Permet d'obtenir un nouvel identifiant unique.
	 * @return Un identifiant unique pour une transaction.
	 */
	private function getNewIdTransaction()
	{
		$requete_prepare = parent::getConnexion()->prepare("SELECT idTransaction FROM t_transaction");
		$requete_prepare->execute();
		$requete_prepare->setFetchMode(PDO::FETCH_OBJ);
		
		//On récupère le plus grand identifiant (sans le T)
		$last = 0;
		while($id = $requete_prepare->fetch())
		{
			$id = $id->idTransaction;
			$num = intval(substr($id, 1, strlen($id)-1));
			if($num > $last)
				$last=$num;
		}
		
		$newId = 'T1';
		if($last != 0) //On incrémente l'identifiant de 1
		{
			$newId = 'T'.($last+1);
		}
		return $newId;
	}
	
	
	/**
	  Créé une transaction dans la base de données.
	 * @param idAnimal L'identifiant de l'animal en vente
	 * @param dateTransaction Date de création de la transaction
	 * @param prixVente Prix de vente de l'animal
	 * @return l'identifiant de la transaction créée.
	 * 		   -1 si la création a échouée
	 */
	public function createTransaction($idAnimal, $dateTransaction, $prixVente)
	{
		$requete_prepare = parent::getConnexion()->prepare("INSERT INTO t_transaction (idTransaction, Animal_idAnimal, dateTransaction, prixVente, vendu) VALUES (:id, :animal, :date, :prix, 0)");
		$identifiant = $this->getNewIdTransaction();
		$requete_prepare->bindParam(':id', $identifiant, PDO::PARAM_STR);
		$requete_prepare->bindParam(':animal', $idAnimal, PDO::PARAM_STR);
		$requete_prepare->bindParam(':date', $dateTransaction, PDO::PARAM_INT);
		$requete_prepare->bindParam(':prix', $prixVente, PDO::PARAM_INT);
		$bool = $requete_prepare->execute();
	
		if($bool)
			return $identifiant;
		else return -1;
	}
	
	/**
	 * Permet de supprimer une transaction.
	 * @param String $idTransaction
	 * @return bool true si la suppression a eu lieu
	 *         false sinon
	 */
	public function deleteTransaction($idTransaction)
	{
		$requete_prepare = parent::getConnexion()->prepare("DELETE FROM t_transaction WHERE idTransaction = :idTransaction");
		$requete_prepare->bindParam(':idTransaction', $idTransaction, PDO::PARAM_STR);
		$bool = $requete_prepare->execute();

		return $bool;
	}
	
	/**
	 * Permet d'obtenir une transaction par son identifiant.
	 * @param String $idTransaction
	 * @return array Un tableau contenant la transaction correspondante.
	 */
	public function getTransactionById($idTransaction)
	{
		$requete_prepare = parent::getConnexion()->prepare("SELECT * FROM t_transaction WHERE idTransaction = :idTransaction");
		$requete_prepare->bindParam(':idTransaction', $idTransaction, PDO::PARAM_STR);
		$requete_prepare->execute();
	
		$donnees = $requete_prepare->fetch(PDO::FETCH_ASSOC);
		return $donnees;
	}
	
	/**
	 * Permet de déclarer une transaction comme achevée.
	 * @param String $idTransaction
	 * @param Int $vendu
	 * @return bool true si la modification a eu lieue
	 *         false sinon
	 */
	public function updateVendu($idTransaction, $vendu)
	{
		$requete_prepare = parent::getConnexion()->prepare("UPDATE t_transaction SET vendu = :vendu WHERE idTransaction = :idTransaction");
		$requete_prepare->bindParam(':vendu', $vendu, PDO::PARAM_INT);
		$requete_prepare->bindParam(':idTransaction', $idTransaction, PDO::PARAM_STR);
		$bool = $requete_prepare->execute();
	
		return $bool;
	}
	
	/**
	 * Permet de lister les transactions non achevées.
	 * @return array Un tableau contenant les transactions non achevées
	 */
	public function listerTransactionsEnCours()
	{
		$requete_prepare = parent::getConnexion()->prepare("SELECT * FROM t_transaction, t_animal, t_raceanimal WHERE t_transaction.Animal_idAnimal = t_animal.idAnimal AND t_animal.RaceAnimal_race = t_raceanimal.idRace AND vendu = 0 ORDER BY dateTransaction DESC");
		$bool = $requete_prepare->execute();
		
		return $requete_prepare->fetchAll(PDO::FETCH_ASSOC);
	}

	/**
	 * Permet de savoir si l'animal passé en parametre est actuellement impliqué dans une vente
	 * @param String idAnimal L'identifiant de l'animal concerné
	 * @return bool True s'il est impliqué, False sinon
	 */
	public function isSelling($idAnimal)
	{
		$requete_prepare = parent::getConnexion()->prepare("SELECT COUNT(*) as COUNT FROM t_transaction WHERE Animal_idAnimal = :idAnimal AND vendu = 0");
		$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
		$requete_prepare->execute();
		$donnees = $requete_prepare->fetch(PDO::FETCH_ASSOC);
		
		if($donnees['COUNT'] == 0)
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	
	/**
	 * Permet d'obtenir une transaction en cours à partir de l'identifiant d'un animal.
	 * @param String idAnimal L'identifiant de l'animal concerné
	 * @return array Un tableau contenant la transaction en cours
	 */
	public function getTransactionEnCoursByAnimal($idAnimal)
	{
		$requete_prepare = parent::getConnexion()->prepare("SELECT * FROM t_transaction WHERE Animal_idAnimal = :idAnimal AND vendu = 0");
		$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
		$requete_prepare->execute();
		return $requete_prepare->fetch(PDO::FETCH_ASSOC);
	}
}



?>