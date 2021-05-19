<!DOCTYPE html>

<HTML>
    <META http-equiv=content-type content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <HEAD>
        <TITLE>Gestion des créneaux</TITLE>
        <LINK rel="stylesheet" href="index_style.css?=<?php echo time(); //pour forcer le css a ne pas être en cache et jouer des tours en dev ?>">
    </HEAD>
    
<?php 
require "index_fonctions.php";
ouvre_bdd();
if (isset($_POST['nom'])) {
    if (valide_formulaire()) {
     echo "<H1 >Ton inscription est validée.<BR><a href='index.php'>Lien de retour à l'accueil</a></H1>";  
     die();
    }
}
$les_creneaux=lire_les_creneaux();
ferme_bdd();
?>

<BODY>
    <H1 class="titre_index">
        Inscription aux créneaux jeu-libre Sand-System
    </H1>
        
    <DIV class="general_index">
        <DIV class="formulaire_index">
            <FORM id="le_formulaire_index" method="post" action="index.php">
                <legend>Informations personnelles :</legend>
                <DIV class="item_formulaire_index">
                    <LABEL> Nom : </LABEL>
                    <INPUT id="nom" name="nom" class="grand">
                </DIV>
                <DIV class="item_formulaire_index">
                    <LABEL> Prénom : </LABEL>
                    <INPUT id="prenom" name="prenom" class="grand">
                </DIV>
                <DIV class="item_formulaire_index">
                    <LABEL> Mail : </LABEL>
                    <INPUT type="email" id="mail" name="mail" class="grand">
                </DIV>
                <DIV class="item_formulaire_index">
                    <LABEL> Téléphone : </LABEL>
                    <INPUT id="telephone" name="telephone" type="tel" class="grand">
                </DIV>
                <DIV class="item_formulaire_index">
                    <LABEL class="radio"> Niveau de jeu estimé : </LABEL>
                    <INPUT type="radio" name="niveau" value="debutant" id="jeu1"> <LABEL for="jeu1" id="jeul1"> débutant</LABEL>
                    <INPUT type="radio" name="niveau" value="intermediaire" id="jeu2"> <LABEL for="jeu2" id="jeul2"> intermédiaire</LABEL>
                    <INPUT type="radio" name="niveau" value="confirme" id="jeu3"> <LABEL for="jeu3" id="jeul3"> confirmé</LABEL>
                    <INPUT type="radio" name="niveau" value="expert" id="jeu4"> <LABEL for="jeu4" id="jeul4"> expert</LABEL>
                </DIV>
                <DIV class="item_formulaire_index">
                    <LABEL class="radio"> Es-tu adhérent(e) SandSystem ? </LABEL>
                    <INPUT type="radio" id="ad1" name="adherent" value="oui"> <LABEL for="ad1" id="adl1"> oui</LABEL>
                    <INPUT type="radio" id="ad2" name="adherent" value="non"> <LABEL for="ad2" id="adl2"> non</LABEL>
                </DIV>
                <DIV class="item_formulaire_index" id="adhesion" style="display : none;">
                    <LABEL> Pour les non-adhérents, la session est à 10 euros</LABEL>
                    <a href="index.php"> lien pour adhérer</a>
                </DIV>
                <DIV class="item_formulaire_index">
                    <textarea name="commentaire" placeholder="Indique ici si tu souhaites jouer avec quelqu'un en particulier"></textarea>
                </DIV>
            </FORM>    
        </DIV>
        
        <DIV class="creneaux_index" id="creneauxdispos">
            <DIV> Liste des créneaux disponibles :</DIV><DIV> (créneaux sélectionnés en orange)</DIV>
<?php 
$nb=0;
foreach ($les_creneaux as $un_creneau) { 
        $id=$un_creneau['id'];
        echo '<DIV>'.secu_ecran(jolie_date($un_creneau['date'])).' '.secu_ecran($un_creneau['heure']).' : </DIV>'; 
        echo '<DIV>';
        if ($un_creneau['feminin']>0) { $nb=1;echo '<INPUT class="checkboxfeminin" form="le_formulaire_index" type="checkbox" id="c'.$id.'feminin" name="c'.$id.'feminin" onclick="click_creneau('.$id.',0)" value=0>';
        echo '<LABEL for="c'.$id.'feminin" id="lc'.$id.'feminin"> féminin</LABEL>';}
        if ($un_creneau['masculin']>0) {$nb=1;echo '<INPUT class="checkboxmasculin" form="le_formulaire_index" type="checkbox" id="c'.$id.'masculin" name="c'.$id.'masculin" onclick="click_creneau('.$id.',1)" value=0>';
        echo '<LABEL for="c'.$id.'masculin" id="lc'.$id.'masculin"> masculin</LABEL>';}
        if ($un_creneau['mixte']>0) {$nb=1;echo '<INPUT class="checkboxmixte" form="le_formulaire_index" type="checkbox" id="c'.$id.'mixte" name="c'.$id.'mixte" onclick="click_creneau('.$id.',2)" value=0>';
        echo '<LABEL for="c'.$id.'mixte" id="lc'.$id.'mixte"> mixte</LABEL>';}
        echo '</DIV>';
        
}
if ($nb==0) {
    echo "Pas de créneaux prévus pour l'instant";
}
    
?>

        </DIV>
        
    </DIV>

    <DIV class="validation_index">
        <DIV><INPUT form="le_formulaire_index" type="checkbox" id="consignesecurite" name="consignesecurite" class="texteconsigne"> 
        <LABEL for="consignesecurite" class="texteconsigne"> Je confirme avoir pris connaissance du protocole de jeu pour une reprise responsable. J'en accepte les termes et les conditions. Je m'engage à le respecter.</LABEL></DIV>
        <DIV><INPUT form="le_formulaire_index" type="checkbox" id="consignergpd" name="consignergpd" class="texteconsigne">
            <LABEL for="consignergpd" class="textergpd"> J'autorise Sandsystem à sauvegarder mes informations personnelles pendant une durée de 6 mois maximum. (à tout moment, il suffit d'envoyer un mail à lemail@lemail.com pour les supprimer)</LABEL></DIV><BR>
        <DIV class="centree"> <A href=" https://drive.google.com/file/d/12l7dqbU4wu52WcvrWphWyRbwFPnm-2qz/view?usp=sharing">lien vers le protocole de reprise</A></DIV><BR><BR>
        <DIV class="centree"><BUTTON type="button"  onclick="validation_formulaire()" > Soumettre la demande de créneaux</BUTTON></DIV>
    </DIV>
    <BR><BR><BR><BR>
    
</BODY>
<SCRIPT src="index_java.js?=<?php echo time(); //pour forcer le js a ne pas être en cache et jouer des tours en dev ?>">  </SCRIPT>
</HTML>