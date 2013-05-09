<?php
require_once '../modeles/CombatModele.php';
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
class ControleurCombat implements IControleur
{
	/** Instance unique */
	private static $_instance;
	
	/** Modele Animal */
	private $_mdlCombat = null;
	
	/** Modele Joueur */
	private $_mdlJoueur = null;
	
	/** Modele Animal */
	private $_mdlAnimal = null;
	
	/**
	 * Constructeur par défaut. Instancie le modele Joueur et le modele Combat. 
	 */
	private function __construct()
	{
		$this->_mdlCombat = CombatModele::getInstance();
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
	 * Permet d'obtenir le modele Combat (pour les tests).
	 * @return Le modele Combat
	 */
	public function getCombatModele()
	{
		return $this->_mdlCombat;
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
				case "IsAllowed" :
					//On démarre la session
					$joueur =$_SESSION [ 'joueur' ];
					$idJoueur = $joueur->getIdFacebook();
						
					$animal = $this->_mdlJoueur->getAnimalByJoueur($idJoueur);
						
					if($animal == null)
					{
												
						$msg = array(
								'error' => "Vous devez acheter un animal dans le market avant de penser a vous battre !"
							);
						echo json_encode($msg);
						break;
					}
					
					$msg = array(
							'ok' => true
						);
					echo json_encode($msg);
					break;
					
				case "Arene" :
					//Interface à deux boutons 'Créer combat' et et 'Rejoindre combat'
					$smarty = new Smarty();
					$smarty->assign("action", "menu");
					$smarty->display("../vues/indexCombats.html");
					break;
				
				case "getCreditJoueur":
					//On démarre la session
					$joueur =$_SESSION [ 'joueur' ];
					$idJoueur = $joueur->getIdFacebook();
					
					$credit = array(
								'credit' => $joueur->getCredit()
							);
					
					echo json_encode($credit);
					break;
					
				case "clicCreer" :
					//On démarre la session
					@session_start();
					$smarty = new Smarty();
					$joueur = $_SESSION [ 'joueur' ];
					$date = time();
					$idJoueur = $joueur->getIdFacebook();

					$moneyToGive = $_GET['money'];
					$playerMoney = $joueur->getCredit();
					
					if($moneyToGive > $playerMoney || $moneyToGive < 0)
					{
						$msg = array(
								'error' => "Vous devez donner une somme d'argent positive inférieure à votre crédit total !"
						);
							
						echo json_encode($msg);						
						break;
					}
					
				 	$idAnimal = $this->_mdlJoueur->getAnimalByJoueur($idJoueur)->getIdAnimal();
					$idCombat = $this->_mdlCombat->ajouterCombat($idAnimal, $date, $moneyToGive);
					$this->_mdlJoueur->debiterCredit($idJoueur, $moneyToGive);
					$joueur->setCredit($joueur->getCredit()-$moneyToGive);
					
					$_SESSION [ 'combat' ] = array('idCombat' => $idCombat,
													'sommeJ1' => $moneyToGive);
					
					$niveauAnimal = $this->_mdlAnimal->getLevelById($idAnimal);
					$combats = $this->_mdlCombat->getCombatsParNiveau($niveauAnimal, $idJoueur);
										
					echo json_encode($combats);	
					break;				

				case "clicRejoindre" :
					//On démarre la session
					@session_start();
					$joueur = $_SESSION [ 'joueur' ];
					
					$combats = array();
					
					if(isset($_SESSION [ 'combat' ][ 'idCombat' ]))
					{
						unset($_SESSION [ 'combat' ]);	
					}
					
					$idJoueur = $joueur->getIdFacebook();
					$idAnimal = $this->_mdlJoueur->getAnimalByJoueur($idJoueur)->getIdAnimal();
					
					$niveauAnimal = $this->_mdlAnimal->getLevelById($idAnimal);					
					
					$combats = $this->_mdlCombat->getCombatsParNiveau($niveauAnimal, $idJoueur);
				
					echo json_encode($combats);
					break;
					
				case "combatRejointVerif" :
					//On démarre la session
					@session_start();
					$joueur = $_SESSION [ 'joueur' ];
					$idJoueur = $joueur->getIdFacebook();
					
					$idCombatToJoin = $_GET['idCombat'];
					
					//Supprimer le combat déjà créé s'il y en a un
					if(isset($_SESSION [ 'combat' ][ 'idCombat' ]))
					{
						$this->_mdlCombat->deleteCombatByIdCombat($_SESSION [ 'combat' ][ 'idCombat' ]);
						$this->_mdlJoueur->augmenterCredit($idJoueur, $_SESSION [ 'combat' ][ 'sommeJ1']);
						$joueur->setCredit($joueur->getCredit()+$_SESSION [ 'combat' ][ 'sommeJ1']);			
					}
				
					$data = array(
							'idCombat' => $idCombatToJoin,
							'credit' => $joueur->getCredit()
					);
										
					echo json_encode($data);
					break;
					
				case "finalizeCombatRejoint" :
					//On démarre la session
					@session_start();
					$joueur = $_SESSION [ 'joueur' ];
					
					$idJoueur = $joueur->getIdFacebook();
					$idAnimal = $this->_mdlJoueur->getAnimalByJoueur($idJoueur)->getIdAnimal();
					$idCombat= $_GET['idCombat'];
					$money  = $_GET['money'];
					
					//Insère la ligne dans la bdd
					$this->_mdlCombat->rejoindreCombat($idCombat, $idAnimal, $money);
					$this->_mdlJoueur->debiterCredit($idJoueur, $money);
					$this->_mdlCombat->startCombat($idCombat);
					$dataCombat = $this->_mdlCombat->getAllAboutCombat($idCombat);
					
					$_SESSION [ 'combat' ] = $dataCombat;
					
					$data = array(
							'idCombat' => $idCombat
					);
					
					echo json_encode($data);
					break;
					
				case "finalizeCombatCree" :
						//On démarre la session
						@session_start();
						$joueur = $_SESSION [ 'joueur' ];
						/*
						$_SESSION [ 'combat' ] = array('idCombat' => $idCombat,
								'sommeJ1' => $moneyToGive);*/
							
						$idJoueur = $joueur->getIdFacebook();
						$idAnimal = $this->_mdlJoueur->getAnimalByJoueur($idJoueur)->getIdAnimal();
						$idCombat= $_SESSION [ 'combat' ][ 'idCombat'];
						
						$dataCombat = $this->_mdlCombat->getAllAboutCombat($idCombat);
							
						$_SESSION [ 'combat' ] = $dataCombat;
							
						$data = array(
								'idCombat' => $idCombat
						);
							
						echo json_encode($data);
						break;
						
				case "isThereAnOpponnent" :
					@session_start();
					if(isset($_SESSION [ 'combat' ][ 'idCombat' ]))
					{
						$idCombat= $_SESSION [ 'combat' ][ 'idCombat' ];
						
						$ok = $this->_mdlCombat->isThereAnOpponent($idCombat);
						
						if($ok == true)
						{
							$data = array(
									'idCombat' => $idCombat
							);
							echo json_encode($data);
							break;
						}
					}
					echo json_encode(array(-1));
					break;
						
				case "InitialisationCombat" : 
					@session_start();
					/*
					$_SESSION [ 'combat' ] = array('idCombat' => 'C0001',
												   'animal1' => new Animal('A2', null, 'Lapin', 20, 13, 16, 1, 'Lapin'),
												   'animal2' => new Animal('A1', null, 'T-Rex', 12, 7, 25, 1, 'T-Rex'));
					*/
					//---------------------------
					$animalA = $_SESSION [ 'combat' ][ 'animal1' ];
					$animalB = $_SESSION [ 'combat' ][ 'animal2' ];
					
					
					$animal = $this->_mdlJoueur->getAnimalByJoueur($_SESSION[ 'joueur' ]->getIdFacebook());
					$competences = AnimalModele::getInstance()->getCompetencesByAnimal($animal->getIdAnimal());
					
					if($animalA->getIdAnimal() != $animal->getIdAnimal())
					{
						$animal2 = $animalA;
					}
					else 
					{
						$animal2 = $animalB;
					}

					$array = array(
									'idFacebook' => $_SESSION[ 'joueur' ]->getIdFacebook(),
									'nomJoueur'  => $_SESSION[ 'joueur' ]->getNomJoueur(),
									'idAnimal'   => $animal->getIdAnimal(),
									'nomAnimal'  => $animal->getNomAnimal(),
									'vieAnimal'  => $animal->getVie(),
									'niveauAnimal'  => $animal->getNiveau(),
									'defenseAnimal' =>$animal->getDefense(),
									'attaqueAnimal' =>$animal->getAttaque(),
									'raceAnimal' => $animal->getRace(),
									'competencesAnimal' => $competences,
							
									'idAnimal2' => $animal2->getIdAnimal(),
									'niveauAnimal2' => $animal2->getNiveau(),
									'raceAnimal2' => $animal2->getRace(),
									'vieAnimal2' => $animal2->getVie()
									);
					echo json_encode($array);
					break;
					
				case "NewActionCombat" :
					$idCombat = $_SESSION [ 'combat' ][ 'idCombat' ];
					$idAnimal = $_GET[ 'idAnimal' ];
					$idCompetence = $_GET[ 'idCompetence' ];
					$date = time();
					$lastAction = $this->_mdlCombat->getLastAction($idCombat);
					$idAction = $this->_mdlCombat->ajouterAction($idCombat, $idAnimal, $idCompetence, 0, $date);
					$competence = $this->_mdlAnimal->getCompetenceById($idCompetence);
					$_SESSION[ 'infos_combat' ]['action_attaque'] = $idAction;
					if($competence->getType() == 'defense')
					{
						
						$degats = $this->_mdlCombat->calculerDegats($idCombat, $lastAction);
						$this->_mdlCombat->updateDegatsProvoques($idAction, $degats);
						
						//On met les infos en session pour faciliter le travail lors de l'affichage du resultat
						$_SESSION[ 'infos_combat' ] = array('idActionAttaque' => $lastAction['idAction'],
															'idActionDefense' => $idAction,
															'degats'          => $degats);
					}
					echo 1;//On indique que ca s'est bien passé
					break;
				
				case "isMyTurn" :
					if(isset($_SESSION [ 'combat' ][ 'idCombat' ]))
					{
						$idCombat = $_SESSION [ 'combat' ][ 'idCombat' ];
						$idAnimal = $_GET[ 'idAnimal' ];
						echo $this->_mdlCombat->getMyTurn($idCombat, $idAnimal);
					}
					break;
					
				case "GetResult" :
					$ok = false;
					
					$degats = $this->_mdlCombat->getDegatsProvoques($_SESSION[ 'infos_combat' ]['action_attaque']);
					
					
					if(0 != $degats)
					{
						$ok = true;
					}
					if(isset($_SESSION [ 'infos_combat' ]))
					{
						$ok = true;
					}
					
					if($ok)
					{
						//On récupere les 2 actions
						$actionAttaque = $this->_mdlCombat->getAction($_SESSION [ 'infos_combat' ][ 'idActionAttaque' ]);
						$actionDefense = $this->_mdlCombat->getAction($_SESSION [ 'infos_combat' ][ 'idActionDefense' ]);
						
						$animalAttaquant = $this->_mdlAnimal->getAnimal($actionAttaque['Animal_idAnimal']);
						$animalDefenseur = $this->_mdlAnimal->getAnimal($actionDefense['Animal_idAnimal']);
						
						$attaque = $this->_mdlAnimal->getCompetenceById($actionAttaque['Competence_idCompetence']);
						$defense = $this->_mdlAnimal->getCompetenceById($actionDefense['Competence_idCompetence']);
						
						$tab['message'][] = $animalAttaquant->getNomAnimal().' lance '.$attaque->getNomCompetence();
						$tab['message'][] = $animalDefenseur->getNomAnimal().' défend avec '.$defense->getNomCompetence();
						$tab['message'][] = $animalDefenseur->getNomAnimal().' perd '.$_SESSION [ 'infos_combat' ][ 'degats' ].' pts de vie';
						$tab['animalBlesse'] = $animalDefenseur->getIdAnimal();
						$tab['degats'] = $_SESSION [ 'infos_combat' ][ 'degats' ];
						
						unset($_SESSION[ 'infos_combat' ]);
						
						echo json_encode($tab);
						break;
					}

					echo json_encode(array($degats));
					break;
				
				case "FinCombat":
					$idAnimalVainqueur = $_GET[ 'idAnimalVainqueur' ];
					
					//On récompense le vainqueur
					$gains = $this->_mdlCombat->recompenserVainqueurCombat($idAnimalVainqueur);
					
					//On indique que l'animal perdant est mort
					@session_start();
					$animal1 = $_SESSION [ 'combat' ][ 'animal1' ];
					$animal2 = $_SESSION [ 'combat' ][ 'animal2' ];
					$idPerdant = ($idAnimalVainqueur == $animal1->getIdAnimal()) ? $animal2->getIdAnimal : $animal1->getIdAnimal();
					
					$this->_mdlAnimal->declarerAnimalMort($idPerdant);
					
					$tab = array();
					$tab['gains'] = $gains;
					echo json_encode($tab);
					
					break;
					
				case "Abandon":
					$idAnimalAbandon = $_GET['idAnimalAbandon'];
					
					@session_start();
					$animal1 = $_SESSION [ 'combat' ][ 'animal1' ];
					$animal2 = $_SESSION [ 'combat' ][ 'animal2' ];
					
					$idAnimalAdversaire = ($idAnimalAbandon == $animal1->getIdAnimal()) ? $animal2->getIdAnimal : $animal1->getIdAnimal();
					$this->_mdlCombat->gererAbandon($idAnimalAbandon, $idAnimalAdversaire);
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


if(isset($_GET[ 'actionCombat']))
{
	$action = $_GET['actionCombat'];
	//Instanciation du controleur principal et demande d'initialisation du jeu
	$controleurCombat = ControleurCombat::getInstance();
	//Le controleur traite l'action
	$controleurCombat->traiterAction($action);
}

?>