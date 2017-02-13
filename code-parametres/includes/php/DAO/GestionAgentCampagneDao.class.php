<?php
include_once 'includes/php/DAO/abstract/AbstractDAO.class.php';
include_once 'includes/php/DTO/AgentCampagneDTO.class.php';
include_once 'includes/php/DTO/CampagneDTO.class.php';
include_once 'includes/php/DTO/ReponseDTO.class.php';

/**
 */
class GestionAgentCampagneDAO extends AbstractDAO {
	
	/**
	 * Constructeur de la classe
	 */
	function __construct() {
		parent::__construct ();
	}
	
	/**
	 * Recherche une campagne par son identifiant
	 *
	 * @param unknown $agent        	
	 * @return Ambigous <NULL, unknown>
	 */
	public function rechercherUneCampagneParAgent($agent) {
		$retour = null;
		$result = $this->fetchAllQuery ( BDD_SELECT_CAMPAGNE . " JOIN " . BDD_TABLE_AGENT_CAMPAGNE . " ON " . BDD_CAMPAGNE_NUMERO . " = " . BDD_AGENT_CAMPAGNE_ID_CAMPAGNE . " WHERE " . BDD_AGENT_CAMPAGNE_ID_AGENT . " = :agent", array (
				':agent' => $agent 
		), 'Campagne' );
		if ($result) {
			$retour = $result [0];
		}
		return $retour;
	}
	
	/**
	 *
	 * @param unknown $agent        	
	 * @return Ambigous <NULL, unknown>
	 */
	public function rechercherAgentCampagneParAgent($agent) {
		$retour = null;
		$result = $this->fetchAllQuery ( BDD_SELECT_AGENT_CAMPAGNE_BY_AGENT . " ORDER BY " . BDD_AGENT_CAMPAGNE_DATE_CREATION . " ASC", array (
				':numero' => $agent 
		), 'AgentCampagne' );
		if ($result) {
			$retour = $result [0];
		}
		return $retour;
	}
	
	/**
	 * remonte toutes les campagnes pour un agent
	 *
	 * @param unknown $agent
	 *        	l'id de l'agent
	 */
	public function rechercherListeAgentCampagneParAgent($agent) {
		return $this->fetchAllQuery ( BDD_SELECT_AGENT_CAMPAGNE_BY_AGENT, array (
				':numero' => $agent 
		), 'AgentCampagne' );
	}
	/**
	 * recherche un agent campagne par son id
	 * 
	 * @param unknown $id        	
	 * @return Ambigous <NULL, unknown>
	 */
	public function rechercherAgentCampagneParId($id) {
		$retour = null;
		$result = $this->fetchAllQuery ( BDD_SELECT_AGENT_CAMPAGNE_BY_ID, array (
				':numero' => $id 
		), 'AgentCampagne' );
		if ($result) {
			$retour = $result [0];
		}
		return $retour;
	}
	
	/**
	 * remonte toutes les campagnes
	 *
	 * @param unknown $agent
	 *        	l'id de l'agent
	 */
	public function rechercherListeAgentCampagne() {
		return $this->fetchAllQuery ( BDD_SELECT_AGENT_CAMPAGNE, null, 'AgentCampagne' );
	}
	
	/**
	 *
	 * @param unknown $agent        	
	 */
	public function rechercherListeReponseParAgent($agent) {
		return $this->fetchAllQuery ( BDD_SELECT_REPONSE . " JOIN " . BDD_TABLE_AGENT_CAMPAGNE . " ON " . BDD_AGENT_CAMPAGNE_ID . " = " . BDD_AGENT_REPONSE_ID_AGENT_CAMPAGNE . " WHERE " . BDD_AGENT_CAMPAGNE_ID_AGENT . " = :idAgent ORDER BY " . BDD_AGENT_REPONSE_NUMERO_QUESTION . " ASC", array (
				':idAgent' => $agent 
		), 'Reponse' );
	}
	/**
	 * Supprime les réponses à la question
	 *
	 * @param unknown $idCampagneAgent        	
	 * @param unknown $idQuestion        	
	 */
	public function supprimeReponseParAgentCampagneEtQuestion($idCampagneAgent, $idQuestion) {
		$this->delete ( BDD_DELETE_REPONSE_BY_ID_CAMPAGNE_AGENT_AND_ID_QUESTION, array (
				':idCampagneAgent' => $idCampagneAgent,
				':idQuestion' => $idQuestion 
		) );
	}
	
	/**
	 * ajout une ligne une reponse pour une question / campagne agent
	 *
	 * @param unknown $r_reponse        	
	 */
	public function insertReponse($r_reponse) {
		$this->insert ( BDD_INSERT_REPONSE, array (
				':idQuestion' => $r_reponse->getNumeroQuestion (),
				':idAgentCampagne' => $r_reponse->getIdAgentCampagne (),
				':idReponse' => $r_reponse->getIdReponse (),
				':libellereponse' => $r_reponse->getLibelleReponse () 
		) );
	}
	
	/**
	 * recherche une reponse en fonction des parametre d'entre
	 *
	 * @param unknown $idReponse        	
	 * @param unknown $idAgentCampagne        	
	 * @param unknown $idQuestion        	
	 */
	public function compterReponseParReponseEtAgentCampagneEtQuestion($idReponse, $idAgentCampagne, $idQuestion) {
		return $this->countQuery ( BDD_SELECT_REPONSE . " WHERE " . BDD_AGENT_REPONSE_ID_AGENT_CAMPAGNE . " = :idAgentCampagne AND " . BDD_AGENT_REPONSE_NUMERO_QUESTION . " = :idQuestion AND " . BDD_AGENT_REPONSE_ID_REPONSE . " = :idReponse", array (
				':idAgentCampagne' => $idAgentCampagne,
				':idQuestion' => $idQuestion,
				':idReponse' => $idReponse 
		) );
	}
	
	/**
	 *
	 * @param unknown $idAgentCampagne        	
	 * @param unknown $idQuestion        	
	 */
	public function rechercherReponseParAgentCampagneEtQuestion($idAgentCampagne, $idQuestion) {
		return $this->fetchAllQuery ( BDD_SELECT_REPONSE . " WHERE " . BDD_AGENT_REPONSE_ID_AGENT_CAMPAGNE . " = :idAgentCampagne AND " . BDD_AGENT_REPONSE_NUMERO_QUESTION . " = :idQuestion ", array (
				':idAgentCampagne' => $idAgentCampagne,
				':idQuestion' => $idQuestion 
		), 'Reponse' );
	}
	
	/**
	 *
	 * @param unknown $idAgentCampagne
	 * @param unknown $idQuestion
	 * @param $idReponse
	 */
	public function rechercherReponseParAgentCampagneEtQuestionEtReponse($idAgentCampagne, $idQuestion, $idReponse) {
		$retour = null;
		$result = $this->fetchAllQuery ( BDD_SELECT_REPONSE . " WHERE " . BDD_AGENT_REPONSE_ID_AGENT_CAMPAGNE . " = :idAgentCampagne AND " . BDD_AGENT_REPONSE_NUMERO_QUESTION . " = :idQuestion AND " . BDD_AGENT_REPONSE_ID_REPONSE . " = :idReponse ", array (
				':idAgentCampagne' => $idAgentCampagne,
				':idQuestion' => $idQuestion,
				':idReponse' => $idReponse 
				), 
				'Reponse' );
		if ($result) {
			$retour = $result [0];
		}
		return $retour;
	}
	
	/**
	 *
	 * @param unknown $agent
	 */
	public function rechercherReponsesParAgentCampagne($idAgentCampagne) {
		return $this->fetchAllQuery ( BDD_SELECT_REPONSE . " WHERE " . BDD_AGENT_REPONSE_ID_AGENT_CAMPAGNE . " = :idAgentCampagne ORDER BY " . BDD_AGENT_REPONSE_NUMERO_QUESTION . ", ".BDD_AGENT_REPONSE_ID_REPONSE." ASC", array (
				':idAgentCampagne' => $idAgentCampagne
		), 'Reponse' );
	}
}
?>