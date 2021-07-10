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
			tablabel[i].innerHTML="Présent(e)";
	    } else {
	        tablabel[i].style.backgroundColor="#2687c9"
	        tablabel[i].style.color="white"
			tablabel[i].innerHTML="Absent(e)";
	    }
    }
}

function click_creneau(id) { 
    checkbox=document.getElementById('c'+id.toString())
	label=document.getElementById('cl'+id.toString())
	if (checkbox.checked) {
		label.style.backgroundColor="#FAB315"
		label.style.color="black"
		label.innerHTML="Présent(e)";
	} else {
		label.style.backgroundColor="#2687c9"
		label.style.color="white"
		label.innerHTML="Absent(e)";
	}
 }
 
 function validation_formulaire() {
    LeFormulaire.submit()
}