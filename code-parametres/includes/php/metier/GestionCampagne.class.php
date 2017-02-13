<?php
include_once 'includes/php/metier/abstract/AbstractGestion.class.php';
include_once 'includes/php/DAO/GestionCampagneDao.class.php';
include_once 'includes/php/DTO/CampagneDTO.class.php';
include_once 'includes/php/DTO/AgentCampagneDTO.class.php';
include_once 'includes/php/commons/Constantes.php';

include_once 'includes/php/commons/Constantes.php';
require_once 'includes/php/lib/apache-log4php-2.3.0/src/main/php/Logger.php';
Logger::configure ( "configuration/log4php.xml" );

/**
 *
 *
 * Enter description here ...
 *
 * @author m.emschwiller
 *        
 */
class GestionCampagne extends AbstractGestion {
	
	/**
	 * Constructeur de la classe
	 */
	function __construct() {
		parent::__construct ();
		$this->logger = Logger::getLogger ( __CLASS__ );
		$this->logger->debug ( 'Dans le constructeur!' );
		// ....
		$this->s_titre = TITRE_GESTION_CAMPAGNE;
	}
	
	/**
	 * Enter description here .
	 * ..
	 */
	function doExecute() {
		$s_html = '';
		switch ($this->idPage) {
			// liste des campagnes
			case 1 :
				$s_html = $this->accueil ();
				break;
			// detail de la campagne
			case 2 :
				$s_html = $this->detailCampagne ( 'accueilCampagne', ACTION_GESTION_CAMPAGNE );
				break;
			// suppression de la campagne
			// + liste des campagnes
			case 3 :
				$this->supprimerCampagne ();
				$s_html = $this->accueil ();
				break;
			// modification de la camapgne
			case PAGE_MODIFIER_CAMPAGNE :
				$s_html = $this->modifierCampagne ();
				break;
			// creation d'une campagne
			case PAGE_AJOUTER_CAMPAGNE :
				$s_html = $this->ajouterCampagne ();
				break;
			// verification + enregistrement de la nouvellec campagne
			case PAGE_VALIDER_CREATION_CAMPAGNE :
				$s_html = $this->verifierCampagne ( true );
				if (empty ( $s_html )) {
					$idCampagne = $this->creerCampagne ();
					$s_html .= $this->ajouterListeAgents ( $idCampagne );
					$s_html .= $this->accueil ();
				} else {
					$s_html .= $this->ajouterCampagne ( true );
				}
				break;
			// verification + modification de la campagne
			case PAGE_VALIDER_MAJ_CAMPAGNE :
				$s_html = $this->verifierCampagne ();
				if ($s_html == null || '' == $s_html) {
					$s_html .= $this->majCampagne ();
					$s_html .= $this->accueil ();
				} else {
					$s_html .= $this->modifierCampagne ( true );
				}
				break;
			// suppression de l'agent de la campagne
			case 8 :
				$this->supprimerAgent ();
				$s_html = $this->detailCampagne ( 'accueilCampagne', ACTION_GESTION_CAMPAGNE );
				break;
			// jouter un agent
			case 9 :
				$s_html = $this->ajouterUnagent ();
				// if (empty ( $s_html )) {
				// $s_html .= $this->detailCampagne ( 'accueilCampagne', ACTION_GESTION_CAMPAGNE );
				// } else {
				$s_html .= $this->modifierCampagne ( true );
				// }
				break;
			default :
				$this->logger->warn ( 'Page par defaut' );
				$s_html = $this->accueil ();
				break;
		}
		return $s_html;
	}
	
	/**
	 * affiche la liste des campagnes
	 *
	 * @return string
	 */
	function accueil() {
		$this->logger->info ( 'creation de l\'accueil' );
		$this->s_sous_titre_niv_deux = SOUS_TITRE_DEUX_GESTION_CAMPAGNE_ACCUEIL;
		return $this->afficherListeCampagne ( 'accueilCampagne', ACTION_GESTION_CAMPAGNE );
	}
	
	/**
	 * Supprime un aganet
	 */
	function supprimerAgent() {
		$this->logger->info ( 'supprimer Agent ' . $_POST [INPUT_ID_AGENT] );
		$this->o_managerDbGCDAO->supprimerAgentparIdAgentEtIdCampagne ( $_POST [INPUT_ID_AGENT], $_POST [ID_NUMERO_CAMPAGNE] );
	}
	
	/**
	 * Ajout un agent
	 *
	 * @return string
	 */
	function ajouterUnagent() {
		$this->logger->info ( 'ajouter un agent ' . $_POST [INPUT_AJOUTER_ID_AGENT] );
		$s_html = '';
		$s_html_erreur = '';
		$s_html_warning = '';
		if (empty ( $_POST [INPUT_AJOUTER_ID_AGENT] )) {
			$s_html = '<div class="message negative" id="erreurs"> <ul><li>' . MESSAGE_ERREUR_AGENT_OBLIGATOIRE . '</li></ul></div>';
		} else {
			$idCampagne = '';
			if (isset ( $_POST [ID_NUMERO_CAMPAGNE] )) {
				$idCampagne = $_POST [ID_NUMERO_CAMPAGNE];
			} else if (isset ( $_POST [CAMPAGNE_INPUT_NAME_NUMERO] )) {
				$idCampagne = $_POST [CAMPAGNE_INPUT_NAME_NUMERO];
			} else {
				$this->logger->warn ( 'Dans ajouter un agent, mais y\'a pas de numero de campagne' );
			}
			$userLdap = $this->rechercheParIdentifiant ( $_POST [INPUT_AJOUTER_ID_AGENT] );
			if ($userLdap == null || empty($userLdap) || $userLdap->getIdentifiant() == null || trim($userLdap->getIdentifiant()) == '') {
				$s_html_erreur .= '<li>L\'agent '.$_POST [INPUT_AJOUTER_ID_AGENT].' n\'existe pas dans le LDAP</li>';
			}  else {
				if (0 != $this->o_managerDbGCDAO->coutAgent($_POST [INPUT_AJOUTER_ID_AGENT])) {
					$s_html_warning .= '<li>L\'agent '.$_POST [INPUT_AJOUTER_ID_AGENT].' est déjà rattachée à une autre campagne</li>';
				}
				$agentCampagne = new AgentCampagne ();
				$agentCampagne->setIdCampagne ( $idCampagne );
				$agentCampagne->setIdAgent ( $_POST [INPUT_AJOUTER_ID_AGENT] );
				$this->o_managerDbGCDAO->creerAgentCampagne ( $agentCampagne );
			}
			if (!empty($s_html_erreur)) {
				$s_html.= '<div class="message negative" id="erreurs"> <ul>' . $s_html_erreur . '</ul></div>';
			}
			if (!empty($s_html_warning)) {
				$s_html.= '<div class="message warning" id="warning"> <ul>' . $s_html_warning . '</ul></div>';
			}
			
		}
		return $s_html;
	}
	
	/**
	 * pour la suppression d'un campagne en BDD
	 */
	function supprimerCampagne() {
		$this->logger->info ( 'Suppression de la campagne ' . $_POST [ID_NUMERO_CAMPAGNE] );
		// on supprime les agents liee a la campagne
		$this->o_managerDbGCDAO->supprimerUnAgentCampagneParNumero ( $_POST [ID_NUMERO_CAMPAGNE] );
		// on supprimer la campagne
		$this->o_managerDbGCDAO->supprimerUneCampagneParNumero ( $_POST [ID_NUMERO_CAMPAGNE] );
	}
	
	/**
	 * Creation de la campagne en BDD
	 */
	function creerCampagne() {
		$this->logger->info ( 'Creer une campagne ' );
		// on cree la campagne
		$id = $this->o_managerDbGCDAO->creerCampagne ( new Campagne ( true ) );
		$this->logger->info ( 'Creation de la campagne ' . $id );
		return $id;
	}
	
	/**
	 * Mise à jour de la campagne en BDD
	 */
	function majCampagne() {
		$this->logger->info ( 'Mise à jour de la campagne ' . $_POST [CAMPAGNE_INPUT_NAME_NUMERO] );
		// on cree la campagne
		$id = $this->o_managerDbGCDAO->modifierCampagne ( new Campagne ( true ) );
		$this->logger->info ( 'Creation de la campagne ' . $id );
	}
	
	/**
	 * pour l'ajout d'une campagne
	 */
	function ajouterCampagne($afficher_formulaire = false) {
		$this->logger->info ( 'Ajouter une campagne' );
		// on recherche la liste des campagnes
		$this->s_sous_titre_niv_un = SOUS_TITRE_UN_GESTION_CAMPAGNE_CREER;
		$s_html = '<div><form name="accueilCampagne" method="post" action="' . PAGE_PHP_ACTION . '" enctype="multipart/form-data">';
		// detail de la campagne
		$campagne = new Campagne ( $afficher_formulaire );
		$s_html .= $this->afficherCampagne ( $campagne );
		// les acteurs
		$s_html .= '<div id="listeAgentCampagne">';
		$s_html .= '<p><label for="fichier">' . CAMPAGNE_CREER_LISTE_AGENTS . '</label>' . ClassUtil::creerInputFile ( CAMPAGNE_INPUT_NAME_FICHIER ) . '</p>';
		$s_html .= $this->afficherTableAgent ( $campagne );
		$s_html .= '</div>';
		
		// ajout des boutons Modifier et retour à la liste
		$s_html .= '<div><input type="hidden" value="' . ACTION_GESTION_CAMPAGNE . '" name="' . TYPE_ACTION . '" />';
		$s_html .= '<input type="hidden" value="' . PAGE_VALIDER_CREATION_CAMPAGNE . '" name="' . ID_PAGE . '" /><input type="hidden" value="" name="' . ID_NUMERO_CAMPAGNE . '" />';
		$s_html .= '<input type="submit" value="' . CAMPAGNE_ACTION_CREER . '"/>';
		$s_html .= '<input type="submit" value="' . CAMPAGNE_ACTION_RETOUR_A_LA_LISTE . '" class="listeCampagnes"/>';
		$s_html .= '</div></form>';
		// fermeture du block
		$s_html .= '</div>';
		return $s_html;
	}
	
	/**
	 * pour la modification d'une campagne
	 */
	function modifierCampagne($afficher_formulaire = false) {
		$n_numero = '';
		if (isset ( $_POST [ID_NUMERO_CAMPAGNE] )) {
			$n_numero = $_POST [ID_NUMERO_CAMPAGNE];
		} else if (isset ( $_POST [CAMPAGNE_INPUT_NAME_NUMERO] )) {
			$n_numero = $_POST [CAMPAGNE_INPUT_NAME_NUMERO];
		} else {
			$this->logger->warn ( 'Dans modifier la campagne, mais y\'a pas de numero de campagne' );
		}
		$this->logger->info ( 'Modifier la campagne ' . $n_numero );
		// on recherche la liste des campagnes
		if ($afficher_formulaire) {
			$campagne = new Campagne ( true );
		} else {
			$campagne = $this->o_managerDbGCDAO->rechercherUneCampagneParNumero ( $n_numero );
		}
		$agents_campagne = $this->o_managerDbGCDAO->rechercherListeAgents ( $campagne->getNumero () );
		$this->s_sous_titre_niv_un = SOUS_TITRE_UN_GESTION_CAMPAGNE_MODIFIER . $campagne->getNumero ();
		$s_html = '<div><form name="accueilCampagne" method="post" action="' . PAGE_PHP_ACTION . '">';
		// detail de la campagne
		$s_html .= $this->afficherCampagne ( $campagne );
		// les acteurs
		$s_html .= '<div id="listeAgentCampagne"><p>';
		if (null == $campagne->getDateDebut () || ClassUtil::beforeDate ( new DateTime (), new DateTime ( $campagne->getDateDebut () ) )) {
			$s_html .= '<label for="idAgent">' . CAMPAGNE_CREER_LISTE_AGENTS . '</label>' . ClassUtil::creerInputText ( INPUT_AJOUTER_ID_AGENT ) . '<input type="submit" name="' . ACTION_VALUE_AJOUTER_UN_AGENT . '" value="' . BOUTON_VALUE_AJOUTER_UN_AGENT . '" onclick="ajouterUnAgent();" >';
		}
		$s_html .= '</p>';
		$s_html .= $this->afficherTableAgent ( $campagne );
		$s_html .= '</div>';
		
		// ajout des boutons Modifier et retour à la liste
		$s_html .= '<div><input type="hidden" value="' . ACTION_GESTION_CAMPAGNE . '" name="' . TYPE_ACTION . '" />';
		$s_html .= '<input type="hidden" value="' . PAGE_VALIDER_MAJ_CAMPAGNE . '" name="' . ID_PAGE . '" />';
		$s_html .= '<input type="submit" value="' . CAMPAGNE_ACTION_MAJ_VALIDER . '"/>';
		$s_html .= '<input type="submit" value="' . CAMPAGNE_ACTION_RETOUR_A_LA_LISTE . '"  class="listeCampagnes"/>';
		$s_html .= '</div></form>';
		// fermeture du block
		$s_html .= '</div>';
		return $s_html;
	}
	
	/**
	 * verifier une campagne
	 */
	function verifierCampagne($est_creation = false) {
		$this->logger->debug ( 'verifier la campagne ' );
		$s_message = '';
		if (empty ( $_POST [CAMPAGNE_INPUT_NAME_DESCRIPTION] ) || '' == trim ( $_POST [CAMPAGNE_INPUT_NAME_DESCRIPTION] )) {
			$s_message .= '<li>' . MESSAGE_ERREUR_CAMPAGNE_DESCRIPTION . '</li>';
		} else {
			// on verifie qu'une campagne n'existe pas deja avec cette description (dans le cas de la creation)
			if (($est_creation && $this->o_managerDbGCDAO->verifierDescription ( $_POST [CAMPAGNE_INPUT_NAME_DESCRIPTION] )) || $this->o_managerDbGCDAO->verifierDescriptionEtPasID ( $_POST [CAMPAGNE_INPUT_NAME_DESCRIPTION], $_POST [CAMPAGNE_INPUT_NAME_NUMERO] )) {
				$s_message .= '<li>' . MESSAGE_ERREUR_CAMPAGNE_DESCRIPTION_EXISTE_DEJA . '</li>';
			}
		}
		// verification des dates
		$date_depart = '';
		if (empty ( $_POST [CAMPAGNE_INPUT_NAME_DATE_DEPART] )) {
			$s_message .= '<li>' . MESSAGE_ERREUR_CAMPAGNE_DATE_DEPART . '</li>';
		} else {
			$date_depart = ClassUtil::getDateBdd ( $_POST [CAMPAGNE_INPUT_NAME_DATE_DEPART] );
		}
		$date_butoir = '';
		if (empty ( $_POST [CAMPAGNE_INPUT_NAME_DATE_BUTOIR] )) {
			$s_message .= '<li>' . MESSAGE_ERREUR_CAMPAGNE_DATE_BUTOIR . '</li>';
		} else {
			$date_butoir = ClassUtil::getDateBdd ( $_POST [CAMPAGNE_INPUT_NAME_DATE_BUTOIR] );
		}
		$date_migration = '';
		if (empty ( $_POST [CAMPAGNE_INPUT_NAME_DATE_MIGRATION] )) {
			$s_message .= '<li>' . MESSAGE_ERREUR_CAMPAGNE_DATE_MIGRATION . '</li>';
		} else {
			$date_migration = ClassUtil::getDateBdd ( $_POST [CAMPAGNE_INPUT_NAME_DATE_MIGRATION] );
		}
		$objDateTime = new DateTime ( 'NOW' );
		$d_dateJour = $objDateTime->format (FORMAT_DATE_AAAA_MM_JJ );
		if (! empty ( $date_depart ) && ! empty ( $d_dateJour ) && ! ClassUtil::beforeDate ( $d_dateJour, $date_depart)) {
			$s_message .= '<li>' . MESSAGE_ERREUR_CAMPAGNE_DATE_JOUR_AVANT_DATE_DEPART . '</li>';
		}
		if (! empty ( $date_depart ) && ! empty ( $date_butoir ) && ! ClassUtil::beforeDate ( $date_depart, $date_butoir )) {
			$s_message .= '<li>' . MESSAGE_ERREUR_CAMPAGNE_DATE_DEPART_AVANT_DATE_BUTOIR . '</li>';
		}
		if (! empty ( $date_migration ) && ! empty ( $date_butoir ) && ! ClassUtil::beforeDate ( $date_butoir, $date_migration )) {
			$s_message .= '<li>' . MESSAGE_ERREUR_CAMPAGNE_DATE_BUTOIR_AVANT_DATE_MIGRATION . '</li>';
		}
		
		// verification du delai
		if (empty ( $_POST [CAMPAGNE_INPUT_NAME_DELAI] )) {
			$s_message .= '<li>' . MESSAGE_ERREUR_CAMPAGNE_DELAI . '</li>';
		}
		
		if ($_FILES != null && ! empty ( $_FILES [CAMPAGNE_INPUT_NAME_FICHIER] ['name'] ) && EXTENSION_CSV != pathinfo ( $_FILES [CAMPAGNE_INPUT_NAME_FICHIER] ['name'], PATHINFO_EXTENSION )) {
			// il y a un fichier, on verifie le format
			$s_message .= '<li>' . MESSAGE_ERREUR_IMPORT_FICHIER_CSV . '</li>';
		}
		// on afiche la div
		if (! empty ( $s_message )) {
			$this->logger->error ( 'il existe des erreurs dans la campagne : ' . $s_message );
			$s_message = '<div class="message negative" id="erreurs"> <ul>' . $s_message . '</ul></div>';
		}
		return $s_message;
	}
	
	/**
	 *
	 * @param unknown $idCampagne        	
	 */
	function ajouterListeAgents($idCampagne = '') {
		$s_html = '';
		$s_html_erreur = '';
		$s_html_warning = '';
		if ($_FILES != null) {
			// il y a un fichier csv
			$chemin_csv_file_agent = $_FILES [CAMPAGNE_INPUT_NAME_FICHIER] ['tmp_name'];
			// on traite le fichier
			if (! empty ( $chemin_csv_file_agent )) {
				$row = 1;
				if (($handle = fopen ( $chemin_csv_file_agent, "r" )) !== FALSE) {
					while ( ($data = fgetcsv ( $handle, 1000, CSV_CARACTERE_SEPARATEUR )) !== FALSE ) {
						if ($this->logger->isDebugEnabled ()) {
							$this->logger->debug ( 'detail de la ligne ' . $row . '  : ' . $data [0] );
						}
						$userLdap = $this->rechercheParIdentifiant ( $data [0] );
						if ($userLdap == null || empty($userLdap) || $userLdap->getIdentifiant() == null || trim($userLdap->getIdentifiant()) == '') {
							$s_html_erreur .= '<li>L\'agent '.$data [0].' n\'existe pas dans le LDAP</li>';
						}  else {
							if (0 != $this->o_managerDbGCDAO->coutAgent($data [0])) {
								$s_html_warning .= '<li>L\'agent '.$data [0].' est déjà rattaché(e) à une autre campagne</li>';
							}
							$agentCampagne = new AgentCampagne ();
							$agentCampagne->setIdCampagne ( $idCampagne );
							$agentCampagne->setIdAgent ( $data [0] );
							$this->o_managerDbGCDAO->creerAgentCampagne ( $agentCampagne );
						}
					}
					fclose ( $handle );
				}
			}
		}
		if (!empty($s_html_erreur)) {
			$s_html.= '<div class="message negative" id="erreurs"> <ul>' . $s_html_erreur . '</ul></div>';
		}
		if (!empty($s_html_warning)) {
			$s_html.= '<div class="message warning" id="warning"> <ul>' . $s_html_warning . '</ul></div>';
		}
		return $s_html;
	}
}
?>