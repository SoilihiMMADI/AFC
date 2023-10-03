<?php

function formSelectDepuisRecordset($LD_label, $LD_nom, $LD_id, $recordset, $TabIndex, $ValeurOpt) {
    $code = '<label for="' . $LD_id . '">' . $LD_label . '</label>' . "\n"
            . '    <select name="' . $LD_nom . '" id="' . $LD_id . '" class="titre" tabindex="' . $TabIndex . '">' . "\n";

    //$recordset->setFetchMode(PDO::FETCH_NUM);

    /* $ligne = $recordset->fetch();
      while ($ligne != false) {
      $code .= '        <option ';
      if ($ligne[0] == $ValeurOpt) {
      $code .= 'selected="selected" ';
      }
      $code .= 'value="' . $ligne[0] . '">' . $ligne[1] . '</option>' . "\n";
      $ligne = $recordset->fetch();
      } */
    foreach ($recordset as $unRecordset) {
        $code .= '         <option ';
        if ($unRecordset[0] == $ValeurOpt) {
            $code .= 'selected="selected" ';
        }
        $code .= 'value="' . $unRecordset[0] . '">' . $unRecordset[1] . '</option>' . "\n";
    }
    $code .= '</select>';
    return $code;
}

function formBoutonSubmit($Nom, $Id, $Val, $TabIndex) {
    $code = '<input type="submit" name="' . $Nom . '" id="' . $Id . '" value="' . $Val . '" tabindex="' . $TabIndex . '">';
    return $code;
}

function formBoutonRadio($Nom, $Val, $TabIndex, $checked) {
    $code = '<input type="radio" name="' . $Nom . ' value="'.$Val.'" tabindex="' . $TabIndex . '"';
    if($checked == TRUE){
        $code .= 'checked="checked"';
    }
    $code.= '/>';
    return $code;
}

function formBoutonReset($Nom, $Id, $Val, $TabIndex) {
    $code = '<input type="reset" name="' . $Nom . '" id="' . $Id . '" value="' . $Val . '" tabindex="' . $TabIndex . '">';
    return $code;
}

function formInputHidden($Nom, $Id, $Val) {
    $code = '<input type="hidden" name="' . $Nom . '" id="' . $Id . '" value="' . $Val . '">';
    return $code;
}

function formTextArea($Label, $Nom, $Id, $Val, $Larg, $Haut, $MaxLong, $TabIndex, $LectSeul) {
    $code = '<label for="' . $Id . '"> ' . $Label . ' : </label>'
            . '<textarea name="' . $Nom . '" id="' . $Id . '" cols="' . $Larg . '" rows="' . $Haut . '" '
            . 'maxlegth="' . $MaxLong . '" tabindex="' . $TabIndex . '" readonly="' . $LectSeul . '"> ' . $Val . ' </textarea>';
    return $code;
}

function formInputText($leLabel, $leNom, $id, $valeur, $taille, $longueurMax, $tabIndex, $lectureSeule, $obligation = null) {
    $codeHTML = '<label for="' . $id . '">' . $leLabel . ' : </label> '
            . '<input type="text" name="' . $leNom . '"id="' . $id . '"size="' . $taille . '"'
            . 'maxlength="' . $longueurMax . '"value="' . $valeur . '"tabIndex="' . $tabIndex . '"';

    if ($lectureSeule == true) {
        $codeHTML = $codeHTML . ' readonly="readonly" ';
    }
    if ($obligation == true) {
        $codeHTML = $codeHTML . 'required="required"';
    }
    $codeHTML = $codeHTML . '/>';

    return $codeHTML;
}

function formInputPassword($leLabel, $leNom, $id, $valeur, $taille, $longueurMax, $tabIndex, $obligation = null) {
    $codeHTML = '<label for="' . $id . '">' . $leLabel . ' : </label> '
            . '<input type="password" name="' . $leNom . '"id="' . $id . '"size="' . $taille . '"'
            . 'maxlength="' . $longueurMax . '"value="' . $valeur . '"tabIndex="' . $tabIndex . '"';

    if ($obligation == true) {
        $codeHTML = $codeHTML . 'required="required"';
    }
    $codeHTML = $codeHTML . '/>';

    return $codeHTML;
}

function dateFiche() {
    $laDate = new DateTime();
    $leMois = (int) ($laDate->format('m')) - 1;
    $lAnnee = (int) ($laDate->format('Y'));
    if ($leMois == 0) {
        $lAnnee--;
        $leMois = 12;
    } return (new DateTime((string) $lAnnee . '-' . (string) $leMois))->format('Ym');
}

function dateActuelle() {
    $laDate = new DateTime();
    $leMois = (int) ($laDate->format('m')) ;
    $lAnnee = (int) ($laDate->format('Y'));
    if ($leMois == 0) {
        $lAnnee--;
        $leMois = 12;
    } return (new DateTime((string) $lAnnee . '-' . (string) $leMois))->format('Ym');
}

function formInputTextSansLabel($leNom, $id, $valeur, $taille, $longueurMax, $tabIndex, $lectureSeule, $obligation = null) {
    $codeHTML = '<input type="text" name="' . $leNom . '"id="' . $id . '"size="' . $taille . '"'
            . 'maxlength="' . $longueurMax . '"value="' . $valeur . '"tabIndex="' . $tabIndex . '"';

    if ($lectureSeule == true) {
        $codeHTML = $codeHTML . ' readonly="readonly" ';
    }
    if ($obligation == true) {
        $codeHTML = $codeHTML . 'required="required"';
    }
    $codeHTML = $codeHTML . '/>';

    return $codeHTML;
}

?>