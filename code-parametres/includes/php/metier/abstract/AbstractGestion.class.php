<?php
require_once 'includes/php/lib/apache-log4php-2.3.0/src/main/php/Logger.php';
Logger::configure ( "configuration/log4php.xml" );
include_once 'includes/php/pojo/UserLDAP.php';
include_once 'includes/php/DAO/GestionCampagneDao.class.php';
include_once 'includes/php/commons/Constantes.php';
include_once 'includes/php/utils/ClassUtils.php';
include_once 'includes/php/pojo/QuestionXML.php';
require_once 'includes/php/pojo/ReponseXML.php';
require_once 'includes/php/pojo/GroupeXML.php';
include_once 'includes/php/utils/ParseXMLConstante.php';
/**
 *
 *
 * Enter description here ...
 *
 * @author m.emschwiller
 *        
 */
abstract class AbstractGestion {
	protected $idPage;
	protected $s_titre;
	private $s_erreur = '';
	protected $s_sous_titre_niv_un = '';
	protected $s_sous_titre_niv_deux = '';
	protected $o_managerDb;
	protected $o_managerDbGCDAO;
	
	/**
	 * Constructeur de la classe.
	 */
	function __construct() {
		try {
			$this->logger = Logger::getLogger ( __CLASS__ );
			if (isset ( $_POST [ID_PAGE] )) {
				$this->idPage = $_POST [ID_PAGE];
			}
			if (empty ( $this->idPage )) {
				$this->logger->debug ( 'id de la page par defaut' );
				$this->idPage = 1;
			}
			$this->logger->debug ( 'id de la page ' . $this->idPage );
			$this->o_managerDbGCDAO = new GestionCampagneDAO ();
		} catch ( Exception $e ) {
			$objDateTime = new DateTime ( 'NOW' );
			$s_codeMessag = $objDateTime->format ( FORMAT_DATE_ERREUR_TECHNIQUE );
			$this->s_erreur = '<div class="message negative" id="erreurs"> Une erreur technique est survenue, veuillez contacter l\'administrateur avec le num&eacute;ro suivant : ' . $s_codeMessag . '</div>';
			$this->logger->fatal ( 'Une erreur technique est survenue avec le code : ' . $s_codeMessag . "\nmessage : " . $e->getMessage () . "\nstack trace: " . $e->getTraceAsString () );
		}
	}
	
	/**
	 * Enter description here .
	 *
	 * ..
	 */
	function execute() {
		$s_html_corps = $this->s_erreur;
		if ('' == $s_html_corps) {
			try {
				$s_html_corps = $this->doExecute ();
			} catch ( Exception $e ) {
				$objDateTime = new DateTime ( 'NOW' );
				$s_codeMessag = $objDateTime->format ( FORMAT_DATE_ERREUR_TECHNIQUE );
				$s_html_corps .= '<div class="message negative" id="erreurs"> Une erreur technique est survenue, veuillez contacter l\'administrateur avec le num&eacute;ro suivant : ' . $s_codeMessag . '</div>';
				$this->logger->fatal ( 'Une erreur technique est survenue avec le code : ' . $s_codeMessag . "\nmessage : " . $e->getMessage () . "\nstack trace: " . $e->getTraceAsString () );
			}
		}
		$s_html = '<div id="page">' . $this->afficherTitre () . "\n";
		$s_html .= '<div id="soustitre">';
		$s_html .= '<br/>' . "\n" . '<p class="commentaire"><b>' . $this->s_sous_titre_niv_un . '</b></p>';
		$s_html .= '<br/>' . "\n" . '<p class="commentaire">' . $this->s_sous_titre_niv_deux . '</p>';
		$s_html .= '</div>' . $s_html_corps . '</div>' . "\n";
		return $s_html;
	}
	
	/**
	 * Enter description here .
	 *
	 *
	 *
	 *
	 * ..
	 */
	public abstract function doExecute();
	function afficherTitre() {
		return '<div id="header"><div id="head"><p class="grandtitre">' . $this->s_titre . '</p></div></div>' . "\n";
	}
	
	/**
	 * Affiche la liste des campagnes
	 *
	 * @param unknown $s_nom_Formulaire        	
	 * @param string $b_afficher_lien        	
	 * @return string
	 */
	protected function afficherListeCampagne($s_nom_Formulaire, $s_type_action, $b_afficher_lien = true) {
		// on recherche la liste des campagnes
		$listeCampagnes = $this->o_managerDbGCDAO->rechercherToutesCampagnes ();
		// ici les données utiles
		if (! isset ( $listeCampagnes ) || $listeCampagnes == null || empty ( $listeCampagnes )) {
			$this->logger->info ( 'la liste des campagnes est vide' );
			$s_html = 'Il n\'y a pas de données dans la table ' . BDD_TABLE_CAMPAGNE;
		} else {
			$s_html = '<table id="datatables" class="display">';
			$s_html .= '<thead><tr><th>' . CAMPAGNE_ACCUEIL_TABLEAU_COL_NUMERO . '</th>';
			$s_html .= '<th>' . CAMPAGNE_ACCUEIL_TABLEAU_COL_DESCRIPTION . '</th>';
			$s_html .= '<th>' . CAMPAGNE_ACCUEIL_TABLEAU_COL_DATE_DEPART . '</th>';
			$s_html .= '<th>' . CAMPAGNE_ACCUEIL_TABLEAU_COL_DATE_BUTOIR . '</th>';
			$s_html .= '<th>' . CAMPAGNE_ACCUEIL_TABLEAU_COL_DATE_MIGRATION . '</th>';
			$s_html .= '<th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th></tr></thead>';
			$s_html .= '<tbody>';
			foreach ( $listeCampagnes as $uneCampagne ) {
				$s_html .= '<tr><td>' . $uneCampagne->getNumero () . '</td>
						        <td>' . $uneCampagne->getDescription () . '</td>
						        <td><span class="champCache">' .$uneCampagne->getDateDebut ().'</span>'. ClassUtil::dateToString ( $uneCampagne->getDateDebut (), FORMAT_DATE_JJ_MM_AAAA ) . '</td>
						        <td><span class="champCache">' .$uneCampagne->getDateButoir ().'</span>'. ClassUtil::dateToString ( $uneCampagne->getDateButoir (), FORMAT_DATE_JJ_MM_AAAA ) . '</td>
						        <td><span class="champCache">' .$uneCampagne->getDateMigration ().'</span>'. ClassUtil::dateToString ( $uneCampagne->getDateMigration (), FORMAT_DATE_JJ_MM_AAAA ) . '</td>';
				
				$s_html .= '<td><a class="detailCampagne" href="#"><img src="includes/images/voir.png"/></a>';
				// suppresion si la date du jour est < à la date de départ
				if ($b_afficher_lien) {
					if (null == $uneCampagne->getDateDebut () || ClassUtil::beforeDate ( new DateTime (), new DateTime ( $uneCampagne->getDateDebut () ) )) {
						$s_html .= '&nbsp;<a class="supprimerCampagne" href="#" ><img src="includes/images/supprimer.png"/></a>';
					}
				}
				$s_html .= '</td>';
				
				$s_html .= '</tr>';
			}
			$s_html .= '</tbody>';
			$s_html .= '</table>';
		}
		$s_html .= '<form name="' . $s_nom_Formulaire . '" method="post" action="' . PAGE_PHP_ACTION . '">';
		$s_html .= '<input type="hidden" value="' . $s_type_action . '" name="' . TYPE_ACTION . '" />';
		$s_html .= '<input type="hidden" value="" name="' . ID_NUMERO_CAMPAGNE . '" />';
		if ($b_afficher_lien) {
			$s_html .= '<input type="hidden" value="' . PAGE_AJOUTER_CAMPAGNE . '" name="' . ID_PAGE . '" />';
			$s_html .= '<input type="submit" value="' . CAMPAGNE_ACTION_AJOUTER . '" />';
		} else {
			$s_html .= '<input type="hidden" value="1" name="' . ID_PAGE . '" />';
			$s_html .= '<input type="submit" value="Retour" />';
		}
		$s_html .= '</form>';
		return $s_html;
	}
	
	/**
	 * afficher une campagne
	 *
	 * @param string $eCampagne        	
	 * @param string $isReadonly        	
	 * @param string $isDisabled        	
	 * @return string
	 */
	protected function afficherCampagne($eCampagne = null, $isReadonly = false, $isDisabled = false) {
		if ($eCampagne == null) {
			$campagne = new Campagne ();
		} else {
			$campagne = $eCampagne;
		}
		$delaiAvantRelance = $campagne->getDelaiAvantRelance ();
		if (empty ( $delaiAvantRelance )) {
			$campagne->setDelaiAvantRelance ( ParseXMLConstante::getInstance ()->getValeur ( DELAI_AVANT_RELANCE_DEFAUT ) );
		}
		$s_html = '<div id="detailCampagne">';
		$s_html .= ClassUtil::creerInputHidden ( CAMPAGNE_INPUT_NAME_NUMERO, $campagne->getNumero () ) . "\n";
		$s_html .= '<label for="description">' . CAMPAGNE_DETAIL_DESCRIPTION . '</label>' . ClassUtil::creerInputText ( CAMPAGNE_INPUT_NAME_DESCRIPTION, $campagne->getDescription (), $isReadonly, $isDisabled ) . '<br/>' . "\n";
		$s_html .= '<label for="dateDep">' . CAMPAGNE_DETAIL_DATE_DEPART . '</label>' . ClassUtil::creerInputText ( CAMPAGNE_INPUT_NAME_DATE_DEPART, ClassUtil::dateToString ( $campagne->getDateDebut (), FORMAT_DATE_JJ_MM_AAAA ), $isReadonly, $isDisabled, true ) . ' <br/>' . "\n";
		$s_html .= '<label for="dateBut">' . CAMPAGNE_DETAIL_DATE_BUTOIR . '</label>' . ClassUtil::creerInputText ( CAMPAGNE_INPUT_NAME_DATE_BUTOIR, ClassUtil::dateToString ( $campagne->getDateButoir (), FORMAT_DATE_JJ_MM_AAAA ), $isReadonly, $isDisabled, true ) . ' <br/>' . "\n";
		$s_html .= '<label for="dateMig">' . CAMPAGNE_DETAIL_DATE_MIGRATION . '</label>' . ClassUtil::creerInputText ( CAMPAGNE_INPUT_NAME_DATE_MIGRATION, ClassUtil::dateToString ( $campagne->getDateMigration (), FORMAT_DATE_JJ_MM_AAAA ), $isReadonly, $isDisabled, true ) . ' <br/>' . "\n";
		$s_html .= '<label for="delai">' . CAMPAGNE_DETAIL_DELAI_AVANT_RELANCE . '</label>' . ClassUtil::creerInputText ( CAMPAGNE_INPUT_NAME_DELAI, $campagne->getDelaiAvantRelance (), $isReadonly, $isDisabled ) . "\n";
		$s_html .= '</div>' . "\n";
		return $s_html;
	}
	
	/**
	 *
	 * @param unknown $campagne        	
	 * @return string
	 */
	protected function afficherTableAgent($campagne, $b_suppression = true) {
		$agents_campagne = $this->o_managerDbGCDAO->rechercherListeAgents ( $campagne->getNumero () );
		$s_html = '<input type="hidden" name="' . INPUT_ID_AGENT . '" value=""/> <table id="datatablesAgents" class="display" >';
		$s_html .= '<thead><tr><th>' . CAMPAGNE_DETAIL_TABLEAU_COL_IDENTIFIANT . '</th>';
		$s_html .= '<th>' . CAMPAGNE_DETAIL_TABLEAU_COL_NOM . '</th>';
		$s_html .= '<th>' . CAMPAGNE_DETAIL_TABLEAU_COL_PRENOM . '</th>';
		if ($b_suppression) {
			$s_html .= '<th></th>';
		}
		$s_html .= '</tr></thead>';
		$s_html .= '<tbody>';
		if (! isset ( $agents_campagne ) || $agents_campagne == null || empty ( $agents_campagne )) {
			$this->logger->info ( 'la liste des agents campagnes est vide' );
		} else {
			foreach ( $agents_campagne as $unAgentCampagne ) {
				$utilisateur = $this->rechercheParIdentifiant ( $unAgentCampagne->getIdAgent () );
				$s_html .= '<tr><td>' . $utilisateur->getIdentifiant () . '</td><td>' . $utilisateur->getNom () . '</td><td>' . $utilisateur->getPrenom () . '</td>';
				if ($b_suppression) {
					$s_html .= '<td>';
					if (null == $campagne->getDateDebut () || ClassUtil::beforeDate ( new DateTime (), new DateTime ( $campagne->getDateDebut () ) )) {
						$s_html .= '<a href="#" class="supprimerAgent"><img src="includes/images/supprimer.png"/></a>';
					}
					$s_html .= '</td>';
				}
				$s_html .= '</tr>';
			}
		}
		$s_html .= '</tbody>';
		$s_html .= '</table>';
		return $s_html;
	}
	
	/**
	 *
	 * @return string
	 */
	function detailCampagne($s_nom_Formulaire, $s_action, $b_suppression = true) {
		$this->logger->info ( 'detail d\'une campagne' );
		$n_numero = '';
		if (isset ( $_POST [ID_NUMERO_CAMPAGNE] )) {
			$n_numero = $_POST [ID_NUMERO_CAMPAGNE];
		} else if (isset ( $_POST [CAMPAGNE_INPUT_NAME_NUMERO] )) {
			$n_numero = $_POST [CAMPAGNE_INPUT_NAME_NUMERO];
		} else {
			$this->logger->warn ( 'Dans detail la campagne, mais y\'a pas de numero de campagne' );
		}
		
		// on recherche la liste des campagnes
		$campagne = $this->o_managerDbGCDAO->rechercherUneCampagneParNumero ( $n_numero );
		$this->s_sous_titre_niv_un = SOUS_TITRE_UN_GESTION_CAMPAGNE_DETAIL . $campagne->getNumero ();
		$s_html = '<div><form name="' . $s_nom_Formulaire . '" method="post" action="' . PAGE_PHP_ACTION . '">';
		if ($campagne != null) {
			// detail de la campagne
			$s_html .= $this->afficherCampagne ( $campagne, true, true );
			// les acteurs
			$s_html .= '<div id="listeAgentCampagne">' . CAMPAGNE_CREER_LISTE_AGENTS . '<p>';
			$s_html .= '</p>' . $this->afficherTableAgent ( $campagne, $b_suppression );
			$s_html .= '</div>';
			// ajout des boutons Modifier et retour à la liste
			$s_html .= '<div><input type="hidden" value="' . $s_action . '" name="' . TYPE_ACTION . '" />';
			
			$s_html .= '<input type="hidden" value="' . $campagne->getNumero () . '" name="' . ID_NUMERO_CAMPAGNE . '" />';
			// si on ne peut pas la suprimer on ne peut pas la modifier
			if ($b_suppression && (null == $campagne->getDateDebut () || ClassUtil::beforeDate ( new DateTime (), new DateTime ( $campagne->getDateDebut () ) ))) {
				$s_html .= '<input type="submit" value="' . CAMPAGNE_ACTION_MODIFIER . '"/>';
			}
			$s_class = '';
			$s_page = '';
			if (! $b_suppression) {
				$s_page = '5';
			} else {
				$s_class = 'class="listeCampagnes"';
				$s_page = PAGE_MODIFIER_CAMPAGNE;
			}
			$s_html .= '<input type="submit" value="' . CAMPAGNE_ACTION_RETOUR_A_LA_LISTE . '" ' . $s_class . ' />';
			$s_html .= '<input type="hidden" value="' . $s_page . '" name="' . ID_PAGE . '" />';
			$s_html .= '</div></form>';
			// fermeture du block
			$s_html .= '</div>';
		}
		return $s_html;
	}
	
	/**
	 *
	 * @param string $code_question
	 * @return multitype:QuestionXML
	 */
	function lireFichierXml($code_question = '') {
		$liste_question = array ();
		$dom = new DOMDocument ();
		$dom->load ( FICHIER_MIGRATION_QUESTION );
		// on verifie que la fichier est valide
		if ($dom->schemaValidate ( FICHIER_MIGRATION_QUESTION_XSD )) {
			$this->logger->debug ( 'Le fichier xml ' . FICHIER_MIGRATION_QUESTION . ' est valide' );
			$dom_questions = $dom->getElementsByTagName ( "question" );
			$nb_question = 0;
			$nb_etape_session = 1; // on commence a 1 car il faut prendre en compte le recaptitulatif
			foreach ( $dom_questions as $dom_question ) {
				$nb_etape_session ++;
				$s_code = $dom_question->getElementsByTagName ( "code" )->item ( 0 )->nodeValue;
				if ($dom_question->getElementsByTagName ( "libelle_niveau2" )->length != 0) {
					$s_libelle_niveau2 = $dom_question->getElementsByTagName ( "libelle_niveau2" )->item ( 0 )->nodeValue;
				} else {
					$s_libelle_niveau2 = '';
				}
				// creation de la question
				$bean_question = new QuestionXML ( $s_code, $dom_question->getElementsByTagName ( "libelle" )->item ( 0 )->nodeValue, $s_libelle_niveau2, $dom_question->getElementsByTagName ( "titre" )->item ( 0 )->nodeValue, $dom_question->getElementsByTagName ( "nbQuestionColonne" )->item ( 0 )->nodeValue, $dom_question->getElementsByTagName ( "libelle_recapitulatif" )->item ( 0 )->nodeValue );
				// on ajoute la question si le code question n'est pas defini ou si le code de la question est égale au numéroe de question
				$dom_reponses = $dom_question->getElementsByTagName ( "reponse" );
				if (! isset ( $code_question ) || empty ( $code_question ) || $code_question == $s_code) {
					$this->logger->debug ( 'Ajout de la question ' . $s_code . ' a la liste des questions' );
					// on parcour les reponses
					foreach ( $dom_reponses as $dom_repo ) {
						$idReponse = $dom_repo->getElementsByTagName ( "code" )->item ( 0 )->nodeValue;
						if ($dom_repo->getElementsByTagName ( "actif" )->length != 0) {
							$s_actif = $dom_repo->getElementsByTagName ( "actif" )->item ( 0 )->nodeValue;
							$this->logger->debug ( 'la reponse ' . $idReponse . ' a l\'attribut actif de renseigne : ' . $s_actif );
						} else {
							$s_actif = 1;
							$this->logger->debug ( 'la reponse ' . $idReponse . ' est active car l\'attibut n\'est pas present' );
						}
						$s_lib_reponse = $dom_repo->getElementsByTagName ( "libelle" )->item ( 0 )->nodeValue;
						$_SESSION [SESSION_LIBELLE_REPONSE] [$s_code . '_' . $idReponse] = $s_lib_reponse;
						$r_reponse = new ReponseXML ( $idReponse, $s_lib_reponse, $s_actif );
						$dom_groupes = $dom_repo->getElementsByTagName("groupe");
						if ($dom_groupes->length != 0) { 
							for($i=0; $i<$dom_groupes->length; $i++) {
								$r_reponse->addGroupe(new GroupeXML($dom_groupes->item ( $i )->nodeValue));
							}
						}
						$bean_question->addReponse ($r_reponse );
					}
					$liste_question [$nb_question] = $bean_question;
					$nb_question ++;
				}
			}
		} else {
			$this->logger->error ( 'Le fichier xml ' . FICHIER_MIGRATION_QUESTION . ' n\'est valide, avec les erreurs suivants :' );
			foreach ( libxml_get_errors () as $error ) {
				$this->logger->error ( '[' . $error->level . '] : ' . $error->code . ' a la ligne ' . $error->line . ' avec le message "' . $error->message . '". ' );
				/*
				 * $error->level; // niveau $error->code; // le code $error->line; // la ligne $error->message; // le message
				*/
			}
		}
		$_SESSION [NOMBRE_ETAPE_TOTAL] = $nb_etape_session;
		return $liste_question;
	}
	
	/**
	 * Creation de l'entete de mail
	 *
	 * @param string $s_from
	 * @return string l'entete du mail
	 */
	protected function creationHeaderMail($s_from = '') {
		$header = "From: " . $s_from . "\n";
		$header .= "MIME-Version: 1.0" . "\n" . "Content-Type: text/plain; charset=UTF-8" . "\n";
		$header .= "Content-Type: multipart/alternative;" . "\n" . "\n";
		return $header;
	}
	
	/**
	 * Recherche le template de mail
	 *
	 * @param string $s_nom_fichier
	 */
	protected function rechercheTemplateMail($s_nom_fichier = '') {
		$handle = fopen ( $s_nom_fichier, "ru" );
		$s_message_template = '';
		while ( $line = fgets ( $handle ) ) {
			$s_message_template .= utf8_decode ( $line );
		}
		fclose ( $handle );
		return $s_message_template;
	}
	
	/**
	 * recherche dans le LDAP un utilisateur en fonction de son ID
	 * @param unknown $s_identifiant        	
	 * @return UserLDAP
	 */
	protected function rechercheParIdentifiant($s_identifiant) {
		$utilisateurLdap = new UserLDAP();
		ParseXMLConstante::getInstance ()->getValeur ( LDAP_SERVER );
		// La séquence de base avec LDAP est connexion, liaison, recherche, interprétation du résultat déconnexion
		$this->logger->debug('Connexion au serveur LDAP');
		$ds = ldap_connect ( ParseXMLConstante::getInstance ()->getValeur ( LDAP_SERVER ), ParseXMLConstante::getInstance ()->getValeur ( LDAP_PORT) );
		// doit être un serveur LDAP valide !
		if ($ds) {
			$this->logger->debug( 'connexion en cours');
			ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, ParseXMLConstante::getInstance ()->getValeur ( LDAP_VERSION ));
			ldap_set_option($ds, LDAP_OPT_REFERRALS, 0);
			$r = ldap_bind ( $ds, ParseXMLConstante::getInstance ()->getValeur ( LDAP_USER ), ParseXMLConstante::getInstance ()->getValeur ( LDAP_MDP ) );
			// connexion anonyme, typique
			// pour un accès en lecture seule.
			$this->logger->debug('Le résultat de connexion est ' . $r );
			// Recherche par nom
			$sr = ldap_search ( $ds, ParseXMLConstante::getInstance ()->getValeur ( LDAP_DC ) ,  ParseXMLConstante::getInstance ()->getValeur (LDAP_CHAMP_IDENTIFIANT)."=".$s_identifiant);
			// $sr = ldap_search ( $ds, /*ParseXMLConstante::getInstance ()->getValeur ( LDAP_DC ) */ '*',  /*ParseXMLConstante::getInstance ()->getValeur (LDAP_CHAMP_IDENTIFIANT).'='.$s_identifiant*/ "*");
			$this->logger->debug($sr);
			$this->logger->debug( 'Le nombre d\'entrées retournées est ' . ldap_count_entries ( $ds, $sr ));
			$info = ldap_get_entries ( $ds, $sr );
			if ($info ["count"] == 1) {
				if (isset($info [0] [ParseXMLConstante::getInstance ()->getValeur (LDAP_CHAMP_MAIL)])) {
					$utilisateurLdap->setEmail($info [0] [ParseXMLConstante::getInstance ()->getValeur (LDAP_CHAMP_MAIL)] [0]);
				}
				$utilisateurLdap->setIdentifiant($s_identifiant);
				$utilisateurLdap->setNom($info [0] [ParseXMLConstante::getInstance ()->getValeur (LDAP_CHAMP_NOM)] [0]);
				$utilisateurLdap->setPrenom($info [0] [ParseXMLConstante::getInstance ()->getValeur (LDAP_CHAMP_PRENOM)] [0]);
			} else {
				$this->logger->error('l\'utilisateur '.$s_identifiant.' n\'existe pas ');
			}
			ldap_close ( $ds );
		} else {
			$this->logger->error('Impossible de se connecter au serveur LDAP');
		}
		return $utilisateurLdap;
	}
}
?>