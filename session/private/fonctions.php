<?php
require "config.php";
use PHPMailer\PHPMailer\PHPMailer; 
use PHPMailer\PHPMailer\SMTP;
if ($mail_login) {
	require '../PHPMailer/src/PHPMailer.php';
	require '../PHPMailer/src/SMTP.php';
	require '../PHPMailer/src/Exception.php';
}

function creation_compte($login) { // création d'un nouveau compte
	global $dbh,$mysql_tablename,$mysql_loginname,$mysql_liste_admin;
	if (in_array($login,$mysql_liste_admin)) {
		return false;
	}
	try {
		$stmt=$dbh->prepare("INSERT INTO ".$mysql_tablename." (".$mysql_loginname.") VALUES (?)");
		$stmt->bindParam(1,$login);
		$stmt->execute();
		return true; 
	} catch (PDOException $e) {
		return false;
	}
	return false;
}

function maj_compte($login,$password) { // modif du mot de passe d'un compte
	global $dbh,$mysql_tablename,$mysql_loginname,$mysql_passwordname,$mysql_liste_admin,$mysql_noncetimename,$mail_login;
	if (in_array($login,$mysql_liste_admin)) {
		return false;
	}
	try {
		$hash=password_hash($password, PASSWORD_DEFAULT);
		if ($mail_login) {
			$stmt=$dbh->prepare("UPDATE ".$mysql_tablename." SET ".$mysql_passwordname."=?,".$mysql_noncetimename."=0 WHERE ".$mysql_loginname."=?");
		} else {
			$stmt=$dbh->prepare("UPDATE ".$mysql_tablename." SET ".$mysql_passwordname."=? WHERE ".$mysql_loginname."=?");		
		}
		$stmt->bindParam(1,$hash);
		$stmt->bindParam(2,$login);
		$stmt->execute();
		return true;
	} catch (PDOException $e) {
		return false;
	}
	return false;
}

function maj_compte_mail($login,$mail) { // modif du mail d'un compte
	global $dbh,$mysql_tablename,$mysql_loginname,$mysql_liste_admin,$mysql_mailname,$mail_login;
	if (in_array($login,$mysql_liste_admin)) {
		return false;
	}
	try {
		$stmt=$dbh->prepare("UPDATE ".$mysql_tablename." SET ".$mysql_mailname."=? WHERE ".$mysql_loginname."=?");
		$stmt->bindParam(1,$mail);
		$stmt->bindParam(2,$login);
		$stmt->execute();
		return true;
	} catch (PDOException $e) {
		return false;
	}
	return false;
}

function ferme_bdd() {
	global $dbh;
	$dbh=null;
	return ;
}

function ouvre_bdd() { // renvoie true/false en cas de réussite/échec de la connexion
	global $dbh, $mysql_host, $mysql_dbname, $mysql_user, $mysql_pass;
	try { 
		$dbh = new PDO('mysql:host='.$mysql_host.';dbname='.$mysql_dbname,$mysql_user,$mysql_pass);
		return true;
	} catch (PDOException $e) {
		return false;
	}
	return false;
}

function recuperation_par_mail($login,$mail) {
	global $dbh,$mysql_tablename,$mysql_loginname,$mysql_mailname,$mail_from, $mail_fromName,$adresse_site_demande,$mysql_noncename,$mysql_noncetimename,$mysql_noncetimevalue;
	try {	
		$stmt=$dbh->prepare("SELECT COUNT(*) AS nb FROM ".$mysql_tablename." WHERE ".$mysql_loginname."=? AND ".$mysql_mailname."=?");
		$stmt->bindParam(1,$login);
		$stmt->bindParam(2,$mail);
		$stmt->execute();
		$row=$stmt->fetch(); 
		if ($row['nb']!='0') { // login/mail correspondent à un vrai compte
			$nonce=urlencode(base64_encode(random_bytes(60)));
			$noncetime=time()+$mysql_noncetimevalue;
			$stmt=$dbh->prepare("UPDATE ".$mysql_tablename." SET ".$mysql_noncename."=?,".$mysql_noncetimename."=? WHERE ".$mysql_loginname."=?");
			$stmt->bindParam(1,$nonce);
			$stmt->bindParam(2,$noncetime);
			$stmt->bindParam(3,$login);
			$stmt->execute();
			$instance_mail = new PHPmailer();
			$instance_mail->CharSet = 'UTF-8';
			$instance_mail->setFrom($mail_from, $mail_fromName);
			$instance_mail->ContentType = 'text/plain';
			$instance_mail->Subject='Demande de changement de mot de passe';
			$msg="Bonjour, \n\nUne demande de nouveau mot de passe vient d'être reçu pour ce compte!\n";
			$msg.="Utilise ce lien, valable 24h, pour définir un nouveau mot de passe :\n";
			$msg.=$adresse_site_demande."demandemail.php?login=".urlencode($login)."&nonce=".$nonce."\n";
			$msg.="--\n Merci de ne pas répondre à ce mail automatique";
			$instance_mail->Body=$msg;
			$instance_mail->AddAddress($mail);
			if (!$instance_mail->send()) {
				return false;  // passer à true éventuellement mais cela donne peu d'infos confidentielles
			}
			$instance_mail->SmtpClose();
			unset($instance_mail);
			return true; 
		} else {
			return true;   // pour ne pas indiquer que c'est un faux compte
		}
	} catch (PDOException $e) { 
		return false;
	}
	return false;

}

function test_existence_compte($login) { // renvoie vrai en cas d'erreur ou si le compte existe.
	global $dbh,$mysql_tablename,$mysql_loginname;
	try {	
		$stmt=$dbh->prepare("SELECT COUNT(*) AS nb FROM ".$mysql_tablename." WHERE ".$mysql_loginname."=?");
		$stmt->bindParam(1,$login);
		$stmt->execute();
		$row=$stmt->fetch(); 
		if ($row['nb']!='0') {
			return true; 
		} else {
			return false;
		}
	} catch (PDOException $e) { 
		return false;
	}
	return false;
}

function verifie_compte($login,$password) { //vérifie dans la base de donnée que le login est correct
	global $dbh,$mysql_tablename,$mysql_loginname,$mysql_passwordname;
	try {
		$stmt=$dbh->prepare("SELECT ".$mysql_passwordname." AS pwd FROM ".$mysql_tablename." WHERE ".$mysql_loginname."=?");
		$stmt->bindParam(1,$login);
		$stmt->execute();
		while ($row=$stmt->fetch()) {
			if (password_verify($password,$row['pwd'])) { return true;}
			return false;
		}
	} catch (PDOException $e) {
		return false;
	}
	return false;
}

function verifie_nonce($login,$nonce) { // vérifie que le lien de réinitialisation par mail est correct
	global $dbh,$mysql_tablename,$mysql_loginname,$mysql_mailname,$mail_from, $mail_fromName,$adresse_site_demande,$mysql_noncename,$mysql_noncetimename,$mysql_noncetimevalue;
	try {	
		$urlnonce=urlencode($nonce);
		$stmt=$dbh->prepare("SELECT nonce,noncetime FROM ".$mysql_tablename." WHERE ".$mysql_loginname."=? AND ".$mysql_noncename."=?");
		$stmt->bindParam(1,$login);
		$stmt->bindParam(2,$urlnonce);
		$stmt->execute();
		while ($row=$stmt->fetch()) {
			if (intval($row['noncetime'])>time()) {
				return true;
			}
			return false;
		}
	} catch (PDOException $e) {
		return false;
	}
	return false;
}

function securise_login($login) { // vérifie que le nom de login a un format valide,
	// et s'affiche sans danger à l'écran
	return htmlentities(preg_replace('`([^a-zA-Z0-9 ])`i', '', $login));
}

function supprime_compte($login) {
	global $dbh,$mysql_tablename,$mysql_loginname,$mysql_liste_admin;
	if (in_array($login,$mysql_liste_admin)) {
		return false;
	}
	try {
		$stmt=$dbh->prepare("DELETE FROM ".$mysql_tablename." WHERE ".$mysql_loginname."=?");
		$stmt->bindParam(1,$login);
		$stmt->execute();
		return true;
	} catch (PDOException $e) {
		return false;
	}
	return false;
}

?>
