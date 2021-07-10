<?php 

require "../general_fonctions.php";
use PHPMailer\PHPMailer\PHPMailer; //pour le mail
use PHPMailer\PHPMailer\SMTP;
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
require '../PHPMailer/src/Exception.php';


function envoie_mail_creneaux_vides($creneaux_vides) { 
    global $dbh, $mail_from, $mail_fromName;
    try {
        $mail = new PHPmailer();
        $mail->CharSet = 'UTF-8';
        $mail->setFrom($mail_from, $mail_fromName);
        $mail->ContentType = 'text/plain';
        $mail->Subject='Désistement de staff';
        $msg="Attention :\n\nLes créneaux suivants n'ont plus de staff en gestion : \n " ;
        foreach ($creneaux_vides as $idcreneau) { 
            $lecreneau=trouveCreneau($idcreneau); 
            $msg.=jolie_date($lecreneau['date']).' '.$lecreneau['heure']."\n";
        }
        $mail->Body=$msg;
        $mail->AddAddress($mail_from);
        $mail->AddAddress('ca@sandsystem.com')
        //echo "<BR> Le mail qui sera envoyé : <BR><TEXTAREA style='width: 80%;heigth : 50px;text-align : left;'>".$msg."</TEXTAREA>";
        if (!$mail->send()) {
            echo 'Mailer error: ' . $mail->ErrorInfo;
        }
        $mail->SmtpClose();
        unset($mail); 
    } catch (Exception $e) {
        echo "Erreur dans l'envoi de mail pour prévenir l'admin";
    }
    
}

function lire_les_creneaux_staffeur($staffeur) { // liste les créneaux gérés par le staffeur
    global $dbh;
    try {
        $stmt=$dbh->prepare('SELECT * FROM GESTIONCRENEAUX JOIN STAFF ON idstaff=id WHERE nom=?');
        $stmt->bindParam(1,$staffeur);
        $stmt->execute();
        $tab_res=[];
        while ($row=$stmt->fetch()) {
            array_push($tab_res,$row['idcreneau']); 
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
                array_push($tab_res[$row['idcreneau']],$row['nom']);
            } else {
                $tab_res[$row['idcreneau']]=[$row['nom']];
            }
        }    
        return $tab_res;
    } catch (Exception $e) {
        print "Erreur dans la base de données du staff";
        die();
    }
}

function met_a_jour_creneau_staff($staffeur) {
    global $dbh,$les_creneaux,$les_creneaux_du_staffeur;
    $stmt=$dbh->prepare('SELECT * FROM STAFF WHERE nom=?');
    $stmt->bindParam(1,$staffeur);
    $idstaff=-1;
    $stmt->execute();
    while ($row=$stmt->fetch()) {
        $idstaff=intval($row['id']);
    }
    if ($idstaff==-1) { die();}
    $stmt=$dbh->prepare('INSERT INTO GESTIONCRENEAUX  (idcreneau,idstaff) VALUES (?,?)');
    $stmt->bindParam(1,$id);
    $stmt->bindParam(2,$idstaff);
    $stmt2=$dbh->prepare('DELETE FROM GESTIONCRENEAUX WHERE idcreneau=? AND idstaff=?');
    $stmt2->bindParam(1,$id);
    $stmt2->bindParam(2,$idstaff);
    $stmt3=$dbh->prepare('SELECT nbstaff FROM CRENEAUX WHERE id=?');
    $stmt3->bindParam(1,$id);
    $stmt4=$dbh->prepare('UPDATE CRENEAUX SET nbstaff=? WHERE id=?');
    $stmt4->bindParam(1,$nbstaff);
    $stmt4->bindParam(2,$id);
    $creneaux_vides=[];
    foreach ($les_creneaux as $un_creneau) {
        $id=$un_creneau['id'];
        if (isset($_POST['c'.$id])) {
            if (!in_array($id,$les_creneaux_du_staffeur)) {
                $stmt3->execute();
                $nbstaff=-1;
                while ($row=$stmt3->fetch()) {
                    $nbstaff=intval($row['nbstaff']);
                }
                if ($nbstaff==-1) {echo "erreur dans la base créneaux"; die();}
                $nbstaff++;
                $stmt4->execute();
                $stmt->execute();
            }
        } elseif (in_array($id,$les_creneaux_du_staffeur)) {
            $stmt3->execute();
            $nbstaff=-1;
            while ($row=$stmt3->fetch()) {
                $nbstaff=intval($row['nbstaff']);
            }
            if ($nbstaff==-1) {echo "erreur dans la base créneaux"; die();}
            $nbstaff--;
            $stmt4->execute();
            $stmt2->execute();
            if ($nbstaff==0) {
                array_push($creneaux_vides,$id);
            }
        }
    }
    if (!$creneaux_vides==[]) {
        envoie_mail_creneaux_vides($creneaux_vides);
    }
}

function pas_un_staffeur($staffeur) {
    global $dbh,$terrainpossible,$couleurpossible;
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