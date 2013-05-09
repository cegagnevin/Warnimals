<?php
require_once '../modeles/EntrainementModele.php';
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
 * Le Controleur gère l'entrainement.
 */
class ControleurEntrainement implements IControleur
{
	/** Instance unique */
	private static $_instance;
	/** Modele Entrainement */
	private $_mdlEntrainement = null;
	/** Modele Joueur */
	private $_mdlJoueur = null;
	
	/**
	 * Constructeur par défaut. Instancie le modele Joueur et le modele Entrainement. 
	 */
	private function __construct()
	{
		$this->_mdlEntrainement = EntrainementModele::getInstance();
		$this->_mdlJoueur = JoueurModele::getInstance();
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
	 * Permet d'obtenir le modele Entrainement (pour les tests).
	 * @return Le modele Entrainement
	 */
	public function getEntrainementModele()
	{
		return $this->_mdlEntrainement;
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
	 * Traite l'action émise par la vue.
	 * @param action L'action à effectuer
	 */
	public function traiterAction($action)
	{
		try 
		{
			$action = htmlspecialchars($action);
			
			switch ($action)
			{
				case "IsAllowed" :
					//On démarre la session
					$joueur =$_SESSION [ 'joueur' ];
					$idJoueur = $joueur->getIdFacebook();
				
					$animal = $this->_mdlJoueur->getAnimalByJoueur($idJoueur);
				
					if($animal == null)
					{
				
						$msg = array(
								'error' => "Vous devez acheter un animal dans le market avant de penser a l'entrainer !"
						);
						echo json_encode($msg);
						break;
					}
						
					$msg = array(
							'ok' => true
					);
					echo json_encode($msg);
					break;
										
				case "Entrainement" : //Exécuté lorsque le controleur principal demande l'affichage de l'entrainement
					$smarty = new Smarty();
					$smarty = $this->afficherEntrainements($smarty);
					$smarty->display("../vues/entrainement.html");
					exit();
					break;
					
				case "EntrainementIndividuel" : //Choix de l'entrainement individuel
					try 
					{
						$entrainementIndividuel = $this->_mdlEntrainement->souscrireEntrainementIndividuel();
						
						$message = 'Votre inscription a bien été prise en compte. Vous pourrez récupérer votre animal à partir ';
						$dateFin = $entrainementIndividuel->getDateDebut() + $entrainementIndividuel->getDuree();
						if(date("d/m/Y") == date("d/m/Y", $dateFin))//Aujourd'hui
							$message .= 'de '.date("H\hi", $dateFin);
						else $message .= 'du '.date("d/m/Y \à H\hi", $dateFin);
						
						echo $message;
					}
					catch(Exception $e)
					{
						//Envoi du message d'erreur à la requete ajax
						echo $e->getMessage();
					}
					break;
					
				case "EntrainementCollectif" : //Choix d'un entrainement collectif
					try
					{
						$idEntrainement = htmlspecialchars($_GET[ 'idEntrainementCollectif' ]);
						$entrainementCollectif = $this->_mdlEntrainement->souscrireEntrainementCollectif($idEntrainement);
				
						$message = 'Votre inscription a bien été prise en compte. Vous pourrez récupérer votre animal à partir ';
						$dateFin = $entrainementCollectif->getDateDebut() + $entrainementCollectif->getDuree();
						if(date("d/m/Y") == date("d/m/Y", $dateFin))//Aujourd'hui
							$message .= 'de '.date("H\hi", $dateFin);
						else $message .= 'du '.date("d/m/Y \à H\hi", $dateFin);
						
						$message .= " si l'entrainement a lieu. Dans le cas contraire, vous serez remboursé des frais d'inscription.";
				
						echo $message;
					}
					catch(Exception $e)
					{
						//Envoi du message d'erreur à la requete ajax
						echo $e->getMessage();
					}
					break;
					
				default : //On redirige vers la page d'erreur
					$this->afficherErreur('Action "'.$action.'" indéfinie',
					'L\'erreur est survenue dans le controleur principal, méthode traiterAction(). ',
					'');
	
			}
		}
		catch(Exception $e)
		{
			$this->afficherErreur($e->getFile().":".$e->getLine(),
					$e->getMessage(),
					$e->getTraceAsString());
		}
	}
	
	/**
	 * Assigne les variables smarty afin d'afficher les propositions d'entrainements.
	 */
	public function afficherEntrainements(Smarty $smarty)
	{
		@session_start();
		$joueur = $_SESSION[ 'joueur' ];
		$idJoueur = $joueur->getIdFacebook();
		$animalJoueur = $this->_mdlJoueur->getAnimalByJoueur($idJoueur);
		//Entrainement individuel
		$entrainementIndividuel = $this->_mdlEntrainement->getEntrainementIndividuelByNiveau($animalJoueur->getNiveau());
		$smarty = $this->_mdlEntrainement->assignEntrainementIndividuel($entrainementIndividuel, $smarty);
		//Entrainements collectifs
		$smarty = $this->_mdlEntrainement->assignEntrainementsCollectifs($smarty);
		
		return $smarty;
	}
	
	/**
	 * Permet d'afficher le détail d'une erreur sur une page dédiée.
	 * @param erreur_type
	 * @param erreur_message
	 * @param erreur_trace
	 */
	public function afficherErreur($erreur_type, $erreur_message, $erreur_trace)
	{
		$smarty = new Smarty();
		$smarty->assign('erreur_type', $erreur_type);
		$smarty->assign('erreur_message', $erreur_message);
		$smarty->assign('erreur_trace', $erreur_trace);
		$smarty->display("../vues/erreur.html");
		exit(1);
	}
	

}


if(isset($_GET[ 'actionEntrainement']))
{
	$action = $_GET['actionEntrainement'];
	//Instanciation du controleur Entrainement et demande d'initialisation du jeu
	$controleurEntrainement = ControleurEntrainement::getInstance();
	//Le controleur traite l'action
	$controleurEntrainement->traiterAction($action);
}

?>