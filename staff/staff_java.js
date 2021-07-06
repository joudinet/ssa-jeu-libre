let LeFormulaire=document.getElementById("le_formulaire_index")


window.onload = function () {
    leschecked()
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
	    if (screen.width<920) {
	        tablabel[i].innerHTML="Gérer"
	    }
    }
}

function click_creneau(id) { 
    leschecked()
 }
 
 function validation_formulaire() {
    LeFormulaire.submit()
}