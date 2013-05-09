<?php
require_once './../mapping/AnimalDAO.php';
require_once './../modeles/TransactionModele.php';

/**
 *
 * Modèle Animal. Intéragit avec le DAO et s'occupe de faire les vérifications.
 *
 */
class AnimalModele
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
		$this->_dao = new AnimalDAO();
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
	 * Permet d'obtenir le DAO de l'Animal.
	 * @return Le DAO de l'Animal.
	 */
	public function getDAOAnimal()
	{
		return $this->_dao;
	}

	/**
	 * Permet de changer le propriétaire de l'animal passé en paramètre.
	 * @param L'identifiant du nouveau propriétaire.
	 * @param L'identifiant de l'animal.
	 * @return true si la modification a été faite.
	 * 		   false sinon
	 */
	public function changerProprietaire($idProprietaire, $idAnimal)
	{
		//Appelle la méthode updateProprietaire du AnimalDAO en passant en paramètre les infos nécessaires
		return $this->_dao->updateProprietaire($idProprietaire, $idAnimal);
	}

	/**
	 * Permet d'obtenir tout les animaux qui sont en vente.
	 */
	public function getAnimauxEnVente()
	{
		return $this->_dao->getAnimauxEnVente();
	}
	
	/**
	 * Assign dans l'objet Smarty passé en paramètre les animaux en vente.
	 * @param $smarty L'objet Smarty sur lequel faire les assignations.
	 * @return L'objet Smarty assigné
	 */
	public function assignAnimauxEnVente($smarty)
	{
		//Animaux sans proprietaire
		$animals = array();
		$animals = $this->getAnimauxEnVente();
		$listeAnimaux = array();
	
		foreach ($animals as $animal) {
			$tab['idAnimal'] = $animal->getIdAnimal();
			$tab['nomAnimal'] = $animal->getNomAnimal();
			$tab['proprietaire'] = $animal->getProprietaire();
			$tab['vie'] = $animal->getVie();
			$tab['attaque'] = $animal->getAttaque();
			$tab['defense'] = $animal->getDefense();
	
			array_push($listeAnimaux, $tab);
		}
		$smarty->assign("animals",$listeAnimaux);
		return $smarty;
	}
	
	
	/**
	 * Permet d'obtenir un animal en base d'après son identifiant.
	 * @param StringidAnimal L'identifiant de l'animal
	 * @return l'animal correspondant
	 */
	public function getAnimal($idAnimal)
	{
		return $this->_dao->getAnimal($idAnimal);
	}
	
	/**
	 * Permet d'inscrire un animal à un entrainement.
	 * @param String $idAnimal
	 * @param String $idEntrainement
	 */
	public function inscriptionEntrainement($idAnimal, $idEntrainement)
	{
		//Ajoute la ligne dans la table intermédiaire Entrainement_Animal
		$dateInscription = time();
		$this->_dao->inscriptionEntrainement($idAnimal, $idEntrainement, $dateInscription);
		return $dateInscription;
	}
	
	/**
	 * Permet de désiinscrire un animal à un entrainement.
	 * @param String $idAnimal
	 * @param String $idEntrainement
	 */
	public function desinscriptionEntrainement($idAnimal, $idEntrainement)
	{
		return $this->_dao->desinscriptionEntrainement($idAnimal, $idEntrainement);
	}
	
	/**
	 * Permet de savoir si un animal est disponible ou occupé.
	 * @param String $idAnimal L'identifiant de l'animal
	 * @return true si l'animal est disponible
	 * 	       false si l'animal est occupé
	 */
	public function isAvailable($idAnimal)
	{
		$rep = true;
		
		//On vérifie s'il l'animal est actuellement impliqué dans un entrainement
		if($this->_dao->isTraining($idAnimal))
			$rep = false;
		
		//On vérifie s'il l'animal est actuellement impliqué dans une transaction
		if(TransactionModele::getInstance()->isSelling($idAnimal))
			$rep = false;
		
		return $rep;
	}
	
	/**
	 * Permet de savoir le niveau de l'animal dont l'id est passé en paramètre
	 * @param String $idAnimal
	 */
	public function getLevelById($idAnimal)
	{
		return $this->_dao->getLevelById($idAnimal);
	}
	
	
	/**
	 * Permet d'obtenir les competences d'un animal.
	 * @param String $idAnimal L'identifiant de l'animal
	 * @return Un tableau contenant les compétences
	 */
	public function getCompetencesByAnimal($idAnimal)
	{
		return $this->_dao->getCompetencesByAnimal($idAnimal);
	}
	
	/**
	 * Permet d'obtenir la compétence correspondant à l'identifiant donné.
	 * @param String $idCompetence L'identifiant de la compétence
	 * @return La compétence
	 */
	public function getCompetenceById($idCompetence)
	{
		return $this->_dao->getCompetenceById($idCompetence);
	}
	
	/**
	 * Effectue les traitements nécessaires à la mort d'un animal. 
	 * Met à 0 la vie de l'animal en question et supprime la relation avec son propriétaire.
	 * @param String $idAnimal L'identifiant de l'animal mort
	 * @return true si les traitements ont été effectué avec succès
	 * 		   false sinon
	 */
	public function declarerAnimalMort($idAnimal)
	{
		//Met la vie à 0 de l'animal
		$bool1 = $this->_dao->updateVie($idAnimal, 0);
		
		//On supprime la relation avec le propriétaire
		$bool2 = $this->_dao->deleteRelationAnimalProprietaire($idAnimal);
		
		return $bool1 && $bool2;
	}
}


?>