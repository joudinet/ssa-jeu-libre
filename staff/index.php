<!DOCTYPE html>

<html>
<head>
    <meta http-equiv=content-type content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des créneaux</title>
    <link rel="stylesheet" href="staff_style.css?=<?php echo time(); //pour forcer le css a ne pas être en cache et jouer des tours en dev ?>">
    <script src="staff_java.js?=<?php echo time(); //pour forcer le js a ne pas être en cache et jouer des tours en dev ?>" defer></script>
</head>

<?php
require "staff_fonctions.php";
ouvre_bdd();
$les_creneaux=lire_les_creneaux();
$staffeur=$_SERVER['PHP_AUTH_USER'];
if (pas_un_staffeur($staffeur)) { die(); }
$les_creneaux_du_staffeur=lire_les_creneaux_staffeur($staffeur);
if (isset($_POST['validation'])) {
    met_a_jour_creneau_staff($staffeur);
}
$les_creneaux=lire_les_creneaux();
$les_creneaux_du_staffeur=lire_les_creneaux_staffeur($staffeur);
ferme_bdd();
?>
    
<body>    
    <nav class="menu">
        <ul>
             <li><a href="#">Inscription gestion</a></li><!-- ' -->
             <li><a href="gestion.php">Récapitulatif</a></li>
        </ul>
    </nav>
<BR><BR>
    <form id="le_formulaire_index" class="formulaire_index" method="post" action="index.php">
        <input type="hidden" name="validation" value="">
    <fieldset class="creneaux_index" id="creneauxdispos">
        <legend>Liste des créneaux disponibles : (créneaux sélectionnés en orange)</legend>
    <div class="creneaux_index">
<?php 
foreach ($les_creneaux as $un_creneau) {
    $no_creneau = true;
    $id = $un_creneau['id'];
    $nbstaff=$un_creneau['nbstaff'];
    //echo '<div></div>';
    echo '<div ';
    if ($nbstaff==0) {
        echo 'class="creneauvide"';
    }
    echo '>'.secu_ecran(jolie_date($un_creneau['date'])).' '.secu_ecran($un_creneau['heure']).' :</div>';
    echo '<div>';
    echo $nbstaff." personne(s)";
    echo '</div>';
    echo '<div>';
        echo '<input class="checkboxfeminin" type="checkbox" id="c'.$id.'" name="c'.$id.'" onclick="click_creneau('.$id.')" value=0 />';
        echo '<label id="lc'.$id.'" for="c'.$id.'" class="round">Gérer ce créneau</label>';
    echo '</div>';
}

?>
    </div>
    </fieldset><BR>
    <button type="button" onclick="validation_formulaire()" >Mettre à jour la gestion des créneaux</button>
    </form>
    
</body>

<script>
<?php

foreach ($les_creneaux_du_staffeur as $id) {
    echo "document.getElementById('c".$id."').checked=true\n";
} 
?>
</script>


</html>