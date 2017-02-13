<?php
require_once 'includes/php/lib/apache-log4php-2.3.0/src/main/php/Logger.php';
Logger::configure ( "configuration/log4php.xml" );
include_once 'includes/php/commons/Constantes.php';

/**
 *
 * @author m.emschwiller
 *        
 */
class ClassUtil {
	
	/**
	 *
	 * @var unknown
	 */
	protected static $logger = NULL;
	
	/**
	 * Constructeur de la classe.
	 */
	function __construct() {
	}
	protected static function logger() {
		if (self::$logger === NULL)
			self::$logger = Logger::getLogger ( __CLASS__ );
		return self::$logger;
	}
	
	/**
	 * convrtir une date en chaine de caractere
	 *
	 * @param unknown $date        	
	 * @param string $format        	
	 * @throws Exception si le format est NULL ou vide
	 * @return Ambigous <NULL, string>
	 */
	static function dateToString($sdate, $format = '') {
		$s_retour = null;
		if (empty ( $sdate )) {
			$s_retour = null;
		} else if (empty ( $format )) {
			throw new Exception ( 'le format est vide.' );
		} else {
			$s_retour = date_format ( new DateTime ( $sdate ), $format );
		}
		return $s_retour;
	}
	
	/**
	 *
	 * @param unknown $date_entre        	
	 * @return unknown
	 */
	static function getDateBdd($date_entre = '') {
		$d_date = null;
		if (! empty ( $date_entre )) {
			$date = DateTime::createFromFormat ( FORMAT_DATE_JJ_MM_AAAA, $date_entre );
			$d_date = $date->format ( FORMAT_DATE_AAAA_MM_JJ );
		}
		return $d_date;
	}
	
	/**
	 * Ajout $nb_jour a la date $date_entre
	 *
	 * @param unknown $date_entre        	
	 * @param unknown $nb_jour        	
	 */
	static function ajouterJourAUneDate($date_entre, $nb_jour) {
		if (empty ( $date_entre )) {
			throw new Exception ( 'La date est vide.' );
		}
		if (empty ( $nb_jour )) {
			throw new Exception ( 'Le nombre de jour est vide.' );
		}
		return date ( FORMAT_DATE_AAAA_MM_JJ, strtotime ( '+' . $nb_jour . ' day', strtotime ( $date_entre ) ) );
	}
	
	/**
	 * Retourne $s_string si la valeur n'est pas null ou videsinon retourne $s_defaut
	 *
	 * @param unknown $s_string        	
	 * @param string $s_defaut        	
	 * @return Ambigous <string, unknown>
	 */
	static function defautIfEmpty($s_string, $s_defaut = '') {
		$s_retour = '';
		if (empty ( $s_string )) {
			$s_retour = $s_defaut;
		} else {
			$s_retour = $s_string;
		}
		return $s_retour;
	}
	
	/**
	 * si la valeur est vide ou null, la fonction retour 'N', sinon elle retourne 'O'
	 * 
	 * @param string $s_string        	
	 * @return Ambigous <NULL, string>
	 */
	static function presentOuiSinonNon($s_string = '') {
		$s_retour = null;
		if (empty ( $s_string )) {
			$s_retour = 'N';
		} else {
			$s_retour = 'O';
		}
		return $s_retour;
	}
	
	/**
	 * verifie les droits des utilisateurs
	 *
	 * @param unknown $s_identifiant        	
	 * @param string $b_administrateur        	
	 * @param string $b_consultant        	
	 * @param string $b_sansDroit        	
	 * @return boolean
	 */
	static function verfierRoleIdentifiant($s_identifiant = '', $b_administrateur = false, $b_consultant = false) {
		$b_autorise = false;
		// on traite le fichier
		$row = 1;
		if (! $b_consultant && ! $b_administrateur) {
			// log
			self::logger ()->warn ( 'On test alors que ni ' . ROLE_ADMIN . ' ni ' . ROLE_CONSULTANT . ' n\'est autorise' );
		}
		if (($handle = fopen ( CHEMIN_FICHIER_DROIT_UTILISATEUR, "r" )) !== FALSE) {
			while ( ($data = fgetcsv ( $handle, 1000, CSV_CARACTERE_SEPARATEUR )) !== FALSE ) {
				if (self::logger ()->isDebugEnabled ()) {
					self::logger ()->debug ( 'Detail de la ligne ' . $row . '  : ' . $data [0] . ' <=> ' . $data [1] );
				}
				if ($s_identifiant == $data [0]) {
					// on est sur le bon utilisateur
					// si le flag $b_administrateur est a true, alors on verifie qu'il est administrateur
					if ($b_administrateur) {
						if (ROLE_ADMIN == $data [1]) {
							$b_autorise = true;
						} else {
							// log
							self::logger ()->trace ( 'L\'utilisateur ' . $s_identifiant . ' n\'est pas ' . ROLE_ADMIN );
						}
					}
					if ($b_consultant) {
						if (ROLE_CONSULTANT == $data [1]) {
							$b_autorise = true;
						} else {
							// log
							self::logger ()->trace ( 'L\'utilisateur ' . $s_identifiant . ' n\'est pas ' . ROLE_CONSULTANT );
						}
					}
				}
			}
			fclose ( $handle );
		} else {
			self::logger ()->warn ( 'Il n\'y a pas de ligne dans le fichier ' . CHEMIN_FICHIER_DROIT_UTILISATEUR );
		}
		
		if (! $b_autorise) {
			self::logger ()->info ( 'L\'utilisateur ' . $s_identifiant . ' n\'a pas les droits.' );
		}
		return $b_autorise;
	}
	
	/**
	 * retour <code>true</code> si la date 1 est strictement avant la date 2
	 * 
	 * @param Date $date1        	
	 * @param Date $date2        	
	 * @return boolean
	 * @throws Exception si une date est vide
	 */
	static function beforeDate($date1, $date2) {
		if (empty ( $date1 ) || empty ( $date2 )) {
			throw new Exception ( 'Une date est vide.' );
		}
		return $date1 < $date2;
	}
	
	/**
	 * creation d'un champ input type hidden
	 *
	 * @param string $name        	
	 * @param string $value        	
	 * @return string
	 */
	static function creerInputHidden($name = '', $value = '') {
		return '<input  type="hidden" value="' . $value . '" name="' . $name . '"  />';
	}
	
	/**
	 * creerInputText
	 *
	 * @param string $name        	
	 * @param string $value        	
	 * @param string $isReadonly        	
	 * @param string $isDisabled        	
	 * @return string
	 */
	static function creerInputText($name = '', $value = '', $isReadonly = false, $isDisabled = false, $isDate = false) {
		$s_html = '<input  type="text" value="' . $value . '" name="' . $name . '" ';
		
		if ($isReadonly) {
			$s_html .= ' readonly="true" ';
		}
		
		if ($isDisabled) {
			$s_html .= ' disabled="true" ';
		}
		
		if ($isDate) {
			$s_html .= ' class="contentDate" ';
		}
		$s_html .= ' />';
		// parceque en readonly le champ ne remonte pas au serveur
		if ($isReadonly) {
			$s_html .= self::creerInputHidden ( $name, $value );
		}
		return $s_html;
	}
	
	/**
	 * creation d'un champ file type hidden
	 *
	 * @param string $name        	
	 * @param string $value        	
	 * @return string
	 */
	static function creerInputFile($name = '') {
		return '<input  type="file" name="' . $name . '"  />';
	}
}
?>