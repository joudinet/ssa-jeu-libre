<?php

require "config.php";

setlocale(LC_TIME, 'fr_FR');   //pour l'affichage des dates
date_default_timezone_set('Europe/Paris');


$terrainpossible=['reserve','feminin','mixte','masculin']; // champs autorisés pour les terrains
$couleurpossible=["#CBCBCB","#FF0099","#00CCFF","#33FF00"]; // reservé, féminin, mixte, masculin
$les_couleurs=["feminin"=>"#00CCFF","masculin"=>"#33FF00","mixte"=>"#FF0099","reserve"=>"#CBCBCB"];


function lire_annonce() {
    global $dbh;
    $stmt=$dbh->query("SELECT * FROM ANNONCE WHERE id=1");
    while ($row=$stmt->fetch()) {
        $text=$row["texte"];
        return explode("\n",$text);
    }
    return [];
}

function affiche_annonce() {
    global $annonce;
    if (!$annonce==[]) {
        echo "<h3>";
        foreach ($annonce as $ligne) {
            echo secu_ecran($ligne);
            echo "<BR>";
        }
        echo "</h3>";
    }
}

function lire_les_creneaux_du_jour() { // renvoie les créneaux du jour classés par ordre chronologique, en vérifiant les données
    global $dbh,$terrainpossible,$couleurpossible;
    try {
        $date=date('Y-m-d');
        $stmt=$dbh->prepare('SELECT * FROM CRENEAUX WHERE date=? ORDER BY heure');
        $stmt->bindParam(1,$date);
        $stmt->execute();
        $tab_res=[];
        while ($row=$stmt->fetch()) {
            if (!in_array($row['C1'],$couleurpossible) || !in_array($row['C2'],$couleurpossible) || !in_array($row['C3'],$couleurpossible) || !in_array($row['C4'],$couleurpossible)) {
                throw new Exception('erreur');   }
            if (!in_array($row['T1'],$terrainpossible) || !in_array($row['T2'],$terrainpossible) || !in_array($row['T3'],$terrainpossible) || !in_array($row['T4'],$terrainpossible)) {
                throw new Exception('erreur');   }
            array_push($tab_res,$row);
        }
        return $tab_res;
    } catch (Exception $e) {
        print "Erreur dans la base de données des créneaux";
        die();
    }
}

function lire_les_creneaux($vieux=false) { // renvoie les créneaux classés par ordre chronologique, en vérifiant les données
    global $dbh,$terrainpossible,$couleurpossible;
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
            if (!in_array($row['C1'],$couleurpossible) || !in_array($row['C2'],$couleurpossible) || !in_array($row['C3'],$couleurpossible) || !in_array($row['C4'],$couleurpossible)) {
                throw new Exception('erreur');   }
            if (!in_array($row['T1'],$terrainpossible) || !in_array($row['T2'],$terrainpossible) || !in_array($row['T3'],$terrainpossible) || !in_array($row['T4'],$terrainpossible)) {
                throw new Exception('erreur');   }
            array_push($tab_res,$row);
        }
        return $tab_res;
    } catch (Exception $e) {
        print "Erreur dans la base de données des créneaux";
        die();
    }
}

function jolie_date($date) { // date sous forme  "jour numéro_jour mois " en lettres
    return strftime('%A %e %B',strtotime($date));
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

function ferme_bdd() {
    global $dbh;
    $dbh=null;
    return ;
}

?>
