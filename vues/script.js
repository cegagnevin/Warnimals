/********************************************************************************
***********************************  PLAN     ***********************************
*********************************************************************************
	
		  | - A - Entrainement
		  | - B - Arène
		  | - C - PréArene
		  | - D - Market
		  | - E - Interface Principale

/*********************************************************************************
***************************** A-  Entrainement ***********************************
**********************************************************************************/

$(document).ready(function()
{
	//Inscription à l'entrainement individuel
	$("#entrainementInd").click(function() {
		inscriptionEntrainementIndividuel();
	});
	
	//Inscription à un entrainement collectif
	$("button.collectif").click(function() {
		var idEntrainement = $(this).next().val();
		inscriptionEntrainementCollectif(idEntrainement);
	});
	
	$("#houseArene").click(function(){
		$.ajax({ // fonction permettant de faire de l'ajax
			   type: "GET", // methode de transmission des données au fichier php
			   url: "../controleurs/ControleurCombat.php", // url du fichier php
			   data: "actionCombat=IsAllowed", // données à transmettre
			   dataType: "json",
			   success: function(msg){ // si l'appel a bien fonctionné
					if(msg != null) // si la connexion en php a fonctionné
					{
						if(msg.ok == true)
						{
							window.location.href="../controleurs/ControleurPrincipal.php?actionPrincipale=Arene";
						}
						else
						{
							alert(msg.error);
							return;
						}
					}
					else 
					{
						alert("Something went wrong");
					}
			   }
			});
		
	});
	
	$("#houseEntrainement").click(function(){
		$.ajax({ // fonction permettant de faire de l'ajax
			   type: "GET", // methode de transmission des données au fichier php
			   url: "../controleurs/ControleurEntrainement.php", // url du fichier php
			   data: "actionEntrainement=IsAllowed", // données à transmettre
			   dataType: "json",
			   success: function(msg){ // si l'appel a bien fonctionné
					if(msg != null) // si la connexion en php a fonctionné
					{
						if(msg.ok == true)
						{
							window.location.href="../controleurs/ControleurPrincipal.php?actionPrincipale=Entrainement";
						}
						else
						{
							alert(msg.error);
							return;
						}
					}
					else 
					{
						alert("Something went wrong");
					}
			   }
			});
	});
	
	$("#houseProfil").click(function(){
		window.location.href="../controleurs/ControleurPrincipal.php?actionPrincipale=Profil";
	});
	
	$("#houseParis").click(function(){
		window.location.href="../controleurs/ControleurPrincipal.php?actionPrincipale=Paris";
	});
	
	$("#houseMarket").click(function(){
		window.location.href="../controleurs/ControleurPrincipal.php?actionPrincipale=Market";
	});
});




/**
 * Envoi au serveur une requete AJAX permettant de faire l'inscription à un entrainement individuel.
 */		
function inscriptionEntrainementIndividuel()
{	
	$.ajax({ // fonction permettant de faire de l'ajax
	   type: "GET", // methode de transmission des données au fichier php
	   url: "../controleurs/ControleurEntrainement.php", // url du fichier php
	   data: "actionEntrainement=EntrainementIndividuel", // données à transmettre
	   success: function(msg){ // si l'appel a bien fonctionné
			if(msg != null) // si la connexion en php a fonctionné
			{
				$("div#entrainement_msg").html(msg);
				desactiverBoutonsEntrainements();
			}
			else // si la connexion en php n'a pas fonctionnée
			{
				$("div#entrainement_msg").html("Une erreur est survenue, veuillez réessayer ultérieurement.");
			}
	   }
	});
}


/**
 * Envoi au serveur une requete AJAX permettant de faire l'inscription à un entrainement collectif.
 */		
function inscriptionEntrainementCollectif(idEntrainement)
{	
	$.ajax({ // fonction permettant de faire de l'ajax
	   type: "GET", // methode de transmission des données au fichier php
	   url: "../controleurs/ControleurEntrainement.php", // url du fichier php
	   data: "actionEntrainement=EntrainementCollectif&idEntrainementCollectif="+idEntrainement, // données à transmettre
	   success: function(msg){ // si l'appel a bien fonctionné
			if(msg != null) // si la connexion en php a fonctionnée
			{
				$("div#entrainement_msg").html(msg);
				desactiverBoutonsEntrainements();
			}
			else // si la connexion en php n'a pas fonctionnée
			{
				$("div#entrainement_msg").html("Une erreur est survenue, veuillez réessayer ultérieurement.");
			}
	   }
	});
}

/**
 * Desactive les boutons de choix des entrainements (collectifs et individuels).
 */		
function desactiverBoutonsEntrainements()
{	
	$("button").attr("disabled","disabled").addClass( 'disabled' );
}



/*********************************************************************************
************************************ B-  Arène ***********************************
**********************************************************************************/

//-------------- Variables globales ---------------

infosJoueur = new Array(); //Contient : 'idFacebook', 'nomJoueur', 'idAnimal', 'nomAnimal', 'vieAnimal', 'defenseAnimal', 'attaqueAnimal', 'raceAnimal'
infosAdversaire = new Array(); //Contient : 'niveauAnimal' et 'raceAnimal'
//manageMyTurn_execution = null; //Contient l'éxecution du traitement "manageMyTurn"
timer_execution = null; //Contient l'éxecution de la fonction "launchTimer()"
//turn = ''; //Contient le tour courant

//-------------------------------------------------


$(document).ready(function()
{
	getInfosCombat();
});

/**
 * Demande au serveur les informations nécessaires pour le combat.
 * Permet la récupération du nom du joueur, de son animal et de savoir si c'est à lui de jouer.
 */		
function getInfosCombat()
{	
	$.ajax({ // fonction permettant de faire de l'ajax
	   type: "GET", // methode de transmission des données au fichier php
	   url: "../controleurs/ControleurCombat.php", // url du fichier php
	   dataType: "json",
	   data: "actionCombat=InitialisationCombat", // données à transmettre
	   success: function(msg){ // si l'appel a bien fonctionné
			if(msg != null) // si la connexion en php a fonctionnée
			{
				infosJoueur[ 'idFacebook'] = msg.idFacebook;
				infosJoueur[ 'nomJoueur' ] = msg.nomJoueur;
				
				infosJoueur[ 'idAnimal' ] = msg.idAnimal;
				infosJoueur[ 'nomAnimal' ] = msg.nomAnimal;
				infosJoueur[ 'niveauAnimal' ] = msg.niveauAnimal;
				infosJoueur[ 'vieAnimal' ] = msg.vieAnimal;
				infosJoueur[ 'defenseAnimal' ] = msg.defenseAnimal;
				infosJoueur[ 'attaqueAnimal' ] = msg.attaqueAnimal;
				infosJoueur[ 'raceAnimal' ] = msg.raceAnimal;
				infosJoueur[ 'competencesAnimal' ] = msg.competencesAnimal;

				infosAdversaire[ 'idAnimal' ] = msg.idAnimal2;
				infosAdversaire[ 'niveauAnimal' ] = msg.niveauAnimal2;
				infosAdversaire[ 'raceAnimal' ] = msg.raceAnimal2;
				infosAdversaire[ 'vieAnimal' ] = msg.vieAnimal2;
				
				initCombat(); //On initialise le combat
			}
			else // si la connexion en php n'a pas fonctionnée
			{
				alert("Une erreur est survenue, veuillez réessayer ultérieurement.");
			}
	   }
	});
}

function manageMyTurn()
{
	$.ajax({ // fonction permettant de faire de l'ajax
		   type: "GET", // methode de transmission des données au fichier php
		   url: "../controleurs/ControleurCombat.php", // url du fichier php
		   data: "actionCombat=isMyTurn&idAnimal="+infosJoueur[ 'idAnimal' ], // données à transmettre
		   success: function(msg){ // si l'appel a bien fonctionné
				if(msg != null) // si la connexion en php a fonctionnée
				{
					console.log('Recu par "manageMyTurn()" : '+msg);
					
					if(msg == 'attaque')
					{
						timer_execution = setTimeout("hideDefenses();	showAttacks(); launchTimer();", 1000);
					}
					else if(msg == 'defense')
					{
						timer_execution = setTimeout("showDefenses();	hideAttacks(); launchTimer();", 1000);
					}
					else if(msg == 'wait')
					{
						hideAttacks();
						hideDefenses();
						setTimeout("manageMyTurn()", 1000);
					}
					
				}
				else // si la connexion en php n'a pas fonctionnée
				{
					alert("Une erreur est survenue lors de la demande du tour au serveur, veuillez réessayer ultérieurement.");
				}
		   }
	});
}


/**
 * Initialise le combat.
 */
function initCombat()
{
	afficherAnimaux();
	
	initMenuAttaques();
	
	$( ".menu_attaques" ).menu();
	$("#attaques_animal a").click(function() {
		onClickCompetence($(this).prev().val());
	});
	
	initMenuDefenses();
	
	$( ".menu_defenses" ).menu();
	$("#defenses_animal a").click(function() {
		onClickCompetence($(this).prev().val(), $(this).parent());
	});	
	
	$("#abandonner").click(function() {
		if(confirm("Etes-vous sur de vouloir abandonner ce combat? Vous êtes sur le point de perdre l'argent que vous avez engagé."))
		{
			abandon();
		}
	});
	
	
	//On lance le jeu 
	manageMyTurn();
}

function onClickCompetence(competence, blocCompetence)
{
	sendAction(infosJoueur[ 'idAnimal' ], competence);
	clearTimeout(timer_execution);
	$("#knobTimer").parent().hide();
	hideAttacks();
	hideDefenses();
	
	//Demander le résultat de l'action
	setTimeout("askResult()", 500);
}

function askResult()
{
	console.log("Ask result !");
	$.ajax({ // fonction permettant de faire de l'ajax
		   type: "GET", // methode de transmission des données au fichier php
		   url: "../controleurs/ControleurCombat.php", // url du fichier php
		   dataType: "json",
		   data: "actionCombat=GetResult", // données à transmettre
		   success: function(msg){ // si l'appel a bien fonctionné
			   //alert("MSG:"+msg);
				if(msg != null) // si la connexion en php a fonctionnée
				{
					if(msg != -1)//Resultats recus
					{
						//On affiche les messages dans la console d'actions
						for(var i=0 ; i<msg.message.length ; i++)
						{
							notifier(msg.message[i]);
						}
						notifier('-------------------------------------');
						
						//On débite la vie de l'animal attaqué
						debiterVie(msg.animalBlesse, msg.degats);
						console.log(msg);
						
						//On relance le jeu en demandant le tour actuel
						manageMyTurn();
					}
					else
					{
						console.log("On redemande les resultats...");
						setTimeout("askResult()", 1000);
					}
				}
				else
				{
					alert("Une erreur est survenue lors de la demande du resultat au serveur, veuillez réessayer ultérieurement.");
				}
		   }
	});
}


/**
 * Fills the attacks menu of attacks of the current animal.
 */
function initMenuAttaques()
{
	var attaques = '';
	for(var i=0 ; i<infosJoueur[ "competencesAnimal" ].length ; i++)
	{
		if(infosJoueur[ "competencesAnimal" ][i][ "type"] == 'attaque')
		{
			attaques += '<li>'+
							'<input type="hidden" value="'+infosJoueur[ "competencesAnimal" ][i][ "idCompetence"]+'" />'+
							'<a href="#">'+
								infosJoueur[ "competencesAnimal" ][i][ "nomCompetence"]+
							'</a>'+
						'</li>';
		}
	}
	$('.menu_attaques').html(attaques);
}

/**
 * Fills the defenses menu of defenses of the current animal.
 */
function initMenuDefenses()
{
	var defenses = '';
	for(var i=0 ; i<infosJoueur[ "competencesAnimal" ].length ; i++)
	{
		if(infosJoueur[ "competencesAnimal" ][i][ "type"] == 'defense')
		{
			defenses += '<li>'+
							'<input type="hidden" value="'+infosJoueur[ "competencesAnimal" ][i][ "idCompetence"]+'" />'+
							'<a href="#">'+
								infosJoueur[ "competencesAnimal" ][i][ "nomCompetence"]+
							'</a>'+
						'</li>';
		}
	}
	$('.menu_defenses').html(defenses);
}

/**
 * Sends an action to the server. An action represents a competence choiced by the player for his animal.
 * @param idAnimal The animal concerned
 * @param idCompetence The competence used 
 */
function sendAction(idAnimal, idCompetence)
{
	$.ajax({ // fonction permettant de faire de l'ajax
		   type: "GET", // methode de transmission des données au fichier php
		   url: "../controleurs/ControleurCombat.php", // url du fichier php
		   data: "actionCombat=NewActionCombat&idAnimal="+idAnimal+"&idCompetence="+idCompetence, // données à transmettre
		   success: function(msg){ // si l'appel a bien fonctionné
				if(msg == 1) // si la connexion en php a fonctionnée
				{
					console.log('Action envoyée : '+idAnimal+' | '+idCompetence);
				}
				else // si la connexion en php n'a pas fonctionnée
				{
					alert("Une erreur est survenue lors de l'envoi de l'action au serveur, veuillez réessayer ultérieurement.");
				}
		   }
	});
}

/**
 * Allows to get the relative path to the image corresponding of animal's characteristics.
 * @param race The race of the animal (First capital is important)
 * @param level The level of the animal
 * @param position The position where display the image ( 'L' for left OR 'R' for right)
 */
function getRelativePathToImg(race, level, position)
{	
	var path = race+'/'+race+'_';

	if(level > 0 && level < 10)
	{
		path += '1';
	}
	else if(level >= 10 && level < 20)
	{
		path += '2';
	}
	else //level >= 20
	{
		path += '3';
	}
	path += '_'+position+'.svg';
	return path;
}

/**
 * Displays animals with their progress bar which represents their life.
 */
function afficherAnimaux()
{
	var animal1 = getRelativePathToImg(infosJoueur[ 'raceAnimal' ], infosJoueur[ 'niveauAnimal' ], 'L');
	var animal2 = getRelativePathToImg(infosAdversaire[ 'raceAnimal' ], infosAdversaire[ 'niveauAnimal' ], 'R');
	
	$('#animal1').html("<div id='progressbar1' class='progressbar'></div><img src='../images/"+animal1+"' alt='Animal1' />");
	$('#animal2').html("<div id='progressbar2' class='progressbar'></div><img src='../images/"+animal2+"' alt='Animal2' />");
	
	var vie1 = parseInt(infosJoueur[ 'vieAnimal' ]);
	var vie2 = parseInt(infosAdversaire[ 'vieAnimal' ]);
	$( "#progressbar1" ).progressbar({
		  max: vie1, 
	      value: vie1
	    });
	
	$( "#progressbar2" ).progressbar({
		  max: vie2,
	      value: vie2
	    });
}

/**
 * Debite la vie de l'animal blessé.
 */
function debiterVie(idAnimal, degats)
{
	var numAnimal = 1;
	if(idAnimal != infosJoueur[ 'idAnimal' ])
	{
		numAnimal = 2;
	}
	
	var vie = $( "#progressbar"+numAnimal ).progressbar('option', 'value');
	vie -= degats;
	$( "#progressbar"+numAnimal ).progressbar({
	      value: vie
	});
		
	if(vie <= 0)
	{
		if(idAnimal == infosJoueur[ 'idAnimal' ])
		{
			console.log("Votre animal est mort... Défaite");
			finCombat(infosAdversaire[ 'idAnimal' ]);
		}
		else
		{
			console.log("L'animal adverse est mort ! Victoire !!");
			finCombat(infosJoueur[ 'idAnimal' ]);
		}
	}
}

/**
 * Annonce le vainqueur au serveur qui va effectuer les traitements adéquats.
 */
function finCombat(idAnimalVainqueur)
{
	clearTimeout(timer_execution);
	hideAttacks();
	hideDefenses();
	$.ajax({ // fonction permettant de faire de l'ajax
		   type: "GET", // methode de transmission des données au fichier php
		   url: "../controleurs/ControleurCombat.php", // url du fichier php
		   dataType: "json",
		   data: "actionCombat=FinCombat&idAnimalVainqueur="+idAnimalVainqueur, // données à transmettre
		   success: function(msg){ // si l'appel a bien fonctionné
				if(msg != null) // si la connexion en php a fonctionnée
				{
					alert("Vous avez gagné "+msg.gains+" pièces d'or !");
					//On redirige l'utilisateur sur la page principale
					document.location.href="interfacePrincipale.html";
				}
				else // si la connexion en php n'a pas fonctionnée
				{
					alert("Une erreur est survenue lors de l'envoi de la fin du combat au serveur, veuillez réessayer ultérieurement.");
				}
		   }
	});
}

/**
 * Launch a timer which start at 30 seconds. If the time's up, the player cancels the game.
 */
function launchTimer()
{
	//console.log("launchTimer()");
	$("#knobTimer").knob({
        'min': 0,
        'max': 30,
        "fgColor":"#d19000",
        "cursor": false,
        "readOnly": true
    });
	$("#knobTimer").show();
	
	var timer = parseInt($("#knobTimer").val());
	if(timer == 0) //Abandon
	{
		abandon();
	}
	else
	{
		$('#knobTimer').val(timer-1).trigger('change');
		timer_execution = setTimeout("launchTimer()", 1000);
	}
}

/**
 * Executé lorsque le joueur abandonne la partie (trop long à choisir sa compétence ou abandon volontaire).
 */
function abandon()
{
	console.log("Abandon de la partie");
	clearTimeout(timer_execution);
	$("#knobTimer").parent().hide();	
	
	$.ajax({ // fonction permettant de faire de l'ajax
		   type: "GET", // methode de transmission des données au fichier php
		   url: "../controleurs/ControleurCombat.php", // url du fichier php
		   data: "actionCombat=Abandon&idAnimalAbandon="+infosJoueur[ 'idAnimal' ], // données à transmettre
		   success: function(msg){ // si l'appel a bien fonctionné
				if(msg != null) // si la connexion en php a fonctionnée
				{
					console.log(msg);
					alert("Vous venez d'abandonner la partie. Cet abandon sera inscrit sur votre profil.");
					//On redirige l'utilisateur sur la page principale
					document.location.href="interfacePrincipale.html";
				}
				else // si la connexion en php n'a pas fonctionnée
				{
					alert("Une erreur est survenue lors de l'envoi l'abandon du combat au serveur, veuillez réessayer ultérieurement.");
				}
		   }
	});
}

/**
 * Writes the message given into the textarea which displays all actions of the current fight.
 * @param message The message to display
 */
function notifier(message)
{
	$('#arene_actions textarea').append(message+'\n');
}

function showAttacks()
{
	$("#attaques_animal").show('fade', 1000);
	//Ajouter le timer
}

function hideAttacks()
{
	$("#attaques_animal").hide('fade', 1000);
}

function showDefenses()
{
	$("#defenses_animal").show('fade', 1000);
	//Ajouter le timer
}

function hideDefenses()
{
	$("#defenses_animal").hide('fade', 1000);
}



/*********************************************************************************
************************************ C-  PréArene ********************************
**********************************************************************************/

//-------------- Variables globales ---------------

combats = new Array(); //Contient : 'idCombat', 'idJoueur', 'nomAnimal', 'vieAnimal', 'attaqueAnimal', 'defenseAnimal', 'niveauAnimal', 'nomJoueur'

//-------------------------------------------------


$(document).ready(function()
{
	//Clic sur création du combat
	$("#create").click(function() {
		//creerCombat();
		askMoney();
	});
	
	//Clic sur rejoindre un combat
	$("#join").click(function() {
		rejoindreCombat();
	});
	

});

function askMoney()
{
	$.ajax({ // fonction permettant de faire de l'ajax
		   url: "../controleurs/ControleurCombat.php", // url du fichier php
		   data: "actionCombat=getCreditJoueur", // données à transmettre
		   dataType: "json",
		   success: function(msg){ // si l'appel a bien fonctionné
			   if(msg != null) // si la connexion en php a fonctionnée
				{
				   console.log('Credit joueur ' + msg.credit);
				   clearButton();
				   
				   //Display a message to ask the money
				   var message = "<div id='panneau'><p class='title'>Vous souhaitez créer un combat</p>" +
							"<p class='message'>Pour que votre combat soit finalisé, il vous faut donner une somme d'argent non nulle.<br/><br/> " +
							"Vous avez actuellement <b id='money'>" + msg.credit + "</b> pièce d'or. Combien souhaitez vous mettre de pièces ?<br/><br/>" +
							"<input type='text' name='money' id='moneyPut'/> pièces<br/>" +
							"<a href ='#' id='validate'>Valider</a></p>";

					$("#combats").html(message);

					$("#validate").click(function() {
						creerCombat();
					});
				}
			   else
			   	{
				   alert("Une erreur est survenue, veuillez réessayer ultérieurement.");
				}
		   }
	});	
}

function creerCombat()
{
	var money = $('#moneyPut').val();
	if(isNaN(money) || money == "")
	{
		alert('Vous devez entrer un nombre !');
		return;
	}
	
	
	$.ajax({ // fonction permettant de faire de l'ajax
		   url: "../controleurs/ControleurCombat.php", // url du fichier php
		   data: "actionCombat=clicCreer&money="+ money, // données à transmettre
		   dataType: "json",
		   success: function(msg){ // si l'appel a bien fonctionné
			   if(msg != null) // si la connexion en php a fonctionnée
				{
				   if(msg.error != null)
				   {
					   alert(msg.error);
					   return;
				   }
				   
				   //Display a message to ask the money
				   var message = "<div id='panneau'><p class='title'>Votre combat a été créé</p>" +
				   		"<br/><p class='message'>Veuillez attendre un adversaire. <br/>Ceci peut prendre quelques minutes ...<br/>" +
				   		"Si vous le désirez, vous pouvez rejoindre un combat en attente d'adversaire, ceci mettra fin à votre création de combat.</p>";
				   
				   $("#combats").html(message);
				   
				   //console.log("Nombre d'itération : " + msg.length);
				   for(var i=0 ; i<msg.length ; i++)
				   {
					   var tab = new Array();
					   tab[ 'idCombat'] = msg[i].idCombat;
					   tab[ 'idJoueur'] = msg[i].idJoueur;
					   tab[ 'nom_animal'] = msg[i].nom_animal;
					   tab[ 'vie_animal'] = msg[i].vie_animal;
					   tab[ 'attaque_animal'] = msg[i].attaque_animal;
					   tab[ 'defense_animal'] = msg[i].defense_animal;
					   tab[ 'niveau_animal'] = msg[i].niveau_animal;
					   tab[ 'nom_joueur'] = msg[i].nom_joueur;
					
					   combats.push(tab);
				   }
		
				   showCombatList("create");
				   
				   $("#home").remove();
				   
				   isThereAnOpponnent();
				}
			   else
			   	{
				   alert("Something went wrong");
				}
		   }
	});	
}

//Ne boucle pas correctement
function isThereAnOpponnent()
{
	console.log("Entre dans isThereAnOpponnent()");
	$.ajax({ // fonction permettant de faire de l'ajax
		   type: "GET",
		   url: "../controleurs/ControleurCombat.php", // url du fichier php
		   dataType: "json",
		   data: "actionCombat=isThereAnOpponnent", // données à transmettre
		   success: function(msg){ // si l'appel a bien fonctionné
			   if(msg != -1) // si la connexion en php a fonctionnée
				{
				   $.ajax({ // fonction permettant de faire de l'ajax
					   type: "GET",
					   url: "../controleurs/ControleurCombat.php", // url du fichier php
					   dataType: "json",
					   data: "actionCombat=finalizeCombatCree", // données à transmettre
					   success: function(msg){ // si l'appel a bien fonctionné
						   console.log(msg);
						   if(msg != null) // si la connexion en php a fonctionnée
							{
							   window.location.href="../vues/arene.html";
							}
						   else // si la connexion en php n'a pas fonctionnée
							{
							   alert("Une erreur est survenue, veuillez réessayer ultérieurement.");
							}
					   }
				
				   }); 
				}
			   else
			   	{
				   //alert("pas trouvé");
				   console.log("isThereAnOpponnent()");
				   isThereAnOpponnent();
				}
		   }
	});
}

function rejoindreCombat()
{
	$.ajax({ // fonction permettant de faire de l'ajax
		   url: "../controleurs/ControleurCombat.php", // url du fichier php
		   data: "actionCombat=clicRejoindre", // données à transmettre
		   dataType: "json",
		   success: function(msg){ // si l'appel a bien fonctionné
			   if(msg != null) // si la connexion en php a fonctionnée
				{
				   console.log('Liste des combats en cours d\'affichage');
				   clearButton();
				   
				   //console.log("Nombre d'itération : " + msg.length);
				   for(var i=0 ; i<msg.length ; i++)
				   {
					   var tab = new Array();
					   tab[ 'idCombat'] = msg[i].idCombat;
					   tab[ 'idJoueur'] = msg[i].idJoueur;
					   tab[ 'nom_animal'] = msg[i].nom_animal;
					   tab[ 'vie_animal'] = msg[i].vie_animal;
					   tab[ 'attaque_animal'] = msg[i].attaque_animal;
					   tab[ 'defense_animal'] = msg[i].defense_animal;
					   tab[ 'niveau_animal'] = msg[i].niveau_animal;
					   tab[ 'nom_joueur'] = msg[i].nom_joueur;
					   tab[ 'nbVictoires' ] = msg[i].nbVictoires;
					   tab[ 'nbDefaites' ] = msg[i].nbDefaites;
					   tab[ 'nbAbandons' ] = msg[i].nbAbandons;
					   
					   combats.push(tab);
				   }
				   
				   showCombatList("join");
				   
				}
			   else // si la connexion en php n'a pas fonctionnée
				{
				   alert("Une erreur est survenue, veuillez réessayer ultérieurement.");
				}
		   }
	
	});
}

function clearButton()
{
	$("#create").hide();
	$("#join").hide();
}

function showCombatList(choice)
{
	if (combats.length == 0)
	{
		var message = "<div id='panneau'><p class='title'>Aucun combat en attente d'adversaire</p>" +
	   		"<br/><p class='message'>Il n'y a aucun combat en attente d'adversaire.<br/>" +
	   		"Si vous le désirez, vous pouvez créer votre combat et attendre qu'un joueur vous rejoigne.</p>";
	   
		$("#combats").html(message);
		return;
	}

	var tableHeader;
	if(choice == "create")
	{
		tableHeader = "<table id='tab_combats_create'>" +
			"<tr id='header_tab_combat'>" +
			"<td><b>Proprietaire</b></td>" +
			"<td><b>Animal</b></td>" +
			"<td><b>Niveau</b></td>" +
			"<td><b>Victoires</b></td>" +
			"<td><b>Defaites</b></td>" +
			"<td><b>Abandons</b></td>" +
			"<td>Rejoindre ?</td></tr>";
		$("#combats").append(tableHeader);
	
	}
	else if(choice == "join")
	{
		tableHeader = "<table id='tab_combats'>" +
		"<tr id='header_tab_combat'>" +
		"<td><b>Proprietaire</b></td>" +
		"<td><b>Animal</b></td>" +
		"<td><b>Niveau</b></td>" +
		"<td><b>Victoires</b></td>" +
		"<td><b>Defaites</b></td>" +
		"<td><b>Abandons</b></td>" +
		"<td>Rejoindre ?</td></tr>";
		$("#combats").html(tableHeader);
	}
	
	
	for(var i=0 ; i<combats.length ; i++)
	{
		var idCombat = combats[i]['idCombat'];
		var combatsToDisplay = "<tr class='line_tab_combat'>" +
				"<td>" + combats[i]['nom_joueur'] + "</td>" +
				"<td>" + combats[i]['nom_animal'] + "</td>" +
				"<td><b>" + combats[i]['niveau_animal'] + "</b></td>" +
				"<td>" + combats[i]['nbVictoires'] + "</td>" +
				"<td>" + combats[i]['nbDefaites'] + "</td>" +
				"<td>" + combats[i]['nbAbandons'] + "</td>" +
				"<td><input type='hidden' id='idCombat' value='"+ idCombat + "'/><input type='button' id='join' value='Rejoindre'/></td></tr>";

		$("#tab_combats").append(combatsToDisplay);
		$("#tab_combats_create").append(combatsToDisplay);
		
		//Clic sur rejoindre
		$("#join").click(function() {
			var idCombat = $("#idCombat").val();
			askMoneyJoinFight(idCombat);
		});
	}
	
	var tableEnd = "</table>";
	
	if (combats.length < 5)
	{
		var css_table = {"width":"600px", "height":"300px", "position":"absolute", "left":"50%", "top":"70%", "margin-left":"-150px", "margin-top":"-300px", "border":"2px solid #a39f90", "border-radius":"1%"};
		$("#tab_combats").css(css_table);
	}
	else
	{
		var css_table = {"width":"600px", "height":"500px", "position":"absolute", "left":"50%", "top":"70%", "margin-left":"-150px", "margin-top":"-300px", "border":"2px solid #a39f90", "border-radius":"1%"};
		$("#tab_combats").css(css_table);
	}

	$("#combats").append(tableEnd);
	
}


function askMoneyJoinFight(idCombat)
{
	$.ajax({ // fonction permettant de faire de l'ajax
		   url: "../controleurs/ControleurCombat.php", // url du fichier php
		   data: "actionCombat=combatRejointVerif&idCombat="+idCombat, // données à transmettre
		   dataType: "json",
		   success: function(msg){ // si l'appel a bien fonctionné
			   if(msg != null) // si la connexion en php a fonctionnée
				{
				   //Display a message to ask the money
				   var message = "<div id='panneau'><p class='title'>Vous souhaitez rejoindre un combat</p>" +
							"<p class='message'>Pour que votre combat soit finalisé, il vous faut donner une somme d'argent non nulle.<br/><br/> " +
							"Vous avez actuellement <b id='money'>" + msg.credit + "</b> pièce d'or. Combien souhaitez vous mettre de pièces ?<br/><br/>" +
							"<input type='text' name='money' id='moneyPut'/> pièces<br/>" +
							"<a href ='#' id='validate'>Valider</a></p></div>";

					$("#combats").html(message);

					$("#validate").click(function() {
						finalizeJoin(msg.idCombat);
					});
				   
				}
			   else // si la connexion en php n'a pas fonctionnée
				{
				   alert("Une erreur est survenue, veuillez réessayer ultérieurement.");
				}
		   }
	
	});
}

function finalizeJoin(idCombat)
{
	var money = $("#moneyPut").val();
	
	if(isNaN(money) || money == "")
	{
		alert('Vous devez entrer un nombre !');
		return;
	}
	
	$.ajax({ // fonction permettant de faire de l'ajax
		   url: "../controleurs/ControleurCombat.php", // url du fichier php
		   data: "actionCombat=finalizeCombatRejoint&idCombat="+idCombat+"&money="+money, // données à transmettre
		   dataType: "json",
		   success: function(msg){ // si l'appel a bien fonctionné
			   if(msg != null) // si la connexion en php a fonctionnée
				{
				   window.location.href = "../vues/arene.html";
				}
			   else // si la connexion en php n'a pas fonctionnée
				{
				   alert("Une erreur est survenue, veuillez réessayer ultérieurement.");
				}
		   }
	
	});
}


/*********************************************************************************
***************************** D-  Market *****************************************
**********************************************************************************/
//-------------- Variables globales ---------------

refreshTimeMarket = 10000; //10sec 

//-------------------------------------------------


$(document).ready(function()
{
	if($("#marketMain").length)
	{
		afficherEnTeteMarket();
		listerAnimauxEnVente();
	}
});

/**
 * Demande au serveur la liste des animaux en vente.
 */		
function listerAnimauxEnVente()
{	
	$.ajax({ // fonction permettant de faire de l'ajax
	   type: "GET", // methode de transmission des données au fichier php
	   url: "../controleurs/ControleurMarket.php", // url du fichier php
	   dataType: "json",
	   data: "actionMarket=ListerTransactions", // données à transmettre
	   success: function(msg){ // si l'appel a bien fonctionné
			if(msg != null) // si la connexion en php a fonctionnée
			{
				console.log("Rafraichissement du tableau...");
				afficherAnimauxEnVente(msg);
				setTimeout("listerAnimauxEnVente()", refreshTimeMarket);
			}
			else // si la connexion en php n'a pas fonctionnée
			{
				alert("Une erreur est survenue dans la demande des animaux en vente au serveur, veuillez réessayer ultérieurement.");
			}
	   }
	});
	
}

/**
 * Demande au serveur si l'animal du joueur est en vente ou pas.
 */		
function afficherEnTeteMarket()
{	
	$.ajax({ // fonction permettant de faire de l'ajax
	   type: "GET", // methode de transmission des données au fichier php
	   url: "../controleurs/ControleurMarket.php", // url du fichier php
	   data: "actionMarket=IsSelling", // données à transmettre
	   success: function(msg){ // si l'appel a bien fonctionné
			if(msg != null) // si la connexion en php a fonctionnée
			{
				if(msg == true) //On affiche l'en tete d'annulation de la vente
				{
					if($("#formMiseEnVenteAnimal tr").length > 1) //On enleve un eventuel header deja présent
					{
						$("#formMiseEnVenteAnimal tr").first().remove();
					}
					
					var form = "<tr>"+
								  "<td>"+
									"Votre animal est actuellement mis en vente."+ 
									"<button id='annulerVente'>Annuler</button>"+
								  "</td>"+
							   "</tr>";
		
					$("#formMiseEnVenteAnimal").prepend(form);
					
					$("#annulerVente").click(function() {
						annulerVente();
					});
				}
				else //On affiche l'en tete de mis en vente d'un animal
				{
					if($("#formMiseEnVenteAnimal tr").length > 1) //On enleve un eventuel header deja présent
					{
						$("#formMiseEnVenteAnimal tr").first().remove();
					}
					
					var form = "<tr>"+
								  "<td>"+
									"Je souhaite mettre en vente mon animal pour <input id='prixVente' size='10' type='text' placeholder='Prix de vente'/> pièces d'or"+ 
									"<button id='vendre'>Vendre</button>"+
								  "</td>"+
							   "</tr>";
					
					$("#formMiseEnVenteAnimal").prepend(form);
					
					$("#vendre").click(function() {
						vendreAnimal($("#prixVente").val());
					});
				}
			}
			else // si la connexion en php n'a pas fonctionnée
			{
				alert("Une erreur est survenue dans la demande au serveur si l'animal du joueur est en vente, veuillez réessayer ultérieurement.");
			}
	   }
	});
	
}


/**
 * Affiche la liste des animaux en vente.
 * @param array Tableau Json contenant la liste des animaux en vente
 */		
function afficherAnimauxEnVente(tabAnimaux)
{	
	//On enleve les anciennes valeurs du tableau
	$("#listeAnimauxEnVente tr").remove();
	
	//On reconstruit le tableau avec les nouvelles valeurs
	var i = 0;
	var transaction = null;
	for(i=0 ; i<tabAnimaux.length ; i++)
	{
		var date = new Date(tabAnimaux[i]['dateTransaction']*1000);
		//var dateTransaction = $.datepicker.formatDate('dd/mm/yy', date);
		var dateTransaction = date.getDate()+"/"+(date.getMonth()+1)+"/"+date.getFullYear()+" à "+date.getHours()+"h"+date.getMinutes();
		
		transaction = "<tr><td>"+
						  "<table class='transaction'>"+
							"<tr>"+
								"<td><span class='nomAnimal'>"+tabAnimaux[i]['nomAnimal']+"</span> - <strong class='raceAnimal'>"+tabAnimaux[i]['nomRace']+"</strong></td>"+
								"<td colspan='3'><span class='dateVente'>Mis en vente le "+dateTransaction+"</span></td>"+
							"</tr>"+
							"<tr>"+
								"<td><br/>Attaque : <span class='highlight'>"+tabAnimaux[i]['attaque']+"</span></td>"+
								"<td><br/>Victoires : <span class='highlight'>"+tabAnimaux[i]['nbVictoires']+"</span></td>"+
								"<td rowspan='3'><span class='prix'>"+tabAnimaux[i]['prixVente']+" PO</span></td>"+
								"<td rowspan='3'><input type='hidden' value='"+tabAnimaux[i]['idTransaction']+"' /><button class='acheter'>Acheter</button></td>"+
							"</tr>"+	
							"<tr>"+
								"<td>Défense : <span class='highlight'>"+tabAnimaux[i]['defense']+"</span></td>"+
								"<td>Abandons : <span class='highlight'>"+tabAnimaux[i]['nbAbandons']+"</span></td>"+
							"</tr>"+
							"<tr>"+
								"<td>Vie : <span class='highlight'>"+tabAnimaux[i]['vie']+"</span></td>"+
								"<td></td>"+
							"</tr>";
						  "</table>"+
					   "</td></tr>";
		
		$("#listeAnimauxEnVente").append(transaction);
	}

	
	$(".acheter").click(function() {
		var idTransaction = $(this).prev().val();
		acheterAnimal(idTransaction);
	});
}

/**
 * Permet l'achat d'un animal
 * @param idTransaction Le numéro de la transaction concernée
 */		
function acheterAnimal(idTransaction)
{	
	if(confirm("Etes-vous sur de vouloir acheter cet animal?"))
	{
		$.ajax({ // fonction permettant de faire de l'ajax
			   type: "GET", // methode de transmission des données au fichier php
			   url: "../controleurs/ControleurMarket.php", // url du fichier php
			   data: "actionMarket=Acheter&idTransaction="+idTransaction, // données à transmettre
			   success: function(msg){ // si l'appel a bien fonctionné
					if(msg != 1) //Erreur
					{
						console.log(msg);
						alert(msg);
					}
					else //Achat OK
					{
						alert("Félicitation ! Vous venez d'acquérir un nouvel animal !");
						//On redirige le joueur vers son profil
						window.location.href = "../controleurs/ControleurPrincipal.php?actionPrincipale=Profil";
					}
			   }
		});
	}
}

/**
 * Permet au joueur de vendre son animal au prix qu'il y fixé.
 * @param idTransaction Le numéro de la transaction concernée
 */		
function vendreAnimal(prix)
{	
	$("#erreurMarket").html("");
	
	//Controles
	if(prix.length == 0)
	{
		var message = "Veuillez renseigner un prix";
	}
	else if(!parseInt(prix))
	{
		var message = "Le prix doit être un nombre entier";
	}
	
	//On tronque le prix à sa valeur entiere
	prix = parseInt(prix);
	
	if(typeof message != "undefined")
	{
		$("#erreurMarket").html(message);
	}
	else
	{
		if(confirm("Etes-vous sur de vouloir mettre en vente votre animal pour "+prix+" PO ?"))
		{
			$.ajax({ // fonction permettant de faire de l'ajax
				   type: "GET", // methode de transmission des données au fichier php
				   url: "../controleurs/ControleurMarket.php", // url du fichier php
				   data: "actionMarket=Vendre&prixVente="+prix, // données à transmettre
				   success: function(msg){ // si l'appel a bien fonctionné
						if(msg != 1) //Erreur
						{
							console.log(msg);
							alert(msg);
						}
						else //Mise en vente OK
						{
							alert("Félicitation ! Vous venez de mettre en vente votre animal !");
							afficherEnTeteMarket();
							listerAnimauxEnVente();
						}
				   }
			});
		}
	}
}


/**
 * Demande au serveur l'annulation de la mise en vente du joueur courant.
 */		
function annulerVente()
{	
	if(confirm("Etes-vous sur de vouloir annuler la mise en vente de votre animal?"))
	{
		$.ajax({ // fonction permettant de faire de l'ajax
			   type: "GET", // methode de transmission des données au fichier php
			   url: "../controleurs/ControleurMarket.php", // url du fichier php
			   data: "actionMarket=AnnulerVente", // données à transmettre
			   success: function(msg){ // si l'appel a bien fonctionné
					if(msg == 1) //OK
					{
						alert("La vente a bien été annulée.");
						afficherEnTeteMarket();
						listerAnimauxEnVente();
					}
					else //Erreur
					{
						alert("Une erreur est survenue dans la demande au serveur de l'annulation de la vente, veuillez réessayer ultérieurement.");
					}
			   }
		});
	}
}


/*********************************************************************************
***************************** E-  Interface Principale ***************************
**********************************************************************************/

$(document).ready(function()
{
	if($(".background_interfacePrincipale").length)
	{
		positionnerMaisons();
		
		$(window).resize(function() {
			positionnerMaisons();
		});
	}
});


/**
 * Positionne les maisons de l'interface principale en fonction du ratio de l'écran.
 */		
function positionnerMaisons()
{	
	//Détermination du ratio
	var ratio = screen.width / screen.height;
	
	if(ratio > 1.77) // 1920*1080  OU  1600*900
	{
		positionner_1_77();
	}
	else //if(ratio == 1.25) //1280*1024
	{
		positionner_1_25();
	}
		
}

/**
 * Positionne les maisons de l'interface principale pour un ratio de 1.77.
 */		
function positionner_1_77()
{
	//Market
	$("#lien_market").css({
		"position": "absolute",
		"right": "3%",
		"height" : "40%",
		"cursor": "pointer",
		"top": "27.6%"
	});
	
	$("#lien_market").click(function() {
		window.location.href = "../controleurs/ControleurPrincipal.php?actionPrincipale=Market";
	});
	
	//Paris
	$("#lien_paris").css({
		"position": "absolute",
		"right": "24%",
		"height" : "30%",
		"cursor": "pointer",
		"top": "31.7%"
	});
	
	$("#lien_paris").click(function() {
		window.location.href = "../controleurs/ControleurPrincipal.php?actionPrincipale=Paris";
	});
	
	//Arène
	$("#lien_arene").css({
		"position": "absolute",
		"left": "3%",
		"height" : "40%",
		"cursor": "pointer",
		"top": "26.7%"
	});
	
	$("#lien_arene").click(function() {
		window.location.href = "../controleurs/ControleurPrincipal.php?actionPrincipale=Arene";
	});
	
	//Entrainement
	$("#lien_entrainement").css({
		"position": "absolute",
		"left": "24%",
		"height" : "30%",
		"cursor": "pointer",
		"top": "31.9%"
	});
	
	$("#lien_entrainement").click(function() {
		window.location.href = "../controleurs/ControleurPrincipal.php?actionPrincipale=Entrainement";
	});
	
	//Profil
	$("#lien_profil").css({
		"position": "absolute",
		"left": "44.2%",
		"height" : "21%",
		"cursor": "pointer",
		"top": "38.6%"
	});
	
	$("#lien_profil").click(function() {
		window.location.href = "../controleurs/ControleurPrincipal.php?actionPrincipale=Profil";
	});
}

/**
 * Positionne les maisons de l'interface principale pour un ratio de 1.25.
 */		
function positionner_1_25()
{
	//Market
	$("#lien_market").css({
		"position": "absolute",
		"right": "2.4%",
		"height" : "28%",
		"cursor": "pointer",
		"top": "24%"
	});
	
	$("#lien_market").click(function() {
		window.location.href = "../controleurs/ControleurPrincipal.php?actionPrincipale=Market";
	});
	
	//Paris
	$("#lien_paris").css({
		"position": "absolute",
		"right": "22%",
		"height" : "23%",
		"cursor": "pointer",
		"top": "38.2%"
	});
	
	$("#lien_paris").click(function() {
		window.location.href = "../controleurs/ControleurPrincipal.php?actionPrincipale=Paris";
	});
	
	//Arène
	$("#lien_arene").css({
		"position": "absolute",
		"left": "2.4%",
		"height" : "28%",
		"cursor": "pointer",
		"top": "36.5%"
	});
	
	$("#lien_arene").click(function() {
		window.location.href = "../controleurs/ControleurPrincipal.php?actionPrincipale=Arene";
	});
	
	//Entrainement
	$("#lien_entrainement").css({
		"position": "absolute",
		"left": "22%",
		"height" : "23%",
		"cursor": "pointer",
		"top": "38.5%"
	});
	
	$("#lien_entrainement").click(function() {
		window.location.href = "../controleurs/ControleurPrincipal.php?actionPrincipale=Entrainement";
	});
	
	//Profil
	$("#lien_profil").css({
		"position": "absolute",
		"left": "42.5%",
		"height" : "21%",
		"cursor": "pointer",
		"top": "38.8%"
	});
	
	$("#lien_profil").click(function() {
		window.location.href = "../controleurs/ControleurPrincipal.php?actionPrincipale=Profil";
	});
}


