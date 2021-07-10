<?php
require "header.php";
ouvre_bdd();
if (isset($_POST['but'])) {
    $id=intval($_POST['inputid']);
    if ($_POST['but']=="supprime demande") {
        if ($id<1) { die(); }
        supprime_demande($id);
    }
}

$les_creneaux=lire_les_creneaux();
?>

<h3 class="titre_index">Occupation provisoire des terrains en heures creuses :</h1>
 
 <div class="les_inscrits">
<?php 
    foreach ($les_creneaux as $un_creneau) {
        if ($un_creneau['reservation']=="oui") {
            $les_joueurs=lire_joueurs_creneau($un_creneau['id']);
            echo "<div> ".secu_ecran(jolie_date($un_creneau['date']))." ".secu_ecran($un_creneau['heure'])." : </div>";
            echo "<div> ";
            foreach ($les_joueurs as $un_joueur) {
                echo "<div >".$un_joueur['prenom']." ".$un_joueur['nom']."<button onclick='supprimeDemande(".$un_joueur['id'].")' class='round bgred'>x</button></div> ";
            }
            echo "</div>";
        }
    } 
ferme_bdd();?>
 </div>

 <form id="Formulaire" method="post" action="heurescreuses.php">
    <input id="inputid" type="hidden" name="inputid" value="0">
    <input id="but" type="hidden" name="but" >
</form>

</body>

<SCRIPT src="gestion_java.js?=<?php echo time(); //pour forcer le js a ne pas être en cache et jouer des tours en dev ?>">  </SCRIPT>

</html>