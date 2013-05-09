<?php
require_once './../../lib/simpletest/autorun.php';
require_once './../metiers/EntrainementIndividuel.php';
require_once './../metiers/Animal.php';
require_once './../metiers/OffreEntrainement.php';

/**
 *
 * Classe de test de la classe EntrainementIndividuel (métiers).
 *
 */
class EntrainementIndividuelTestCase extends UnitTestCase
{

	/**
	 * Test des getters et des setters de la classe EntrainementIndividuel.
	 */
	function test_getters_setters()
	{
		/* Initialisation de l'entrainement à tester */
		$entrainement = new EntrainementIndividuel(null, null, null, null, new OffreEntrainement(null, null, null, null, null), null); //Création d'un entrainement ind vide
		$entrainement->setIdEntrainement("ETEST001"); /* IdEntrainement*/
		$entrainement->setDuree(360); /* Durée de l'entrainement */
		$entrainement->setPrix(50); /* Prix de l'entrainement */
		$entrainement->setDateDebut(1234567890); /* Date de début de l'entrainement */
		$offre = new OffreEntrainement("O1", 1, 2, 3, 0);
		$entrainement->setOffre($offre); /* L'offre */
		//$animal = new Animal("ATEST001", "JTEST001", "Animal test", 30, 10, 20); /* Animal suivant l'entrainement */
		$idAnimal = "ATEST001";
		$entrainement->setAnimal($idAnimal);
		
		/* Asserts */
		$this->assertNotNull($entrainement);
		$this->assertEqual($entrainement->getIdEntrainement(), "ETEST001"); /* IdEntrainement */
		$this->assertEqual($entrainement->getDuree(), 360); /* Durée de l'entrainement */
		$this->assertEqual($entrainement->getPrix(), 50); /* Prix de l'entrainement */
		$this->assertEqual($entrainement->getDateDebut(), 1234567890); /* Date de début de l'entrainement */
		$this->assertEqual($entrainement->getAnimal(), $idAnimal); /* Animal suivant l'entrainement */
		$this->assertEqual($entrainement->getType(), 'individuel'); /* Type d'entrainement */
		$this->assertEqual($entrainement->getOffre(), $offre); /* L'offre */
	}

	/**
	 * Test du constructeur totalement renseigné de l'Entrainement.
	 */
	function test_constructeur_total()
	{
		/* Constructeur totalement renseigné */
		//$animal = new Animal("ATEST001", "JTEST001", "Animal test", 30, 10, 20); /* Animal suivant l'entrainement */
		$idAnimal = "ATEST001";
		$offre = new OffreEntrainement("O1", 1, 2, 3, 0);
		$entrainement = new EntrainementIndividuel("ETEST002", 360, 50, 1234567890, $offre, $idAnimal);
		
		/* Asserts */
		$this->assertNotNull($entrainement);
		$this->assertEqual($entrainement->getIdEntrainement(), "ETEST002"); /* IdEntrainement */
		$this->assertEqual($entrainement->getDuree(), 360); /* Durée de l'entrainement */
		$this->assertEqual($entrainement->getPrix(), 50); /* Prix de l'entrainement */
		$this->assertEqual($entrainement->getDateDebut(), 1234567890); /* Date de début de l'entrainement */
		$this->assertEqual($entrainement->getAnimal(), $idAnimal); /* Animal suivant l'entrainement */
		$this->assertEqual($entrainement->getOffre(), $offre); /* L'offre */
	}

	/**
	 *
	 * Test du toString() de l'Entrainement.
	 *
	 */
	function test_toString()
	{
		//$animal = new Animal("ATEST001", "JTEST001", "Animal test", 30, 10, 20);
		$idAnimal = "ATEST001";
		$offre = new OffreEntrainement("O1", 1, 2, 3, 0);
		$entrainement = new EntrainementIndividuel("ETEST002", 360, 50, 1234567890, $offre, $idAnimal);
		$toStringReceived = $entrainement->__toString();
		
		/* Assert du toString() */
		$this->assertNotNull($entrainement);
		$toStringExpected = "(ETEST002) - Duree : 360. Prix : 50. Date debut : 1234567890. Animal : ATEST001";
		$this->assertEqual($toStringReceived, $toStringExpected);
	}
	
}

?>