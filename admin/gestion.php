
<?php require "header.php"; 
ouvre_bdd(); 
if (isset($_POST['annonce'])) { 
    maj_annonce();
} elseif (isset($_POST['nom']) && isset($_POST['but'])) { 
    if ($_POST['but']=="ajout") {
        ajout_staff();
    } elseif ($_POST['but']=="modif") {
        modif_staff();
    } elseif ($_POST['but']=="suppression") {
        $les_creneaux=lire_les_creneaux();
        supprime_staff();
    }
} 
$annonce=lire_annonce() ;
$le_staff=lecture_staff();
ferme_bdd();
?>    
    
    
    <DIV class="creationCreneau">
        <FORM id="Formulaire" method="post" action="gestion.php">
                <legend>Annonce de la page d'accueil :</legend>
                <textarea class="annonce" style="width : 80%;" rows=10 name="annonce"><?php 
                foreach ($annonce as $ligne) {
                    echo secu_ecran($ligne)."\n";
                    }?></textarea>
        </FORM> 
        <BUTTON onclick="valideFormulaire()"> Mettre à jour l'annonce avec ces informations.</BUTTON>
    </DIV> <BR>

    <DIV class="AjoutStaffeur">
        <FORM  method="post" action="gestion.php">
                <legend>Ajout d'une personne au staff :</legend><BR>
         <div class="perso"> <!-- grid layout on fieldset is buggy on Chrome -->
            <label for="nom">Nom</label>
            <input id="nom" name="nom"/>
            <label for="mail">Mail</label>
            <input type="email" id="mail" name="mail" />
            <label for="telephone">Téléphone</label>
            <input id="telephone" name="telephone" />
            <input type="hidden" name="but" value="ajout" />
            <div></div>
            <input class="bouton" type="submit" value="Ajouter cette personne au staff">
        </div>
        </FORM>
    </DIV>
    <BR><BR>
  
    <DIV class="ModifStaffeur">
        <form id="FormulaireBis" method="post" action="gestion.php">
        Modification les données de :
            <select id="amodifier" name="amodifier" onchange="ajusteDonneesStaff()">
<?php
            foreach($le_staff as $key=>$un_staff) {
                echo "<option value='".$key."'> ".$un_staff['nom']."</option>\n";
            }
?>
            </select><BR><BR>
         <div class="perso"> <!-- grid layout on fieldset is buggy on Chrome -->
            <label for="nommodif">Nom</label>
            <input id="nommodif" name="nom" />
            <label for="mailmodif">Mail</label>
            <input type="email" id="mailmodif" name="mail" />
            <label for="telephonemodif">Téléphone</label>
            <input id="telephonemodif" name="telephone" />
            <input id="buthidden" type="hidden" name="but" value="modif" />
            <div style="margin-top : 15px;"><button onclick="suppression()"> Supprimer cette personne</button></div>
            <input class="bouton" type="submit" value="Modifier ces données">        
        </div>
        </form>
    </DIV>
 

</BODY>
<script>
    let leStaff = <?php echo json_encode($le_staff); echo "\n"; ?>
</script>
<SCRIPT src="gestion_java.js?=<?php echo time(); //pour forcer le js a ne pas être en cache et jouer des tours en dev ?>">  </SCRIPT>
<script>
    ajusteDonneesStaff()
</script>
</HTML>