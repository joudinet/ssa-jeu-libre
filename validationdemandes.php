
<?php require "header.php"; 
ouvre_bdd();
$les_creneaux=lire_les_creneaux();
if (isset($_POST['creneau'])) {
    $les_creneaux_demandes=recupere_liste_creneaux_demandes();
    if (isset($_POST['but'])) { // test inutile car défini si creneau l'est?
        $id=intval($_POST['inputid']);
        if ($id<1) { echo "erreur dans la base de données"; die(); }
        if ($_POST['but']=="supprimeattente") {
            supprimeattente($id);
        } elseif ($_POST['but']=="reinitcreneau") {
            reinitialise_creneau($id);
        } elseif ($_POST['but']=="reinittout") {
            reinitialise_tout($id);
        } elseif ($_POST['but']=="validecreneau") {
            valide_creneau($id);
        } elseif ($_POST['but']=="validetout") {
            valide_tout($id);
        } elseif ($_POST['but']=="changeetat") {
            change_etat($id);
        } elseif ($_POST['but']=="changeterrain") {
            change_terrain($id);
        } elseif ($_POST['but']=="changeadherent") {
            change_adherent($id);
        } elseif ($_POST['but']=="ajoutepersonne") {
            ajoute_personne($id);
        }
    }
    $stmt = $dbh->prepare("SELECT * FROM RESULTAT WHERE idcreneau=? AND terrain=? AND etat=?");
    $stmt->bindParam(1, $idcreneau);
    $stmt->bindParam(2, $terrain);
    $stmt->bindParam(3, $etat);
?>
    <DIV class="containeurdemandesgeneral"> 
    <DIV class="containeurdemandes">
<?php
    foreach ($les_creneaux_demandes as $idcreneau) {
        $le_creneau=trouveCreneau($idcreneau);
        echo '<DIV class="unjourtitre"> Créneau du '.secu_ecran(jolie_date($le_creneau['date'])).' sur la période : '.secu_ecran($le_creneau['heure']);
        echo ' <BUTTON onclick="reinitCreneau('.$idcreneau.')">Remets toutes les personnes en attente</BUTTON>';
        echo ' <BUTTON onclick="valideCreneau('.$idcreneau.')">Valider les personnes en attente</BUTTON>';
        echo '</DIV>'; ?>
        <DIV class="unjour">
 <?php
        foreach(['1','2','3','4'] as $numero_terrain) {
            $terrain='T'.$numero_terrain;?>
            <TABLE>
                <TR><TH style="background-color:<?php echo $le_creneau['C'.$numero_terrain];?>;"><?php echo $terrain.' : '.$le_creneau[$terrain]; ?></TH></TR>
<?php if (in_array($le_creneau[$terrain],['feminin','mixte','masculin'])) { ?>
<TR><TD><BUTTON type="button" onclick="nouveau(<?php echo $idcreneau.',\''.$terrain.'\''?>)" >Ajouter une personne </BUTTON></TD></TR>
<?php } else { ?>
<TR><TD>Créneau sans ajout</TD></TR>
<?php  }        $etat="valide";
                $stmt->execute();
                $nbvalides=0;
                while ($row=$stmt->fetch()) { $nbvalides+=1;?>
                    <TR><TD onclick="this.children[0].style.display='block'" style="color : <?php if ($row['adherent']=='non') { echo "darkorange"; } else { echo "blue";} ?>;" onMouseOver="maj_info('info<?php echo secu_ecran_int($row['id']); ?>')">
<DIV  class="formulairecache">
    <DIV>        Déplacer <?php echo secu_ecran($row['prenom']).' '.secu_ecran($row['nom']); ?> sur 
<?php               foreach(['T1','T2','T3','T4'] as $tmp) {
                        if ($tmp!=$terrain && in_array($le_creneau[$tmp],['feminin','mixte','masculin'])) {
                             echo "<BUTTON type='button' onclick='event.stopPropagation();changeTerrain(".secu_ecran_int($row['id']).",\"".$tmp."\")'>".$tmp."</BUTTON>";
                        }
                    } ?>
                  
                 <BUTTON type="button" onclick="event.stopPropagation();changeEtat(<?php echo secu_ecran_int($row['id'])?> ,'attente')">Mettre cette personne en attente</BUTTON>
                 <button type="button"  onclick="event.stopPropagation();changeAdherent(<?php echo secu_ecran_int($row['id']);?>)">Changer le statut adhérent</button>
                 <BUTTON type="button" onclick="event.stopPropagation();this.parentNode.parentNode.style.display='none'"> Fermer cette fenêtre</BUTTON>
    </DIV>  
</DIV>                        <?php echo secu_ecran($row['prenom']).' '.secu_ecran($row['nom']); ?>
                    <button type="button" class="petitboutonmodif bgred" onclick="event.stopPropagation();changeEtat(<?php echo secu_ecran_int($row['id']); ?>,'supprime')"> X </button>
                    </TD></TR>
                    
              <DIV hidden><DIV id="info<?php echo secu_ecran_int($row['id']); ?>">   <BR> 
                <DIV> Nom : <?php echo secu_ecran($row['nom']); ?></DIV>
                <DIV> Prénom : <?php echo secu_ecran($row['prenom']); ?></DIV>
                <DIV> Mail : <?php echo secu_ecran($row['mail']); ?></DIV>
                <DIV> Telephone : <?php echo secu_ecran($row['telephone']); ?></DIV>
                <DIV> Niveau : <?php echo secu_ecran($row['niveau']); ?></DIV>
                <DIV> Adhérent : <?php echo secu_ecran($row['adherent']); ?></DIV>
                <DIV> Commentaire/partenaire :</DIV>
                <DIV> <TEXTAREA rows="4" > <?php echo secu_ecran($row['commentaire']); ?></TEXTAREA> </DIV>
              </DIV> </DIV>
<?php           }
                while ($nbvalides<$le_creneau['VMAX'.$numero_terrain]) {$nbvalides++; echo "<TR><TD style='color: white;'> _ </TD></TR>";}?>

<?php           $etat="attente";
                $stmt->execute();
                while ($row=$stmt->fetch()) { ?>
                    <TR><TD onclick="this.children[0].style.display='block'" style="background-color : lightgray;color : <?php if ($row['adherent']=='non') { echo "darkorange"; } else { echo "blue";} ?>;" onMouseOver="maj_info('info<?php echo secu_ecran_int($row['id']); ?>')">
<DIV  class="formulairecache">
    <DIV>        Déplacer <?php echo secu_ecran($row['prenom']).' '.secu_ecran($row['nom']); ?> sur 
<?php               foreach(['T1','T2','T3','T4'] as $tmp) {
                        if ($tmp!=$terrain && in_array($le_creneau[$tmp],['feminin','mixte','masculin'])) {
                             echo "<BUTTON type='button' onclick='event.stopPropagation();changeTerrain(".secu_ecran_int($row['id']).",\"".$tmp."\")'>".$tmp."</BUTTON>";
                        }
                    } ?>
                 <button type="button" onclick="event.stopPropagation();changeAdherent(<?php echo secu_ecran_int($row['id']);?>)">Changer le statut adhérent</button>
                 <BUTTON type="button" onclick="event.stopPropagation();this.parentNode.parentNode.style.display='none'"> Fermer cette fenêtre</BUTTON>
    </DIV>  
</DIV>                        <?php echo secu_ecran($row['prenom']).' '.secu_ecran($row['nom']); ?>
                    <button type="button" class="petitboutonmodif bggreen" onclick="event.stopPropagation();changeEtat(<?php echo secu_ecran_int($row['id']); ?>,'valide')"> V </button>
                    <button type="button" class="petitboutonmodif bgred" onclick="event.stopPropagation();changeEtat(<?php echo secu_ecran_int($row['id']); ?>,'supprime')"> X </button>
                    </TD></TR>
                    
              <DIV hidden><DIV id="info<?php echo secu_ecran_int($row['id']); ?>">   <BR> 
                <DIV> Nom : <?php echo secu_ecran($row['nom']); ?></DIV>
                <DIV> Prénom : <?php echo secu_ecran($row['prenom']); ?></DIV>
                <DIV> Mail : <?php echo secu_ecran($row['mail']); ?></DIV>
                <DIV> Telephone : <?php echo secu_ecran($row['telephone']); ?></DIV>
                <DIV> Niveau : <?php echo secu_ecran($row['niveau']); ?></DIV>
                <DIV> Adhérent : <?php echo secu_ecran($row['adherent']); ?></DIV>
                <DIV> Commentaire/partenaire :</DIV>
                <DIV> <TEXTAREA rows="4" > <?php echo secu_ecran($row['commentaire']); ?></TEXTAREA> </DIV>
              </DIV> </DIV>
<?php           }?>

<?php           $etat="supprime";
                $stmt->execute();
                while ($row=$stmt->fetch()) { ?>
                    <TR><TD onclick="this.children[0].style.display='block'" style="background-color : #838383;color : #AF0000;" onMouseOver="maj_info('info<?php echo secu_ecran_int($row['id']); ?>')">
<DIV  class="formulairecache">
    <DIV>        <button type="button" class="bggreen" onclick="event.stopPropagation();changeEtat(<?php echo secu_ecran_int($row['id']); ?>,'valide')"> Remettre <?php echo secu_ecran($row['prenom']).' '.secu_ecran($row['nom']); ?> sur le créneau </button>
                 <BUTTON type="button" onclick="event.stopPropagation();this.parentNode.parentNode.style.display='none'"> Fermer cette fenêtre</BUTTON>
    </DIV>  
</DIV>                        <?php echo secu_ecran($row['prenom']).' '.secu_ecran($row['nom']); ?>
                    </TD></TR>
                    
              <DIV hidden><DIV id="info<?php echo secu_ecran_int($row['id']); ?>">   <BR> 
                <DIV> Nom : <?php echo secu_ecran($row['nom']); ?></DIV>
                <DIV> Prénom : <?php echo secu_ecran($row['prenom']); ?></DIV>
                <DIV> Mail : <?php echo secu_ecran($row['mail']); ?></DIV>
                <DIV> Telephone : <?php echo secu_ecran($row['telephone']); ?></DIV>
                <DIV> Niveau : <?php echo secu_ecran($row['niveau']); ?></DIV>
                <DIV> Adhérent : <?php echo secu_ecran($row['adherent']); ?></DIV>
                <DIV> Commentaire/partenaire :</DIV>
                <DIV> <TEXTAREA rows="4" > <?php echo secu_ecran($row['commentaire']); ?></TEXTAREA> </DIV>
              </DIV> </DIV>
<?php           }?>

            </TABLE>
<?php       } ?>
        </DIV>
<?php
    } ?>
    </DIV>
    <DIV class="containeurinfo">
        <BR><BR>
        <DIV>
            <DIV>En <SPAN style="color: darkorange;">orange</SPAN> : les personnes  non adhérentes</DIV>
            <DIV>En <SPAN style="color: red;">rouge</SPAN> : les personnes supprimées</DIV>
            <DIV><button type="button" class="petitboutonmodif bgred">X</button> pour supprimer une personne</DIV>
            <DIV><button type="button" class="petitboutonmodif bggreen">V</button> pour valider une personne</DIV>
                 <BR><BR><BR>
            <DIV>Information sur la personne actuelle : </DIV>
            <DIV id="lesinfos">
              <DIV>    <BR>
                <DIV> Nom : <LABEL id="infonom"></LABEL></DIV>
                <DIV> Prénom :<LABEL id="infoprenom"></LABEL></DIV>
                <DIV> Mail : <LABEL id="infomail"></LABEL></DIV>
                <DIV> Telephone :<LABEL id="infotelephone"></LABEL></DIV>
                <DIV> Niveau :<LABEL id="infoniveau"></LABEL></DIV>
                <DIV> Adhérent :<LABEL id="infoadherent"></LABEL></DIV>
                <DIV> Commentaire/partenaire :</DIV>
                <DIV> <LABEL id="infocomm"></LABEL> </DIV>
              </DIV>
            </DIV>
        </DIV>
    </DIV>
    </DIV>
    
<?php    
}
ferme_bdd();
?>    
    <BR><BR> 
    <FORM id="Formulaire" method="post" action="validationdemandes.php">
<?php
    foreach ($les_creneaux as $un_creneau) {
        $id=$un_creneau['id'];
        echo '<DIV><INPUT type="checkbox" name="creneau[]" id="c'.$id.'" value="'.$id.'"';
        if (isset($_POST['creneau']) && in_array($id,$les_creneaux_demandes)) { echo ' checked';}
        echo '> <LABEL for="c'.$id.'"> '.secu_ecran(jolie_date($un_creneau['date'])).', '.secu_ecran($un_creneau['heure']).'</LABEL></DIV>';
    }
?>
    <input hidden id="inputid" name="inputid" value="1">
    <input hidden id="but" name="but" value="">
    <input hidden id="texte" name="texte" value="">
    </FORM><BR>
    <BUTTON onclick="valideFormulaire()">Afficher les demandes des joueurs pour ces créneaux</BUTTON>
    
    
            <DIV id="formulairecache" class="formulairecache">
                
                <DIV>
                <legend>Informations sur la personne à ajouter :</legend>
                <DIV class="item_formulaire_index">
                    <LABEL> Nom : </LABEL>
                    <INPUT id="nomcache" name="nomcache" form="Formulaire">
                </DIV>
                <DIV class="item_formulaire_index">
                    <LABEL> Prénom : </LABEL>
                    <INPUT id="prenomcache" name="prenomcache" form="Formulaire">
                </DIV>
                <DIV class="item_formulaire_index">
                    <LABEL> Mail : </LABEL>
                    <INPUT type="email" id="mailcache" name="mailcache" form="Formulaire">
                </DIV>
                <DIV class="item_formulaire_index">
                    <LABEL> Téléphone : </LABEL>
                    <INPUT id="telephonecache" name="telephonecache" form="Formulaire">
                </DIV>
                <DIV class="item_formulaire_index">
                    <LABEL> Niveau de jeu  : </LABEL>
                    <INPUT type="radio" id="iddeb" name="niveaucache" value="debutant" form="Formulaire"> <LABEL for="iddeb"> débutant</LABEL>
                    <INPUT type="radio" id="idint" name="niveaucache" value="intermediaire" form="Formulaire"> <LABEL for="idint"> intermédiaire</LABEL>
                    <INPUT type="radio" id="idcon" name="niveaucache" value="confirme" form="Formulaire"> <LABEL for="idcon"> confirmé</LABEL>
                    <INPUT type="radio" id="idexp" name="niveaucache" value="expert" form="Formulaire" checked> <LABEL for="idexp"> expert</LABEL>
                </DIV>
                <DIV class="item_formulaire_index">
                    <LABEL> adhérent(e) SandSystem : </LABEL>
                    <INPUT type="radio" id="idoui" name="adherentcache" value="oui" form="Formulaire" checked> <LABEL for="idoui"> oui</LABEL>
                    <INPUT type="radio" id="idnon" name="adherentcache" value="non" form="Formulaire"> <LABEL for="idnon"> non</LABEL>
                </DIV>
                <BUTTON type="button" onclick="ajoutePersonne()"> Ajouter cette personne au créneau</BUTTON>
                <BUTTON type="button" onclick="fermerFormulaireCache()">Fermer cette fenêtre</BUTTON>
                </DIV>
            </DIV>
    
    
    
</BODY>
<SCRIPT src="gestion_java.js?=<?php echo time(); //pour forcer le js a ne pas être en cache et jouer des tours en dev ?>">  </SCRIPT>
<SCRIPT> 

</SCRIPT>
</HTML>