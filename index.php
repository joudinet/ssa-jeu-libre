<!DOCTYPE html>

<html>
<head>
    <meta http-equiv=content-type content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des créneaux</title>
    <link rel="stylesheet" href="index_style.css?=<?php echo time(); //pour forcer le css a ne pas être en cache et jouer des tours en dev ?>">
    <script src="index_java.js?=<?php echo time(); //pour forcer le js a ne pas être en cache et jouer des tours en dev ?>" defer></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>

<?php
require "index_fonctions.php";
ouvre_bdd();
if (isset($_POST['nom'])) {
    if (valide_formulaire()) {
     echo "<p>Votre demande d'inscription a bien été prise en compte et un e-mail vous a été envoyé.<br/><a href='index.php'>Lien de retour à l'accueil</a></p>";
     die();
    }
}
$les_creneaux=lire_les_creneaux();
$annonce=lire_annonce();
ferme_bdd();
?>
<body>
    <nav class="menu">
        <ul>
             <li><a href="#">Demande de pré-inscription</a></li><!-- ' -->
             <li><a href="inscrits.php">Occupation des terrains</a></li>
        </ul>
    </nav>
    <h1 class="titre_index">Pré-inscription aux créneaux de jeu libre à Sand System</h1>

    <?php echo affiche_annonce(); ?>
    <h4>⚠️ les créneaux demandés ne sont pas automatiquement attribués ! <BR><BR>

Un email sera envoyé les lundis soirs et les jeudis soir avec les créneaux définitifs et la liste d'attente :
le club répartit les demandes en priorisant une séance par personne et plus s'il reste des places disponibles !
Cette règle ne vaut pas pour les personnes s'inscrivant la veille pour le lendemain ou une fois les tableaux définitifs envoyés 😉</h2>

    <form id="le_formulaire_index" class="formulaire_index" method="post" action="index.php">
        <fieldset>
            <legend>Informations personnelles :</legend>
        <div class="perso"> <!-- grid layout on fieldset is buggy on Chrome -->
            <label for="nom">Nom</label>
            <input id="nom" name="nom" />
            <label for="prenom">Prénom</label>
            <input id="prenom" name="prenom" />
            <label for="mail">Mail</label>
            <input type="email" id="mail" name="mail" />
            <label for="telephone">Téléphone</label>
            <input id="telephone" name="telephone" type="tel" />
        </div>
        </fieldset>
        <fieldset>
            <legend>Niveau de jeu estimé :</legend>
            <input type="radio" name="niveau" value="debutant" id="jeu1" />
            <label id="jeul1" for="jeu1" class="round">débutant</label>
            <input type="radio" name="niveau" value="intermediaire" id="jeu2" />
            <label id="jeul2" for="jeu2" class="round">intermédiaire</label>

            <input type="radio" name="niveau" value="confirme" id="jeu3" />
            <label id="jeul3" for="jeu3" class="round">confirmé</label>
            <input type="radio" name="niveau" value="expert" id="jeu4" />
            <label id="jeul4" for="jeu4" class="round">expert</label>
        </fieldset>
        <fieldset>
            <legend>Es-tu adhérent(e) SandSystem ?</legend>
            <input type="radio" id="ad1" name="adherent" value="oui" />
            <label id="adl1" for="ad1" class="round">oui</label>
            <input type="radio" id="ad2" name="adherent" value="non" />
            <label id="adl2" for="ad2" class="round">non</label>
            <div id="adherer" style="display: none">
                Pour les non-adhérents, la session est à 10 euros
                    (<a href="http://www.sandsystem.com/beach-volley/inscription-2020-2021/">lien pour adhérer</a>).
            </div>
        </fieldset>
        <fieldset>
            <legend>Partenaires</legend>
            <textarea name="commentaire" placeholder="Partenaire(s) souhaité(s) - Si tu as une remarque et/ou une question, c'est ici aussi !"></textarea>
        </fieldset>
    <fieldset class="creneaux_index" id="creneauxdispos">
        <legend>Liste des créneaux disponibles : (créneaux sélectionnés en orange)</legend>
    <div class="creneaux_index">
<?php
foreach ($les_creneaux as $un_creneau) {
    $no_creneau = true;
    $id = $un_creneau['id'];
    echo '<div>'.secu_ecran(jolie_date($un_creneau['date'])).' '.secu_ecran($un_creneau['heure']).' :</div>';
    echo '<div>';
    if ($un_creneau['feminin']>0) {
        $no_creneau = false;
        echo '<input class="checkboxfeminin" type="checkbox" id="c'.$id.'feminin" name="c'.$id.'feminin" onclick="click_creneau('.$id.',0)" value=0 />';
        echo '<label id="lc'.$id.'feminin" for="c'.$id.'feminin" class="round">féminin</label>';
    }
    if ($un_creneau['masculin']>0) {
        $no_creneau = false;
        echo '<input class="checkboxmasculin" type="checkbox" id="c'.$id.'masculin" name="c'.$id.'masculin" onclick="click_creneau('.$id.',1)" value=0 />';
        echo '<label id="lc'.$id.'masculin" for="c'.$id.'masculin" class="round">masculin</label>';
    }
    if ($un_creneau['mixte']>0) {
        $no_creneau = false;
        echo '<input class="checkboxmixte" form="le_formulaire_index" type="checkbox" id="c'.$id.'mixte" name="c'.$id.'mixte" onclick="click_creneau('.$id.',2)" value=0 />';
        echo '<label id="lc'.$id.'mixte" for="c'.$id.'mixte" class="round">mixte</label>';
    }
    if ($no_creneau) {
        echo "Aucun créneau disponible.";
    }
    echo '</div>';
}

?>
    </div>
    </fieldset>

    <footer>
    <div>
        <input type="checkbox" id="consignesecurite" name="consignesecurite" class="texteconsigne">
        <label for="consignesecurite" class="texteconsigne">
            Je confirme avoir pris connaissance du <a href=" https://drive.google.com/file/d/12l7dqbU4wu52WcvrWphWyRbwFPnm-2qz/view?usp=sharing">protocole de jeu</a> pour une reprise responsable. J'en accepte les termes et les conditions. Je m'engage à le respecter.
        </label>
    </div>
    <div>
         <input type="checkbox" id="consignergpd" name="consignergpd" class="texteconsigne">
         <label for="consignergpd" class="textergpd">
             J'autorise Sandsystem à sauvegarder mes informations personnelles pendant une durée de 6 mois maximum. (à tout moment, il suffit d'envoyer un mail à info@sandsystem.com pour les supprimer)
        </label>
    </div>
    <div class="g-recaptcha" data-sitekey="<?php echo $captcha_publicKey; ?>"></div>
        <button type="button" onclick="validation_formulaire()" >Soumettre la demande de créneaux</button>
    </footer>
    </form>
</body>
</html>
