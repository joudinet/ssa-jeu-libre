<?php
// MySQL connection data
// host
$mysql_host='localhost';
// infos connexion
$mysql_user='';
$mysql_pass='';
// info base de données
$mysql_dbname='';
$mysql_tablename='';
$mysql_loginname='login';
$mysql_passwordname='password';
$mysql_mailname='mail';   // utile uniquement si  $mail_login=true
// info de gestion
$mysql_liste_admin=['admin']; //comptes non modifiables ou effacables
$mail_login=false;     // autorise la réinitialisatin par mail/modif de mail
?>
