<?php

// include_once 'includes/php/pojo/Reponse.php';

/**
 *
 * @author m.emschwiller
 *        
 */
class QuestionXML {
	private $s_identifiant;
	private $s_titre;
	private $s_libelle;
	private $s_libelleNiveau2;
	private $l_reponses = array ();
	private $i_nbQuestionColonne;
	private $s_libelle_recapitulatif;
	
	/**
	 * constructeur de la question
	 *
	 * @param string $s_identifiant        	
	 * @param string $s_libelle        	
	 * @param string $s_libelleNiveau2        	
	 * @param string $s_titre        	
	 */
	function __construct($s_identifiant, $s_libelle, $s_libelleNiveau2 = '', $s_titre, $i_nbQuestionColonne, $s_libelle_recapitulatif) {
		$this->setIdentifiant ( $s_identifiant );
		$this->setLibelle ( $s_libelle );
		$this->setLibelleNiveau2 ( $s_libelleNiveau2 );
		$this->setTitre ( $s_titre );
		$nb_repActif = 0;
		$this->setNbQuestionColonne ( $i_nbQuestionColonne );
		$this->setLibelleRecapitulatif ( $s_libelle_recapitulatif );
	}
	
	/**
	 */
	public function getIdentifiant() {
		return $this->s_identifiant;
	}
	
	/**
	 *
	 * @param unknown $s_identifiant        	
	 */
	public function setIdentifiant($s_identifiant) {
		$this->s_identifiant = $s_identifiant;
	}
	/**
	 */
	public function getLibelle() {
		return $this->s_libelle;
	}
	
	/**
	 *
	 * @param unknown $s_libelle        	
	 */
	public function setLibelle($s_libelle) {
		$this->s_libelle = $s_libelle;
	}
	
	/**
	 */
	public function getLibelleNiveau2() {
		return $this->s_libelleNiveau2;
	}
	
	/**
	 *
	 * @param unknown $s_libelleNiveau2        	
	 */
	public function setLibelleNiveau2($s_libelleNiveau2) {
		$this->s_libelleNiveau2 = $s_libelleNiveau2;
	}
	
	/**
	 */
	public function getNbQuestionColonne() {
		return $this->i_nbQuestionColonne;
	}
	
	/**
	 *
	 * @param unknown $i_nbQuestionColonne        	
	 */
	public function setNbQuestionColonne($i_nbQuestionColonne) {
		$this->i_nbQuestionColonne = $i_nbQuestionColonne;
	}
	
	/**
	 */
	public function getTitre() {
		return $this->s_titre;
	}
	
	/**
	 *
	 * @param unknown $s_titre        	
	 */
	public function setTitre($s_titre) {
		$this->s_titre = $s_titre;
	}
	
	/**
	 *
	 * @return multitype:
	 */
	public function getListeReponses() {
		return $this->l_reponses;
	}
	
	/**
	 *
	 * @param unknown $l_reponses        	
	 */
	public function setListeReponses($l_reponses) {
		$this->l_reponses = $l_reponses;
	}
	
	/**
	 * ajouter une reponse a la liste des reponses
	 *
	 * @param unknown $r_reponse        	
	 */
	public function addReponse($r_reponse) {
		$taille = sizeof ( $this->l_reponses );
		$this->l_reponses [$taille + 1] = $r_reponse;
		if (1 == $r_reponse->getactif ()) {
			$this->nb_repActif ++;
		}
	}
	
	/**
	 * retourn le nombre de reponse
	 */
	public function getNbRep() {
		return sizeof ( $this->l_reponses );
	}
	
	/**
	 */
	public function getLibelleRecapitulatif() {
		return $this->s_libelle_recapitulatif;
	}
	
	/**
	 *
	 * @param unknown $s_libelle_recapitulatif        	
	 */
	public function setLibelleRecapitulatif($s_libelle_recapitulatif) {
		$this->s_libelle_recapitulatif = $s_libelle_recapitulatif;
	}
	
	/**
	 *
	 * @return string
	 */
	public function __toString() {
		return 'identifiant : ' . $this->s_identifiant . ',libelle : ' . $this->s_libelle;
	}
}
?>