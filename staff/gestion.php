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
$les_staffeurs=lire_les_staffeurs();
$staffeur=$_SERVER['PHP_AUTH_USER'];
if (pas_un_staffeur($staffeur)) { die(); }
ferme_bdd();
?>
    
<body>    
    <nav class="menu">
        <ul>
             <li><a href="index.php">Inscription gestion</a></li><!-- ' -->
             <li><a href="#">Récapitulatif</a></li>
        </ul>
    </nav>
    
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
            echo $nom.", ";
        }
    }
    echo '</div>';
}

?>
    </div>
</body>
</html>