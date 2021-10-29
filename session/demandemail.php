<?php
if (!(isset($_GET["login"]) && isset($_GET["nonce"]))) {
	die();
}?>

<?php   
require "private/fonctions.php";
// Traitement du formulaire après envoi
$tentativeModification=false;
$modificationCorrecte=false; //utilisé en cas de tentative de modification uniquement
$typeErreur=""; // inutile : jamais utilisé sans être redéfini. Juste au cas où
if (isset($_POST["newpassword"]) && isset($_POST["newpasswordbis"])){
	$tentativeModification=true;
	if (ouvre_bdd()) {
		if (!verifie_nonce($_GET["login"],$_GET["nonce"])) {
			$typeErreur="le lien n'est plus valable";
		} elseif ($_POST["newpassword"]!=$_POST["newpasswordbis"]) {
			$typeErreur="entrez deux fois le nouveau mot de passe";
		} elseif (securise_login($_POST["newpassword"])!=$_POST["newpassword"]) {
			$typeErreur="nouveau mot de passe incorrect";
		} else {
			$modificationCorrecte=maj_compte($_GET["login"],$_POST["newpassword"]);
			$typeErreur="impossible de modifier le mot de passe";
		}
		ferme_bdd();
	} else {
		$typeErreur="base de données inaccessible";
	} 
}?>

<!doctype html>
<html>
<head>
<meta http-equiv=content-type content="text/html;charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Page de gestion</title>
<style type="text/css">
.login_box {
	width: 400px;
	margin: auto;
}
label {
	display: block;
	width: 170px;
	float: left;
} 
</style>
</head>

<body>

<?php
if ($modificationCorrecte) { ?>
    Le nouveau mot de passe a été validé. Pour la page d'index c'est <a href="index.php"> ici </a> <BR>
<?php } else { ?> 
    Pour la page d'index c'est <a href="index.php"> ici </a> <BR>
     <div class="login_box">
     	<form action="" method="post">
     		<fieldset>
     			<legend>Réinitialisation du mot de passe</legend>
     			<?php if ($tentativeModification) {
				echo "<p style=\"color:red; \"> le mot de passe n'a pas été correctement réinitialisé </p>";
				echo "<p style=\"color:red; \">".$typeErreur."</p>";
				} ?>
      			<label for="newpassword">Nouveau mot de passe :</label>
     			<input type="password" name="newpassword" id="newpassword" placeholder="Entrez votre nouveau mot de passe" required>
     			<label for="newpasswordbis">Nouveau mot de passe bis:</label>
     			<input type="password" name="newpasswordbis" id="newpasswordbis" placeholder="Entrez votre nouveau mot de passe" required>
     			<input type="submit" value="Envoyer"/>
     		</fieldset>
     	</form>
     </div>
<?php } ?>
</body>
</html>
