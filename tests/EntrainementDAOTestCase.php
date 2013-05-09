<?php
require_once('./../../lib/simpletest/autorun.php');
require_once './../mapping/EntrainementDAO.php';
require_once './../metiers/EntrainementCollectif.php';
require_once './../metiers/EntrainementIndividuel.php';

/**
 *
 * Classe de test de la classe EntrainementDAO (package mapping)
 *
 */
class EntrainementDAOTestCase extends UnitTestCase
{
	/** DAO */
	private $_dao;
	
	/** DAO Entrainement à tester */
	private $_daoEntrainement;
	
	/**
	 * Constructeur par défaut. Instancie le DAO et le DAO Entrainement à tester.
	 */
	public function __construct()
	{
		$this->_dao = new DAO();
		$this->_daoEntrainement = new EntrainementDAO();
	}
	
	/**
	 * Test du constructeur de EntrainementDAOTestCase.
	 */
	function test_constructeur_EntrainementDAOTestCase()
	{
		//Asserts
		$this->assertNotNull($this->_dao);
		$this->assertNotNull($this->_daoEntrainement);
		$this->assertTrue($this->_dao instanceof DAO);
		$this->assertTrue($this->_daoEntrainement instanceof EntrainementDAO);
	}
	
	/**
	 *  Permet d'ajouter un entrainement de test dans la base de données et d'être sur que l'ajout ait eu lieu.
	 * @param String $idEntrainement
	 * @param String $joueur
	 * @param int $duree
	 * @param int $prix
	 * @param timestamp $date
	 * @param String $idOffre
	 */
	private function addTraining($idEntrainement, $duree, $prix, $date, $type, $idOffre, $niveauMax=0, $nbParticipantsMin=0, $annule=0)
	{
		try
		{
			//Insertion
			$requete_prepare = $this->_dao->getConnexion()->prepare("INSERT INTO t_entrainement (idEntrainement, duree, prix, dateDebut, type, niveauMax, nbParticipantsMin, annule, OffreEntrainement_idOffre) VALUES (:id, :duree, :prix, :date, :type, :niveauMax, :nbParticipantsMin, :annule, :offre)");
			$requete_prepare->bindParam(':id', $idEntrainement, PDO::PARAM_STR);
			$requete_prepare->bindParam(':duree', $duree, PDO::PARAM_INT);
			$requete_prepare->bindParam(':prix', $prix, PDO::PARAM_INT);
			$requete_prepare->bindParam(':date', $date, PDO::PARAM_INT);
			$requete_prepare->bindParam(':type', $type, PDO::PARAM_INT);
			$requete_prepare->bindParam(':niveauMax', $niveauMax, PDO::PARAM_INT);
			$requete_prepare->bindParam(':nbParticipantsMin', $nbParticipantsMin, PDO::PARAM_INT);
			$requete_prepare->bindParam(':annule', $annule, PDO::PARAM_INT);
			$requete_prepare->bindParam(':offre', $idOffre, PDO::PARAM_STR);
			$bool = $requete_prepare->execute();
	
			//Select pour vérification
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT * FROM t_entrainement WHERE idEntrainement = :idEntrainement");
			$requete_prepare->bindParam(':idEntrainement', $idEntrainement, PDO::PARAM_STR);
			$requete_prepare->execute();
			$donnees = $requete_prepare->fetch(PDO::FETCH_OBJ);
			$dureeReceived = $donnees->duree;
			$prixReceived = $donnees->prix;
			$typeReceived = $donnees->type;
			$niveauMaxReceived = $donnees->niveauMax;
			$nbParticipantsMinReceived = $donnees->nbParticipantsMin;
			$annuleReceived = $donnees->annule;
			$dateSouscriptionReceived = $donnees->dateDebut;
			$offreReceived = $donnees->OffreEntrainement_idOffre;
			
			//Asserts
			$this->assertTrue($bool);
			$this->assertEqual($duree, $dureeReceived);
			$this->assertEqual($prix, $prixReceived);
			$this->assertEqual($type, $typeReceived);
			$this->assertEqual($niveauMax, $niveauMaxReceived);
			$this->assertEqual($nbParticipantsMin, $nbParticipantsMinReceived);
			$this->assertEqual($annule, $annuleReceived);
			$this->assertEqual($date, $dateSouscriptionReceived);
			$this->assertEqual($idOffre, $offreReceived);
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : '.$e->getTraceAsString().'<br/>';
		}
	}
	
	/**
	 * Permet de supprimer un entrainement de test de la base de données et d'être sur que la suppression ait eu lieu.
	 * @param String $idTrain
	 */
	private function deleteTraining($idTrain)
	{
		try
		{
			//Suppression
			$requete_prepare = $this->_dao->getConnexion()->prepare("DELETE FROM t_entrainement WHERE idEntrainement = :idEntrainement");
			$requete_prepare->bindParam(':idEntrainement', $idTrain, PDO::PARAM_STR);
			$bool = $requete_prepare->execute();
	
			//Select pour vérification
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT COUNT(*) as COUNT FROM t_entrainement WHERE idEntrainement = :idEntrainement");
			$requete_prepare->bindParam(':idEntrainement', $idTrain, PDO::PARAM_STR);
			$requete_prepare->execute();
			$count = $requete_prepare->fetch(PDO::FETCH_OBJ)->COUNT;
	
			//Asserts
			$this->assertTrue($bool);
			$this->assertEqual($count, 0);
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : '.$e->getTraceAsString().'<br/>';
		}
	}
	
	
	/**
	 * Permet d'ajouter un animal de test dans la base de données et d'être sur que l'ajout ait eu lieu.
	 * @param String $idAnimal
	 * @param String $proprietaire
	 * @param String $nomAnimal
	 * @param int $vie
	 * @param int $defense
	 * @param int $attaque
	 */
	private function addAnimal($idAnimal, $proprietaire, $nomAnimal, $vie, $defense, $attaque, $niveau)
	{
		try
		{
			//Insertion
			$requete_prepare = $this->_dao->getConnexion()->prepare("INSERT INTO t_animal (idAnimal, nomAnimal, vie, defense, attaque, niveau) VALUES (:id, :nom, :vie, :def, :att, :niveau)");
			$requete_prepare->bindParam(':id', $idAnimal, PDO::PARAM_STR);
			$requete_prepare->bindParam(':nom', $nomAnimal, PDO::PARAM_STR);
			$requete_prepare->bindParam(':vie', $vie, PDO::PARAM_INT);
			$requete_prepare->bindParam(':def', $defense, PDO::PARAM_INT);
			$requete_prepare->bindParam(':att', $attaque, PDO::PARAM_INT);
			$requete_prepare->bindParam(':niveau', $niveau, PDO::PARAM_INT);
			$boolInsert1 = $requete_prepare->execute();
	
			$requete_prepare = $this->_dao->getConnexion()->prepare("INSERT INTO t_joueur_animal (Joueur_idFacebook, Animal_idAnimal) VALUES (:idJ, :idA)");
			$requete_prepare->bindParam(':idJ', $proprietaire, PDO::PARAM_STR);
			$requete_prepare->bindParam(':idA', $idAnimal, PDO::PARAM_STR);
			$boolInsert2 = $requete_prepare->execute();
	
			//Select pour vérification
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT * FROM t_joueur_animal WHERE Animal_idAnimal = :idAnimal AND Joueur_idFacebook = :idFacebook");
			$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
			$requete_prepare->bindParam(':idFacebook', $proprietaire, PDO::PARAM_STR);
			$requete_prepare->execute();
			$donnees = $requete_prepare->fetch(PDO::FETCH_OBJ);
			$proprietaireReceived = $donnees->Joueur_idFacebook;
	
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT * FROM t_animal WHERE idAnimal = :idAnimal");
			$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
			$requete_prepare->execute();
			$donnees = $requete_prepare->fetch(PDO::FETCH_OBJ);
			$idAnimalReceived = $donnees->idAnimal;
			$nomReceived = $donnees->nomAnimal;
			$vieReceived = $donnees->vie;
			$defenseReceived = $donnees->defense;
			$attaqueReceived = $donnees->attaque;
			$niveauReceived = $donnees->niveau;
	
			//Asserts
			$this->assertTrue($boolInsert1);
			$this->assertTrue($boolInsert2);
			$this->assertEqual($idAnimal, $idAnimalReceived);
			$this->assertEqual($proprietaire, $proprietaireReceived);
			$this->assertEqual($nomAnimal, $nomReceived);
			$this->assertEqual($vie, $vieReceived);
			$this->assertEqual($defense, $defenseReceived);
			$this->assertEqual($attaque, $attaqueReceived);
			$this->assertEqual($niveau, $niveauReceived);
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : '.$e->getTraceAsString().'<br/>';
		}
	}
	
	/**
	 * Permet de supprimer un animal de test de la base de données et d'être sur que la suppression ait eu lieu.
	 * @param String $idAnimal
	 */
	private function deleteAnimal($idAnimal)
	{
		try
		{
			//Suppression
			$requete_prepare = $this->_dao->getConnexion()->prepare("DELETE FROM t_animal WHERE idAnimal = :idAnimal");
			$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
			$boolDelete1 = $requete_prepare->execute();
				
			$requete_prepare = $this->_dao->getConnexion()->prepare("DELETE FROM t_joueur_animal WHERE Animal_idAnimal = :idAnimal");
			$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
			$boolDelete2 = $requete_prepare->execute();
				
			//Select pour vérification
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT COUNT(*) as COUNT1 FROM t_animal WHERE idAnimal = :id");
			$requete_prepare->bindParam(':id', $idAnimal, PDO::PARAM_STR);
			$requete_prepare->execute();
			$count1 = $requete_prepare->fetch(PDO::FETCH_OBJ)->COUNT1;
				
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT COUNT(*) as COUNT2 FROM t_joueur_animal WHERE Animal_idAnimal = :id");
			$requete_prepare->bindParam(':id', $idAnimal, PDO::PARAM_STR);
			$requete_prepare->execute();
			$count2 = $requete_prepare->fetch(PDO::FETCH_OBJ)->COUNT2;
				
			//Asserts
			$this->assertTrue($boolDelete1);
			$this->assertTrue($boolDelete2);
			$this->assertEqual($count1, 0);
			$this->assertEqual($count2, 0);
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : '.$e->getTraceAsString().'<br/>';
		}
	}
	
	
	// -------------------------------- TESTS DE LA CLASSE ENTRAINEMENTDAO ---------------------------------------

	/**
	 * Test de la méthode getEntrainementsDispo() de EntrainementDAO
	 */
	function test_getEntrainementsDispo()
	{
		//On ajoute 2 entrainements en base
		$this->addTraining("ETEST001", 360, 100, time()+120, 'individuel', "OTEST001"); //A venir dans 2mn
		$this->addTraining("ETEST002", 360, 200, time()-60, 'individuel', "OTEST002"); //En cours
		
		//Select sur les résultats à obtenir
		$currentTime = time();
		$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT * FROM t_entrainement WHERE dateDebut > :currentTime");
		$requete_prepare->bindParam(':currentTime', $currentTime, PDO::PARAM_INT);
		$requete_prepare->execute();
		
		$entrainementsDispoExpected = array();
		$donnees = $requete_prepare->fetchAll(PDO::FETCH_OBJ);
		foreach ($donnees as $value)
		{
			$entrainement = new EntrainementCollectif($value->idEntrainement,
										     $value->duree, 
											 $value->prix,
											 $value->dateDebut,
											 new OffreEntrainement($value->OffreEntrainement_idOffre, null, null, null, null),
										     null);
			array_push($entrainementsDispoExpected, $entrainement);
		}
		
		//Exécution de la méthode à tester
		$entrainementsDispoReceived = $this->_daoEntrainement->getEntrainementsDispo();
		
		//Asserts
		$this->assertNotNull($entrainementsDispoExpected);
		$this->assertNotNull($entrainementsDispoReceived);
		$this->assertEqual($entrainementsDispoExpected, $entrainementsDispoReceived);
		
		//Suppression des enregistrements de test créés
		$this->deleteTraining("ETEST001");
		$this->deleteTraining("ETEST002");
	}
	
	/**
	 * Test de la méthode ajouterEntrainement() de EntrainementDAO
	 */
	function test_ajouterEntrainement()
	{
		try
		{
			//Exécution de la méthode
			$dureeExpected = 360;
			$prixExpected = 1500;
			$dateDebutExpected = 1356550776;
			$typeExpected = "individuel";
			$idOffreExpected = "O1";
			$niveauMaxExpected = 3;
			$nbParticipantsMinExpected = 10;
			$nbParticipantsMaxExpected = 10;
			$idEntrainementExpected = $this->_daoEntrainement->ajouterEntrainement($dureeExpected, $prixExpected, $dateDebutExpected, $typeExpected, $niveauMaxExpected, $nbParticipantsMinExpected, $nbParticipantsMaxExpected, $idOffreExpected);
			
			//Select pour vérification
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT * FROM t_entrainement WHERE idEntrainement = :idEntrainement");
			$requete_prepare->bindParam(':idEntrainement', $idEntrainementExpected, PDO::PARAM_STR);
			$bool = $requete_prepare->execute();
			$donnees = $requete_prepare->fetch(PDO::FETCH_OBJ);
			$idEntrainementReceived = $donnees->idEntrainement;
			$dureeReceived = $donnees->duree;
			$prixReceived = $donnees->prix;
			$dateDebutReceived = $donnees->dateDebut;
			$typeReceived = $donnees->type;
			$niveauMaxReceived = $donnees->niveauMax;
			$nbParticipantsMinReceived = $donnees->nbParticipantsMin;
			$nbParticipantsMaxReceived = $donnees->nbParticipantsMax;
			$annuleReceived = $donnees->annule;
			$typeReceived = $donnees->type;
			$idOffreReceived = $donnees->OffreEntrainement_idOffre;
			
			//Asserts
			$this->assertEqual($idEntrainementExpected, $idEntrainementReceived);
			$this->assertEqual($dureeExpected, $dureeReceived);
			$this->assertEqual($prixExpected, $prixReceived);
			$this->assertEqual($dateDebutExpected, $dateDebutReceived);
			$this->assertEqual($typeExpected, $typeReceived);
			$this->assertEqual($niveauMaxExpected, $niveauMaxReceived);
			$this->assertEqual($nbParticipantsMinExpected, $nbParticipantsMinReceived);
			$this->assertEqual($nbParticipantsMaxExpected, $nbParticipantsMaxReceived);
			$this->assertEqual(0, $annuleReceived);
			$this->assertEqual($idOffreExpected, $idOffreReceived);
			
			//Suppression de l'entrainement inséré
			$this->deleteTraining($idEntrainementExpected);
			
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : <pre>'.$e->getTraceAsString().'</pre><br/>';
		}
	}
	
	
	/**
	 * Test de la méthode getOffreAleatoireByNiveau() de EntrainementDAO
	 */
	function test_getOffreAleatoireByNiveau()
	{
		try
		{
			//Exécution de la méthode avec un niveau compris entre 0 et 10
			$offreReceived = $this->_daoEntrainement->getOffreAleatoireByNiveau(1);
				
			//Asserts
			$this->assertNotNull($offreReceived);
			$this->assertNotNull($offreReceived->getIdOffre());
			$this->assertTrue($offreReceived->getAttaqueOffre() >= 0 && $offreReceived->getAttaqueOffre() <= 6);
			$this->assertTrue($offreReceived->getDefenseOffre() >= 0 && $offreReceived->getDefenseOffre() <= 6);
			$this->assertTrue($offreReceived->getVieOffre() >= 0 && $offreReceived->getVieOffre() <= 6);
			$this->assertTrue(in_array($offreReceived->getLevelUp(), array(0,1)));
			
			$sommePoints = $offreReceived->getSommePoints();
			$this->assertTrue($sommePoints > 0 && $sommePoints <= 6);

			
			//Exécution de la méthode avec un niveau compris entre 11 et 20
			$offreReceived = $this->_daoEntrainement->getOffreAleatoireByNiveau(15);
			
			//Asserts
			$this->assertNotNull($offreReceived);
			$this->assertNotNull($offreReceived->getIdOffre());
			$this->assertTrue($offreReceived->getAttaqueOffre() >= 0 && $offreReceived->getAttaqueOffre() <= 4);
			$this->assertTrue($offreReceived->getDefenseOffre() >= 0 && $offreReceived->getDefenseOffre() <= 4);
			$this->assertTrue($offreReceived->getVieOffre() >= 0 && $offreReceived->getVieOffre() <= 4);
			$this->assertTrue(in_array($offreReceived->getLevelUp(), array(0,1)));
				
			$sommePoints = $offreReceived->getSommePoints();
			$this->assertTrue($sommePoints > 0 && $sommePoints <= 4);
				
			
			//Exécution de la méthode avec un niveau compris entre 21 et 30
			$offreReceived = $this->_daoEntrainement->getOffreAleatoireByNiveau(23);
				
			//Asserts
			$this->assertNotNull($offreReceived);
			$this->assertNotNull($offreReceived->getIdOffre());
			$this->assertTrue($offreReceived->getAttaqueOffre() >= 0 && $offreReceived->getAttaqueOffre() <= 2);
			$this->assertTrue($offreReceived->getDefenseOffre() >= 0 && $offreReceived->getDefenseOffre() <= 2);
			$this->assertTrue($offreReceived->getVieOffre() >= 0 && $offreReceived->getVieOffre() <= 2);
			$this->assertTrue(in_array($offreReceived->getLevelUp(), array(0,1)));
			
			$sommePoints = $offreReceived->getSommePoints();
			$this->assertTrue($sommePoints > 0 && $sommePoints <= 2);
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : <pre>'.$e->getTraceAsString().'</pre><br/>';
		}
	}
	
	
	/**
	 * Test de la méthode getEntrainementsFinisEnAttente() de EntrainementDAO
	 */
	function test_getEntrainementsFinisEnAttente()
	{
		try
		{
			//Récupération du nombre d'entrainements concernés
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT COUNT(*) as COUNT
					FROM t_entrainement, t_entrainementoffre, t_entrainement_animal
					WHERE t_entrainement.OffreEntrainement_idOffre = t_entrainementoffre.idOffre
					AND t_entrainement.idEntrainement = t_entrainement_animal.Entrainement_idEntrainement
					AND dateDebut + duree < :currentTime
					AND valide = 0");
			$currentTime = time();
			$requete_prepare->bindParam(':currentTime', $currentTime, PDO::PARAM_INT);
			$requete_prepare->execute();
			$count = $donnees = $requete_prepare->fetch(PDO::FETCH_OBJ)->COUNT;
			
			//Création des entrainements de test
			$time_100 = time() - 100;
			$time_200 = time() - 200;
			$this->addTraining("ETEST0010", 10, 1000,  $time_100,'individuel', "O1");
			$this->addTraining("ETEST0011", 10, 1000, $time_200,'collectif',  "O2");
				
			//Inscription aux entrainements
			$dateSouscription = time() - 400;
			$requete_prepare = $this->_dao->getConnexion()->prepare("INSERT INTO t_entrainement_animal (Entrainement_idEntrainement, Animal_idAnimal, dateSouscription, valide)
					VALUES ('ETEST0010', 'ATEST0010', :dateSouscription, 0),
						   ('ETEST0011', 'ATEST0011', :dateSouscription, 0),
					       ('ETEST0011', 'ATEST0012', :dateSouscription, 0)");
			$requete_prepare->bindParam(':dateSouscription', $dateSouscription, PDO::PARAM_INT);
			$bool = $requete_prepare->execute();
			$this->assertTrue($bool);
				
			//Récupération du tableau des résultats attendu
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT idEntrainement, duree, prix, dateDebut, type, idOffre, attaque_offre, defense_offre, vie_offre, levelUp
					FROM t_entrainement, t_entrainementoffre, t_entrainement_animal
					WHERE t_entrainement.OffreEntrainement_idOffre = t_entrainementoffre.idOffre
					AND t_entrainement.idEntrainement = t_entrainement_animal.Entrainement_idEntrainement
					AND dateDebut + duree < :currentTime
					AND valide = 0");
				
			$requete_prepare->bindParam(':currentTime', $currentTime, PDO::PARAM_INT);
			$requete_prepare->execute();
				
			$entrainementsExpected = array();
			while($donnees = $requete_prepare->fetch(PDO::FETCH_ASSOC))
			{
				$idEntrainement = $donnees[ 'idEntrainement' ];
				$duree = $donnees[ 'duree' ];
				$prix = $donnees[ 'prix' ];
				$dateDebut = $donnees[ 'dateDebut' ];
				$type = $donnees[ 'type' ];
				$idOffre = $donnees[ 'idOffre' ];
				$attaque_offre = $donnees[ 'attaque_offre' ];
				$defense_offre = $donnees[ 'defense_offre' ];
				$vie_offre = $donnees[ 'vie_offre' ];
				$levelUp = $donnees[ 'levelUp' ];
					
				$offre = new OffreEntrainement($idOffre, $attaque_offre, $defense_offre, $vie_offre, $levelUp);
					
				if($type == 'individuel')
					$entrainement = new EntrainementIndividuel($idEntrainement, $duree, $prix, $dateDebut, $offre, null);
				else $entrainement = new EntrainementCollectif($idEntrainement, $duree, $prix, $dateDebut, $offre, array());
					
				array_push($entrainementsExpected, $entrainement);
			}			
			
			//On ajoute nos entrainements de tests attendus
			$entrainement1 = new EntrainementIndividuel("ETEST0010", 10, 1000, $time_100, new OffreEntrainement("O1", 3, 1, 1, 0), null);
			array_push($entrainementsExpected, $entrainement1);
			$entrainement2 = new EntrainementCollectif("ETEST0011", 10, 1000, $time_200, new OffreEntrainement("O2", 0, 2, 4, 0), array());
			array_push($entrainementsExpected, $entrainement2);
			array_push($entrainementsExpected, $entrainement2);
				
			$entrainementsExpected = array_unique($entrainementsExpected);
			
			//Asserts
			$tailleExpected = $count + 2;
			$this->assertEqual(count($entrainementsExpected), $tailleExpected);
			$this->assertTrue(in_array($entrainement1, $entrainementsExpected));
			$this->assertTrue(in_array($entrainement2, $entrainementsExpected));
			
			//Suppressions
			$this->deleteTraining("ETEST0010");
			$this->deleteTraining("ETEST0011");
			
			$requete_prepare = $this->_dao->getConnexion()->prepare("DELETE FROM t_entrainement_animal WHERE Entrainement_idEntrainement IN ('ETEST0010', 'ETEST0011')");
			$bool = $requete_prepare->execute();
			$this->assertTrue($bool);

		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : <pre>'.$e->getTraceAsString().'</pre><br/>';
		}
	}
		
	/**
	 * Test de la méthode getAnimalsByEntrainement() de EntrainementDAO
	 */
	function test_getAnimalsByEntrainement()
	{
		try
		{
			//Création d'entrainement et animaux de test
			$idEntrainement = 'ETEST001';
			$duree = 60;
			$prix = 500;
			$date = time() - 300;
			$type = 'collectif';
			$idOffre = 'OTEST001';
			$this->addTraining($idEntrainement, $duree, $prix, $date, $type, $idOffre);
			
			$idAnimal1 = 'ATEST001';
			$nomAnimal1 = 'Lapin test 1';
			$vie1 = 10;
			$defense1 = 20;
			$attaque1 = 5;
			$niveau1 = 1;
			$this->addAnimal($idAnimal1, '', $nomAnimal1, $vie1, $defense1, $attaque1, $niveau1);
			$animal1 = new Animal($idAnimal1, null, $nomAnimal1, $vie1, $defense1, $attaque1, $niveau1);
			
			$idAnimal2 = 'ATEST002';
			$nomAnimal2 = 'Lapin test 2';
			$vie2 = 30;
			$defense2 = 30;
			$attaque2 = 10;
			$niveau2 = 5;
			$this->addAnimal($idAnimal2, '', $nomAnimal2, $vie2, $defense2, $attaque2, $niveau2);
			$animal2 = new Animal($idAnimal2, null, $nomAnimal2, $vie2, $defense2, $attaque2, $niveau2);
			
			//Inscription des animaux à l'entrainement
			$requete_prepare = $this->_dao->getConnexion()->prepare("INSERT INTO t_entrainement_animal (Entrainement_idEntrainement, Animal_idAnimal, dateSouscription, valide) 
																	 VALUES (:idEntrainement, :idAnimal1, :dateSouscription, 0),
																			(:idEntrainement, :idAnimal2, :dateSouscription, 0)");
			$requete_prepare->bindParam(':idEntrainement', $idEntrainement, PDO::PARAM_STR);
			$requete_prepare->bindParam(':idAnimal1', $idAnimal1, PDO::PARAM_STR);
			$requete_prepare->bindParam(':idAnimal2', $idAnimal2, PDO::PARAM_STR);
			$requete_prepare->bindParam(':dateSouscription', $date, PDO::PARAM_INT);
			$bool = $requete_prepare->execute();
			$this->assertTrue($bool);
			
			//Exécution de la méthode à tester
			$animauxReceived = $this->_daoEntrainement->getAnimalsByEntrainement($idEntrainement);
			
			//Asserts
			$this->assertNotNull($animauxReceived);
			$this->assertTrue(in_array($animal1, $animauxReceived));
			$this->assertTrue(in_array($animal2, $animauxReceived));
			$this->assertTrue(count($animauxReceived) >= 2);
			
			//Suppressions
			$this->deleteAnimal($idAnimal1);
			$this->deleteAnimal($idAnimal2);
			$this->deleteTraining($idEntrainement);
			
			$requete_prepare = $this->_dao->getConnexion()->prepare("DELETE FROM t_entrainement_animal WHERE Entrainement_idEntrainement = :idEntrainement");
			$requete_prepare->bindParam(':idEntrainement', $idEntrainement, PDO::PARAM_STR);
			$bool = $requete_prepare->execute();
			$this->assertTrue($bool);
			
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : <pre>'.$e->getTraceAsString().'</pre><br/>';
		}
	}
	
	
	/**
	 * Test de la méthode getCollectiveTrainingsOfToday() de EntrainementDAO
	 */
	function test_getCollectiveTrainingsOfToday()
	{
		try
		{
			//On compte le nombre d'entrainements collectifs nous interessant en base (d'aujourd'hui)
			$ajdMin = mktime(0, 0, 0, date("n"), date("j"), date("Y")); //Aujourd'hui à 00h00m00s
			$ajdMax = mktime(23, 59, 59, date("n"), date("j"), date("Y")); //Aujourd'hui à 23h59m59s
			
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT COUNT(*) as COUNT
																FROM t_entrainement
																WHERE type = 'collectif'
																AND dateDebut BETWEEN :ajdMin AND :ajdMax");
			$requete_prepare->bindParam(':ajdMin', $ajdMin, PDO::PARAM_STR);
			$requete_prepare->bindParam(':ajdMax', $ajdMax, PDO::PARAM_STR);
			$requete_prepare->execute();
			$countBefore = $requete_prepare->fetch(PDO::FETCH_OBJ)->COUNT;
			
			//On ajoute 2 entrainements collectifs de test en base
			$idEntrainement1 = 'ETEST0012';
			$duree1 = 3000;
			$prix1 = 500;
			$date1 = time();
			$type = 'collectif';
			$idOffre1 = 'O1';
			$entrainement1 = new EntrainementCollectif($idEntrainement1, $duree1, $prix1, $date1, new OffreEntrainement('O1', 3, 1, 1, 0), array());
			$this->addTraining($idEntrainement1, $duree1, $prix1, $date1, $type, $idOffre1);
			
			$idEntrainement2 = 'ETEST0021';
			$duree2 = 6000;
			$prix2 = 700;
			$date2 = time();
			$type = 'collectif';
			$idOffre2 = 'O2';
			$entrainement2 = new EntrainementCollectif($idEntrainement2, $duree2, $prix2, $date2, new OffreEntrainement('O2', 0, 2, 4, 0), array());
			$this->addTraining($idEntrainement2, $duree2, $prix2, $date2, $type, $idOffre2);
			
			//On exécute la méthode à tester
			$entrainementReceived = $this->_daoEntrainement->getCollectiveTrainingsOfToday();
			
			//Asserts
			$this->assertNotNull($entrainementReceived);
			$this->assertEqual(count($entrainementReceived), $countBefore + 2); //Insertion de 2 entrainements test en plus
			$this->assertTrue(in_array($entrainement1, $entrainementReceived));
			$this->assertTrue(in_array($entrainement2, $entrainementReceived));
			
			//Suppressions
			$this->deleteTraining($idEntrainement1);
			$this->deleteTraining($idEntrainement2);
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : <pre>'.$e->getTraceAsString().'</pre><br/>';
		}
	}
	
	
		
	/**
	 * Test de la méthode getTrainingById() de EntrainementDAO
	 */
	function test_getTrainingById()
	{
		try
		{
			//On créé un entrainement individuel de test
			$idEntrainement1 = "ETEST003";
			$duree1 = 300;
			$prix1 = 560;
			$dateDebut1 = time() - 100;
			$offre1 = new OffreEntrainement("O4", 0, 5, 0, 0);
			$entrainementInd = new EntrainementIndividuel($idEntrainement1, $duree1, $prix1, $dateDebut1, $offre1, array());
			//On le persiste
			$this->addTraining($idEntrainement1, $duree1, $prix1, $dateDebut1, 'individuel', $offre1->getIdOffre());
			
			//On exécute la méthode à tester
			$entrainementIndReceived = $this->_daoEntrainement->getTrainingById($idEntrainement1);
			
			//Asserts
			$this->assertNotNull($entrainementIndReceived);
			$this->assertEqual($entrainementIndReceived, $entrainementInd);
			
			//Suppressions
			$this->deleteTraining($idEntrainement1);
			
			//---------------------------------------------------------------------
			
			//On créé un entrainement collectif de test
			$idEntrainement2 = "ETEST003";
			$duree2 = 888;
			$prix2 = 1200;
			$dateDebut2 = time();
			$niveauMax2 = 10;
			$nbParticipantsMin2 = 2;
			$offre2 = new OffreEntrainement("O5", 2, 2, 2, 0);
			$entrainementCo = new EntrainementCollectif($idEntrainement2, $duree2, $prix2, $dateDebut2, $offre2, array(), $niveauMax2, $nbParticipantsMin2, 0);
			//On le persiste
			$this->addTraining($idEntrainement2, $duree2, $prix2, $dateDebut2, 'collectif', $offre2->getIdOffre(), $niveauMax2, $nbParticipantsMin2, 0);
				
			//On exécute la méthode à tester
			$entrainementCoReceived = $this->_daoEntrainement->getTrainingById($idEntrainement2);
				
			//Asserts
			$this->assertNotNull($entrainementCoReceived);
			$this->assertEqual($entrainementCoReceived, $entrainementCo);
				
			//Suppressions
			$this->deleteTraining($idEntrainement2);
		
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : <pre>'.$e->getTraceAsString().'</pre><br/>';
		}
	}
	
	
	
	/**
	 * Test de la méthode deleteTrainingById() de EntrainementDAO
	 */
	function test_deleteTrainingById()
	{
		try
		{
			//On créé un entrainement individuel de test
			$idEntrainement = "ETEST003";
			$duree = 300;
			$prix = 560;
			$dateDebut = time() - 1000;
			$offre = new OffreEntrainement("O4", 0, 5, 0, 0);
			$this->addTraining($idEntrainement, $duree, $prix, $dateDebut, 'individuel', $offre->getIdOffre());
			
			$idAnimal = 'ATEST009';
			
			//On créé une inscription d'un animal de test à cet entrainement
			$requete_prepare = $this->_dao->getConnexion()->prepare("INSERT INTO t_entrainement_animal (Entrainement_idEntrainement, Animal_idAnimal, dateSouscription, valide)
																	 VALUES (:idEntrainement, :idAnimal, :dateSouscription, 0)");
			$requete_prepare->bindParam(':idEntrainement', $idEntrainement, PDO::PARAM_STR);
			$requete_prepare->bindParam(':idAnimal', $idAnimal, PDO::PARAM_STR);
			$requete_prepare->bindParam(':dateSouscription', $dateDebut, PDO::PARAM_INT);
			$bool = $requete_prepare->execute();
			$this->assertTrue($bool);
			
				
			//On exécute la méthode à tester
			$boolReceived = $this->_daoEntrainement->deleteTrainingById($idEntrainement);
				
			//Vérifications
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT COUNT(*) as COUNT
					FROM t_entrainement
					WHERE idEntrainement = :idEntrainement");
			$requete_prepare->bindParam(':idEntrainement', $idEntrainement, PDO::PARAM_STR);
			$donnees = $requete_prepare->fetch(PDO::FETCH_ASSOC);
			$countReceived1 = $donnees['COUNT'];
			
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT COUNT(*) as COUNT
					FROM t_entrainement_animal
					WHERE Entrainement_idEntrainement = :idEntrainement");
			$requete_prepare->bindParam(':idEntrainement', $idEntrainement, PDO::PARAM_STR);
			$donnees = $requete_prepare->fetch(PDO::FETCH_ASSOC);
			$countReceived2 = $donnees['COUNT'];
			
			//Asserts
			$this->assertTrue($boolReceived);
			$this->assertEqual($countReceived1, 0);
			$this->assertEqual($countReceived2, 0);
				
			//Suppressions
			$this->deleteTraining($idEntrainement);
			$requete_prepare = $this->_dao->getConnexion()->prepare("DELETE FROM t_entrainement_animal WHERE Entrainement_idEntrainement = :idEntrainement");
			$requete_prepare->bindParam(':idEntrainement', $idEntrainement, PDO::PARAM_STR);
			$bool = $requete_prepare->execute();
			$this->assertTrue($bool);
				
			
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : <pre>'.$e->getTraceAsString().'</pre><br/>';
		}
	}
	
	
	/**
	 * Test de la méthode getAllCollectiveTrainings() de EntrainementDAO
	 */
	function test_getAllCollectiveTrainings()
	{
		try
		{
			//On ajoute 2 entrainements collectifs de test en base
			$idEntrainement1 = 'ETEST0012';
			$duree1 = 3000;
			$prix1 = 500;
			$date1 = time();
			$type = 'collectif';
			$idOffre1 = 'O1';
			$entrainement1 = new EntrainementCollectif($idEntrainement1, $duree1, $prix1, $date1, new OffreEntrainement('O1', 3, 1, 1, 0), array());
			$this->addTraining($idEntrainement1, $duree1, $prix1, $date1, $type, $idOffre1);
				
			$idEntrainement2 = 'ETEST0021';
			$duree2 = 6000;
			$prix2 = 700;
			$date2 = time();
			$type = 'collectif';
			$idOffre2 = 'O2';
			$entrainement2 = new EntrainementCollectif($idEntrainement2, $duree2, $prix2, $date2, new OffreEntrainement('O2', 0, 2, 4, 0), array());
			$this->addTraining($idEntrainement2, $duree2, $prix2, $date2, $type, $idOffre2);
				
			
			//On construit le tableau attendu
			$requete_prepare = $this->_dao->getConnexion()->prepare("SELECT *
					FROM t_entrainement, t_entrainementoffre
					WHERE t_entrainement.OffreEntrainement_idOffre = t_entrainementoffre.idOffre
					AND type = 'collectif'
					ORDER BY dateDebut ASC");
			
			$requete_prepare->execute();
			
			$entrainementsExpected = array();
			while($donnees = $requete_prepare->fetch(PDO::FETCH_ASSOC))
			{
				$idEntrainement = $donnees[ 'idEntrainement' ];
				$duree = $donnees[ 'duree' ];
				$prix = $donnees[ 'prix' ];
				$dateDebut = $donnees[ 'dateDebut' ];
				$type = $donnees[ 'type' ];
				$niveauMax = $donnees[ 'niveauMax' ];
				$nbParticipantsMax = $donnees[ 'nbParticipantsMin' ];
				$annule = $donnees[ 'annule' ];
				$idOffre = $donnees[ 'idOffre' ];
				$attaque_offre = $donnees[ 'attaque_offre' ];
				$defense_offre = $donnees[ 'defense_offre' ];
				$vie_offre = $donnees[ 'vie_offre' ];
				$levelUp = $donnees[ 'levelUp' ];
			
				$offre = new OffreEntrainement($idOffre, $attaque_offre, $defense_offre, $vie_offre, $levelUp);
			
				$entrainement = new EntrainementCollectif($idEntrainement, $duree, $prix, $dateDebut, $offre, array(), $niveauMax, $nbParticipantsMax, $annule);
			
				array_push($entrainementsExpected, $entrainement);
			}
			
			//On exécute la méthode à tester
			$entrainementsReceived = $this->_daoEntrainement->getAllCollectiveTrainings();
				
			//Asserts
			$this->assertNotNull($entrainementsReceived);
			$this->assertNotNull($entrainementsExpected);
			$this->assertEqual($entrainementsReceived, $entrainementsExpected);
				
			//Suppressions
			$this->deleteTraining($idEntrainement1);
			$this->deleteTraining($idEntrainement2);
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : <pre>'.$e->getTraceAsString().'</pre><br/>';
		}
	}
	
	/**
	 * Test de la méthode getAnimalsEnrolledByTraining() de EntrainementDAO
	 */
	function test_getAnimalsEnrolledByTraining()
	{
		try
		{
			//On créé un entrainement de test
			$idEntrainement = 'ETEST0012';
			$duree = 3000;
			$prix = 500;
			$date = time();
			$type = 'collectif';
			$idOffre = 'O1';
			$entrainement = new EntrainementCollectif($idEntrainement, $duree, $prix, $date, new OffreEntrainement('O1', 3, 1, 1, 0), array());
			$this->addTraining($idEntrainement, $duree, $prix, $date, $type, $idOffre);
	
			//On créé 2 animaux de test
			$idAnimal1 = "ATEST0010";
			$nomAnimal1 = "Lapin test 1";
			$vie1 = 10;
			$defense1 = 15;
			$attaque1 = 20;
			$niveau1 = 3;
			$proprietaire1 = "JTEST0010";
			$animal1 = new Animal($idAnimal1, $proprietaire1, $nomAnimal1, $vie1, $defense1, $attaque1, $niveau1);
			$this->addAnimal($idAnimal1, $proprietaire1, $nomAnimal1, $vie1, $defense1, $attaque1, $niveau1);
			
			$idAnimal2 = "ATEST0012";
			$nomAnimal2 = "Lapin test 2";
			$vie2 = 12;
			$defense2 = 25;
			$attaque2 = 22;
			$niveau2 = 5;
			$proprietaire2 = "JTEST0012";
			$animal2 = new Animal($idAnimal2, $proprietaire2, $nomAnimal2, $vie2, $defense2, $attaque2, $niveau2);
			$this->addAnimal($idAnimal2, $proprietaire2, $nomAnimal2, $vie2, $defense2, $attaque2, $niveau2);
			
			//Et on inscrit les 2 animaux à cet entrainement
			$requete_prepare = $this->_dao->getConnexion()->prepare("INSERT INTO t_entrainement_animal (Entrainement_idEntrainement, Animal_idAnimal, dateSouscription, valide)
																	VALUES (:idEntrainement, :idAnimal1, :dateSouscription, 0),
																	       (:idEntrainement, :idAnimal2, :dateSouscription, 0)");
			$requete_prepare->bindParam(':idEntrainement', $idEntrainement, PDO::PARAM_STR);
			$requete_prepare->bindParam(':idAnimal1', $idAnimal1, PDO::PARAM_STR);
			$requete_prepare->bindParam(':idAnimal2', $idAnimal2, PDO::PARAM_STR);
			$requete_prepare->bindParam(':dateSouscription', $date, PDO::PARAM_INT);
			$bool = $requete_prepare->execute();
			$this->assertTrue($bool);
			
			//On exécute la méthode à tester
			$animalsReceived = $this->_daoEntrainement->getAnimalsEnrolledByTraining($idEntrainement);
			
			//Asserts
			$this->assertNotNull($animalsReceived);
			$this->assertEqual(count($animalsReceived), 2);
			$this->assertTrue(in_array($animal1, $animalsReceived));
			$this->assertTrue(in_array($animal2, $animalsReceived));
	
			//Suppressions
			$this->deleteTraining($idEntrainement);
			$this->deleteAnimal($idAnimal1);
			$this->deleteAnimal($idAnimal2);
			
			$requete_prepare = $this->_dao->getConnexion()->prepare("DELETE FROM t_entrainement_animal 
																	 WHERE Entrainement_idEntrainement = :idEntrainement");
			$requete_prepare->bindParam(':idEntrainement', $idEntrainement, PDO::PARAM_STR);
			$bool = $requete_prepare->execute();
			$this->assertTrue($bool);
		}
		catch(Exception $e)
		{
			echo 'Exception reçue : '.$e->getMessage().'<br/>';
			echo 'Trace : <pre>'.$e->getTraceAsString().'</pre><br/>';
		}
	}
}

?>