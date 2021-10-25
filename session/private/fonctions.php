<?php
require "config.php";

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

function maj_compte($login,$password) { // modif du mot de passe d'un nouveau compte
	global $dbh,$mysql_tablename,$mysql_loginname,$mysql_passwordname,$mysql_liste_admin;
	if (in_array($login,$mysql_liste_admin)) {
		return false;
	}
	try {
		$stmt=$dbh->prepare("UPDATE ".$mysql_tablename." SET ".$mysql_passwordname."=? WHERE ".$mysql_loginname."=?");
		$hash=password_hash($password, PASSWORD_DEFAULT);
		$stmt->bindParam(1,$hash);
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
