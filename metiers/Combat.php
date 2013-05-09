<?php

/**
 *
* Classe représentant un combat sous Warnimals.
*
*/
class Combat
{
	/** Identifiant du combat (unique) */
	private $_idCombat;
	/** Identifiant de l'animal 1 */
	private $_animal1;
	/** Identifiant de l'animal 2 */
	private $_animal2;
	/** Date de création du combat */
	private $_date;
	/** Commencé ou non */
	private $_estCommence;
	
	/**
	 * Constructeur totalement renseigné du Combat.
	 * @param idCombat L'identifiant du Combat.
	 * @param animal1 L'identifiant de l'animal 1
	 * @param animal2 L'identifiant de l'animal 2
	 * @param date La date de création du combat
	 * @param estCommence Un boolean indiquant si le combat est commencé ou non
	 */
	public function __construct($idCombat, $animal1, $animal2, $date, $estCommence)
	{
		$this->setIdCombat($idCombat);
		$this->setAnimal1($animal1);
		$this->setAnimal2($animal2);
		$this->setDate($date);
		$this->setEstCommence($estCommence);
	}

	/**
	 * Permet d'obtenir l'identifiant de l'Animal.
	 * @return L'identifiant de l'Animal.
	 */
	public function getIdCombat()
	{
		return $this->_idCombat;
	}
	
	
	/**
	 * Permet de modifier l'identifiant du Combat.
	 * @param idFacebook Le nouvel idenfiant du Combat.
	 */
	public function setIdCombat($idCombat)
	{
		$this->_idCombat = $idCombat;
	}
	
	/**
	 * Permet d'obtenir l'identifiant de l'animal 1.
	 * @return L'identifiant de l'Animal 1.
	 */
	public function getAnimal1()
	{
		return $this->_animal1;
	}
	
	
	/**
	 * Permet de modifier l'identifiant de l'animal 1.
	 * @param $animal1 Le nouvel animal 1
	 */
	public function setAnimal1($animal1)
	{
		$this->_animal1 = $animal1;
	}
	
	/**
	 * Permet d'obtenir l'identifiant de l'animal 2.
	 * @return L'identifiant de l'Animal 2.
	 */
	public function getAnimal2()
	{
		return $this->_animal2;
	}
	
	
	/**
	 * Permet de modifier l'identifiant de l'animal 2.
	 * @param $animal2 Le nouvel animal 2
	 */
	public function setAnimal2($animal2)
	{
		$this->_animal2 = $animal2;
	}
	
	/**
	 * Permet d'obtenir la date du combat
	 * @return La date de création du combat
	 */
	public function getDate()
	{
		return $this->_date;
	}
	
	
	/**
	 * Permet de modifier la date du combat.
	 * @param $date La date du combat
	 */
	public function setDate($date)
	{
		$this->_date = $date;
	}
	
	/**
	 * Permet de savoir si le combat est commencé
	 * @return True/ false si le combat est commencé ou non
	 */
	public function getEstCommence()
	{
		return $this->_estCommence;
	}
	
	
	/**
	 * Permet de modifier le booleen indiquant si le combat est commencé.
	 * @param $estCommence Le booleen indiquant si le combat est commencé.
	 */
	public function setEstCommence($estCommence)
	{
		$this->_estCommence = $estCommence;
	}
	
	/**
	 * toString() de la classe Combat. Retourne une chaine de caractères contenant la description du combat.
	 */
	public function __toString()
	{		
		$chaine  = 'Id Combat : '.$this->getIdCombat().'. ';
		$chaine .= 'Animal1 : '.$this->getAnimal1().'. ';
		$chaine .= 'Animal2 : '.$this->getAnimal2().'. ';
		$chaine .= 'Date : '.$this->getDate().'. ';
		$chaine .= 'EstCommencé : '.$this->getEstCommence();
	
		return $chaine;
	}

}

?>