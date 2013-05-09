<?php
require_once('./../../lib/simpletest/autorun.php');
require_once './../metiers/Animal.php';
require_once './../mapping/DAO.php';

/**
 *
 * Classe de test de la classe Animal (métiers).
 *
 */
class AnimalTestCase extends UnitTestCase
{

	/**
	 *
	 * Test des getters et des setters de la classe Animal.
	 *
	 */
	function test_getters_setters()
	{
		/* Initialisation du joueur à tester */
		$animal = new Animal(null,null, null,null,null,null, null); //Création d'un animal vide
		$animal->setIdAnimal("1"); /* IdAnimal*/
		$animal->setNomAnimal("Lapin"); /* Nom de l'animal */
		$animal->setVie(30); /* Vie de l'animal */
		$animal->setDefense(30); /* Défense de l'animal */
		$animal->setAttaque(30); /* Attaque de l'animal */
		$animal->setNiveau(1); /* Niveau de l'animal */
		
		/* Asserts */
		$this->assertNotNull($animal);
		$this->assertEqual($animal->getIdAnimal(), "1"); /* IdAnimal */
		$this->assertEqual($animal->getNomAnimal(), "Lapin"); /* Nom de l'animal */
		$this->assertEqual($animal->getVie(), 30); /* Vie de l'animal */
		$this->assertEqual($animal->getDefense(), 30); /* Défense de l'animal */
		$this->assertEqual($animal->getAttaque(), 30); /* Attaque de l'animal */
		$this->assertEqual($animal->getNiveau(), 1); /* Niveau de l'animal */
		$animal = null;
	}

	/**
	 *
	 * Test du constructeur totalement renseigné de l'Animal.
	 *
	 */
	function test_constructeur_total()
	{
		/* Constructeur totalement renseigné */
		$animal = new Animal("1","J2", "Lapin",30,30,30,1);
		
		/* Asserts */
		$this->assertNotNull($animal);
		$this->assertEqual($animal->getIdAnimal(), "1"); /* IdAnimal */
		$this->assertEqual($animal->getProprietaire(), "J2"); /* Proprietaire */
		$this->assertEqual($animal->getNomAnimal(), "Lapin"); /* Nom de l'animal */
		$this->assertEqual($animal->getVie(), 30); /* Vie de l'animal */
		$this->assertEqual($animal->getDefense(), 30); /* Défense de l'animal */
		$this->assertEqual($animal->getAttaque(), 30); /* Attaque de l'animal */
		$this->assertEqual($animal->getNiveau(), 1); /* Niveau de l'animal */
		$animal = null;
	}

	/**
	 *
	 * Test du toString() de Animal.
	 *
	 */
	function test_toString()
	{
		$animal = new Animal("1","J2", "Lapin",30,30,30,1);
		$toStringReceived = $animal->__toString();
		
		/* Assert du toString() */
		$this->assertNotNull($animal);
		$toStringExpected = "(1) Lapin de J2. 30/30/30";
		$this->assertEqual($toStringReceived, $toStringExpected);
		$animal = null;
	}

	/**
	 *
	 * Test de la méthode getFiche() de Animal
	 *
	*/	
	function test_getFiche()
	{
		$animal = new Animal("1","J2", "Lapin",30,30,30,1);
		$ficheReceived = $animal->getFiche();
		
		/* Assert de la méthode getFiche() */
		$this->assertNotNull($animal);
		$ficheExpected = array();
		$ficheExpected['nomAnimal'] = "Lapin";
		$ficheExpected['attaqueCoef'] = 30;
		$ficheExpected['defenseCoef'] = 30;
		$ficheExpected['vieCoef'] = 30;
		$ficheExpected['niveau'] = 1;
		
		$this->assertEqual($ficheReceived, $ficheExpected);
		$animal = null;
		$ficheExpected = null;
		$ficheReceived = null;
	}
	
}

?>