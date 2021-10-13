<?php 

require "../general_fonctions.php";

function lire_les_creneaux_staffeur($staffeur) { // liste les créneaux gérés par le staffeur
    global $dbh;
    try {
        $date=date('Y-m-d');
        $stmt=$dbh->prepare('SELECT * FROM GESTIONCRENEAUX JOIN STAFF ON idstaff=STAFF.id JOIN CRENEAUX ON CRENEAUX.id=idcreneau WHERE nom=? and date>=?');
        $stmt->bindParam(1,$staffeur);
        $stmt->bindParam(2,$date);
        $stmt->execute();
        $tab_res=[];
        while ($row=$stmt->fetch()) {
            array_push($tab_res,[$row['idcreneau'],$row['statut']]); 
        }    
        return $tab_res;
    } catch (Exception $e) {
        print "Erreur dans la base de données du staff";
        die();
    }
}

function lire_les_staffeurs() {
    global $dbh;
    try {
        $stmt=$dbh->prepare('SELECT * FROM GESTIONCRENEAUX JOIN STAFF ON idstaff=id');
        $stmt->execute();
        $tab_res=[];
        while ($row=$stmt->fetch()) {
            if (array_key_exists($row['idcreneau'],$tab_res)) {
                array_push($tab_res[$row['idcreneau']],[$row['nom'],$row['statut']]);
            } else {
                $tab_res[$row['idcreneau']]=[[$row['nom'],$row['statut']]];
            }
        }    
        return $tab_res;
    } catch (Exception $e) {
        print "Erreur dans la base de données du staff";
        die();
    }
}

function pas_un_staffeur($staffeur) {
    global $dbh;
    try {
        $stmt=$dbh->prepare('SELECT * FROM STAFF WHERE nom=?');
        $stmt->bindParam(1,$staffeur);
        $stmt->execute();
        while ($row=$stmt->fetch()) {
            return false;
        }
    } catch (Exception $e) { echo "erreur";die();} 
    return true;
}

function trouveCreneau($id) { // renvoie la ligne de $liste_creneau associé au creneau défini par son id
    global $les_creneaux;
    foreach ($les_creneaux as $uncreneau) {
        if ($uncreneau['id']==$id) {
            return $uncreneau;
        }
    }
    echo "erreur dans la base de données des créneaux";
    die();
}
?>