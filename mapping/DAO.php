<?php
require_once '../settings/Configuration.php';

/**
 * Classe mère qui gère la connexion à la base de données. Les DAO spécialisés devront étendre cette classe.
 */
class DAO 
{		
	/** La connexion à la base de données */
	private $_connexion = null;
	
	/**
	 * Constructeur. Initialise la connexion à la base de données.
	 * @throws PDOException Lorsqu'un problème survient lors de l'utilisation d'un objet PDO.
	 */
	public function __construct()
	{
		if($this->_connexion == null)
		{
			if(MODE_TEST) //Mode test
			{
				$this->_connexion = new PDO('mysql:host='.HOST_LOCAL.';dbname='.DB_LOCAL, USER_LOCAL, PWD_LOCAL);
			}
			else //Mode production
			{
				$this->_connexion = new PDO('mysql:host='.HOST_FRANCESERV.';dbname='.DB_FRANCESERV, USER_FRANCESERV, PWD_FRANCESERV);
			}
			$this->_connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
	}
	
	/**
	 * Destructeur. Ferme la connexion à la base de données.
	 */
	public function __destruct()
	{
		$this->_connexion = null;
	}
	
	/**
	 * Permet d'obtenir la connexion à la base de données.
	 * @return La connexion à la base.
	 */
	public function getConnexion()
	{
		return $this->_connexion;
	}
	
}



?>