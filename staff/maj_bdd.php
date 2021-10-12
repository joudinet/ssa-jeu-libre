<?php 
require "staff_fonctions.php"; 
ouvre_bdd();
$staffeur=$_SERVER['PHP_AUTH_USER'];
if (!(isset($_POST['id']))) { echo "pb données"; die(); } 
$stmt=$dbh->prepare('SELECT * FROM STAFF WHERE nom=?');
$stmt->bindParam(1,$staffeur);
$stmt->execute();
$idstaffeur=-1;
while ($row=$stmt->fetch()) {
	$idstaffeur=$row['id'];
}
if ($idstaffeur==-1) {echo "pb données"; die(); }
$id=$_POST['id'];
$stmt=$dbh->prepare('SELECT * FROM GESTIONCRENEAUX JOIN STAFF ON idstaff=STAFF.id  WHERE nom=? AND idcreneau=?');
$stmt->bindParam(1,$staffeur);
$stmt->bindParam(2,$id);
$stmt->execute();

while ($row=$stmt->fetch()) { 
	if ($row['statut']=="oui") {
	    if ($_POST['action']=='avecclick') {
		$stmt=$dbh->prepare('UPDATE GESTIONCRENEAUX SET statut="sibesoin"  WHERE idcreneau=? AND idstaff=?');
		$stmt->bindParam(1,$id);
		$stmt->bindParam(2,$idstaffeur);
		$stmt->execute();		
    		$stmt3=$dbh->prepare('SELECT nbstaff FROM CRENEAUX WHERE id=?');
    		$stmt3->bindParam(1,$id);
    		$stmt4=$dbh->prepare('UPDATE CRENEAUX SET nbstaff=? WHERE id=?');
    		$stmt4->bindParam(1,$nbstaff);
    		$stmt4->bindParam(2,$id);
		$stmt3->execute();
		while ($row=$stmt3->fetch()) {
			$nbstaff=intval($row['nbstaff'])-1;
		} 
		$stmt4->execute();
		if ($nbstaff==0) {
			echo json_encode(["sibesoin",$nbstaff]);
		} else { 
			echo json_encode(["sibesoin",$nbstaff]);
		}
	    } else {
		echo json_encode(["oui",-1]);
	    }
	    ferme_bdd();
	    exit();
        }  else {
	    if ($_POST['action']=='avecclick') {
		$stmt=$dbh->prepare('DELETE FROM GESTIONCRENEAUX WHERE idcreneau=? AND idstaff=?');
		$stmt->bindParam(1,$id);
		$stmt->bindParam(2,$idstaffeur);
		$stmt->execute();		
		echo json_encode(["non",-1]);
	    } else {
		echo json_encode(["sibesoin",-1]);
	    }
	    ferme_bdd();
	    exit();
        } 
}
if ($_POST['action']=='avecclick') {
   $stmt=$dbh->prepare('INSERT INTO GESTIONCRENEAUX (idcreneau,idstaff,statut) VALUES (?,?,"oui")');
   $stmt->bindParam(1,$id);
   $stmt->bindParam(2,$idstaffeur);
   $stmt->execute();
   $stmt3=$dbh->prepare('SELECT nbstaff FROM CRENEAUX WHERE id=?');
   $stmt3->bindParam(1,$id);
   $stmt4=$dbh->prepare('UPDATE CRENEAUX SET nbstaff=? WHERE id=?');
   $stmt4->bindParam(1,$nbstaff);
   $stmt4->bindParam(2,$id);
   $stmt3->execute();
   while ($row=$stmt3->fetch()) {
	$nbstaff=intval($row['nbstaff'])+1;
   } 
   $stmt4->execute();
   echo json_encode(["oui",$nbstaff]);
} else {
   echo json_encode(["non",$nbstaff]);
}
ferme_bdd();
?>