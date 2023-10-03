<?php

/**
 * Classe Frais
 *
 */
abstract class Frais {

    protected $idVisiteur;
    protected $moisFicheFrais;
    protected $numFrais;

    /**
     * Constructeur de la classe.
     *
     *  Rappel : en PHP le constructeur est toujours nommé
     *          __construct().
     *
     */
    public function __construct($unIdVisiteur, $unMoisFicheFrais, $unNumFrais) {
        $this->idVisiteur = $unIdVisiteur;
        $this->moisFicheFrais = $unMoisFicheFrais;
        $this->numFrais = $unNumFrais;
    }

    /**
     * Retourne l'id du visiteur.
     *
     * @return string L'id du visiteur.
     */
    public function getIdVisiteur() {
        return $this->idVisiteur;
    }

    /**
     * Retourne le mois de la fiche de frais.
     *
     * @return string Le mois de la fiche.
     */
    public function getMoisFiche() {
        return $this->moisFicheFrais;
    }

    /**
     * Retourne le numéro du frais (de la ligne).
     *
     * @return int Le numéro du frais.
     */
    public function getNumFrais() {
        return $this->numFrais;
    }

    abstract public function getMontant();

}

final class FraisForfaitise extends Frais {
    private $quantite;
    private $categorieFraisForfaitise;
    
    public function __construct($unIdVisiteur, $unMoisFicheFrais, $unNumFrais, $uneQuantite, $uneCategorieFraisForfaitise) {
        parent::__construct($unIdVisiteur, $unMoisFicheFrais, $unNumFrais);
        $this->quantite = $uneQuantite;
        $this->categorieFraisForfaitise = $uneCategorieFraisForfaitise;
        }
       
    public function getQuantite(){
        return (int)$this->quantite;
    }
    
    public function getLaCategorieFraisForfaitise(){
        return $this->categorieFraisForfaitise;
    }
    
    public function getMontant() {
        $montant = ($this->quantite * $this->categorieFraisForfaitise->getMontant());
        return $montant;
    }

}

final class FraisHorsForfait extends Frais {
    private $libelle;
    private $date;
    private $montant;
    private $action;


    public function __construct($unIdVisiteur, $unMoisFicheFrais, $unNumFrais, $unLibelle, $uneDate, $unMontant, $uneAction = 'O') {
        parent::__construct($unIdVisiteur, $unMoisFicheFrais, $unNumFrais);
        $this->libelle = $unLibelle;
        $this->date = $uneDate;
        $this->montant = $unMontant;
        $this->action = $uneAction;
        }
    
    public function getLibelle(){
        return $this->libelle;
    }
    
    public function getDate(){
        return $this->date;
    }
    
    public function getMontant() {
        return $this->montant;
    }
    
    public function getAction() {
        return $this->action;
    }
}

