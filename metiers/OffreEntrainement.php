<?php

/**
 * Cette classe représente une offre pour un entrainement.
 */
class OffreEntrainement 
{
	/** Identifiant de l'offre */
	private $_idOffre;
	/** Points d'attaque ajoutés */
	private $_attaqueOffre;
	/** Points de défense ajoutés */
	private $_defenseOffre;
	/** Points de vie ajoutés */
	private $_vieOffre;
	/** Passage du niveau supérieur */
	private $_levelUp;
	
	/**
	 * Constructeur par défaut d'une offre.
	 * @param $idEntrainement Identifiant de l'entainement
	 * @param $duree Durée de l'entrainement
	 * @param $prix Prix de l'entrainement
	 * @param $dateDebut Date de début de l'entrainement
	 * @param $idOffre Identifiant de l'offre
	 * @param $animauxInscrits Animaux inscrits à l'entrainement.
	 */
	public function __construct($idOffre, $attaqueOffre, $defenseOffre, $vieOffre, $levelUp)
	{
		$this->setIdOffre($idOffre);
		$this->setAttaqueOffre($attaqueOffre);
		$this->setDefenseOffre($defenseOffre);
		$this->setVieOffre($vieOffre);
		$this->setLevelUp($levelUp);
	}
	
	/**
	 * Permet d'obtenir l'identifiant de l'offre.
	 * @return L'identifiant de l'offre.
	 */
	public function getIdOffre()
	{
		return $this->_idOffre;
	}
	
	/**
	 * Permet d'obtenir le nombre de points d'attaque ajoutés.
	 * @return Le nombre de points d'attaque ajoutés.
	 */
	public function getAttaqueOffre()
	{
		return $this->_attaqueOffre;
	}
	
	/**
	 * Permet d'obtenir le nombre de points de défense ajoutés.
	 * @return Le nombre de points de défense ajoutés.
	 */
	public function getDefenseOffre()
	{
		return $this->_defenseOffre;
	}
	
	/**
	 * Permet d'obtenir le nombre de points de vie ajoutés.
	 * @return Le nombre de points de vie ajoutés.
	 */
	public function getVieOffre()
	{
		return $this->_vieOffre;
	}
	
	/**
	 * Permet de savoir si l'animal passera au niveau supérieur ou non.
	 * @return 0 Ne passe pas au niveau supérieur
	 * 		   1 Passe au niveau supérieur
	 */
	public function getLevelUp()
	{
		return $this->_levelUp;
	}
	
	/**
	 * Correspond à la somme des points (attaqueOffre + defenseOffre + vieOffre + levelUp)
	 * @return La somme des points
	 */
	public function getSommePoints()
	{
		return $this->_attaqueOffre + $this->getDefenseOffre() + $this->getVieOffre() + $this->getLevelUp();
	}
	
	/**
	 * Permet de modifier l'identifiant de l'offre
	 * @param idOffre Le nouvel identifiant
	 */
	public function setIdOffre($idOffre)
	{
		$this->_idOffre = $idOffre;
	}
	
	/**
	 * Permet de modifier le nombre de points d'attaque ajoutés par l'offre
	 * @param attaqueOffre Le nouveau nombre de points d'attaque à ajouter
	 */
	public function setAttaqueOffre($attaqueOffre)
	{
		$this->_attaqueOffre = $attaqueOffre;
	}
	
	/**
	 * Permet de modifier le nombre de points de défense ajoutés par l'offre
	 * @param defenseOffre Le nouveau nombre de points de défense à ajouter
	 */
	public function setDefenseOffre($defenseOffre)
	{
		$this->_defenseOffre = $defenseOffre;
	}
	
	/**
	 * Permet de modifier le nombre de points de vie ajoutés par l'offre
	 * @param vieOffre Le nouveau nombre de points de vie à ajouter
	 */
	public function setVieOffre($vieOffre)
	{
		$this->_vieOffre = $vieOffre;
	}
	
	/**
	 * Permet de modifier la propriété LevelUp [0;1]
	 * @param levelUp La nouvel valeur pour la propriété LevelUp
	 */
	public function setLevelUp($levelUp)
	{
		$this->_levelUp = $levelUp;
	}
	
	/**
	 * Décrit l'offre par une chaine de caractères.
	 */
	public function __toString()
	{
		$chaine = "Numero d'offre : ".$this->getIdOffre().". ";
		$chaine .= "Attaque : ".$this->getAttaqueOffre().". ";
		$chaine .= "Defense : ".$this->getDefenseOffre().". ";
		$chaine .= "Vie : ".$this->getVieOffre().". ";
		$chaine .= "LevelUp : ".$this->getLevelUp();

		return $chaine;
	}
		
}


?>
