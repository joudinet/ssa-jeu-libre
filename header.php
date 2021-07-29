<!DOCTYPE html>

<html>
<head>
    <meta http-equiv=content-type content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des créneaux</title>
    <link rel="stylesheet" href="index_style.css?=<?php echo time(); //pour forcer le css a ne pas être en cache et jouer des tours en dev ?>">
    <script src="index_java.js?=<?php echo time(); //pour forcer le js a ne pas être en cache et jouer des tours en dev ?>" defer></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>

<body>
    <nav class="menu">
        <ul>
             <li><a href="index.php">Liste des créneaux</a></li>
             <li><a href="inscrits.php">Inscrits en heures creuses</a></li>
        </ul>
    </nav>

    <?php require "index_fonctions.php"; ?>
