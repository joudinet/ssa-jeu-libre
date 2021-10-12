<!DOCTYPE html>

<html>
<head>
    <meta http-equiv=content-type content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des créneaux</title>
    <link rel="stylesheet" href="staff_style.css?=<?php echo time(); //pour forcer le css a ne pas être en cache et jouer des tours en dev ?>">
 </head>
    
<body>    
    <nav class="menu">
        <ul>
             <li><a href="index.php">Gérer mes présences</a></li><!-- ' -->
             <li><a href="recapitulatif.php">Récapitulatif</a></li>
             <li><a href="coordonnees.php">Coordonnées du staff</a></li>
         </ul>
    </nav>

<?php
require "staff_fonctions.php";
?>