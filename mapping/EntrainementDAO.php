<?php
require_once 'DAO.php';
require_once '../metiers/Entrainement.php';
require_once '../metiers/OffreEntrainement.php';
require_once '../metiers/Animal.php';

/**
 *
 * DAO de l'Entrainement
 *
 */
class EntrainementDAO extends DAO
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
	 * Permet d'obtenir un nouvel identifiant unique de la forme "E___".
	 * @return Un identifiant unique pour un entrainement.
	 */
	private function getNewIdEntrainement()
	{
		$requete_prepare = parent::getConnexion()->prepare("SELECT idEntrainement FROM t_entrainement");
		$requete_prepare->execute();
		$requete_prepare->setFetchMode(PDO::FETCH_OBJ);
	
		//On récupère le plus grand identifiant (sans le E)
		$last = 0;
		while($id = $requete_prepare->fetch())
		{
			$id = $id->idEntrainement;
			$num = intval(substr($id, 1, strlen($id)-1));
			if($num > $last)
				$last=$num;
		}
	
		$newId = 'E1';
		if($last != 0) //On incrémente l'identifiant de 1
		{
			$newId = 'E'.($last+1);
		}
		return $newId;
	}
	
	
	/**
	 * Permet d'obtenir les entrainements actuellement disponibles (Entrainements qui ne sont pas encore commencés).
	 * @return Un tableau contenant
	 */
	public function getEntrainementsDispo()
	{
		$currentTime = time();
		$requete_prepare = parent::getConnexion()->prepare("SELECT * FROM t_entrainement WHERE dateDebut > :currentTime ");
		$requete_prepare->bindParam(':currentTime', $currentTime, PDO::PARAM_INT);
		$requete_prepare->execute();
		
		$entrainementsDispo = array();
		$donnees = $requete_prepare->fetchAll(PDO::FETCH_OBJ);
		foreach ($donnees as $value)
		{
			$entrainement = new EntrainementCollectif($value->idEntrainement,
										     $value->duree, 
											 $value->prix,
											 $value->dateDebut,
											 new OffreEntrainement($value->OffreEntrainement_idOffre, null, null, null, null),
											 null);
			array_push($entrainementsDispo, $entrainement);
		}
		
		return $entrainementsDispo;
	}
	

	
	/**
	 *  Permet d'ajouter un entrainement dans la base de données..
	 * @param int $duree Durée de l'entrainement en secondes.
	 * @param int $prix Prix de l'entrainement.
	 * @param timestamp $dateDebut Date à laquelle l'entrainement débute.
	 * @param String $type Type d'entrainement (collectif/individuel).
	 * @param int $niveauMax Le niveau maximum pour participer à l'entrainement (inclusive).
	 * @param String $idOffre Identifiant de l'offre associée à cet entrainement.
	 * @return L'identifiant de l'entrainement ajouté. 
	 */
	public function ajouterEntrainement($duree, $prix, $dateDebut, $type, $niveauMax, $nbParticipantsMin, $nbParticipantsMax, $idOffre)
	{
		//Identifiant de l'entrainement qui va etre inséré
		$idEntrainement = $this->getNewIdEntrainement();
		
		//Insertion
		$requete_prepare = parent::getConnexion()->prepare("INSERT INTO t_entrainement (idEntrainement, duree, prix, dateDebut, type, niveauMax, nbParticipantsMin, nbParticipantsMax, annule, OffreEntrainement_idOffre) VALUES (:idEntrainement, :duree, :prix, :dateDebut, :type, :niveauMax, :nbParticipantsMin, :nbParticipantsMax, 0, :idOffre)");
		$requete_prepare->bindParam(':idEntrainement', $idEntrainement, PDO::PARAM_STR);
		$requete_prepare->bindParam(':duree', $duree, PDO::PARAM_INT);
		$requete_prepare->bindParam(':prix', $prix, PDO::PARAM_INT);
		$requete_prepare->bindParam(':dateDebut', $dateDebut, PDO::PARAM_INT);
		$requete_prepare->bindParam(':type', $type, PDO::PARAM_STR);
		$requete_prepare->bindParam(':niveauMax', $niveauMax, PDO::PARAM_INT);
		$requete_prepare->bindParam(':nbParticipantsMin', $nbParticipantsMin, PDO::PARAM_INT);
		$requete_prepare->bindParam(':nbParticipantsMax', $nbParticipantsMax, PDO::PARAM_INT);
		$requete_prepare->bindParam(':idOffre', $idOffre, PDO::PARAM_STR);
		$requete_prepare->execute();
		
		return $idEntrainement;
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
		//Détermination du seuil en fonction du niveau indiqué
		if($niveau > 0 && $niveau <= 10)
			$seuil = 6;
		elseif($niveau > 10 && $niveau <= 20)
			$seuil = 4;
		else // > 20
			$seuil = 2;

			
		//Select d'une offre aléatoire
		$requete_prepare = parent::getConnexion()->prepare("SELECT * FROM t_entrainementoffre 
															WHERE attaque_offre + defense_offre + vie_offre + levelUp <= :seuil
															ORDER BY RAND( ) 
															LIMIT 1");
		$requete_prepare->bindParam(':seuil', $seuil, PDO::PARAM_INT);
		$requete_prepare->execute();
		$donnees = $requete_prepare->fetch(PDO::FETCH_ASSOC);
		
		return new OffreEntrainement($donnees['idOffre'],
									   $donnees['attaque_offre'],
									   $donnees['defense_offre'],
									   $donnees['vie_offre'],
									   $donnees['levelUp']);
		
	}
	
	/**
	 * Permet d'obtenir tous les entrainements qui sont finis mais qui n'ont pas encore été validés (l'amélioration des animaux n'a pas encore été faite).
	 * @return array Un tableau contenant les entrainements finis en attente de validation.
	 */
	public function getEntrainementsFinisEnAttente()
	{
		$requete_prepare = parent::getConnexion()->prepare("SELECT idEntrainement, duree, prix, dateDebut, type, idOffre, attaque_offre, defense_offre, vie_offre, levelUp
															FROM t_entrainement, t_entrainementoffre, t_entrainement_animal
															WHERE t_entrainement.OffreEntrainement_idOffre = t_entrainementoffre.idOffre
															AND t_entrainement.idEntrainement = t_entrainement_animal.Entrainement_idEntrainement
															AND dateDebut + duree < :currentTime
															AND valide = 0");
		
		$currentTime = time();
		$requete_prepare->bindParam(':currentTime', $currentTime, PDO::PARAM_INT);
		$requete_prepare->execute();
		
		$entrainements = array();
		while($donnees = $requete_prepare->fetch(PDO::FETCH_ASSOC))
		{
			$idEntrainement = $donnees[ 'idEntrainement' ];
			$duree = $donnees[ 'duree' ];
			$prix = $donnees[ 'prix' ];
			$dateDebut = $donnees[ 'dateDebut' ];
			$type = $donnees[ 'type' ];
			$idOffre = $donnees[ 'idOffre' ];
			$attaque_offre = $donnees[ 'attaque_offre' ];
			$defense_offre = $donnees[ 'defense_offre' ];
			$vie_offre = $donnees[ 'vie_offre' ];
			$levelUp = $donnees[ 'levelUp' ];
			
			$offre = new OffreEntrainement($idOffre, $attaque_offre, $defense_offre, $vie_offre, $levelUp);
			
			if($type == 'individuel')
				$entrainement = new EntrainementIndividuel($idEntrainement, $duree, $prix, $dateDebut, $offre, null);
			else $entrainement = new EntrainementCollectif($idEntrainement, $duree, $prix, $dateDebut, $offre, array());
			
			array_push($entrainements, $entrainement);
		}
		
		return array_unique($entrainements);
	}
	
	/**
	 * Permet d'obtenir le ou les animaux pour un entrainement donné.
	 * @param String $idEntrainement
	 * @return array Un tableau contenant le/les animaux qui ont suivi cet entrainement.
	 */
	public function getAnimalsByEntrainement($idEntrainement)
	{
		$requete_prepare = parent::getConnexion()->prepare("SELECT idAnimal, nomAnimal, vie, defense, attaque, niveau 
															FROM t_animal, t_entrainement_animal
															WHERE t_animal.idAnimal = t_entrainement_animal.Animal_idAnimal
															AND Entrainement_idEntrainement = :idEntrainement");
		
		$requete_prepare->bindParam(':idEntrainement', $idEntrainement, PDO::PARAM_STR);
		$requete_prepare->execute();
		
		$animals = array();
		while($donnees = $requete_prepare->fetch(PDO::FETCH_ASSOC))
		{
			$idAnimal = $donnees[ 'idAnimal' ];
			$nomAnimal = $donnees[ 'nomAnimal' ];
			$vie = $donnees[ 'vie' ];
			$defense = $donnees[ 'defense' ];
			$attaque = $donnees[ 'attaque' ];
			$niveau = $donnees[ 'niveau' ];
				
			$animal = new Animal($idAnimal, null, $nomAnimal, $vie, $defense, $attaque, $niveau);
			array_push($animals, $animal);
		}

		return $animals;
	}
	
	/**
	 * Permet d'obtenir les entrainements collectifs commençant aujourd'hui.
	 * @return array Un tableau contenant les entrainements collectifs commençant aujourd'hui.
	 */
	public function getCollectiveTrainingsOfToday()
	{
		$ajdMin = mktime(0, 0, 0, date("n"), date("j"), date("Y")); //Aujourd'hui à 00h00m00s
		$ajdMax = mktime(23, 59, 59, date("n"), date("j"), date("Y")); //Aujourd'hui à 23h59m59s
		
		$requete_prepare = parent::getConnexion()->prepare("SELECT *
															FROM t_entrainement, t_entrainementoffre
															WHERE t_entrainement.OffreEntrainement_idOffre = t_entrainementoffre.idOffre
															AND type = 'collectif' 
															AND dateDebut >= :ajdMin 
															AND dateDebut <= :ajdMax
															ORDER BY dateDebut ASC");
														
		$requete_prepare->bindParam(':ajdMin', $ajdMin, PDO::PARAM_STR);
		$requete_prepare->bindParam(':ajdMax', $ajdMax, PDO::PARAM_STR);
		$requete_prepare->execute();
		
		$entrainements = array();
		while($donnees = $requete_prepare->fetch(PDO::FETCH_ASSOC))
		{
			$idEntrainement = $donnees[ 'idEntrainement' ];
			$duree = $donnees[ 'duree' ];
			$prix = $donnees[ 'prix' ];
			$dateDebut = $donnees[ 'dateDebut' ];
			$type = $donnees[ 'type' ];
			$niveauMax = $donnees[ 'niveauMax' ];
			$nbParticipantsMin = $donnees[ 'nbParticipantsMin' ];
			$nbParticipantsMax = $donnees[ 'nbParticipantsMax' ];
			$annule = $donnees[ 'annule' ];
			$idOffre = $donnees[ 'idOffre' ];
			$attaque_offre = $donnees[ 'attaque_offre' ];
			$defense_offre = $donnees[ 'defense_offre' ];
			$vie_offre = $donnees[ 'vie_offre' ];
			$levelUp = $donnees[ 'levelUp' ];
				
			$offre = new OffreEntrainement($idOffre, $attaque_offre, $defense_offre, $vie_offre, $levelUp);

			$entrainement = new EntrainementCollectif($idEntrainement, $duree, $prix, $dateDebut, $offre, array(), $niveauMax, $nbParticipantsMin, $nbParticipantsMax, $annule);
				
			array_push($entrainements, $entrainement);
		}
		
		return $entrainements;
	}

	
	/**
	 * Permet d'obtenir l'entrainement correspond à l'identifiant.
	 * @param L'identifiant de l'entrainement
	 * @return Un entrainement
	 */
	public function getTrainingById($idTraining)
	{
		$requete_prepare = parent::getConnexion()->prepare("SELECT *
				FROM t_entrainement, t_entrainementoffre
				WHERE t_entrainement.OffreEntrainement_idOffre = t_entrainementoffre.idOffre
				AND idEntrainement = :idEntrainement");
		
		$requete_prepare->bindParam(':idEntrainement', $idTraining, PDO::PARAM_STR);
		$requete_prepare->execute();
		$donnees = $requete_prepare->fetch(PDO::FETCH_ASSOC);

		$idEntrainement = $donnees[ 'idEntrainement' ];
		$duree = $donnees[ 'duree' ];
		$prix = $donnees[ 'prix' ];
		$dateDebut = $donnees[ 'dateDebut' ];
		$type = $donnees[ 'type' ];
		$niveauMax = $donnees[ 'niveauMax' ];
		$nbParticipantsMix = $donnees[ 'nbParticipantsMin' ];
		$nbParticipantsMax = $donnees[ 'nbParticipantsMax' ];
		$annule = $donnees[ 'annule' ];
		$idOffre = $donnees[ 'idOffre' ];
		$attaque_offre = $donnees[ 'attaque_offre' ];
		$defense_offre = $donnees[ 'defense_offre' ];
		$vie_offre = $donnees[ 'vie_offre' ];
		$levelUp = $donnees[ 'levelUp' ];
	
		$offre = new OffreEntrainement($idOffre, $attaque_offre, $defense_offre, $vie_offre, $levelUp);
	
		if($type == "collectif")
			$entrainement = new EntrainementCollectif($idEntrainement, $duree, $prix, $dateDebut, $offre, array(), $niveauMax, $nbParticipantsMix, $nbParticipantsMax, $annule);
		else $entrainement = new EntrainementIndividuel($idEntrainement, $duree, $prix, $dateDebut, $offre, null);
		
		return $entrainement;
	}
	
	
	/**
	 * Supprime un entrainement dont l'identifiant est passé en paramètre ainsi que les références qu'il peut avoir dans la table
	 * t_entrainement_animal.
	 * @param L'identifiant de l'entrainement
	 * @return true si la suppression a eu lieu
	 * 		   false sinon
	 */
	public function deleteTrainingById($idTraining)
	{
		$requete_prepare = parent::getConnexion()->prepare("DELETE FROM t_entrainement_animal WHERE Entrainement_idEntrainement = :idEntrainement");
		$requete_prepare->bindParam(':idEntrainement', $idTraining, PDO::PARAM_STR);
		$bool = $requete_prepare->execute();
		
		if($bool)
		{
			$requete_prepare = parent::getConnexion()->prepare("DELETE FROM t_entrainement WHERE idEntrainement = :idEntrainement");
			$requete_prepare->bindParam(':idEntrainement', $idTraining, PDO::PARAM_STR);
			$bool = $requete_prepare->execute();
			return $bool;
		}
		return false;
	}
	
	
	/**
	 * Permet d'obtenir tous les entrainements collectifs de la base.
	 * @return array Un tableau contenant les entrainements collectifs
	 */
	public function getAllCollectiveTrainings()
	{
		$requete_prepare = parent::getConnexion()->prepare("SELECT *
				FROM t_entrainement, t_entrainementoffre
				WHERE t_entrainement.OffreEntrainement_idOffre = t_entrainementoffre.idOffre
				AND type = 'collectif'
				ORDER BY dateDebut ASC");

		$requete_prepare->execute();
	
		$entrainements = array();
		while($donnees = $requete_prepare->fetch(PDO::FETCH_ASSOC))
		{
			$idEntrainement = $donnees[ 'idEntrainement' ];
			$duree = $donnees[ 'duree' ];
			$prix = $donnees[ 'prix' ];
			$dateDebut = $donnees[ 'dateDebut' ];
			$type = $donnees[ 'type' ];
			$niveauMax = $donnees[ 'niveauMax' ];
			$nbParticipantsMax = $donnees[ 'nbParticipantsMin' ];
			$annule = $donnees[ 'annule' ];
			$idOffre = $donnees[ 'idOffre' ];
			$attaque_offre = $donnees[ 'attaque_offre' ];
			$defense_offre = $donnees[ 'defense_offre' ];
			$vie_offre = $donnees[ 'vie_offre' ];
			$levelUp = $donnees[ 'levelUp' ];
	
			$offre = new OffreEntrainement($idOffre, $attaque_offre, $defense_offre, $vie_offre, $levelUp);
	
			$entrainement = new EntrainementCollectif($idEntrainement, $duree, $prix, $dateDebut, $offre, array(), $niveauMax, $nbParticipantsMax, $annule);
	
			array_push($entrainements, $entrainement);
		}
	
		return $entrainements;
	}
	
	
	/**
	 * Permet d'obtenir tous les animaux inscrits à un entrainement.
	 * @return array Un tableau contenant les animaux inscrits.
	 */
	public function getAnimalsEnrolledByTraining($idEntrainement)
	{
		$requete_prepare = parent::getConnexion()->prepare("SELECT *
															FROM t_animal, t_entrainement_animal, t_joueur_animal
															WHERE t_entrainement_animal.Animal_idAnimal = t_animal.idAnimal
															AND t_entrainement_animal.Animal_idAnimal = t_joueur_animal.Animal_idAnimal
															AND Entrainement_idEntrainement = :idEntrainement");
	
		$requete_prepare->bindParam(':idEntrainement', $idEntrainement, PDO::PARAM_STR);
		$requete_prepare->execute();
	
		$animals = array();
		while($donnees = $requete_prepare->fetch(PDO::FETCH_ASSOC))
		{
			$idAnimal = $donnees[ 'idAnimal' ];
			$nomAnimal = $donnees[ 'nomAnimal' ];
			$vie = $donnees[ 'vie' ];
			$defense = $donnees[ 'defense' ];
			$attaque = $donnees[ 'attaque' ];
			$niveau = $donnees[ 'niveau' ];
			$proprietaire = $donnees[ 'Joueur_idFacebook' ];
	
			$animal = new Animal($idAnimal, $proprietaire, $nomAnimal, $vie, $defense, $attaque, $niveau);
	
			array_push($animals, $animal);
		}
	
		return $animals;
	}
	
}
?>