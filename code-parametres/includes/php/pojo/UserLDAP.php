<?php

/**
 * @author m.emschwiller
 *
 */
class UserLDAP {
	private $s_identifiant;
	private $s_nom;
	private $s_prenom;
	private $s_email;
	function __construct() {

	}
	public function getIdentifiant() {
		return $this->s_identifiant;
	}
	public function setIdentifiant($s_identifiant) {
		$this->s_identifiant = $s_identifiant;
	}
	public function getNom() {
		return $this->s_nom;
	}
	public function setNom($s_nom) {
		$this->s_nom = $s_nom;
	}
	public function getPrenom() {
		return $this->s_prenom;
	}
	public function setPrenom($s_prenom) {
		$this->s_prenom = $s_prenom;
	}
	public function getEmail() {
		return $this->s_email;
	}
	public function setEmail($s_email) {
		$this->s_email = $s_email;
	}
	public function __toString() {
		return 'identifiant : ' . $this->s_identifiant . ',nom : ' . $this->s_nom . ', prenom : ' . $this->s_prenom;
	}
}
?>