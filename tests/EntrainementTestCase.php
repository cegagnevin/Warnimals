<?php
require_once './../../lib/simpletest/autorun.php';
require_once './../metiers/Entrainement.php';

/**
 *
 * Classe de test de la classe Entrainement (métiers).
 *
 */
class EntrainementTestCase extends UnitTestCase
{

	/**
	 * Test des getters et des setters de la classe Entrainement.
	 */
	function test_getters_setters()
	{
		/* Initialisation de l'entrainement à tester */
		$entrainement = new Entrainement(null, null, null, null); //Création d'un entrainement vide
		$entrainement->setIdEntrainement("ETEST001"); /* IdEntrainement*/
		$entrainement->setDuree(360); /* Durée de l'entrainement */
		$entrainement->setPrix(50); /* Prix de l'entrainement */
		$entrainement->setDateDebut(1234567890); /* Date de début de l'entrainement */
		
		/* Asserts */
		$this->assertNotNull($entrainement);
		$this->assertEqual($entrainement->getIdEntrainement(), "ETEST001"); /* IdEntrainement */
		$this->assertEqual($entrainement->getDuree(), 360); /* Durée de l'entrainement */
		$this->assertEqual($entrainement->getPrix(), 50); /* Prix de l'entrainement */
		$this->assertEqual($entrainement->getDateDebut(), 1234567890); /* Date de début de l'entrainement */
	}
	
	/**
	 * Test de la méthode getEtat() de la classe Entrainement.
	 */
	function test_getEtat()
	{
		/* Entrainement à venir */
		$dateDebut = time()+60;
		$entrainement = new Entrainement("ETEST001", 360, 50, $dateDebut); 
		/* Exécution de la méthode à tester */
		$etatReceived = $entrainement->getEtat();
		/* Asserts */
		$this->assertNotNull($entrainement);
		$this->assertEqual($etatReceived, 0); 
		
		/* Entrainement en cours */
		$dateDebut = time()-60;
		$entrainement = new Entrainement("ETEST001", 360, 50, $dateDebut);
		/* Exécution de la méthode à tester */
		$etatReceived = $entrainement->getEtat();
		/* Asserts */
		$this->assertNotNull($entrainement);
		$this->assertEqual($etatReceived, 1);

		/* Entrainement fin */
		$dateDebut = time()-360;
		$entrainement = new Entrainement("ETEST001", 60, 50, $dateDebut);
		/* Exécution de la méthode à tester */
		$etatReceived = $entrainement->getEtat();
		/* Asserts */
		$this->assertNotNull($entrainement);
		$this->assertEqual($etatReceived, 2);
	}

	/**
	 * Test du constructeur totalement renseigné de l'Entrainement.
	 */
	function test_constructeur_total()
	{
		/* Constructeur totalement renseigné */
		$entrainement = new Entrainement("E1", 360, 50, 1234567890);
		
		/* Asserts */
		$this->assertNotNull($entrainement);
		$this->assertEqual($entrainement->getIdEntrainement(), "E1"); /* IdEntrainement */
		$this->assertEqual($entrainement->getDuree(), 360); /* Durée de l'entrainement */
		$this->assertEqual($entrainement->getPrix(), 50); /* Prix de l'entrainement */
		$this->assertEqual($entrainement->getDateDebut(), 1234567890); /* Date de début de l'entrainement */
	}

	/**
	 *
	 * Test du toString() de l'Entrainement.
	 *
	 */
	function test_toString()
	{
		$entrainement = new Entrainement("E1", 360, 50, 1234567890);
		$toStringReceived = $entrainement->__toString();
		
		/* Assert du toString() */
		$this->assertNotNull($entrainement);
		$toStringExpected = "(E1) - Duree : 360. Prix : 50. Date debut : 1234567890";
		$this->assertEqual($toStringReceived, $toStringExpected);
	}
	
}

?>