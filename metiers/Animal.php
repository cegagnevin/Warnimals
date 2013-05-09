<?php

/**
 *
* Classe représentant un animal sous Warnimals.
*
*/
class Animal
{
	/** Identifiant de l'animal (unique) */
	private $_idAnimal;
	/** Nom de l'animal */
	private $_nomAnimal;
	/** Propriétaire de l'animal (id Facebook)*/
	private $_proprietaire;
	/** Vie de l'animal */
	private $_vie;
	/** Défense de l'animal */
	private $_defense;
	/** Attaque de l'animal */
	private $_attaque;
	/** Niveau de l'animal */
	private $_niveau;
	/** Race de l'animal */
	private $_race;
	/** Nombre de victoires */
	private $_nbVictoires;
	/** Nombre de défaites */
	private $_nbDefaites;
	/** Nombre d'abandons */
	private $_nbAbandons;
	
	
	/**
	 * Constructeur totalement renseigné de l'Animal.
	 * @param idAnimal L'identifiant de l'Animal.
	 * @param nomAnimal Le nom de l'Animal.
	 * @param vie La vie de l'Animal.
	 * @param defense La défense de l'Animal.
	 * @param attaque L'attaque de l'Animal.
	 * @param niveau Le niveau de l'Animal.
	 * @param race La race de l'Animal.
	 */
	public function __construct($idAnimal, $proprietaire, $nomAnimal, $vie, $defense, $attaque, $niveau, $race=null, $nbVictoires = 0, $nbDefaites =0, $nbAbandons =0)
	{
		$this->setIdAnimal($idAnimal);
		$this->setProprietaire($proprietaire);
		$this->setNomAnimal($nomAnimal);
		$this->setVie($vie);
		$this->setDefense($defense);
		$this->setAttaque($attaque);
		$this->setNiveau($niveau);
		$this->setRace($race);
		$this->setNbVictoires($nbVictoires);
		$this->setNbDefaites($nbDefaites);
		$this->setNbAbandons($nbAbandons);
	}
	
	/**
	 * Permet d'obtenir l'identifiant de l'Animal.
	 * @return L'identifiant de l'Animal.
	 */
	public function getIdAnimal()
	{
		return $this->_idAnimal;
	}
	
	/**
	 * Permet d'obtenir le propriétaire de l'Animal.
	 * @return Le propriétaire de l'Animal.
	 */
	public function getProprietaire()
	{
		return $this->_proprietaire;
	}
	
	/**
	 * Permet d'obtenir le nom de l'Animal.
	 * @return Le nom de l'Animal.
	 */
	public function getNomAnimal()
	{
		return $this->_nomAnimal;
	}
	
	/**
	 * Permet d'obtenir la valeur de la vie de l'Animal.
	 * @return La valeur de la vie de l'Animal.
	 */
	public function getVie()
	{
		return $this->_vie;
	}
	
	/**
	 * Permet d'obtenir la valeur de l'attaque de l'Animal.
	 * @return La valeur de l'attaque de l'Animal.
	 */
	public function getAttaque()
	{
		return $this->_attaque;
	}
	
	/**
	 * Permet d'obtenir la valeur de la défense de l'Animal.
	 * @return La valeur de la défense de l'Animal.
	 */
	public function getDefense()
	{
		return $this->_defense;
	}
	
	/**
	 * Permet d'obtenir la valeur du niveau de l'Animal.
	 * @return La valeur du niveau de l'Animal.
	 */
	public function getNiveau()
	{
		return $this->_niveau;
	}	
	
	/**
	 * Permet d'obtenir la race de l'Animal.
	 * @return La race de l'Animal.
	 */
	public function getRace()
	{
		return $this->_race;
	}
	
	/**
	 * Permet d'obtenir le nombre de victoires de l'Animal.
	 * @return le nombre de victoires de l'Animal.
	 */
	public function getNbVictoires()
	{
		return $this->_nbVictoires;
	}
	
	/**
	 * Permet d'obtenir le nombre de défaites de l'Animal.
	 * @return le nombre de défaites de l'Animal.
	 */
	public function getNbDefaites()
	{
		return $this->_nbDefaites;
	}
	/**
	 * Permet d'obtenir le nombre d'abandonds de l'Animal.
	 * @return le nombre d'abandonds de l'Animal.
	 */
	public function getNbAbandons()
	{
		return $this->_nbAbandons;
	}
	
	/**
	 * Permet de modifier l'identifiant de l'Animal.
	 * @param idFacebook Le nouvel idenfiant de l'Animal.
	 */
	public function setIdAnimal($idAnimal)
	{
		$this->_idAnimal = $idAnimal;
	}
	
	/**
	 * Permet de modifier le propriétaire de l'Animal.
	 * @param proprietaire Le nouvel proprietaire de l'Animal.
	 */
	public function setProprietaire($proprietaire)
	{
		$this->_proprietaire = $proprietaire;
	}
	
	/**
	 * Permet de modifier le nom de l'Animal.
	 * @param idFacebook Le nouveau nom de l'Animal.
	 */
	public function setNomAnimal($nomAnimal)
	{
		$this->_nomAnimal = $nomAnimal;
	}
	
	/**
	 * Permet de modifier le coefficient de la vie de l'Animal.
	 * @param idFacebook Le nouveau coefficient de la vie de l'Animal.
	 */
	public function setVie($vie)
	{
		$this->_vie = $vie;
	}
	
	/**
	 * Permet de modifier le coefficient de l'attaque de l'Animal.
	 * @param idFacebook Le nouveau coefficient de l'attaque de l'Animal.
	 */
	public function setAttaque($attaque)
	{
		$this->_attaque = $attaque;
	}
	
	/**
	 * Permet de modifier le coefficient de la défense de l'Animal.
	 * @param idFacebook Le nouveau coefficient de la défense de l'Animal.
	 */
	public function setDefense($defense)
	{
		$this->_defense = $defense;
	}

	/**
	 * Permet de modifier le niveau de l'Animal.
	 * @param niveau Le nouveau niveau de l'Animal.
	 */
	public function setNiveau($niveau)
	{
		$this->_niveau = $niveau;
	}
	
	/**
	 * Permet de modifier la race de l'Animal.
	 * @param race La nouvelle race de l'Animal.
	 */
	public function setRace($race)
	{
		$this->_race = $race;
	}
	
	
	/**
	 * Permet de modifier le nombre de victoires de l'Animal.
	 * @param nbVictoires le nombre de victoires de l'Animal.
	 */
	public function setNbVictoires($nbVictoires)
	{
		$this->_nbVictoires = $nbVictoires;
	}
	
	/**
	 * Permet de modifier le nombre de défaites de l'Animal.
	 * @param nbDefaites le nombre de défaites de l'Animal.
	 */
	public function setNbDefaites($nbDefaites)
	{
		$this->_nbDefaites = $nbDefaites;
	}
	
	/**
	 * Permet de modifier le nombre d'abandons de l'Animal.
	 * @param nbAbandons le nombre d'abandons de l'Animal.
	 */
	public function setNbAbandons($nbAbandons)
	{
		$this->_nbAbandons = $nbAbandons;
	}
	
	/**
	 * toString() de la classe Animal. Retourne une chaine de caractères contenant la description de l'animal.
	 */
	public function __toString()
	{		
		$chaine = "(".$this->getIdAnimal().") ".$this->getNomAnimal()." de ".$this->getProprietaire().".";
		$chaine .= " ".$this->getVie()."/";
		$chaine .= $this->getDefense()."/";
		$chaine .=  $this->getAttaque();
	
		return $chaine;
	}
	
	/**
	 * Permet d'obtenir la fiche descriptive d'un animal.
	 * @return La fiche descriptive de l'animal.
	 */
	public function getFiche()
	{
		/*
		$chaine = "<table border='1' width=25% align='center'><tr>";
		$chaine .= "<td colspan='3' align='center'><b>". $this->getNomAnimal() ."</b></td>";
		$chaine .= "</tr>";
		$chaine .= "<tr>";
		$chaine .= "<td><b>Attaque</b></td>";
		$chaine .= "<td><b>Défense</b></td>";
		$chaine .= "<td><b>Vie</b></td>";
		$chaine .= "</tr>";
		$chaine .= "<tr>";
		$chaine .= "<td>". $this->getAttaque() ."</td>";
		$chaine .= "<td>". $this->getDefense() ."</td>";
		$chaine .= "<td>". $this->getVie() ."</td>";
		$chaine .= "</tr></table>";
		*/
		
		$chaine = array();
		$chaine['nomAnimal'] = $this->getNomAnimal();
		$chaine['attaqueCoef'] = $this->getAttaque();
		$chaine['defenseCoef'] = $this->getDefense();
		$chaine['vieCoef'] = $this->getVie();
		$chaine['niveau'] = $this->getNiveau();
		
		return $chaine;
	}
	


}

?>