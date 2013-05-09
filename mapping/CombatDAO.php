<?php
require_once 'DAO.php';
require_once '../metiers/Combat.php';
require_once '../metiers/Animal.php';
require_once '../settings/Configuration.php';

/**
 *
 * DAO du Combat
 *
 */
class CombatDAO extends DAO
{
	/**
	 * Constructeur. Initialise la connexion à la base de données.
	 * @throws PDOException Lorsqu'un problème survient lors de l'utilisation d'un objet PDO.
	 */
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Destructeur. Ferme la connexion à la base de données.
	 */
	public function __destruct()
	{
		parent::__destruct();
	}

	
	/**
	 * Permet d'obtenir un nouvel identifiant unique.
	 * @return Un identifiant unique pour un combat.
	 */
	private function getNewIdCombat()
	{
		$requete_prepare = parent::getConnexion()->prepare("SELECT idCombat FROM t_combat");
		$requete_prepare->execute();
		$requete_prepare->setFetchMode(PDO::FETCH_OBJ);
	
		//On récupère le plus grand identifiant (sans le C)
		$last = 0;
		while($id = $requete_prepare->fetch())
		{
			$id = $id->idCombat;
			$num = intval(substr($id, 1, strlen($id)-1));
			if($num > $last)
				$last=$num;
		}
	
		$newId = 'C1';
		if($last != 0) //On incrémente l'identifiant de 1
		{
			$newId = 'C'.($last+1);
		}
		return $newId;
	}
	
	/**
	 * Permet d'obtenir un nouvel identifiant unique.
	 * @return Un identifiant unique pour une action de combat.
	 */
	private function getNewIdAction()
	{
		$requete_prepare = parent::getConnexion()->prepare("SELECT idAction FROM t_combat_action");
		$requete_prepare->execute();
		$requete_prepare->setFetchMode(PDO::FETCH_OBJ);
	
		//On récupère le plus grand identifiant (sans le A)
		$last = 0;
		while($id = $requete_prepare->fetch())
		{
			$id = $id->idAction;
			$num = intval(substr($id, 1, strlen($id)-1));
			if($num > $last)
				$last=$num;
		}
	
		$newId = 'A1';
		if($last != 0) //On incrémente l'identifiant de 1
		{
			$newId = 'A'.($last+1);
		}
		return $newId;
	}
	
	/**
	 * Permet d'inscrire un animal à un combat déjà créé.
	 * @param Le combat auquel l'animal souhaite s'inscrire
	 * @param L'animal qui veut s'inscrire
	 * @return true si l'inscription a été faite
	 * 		   false sinon
	 */
	public function inscriptionCombat($idCombat, $idAnimal)
	{
		$estCommence = true;
		$requete_prepare = parent::getConnexion()->prepare("UPDATE t_combat SET estCommence = :estCommence WHERE idCombat = :idCombat");
		$requete_prepare->bindParam(':idCombat', $idCombat, PDO::PARAM_STR);
		$requete_prepare->bindParam(':estCommence', $estCommence, PDO::PARAM_BOOL);
		$bool1 = $requete_prepare->execute();
		
		$requete_prepare = parent::getConnexion()->prepare("INSERT INTO t_combat_animal (Combat_idCombat, Animal_idAnimal) VALUES (:idCombat, :idAnimal)");
		$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
		$requete_prepare->bindParam(':idCombat', $idCombat, PDO::PARAM_STR);
		$bool2 = $requete_prepare->execute();
		
		return $bool1 && $bool2;
	}

	/**
	 * Permet d'ajouter un combat qu'un joueur a décidé de créer pour son animal.
	 * @param String $idAnimal
	 * @param int $dateCombat Timestamp
	 * @return l'identifiant du combat créé
	 */
	public function ajouterCombat($idAnimal, $dateCombat, $money)
	{
		$identifiant = $this->getNewIdCombat();
		$requete_prepare = parent::getConnexion()->prepare("INSERT INTO t_combat (idCombat, dateCombat, estCommence) VALUES (:idCombat, :dateCombat, 0)");
		$requete_prepare->bindParam(':idCombat', $identifiant, PDO::PARAM_STR);
		$requete_prepare->bindParam(':dateCombat', $dateCombat, PDO::PARAM_INT);
		$requete_prepare->execute();
		
		/*
		$requete_prepare = parent::getConnexion()->prepare("INSERT INTO t_combat_animal (Combat_idCombat, Animal_idAnimal, date) VALUES (:idCombat, :idAnimal, :dateCombat)");
		$requete_prepare->bindParam(':idCombat', $identifiant, PDO::PARAM_STR);
		$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
		$requete_prepare->bindParam(':dateCombat', $dateCombat, PDO::PARAM_INT);
		$requete_prepare->execute();
		*/
		
		$requete_prepare = parent::getConnexion()->prepare("INSERT INTO t_combat_animal (Combat_idCombat, Animal_idAnimal, sommeEngagee) VALUES (:idCombat, :idAnimal, :money)");
		$requete_prepare->bindParam(':idCombat', $identifiant, PDO::PARAM_STR);
		$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
		$requete_prepare->bindParam(':money', $money, PDO::PARAM_INT);
		$requete_prepare->execute();
		
		return $identifiant;
	}
	
	
	public function deleteCombatByJoueur($idJoueur)
	{
		
		/*
		SELECT idCombat
		FROM t_combat, t_combat_animal, t_joueur_animal
		WHERE t_combat.idCombat = t_combat_animal.Combat_idCombat
		AND t_combat_animal.Animal_idAnimal = t_joueur_animal.Animal_idAnimal
		AND t_joueur_animal.Joueur_idFacebook =  '1'
		 */
		/*
		DELETE FROM t_combat_animal
		WHERE t_combat_animal.Animal_idAnimal = (
		SELECT Animal_idAnimal
		FROM t_joueur_animal
		WHERE t_joueur_animal.Joueur_idFacebook =  '9f20d53fab1470b02a217242c4eb1cf59303aa7c')
		 */
		/*
		DELETE FROM t_combat
		WHERE idCombat = :idCombat
		*/
			
		$requete_prepare = parent::getConnexion()->prepare("SELECT idCombat
														FROM t_combat, t_combat_animal, t_joueur_animal
														WHERE t_combat.idCombat = t_combat_animal.Combat_idCombat
														AND t_combat_animal.Animal_idAnimal = t_joueur_animal.Animal_idAnimal
														AND t_joueur_animal.Joueur_idFacebook =  :idJoueur");

		$requete_prepare->bindParam(':idJoueur', $idJoueur, PDO::PARAM_STR);
		$requete_prepare->execute();
		$donnees = $requete_prepare->fetch(PDO::FETCH_OBJ);
		@$idCombat = $donnees->idCombat;
		
		if($idCombat != null)
		{
			
			$requete_prepare = parent::getConnexion()->prepare("DELETE FROM t_combat_animal
														WHERE t_combat_animal.Animal_idAnimal = (
														SELECT Animal_idAnimal
														FROM t_joueur_animal
														WHERE t_joueur_animal.Joueur_idFacebook =  :idJoueur)");
			$requete_prepare->bindParam(':idJoueur', $idJoueur, PDO::PARAM_STR);
			$requete_prepare->execute();
			
			$requete_prepare = parent::getConnexion()->prepare("DELETE FROM t_combat
															WHERE idCombat = :idCombat");
			$requete_prepare->bindParam(':idCombat', $idCombat, PDO::PARAM_STR);
			$requete_prepare->execute();
		}
	}
	
	public function deleteCombatByIdCombat($idCombat)
	{
	
		$requete_prepare = parent::getConnexion()->prepare("DELETE FROM t_combat_animal
					WHERE t_combat_animal.Combat_idCombat =  :idCombat");
		$requete_prepare->bindParam(':idCombat', $idCombat, PDO::PARAM_STR);
		$requete_prepare->execute();
				
		$requete_prepare = parent::getConnexion()->prepare("DELETE FROM t_combat
				WHERE idCombat = :idCombat");
		$requete_prepare->bindParam(':idCombat', $idCombat, PDO::PARAM_STR);
		$requete_prepare->execute();
		
		return true;
		
	}
	
	public function listerCombats($level, $idJoueur = null)
	{
		//Le level min est le level -2 au level de l'animal (si l'animal est level 12, le level min est 10)
		//Le level max est le level +2 au level de l'animal (si l'animal est level 14, le level max est 16)
		
		$ecartNiveau = ECART_NIVEAU;
		
		/*$requete = "SELECT t_combat.idCombat as idCombat, t_combat_animal.Animal_idAnimal as idAnimal, Joueur_idFacebook as idJoueur
					FROM t_combat, t_combat_animal, t_animal, t_joueur_animal
					WHERE t_combat_animal.Animal_idAnimal = t_animal.idAnimal
					AND t_combat_animal.Combat_idCombat = t_combat.idCombat
					AND niveau BETWEEN :niveau - :ecart AND :niveau + :ecart
					AND t_joueur_animal.Animal_idAnimal = t_animal.idAnimal ";
		*/
		
		$requete = "SELECT COUNT(*), t_combat.idCombat as idCombat, t_combat_animal.Animal_idAnimal as idAnimal, Joueur_idFacebook as idJoueur
				FROM t_combat, t_combat_animal, t_animal, t_joueur_animal
				WHERE t_combat_animal.Animal_idAnimal = t_animal.idAnimal
				AND t_combat_animal.Combat_idCombat = t_combat.idCombat
				AND niveau BETWEEN :niveau - :ecart AND :niveau + :ecart
				AND t_joueur_animal.Animal_idAnimal = t_animal.idAnimal ";
				
		if($idJoueur != null) $requete .= "AND Joueur_idFacebook != :idJoueur ";
		
		$requete .= "GROUP BY t_combat.idCombat
					HAVING COUNT(*) < 2";

		$requete_prepare = parent::getConnexion()->prepare($requete);
		$requete_prepare->bindParam(':niveau', $level, PDO::PARAM_INT);
		$requete_prepare->bindParam(':ecart', $ecartNiveau, PDO::PARAM_INT);
		if($idJoueur != null) $requete_prepare->bindParam(':idJoueur', $idJoueur, PDO::PARAM_STR);
		$combats = $requete_prepare->execute();
		$donnees = $requete_prepare->fetchAll(PDO::FETCH_ASSOC);

		return $donnees;
	}
	
	
	/**
	 * Teste si un combat est commencé, renvoie false s'il est commencé, true sinon 
	 * @param String $idCombat
	 * @return true or false
	 */
	public function isFightAvailable($idCombat)
	{
		$requete_prepare = parent::getConnexion()->prepare("SELECT estCommence FROM t_combat WHERE idCombat = :idCombat");
		$requete_prepare->bindParam(':idCombat', $idCombat, PDO::PARAM_STR);
		$requete_prepare->execute();
		$donnees = $requete_prepare->fetch(PDO::FETCH_ASSOC);
		
		return !$donnees['estCommence'];
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
		$identifiant = $this->getNewIdAction();
		$requete_prepare = parent::getConnexion()->prepare("INSERT INTO t_combat_action (idAction, Combat_idCombat, Animal_idAnimal, Competence_idCompetence, degatsProvoques, dateAction) VALUES (:idAction, :idCombat, :idAnimal, :idCompetence, :degats, :date)");
		$requete_prepare->bindParam(':idAction', $identifiant, PDO::PARAM_STR);
		$requete_prepare->bindParam(':idCombat', $idCombat, PDO::PARAM_STR);
		$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
		$requete_prepare->bindParam(':idCompetence', $idCompetence, PDO::PARAM_STR);
		$requete_prepare->bindParam(':degats', $degats, PDO::PARAM_INT);
		$requete_prepare->bindParam(':date', $date, PDO::PARAM_INT);
		$requete_prepare->execute();
		
		return $identifiant;
	}
	
	/**
	 * Permet d'obtenir la derniere action effectuée pour un combat donné.
	 * @param String $idCombat
	 * @return Un tableau contenant les caractéristiques de l'action.
	 */
	public function getLastAction($idCombat)
	{
		$requete_prepare = parent::getConnexion()->prepare("SELECT * FROM t_combat_action WHERE Combat_idCombat = :idCombat AND dateAction = (SELECT MAX(dateAction) FROM t_combat_action WHERE Combat_idCombat = :idCombat)");
		$requete_prepare->bindParam(':idCombat', $idCombat, PDO::PARAM_STR);
		$requete_prepare->execute();
		$donnees = $requete_prepare->fetchAll(PDO::FETCH_ASSOC);
		return ($donnees != null)? $donnees[0] : null;
	}

	/**
	 * Permet d'obtenir l'action correspondant à l'identifiant donné.
	 * @param String $idAction
	 * @return Un tableau contenant les caractéristiques de l'action.
	 */
	public function getAction($idAction)
	{
		$requete_prepare = parent::getConnexion()->prepare("SELECT * FROM t_combat_action WHERE idAction = :idAction");
		$requete_prepare->bindParam(':idAction', $idAction, PDO::PARAM_STR);
		$requete_prepare->execute();
		$donnees = $requete_prepare->fetchAll(PDO::FETCH_ASSOC);
		return ($donnees != null)? $donnees[0] : null;
	}
	
	/**
	 * Permet d'obtenir un combat en base d'après son identifiant.
	 * @param idCombat L'identifiant du combat
	 * @return le combat correspondant
	 */
	public function getCombat($idCombat)
	{
		$requete_prepare = parent::getConnexion()->prepare("SELECT * FROM t_combat, t_combat_animal WHERE Combat_idCombat = idCombat AND idCombat = :idCombat");
		$requete_prepare->bindParam(':idCombat', $idCombat, PDO::PARAM_STR);
		$requete_prepare->execute();
		$animaux = array();
		while($donnees = $requete_prepare->fetch(PDO::FETCH_ASSOC))
		{
			$idCombat = $donnees[ 'idCombat' ];
			$animaux[] = $donnees[ 'Animal_idAnimal' ];
			$dateCombat = $donnees[ 'dateCombat' ];
			$estCommence = $donnees[ 'estCommence' ];
		}
		
		$combat = new Combat($idCombat, $animaux[0], $animaux[1], $dateCombat, $estCommence);

		return $combat;
	}
	
	/**
	 * Permet d'obtenir le pourcentage d'absorption d'une compétence défensive pour une puissance d'attaque donnée.
	 * @param $idDefense L'identifiant de la compétence défensive
	 * @param $puissanceAttaque La puissance de l'attaque
	 * @return float Le pourcentage d'absorption compris entre 0 et 1.
	 */
	public function getAbsorption($idDefense, $puissanceAttaque)
	{
		$requete_prepare = parent::getConnexion()->prepare("SELECT absorptionAttaque".$puissanceAttaque." FROM t_absorption WHERE Competence_defense = :idCompetence");
		$requete_prepare->bindParam(':idCompetence', $idDefense, PDO::PARAM_STR);
		$requete_prepare->execute();
		$donnees = $requete_prepare->fetch(PDO::FETCH_ASSOC);
	
		$colonne = 'absorptionAttaque'.$puissanceAttaque;
		return $donnees[$colonne];
	}
	
	/**
	 * Permet de modifier les dégats provoqués par une attaque dont l'identiant de l'action concernée est passée en paramètre.
	 * @param $idAction L'identifiant de l'action
	 * @param $degats La nouvelle valeur des degats
	 */
	public function updateDegatsProvoques($idAction, $degats)
	{
		$requete_prepare = parent::getConnexion()->prepare("UPDATE t_combat_action SET degatsProvoques = :degats WHERE idAction = :idAction");
		$requete_prepare->bindParam(':degats', $degats, PDO::PARAM_INT);
		$requete_prepare->bindParam(':idAction', $idAction, PDO::PARAM_STR);
		$requete_prepare->execute();
	}
	
	/**
	 * Permet d'obtenir les dégats provoqués par une attaque dont l'identiant de l'action concernée est passée en paramètre.
	 * @param $idAction L'identifiant de l'action
	 * @param $degats La nouvelle valeur des degats
	 * @return Les degats provoques
	 */
	public function getDegatsProvoques($idAction)
	{
		$requete_prepare = parent::getConnexion()->prepare("SELECT degatsProvoques FROM t_combat_action WHERE idAction = :idAction");
		$requete_prepare->bindParam(':idAction', $idAction, PDO::PARAM_STR);
		$requete_prepare->execute();
		$donnees = $requete_prepare->fetch(PDO::FETCH_ASSOC);
		
		return $donnees['degatsProvoques'];
	}

	/**
	 * Permet d'obtenir la somme que le propriétaire a engagé pour que son animal combatte.
	 * @param $idCombat L'identifiant du combat concerné
	 * @param $idAnimal L'identifiant de l'animal inscrit
	 */
	public function getSommeEngagee($idCombat, $idAnimal)
	{
		$requete_prepare = parent::getConnexion()->prepare("SELECT sommeEngagee FROM t_combat_animal WHERE Combat_idCombat = :idCombat AND Animal_idAnimal = :idAnimal");
		$requete_prepare->bindParam(':idCombat', $idCombat, PDO::PARAM_STR);
		$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
		$requete_prepare->execute();
		$donnees = $requete_prepare->fetch(PDO::FETCH_ASSOC);
		return $donnees['sommeEngagee'];
	}
	
	/**
	 * Permet à un animal de rejoindre un combat déjà créé
	 * @param $idCombat l'identifiant du combat que souhaite rejoindre l'animal
	 * @param $idAnimal l'identifiant de l'animal souhaitant rejoindre le combat
	 * @param $mise l'argent qu'a investi le propriétaire dans le combat
	 */
	public function rejoindreCombat($idCombat, $idAnimal, $mise)
	{		
		$requete_prepare = parent::getConnexion()->prepare("INSERT INTO t_combat_animal (Combat_idCombat, Animal_idAnimal, sommeEngagee) VALUES (:idCombat, :idAnimal, :mise)");
		$requete_prepare->bindParam(':idCombat', $idCombat, PDO::PARAM_STR);
		$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
		$requete_prepare->bindParam(':mise', $mise, PDO::PARAM_INT);
		$requete_prepare->execute();
		
		return $idCombat;
	}
	
	/**
	 * Permet de démarrer un combat
	 * @param $idCombat l'identifiant du combat à démarrer
	 */
	public function startCombat($idCombat)
	{
		$requete_prepare = parent::getConnexion()->prepare("UPDATE t_combat SET estCommence = 1 WHERE idCombat = :idCombat");
		$requete_prepare->bindParam(':idCombat', $idCombat, PDO::PARAM_STR);
		$requete_prepare->execute();
	}
	
	
	/**
	 * 
	 * @param $idCombat
	 */
	public function getAnimalsByCombat($idCombat)
	{
		$requete_prepare = parent::getConnexion()->prepare("SELECT * FROM t_combat_animal WHERE Combat_idCombat = :idCombat");
		$requete_prepare->bindParam(':idCombat', $idCombat, PDO::PARAM_STR);
		$requete_prepare->execute();
		$donnees = $requete_prepare->fetchAll(PDO::FETCH_ASSOC);
		
		return $donnees;
	}
	
	/**
	 * 
	 * @param unknown_type $idCombat
	 */
	public function countPlayersByCombat($idCombat)
	{
		$requete_prepare = parent::getConnexion()->prepare("SELECT COUNT(*) as nbPlayers FROM t_combat_animal WHERE Combat_idCombat = :idCombat");
		$requete_prepare->bindParam(':idCombat', $idCombat, PDO::PARAM_STR);
		$requete_prepare->execute();
		$donnees = $requete_prepare->fetch(PDO::FETCH_ASSOC);
		
		return $donnees['nbPlayers'];
	}
}	

?>