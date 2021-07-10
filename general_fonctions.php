<?php

error_reporting(E_ALL); // pour activer les erreurs
ini_set("display_errors", 1);  // à commenter à la fin bien sûr

require "config.php";
setlocale(LC_TIME, 'fr_FR');   //pour l'affichage des dates
date_default_timezone_set('Europe/Paris');

function affiche_annonce() {
    global $annonce;
    if (!$annonce==[]) {
        $str = '<p>' . implode("<br>", $annonce) . '</p>';
        print_r($str);
    }
}

function ferme_bdd() {
    global $dbh;
    $dbh=null;
    return ;
}

function jolie_date($date) { // date sous forme  "jour numéro_jour mois " en lettres
    return strftime('%A %e %B',strtotime($date));
}

function lire_annonce() {
    global $dbh;
    $stmt=$dbh->query("SELECT * FROM DIVERS WHERE intitule='annonce'");
    while ($row=$stmt->fetch()) {
        $text=$row["contenu"];
        return explode("\n",$text);
    }
    return [];
}

function lire_les_creneaux($vieux=false) { // renvoie les créneaux classés par ordre chronologique
    global $dbh,$mysql_dbname;
    try {
        $date=date('Y-m-d');
        if ($vieux) {
            $stmt=$dbh->prepare('SELECT * FROM CRENEAUX WHERE date<? ORDER BY date,heure');
        } else {
            $stmt=$dbh->prepare('SELECT * FROM CRENEAUX WHERE date>=? ORDER BY date,heure');
        } 
        $stmt->bindParam(1,$date);
        $stmt->execute();
        $tab_res=[];
        while ($row=$stmt->fetch()) {
            array_push($tab_res,$row);
        }
        return $tab_res;
    } catch (Exception $e) {
        print "Erreur dans la base de données des créneaux";
        die();
    }
}

function lire_les_creneaux_du_jour() { // renvoie les créneaux du jour classés par ordre chronologique
    global $dbh;
    try {
        $date=date('Y-m-d');
        $stmt=$dbh->prepare('SELECT * FROM CRENEAUX WHERE date=? ORDER BY heure');
        $stmt->bindParam(1,$date);
        $stmt->execute();
        $tab_res=[];
        while ($row=$stmt->fetch()) {
            array_push($tab_res,$row);
        }
        return $tab_res;
    } catch (Exception $e) {
        print "Erreur dans la base de données des créneaux";
        die();
    }
}

function ouvre_bdd() {
    global $dbh, $mysql_host, $mysql_dbname, $mysql_user, $mysql_pass;
    try {
        $dbh = new PDO('mysql:host=' . $mysql_host . ';dbname=' . $mysql_dbname,
                       $mysql_user, $mysql_pass);
    } catch (PDOException $e) {
        print "Connection error: " . $e->getMessage();
        die();
    }
    return;
}

function secu_bdd($string) {   // inutile avec requete préparée en utf-8 ?
    return $string;
}

function secu_ecran($str) { //securise une donnée avant de l'afficher sur la page HTML
    $res=htmlentities($str);
    return $res;
}

function secu_ecran_int($str) { //securise une donnée avant de l'afficher sur la page HTML
    $res=intval($str);
    if ($res<1) { die();}
    return $res;
}

?>
