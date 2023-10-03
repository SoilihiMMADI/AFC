<?php

if (!isset($_REQUEST['action'])) {
    $_REQUEST['action'] = 'choixInitialVisiteur';
}
$action = $_REQUEST['action'];
$nom = $_SESSION['nom'];
$prenom = $_SESSION['prenom'];
$date = dateFiche();

switch ($action) {
    case 'choixInitialVisiteur': {

            $visiteurs = $pdo->getVisiteurs();
            include("vues/v_sommaire.php");
            include("vues/v_valideFraisChoixVisiteur.php");
            break;
        }
    case 'afficherFicheFraisSelectionnee': {
            $_SESSION['idVisiteur'] = $_POST['lstVisiteur'];
            $_SESSION['moisFiche'] = $_POST['txtMoisFiche'];
            $visiteurs = $pdo->getVisiteurs();
            $ficheFrais = new FicheFrais($_SESSION['idVisiteur'], $_SESSION['moisFiche']);
            $ficheFrais->initAvecInfosBDD();
            $libelleEtat = $ficheFrais->getLibelleEtat();
            $idEtat = $ficheFrais->getIdEtat();
            $lesQuantites = $ficheFrais->getLesQuantitesDeFraisForfaitises();
            $infosFHF = $ficheFrais->getLesInfosFraisHorsForfait();
            $leNbJustificatifs = $ficheFrais->getNbJustificatitfs();
            include("vues/v_sommaire.php");

            if ($idEtat != 'CL') {
                include("vues/v_valideFraisChoixVisiteur.php");
                ajouterErreur('Cette fiche n\'est pas clôturée : impossible de la valider.');
                switch ($idEtat) {
                    case 'VA': {
                            ajouterErreur('Elle a déjà été valider.');
                            break;
                        }
                    case 'MP': {
                            ajouterErreur('Elle est dans l\'état \'mise en paiement\' .');
                            break;
                        }
                    case 'RB': {
                            ajouterErreur('Elle a été remboursée.');
                            break;
                        }
                    case 'CR':{
                            ajouterErreur('Elle est en cours de saisie');
                            break;
                        }
                    case '00':{
                            ajouterErreur('Il n\'y a pas de fiche pour ce mois.');
                            break;
                        }
                    default :{
                            break;
                        }
                }
                include ("vues/v_erreurs.php");
            }
            else {
                include("vues/v_valideFraisChoixVisiteur.php");
                include("vues/v_valideFraisCorpsFiche.php");
            }
            break;
        }
    case 'enregModifFF': {
            $visiteurs = $pdo->getVisiteurs();
            $ficheFrais = new FicheFrais($_SESSION['idVisiteur'], $_SESSION['moisFiche']);
            $ficheFrais->initAvecInfosBDDSansFF();
            $ficheFrais->ajouterUnFraisForfaitise('ETP', $_POST['txtEtape']);
            $ficheFrais->ajouterUnFraisForfaitise('KM', $_POST['txtKm']);
            $ficheFrais->ajouterUnFraisForfaitise('NUI', $_POST['txtNuitee']);
            $ficheFrais->ajouterUnFraisForfaitise('REP', $_POST['txtRepas']);
            $ficheFrais->controlerQtesFraisForfaitises();
            if ($ficheFrais->mettreAJourLesFraisForfaitises()) {
                $message = 'la mise à jour à bien été effectuée';
                include ('Vues/v_message.php');
            }
            break;
        }
    case 'enregModifFHF': {
            $visiteurs = $pdo->getVisiteurs();
            $ficheFrais = new FicheFrais($_SESSION['idVisiteur'], $_SESSION['moisFiche']);
            $ficheFrais->initAvecInfosBDDSansFHF();
            foreach ($_REQUEST['tabInfosFHF'] as $unFHF) {
                $ficheFrais->ajouterUnFraisHorsForfait($unFHF['hidNumFHF'], $unFHF['txtDateFHF'], $unFHF['txtLibelle'], $unFHF['txtMontant'], $unFHF['rbFHFAction']);
            }
            $ficheFrais->setNbJustificatifs($_POST['txtFHFNbJustificatifsPEC']);
            if ($ficheFrais->controlerNbJustificatifs()) {
                $ficheFrais->mettreAJourLesFraisHorsForfait();
            } else {
                ajouterErreur('le nombre de justificatifs n\'est pas correct');
                $uc = $_REQUEST['validerFicheFrais'];
                $action = $_REQUEST['afficherFicheFraisSelectionnee'];
            }
            $libelleEtat = $ficheFrais->getLibelleEtat();
            $lesQuantites = $ficheFrais->getLesQuantitesDeFraisForfaitises();
            $infosFHF = $ficheFrais->getLesInfosFraisHorsForfait();
            $leNbJustificatifs = $ficheFrais->getNbJustificatitfs();
            include("vues/v_sommaire.php");
            include("vues/v_valideFraisChoixVisiteur.php");
            include("vues/v_valideFraisCorpsFiche.php");
            break;
        }
    case 'validerFicheFrais': {
            $visiteurs = $pdo->getVisiteurs();
            $ficheFrais = new FicheFrais($_SESSION['idVisiteur'], $_SESSION['moisFiche']);
            $ficheFrais->initAvecInfosBDD();
            $ficheFrais->valider();
            $ficheFrais->initAvecInfosBDD();
            $libelleEtat = $ficheFrais->getLibelleEtat();
            $lesQuantites = $ficheFrais->getLesQuantitesDeFraisForfaitises();
            $infosFHF = $ficheFrais->getLesInfosFraisHorsForfait();
            $leNbJustificatifs = $ficheFrais->getNbJustificatitfs();
            include("vues/v_sommaire.php");
            $message = 'la fiche a été validée';
            include ('Vues/v_message.php');
            include("vues/v_valideFraisChoixVisiteur.php");
            include("vues/v_valideFraisCorpsFiche.php");
            break;
        }
    default : {
            include("vues/v_sommaire.php");
            include("vues/v_valideFraisChoixVisiteur.php");
            break;
        }
}