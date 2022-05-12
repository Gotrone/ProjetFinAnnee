<?php
    session_start();
    if (!isset($_POST['login'])){
        header('Location: ../portail.php');
        exit();
    }

    /*Récupération de toutes les ligne du fichier*/
    if ($file = fopen('../membres/data/utilisateur.csv', "a+")) {
      while (!feof($file)){
        $line[] = fgetcsv($file, 1024, ';');
      }
    }

    /*Cherche la correspondance login/mdp avec les utilisateurs dans la base de donnée*/
    for ($i=1; $i < count($line) - 1; $i++) {
      if ($_POST['login'] == $line[$i][10] && password_verify($_POST['password'], $line[$i][11])){
          /*Set l'id et login de session*/
          $_SESSION['id'] =  $line[$i][0];
          $_SESSION['login'] =  $line[$i][10];

          /*En fonction du status revoie l'utilisateur vers l'accueil correspondant*/
          if ($line[$i][12] == "etudiant") {
            header('Location: ../membres/accueilEtudiant.php');
            exit();
          } else if ($line[$i][12] == "responsable"){
            header('Location: ../membres/accueilResponsable.php');
            exit();
          } else {
            header('Location: ../membres/accueilAdmin.php');
            exit();
          }
      }
    }

    /*Si le login et mot de passe ne sont pas trouver alors retour au portail*/
    session_destroy();
    header('Location: ../portail.php?erreur=mdp');
    exit();
?>
