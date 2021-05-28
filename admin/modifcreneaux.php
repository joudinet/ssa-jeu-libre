<?php require "header.php"; 
ouvre_bdd();
$les_creneaux=lire_les_creneaux();
if (isset($_POST['inputid'])) {
    if (isset($_POST['but'])) {
        SupprimeCreneau();
    } else {
        ModifieCreneau();
    }
}
ferme_bdd();
?>
<BR><BR>Liste des créneaux disponibles : <BR><BR><BR>
<?php
if (count($les_creneaux)==0) {
    echo "Pas de créneaux prévus pour l'instant";
} else {
    foreach ($les_creneaux as $un_creneau) {
        $id=$un_creneau['id']; 
        echo '<FORM class="modifcreneau" method="post" action="modifcreneaux.php">';
        echo '<INPUT hidden name="inputid" value="'.$id.'">';
        echo '<DIV></DIV>';
        echo '<DIV style="background-color:'.$un_creneau['C1'].';"> T1 : '.$un_creneau['T1'].'</DIV>';
        echo '<DIV style="background-color:'.$un_creneau['C2'].';"> T2 : '.$un_creneau['T2'].'</DIV>';
        echo '<DIV style="background-color:'.$un_creneau['C3'].';"> T3 : '.$un_creneau['T3'].'</DIV>';
        echo '<DIV style="background-color:'.$un_creneau['C4'].';"> T4 : '.$un_creneau['T4'].'</DIV>';
        echo '<DIV>'.jolie_date($un_creneau['date']).', '.$un_creneau['heure'].'</DIV>';?>
                <DIV class="item_formulaire_index">
                    <LABEL> T1 : </LABEL>
                    <SELECT id="T1" name="T1">
                        <OPTION value="reserve" <?php if ($un_creneau['T1']=="reserve") { echo "selected"; }?>> réservé</OPTION>
                        <OPTION value="feminin" <?php if ($un_creneau['T1']=="feminin") { echo "selected"; }?>> féminin</OPTION>
                        <OPTION value="mixte" <?php if ($un_creneau['T1']=="mixte") { echo "selected"; }?>> mixte</OPTION>
                        <OPTION value="masculin" <?php if ($un_creneau['T1']=="masculin") { echo "selected"; }?>> masculin</OPTION>
                    </SELECT>
<!--                    <SELECT id="C1" name="C1">
                        <OPTION value="#CBCBCB" selected> fond gris</OPTION>
                        <OPTION value="#FF0099"> fond rose</OPTION>
                        <OPTION value="#00CCFF"> fond bleu</OPTION>
                        <OPTION value="#33FF00"> fond vert</OPTION>
                    </SELECT>
-->                </DIV>
                <DIV class="item_formulaire_index">
                    <LABEL> T2 : </LABEL>
                    <SELECT id="T2" name="T2">
                        <OPTION value="reserve" <?php if ($un_creneau['T2']=="reserve") { echo "selected"; }?>> réservé</OPTION>
                        <OPTION value="feminin" <?php if ($un_creneau['T2']=="feminin") { echo "selected"; }?>> féminin</OPTION>
                        <OPTION value="mixte" <?php if ($un_creneau['T2']=="mixte") { echo "selected"; }?>> mixte</OPTION>
                        <OPTION value="masculin" <?php if ($un_creneau['T2']=="masculin") { echo "selected"; }?>> masculin</OPTION>
                    </SELECT>
<!--                    <SELECT id="C2" name="C2">
                        <OPTION value="#CBCBCB"> fond gris</OPTION>
                        <OPTION value="#FF0099"> fond rose</OPTION>
                        <OPTION value="#00CCFF" selected> fond bleu</OPTION>
                        <OPTION value="#33FF00"> fond vert</OPTION>
                    </SELECT>
-->                </DIV>
                <DIV class="item_formulaire_index">
                    <LABEL> T3 : </LABEL>
                    <SELECT id="T3" name="T3">
                        <OPTION value="reserve" <?php if ($un_creneau['T3']=="reserve") { echo "selected"; }?>> réservé</OPTION>
                        <OPTION value="feminin" <?php if ($un_creneau['T3']=="feminin") { echo "selected"; }?>> féminin</OPTION>
                        <OPTION value="mixte" <?php if ($un_creneau['T3']=="mixte") { echo "selected"; }?>> mixte</OPTION>
                        <OPTION value="masculin" <?php if ($un_creneau['T3']=="masculin") { echo "selected"; }?>> masculin</OPTION>
                    </SELECT>
<!--                    <SELECT id="C3" name="C3">
                        <OPTION value="#CBCBCB"> fond gris</OPTION>
                        <OPTION value="#FF0099" selected> fond rose</OPTION>
                        <OPTION value="#00CCFF"> fond bleu</OPTION>
                        <OPTION value="#33FF00"> fond vert</OPTION>
                    </SELECT>
-->                </DIV>
                <DIV class="item_formulaire_index">
                    <LABEL> T4 : </LABEL>
                    <SELECT id="T4" name="T4">
                        <OPTION value="reserve" <?php if ($un_creneau['T4']=="reserve") { echo "selected"; }?>> réservé</OPTION>
                        <OPTION value="feminin" <?php if ($un_creneau['T4']=="feminin") { echo "selected"; }?>> féminin</OPTION>
                        <OPTION value="mixte" <?php if ($un_creneau['T4']=="mixte") { echo "selected"; }?>> mixte</OPTION>
                        <OPTION value="masculin" <?php if ($un_creneau['T4']=="masculin") { echo "selected"; }?>> masculin</OPTION>
                    </SELECT>
<!--                    <SELECT id="C4" name="C4">
                        <OPTION value="#CBCBCB"> fond gris</OPTION>
                        <OPTION value="#FF0099"> fond rose</OPTION>
                        <OPTION value="#00CCFF"> fond bleu</OPTION>
                        <OPTION value="#33FF00" selected> fond vert</OPTION>
                    </SELECT>
-->                </DIV>
<?php
        echo '<DIV><BUTTON type="button" onclick="supprimeCreneau('.$id.')">Supprimer ce créneau</BUTTON></DIV><DIV></DIV>';
        echo '<INPUT type="submit" value="Modifier et reinitialiser ce créneau"></FORM>';
        echo '<BR><BR><BR>';
    }
}
?>     
<FORM id="Formulaire" method="post" action="modifcreneaux.php"><INPUT hidden id="but" name="but" value="rien"><INPUT hidden id="inputid" name="inputid" value="rien"></FORM>

</BODY>
<SCRIPT src="gestion_java.js?=<?php echo time(); //pour forcer le js a ne pas être en cache et jouer des tours en dev ?>">  </SCRIPT>
</HTML>