<?php
require_once 'DAO.php';
require_once '../metiers/Joueur.php';
require_once '../metiers/Animal.php';
require_once '../modeles/JoueurModele.php';

/**
 *
 * DAO du Joueur
 *
 */
class JoueurDAO extends DAO
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
	 * Permet de modifier le crédit du joueur.
	 * @param Le crédit à débiter ou créditer au joueur.
	 * @param Le signe de l'opération (- si l'on veut débiter, + si l'on veut créditer).
	 * @param Le joueur sur lequel on souhaite faire l'opération.
	 */
	public function updateCredit($credit, $signe, $joueur)
	{
		try 
		{
			$requete_prepare = parent::getConnexion()->prepare("UPDATE t_joueur SET credit = credit $signe :credit WHERE idFacebook = :idFacebook");
			//$requete_prepare->bindParam(':signe', $signe, PDO::PARAM_STR);
			$requete_prepare->bindParam(':credit',	$credit, PDO::PARAM_INT);
			$requete_prepare->bindParam(':idFacebook', $joueur, PDO::PARAM_STR);
			$requete_prepare->execute();
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : <pre>'.$e->getTraceAsString().'</pre><br/>';
		}
	}
	
	/**
	 * Permet savoir si le joueur dont l'identifiant passé en paramètre existe en base ou pas.
	 * @param idJoueur L'identifiant du joueur dont on veut savoir s'il existe en base.
	 * @return <code>true</code> si le joueur existe en base.
	 *		   <code>false</code> sinon
	 */
	public function isPlayerExist($idJoueur)
	{
		try 
		{
			$requete_prepare = parent::getConnexion()->prepare("SELECT COUNT(*) as COUNT FROM t_joueur WHERE idFacebook = :idFacebook");
			$requete_prepare->bindParam(':idFacebook', $idJoueur, PDO::PARAM_STR);
			$requete_prepare->execute();
			$result = $requete_prepare->fetch(PDO::FETCH_OBJ)->COUNT;
			if($result == 1) return true;
			return false;
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : <pre>'.$e->getTraceAsString().'</pre><br/>';
		}
	}
	
	/**
	 * Créé un joueur dans la base de données.
	 * @param joueur Le joueur à créer en base.
	 * @return true si le joueur a été créé
	 * 		   false sinon
	 */
	public function createJoueur(Joueur $joueur)
	{
		try
		{
			$idJoueur = $joueur->getIdFacebook();
			$credit = $joueur->getCredit();
			$dateInscription = $joueur->getDateInscription();
			$requete_prepare = parent::getConnexion()->prepare("INSERT INTO t_joueur (idFacebook, credit, dateInscription) VALUES (:idFb, :credit, :dateInscription)");
			$requete_prepare->bindParam(':idFb', $idJoueur, PDO::PARAM_STR);
			$requete_prepare->bindParam(':credit', $credit, PDO::PARAM_INT);
			$requete_prepare->bindParam(':dateInscription', $dateInscription, PDO::PARAM_INT);
			return $requete_prepare->execute();
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : <pre>'.$e->getTraceAsString().'</pre><br/>';
		}
	}
	
	/**
	 * Permet d'obtenir un joueur de la base dont l'identifiant est donné en paramètre.
	 * @param id joueur L'identifiant du joueur.
	 * @return Le joueur correspondant à l'identifiant passé en paramètre.
	 */
	public function getJoueur($idJoueur)
	{
		try
		{
			$requete_prepare = parent::getConnexion()->prepare("SELECT * FROM t_joueur WHERE idFacebook = :idFb");
			$requete_prepare->bindParam(':idFb', $idJoueur, PDO::PARAM_STR);
			$requete_prepare->execute();
			$donnees = $requete_prepare->fetch(PDO::FETCH_ASSOC);
			$idJoueur = $donnees[ 'idFacebook' ];
			$credit = $donnees[ 'credit' ];
			$dateInscription = $donnees[ 'dateInscription' ];
			$nbVictoires = $donnees[ 'nbVictoires' ];
			$nbDefaites = $donnees[ 'nbDefaites' ];
			$nbAbandons = $donnees[ 'nbAbandons' ];
			return new Joueur($idJoueur, null, null, $credit, $dateInscription,  $nbVictoires, $nbDefaites, $nbAbandons);
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : <pre>'.$e->getTraceAsString().'</pre><br/>';
		}
	}
	
	/**
	 * Permet d'obtenir l'animal du joueur passé en paramètre.
	 * @param idJoueur L'identifiant du joueur.
	 * @return L'animal appartenant au joueur.
	 * 		   null si aucun resultat
	 */
	public function getAnimalByJoueur($idJoueur)
	{
		try
		{
			$requete_prepare = parent::getConnexion()->prepare("SELECT idAnimal, nomAnimal, vie, defense, attaque, niveau, nbVictoires, nbDefaites, nbAbandons FROM t_joueur_animal, t_animal WHERE Joueur_idFacebook = :idFb AND Animal_idAnimal = idAnimal");
			$requete_prepare->bindParam(':idFb', $idJoueur, PDO::PARAM_STR);
			$requete_prepare->execute();
			$donnees = $requete_prepare->fetch(PDO::FETCH_ASSOC);
			
			if(empty($donnees))
				return null;
			
			$idAnimal = $donnees['idAnimal'];
			$nomAnimal = $donnees['nomAnimal'];
			$vie = $donnees['vie'];
			$defense = $donnees['defense'];
			$attaque = $donnees['attaque'];
			$niveau = $donnees['niveau'];
			$nbVictoires = $donnees['nbVictoires'];
			$nbDefaites = $donnees['nbDefaites'];
			$nbAbandons = $donnees['nbAbandons'];
			
			$requete_prepare = parent::getConnexion()->prepare("SELECT nomRace FROM t_animal, t_raceanimal WHERE RaceAnimal_race = idRace AND idAnimal = :idAnimal");
			$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
			$requete_prepare->execute();
			$donnees = $requete_prepare->fetch(PDO::FETCH_ASSOC);
			$race = $donnees['nomRace'];
			$a= new Animal($idAnimal, $idJoueur, $nomAnimal, $vie, $defense, $attaque, $niveau, $race, $nbVictoires, $nbDefaites, $nbAbandons);
			return $a;
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : <pre>'.$e->getTraceAsString().'</pre><br/>';
		}
	}
	
	/**
	 * Permet d'obtenir le propriétaire de l'animal passé en paramètre.
	 * @param idAnimal L'identifiant de l'animal.
	 * @return Le propriétaire de l'animal
	 */
	public function getJoueurByAnimal($idAnimal)
	{
		try
		{
			$requete_prepare = parent::getConnexion()->prepare("SELECT * FROM t_joueur_animal, t_joueur WHERE Joueur_idFacebook = idFacebook AND Animal_idAnimal = :idAnimal");
			$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
			$requete_prepare->execute();
			$donnees = $requete_prepare->fetch(PDO::FETCH_ASSOC);
			$idFacebook = $donnees['idFacebook'];
			$credit = $donnees['credit'];
			$dateInscription = $donnees['dateInscription'];
			return new Joueur($idFacebook, null, array(), $credit, $dateInscription);
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : <pre>'.$e->getTraceAsString().'</pre><br/>';
		}
	}
	
	/**
	 * Permet d'ajouter une victoire au joueur et à l'animal victorieux.
	 * @param idAnimal L'identifiant de l'animal victorieux.
	 * @return True si la modification a bien été faite
	 * 		   False sinon
	 */
	public function addVictoire($idAnimal)
	{
		try
		{
			//Ajoute une victoire à l'animal
			$requete_prepare = parent::getConnexion()->prepare("UPDATE t_animal SET nbVictoires = nbVictoires + 1 WHERE idAnimal = :idAnimal");
			$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
			$bool1 = $requete_prepare->execute();
			
			$joueur = JoueurModele::getInstance()->getJoueurByAnimal($idAnimal);
			
			//Ajoute une victoire à son propriétaire
			$idJoueur = $joueur->getIdFacebook();
			$requete_prepare = parent::getConnexion()->prepare("UPDATE t_joueur SET nbVictoires = nbVictoires + 1 WHERE idFacebook = :idFacebook");
			$requete_prepare->bindParam(':idFacebook', $idJoueur, PDO::PARAM_STR);
			$bool2 = $requete_prepare->execute();
			
			return $bool1 && $bool2;
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : <pre>'.$e->getTraceAsString().'</pre><br/>';
		}
	}
	
	/**
	 * Permet d'ajouter une défaite au joueur et à l'animal défaitiste.
	 * @param idAnimal L'identifiant de l'animal défaitiste.
	 * @return True si la modification a bien été faite
	 * 		   False sinon
	 */
	public function addDefaite($idAnimal)
	{
		try
		{
			//Ajoute une défaite à l'animal
			$requete_prepare = parent::getConnexion()->prepare("UPDATE t_animal SET nbDefaites = nbDefaites + 1 WHERE idAnimal = :idAnimal");
			$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
			$bool1 = $requete_prepare->execute();
				
			$joueur = JoueurModele::getInstance()->getJoueurByAnimal($idAnimal);
				
			//Ajoute une défaite à son propriétaire
			$idJoueur = $joueur->getIdFacebook();
			$requete_prepare = parent::getConnexion()->prepare("UPDATE t_joueur SET nbDefaites = nbDefaites + 1 WHERE idFacebook = :idFacebook");
			$requete_prepare->bindParam(':idFacebook', $idJoueur, PDO::PARAM_STR);
			$bool2 = $requete_prepare->execute();
				
			return $bool1 && $bool2;
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : <pre>'.$e->getTraceAsString().'</pre><br/>';
		}
	}
	
	/**
	 * Permet d'ajouter un abandon au joueur et à l'animal concerné.
	 * @param idAnimal L'identifiant de l'animal concerné.
	 * @return True si la modification a bien été faite
	 * 		   False sinon
	 */
	public function addAbandon($idAnimal)
	{
		try
		{
			//Ajoute un abandon à l'animal
			$requete_prepare = parent::getConnexion()->prepare("UPDATE t_animal SET nbAbandons = nbAbandons + 1 WHERE idAnimal = :idAnimal");
			$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
			$bool1 = $requete_prepare->execute();
	
			$joueur = JoueurModele::getInstance()->getJoueurByAnimal($idAnimal);
	
			//Ajoute un abandon à son propriétaire
			$idJoueur = $joueur->getIdFacebook();
			$requete_prepare = parent::getConnexion()->prepare("UPDATE t_joueur SET nbAbandons = nbAbandons + 1 WHERE idFacebook = :idFacebook");
			$requete_prepare->bindParam(':idFacebook', $idJoueur, PDO::PARAM_STR);
			$bool2 = $requete_prepare->execute();
	
			return $bool1 && $bool2;
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : <pre>'.$e->getTraceAsString().'</pre><br/>';
		}
	}
	
}
?>