<?php
session_start ();
include_once 'includes/php/commons/Constantes.php';
include_once 'includes/php/utils/ClassUtils.php';
// il faut verifier que l'utilisateur est ADMIN, sinon on le redirige vers index.php

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
	// on verifie
if (ClassUtil::verfierRoleIdentifiant ( $s_identifiant, true, true )) {
	require_once ('includes/php/entete.php');
	include_once 'includes/php/metier/GestionCampagneConsultation.class.php';
	$ag = new GestionCampagneConsultation ();
	echo $ag->execute ();
	require_once ('includes/php/piedPage.php');
} else {
	header ( 'Location: index.php?droit=KO' );
}

?>
