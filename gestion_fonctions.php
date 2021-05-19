<?php

error_reporting(E_ALL); // pour activer les erreurs
ini_set("display_errors", 1);  // à commenter à la fin bien sûr
//penser à mettre la base de donénes en utf8 pour être sûr?

require "general_fonctions.php";
use PHPMailer\PHPMailer\PHPMailer; //pour le mail
use PHPMailer\PHPMailer\SMTP;
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';
require 'PHPMailer-master/src/Exception.php';
require('fpdf.php'); // pour le PDF

function creationpdf($les_creneaux_demandes) {
    global $dbh,$les_creneaux; 
    $pdf = new PDF_MC_Table();
    $pdf->SetWidths(array(40,40,40,40));
    $pdf->AddPage();
    $pdf->SetFont('Arial','B',7);
    $pdf->SetTextColor(0);
    $stmt = $dbh->prepare("SELECT * FROM RESULTAT WHERE idcreneau=? AND terrain=? AND etat='valide'");
    $stmt->bindParam(1, $idcreneau);
    $stmt->bindParam(2, $terrain);
    $stmt2 = $dbh->prepare("SELECT * FROM RESULTAT WHERE idcreneau=? AND terrain=? AND etat='attente'");
    $stmt2->bindParam(1, $idcreneau);
    $stmt2->bindParam(2, $terrain);
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
        $attente=[[],[],[],[]];
        $attente_adh=[[],[],[],[]];
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
            $stmt2->execute();
            $k=1;
            while ($row=$stmt2->fetch()) {  // récupère un terrain d'un des créneaux
                array_push($attente[$i],pdf($row['prenom'])." ".pdf($row['nom']));
                if ($row['adherent']=="non") {
                    array_push($attente_adh[$i],[true,255,255,0]);
                } else {
                    array_push($attente_adh[$i],[false,0,0,0]);
                }
                $k++;
            }
        }
        for ($i=0;$i<9;$i++) {
            $pdf->Row([$lignes[$i][0],$lignes[$i][1],$lignes[$i][2],$lignes[$i][3]],[$adh[$i][0],$adh[$i][1],$adh[$i][2],$adh[$i][3]]);
        }
        $nb_lignes_attente=max(count($attente[0]),count($attente[1]),count($attente[2]),count($attente[3]));
        for ($i=0;$i<$nb_lignes_attente;$i++) {
            $txt=["","","",""];
            $adh=[[false,0,0,0],[false,0,0,0],[false,0,0,0],[false,0,0,0]];
            if ($i<count($attente[0])) { $txt[0]=$attente[0][$i]; $adh[0]=$attente_adh[0][$i];}
            if ($i<count($attente[1])) { $txt[1]=$attente[1][$i];$adh[1]=$attente_adh[1][$i];}
            if ($i<count($attente[2])) { $txt[2]=$attente[2][$i];$adh[2]=$attente_adh[2][$i];}
            if ($i<count($attente[3])) { $txt[3]=$attente[3][$i];$adh[3]=$attente_adh[3][$i];}
            $pdf->Row($txt,$adh,false);
        }
        $pdf->Ln(); 
   }
    $pdf->Output("F","creneauxPDF.pdf");
    
}

function creationenvoi($les_creneaux_demandes) {
    global $dbh,$les_creneaux; 
    creationpdf($les_creneaux_demandes);
    $liste_mail=[];
    $stmt = $dbh->prepare("SELECT mail FROM RESULTAT WHERE idcreneau=? AND (etat='valide' OR etat='attente')");
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
    $mail->From='jerome.99@hotmail.fr';
    $mail->FromName="L'équipe Sand-System";
    $mail->AddReplyTo('jerome.99@hotmail.fr');     
    $mail->ContentType = 'text/plain';
    $mail->Subject='Créneaux de jeu';
    $mail->Body="Bonjour!\n\n"."Voici, en pièce jointe, la répartition sur les créneaux pour la semaine.\n"."Bon jeu!\n\n"."L'équipe SSA";
    $mail->AddAttachment('creneauxPDF.pdf');
    foreach ($liste_mail as $target) {
        $mail->AddAddress($target);
    } 
    $mail->Send();
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

function envoie_mail($nom,$target,$type_mail,$textecreneau) { // type_mail : annulation, confirmation
    return ;     // trop de spam donc non
    $mail = new PHPmailer();
    $mail->CharSet = 'UTF-8';
    $mail->From='jerome.99@hotmail.fr';
    $mail->FromName='Jerome Nizon';
    $mail->AddAddress($target);
    $mail->AddReplyTo('jerome.99@hotmail.fr');     
    $mail->ContentType = 'text/plain';
    $msg="Bonjour ".$nom."!\n\n";
    $msg.="Tu as demandé un créneau ".$textecreneau."\n";
    if ($type_mail=="confirmation") {
        $mail->Subject='Confirmation de créneau';
        $msg.="Nous avons le plaisir de t'annoncer que ce créneau aura bien lieu!";
    }  else {
        $mail->Subject='Annulation de créneau';
        $msg.="Malheureusement, ce créneau n'est plus disponible.";
       
    }
    $msg.="\n\n"."L'équipe SSA";
    $mail->Body=$msg;
    if( false &&!$mail->Send()){ //Teste le return code de la fonction
        //echo 'Mailer Error: ' . $mail->ErrorInfo;
        $mail->SmtpClose();
        unset($mail);         
        return false;
    }
    echo "<BR> Le mail qui sera envoyé : <BR><TEXTAREA style='width: 80%;heigth : 50px;text-align : left;'>".$msg."</TEXTAREA>";
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
    if (!in_array($etat,['valide','attente','supprime','annule'])) {
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
        if ($etat=='supprime') {
            $libres++;
            if ($etat_actuel=='valide') {$personnevalide--;}
            else if ($etat_actuel=='attente') {$personneattente--;}
            else {throw "erreur inattendue";}
            $stmt->execute();$stmt2->execute();
            envoie_mail($personne['nom'],$personne['mail'],'suppression',jolie_date($lecreneau['date']).", ".$lecreneau['heure']." en ".$typeterrain);
        } else if ($etat=='valide' && $etat_actuel=='attente') {
            if ($personnevalide<$lecreneau['VMAX'.$numero]) {
                $personnevalide++;
                $personneattente--;
                $stmt->execute(); $stmt2->execute();
                envoie_mail($personne['nom'],$personne['mail'],'confirmation',jolie_date($lecreneau['date']).", ".$lecreneau['heure']." en ".$typeterrain);
            } else {
                //echo "\n Le créneau ne peut plus accepter de personnes validées.\n";
            }
        } else if ($etat=='valide' && $etat_actuel=='supprime') {
            if ($personnevalide<$lecreneau['VMAX'.$numero]) {
                $personnevalide++;
                $libres--;
                $stmt->execute();$stmt2->execute();
                envoie_mail($personne['nom'],$personne['mail'],'confirmation',jolie_date($lecreneau['date']).", ".$lecreneau['heure']." en ".$typeterrain);
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

function creationCreneau() {
    global $dbh,$terrainpossible,$couleurpossible;
    try {
        $res=$dbh->query('SELECT MAX(id) FROM CRENEAUX');
        $id=1;
        while ($row=$res->fetch()) {
            $id=1+intval($row[0]);
        }
        $stmt = $dbh->prepare("INSERT INTO CRENEAUX VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,0,0,0,0,0,0,0,0,8,8,8,8,2,2,2,2)");
        $stmt->bindParam(1, $id);
        $stmt->bindParam(2, $date);
        $stmt->bindParam(3, $heure);
        $stmt->bindParam(4, $feminin);
        $stmt->bindParam(5, $mixte);
        $stmt->bindParam(6, $masculin);
        $stmt->bindParam(7, $t1);
        $stmt->bindParam(8, $t2);
        $stmt->bindParam(9, $t3);
        $stmt->bindParam(10, $t4);
        $stmt->bindParam(11, $c1);
        $stmt->bindParam(12, $c2);
        $stmt->bindParam(13, $c3);
        $stmt->bindParam(14, $c4);
        $date=secu_bdd($_POST['date']);
        $heure=secu_bdd($_POST['heure']);
        if (isset($_POST['T1']) && isset($_POST['C1']) && in_array($_POST['T1'],$terrainpossible) && in_array($_POST['C1'],$couleurpossible)) {
            $t1=secu_bdd($_POST['T1']);
            $c1=secu_bdd($_POST['C1']);
        } else {
            $t1="PAS DE JEU LIBRE";
            $c1="#CBCBCB";
        }
        if (isset($_POST['T2']) && isset($_POST['C2']) && in_array($_POST['T2'],$terrainpossible) && in_array($_POST['C2'],$couleurpossible)) {
            $t2=secu_bdd($_POST['T2']);
            $c2=secu_bdd($_POST['C2']);
        } else {
            $t2="PAS DE JEU LIBRE";
            $c2="#CBCBCB";
        }
        if (isset($_POST['T3']) && isset($_POST['C3']) && in_array($_POST['T3'],$terrainpossible) && in_array($_POST['C3'],$couleurpossible)) {
            $t3=secu_bdd($_POST['T3']);
            $c3=secu_bdd($_POST['C3']);
        } else {
            $t3="PAS DE JEU LIBRE";
            $c3="#CBCBCB";
        }
        if (isset($_POST['T4']) && isset($_POST['C4']) && in_array($_POST['T4'],$terrainpossible) && in_array($_POST['C4'],$couleurpossible)) {
            $t4=secu_bdd($_POST['T4']);
            $c4=secu_bdd($_POST['C4']);
        } else {
            $t4="PAS DE JEU LIBRE";
            $c4="#CBCBCB";
        }
        $feminin=0;
        $masculin=0;
        $mixte=0;
        foreach ([$t1,$t2,$t3,$t4] as $terrain) {
            if ($terrain=="feminin") {
                $feminin+=10;
            }
            if ($terrain=="mixte") {
                $mixte+=10;
            }
            if ($terrain=="masculin") {
                $masculin+=10;
            }
        }
        $stmt->execute();
    } catch (Exception $e) {
        echo "Erreur dans la saisie des éléments du créneaux";
    }
}

function ModifieCreneau() {
    global $dbh, $les_creneaux,$terrainpossible,$couleurpossible;
    try {
        $stmt = $dbh->prepare("UPDATE CRENEAUX SET T1=?,T2=?,T3=?,T4=?,C1=?,C2=?,C3=?,C4=? WHERE id=?");
        $stmt->bindParam(1, $t1);
        $stmt->bindParam(2, $t2);
        $stmt->bindParam(3, $t3);
        $stmt->bindParam(4, $t4);
        $stmt->bindParam(5, $c1);
        $stmt->bindParam(6, $c2);
        $stmt->bindParam(7, $c3);
        $stmt->bindParam(8, $c4);
        $stmt->bindParam(9, $id);
        $id=intval($_POST['inputid']);
        if ($id<1) {throw('erreur');}
       if (isset($_POST['T1']) && isset($_POST['C1']) && in_array($_POST['T1'],$terrainpossible) && in_array($_POST['C1'],$couleurpossible)) {
            $t1=secu_bdd($_POST['T1']);
            $c1=secu_bdd($_POST['C1']);
        } else {
            $t1="PAS DE JEU LIBRE";
            $c1="#CBCBCB";
        }
        if (isset($_POST['T2']) && isset($_POST['C2']) && in_array($_POST['T2'],$terrainpossible) && in_array($_POST['C2'],$couleurpossible)) {
            $t2=secu_bdd($_POST['T2']);
            $c2=secu_bdd($_POST['C2']);
        } else {
            $t2="PAS DE JEU LIBRE";
            $c2="#CBCBCB";
        }
        if (isset($_POST['T3']) && isset($_POST['C3']) && in_array($_POST['T3'],$terrainpossible) && in_array($_POST['C3'],$couleurpossible)) {
            $t3=secu_bdd($_POST['T3']);
            $c3=secu_bdd($_POST['C3']);
        } else {
            $t3="PAS DE JEU LIBRE";
            $c3="#CBCBCB";
        }
        if (isset($_POST['T4']) && isset($_POST['C4']) && in_array($_POST['T4'],$terrainpossible) && in_array($_POST['C4'],$couleurpossible)) {
            $t4=secu_bdd($_POST['T4']);
            $c4=secu_bdd($_POST['C4']);
        } else {
            $t4="PAS DE JEU LIBRE";
            $c4="#CBCBCB";
        }
        $stmt->execute();
        $les_creneaux=lire_les_creneaux();
    } catch (Exception $e) {
        echo "Erreur dans la modification du créneaux";
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
        $stmt=$dbh->prepare ("UPDATE RESULTAT SET etat='attente' WHERE idcreneau=?");
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
        $stmt=$dbh->prepare("SELECT * FROM RESULTAT WHERE terrain=? AND idcreneau=?");
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

function SupprimeCreneau() {
    global $dbh,$les_creneaux; 
    try {
        $stmt = $dbh->prepare("DELETE FROM DEMANDES WHERE idcreneau=?");
        $stmt->bindParam(1, $id);
        $stmt2 = $dbh->prepare("DELETE FROM RESULTATS WHERE idcreneau=?");
        $stmt2->bindParam(1, $id);
        $stmt3 = $dbh->prepare("DELETE FROM CRENEAUX WHERE id=?");
        $stmt3->bindParam(1, $id);
        $id=intval($_POST['inputid']);
        if ($id<1) { throw('erreur'); }
        $stmt->execute();
        $stmt2->execute();
        $stmt3->execute();
        $les_creneaux=lire_les_creneaux();
    } catch (Exception $e) {
        echo "Erreur dans la suppression du créneaux";
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