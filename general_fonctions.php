<?php

setlocale(LC_TIME, 'fr_FR');   //pour l'affichage des dates
date_default_timezone_set('Europe/Paris');


$terrainpossible=['reserve','feminin','mixte','masculin']; // champs autorisés pour les terrains
$couleurpossible=["#CBCBCB","#FF0099","#00CCFF","#33FF00"]; // reservé, féminin, mixte, masculin

function lire_les_creneaux() { // renvoie les créneaux classés par ordre chronologique, en vérifiant les données
    global $dbh,$terrainpossible,$couleurpossible;  
    try {
        $res=$dbh->query('SELECT * FROM CRENEAUX ORDER BY date');
        $tab_res=[];
        while ($row=$res->fetch()) {
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
    global $dbh;
    try {
        $dbh = new PDO('mysql:host=localhost;dbname=................);   \\ à compléter avec la base utilisée
    } catch (PDOException $e) {
        print "Erreur d'accès";
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