<?php

/**
 * Cette classe représente une compétence pouvant être apprise par un animal.
 */
class Competence 
{
	/** Identifiant de la compétence */
	private $_idCompetence;
	/** Nom de la compétence */
	private $_nomCompetence;
	/** Dégats affligés par cette compétence */
	private $_degats;
	/** Le type de la compétence */
	private $_type;
	/** Puissance de la compétence */
	private $_codePuissance;
	
	/**
	 * Constructeur par défaut d'une compétence.
	 * @param idCompetence Identifiant de la compétence
	 * @param nomCompetence Le nom de la compétence
	 * @param degats Les degats causés par cette compétence
	 * @param type Le type de la compétence
	 * @param codePuissance Puissance de la compétence
	 */
	public function __construct($idCompetence, $nomCompetence, $degats, $type, $codePuissance=null)
	{
		$this->setIdCompetence($idCompetence);
		$this->setNomCompetence($nomCompetence);
		$this->setDegats($degats);
		$this->setType($type);
		$this->setCodePuissance($codePuissance);
	}
	
	/**
	 * Permet d'obtenir l'identifiant de la compétence
	 * @return L'identifiant de la compétence
	 */
	public function getIdCompetence()
	{
		return $this->_idCompetence;
	}
	
	/**
	 * Permet d'obtenir le nom de la compétence.
	 * @return Le nom de la compétence.
	 */
	public function getNomCompetence()
	{
		return $this->_nomCompetence;
	}
	
	/**
	 * Permet d'obtenir les degats affligés par cette compétence.
	 * @return Les degats affligés par cette compétence.
	 */
	public function getDegats()
	{
		return $this->_degats;
	}
	
	/**
	 * Permet d'obtenir le type de la compétence.
	 * @return Le type de cette compétence.
	 */
	public function getType()
	{
		return $this->_type;
	}
	
	/**
	 * Permet d'obtenir le code puissance de la compétence.
	 * @return Le code puissance de cette compétence.
	 */
	public function getCodePuissance()
	{
		return $this->_codePuissance;
	}
	
	
	/**
	 * Permet de modifier l'identifiant de la compétence
	 * @param idCompetence Le nouvel identifiant de la compétence
	 */
	public function setIdCompetence($idCompetence)
	{
		$this->_idCompetence = $idCompetence;
	}
	
	/**
	 * Permet de modifier le nom de la compétence.
	 * @param nomCompetence Le nouveau nom de la compétence.
	 */
	public function setNomCompetence($nomCompetence)
	{
		$this->_nomCompetence = $nomCompetence;
	}
	
	/**
	 * Permet de modifier les degats affligés par la compétence.
	 * @param degats Les nouveaux dégats qui sont affligés par la compétence.
	 */
	public function setDegats($degats)
	{
		$this->_degats = $degats;
	}
	
	/**
	 * Permet de modifier le type de la compétence.
	 * @param type Le nouveau type de la compétence.
	 */
	public function setType($type)
	{
		$this->_type = $type;
	}
	
	/**
	 * Permet de modifier le code puissance de la compétence.
	 * @param codePuissance Le nouveau code puissance de la compétence.
	 */
	public function setCodePuissance($codePuissance)
	{
		$this->_codePuissance = $codePuissance;
	}
	
	/**
	 * Décrit la compétence par une chaine de caractères.
	 */
	public function __toString()
	{
		$chaine  = "Numero de compétence : ".$this->getIdCompetence().". ";
		$chaine .= "Nom : ".$this->getNomCompetence().". ";
		$chaine .= "Dégats : ".$this->getDegats().". ";
		$chaine .= "Type : ".$this->getType().". ";
		$chaine .= "Code puissance : ".$this->getCodePuissance().".";
		return $chaine;
	}
		
}


?>
