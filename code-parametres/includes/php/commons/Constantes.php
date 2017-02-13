<?php
define ( "VERSION_APPLICATION", "1.0.3" );
define ( "CHEMIN_FICHIER_DROIT_UTILISATEUR", "configuration/droitUtilisateur.csv" );
define ( "CHEMIN_FICHIER_APPLICATION_UTILISATEUR", "configuration/applicationUtilisateur.csv" );
define ( "FICHIER_MIGRATION_QUESTION", "configuration/questionMigration.xml" );
define ( "FICHIER_MIGRATION_QUESTION_XSD", "configuration/questionMigration.xsd" );
define ( "FICHIER_MIGRATION_CONSTANTE", "configuration/constante.xml" );
define ( "FICHIER_MIGRATION_CONSTANTE_XSD", "configuration/constante.xsd" );
define ( "FICHIER_MAIL_DEBUT", "configuration/email_debut.txt" );
define ( "FICHIER_MAIL_RELANCE", "configuration/email_relance.txt" );
define ( "FICHIER_MAIL_RECAP", "configuration/email_recap.txt" );
define ( "REPERTOIRE_EXPORT_BATCH", "REPERTOIRE_EXPORT_BATCH" );
define ( "LIBELLE_LIBREOFFICE_DEFAUT", "LIBELLE_LIBREOFFICE_DEFAUT" );
define ( "LIBELLE_OFFICE_DEFAUT", "LIBELLE_OFFICE_DEFAUT" );
define ( "ROLE_ADMIN", "ADMIN" );
define ( "ROLE_CONSULTANT", "CONSULTANT" );
// valeur par defaut
define ( "CONSTANTE_DEFAUT", "_DEFAUT" );
define ( "ACTIVE_RETOUR_ARRIERE", "ACTIVE_RETOUR_ARRIERE" );
define ( ACTIVE_RETOUR_ARRIERE . CONSTANTE_DEFAUT, 'oui' );
define ( "DELAI_AVANT_RELANCE_DEFAUT", "DELAI_AVANT_RELANCE_DEFAUT" );
define ( DELAI_AVANT_RELANCE_DEFAUT . CONSTANTE_DEFAUT, 5 );
define ( "OPTION_NB_JOURS_REMPLISSAGE_QUESTIONNAIRE", "OPTION_NB_JOURS_REMPLISSAGE_QUESTIONNAIRE" );
define ( OPTION_NB_JOURS_REMPLISSAGE_QUESTIONNAIRE . CONSTANTE_DEFAUT, 30 );
define ( "BDD_HOST", "BDD_HOST" );
define ( "BDD_DB_NAME", "BDD_DB_NAME" );
define ( "BDD_DB_PORT", "BDD_DB_PORT");
define ( "BDD_USER", "BDD_USER" );
define ( "BDD_PASSWORD", "BDD_PASSWORD" );
define ( "OBJET_EMAIL_DEBUT", "OBJET_EMAIL_DEBUT" );
define ( "FROM_EMAIL_DEBUT", "FROM_EMAIL_DEBUT" );
define ( "OBJET_EMAIL_RELANCE", "OBJET_EMAIL_RELANCE" );
define ( "FROM_EMAIL_RELANCE", "FROM_EMAIL_RELANCE" );
define ( "OBJET_EMAIL_RECAP", "OBJET_EMAIL_RECAP" );
define ( "FROM_EMAIL_RECAP", "FROM_EMAIL_RECAP" );
define ("LDAP_SERVER","LDAP_SERVER");
define ("LDAP_PORT","LDAP_PORT");
define("LDAP_USER","LDAP_USER");
define("LDAP_MDP","LDAP_MDP");
define("LDAP_DC","LDAP_DC");
define("LDAP_VERSION", "LDAP_VERSION");
define("LDAP_CHAMP_IDENTIFIANT","LDAP_CHAMP_IDENTIFIANT");
define("LDAP_CHAMP_MAIL","LDAP_CHAMP_MAIL");
define("LDAP_CHAMP_NOM","LDAP_CHAMP_NOM");
define("LDAP_CHAMP_PRENOM","LDAP_CHAMP_PRENOM");

// extension csv
define ( "EXTENSION_CSV", "csv" );
define ( "CSV_CARACTERE_SEPARATEUR", ";" );

define ( "ID_RETOUR_ARRIERE", 0 );

define ( "TITRE_APPLICATION", "Passer &agrave; LibreOffice" );
// les titres
define ( "TITRE_GESTION_CAMPAGNE", "LibreOffice - Gestion des campagnes" );
define ( "TITRE_CONSULTATION_CAMPAGNE", "LibreOffice Now - Consultation" );
define ( "TITRE_MIGRATION", "LibreOffice Now!" );
// les sous titres de niveau un
define ( "SOUS_TITRE_UN_GESTION_CAMPAGNE_DETAIL", "D&eacute;tail de la campagne n&deg;" );
define ( "SOUS_TITRE_UN_GESTION_CAMPAGNE_CREER", "Cr&eacute;er une campagne" );
define ( "SOUS_TITRE_UN_GESTION_CAMPAGNE_MODIFIER", "Modification de la campagne n&deg;" );
// les sous titres de niveau deux
define ( "SOUS_TITRE_DEUX_GESTION_CAMPAGNE_ACCUEIL", "Liste des campagnes - on ne peut agir que sur mes campagnes pas encore termin&eacute;es (date de migration pas atteinte)" );
// message
define ( "MESSAGE_INFO_MIGATION", "Bienvenue dans l'outil de gestion de migration vers LibreOffice.<br /><br />Cet outil vous permet de pr&eacute;parer la configuration de votre poste de travail en vue de la migration.<br />Celle-ci sera r&eacute;alis&eacute;e automatiquement &agrave; la date qui vous a &eacute;t&eacute; communiqu&eacute;e par email en prenant en compte les &eacute;l&eacute;ments que vous aurez communiqu&eacute;s.<br /><br />Vous allez r&eacute;pondre &agrave; 2 s&eacute;ries de questions qui vont permettre de configurer votre poste de travail de fa&ccedil;on optimale et de vous aiguiller vers le dispositif de formation et d'accompagnement adapt&eacute; &agrave; vos besoins.<br />En cas de non r&eacute;ponse, vous pourrez &ecirc;tre bloqu&eacute; dans l'utilisation de vos outils de travail et serez invit&eacute; &agrave; revenir sur le questionnaire pour le compl&eacute;ter.<br /><br />Nous vous invitons donc &agrave; le compl&eacute;ter de fa&ccedil;on pr&eacute;cise et dans son int&eacute;gralit&eacute;, cela devrait vous prendre de 2 &agrave; 5 minutes.<br /><br />Si vous souhaitez avoir plus d'information concernant la migration, l'intranet contient une guide et une pr&eacute;sentation d&eacute;taill&eacute;e de la migration et de son d&eacute;roulement.<br /><a href=\"http://www.metropole.nantes.net/NantesMetropole/?PubliId=316815\" target=\"_blank\">http://www.metropole.nantes.net/NantesMetropole/?PubliId=316815</a><br /><br />Pour d&eacute;marrer le questionnaire, veuillez cliquer sur le bouton ci-dessous." );
define ( "MESSAGE_INFO_DEJA_PASSE", "Vous &ecirc;tes d&eacute;j&agrave; pass&eacute; &agrave; LibreOffice, que voulez-vous faire ?" );
define ("MESSAGE_INFO_MIGRATION_ACHEVEE", "Votre migration est achev&#233;e, veuillez consulter l'intranet ou contacter le STP si vous rencontrez des probl&#232;mes.");
define ( "TITRE_RECAPITULATIF_VALIDATION", "R&eacute;capitulatif et validation" );
// les variables de sessions
define ( "ID_AGENT_CONNECTER", "ID_AGENT_CONNECTER" );
define ("LECTURE_FICHIER_CSV", "LECTURE_FICHIER_CSV");
define ( "PAGE_EN_COURS", "PAGE_EN_COURS" );
define ( "PAGE_TOTAL", "PAGE_TOTAL" );
define ( "ID_AGENT_CAMPAGNE", "ID_AGENT_CAMPAGNE" );
define ( "LISTE_GROUPE_AGENT", "LISTE_GROUPE_AGENT" );
define ( "NUMERO_QUESTION", "NUMERO_QUESTION" );
define ( "NOMBRE_ETAPE_TOTAL", "NOMBRE_ETAPE_TOTAL" );
define ( "INPUT_HIDDEN_NOMBRE_REPONSE_ACTIVE", "nbReponseActive" );

define ( "PAGE_PHP_ACTION", "index.php" );
//
define ( "MIGRATION_ACTION_SUIVANT", "Etape suivante" );
define ( "MIGRATION_ACTION_SOUHAITE_REVENIR_ARRIERE", "Je souhaite revenir en arri&egrave;re" );
define ( "MIGRATION_ACTION_TROMPE", "Je me suis tromp&eacute; en remplissant mon questionnaire" );
define ( "MIGRATION_ACTION_PRECEDENTE", "Etape pr&eacute;c&eacute;dente" );
define ( "SIGNE_NAVIGATION_ETAPE", "inputHiddenSigneNavigationEtape" );
// les actions
define ( "TYPE_ACTION", "typeAction" );
define ( "ACTION_GESTION_CAMPAGNE", "gestionCampagne" );
define ( "ACTION_GESTION_CAMPAGNE_CONSULTATION", "gestionCampagneConsultation" );
define ( "ACTION_GESTION_MIGRATION", "gestionMigration" );
define ( "INPUT_ID_AGENT", "idAgent" );
define ( "INPUT_AJOUTER_ID_AGENT", "idAjoutAgent" );
define ( "CHECK_REPONSE", "reponse_" );

// la page dans l'action
define ( "ID_PAGE", "idPage" );
// format de la date
define ( "FORMAT_DATE_JJ_MM_AAAA", "d/m/Y" );
define ( "FORMAT_DATE_AAAA_MM_JJ", "Y-m-d" );
define ( "FORMAT_DATE_ERREUR_TECHNIQUE", "YmdHis" );

// campagne accueil
define ( "CAMPAGNE_ACCUEIL_TABLEAU_COL_NUMERO", "Num&eacute;ro" );
define ( "CAMPAGNE_ACCUEIL_TABLEAU_COL_DESCRIPTION", "Description" );
define ( "CAMPAGNE_ACCUEIL_TABLEAU_COL_DATE_DEPART", "Date de d&eacute;part" );
define ( "CAMPAGNE_ACCUEIL_TABLEAU_COL_DATE_BUTOIR", "Date butoir questionnaire" );
define ( "CAMPAGNE_ACCUEIL_TABLEAU_COL_DATE_MIGRATION", "Date de migration" );
define ( "CAMPAGNE_ACTION_AJOUTER", "Ajouter une campagne" );
// campagne Detail
define ( "CAMPAGNE_DETAIL_DESCRIPTION", "Description" );
define ( "CAMPAGNE_DETAIL_DATE_DEPART", "Date de d&eacute;part (mail initial)" );
define ( "CAMPAGNE_DETAIL_DATE_BUTOIR", "Date butoir questionnaire" );
define ( "CAMPAGNE_DETAIL_DATE_MIGRATION", "Date de migration effective" );
define ( "CAMPAGNE_DETAIL_DELAI_AVANT_RELANCE", "D&eacute;lai avant relance" );
define ( "CAMPAGNE_DETAIL_TABLEAU_COL_IDENTIFIANT", "Identifiant" );
define ( "CAMPAGNE_DETAIL_TABLEAU_COL_NOM", "Nom" );
define ( "CAMPAGNE_DETAIL_TABLEAU_COL_PRENOM", "Pr&eacute;nom" );
define ( "CAMPAGNE_ACTION_RETOUR_A_LA_LISTE", "Retour &agrave; la liste" );
define ( "CAMPAGNE_ACTION_MODIFIER", "Modifier" );
define ( "CAMPAGNE_ACTION_MAJ_VALIDER", "Valider" );
define ( "CAMPAGNE_DETAIL_AJOUTER_UN_AGENT", "Ajouter un agent" );
define ( "CAMPAGNE_DETAIL_LISTE_AGENTS", "Liste des agents" );
// campagne creer
define ( "CAMPAGNE_CREER_LISTE_AGENTS", "Liste des agents" );
define ( "CAMPAGNE_ACTION_CREER", "Valider" );
// modifier
define ( "BOUTON_VALUE_AJOUTER_UN_AGENT", "Ajouter un agent" );
define ( "ACTION_VALUE_AJOUTER_UN_AGENT", "Ajouter" );
// name input text campagne
define ( "CAMPAGNE_INPUT_NAME_FICHIER", "FichierCsv" );
define ( "CAMPAGNE_INPUT_NAME_NUMERO", "inputNameNumero" );
define ( "CAMPAGNE_INPUT_NAME_DESCRIPTION", "inputNameDescription" );
define ( "CAMPAGNE_INPUT_NAME_DATE_DEPART", "inputNameDateDepart" );
define ( "CAMPAGNE_INPUT_NAME_DATE_BUTOIR", "inputNameDateButoir" );
define ( "CAMPAGNE_INPUT_NAME_DATE_MIGRATION", "inputNameDateMigrtation" );
define ( "CAMPAGNE_INPUT_NAME_DELAI", "inputNameDelai" );
define ( "MESSAGE_EST_OBLIGATOIRE", " est obligatoire" );
define ( "MESSAGE_DOIT_ETRE_AVANT", " doit &ecirc;tre avant " );
define ( "MESSAGE_ERREUR_CAMPAGNE_DESCRIPTION", CAMPAGNE_DETAIL_DESCRIPTION . MESSAGE_EST_OBLIGATOIRE );
define ( "MESSAGE_ERREUR_CAMPAGNE_DATE_DEPART", CAMPAGNE_DETAIL_DATE_DEPART . MESSAGE_EST_OBLIGATOIRE );
define ( "MESSAGE_ERREUR_CAMPAGNE_DATE_BUTOIR", CAMPAGNE_DETAIL_DATE_BUTOIR . MESSAGE_EST_OBLIGATOIRE );
define ( "MESSAGE_ERREUR_CAMPAGNE_DATE_MIGRATION", CAMPAGNE_DETAIL_DATE_MIGRATION . MESSAGE_EST_OBLIGATOIRE );
define ( "MESSAGE_ERREUR_CAMPAGNE_DELAI", CAMPAGNE_DETAIL_DELAI_AVANT_RELANCE . MESSAGE_EST_OBLIGATOIRE );
define ( "MESSAGE_ERREUR_CAMPAGNE_DATE_DEPART_AVANT_DATE_BUTOIR", CAMPAGNE_DETAIL_DATE_DEPART . MESSAGE_DOIT_ETRE_AVANT . CAMPAGNE_DETAIL_DATE_BUTOIR );
define ( "MESSAGE_ERREUR_CAMPAGNE_DATE_JOUR_AVANT_DATE_DEPART", "La date du jour " . MESSAGE_DOIT_ETRE_AVANT . CAMPAGNE_DETAIL_DATE_DEPART );
define ( "MESSAGE_ERREUR_CAMPAGNE_DATE_BUTOIR_AVANT_DATE_MIGRATION", CAMPAGNE_DETAIL_DATE_BUTOIR . MESSAGE_DOIT_ETRE_AVANT . CAMPAGNE_DETAIL_DATE_MIGRATION );
define ( "MESSAGE_ERREUR_CAMPAGNE_DESCRIPTION_EXISTE_DEJA", "La description existe d&eacute;j&agrave;" );
define ( "MESSAGE_ERREUR_IMPORT_FICHIER_CSV", "Le fichier n'est pas de type " . EXTENSION_CSV );
// agent
define ( "MESSAGE_ERREUR_AGENT_OBLIGATOIRE", "L'identifiant de l'agent" . MESSAGE_EST_OBLIGATOIRE );
// consultation recherche
define ( "CAMPAGNE_CONSULTATION_IDENTIFIANT_UTILISATEUR", "identifiantUtilisateur" );
// liste des statuts de migration
define ( "STATUT_MIGRATION_NON_MIGRE", "Non migr&eacute;" );
define ( "STATUT_MIGRATION_CAMPAGNE_EN_COURS", "Campagnes en cours" );
define ( "STATUT_MIGRATION_MIGRE_VOLONTAIRE", "Migr&eacute; (volontaire)" );
define ( "STATUT_MIGRATION_MIGRE_CAMPAGNE", "Migr&eacute; (campagne)" );
// campagne consultation
define ( "MESSAGE_ID_AGENT", "L'agent" . MESSAGE_EST_OBLIGATOIRE );
define ( "MESSAGE_AGENT_NON_PRESENT_LDAP", "L&prime;agent n&prime;est pas pr&eacute;sent dans le LDAP" );

define ( "PAGE_MODIFIER_CAMPAGNE", "4" );
define ( "PAGE_AJOUTER_CAMPAGNE", "5" );
define ( "PAGE_VALIDER_CREATION_CAMPAGNE", "6" );
define ( "PAGE_VALIDER_MAJ_CAMPAGNE", "7" );

define ( "ID_NUMERO_CAMPAGNE", "numero" );
define ( "SESSION_QUESTION_REPONSE", "SESSION_QUESTION_REPONSE" );
define ( "SESSION_LIBELLE_REPONSE", "SESSION_LIBELLE_REPONSE" );
define ( "MESSAGE_CONSULATION_AGENT", "Agent" );
define ( "MESSAGE_CONSULATION_STATUT_MIGRATION", "Statut migration" );
define ( "MESSAGE_CONSULATION_NUMERO_CAMPAGNE", "Num&eacute;ro de campagne" );
define ( "MESSAGE_CONSULATION_DATE_MIGRATION", "Date de migration" );
// statut de migration
define ( "STATUT_NON_MIGRE", "Non migr&eacute;" );
define ( "STATUT_CAMPAGNE_EN_COURS", "Campagne en cours" );
define ( "STATUT_MIGRE_CAMPAGNE", "Migr&eacute; (campagne)" );
define ( "STATUT_MIGRE_VOLONTAIRE", "Migr&eacute; (volontaire)" );
define ( "MESSAGE_RETOUR_ARRIERE", "Retour arri&egrave;re" );
define ( "MESSAGE_REPONSES_AUX_QUESTIONS", "R&eacute;ponses aux questions" );
define ( "LIBELLE_DATE_MIGRATION", "LIBELLE_DATE_MIGRATION" );
define ( "LIBELLE_RA", "LIBELLE_RA" );
?>