<?php
require_once 'includes/php/lib/apache-log4php-2.3.0/src/main/php/Logger.php';
Logger::configure ( "configuration/log4php.xml" );
include_once 'includes/php/utils/ParseXMLConstante.php';

/**
 * Enter description here .
 * ..
 *
 * @author m.emschwiller
 *        
 *         http://wiki.hashphp.org/PDO_Tutorial_for_MySQL_Developers
 */
class AbstractDAO {
	protected $logger;
	private $db;
	
	/**
	 * Constructeur de la classe
	 */
	function __construct() {
		$this->logger = Logger::getLogger ( __CLASS__ );
		$this->logger->debug ( 'Dans le constructeur!' );
		if (empty ( $this->db )) {
			// Connection au serveur
			try {
				// Options de connection
				$options = array (
						PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
						PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION 
				);
				// Initialisation de la connection
				
				$this->db = new PDO ( 'mysql:host=' . ParseXMLConstante::getInstance ()->getValeur ( BDD_HOST ) . ';port=' . ParseXMLConstante::getInstance ()->getValeur ( BDD_DB_PORT ). ';dbname=' . ParseXMLConstante::getInstance ()->getValeur ( BDD_DB_NAME ), ParseXMLConstante::getInstance ()->getValeur ( BDD_USER ), ParseXMLConstante::getInstance ()->getValeur ( BDD_PASSWORD ), $options );
			} catch ( PDOException $p ) {
				throw $p;
			}
		}
	}
	
	/**
	 * recherche de l'ensemble des valeurs en fnction des parametres.
	 *
	 * la requete $query doit etre de la forme "SELECT * FROM table WHERE id=:id AND name=:name"
	 * les parametres $params doivent de la forme array(':name' => $name, ':id' => $id) ou NULL
	 *
	 * @param unknown $query        	
	 * @param string $params        	
	 * @return List<$class>
	 */
	public function fetchAllQuery($query = '', $params = array(), $class = '') {
		$stmt = $this->prepareEtExecuteRequete ( $query, $params );
		// Récupérer toutes les données retournées
		$arrAll = $stmt->fetchAll ( PDO::FETCH_CLASS, $class );
		return $arrAll;
	}
	
	/**
	 * supprime des données en BDD
	 *
	 * @param string $query        	
	 * @param string $params        	
	 */
	public function delete($query = '', $params = array()) {
		$stmt = $this->prepareEtExecuteRequete ( $query, $params );
		$this->logger->info ( 'la requete supprime ' . $stmt->rowCount () . ' ligne(s)' );
	}
	
	/**
	 * update des données en BDD
	 *
	 * @param string $query        	
	 * @param string $params        	
	 */
	public function update($query = '', $params = array()) {
		$stmt = $this->prepareEtExecuteRequete ( $query, $params );
		$this->logger->info ( 'la requete modifie ' . $stmt->rowCount () . ' ligne(s)' );
	}
	
	/**
	 * recherche de l'ensemble des valeurs en fnction des parametres.
	 *
	 * la requete $query doit etre de la forme "SELECT * FROM table WHERE id=:id AND name=:name"
	 * les parametres $params doivent de la forme array(':name' => $name, ':id' => $id) ou NULL
	 *
	 * @param unknown $query        	
	 * @param string $params        	
	 * @return List<$class>
	 */
	public function insert($query = '', $params = array()) {
		$this->prepareEtExecuteRequete ( $query, $params );
		$lastId = $this->db->lastinsertid ();
		return $lastId;
	}
	
	/**
	 * compte le nomnre de ligne
	 *
	 * @param string $query        	
	 * @param unknown $params        	
	 * @return nombre de ligne
	 */
	function countQuery($query = '', $params = array()) {
		$this->verifierPresenceRequete ( $query, $params );
		$stmt = $this->prepareEtExecuteRequete ( $query, $params );
		return $stmt->fetchColumn ( 0 );
	}
	
	/**
	 * prepare et execute la requete
	 *
	 * @param unknown $query        	
	 * @param unknown $params        	
	 * @return $stmt
	 */
	function prepareEtExecuteRequete($query, $params) {
		try {
			$this->verifierPresenceRequete ( $query, $params );
			$stmt = $this->db->prepare ( $query );
			if (empty ( $params )) {
				$stmt->execute ();
			} else {
				$stmt->execute ( $params );
			}
		} catch ( Exception $e ) {
			throw new Exception ( 'Une erreur est survenu lors de l\'execution de la requete : ' . "\n" . $this->construireMessageErreur ( $query, $params ), 0, $e );
		}
		return $stmt;
	}
	
	/**
	 * verifie la presence de la requete
	 *
	 * @param string $query        	
	 * @throws Exception
	 */
	function verifierPresenceRequete($query = '', $params = array()) {
		if (empty ( $query )) {
			$this->logger->error ( 'la requete n\'est pas presente' );
			throw new Exception ( 'la requete n\'est pas presente' );
		} else {
			if ($this->logger->isTraceEnabled ()) {
				$s_requete = $query;
				if ($params != null) {
					$s_requete .= ' avec les parametres suivants : ';
					foreach ( $params as $key => $value ) {
						$s_requete .= $key . ' <=> "' . $value . '" ';
					}
				}
				$this->logger->trace ( $s_requete );
			}
		}
	}
	
	/**
	 *
	 * @param unknown $s_query        	
	 * @param unknown $a_params        	
	 * @return string
	 */
	private function construireMessageErreur($s_query = '', $a_params = array()) {
		$s_retour = '';
		if (! empty ( $s_query )) {
			$s_retour = $s_query;
			if ($a_params != null) {
				$s_retour .= ' avec les parametres suivants : ';
				foreach ( $a_params as $key => $value ) {
					$s_retour .= $key . ' <=> "' . $value . '" ';
				}
			}
		}
		return $s_retour;
	}
}
?>