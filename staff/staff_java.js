
function click_creneau(id,action='avecclick') { 
	let label=document.getElementById('cl'+id.toString())
	let nombre=document.getElementById('cn'+id.toString())
	let titre=document.getElementById('ct'+id.toString())
	var formData = new FormData();
	formData.append('id', id);
	formData.append('action',action)
	fetch("maj_bdd.php", {
	  method : 'POST',
	  headers : {
		'Accept': 'application/json, text/plain, */*',
  	  },
  	  body: formData
	}).then(function(res){ return res.json();})
	  .then(function(res){
		statut=res[0]
		nb=parseInt(res[1],10)
		if (isNaN(nb) || nb<-1 || nb>10000) { return }
		if (statut=="oui") { 
			label.style.backgroundColor="#FAB315"
			label.style.color="black"
			label.innerHTML="Présent(e)";
			if (nb==0) {
			   nombre.innerHTML=nb+" personne(s)"
			   titre.className="creneauvide"
			} else if (nb!=-1) {
			   nombre.innerHTML=nb+" personne(s)"
			   titre.className=""
			}   
		} else if (statut=="non") { 
			label.style.backgroundColor="#2687c9"
			label.style.color="white"
			label.innerHTML="Absent(e)";
			if (nb==0) {
			   nombre.innerHTML=nb+" personne(s)"
			   titre.className="creneauvide"
			} else if (nb!=-1) {
			   nombre.innerHTML=nb+" personne(s)"
			   titre.className=""
			}   
		} else if (statut=="sibesoin") { 
			if (action=='avecclick') {
			if (nb==0) {
				alert('Tu es le seul staffeur sur ce créneau. Es-tu sûr de vouloir te désinscrire du créneau ? N’oublie pas de prévenir de ton absence sur le groupe What’s App du staff pour trouver un-e remplaçant-e.')
				mail_creneau_vide(titre.innerHTML)
			} else {
				//alert('Es-tu sûr de vouloir te désinscrire du créneau ? Être plusieurs staffeurs facilite la gestion du créneau.')
			} }
			label.style.backgroundColor="#2020c9"
			label.style.color="white"
			label.innerHTML="Si besoin";
			if (nb=="0") {
			   nombre.innerHTML=nb+" personne(s)"
			   titre.className="creneauvide"
			} else if (nb!="-1") {
			   nombre.innerHTML=nb+" personne(s)"
			   titre.className=""
			}   
		} 
 	  })
  	  .catch(function(err){ console.log('Request failed', err);});
}

function mail_creneau_vide(titre) {
	var formData = new FormData();
	formData.append('titre', titre);
	fetch("vide.php", {
	  method : 'POST',
	  headers : {
		'Accept': 'application/json, text/plain, */*',
  	  },
  	  body: formData
	}).then(function(res){ return res.json();})
	  .then(function(res){ })	  
}
