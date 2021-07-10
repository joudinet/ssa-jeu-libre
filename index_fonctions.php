<?php

require "general_fonctions.php";
use PHPMailer\PHPMailer\PHPMailer; //pour le mail
use PHPMailer\PHPMailer\SMTP;
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

function envoie_mail_inscription($nom,$target,$creneau_demandes) {
    global $mail_from, $mail_fromName;
    $sent = true;

    $mail = new PHPmailer();
    $mail->CharSet = PHPMailer::CHARSET_UTF8;
    $mail->setFrom($mail_from, $mail_fromName);
    $mail->AddAddress($target);
    $mail->Subject="Inscription au jeu libre en cours";
    $msg= <<<EOD
Bonjour $nom,

Ta demande de participation au jeu libre a bien été prise en compte pour les créneaux suivants :

EOD;
    foreach ($creneau_demandes as $uncreneau) {
        $msg.=jolie_date($uncreneau[0]).", ".$uncreneau[1]."\n";
    }
    $msg.="\n";
    $msg.="Ces créneaux sont des créneaux en heures creuses : pense à nous indiquer si finalement tu ne peux pas venir !\n";
    $msg.="Cela permet aux autres adhérents de vérifier qu'il reste assez de personnes sur le créneau avant de se déplacer.\n\n";
    $msg.=<<<EOD
À bientôt sur les terrains, 😊
-- 
L'équipe SSA
EOD;
 // ' to fix highlighting : je décale d'une ligne ce commentaire : ça fait bugguer l'éditeur de mon fournisseur sinon
    $mail->Body=$msg;
    if(!$mail->send()) {
        echo 'Mailer Error: ' . $mail->ErrorInfo;
        $sent = false;
    }
    $mail->SmtpClose();
    unset($mail);
    return $sent;
}

// normalize a string
function normalize_str($str) {
    if (!class_exists("Normalizer", $autoload = false))
        return strtolower($str);

    // Normalizing a string with FROM_D splits the diacritics out from
    // the base characters, then eliminate them with preg_replace.
    return strtolower(
        preg_replace('/[\x{0300}-\x{036f}]/u', "",
                     Normalizer::normalize($str, Normalizer::FORM_D)));
}

// Return true if $nom and $prenom are inside the result of stmt request.
function test_nom($nom,$prenom,$stmt) {
    while ($row=$stmt->fetch()) {
        if (normalize_str($nom) == normalize_str($row['nom']) and
            normalize_str($prenom) == normalize_str($row['prenom'])) {
            return true;
        }
    }
    return false;
}

function lire_affluence() { // renvoie les créneaux classés par ordre chronologique
    global $dbh,$mysql_dbname;
    try {
        $date=date('Y-m-d');
        $stmt=$dbh->prepare('SELECT idcreneau,count(*) AS nb FROM CRENEAUX JOIN DEMANDES ON idcreneau=CRENEAUX.id WHERE date>=? GROUP BY idcreneau ORDER BY date,heure'); 
        $stmt->bindParam(1,$date);
        $stmt->execute();
        $tab_res=[];
        while ($row=$stmt->fetch()) {
            $tab_res[$row['idcreneau']]=$row['nb'];
        }
        return $tab_res;
    } catch (Exception $e) {
        print "Erreur dans la base de données des créneaux";
        die();
    }
}

function valide_formulaire () { // non utilisé : renvoie true/false si c'est réussi/non réussi
    global $dbh, $captcha_secretKey;

    if (isset($_POST['nom']) && isset($_POST['prenom']) && isset($_POST['mail'])) { 
        if ($_POST['nom']=="" ||$_POST['prenom']=="" ||$_POST['mail']=="") { 
            echo "Formulaire incomplet";
            return false;
        }
        if (!isset($_POST['consignergpd'])) {
            echo "Il faut autoriser Sand System à sauvegarder les données personnelles pour pouvoir s'inscrire en heures creuses. ";
            return false;
        }
        $captcha='';
        if (isset($_POST['g-recaptcha-response'])) {
            $captcha=$_POST['g-recaptcha-response'];
        }
        if (!$captcha) {
            echo "Il faut cocher le captcha. ";
            return false;
        }
        // verify captcha
        $ip = $_SERVER['REMOTE_ADDR'];
        $url = 'https://www.google.com/recaptcha/api/siteverify?secret=' . urlencode($captcha_secretKey) . '&response=' . urlencode($captcha);
        $response = file_get_contents($url);
        $responseKeys = json_decode($response, true);
        if(!$responseKeys["success"]) {
            echo "Mauvais captcha.";
            return false;
        }
        try {
            $res=$dbh->query('SELECT MAX(id) FROM DEMANDES');
            $iddemande=1;
            while ($row=$res->fetch()) {
                $iddemande=1+intval($row[0]);
            }
            $stmt = $dbh->prepare("INSERT INTO DEMANDES (id,nom,prenom,mail,idcreneau) VALUES (?,?,?,?,?)");
            $stmt->bindParam(1, $iddemande);
            $stmt->bindParam(2, $nom);
            $stmt->bindParam(3, $prenom);
            $stmt->bindParam(4, $mail);
            $stmt->bindParam(5, $idcreneau);
            $stmt1 =$dbh->prepare("SELECT nom,prenom FROM demandes  WHERE idcreneau=?");
            $stmt1->bindParam(1,$idcreneau);
            $stmt3=$dbh->prepare("UPDATE CRENEAUX SET nbdemandes=? WHERE id=?");
            $stmt3->bindParam(1,$nbdemandes);
            $stmt3->bindParam(2, $idcreneau);
            $nom=secu_bdd($_POST['nom']);
            $prenom=secu_bdd($_POST['prenom']);
            $mail=secu_bdd($_POST['mail']);
            $les_creneaux=lire_les_creneaux();
            $creneau_demandes=[];
            foreach ($les_creneaux as $uncreneau) {
                $idcreneau=$uncreneau['id'];
                if (isset($_POST['c'.$idcreneau])) {
                    $nbdemandes=$uncreneau['nbdemandes'];
                    $stmt1->execute();
                    if (test_nom($nom,$prenom,$stmt1)) {
                        echo "Tu es déjà inscrit pour ".secu_ecran(jolie_date($uncreneau['date'])).' '.secu_ecran($uncreneau['heure'])."\n";
                    } else {
                        $nbdemandes++;
                        $stmt3->execute();
                        array_push($creneau_demandes,[$uncreneau['date'],$uncreneau['heure']]);
                        $stmt->execute();
                        $iddemande++;                            
                    }
                }
            }
            if ($creneau_demandes==[]) {
                return false;
            } else if (envoie_mail_inscription($prenom,$mail,$creneau_demandes))  {return true; }
            echo "Impossible d'envoyer un mail de confirmation";
            return false;
        } catch (Exception $e) {
        echo "Erreur dans le formulaire";
        return false;
        }
    }
    echo "Formulaire incomplet. ";
    return false;
}

?>
