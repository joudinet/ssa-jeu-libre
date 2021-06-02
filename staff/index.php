<!DOCTYPE html>

<html>
<head>
    <meta http-equiv=content-type content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des créneaux</title>
    <link rel="stylesheet" href="staff_style.css?=<?php echo time(); //pour forcer le css a ne pas être en cache et jouer des tours en dev ?>">
</head>

<?php
require "../general_fonctions.php";
ouvre_bdd();
$les_creneaux_du_jour=lire_les_creneaux_du_jour();
$stmt = $dbh->prepare("SELECT * FROM RESULTAT WHERE idcreneau=? AND terrain=? AND etat='valide'");
$stmt->bindParam(1, $idcreneau);
$stmt->bindParam(2, $terrain);
?>
    <DIV class="containeurdemandesgeneral"> 
    <DIV class="containeurdemandes">
<?php
    foreach ($les_creneaux_du_jour as $le_creneau) {
        $idcreneau=$le_creneau['id'];
        echo '<DIV class="unjourtitre"> Créneau du '.secu_ecran(jolie_date($le_creneau['date'])).' sur la période : '.secu_ecran($le_creneau['heure']);
        echo '</DIV>'; ?>
        <DIV class="unjour">
 <?php
        foreach(['1','2','3','4'] as $numero_terrain) {
            $terrain='T'.$numero_terrain;?>
            <TABLE>
                <TR><TH  style="background-color:<?php echo $le_creneau['C'.$numero_terrain];?>;">  <?php echo $terrain.' : '.$le_creneau[$terrain]; ?>  </TH></TR>
<?php           $stmt->execute();
                $nbvalides=0;
                while ($row=$stmt->fetch()) { $nbvalides+=1; ?>
                    <TR><TD  style="background-color : <?php if ($row['adherent']=='non') { echo "#FFFF00"; } else { echo "white";} ?>;">
                       <?php echo secu_ecran($row['prenom']).' '.secu_ecran($row['nom']); ?>
                    </TD></TR>
<?php           }
                while ($nbvalides<$le_creneau['VMAX'.$numero_terrain]) {$nbvalides++; echo "<TR><TD style='color: white;'> _ </TD></TR>";}?>
            </TABLE>
<?php       } ?>
        </DIV>
<?php
    } ?>
    </DIV>



<?php
ferme_bdd();
?>
    
</body>
</html>