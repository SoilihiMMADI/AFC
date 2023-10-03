<h2>Frais hors forfait</h2>
<?php
if (count($infosFHF) == 0) {

    echo '<p> Pas de frais hors forfait </p>';
} else {
    ?>
    <form name="frmFraisHorsForfait" id="frmFraisHorsForfait" method="post" action="index.php?uc=validerFicheFrais&action=enregModifFHF"
          onsubmit="return confirm('Voulez-vous réellement enregistrer les modifications apportées aux frais hors forfait ?');">
        <table>
            <tr>
                <th>Date</th>
                <th>Libellé</th>
                <th>Montant</th>
                <th>Ok</th>
                <th>Reporter</th>
                <th>Supprimer</th>
            </tr>
            <tr>
                <?php
                $i = 0;
                foreach ($infosFHF as $unFHF) {
                    ?>        
                    <td>
                        <?php echo formInputHidden('tabInfosFHF[' . $i . '][hidNumFHF]', "", $unFHF['numFrais']) ?>
                        <?php echo formInputTextSansLabel('tabInfosFHF[' . $i . '][txtDateFHF]', 'tabInfosFHF[' . $i . '][txtDateFHF]', $unFHF['date'], 12, "", "", TRUE); ?>
                    </td>
                    <td>
                        <?php echo formInputTextSansLabel('tabInfosFHF[' . $i . '][txtLibelle]', 'tabInfosFHF[' . $i . '][txtLibelle]', $unFHF['libelle'], 20, "", "", TRUE); ?>
                    </td>
                    <td>
                        <?php echo formInputTextSansLabel('tabInfosFHF[' . $i . '][txtMontant]', 'tabInfosFHF[' . $i . '][txtMontant]', $unFHF['montant'], 10, "", "", TRUE); ?>
                    </td>
                    <td><input type="radio" name="<?php echo 'tabInfosFHF[' . $i . '][rbFHFAction]' ?>" value="O" <?php ($unFHF['action'] == "O" ? "selected" : "") ?> tabindex="70" checked="checked"/></td>
                    <td><input type="radio" name="<?php echo 'tabInfosFHF[' . $i . '][rbFHFAction]' ?>" value="R" <?php ($unFHF['action'] == "R" ? "selected" : "") ?>tabindex="80" /></td>
                    <td><input type="radio" name="<?php echo 'tabInfosFHF[' . $i . '][rbFHFAction]' ?>" value="S" <?php ($unFHF['action'] == "S" ? "selected" : "") ?> tabindex="90" /></td>
                </tr>
                <?php
                $i++;
            } //fin foreach
            ?>
        </table>
        <p>
            Nb de justificatifs pris en compte :&nbsp;
            <?php echo formInputTextSansLabel('txtFHFNbJustificatifsPEC', 'txtFHFNbJustificatifsPEC', $leNbJustificatifs, 4, "", "", false) ?>        
        </p>
        <p>
            <input type="submit" id="btnEnregistrerModifFHF" name="btnEnregistrerModifFHF" value="Enregistrer les modifications des lignes hors forfait" tabindex="140" />&nbsp;
            <input type="reset" id="btnReinitialiserFHF" name="btnReinitialiserFHF" value="Réinitialiser" tabindex="150" />
        </p>
    </form>
    <?php
} //fin else
?>
</div>
<br />
<br />