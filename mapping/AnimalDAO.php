<?php
require_once 'DAO.php';
require_once '../metiers/Animal.php';
require_once '../metiers/Competence.php';


/**
 *
 * DAO de l'Animal
 *
 */
class AnimalDAO extends DAO
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
	 * @return Un identifiant unique pour un animal.
	 */
	private function getNewIdAnimal()
	{
		$requete_prepare = parent::getConnexion()->prepare("SELECT idAnimal FROM t_animal");
		$requete_prepare->execute();
		$requete_prepare->setFetchMode(PDO::FETCH_OBJ);
		
		//On récupère le plus grand identifiant (sans le A)
		$last = 0;
		while($id = $requete_prepare->fetch())
		{
			$id = $id->idAnimal;
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
	 * Permet de modifier le propriétaire de l'animal dont l'identifiant est passé en paramètres.
	 * @param Le nouveau propriétaire
	 * @param L'identifiant de l'animal dont on veut changer le propriétaire.
	 * @return true si la modification a été faite.
	 * 		   false sinon
	 */
	public function updateProprietaire($idProprietaire, $idAnimal)
	{
		$bool = false;
		$connexion = $this->getConnexion();
		//On regarde si le joueur possèdait un ancien proprio (s'il est présent dans t_joueur_animal)
		$requete_prepare = $connexion->prepare("SELECT  COUNT(*) as COUNT FROM t_joueur_animal WHERE Animal_idAnimal = :id");
		$requete_prepare->bindParam(':id', $idAnimal, PDO::PARAM_STR);
		$requete_prepare->execute();
		$count = $requete_prepare->fetch(PDO::FETCH_OBJ)->COUNT;
		//Si ce n'est pas le cas, on créé une ligne
		if($count == 0)
		{
			$requete_prepare = $connexion->prepare("INSERT INTO t_joueur_animal (Joueur_idFacebook, Animal_idAnimal) VALUES(:idJoueur, :idAnimal)");
			$requete_prepare->bindParam(':idJoueur', $idProprietaire, PDO::PARAM_STR);
			$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
			$bool = $requete_prepare->execute();
		}
		else //Sinon on update la ligne existante
		{
			$requete_prepare = $connexion->prepare("UPDATE t_joueur_animal SET Joueur_idFacebook = :idJoueur WHERE Animal_idAnimal = :idAnimal");
			$requete_prepare->bindParam(':idJoueur', $idProprietaire, PDO::PARAM_STR);
			$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
			$bool = $requete_prepare->execute();
		}
		return $bool;
	}
	
	
	/**
	 * Créé un animal dans la base de données.
	 * @param proprietaire L'identifiant du proprietaire
	 * @param nom Le nom de l'animal
	 * @param vie Le nombre de points de vie de l'animal
	 * @param defense Le nombre de points de défense de l'animal
	 * @param attaque Le nombre de points d'attaque de l'animal
	 * @return l'identifiant de l'animal créé.
	 * 		   -1 si la création a échouée
	 */
	public function createAnimal($proprietaire, $nom, $vie, $defense, $attaque)
	{
		$requete_prepare = parent::getConnexion()->prepare("INSERT INTO t_animal (idAnimal, nomAnimal, vie, defense, attaque) VALUES (:id, :nom, :vie, :def, :att)");
		$identifiant = $this->getNewIdAnimal();
		$requete_prepare->bindParam(':id', $identifiant, PDO::PARAM_STR);
		$requete_prepare->bindParam(':nom', $nom, PDO::PARAM_STR);
		$requete_prepare->bindParam(':vie', $vie, PDO::PARAM_INT);
		$requete_prepare->bindParam(':def', $defense, PDO::PARAM_INT);
		$requete_prepare->bindParam(':att', $attaque, PDO::PARAM_INT);
		$b1 = $requete_prepare->execute();
		
		$requete_prepare = parent::getConnexion()->prepare("INSERT INTO t_joueur_animal (Joueur_idFacebook, Animal_idAnimal) VALUES (:idJ, :idA)");
		$requete_prepare->bindParam(':idJ', $proprietaire, PDO::PARAM_STR);
		$requete_prepare->bindParam(':idA', $identifiant, PDO::PARAM_STR);
		$b2 = $requete_prepare->execute();
		
		$bool = $b1 && $b2;
		if($bool)
			return $identifiant;
		else return -1;
	}
	
	/**
	 * Permet d'obtenir un animal en base d'après son identifiant.
	 * @param idAnimal L'identifiant de l'animal
	 * @return l'animal correspondant
	 */
	public function getAnimal($idAnimal)
	{
		$requete_prepare = parent::getConnexion()->prepare("SELECT * FROM t_animal WHERE idAnimal = :idAnimal");
		$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
		$requete_prepare->execute();
		$donnees = $requete_prepare->fetch(PDO::FETCH_ASSOC);
		$animal = new Animal($donnees[ 'idAnimal' ], null, $donnees[ 'nomAnimal' ], $donnees[ 'vie' ], $donnees[ 'defense' ], $donnees[ 'attaque' ], $donnees[ 'niveau' ], null, $donnees[ 'nbVictoires' ], $donnees[ 'nbDefaites' ], $donnees[ 'nbAbandons' ]);
		
		$requete_prepare = parent::getConnexion()->prepare("SELECT * FROM t_joueur_animal WHERE Animal_idAnimal = :idAnimal");
		$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
		$requete_prepare->execute();
		$donnees = $requete_prepare->fetch(PDO::FETCH_ASSOC);
		$proprietaire = $donnees[ 'Joueur_idFacebook' ];
		
		$animal->setProprietaire($proprietaire);
		
		$requete_prepare = parent::getConnexion()->prepare("SELECT nomRace FROM t_animal, t_raceanimal WHERE RaceAnimal_race = idRace AND idAnimal = :idAnimal");
		$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
		$requete_prepare->execute();
		$donnees = $requete_prepare->fetch(PDO::FETCH_ASSOC);
		$animal->setRace($donnees[ 'nomRace' ]);
		
		return $animal;
	}
	
	/**
	 * Permet d'obtenir tout les animaux en vente de la base.
	 */
	public function getAnimauxEnVente()
	{
		//On récupère les animaux sans propriétaire.
		$requete_prepare = parent::getConnexion()->prepare("SELECT * FROM t_joueur_animal WHERE Joueur_idFacebook = '' ");
		$requete_prepare->execute();
	
		$donnees = $requete_prepare->fetchAll(PDO::FETCH_OBJ);
		$tabAnimals = array();
		$nbOcc = 0;
		foreach ($donnees as $value) 
		{
			$animal = $this->getAnimal($value->Animal_idAnimal);
			$animal->setProprietaire($value->Joueur_idFacebook);
			$tabAnimals[$nbOcc++] = $animal;
		}
	
		return $tabAnimals;
	}
	
	/**
	 * Permet d'inscrire un animal à un entrainement.
	 * Renvoie la date si l'insertion a été correcte, -1 sinon.
	 * @param String $idAnimal
	 * @param String $idEntrainement
	 * @param Date $dateInscription
	 */
	public function inscriptionEntrainement($idAnimal, $idEntrainement, $dateInscription)
	{
		$requete_prepare = parent::getConnexion()->prepare("INSERT INTO t_entrainement_animal (Entrainement_idEntrainement, Animal_idAnimal, dateSouscription) VALUES (:idEntrainement, :idAnimal, :date)");
		$requete_prepare->bindParam(':idEntrainement', $idEntrainement, PDO::PARAM_STR);
		$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
		$requete_prepare->bindParam(':date', $dateInscription, PDO::PARAM_STR);
		$bool = $requete_prepare->execute();
		$return = ($bool == true) ? $dateInscription : "-1";
		return $return;
	}
	
	/**
	 * Permet de désinscrire un animal à un entrainement.
	 * @param String $idAnimal
	 * @param String $idEntrainement
	 */
	public function desinscriptionEntrainement($idAnimal, $idEntrainement)
	{
		$requete_prepare = parent::getConnexion()->prepare("DELETE FROM t_entrainement_animal WHERE Entrainement_idEntrainement = :idEntrainement AND Animal_idAnimal = :idAnimal");
		$requete_prepare->bindParam(':idEntrainement', $idEntrainement, PDO::PARAM_STR);
		$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
		$bool = $requete_prepare->execute();
	}
	
	/**
	 * Permet de savoir si l'animal est en train de s'entrainer.
	 * @param String $idAnimal L'identifiant de l'animal
	 * @return true si il s'entraine
	 * 		   false sinon
	 */
	public function isTraining($idAnimal)
	{
		$currentTime = time();
		$requete_prepare = parent::getConnexion()->prepare("SELECT COUNT(Animal_idAnimal) as COUNT FROM t_entrainement_animal as TEA, t_entrainement as TE WHERE TEA.Entrainement_idEntrainement = TE.idEntrainement AND Animal_idAnimal = :idAnimal AND dateDebut < :currentTime AND dateDebut + duree > :currentTime");
		$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
		$requete_prepare->bindParam(':currentTime', $currentTime, PDO::PARAM_INT);
		$bool = $requete_prepare->execute();
		$count = $requete_prepare->fetch(PDO::FETCH_OBJ)->COUNT;
		return ($count == 0) ? false : true;
	}
	
	/**
	 * Entraine l'animal dont l'identifiant est passé en paramètre (ajoute les points d'amélioration prévus par l'offre de l'entrainement).
	 * Valide l'entrainement pour l'animal donné.
	 * @param Entrainement $entrainement L'entrainement concerné
	 * @param String $idAnimal L'identifiant de l'animal
	 * @return L'animal entrainé.
	 */
	public function entrainerAnimal(Entrainement $entrainement, $idAnimal)
	{
		$amelioration_attaque = $entrainement->getOffre()->getAttaqueOffre();
		$amelioration_defense = $entrainement->getOffre()->getDefenseOffre();
		$amelioration_vie = $entrainement->getOffre()->getVieOffre();
		
		//On améliore les compétences de l'animal selon l'offre de l'entrainement
		$requete_prepare = parent::getConnexion()->prepare("UPDATE t_animal SET attaque = attaque + :attaque, defense = defense + :defense, vie = vie + :vie WHERE idAnimal = :idAnimal");
		$requete_prepare->bindParam(':attaque', $amelioration_attaque, PDO::PARAM_INT);
		$requete_prepare->bindParam(':defense', $amelioration_defense, PDO::PARAM_INT);
		$requete_prepare->bindParam(':vie', $amelioration_vie, PDO::PARAM_INT);
		$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
		$bool = $requete_prepare->execute();
		
		//On valide l'entrainement
		$idEntrainement = $entrainement->getIdEntrainement();
		$requete_prepare = parent::getConnexion()->prepare("UPDATE t_entrainement_animal SET valide = 1 WHERE Entrainement_idEntrainement = :idEntrainement AND Animal_idAnimal = :idAnimal");
		$requete_prepare->bindParam(':idEntrainement', $idEntrainement, PDO::PARAM_STR);
		$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
		$bool = $requete_prepare->execute();
		
		//On récupère l'animal concerné pour le retourner
		$requete_prepare = parent::getConnexion()->prepare("SELECT * FROM t_animal WHERE idAnimal = :idAnimal");
		$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
		$requete_prepare->execute();
		$donnees = $requete_prepare->fetch(PDO::FETCH_ASSOC);
		$identifiant = $donnees[ 'idAnimal' ];
		$nomAnimal = $donnees[ 'nomAnimal' ];
		$attaque = $donnees[ 'attaque' ];
		$defense = $donnees[ 'defense' ];
		$vie = $donnees[ 'vie' ];
		$niveau = $donnees[ 'niveau' ];
		
		$requete_prepare = parent::getConnexion()->prepare("SELECT * FROM t_joueur_animal WHERE Animal_idAnimal = :idAnimal");
		$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
		$requete_prepare->execute();
		$donnees = $requete_prepare->fetch(PDO::FETCH_ASSOC);
		$proprietaire = $donnees[ 'Joueur_idFacebook' ];
		
		return new Animal($identifiant, $proprietaire, $nomAnimal, $vie, $defense, $attaque, $niveau);
	}
	
	
	/**
	 * Augmente le niveau de l'animal. Si une compétence le concernant est disponible au niveau atteint, 
	 * l'animal l'apprendra.
	 * @param String $idAnimal L'identifiant de l'animal
	 * @return La compétence apprise/null si aucune compétence n'est disponible.
	 */
	public function levelUpAnimal($idAnimal)
	{
		//Déclaration
		$competence = null;
		
		//On incrémente le niveau de l'animal
		$requete_prepare = parent::getConnexion()->prepare("UPDATE t_animal SET niveau = niveau + 1 WHERE idAnimal = :idAnimal");
		$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
		$bool = $requete_prepare->execute();
	
		//On récupère le nouveau niveau de l'animal
		$requete_prepare = parent::getConnexion()->prepare("SELECT niveau, RaceAnimal_race as race FROM t_animal WHERE idAnimal = :idAnimal");
		$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
		$requete_prepare->execute();
		$donnees = $requete_prepare->fetch(PDO::FETCH_ASSOC);
		$niveau = $donnees[ 'niveau' ];
		$race = $donnees[ 'race' ];
		
		//On regarde s'il y a des compétences à apprendre pour l'animal à ce niveau
		$requete_prepare = parent::getConnexion()->prepare("SELECT Competence_idCompetence as competence FROM t_competence_raceanimal, t_competence WHERE t_competence_raceanimal.Competence_idCompetence = t_competence.idCompetence AND niveauRequis = :niveau AND RaceAnimal_idRace = :race");
		$requete_prepare->bindParam(':race', $race, PDO::PARAM_STR);
		$requete_prepare->bindParam(':niveau', $niveau, PDO::PARAM_STR);
		$requete_prepare->execute();
		$donnees = $requete_prepare->fetch(PDO::FETCH_ASSOC);
		$competenceAapprendre = $donnees[ 'competence' ];
		
		//Une compétence est disponible
		if($competenceAapprendre != null)
		{
			//On apprend cette compétence à l'animal
			$requete_prepare = parent::getConnexion()->prepare("INSERT INTO t_competence_animal (Competence_idCompetence, Animal_idAnimal) VALUES (:idCompetence, :idAnimal)");
			$requete_prepare->bindParam(':idCompetence', $competenceAapprendre, PDO::PARAM_STR);
			$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
			$bool = $requete_prepare->execute();
			
			//On récupère la compétence apprise
			$requete_prepare = parent::getConnexion()->prepare("SELECT * FROM t_competence WHERE idCompetence = :idCompetence");
			$requete_prepare->bindParam(':idCompetence', $competenceAapprendre, PDO::PARAM_STR);
			$requete_prepare->execute();
			$donnees = $requete_prepare->fetch(PDO::FETCH_ASSOC);
			$idCompetence = $donnees[ 'idCompetence' ];
			$nomCompetence = $donnees[ 'nomCompetence' ];
			$degats = $donnees[ 'degats' ];
			$type = $donnees[ 'type' ];
			
			$competence = new Competence($idCompetence, $nomCompetence, $degats, $type);
		}
	
		return $competence;
	}
	
	/**
	 * Permet de savoir le niveau de l'animal dont l'id est passé en paramètre.
	 * @param String $idAnimal
	 */
	public function getLevelById($idAnimal)
	{
		//On récupère le niveau de l'animal
		$requete_prepare = parent::getConnexion()->prepare("SELECT niveau FROM t_animal WHERE idAnimal = :idAnimal");
		$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
		$requete_prepare->execute();
		$donnees = $requete_prepare->fetch(PDO::FETCH_ASSOC);
		$niveau = $donnees[ 'niveau' ];
		return $niveau;
	}
	
	
	
        /**
         * Permet d'obtenir les competences d'un animal.
         * @param String $idAnimal L'identifiant de l'animal
         * @return Un tableau contenant les compétences
         */
        public function getCompetencesByAnimal($idAnimal)
        {
                $requete_prepare = parent::getConnexion()->prepare("SELECT * FROM t_competence_animal, t_competence WHERE idCompetence = Competence_idCompetence AND Animal_idAnimal = :idAnimal");
                $requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
                $requete_prepare->execute();
        
                $competences = array();
                while($donnees = $requete_prepare->fetch(PDO::FETCH_ASSOC))
                {
                        $competence = array();
                        $competence[ 'idCompetence' ] = $donnees[ 'idCompetence' ];
                        $competence[ 'nomCompetence' ] = $donnees[ 'nomCompetence' ];
                        $competence[ 'degats' ] = $donnees[ 'degats' ];
                        $competence[ 'type' ] = $donnees[ 'type' ];
                                
                        $competences[] = $competence;
                }
                return $competences;
        }
	
	/**
	 * Permet d'obtenir la compétence correspondant à l'identifiant donné.
	 * @param String $idCompetence L'identifiant de la compétence
	 * @return La compétence
	 */
	public function getCompetenceById($idCompetence)
	{
		$requete_prepare = parent::getConnexion()->prepare("SELECT * FROM t_competence WHERE idCompetence = :idCompetence");
		$requete_prepare->bindParam(':idCompetence', $idCompetence, PDO::PARAM_STR);
		$requete_prepare->execute();
	
		$donnees = $requete_prepare->fetch(PDO::FETCH_ASSOC);
		$competence = new Competence($donnees[ 'idCompetence' ], $donnees[ 'nomCompetence' ], $donnees[ 'degats' ], $donnees[ 'type' ], $donnees[ 'codePuissance' ]);

		return $competence;
	}
	
	/**
	 * Permet de modifier la vie d'un animal
	 * @param String $idAnimal L'identifiant de l'animal
	 * @param int $vie Sa nouvelle vie
	 * @param true si la modification a eu lieu
	 * 		  false sinon
	 */
	public function updateVie($idAnimal, $vie)
	{
		$requete_prepare = parent::getConnexion()->prepare("UPDATE t_animal SET vie = :vie WHERE idAnimal = :idAnimal");
		$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
		$requete_prepare->bindParam(':vie', $vie, PDO::PARAM_INT);
		return $requete_prepare->execute();
	}
	
	/**
	 * Permet de supprimer la relation de propriété entre un animal et un joueur
	 * @param String $idAnimal L'identifiant de l'animal
	 * @param true si la modification a eu lieu
	 * 		  false sinon
	 */
	public function deleteRelationAnimalProprietaire($idAnimal)
	{
		$requete_prepare = parent::getConnexion()->prepare("DELETE FROM t_joueur_animal WHERE Animal_idAnimal = :idAnimal");
		$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
		return $requete_prepare->execute();
	}
	
	

}



?>