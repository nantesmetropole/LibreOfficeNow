<?php
session_start ();

include_once 'includes/php/commons/Constantes.php';

include_once 'includes/php/metier/GestionCampagneConsultation.class.php';
$ag = new GestionCampagneConsultation ();
if ('3' == $_POST [ID_PAGE]) {
	$ag->exportCSVCampagne ();
} else if ('4' == $_POST [ID_PAGE]) {
	$ag->exportCSVAgents ();
}

?>
