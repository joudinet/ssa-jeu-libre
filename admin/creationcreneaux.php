
<?php require "header.php"; 
ouvre_bdd();
if (isset($_POST['date'])) {
    creationCreneau();
}
$les_creneaux=lire_les_creneaux() ;
ferme_bdd();
?>    
    
    
    <DIV class="creationCreneau">
        <FORM id="Formulaire" method="post" action="creationcreneaux.php">
                <legend>Informations sur le créneau à créer :</legend>
                <DIV class="item_formulaire_index">
                    <LABEL> Date : </LABEL>
                    <INPUT type="date" id="date" name="date">
                </DIV>
                <DIV class="item_formulaire_index">
                    <LABEL> Horaire : </LABEL>
                    <INPUT id="heure" name="heure">
                </DIV>
                <DIV class="item_formulaire_index">
                    <LABEL> Intitulé : </LABEL>
                    <INPUT id="intitule" name="intitule" value="jeu-libre">
                </DIV>
                <DIV class="item_formulaire_index">
                    <LABEL> Avec réservation ? : </LABEL>
                    <SELECT id="reservation" name="reservation">
                        <OPTION value="non" selected > non</OPTION>
                        <OPTION value="oui"> oui</OPTION>
                    </SELECT>
                </DIV>
            
        </FORM> <BR>
        <BUTTON onclick="valideFormulaire()"> Créer un créneau avec ces informations.</BUTTON>
    </DIV>
    
    <DIV class="rappelcreneau">
        <BR><LABEL>Rappel des créneaux existants :</LABEL><BR><BR>
        <DIV class="grillecreneau">
<?php
if (count($les_creneaux)==0) {
    echo "Pas de créneaux prévus pour l'instant";
} else {
    for ($i=0;$i<count($les_creneaux);$i++) { 
        echo '<DIV>'.jolie_date($les_creneaux[$i]['date']).'</DIV>';
        echo '<DIV>'.$les_creneaux[$i]['heure'].'</DIV>';
        echo '<DIV >  '.$les_creneaux[$i]['intitule'].'</DIV>';
        echo '<DIV >  réservation : '.$les_creneaux[$i]['reservation'].'</DIV>';
     }
}
?>
        </DIV>
    </DIV>

</BODY>
<SCRIPT src="gestion_java.js?=<?php echo time(); //pour forcer le js a ne pas être en cache et jouer des tours en dev ?>">  </SCRIPT>
</HTML>