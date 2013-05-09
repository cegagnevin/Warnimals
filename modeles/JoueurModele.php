<?php
require_once '../../lib/facebook/lib/facebook.php';
require_once './../mapping/JoueurDAO.php';
require_once 'AnimalModele.php';

/**
 *
 * Modèle Joueur. Intéragit avec le DAO et s'occupe de faire les vérifications.
 *
 */
class JoueurModele
{
	/** Instance unique */
	private static $_instance;
	
	/** Connexion de l'appli à Facebook */
	private $_facebook = null;
	
	/** Le DAO Joueur */
	private $_daoJoueur = null;
	
	/**
	 * Constructeur.
	 */
	private function __construct()
	{
		if($this->_facebook == null)
		{
			$this->_facebook = new Facebook(array( 'appId'  => FB_APP_ID,
												   'secret' => FB_APP_SECRET,
			));
		}
		$this->_daoJoueur = new JoueurDAO();
	}
	
	/**
	 * Permet d'obtenir la connexion à facebook.
	 * @return La connexion à Facebook.
	 */
	public function getFacebook()
	{
		return $this->_facebook;
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
	 * Permet de débiter le crédit d'un joueur.
	 * @param L'identifiant du joueur à débiter.
	 * @param credit Le solde à débiter au joueur
	 */
	public function debiterCredit($idJoueur, $credit)
	{
		$this->_daoJoueur->updateCredit($credit, "-", $idJoueur);
	}

	/**
	 * Permet d'augmenter le crédit d'un joueur.
	 * @param L'identifiant du joueur à débiter.
	 * @param credit Le solde à créditer au joueur
	 */
	public function augmenterCredit($idJoueur, $credit)
	{
		$this->_daoJoueur->updateCredit($credit, "+", $idJoueur);
	}
	
	/**
	 * Permet d'effectuer l'achat d'un animal.
	 * @param L'identifiant de l'acheteur.
	 * @param L'identifiant de l'animal à acheter.
	 * @param Le prix d'achat.
	 * @throws Exception Si le crédit est insuffisant pour acheter l'animal
	 */
	public function achete($idJoueur, $idAnimal, $prix)
	{
		$mdlAnimal = AnimalModele::getInstance();
		
		//On vérifie que le joueur a les moyens d'acheter l'animal
		$joueur = $this->getJoueur($idJoueur);
		if($joueur->getCredit() < $prix) //Crédit insuffisant
		{
			throw new LogicException("Crédit insuffisant pour acheter l'animal");
		}
		//On verifie que le joueur ne possede pas deja un animal
		$animal = $this->_daoJoueur->getAnimalByJoueur($idJoueur);
		if($animal != null)
		{
			throw new LogicException("Vous possédez déjà un animal, un seul animal à la fois est autorisé.");
		}

		//On débite le crédit du joueur
		$this->_daoJoueur->updateCredit($prix, "-", $idJoueur);
		@session_start();
		$joueur = $_SESSION['joueur'];
		$creditJoueurSession = $joueur->getCredit();
		$joueur->setCredit($creditJoueurSession - $prix);
		
		//On change le propriétaire de l'animal
		$mdlAnimal->changerProprietaire($idJoueur, $idAnimal);
		
	}

	
	/**
	 * Créé un joueur dans la base de données.
	 * @param idJoueur L'identifiant hashé du joueur
	 * @param nom Le nom du joueur
	 * @param amis La liste des identifiants des amis du joueur
	 * @param credit Le crédit que possède le joueur
	 * @param dateInscription La date à laquelle s'est inscrit le joueur (timestamp)
	 * @return true si le joueur a été créé
	 * 		   false sinon
	 */
	public function createJoueur($idJoueur, $nom, $amis, $credit, $dateInscription)
	{
		$joueur = new Joueur($idJoueur, $nom, $amis, $credit, $dateInscription);
		return $this->_daoJoueur->createJoueur($joueur);
	}
	
	/**
	 * Permet d'obtenir un joueur de la base dont l'identifiant est donné en paramètre.
	 * @param id joueur L'identifiant du joueur.
	 * @return Le joueur correspondant à l'identifiant passé en paramètre.
	 */
	public function getJoueur($idJoueur)
	{
		return $this->_daoJoueur->getJoueur($idJoueur);
	}
	
	/**
	 * Permet d'obtenir l'animal du joueur passé en paramètre.
	 * @param idJoueur L'identifiant du joueur.
	 * @return L'animal appartenant au joueur.
	 */
	public function getAnimalByJoueur($idJoueur)
	{
		return $this->_daoJoueur->getAnimalByJoueur($idJoueur);
	}
	
	/**
	 * Permet de stocker le joueur connecté dans la session
	 * @param idJoueur Identifiant du joueur qui vient de se connecter (non hashé).
	 * @param isPlayerExist true si le joueur est connu de l'appli,
	 * 						false si c'est un nouveau joueur.
	 */
	public function initialisation($idJoueur, $isPlayerExist)
	{
		$idJoueurHash = sha1($idJoueur.SEL_HASH);//On hash l'identifiant du joueur
		if(MODE_TEST) //Mode test
		{
			//On créé un joueur Facebook de test pour pouvoir tester notre application en local
			$amis = array('100000932826926');
			$nom = 'Cédric Gagnevin';
				
			if(!$isPlayerExist)//Nouveau joueur, on le créé en base
			{
				$joueur = new Joueur($idJoueurHash, $nom, $amis, SOLDE_DEPART, time());
				$bool = $this->createJoueur($idJoueurHash, $nom, $amis, SOLDE_DEPART, time());
	
				if(!$bool)
					$this->afficherErreur('Erreur insertion en base.', 'Impossible de créer le joueur dans la base de données, requête échouée.', '');
			}
			else //On récupère l'ancien joueur en base
			{
				$joueur = $this->getJoueur($idJoueurHash);
				$joueur->setAmis($amis);
				$joueur->setNomJoueur($nom);
			}
		}
		else //Mode production
		{
			//On récupère le nom et les amis Facebook du joueur
			$infosFb = $this->getInfosFacebook();
			$amis = $infosFb[ 'amis' ];
			$nom = $infosFb[ 'nom' ];
				
			if(!$isPlayerExist)//Nouveau joueur, on le créé en base
			{
				$joueur = new Joueur($idJoueurHash, $nom, $amis, SOLDE_DEPART, time());
				$bool = $this->createJoueur($idJoueurHash, $nom, $amis, SOLDE_DEPART, time());
	
				if(!$bool)
					$this->afficherErreur('Erreur insertion en base.', 'Impossible de créer le joueur dans la base de données, requête échouée.', '');
			}
			else //On récupère l'ancien joueur en base
			{
				$joueur = $this->getJoueur($idJoueurHash);
				$joueur->setAmis($amis);
				$joueur->setNomJoueur($nom);
			}
		}
	
		@session_start();
		//On stock le joueur en session
		$_SESSION [ 'joueur' ] = $joueur;

	}
	
	
	/**
	 * Permet de savoir si le joueur existe en base ou s'il est nouveau dans le jeu, et de connaitre son identifiant facebook.
	 * Renvoie vers une page d'erreur si la récupération des données Facebook échoue.
	 * @return Retourne un tableau associatif de la forme :
	 * 														array( 'isPlayerExist' => true/false,
	 * 															   'idPlayer'      => IDENTIFIANT DU JOUEUR)
	 */
	public function isPlayerExist()
	{
		if(MODE_TEST) //Mode test
		{
			$userID = '15603901792'; //ID bidon
			$userID_hash = sha1($userID.SEL_HASH);
			$exist = $this->_daoJoueur->isPlayerExist($userID_hash);
			return array( 'isPlayerExist' => $exist,
						  'idPlayer'      => $userID);
		}
		else //Mode production
		{
			$user = $this->_facebook->getUser();
			if($user)
			{
				try
				{
					$user_profile = $this->_facebook->api('/me');
					$userID = $user_profile['id'];
					$userID_hash = sha1($userID.SEL_HASH);
					$exist = $this->_daoJoueur->isPlayerExist($userID_hash);
					return array( 'isPlayerExist' => $exist,
							'idPlayer'      => $userID);
	
				}
				catch (FacebookApiException $e)
				{
					$user = null;
					$this->afficherErreur($e->getCode().', '.$e->getFile().' '.$e->getLine(),
							$e->getMessage(),
							$e->getTraceAsString());
				}
				catch (Exception $e)
				{
					$user = null;
					$this->afficherErreur($e->getCode().', '.$e->getFile().' '.$e->getLine(),
							$e->getMessage(),
							$e->getTraceAsString());
				}
			}
			else
			{
				//On redemande l'authentification FB
				header('Location: '.$this->_facebook->getLoginUrl());
			}
		}
	}
	
	
	/**
	 * Permet de récupérer les informations Facebook du joueur.
	 * Renvoie vers une page d'erreur si la récupération des données Facebook échoue.
	 * @return Un tableau contenant le nom et les amis du joueur :
	 * 										array( 'nom' => NOM_FACEBOOK,
	 * 											   'amis' => ID_AMIS)
	 */
	public function getInfosFacebook()
	{
		$user = $this->_facebook->getUser();
	
		if($user)
		{
			try
			{
				$user_profile = $this->_facebook->api('/me');
				$userID = $user_profile['id'];
				$name = $user_profile['name'];//Nom du joueur
				$tmpLst = $this->_facebook->api('/me/friends?fields=id');
				$amis = array();
				foreach($tmpLst['data'] as $val)
				{
					if (is_array($val))
						$amis[] = $val['id'];
				}
	
				return array( 'id' => $userID,
							  'nom'  => $name,
							  'amis' => $amis);
			}
			catch (FacebookApiException $e)
			{
				$user = null;
				$this->afficherErreur($e->getCode().', '.$e->getFile().' '.$e->getLine(),
						$e->getMessage(),
						$e->getTraceAsString());
			}
		}
		else
		{
			$smarty = new Smarty();
			$smarty->assign('erreur_type', 'Erreur Facebook');
			$smarty->assign('erreur_message', 'Impossible de récupérer les données Facebook du joueur dans getInfosFacebook().');
			$smarty->assign('erreur_trace', '');
			$smarty->display("../vues/erreur.html");
		}
	}
	
	/**
	 * Permet d'obtenir le propriétaire de l'animal passé en paramètre.
	 * @param idAnimal L'identifiant de l'animal.
	 * @return Le propriétaire de l'animal
	 */
	public function getJoueurByAnimal($idAnimal)
	{
		return $this->_daoJoueur->getJoueurByAnimal($idAnimal);
	}
	
	/**
	 * Permet d'ajouter une victoire au joueur et à l'animal victorieux.
	 * @param idAnimal L'identifiant de l'animal victorieux.
	 * @return True si la modification a bien été faite
	 * 		   False sinon
	 */
	public function addVictoire($idAnimal)
	{
		return $this->_daoJoueur->addVictoire($idAnimal);
	}
	
	/**
	 * Permet d'ajouter une défaite au joueur et à l'animal défaitiste.
	 * @param idAnimal L'identifiant de l'animal défaitiste.
	 * @return True si la modification a bien été faite
	 * 		   False sinon
	 */
	public function addDefaite($idAnimal)
	{
		return $this->_daoJoueur->addDefaite($idAnimal);
	}
	
	/**
	 * Permet d'ajouter un abandon au joueur et à l'animal concerné.
	 * @param idAnimal L'identifiant de l'animal concerné.
	 * @return True si la modification a bien été faite
	 * 		   False sinon
	 */
	public function addAbandon($idAnimal)
	{
		return $this->_daoJoueur->addAbandon($idAnimal);
	}
	

}

?>