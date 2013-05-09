<?php
//ini_set('memory_limit', '15M');
header('Content-Type: text/html; charset=UTF-8');

require_once './../../lib/simpletest/autorun.php';

//affichage de la mémoire occupée avant les tests
$mo = memory_get_usage(TRUE)/1024/1024;
echo '<br><span style="color:red;font-weight:bold;">
	  Etat de la mémoire avant les tests: '.$mo.' Mo
	  </span><br>';

$test = new TestSuite('Suite de tests du projet Warnimals');

//Tests DAO
$test->addFile('DAOTestCase.php');
$test->addFile('JoueurDAOTestCase.php');
$test->addFile('AnimalDAOTestCase.php');
$test->addFile('CombatDAOTestCase.php');
$test->addFile('EntrainementDAOTestCase.php');
$test->addFile('CombatDAOTestCase.php');
$test->addFile('TransactionDAOTestCase.php');

//Tests métiers
$test->addFile('AnimalTestCase.php');
$test->addFile('JoueurTestCase.php');
$test->addFile('EntrainementIndividuelTestCase.php');
$test->addFile('EntrainementCollectifTestCase.php');
$test->addFile('OffreEntrainementTestCase.php');
$test->addFile('CompetenceTestCase.php');
$test->addFile('CombatTestCase.php');

//Tests modeles
$test->addFile('AnimalModeleTestCase.php');
$test->addFile('JoueurModeleTestCase.php');
$test->addFile('EntrainementModeleTestCase.php');
$test->addFile('CombatModeleTestCase.php');
$test->addFile('TransactionModeleTestCase.php');

//Tests controleurs
$test->addFile('ControleurPrincipalTestCase.php');
//$test->addFile('ControleurMarketTestCase.php');
$test->addFile('ControleurEntrainementTestCase.php');
$test->addFile('CronTestCase.php');




//Affichage de la mémoire occupée apres les tests
$mo = memory_get_usage(TRUE)/1024/1024;
echo '<br><span style="color:red;font-weight:bold;">
	  Etat de la mémoire après les tests: '.$mo.' Mo
	  </span><br>';

?>