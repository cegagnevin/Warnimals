<?php
require_once '../modeles/TransactionModele.php';
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
class ControleurMarket implements IControleur
{
	/** Instance unique */
	private static $_instance;
	
	/** Modele Transaction */
	private $_mdlTransaction = null;
	
	/** Modele Joueur */
	private $_mdlJoueur = null;
	
	/**
	 * Constructeur par défaut. Instancie le modele Joueur et le TransactionModele. 
	 */
	private function __construct()
	{
		$this->_mdlTransaction = TransactionModele::getInstance();
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
	 * Permet d'obtenir le TransactionModele (pour les tests).
	 * @return Le TransactionModele
	 */
	public function getTransactionModele()
	{
		return $this->_mdlTransaction;
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
	$action = htmlspecialchars($action);
		try
		{
			switch ($action)
			{
				case "Acheter" :
					try {
						@session_start();
						$idTransaction = htmlentities($_GET['idTransaction']);
						$joueur = $_SESSION[ 'joueur' ];
						
						$transaction = $this->_mdlTransaction->getTransactionById($idTransaction);
						$animal = AnimalModele::getInstance()->getAnimal($transaction['Animal_idAnimal']);
						
						//appelle la méthode achete de joueur
						$this->_mdlJoueur->achete($joueur->getIdFacebook(), $animal->getIdAnimal(), $transaction['prixVente']);
						
						//On finalise la transaction
						$this->_mdlTransaction->finaliserTransaction($idTransaction);
						
						echo 1;
					}
					catch(Exception $e)
					{
						echo $e->getMessage();
					}
					
					break;
				
				case "Vendre" :
					try {
						@session_start();
						$prixVente = htmlentities($_GET['prixVente']);
						$joueur = $_SESSION[ 'joueur' ];

						$animal = JoueurModele::getInstance()->getAnimalByJoueur($joueur->getIdFacebook());
						
						//Pas d'animal
						if($animal == null)
						{
							echo "Vous n'avez pas d'animal à mettre en vente";
							break;
						}
						
						//On vérifie si l'animal n'est pas occupé
						if(!AnimalModele::getInstance()->isAvailable($animal->getIdAnimal()))
						{
							echo "Votre animal est actuellement occupé. Vous ne pouvez pas le mettre en vente pour le moment";
							break;
						}
						
						//On créé une transaction associée à la mise en vente
						$this->_mdlTransaction->mettreEnVenteAnimal($animal->getIdAnimal(), $prixVente);
				
						echo 1;
					}
					catch(Exception $e)
					{
						echo $e->__toString();
					}
						
					break;
					
				case "IsSelling" :
					@session_start();
					$joueur = $_SESSION[ 'joueur' ];
					$animal = JoueurModele::getInstance()->getAnimalByJoueur($joueur->getIdFacebook());
			
					//Pas d'animal
					if($animal == null)
					{
						echo false;
						break;
					}
			
					//On regarde si l'animal du joueur est actuellement en vente
					echo $this->_mdlTransaction->isSelling($animal->getIdAnimal());
					break;
					
				case "AnnulerVente" :
					@session_start();
					$joueur = $_SESSION[ 'joueur' ];
					$animal = JoueurModele::getInstance()->getAnimalByJoueur($joueur->getIdFacebook());
					$transaction = $this->_mdlTransaction->getTransactionEnCoursByAnimal($animal->getIdAnimal());
						
					echo $this->_mdlTransaction->retirerDeLaVenteAnimal($transaction['idTransaction']);
					break;
					
				case "Market"  : //Exécuté lorsque le controleur principal demande l'affichage du Market	
					$smarty = new Smarty();
					$smarty->display("../vues/market.html");
					break;
					
				case "ListerTransactions"  : 
					//On retourne les animaux en vente
					$animaux = $this->_mdlTransaction->listerTransactionsEnCours();
					
					echo json_encode($animaux);
					break;
						
				default : //On redirige vers la page d'erreur
					$this->afficherErreur('Action "'.$action.'" indéfinie',
									      'L\'erreur est survenue dans le controleur principal, méthode traiterAction(). ',
									      '');
					break;
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


if(isset($_GET[ 'actionMarket']))
{
	$action = $_GET['actionMarket'];
	//Instanciation du controleur principal et demande d'initialisation du jeu
	$controleurMarket = ControleurMarket::getInstance();
	//Le controleur traite l'action
	$controleurMarket->traiterAction($action);
}

?>