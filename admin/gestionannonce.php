
<?php require "header.php"; 
ouvre_bdd();
if (isset($_POST['annonce'])) {
    maj_annonce();
}
$annonce=lire_annonce() ;
ferme_bdd();
?>    
    
    
    <DIV class="creationCreneau">
        <FORM id="Formulaire" method="post" action="gestionannonce.php">
                <legend>Annonce de la page d'accueil :</legend>
                <textarea class="annonce" rows=10 cols=160 name="annonce"><?php 
                foreach ($annonce as $ligne) {
                    echo secu_ecran($ligne)."\n";
                    }?></textarea>
        </FORM> 
        <BUTTON onclick="valideFormulaire()"> Mettre à jour l'annonce avec ces informations.</BUTTON>
    </DIV>
    

</BODY>
<SCRIPT src="gestion_java.js?=<?php echo time(); //pour forcer le js a ne pas être en cache et jouer des tours en dev ?>">  </SCRIPT>
</HTML>