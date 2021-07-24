<?php

require "../general_fonctions.php";
require('fpdf.php'); // pour le PDF
use PHPMailer\PHPMailer\PHPMailer; //pour le mail
use PHPMailer\PHPMailer\SMTP;
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
require '../PHPMailer/src/Exception.php';

function ajout_staff() {
    global $dbh, $mail_from, $mail_fromName;
    try {
        $nom=secu_bdd($_POST['nom']);
        $mailstaff=secu_bdd($_POST['mail']);
        $telephone=secu_bdd($_POST['telephone']);
        $password="a faire"; //pour une future gestion autre que htpasswd
        $stmt=$dbh->prepare('SELECT * FROM STAFF WHERE nom=?');
        $stmt->bindParam(1,$nom);
        $stmt->execute();
        while ($row=$stmt->fetch()) {
            echo "<BR> <div style='color : red;'>Staffeur déjà présent(e) dans la base de donnée : il faut modifier l'entrée et non la créer</div>";
            return;
        }
        $stmt=$dbh->prepare('SELECT MAX(id) as id FROM STAFF');
        $stmt->execute();
        $nouvel_id=1;
        while ($row=$stmt->fetch()) {
            $nouvel_id=intval($row['id'])+1;
        }
        $stmt=$dbh->prepare('INSERT INTO STAFF (id,nom,mail,telephone,password) VALUES (?,?,?,?,?)');
        $stmt->bindParam(1, $nouvel_id);
        $stmt->bindParam(2, $nom);
        $stmt->bindParam(3, $mailstaff);
        $stmt->bindParam(4, $telephone);
        $stmt->bindParam(5, $password);
        $stmt->execute();
        $lines = file('../staff/.htpasswd');
        $contenu="";
        foreach ($lines as $line_num => $line) {
	        $contenu=$contenu.$line;
        }
        $new_staff="\n".$nom.":".@password_hash("staff", PASSWORD_DEFAULT);
        $contenu=$contenu.$new_staff;
        $h = fopen('../staff/.htpasswd', "w");
        fwrite($h, $contenu);
        fclose($h);
        $mail = new PHPmailer();
        $mail->CharSet = 'UTF-8';
        $mail->setFrom($mail_from, $mail_fromName);
        $mail->ContentType = 'text/plain';
        $mail->Subject='Bienvenue dans le staff jeu-libre';
        $msg="Bonjour ".$nom."\n\nTu es maintenant dans la liste des personnes aidant à gérer les créneaux jeu-libre de Sand System et nous t'en remercions !\n ";
        $msg.="\nPour t'inscrire sur les créneaux de gestion, il suffit de suivre le lien suivant : https://sandsystem.com/jeu-libre/staff\n\n";
        $msg.="Ton login pour cette partie du site est : ".$nom."     et ton mot de passe est : staff\n\n";
        $msg.="--\nL'équipe SSA";
        $mail->Body=$msg;
        $mail->AddAddress($mailstaff);
        echo "<BR> Le mail qui sera envoyé : <BR><TEXTAREA style='width: 80%;heigth : 200px;text-align : left;'>".$msg."</TEXTAREA>";
        //if (!$mail->send()) {
        //    echo 'Mailer error: ' . $mail->ErrorInfo;
        //}
        $mail->SmtpClose();
        unset($mail); 
    } catch (Exception $e) { echo "erreur dans la création du staffeur";die();}
}

function creationCreneau() {
    global $dbh;
    try {
        $res=$dbh->query('SELECT MAX(id) FROM CRENEAUX');
        $id=1;
        while ($row=$res->fetch()) {
            $id=1+intval($row[0]);
        }
        $stmt = $dbh->prepare("INSERT INTO CRENEAUX VALUES (?,?,?,?,?,?,?)");
        $stmt->bindParam(1, $id);
        $stmt->bindParam(2, $date);
        $stmt->bindParam(3, $heure);
        $stmt->bindParam(4, $intitule);
        $stmt->bindParam(5, $reservation);
        $stmt->bindParam(6, $nbstaff);
        $stmt->bindParam(7, $nbdemandes);
        $date=secu_bdd($_POST['date']);
        if ($date<date('Y-m-d')) { echo "on ne créé pas de créneaux dans le passé ;)"; return ; }
        $heure=secu_bdd($_POST['heure']);
        $intitule=secu_bdd($_POST['intitule']);
        if (isset($_POST['reservation']) && $_POST['reservation']=="oui") {
            $reservation="oui";
        } else {
            $reservation="non";
        }
        $nbstaff=0;
        $nbdemandes=0;
        $stmt->execute();
    } catch (Exception $e) {
        echo "Erreur dans la saisie des éléments du créneaux";
    }
}

function lecture_staff() {
    global $dbh;
    try {
        $tab_res=[];
        $stmt=$dbh->query('SELECT * FROM STAFF ORDER BY nom');
        while ($row=$stmt->fetch()) {
            $tab_res[$row['id']]=['nom'=>$row['nom'],'mail'=>$row['mail'],'telephone'=>$row['telephone']];
        }
        return $tab_res;
    } catch (Exception $e) {
        echo "Erreur dans la base de données du staff";
    }
}

function lire_joueurs_creneau($id) {  //demandes des joueurs pour un créneau
    global $dbh,$mysql_dbname;
    try {
        $stmt=$dbh->prepare('SELECT * FROM  DEMANDES  WHERE idcreneau=?'); 
        $stmt->bindParam(1,$id);
        $stmt->execute();
        return $stmt;
    } catch (Exception $e) {
        print "Erreur dans la base de données des créneaux";
        die();
    }
}

function maj_annonce() {
    global $dbh;
    if (isset($_POST["annonce"])) {
        try {
            $txt=secu_bdd($_POST["annonce"]);
            $stmt=$dbh->query("SELECT * FROM DIVERS WHERE intitule='annonce'");
            while ($row=$stmt->fetch()) {
                $stmt=$dbh->prepare("UPDATE DIVERS SET contenu=? WHERE intitule='annonce'");
                $stmt->bindParam(1,$txt);
                $stmt->execute();
                return  ;              
            }
            $stmt=$dbh->prepare("INSERT INTO ANNONCE (intitule,contenu) VALUES ('annonce',?)");
            $stmt->bindParam(1,$txt);
            $stmt->execute();
        } catch (Exception $e) {
            echo "Erreur dans la mise à jour de l'annonce";
        }
    }
}

function modif_staff() {
    global $dbh;
    try {
        $id=intval($_POST['amodifier']);
        $nom=secu_bdd($_POST['nom']);
        $mailstaff=secu_bdd($_POST['mail']);
        $telephone=secu_bdd($_POST['telephone']);
        $stmt=$dbh->prepare('UPDATE STAFF SET nom=?,mail=?,telephone=? WHERE id=?');
        $stmt->bindParam(1,$nom);
        $stmt->bindParam(2,$mailstaff);
        $stmt->bindParam(3,$telephone);
        $stmt->bindParam(4,$id);
        $stmt->execute();
    } catch (Exception $e) {
        echo "Erreur dans la modification du staff";
    }
}

function ModifieCreneau() {
    global $dbh,$les_creneaux;
    try {
        $id=intval($_POST['inputid']);
        if ($id<1) { die();}
        $heure=secu_bdd($_POST['heure']);
        $intitule=secu_bdd($_POST['intitule']);
        if (isset($_POST['reservation']) && $_POST['reservation']=="oui") {
            $reservation="oui";
        } else {
            $reservation="non";
        }
        if ($reservation=="non") {
            $stmt = $dbh->prepare("UPDATE CRENEAUX SET heure=?,intitule=?,reservation=?,nbdemandes=0 WHERE id=?");
            $stmt2=$dbh->prepare("DELETE FROM demandes WHERE idcreneau=?");
            $stmt2->bindParam(1,$id);
            $stmt2->execute();
        } else {
            $stmt = $dbh->prepare("UPDATE CRENEAUX SET heure=?,intitule=?,reservation=? WHERE id=?");
        }
        $stmt->bindParam(1, $heure);
        $stmt->bindParam(2, $intitule);
        $stmt->bindParam(3, $reservation);
        $stmt->bindParam(4, $id);            
        $stmt->execute();
        $les_creneaux=lire_les_creneaux();
    } catch (Exception $e) {
        echo "Erreur dans la modification du créneaux";
    }
}


function remiseazero() {
    global $dbh,$les_creneaux;
    try {
        $msg='CREATE TABLE CRENEAUX ( id INT PRIMARY KEY,date DATE, heure VARCHAR (100), intitule VARCHAR (100),';
        $msg.='reservation VARCHAR (15),nbstaff INT,nbdemandes INT)';
        $dbh->query('DROP TABLE IF EXISTS CRENEAUX');
        $dbh->query($msg);
        $msg='CREATE TABLE DEMANDES ( id INT PRIMARY KEY,nom VARCHAR (50), prenom VARCHAR (50),';
        $msg.='mail VARCHAR (200),idcreneau INT)';
        $dbh->query('DROP TABLE IF EXISTS DEMANDES');
        $dbh->query($msg);
        $msg='CREATE TABLE DIVERS ( intitule VARCHAR (100), contenu VARCHAR (5000) )';
        $dbh->query('DROP TABLE IF EXISTS DIVERS');
        $dbh->query($msg);
        $dbh->query('INSERT INTO DIVERS (intitule,contenu) VALUES ("annonce"," ")');
        $msg='CREATE TABLE GESTIONCRENEAUX ( idcreneau INT,idstaff INT )';
        $dbh->query('DROP TABLE IF EXISTS GESTIONCRENEAUX');
        $dbh->query($msg);
        $msg='CREATE TABLE STAFF ( id INT PRIMARY KEY,nom VARCHAR (50), ';
        $msg.='mail VARCHAR (200), telephone VARCHAR (50), password VARCHAR (100))';
        $dbh->query('DROP TABLE IF EXISTS STAFF');
        $dbh->query($msg);
        $h = fopen('../staff/.htpasswd', "w");
        fwrite($h, "");
        fclose($h);
  } catch (Exception $e) {
        echo "Erreur lors de la remise à zéro";
    }

}

function SupprimeCreneau() {
    global $dbh,$les_creneaux;
    try {
        $stmt = $dbh->prepare("DELETE FROM DEMANDES WHERE idcreneau=?");
        $stmt->bindParam(1, $id);
        $stmt2 = $dbh->prepare("DELETE FROM gestioncreneaux WHERE idcreneau=?");
        $stmt2->bindParam(1, $id);
        $stmt3 = $dbh->prepare("DELETE FROM CRENEAUX WHERE id=?");
        $stmt3->bindParam(1, $id);
        $id=intval($_POST['inputid']);
        if ($id<1) { die(); }
        $stmt->execute();
        $stmt2->execute();
        $stmt3->execute();
        $les_creneaux=lire_les_creneaux();
    } catch (Exception $e) {
        echo "Erreur dans la suppression du créneaux";
    }
}

function supprime_demande($id) {
    global $dbh,$les_creneaux;
    try {
        $stmt=$dbh->prepare("SELECT * FROM DEMANDES WHERE id=?");
        $stmt->bindParam(1, $id);
        $stmt->execute();
        $lademande=$stmt->fetch();
        $idcreneau=intval($lademande['idcreneau']);
        $stmt = $dbh->prepare("DELETE FROM DEMANDES WHERE id=?");
        $stmt->bindParam(1, $id);
        $stmt->execute();
        $stmt=$dbh->prepare("SELECT * FROM CRENEAUX WHERE id=?");
        $stmt->bindParam(1, $idcreneau);
        $stmt->execute();
        $row=$stmt->fetch();
        $nbdemandes=intval($row['nbdemandes'])-1;
        $stmt=$dbh->prepare("UPDATE CRENEAUX SET nbdemandes=? WHERE id=?");
        $stmt->bindParam(1, $nbdemandes);
        $stmt->bindParam(2, $idcreneau);
        $stmt->execute();        
    } catch (Exception $e) {
        echo "Erreur dans la suppression de la demande du joueur";
    }
}

function supprime_staff() {
    global $dbh;
    try {
        $id=intval($_POST['amodifier']);
            if ($id<1) { die(); }
        $stmt = $dbh->prepare("DELETE FROM STAFF WHERE id=?");
        $stmt->bindParam(1,$id);
        $stmt->execute();
        $stmt = $dbh->prepare("SELECT * FROM GESTIONCRENEAUX WHERE idstaff=?");
        $stmt->bindParam(1,$id);
        $stmt->execute();
        $stmt3=$dbh->prepare('SELECT nbstaff FROM CRENEAUX WHERE id=?');
        $stmt3->bindParam(1,$idcreneau);
        $stmt4=$dbh->prepare('UPDATE CRENEAUX SET nbstaff=? WHERE id=?');
        $stmt4->bindParam(1,$nbstaff);
        $stmt4->bindParam(2,$idcreneau);
        $creneauxvides=[];
        while ($row=$stmt->fetch()) {
            $idcreneau=intval($row['idcreneau']);
            $stmt3->execute();
            $row3=$stmt3->fetch();
            $nbstaff=intval($row3['nbstaff'])-1;
            $stmt4->execute();
            if ($nbstaff==0) {
                array_push($creneauxvides,$idcreneau);
            }
        }
        if ($creneauxvides!=[]) {
            echo "<BR><BR> Attention, les créneaux suivants n'ont plus de staff après cette suppression :";
            foreach ($creneauxvides as $uncreneau) {
                $lecreneau=trouveCreneau($uncreneau);
                echo "<BR>".jolie_date($lecreneau['date']).' '.$lecreneau['heure'];;
            }
        }
        $stmt = $dbh->prepare("DELETE FROM GESTIONCRENEAUX WHERE idstaff=?");
        $stmt->bindParam(1,$id);
        $stmt->execute();
        $lines = file('../staff/.htpasswd');
        $contenu="";
        foreach ($lines as $line) {
            if (!(strstr($line,":",true)==$_POST['nom'])) {
	            $contenu=$contenu.$line;
            }
        }
        $h = fopen('../staff/.htpasswd', "w");
        fwrite($h, $contenu);
        fclose($h); 
    } catch (Exception $e) {
        echo "Erreur dans la suppression du staffeur";
    }      
}

function supprime_tous_creneaux() {
    global $dbh;
    try {
        $stmt = $dbh->query("DELETE FROM CRENEAUX");
        $stmt = $dbh->query("DELETE FROM DEMANDES");
        $stmt = $dbh->query("DELETE FROM GESTIONCRENEAUX");
    } catch (Exception $e) {
        echo "Erreur dans la suppression de tous les créneaux";
    }      
}

function supprime_tout_staff() {
    global $dbh;
    try {
        $stmt = $dbh->query("DELETE FROM STAFF");
        $stmt = $dbh->query("DELETE FROM GESTIONCRENEAUX");
        $h = fopen('../staff/.htpasswd', "w");
        fwrite($h, "");
        fclose($h);       
   } catch (Exception $e) {
        echo "Erreur dans la suppression de tous les membres du staff";
    }      
   
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

/// anciennes fonctions à partir d'ici
/// à enlever quand tout est au point
///


function annuleterrain($idcreneau) {
    global $dbh, $mail_from, $mail_fromName;
    $terrain_nouveau=$_POST['texte'];
    if (!in_array($terrain_nouveau,['T1','T2','T3','T4'])) {
        echo "erreur lors de la demande d'annulation' de terrain";
        return;
    }
    try { //echo "demande d'annulation pour le terrain ".$terrain_nouveau." du créneau ".$idcreneau." ";
        $stmt = $dbh->prepare("SELECT * FROM RESULTAT WHERE idcreneau=? AND terrain=? AND (etat='valide' OR etat='attente')");
        $stmt->bindParam(1,$idcreneau);
        $stmt->bindParam(2,$terrain_nouveau);
        $stmt->execute();
        $stmt2= $dbh->prepare("UPDATE RESULTAT SET etat=? WHERE id=?");
        $stmt2->bindParam(1,$etat);
        $stmt2->bindParam(2,$id);
        $etat="supprime";
        $liste_mail=[];
        while ($row=$stmt->fetch()) {
            $id=$row['id'];
            $stmt2->execute();
            $mail=$row['mail'];
            if (!(in_array($mail,$liste_mail) || $mail=="")) {
                array_push($liste_mail,secu_ecran($mail));
            }
        }
        if (!$liste_mail==[]) {
            $mail = new PHPmailer();
            $mail->CharSet = 'UTF-8';
            $mail->setFrom($mail_from, $mail_fromName);
            $mail->ContentType = 'text/plain';
            $mail->Subject='Annulation de créneau';
            $msg= <<<EOD
Hello les beacheurs,

Nous sommes désolés mais vous n'êtes pas assez nombreux pour le créneau de 
EOD;
            $lecreneau=trouveCreneau($idcreneau);
            $msg.=jolie_date($lecreneau['date']).' '.$lecreneau['heure'];
            $msg.=<<<EOD

Nous devons annuler ce créneau mais ce n'est que partie remise!
À bientôt sur les terrains,
-- 
L'équipe SSA
EOD;
            $mail->Body=$msg;
            $mail->AddAddress($mail_from);
            foreach ($liste_mail as $target) {
                $mail->AddBCC($target);
            }
            if (!$mail->send()) {
                echo 'Mailer error: ' . $mail->ErrorInfo;
            }
            $mail->SmtpClose();
            unset($mail); 
        }
    } catch (Exception $e) {
        echo "Erreur dans l'annulation de terrain";
    }
    
}

function creationpdf($les_creneaux_demandes) {
    // on ne met plus les gens en attente : commentaire
    global $dbh,$les_creneaux;
    $pdf = new PDF_MC_Table();
    $pdf->SetWidths(array(40,40,40,40));
    $pdf->AddPage();
    $pdf->SetFont('Arial','B',7);
    $pdf->SetTextColor(0);
    $stmt = $dbh->prepare("SELECT * FROM RESULTAT WHERE idcreneau=? AND terrain=? AND etat='valide'");
    $stmt->bindParam(1, $idcreneau);
    $stmt->bindParam(2, $terrain);
    //$stmt2 = $dbh->prepare("SELECT * FROM RESULTAT WHERE idcreneau=? AND terrain=? AND etat='attente'");
    //$stmt2->bindParam(1, $idcreneau);
    //$stmt2->bindParam(2, $terrain);
    foreach ($les_creneaux_demandes as $idcreneau) {
        $le_creneau=trouveCreneau($idcreneau);
        $pdf->Cell(160,7,pdf(jolie_date($le_creneau['date']))." ".pdf($le_creneau['heure']),1,0,'C');
        $pdf->Ln();
        $lignes=[];
        for ($i=0;$i<9;$i++) {array_push($lignes,["","","",""]);}
        $adh=[];
        for ($i=0;$i<9;$i++) {array_push($adh,[[false,0,0,0],[false,0,0,0],[false,0,0,0],[false,0,0,0]]);} //fond à remplir? +couleur


        $lignes[0]=["T1".pdft($le_creneau['T1']),"T2".pdft($le_creneau['T2']),"T3".pdft($le_creneau['T3']),"T4".pdft($le_creneau['T4'])];
        $adh[0]=[[true,hexdec(substr($le_creneau['C1'],1,2)),hexdec(substr($le_creneau['C1'],3,2)),hexdec(substr($le_creneau['C1'],5,2))],
        [true,hexdec(substr($le_creneau['C2'],1,2)),hexdec(substr($le_creneau['C2'],3,2)),hexdec(substr($le_creneau['C2'],5,2))],
        [true,hexdec(substr($le_creneau['C3'],1,2)),hexdec(substr($le_creneau['C3'],3,2)),hexdec(substr($le_creneau['C3'],5,2))],
        [true,hexdec(substr($le_creneau['C4'],1,2)),hexdec(substr($le_creneau['C4'],3,2)),hexdec(substr($le_creneau['C4'],5,2))]];
        /*$attente=[[],[],[],[]];
        $attente_adh=[[],[],[],[]];*/
        for ($i=0;$i<4;$i++) {
            $terrain=['T1','T2','T3','T4'][$i];
            $stmt->execute();
            $k=1;
            while ($row=$stmt->fetch()) {  // récupère un terrain d'un des créneaux
                $lignes[$k][$i]=pdf($row['prenom'])." ".pdf($row['nom']);
                if ($row['adherent']=="non") {
                    $adh[$k][$i]=[true,255,255,0];
                }
                $k++;
            }
            /*$stmt2->execute();
            $k=1;
            while ($row=$stmt2->fetch()) {  // récupère un terrain d'un des créneaux
                array_push($attente[$i],pdf($row['prenom'])." ".pdf($row['nom']));
                if ($row['adherent']=="non") {
                    array_push($attente_adh[$i],[true,255,255,0]);
                } else {
                    array_push($attente_adh[$i],[false,0,0,0]);
                }
                $k++;
            }*/
        }
        for ($i=0;$i<9;$i++) {
            $pdf->Row([$lignes[$i][0],$lignes[$i][1],$lignes[$i][2],$lignes[$i][3]],[$adh[$i][0],$adh[$i][1],$adh[$i][2],$adh[$i][3]]);
        }
        /*$nb_lignes_attente=max(count($attente[0]),count($attente[1]),count($attente[2]),count($attente[3]));
        for ($i=0;$i<$nb_lignes_attente;$i++) {
            $txt=["","","",""];
            $adh=[[false,0,0,0],[false,0,0,0],[false,0,0,0],[false,0,0,0]];
            if ($i<count($attente[0])) { $txt[0]=$attente[0][$i]; $adh[0]=$attente_adh[0][$i];}
            if ($i<count($attente[1])) { $txt[1]=$attente[1][$i];$adh[1]=$attente_adh[1][$i];}
            if ($i<count($attente[2])) { $txt[2]=$attente[2][$i];$adh[2]=$attente_adh[2][$i];}
            if ($i<count($attente[3])) { $txt[3]=$attente[3][$i];$adh[3]=$attente_adh[3][$i];}
            $pdf->Row($txt,$adh,false);
        }*/
        $pdf->Ln();
   }
    $pdf->Output("F","creneauxPDF.pdf");

}

function creationenvoi($les_creneaux_demandes) {
    // suppression des mails des gens en attente
    global $dbh, $les_creneaux, $mail_from, $mail_fromName;
    creationpdf($les_creneaux_demandes);
    $liste_mail=[];
    $stmt = $dbh->prepare("SELECT mail FROM RESULTAT WHERE idcreneau=? AND etat='valide'"); // suppression des gens en attente ici
    $stmt->bindParam(1, $idcreneau);
    foreach ($les_creneaux_demandes as $idcreneau) {
        $stmt->execute();
        while ($row=$stmt->fetch()) {
            $mail=$row['mail'];
            if (!(in_array($mail,$liste_mail) || $mail=="")) {
                array_push($liste_mail,secu_ecran($mail));
            }
        }
    }
    $mail = new PHPmailer();
    $mail->CharSet = 'UTF-8';
    $mail->setFrom($mail_from, $mail_fromName);
    $mail->ContentType = 'text/plain';
    $mail->Subject='Créneaux de jeu libre';
    $mail->Body= <<<EOD
Hello les beacheurs, 😎 

Vous trouverez en pièce-jointe le planning pour les jours à venir.

En cas d'annulation, merci de prévenir le plus tôt possible en répondant à cet e-mail.

Pour rappel, toute personne n'ayant pas annulé sa réservation au plus tard la veille du créneau (sauf cas extrême) se verra refuser l'accès au site pour la semaine suivante.

À bientôt sur les terrains, 😊 
-- 
L'équipe SSA
EOD;
    $mail->AddAttachment('creneauxPDF.pdf');
    $mail->AddAddress($mail_from);
    foreach ($liste_mail as $target) {
        $mail->AddBCC($target);
    }
    if (!$mail->send()) {
        echo 'Mailer error: ' . $mail->ErrorInfo;
    }
    $mail->SmtpClose();
    unset($mail);
}

function reinitialise_tout($id) {
    echo "à faire : ".$id;
}

function valide_tout($id) {
    echo "à faire : ".$id;
}

class PDF_MC_Table extends FPDF
{
var $widths;
var $aligns;

function SetWidths($w)
{
    //Tableau des largeurs de colonnes
    $this->widths=$w;
}

function SetAligns($a)
{
    //Tableau des alignements de colonnes
    $this->aligns=$a;
}

function Row($data,$fill,$bords=true)
{
    //Calcule la hauteur de la ligne
    $nb=0;
    for($i=0;$i<count($data);$i++)
        $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
    $h=4*$nb;
    //Effectue un saut de page si nécessaire
    $this->CheckPageBreak($h);
    //Dessine les cellules
    for($i=0;$i<count($data);$i++)
    {
        $w=$this->widths[$i];
        $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
        //Sauve la position courante
        $x=$this->GetX();
        $y=$this->GetY();
        //Dessine le cadre
        if ($fill[$i][0]==true) {
            $this->SetFillColor($fill[$i][1],$fill[$i][2],$fill[$i][3]);
            $this->Rect($x,$y,$w,$h,'F');
        }
        if ($bords) {
            $this->Rect($x,$y,$w,$h);
        }
        $this->MultiCell($w,4,$data[$i],0,'C');
        //Repositionne à droite
        $this->SetXY($x+$w,$y);
    }
    //Va à la ligne
    $this->Ln($h);
}

function CheckPageBreak($h)
{
    //Si la hauteur h provoque un débordement, saut de page manuel
    if($this->GetY()+$h>$this->PageBreakTrigger)
        $this->AddPage($this->CurOrientation);
}

function NbLines($w,$txt)
{
    //Calcule le nombre de lignes qu'occupe un MultiCell de largeur w
    $cw=&$this->CurrentFont['cw'];
    if($w==0)
        $w=$this->w-$this->rMargin-$this->x;
    $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
    $s=str_replace("\r",'',$txt);
    $nb=strlen($s);
    if($nb>0 and $s[$nb-1]=="\n")
        $nb--;
    $sep=-1;
    $i=0;
    $j=0;
    $l=0;
    $nl=1;
    while($i<$nb)
    {
        $c=$s[$i];
        if($c=="\n")
        {
            $i++;
            $sep=-1;
            $j=$i;
            $l=0;
            $nl++;
            continue;
        }
        if($c==' ')
            $sep=$i;
        $l+=$cw[$c];
        if($l>$wmax)
        {
            if($sep==-1)
            {
                if($i==$j)
                    $i++;
            }
            else
                $i=$sep+1;
            $sep=-1;
            $j=$i;
            $l=0;
            $nl++;
        }
        else
            $i++;
    }
    return $nl;
}
}

function envoie_mail($prenom,$target,$type_mail,$textecreneau) { // type_mail : complet, confirmation
    global  $mail_from, $mail_fromName;
    $mail = new PHPmailer();
    $mail->CharSet = 'UTF-8';
    $mail->setFrom($mail_from, $mail_fromName);
    $mail->AddAddress($target);
    $mail->ContentType = 'text/plain';
    $msg="Bonjour ".$prenom."!\n\n";
    if ($type_mail=="confirmation") {
        $mail->Subject='Confirmation de créneau';
        $msg.="Je te confirme que le créneau demandé ".$textecreneau." est bien validé.\n\n";
        $msg.="Pensez à vous inscrire plus tôt une prochaine fois, cela simplifie le travail pour nous. 😉 \n\n";
    }  else {
        $mail->Subject='Créneau complet';
        $msg.="Nous sommes désolés mais il n'y a plus de place sur le créneau demandé : ".$textecreneau."\n\n";
        $msg.="N'hésite pas à t'inscrire pour un créneau la semaine prochaine et nous ferons au mieux pour répondre à ta/tes demande(s)..\n\n";
        $msg.="A bientôt sur les terrains !\n\n";

    }
    $msg.="-- \n"."L'équipe SSA";
    $mail->Body=$msg;
    if (!$mail->Send()){ //Teste le return code de la fonction
        //echo 'Mailer Error: ' . $mail->ErrorInfo;
        $mail->SmtpClose();
        unset($mail);
        return false; 
    }
    //echo "<BR> Le mail qui sera envoyé : <BR><TEXTAREA style='width: 80%;heigth : 50px;text-align : left;'>".$msg."</TEXTAREA>";
    $mail->SmtpClose();
    unset($mail);
    return true;
}


function ajoute_personne($idcreneau) {
    global $dbh,$les_creneaux;
    $terrain=$_POST['texte'];
    $le_creneau=trouveCreneau($idcreneau);
    $typeterrain=$le_creneau[$terrain];
    if ($idcreneau<1 || !in_array($terrain,['T1','T2','T3','T4']) || !in_array($typeterrain,['mixte','masculin','feminin'])) { echo "erreur dans la saisie de la personne"; return ;}
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
        $stmt2->bindParam(12, $prio);
        $nom=secu_bdd($_POST['nomcache']);
        $prenom=secu_bdd($_POST['prenomcache']);
        $mail=secu_bdd($_POST['mailcache']);
        $telephone=secu_bdd($_POST['telephonecache']);
        $niveau=secu_bdd($_POST['niveaucache']);
        $adherent=secu_bdd($_POST['adherentcache']);
        $commentaire="";
        $prio="0";
        if (!in_array($typeterrain,['feminin','mixte','masculin'])) { throw('erreur'); }
        $etat='attente';
        $stmt->execute();
        $stmt2->execute();
        $stmt=$dbh->prepare("SELECT * FROM CRENEAUX WHERE id=?");
        $stmt->bindParam(1,$idcreneau);
        $stmt->execute();
        $row=$stmt->fetch();
        $placelibres=$row[$typeterrain]-1;
        $personnesattente=$row["A".substr($terrain,1,1)]+1;
        $stmt=$dbh->prepare("UPDATE CRENEAUX SET ".$typeterrain."=?, A".substr($terrain,1,1)."=? WHERE id=?");
        $stmt->bindParam(1,$placelibres);
        $stmt->bindParam(2,$personnesattente);
        $stmt->bindParam(3,$idcreneau);
        $stmt->execute();
    } catch (PDOException $e) {
        echo "Erreur dans le formulaire";
    }
}

function change_adherent($id) {
    global $dbh;
    try {
        $stmt = $dbh->prepare("SELECT * FROM RESULTAT WHERE id=?");
        $stmt->bindParam(1,$id);
        $stmt->execute();
        $personne=$stmt->fetch();
        if ($personne['adherent']=='non') { $adherent="oui"; } else { $adherent="non";}
        $stmt = $dbh->prepare("UPDATE RESULTAT SET adherent=? WHERE id=?");
        $stmt->bindParam(1,$adherent);
        $stmt->bindParam(2,$id);
        $stmt->execute();
    } catch (Exception $e) {
        echo "Erreur dans le changement de statut adhérent";
    }
}

function change_etat($id) {
    global $dbh;
    $etat=$_POST['texte'];
    if (!in_array($etat,['valide','attente','supprime','sup','annule'])) {
        echo "erreur lors de la demande de changement d'état";
        return;
    }
    try {
        $stmt = $dbh->prepare("SELECT * FROM RESULTAT WHERE id=?");
        $stmt->bindParam(1,$id);
        $stmt->execute();
        $personne=$stmt->fetch();
        $etat_actuel=$personne['etat'];
        $terrain=$personne['terrain'];
        if (!in_array($etat_actuel,['valide','attente','supprime','annule'])) { throw('erreur'); }
        if (!in_array($terrain,['T1','T2','T3','T4'])) { throw('erreur'); }
        $numero=substr($terrain,1,1);
        $stmt = $dbh->prepare("SELECT * FROM CRENEAUX WHERE id=?");
        $stmt->bindParam(1,$personne['idcreneau']);
        $stmt->execute();
        $lecreneau=$stmt->fetch();
        $typeterrain=$lecreneau[$terrain];
        if (!in_array($typeterrain,['feminin','mixte','masculin'])) { throw('erreur'); }
        $libres=$lecreneau[$typeterrain];
        $personnevalide=$lecreneau['V'.$numero];
        $personneattente=$lecreneau['A'.$numero];
        $stmt= $dbh->prepare("UPDATE RESULTAT SET etat=? WHERE id=?");
        $stmt->bindParam(1,$etat);
        $stmt->bindParam(2,$id);
        $stmt2=$dbh->prepare("UPDATE CRENEAUX SET ".$typeterrain."=?, V".$numero."=?,A".$numero."=? WHERE id=?");
        $stmt2->bindParam(1,$libres);
        $stmt2->bindParam(2,$personnevalide);
        $stmt2->bindParam(3,$personneattente);
        $stmt2->bindParam(4,$personne['idcreneau']);
        if ($etat=='sup') {
            if ($etat_actuel=='valide') {$personnevalide--; $libres++;}
            else if ($etat_actuel=='attente') {$personneattente--; $libres++;}
            else if (!$etat_actuel=='supprime') {throw "erreur inattendue";}
            $stmt->execute();$stmt2->execute();
        } else if ($etat=='supprime') {
            $libres++;
            if ($etat_actuel=='valide') {$personnevalide--;}
            else if ($etat_actuel=='attente') {$personneattente--;}
            else {throw "erreur inattendue";}
            $stmt->execute();$stmt2->execute();
        } else if ($etat=='valide' && $etat_actuel=='attente') {
            if ($personnevalide<$lecreneau['VMAX'.$numero]) {
                $personnevalide++;
                $personneattente--;
                $stmt->execute(); $stmt2->execute();
            } else {
                //echo "\n Le créneau ne peut plus accepter de personnes validées.\n";
            }
        } else if ($etat=='valide' && $etat_actuel=='supprime') {
            if ($personnevalide<$lecreneau['VMAX'.$numero]) {
                $personnevalide++;
                $libres--;
                $stmt->execute();$stmt2->execute();
            } else {
                $personneattente++;
                $libres--;
                $etat='attente';
                $stmt->execute();$stmt2->execute();
            }
        } else if ($etat=='attente' && $etat_actuel=='valide') {
                $personnevalide--;
                $personneattente++;
                $stmt->execute();$stmt2->execute();
        } else { echo "combinaison imprévue de changement d'état";}
    } catch (Exception $e) {
        echo "Erreur dans le changement d'état";
    }
}

function change_terrain($id) {
    global $dbh;
    $terrain_nouveau=$_POST['texte'];
    if (!in_array($terrain_nouveau,['T1','T2','T3','T4'])) {
        echo "erreur lors de la demande de changement de terrain";
        return;
    }
    try {
        $stmt = $dbh->prepare("SELECT * FROM RESULTAT WHERE id=?");
        $stmt->bindParam(1,$id);
        $stmt->execute();
        $personne=$stmt->fetch();
        $terrain_actuel=$personne['terrain'];
        if (!in_array($terrain_actuel,['T1','T2','T3','T4'])) { throw('erreur'); }
        $etat=$personne['etat'];
        if (!in_array($etat,['valide','attente','supprime','annule'])) { throw('erreur'); }
        $numero_actuel=substr($terrain_actuel,1,1);
        $numero_nouveau=substr($terrain_nouveau,1,1);
        $stmt = $dbh->prepare("SELECT * FROM CRENEAUX WHERE id=?");
        $stmt->bindParam(1,$personne['idcreneau']);
        $stmt->execute();
        $lecreneau=$stmt->fetch();
        $typeterrain_nouveau=$lecreneau[$terrain_nouveau];
        $typeterrain_actuel=$lecreneau[$terrain_actuel];
        if (!in_array($typeterrain_nouveau,['feminin','mixte','masculin'])) { throw('erreur'); }
        if (!in_array($typeterrain_actuel,['feminin','mixte','masculin'])) { throw('erreur'); }
        $libre_nouveau=$lecreneau[$typeterrain_nouveau];
        $libre_actuel=$lecreneau[$typeterrain_actuel];
        $personnevalide_nouveau=$lecreneau['V'.$numero_nouveau];
        $personneattente_nouveau=$lecreneau['A'.$numero_nouveau];
        $personnevalide_actuel=$lecreneau['V'.$numero_actuel];
        $personneattente_actuel=$lecreneau['A'.$numero_actuel];
        $stmt= $dbh->prepare("UPDATE RESULTAT SET terrain=?,etat=? WHERE id=?");
        $stmt->bindParam(1,$terrain_nouveau);
        $stmt->bindParam(2,$etat);
        $stmt->bindParam(3,$id);
        if ($etat=='valide') {$personnevalide_actuel--;} else {$personneattente_actuel--;}
        if ($personnevalide_nouveau>=$lecreneau['VMAX'.$numero_nouveau]) {$personneattente_nouveau++; $etat='attente';} else {$personnevalide_nouveau++; $etat='valide';}
        $stmt->execute();
        if ($typeterrain_nouveau==$typeterrain_actuel) {
            $stmt2=$dbh->prepare("UPDATE CRENEAUX SET V".$numero_nouveau."=?,A".$numero_nouveau."=?,V".$numero_actuel."=?,A".$numero_actuel."=? WHERE id=?");
            $stmt2->bindParam(1,$personnevalide_nouveau);
            $stmt2->bindParam(2,$personneattente_nouveau);
            $stmt2->bindParam(3,$personnevalide_actuel);
            $stmt2->bindParam(4,$personneattente_actuel);
            $stmt2->bindParam(5,$personne['idcreneau']);
            $stmt2->execute();
        } else {
            $libre_nouveau--;
            $libre_actuel++;
            $stmt2=$dbh->prepare("UPDATE CRENEAUX SET V".$numero_nouveau."=?,A".$numero_nouveau."=?,V".$numero_actuel."=?,A".$numero_actuel."=?,".$typeterrain_nouveau."=?,".$typeterrain_actuel."=? WHERE id=?");
            $stmt2->bindParam(1,$personnevalide_nouveau);
            $stmt2->bindParam(2,$personneattente_nouveau);
            $stmt2->bindParam(3,$personnevalide_actuel);
            $stmt2->bindParam(4,$personneattente_actuel);
            $stmt2->bindParam(5,$libre_nouveau);
            $stmt2->bindParam(6,$libre_actuel);
            $stmt2->bindParam(7,$personne['idcreneau']);
            $stmt2->execute();
        }
    } catch (Exception $e) {
        echo "Erreur dans le changement de terrain";
    }
}


function indiquecreneaucomplet($id) {
    global $dbh;
    try {
        $stmt = $dbh->prepare("SELECT * FROM RESULTAT WHERE id=?");
        $stmt->bindParam(1,$id);
        $stmt->execute();
        $personne=$stmt->fetch();
        $terrain=$personne['terrain'];
        $stmt = $dbh->prepare("SELECT * FROM CRENEAUX WHERE id=?");
        $stmt->bindParam(1,$personne['idcreneau']);
        $stmt->execute();
        $lecreneau=$stmt->fetch();
        $typeterrain=$lecreneau[$terrain];
        if (!in_array($typeterrain,['feminin','mixte','masculin'])) { throw('erreur'); }
        envoie_mail($personne['prenom'],$personne['mail'],'complet',jolie_date($lecreneau['date']).", ".$lecreneau['heure']." en ".$typeterrain);
    } catch (Exception $e) {
        echo "Erreur dans la validation avec mail";
    }
}




function pdf($str) {  // décode une chaine protégée pour l'affichage dans le PDF
    return utf8_decode(html_entity_decode($str));
}
function pdft($str) { // transforme le titre pour le pdf
    if ($str=='feminin') { return "\n JEU LIBRE FEMININ";}
    if ($str=='masculin') { return "\n JEU LIBRE MASCULIN";}
    if ($str=='mixte') { return "\n JEU LIBRE MIXTE";}
    return "\n ";
}

function recupere_liste_creneaux_demandes() { //isset($_POST['creneau']) est vrai : recupère les id de créneaux
    $resultat=[];
    foreach ($_POST['creneau'] as $id) {
        $res=intval($id);
        if ($res<1) {$res=1;} // ne doit pas arriver
        array_push($resultat,$res) ;
    }
    return $resultat;
}

function reinitialise_creneau($id) { // remets tout le monde en attente en fait
    global $dbh,$les_creneaux;
    try {
        $le_creneau=trouveCreneau($id);
        $stmt=$dbh->prepare ("UPDATE RESULTAT SET etat='attente' WHERE idcreneau=? AND etat!='sup'");
        $stmt->bindParam(1,$id);
        $stmt->execute();
        $personnes_attente=[];
        $places_libres=[];
        $personnes_attente['T1']=0;
        $personnes_attente['T2']=0;
        $personnes_attente['T3']=0;
        $personnes_attente['T4']=0;
        $places_libres['feminin']=0;
        $places_libres['mixte']=0;
        $places_libres['masculin']=0;
        $stmt=$dbh->prepare("SELECT * FROM RESULTAT WHERE terrain=? AND idcreneau=? AND etat!='sup'");
        $stmt->bindParam(1,$t);
        $stmt->bindParam(2,$id);
        foreach (['1','2','3','4'] as $numero_terrain) {
            $t='T'.$numero_terrain;
            $type=$le_creneau[$t];
            if (in_array($type,['masculin','feminin','mixte'])) {
                $places_libres[$type]+=$le_creneau['VMAX'.$numero_terrain]+$le_creneau['AMAX'.$numero_terrain];
                $stmt->execute();
                while ($row=$stmt->fetch()) {
                    $personnes_attente[$t]++;
                    $places_libres[$type]--;
                }
            }
        }
        $stmt=$dbh->prepare("UPDATE CRENEAUX SET feminin=?,mixte=?,masculin=?,V1=0,V2=0,V3=0,V4=0,A1=?,A2=?,A3=?,A4=? WHERE id=?");
        $stmt->bindParam(1,$places_libres['feminin']);
        $stmt->bindParam(2,$places_libres['mixte']);
        $stmt->bindParam(3,$places_libres['masculin']);
        $stmt->bindParam(4,$personnes_attente['T1']);
        $stmt->bindParam(5,$personnes_attente['T2']);
        $stmt->bindParam(6,$personnes_attente['T3']);
        $stmt->bindParam(7,$personnes_attente['T4']);
        $stmt->bindParam(8,$id);
        $stmt->execute();
    } catch (Exception $e) {
        echo "Erreur dans la reinitialisation du créneau";
    }

}

function valideavecmail($id) {
    global $dbh;
    try {
        $stmt = $dbh->prepare("SELECT * FROM RESULTAT WHERE id=?");
        $stmt->bindParam(1,$id);
        $stmt->execute();
        $personne=$stmt->fetch();
        $etat_actuel=$personne['etat'];
        $terrain=$personne['terrain'];
        if (!in_array($etat_actuel,['attente','supprime','annule'])) { throw('erreur'); }
        if (!in_array($terrain,['T1','T2','T3','T4'])) { throw('erreur'); }
        $numero=substr($terrain,1,1);
        $stmt = $dbh->prepare("SELECT * FROM CRENEAUX WHERE id=?");
        $stmt->bindParam(1,$personne['idcreneau']);
        $stmt->execute();
        $lecreneau=$stmt->fetch();
        $typeterrain=$lecreneau[$terrain];
        if (!in_array($typeterrain,['feminin','mixte','masculin'])) { throw('erreur'); }
        $libres=$lecreneau[$typeterrain];
        $personnevalide=$lecreneau['V'.$numero];
        $personneattente=$lecreneau['A'.$numero];
        $stmt= $dbh->prepare("UPDATE RESULTAT SET etat='valide' WHERE id=?");
        $stmt->bindParam(1,$id);
        $stmt2=$dbh->prepare("UPDATE CRENEAUX SET ".$typeterrain."=?, V".$numero."=?,A".$numero."=? WHERE id=?");
        $stmt2->bindParam(1,$libres);
        $stmt2->bindParam(2,$personnevalide);
        $stmt2->bindParam(3,$personneattente);
        $stmt2->bindParam(4,$personne['idcreneau']);
        if ($etat_actuel=='attente') {
            if ($personnevalide<$lecreneau['VMAX'.$numero]) {
                $personnevalide++;
                $personneattente--;
                $stmt->execute(); $stmt2->execute();
                envoie_mail($personne['prenom'],$personne['mail'],'confirmation',jolie_date($lecreneau['date']).", ".$lecreneau['heure']." en ".$typeterrain);
            } else {
                echo "\n Le créneau ne peut plus accepter de personnes validées.\n";
            }
        } else if ($etat_actuel=='supprime') {
            if ($personnevalide<$lecreneau['VMAX'.$numero]) {
                $personnevalide++;
                $libres--;
                $stmt->execute();$stmt2->execute();
                envoie_mail($personne['prenom'],$personne['mail'],'confirmation',jolie_date($lecreneau['date']).", ".$lecreneau['heure']." en ".$typeterrain);
            } else {
                $personneattente++;
                $libres--;
                $etat='attente';
                $stmt->execute();$stmt2->execute();
            }
        } else { echo "combinaison imprévue de changement d'état";}
    } catch (Exception $e) {
        echo "Erreur dans la validation avec mail";
    }
}

function valide_creneau($id) {
    global $dbh,$les_creneaux;
    $le_creneau=trouveCreneau($id);
    try {
        $stmt=$dbh->prepare("SELECT * FROM RESULTAT WHERE idcreneau=? AND terrain=?");
        $stmt->bindParam(1,$id);
        $stmt->bindParam(2,$terrain);
        $_POST['texte']='valide';
        foreach (['T1','T2','T3','T4'] as $terrain) {
            if (in_array($le_creneau[$terrain],['feminin','mixte','masculin'])) {
                $stmt->execute();
                while ($row=$stmt->fetch()) {
                    if ($row['etat']!='valide') {change_etat($row['id']);}
                }
            }
        }

    } catch (Exception $e) {
        echo "Erreur dans la validation des personnes en attente";
    }
}


?>
