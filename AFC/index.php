<?php

session_start();
require_once("include/fct.inc.php");
require_once ("include/class.pdogsb.inc.php");
require_once("include/Bibliotheque01.inc.php");
require_once("include/class.FicheFrais.inc.php");
require_once("include/class.CategorieFraisForfaitise.inc.php");

include("vues/v_entete.php");

$pdo = PdoGsb::getPdoGsb();
$estConnecte = estConnecte();

if (!isset($_REQUEST['uc']) || !$estConnecte) {
    $_REQUEST['uc'] = 'connexion';
}

$uc = $_REQUEST['uc'];

switch ($uc) {
    case 'connexion': {
            include("controleurs/c_connexion.php");
            break;
        }
    case 'validerFicheFrais': {
            include ('controleurs/c_valideFrais.php');
            break;
        }
    case 'cloturerSaisieFichesFrais': {
            include ('controleurs/c_clotureFicheSaisie.php');
            break;
        }
//	case 'gererFrais' :{
//		include("controleurs/c_gererFrais.php");break;
//	}
//	case 'etatFrais' :{
//		include("controleurs/c_etatFrais.php");break;
//	}
}

include("vues/v_pied.php");
?>

