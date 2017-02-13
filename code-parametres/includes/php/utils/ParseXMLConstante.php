<?php
include_once 'includes/php/commons/Constantes.php';
include_once 'includes/php/lib/apache-log4php-2.3.0/src/main/php/Logger.php';
Logger::configure ( "configuration/log4php.xml" );
/*
 *
 */
class ParseXMLConstante {
	private static $_instance;
	private static $a_clefValeur;
	protected static $logger = NULL;
	
	/**
	 * Empêche la création externe d'instances.
	 */
	private function __construct() {
	}
	
	/**
	 * Empêche la copie externe de l'instance.
	 */
	private function __clone() {
	}
	protected static function logger() {
		if (self::$logger === NULL)
			self::$logger = Logger::getLogger ( __CLASS__ );
		return self::$logger;
	}
	
	/**
	 * Renvoi de l'instance et initialisation si nécessaire.
	 */
	public static function getInstance() {
		if (! (self::$_instance instanceof self)) {
			self::$_instance = new self ();
			if (! isset ( self::$a_clefValeur ) || self::$a_clefValeur == null || empty ( self::$a_clefValeur )) {
				// alors on lit le fichier
				self::lireFichierXml ();
			}
		}
		return self::$_instance;
	}
	
	/**
	 *
	 * @param string $code_question        	
	 * @return multitype:QuestionXML
	 */
	private static function lireFichierXml() {
		$liste_question = array ();
		$dom = new DOMDocument ();
		$dom->load ( FICHIER_MIGRATION_CONSTANTE );
		// on verifie que le fichier est valide
		if ($dom->schemaValidate ( FICHIER_MIGRATION_CONSTANTE_XSD )) {
			self::logger ()->debug ( 'Le fichier xml ' . FICHIER_MIGRATION_CONSTANTE . ' est valide' );
			self::$a_clefValeur = array ();
			$dom_constantes = $dom->getElementsByTagName ( "constante" );
			foreach ( $dom_constantes as $dom_constante ) {
				$s_code = $dom_constante->getElementsByTagName ( "code" )->item ( 0 )->nodeValue;
				$s_valeur = $dom_constante->getElementsByTagName ( "valeur" )->item ( 0 )->nodeValue;
				self::$a_clefValeur [$s_code] = $s_valeur;
			}
		} else {
			self::logger ()->error ( 'Le fichier xml ' . FICHIER_MIGRATION_CONSTANTE . ' n\'est valide, avec les erreurs suivants :' );
			foreach ( libxml_get_errors () as $error ) {
				self::logger ()->error ( '[' . $error->level . '] : ' . $error->code . ' a la ligne ' . $error->line . ' avec le message "' . $error->message . '". ' );
				/*
				 * $error->level; // niveau $error->code; // le code $error->line; // la ligne $error->message; // le message
				 */
			}
		}
	}
	
	/**
	 *
	 * @param unknown $s_clef        	
	 * @return NULL
	 */
	public function getValeur($s_clef, $b_defaut = true) {
		self::logger ()->debug ( 'traite la clef ' . $s_clef );
		$s_retour = null;
		if (array_key_exists ( $s_clef, self::$a_clefValeur )) {
			self::logger ()->debug ( 'la clef existe dans le fichier ' );
			$s_retour = self::$a_clefValeur [$s_clef];
		} else if ($b_defaut) {
			$s_retour = constant ( $s_clef . CONSTANTE_DEFAUT );
			self::logger ()->warn ( 'la clef n\'existe pas dans le fichier ' . FICHIER_MIGRATION_CONSTANTE . ', la clef par defaut est utilisee ' . $s_clef . CONSTANTE_DEFAUT . ', avec la valeur ' . $s_retour );
		} else {
			$s_retour = $s_clef;
		}
		self::logger ()->debug ( 'retourne la valeur ' . $s_retour );
		return $s_retour;
	}
}
?>