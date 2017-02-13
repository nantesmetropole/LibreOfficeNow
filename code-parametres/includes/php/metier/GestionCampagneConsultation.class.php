<?php
include_once 'includes/php/metier/abstract/AbstractGestion.class.php';
include_once 'includes/php/DAO/GestionAgentCampagneDao.class.php';
include_once 'includes/php/commons/Constantes.php';
include_once 'includes/php/pojo/QuestionXML.php';
require_once 'includes/php/pojo/ReponseXML.php';
include_once 'includes/php/commons/Constantes.php';
require_once 'includes/php/lib/apache-log4php-2.3.0/src/main/php/Logger.php';
Logger::configure ( "configuration/log4php.xml" );

/**
 *
 *
 *
 *
 * Enter description here ...
 *
 * @author m.emschwiller
 *        
 */
class GestionCampagneConsultation extends AbstractGestion {
	
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
		$this->s_titre = TITRE_CONSULTATION_CAMPAGNE;
	}
	
	/**
	 * Enter description here .
	 *
	 *
	 *
	 *
	 * ..
	 */
	function doExecute() {
		$s_html = '';
		switch ($this->idPage) {
			case 1 :
				$s_html = $this->accueil ();
				break;
			case 2 :
				$s_html = $this->verifierPresenceIdAgent ();
				if (empty ( $s_html )) {
					$s_html .= $this->afficherDetailCampagne ();
				} else {
					$s_html .= $this->accueil ();
				}
				break;
			case 3 :
				$this->exportCSVCampagne ();
				break;
			case 4 :
				$this->exportCSVAgents ();
				break;
			case 5 :
				$s_html .= $this->afficherListeCampagne ( 'accueilCampagneConsultation', ACTION_GESTION_CAMPAGNE_CONSULTATION, false );
				break;
			case 6 :
				$s_html .= $this->detailCampagne ( 'accueilCampagneConsultation', ACTION_GESTION_CAMPAGNE_CONSULTATION, false );
				break;
			default :
				$this->logger->warn ( 'Page par defaut' );
				$s_html = $this->accueil ();
				break;
		}
		return $s_html;
	}
	
	/**
	 *
	 * @return string
	 */
	function accueil() {
		$this->logger->info ( 'creation de l\'accueil' );
		$s_html = '<form name="accueilCampagneConsultation" method="post" action="index.php">';
		$s_value = '';
		if (isset ( $_POST [CAMPAGNE_CONSULTATION_IDENTIFIANT_UTILISATEUR] )) {
			$s_value = $_POST [CAMPAGNE_CONSULTATION_IDENTIFIANT_UTILISATEUR];
		}
		$s_html .= ClassUtil::creerInputText ( CAMPAGNE_CONSULTATION_IDENTIFIANT_UTILISATEUR, $s_value );
		$s_html .= '<input type="submit" class="consulterParAgent" value="Rechercher" />';
		$s_html .= '<input type="hidden" value="" name="' . ID_PAGE . '" /><input type="hidden" value="' . ACTION_GESTION_CAMPAGNE_CONSULTATION . '" name="' . TYPE_ACTION . '" />';
		$s_html .= '<br/><br/><a href="#" class="consulterListeCampagnes">Voir la liste des campagnes (lecture seule)</a>';
		$s_html .= '<br/><br/><a href="javascript:exportCSV(4)">Export CSV - agents</a>';
		$s_html .= '<br/><br/><a href="javascript:exportCSV(3)">Export CSV - campagnes</a>';
		$s_html .= '</form>';
		return $s_html;
	}
	
	/**
	 * export la liste des campagne
	 * par ligne : Description; Date de départ; Date butoir; Date de migration; identifiant agent 1; identifiant agent 2; .
	 * ...
	 */
	public function exportCSVCampagne() {
		$this->logger->info ( 'export CSV des campagnes' );
		$output = $this->enteteCsv ( 'campagnes.csv' );
		// recherche la liste des campagnes
		$listeCampagne = $this->o_managerDbGCDAO->rechercherToutesCampagnes ();
		// output the column headings
		if ($listeCampagne != null && ! empty ( $listeCampagne )) {
			foreach ( $listeCampagne as $uneCampagne ) {
				// format de la campagne
				$a_ligne = array (
						$uneCampagne->getDescription (),
						ClassUtil::dateToString ( $uneCampagne->getDateDebut (), FORMAT_DATE_JJ_MM_AAAA ),
						ClassUtil::dateToString ( $uneCampagne->getDateButoir (), FORMAT_DATE_JJ_MM_AAAA ),
						ClassUtil::dateToString ( $uneCampagne->getDateMigration (), FORMAT_DATE_JJ_MM_AAAA ) 
				);
				// ajout les agents
				$liste_agent = $this->o_managerDbGCDAO->rechercherListeAgents ( $uneCampagne->getNumero () );
				if ($liste_agent != null && ! empty ( $liste_agent )) {
					foreach ( $liste_agent as $unAgent ) {
						// ajout uniquement de l'id
						array_push ( $a_ligne, $unAgent->getIdAgent () );
					}
				}
				// ajout la ligne au fichier
				fputcsv ( $output, $a_ligne, CSV_CARACTERE_SEPARATEUR );
			}
		}
		fclose ( $output );
		exit ();
	}
	
	/**
	 * export de la liste des agents
	 * par ligne Identifiant agent; Volontaire (O/N); Retour arrière (O/N);Commentaire retour arrière; Numéro campagne; Description campagne; date début campagne; date fin campagne; date migration (campagne ou volontaire); [libellé question; réponse question (O/N)] répété pour chaque question
	 */
	public function exportCSVAgents() {
		$this->logger->info ( 'export CSV des agents' );
		$output = $this->enteteCsv ( 'agents.csv' );
		// recherche de la liste des agents campagnes
		$a_agentCampagne = $this->o_managerDb->rechercherListeAgentCampagne ();
		$a_questionsReponses = array();
		if ($a_agentCampagne != null && ! empty ( $a_agentCampagne )) {
			// ajout de l'entete
			$a_ligne = array('Identifiant agent','Volontaire', 'Retour arrière','Commentaire retour arrière','Numéro campagne','Description campagne','date début campagne','date fin campagne','date migration');
			// lecture flux xml pour les questions
			$liste_question = $this->lireFichierXml();
			if ($liste_question == null || empty ( $liste_question )) {
				$this->logger->warn ( 'la liste des question est vide' );
			} else {
				foreach ( $liste_question as $q_question ) {
					// id de la question 
					$i_idQuestion = $q_question->getIdentifiant ();
					$a_questionReponses = array();
					foreach ($q_question->getListeReponses() as $r_reponse) {
						if ($r_reponse->getActif() == 1) {
							array_push ( $a_ligne, $r_reponse->getLibelle());
							array_push($a_questionReponses,$r_reponse->getIdentifiant());
						}
					}
					$a_questionsReponses[$i_idQuestion] = $a_questionReponses;
				}
			}
			fputcsv ( $output, $a_ligne, CSV_CARACTERE_SEPARATEUR );
			foreach ( $a_agentCampagne as $unAgentCampagne ) {
				// ajout de l'agent
				$a_ligne = array (
						$unAgentCampagne->getIdAgent (),
						ClassUtil::presentOuiSinonNon ( $unAgentCampagne->getIdCampagne () ) 
				);
				// si l'agent campagne a une reponse id zero alors, il y a retour arriere
				$a_reponse = $this->o_managerDb->rechercherReponseParAgentCampagneEtQuestion ( $unAgentCampagne->getId (), ID_RETOUR_ARRIERE );
				if ($a_reponse == null || empty ( $a_reponse )) {
					array_push ( $a_ligne, 'N', ' ' );
				} else {
					array_push ( $a_ligne, 'O', $a_reponse [0]->getLibelleReponse () );
				}
				// si y'a un id campagne
				$this->logger->info ( $unAgentCampagne->getIdCampagne () );
				if ($unAgentCampagne->getIdCampagne () == '') {
					array_push ( $a_ligne, ' ', ' ', ' ', ' ', ClassUtil::dateToString ( ClassUtil::ajouterJourAUneDate ( $unAgentCampagne->getDateCreation (), 1 ), FORMAT_DATE_JJ_MM_AAAA ) );
				} else {
					// recherche la campagne
					$uneCampagne = $this->o_managerDbGCDAO->rechercherUneCampagneParNumero ( $unAgentCampagne->getIdCampagne () );
					array_push ( $a_ligne, $unAgentCampagne->getIdCampagne (), $uneCampagne->getDescription (), ClassUtil::dateToString ( $uneCampagne->getDateDebut (), FORMAT_DATE_JJ_MM_AAAA ), ClassUtil::dateToString ( $uneCampagne->getDateButoir (), FORMAT_DATE_JJ_MM_AAAA ), ClassUtil::dateToString ( $uneCampagne->getDateMigration (), FORMAT_DATE_JJ_MM_AAAA ) );
				}
				// ajout des reponses
				$i_taille = sizeof($a_questionsReponses);
				$this->logger->debug( 'traite '.$i_taille.' questions' );
				for ($i = 0; $i < $i_taille; $i++) {
					$i_idQuestion = $i+1;
					$a_questionReponses = $a_questionsReponses [$i_idQuestion];
					foreach ( $a_questionReponses as $i_idRep ) {
						$r_Rep = $this->o_managerDb->rechercherReponseParAgentCampagneEtQuestionEtReponse ( $unAgentCampagne->getId (), $i_idQuestion, $i_idRep );
						if ($r_Rep == null || empty ( $r_Rep )) {
							array_push ( $a_ligne, 'N' );
						} else {
							array_push ( $a_ligne, 'O' );
						}
					}
				}
				// ajout la ligne au fichier
				fputcsv ( $output, $a_ligne, CSV_CARACTERE_SEPARATEUR );
			}
		} else {
			$this->logger->info ( 'la table agent campagne est vide' );
		}
		fclose ( $output );
		exit ();
	}
	
	
	/**
	 *
	 * @param unknown $nomFichier        	
	 * @return unknown
	 */
	function enteteCsv($nomFichier) {
		// output headers so that the file is downloaded rather than displayed
		header ( 'Content-Type: text/csv; charset=utf-8' );
		header ( 'Content-Disposition: attachment; filename=' . $nomFichier . '' );
		header ( 'Cache-control: public' );
		// create a file pointer connected to the output stream
		$output = fopen ( 'php://output', 'w' );
		return $output;
	}
	
	/**
	 *
	 * @return string
	 */
	function verifierPresenceIdAgent() {
		$s_html = '';
		if (empty ( $_POST [CAMPAGNE_CONSULTATION_IDENTIFIANT_UTILISATEUR] )) {
			$s_html = '<div class="message negative" id="erreurs"> <ul><li>' . MESSAGE_ID_AGENT . '</li></ul></div>';
		}
		return $s_html;
	}
	
	/**
	 */
	function afficherDetailCampagne() {
		$this->logger->info ( 'afficher Detail Campagne' );
		$s_html = '';
		$idAgent = $_POST [CAMPAGNE_CONSULTATION_IDENTIFIANT_UTILISATEUR];
		$us_agent_ldap = $this->rechercheParIdentifiant ( $idAgent );
		if ($us_agent_ldap == null || empty($us_agent_ldap) || $us_agent_ldap->getIdentifiant() == null || trim($us_agent_ldap->getIdentifiant()) == '') {
			$this->logger->warn ( 'agent ' . $idAgent . ' non present dans le LDAP' );
			$s_html .= '<div class="message negative" id="erreurs"> <ul><li>' . MESSAGE_AGENT_NON_PRESENT_LDAP . '</li></ul></div>';
			$s_html .= $this->accueil ();
		} else {
			$campagne = $this->o_managerDb->rechercherUneCampagneParAgent ( $idAgent );
			$reponses = $this->o_managerDb->rechercherListeReponseParAgent ( $idAgent );
			
			$s_statut_migration = '';
			$s_date_migration = '';
			$s_numero_migration = '';
			// si la campagne est vide soit Non Migre soit Migré volontaire
			if ($campagne == null || empty ( $campagne )) {
				$this->logger->debug ( 'L\'agent ' . $idAgent . ' n\'a pas de campagne rattache' );
				// si y'a des reponses => Migre volontaire
				if ($reponses == null || empty ( $reponses )) {
					$s_statut_migration = STATUT_NON_MIGRE;
				} else {
					$s_statut_migration = STATUT_MIGRE_VOLONTAIRE;
					$agentCampagne = $this->o_managerDb->rechercherAgentCampagneParAgent ( $idAgent );
					$s_date_migration = ClassUtil::dateToString ( ClassUtil::ajouterJourAUneDate ( $agentCampagne->getDateCreation (), 1 ), FORMAT_DATE_JJ_MM_AAAA );
				}
			} 			// si pas de reponse alors Non migré, sinon migré
			else {
				$s_numero_migration = $campagne->getNumero ();
				$s_date_migration = ClassUtil::dateToString ( $campagne->getDateMigration (), FORMAT_DATE_JJ_MM_AAAA );
				// si la date de debut est dans le passe => non migré
				if (ClassUtil::beforeDate ( new DateTime (), new DateTime ( $campagne->getDateDebut () ) )) {
					$s_statut_migration = STATUT_NON_MIGRE;
				} else if (ClassUtil::beforeDate ( new DateTime ( $campagne->getDateMigration () ), new DateTime () )) {
					$s_statut_migration = STATUT_MIGRE_CAMPAGNE;
				} else {
					$s_statut_migration = STATUT_CAMPAGNE_EN_COURS;
				}
			}
			
			$s_html .= '<div class="consultationCampagne">';
			// partie gauche agent + statut migration
			$s_html .= '<ul><li><u>' . MESSAGE_CONSULATION_AGENT . '</u>&nbsp;:&nbsp;' . $us_agent_ldap->getIdentifiant () . '&nbsp;&nbsp;' . $us_agent_ldap->getPrenom () . '&nbsp;' . $us_agent_ldap->getNom () . '</li>';
			$s_html .= '<li><u>' . MESSAGE_CONSULATION_STATUT_MIGRATION . '</u>&nbsp;:&nbsp;' . $s_statut_migration . '</li></ul>';
			$s_html .= '<ul><li><u>' . MESSAGE_CONSULATION_NUMERO_CAMPAGNE . '</u>&nbsp;:&nbsp;' . $s_numero_migration . '</li>';
			$s_html .= '<li><u>' . MESSAGE_CONSULATION_DATE_MIGRATION . '</u>&nbsp;:&nbsp;' . $s_date_migration . '</li></ul>';
			$s_html .= '</div>';
			
			if ($reponses == null || empty ( $reponses )) {
				$s_html .= 'Pas de reponses';
			} else {
				$s_html .= '<div class="listeReponseConsultationCampagne">';
				$s_reponse_zero = '';
				$s_html_question = '';
				$id_question_precedente = '';
				foreach ( $reponses as $uneReponse ) {
					if (ID_RETOUR_ARRIERE == $uneReponse->getNumeroQuestion ()) {
						$s_reponse_zero = $uneReponse->getLibelleReponse ();
					} else {
						if ($id_question_precedente == null || empty ( $id_question_precedente ) || $id_question_precedente != $uneReponse->getNumeroQuestion ()) {
							if ($id_question_precedente != $uneReponse->getNumeroQuestion ()) {
								$s_html_question .= '</textarea>';
							}
							$s_html_question .= '&nbsp;&nbsp;<textarea rows="4" cols="25">';
						}
						$s_html_question .= $uneReponse->getLibelleReponse () . "\n";
					}
					$id_question_precedente = $uneReponse->getNumeroQuestion ();
				}
				if (! empty ( $s_html_question )) {
					$s_html_question .= '</textarea>';
				}
				$s_html .= $this->genererRetourArriere ( $s_reponse_zero );
				$s_html .= '<li><u>' . MESSAGE_REPONSES_AUX_QUESTIONS . '</u>' . $s_html_question . '</li>';
				$s_html .= '</ul></div>';
			}
			// affiche le bouton de retour
			$s_html .= '<form name="accueilCampagneConsultation" method="post" action="index.php">';
			$s_html .= '<input type="hidden" value="" name="' . ID_PAGE . '" />';
			$s_html .= '<input type="hidden" value="' . ACTION_GESTION_CAMPAGNE_CONSULTATION . '" name="' . TYPE_ACTION . '" />';
			$s_html .= '<input type="submit" value="Retour" />';
			$s_html .= '</form>';
		}
		return $s_html;
	}
	
	/**
	 *
	 * @param unknown $s_libelleReponse        	
	 * @return string
	 */
	private function genererRetourArriere($s_libelleReponse = null) {
		$s_commentaire = ClassUtil::defautIfEmpty ( $s_libelleReponse );
		$s_retour_arriere = 'Non';
		if (! empty ( $s_commentaire )) {
			$s_retour_arriere = 'Oui';
		}
		return '<ul><li><u>' . MESSAGE_RETOUR_ARRIERE . '</u>&nbsp;&nbsp;' . $s_retour_arriere . '&nbsp;&nbsp;<textarea rows="4" cols="50">' . $s_commentaire . '</textarea></li>';
	}
}
?>