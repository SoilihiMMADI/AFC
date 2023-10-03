<h2>Frais au forfait</h2>
                <form name="frmFraisForfait" id="frmFraisForfait" method="post" action="index.php?uc=validerFicheFrais&action=enregModifFF"
                      onsubmit="return confirm('Voulez-vous réellement enregistrer les modifications apportées aux frais forfaitisés ?');">
                    <table>
                        <tr>
                            <th>Forfait<br />étape</th>
                            <th>Frais<br />kilométriques</th>
                            <th>Nuitée<br />hôtel</th>
                            <th>Repas<br />restaurant</th>
                            <th></th>
                        </tr>
                        <tr>
                            <td>
                               <?php echo formInputTextSansLabel('txtEtape', 'txtEtape', $lesQuantites[0], 3, "", 30, false); ?>
                            </td>
                            <td>
                                <?php echo formInputTextSansLabel('txtKm', 'txtKm', $lesQuantites[1], 3, "", 35, false); ?>
                            </td>
                            <td>
                                <?php echo formInputTextSansLabel('txtNuitee', 'txtNuitee', $lesQuantites[2], 3, "", 40, false); ?>
                            </td>
                            <td>
                                <?php echo formInputTextSansLabel('txtRepas', 'txtRepas', $lesQuantites[3], 3, "", 45, false); ?>
                            </td>
                            <td>
                                <?php echo formBoutonSubmit('btnEnregistrerFF', 'btnEnregistrerFF', 'Enregistrer', 50); ?>&nbsp;
                                <?php echo formBoutonReset('btnReinitialiserFF', 'btnReinitialiserFF', 'Réinitialiser', 60); ?>
                            </td>
                        </tr>
                    </table>
                </form>
                <br />
                <br />
<?php
