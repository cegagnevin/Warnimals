<?php
require_once 'Entrainement.php';

/**
 * Cette classe abstraite représente un entrainement collectif.
 */
class EntrainementCollectif extends Entrainement
{
	/** Animaux inscrits à l'entrainement */
	private $_animauxInscrits;
	/** Niveau maximum pour participer à l'entrainement */
	private $_niveauMax;
	/** Nb de participants minimum pour que l'entrainement ait lieu */
	private $_nbParticipantsMin;
	/** Nb de participants max pour l'entrainement */
	private $_nbParticipantsMax;
	/** Entrainement annulé/non annulé */
	private $_annule;
	
	/**
	 * Constructeur par défaut d'un entrainement.
	 * @param $idEntrainement Identifiant de l'entainement
	 * @param $duree Durée de l'entrainement
	 * @param $prix Prix de l'entrainement
	 * @param $dateDebut Date de début de l'entrainement
	 * @param $offre L'offre
	 * @param $animauxInscrits Animaux inscrits à l'entrainement.
	 */
	public function __construct($idEntrainement, $duree, $prix, $dateDebut, OffreEntrainement $offre, $animauxInscrits, $niveauMax=0, $nbParticipantsMin=0, $nbParticipantsMax=0, $annule=0)
	{
		parent::__construct($idEntrainement, $duree, $prix, $dateDebut, $offre);
		$this->setAnimauxInscrits($animauxInscrits);
		$this->setNiveauMax($niveauMax);
		$this->setNbParticipantsMin($nbParticipantsMin);
		$this->setNbParticipantsMax($nbParticipantsMax);
		$this->setAnnule($annule);
	}
	
	/**
	 * Permet d'obtenir la liste des animaux inscrits à l'entrainement.
	 * @return la liste des animaux inscrits à l'entrainement.
	 */
	public function getAnimauxInscrits()
	{
		return $this->_animauxInscrits;
	}
	
	/**
	 * Permet d'obtenir le type d'entrainement.
	 * @return Le type d'entrainement
	 */
	public function getType()
	{
		return 'collectif';
	}
	
	/**
	 * Permet d'obtenir le niveau maximal pour participer à l'entrainement
	 * @return Le niveau maximal
	 */
	public function getNiveauMax()
	{
		return $this->_niveauMax;
	}
	
	/**
	 * Permet d'obtenir le nombre min de participants pour que l'entrainement ait lieu
	 * @return Le nombre min de participants
	 */
	public function getNbParticipantsMin()
	{
		return $this->_nbParticipantsMin;
	}
	
	/**
	 * Permet d'obtenir le nombre max de participants pour l'entrainement.
	 * @return Le nombre max de participants
	 */
	public function getNbParticipantsMax()
	{
		return $this->_nbParticipantsMax;
	}
	
	/**
	 * Permet de savoir si un entrainement est annulé ou pas
	 * @return 1 annulé
	 * 		   0 non annulé
	 */
	public function getAnnule()
	{
		return $this->_annule;
	}
	
	/**
	 * Permet de modifier la liste des animaux inscrits à l'entrainement.
	 * @param animauxInscrits La liste des animaux inscrits à l'entrainement.
	 */
	public function setAnimauxInscrits($animauxInscrits)
	{
		$this->_animauxInscrits = $animauxInscrits;
	}
	
	/**
	 * Permet de modifier le niveau maximal pour participer à l'entrainement
	 * @param int Le niveau maximal
	 */
	public function setNiveauMax($niveauMax)
	{
		$this->_niveauMax = $niveauMax;
	}
	
	/**
	 * Permet de modifier le nombre max de participants pour l'entrainement
	 * @param int Le nombre max de participants
	 */
	public function setNbParticipantsMax($nbParticipantsMax)
	{
		$this->_nbParticipantsMax = $nbParticipantsMax;
	}
	
	/**
	 * Permet de modifier le nombre min de participants pour que l'entrainement ait lieu
	 * @param int Le nombre min de participants
	 */
	public function setNbParticipantsMin($nbParticipantsMin)
	{
		$this->_nbParticipantsMin = $nbParticipantsMin;
	}
	
	/**
	 * Permet de modifier si un entrainement est annulé ou pas
	 * @param 1 annulé
	 * 		   0 non annulé
	 */
	public function setAnnule($annule)
	{
		$this->_annule = $annule;
	}
	
	/**
	 * Décrit l'entrainement collectif par une chaine de caractères.
	 */
	public function __toString()
	{
		$chaine = parent::__toString();
		$chaine .= '. Animaux inscrits : ';
		$animauxInscrits = $this->getAnimauxInscrits();
		foreach($animauxInscrits as $animal)
		{
			$chaine .= $animal->getIdAnimal().' - ';
		}
		return $chaine;
	}
		
}


?>
