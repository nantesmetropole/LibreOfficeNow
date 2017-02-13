<?php
define ( "BDD_AGENT_REPONSE_NUMERO_QUESTION", "NUMERO_QUESTION" );
define ( "BDD_AGENT_REPONSE_ID_AGENT_CAMPAGNE", "ID_AGENT_CAMPAGNE" );
define ( "BDD_AGENT_REPONSE_ID_REPONSE", "ID_REPONSE" );
define ( "BDD_AGENT_REPONSE_LIBELLE_REPONSE", "LIBELLE_REPONSE" );
define ( "BDD_TABLE_REPONSE", "REPONSE" );

define ( "BDD_SELECT_REPONSE", " SELECT " . BDD_AGENT_REPONSE_NUMERO_QUESTION . ", " . BDD_AGENT_REPONSE_ID_AGENT_CAMPAGNE . ", " . BDD_AGENT_REPONSE_ID_REPONSE . ", " . BDD_AGENT_REPONSE_LIBELLE_REPONSE . " FROM " . BDD_TABLE_REPONSE );
define ( "BDD_SELECT_REPONSE_BY_AGENT", BDD_SELECT_REPONSE . " JOIN ON " . BDD_TABLE_AGENT_CAMPAGNE . " ON " . BDD_AGENT_CAMPAGNE_ID . " = " . BDD_AGENT_REPONSE_ID_AGENT_CAMPAGNE . " WHERE " . BDD_AGENT_CAMPAGNE_ID_AGENT . " = :idAgent" );
define ( "BDD_INSERT_REPONSE", "INSERT INTO " . BDD_TABLE_REPONSE . " (" . BDD_AGENT_REPONSE_NUMERO_QUESTION . ", " . BDD_AGENT_REPONSE_ID_AGENT_CAMPAGNE . ", " . BDD_AGENT_REPONSE_ID_REPONSE . ", " . BDD_AGENT_REPONSE_LIBELLE_REPONSE . ") VALUES (:idQuestion, :idAgentCampagne, :idReponse, :libellereponse)" );
define ( "BDD_DELETE_REPONSE_BY_ID_CAMPAGNE_AGENT_AND_ID_QUESTION", "DELETE FROM " . BDD_TABLE_REPONSE . " WHERE " . BDD_AGENT_REPONSE_ID_AGENT_CAMPAGNE . " = :idCampagneAgent AND " . BDD_AGENT_REPONSE_NUMERO_QUESTION . " = :idQuestion" );

/**
 * Enter description here .
 *
 * @author m.emschwiller
 *        
 */
class Reponse {
	public $NUMERO_QUESTION;
	public $ID_AGENT_CAMPAGNE;
	public $ID_REPONSE;
	public $LIBELLE_REPONSE;
	
	/**
	 * Constructeur de la classe
	 */
	function __construct() {
	}
	
	/**
	 * Enter id here .
	 */
	public function getNumeroQuestion() {
		return $this->NUMERO_QUESTION;
	}
	
	/**
	 * Enter id here .
	 *
	 * @param unknown_type $numero_question        	
	 */
	public function setNumeroQuestion($numero_question) {
		$this->NUMERO_QUESTION = $numero_question;
	}
	
	/**
	 * Enter idCampagne here .
	 */
	public function getIdAgentCampagne() {
		return $this->ID_AGENT_CAMPAGNE;
	}
	
	/**
	 * Enter idCampagne here .
	 * @param unknown_type $idCampagne        	
	 */
	public function setIdAgentCampagne($idAgentCampagne) {
		$this->ID_AGENT_CAMPAGNE = $idAgentCampagne;
	}
	
	/**
	 * Enter idAgent here .
	 */
	public function getIdReponse() {
		return $this->ID_REPONSE;
	}
	
	/**
	 * Enter idReponse here .
	 * @param unknown_type $idReponse        	
	 */
	public function setIdReponse($idReponse) {
		$this->ID_REPONSE = $idReponse;
	}
	
	/**
	 * Enter LibelleReponse here .
	 */
	public function getLibelleReponse() {
		return $this->LIBELLE_REPONSE;
	}
	
	/**
	 * Enter LibelleReponse here .
	 * @param unknown_type $LibelleReponse        	
	 */
	public function setLibelleReponse($libelleReponse) {
		$this->LIBELLE_REPONSE = $libelleReponse;
	}
	public function __toString() {
		return 'LIBELLE_REPONSE : ' . $this->LIBELLE_REPONSE;
	}
}
?>