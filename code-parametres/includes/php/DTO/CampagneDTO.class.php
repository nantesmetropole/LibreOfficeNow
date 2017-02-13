<?php
include_once 'includes/php/commons/Constantes.php';

define ( "BDD_CAMPAGNE_NUMERO", "NUMERO" );
define ( "BDD_CAMPAGNE_DESCRIPTION", "DESCRIPTION" );
define ( "BDD_CAMPAGNE_DATE_DEBUT", "DATE_DEBUT" );
define ( "BDD_CAMPAGNE_DATE_BUTOIR", "DATE_BUTOIR" );
define ( "BDD_CAMPAGNE_DATE_MIGRATION", "DATE_MIGRATION" );
define ( "BDD_CAMPAGNE_DELAI_AVANT_RELANCE", "DELAI_AVANT_RELANCE" );
define ( "BDD_TABLE_CAMPAGNE", "CAMPAGNE" );

define ( "BDD_SELECT_CAMPAGNE", " SELECT C." . BDD_CAMPAGNE_NUMERO . ", C." . BDD_CAMPAGNE_DESCRIPTION . ", C." . BDD_CAMPAGNE_DATE_DEBUT . ", C." . BDD_CAMPAGNE_DATE_BUTOIR . ", C." . BDD_CAMPAGNE_DATE_MIGRATION . ", C." . BDD_CAMPAGNE_DELAI_AVANT_RELANCE . " FROM " . BDD_TABLE_CAMPAGNE ." C ");
define ( "BDD_SELECT_CAMPAGNE_BY_NUMERO", BDD_SELECT_CAMPAGNE . " WHERE " . BDD_CAMPAGNE_NUMERO . " = :numero" );
define ( "BDD_DELETE_CAMPAGNE_BY_NUMERO", "DELETE FROM " . BDD_TABLE_CAMPAGNE . " WHERE " . BDD_CAMPAGNE_NUMERO . " = :numero" );
define ( "BDD_INSERT_CAMPAGNE", "INSERT INTO " . BDD_TABLE_CAMPAGNE . " (" . BDD_CAMPAGNE_NUMERO . ", " . BDD_CAMPAGNE_DESCRIPTION . ", " . BDD_CAMPAGNE_DATE_DEBUT . ", " . BDD_CAMPAGNE_DATE_BUTOIR . ", " . BDD_CAMPAGNE_DATE_MIGRATION . ", " . BDD_CAMPAGNE_DELAI_AVANT_RELANCE . " ) VALUES (:numero, :description, :dateDebut, :dateButoir, :dateMigration, :delaiAvantRelance)" );
define ( "BDD_UPDATE_CAMPAGNE_BY_NUMERO", "UPDATE " . BDD_TABLE_CAMPAGNE . " SET " . BDD_CAMPAGNE_DESCRIPTION . " = :description, " . BDD_CAMPAGNE_DATE_DEBUT . " = :dateDebut, " . BDD_CAMPAGNE_DATE_BUTOIR . " = :dateButoir, " . BDD_CAMPAGNE_DATE_MIGRATION . " = :dateMigration, " . BDD_CAMPAGNE_DELAI_AVANT_RELANCE . " = :delaiAvantRelance  WHERE " . BDD_CAMPAGNE_NUMERO . " = :numero " );

/**
 * Enter description here .
 *
 *
 *
 * @author m.emschwiller
 *        
 */
class Campagne {
	public $DESCRIPTION;
	public $NUMERO;
	public $DATE_DEBUT;
	public $DATE_BUTOIR;
	public $DATE_MIGRATION;
	public $DELAI_AVANT_RELANCE;
	
	/**
	 * Constructeur de la classe
	 */
	function __construct($b_valoriser_par_formulaire = false) {
		if ($b_valoriser_par_formulaire) {
			$this->setNumero ( $_POST [CAMPAGNE_INPUT_NAME_NUMERO] );
			$this->setDescription ( trim ( $_POST [CAMPAGNE_INPUT_NAME_DESCRIPTION] ) );
			$this->setDateDebut ( ClassUtil::getDateBdd ( $_POST [CAMPAGNE_INPUT_NAME_DATE_DEPART] ) );
			$this->setDateButoir ( ClassUtil::getDateBdd ( $_POST [CAMPAGNE_INPUT_NAME_DATE_BUTOIR] ) );
			$this->setDateMigration ( ClassUtil::getDateBdd ( $_POST [CAMPAGNE_INPUT_NAME_DATE_MIGRATION] ) );
			$this->setDelaiAvantRelance ( $_POST [CAMPAGNE_INPUT_NAME_DELAI] );
		}
	}
	
	/**
	 * Enter description here .
	 *
	 *
	 *
	 *
	 * ..
	 */
	public function getDescription() {
		return $this->DESCRIPTION;
	}
	
	/**
	 * Enter description here .
	 *
	 *
	 *
	 * ..
	 *
	 * @param unknown_type $description        	
	 */
	public function setDescription($description) {
		$this->DESCRIPTION = $description;
	}
	
	/**
	 * Enter description here .
	 *
	 *
	 *
	 *
	 * ..
	 */
	public function getNumero() {
		return $this->NUMERO;
	}
	
	/**
	 * Enter description here .
	 *
	 *
	 *
	 * ..
	 *
	 * @param unknown_type $numero        	
	 */
	public function setNumero($numero) {
		$this->NUMERO = $numero;
	}
	
	/**
	 * Enter description here .
	 *
	 *
	 *
	 *
	 * ..
	 */
	public function getDateDebut() {
		return $this->DATE_DEBUT;
	}
	
	/**
	 * Enter description here .
	 *
	 *
	 *
	 * ..
	 *
	 * @param unknown_type $dateDepart        	
	 */
	public function setDateDebut($dateDebut) {
		$this->DATE_DEBUT = $dateDebut;
	}
	
	/**
	 * Enter description here .
	 *
	 *
	 *
	 *
	 * ..
	 */
	public function getDateButoir() {
		return $this->DATE_BUTOIR;
	}
	
	/**
	 * Enter description here .
	 *
	 *
	 *
	 * ..
	 *
	 * @param unknown_type $dateButoir        	
	 */
	public function setDateButoir($dateButoir) {
		$this->DATE_BUTOIR = $dateButoir;
	}
	
	/**
	 * Enter description here .
	 *
	 *
	 *
	 *
	 * ..
	 */
	public function getDateMigration() {
		return $this->DATE_MIGRATION;
	}
	
	/**
	 * Enter description here .
	 *
	 *
	 *
	 * ..
	 *
	 * @param unknown_type $dateMigration        	
	 */
	public function setDateMigration($dateMigration) {
		$this->DATE_MIGRATION = $dateMigration;
	}
	
	/**
	 * Enter description here .
	 *
	 *
	 *
	 *
	 * ..
	 */
	public function getDelaiAvantRelance() {
		return $this->DELAI_AVANT_RELANCE;
	}
	
	/**
	 * Enter description here .
	 *
	 *
	 *
	 * ..
	 *
	 * @param unknown_type $delaiAvantRelance        	
	 */
	public function setDelaiAvantRelance($delaiAvantRelance) {
		$this->DELAI_AVANT_RELANCE = $delaiAvantRelance;
	}
}
?>