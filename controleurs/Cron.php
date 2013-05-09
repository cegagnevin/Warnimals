<?php

require_once '../settings/Configuration.php';
require_once '../modeles/EntrainementModele.php';

//Affichage des erreurs en fonction du mode choisi
if(AFFICHAGE_ERREURS)
{
	ini_set('display_errors', 1);
	error_reporting(E_ALL);
}

/**
 * Cron se déclanchant à chaque fois qu'un joueur se connecte au jeu.
 * Role : - Ajouter ou supprimer des entrainements
 * 		  - Mettre en vente des animaux sur le Market
 */
class Cron
{
	/** Instance unique */
	private static $_instance;
	/** Modele Entrainement */
	private $_mdlEntrainement;
	
	
	/**
	 * Constructeur par défaut.
	 */
	private function __construct()
	{
		$this->_mdlEntrainement = EntrainementModele::getInstance();
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
	 * Effectue l'ensemble des tâches nécessaires au déroulement de l'application :
	 *  - Ajout des offres individuelles pour les entrainements (chaque jour).
	 *  - Validation des entrainements finis
	 */
	public function run()
	{
		//Gestion des offres pour les entrainements individuels dans le fichier "offres_entrainements.txt"
		$this->_mdlEntrainement->gererOffresEntrainementsIndividuels();
		
		//Gestion des entrainements collectifs proposés pour la journée
		$this->_mdlEntrainement->gererEntrainementsCollectifs();
		
		/*Permet d'entrainer les animaux ayant finis leur entrainement. Ces animaux voient donc leurs caractéristiques
		* améliorées en fonction de l'offre de l'entrainement suivi.
		* S'ils gagnent un niveau, ils apprennent les compétences disponibles à ce niveau atteint.*/
		$this->_mdlEntrainement->entrainerAnimaux();
		
		//On supprime les entrainements annulés (manque de participants) avec remboursement des joueurs
		$this->_mdlEntrainement->cancelIncompleteCollectiveTrainings();
		
		
	}
	
}

?>