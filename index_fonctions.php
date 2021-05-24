<?php

error_reporting(E_ALL); // pour activer les erreurs
ini_set("display_errors", 1);  // à commenter à la fin bien sûr
//penser à mettre la base de donénes en utf8 pour être sûr?

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
    $mail->Subject="Demande d'inscription";
    $msg="Bonjour ".$nom.",\n\n"."Votre demande d'inscription a bien été prise en compte.\n"."Vous recevrez un e-mail dès que les créneaux seront finalisés.\n\n"."Voici la liste des créneaux demandés :\n";
    foreach ($creneau_demandes as $uncreneau) {
        $msg.=jolie_date($uncreneau[0]).", ".$uncreneau[1]." en ".$uncreneau[2]."\n";
    }
    $msg.="\n-- \nL'équipe SSA";
    $mail->Body=$msg;
    if(!$mail->send()) {
        echo 'Mailer Error: ' . $mail->ErrorInfo;
        $sent = false;
    }
    //echo "<BR> Le mail qui sera envoyé : <BR><TEXTAREA style='width: 80%;heigth : 50px;'>".$msg."</TEXTAREA>";
    $mail->SmtpClose();
    unset($mail);
    return $sent;
}

function test_nom($nom,$prenom,$stmt) { //$stmt requete "nom,prenom" pour le terrain à tester
    // renvoie vraie si les noms sont identiques
    while ($row=$stmt->fetch()) {
        if ($nom==$row['nom'] && $prenom==$row['prenom']) {
            return true;
        }
    }
    return false;
}

function valide_formulaire () { // non utilisé : renvoie true/false si c'est réussi/non réussi
    global $dbh, $captcha_secretKey;

    if (isset($_POST['nom']) && isset($_POST['prenom']) && isset($_POST['mail']) && isset($_POST['telephone']) && isset($_POST['commentaire'])) {
        if ($_POST['nom']=="" ||$_POST['prenom']=="" ||$_POST['mail']=="" ||$_POST['telephone']=="") {
            echo "Formulaire incomplet";
            return false;
        }
        if (!isset($_POST['consignesecurite'])) {
            echo "Il faut valider les consignes de sécurité pour pouvoir demander un créneau. ";
            return false;
        }
        if (!isset($_POST['consignergpd'])) {
            echo "Il faut autoriser Sand System à sauvegarder les données personnelles pour pouvoir demander un créneau. ";
            return false;
        }
        if (!isset($_POST['adherent'])) {
            echo "Il faut indiquer si tu es adhérent SandSystem ou non. ";
            return false;
        }
        if (!isset($_POST['niveau'])) {
            echo "Il faut indiquer un niveau de jeu. ";
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
            $res=$dbh->query('SELECT MAX(id) FROM RESULTAT');
            $idresultat=1;
            while ($row=$res->fetch()) {
                $idresultat=1+intval($row[0]);
            }
            $stmt = $dbh->prepare("INSERT INTO DEMANDES (id,nom,prenom,mail,telephone,niveau,adherent,commentaire,idcreneau,typeterrain,prio) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
            $stmt->bindParam(1, $iddemande);
            $stmt->bindParam(2, $nom);
            $stmt->bindParam(3, $prenom);
            $stmt->bindParam(4, $mail);
            $stmt->bindParam(5, $telephone);
            $stmt->bindParam(6, $niveau);
            $stmt->bindParam(7, $adherent);
            $stmt->bindParam(8, $commentaire);
            $stmt->bindParam(9, $idcreneau);
            $stmt->bindParam(10, $typeterrain);
            $stmt->bindParam(11, $prio);
            $stmt1 =$dbh->prepare("SELECT nom,prenom FROM RESULTAT JOIN CRENEAUX ON idcreneau=CRENEAUX.id WHERE (etat='valide' OR etat='attente') AND idcreneau=? AND ((terrain='T1' AND T1=?) OR (terrain='T2' AND T2=?) OR (terrain='T3' AND T3=?) OR (terrain='T4' AND T4=?))");
            $stmt1->bindParam(1,$idcreneau);
            $stmt1->bindParam(2,$typeterrain);
            $stmt1->bindParam(3,$typeterrain);
            $stmt1->bindParam(4,$typeterrain);
            $stmt1->bindParam(5,$typeterrain);
            $stmt2 = $dbh->prepare("INSERT INTO RESULTAT (id,nom,prenom,mail,telephone,niveau,adherent,commentaire,idcreneau,terrain,etat,prio) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
            $stmt2->bindParam(1, $idresultat);
            $stmt2->bindParam(2, $nom);
            $stmt2->bindParam(3, $prenom);
            $stmt2->bindParam(4, $mail);
            $stmt2->bindParam(5, $telephone);
            $stmt2->bindParam(6, $niveau);
            $stmt2->bindParam(7, $adherent);
            $stmt2->bindParam(8, $commentaire);
            $stmt2->bindParam(9, $idcreneau);
            $stmt2->bindParam(10, $terrain);
            $stmt2->bindParam(11,$etat);
            $etat="attente";
            $stmt2->bindParam(12, $prio);
            $stmt3=$dbh->prepare("UPDATE CRENEAUX SET feminin=?,masculin=?,mixte=?,A1=?,A2=?,A3=?,A4=? WHERE id=?");
            $stmt3->bindParam(1, $feminin);
            $stmt3->bindParam(2, $masculin);
            $stmt3->bindParam(3, $mixte);
            $stmt3->bindParam(4, $a['1']);
            $stmt3->bindParam(5, $a['2']);
            $stmt3->bindParam(6, $a['3']);
            $stmt3->bindParam(7, $a['4']);
            $stmt3->bindParam(8, $idcreneau);
            $nom=secu_bdd($_POST['nom']);
            $prenom=secu_bdd($_POST['prenom']);
            $mail=secu_bdd($_POST['mail']);
            $telephone=secu_bdd($_POST['telephone']);
            $niveau=secu_bdd($_POST['niveau']);
            if (!in_array($niveau,["debutant","intermediaire","confirme","expert"])) {
                echo "Il faut indiquer un niveau de jeu. ";
                return false;
            }
            $adherent=secu_bdd($_POST['adherent']);
            if (!in_array($adherent,["oui","non"])) {
                echo "Il faut indiquer si tu êtes adhérent SandSystem ou non. ";
                return false;
            }
            $commentaire=secu_bdd($_POST['commentaire']);
            $les_creneaux=lire_les_creneaux();
            $creneau_demandes=[];
            foreach ($les_creneaux as $uncreneau) {
                $idcreneau=$uncreneau['id'];
                $a['1']=$uncreneau['A1'];
                $a['2']=$uncreneau['A2'];
                $a['3']=$uncreneau['A3'];
                $a['4']=$uncreneau['A4'];
                foreach (['feminin','masculin','mixte'] as $typeterrain) {
                    if (isset($_POST['c'.$idcreneau.$typeterrain])) {
                        $typeterrain=$typeterrain;
                        $prio="0";
                        $affectation_reussie=false;
                        $stmt1->execute();
                        if (test_nom($nom,$prenom,$stmt1)) {
                            echo "Tu es déjà inscrit pour ".secu_ecran(jolie_date($uncreneau['date'])).' '.secu_ecran($uncreneau['heure'])." en ".$typeterrain."\n";
                        } else {
                            foreach (['1','2','3','4'] as $n) {
                                $t='T'.$n;
                                if ($uncreneau[$t]==$typeterrain && !$affectation_reussie &&  ($uncreneau['V'.$n]+$uncreneau['A'.$n]<$uncreneau['VMAX'.$n]+$uncreneau['AMAX'.$n])) {
                                    $terrain=$t;
                                    $stmt2->execute();
                                    $idresultat++;
                                    $affectation_reussie=true;
                                    $uncreneau[$typeterrain]-=1;
                                    $feminin=$uncreneau['feminin'];
                                    $masculin=$uncreneau['masculin'];
                                    $mixte=$uncreneau['mixte'];
                                    $a[$n]+=1;
                                    $stmt3->execute();
                                }
                            }
                            if ($affectation_reussie) {
                                array_push($creneau_demandes,[$uncreneau['date'],$uncreneau['heure'],$typeterrain]);
                                $stmt->execute();
                                $iddemande++;
                            } else {
                                echo "Désolé, la dernière place pour ".secu_ecran(jolie_date($uncreneau['date'])).' '.secu_ecran($uncreneau['heure'])." en ".$typeterrain." vient d'être prise\n";
                            }
                        }
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
