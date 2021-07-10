let footer=document.getElementById("footer")
let infoindex=document.getElementById("infoIndex")
let LeFormulaire=document.getElementById("le_formulaire_index")

window.onload = function () {
    if (leschecked()) {
        footer.style.display="block"
        infoindex.style.display="block"
    }
}

function leschecked() {
    au_moins_un=false
    tabinput=document.getElementById('creneauxdispos').getElementsByTagName("input")
    tablabel=document.getElementById('creneauxdispos').getElementsByTagName("label")
    for (i=0;i<tabinput.length;i++) {
	    if (tabinput[i].checked) {
	        tablabel[i].style.backgroundColor="#FAB315"
	        tablabel[i].style.color="black"
            au_moins_un=true
	    } else {
	        tablabel[i].style.backgroundColor="#2687c9"
	        tablabel[i].style.color="white"
	    }
    }
    return au_moins_un
}

function click_creneau(id) {
    if (leschecked()) {
        footer.style.display="block"
        infoindex.style.display="block"
    } else {
        footer.style.display="none"
        infoindex.style.display="none"
    }
}

function validation_formulaire() {
    if (nom.value=="" || prenom.value=="" ||  mail.value=="") {  // telephone.value=="" ||
	alert('Le formulaire est incomplet')
	return
    }
    if (!consignergpd.checked) {
	alert('Il faut autoriser Sand System à sauvegarder les données !')
	return
    }
    LeFormulaire.submit()
}



/*let LeFormulaire=document.getElementById("le_formulaire_index")
let consignesecurite=document.getElementById("consignesecurite")
let consignergpd=document.getElementById("consignergpd")
let adherent=document.getElementsByName("adherent")
let niveau=document.getElementsByName("niveau")
let jeu1=document.getElementById("jeu1")
let jeu2=document.getElementById("jeu2")
let jeu3=document.getElementById("jeu3")
let jeu4=document.getElementById("jeu4")
let jeul1=document.getElementById("jeul1")
let jeul2=document.getElementById("jeul2")
let jeul3=document.getElementById("jeul3")
let jeul4=document.getElementById("jeul4")
let ad1=document.getElementById("ad1")
let ad2=document.getElementById("ad2")
let adl1=document.getElementById("adl1")
let adl2=document.getElementById("adl2")
let adhesion=document.getElementById("adherer")
let nom=document.getElementById("nom")
let prenom=document.getElementById("prenom")
let mail=document.getElementById("mail")
let telephone=document.getElementById("telephone")



function test_feminin() {
    tab=document.getElementsByClassName("checkboxfeminin")
    res=false
    for (var unebox of tab) {
	res=res || unebox.checked
    }
    return res
}

function test_masculin() {
    tab=document.getElementsByClassName("checkboxmasculin")
    res=false
    for (var unebox of tab) {
	res=res || unebox.checked
    }
    return res
}

function lesradio() {
   jeul1.style.backgroundColor="#2687c9"
   jeul2.style.backgroundColor="#2687c9"
   jeul3.style.backgroundColor="#2687c9"
   jeul4.style.backgroundColor="#2687c9"
   adl1.style.backgroundColor="#2687c9"
   adl2.style.backgroundColor="#2687c9"
   jeul1.style.color="white"
   jeul2.style.color="white"
   jeul3.style.color="white"
   jeul4.style.color="white"
   adl1.style.color="white"
   adl2.style.color="white"
   if (jeu1.checked) { jeul1.style.backgroundColor="#FAB315"; jeul1.style.color="black" }
   if (jeu2.checked) { jeul2.style.backgroundColor="#FAB315"; jeul2.style.color="black"  }
   if (jeu3.checked) { jeul3.style.backgroundColor="#FAB315"; jeul3.style.color="black" }
   if (jeu4.checked) { jeul4.style.backgroundColor="#FAB315"; jeul4.style.color="black" }
   if (ad1.checked) { adl1.style.backgroundColor="#FAB315"; adl1.style.color="black" }
   if (ad2.checked) { adl2.style.backgroundColor="#FAB315"; adl2.style.color="black" }
}

function leschecked() {
    tabinput=document.getElementById('creneauxdispos').getElementsByTagName("input")
    tablabel=document.getElementById('creneauxdispos').getElementsByTagName("label")
    for (i=0;i<tabinput.length;i++) {
	if (tabinput[i].checked) {
	    tablabel[i].style.backgroundColor="#FAB315"
	    tablabel[i].style.color="black"
	} else {
	    tablabel[i].style.backgroundColor="#2687c9"
	    tablabel[i].style.color="white"
	}
    }
}

//jeu1.onclick= function () {lesradio()}

//jeu2.onclick= function () {lesradio()}

//jeu3.onclick= function () {lesradio()}

//jeu4.onclick= function () {lesradio()}

ad1.onclick= function () {lesradio() ; pour_adherent();}

ad2.onclick= function () {lesradio(); pas_adherent()}

function pour_adherent() {
  adhesion.style.display="none";
}

function pas_adherent() {
  adhesion.style.display="block";
}

function click_creneau(id,type) { // type 0,1,2 : féminin, masculin,mixte       value de l'input : 0,1,2 : défaut, prio, pasprio
    let lesid=[document.getElementById("c"+id.toString()+"feminin")
	       ,document.getElementById("c"+id.toString()+"masculin")
	       ,document.getElementById("c"+id.toString()+"mixte")]
    let lesidlabel=[document.getElementById("lc"+id.toString()+"feminin")
	       ,document.getElementById("lc"+id.toString()+"masculin")
	       ,document.getElementById("lc"+id.toString()+"mixte")]
    if (test_masculin() && test_feminin()) {
	alert('impossible de choisir masculin et féminin en même temps')
	lesid[type].checked=false
    }
    leschecked()
 }

function nbCreneauChecked() {
    tabinput=document.getElementById('creneauxdispos').getElementsByTagName("input")
    val=0
    for (i=0;i<tabinput.length;i++) {
	if (tabinput[i].checked) { val+=1}
    }
    return val
}

function validation_formulaire() {
    if (nom.value=="" || prenom.value=="" ||  mail.value=="") {  // telephone.value=="" ||
	alert('Le formulaire est incomplet')
	return
    }
    //if (!consignesecurite.checked) {
	//alert('Il faut penser à valider les consignes de sécurité !')
	//return
    //}
    if (!consignergpd.checked) {
	alert('Il faut autoriser Sand System à sauvegarder les données !')
	return
    }
    val=nbCreneauChecked()
    if (val == 0) {
	alert('Il faut choisir au moins un créneau')
	return
    }
    bouton_adherent=false
    for (i=0;i<adherent.length;i++) {
	bouton_adherent=bouton_adherent || adherent[i].checked
    }
    //if (!bouton_adherent) {
	//alert('Il faut indiquer si tu es adhérent SandSystem')
	//return
    //}
    bouton_niveau=false
    for (i=0;i<niveau.length;i++) {
	 bouton_niveau=bouton_niveau || niveau[i].checked
    }
    //if (!bouton_niveau) {
	//alert('Il faut indiquer quel est ton niveau')
	//return
    //}
    LeFormulaire.submit()
}*/
