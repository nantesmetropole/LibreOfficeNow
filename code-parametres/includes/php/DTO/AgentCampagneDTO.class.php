<?php
define ( "BDD_AGENT_CAMPAGNE_ID", "ID" );
define ( "BDD_AGENT_CAMPAGNE_ID_CAMPAGNE", "ID_CAMPAGNE" );
define ( "BDD_AGENT_CAMPAGNE_ID_AGENT", "ID_AGENT" );
define ( "BDD_AGENT_CAMPAGNE_DATE_CREATION", "DATE_CREATION" );
define ( "BDD_AGENT_CAMPAGNE_DATE_RELANCE", "DATE_RELANCE" );
define ( "BDD_AGENT_CAMPAGNE_DATE_DEBUT", "DATE_DEBUT" );
define ( "BDD_AGENT_CAMPAGNE_DATE_MIGRATION", "DATE_MIGRATION" );
define ( "BDD_TABLE_AGENT_CAMPAGNE", "AGENTS_CAMPAGNE" );


define ( "BDD_SELECT_AGENT_CAMPAGNE", " SELECT AC." . BDD_AGENT_CAMPAGNE_ID . ", AC." . BDD_AGENT_CAMPAGNE_ID_CAMPAGNE . ", AC." . BDD_AGENT_CAMPAGNE_ID_AGENT . ", AC." . BDD_AGENT_CAMPAGNE_DATE_CREATION . ", AC." . BDD_AGENT_CAMPAGNE_DATE_RELANCE . ", AC.".BDD_AGENT_CAMPAGNE_DATE_DEBUT.", AC.".BDD_AGENT_CAMPAGNE_DATE_MIGRATION." FROM " . BDD_TABLE_AGENT_CAMPAGNE." AC " );
define ( "BDD_SELECT_AGENT_CAMPAGNE_BY_CAMPAGNE", BDD_SELECT_AGENT_CAMPAGNE . " WHERE " . BDD_AGENT_CAMPAGNE_ID_CAMPAGNE . " = :numero" );
define ( "BDD_SELECT_AGENT_CAMPAGNE_BY_AGENT", BDD_SELECT_AGENT_CAMPAGNE . " WHERE " . BDD_AGENT_CAMPAGNE_ID_AGENT . " = :numero" );
define ( "BDD_SELECT_AGENT_CAMPAGNE_BY_ID", BDD_SELECT_AGENT_CAMPAGNE . " WHERE " . BDD_AGENT_CAMPAGNE_ID . " = :numero" );
define ( "BDD_DELETE_AGENT_CAMPAGNE_BY_ID_CAMPAGNE", "DELETE FROM " . BDD_TABLE_AGENT_CAMPAGNE . " WHERE " . BDD_AGENT_CAMPAGNE_ID_CAMPAGNE . " = :numero" );
define ( "BDD_DELETE_AGENT_CAMPAGNE_BY_ID_AGENT_AND_ID_CAMPAGNE", "DELETE FROM " . BDD_TABLE_AGENT_CAMPAGNE . " WHERE " . BDD_AGENT_CAMPAGNE_ID_CAMPAGNE . " = :idCampagne AND " . BDD_AGENT_CAMPAGNE_ID_AGENT . " = :idAgent" );
define ( "BDD_INSERT_AGENT_CAMPAGNE", "INSERT INTO " . BDD_TABLE_AGENT_CAMPAGNE . " (" . BDD_AGENT_CAMPAGNE_ID . ", " . BDD_AGENT_CAMPAGNE_ID_AGENT . ", " . BDD_AGENT_CAMPAGNE_ID_CAMPAGNE . ", " . BDD_AGENT_CAMPAGNE_DATE_CREATION . ", " . BDD_AGENT_CAMPAGNE_DATE_RELANCE . ") VALUES (:id, :idAgent, :idCampagne, :dateCreation, :dateRelance)" );
define ( "BDD_UPDATE_AGENT_CAMPAGNE", "UPDATE " . BDD_TABLE_AGENT_CAMPAGNE . " SET " . BDD_AGENT_CAMPAGNE_DATE_MIGRATION . " = null WHERE " . BDD_AGENT_CAMPAGNE_ID. " = :id" );

/**
 * Enter description here .
 *
 * @author m.emschwiller
 *        
 */
class AgentCampagne {
	public $ID;
	public $ID_CAMPAGNE;
	public $ID_AGENT;
	public $DATE_CREATION;
	public $DATE_RELANCE;
	public $DATE_DEBUT;
	public $DATE_MIGRATION;
	
	/**
	 * Constructeur de la classe
	 */
	function __construct() {
	}
	
	/**
	 * Enter id here .
	 *
	 */
	public function getId() {
		return $this->ID;
	}
	
	/**
	 * Enter id here ...
	 *
	 * @param unknown_type $id        	
	 */
	public function setId($id) {
		$this->ID = $id;
	}
	
	/**
	 * Enter idCampagne here .
	 * ..
	 */
	public function getIdCampagne() {
		return $this->ID_CAMPAGNE;
	}
	
	/**
	 * Enter idCampagne here ...
	 *
	 * @param unknown_type $idCampagne        	
	 */
	public function setIdCampagne($idCampagne) {
		$this->ID_CAMPAGNE = $idCampagne;
	}
	
	/**
	 * Enter idAgent here .
	 * ..
	 */
	public function getIdAgent() {
		return $this->ID_AGENT;
	}
	
	/**
	 * Enter idAgent here ...
	 *
	 * @param unknown_type $idAgent        	
	 */
	public function setIdAgent($idAgent) {
		$this->ID_AGENT = $idAgent;
	}
	
	/**
	 * Enter dateCreation here .
	 * ..
	 */
	public function getDateCreation() {
		return $this->DATE_CREATION;
	}
	
	/**
	 * Enter dateCreation here ...
	 *
	 * @param unknown_type $dateCreation        	
	 */
	public function setDateCreation($dateCreation) {
		$this->DATE_CREATION = $dateCreation;
	}
	
	/**
	 * Enter description here .
	 *
	 */
	public function getDateRelance() {
		return $this->DATE_RELANCE;
	}
	
	/**
	 *
	 * Enter description here ...
	 *
	 * @param unknown_type $dateRelance        	
	 */
	public function setDateRelance($dateRelance) {
		$this->DATE_RELANCE = $dateRelance;
	}
	/**
	 * 
	 * @return unknown
	 */
	public function getDateMigration() {
		return $this->DATE_MIGRATION;
	}
	/**
	 * 
	 * @param unknown $dateMigration
	 */
	public function setDateMigration($dateMigration) {
		$this->DATE_MIGRATION = $dateMigration;
	}
	/**
	 * 
	 */
	public function getDateDebut() {
		return $this->DATE_DEBUT;
	}
	/**
	 * 
	 * @param unknown $dateDebut
	 */
	public function setDateDebut($dateDebut) {
		$this->DATE_DEBUT = $dateDebutn;
	}
}
?>