<?php

/**
 * @author m.emschwiller
 *
 */
class ReponseXML {
	private $s_identifiant;
	private $i_actif;
	private $s_libelle;
	private $l_groupes = array ();
	function __construct($s_identifiant, $s_libelle, $i_actif = 1) {
		$this->setIdentifiant ( $s_identifiant );
		$this->setLibelle ( $s_libelle );
		$this->setActif ( $i_actif );
	}
	public function getIdentifiant() {
		return $this->s_identifiant;
	}
	public function setIdentifiant($s_identifiant) {
		$this->s_identifiant = $s_identifiant;
	}
	public function getLibelle() {
		return $this->s_libelle;
	}
	public function setLibelle($s_libelle) {
		$this->s_libelle = $s_libelle;
	}
	public function getActif() {
		return $this->i_actif;
	}
	public function setActif($i_actif) {
		$this->i_actif = $i_actif;
	}
	
	/**
	 *
	 * @return multitype:
	 */
	public function getListeGroupe() {
		return $this->l_groupes;
	}
	
	/**
	 *
	 * @param unknown $l_groupes
	 */
	public function setListeGroupe($l_groupes) {
		$this->l_groupes = $l_groupes;
	}
	
	/**
	 * ajouter un groupe a la liste des groupes
	 *
	 * @param unknown $g_groupe
	 */
	public function addGroupe($g_groupe) {
		$taille = sizeof ( $this->l_groupes );
		$this->l_groupes [$taille + 1] = $g_groupe;
	}
	
	public function __toString() {
		return 'identifiant : ' . $this->s_identifiant . ',libelle : ' . $this->s_libelle;
	}
}
?>