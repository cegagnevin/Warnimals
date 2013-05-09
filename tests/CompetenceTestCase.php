<?php
require_once('./../../lib/simpletest/autorun.php');
require_once './../metiers/Competence.php';

/**
 *
 * Classe de test de la classe Competence (métiers).
 *
 */
class CompetenceTestCase extends UnitTestCase
{

	/**
	 *
	 * Test des getters et des setters de la classe Competence.
	 *
	 */
	function test_getters_setters()
	{
		/* Initialisation de la compétence à tester */
		$competence = new Competence(null, null, null, null, null);
		$competence->setIdCompetence("CTEST001");
		$competence->setNomCompetence("Morsure");
		$competence->setType("attaque");
		$competence->setDegats(5);
		$competence->setCodePuissance(1);
		
		/* Asserts */
		$this->assertNotNull($competence);
		$this->assertEqual($competence->getIdCompetence(), "CTEST001"); 
		$this->assertEqual($competence->getNomCompetence(), "Morsure");
		$this->assertEqual($competence->getDegats(), 5); 
		$this->assertEqual($competence->getType(), 'attaque');
		$this->assertEqual($competence->getCodePuissance(), 1);
	}

	/**
	 *
	 * Test du constructeur totalement renseigné de la compétence.
	 *
	 */
	function test_constructeur_total()
	{
		/* Constructeur totalement renseigné */
		$competence = new Competence("CTEST001", "Morsure", 5, 'attaque', 1);
		
		/* Asserts */
		$this->assertNotNull($competence);
		$this->assertEqual($competence->getIdCompetence(), "CTEST001"); 
		$this->assertEqual($competence->getNomCompetence(), "Morsure"); 
		$this->assertEqual($competence->getDegats(), 5); 
		$this->assertEqual($competence->getType(), 'attaque');
		$this->assertEqual($competence->getCodePuissance(), 1);
	}

	/**
	 *
	 * Test du toString() de la compétence.
	 *
	 */
	function test_toString()
	{
		$competence = new Competence("CTEST001", "Morsure", 5, 'attaque', 1);
		$toStringReceived = $competence->__toString();
		
		/* Assert du toString() */
		$this->assertNotNull($competence);
		$toStringExpected = "Numero de compétence : CTEST001. Nom : Morsure. Dégats : 5. Type : attaque. Code puissance : 1.";
		$this->assertEqual($toStringReceived, $toStringExpected);
	}
	
}

?>