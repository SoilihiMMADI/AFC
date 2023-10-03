<?php

require_once 'Include/class.Frais.inc.php';
require_once 'Include/class.FicheFrais.inc.php';

if (!isset($_REQUEST['action'])) {
    $_REQUEST['action'] = 'demanderConfirmationClotureFiches';
}
$action = $_REQUEST['action'];
$nom = $_SESSION['nom'];
$prenom = $_SESSION['prenom'];
$mois = dateFiche();
switch ($action) {
    case 'demanderConfirmationClotureFiches': {
            include("Vues/v_sommaire.php");   
            $nbFicheACloturer = $pdo->nbFicheACloturer($mois);
            if ($nbFicheACloturer == 0) {
                ajouterErreur('Aucune fiche a cloturer pour le mois de ' . $mois);
                include 'Vues/v_erreurs.php';
            } else {
                $message = 'Voulez vous cloturer la/les fiche(s) pour le mois de ' . $mois;
                include ('Vues/v_messageOuiNon.php');
            }
            break;
        }

    case 'traiterReponseClotureFiches': {
            include("Vues/v_sommaire.php");    
            $nbFicheCloturer = $pdo->cloturerFicheCR($mois);
            $message =  " $nbFicheCloturer  fiche(s) a(ont) bien été cloturée(s) ";
            include ('Vues/v_message.php');
            break;
        }
        
    default : {
            $nbFicheACloturer = $pdo->nbFicheACloturer($mois);
            if ($nbFicheACloturer == 0) {
                ajouterErreur('Aucune fiche a cloturer pour le mois de ' . $mois);
                include ('Vues/v_erreurs.php');
            } else {
                $message = 'Voulez vous cloturer la/les fiche(s) pour le mois de ' . $mois;
                include ('Vues/v_messageOuiNon.php');
            }
            break;
        }
}