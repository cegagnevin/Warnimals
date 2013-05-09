<?php
require_once '../modeles/AnimalModele.php';
require_once '../modeles/JoueurModele.php';
require_once '../../lib/smarty/Smarty.class.php';
require_once '../settings/Configuration.php';
require_once 'IControleur.php';

//Affichage des erreurs en fonction du mode choisi
if(AFFICHAGE_ERREURS)
{
	ini_set('display_errors', 1);
	error_reporting(E_ALL);
}

/**
 * Le Controleur gère le market.
 */
class ControleurProfil implements IControleur
{
	/** Instance unique */
	private static $_instance;
	
	/** Modele Joueur */
	private $_mdlJoueur = null;
	
	/** Modele Animal */
	private $_mdlAnimal = null;
	
	/**
	 * Constructeur par défaut. Instancie le modele Joueur et le modele Animal. 
	 */
	private function __construct()
	{
		$this->_mdlJoueur = JoueurModele::getInstance();
		$this->_mdlAnimal = AnimalModele::getInstance();
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
	 * Permet d'obtenir le modele Joueur (pour les tests).
	 * @return Le modele Joueur
	 */
	public function getJoueurModele()
	{
		return $this->_mdlJoueur;
	}
	
	/**
	 * Permet d'obtenir le modele Animal (pour les tests).
	 * @return Le modele Animal
	 */
	public function getAnimalModele()
	{
		return $this->_mdlAnimal;
	}
	
	/**
	 * Traite l'action émise par la vue.
	 * @param action L'action à effectuer
	 */
	public function traiterAction($action)
	{
		$action = htmlspecialchars($action);
		try
		{
			switch ($action)
			{
				case "Profil" :
					//Récupérer photo facebook et afficher caractéristiques
					//Instance du joueur, de type Joueur
					$joueur = $_SESSION [ 'joueur' ];
					$idJoueur = $joueur->getIdFacebook();
					$animal=null;					
					
					$infosFacebook = $this->_mdlJoueur->getInfosFacebook();
					$idFacebook = $infosFacebook['id'];
					$urlPicture = "https://graph.facebook.com/" . $idFacebook . "/picture?type=large";
					
					
					$animal = $this->_mdlJoueur->getAnimalByJoueur($idJoueur);
	
					//$animal->setIdAnimal(null);
					$smarty = new Smarty();
					$smarty->assign("action", "profil");
					$smarty->assign("joueur", $joueur);
					$smarty->assign("animal", $animal);
					$smarty->assign("urlPicture", $urlPicture);
					$smarty->display("../vues/profil.html");
					break;
					
				default : //On redirige vers la page d'erreur
					$this->afficherErreur('Action "'.$action.'" indéfinie',
									      'L\'erreur est survenue dans le controleur combat, méthode traiterAction(). ',
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


if(isset($_GET[ 'actionProfil']))
{
	$action = $_GET['actionProfil'];
	//Instanciation du controleur profil et demande d'initialisation du jeu
	$controleurProfil = ControleurProfil::getInstance();
	//Le controleur traite l'action
	$controleurProfil->traiterAction($action);
}

?>