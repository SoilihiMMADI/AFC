<div id="contenu">
    <h2>Validation d'une fiche de frais visiteur</h2>
    <br />
    <form name="frmChoixVisiteurMoisFiche" id="frmChoixVisiteurMoisFiche" method="post" action="index.php">
        <?php
//si choix du visiteur dans la liste déroulante
        if (isset($_POST['lstVisiteur']) && isset($_POST['txtMoisFiche'])) {
            echo formSelectDepuisRecordset('Visiteur : ', 'lstVisiteur', 'lstVisiteur', $visiteurs, 10, $_POST['lstVisiteur']);
        }
//sinon si premier affichage ou aucun choix dans la liste de visisteur 
        else {
            echo formSelectDepuisRecordset('Visiteur : ', 'lstVisiteur', 'lstVisiteur', $visiteurs, 10, NULL);
        }
        echo formInputText('Mois ', 'txtMoisFiche', 'txtMoisFiche', $date, 50, 40, 10, false);
        echo formBoutonSubmit('btnOk', 'btnOk', 'Ok', 20);
        echo formInputHidden('uc', 'uc', 'validerFicheFrais');
        echo formInputHidden('action', 'action', 'afficherFicheFraisSelectionnee');
        ?>
    </form>
    <br />
    <br />

        <?php
            