<?php require "header.php"; 
ouvre_bdd();
if (isset($_POST['but'])) {
    if ($_POST['but']=="supprimeCreneaux") {
        supprime_tous_creneaux();
    } elseif ($_POST['but']=="supprimeStaff") {
        supprime_tout_staff();
    } elseif ($_POST['but']=="remiseazero") {
        remiseazero();
    }
}
ferme_bdd();
?>  

<BR><BR>
<form  method="post" action="initialisation.php">
    <input type="hidden" name="but" value="supprimeCreneaux">
    <input class="bouton" type="submit" value="Supprimer tous les créneaux">        
</form>

<form  method="post" action="initialisation.php">
    <input type="hidden" name="but" value="supprimeStaff">
    <input class="bouton" type="submit" value="Supprimer tout le staff">        
</form>

<form  method="post" action="initialisation.php">
    <input type="hidden" name="but" value="remiseazero">
    <input class="bouton" type="submit" value="Tout remettre à zéro">        
</form>

</BODY>
</HTML>