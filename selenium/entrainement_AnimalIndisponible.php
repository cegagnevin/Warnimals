<?php
	require_once '../mapping/DAO.php';
	require_once 'dump_database.php';
	
	$dao = new DAO();
	
	if($_GET[ 'mode' ] == 0) //Initialisation
	{
		dump_MySQL('localhost', 'root', '', 'warnimals_db1', 2);
		
		$script = <<<EOF
DELETE FROM t_joueur;
INSERT INTO t_joueur (idFacebook, credit, dateInscription) VALUES ('9f20d53fab1470b02a217242c4eb1cf59303aa7c', 1000, 1358547171);
DELETE FROM t_entrainement;
DELETE FROM t_entrainement_animal;
INSERT INTO t_entrainement_animal (Entrainement_idEntrainement, Animal_idAnimal, dateSouscription, valide) VALUES ('ETEST001', 'A2', time()-100, 0);		
	
EOF;
		
		$requete_prepare = $dao->getConnexion()->prepare($script);
		$bool = $requete_prepare->execute();
		if($bool)
		{
			echo 'OK';
		}
		
	}
	else if($_GET[ 'mode' ] == 1) //Remise de la base à son état initial
	{
		$script = file_get_contents('sauvegarde.sql');
		$requete_prepare = $dao->getConnexion()->prepare($script);
		$bool = $requete_prepare->execute();
		echo 'OK';
	}

?>