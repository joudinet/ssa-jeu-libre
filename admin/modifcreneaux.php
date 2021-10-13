<?php require "header.php"; 
ouvre_bdd();
$les_creneaux=lire_les_creneaux();
if (isset($_POST['inputid'])) { // n+1 formulaires : un par créneau + un avec "but" pour la suppression. Tous ont inputid
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
        echo '<DIV class="item_formulaire_index">'.jolie_date($un_creneau['date']).'</DIV>';?>
 
                <DIV class="item_formulaire_index">
                    <LABEL> Horaire : </LABEL>
                    <INPUT id="heure" name="heure" value="<?php echo $un_creneau['heure']?>">
                </DIV>
                <DIV class="item_formulaire_index">
                    <LABEL> Intitulé : </LABEL>
                    <INPUT id="intitule" name="intitule" value="<?php echo $un_creneau['intitule']?>">
                </DIV>
                <DIV class="item_formulaire_index">
                    <LABEL> Avec réservation ? : </LABEL>
                    <SELECT id="reservation" name="reservation">
                        <OPTION value="non" <?php if ( $un_creneau['reservation']!='oui') {echo "selected";}?>> non</OPTION>
                        <OPTION value="oui" <?php if ( $un_creneau['reservation']=='oui') {echo "selected";}?>> oui</OPTION>
                    </SELECT>
                </DIV>
                <div></div>
 <?php
        echo '<DIV><BUTTON type="button" onclick="supprimeCreneau('.$id.')">Supprimer ce créneau</BUTTON></DIV>';
        echo '<INPUT type="submit" value="Enregistrer les modifs de ce créneau"></FORM>';
        echo '<BR><BR><BR>';
    }
}
?>     
<FORM id="Formulaire" method="post" action="modifcreneaux.php"><INPUT hidden id="but" name="but" value="supprime">
      <INPUT hidden id="inputid" name="inputid" value="rien"></FORM>

</BODY>
<SCRIPT src="gestion_java.js?=<?php echo time(); //pour forcer le js a ne pas être en cache et jouer des tours en dev ?>">  </SCRIPT>
</HTML>