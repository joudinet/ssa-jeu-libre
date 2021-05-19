<?php require "header.php"; 
ouvre_bdd();
$les_creneaux=lire_les_creneaux();
if (isset($_POST['creneau'])) {
    if (isset($_POST['but'])) { // test inutile car défini si creneau l'est?
        $les_creneaux_demandes=recupere_liste_creneaux_demandes();
        if ($_POST['but']=="creationpdf") {
            creationpdf($les_creneaux_demandes);
        } elseif ($_POST['but']=="creationenvoi") {
            creationenvoi($les_creneaux_demandes);
        }
    }  
}
ferme_bdd();
?>

    <BR><BR> 
    <FORM id="Formulaire" method="post" action="creationpdf.php">
<?php
    foreach ($les_creneaux as $un_creneau) {
        $id=$un_creneau['id'];
        echo '<DIV><INPUT type="checkbox" name="creneau[]" id="c'.$id.'" value="'.$id.'"';
        if (isset($_POST['creneau']) && in_array($id,$les_creneaux_demandes)) { echo ' checked';}
        echo '> <LABEL for="c'.$id.'"> '.secu_ecran(jolie_date($un_creneau['date'])).', '.secu_ecran($un_creneau['heure']).'</LABEL></DIV>';
    }
?>
    <input hidden id="but" name="but" value="">
    </FORM>
    <BUTTON onclick="creationPDF()">Créer le PDF pour ces créneaux</BUTTON>
    <BUTTON onclick="creationEnvoi()">Créer le PDF pour ces créneaux et envoyer un mail aux joueurs concernés</BUTTON><BR>
        (pour les personnes validées et en attente  uniquement)
      <BR><BR>  
<?php
if (isset($_POST['creneau'])) {
    echo '<a href="creneauxPDF.pdf">Voir le fichier PDF</a> ou bien ';
   echo '<a href="creneauxPDF.pdf" download>télécharger le fichier PDF</a>';
}?>

</BODY>
<SCRIPT src="gestion_java.js?=<?php echo time(); //pour forcer le js a ne pas être en cache et jouer des tours en dev ?>">  </SCRIPT>
</HTML>