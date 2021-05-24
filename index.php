<!DOCTYPE html>

<html>
<head>
    <meta http-equiv=content-type content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des créneaux</title>
    <link rel="stylesheet" href="index_style.css?=<?php echo time(); //pour forcer le css a ne pas être en cache et jouer des tours en dev ?>">
</head>

<?php
require "index_fonctions.php";
ouvre_bdd();
if (isset($_POST['nom'])) {
    if (valide_formulaire()) {
     echo "<p>Votre demande d'inscription a bien été prise en compte et un e-mail vous as été envoyé.<br/><a href='index.php'>Lien de retour à l'accueil</a></p>";
     die();
    }
}
$les_creneaux=lire_les_creneaux();
ferme_bdd();
?>
<body>
    <h1 class="titre_index">Inscription aux créneaux de jeu libre à Sand System</h1>

    <div class="general_index">
        <form id="le_formulaire_index" class="formulaire_index" method="post" action="index.php">
            <fieldset>
                <legend>Informations personnelles :</legend>
                <label>Nom :
                    <input id="nom" name="nom" class="grand">
                </label>
                <label>Prénom :
                    <input id="prenom" name="prenom" class="grand">
                </label>
                <label>Mail :
                    <input type="email" id="mail" name="mail" class="grand">
                </label>
                <label>Téléphone :
                    <input id="telephone" name="telephone" type="tel" class="grand">
                </label>
            </fieldset>
            <fieldset>
                <legend>Niveau de jeu estimé :</legend>
                <label id="jeul1">débutant
                   <input type="radio" name="niveau" value="debutant" id="jeu1">
                </label>
                <label id="jeul2">intermédiaire
                   <input type="radio" name="niveau" value="intermediaire" id="jeu2">
                </label>
                <label id="jeul3">confirmé
                   <input type="radio" name="niveau" value="confirme" id="jeu3">
                </label>
                <label id="jeul4">expert
                   <input type="radio" name="niveau" value="expert" id="jeu4">
                </label>
            </fieldset>
            <fieldset>
                <legend>Es-tu adhérent(e) SandSystem ?</legend>
                <label id="adl1">oui
                   <input type="radio" id="ad1" name="adherent" value="oui">
                </label>
                <label id="adl1">non
                   <input type="radio" id="ad2" name="adherent" value="non">
                </label>
                <div id="adhesion" style="display: none">
                    Pour les non-adhérents, la session est à 10 euros
                    <a href="index.php"> lien pour adhérer</a>
                </div>
            </fieldset>
                <textarea name="commentaire" placeholder="Indique ici si tu souhaites jouer avec quelqu'un en particulier"></textarea>
            </form>
        </div>

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
            <LABEL for="consignergpd" class="textergpd"> J'autorise Sandsystem à sauvegarder mes informations personnelles pendant une durée de 6 mois maximum. (à tout moment, il suffit d'envoyer un mail à info@sandystem.com pour les supprimer)</LABEL></DIV><BR>
        <DIV class="centree"> <A href=" https://drive.google.com/file/d/12l7dqbU4wu52WcvrWphWyRbwFPnm-2qz/view?usp=sharing">lien vers le protocole de reprise</A></DIV><BR><BR>
        <DIV class="centree"><BUTTON type="button"  onclick="validation_formulaire()" > Soumettre la demande de créneaux</BUTTON></DIV>
    </DIV>
    <BR><BR><BR><BR>

</BODY>
<SCRIPT src="index_java.js?=<?php echo time(); //pour forcer le js a ne pas être en cache et jouer des tours en dev ?>">  </SCRIPT>
</HTML>
