<?php

/**
 *
 * Classe représentant un joueur sous Warnimals.
 *
 */
class Joueur
{
	/** Identifiant Facebook du joueur (unique) */
	private $_idFacebook;
	/** Liste des amis du joueur. Contient les identifiants Facebook des amis. */
	private $_amis;
	/** Nom du joueur récupéré à partir de son profil Facebook */
	private $_nomJoueur;
	/** Crédit du joueur sous Warnimals. */
	private $_credit;
	/** Date de l'inscription du joueur sous Warnimals (sa première connexion). */
	private $_dateInscription;
	/** Nombre de victoires */
	private $_nbVictoires;
	/** Nombre de défaites */
	private $_nbDefaites;
	/** Nombre d'abandons */
	private $_nbAbandons;
	
	/**
	 * Constructeur totalement renseigné de Joueur.
	 * @param idFacebook L'identifiant Facebook du joueur.
	 * @param nom Le nom du joueur correspondant à celui de son profil Facebook.
	 * @param amis Un tableau contenant la liste des identifiants Facebook des amis du joueur.
	 * @param credit Le crédit du joueur sous Warnimals.
	 * @param dateInscription La date d'inscription du joueur à Warnimals.
	 */
	public function __construct($idFacebook, $nom, $amis, $credit, $dateInscription, $nbVictoires = 0, $nbDefaites =0, $nbAbandons =0 )
	{
		$this->setIdFacebook($idFacebook);
		$this->setNomJoueur($nom);
		$this->setAmis($amis);
		$this->setCredit($credit);
		$this->setDateInscription($dateInscription);
		$this->setNbVictoires($nbVictoires);
		$this->setNbDefaites($nbDefaites);
		$this->setNbAbandons($nbAbandons);
	}
	
	/**
	 * Permet d'obtenir l'identifiant Facebook du joueur.
	 * @return L'identifiant Facebook du joueur.
	 */
	public function getIdFacebook()
	{
		return $this->_idFacebook;
	}
	
	/**
	 * Permet d'obtenir les identifiants Facebook des amis du joueur.
	 * @return Un tableau contenant la liste des identifiants Facebook des amis du joueur.
	 */
	public function getAmis()
	{
		return $this->_amis;
	}
	
	/**
	 * Permet d'obtenir le nom du Joueur. Ce nom correspond à celui du profil Facebook du Joueur.
	 * @return Le nom du joueur correspondant à celui de son profil Facebook.
	 */
	public function getNomJoueur()
	{
		return $this->_nomJoueur;
	}

	/**
	 * Permet d'obtenir le crédit du joueur sous Warnimals.
	 * @return Le crédit du joueur sous Warnimals.
	 */
	public function getCredit()
	{
		return $this->_credit;
	}
	
	/**
	 * Permet d'obtenir la date d'inscription du joueur (la date de sa 1ère connexion au jeu).
	 * @return La date d'inscription du joueur à Warnimals.
	 */
	public function getDateInscription()
	{
		return $this->_dateInscription;
	}
	
	/**
	 * Permet d'obtenir le nombre de victoires du joueur.
	 * @return le nombre de victoires du joueur.
	 */
	public function getNbVictoires()
	{
		return $this->_nbVictoires;
	}
	
	/**
	 * Permet d'obtenir le nombre de défaites du joueur.
	 * @return le nombre de défaites du joueur.
	 */
	public function getNbDefaites()
	{
		return $this->_nbDefaites;
	}
	/**
	 * Permet d'obtenir le nombre d'abandonds du joueur.
	 * @return le nombre d'abandonds du joueur.
	 */
	public function getNbAbandons()
	{
		return $this->_nbAbandons;
	}
	
	/**
	 * Permet de modifier l'identifiant Facebook du joueur.
	 * @param idFacebook Le nouvel idenfiant Facebook du Joueur.
	 */
	public function setIdFacebook($idFacebook)
	{
		$this->_idFacebook = $idFacebook;
	}

	/**
	 * Permet de modifier la liste des amis Facebook du joueur.
	 * @param listeAmis La nouvelle liste des amis du joueur.
	 */
	public function setAmis($listeAmis)
	{
		$this->_amis = $listeAmis;
	}
	
	/**
	 * Permet de modifier le nom du joueur.
	 * @param nom Le nouveau nom du joueur.
	 */
	public function setNomJoueur($nom)
	{
		$this->_nomJoueur = $nom;
	}
	
	/**
	 * Permet de modifier le crédit du joueur dans Warnimals.
	 * @param credit Le nouveau crédit du joueur.
	 */
	public function setCredit($credit)
	{
		$this->_credit = $credit;
	}
	
	/**
	 * Permet de modifier la date d'inscription du joueur à Warnimals.
	 * @param dateInscription La date d'inscription du joueur sur Warnimals.
	 */
	public function setDateInscription($dateInscription)
	{
		$this->_dateInscription = $dateInscription;
	}
	
	/**
	 * Permet de modifier le nombre de victoires du joueur.
	 * @param nbVictoires le nombre de victoires du joueur.
	 */
	public function setNbVictoires($nbVictoires)
	{
		$this->_nbVictoires = $nbVictoires;
	}
	
	/**
	 * Permet de modifier le nombre de défaites du joueur.
	 * @param nbDefaites le nombre de défaites du joueur.
	 */
	public function setNbDefaites($nbDefaites)
	{
		$this->_nbDefaites = $nbDefaites;
	}
	
	/**
	 * Permet de modifier le nombre d'abandons du joueur.
	 * @param nbAbandons le nombre d'abandons du joueur.
	 */
	public function setNbAbandons($nbAbandons)
	{
		$this->_nbAbandons = $nbAbandons;
	}
	
	/**
	 * toString() de la classe Joueur. Retourne une chaine de caractères contenant la description du joueur.
	 */
	public function __toString()
	{
		$chaine = $this->getNomJoueur()." (".$this->getIdFacebook()."). ";
		$chaine .= "Inscrit le : ".$this->getDateInscription().". ";
		$chaine .= "Crédit : ".$this->getCredit().". ";
		$chaine .= "Liste des amis (".count($this->getAmis()).") : ";
		$amis = $this->getAmis();
		foreach ($amis as $ami)
		{
			$chaine .= $ami." | ";
		}
		return $chaine;
	}
	

}

?>