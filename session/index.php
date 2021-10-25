<?php
session_start();
if (!isset($_SESSION["login"])) {
	header('Location:login.php');
	die();
}?>

Bienvenu(e) <?php echo $_SESSION['login']; ?> <BR><BR>
Pour la page de gestion c'est <a href="gestion.php"> ici </a> <BR>
Pour se déconnecter c'est <a href="deconnexion.php"> ici </a>






