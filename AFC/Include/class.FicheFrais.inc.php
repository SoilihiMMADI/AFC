<?php

require_once './Include/class.pdogsb.inc.php';
require_once './Include/fct.inc.php';
require_once './Include/class.Frais.inc.php';

final class FicheFrais {

    private $idVisiteur;
    private $moisFiche;
    private $nbJustificatifs = 0;
    private $montantValide = 0;
    private $dateDerniereModif;
    private $idEtat;
    private $libelleEtat;
    static $pdo;

    /**
     * On utilise 2 collections pour stocker les frais :
     * plus efficace car on doit extraire soit les FF soit les FHF.
     * Avec une seule collection on serait toujours obligé de parcourir et
     * de tester le type de tous les frais avant de les extraires.
     *
     */
    private $lesFraisForfaitises = []; // Un tableau associatif de la forme : <idCategorie>, <objet FraisForfaitise>
    private $lesFraisHorsForfait = [];

    /**
     * Un tableau des numéros de ligne des frais forfaitisés.
     * Les lignes de frais forfaitisés sont numérotées en fonction de leur catégorie.
     * Le tableau est static ce qui évite de le déclarer dans chaque instance de
     * FicheFrais.
     *
     */
    static private $tabNumLigneFraisForfaitise = ['ETP' => 1,
        'KM' => 2,
        'NUI' => 3,
        'REP' => 4];

    function __construct($unIdVisiteur, $unMoisFiche) {
        $this->idVisiteur = $unIdVisiteur;
        $this->moisFiche = $unMoisFiche;
        self::$pdo = PdoGsb::getPdoGsb();
    }

    public function initAvecInfosBDD() {

        $this->initInfosFicheSansLesFrais();
        $this->initLesFraisForfaitises();
        $this->initLesFraisHorsForfait();
    }

    public function initAvecInfosBDDSansFF() {

        $this->initInfosFicheSansLesFrais();
        $this->initLesFraisHorsForfait();
    }
    
    public function initAvecInfosBDDSansFHF() {

        $this->initInfosFicheSansLesFrais();
        $this->initLesFraisForfaitises();
    }

    private function initInfosFicheSansLesFrais() {

        $req = self::$pdo->getInfosFiche($this->idVisiteur, $this->moisFiche);
        if ($req->rowCount() == 0) {
            $this->idEtat = '00';
        } else {
            $req = $req->fetch(PDO::FETCH_ASSOC);
            $this->nbJustificatifs = $req['FICHE_NB_JUSTIFICATIFS'];
            $this->idEtat = $req['EFF_ID'];
            $this->libelleEtat = $req['EFF_LIBELLE'];
            $this->montantValide = $req['FICHE_MONTANT_VALIDE'];
            $this->dateDerniereModif = $req['FICHE_DATE_DERNIERE_MODIF'];
        }
    }

    private function initLesFraisForfaitises() {

        $req = self::$pdo->getLignesFF($this->idVisiteur, $this->moisFiche);
        
        foreach ($req as $ligne) {
            $categorieFraisForfaitise = new CategorieFraisForfaitise($ligne['idCategorie'], $ligne['libelle'], $ligne['montant']);
            $fraisForfaitise = new FraisForfaitise($this->idVisiteur, $this->moisFiche, $ligne['numFrais'], $ligne['quantite'], $categorieFraisForfaitise);
            $this->lesFraisForfaitises[$ligne['idCategorie']] = $fraisForfaitise;
        }
    }

    private function initLesFraisHorsForfait() {
        $req = self::$pdo->getLignesFHF($this->idVisiteur, $this->moisFiche);
        
        foreach ($req as $ligne) {
            $fraisHorsForfait = new FraisHorsForfait($this->idVisiteur, $this->moisFiche, $ligne['FRAIS_NUM'], $ligne['LFHF_LIBELLE'], $ligne['LFHF_DATE'], $ligne['LFHF_MONTANT']);
            $this->lesFraisHorsForfait[] = $fraisHorsForfait;
        }
    }

    public function getLibelleEtat() {
        return $this->libelleEtat;
    }

    public function getNbJustificatitfs() {
        return $this->nbJustificatifs;
    }

    /**
     *
     * Retourne un tableau contenant les quantités pour chaque ligne de frais
     * forfaitisé de la fiche de frais.
     *
     * @return array Le tableau demandé.
     */
    public function getLesQuantitesDeFraisForfaitises() {
        $lesQuantites = [];
        foreach ($this->lesFraisForfaitises as $uneLFF) {
            $lesQuantites[] = $uneLFF->getQuantite();
        }
        return $lesQuantites;
    }

    /**
     *
     * Ajoute à la fiche de frais un frais forfaitisé (une ligne) dont
     * l'id de la catégorie et la quantité sont passés en paramètre.
     * Le numéro de la ligne est automatiquement calculé à partir de l'id de
     * sa catégorie.
     *
     * @param string $idCategorie L'ide de la catégorie du frais forfaitisé.
     * @param int $quantite Le nombre d'unité(s).
     */
    public function ajouterUnFraisForfaitise($idCategorie, $quantite) {
        $req = self::$pdo->getInfosCategorieFrais($idCategorie);
        $categorieFraisForfaitise = new CategorieFraisForfaitise($idCategorie, $req['CFF_LIBELLE'], $req['CFF_MONTANT']);
        $fraisForfaitise = new FraisForfaitise($this->idVisiteur, $this->moisFiche, $this->getNumLigneFraisForfaitise($idCategorie), $quantite, $categorieFraisForfaitise);
        $this->lesFraisForfaitises[$idCategorie] = $fraisForfaitise;
    }

    /**
     *
     * Ajoute à la fiche de frais un frais forfaitisé (une ligne) dont
     * l'id de la catégorie et la quantité sont passés en paramètre.
     * Le numéro de la ligne est automatiquement calculé à partir de l'id de
     * sa catégorie.
     *
     * @param int $numFrais Le numéro de la ligne de frais hors forfait.
     * @param string $libelle Le libellé du frais.
     * @param string $date La date du frais, sous la forme AAAA-MM-JJ.
     * @param float $montant Le montant du frais.
     * @param string $action L'action à réaliser éventuellement sur le frais.
     */
    public function ajouterUnFraisHorsForfait($numFrais, $libelle, $date, $montant, $action = NULL) {
        $fraisHorsForfait = new FraisHorsForfait($this->idVisiteur, $this->moisFiche, $numFrais, $libelle, $date, $montant, $action);
            $this->lesFraisHorsForfait[] = $fraisHorsForfait;
    }

    /**
     *
     * Retourne la collection des frais forfaitisés de la fiche de frais.
     *
     * @return array La collections des frais forfaitisés.
     */
    public function getLesFraisForfaitises() {

        return $this->lesFraisForfaitises;
    }

    /**
     *
     * Retourne la collection des frais forfaitisés de la fiche de frais.
     *
     * @return array la collections des frais forfaitisés.
     */
    public function getLesFraisHorsForfait() {
        return $this->lesFraisHorsForfait;
    }

    /**
     *
     * Retourne un tableau associatif d'informations sur les frais forfaitisés
     * de la fiche de frais :
     * - le numéro du frais (numFrais),
     * - son libellé (libelle),
     * - sa date (date),
     * - son montant (montant),
     * - son action (action).
     *
     * @return array Le tableau demandé.
     */
    public function getLesInfosFraisHorsForfait() {
        $infosFHF = [];
        foreach ($this->lesFraisHorsForfait  as $unFHF) {
                $infosFHF [] = ['libelle'=>$unFHF->getLibelle(), 'numFrais'=>$unFHF->getNumFrais(), 'date'=>$unFHF->getDate(), 'montant'=>$unFHF->getMontant(), 'action'=>$unFHF->getAction()];                
        }
        return $infosFHF;
    }

    /**
     *
     * Retourne le numéro de ligne d'un frais forfaitisé dont l'identifiant de
     * la catégorie est passé en paramètre.
     * Chaque fiche de frais comporte systématiquement 4 lignes de frais forfaitisés.
     * Chaque ligne de frais forfaitisé correspond à une catégorie de frais forfaitisé.
     * Les lignes de frais forfaitisés d'une fiche sont numérotées de 1 à 4.
     * Ce numéro dépend de la catégorie de frais forfaitisé :
     * - ETP : 1,
     * - KM  : 2,
     * - NUI : 3,
     * - REP : 4.
     *
     * @param string $idCategorieFraisForfaitise L'identifiant de la catégorie de frais forfaitisé.
     * @return int Le numéro de ligne du frais.
     *
     */
    private function getNumLigneFraisForfaitise($idCategorieFraisForfaitise) {
        $numFrais = self::$tabNumLigneFraisForfaitise[$idCategorieFraisForfaitise];
        return $numFrais;
    }
    
    public function getIdEtat() {
        return $this->idEtat;
    }

    /**
     *
     * Contrôle que les quantités de frais forfaitisés passées en paramètre
     * dans un tableau sont bien des numériques entiers et positifs.
     * Cette méthode s'appuie sur la fonction lesQteFraisValides().
     *
     * @return booléen Le résultat du contrôle.
     */
    public function controlerQtesFraisForfaitises() {
        if (lesQteFraisValides($this->getLesQuantitesDeFraisForfaitises())) {
            return true;
        } else {
            return false;
        }
    }
    
    public function controlerNbJustificatifs() {
        if(estEntierPositif($this->nbJustificatifs)){
            return true;
        }else {
            return false;
        }
        
    }

    /**
     *
     * Met à jour dans la base de données les quantités des lignes de frais forfaitisées.
     *
     * @return bool Le résultat de la mise à jour.
     *
     */
    public function mettreAJourLesFraisForfaitises() {
        $tab2DFF = [];
        $numLigne = 0;
        $quantite = 0;
        $i = 0;
        foreach ($this->lesFraisForfaitises as $unFF) {
            $quantite = $unFF->getQuantite();
            $numLigne = $unFF->getNumFrais();
            $tab2DFF [$i] = [$numLigne, $quantite];
            $i++;
        }
        return self::$pdo->setLesQuantitesFraisForfaitises($this->idVisiteur, $this->moisFiche, $tab2DFF);
    }
    
    public function mettreAJourLesFraisHorsForfait(){
        $tab2DFF = [];
        $numLigne = 0;
        $action = '';
        $i = 0;
        foreach ($this->lesFraisHorsForfait as $unFF) {
            $action = $unFF->getAction();
            $numLigne = $unFF->getNumFrais();
            $tab2DFF [$i] = [$numLigne, $action];
            $i++;
        }
        return self::$pdo->setLesFraisHorsForfait($this->idVisiteur, $this->moisFiche, $tab2DFF, $this->nbJustificatifs);
    }
    
    public function setNbJustificatifs($nb) {
        $this->nbJustificatifs = $nb;
    }
    
    public function calculerLeMontantValide(){
        $lesFrais = array_merge($this->lesFraisForfaitises, $this->lesFraisHorsForfait);
        $lesMontants = 0;
        foreach($lesFrais as $unF){
            $lesMontants += $unF->getMontant();
        }
        return $lesMontants;
    }
    
    public function valider(){     
        return self::$pdo->majFicheFrais($this->idVisiteur, $this->moisFiche, 'VA', $this->calculerLeMontantValide());
    }

}
