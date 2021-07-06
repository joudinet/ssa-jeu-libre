
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
                    <LABEL> Avec réservation ? : </LABEL>
                    <SELECT id="reservation" name="reservation">
                        <OPTION value="non" selected > non</OPTION>
                        <OPTION value="oui"> oui</OPTION>
                    </SELECT>
                </DIV>
                <DIV class="item_formulaire_index">
                    <LABEL> Terrain 1 : </LABEL>
                    <SELECT id="T1" name="T1">
                        <OPTION value="reserve" selected> réservé</OPTION>
                        <OPTION value="feminin"> féminin</OPTION>
                        <OPTION value="mixte"> mixte</OPTION>
                        <OPTION value="masculin"> masculin</OPTION>
                    </SELECT>
<!--                    <SELECT id="C1" name="C1">
                        <OPTION value="#CBCBCB" selected> fond gris</OPTION>
                        <OPTION value="#FF0099"> fond rose</OPTION>
                        <OPTION value="#00CCFF"> fond bleu</OPTION>
                        <OPTION value="#33FF00"> fond vert</OPTION>
                    </SELECT>
-->                </DIV>
                <DIV class="item_formulaire_index">
                    <LABEL> Terrain 2 : </LABEL>
                    <SELECT id="T2" name="T2">
                        <OPTION value="reserve"> réservé</OPTION>
                        <OPTION value="feminin" selected> féminin</OPTION>
                        <OPTION value="mixte"> mixte</OPTION>
                        <OPTION value="masculin"> masculin</OPTION>
                    </SELECT>
<!--                    <SELECT id="C2" name="C2">
                        <OPTION value="#CBCBCB"> fond gris</OPTION>
                        <OPTION value="#FF0099"> fond rose</OPTION>
                        <OPTION value="#00CCFF" selected> fond bleu</OPTION>
                        <OPTION value="#33FF00"> fond vert</OPTION>
                    </SELECT>
-->                </DIV>
                <DIV class="item_formulaire_index">
                    <LABEL> Terrain 3 : </LABEL>
                    <SELECT id="T3" name="T3">
                        <OPTION value="reserve"> réservé</OPTION>
                        <OPTION value="feminin"> féminin</OPTION>
                        <OPTION value="mixte" selected> mixte</OPTION>
                        <OPTION value="masculin"> masculin</OPTION>
                    </SELECT>
<!--                    <SELECT id="C3" name="C3">
                        <OPTION value="#CBCBCB"> fond gris</OPTION>
                        <OPTION value="#FF0099" selected> fond rose</OPTION>
                        <OPTION value="#00CCFF"> fond bleu</OPTION>
                        <OPTION value="#33FF00"> fond vert</OPTION>
                    </SELECT>
-->                </DIV>
                <DIV class="item_formulaire_index">
                    <LABEL> Terrain 4 : </LABEL>
                    <SELECT id="T4" name="T4">
                        <OPTION value="reserve"> réservé</OPTION>
                        <OPTION value="feminin"> féminin</OPTION>
                        <OPTION value="mixte"> mixte</OPTION>
                        <OPTION value="masculin" selected> masculin</OPTION>
                    </SELECT>
<!--                    <SELECT id="C4" name="C4">
                        <OPTION value="#CBCBCB"> fond gris</OPTION>
                        <OPTION value="#FF0099"> fond rose</OPTION>
                        <OPTION value="#00CCFF"> fond bleu</OPTION>
                        <OPTION value="#33FF00" selected> fond vert</OPTION>
                    </SELECT>
-->                </DIV>
            
        </FORM> 
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
        echo '<DIV>'.jolie_date($les_creneaux[$i]['date']).', '.$les_creneaux[$i]['heure'].'</DIV>';
        echo '<DIV style="background-color:'.$les_creneaux[$i]['C1'].';"> T1 : '.$les_creneaux[$i]['T1'].'</DIV>';
        echo '<DIV style="background-color:'.$les_creneaux[$i]['C2'].';"> T2 : '.$les_creneaux[$i]['T2'].'</DIV>';
        echo '<DIV style="background-color:'.$les_creneaux[$i]['C3'].';"> T3 : '.$les_creneaux[$i]['T3'].'</DIV>';
        echo '<DIV style="background-color:'.$les_creneaux[$i]['C4'].';"> T4 : '.$les_creneaux[$i]['T4'].'</DIV>';
    }
}
?>
        </DIV>
    </DIV>

</BODY>
<SCRIPT src="gestion_java.js?=<?php echo time(); //pour forcer le js a ne pas être en cache et jouer des tours en dev ?>">  </SCRIPT>
</HTML>