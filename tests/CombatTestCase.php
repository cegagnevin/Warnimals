<?php
require_once('./../../lib/simpletest/autorun.php');
require_once './../metiers/Combat.php';

/**
 *
 * Classe de test de la classe Combat (métiers).
 *
 */
class CombatTestCase extends UnitTestCase
{

	/**
	 *
	 * Test des getters et des setters de la classe Combat.
	 *
	 */
	function test_getters_setters()
	{
		/* Initialisation du joueur à tester */
		$combat = new Combat(null, null, null, null, null); 
		$combat->setIdCombat('C1'); 
		$combat->setAnimal1('A1'); 
		$combat->setAnimal2('A2'); 
		$combat->setDate(1362314659);
		$combat->setEstCommence(true);
		
		/* Asserts */
		$this->assertNotNull($combat);
		$this->assertEqual($combat->getIdCombat(), "C1"); 
		$this->assertEqual($combat->getAnimal1(), "A1");
		$this->assertEqual($combat->getAnimal2(), "A2"); 
		$this->assertEqual($combat->getDate(), 1362314659);
		$this->assertEqual($combat->getEstCommence(), true);
	}

	/**
	 *
	 * Test du constructeur totalement renseigné du Combat.
	 *
	 */
	function test_constructeur_total()
	{
		/* Constructeur totalement renseigné */
		$combat = new Combat("C1","A1", "A2", 1362314659, true);
		
		/* Asserts */
		$this->assertNotNull($combat);
		$this->assertEqual($combat->getIdCombat(), "C1"); 
		$this->assertEqual($combat->getAnimal1(), "A1"); 
		$this->assertEqual($combat->getAnimal2(), "A2"); 
		$this->assertEqual($combat->getDate(), 1362314659);
		$this->assertEqual($combat->getEstCommence(), true);
	}

	/**
	 *
	 * Test du toString() de Combat.
	 *
	 */
	function test_toString()
	{
		$combat = new Combat("C1","A1", "A2", 1362314659, true);
		$toStringReceived = $combat->__toString();
		
		/* Assert du toString() */
		$this->assertNotNull($combat);
		$toStringExpected = "Id Combat : C1. Animal1 : A1. Animal2 : A2. Date : 1362314659. EstCommencé : 1";
		$this->assertEqual($toStringReceived, $toStringExpected);
	}
}

?>