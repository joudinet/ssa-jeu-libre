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
$les_creneaux=lire_les_creneaux();
ferme_bdd();
$nom=["feminin"=>"féminin","reserve"=>"réservé","masculin"=>"masculin","mixte"=>"mixte"];
?>
<body>
   <nav class="menu">
        <ul>
             <li><a href="index.php">Demande d'inscription</a></li><!-- ' -->
             <li><a href="#">Occupation des terrains</a></li>
        </ul>
    </nav>

           <h1 class="titre_index">Occupation provisoire des terrains pour chaque créneau</h1>
    <table>
<?php foreach ($les_creneaux as $un_creneau) {
    echo "<tr>";
    echo "<th rowspan='2'>".secu_ecran(jolie_date($un_creneau['date']))." ".secu_ecran($un_creneau['heure'])."</td>";
    foreach (['1','2','3','4'] as $terrain) {
        echo "<td style='background-color:".$un_creneau["C".$terrain].";'>T".$terrain." : ".$nom[$un_creneau["T".$terrain]]."</td>";
    }
    echo "</tr>\n";
    echo "<tr>";
    foreach (['1','2','3','4'] as $terrain) {
        echo "<td>".($un_creneau["V".$terrain]+$un_creneau["A".$terrain])." personnes</td>";
    }
    echo "</tr>\n";

} ?>
    </table>
</body>
</html>
