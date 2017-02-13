<?php
require_once 'includes/php/lib/apache-log4php-2.3.0/src/main/php/Logger.php';
Logger::configure ( "configuration/log4php.xml" );
include_once 'includes/php/metier/abstract/AbstractGestion.class.php';
include_once 'includes/php/DAO/GestionAgentCampagneDao.class.php';
include_once 'includes/php/commons/Constantes.php';
include_once 'includes/php/utils/ParseXMLConstante.php';
include_once 'includes/php/utils/ClassUtils.php';
// namespace php\metier;

/**
 *
 * @author m.emschwiller
 *        
 */
class GestionBatch extends AbstractGestion {
	
	/**
	 */
	function __construct() {
		parent::__construct ();
		$this->logger = Logger::getLogger ( __CLASS__ );
		$this->logger->debug ( 'Dans le constructeur!' );
		// instance de BDD
		$this->o_managerDb = new GestionAgentCampagneDAO ();
	}
	
	/**
	 * (non-PHPdoc)
	 *
	 * @see AbstractGestion::doExecute()
	 *
	 */
	public function doExecute() {
		// envoi un mail a tous les agents qui ont une campagne qui commence
		$this->debutCampagne ();
		// envoi un mail de relance en fct de la date butoir et du delai de relance en fct de la date du jour
		$this->relanceCampagne ();
		// export un fichier csv avec tous les agents qui ont migré a la date du jour
		$this->exporterAgent();
	}
	
	/**
	 * envoi un mail a tous les agents dont la date de début est aujourd'hui
	 */
	private function debutCampagne() {
		$this->logger->info ( "Traite l'ensemble des agents qui sont en debut de campagne aujourd'hui" );
		// recherche tous les agents qui ont une campagne qui commence aujourd'hui
		$ac_AgentsCampagnes = $this->o_managerDbGCDAO->rechercherListeAgentsPourUneDebutCampagneAujourdui ();
		if ($ac_AgentsCampagnes == null || empty ( $ac_AgentsCampagnes ) || 0 == sizeof ( $ac_AgentsCampagnes )) {
			$this->logger->info ( "Aucune campagne commence aujourd'hui" );
		} else {
			$this->logger->debug ( 'traite la liste des agents campagne' );
			$this->envoiMailAgent ( $ac_AgentsCampagnes, OBJET_EMAIL_DEBUT, FROM_EMAIL_DEBUT, FICHIER_MAIL_DEBUT, 'modifierDateDebut' );
		}
		$this->logger->info ( "Fin du traitement" );
	}
	
	/**
	 * envoi un mail a tout  les agents qui non pas repondu a l'enquete et avec la date de relance aujourd'hui
	 */
	private function relanceCampagne() {
		$this->logger->info ( "Traite l'ensemble des agents qui sont en relance de campagne aujourd'hui" );
		// recherche tous les agents qui ont une campagne qui commence aujourd'hui
		$ac_AgentsCampagnes = $this->o_managerDbGCDAO->rechercherListeAgentsPourUneButoireMoisDelaiAvantRelanceCampagneAujourdui ();
		if ($ac_AgentsCampagnes == null || empty ( $ac_AgentsCampagnes ) || 0 == sizeof ( $ac_AgentsCampagnes )) {
			$this->logger->info ( "Aucune campagne commence aujourd'hui" );
		} else {
			$this->logger->debug ( 'traite la liste des agents campagne' );
			$this->envoiMailAgent ( $ac_AgentsCampagnes, OBJET_EMAIL_RELANCE, FROM_EMAIL_RELANCE, FICHIER_MAIL_RELANCE, 'modifierDateRelance' );
		}
		$this->logger->info ( "Fin du traitement" );
	}
	
	/**
	 * export les agents qui ont migrés
	 */
	private function exporterAgent (){
		$this->logger->info ( "Traite l'ensemble des agents la date de fin de migration aujourd'hui ou qui ont migre volontairement" );
		$ac_AgentsCampagnes = $this->o_managerDbGCDAO->rechercherListeAgentsPourExport ();
		if ($ac_AgentsCampagnes == null || empty ( $ac_AgentsCampagnes ) || 0 == sizeof ( $ac_AgentsCampagnes )) {
			$this->logger->info ( "Aucune campagne date de fin de migration aujourd'hui" );
		} else {
			$this->logger->debug ( 'traite la liste des agents campagne' );
			$a_liste_question = $this->lireFichierXml (); // on charge la liste complete des questions
			$s_repertoireExport = ParseXMLConstante::getInstance ()->getValeur ( REPERTOIRE_EXPORT_BATCH );
			$s_libelle_libreoffice_defaut = ParseXMLConstante::getInstance ()->getValeur ( LIBELLE_LIBREOFFICE_DEFAUT );
			$s_libelle_office_defaut = ParseXMLConstante::getInstance ()->getValeur ( LIBELLE_OFFICE_DEFAUT );
			if (!file_exists($s_repertoireExport)) {
				mkdir($s_repertoireExport);
			}
			$output = fopen ( $s_repertoireExport."/batch-".date ( FORMAT_DATE_AAAA_MM_JJ ).".csv", 'w' );
			foreach ( $ac_AgentsCampagnes as $ac_agentCampagne ) {
				//
				// ajout la ligne au fichier
				// ligne par defaut
				fputcsv ( $output, array($ac_agentCampagne->getIdAgent (),'addmember',$s_libelle_libreoffice_defaut), CSV_CARACTERE_SEPARATEUR );
				// recherche des applications 
				$a_reponses = $this->o_managerDb->rechercherReponsesParAgentCampagne ( $ac_agentCampagne->getId(), ID_RETOUR_ARRIERE );
				// recherche du retour arriere
				// si l'agent campagne a une reponse id zero alors, il y a retour arriere
				$a_groupes = array();
				if ($a_reponses == null || empty ( $a_reponses )) {
					$this->logger->debug('pas de reponses');
				} else {
					foreach ($a_reponses as $a_reponse) {
						if (ID_RETOUR_ARRIERE == $a_reponse->getNumeroQuestion()) {
							fputcsv ( $output, array($ac_agentCampagne->getIdAgent (),'delmember',$s_libelle_office_defaut), CSV_CARACTERE_SEPARATEUR );
						} else {
							$q_question = $a_liste_question[$a_reponse->getNumeroQuestion()-1];
							foreach ($q_question->getListeReponses() as $r_reponse) {
								if ($a_reponse->getIdReponse() == $r_reponse->getIdentifiant()) {
									foreach ($r_reponse->getListeGroupe() as $g_groupe) {
										if (in_array($g_groupe->getIdentifiant(),$a_groupes)) { 
											$this->logger->debug('le groupe '.$g_groupe.' existe deja pour l\'agent '.$ac_agentCampagne->getIdAgent ());
										} else {
											array_push($a_groupes, $g_groupe->getIdentifiant());
										}
									}
								}
							}
						}
					}
					foreach ($a_groupes as $g_id_groupe) {
						fputcsv ( $output, array($ac_agentCampagne->getIdAgent (),'addmember', $g_id_groupe), CSV_CARACTERE_SEPARATEUR );
					}
				}
				// mise a jour de la date de migration
				$this->o_managerDbGCDAO->modifierDateMigration($ac_agentCampagne->getId());
			}
			fclose ( $output );
		}
		$this->logger->info ( "Fin du traitement" );
	}
	
	/**
	 * envoi un mail a un agent
	 * 
	 * @param unknown $ac_AgentsCampagnes        	
	 * @param unknown $s_c_object        	
	 * @param unknown $s_c_header        	
	 * @param unknown $s_c_fichier        	
	 */
	private function envoiMailAgent($ac_AgentsCampagnes, $s_c_object, $s_c_header, $s_c_fichier, $s_nomFunction = '') {
		$a_campagnes = array ();
		$s_object = ParseXMLConstante::getInstance ()->getValeur ( $s_c_object );
		$s_header = $this->creationHeaderMail ( ParseXMLConstante::getInstance ()->getValeur ( $s_c_header ) );
		$s_message_template = $this->rechercheTemplateMail ( $s_c_fichier );
		$o_camp = null;
		foreach ( $ac_AgentsCampagnes as $ac_agentCampagne ) {
			$s_message = $s_message_template;
			// on recupere la campagne de l'agent
			// en BDD si on ne la pas deja
			// sinon dans le "cache"
			if (array_key_exists ( $ac_agentCampagne->getIdCampagne (), $a_campagnes )) {
				$o_camp = $a_campagnes [$ac_agentCampagne->getIdCampagne ()];
				$this->logger->debug ( 'utilisation du cache pour la campagne ' . $ac_agentCampagne->getIdCampagne () );
			} else {
				$o_camp = $this->o_managerDbGCDAO->rechercherUneCampagneParNumero ( $ac_agentCampagne->getIdCampagne () );
				$a_campagnes [$ac_agentCampagne->getIdCampagne ()] = $o_camp;
				$this->logger->debug ( 'ajout de la campagne ' . $ac_agentCampagne->getIdCampagne () . ' au cache' );
			}
			$patterns = array ();
			$patterns [0] = '/##date_butoir##/';
			$patterns [1] = '/##date_migration##/';
			$replacements = array ();
			$replacements [0] = ClassUtil::dateToString ( $o_camp->getDateButoir (), FORMAT_DATE_JJ_MM_AAAA );
			$replacements [1] = ClassUtil::dateToString ( $o_camp->getDateMigration (), FORMAT_DATE_JJ_MM_AAAA );
			$s_message = preg_replace ( $patterns, $replacements, $s_message_template );
			$o_agent = $this->rechercheParIdentifiant ( $ac_agentCampagne->getIdAgent () );
			mail ( $o_agent->getEmail (), $s_object, $s_message, $s_header );
			if ($s_nomFunction != '') {
				call_user_func ( array ( $this->o_managerDbGCDAO, $s_nomFunction), $ac_agentCampagne->getId());
			}
		}
	}
}

?>