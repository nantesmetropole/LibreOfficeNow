<?php

// include_once 'includes/php/pojo/Reponse.php';

/**
 *
 * @author m.emschwiller
 *        
 */
class GroupeXML {
	private $s_identifiant;
	
	/**
	 * constructeur du groupe
	 *
	 * @param string $s_identifiant        	
	 */
	function __construct($s_identifiant) {
		$this->setIdentifiant ( $s_identifiant );
	}
	
	/**
	 */
	public function getIdentifiant() {
		return $this->s_identifiant;
	}
	
	/**
	 *
	 * @param unknown $s_identifiant        	
	 */
	public function setIdentifiant($s_identifiant) {
		$this->s_identifiant = $s_identifiant;
	}
	
	/**
	 *
	 * @return string
	 */
	public function __toString() {
		return 'identifiant : ' . $this->s_identifiant;
	}
}
?>