<?php
require_once './../mapping/TransactionDAO.php';

/**
 *
 * Modèle Transaction. Intéragit avec le DAO et s'occupe de faire les vérifications.
 *
 */
class TransactionModele
{
	/** Instance unique */
	private static $_instance;
	
	/** Le DAO Animal */
	private $_dao = null;
		
	
	/**
	 * Constructeur.
	 */
	private function __construct()
	{
		$this->_dao = new TransactionDAO();
	}
	
	/**
	 * Renvoi de l'instance et initialisation si nécessaire.
	 * @return L'instance du controleur
	 */
	public static function getInstance ()
	{
		if (self::$_instance == null)
			self::$_instance = new self();
		return self::$_instance;
	}
	
	/**
	 * Permet d'obtenir le DAO de Transaction.
	 * @return Le DAO de Transaction.
	 */
	public function getDAOTransaction()
	{
		return $this->_dao;
	}

	/**
	  Créé une transaction dans la base de données.
	 * @param idAnimal Animal en vente
	 * @param prixVente Prix de vente de l'animal
	 * @return l'identifiant de la transaction créée.
	 * 		   -1 si la création a échouée
	 */
	public function mettreEnVenteAnimal($idAnimal, $prixVente)
	{
		$dateTransaction = time();
		return $this->_dao->createTransaction($idAnimal, $dateTransaction, $prixVente);
	}
	
	/**
	 * Permet de supprimer une transaction.
	 * @param String $idTransaction
	 * @return bool true si la suppression a eu lieu
	 *         false sinon
	 */
	public function retirerDeLaVenteAnimal($idTransaction)
	{
		return $this->_dao->deleteTransaction($idTransaction);
	}
	
	/**
	 * Permet de déclarer une transaction comme achevée et de changer le propriétaire de l'animal en question.
	 * @param String $idTransaction
	 * @return bool true si la modification a eu lieue
	 *         false sinon
	 */
	public function finaliserTransaction($idTransaction)
	{
		//On indique la transaction comme achevée
		$bool1 = $this->_dao->updateVendu($idTransaction, 1);
	}
	
	/**
	 * Permet de lister les transactions non achevées.
	 * @return array Un tableau contenant les transactions non achevées
	 */
	public function listerTransactionsEnCours()
	{
		return $this->_dao->listerTransactionsEnCours();
	}
	
	/**
	 * Permet d'obtenir une transaction par son identifiant.
	 * @param String $idTransaction
	 * @return array Un tableau contenant la transaction correspondante.
	 */
	public function getTransactionById($idTransaction)
	{
		return $this->_dao->getTransactionById($idTransaction);
	}
	
	/**
	 * Permet de savoir si l'animal passé en parametre est actuellement impliqué dans une vente
	 * @param String idAnimal L'identifiant de l'animal concerné
	 * @return bool True s'il est impliqué, False sinon
	 */
	public function isSelling($idAnimal)
	{
		return $this->_dao->isSelling($idAnimal);
	}
	
	/**
	 * Permet d'obtenir une transaction en cours à partir de l'identifiant d'un animal.
	 * @param String idAnimal L'identifiant de l'animal concerné
	 * @return array Un tableau contenant la transaction en cours
	 */
	public function getTransactionEnCoursByAnimal($idAnimal)
	{
		return $this->_dao->getTransactionEnCoursByAnimal($idAnimal);
	}
	
}


?>