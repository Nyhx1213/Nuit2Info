<section> 
    <?php
        require_once "../connect.php";
        require_once "../fonctions/qcm.php";
    ?>
    <h1> Bienvenu sur le jeu interactive xxx </h1> 
    <p>Voici la liste des themes possibles : </p>
    <?php 
        afficherTheme($db);
    ?> 
</section> 