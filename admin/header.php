<!DOCTYPE html>

<HTML>
    <META http-equiv=content-type content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <HEAD>
        <TITLE>Gestion des créneaux</TITLE>
        <LINK rel="stylesheet" href="gestion_style.css?=<?php echo time(); //pour forcer le css a ne pas être en cache et jouer des tours en dev ?>">
    </HEAD>

<?php
require "gestion_fonctions.php";
?>

<BODY><BR>
<ul id="menu">
    <li><a href="gestion.php">Gestion</a></li>
    <li><a href="creationcreneaux.php">Création de créneaux</a></li>
    <li><a href="modifcreneaux.php">Modification/suppression de créneaux</a></li>
    <li><a href="heurescreuses.php">Heures creuses</a></li>
    <li><a href="initialisation.php">Initialisation</a></li>
 <!--   <li><a href="validationdemandes.php">Validation des demandes</a></li>
    <li><a href="creationpdf.php">Creation de PDF/envoi de Mail</a></li>
    <li><a href="vieuxcreneaux.php">Vieux créneaux</a></li> -->
</ul>
