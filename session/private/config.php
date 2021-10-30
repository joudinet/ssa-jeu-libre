<?php
// MySQL connection data
// host
$mysql_host='localhost';
// infos connexion
$mysql_user='';
$mysql_pass='';
// info base de données
$mysql_dbname='';
$mysql_tablename='STAFF';
$mysql_loginname='nom';
$mysql_passwordname='password';
// info de gestion
$mysql_liste_admin=['admin']; //comptes non modifiables ou effacables
$mail_login=true;     // autorise la réinitialisatin par mail/modif de mail
// utile uniquement si  $mail_login=true
$mysql_mailname='mail';  
$mysql_noncename='nonce';   // nonce à envoyer en cas de demande par reset par mail
$mysql_noncetimename='noncetime'; // date limite de validité du nonce
$mysql_noncetimevalue=24*60*60; //durée de validité du nonce en secondes
$adresse_site_demande='https://www.sandsystem.com/jeu-libre/session/';
$mail_from='jeu-libre@sandsystem.com';
$mail_fromName='SandSysteml';?>
