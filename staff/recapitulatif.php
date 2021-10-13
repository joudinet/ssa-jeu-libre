
<?php
require "header.php";
ouvre_bdd();
$les_creneaux=lire_les_creneaux();
$les_staffeurs=lire_les_staffeurs();
$staffeur=$_SERVER['PHP_AUTH_USER'];
if (pas_un_staffeur($staffeur)) { die(); }
ferme_bdd();
?>

    En <span style='color:red'>rouge</span> les personnes disponibles si besoin
    <div class="creneaux_index_staff">
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
    if (array_key_exists($id,$les_staffeurs)) {
        foreach ($les_staffeurs[$id] as $nom) {
            if ($nom[1]=='oui') {echo $nom[0].", ";}
        }
        foreach ($les_staffeurs[$id] as $nom) {
            if ($nom[1]=='sibesoin') {echo "<span style='color:red'>".$nom[0]."</span>, ";}
        }
    }
    echo '</div>';
}

?>
    </div>
</body>
</html>