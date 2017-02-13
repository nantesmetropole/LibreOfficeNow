<?php
include_once 'includes/php/metier/abstract/AbstractGestion.class.php';
include_once 'includes/php/DAO/GestionAgentCampagneDao.class.php';
include_once 'includes/php/DTO/CampagneDTO.class.php';
include_once 'includes/php/DTO/ReponseDTO.class.php';
include_once 'includes/php/commons/Constantes.php';

include_once 'includes/php/lib/apache-log4php-2.3.0/src/main/php/Logger.php';
include_once 'includes/php/pojo/QuestionXML.php';
require_once 'includes/php/pojo/ReponseXML.php';

Logger::configure ( "configuration/log4php.xml" );

define ( "NAME_GESTION_MIGRATION", "gestionMigration" );

define ( "PAGE_AJOUTER_QUESTION", "2" );
define ( "PAGE_FIN_MIGRATION", "3" );
define ( "PAGE_VALIDER_RA", "6" );
//
define ( "NB_PAGE_PREMIERE_MIGRATION", 5 );
define ( "NB_PAGE_RA", 4 );

/**
 *
 *
 *
 * Enter description here ...
 *
 * @author m.emschwiller
 *        
 */
class GestionMigration extends AbstractGestion {
	
	/**
	 * le pourcentage d'avancement de la campagne
	 *
	 * @var unknown
	 */
	private $i_pourcentageAvancement;
	
	/**
	 * affiche ou non la barre de progression
	 * 
	 * @var unknown
	 */
	private $b_pourcentageAv;
	
	/**
	 * Constructeur de la classe
	 */
	function __construct() {
		parent::__construct ();
		$this->logger = Logger::getLogger ( __CLASS__ );
		$this->logger->debug ( 'Dans le constructeur!' );
		// instance de BDD
		$this->o_managerDb = new GestionAgentCampagneDAO ();
		// ....
		$this->s_titre = TITRE_MIGRATION;
		$this->b_pourcentageAv = true;
	}
	
	/**
	 * Enter description here .
	 */
	function doExecute() {
		$s_html = '';
		switch ($this->idPage) {
			case 1 :
				$s_html .= $this->accueil ();
				break;
			case PAGE_AJOUTER_QUESTION :
				$this->enregistrerReponseSession ();
				$s_html .= $this->reponseQuestion ();
				break;
			case PAGE_FIN_MIGRATION :
				$s_html .= $this->affichageFin ();
				break;
			case 4 :
				$s_html .= $this->retourArriere ();
				break;
			case PAGE_VALIDER_RA :
				if ('' == trim ( $_POST [LIBELLE_RA] )) {
					$s_html .= '<div class="message negative" id="erreurs"> <ul><li>La saisie de la r&eacute;ponse est obligatoire</li></ul></div>';
					$_SESSION [PAGE_EN_COURS] = $_SESSION [PAGE_EN_COURS] - 1;
					$s_html .= $this->retourArriere ();
				} else {
					$s_html .= $this->rARecap ();
				}
				break;
			default :
				$this->logger->warn ( 'Page par defaut' );
				$s_html = $this->accueil ();
				break;
		}
		if ($this->b_pourcentageAv) {
			$s_html .= $this->avancement ();
		}
		return $s_html;
	}
	
	/**
	 * gestion de l'avancement
	 *
	 * @return string la div de gestion de l'avancement
	 */
	function avancement() {
		$i_pourcentage = 100 * ($_SESSION [PAGE_EN_COURS] / $_SESSION [PAGE_TOTAL]);
		return "\n" . '<br/><div id="progressbar" style="display:inline-block; width:50%;"><input type="hidden" value="' . $i_pourcentage . '" name="progressbarVal"/></div><br/>';
	}
	
	/**
	 * page d'acceuil pour les réponses aux questions
	 *
	 * @return string
	 */
	function accueil() {
		$this->logger->info ( 'creation de l\'accueil' );
		$_SESSION [SESSION_QUESTION_REPONSE] = array ();
		$_SESSION [SESSION_LIBELLE_REPONSE] = array ();
		$idAgent = $_SESSION [ID_AGENT_CONNECTER];
		$userLdap = $this->rechercheParIdentifiant ( $idAgent );
		$s_html = '';
		if ($userLdap == null || empty($userLdap) || $userLdap->getIdentifiant() == null || trim($userLdap->getIdentifiant()) == '') { 
			$s_html .= '<p class="commentaire">Bonjour ' . $idAgent . ', vous n\'etes pas dans le LDAP</p>';
		} else {
			$s_html .= '<p class="commentaire">Bonjour ' . $userLdap->getPrenom () . ' ' . $userLdap->getNom () . ',</p>';
			// l'agent est-il affecté a une campagne ?
			$agentsCampagne = $this->o_managerDb->rechercherListeAgentCampagneParAgent ( $idAgent );
			
			$reponses = $this->o_managerDb->rechercherListeReponseParAgent ( $idAgent );
			$s_html .= '<form name="' . NAME_GESTION_MIGRATION . '" method="post" action="' . PAGE_PHP_ACTION . '">';
			$s_html .= '<input type="hidden" value="' . ACTION_GESTION_MIGRATION . '" name="' . TYPE_ACTION . '" />';
			if ($agentsCampagne == null || empty ( $agentsCampagne ) || $reponses == null || empty ( $reponses )) {
				// s'il n'y a pas encore d'agentcampagne => volontaire => on cree l'agentcampagne
				if ($agentsCampagne == null || empty ( $agentsCampagne )) {
					$_SESSION [ID_AGENT_CAMPAGNE] = null;
					$_SESSION [LIBELLE_DATE_MIGRATION] = ' sous 24h';
				} else {
					$_SESSION [ID_AGENT_CAMPAGNE] = $agentsCampagne [0]->getId ();
					if ($agentsCampagne [0]->getIdCampagne () == null) {
						$_SESSION [LIBELLE_DATE_MIGRATION] = ' sous 24h';
					} else {
						$uneCampagne = $this->o_managerDbGCDAO->rechercherUneCampagneParNumero ( $agentsCampagne [0]->getIdCampagne () );
						$_SESSION [LIBELLE_DATE_MIGRATION] = ' le ' . ClassUtil::dateToString ( $uneCampagne->getDateMigration (), FORMAT_DATE_JJ_MM_AAAA );
					}
				}
				$_SESSION [PAGE_EN_COURS] = 1;
				$_SESSION [NUMERO_QUESTION] = 0;
				$_SESSION [PAGE_TOTAL] = NB_PAGE_PREMIERE_MIGRATION;
				$this->logger->debug ( 'L\'agent ' . $idAgent . ' n\'a pas de campagne rattache ou n\'a pas encore repondu' );
				$_SESSION[LECTURE_FICHIER_CSV] = true;
				// migration volontaire
				$s_html .= '<br/><p class="commentaire ligne">' . MESSAGE_INFO_MIGATION . '</p><br/>';
				$s_html .= '<input type="submit" value="' . MIGRATION_ACTION_SUIVANT . '" />';
				$s_html .= '<input type="hidden" value="' . PAGE_AJOUTER_QUESTION . '" name="' . ID_PAGE . '" />';
			} else {
				$_SESSION [LIBELLE_DATE_MIGRATION] = ' manque une info';
				$this->logger->debug ( 'L\'agent ' . $idAgent . ' est rattache a une ou plusieurs campagne ' );
				
				// on prend la premiere reponse pour avoir l'id agentcampagne
				$_SESSION [ID_AGENT_CAMPAGNE] = $reponses [0]->getIdAgentCampagne ();
				$agentCampagne_unAgentCampagne = $this->o_managerDb->rechercherAgentCampagneParId ( $_SESSION [ID_AGENT_CAMPAGNE] );
				
				// si y'a un id de campagne on recherche la campagne
				// sinon on prend la date de creation + 24h + 30 jours
				$d_dateMigration = null;
				if ($agentCampagne_unAgentCampagne->getIdCampagne () == null) {
					$d_dateMigration = ClassUtil::ajouterJourAUneDate ( $agentCampagne_unAgentCampagne->getDateCreation (), 1 );
				} else {
					$uneCampagne = $this->o_managerDbGCDAO->rechercherUneCampagneParNumero ( $agentsCampagne [0]->getIdCampagne () );
					$d_dateMigration = $uneCampagne->getDateMigration ();
				}	
				$_SESSION [LIBELLE_DATE_MIGRATION] = ' le ' . ClassUtil::dateToString ( $d_dateMigration, FORMAT_DATE_JJ_MM_AAAA );
				$this->logger->debug ( 'migration prevu '.$_SESSION [LIBELLE_DATE_MIGRATION] );
				if (ClassUtil::beforeDate ( new DateTime (), new DateTime ( ClassUtil::ajouterJourAUneDate ( $d_dateMigration, ParseXMLConstante::getInstance ()->getValeur ( OPTION_NB_JOURS_REMPLISSAGE_QUESTIONNAIRE ) ) ) )) {
					$s_html .= '<br/><p class="commentaire ligne">' . MESSAGE_INFO_DEJA_PASSE . '</p>';
					$s_html .= '<br/><input type="submit" value="' . MIGRATION_ACTION_TROMPE . '" class="migrationTrompe" />';
				} else {
					$this->b_pourcentageAv = false;
					$s_html .= '<br/><p class="commentaire ligne">' . MESSAGE_INFO_MIGRATION_ACHEVEE . '</p>';
				}
				
				if (0 == strcasecmp ( 'oui', ParseXMLConstante::getInstance ()->getValeur ( ACTIVE_RETOUR_ARRIERE ) )) {
					$s_html .= '<br/><input type="submit" value="' . MIGRATION_ACTION_SOUHAITE_REVENIR_ARRIERE . '" class="retourArriereRevenir" />';
				}
				$s_html .= '<input type="hidden" value="" name="' . ID_PAGE . '" />';
				
				$_SESSION [PAGE_EN_COURS] = 1;
				$_SESSION [NUMERO_QUESTION] = 0;
				$_SESSION [PAGE_TOTAL] = NB_PAGE_RA;
			}
			$s_html .= '</form>';
		}
		return $s_html;
	}
	
	/**
	 * Affiche le message de fin
	 *
	 * @return unknown
	 */
	function affichageFin() {
		$this->logger->info ( 'Page de fin ' );
		// on creer l'agent campagne s'il n'y en a pas
		if (! isset ( $_SESSION [ID_AGENT_CAMPAGNE] ) || empty ( $_SESSION [ID_AGENT_CAMPAGNE] )) {
			$agentsCampagne = new AgentCampagne ();
			$agentsCampagne->setIdAgent ( $_SESSION [ID_AGENT_CONNECTER] );
			$_SESSION [ID_AGENT_CAMPAGNE] = $this->o_managerDbGCDAO->creerAgentCampagne ( $agentsCampagne );
		} else {
			// l'agent campagne existe, il faut le prendre en compte dans les batch, on supprime la date de migration
			 $this->o_managerDbGCDAO->modifierDateMigrationAgentCampagne( $_SESSION [ID_AGENT_CAMPAGNE] );
		}
		// on enregistre les reponses
		$this->enregistrerReponse ();
		$_SESSION [PAGE_EN_COURS] = $_SESSION [PAGE_TOTAL];
		$s_html = '<p class="commentaire ligne">Votre demande a bien &eacute;t&eacute; enregistr&eacute;e, vous allez recevoir un email d&prime;information vous pr&eacute;cisant les dispositifs d&prime;aide disponibles.</p>';
		$s_html .= '</br><p class="commentaire ligne">La configuration de votre poste de travail interviendra ' . $_SESSION [LIBELLE_DATE_MIGRATION] . ',</p>';
		$s_html .= '</br><p class="commentaire ligne">Cordialement,</p>';
		$s_html .= '<p class="commentaire ligne">Les &eacute;quipes de DGRN</p>';
		$s_html .= '<input type="hidden" value="" name="' . ID_PAGE . '" />';
		// on vide la session
		$_SESSION [SESSION_QUESTION_REPONSE] = array ();
		$_SESSION [SESSION_LIBELLE_REPONSE] = array ();
		$s_object = ParseXMLConstante::getInstance ()->getValeur ( OBJET_EMAIL_RECAP );
		$s_header = $this->creationHeaderMail ( ParseXMLConstante::getInstance ()->getValeur ( FROM_EMAIL_RECAP ) );
		$s_message_template = $this->rechercheTemplateMail ( FICHIER_MAIL_RECAP );
		$patterns = array ();
		$patterns [0] = '/##date_migration##/';
		$patterns [1] = '/##liste_url##/';
		$replacements = array ();
		$replacements [0] = $_SESSION [LIBELLE_DATE_MIGRATION];
		$s_liste_url = '';
		foreach ($_SESSION [LISTE_GROUPE_AGENT] as $g_id_groupe) {
			$s_val_retour = utf8_decode(ParseXMLConstante::getInstance ()->getValeur ( $g_id_groupe."_URL", false));
			if ($g_id_groupe."_URL" == $s_val_retour) {
				$this->logger->debug("pas de valeur pour la clef ".$s_val_retour);
			} else {
				$s_liste_url .= $s_val_retour."\n";
			}
		}
		$replacements [1] = $s_liste_url;
		$s_message = preg_replace ( $patterns, $replacements, $s_message_template );
		$o_agent = $this->rechercheParIdentifiant ( $_SESSION [ID_AGENT_CONNECTER] );
		mail ( $o_agent->getEmail (), $s_object, $s_message, $s_header );
		return $s_html;
	}
	
	/**
	 * Permet d'enregistrer les reponses à la question
	 * le traitement est le suivant, suppression des reponses existantes pour la question, puis enregistrement des nouvelles reponses
	 */
	function enregistrerReponse() {
		$this->logger->info ( 'enregistre les reponses aux questions' );
		if (isset ( $_SESSION [SESSION_QUESTION_REPONSE] )) {
			foreach ( $_SESSION [SESSION_QUESTION_REPONSE] as $i_numeroQuestion => $a_reponses ) {
				// on supprime la liste des réponses de la question
				$this->o_managerDb->supprimeReponseParAgentCampagneEtQuestion ( $_SESSION [ID_AGENT_CAMPAGNE], $i_numeroQuestion );
				// on enregistre les réponses
				foreach ( $a_reponses as $i_numeroReponse => $b_valuerReponse ) {
					// la valeur est cochée
					if ($b_valuerReponse) {
						$re = new Reponse ();
						$re->setNumeroQuestion ( $i_numeroQuestion );
						$re->setIdAgentCampagne ( $_SESSION [ID_AGENT_CAMPAGNE] );
						if (ID_RETOUR_ARRIERE == $i_numeroQuestion) {
							// on est sur le retour arriere donc pas d'enregistrement
							$re->setIdReponse ( ID_RETOUR_ARRIERE );
						} else {
							$re->setIdReponse ( $i_numeroReponse );
						}
						if (array_key_exists ( $i_numeroQuestion . '_' . $i_numeroReponse, $_SESSION [SESSION_LIBELLE_REPONSE] )) {
							$re->setLibelleReponse ( $_SESSION [SESSION_LIBELLE_REPONSE] [$i_numeroQuestion . '_' . $i_numeroReponse] );
						}
						$this->o_managerDb->insertReponse ( $re );
					}
				}
			}
		} else {
			$this->logger->debug ( 'Rien a enregistrer' );
		}
	}
	
	/**
	 * la gestion du retour arriere
	 *
	 * @return string
	 */
	function retourArriere() {
		$s_html = '<form name="' . NAME_GESTION_MIGRATION . '" method="post" action="' . PAGE_PHP_ACTION . '">';
		$s_html .= '<input type="hidden" value="' . ACTION_GESTION_MIGRATION . '" name="' . TYPE_ACTION . '" />';
		$s_html .= '<p class="commentaire ligne">Pour quelles raisons voulez-vous revenir en arri&egrave;re ? </p></br>';
		$s_html .= '<textarea name="' . LIBELLE_RA . '" value="" cols="40" rows=7></textarea></br>';
		// gestion des boutons
		$s_html .= '<input type="hidden" value="' . PAGE_VALIDER_RA . '" name="' . ID_PAGE . '" />';
		$s_html .= '<input type="submit" class="annulerRetour" value="Annuler" />';
		$s_html .= '<input type="submit" value="Valider" />';
		$s_html .= '</form>';
		$_SESSION [PAGE_EN_COURS] = $_SESSION [PAGE_EN_COURS] + 1;
		$_SESSION [PAGE_TOTAL] = NB_PAGE_RA;
		return $s_html;
	}
	/**
	 *
	 * @return string
	 */
	function rARecap() {
		$s_html = '<form name="' . NAME_GESTION_MIGRATION . '" method="post" action="' . PAGE_PHP_ACTION . '">';
		$s_html .= '<input type="hidden" value="' . ACTION_GESTION_MIGRATION . '" name="' . TYPE_ACTION . '" />';
		$s_html .= '<p class="commentaire ligne">Vous voulez revenir en arri&egrave;re pour les raisons suivantes : </p></br>';
		$s_html .= '<p class="commentaire ligne">' . $_POST [LIBELLE_RA] . '</p></br>';
		// gestion des boutons
		$s_html .= '<input type="hidden" value="' . PAGE_FIN_MIGRATION . '" name="' . ID_PAGE . '" />';
		$s_html .= '<input type="submit" value="Valider" />';
		$s_html .= '</form>';
		$_SESSION [SESSION_LIBELLE_REPONSE] [ID_RETOUR_ARRIERE . '_' . ID_RETOUR_ARRIERE] = $_POST [LIBELLE_RA];
		$_SESSION [SESSION_QUESTION_REPONSE] [ID_RETOUR_ARRIERE] = array (
				ID_RETOUR_ARRIERE => true 
		);
		$_SESSION [PAGE_EN_COURS] = NB_PAGE_RA - 1;
		return $s_html;
	}
	
	/**
	 * enregistre les valeurs dans la session
	 */
	function enregistrerReponseSession() {
		$this->logger->info ( 'enregistre dans la session les reponses a la question' );
		if (isset ( $_POST [INPUT_HIDDEN_NOMBRE_REPONSE_ACTIVE] )) {
			// on enregistre les réponses
			$a_reponses = array ();
			for($i = 1; $i <= $_POST [INPUT_HIDDEN_NOMBRE_REPONSE_ACTIVE]; $i ++) {
				// la valeur est cochée
				if (isset ( $_POST [CHECK_REPONSE . $i] ) && 'on' == $_POST [CHECK_REPONSE . $i]) {
					$a_reponses [$i] = true;
				} else {
					$a_reponses [$i] = false;
				}
				// cas ou c'est pas actif
				if (isset ( $_POST ['libelle' . CHECK_REPONSE . $i] )) {
					$_SESSION [SESSION_LIBELLE_REPONSE] [$_SESSION [NUMERO_QUESTION] . '_' . $i] = $_POST ['libelle' . CHECK_REPONSE . $i];
				}
			}
			$_SESSION [SESSION_QUESTION_REPONSE] [$_SESSION [NUMERO_QUESTION]] = $a_reponses;
		} else {
			$this->logger->debug ( 'Rien a enregistrer' );
		}
	}
	
	/**
	 *
	 * @return le flux html
	 */
	function reponseQuestion() {
		$this->logger->info ( 'reponse Question' );
		// on recherche le numero de la question a poser
		$this->rechercheNumeroQuestion ();
		// on parse le fichier xml
		$liste_question = $this->lireFichierXml ( $_SESSION [NUMERO_QUESTION] );
		if ($_SESSION[NUMERO_QUESTION] != 1 && isset($_SESSION[LECTURE_FICHIER_CSV])) {
			$_SESSION[LECTURE_FICHIER_CSV] = false;
		}
		// le numero de question doit etre celui du code
		$s_html = '';
		// si la liste est vide il faut afficher le recapitulatif.
		$s_html .= '<form name="' . NAME_GESTION_MIGRATION . '" method="post" action="' . PAGE_PHP_ACTION . '">';
		$s_html .= '<input type="hidden" value="' . ACTION_GESTION_MIGRATION . '" name="' . TYPE_ACTION . '" />';
		$id_etape = 1;
		if ($liste_question == null || empty ( $liste_question )) {
			$id_etape = $_SESSION [NOMBRE_ETAPE_TOTAL];
			$s_html .= '<p class="commentaire ligne"><b>Etape ' . $_SESSION [NOMBRE_ETAPE_TOTAL] . '/' . $_SESSION [NOMBRE_ETAPE_TOTAL] . ' : ' . TITRE_RECAPITULATIF_VALIDATION . '</b></p>';
			$s_html .= '</br><p class="commentaire ligne">Veuillez v&eacute;rifier que les &eacute;l&eacute;ments ci-dessous sont exacts, ils vont &ecirc;tre utilis&eacute; d&prime;une part pour configurer votre poste de travail de mani&egrave;re optimale et d&prime;autre part pour vous d&eacute;livrer une information adapt&eacute;e &agrave; vos besoins.</p>';
			$s_html .= '</br><p class="commentaire ligne">Une fois la v&eacute;rification termin&eacute;e, appuyer sur le bouton valider.</p>';
			$s_html .= '<p class="commentaire ligne">vous recevrez un mail de confirmation puis votre poste de travail sera configur&eacute; ' . $_SESSION [LIBELLE_DATE_MIGRATION] . '.</p>';
			// $s_html .= '</br><p class="commentaire ligne">(Dans le cas d&prime;une migration volontaire, vous avez la possibilit&eacute; de revenir en arri&egrave;re &agrave; tout moment en revenant sur ce site tant que la migration officielle n&prime;a pas d&eacute;but&eacute;.)</p>';
			// recapitulatif
			$liste_question = $this->lireFichierXml (); // on charge la liste complete des questions
			                                            // on itere que chaque question
			if ($liste_question == null || empty ( $liste_question )) {
				$this->logger->warn ( 'la liste des question est vide' );
			} else {
				$a_groupes = array();
				foreach ( $liste_question as $q_question ) {
					$s_html .= '</br><p class="commentaire ligne">' . $q_question->getLibelleRecapitulatif () . '&nbsp;:</p>';
					// pour chaque question on recupere la liste des reponses
					$liste_reponses = $_SESSION [SESSION_QUESTION_REPONSE] [$q_question->getIdentifiant ()];
					$this->logger->debug ( 'liste_reponses de la session' );
					if ($liste_reponses == null || empty ( $liste_reponses )) {
						$a_liste_reponses = $this->o_managerDb->rechercherReponseParAgentCampagneEtQuestion ( $_SESSION [ID_AGENT_CAMPAGNE], $q_question->getIdentifiant () );
						$this->logger->debug ( 'liste_reponses de la BDD' );
						if ($a_liste_reponses == null || empty ( $a_liste_reponses )) {
							$this->logger->debug ( 'la liste des reponse de la bdd est vide' );
						} 
						else {
							foreach ( $a_liste_reponses as $reponse_uneReponse ) {
								$liste_reponses [$reponse_uneReponse->getIdReponse ()] = true;
							}
						}
					}
					$this->logger->debug ( 'itere sur la liste des reponses' );
					$nb_rep = 0;
					foreach ( $liste_reponses as $i_numeroReponse => $b_valuerReponse ) {
						if ($b_valuerReponse) {
							
							if (0 == $nb_rep) {
								$s_html .= '<ul>';
							}
							$s_html .= '<li>' . $_SESSION [SESSION_LIBELLE_REPONSE] [$q_question->getIdentifiant () . '_' . $i_numeroReponse] . '</li>';
							foreach ($q_question->getListeReponses() as $r_reponseXML) {
								if ($i_numeroReponse == $r_reponseXML->getIdentifiant()) {
									foreach ($r_reponseXML->getListeGroupe() as $g_groupe) {
										if (in_array($g_groupe->getIdentifiant(),$a_groupes)) { 
											$this->logger->debug('le groupe '.$g_groupe.' existe deja pour l\'agent ');
										} else {
											array_push($a_groupes, $g_groupe->getIdentifiant());
										}
									}
								}
							}
							$nb_rep ++;
						}
					}
					if (0 != $nb_rep) {
						$s_html .= '</ul>';
					}
				}
				$_SESSION [LISTE_GROUPE_AGENT] = $a_groupes;
			}
			$s_html .= '</br>';
			$id_page_suivante = PAGE_FIN_MIGRATION;
		} 		// sinon on affiche la question
		else {
			$q_question = $liste_question [0];
			$id_etape = $q_question->getIdentifiant ();
			$s_html .= '<p class="commentaire ligne"><b>Etape ' . $q_question->getIdentifiant () . '/' . $_SESSION [NOMBRE_ETAPE_TOTAL] . ' : ' . $liste_question [0]->getTitre () . '</b></p>';
			$s_html .= '</br><p class="commentaire ligne">' . $q_question->getLibelle () . '</p>';
			if ($liste_question [0]->getLibelleNiveau2 () != '') {
				$s_html .= '</br><p class="commentaire ligne">' . $q_question->getLibelleNiveau2 () . '</p>';
			}
			$liste_reponses = $liste_question [0]->getListeReponses ();
			if ($liste_reponses == null || empty ( $liste_reponses )) {
				$this->logger->warn ( 'il n\'y a pas de reponses pour la question ' . $q_question->getIdentifiant () );
			} else {
				$this->logger->debug ( 'traite les reponses pour la question ' . $q_question->getIdentifiant () );
				$s_html .= '<div class="listeReponse">';
				$nb_reponseActive = $q_question->getNbRep ();
				$s_html .= '<input type="hidden" name="' . INPUT_HIDDEN_NOMBRE_REPONSE_ACTIVE . '" value="' . $nb_reponseActive . '"/>';
				$nb_questionAffichee = 0;
				$nb_question_colonne = 0;
				foreach ( $liste_reponses as $r_reponse ) {
					$this->logger->debug ( 'traite la reponse  ' . $r_reponse . ' (actif : ' . $r_reponse->getActif () . ') a la question ' . $q_question->getIdentifiant () );
					// si la question est active, on la traite, sinon elle ne sera pas affichee
					if (1 == $r_reponse->getActif ()) {
						if (0 == $nb_question_colonne) {
							$s_html .= '<ul>';
						}
						$nb_questionAffichee ++;
						$nb_question_colonne ++;
						$s_html .= '<li>' . ClassUtil::creerInputHidden ( 'libelle' . CHECK_REPONSE . $r_reponse->getIdentifiant (), $r_reponse->getLibelle () );
						$s_checked = $this->rechercherReponseParAgentEtQuestion ( $r_reponse->getIdentifiant () );
						$s_html .= '<input type="checkbox" name="' . CHECK_REPONSE . $r_reponse->getIdentifiant () . '" ' . $s_checked . '/>' . $r_reponse->getLibelle ();
						$s_html .= '</li>';
						// si on est a la fin de la liste
						// mais qu'il reste une place ou plusieurs puce de dispo, alors on les ajout
						if ($nb_reponseActive == $nb_questionAffichee && $q_question->getNbQuestionColonne () != $nb_question_colonne) {
							for($i = $nb_question_colonne; $i < $q_question->getNbQuestionColonne (); $i ++) {
								$s_html .= '<li>&nbsp;</li>';
							}
						}
						// si on a la fin de la colonne ou du nombre de reponse on ferme la balise
						if ($q_question->getNbQuestionColonne () == $nb_question_colonne || $nb_reponseActive == $nb_questionAffichee) {
							$s_html .= '</ul>';
							$nb_question_colonne = 0;
						}
					}
				}
			
				$s_html .= '</div>';
			}
			$id_page_suivante = PAGE_AJOUTER_QUESTION;
		}
		// gestion des boutons
		$s_html .= "<div class='navigationsFormulaire'>";
		$s_html .= '<input type="hidden" value="" name="' . SIGNE_NAVIGATION_ETAPE . '" />';
		$s_html .= '<input type="hidden" value="' . $id_page_suivante . '" name="' . ID_PAGE . '" />';
		if ($id_etape != '1') {
			$s_html .= '<input type="submit" class="migrationEtapePrecedente" value="' . MIGRATION_ACTION_PRECEDENTE . '" />';
		}
		$s_html .= '<input type="submit" class="migrationEtapeSuivante" value="' . MIGRATION_ACTION_SUIVANT . '" />';
		$s_html .= '</div></form>';
		return $s_html;
	}
	
	/**
	 *
	 * @param unknown $idReponse        	
	 * @return string
	 */
	private function rechercherReponseParAgentEtQuestion($idReponse) {
		$nb_ligne = $this->o_managerDb->compterReponseParReponseEtAgentCampagneEtQuestion ( $idReponse, $_SESSION [ID_AGENT_CAMPAGNE], $_SESSION [NUMERO_QUESTION] );
		if ($_SESSION[NUMERO_QUESTION] == 1 && isset($_SESSION[LECTURE_FICHIER_CSV]) && $_SESSION[LECTURE_FICHIER_CSV]) {
			// on recherche dans le fichier pour la question 1
			if (1 == $_SESSION [NUMERO_QUESTION]) {
				if (($handle = fopen ( CHEMIN_FICHIER_APPLICATION_UTILISATEUR, "r" )) !== FALSE) {
					$row = 1;
					while ( ($data = fgetcsv ( $handle, 1000, CSV_CARACTERE_SEPARATEUR )) !== FALSE ) {
						if ($this->logger->isDebugEnabled ()) {
							$this->logger->debug ( 'detail de la ligne ' . $row . '  : ' . $data [0] );
						}
						if ($_SESSION [ID_AGENT_CONNECTER] == $data [1] && $idReponse == $data [0]) {
							// on traite l'agent qui nous interesse pour ce cas
							$nb_ligne = 1;
						}
						$row ++ ;
					}
					fclose ( $handle );
				}
			}
		}
		// on recherche dans le session
		if (array_key_exists ( $_SESSION [NUMERO_QUESTION], $_SESSION [SESSION_QUESTION_REPONSE] ) && array_key_exists ( $idReponse, $_SESSION [SESSION_QUESTION_REPONSE] [$_SESSION [NUMERO_QUESTION]] ) && $_SESSION [SESSION_QUESTION_REPONSE] [$_SESSION [NUMERO_QUESTION]] [$idReponse]) {
			$nb_ligne = 1;
		}
		if (isset ( $nb_ligne ) && ! empty ( $nb_ligne ) && $nb_ligne != 0) {
			$s_retour = 'checked';
		} else {
			$s_retour = '';
		}
		return $s_retour;
	}
	
	/**
	 * met dans la variable de session NUMERO_QUESTION, le numero de la question a rechercher dans le fichier xml
	 * met à jour la variable de session PAGE_EN_COURS, permet de determiner le % d'avancement
	 */
	function rechercheNumeroQuestion() {
		// on recherche le numero de question à lire
		if (isset ( $_SESSION [NUMERO_QUESTION] )) {
			if (! isset ( $_POST [SIGNE_NAVIGATION_ETAPE] ) || '+' == $_POST [SIGNE_NAVIGATION_ETAPE]) {
				$_SESSION [NUMERO_QUESTION] = $_SESSION [NUMERO_QUESTION] + 1;
				$_SESSION [PAGE_EN_COURS] = $_SESSION [PAGE_EN_COURS] + 1;
			} else if ('-' == $_POST [SIGNE_NAVIGATION_ETAPE]) {
				$_SESSION [NUMERO_QUESTION] = $_SESSION [NUMERO_QUESTION] - 1;
				$_SESSION [PAGE_EN_COURS] = $_SESSION [PAGE_EN_COURS] - 1;
			} else {
				$this->logger->warn ( 'il n\'y a pas de signe ' );
			}
		} else {
			// on est à la premiere question
			$_SESSION [NUMERO_QUESTION] = 1;
		}
		// si page en cours est sup au nb de page total, alors on le force au nb de page total
		if ($_SESSION [PAGE_EN_COURS] > $_SESSION [PAGE_TOTAL]) {
			$_SESSION [PAGE_EN_COURS] = $_SESSION [PAGE_TOTAL];
		}
		$this->logger->debug ( 'le numero de question ' . $_SESSION [NUMERO_QUESTION] );
	}
	
}
?>