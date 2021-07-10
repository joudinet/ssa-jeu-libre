


<?php
require "header.php";
?>

<div class="coordonneesstaff">

<?php
ouvre_bdd();
try {
    foreach ($dbh->query('SELECT * FROM STAFF ORDER BY nom') as $row) {
        echo "<DIV> ".secu_ecran($row['nom'])." </DIV>";
        echo "<DIV> ".secu_ecran($row['telephone'])." </DIV>";
    }
} catch (Exception $e) {
    echo "Erreur dans la base de données staff";
}

ferme_bdd();
?>

</div>
    
</body>
</html>