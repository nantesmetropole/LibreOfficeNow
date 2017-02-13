<?php
session_start ();
include_once 'includes/php/commons/Constantes.php';
include_once 'includes/php/utils/ParseXMLConstante.php';

require_once ('includes/php/entete.php');

// on recupere l'id de l'utilisateur
$s_identifiant = '';
if (isset ( $_POST ['REMOTE_USER'] )) {
	$s_identifiant = $_POST ['REMOTE_USER'];
}
if (isset ( $_SERVER ['REMOTE_USER'] )) {
	$s_identifiant = $_SERVER ['REMOTE_USER'];
}
if (isset ( $_SERVER ['HTTP_CAS_USER'] )) {
	$s_identifiant = $_SERVER ['HTTP_CAS_USER'];
}
if ('' != $s_identifiant) {
	$_SESSION [ID_AGENT_CONNECTER] = $s_identifiant;
}
if (!isset($_SESSION [ID_AGENT_CONNECTER]) || $_SESSION [ID_AGENT_CONNECTER] == '') {
	echo '<div class="message negative" id="erreurs"><ul><li>Pour acc&egrave;der &agrave; ce site vous devez avoir un identifiant.</li></ul></div>';
} else {
	$ag = null;
	if (isset ( $_POST [TYPE_ACTION] )) {
		switch ($_POST [TYPE_ACTION]) {
			case ACTION_GESTION_CAMPAGNE :
				include_once 'includes/php/metier/GestionCampagne.class.php';
				$ag = new GestionCampagne ();
				break;
			case ACTION_GESTION_CAMPAGNE_CONSULTATION :
				include_once 'includes/php/metier/GestionCampagneConsultation.class.php';
				$ag = new GestionCampagneConsultation ();
				break;
			case ACTION_GESTION_MIGRATION :
			default :
				include_once 'includes/php/metier/GestionMigration.class.php';
				$ag = new GestionMigration ();
				break;
		}
		echo $ag->execute ();
	} else {
		if (isset($_GET['droit'])) {
			echo '<div class="message negative" id="erreurs"><ul><li>Vous n\'avez pas les droits.</li></ul></div>';
			$_SESSION [ID_AGENT_CONNECTER] = '';
		} else {
			// echo '<div class="message negative" id="erreurs"><ul><li>La page demand&eacute; n\'existe pas.</li></ul></div>';
			include_once 'includes/php/metier/GestionMigration.class.php';
			$ag = new GestionMigration ();
			echo $ag->execute ();
		}
	}
}
require_once ('includes/php/piedPage.php');
?>