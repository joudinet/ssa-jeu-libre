
<?php
require "header.php";
ouvre_bdd();
if (isset($_POST['nom'])) {
    if (valide_formulaire()) {
     echo "<p>Ta demande d'inscription a bien été prise en compte et un e-mail t'a été envoyé.<br/><a href='index.php'>Lien de retour à l'accueil</a></p>";
     die();
    }
}
$les_creneaux=lire_les_creneaux();
$annonce=lire_annonce();
ferme_bdd();
?>

    <h1 class="titre_index">Créneaux de jeu libre à Sand System</h1>
    
    <?php echo affiche_annonce(); ?>

    <form id="le_formulaire_index" class="formulaire_index" method="post" action="index.php">
    
    <fieldset class="creneaux_index" id="creneauxdispos">
        <legend>Liste des créneaux disponibles : (créneaux sélectionnés en orange)</legend>
        <div class="creneaux_index">
        <?php
        foreach ($les_creneaux as $un_creneau) {
            $no_creneau = true;
            $id = $un_creneau['id'];
            echo '<div>'.secu_ecran(jolie_date($un_creneau['date'])).' '.secu_ecran($un_creneau['heure']).' :</div>';
            echo '<div>';
            if ($un_creneau['nbstaff']=="0") {
                echo "<div class='round' style='background-color: red;'>en attente</div>";
            } elseif ($un_creneau['reservation']=="oui") {
                echo '<input  type="checkbox" id="c'.$id.'" name="c'.$id.'" onclick="click_creneau('.$id.')" />';
                echo '<label id="cl'.$id.'" for="c'.$id.'" class="round">ouvert avec inscription</label>'; 
                echo $un_creneau['nbdemandes']." personne(s)";
            } else {
                echo "<div class='round'  style='background-color: green;'>créneau ouvert</div>"; 
            }
            echo '</div>';
        } ?>
        </div>
    </fieldset>
 
    <fieldset id="infoIndex" class="info_index" style="display:none">
            <legend>Informations personnelles :</legend>
        <div class="perso"> <!-- grid layout on fieldset is buggy on Chrome -->
            <label for="nom">Nom</label>
            <input id="nom" name="nom" />
            <label for="prenom">Prénom</label>
            <input id="prenom" name="prenom" />
            <label for="mail">Mail</label>
            <input type="email" id="mail" name="mail" />
        </div>
        </fieldset>

    <footer id="footer" style="display:none">
    <div>
         <input type="checkbox" id="consignergpd" name="consignergpd" class="texteconsigne">
         <label for="consignergpd" class="textergpd">
             J'autorise Sandsystem à sauvegarder mes informations personnelles pendant une durée de 6 mois maximum. (à tout moment, il suffit d'envoyer un mail à jeu-libre@sandsystem.com pour les supprimer)
        </label>
    </div>
    <div class="g-recaptcha" data-sitekey="<?php echo $captcha_publicKey; ?>"></div>
    <button type="button" onclick="validation_formulaire()" >Soumettre la demande de créneaux</button>
    </footer>
    </form>
</body>
</html>
