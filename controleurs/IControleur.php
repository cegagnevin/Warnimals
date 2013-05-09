<?php

/**
 * Définit les besoins auquels les controleurs qui implémentent cette interface doivent répondre.
 */
interface IControleur
{
	
	/**
	 * Traite les actions émises par la vue affectée au controleur.
	 * @param action L'action à effectuer
	 */
	public function traiterAction($action);
	
}

?>