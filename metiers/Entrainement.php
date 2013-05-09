<?php

/**
 * Cette classe abstraite représente un entrainement.
 */
abstract class Entrainement
{
	/** Identifiant de l'entrainement */
	private $_idEntrainement;
	
	/** La durée de l'entrainement en secondes */
	private $_duree;
	
	/** Prix de l'entrainement */
	private $_prix;
	
	/** La date à l'entrainement doit débuter */
	private $_dateDebut;
	
	/** L'offre correspondant à l'entrainement */
	private $_offre;
	
	/**
	 * Constructeur par défaut d'un entrainement.
	 * @param $idEntrainement Identifiant de l'entainement
	 * @param $duree Durée de l'entrainement
	 * @param $prix Prix de l'entrainement
	 * @param $dateDebut Date de début de l'entrainement
	 * @param $idOffre Identifiant de l'offre correspondant à l'entrainement.
	 */
	public function __construct($idEntrainement, $duree, $prix, $dateDebut, OffreEntrainement $offre)
	{
		$this->setIdEntrainement($idEntrainement);
		$this->setDuree($duree);
		$this->setPrix($prix);
		$this->setDateDebut($dateDebut);	
		$this->setOffre($offre);
	}
	
	/**
	 * Permet d'obtenir le type d'entrainement.
	 * @return Le type d'entrainement
	 */
	abstract public function getType();
	
	/**
	 * Permet d'obtenir la valeur de l'identifiant de l'Entrainement.
	 * @return La valeur de l'identifiant de l'Entrainement.
	 */
	public function getIdEntrainement()
	{
		return $this->_idEntrainement;
	}
	
	/**
	 * Permet d'obtenir la valeur de la durée de l'Entrainement.
	 * @return La valeur de la durée de l'Entrainement.
	 */
	public function getDuree()
	{
		return $this->_duree;
	}
	
	/**
	 * Permet d'obtenir la valeur du prix de l'Entrainement.
	 * @return La valeur du prix de l'Entrainement.
	 */
	public function getPrix()
	{
		return $this->_prix;
	}	
	
	/**
	 * Permet d'obtenir l'identifiant de l'offre correspondant à l'entrainement.
	 * @return L'offre
	 */
	public function getOffre()
	{
		return $this->_offre;
	}
	
	/**
	 * Permet l'état de l'Entrainement.
	 * @return L'état de l'Entrainement : 0 -> A venir,
	 * 									  1  -> En cours, 
	 * 									  2  -> Fini.
	 */
	public function getEtat()
	{
		$bool = 2; //Initialisé à FINI.
		$currentTime = time(); //Timestamp actuel 
		$dateFin = $this->getDateDebut() + $this->getDuree();
		
		if($this->getDateDebut() > $currentTime) //A venir
		{
			$bool = 0;
		}
		else if($currentTime >= $this->getDateDebut() && $currentTime < $dateFin) //En cours
		{
			$bool = 1;
		}
		
		return $bool; //Fini dans les autres cas
	}
	
	/**
	 * Permet d'obtenir la valeur de la date de début de l'Entrainement.
	 * @return La valeur de la date de début de l'Entrainement.
	 */
	public function getDateDebut()
	{
		return $this->_dateDebut;
	}
	
	/**
	 * Permet de modifier l'identifiant de l'Entrainement.
	 * @param idEntrainement Le nouvel idenfiant de l'Entrainement.
	 */
	public function setIdEntrainement($idEntrainement)
	{
		$this->_idEntrainement = $idEntrainement;
	}
	
	/**
	 * Permet de modifier la durée de l'Entrainement.
	 * @param duree La nouvelle durée de l'Entrainement.
	 */
	public function setDuree($duree)
	{
		$this->_duree = $duree;
	}
	
	/**
	 * Permet de modifier le prix de l'Entrainement.
	 * @param prix Le nouveau prix de l'Entrainement.
	 */
	public function setPrix($prix)
	{
		$this->_prix = $prix;
	}	
	
	/**
	 * Permet de modifier la date de début de l'Entrainement.
	 * @param dateDebut La nouvelle date de début de l'Entrainement.
	 */
	public function setDateDebut($dateDebut)
	{
		$this->_dateDebut = $dateDebut;
	}
	
	/**
	 * Permet de modifier l'offre correspodnant à l'entrainement.
	 * @param idOffre La nouvelle offre
	 */
	public function setOffre($offre)
	{
		$this->_offre = $offre;
	}
	
	/**
	 * Décrit l'entrainement par une chaine de caractères.
	 */
	public function __toString()
	{
		$chaine = '('.$this->getIdEntrainement().') - ';
		$chaine .= 'Duree : '.$this->getDuree().'. ';
		$chaine .= 'Prix : '.$this->getPrix().'. ';
		$chaine .= 'Date debut : '.$this->getDateDebut();
		return $chaine;
	}
		
}


?>
