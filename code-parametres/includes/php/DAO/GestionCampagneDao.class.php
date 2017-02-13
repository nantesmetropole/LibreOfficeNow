<?php
include_once 'includes/php/DAO/abstract/AbstractDAO.class.php';
include_once 'includes/php/DTO/CampagneDTO.class.php';
include_once 'includes/php/DTO/AgentCampagneDTO.class.php';
include_once 'includes/php/DTO/ReponseDTO.class.php';
include_once 'includes/php/commons/Constantes.php';


/**
 * Classe de DAO Campagne
 */
class GestionCampagneDAO extends AbstractDAO {
	
	/**
	 * Constructeur de la classe
	 */
	function __construct() {
		parent::__construct ();
	}
	
	/**
	 * Recherche de la liste des campagnes
	 *
	 * @return array de campagne
	 */
	public function rechercherToutesCampagnes() {
		return $this->fetchAllQuery ( BDD_SELECT_CAMPAGNE, null, 'Campagne' );
	}
	
	/**
	 * Recherche une campagne par son identifiant
	 *
	 * @param unknown $numero        	
	 * @return Ambigous <NULL, unknown>
	 */
	public function rechercherUneCampagneParNumero($numero) {
		$retour = null;
		$result = $this->fetchAllQuery ( BDD_SELECT_CAMPAGNE_BY_NUMERO, array (
				':numero' => $numero 
		), 'Campagne' );
		if ($result) {
			$retour = $result [0];
		}
		return $retour;
	}
	
	/**
	 * recherche la liste des agents
	 * 
	 * @param unknown $numero        	
	 */
	public function rechercherListeAgents($numero) {
		return $this->fetchAllQuery ( BDD_SELECT_AGENT_CAMPAGNE_BY_CAMPAGNE, array (
				':numero' => $numero 
		), 'AgentCampagne' );
	}
	
	/**
	 * creation de la campagne en BDD
	 *
	 * @param unknown $campagne        	
	 */
	public function creerCampagne($campagne) {
		$params = array (
				':numero' => $campagne->getNumero (),
				':description' => $campagne->getDescription (),
				':dateDebut' => ClassUtil::dateToString ( $campagne->getDateDebut (), FORMAT_DATE_AAAA_MM_JJ ),
				':dateButoir' => ClassUtil::dateToString ( $campagne->getDateButoir (), FORMAT_DATE_AAAA_MM_JJ ),
				':dateMigration' => ClassUtil::dateToString ( $campagne->getDateMigration (), FORMAT_DATE_AAAA_MM_JJ ),
				':delaiAvantRelance' => $campagne->getDelaiAvantRelance () 
		);
		$id = $this->insert ( BDD_INSERT_CAMPAGNE, $params );
		return $id;
	}
	public function coutAgent($i_idAgent) {
		return $this->countQuery ( "SELECT COUNT(*) FROM " . BDD_TABLE_AGENT_CAMPAGNE . " WHERE " . BDD_AGENT_CAMPAGNE_ID_AGENT . " = :idAgent", array (
				':idAgent' => $i_idAgent
		) );
	}
	
	/**
	 * creation de l'agent en BDD
	 *
	 * @param unknown $agentCampagne        	
	 */
	public function creerAgentCampagne($agentCampagne) {
		$params = array (
				':id' => $agentCampagne->getId (),
				':idAgent' => $agentCampagne->getIdAgent (),
				':idCampagne' => $agentCampagne->getIdCampagne (),
				':dateCreation' => date ( FORMAT_DATE_AAAA_MM_JJ ),
				':dateRelance' => ClassUtil::dateToString ( $agentCampagne->getDateRelance (), FORMAT_DATE_AAAA_MM_JJ ) 
		);
		$id = $this->insert ( BDD_INSERT_AGENT_CAMPAGNE, $params );
		return $id;
	}
	/**
	 * 
	 * @param unknown $agentCampagne
	 */
	public function modifierDateMigrationAgentCampagne($id) {
		$params = array (':id' => $id);
		$this->update(BDD_UPDATE_AGENT_CAMPAGNE, $params);
	}
	/**
	 * Supprime une campagne par son identifiant
	 *
	 * @param unknown $numero        	
	 */
	public function supprimerUneCampagneParNumero($numero) {
		// puis supprimer la table campagne
		$this->delete ( BDD_DELETE_CAMPAGNE_BY_NUMERO, array (
				':numero' => $numero 
		) );
	}
	
	/**
	 * Supprime une campagne par son identifiant
	 *
	 * @param unknown $numero        	
	 */
	public function supprimerUnAgentCampagneParNumero($numero) {
		// il faut supprimer la table agents_campagne avant
		$this->delete ( BDD_DELETE_AGENT_CAMPAGNE_BY_ID_CAMPAGNE, array (
				':numero' => $numero 
		) );
	}
	
	/**
	 *
	 * @param unknown $idAgent        	
	 * @param unknown $idCampagne        	
	 */
	public function supprimerAgentparIdAgentEtIdCampagne($idAgent, $idCampagne) {
		$this->delete ( BDD_DELETE_AGENT_CAMPAGNE_BY_ID_AGENT_AND_ID_CAMPAGNE, array (
				':idAgent' => $idAgent,
				':idCampagne' => $idCampagne 
		) );
	}
	
	/**
	 * verifie qu'une campagne n'existe pas deja avec cette description
	 *
	 * @param unknown $s_description        	
	 */
	public function verifierDescription($s_description) {
		$b_retour = null;
		if (0 == $this->countQuery ( "SELECT COUNT(*) FROM " . BDD_TABLE_CAMPAGNE . " WHERE " . BDD_CAMPAGNE_DESCRIPTION . " = :description", array (
				':description' => $s_description 
		) )) {
			$b_retour = false;
		} else {
			$b_retour = true;
		}
		return $b_retour;
	}
	
	/**
	 * verifie qu'une campagne n'existe pas deja avec cette description
	 *
	 * @param unknown $s_description        	
	 * @param unknown $numero        	
	 * @return Ambigous <NULL, boolean>
	 */
	public function verifierDescriptionEtPasID($s_description, $numero) {
		$b_retour = null;
		if (0 == $this->countQuery ( "SELECT COUNT(*) FROM " . BDD_TABLE_CAMPAGNE . " WHERE " . BDD_CAMPAGNE_DESCRIPTION . " = :description AND " . BDD_CAMPAGNE_NUMERO . " NOT IN (:numero)", array (
				':description' => $s_description,
				':numero' => $numero 
		) )) {
			$b_retour = false;
		} else {
			$b_retour = true;
		}
		return $b_retour;
	}
	
	/**
	 * modification de la campagne
	 * 
	 * @param unknown $campagne        	
	 */
	public function modifierCampagne($campagne) {
		$params = array (
				':numero' => $campagne->getNumero (),
				':description' => $campagne->getDescription (),
				':dateDebut' => ClassUtil::dateToString ( $campagne->getDateDebut (), FORMAT_DATE_AAAA_MM_JJ ),
				':dateButoir' => ClassUtil::dateToString ( $campagne->getDateButoir (), FORMAT_DATE_AAAA_MM_JJ ),
				':dateMigration' => ClassUtil::dateToString ( $campagne->getDateMigration (), FORMAT_DATE_AAAA_MM_JJ ),
				':delaiAvantRelance' => $campagne->getDelaiAvantRelance () 
		);
		$this->update ( BDD_UPDATE_CAMPAGNE_BY_NUMERO, $params );
	}
	
	public function rechercherListeAgentsPourUneDebutCampagneAujourdui() {
		return $this->fetchAllQuery ( BDD_SELECT_AGENT_CAMPAGNE."  JOIN ".BDD_TABLE_CAMPAGNE." C ON C.".BDD_CAMPAGNE_NUMERO." = AC.".BDD_AGENT_CAMPAGNE_ID_CAMPAGNE." WHERE C.".BDD_CAMPAGNE_DATE_DEBUT." <= :date AND AC.".BDD_AGENT_CAMPAGNE_DATE_DEBUT." IS NULL "  , array (
				':date' => date ( FORMAT_DATE_AAAA_MM_JJ )
		), 'AgentCampagne' );
	}
	
	public function modifierDateDebut($id) {
		$params = array (':numero' => $id, ':date' => date ( FORMAT_DATE_AAAA_MM_JJ ));
		$this->update ( "UPDATE " . BDD_TABLE_AGENT_CAMPAGNE . " SET " . BDD_AGENT_CAMPAGNE_DATE_DEBUT . " = :date  WHERE " . BDD_AGENT_CAMPAGNE_ID . " = :numero ", $params );
	}
	
	public function rechercherListeAgentsPourUneButoireMoisDelaiAvantRelanceCampagneAujourdui() {
		return $this->fetchAllQuery ( BDD_SELECT_AGENT_CAMPAGNE." JOIN ".BDD_TABLE_CAMPAGNE." ON ".BDD_CAMPAGNE_NUMERO." = ".BDD_AGENT_CAMPAGNE_ID_CAMPAGNE." LEFT JOIN ".BDD_TABLE_REPONSE." ON ".BDD_AGENT_REPONSE_ID_AGENT_CAMPAGNE." = ".BDD_AGENT_CAMPAGNE_ID." WHERE ".BDD_CAMPAGNE_DATE_BUTOIR." <= DATE_ADD(:date , INTERVAL ".BDD_CAMPAGNE_DELAI_AVANT_RELANCE." DAY) AND ".BDD_AGENT_REPONSE_ID_AGENT_CAMPAGNE." IS NULL AND AC.".BDD_AGENT_CAMPAGNE_DATE_RELANCE." IS NULL"  , array (
				':date' => date ( FORMAT_DATE_AAAA_MM_JJ )
		), 'AgentCampagne' );
	}
	
	public function modifierDateRelance ($id) {
		$params = array (':numero' => $id, ':date' => date ( FORMAT_DATE_AAAA_MM_JJ ));
		$this->update ( "UPDATE " . BDD_TABLE_AGENT_CAMPAGNE . " SET " . BDD_AGENT_CAMPAGNE_DATE_RELANCE . " = :date  WHERE " . BDD_AGENT_CAMPAGNE_ID . " = :numero ", $params );
	}
	
	public function rechercherListeAgentsPourExport() {
		return $this->fetchAllQuery ( BDD_SELECT_AGENT_CAMPAGNE." WHERE ".BDD_AGENT_CAMPAGNE_DATE_MIGRATION." IS NULL AND (".BDD_AGENT_CAMPAGNE_ID_CAMPAGNE." IS NULL OR ".BDD_AGENT_CAMPAGNE_ID_CAMPAGNE." IN (SELECT ".BDD_CAMPAGNE_NUMERO." FROM ".BDD_TABLE_CAMPAGNE." WHERE ".BDD_CAMPAGNE_DATE_MIGRATION." <= :date ))", array (
				':date' => date ( FORMAT_DATE_AAAA_MM_JJ )
		), 'AgentCampagne' );
	}
	
	public function modifierDateMigration ($id) {
		$params = array (':numero' => $id, ':date' => date ( FORMAT_DATE_AAAA_MM_JJ ));
		$this->update ( "UPDATE " . BDD_TABLE_AGENT_CAMPAGNE . " SET " . BDD_AGENT_CAMPAGNE_DATE_MIGRATION . " = :date  WHERE " . BDD_AGENT_CAMPAGNE_ID . " = :numero ", $params );
	}
}
?>