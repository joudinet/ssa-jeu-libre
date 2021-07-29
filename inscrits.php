
<?php
require "header.php";
ouvre_bdd();
$les_creneaux=lire_les_creneaux();
$affluence=lire_affluence();
ferme_bdd();
?>

    <h1 class="titre_index">Occupation provisoire des terrains en heures creuses :</h1>
 
    <div class="les_inscrits">
<?php foreach ($les_creneaux as $un_creneau) {
    if ($un_creneau['reservation']=="oui") {
        echo "<div> ".secu_ecran(jolie_date($un_creneau['date']))." ".secu_ecran($un_creneau['heure'])." : </div>";
        echo "<div> ";
        if (array_key_exists($un_creneau['id'],$affluence)) {
            echo $affluence[$un_creneau['id']];
        } else {
            echo "0";
        }
        echo " personne(s)</div>";
    }
} ?>
    </div>
 
</body>
</html>
