<?php
require_once('./../../lib/simpletest/autorun.php');
require_once './../metiers/OffreEntrainement.php';

/**
 *
 * Classe de test de la classe OffreEntrainement (métiers).
 *
 */
class OffreEntrainementTestCase extends UnitTestCase
{

	/**
	 *
	 * Test des getters et des setters de la classe OffreEntrainement.
	 *
	 */
	function test_getters_setters()
	{
		/* Initialisation du joueur à tester */
		$offre = new OffreEntrainement(null, null, null, null, null);
		$offre->setIdOffre("OTEST001");
		$offre->setAttaqueOffre(2);
		$offre->setDefenseOffre(1);
		$offre->setVieOffre(0);
		$offre->setLevelUp(1);
		
		/* Asserts */
		$this->assertNotNull($offre);
		$this->assertEqual($offre->getIdOffre(), "OTEST001"); 
		$this->assertEqual($offre->getAttaqueOffre(), 2);
		$this->assertEqual($offre->getDefenseOffre(), 1); 
		$this->assertEqual($offre->getVieOffre(), 0); 
		$this->assertEqual($offre->getLevelUp(), 1); 
		$this->assertEqual($offre->getSommePoints(), 4);
	}

	/**
	 *
	 * Test du constructeur totalement renseigné de l'Offre.
	 *
	 */
	function test_constructeur_total()
	{
		/* Constructeur totalement renseigné */
		$offre = new OffreEntrainement("OTEST001", 2, 1, 0, 1);
		
		/* Asserts */
		$this->assertNotNull($offre);
		$this->assertEqual($offre->getIdOffre(), "OTEST001"); 
		$this->assertEqual($offre->getAttaqueOffre(), 2); 
		$this->assertEqual($offre->getDefenseOffre(), 1); 
		$this->assertEqual($offre->getVieOffre(), 0); 
		$this->assertEqual($offre->getLevelUp(), 1);
	}

	/**
	 *
	 * Test du toString() de l'Offre.
	 *
	 */
	function test_toString()
	{
		$offre = new OffreEntrainement("OTEST001", 2, 1, 0, 1);
		$toStringReceived = $offre->__toString();
		
		/* Assert du toString() */
		$this->assertNotNull($offre);
		$toStringExpected = "Numero d'offre : OTEST001. Attaque : 2. Defense : 1. Vie : 0. LevelUp : 1";
		$this->assertEqual($toStringReceived, $toStringExpected);
	}
	
}

?>