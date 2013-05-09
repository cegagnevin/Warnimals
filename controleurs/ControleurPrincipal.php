<?php
require_once '../mapping/JoueurDAO.php';
require_once '../../lib/smarty/Smarty.class.php';
require_once '../settings/Configuration.php';
require_once '../modeles/JoueurModele.php';
require_once 'ControleurMarket.php';
require_once 'ControleurEntrainement.php';
require_once 'ControleurCombat.php';
require_once 'ControleurProfil.php';
require_once 'Cron.php';
require_once 'IControleur.php';

//Affichage des erreurs en fonction du mode choisi
if(AFFICHAGE_ERREURS)
{
	ini_set('display_errors', 1);
	error_reporting(E_ALL);
}
	
	
/**
 * Le Controleur Principal gère la connexion d'un joueur à partir de l'extérieur de l'application.
 * Il gère également l'ensemble des événements qui survienne sur la vue "index.php" ainsi que "interfacePrincipale.php"
 */
class ControleurPrincipal implements IControleur
{
	/** Instance unique */
	private static $_instance;
	
	/** Modele Joueur */
	private $_mdlJoueur = null;
	
	/**
	 * Constructeur par défaut.
	 */
	private function __construct()
	{
		$this->_mdlJoueur = JoueurModele::getInstance();	
	}
	
	/**
	 * Retourner le modele Joueur instancié
	 * @return Le modele Joueur
	 */
	public function getJoueurModele()
	{
		return $this->_mdlJoueur;	
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
	 * Traite l'action émise par la vue.
	 * @param action L'action à effectuer
	 */
	public function traiterAction($action)
	{
		try
		{
			//Never trust users input
			$action = htmlspecialchars($action);
			
			switch ($action)
			{
				case "Jouer" :
					//On exécute le Cron principal qui fait les traitements automatiques
					Cron::getInstance()->run();
					
					//Savoir si le joueur est nouveau ou pas
					$arrayPlayer = $this->_mdlJoueur->isPlayerExist();
					$this->_mdlJoueur->initialisation($arrayPlayer[ 'idPlayer'], $arrayPlayer[ 'isPlayerExist']); //Initialise le jeu
					$smarty = new Smarty();
					$smarty->display("../vues/interfacePrincipale.html");
					break;
					
				case "Market" :
					ControleurMarket::getInstance()->traiterAction($action); //On délégue au controleurMarket le soin d'afficher le Market.
					break;
					
				case "Entrainement" :
				 	ControleurEntrainement::getInstance()->traiterAction($action); //On délégue au controleuEntrainement le soin d'afficher l'Entrainement.
				 	break;
				 	
				case "Arene" :
				 	ControleurCombat::getInstance()->traiterAction($action); //On délégue au controleuCombat le soin d'afficher le combat.
				 	break;
				 	/*
				 	$smarty = new Smarty();
				 	$smarty->display("../vues/arene.html");
				 	break;*/
				 	
				case "Paris" :
				 	$smarty = new Smarty();
				 	$smarty->display("../vues/paris.html");
				 	break;
				 	
				case "Profil" :
					ControleurProfil::getInstance()->traiterAction($action); //On délégue au controleurMarket le soin d'afficher le Market.
					break;
				 	
				case "InterfacePrincipale" :
					$smarty = new Smarty();
					$smarty->display("../vues/interfacePrincipale.html");
					break;
					 	
				default : 
					//On redirige vers la page d'erreur
					$this->afficherErreur('Action "'.$action.'" indéfinie',
									      'L\'erreur est survenue dans le controleur principal, méthode traiterAction(). ',
									      '');	
			}
		}
		catch(SmartyException $e)
		{
			//On ignore les exceptions Smarty
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : <pre>'.$e->getTraceAsString().'</pre><br/>';
		}
	}
	
	/**
	 * Permet d'afficher le détail d'une erreur sur une page dédiée.
	 * @param erreur_type
	 * @param erreur_message
	 * @param erreur_trace
	 */
	private function afficherErreur($erreur_type, $erreur_message, $erreur_trace)
	{
		$smarty = new Smarty();
		$smarty->assign('erreur_type', $erreur_type);
		$smarty->assign('erreur_message', $erreur_message);
		$smarty->assign('erreur_trace', $erreur_trace);
		$smarty->display("../vues/erreur.html");
		die();
	}
	
	

}

if(isset($_GET[ 'actionPrincipale']))
{
	//Récupération de l'action
	$action = $_GET[ 'actionPrincipale'];
	//Instanciation du controleur principal et demande d'initialisation du jeu
	$controleurPrincipal = ControleurPrincipal::getInstance();
	//Le controleur traite l'action
	$controleurPrincipal->traiterAction($action);
}
?>