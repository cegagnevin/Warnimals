<?php

require_once('./../../lib/simpletest/autorun.php');
require_once './../mapping/DAO.php';

/**
 *
 * Classe de test de la classe DAO (package mapping)
 *
 */
class DAOTestCase extends UnitTestCase
{

	/**
	 * Test de l'initialisation d'une connexion à la base de données dans le constructeur.
	 */
	function test_constructeur()
	{
		$dao = new DAO(); /* Initialisation de la connexion à la base lors de la construction */
		/* Asserts */
		$this->assertNotNull($dao);
		$this->assertNotNull($dao->getConnexion());
	}
	
	/**
	 * Test de la fermeture de la connexion à la base de données dans le constructeur.
	 */
	function test_destructeur()
	{
		$dao = new DAO(); 
		$dao->__destruct(); /* Fermeture de la connexion à la base */
		/* Assert */
		$this->assertNull($dao->getConnexion());
	}
	
	
}

?>