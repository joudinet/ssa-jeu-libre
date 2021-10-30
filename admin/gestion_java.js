let amodifier=document.getElementById("amodifier")
let nommodif=document.getElementById("nommodif")
let mailmodif=document.getElementById("mailmodif")
let telephonemodif=document.getElementById("telephonemodif")
let buthidden=document.getElementById("buthidden")
let FormulaireBis=document.getElementById("FormulaireBis")
let Formulaire = document.getElementById("Formulaire")
let inputid=document.getElementById("inputid")
let but=document.getElementById("but")

function ajusteDonneesStaff() {
    if (leStaff[amodifier.value]==null) { return }
    nommodif.value=leStaff[amodifier.value]['nom']
    mailmodif.value=leStaff[amodifier.value]['mail']
    telephonemodif.value=leStaff[amodifier.value]['telephone']
}

function suppression() {
    buthidden.value="suppression"
    FormulaireBis.submit()
}

function supprimeDemande(id) {
    inputid.value=id
    but.value="supprime demande"
    Formulaire.submit()
}







let texte=document.getElementById("texte")
let lesinfos=document.getElementById("lesinfos")
let formulairecache=document.getElementById("formulairecache")
let touslescreneaux=document.getElementsByName("creneau[]")

function annuleTerrain(id,leterrain) {
    but.value="annuleterrain"
    inputid.value=id
    texte.value=leterrain
    Formulaire.submit()
}

function ajoutePersonne() {
    but.value="ajoutepersonne"
    Formulaire.submit()
}

function changeAdherent (id) {
    but.value="changeadherent"
    inputid.value=id
    Formulaire.submit()
}

function changeEtat(id,etat) {
    but.value="changeetat"
    inputid.value=id
    texte.value=etat
    Formulaire.submit()
}

function changeTerrain(id,leterrain) {
    but.value="changeterrain"
    inputid.value=id
    texte.value=leterrain
    Formulaire.submit()
}

function creationPDF() {
    but.value="creationpdf"
    Formulaire.submit()
}

function creationEnvoi() {
    but.value="creationenvoi"
    Formulaire.submit()
}

function fermerFormulaireCache() {
    formulairecache.style.display="none"
}

function indiqueCreneauComplet(id) {
    but.value="indiquecreneaucomplet"
    inputid.value=id
    Formulaire.submit()
}

function maj_info(id) { // rendre visible la boite de commentaire pour le joueur id
    while (lesinfos.firstChild) {
        lesinfos.removeChild(lesinfos.lastChild);
    }   
    newNode = document.importNode(document.getElementById(id), true);
    lesinfos.appendChild(newNode)
}

function nouveau(idcreneau,terrain) {
    formulairecache.style.display="block"
    texte.value=terrain
    inputid.value=idcreneau
}

function reinitCreneau(id) {
    but.value="reinitcreneau"
    inputid.value=id
    Formulaire.submit()
}

function supprimeCreneau(id) { 
    if (confirm("Etes-vous sûr de vouloir supprimer le créneau?")) {
	but.value="supprimecreneau"
    	inputid.value=id
    	Formulaire.submit()
    }
}

function valideAvecMail(id) {
    but.value="valideavecmail"
    inputid.value=id
    Formulaire.submit()
}

function valideCreneau(id) {
    but.value="validecreneau"
    inputid.value=id
    Formulaire.submit()
}

function valideFormulaire() {
    Formulaire.submit()
}

function valideFormulaireTous() {
    for (var unebox of touslescreneaux) {
        unebox.checked=true
    }
    Formulaire.submit();
}

