<div id="contenu">
    <div class="info">
        <p>
            Attention : <br />
            <?php echo $message ?>
        </p>
    </div>
    <form name="frmChoix" id="frmChoix" action="index.php" method="post">
        <label for="mois">Mois :</label>
        <?php echo formInputTextSansLabel('mois', 'mois', $mois, '', '', '', TRUE);
        echo formInputHidden('uc', 'uc', 'cloturerSaisieFichesFrais');
        echo formInputHidden('action', 'action', 'traiterReponseClotureFiches');
        echo formBoutonSubmit('btnOUI', 'btnOUI', 'OUI', 20);
        ?>
        <a href="index.php?uc=validerFicheFrais&action=choixInitialVisiteur"><input type="button" name="btnNON" value="NON" /></a>
    </form>
</div>
