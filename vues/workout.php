<?php 
session_start();
require_once "connect.php";
$db = new PDO(DNS, LOGIN, PASSWORD, $options);
include "permission.inc.php";
?>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title> Workout Manager</title> 
        <link rel="stylesheet" href="css.css">
        <link rel="stylesheet" href="pico.min.css">
    </head>
    <body>
    <header>
        <nav>
        <ul>
            <li>
            <?php if(isset($_SESSION['login'])){
                    echo'<a href="profile.php">Welcome '.$_SESSION['login'].'</a>';
                }
            ?>
            </li>
        </ul>
        <ul>
            <li>
                <strong><a href="index.php">Basketball Management Application</a></strong>
            </li>
        </ul>
        <ul>
            <li> 
                <?php if(isset($_SESSION['login'])){
                    echo'<a href="disconnect.php"> Disconnect</a>';
                }
                    else echo '<a href="index.php">Login</a>';
                ?>
            </li>
        </ul>
        </nav>
    </header>
    <main>
        <?php
            try {     
                $sqlE = 'SELECT * FROM Equipe'; //Request Teams
                $statementE = $db->prepare($sqlE); //Statement Team.
                $statementE->execute();      
                
            //FORM 1
                echo
                '<form action="workout.php" method=post class="workout-form">
                    <label for="team"> <h1>Choose a team</h1></label>
                    <select id="team" name="team">';
                        
                    while ($row=$statementE->fetch()){  //While statement to show every team available.
                        echo '<option value="'.$row['EquipeID'].'">'.$row['NomEquipe'].'</option>';

                    }
                    echo'<input type="submit" value="Submit" > </form>';
                        
            //FORM 1 END
                    if (isset($_POST['team'])&&!empty($_POST['team'])) {  // If $_POST team is chosen (I need to add refresh and a lot more conditions.)
                        $sqlcheckteam='SELECT count(EquipeID) as CountTeam FROM Equipe
                                    WHERE EquipeID=:id';
                        $statementcheckteam= $db->prepare($sqlcheckteam);
                        $statementcheckteam->bindParam(':id', $_POST['team']);
                        $statementcheckteam->execute();
                        
                        $rowcheckteam= $statementcheckteam->fetch();

                        if($rowcheckteam['CountTeam']<1){
                            echo'<h2 class=" message error"> An error has occurred with your team input, please try again or contact support.</h2>'; 
                            exit();
                        }
                        else {

                            $sqlM = 'SELECT Membre.Nom, Membre.MembreID FROM Membre INNER JOIN MembresEquipe ON Membre.MembreID = MembresEquipe.MembreID
                        INNER JOIN Equipe ON MembresEquipe.EquipeID = Equipe.EquipeID
                        Where Equipe.EquipeID = :chosID '; //Request that connects the team ID selected to the member ID.
                        $statementM = $db->prepare($sqlM); //Statement Members.
                        $statementM->bindParam(':chosID', $_POST['team']); //Param binding the chosen team value to sqlM.
                        $statementM->execute();//Statement Membre Execution. 
            // FORM 2     
                        echo
                        '<form action="workout.php" method=post class="workout-form">
                            <input type="hidden" value="'.$_POST['team'].'" name="team" id="team">
                            <label for="player"><h1>Choose a player</h1></label>                    
                            <select id="player" name="player">';

                            while ($row=$statementM->fetch()){ //While loop that will go search player names if team is set.
                                echo '<option value="'.$row['MembreID'].'">'.$row['Nom'].'</option>';
                            }
                        echo
                            '<input type="submit" value="Submit" class="button"> </form>';
        
                            $statementM->closeCursor();
                        }
                        }
            // FORM 2 END
                        if (isset($_POST['player'])&&!empty($_POST['player'])) { //If the person chose a player.

                            $sqlcheckplayer='SELECT count(MembreID) as CountMembre FROM Membre
                                            WHERE MembreID = :id';
                            $statementcheckplayer= $db->prepare($sqlcheckplayer);
                            $statementcheckplayer->bindParam(':id',$_POST['player']);
                            $statementcheckplayer->execute();

                            $rowcheckplayer= $statementcheckplayer->fetch();

                            if($rowcheckplayer['CountMembre']<1){
                                echo'<h2 class="message error"> An error with your player input has occurred, please try again or contact support.</h2>';
                                exit();
                            }
                            else {

                                $sqlT = 'SELECT * FROM Entrainement WHERE EntrainementID 
                            NOT IN (SELECT EntrainementID FROM Participer INNER JOIN Membre ON Participer.MembreID = Membre.MembreID 
                                    WHERE Participer.MembreID=:playerID) '; 

            //Above ensures that the program won't pick a player that is already in a workout. 
                            $statementT=$db->prepare($sqlT);
                            $statementT->bindParam(':playerID', $_POST['player']); // Bind the member ID from above.
                            $statementT->execute();
            // FORM 3 
                        echo
                        '<form action="workout.php" method=post class="workout-form">
                            <input type="hidden" value="'.$_POST['team'].'" name="team" id="team">
                            <input type="hidden" value="'.$_POST['player'].'" name="player" id="player"> 
                            <label for="workout"><h1>Choose a workout</h1></label> 
                            <select id="workout" name="workout">';
                            
                            while ($row=$statementT->fetch()) { //Shows the options.
                                echo'<option value="'.$row['EntrainementID'].'">'.$row['TypeEntrainement'].' '.$row['DateEntrainement'].'</option>'; 
                            }
                            echo'<input type="submit" value="Submit" class="button"> </form>';
                            
                            $statementT->closeCursor();
                        }
                        }
                        if (isset($_POST['workout'])&&!empty($_POST['workout'])) {

                            $sqlcheckwork='SELECT count(EntrainementID) as Countres FROM Entrainement
                            WHERE EntrainementID=:id';
                            $statementcheckwork= $db->prepare($sqlcheckwork);
                            $statementcheckwork->bindParam(':id',$_POST['workout']);
                            $statementcheckwork->execute();

                            $rowcheck= $statementcheckwork->fetch();

                        if($rowcheck['Countres']<1){
                            echo'<h2 class="message error">An error has occured with your selected input, please try again or contact support.</h2>';
                            exit();
                    }
                    else {
                        $statementcheckwork->closeCursor();
                    
                            // FORM 3 END
                                $sqlName= 'SELECT Nom, Prenom FROM Membre WHERE MembreID=:id';
                                $statementName= $db->prepare($sqlName);
                                $statementName->bindParam(':id',$_POST['player']);
                                $statementName->execute();
                                
                                $sqlW = 'INSERT INTO Participer(EntrainementID, MembreID) 
                            Values 
                                (:workID, :playerID)';
                            $statementW = $db->prepare($sqlW);
                            $statementW->bindParam('workID', $_POST['workout']);
                            $statementW->bindParam('playerID',$_POST['player']);
                            $statementW->execute();
                            $statementW->closeCursor();
                            $row=$statementName->fetch();
                            echo'<h2 class="message success">'.$row['Prenom'].' '.$row['Nom'].' has been successfully added to the workout!</h2>';   
                        }
                    }
                        //part above inserts values into the database.
                        $statementE->closeCursor();
                        $db=null;
                    }
                    catch (PDOException $e){
                        echo('echec :'.$e->getMessage());
                    }
            ?>

        </main>
    </body>
</html>