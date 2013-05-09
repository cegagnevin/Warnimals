<?php
require_once('./../../lib/simpletest/autorun.php');
require_once './../metiers/Joueur.php';
require_once './../mapping/DAO.php';

/**
 *
 * Classe de test de la classe Joueur (métiers).
 *
 */
class JoueurTestCase extends UnitTestCase 
{

	/**
	 * Test des getters et des setters de la classe Joueur. 
	 */
	function test_getters_setters() 
	{
		/* Initialisation du joueur à tester */
		$joueur = new Joueur(null,null,null,null,null); //Création d'un joueur vide
		$joueur->setIdFacebook("100000932826926"); /* IdFacebook */
		$amis = array("100000932826921","100000932826922","100000932826923");
		$joueur->setAmis($amis); /* Liste des amis */
		$joueur->setNomJoueur("Alexis"); /* Nom du joueur */
		$joueur->setCredit(1000); /* Credit du joueur */
		$joueur->setDateInscription("24-11-2012"); /* Date d'inscription du joueur */
		
		/* Asserts */
		$this->assertNotNull($joueur);
		$this->assertEqual($joueur->getIdFacebook(), "100000932826926"); /* IdFacebook */
		$this->assertEqual($joueur->getAmis(), array("100000932826921","100000932826922","100000932826923")); /* Liste des amis */
		$this->assertEqual($joueur->getNomJoueur(), "Alexis"); /* Nom du joueur */
		$this->assertEqual($joueur->getCredit(), 1000); /* Credit du joueur */
		$this->assertEqual($joueur->getDateInscription(), "24-11-2012"); /* Date d'inscription du joueur */
	}
	
	/**
	 * Test du constructeur totalement renseigné de Joueur.
	 */
	function test_constructeur_total() 
	{
		/* Constructeur totalement renseigné */
		$amis = array("100000932826921","100000932826922","100000932826923");
		$joueur = new Joueur("100000932826926","Alexis", $amis, 1000, "24-11-2012");

		/* Asserts */
		$this->assertNotNull($joueur);
		$this->assertEqual($joueur->getIdFacebook(), "100000932826926"); /* IdFacebook */
		$this->assertEqual($joueur->getAmis(), array("100000932826921","100000932826922","100000932826923")); /* Liste des amis */
		$this->assertEqual($joueur->getNomJoueur(), "Alexis"); /* Nom du joueur */
		$this->assertEqual($joueur->getCredit(), 1000); /* Credit du joueur */
		$this->assertEqual($joueur->getDateInscription(), "24-11-2012"); /* Date d'inscription du joueur */
	}
	
	/**
	 * Test du toString() de Joueur.
	 */
	function test_toString() 
	{
		$amis = array("100000932826921","100000932826922","100000932826923");
		$joueur = new Joueur("100000932826926","Alexis", $amis, 1000, "24-11-2012");
		$toStringReceived = $joueur->__toString();
		
		/* Assert du toString() */
		$this->assertNotNull($joueur);
		$toStringExpected = "Alexis (100000932826926). Inscrit le : 24-11-2012. Crédit : 1000. Liste des amis (3) : 100000932826921 | 100000932826922 | 100000932826923 | ";
		$this->assertEqual($toStringReceived, $toStringExpected); 
	}
}
 
?>