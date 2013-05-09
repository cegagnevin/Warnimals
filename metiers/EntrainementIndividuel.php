<?php
require_once 'Entrainement.php';

/**
 * Cette classe abstraite représente un entrainement individuel.
 */
class EntrainementIndividuel extends Entrainement
{
	/** Animal suivant l'entrainement */
	private $_animal;
	
	/**
	 * Constructeur par défaut d'un entrainement.
	 * @param $idEntrainement Identifiant de l'entainement
	 * @param $duree Durée de l'entrainement
	 * @param $prix Prix de l'entrainement
	 * @param $dateDebut Date de début de l'entrainement
	 * @param $offre L'offre
	 * @param $animal L'animal qui suit l'entrainement.
	 */
	public function __construct($idEntrainement, $duree, $prix, $dateDebut,OffreEntrainement $offre, $animal)
	{
		parent::__construct($idEntrainement, $duree, $prix, $dateDebut, $offre);
		$this->setAnimal($animal);
	}
	
	/**
	 * Permet d'obtenir le type d'entrainement.
	 * @return Le type d'entrainement
	 */
	public function getType()
	{
		return 'individuel';
	}
	
	/**
	 * Permet d'obtenir l'identifiant de l'animal suivant l'entrainement.
	 * @return La valeur de l'identifiant de l'animal suivant l'entrainement.
	 */
	public function getAnimal()
	{
		return $this->_animal;
	}
	
	/**
	 * Permet de modifier l'animal qui suit l'entrainement.
	 * @param duree L'animal qui suit l'entrainement.
	 */
	public function setAnimal($animal)
	{
		$this->_animal = $animal;
	}
	
	/**
	 * Décrit l'entrainement individuel par une chaine de caractères.
	 */
	public function __toString()
	{
		$chaine = parent::__toString();
		$chaine .= '. Animal : '.$this->getAnimal();
		return $chaine;
	}
		
}


?>
