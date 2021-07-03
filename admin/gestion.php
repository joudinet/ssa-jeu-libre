
<?php require "header.php"; 
ouvre_bdd();
if (isset($_POST['annonce'])) {
    maj_annonce();
} elseif (isset($_POST['nom'])) {
    ajout_staff();
}
$annonce=lire_annonce() ;
ferme_bdd();
?>    
    
    
    <DIV class="creationCreneau">
        <FORM id="Formulaire" method="post" action="gestion.php">
                <legend>Annonce de la page d'accueil :</legend>
                <textarea class="annonce" rows=10 cols=160 name="annonce"><?php 
                foreach ($annonce as $ligne) {
                    echo secu_ecran($ligne)."\n";
                    }?></textarea>
        </FORM> 
        <BUTTON onclick="valideFormulaire()"> Mettre à jour l'annonce avec ces informations.</BUTTON>
    </DIV> <BR>

    <DIV class="creationCreneau">
        <FORM id="FormulaireBis" method="post" action="gestion.php">
                <legend>Ajout d'une personne au staff :</legend><BR>
         <div class="perso"> <!-- grid layout on fieldset is buggy on Chrome -->
            <label for="nom">Nom</label>
            <input id="nom" name="nom" />
            <label for="mail">Mail</label>
            <input type="email" id="mail" name="mail" />
       </FORM> 
        <input type="submit" value="Ajouter cette personne au staff">
    </DIV>
    

</BODY>
<SCRIPT src="gestion_java.js?=<?php echo time(); //pour forcer le js a ne pas être en cache et jouer des tours en dev ?>">  </SCRIPT>
</HTML>