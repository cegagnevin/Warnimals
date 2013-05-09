<?php

/**
 * Contient l'ensemble des constantes nécessaires au fonctionnement de l'application.
 */



	//----------- MODE TEST/PRODUCTION ----------
	
		//true pour mettre en mode test | false pour mettre en mode production
		define("MODE_TEST", true);
		
		//Forcer l'affichage des erreurs
		define("AFFICHAGE_ERREURS", true);
		
	//-------------------------------------------
	
	
	//------------------ LOCAL ------------------
	
		// L'hote de la base de données 
		define("HOST_LOCAL", 'localhost'); 
		// Le nom d'utilisateur 
		define("USER_LOCAL", 'root');
		// Le mot de passe correspondant 
		define("PWD_LOCAL", ''); 
		// Le nom de la base de données 
		define("DB_LOCAL", 'warnimals_db1');
		
		//Le chemin absolu du dossier racine WaRnimals en local
		if(MODE_TEST) define("WARNIMALS_PATH", 'C:/xampp/htdocs/WaRnimals');
		
	//-------------------------------------------
	
	
	//--------------- PRODUCTION ----------------
	
		// L'hote de la base de données 
		define("HOST_FRANCESERV", ''); 
		// Le nom d'utilisateur 
		define("USER_FRANCESERV", '');
		// Le mot de passe correspondant
		define("PWD_FRANCESERV", '');
		// Le nom de la base de données 
		define("DB_FRANCESERV", ''); 
		
		//Le chemin absolu du dossier racine WaRnimals en production
		if(!MODE_TEST) define("WARNIMALS_PATH", '');
	
	//-------------------------------------------

		
	//---------------- FACEBOOK -----------------
	
		/* Facebook application ID */
		define("FB_APP_ID", '');
		/* Facebook application secret */
		define("FB_APP_SECRET", '');
		/* Sel du hashage de l'identifiant Fb du joueur */
		define("SEL_HASH", '');
	
	//-------------------------------------------
	
	//---------------- ENTRAINEMENT -----------------
		/* Cout supplémentaire dans l'algorithme de calcul du prix d'un entrainement */
		define("COUT_SUPP_ENTRAINEMENT", 200); 
		/* Coefficient multiplicateur utilisé dans l'algorithme de calcul du prix d'un entrainement individuel */
		define("COEF_MULTI_PRIX_ENTRAINEMENT_IND", 2);
		/* Coefficient multiplicateur utilisé dans l'algorithme de calcul du prix d'un entrainement collectif */
		define("COEF_MULTI_PRIX_ENTRAINEMENT_CO", 1.5);
		/* Coefficient multiplicateur utilisé dans l'algorithme de calcul de la duree d'un entrainement individuel */
		define("COEF_MULTI_DUREE_ENTRAINEMENT_IND", 50);
		/* Coefficient multiplicateur utilisé dans l'algorithme de calcul de la duree d'un entrainement collectif */
		define("COEF_MULTI_DUREE_ENTRAINEMENT_CO", 50);
		
		/* Date minimum de début d'un entrainement collectif en secondes */
		$ajd_8h = mktime(8, 0, 0, date("n"), date("j"), date("Y"));
	 	define("DATE_MIN", $ajd_8h); //Ajd 8h
		/* Date maximale de début d'un entrainement collectif en secondes */
	 	$ajd_22h = mktime(22, 0, 0, date("n"), date("j"), date("Y"));
		define("DATE_MAX", $ajd_22h); //Ajd 22h
		/* Temps d'écart minimum entre les dates de début des entrainements proposés en secondes */
		define("ECART_MIN", 60*60*2); //2h
		
		/* Nombre de participants minimum requis pour les entrainements collectifs */
		define("NB_PARTICIPANTS_MIN", 2);
		/* Nombre de participants maximum requis pour les entrainements collectifs */
		define("NB_PARTICIPANTS_MAX", 5);
	
	//-------------------------------------------
	
	//---------------- COMBATS ------------------
		/* Niveau d'écart autorisé entre les animaux d'un même combat */	
		define("ECART_NIVEAU", 2);
	
	//-------------------------------------------
		
		
	//---------------- GENERAL -----------------
		/* Solde de départ lorsqu'un joueur utilise le jeu pour la 1ere fois */
		define("SOLDE_DEPART", 1000);
		
	//-------------------------------------------

?>