<?php

/**
 * Classe d'accès aux données.

 * Utilise les services de la classe PDO
 * pour l'application GSB
 * Les attributs sont tous statiques,
 * les 4 premiers pour la connexion
 * $monPdo de type PDO
 * $monPdoGsb qui contiendra l'unique instance de la classe

 * @package default
 * @author Cheri Bibi
 * @version    1.0
 * @link       http://www.php.net/manual/fr/book.pdo.php
 */
class PdoGsb {

    private static $serveur = 'sqlsrv:server=LAPTOP-14IFAF3V';
    //private static $bdd='dbname=gsbV2';
    private static $bdd = 'Database=GSB_VALIDE_V2';
    private static $user = 'sa';
    private static $mdp = 'sa2019';
    private static $monPdo;
    private static $monPdoGsb = null;

    /**
     * Constructeur privé, crée l'instance de PDO qui sera sollicitée
     * pour toutes les méthodes de la classe
     *
     * @version 1.1 Utilise self:: en lieu et place de PdoGsb::
     *
     */
    private function __construct() {
        self::$monPdo = new PDO(self::$serveur . ';' . self::$bdd, self::$user, self::$mdp);
        self::$monPdo->query("SET CHARACTER SET utf8");
    }

    public function _destruct() {
        self::$monPdo = null;
    }

    /**
     * Fonction statique qui crée l'unique instance de la classe

     * Appel : $instancePdoGsb = PdoGsb::getPdoGsb();

     * @return l'unique objet de la classe PdoGsb
     *
     * @version 1.1 Utilise self:: en lieu et place de PdoGsb::
     *
     */
    public static function getPdoGsb() {
        if (self::$monPdoGsb == null) {
            self::$monPdoGsb = new PdoGsb();
        }
        return self::$monPdoGsb;
    }

    /**
     * Retourne les informations d'un visiteur

     * @param $login
     * @param $mdp
     * @return l'id, le nom et le prénom sous la forme d'un tableau associatif
     */
    public function getInfosVisiteur($login, $mdp) {
        $req = "exec infos_visiteur '$login', '$mdp'";
        $rs = PdoGsb::$monPdo->query($req);
        $ligne = $rs->fetch();
        return $ligne;
    }

    public function getInfosComptable($login, $mdp) {
        $req = "exec infos_comptable '$login', '$mdp'";
        $rs = PdoGsb::$monPdo->query($req);
        $ligne = $rs->fetch();
        return $ligne;
    }

    public function getVisiteurs() {
        $req = "exec les_visiteurs";
        $res = PdoGsb::$monPdo->query($req);
        $lignes = $res->fetchAll();
        return $lignes;
    }

    public function getInfosFiche($idVisi, $mois) {
        $req = "exec infos_fiche_frais_visiteur '$idVisi', '$mois'";
        $res = PdoGsb::$monPdo->query($req);
        //$lignes = $res->fetchAll();
        return $res;
    }

    /**
     * Retourne sous forme d'un tableau associatif toutes les lignes de frais hors forfait
     * concernées par les deux arguments

     * La boucle foreach ne peut être utilisée ici car on procède
     * à une modification de la structure itérée - transformation du champ date-

     * @param $idVisiteur
     * @param $mois sous la forme aaaamm
     * @return tous les champs des lignes de frais hors forfait sous la forme d'un tableau associatif
     */
    public function getLignesFHF($idVisiteur, $mois) {
        $req = "exec LES_FRAIS_HORS_FORFAIT '$idVisiteur', '$mois'";
        $res = PdoGsb::$monPdo->query($req);
        $lesLignes = $res->fetchAll();
        $nbLignes = count($lesLignes);
        for ($i = 0; $i < $nbLignes; $i++) {
            $date = $lesLignes[$i]['LFHF_DATE'];
            $lesLignes[$i]['LFHF_DATE'] = dateAnglaisVersFrancais($date);
        }
        return $lesLignes;
    }

    /**
     * Retourne le nombre de justificatif d'un visiteur pour un mois donné

     * @param $idVisiteur
     * @param $mois sous la forme aaaamm
     * @return le nombre entier de justificatifs
     */
    public function getNbjustificatifs($idVisiteur, $mois) {
        $req = "exec nb_justificatifs '$idVisiteur', '$mois'";
        $res = PdoGsb::$monPdo->query($req);
        $laLigne = $res->fetch();
        return $laLigne['nb'];
    }

    /**
     * Retourne sous forme d'un tableau associatif toutes les lignes de frais au forfait
     * concernées par les deux arguments

     * @param $idVisiteur
     * @param $mois sous la forme aaaamm
     * @return l'id, le libelle et la quantité sous la forme d'un tableau associatif
     */
    public function getLignesFF($idVisiteur, $mois) {
        $req = "exec LES_FRAIS_FORFAIT '$idVisiteur', '$mois'";
        $res = PdoGsb::$monPdo->query($req);
        $lesLignes = $res->fetchAll();
        return $lesLignes;
    }

    /**
     * Retourne tous les id de la table FraisForfait

     * @return un tableau associatif
     */
    public function getLesIdFrais() {
        $req = "select fraisforfait.id as idfrais from fraisforfait order by fraisforfait.id";
        $res = PdoGsb::$monPdo->query($req);
        $lesLignes = $res->fetchAll();
        return $lesLignes;
    }

    /**
     * Met à jour la table ligneFraisForfait

     * Met à jour la table ligneFraisForfait pour un visiteur et
     * un mois donné en enregistrant les nouveaux montants

     * @param $idVisiteur
     * @param $mois sous la forme aaaamm
     * @param $lesFrais tableau associatif de clé idFrais et de valeur la quantité pour ce frais
     * @return un tableau associatif
     */
    public function majFraisForfait($idVisiteur, $mois, $lesFrais) {
        $lesCles = array_keys($lesFrais);
        foreach ($lesCles as $unIdFrais) {
            $qte = $lesFrais[$unIdFrais];
            $req = "exec SP_LIGNE_FF_MAJ '$idVisiteur', '$mois', '$unIdFrais', '$qte'";
            PdoGsb::$monPdo->exec($req);
        }
    }

    /**
     * met à jour le nombre de justificatifs de la table ficheFrais
     * pour le mois et le visiteur concerné

     * @param $idVisiteur
     * @param $mois sous la forme aaaamm
     */
    public function majNbJustificatifs($idVisiteur, $mois, $nbJustificatifs) {
        $req = "update fichefrais set nbjustificatifs = $nbJustificatifs
		where fichefrais.idvisiteur = '$idVisiteur' and fichefrais.mois = '$mois'";
        PdoGsb::$monPdo->exec($req);
    }

    /**
     * Teste si un visiteur possède une fiche de frais pour le mois passé en argument

     * @param $idVisiteur
     * @param $mois sous la forme aaaamm
     * @return vrai ou faux
     */
    public function estPremierFraisMois($idVisiteur, $mois) {
        $ok = false;
        $req = "select count(*) as nblignesfrais from fichefrais
		where fichefrais.mois = '$mois' and fichefrais.idvisiteur = '$idVisiteur'";
        $res = PdoGsb::$monPdo->query($req);
        $laLigne = $res->fetch();
        if ($laLigne['nblignesfrais'] == 0) {
            $ok = true;
        }
        return $ok;
    }

    /**
     * Retourne le dernier mois en cours d'un visiteur

     * @param $idVisiteur
     * @return le mois sous la forme aaaamm
     */
    public function dernierMoisSaisi($idVisiteur) {
        $req = "select max(mois) as dernierMois from fichefrais where fichefrais.idvisiteur = '$idVisiteur'";
        $res = PdoGsb::$monPdo->query($req);
        $laLigne = $res->fetch();
        $dernierMois = $laLigne['dernierMois'];
        return $dernierMois;
    }

    /**
     * Crée une nouvelle fiche de frais et les lignes de frais au forfait pour un visiteur et un mois donnés

     * récupère le dernier mois en cours de traitement, met à 'CL' son champs idEtat, crée une nouvelle fiche de frais
     * avec un idEtat à 'CR' et crée les lignes de frais forfait de quantités nulles
     * @param $idVisiteur
     * @param $mois sous la forme aaaamm
     */
    public function creeNouvellesLignesFrais($idVisiteur, $mois) {
        $dernierMois = $this->dernierMoisSaisi($idVisiteur);
        $laDerniereFiche = $this->getLesInfosFicheFrais($idVisiteur, $dernierMois);
        if ($laDerniereFiche['idEtat'] == 'CR') {
            $this->majEtatFicheFrais($idVisiteur, $dernierMois, 'CL');
        }
        $req = "insert into fichefrais(idvisiteur,mois,nbJustificatifs,montantValide,dateModif,idEtat)
		values('$idVisiteur','$mois',0,0,now(),'CR')";
        PdoGsb::$monPdo->exec($req);
        $lesIdFrais = $this->getLesIdFrais();
        foreach ($lesIdFrais as $uneLigneIdFrais) {
            $unIdFrais = $uneLigneIdFrais['idfrais'];
            $req = "insert into lignefraisforfait(idvisiteur,mois,idFraisForfait,quantite)
			values('$idVisiteur','$mois','$unIdFrais',0)";
            PdoGsb::$monPdo->exec($req);
        }
    }

    /**
     * Crée un nouveau frais hors forfait pour un visiteur un mois donné
     * à partir des informations fournies en paramètre

     * @param $idVisiteur
     * @param $mois sous la forme aaaamm
     * @param $libelle : le libelle du frais
     * @param $date : la date du frais au format français jj//mm/aaaa
     * @param $montant : le montant
     */
    public function creeNouveauFraisHorsForfait($idVisiteur, $mois, $libelle, $date, $montant) {
        $dateFr = dateFrancaisVersAnglais($date);
        $req = "insert into lignefraishorsforfait
		values('','$idVisiteur','$mois','$libelle','$dateFr','$montant')";
        PdoGsb::$monPdo->exec($req);
    }

    /**
     * Supprime le frais hors forfait dont l'id est passé en argument

     * @param $idFrais
     */
    public function supprimerFraisHorsForfait($idFrais) {
        $req = "delete from lignefraishorsforfait where lignefraishorsforfait.id =$idFrais ";
        PdoGsb::$monPdo->exec($req);
    }

    /**
     * Retourne les mois pour lesquel un visiteur a une fiche de frais

     * @param $idVisiteur
     * @return un tableau associatif de clé un mois -aaaamm- et de valeurs l'année et le mois correspondant
     */
    public function getLesMoisDisponibles($idVisiteur) {
        $req = "select fichefrais.mois as mois from  fichefrais where fichefrais.idvisiteur ='$idVisiteur'
		order by fichefrais.mois desc ";
        $res = PdoGsb::$monPdo->query($req);
        $lesMois = array();
        $laLigne = $res->fetch();
        while ($laLigne != null) {
            $mois = $laLigne['mois'];
            $numAnnee = substr($mois, 0, 4);
            $numMois = substr($mois, 4, 2);
            $lesMois["$mois"] = array(
                "mois" => "$mois",
                "numAnnee" => "$numAnnee",
                "numMois" => "$numMois"
            );
            $laLigne = $res->fetch();
        }
        return $lesMois;
    }

    /**
     * Retourne les informations d'une fiche de frais d'un visiteur pour un mois donné

     * @param $idVisiteur
     * @param $mois sous la forme aaaamm
     * @return un tableau avec des champs de jointure entre une fiche de frais et la ligne d'état
     */
    public function getLesInfosFicheFrais($idVisiteur, $mois) {
        $req = "select ficheFrais.idEtat as idEtat, ficheFrais.dateModif as dateModif, ficheFrais.nbJustificatifs as nbJustificatifs,
			ficheFrais.montantValide as montantValide, etat.libelle as libEtat from  fichefrais inner join Etat on ficheFrais.idEtat = Etat.id
			where fichefrais.idvisiteur ='$idVisiteur' and fichefrais.mois = '$mois'";
        $res = PdoGsb::$monPdo->query($req);
        $laLigne = $res->fetch();
        return $laLigne;
    }

    /**
     * Modifie l'état et la date de modification d'une fiche de frais

     * Modifie le champ idEtat et met la date de modif à aujourd'hui
     * @param $idVisiteur
     * @param $mois sous la forme aaaamm
     */
    public function majEtatFicheFrais($idVisiteur, $mois, $etat) {
        $req = "update ficheFrais set idEtat = '$etat', dateModif = now()
		where fichefrais.idvisiteur ='$idVisiteur' and fichefrais.mois = '$mois'";
        PdoGsb::$monPdo->exec($req);
    }

    /**
     *
     * Met à jour dans la base de données les quantités des lignes de frais forfaitisées
     * pour la fiche de frais dont l'id du visiteur et le mois de la fiche sont passés en paramètre.
     * Une transaction est utilisée pour garantir que toutes les mises à jour ont bien abouti, ou aucune.
     * 
     * @param string $unIdVisiteur L'id du visiteur.
     * @param string $unMois Le mois de la fiche de frais.
     * @param array $lesFraisForfaitises Un tableau à 2 dimensions contenant pour chaque frais forfaitisé
     * le numéro de ligne et la quantité.
     * @return boolean Le résultat de la mise à jour.
     */
    public function setLesQuantitesFraisForfaitises($unIdVisiteur, $unMois, $lesFraisForfaitises) {
        $bdd = PdoGsb::$monPdo;
        
        $req = $bdd->prepare('exec SP_LIGNE_FF_MAJ :idVisiteur, :mois, :unIdFrais, :quantite');
        $req->bindValue(':idVisiteur', $unIdVisiteur);
        $req->bindValue(':mois', $unMois);
        try {
            $bdd->beginTransaction();
            foreach ($lesFraisForfaitises as $unFF) {
                $unIdFrais = $unFF[0];
                $quantite = $unFF[1];
                $req->bindValue(':unIdFrais', $unIdFrais);
                $req->bindParam(':quantite', $quantite);
                $req->execute();
            }
            $bdd->commit();
        } catch (Exception $e) {
            PdoGsb::$monPdo->rollBack();
            echo 'Erreur !: ' . $e->getMessage() . '<br />';
            return false;
            //exit();            
        }
        return true;
    }

    /**
     *
     * Met à jour les frais hors forfait dans la base de données.
     * La mise à jour consiste à :
     * - reporter ou supprimer certaine(s) ligne(s) des frais hors forfait ;
     * - mettre à jour le nombre de justificatifs pris en compte.
     * Une transaction est utilisée pour assurer la cohérence des données.
     * 
     * @param string $unIdVisiteur L'id du visiteur.
     * @param string $unMois Le mois de la fiche de frais.
     * @param array $lesFraisHorsForfait Un tableau à 2 dimensions contenant
     * pour chaque frais hors forfaitisé le numéro de ligne et l'action (R ou S) à effectuer.
     * @param type $nbJustificatifsPEC Le nombre de justificatifs pris en compte.
     * @return bool Le résultat de la mise à jour (TRUE : ok ; FALSE : pas ok).
     */
    public function setLesFraisHorsForfait($unIdVisiteur, $unMois, $lesFraisHorsForfait, $nbJustificatifsPEC) {
        $bdd = PdoGsb::$monPdo;        
        $report = $bdd->prepare('exec SP_LIGNE_FHF_REPORTE :idVisiteur, :mois, :fraisNum');
        $report->bindParam(':idVisiteur', $unIdVisiteur);
        $report->bindParam(':mois', $unMois);
        $report->bindParam(':fraisNum', $fraisNum);
        $sup = $bdd->prepare('exec SP_LIGNE_FHF_SUPPRIME :idVisiteur, :mois, :fraisNum');
        $sup->bindParam(':idVisiteur', $unIdVisiteur);
        $sup->bindParam(':mois', $unMois);
        $sup->bindParam(':fraisNum', $fraisNum);
        $setNbJustif = $bdd->prepare('exec SP_FICHE_NB_JPEC_MAJ :idVisiteur, :mois, :nbJustificatifs');
        $setNbJustif->bindParam(':idVisiteur', $unIdVisiteur);
        $setNbJustif->bindParam(':mois', $unMois);
        $setNbJustif->bindValue(':nbJustificatifs', $nbJustificatifsPEC);
        try {
            $bdd->beginTransaction();
            foreach ($lesFraisHorsForfait as $unFHF) {
                $fraisNum = $unFHF[0];
                $action = $unFHF[1];
                switch ($action) {
                    case 'S':                        
                        $sup->execute();
                        break;
                    case 'R':
                        $report->execute();
                        break;
                    default:
                        break;
                }
                $setNbJustif->execute();
            }
            $bdd->commit();
            echo 'Mise à jour éffectuée';
        } catch (Exception $e) {
            PdoGsb::$monPdo->rollBack();
            echo 'Erreur !: ' . $e->getMessage() . '<br />';
            return false;
            //exit();            
        }
        return true;
    }
    
    public function majFicheFrais($unIdVisiteur, $unMois, $unEtat, $unMontantValide) {
        $bdd = PdoGsb::$monPdo; 
        $maj = $bdd->prepare('exec SP_FICHE_VALIDE :idVisiteur, :mois, :etatFiche, :montantValide');
        $maj->bindParam(':idVisiteur', $unIdVisiteur);
        $maj->bindParam(':mois', $unMois);
        $maj->bindParam(':etatFiche', $unEtat);
        $maj->bindParam(':montantValide', $unMontantValide);
        try {
            $bdd->beginTransaction();
            $maj->execute();
            $bdd->commit();
            
        } catch (Exception $e) {
            PdoGsb::$monPdo->rollBack();
            echo 'Erreur !: ' . $e->getMessage() . '<br />';
            return false;
            //exit();            
        }
        return true;
    }
    
    public function nbFicheACloturer($unMois) {
        $bdd = PdoGsb::$monPdo; 
        $req = "select dbo.F_FICHE_A_CLOTURER_NB ('$unMois')"; 
        $res = $bdd->query($req);
       return $res->fetchColumn(0);
        
    }
    
    public function cloturerFicheCR($unMois) {
        $bdd = PdoGsb::$monPdo; 
        $cloturer = $bdd->prepare('exec SP_CLOTURER_FICHE_CR :mois');
        $cloturer->bindParam(':mois', $unMois);
        $cloturer->execute();
        $nbCloturer = $cloturer->rowCount();
        return $nbCloturer;
    }

    public function getInfosCategorieFrais($idCategorie) {
        $req = "EXEC SP_CATEGORIE_FF_GET_INFOS '$idCategorie'";
        $res = PdoGsb::$monPdo->query($req);
        $lignes = $res->fetch();
        return $lignes;
    }
    
    
}
?>