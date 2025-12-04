<?php 
    function afficherTheme(PDO $db){
        $sql = "SELECT * FROM ni_themes ORDER BY libelle";

        $statement = $db->prepare($sql);
        $statement->execute();

        while ($row = $statement->fetch()) {
            echo "<p><a href='scene.php?id_theme=".$row['id_theme']."'>".$row['libelle']."</a></p>";
        }

        $statement->closeCursor();
    }


    
?> 