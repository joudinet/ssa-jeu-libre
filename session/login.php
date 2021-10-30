<?php  
session_start(); 
require "private/fonctions.php";
// Traitement du formulaire après envoi
$loginIncorrect=false; // pour ne pas afficher de message login incorrect par défaut
$recuperation=false; // ne pas afficher de message pour la récupération
$recuperationCorrecte=false;
$typeErreur="";
if (isset($_POST["login"])){
	if (isset($_POST["password"])){
		if (ouvre_bdd()) {
			$loginIncorrect=!(verifie_compte($_POST["login"],$_POST["password"]));
			ferme_bdd();
			if (!$loginIncorrect) {
				$_SESSION["login"]=$_POST["login"];
				header("Location:index.php");
				die();
			} 
		} else {
			$loginIncorrect=true;
		}
	} elseif (isset($_POST["mail"]) && $mail_login){
		$recuperation=true;
		if (ouvre_bdd()) {
			$recuperationCorrecte=recuperation_par_mail($_POST["login"],$_POST["mail"]);
			$typeErreur="Erreur lors de l'envoi de mail";
			ferme_bdd();
		} else {
			$typeErreur="Erreur lors de l'envoi de mail";
		}

	}
}
?>

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
     <div class="login_box">
     	<form action="" method="post">
     		<fieldset>
     			<legend>Formulaire d'authentification</legend>
     			<?php if ($loginIncorrect)
     				echo "<p style=\"color:red; \">login ou mot de passe incorrect</p>";
     				?>
     			<label for="login">Login :</label>
     			<input type="text" name="login" id="login" placeholder="Entrez votre login" required/>
     			<label for="password">Password :</label>
     			<input type="password" name="password" id="password" placeholder="Entrez votre mot de passe" required>
     			<input type="submit" name="Envoyer" value="Envoyer"/>
     		</fieldset>
     	</form>
     </div>
	 <?php if ($mail_login) {?>
     <div class="login_box">
     	<form action="" method="post">
     		<fieldset>
     			<legend>Mot de passe oublié?</legend>
     			<?php if ($recuperation) {
					if ($recuperationCorrecte) {
     					echo "<p style=\"color:red; \">un mail a été envoyé à l'adresse pour changer le mot de passe</p>";
					} else {
						echo "<p style=\"color:red; \">".$typeErreur."</p>";
					}
				}?>
     			<label for="login">Login :</label>
     			<input type="text" name="login" id="login" placeholder="Entrez votre login" required/>
     			<label for="mail">Mail :</label>
     			<input type="email" name="mail" id="mail" placeholder="Entrez votre email" required>
     			<input type="submit" name="Envoyer" value="Envoyer"/>
     		</fieldset>
     	</form>
     </div>
	 <?php } ?>
</body>
</html>