<?php
require_once './../mapping/CombatDAO.php';
require_once 'AnimalModele.php';
require_once 'JoueurModele.php';

/**
 *
 * Modèle Combat. Intéragit avec le DAO et s'occupe de faire les vérifications.
 *
 */
class CombatModele
{
	/** Instance unique */
	private static $_instance;

	/** Le DAO Animal */
	private $_dao = null;

	/** Le modèle Animal */
	private $_mdlAnimal = null;

	/** Le modèle Joueur */
	private $_mdlJoueur = null;
	
	/**
	 * Constructeur.
	 */
	private function __construct()
	{
		$this->_dao = new CombatDAO();
		$this->_mdlAnimal = AnimalModele::getInstance();
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
	 * Permet d'obtenir le DAO du Combat.
	 * @return Le DAO du Combat.
	 */
	public function getDAOCombat()
	{
		return $this->_dao;
	}

	/**
	 * Permet d'obtenir le modèle de l'animal.
	 * @return Le modèle de l'Animal.
	 */
	public function getAnimalModel()
	{
		return $this->_mdlAnimal;
	}
	
	/** Permet d'obtenir le modèle du joueur.
	 * @return Le modèle du joueur.
	 */
	public function getJoueurModele()
	{
		return $this->_mdlJoueur;	
	}
	
	/**
	 * Permet d'ajouter un combat.
	 */
	public function ajouterCombat($idAnimal, $date, $money)
	{
		$idCombat = $this->_dao->ajouterCombat($idAnimal, $date, $money);
		return $idCombat;
	}	
	
	
	/**
	 * Permet de s'inscrire un combat.
	 */
	public function inscriptionCombat($idCombat, $idAnimal)
	{
		return $this->_dao->inscriptionCombat($idCombat, $idAnimal);
	}
	
	/**
	 * Permet de lister les combats suivant un niveau d'animal.
	 */
	public function listerCombatsParNiveau($level, $idJoueur)
	{
		return $this->_dao->listerCombats($level, $idJoueur);
	}
	
	/**
	 * Ajoute une action d'un combat en base.
	 * @param String $idCombat
	 * @param String $idAnimal
	 * @param String $idCompetence
	 * @param int $degats
	 * @param int $date
	 */
	public function ajouterAction($idCombat, $idAnimal, $idCompetence, $degats, $date)
	{
		return $this->_dao->ajouterAction($idCombat, $idAnimal, $idCompetence, $degats, $date);
	}
	
	/**
	 * Assign dans l'objet Smarty passé en paramètre les combats.
	 * @param $smarty L'objet Smarty sur lequel faire les assignations.
	 * @return L'objet Smarty assigné
	 */
	public function assignCombatParNiveau($level, $smarty)
	{
		$combats = array();
		$combats = $this->listerCombatsParNiveau($level);
		$listeCombats = array();
	
		foreach ($combats as $combat) {
			$animal = $this->_mdlAnimal->getAnimal($combat['idAnimal']);
			$idJoueur = $animal->getProprietaire();
			$joueur = $this->_mdlJoueur->getJoueur($idJoueur);
				
			$tab['idCombat'] = $combat['idCombat'];
			$tab['idJoueur'] = $combat['idJoueur'];
			$tab['nom_animal'] = $animal->getNomAnimal();
			$tab['vie_animal'] = $animal->getVie();
			$tab['attaque_animal'] = $animal->getAttaque();
			$tab['defense_animal'] = $animal->getDefense();
			$tab['niveau_animal'] = $animal->getNiveau();
			if ($joueur->getNomJoueur() === null)
			{
				$tab['nom_joueur'] = "Joueur anonyme";
			}
			else
			{
				$tab['nom_joueur'] = $joueur->getNomJoueur();
			}
				
			array_push($listeCombats, $tab);
		}
		$smarty->assign("combats",$listeCombats);
		return $smarty;
		return $this->_dao->ajouterAction($idCombat, $idAnimal, $idCompetence, $degats, $date);
	}
	
	
	/**
	 * Retourne les combats correspondants au level de l'animal passé en paramètre
	 */
	public function getCombatsParNiveau($level, $idJoueur = null)
	{
		$combats = array();
		$combats = $this->listerCombatsParNiveau($level, $idJoueur);
		$tab = array();
		$listeCombats = array();
		
		foreach ($combats as $combat) {
			$animal = $this->_mdlAnimal->getAnimal($combat['idAnimal']);
			if($idJoueur == null)
				$idJoueur = $animal->getProprietaire();
			$joueur = $this->_mdlJoueur->getJoueur($idJoueur);
		
			$tab['idAnimal'] = $combat['idAnimal'];
			$tab['idCombat'] = $combat['idCombat'];
			$tab['idJoueur'] = $combat['idJoueur'];
			$tab['nom_animal'] = $animal->getNomAnimal();
			$tab['vie_animal'] = $animal->getVie();
			$tab['attaque_animal'] = $animal->getAttaque();
			$tab['defense_animal'] = $animal->getDefense();
			$tab['niveau_animal'] = $animal->getNiveau();
			$tab['nbVictoires'] = $animal->getNbVictoires();
			$tab['nbDefaites'] = $animal->getNbDefaites();
			$tab['nbAbandons'] = $animal->getNbAbandons();
			
			//echo "NbWins : ".$tab['nbVictoires']." | NbLoses : ".$tab['nbDefaites']." | NbAb : ".$tab['nbAbandons'];
							
			if ($joueur->getNomJoueur() === null)
			{
				$tab['nom_joueur'] = "Joueur anonyme";
			}
			else
			{
				$tab['nom_joueur'] = $joueur->getNomJoueur();
			}
		
			array_push($listeCombats, $tab);
		}
		
		return $listeCombats;
	}
		
	public function deleteCombatByIdJoueur($idJoueur)
	{
		return $this->_dao->deleteCombatByJoueur($idJoueur);
	}
	
	public function deleteCombatByIdCombat($idCombat)
	{
		return $this->_dao->deleteCombatByIdCombat($idCombat);
	}
	
	/**
	 * Permet de connaitre le tour que doit jouer l'animal passé en parametre.
	 * @param String $idCombat
	 * @param String $idAnimal
	 * @return defense Si l'animal doit défendre
	 * 	       attaque Si l'animal doit attaquer
	 * 		   wait    Si l'animal doit attendre
	 */
	public function getMyTurn($idCombat, $idAnimal)
	{
		$action = $this->_dao->getLastAction($idCombat);
		$competence = $this->_mdlAnimal->getCompetenceById($action[ 'Competence_idCompetence' ]);
		
		//Au début du combat
		if(empty($action))
		{
			$combat = $this->_dao->getCombat($idCombat);
			
			if($combat->getAnimal1() == $idAnimal)
			{
				return 'attaque';
			}
			else 
			{
				return 'wait';
			}
		}
		
		
		if($action[ 'Animal_idAnimal' ] == $idAnimal)
		{
			if($competence->getType() == 'attaque')
			{
				return 'wait';
			}
			else 
			{
				return 'attaque';
			}
		}
		else
		{
			if($competence->getType() == 'attaque')
			{
				return 'defense';
			}
			else
			{
				return 'wait';
			}
		}
	}
	
	/**
	 * Permet d'obtenir la derniere action effectuée pour un combat donné.
	 * @param String $idCombat
	 * @return Un tableau contenant les caractéristiques de l'action.
	 */
	public function getLastAction($idCombat)
	{
		return $this->_dao->getLastAction($idCombat);
	}
	
	/**
	 * Permet de connaitre le résultat d'une action en rapport à un combat.
	 * @param String $idCombat
	 * @return array Tableau contenant le message, l'animal blessé et sa vie restante (en %).
	 */
	public function getActionResult($idCombat)
	{
		//A completer
	}
	
	/**
	 * Permet d'obtenir l'action correspondant à l'identifiant donné.
	 * @param String $idAction
	 * @return Un tableau contenant les caractéristiques de l'action.
	 */
	public function getAction($idAction)
	{
		return $this->_dao->getAction($idAction);
	}
	
	/**
	 * Permet de connaitre les dégats infligés en fonction d'un couple attaque/defense.
	 * @param String $idCombat
	 * @param String $actionAttaque Un tableau contenant les infos de l'action correspondant à l'attaque lancée.
	 * @return int Dégâts infligés
	 */
	public function calculerDegats($idCombat, $actionAttaque)
	{
		//On récupère la défense lancée
		$actionDefense = $this->getLastAction($idCombat);
		
		//On récupère les animaux impliqués dans le combat
		$animalAttaquant = AnimalModele::getInstance()->getAnimal($actionAttaque[ 'Animal_idAnimal' ]);
		$animalDefenseur = AnimalModele::getInstance()->getAnimal($actionDefense[ 'Animal_idAnimal' ]);
		
		//On récupère l'attaque et la défense
		$competenceAttaque =  AnimalModele::getInstance()->getCompetenceById($actionAttaque[ 'Competence_idCompetence' ]);
		$competenceDefense =  AnimalModele::getInstance()->getCompetenceById($actionDefense[ 'Competence_idCompetence' ]);
		
		//On calcule les dégats 
		$degatsAttaque  = $competenceAttaque->getDegats();
		$ptsAttaque = $animalAttaquant->getAttaque();
		$ptsDefense = $animalDefenseur->getDefense();
		$absorption = $this->_dao->getAbsorption($competenceDefense->getIdCompetence(), $competenceAttaque->getCodePuissance());
		
		$degatsInfliges = ($degatsAttaque + ($degatsAttaque * ($ptsAttaque/100))) * (1 - (($absorption * $ptsDefense)/100) - $absorption);
		$degatsInfliges = round($degatsInfliges, 0, PHP_ROUND_HALF_UP); //On arrondit par excès
		
		return $degatsInfliges;
	}
	
	/**
	 * Permet de modifier les dégats provoqués par une attaque dont l'identiant de l'action concernée est passée en paramètre.
	 * @param $idAction L'identifiant de l'action
	 * @param $degats La nouvelle valeur des degats
	 */
	public function updateDegatsProvoques($idAction, $degats)
	{
		$this->_dao->updateDegatsProvoques($idAction, $degats);
	}
	
	/**
	 * Permet d'obtenir les dégats provoqués par une attaque dont l'identiant de l'action concernée est passée en paramètre.
	 * @param $idAction L'identifiant de l'action
	 * @param $degats La nouvelle valeur des degats
	 * @return Les degats provoques
	 */
	public function getDegatsProvoques($idAction)
	{
		return $this->_dao->getDegatsProvoques($idAction);
	}
	
	/**
	 * Permet de récompenser le propriétaire de l'animal vainqueur du combat.
	 * @param $idVainqueur L'identifiant de l'animal vainqueur
	 * @return Les gains du vainqueur
	 */
	public function recompenserVainqueurCombat($idVainqueur)
	{
		$idCombat = $_SESSION [ 'combat' ][ 'idCombat' ];
		//Calcul de la somme à créditer au vainqueur
		$sommeEngageeGagnant = $this->_dao->getSommeEngagee($idCombat, $idVainqueur);
		
		$animal1 = $_SESSION [ 'combat' ][ 'animal1' ];
		$animal2 = $_SESSION [ 'combat' ][ 'animal2' ];
		if($animal1->getIdAnimal() == $idVainqueur)
		{
			$idPerdant = $animal2->getIdAnimal();
		}
		else
		{
			$idPerdant = $animal1->getIdAnimal();
		}

		$sommeEngageePerdant = $this->_dao->getSommeEngagee($idCombat, $idPerdant);

		//Calcul de la difference
		$difference = $sommeEngageeGagnant - $sommeEngageePerdant;
	
		$gains = (2 * $sommeEngageeGagnant) + $sommeEngageePerdant;
		if($difference < 0) //On soustrait l'écart entre les mises
		{
			$gains += $difference;
		}
		
		//On récompense le vainqueur
		$idJoueurVainqueur = $this->_mdlJoueur->getJoueurByAnimal($idVainqueur);
		$this->_mdlJoueur->augmenterCredit($idJoueurVainqueur->getIdFacebook(), $gains);
		
		//On ajoute 1 victoire au vainqueur
		$this->_mdlJoueur->addVictoire($idVainqueur);
		
		//On ajoute 1 défaite au perdant
		$this->_mdlJoueur->addDefaite($idPerdant);
		
		return $gains;
	}
	
	/**
	 * Gère l'abandon d'un animal. Redonne la somme d'argent engagée par l'adversaire. Inscrit l'abandon dans le profil du joueur concerné.
	 * @param $idAnimalAbandon L'identifiant de l'animal ayant abandonné
	 * @param $idAnimalAdversaire L'identifiant de l'animal n'ayant pas abandonné
	 */
	public function gererAbandon($idAnimalAbandon, $idAnimalAdversaire)
	{
		@session_start();
		$idCombat = $_SESSION [ 'combat' ][ 'idCombat' ];
		//On rembourse la somme d'argent engagée par le joueur n'ayant pas abandonné
		$sommeEngageeAdversaire = $this->_dao->getSommeEngagee($idCombat, $idAnimalAdversaire);
		
		$joueurAdversaire = $this->_mdlJoueur->getJoueurByAnimal($idAnimalAdversaire);
		$this->_mdlJoueur->augmenterCredit($joueurAdversaire->getIdFacebook(), $sommeEngageeAdversaire);
		
		//On inscrit l'abandon dans le profil du joueur concerné
		$this->_mdlJoueur->addAbandon($idAnimalAbandon);
		
	}		
	
	
	/**
	 * Permet à un animal de rejoindre un combat déjà créé
	 * @param $idCombat l'identifiant du combat que souhaite rejoindre l'animal
	 * @param $idAnimal l'identifiant de l'animal souhaitant rejoindre le combat
	 * @param $mise l'argent qu'a investi le propriétaire dans le combat
	 */
	public function rejoindreCombat($idCombat, $idAnimal, $mise)
	{
		return $this->_dao->rejoindreCombat($idCombat, $idAnimal, $mise);
	}
	
	/**
	 * Permet de démarrer un combat
	 * @param $idCombat l'identifiant du combat à démarrer
	 */
	public function startCombat($idCombat)
	{
		return $this->_dao->startCombat($idCombat);
	}
	
	/**
	 * 
	 * @param $idCombat
	 */
	public function getAllAboutCombat($idCombat)
	{
		//Get the fight with id of animals
		$combat = $this->_dao->getAnimalsByCombat($idCombat);
		
		$idAnimal1 = $combat[0][ 'Animal_idAnimal' ];
		$idAnimal2 = $combat[1][ 'Animal_idAnimal' ];
	
		$animal1 = $this->_mdlAnimal->getAnimal($idAnimal1);
		$animal2 = $this->_mdlAnimal->getAnimal($idAnimal2);
		
		return array('idCombat' => $idCombat,
				'animal1' => $animal1,
				'animal2' => $animal2);
	}
	
	/**
	 * 
	 * @param unknown_type $idCombat
	 */
	public function isThereAnOpponent($idCombat)
	{
		$nbPlayers = $this->_dao->countPlayersByCombat($idCombat);
		if($nbPlayers == 2)
			return true;
		return false;
	}
}


?>