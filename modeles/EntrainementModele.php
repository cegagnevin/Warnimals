<?php
require_once './../settings/Configuration.php';
require_once './../mapping/EntrainementDAO.php';
require_once './../mapping/AnimalDAO.php';
require_once './../metiers/EntrainementIndividuel.php';
require_once './../metiers/EntrainementCollectif.php';
require_once './../modeles/JoueurModele.php';
require_once './../modeles/AnimalModele.php';

/**
 *
 * Modèle Entrainement. Intéragit avec le DAO et s'occupe de faire les vérifications.
 *
 */
class EntrainementModele
{
	/** Instance unique */
	private static $_instance;
	
	/** Le DAO Entrainement*/
	private $_daoEntrainement = null;
		
	/** Le DAO Animal*/
	private $_daoAnimal = null;
	
	/**
	 * Constructeur.
	 */
	private function __construct()
	{
		$this->_daoEntrainement = new EntrainementDAO();
		$this->_daoAnimal = new AnimalDAO();
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
	 * Permet d'obtenir le DAO de l'Entrainement.
	 * @return Le DAO de l'Entrainement.
	 */
	public function getDAOEntrainement()
	{
		return $this->_daoEntrainement;
	}
	
	/**
	 * Permet d'obtenir le DAO d'Animal.
	 * @return Le DAO d'Animal.
	 */
	public function getDAOAnimal()
	{
		return $this->_daoAnimal;
	}
	
	/**
	 *  Permet d'obtenir une offre aléatoire en fonction d'un niveau.
	 *  Si $niveauAnimal = [ 20 ; 30 ], attaque_offre + defense_offre + vie_offre + levelUp = [ 1 ; 2]
	 *  Si $niveauAnimal = [ 10 ; 20 ], attaque_offre + defense_offre + vie_offre + levelUp = [ 1 ; 4]
	 *  Si $niveauAnimal = [  0 ; 10 ], attaque_offre + defense_offre + vie_offre + levelUp = [ 1 ; 6]
	 * @param int $niveau Le niveau donné.
	 * @return Une offre aléatoire.
	 */
	public function getOffreAleatoireByNiveau($niveau)
	{
		return $this->_daoEntrainement->getOffreAleatoireByNiveau($niveau);
	}

	/** 
	 * Permet d'obtenir une proposition d'entrainement individuel correspondant au niveau passé en paramètre.
	 * @param int $niveauAnimal Le niveau de l'animal
	 * @return Un entrainement individuel
	 */
	public function getEntrainementIndividuelByNiveau($niveauAnimal)
	{
		//Si $niveauAnimal = [ 20 ; 30 ], attaque_offre + defense_offre + vie_offre + levelUp = [ 1 ; 2] 
		//Si $niveauAnimal = [ 10 ; 20 ], attaque_offre + defense_offre + vie_offre + levelUp = [ 1 ; 4]
		//Si $niveauAnimal = [  0 ; 10 ], attaque_offre + defense_offre + vie_offre + levelUp = [ 1 ; 6]
		
		$entrainement = null;
		//On récupère l'offre du jour adaptée au niveau de l'animal
		$path = WARNIMALS_PATH.'/files/offres_entrainements_individuels.txt';
		$file = fopen($path, 'r');

		if($file != null)
		{	
			$dateOffre = trim(fgets($file));
			if($dateOffre == date("d/m/Y")) //Si c'est bien l'offre du jour
			{	
				$tab_offres = unserialize(fgets($file));
				
				//On prend l'offre adaptée
				if($niveauAnimal > 0 && $niveauAnimal <= 10)
					$offre = $tab_offres[ 'offre1' ];
				elseif($niveauAnimal > 10 && $niveauAnimal <= 20)
					$offre = $tab_offres[ 'offre2' ];
				else $offre = $tab_offres[ 'offre3' ];
				
				
				$prix = COEF_MULTI_PRIX_ENTRAINEMENT_IND * ceil((20 * $niveauAnimal) + ($niveauAnimal * $offre->getSommePoints()) + COUT_SUPP_ENTRAINEMENT);
				$duree = ceil($prix * COEF_MULTI_DUREE_ENTRAINEMENT_IND);
				$dateDebut = time();
					
				$entrainement =  new EntrainementIndividuel(null, $duree, $prix, $dateDebut, $offre, null);
			}
			fclose($file);
		}
		return $entrainement;
	}
	
	/**
	 * Créé un entrainement collectif aléatoire en base. Un entrainement collectif commence aléatoirement entre 8h et 22h mais doit être au minimum espacé de 2h avec les autres entrainements collectifs.
	 * Un entrainement collectif est moins couteux qu'un entrainement individuel mais il dure plus longtemps et il est possible qu'il
	 * n'ait pas lieu si le nombre minimum de participants n'est pas respecté.
	 * $return L'identifiant de l'entrainement collectif créé.
	 */
	public function createAleatoryCollectiveTraining()
	{
		$idEntrainementCree = null;
		//On calcule la date de début de l'entrainement
		$dateDebut = $this->getAleatoryTimeForCollectiveTraining();
		if($dateDebut != -1) //S'il y a une date de dispo
		{
			//On récupère une offre au hasard dans la base de données
			$offre = $this->getOffreAleatoireByNiveau(1); //On passe le niveau 1 en paramètre pour faire un random sur l'ensemble des offres (pas de restriction)
			//On calcule le niveau manimum des animaux pouvant participer à l'entrainement
			if(in_array( $offre->getSommePoints(), array(5, 6)))
					$niveauMax = rand(1, 10);
			elseif(in_array( $offre->getSommePoints(), array(3, 4)))
					$niveauMax = rand(11, 20);
			else //if(in_array( array(1, 2), $offre->getSommePoints()))
					$niveauMax = rand(21, 30);
			//On calcule le prix de l'entrainement
			$prix = ceil( COEF_MULTI_PRIX_ENTRAINEMENT_CO * (( $niveauMax * 20 ) + ( $niveauMax * $offre->getSommePoints() ) + COUT_SUPP_ENTRAINEMENT) );
			//On calcule la durée de l'entrainement
			$duree = ceil($prix * COEF_MULTI_DUREE_ENTRAINEMENT_CO);
			//On récupere le nombre de participants min 
			$nbParticipantsMin = NB_PARTICIPANTS_MIN;
			//On récupere le nombre de participants max
			$nbParticipantsMax = NB_PARTICIPANTS_MAX;
			//On créé l'entrainement collectif en base
			$idEntrainementCree = $this->_daoEntrainement->ajouterEntrainement($duree, $prix, $dateDebut, 'collectif', $niveauMax, $nbParticipantsMin, $nbParticipantsMax, $offre->getIdOffre());
			if($idEntrainementCree == null)
				throw new Exception("Problème lors de la création d'un entrainement collectif en base.");
		}
		return $idEntrainementCree;
	}
	
	/**
	 * Permet d'obtenir une date aléatoire pour le début d'un entrainement collectif.
	 * Cette date est compris entre 8h et 22h du jour courant et est exprimée en secondes.
	 * @return int La date exprimée en secondes.
	 * 		   -1 s'il n'y a aucun créneau dispo
	 */
	public function getAleatoryTimeForCollectiveTraining()
	{
		//On récupère les entrainements collectifs du jour par date de début croissantes.
		$entrainementsCo = $this->_daoEntrainement->getCollectiveTrainingsOfToday();
		
		//On genere des dates aleatoires dans les intervalles de temps dispo
		$datesAlea = array();
		
		$timeCurrent = mktime(8, 0, 0, date("n"), date("j"), date("Y")); //Aujourd'hui à 8h
		if(time() > $timeCurrent) //S'il est plus de 8h, on commence à partir de la date actuelle+30mn
			$timeCurrent = time();
		foreach ($entrainementsCo as $entrainement)
		{
			//On récupère la date de début de l'entrainement
			$dateDebut = $entrainement->getDateDebut();
				
			$dateMin = $dateDebut - ECART_MIN;
			$dateMax = $dateDebut + ECART_MIN;
			
			if($dateMin > $timeCurrent) //Créneau de libre, on genere une date dedans
			{
				array_push($datesAlea, rand($timeCurrent, $dateMin));
			}
			if($dateMax > $timeCurrent)
				$timeCurrent = $dateMax;
		}
		if($timeCurrent < mktime(22, 0, 0, date("n"), date("j"), date("Y")))//Si il n'est pas supérieur à 22h
		{
			array_push($datesAlea, rand($timeCurrent, mktime(22, 0, 0, date("n"), date("j"))));
		}
		
		if(count($datesAlea) == 0)
			return -1;
		
		//On retourne une date choisie aléatoirement dans le tableau des dates aléatoires
		$rang = rand(0, 20) % count($datesAlea);
		return $datesAlea[$rang];
	}

	
	
	/**
	 * Assigne les variables Smarty de l'entrainement individuel passé en paramètre.
	 * @param EntrainementIndividuel $entrainementInd Un entrainement individuel
	 * @param Smarty $smarty L'objet Smarty sur lequel faire l'assignation
	 * @return L'objet Smarty avec l'assignation faite.
	 */
	public function assignEntrainementIndividuel(EntrainementIndividuel $entrainementInd, $smarty)
	{
		$tab['idEntrainementInd'] = $entrainementInd->getIdEntrainement();
		$tab['dureeEntrainementInd'] = date("G\hi",$entrainementInd->getDuree());
		$tab['prixEntrainementInd'] = $entrainementInd->getPrix();
		$tab['idOffreInd'] = $entrainementInd->getOffre()->getIdOffre();
		$tab['vieOffreInd'] = $entrainementInd->getOffre()->getVieOffre();
		$tab['attaqueOffreInd'] = $entrainementInd->getOffre()->getAttaqueOffre();
		$tab['defenseOffreInd'] = $entrainementInd->getOffre()->getDefenseOffre();
		$tab['levelUpOffreInd'] = $entrainementInd->getOffre()->getLevelUp();
		
		$smarty->assign("entrainementIndividuel", $tab);
		return $smarty;
	}

	
	/** 
	 * Permet d'inscrire un animal à un entrainement individuel.
	 * @return L'entrainement auquel le joueur a souscrit.
	 */
	public function souscrireEntrainementIndividuel()
	{
		@session_start();
		$joueur = $_SESSION[ 'joueur' ];
		$idJoueur = $joueur->getIdFacebook();
		
		$entrainementIndividuel = null;
		//On vérifie que l'animal est disponible
		$animal = JoueurModele::getInstance()->getAnimalByJoueur($idJoueur);
		
		if(AnimalModele::getInstance()->isAvailable($animal->getIdAnimal()))
		{
			//On vérifie que le joueur a le solde suffisant
			$joueur = JoueurModele::getInstance()->getJoueur($idJoueur);
			$entrainementIndividuel = $this->getEntrainementIndividuelByNiveau($animal->getNiveau());
			$entrainementIndividuel->setDateDebut(time()); //On réajuste la date de début
			if($joueur->getCredit() >= $entrainementIndividuel->getPrix())
			{
				//On créé l'entrainement en base
				$idEntrainement = $this->_daoEntrainement->ajouterEntrainement($entrainementIndividuel->getDuree(),
																			   $entrainementIndividuel->getPrix(),
																			   $entrainementIndividuel->getDateDebut(),
																			   $entrainementIndividuel->getType(),
																			   0,
																			   0,
																			   0,
																		       $entrainementIndividuel->getOffre()->getIdOffre());
				$entrainementIndividuel->setIdEntrainement($idEntrainement);
					
				//On inscrit l'animal à l'entrainement
				AnimalModele::getInstance()->inscriptionEntrainement($animal->getIdAnimal(), $idEntrainement);
				
				//On débite le joueur
				JoueurModele::getInstance()->debiterCredit($idJoueur, $entrainementIndividuel->getPrix());
				//On met a jour le joueur en session
				@session_start();
				$_SESSION[ 'joueur' ]->setCredit($joueur->getCredit() - $entrainementIndividuel->getPrix());
			}
			else throw new Exception("Votre crédit est insuffisant pour soucrire à cet entrainement");

		}
		else throw new Exception("Votre animal est actuellement indisponible");
		
		return $entrainementIndividuel;
	}
	
	/**
	 * Genere 3 offres d'entrainements individuels aléatoires par jour et les sauvegarde dans un fichier.
	 * Structure du fichier :
	 *  - Jour de l'offre au format JJ/MM/YYYY
	 *  - Tableau indexé de la façon suivante :
	 *  		array( 'offre1' => $offre1,
	 *  			   'offre2' => $offre2,
	 *  			   'offre3' => $offre3)
	 *
	 *  @throws Exception Si l'ouverture de fichier a échoué
	 */
	public function gererOffresEntrainementsIndividuels()
	{
		//Ouverture du fichier "offres_entrainements_individuels.txt"
		$path = WARNIMALS_PATH.'/files/offres_entrainements_individuels.txt';
		$file = fopen($path, 'r+');
		if($file != null)
		{
			//On lit la date à laquelle le fichier a été édité pour la dernière fois
			$dateOffres = trim(fgets($file));
			fclose($file); //Fermeture du fichier
				
			if(!$dateOffres || $dateOffres != date("d/m/Y")) //Si le fichier est vierge ou s'il ne date pas d'ajd
			{
				//Génération des offres
				$offre1 = self::getOffreAleatoireByNiveau(5);
				$offre2 = self::getOffreAleatoireByNiveau(15);
				$offre3 = self::getOffreAleatoireByNiveau(25);
				//Constitution du tableau à sérialiser
				$offresDuJour = array( 'offre1' => $offre1,
						'offre2' => $offre2,
						'offre3' => $offre3);
				$offresDuJour_serialize = serialize($offresDuJour);
	
				//Ouverture du fichier "offres_entrainements_individuels.txt"
				$path = WARNIMALS_PATH.'/files/offres_entrainements_individuels.txt';
				$file = fopen($path, 'r+');
				if($file != null)
				{
					//On écrit dans le fichier
					fwrite($file, date("d/m/Y")."\n"); //La date du jour
					fwrite($file, $offresDuJour_serialize); //Le tableau des offres du jour serialisé.
					fclose($file); //Fermeture du fichier
				}
			}
		}
		else throw new Exception("Le fichier '../files/offres_entrainements_individuels.txt' est introuvable");
	
	}
	
	/**
	 * Gère les entrainements collectifs. Il doit y avoir constamment 3 entrainements collectifs de proposé aux joueurs.
	 * Ces entrainements sont répartis aléatoirement dans une journée (à savoir qu'ils doivent être espacé de 2h minimum).
	 * La date de début d'un entrainement est obligatoirement comprise entre 8h00 et 22h00 (heure du serveur).
	 * Un entrainement collectif est moins couteux qu'un entrainement individuel mais il dure plus longtemps et il est possible qu'il
	 * n'ait pas lieu si le nombre minimum de participants n'est pas respecté.
	 * S'il n'y pas pas d'entrainements collectifs de proposés le jour même :	
	 * 		- De 00h00 à 12h00, 3 entrainements collectifs sont générés
	 * 		- De 12h00 à 17h00, 2 entrainements collectifs sont générés
	 * 		- De 17h00 à 21h00, 1 entrainement collectif est généré.
	 */
	public function gererEntrainementsCollectifs()
	{
		//On récupere la liste des entrainements collectifs proposés aujourd'hui
		$entrainementsCollectifs = $this->_daoEntrainement->getCollectiveTrainingsOfToday();
		//Si les entrainements collectifs du jour n'ont pas été généré, on le fait
		if(count($entrainementsCollectifs) == 0)
		{
			$time = time();
			//17h00-21h00, on ne propose que 1 entrainement (21h car il faut laisse le temps aux gens de s'inscrire)
			if($time >= mktime(17, 0, 0, date("n"), date("j"), date("Y"))
			   && $time <= mktime(21, 00, 0, date("n"), date("j"), date("Y")))
			{
				$nbEntrainements = 1;
			}
			//12h00-17h00, on ne propose que 2 entrainements
			elseif($time >= mktime(12, 0, 0, date("n"), date("j"), date("Y"))
				   && $time < mktime(17, 0, 0, date("n"), date("j"), date("Y")))
			{
				$nbEntrainements = 2;
			}
			//00h00-12h00, on propose 3 entrainements
			elseif($time > mktime(0, 0, 0, date("n"), date("j"), date("Y")))
			{
				$nbEntrainements = 3;
			}
			else $nbEntrainements = 0;
			
			//On génère N entrainements collectifs aléatoirement
			for($i=0 ; $i<$nbEntrainements ; $i++)
			{
				$this->createAleatoryCollectiveTraining();
			}
		}
	}
	
	
	/**
	 * Permet d'entrainer les animaux ayant finis leur entrainement. Ces animaux voient donc leurs caractéristiques
	 * améliorées en fonction de l'offre de l'entrainement suivi.
	 * S'ils gagnent un niveau, ils apprennent les compétences disponibles à ce niveau atteint.
	 */
	public function entrainerAnimaux()
	{
		
		//On regarde tous les entrainements finis en attente de validation
		$entrainementsEnAttente = $this->_daoEntrainement->getEntrainementsFinisEnAttente();

		//Pour chaque entrainement, on récupère l'animal associé
		foreach($entrainementsEnAttente as $entrainement)
		{
			$animals = $this->_daoEntrainement->getAnimalsByEntrainement($entrainement->getIdEntrainement());
			//On entraine tous les animaux associés à l'entrainement courant
			
			foreach ($animals as $animal)
			{
				//Si l'offre de l'entrainement prévoie un gain de niveau, on passe l'animal au niveau supérieur
				if($entrainement->getOffre()->getLevelUp() == 1)
				{
					$this->_daoAnimal->levelUpAnimal($animal->getIdAnimal());
				}
				
				//Amélioration des caractéristiques de l'animal et validation de l'entrainement pour l'animal
				$this->_daoAnimal->entrainerAnimal($entrainement, $animal->getIdAnimal());
			}
			
			//On supprime l'entrainement de la table t_entrainement et t_entrainement_animal
			$this->_daoEntrainement->deleteTrainingById($entrainement->getIdEntrainement());
		}
	}

	/**
	 * Assigne les variables Smarty des entrainements collectifs du jour.
	 * @param Smarty $smarty L'objet Smarty sur lequel faire l'assignation
	 * @param Animal $animal L'animal du joueur
	 * @return L'objet Smarty avec l'assignation faite.
	 */
	public function assignEntrainementsCollectifs(Smarty $smarty)
	{
		//Récupération des entrainements co du jour
		$entrainementsCo = $this->_daoEntrainement->getCollectiveTrainingsOfToday();
		
		$i=0;
		$tab = array();
		foreach($entrainementsCo as $entrainement)
		{
			$i++;
			$tab["idEntrainementCo$i"] = $entrainement->getIdEntrainement();
			$tab["dateDebutEntrainementCo$i"] = date("j/m/Y \à H\hi",$entrainement->getDateDebut());
			$tab["dureeEntrainementCo$i"] = date("G\hi",$entrainement->getDuree());
			$tab["prixEntrainementCo$i"] = $entrainement->getPrix();
			$tab["vieOffreCo$i"] = $entrainement->getOffre()->getVieOffre();
			$tab["attaqueOffreCo$i"] = $entrainement->getOffre()->getAttaqueOffre();
			$tab["defenseOffreCo$i"] = $entrainement->getOffre()->getDefenseOffre();
			$tab["levelUpOffreCo$i"] = $entrainement->getOffre()->getLevelUp();
			$tab["niveauMaxCo$i"] = $entrainement->getNiveauMax();
			$tab["nbParticipantsMinCo$i"] = $entrainement->getNbParticipantsMin();
			$tab["nbParticipantsMaxCo$i"] = $entrainement->getNbParticipantsMax();
		}
		
		$smarty->assign("entrainementsCollectifs", $tab);
		return $smarty;
	}
	
	/**
	 * Permet d'inscrire un animal à un entrainement collectif
	 * @param L'identifiant de l'entrainement
	 * @return L'entrainement auquel le joueur a souscrit.
	 */
	public function souscrireEntrainementCollectif($idEntrainement)
	{
		@session_start();
		$joueur = $_SESSION[ 'joueur' ];
		$idJoueur = $joueur->getIdFacebook();
	
		$entrainementCollectif = null;
		//On vérifie que l'animal est disponible
		$animal = JoueurModele::getInstance()->getAnimalByJoueur($idJoueur);
		if(AnimalModele::getInstance()->isAvailable($animal->getIdAnimal()))
		{
			//On vérifie que le joueur a le solde suffisant
			$joueur = JoueurModele::getInstance()->getJoueur($idJoueur);
			/** On recupere l'entrainement collectif demandé */
			$entrainementCollectif = $this->_daoEntrainement->getTrainingById($idEntrainement);
			
			if($joueur->getCredit() >= $entrainementCollectif->getPrix())
			{
				// On verifie que l'entrainement n'est pas annulé
				if($entrainementCollectif->getAnnule() == 1)
					throw new LogicException("L'entrainement a été annulé, nous ne pouvez vous y inscrire.");
				// On verifie que l'entrainement n'ait pas deja commencé
				if($entrainementCollectif->getDateDebut() < time())
					throw new LogicException("L'entrainement a déjà commencé, nous ne pouvez vous y inscrire.");
				// On verifie que l'animal ne dépasse pas le niveau max
				if($entrainementCollectif->getNiveauMax() < $animal->getNiveau())
					throw new LogicException("Votre animal a un niveau supérieur au niveau maximal autorisé pour cet entrainement.");
				
				// On verifie qu'il y a encore de la place pour l'entrainement
				$animaux = $this->_daoEntrainement->getAnimalsByEntrainement($idEntrainement);
				if(count($animaux) > $entrainementCollectif->getNbParticipantsMax())
					throw new LogicException("L'entrainement est complet. Vous ne pouvez pas vous y inscrire.");
					
				//On inscrit l'animal à l'entrainement
				AnimalModele::getInstance()->inscriptionEntrainement($animal->getIdAnimal(), $idEntrainement);
	
				//On débite le joueur
				JoueurModele::getInstance()->debiterCredit($idJoueur, $entrainementCollectif->getPrix());
				//On met a jour le joueur en session
				@session_start();
				$_SESSION[ 'joueur' ]->setCredit($joueur->getCredit() - $entrainementCollectif->getPrix());
			}
			else throw new Exception("Votre crédit est insuffisant pour soucrire à cet entrainement");
	
		}
		else throw new Exception("Votre animal est actuellement indisponible");
	
		return $entrainementCollectif;
	}
	
	/**
	 * Annule les entrainements collectifs qui n'ont pas le nombre de participants minimum requis à la date de début de l'entrainement.
	 * Les joueurs aillant souscrit à ces entrainements sont remboursés.
	 */
	public function cancelIncompleteCollectiveTrainings()
	{
		//On récupère tous les entrainements collectifs
		$entrainementsCollectifs = $this->_daoEntrainement->getAllCollectiveTrainings();
		
		foreach ($entrainementsCollectifs as $entrainement)
		{
			if($entrainement->getDateDebut() <= time() && $entrainement->getType() == 'collectif')//Il l'entrainement a commencé, on regarde s'il a assez d'animaux inscrits
			{
				//On récupère les animaux inscrits à cet entrainement
				$animals = $this->_daoEntrainement->getAnimalsEnrolledByTraining($entrainement->getIdEntrainement());

				if(count($animals) < $entrainement->getNbParticipantsMin())//Effectif incomplet
				{
					//On rembourse chaque joueur
					foreach($animals as $animal)
					{
						$idJoueur = $animal->getProprietaire();
						JoueurModele::getInstance()->augmenterCredit($idJoueur, $entrainement->getPrix());
					}
				}
				
				//On supprime l'entrainement car il est annule
				$this->_daoEntrainement->deleteTrainingById($entrainement->getIdEntrainement());
			}
			
		}
		
	}

				
}

	
?>