<?php
session_start();
if (!isset($_SESSION["login"])) {
	header('Location:login.php');
	die();
}?>

<?php   
require "private/fonctions.php";
// Traitement du formulaire après envoi
$tentativeCreation=false;
$creationCorrecte=false; //utilisé en cas de tentative de création uniquement
$tentativeSuppression=false;
$suppressionCorrecte=false; //utilisé en cas de tentative de suppression uniquement
$typeErreur=""; // inutile : jamais utilisé sans être redéfini. Juste au cas où
if (isset($_POST["login"])){
	$tentativeCreation=true;
	if ($_POST["login"]!=securise_login($_POST["login"])) {
		$typeErreur="nom de login incorrect";
	} elseif (ouvre_bdd()) {
		if (test_existence_compte($_POST["login"])) {
			$typeErreur="compte déjà existant";
		} elseif (creation_compte($_POST["login"])) {
			if ($mail_login) {
				$creationCorrecte=maj_compte($_POST["login"],$_POST["password"]);
				$creationCorrecte=$creationCorrecte && maj_compte_mail($_POST["login"],$_POST["mail"]);
			} else {
				$creationCorrecte=maj_compte($_POST["login"],$_POST["password"]);
			}
			$typeErreur="impossible d'attribuer un mot de passe et/ou mail à ce compte";
		} else {
			$typeErreur="impossible de créer ce compte";
		}
		ferme_bdd();
	} else {
		$typeErreur="base de données inaccessible";
	}
} elseif (isset($_POST["loginSuppression"])){
	$tentativeSuppression=true;
	if ($_POST["loginSuppression"]!=securise_login($_POST["loginSuppression"])) {
		$typeErreur="nom de login incorrect";
	} elseif (ouvre_bdd()) {
		if (test_existence_compte($_POST["loginSuppression"])) {
			$suppressionCorrecte=supprime_compte($_POST["loginSuppression"]);
			$typeErreur="impossible de supprimer ce compte";
		} else {
			$typeErreur="compte inexistant";
		}
		ferme_bdd();
	} else {
		$typeErreur="base de données inaccessible";
	}
} ?>

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
	width: 150px;
	float: left;
} 
</style>
</head>

<body>
Pour la page d'index c'est <a href="index.php"> ici </a> <BR>
     <div class="login_box">
     	<form action="" method="post">
     		<fieldset>
     			<legend>Création d'un compte</legend>
     			<?php if ($tentativeCreation) {
				if ($creationCorrecte) {
					echo "<p style=\"color:red; \"> le compte de ".$_POST["login"];
					echo " a été correctement créé</p>";
				} else {
					echo "<p style=\"color:red; \"> erreur : ".$typeErreur."</p>";
				}
     			      } ?>
     			<label for="login">Login :</label>
     			<input type="text" name="login" id="login" placeholder="Entrez votre login" required/>
     			<label for="password">Password :</label>
     			<input type="password" name="password" id="password" placeholder="Entrez votre mot de passe" required>
				<?php if ($mail_login) {?>
				<label for="mail">Mail :</label>
     			<input type="email" name="mail" id="mail" placeholder="Entrez votre mail"/>
				<?php }?>
    			<input type="submit" value="Envoyer"/>
     		</fieldset>
     	</form>
     </div>
     <div class="login_box">
     	<form action="" method="post">
     		<fieldset>
     			<legend>Suppression  d'un compte</legend>
     			<?php if ($tentativeSuppression) {
				if ($suppressionCorrecte) {
					echo "<p style=\"color:red; \"> le compte de ".$_POST["loginSuppression"];
					echo " a été correctement supprimé</p>";
				} else {
					echo "<p style=\"color:red; \"> erreur : ".$typeErreur."</p>";
				}
     			      } ?>
     			<label for="loginSuppression">Login :</label>
     			<input type="text" name="loginSuppression" id="loginSuppression" placeholder="Entrez votre login" required/>
     			<input type="submit" value="Envoyer"/>
     		</fieldset>
     	</form>
     </div>
</body>
</html>
