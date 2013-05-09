<?php
require_once './../../lib/simpletest/autorun.php';
require_once './../metiers/EntrainementCollectif.php';
require_once './../metiers/Animal.php';
require_once './../metiers/OffreEntrainement.php';

/**
 *
 * Classe de test de la classe EntrainementCollectif (métiers).
 *
 */
class EntrainementCollectifTestCase extends UnitTestCase
{

	/**
	 * Test des getters et des setters de la classe EntrainementCollectif.
	 */
	function test_getters_setters()
	{
		/* Initialisation de l'entrainement à tester */
		$offre = new OffreEntrainement("O1", 1, 2, 3, 0);
		$entrainement = new EntrainementCollectif(null, null, null, null, $offre, null); //Création d'un entrainement collectif vide
		$entrainement->setIdEntrainement("ETEST001"); /* IdEntrainement*/
		$entrainement->setDuree(360); /* Durée de l'entrainement */
		$entrainement->setPrix(50); /* Prix de l'entrainement */
		$entrainement->setDateDebut(1234567890); /* Date de début de l'entrainement */
		$entrainement->setOffre($offre); /* L'offre */
		$entrainement->setNiveauMax(5); /* Le niveau max pour participer à l'entrainement */
		$entrainement->setNbParticipantsMin(10); /* Le nb de participants min pour que l'entrainement ait lieu */
		$entrainement->setAnnule(0); /* Non annule */
		$animal1 = new Animal("ATEST001", "JTEST001", "Animal test 1", 30, 10, 20, 1); /* Animal suivant l'entrainement */
		$animal2 = new Animal("ATEST002", "JTEST002", "Animal test 2", 40, 10, 50, 1); /* Animal suivant l'entrainement */
		$animaux = array();
		array_push($animaux, $animal1);
		array_push($animaux, $animal2);
		$entrainement->setAnimauxInscrits($animaux);
		
		/* Asserts */
		$this->assertNotNull($entrainement);
		$this->assertEqual($entrainement->getIdEntrainement(), "ETEST001"); /* IdEntrainement */
		$this->assertEqual($entrainement->getDuree(), 360); /* Durée de l'entrainement */
		$this->assertEqual($entrainement->getPrix(), 50); /* Prix de l'entrainement */
		$this->assertEqual($entrainement->getDateDebut(), 1234567890); /* Date de début de l'entrainement */
		$this->assertEqual($entrainement->getAnimauxInscrits(), $animaux); /* Animaux suivant l'entrainement */
		$this->assertEqual($entrainement->getType(), 'collectif'); /* Type d'entrainement */
		$this->assertEqual($entrainement->getOffre(), $offre); /* L'offre */
		$this->assertEqual($entrainement->getNiveauMax(), 5); /* Le niveau max pour participer à l'entrainement */
		$this->assertEqual($entrainement->getNbParticipantsMin(), 10); /* Le nb de participants min pour que l'entrainement ait lieu */
		$this->assertEqual($entrainement->getAnnule(), 0); /* Non annule */
	}

	/**
	 * Test du constructeur totalement renseigné de l'Entrainement.
	 */
	function test_constructeur_total()
	{
		/* Constructeur totalement renseigné */
		$animal1 = new Animal("ATEST001", "JTEST001", "Animal test 1", 30, 10, 20, 1); /* Animal suivant l'entrainement */
		$animal2 = new Animal("ATEST002", "JTEST002", "Animal test 2", 40, 10, 50, 1); /* Animal suivant l'entrainement */
		$animaux = array();
		array_push($animaux, $animal1);
		array_push($animaux, $animal2);
		$offre = new OffreEntrainement("O1", 1, 2, 3, 0);
		$entrainement = new EntrainementCollectif("ETEST002", 360, 50, 1234567890, $offre, $animaux, 5, 10, 0);
		
		/* Asserts */
		$this->assertNotNull($entrainement);
		$this->assertEqual($entrainement->getIdEntrainement(), "ETEST002"); /* IdEntrainement */
		$this->assertEqual($entrainement->getDuree(), 360); /* Durée de l'entrainement */
		$this->assertEqual($entrainement->getPrix(), 50); /* Prix de l'entrainement */
		$this->assertEqual($entrainement->getDateDebut(), 1234567890); /* Date de début de l'entrainement */
		$this->assertEqual($entrainement->getOffre(), $offre); /* L'offre */
		$this->assertEqual($entrainement->getAnimauxInscrits(), $animaux); /* Animal suivant l'entrainement */
		$this->assertEqual($entrainement->getNiveauMax(), 5); /* Le niveau max pour participer à l'entrainement */
		$this->assertEqual($entrainement->getNbParticipantsMin(), 10); /* Le nb de participants min pour que l'entrainement ait lieu */
		$this->assertEqual($entrainement->getAnnule(), 0); /* Non annule */
	}

	/**
	 *
	 * Test du toString() de l'Entrainement.
	 *
	 */
	function test_toString()
	{
		$animal1 = new Animal("ATEST001", "JTEST001", "Animal test 1", 30, 10, 20, 1); /* Animal suivant l'entrainement */
		$animal2 = new Animal("ATEST002", "JTEST002", "Animal test 2", 40, 10, 50, 1); /* Animal suivant l'entrainement */
		$animaux = array();
		array_push($animaux, $animal1);
		array_push($animaux, $animal2);
		$offre = new OffreEntrainement("O1", 1, 2, 3, 0);
		$entrainement = new EntrainementCollectif("ETEST002", 360, 50, 1234567890, $offre, $animaux);
		$toStringReceived = $entrainement->__toString();
		
		/* Assert du toString() */
		$this->assertNotNull($entrainement);
		$toStringExpected = "(ETEST002) - Duree : 360. Prix : 50. Date debut : 1234567890. Animaux inscrits : ATEST001 - ATEST002 - ";
		$this->assertEqual($toStringReceived, $toStringExpected);
	}
	
}

?>