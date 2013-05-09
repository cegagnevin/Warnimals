<?php
require_once 'DAO.php';



/**
 *
 * DAO d'un pari
 *
 */
class PariDAO extends DAO
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
	 * @return Un identifiant unique pour un pari.
	 */
	private function getNewIdPari()
	{
		$requete_prepare = parent::getConnexion()->prepare("SELECT idParis FROM t_pari");
		$requete_prepare->execute();
		$requete_prepare->setFetchMode(PDO::FETCH_OBJ);
		
		//On récupère le plus grand identifiant (sans le P)
		$last = 0;
		while($id = $requete_prepare->fetch())
		{
			$id = $id->idTransaction;
			$num = intval(substr($id, 1, strlen($id)-1));
			if($num > $last)
				$last=$num;
		}
		
		$newId = 'P1';
		if($last != 0) //On incrémente l'identifiant de 1
		{
			$newId = 'P'.($last+1);
		}
		return $newId;
	}
	
	
}



?>