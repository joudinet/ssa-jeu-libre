prérequis : 
-une table déjà créée avec un champs pour le login et le mot de passe.
 s'il y une clef primaire, penser à la mettre en auto incrément si la fonction 
 de création dde ce module est utilisée : 
	ALTER TABLE `latable` MODIFY COLUMN `colonne_id` INT NOT NULL AUTO_INCREMENT;

-première utilisation : 
	mettre à jour le fichier private/config pour la connexino à la base de donnée
	vider la variable admin
	écrire après session_start dans le fichier index.php :   $_SESSION["login"]="admin_tmp";
	 (pour commencer une session et créer le/les comptes admin)
	mettre à jour la variable admin en fonction puis effacer la ligne en plus dans index.php

les fichiers index.php, login.php, gestion.php, deconnexion.php sont un exemple simple d'utilisation

les fonctions : 

-creation_compte($login) : 
	creation d'une entrée dans la base de donnée en indiquant juste le login et en laissant les autre champs
	à la valeur de départ : d'où l'importance que la clef primaire soit en auto-incrément
	à remplacer par une fonction maison si on veut en plus remplir les autre champs.
	Ne fais rien si $login est dans la liste des comptes admin
	*renvoie false en cas d'échec de la création et true sinon

-maj_compte($login,$password):
	modification du mot de passe $login
	Ne fais rien si $login est dans la liste des comptes admin
	*renvoie false en cas d'échec de la modification et true sinon

-ouvre_bdd() et ferme_bdd() :
	Ouverture/fermeture de la base.
	ouvre_bdd renvoie true si l'ouverture est réussi et false sinon

-securise_login($login) :
	renvoie une version filtrée du mot $login
	Cette version doit pouvoir être affichable et être un login acceptable.
	Ici on ne garde que les lettres minuscules/majuscules, les chiffres et les espaces

-function supprime_compte($login)
	supprime le compte $login
	renvoie true si c'est réussi et false sinon

-test_existence_compte($login) : 
	Vérifie si le compte $login apparait dans la base de donnée
	*renvoie vrai si c'est le cas et false sinon (ou en cas d'erreur)

-verifie_compte($login,$password) :
	Vérifie que le login et le mot de passe correspondent dans la base de donnée.
	*renvoie true si c'est le cas et false sinon (ou en cas d'erreur)