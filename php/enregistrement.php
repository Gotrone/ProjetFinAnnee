<?php
    if (!isset($_POST["submit"])){
        header('Location:../portail.php');
    }

    include '../php/logFunction.php';

    /*Récupération de toutes les ligne du fichier*/
    if ($file = fopen('../membres/data/utilisateur.csv', "a+")) {
      while (!feof($file)){
        $line[] = fgetcsv($file, 1024, ';');
      }
    }

    /*Vérifie si le login existe déjà*/
    $exist = false;
    for ($i=1; $i < count($line) - 1; $i++) {
      /*$line[$i][10] represente la colonne avec tout les login*/
      if ($line[$i][10] == $_POST['login']) {
        $exist = true;
      }
    }

    $id = count($line) - 2; //Creer un identifiant unique pour le nouvel utilisateur

    /*Si l'utilisateur $_POST['login'] n'est pas déjà inscrit*/
    if (!$exist) {
      if ($_POST['status'] == "etudiant") {
        /*Si l'utilisateur est inscrit à partir de la page étudiante*/
        $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
        //Met toute les infomation du formulaire dans une liste
        $etudiant = array($id, $_POST['nom'], $_POST['prenom'], $_POST['genre'],
                       $_POST['dateNaissance'], $_POST['codePostal'], $_POST['telephone'],
                       $_POST['parcours'], $_POST['ects'], $_POST['moyenne'], $_POST['login'],
                       $pass, $_POST['status']);
        //Insert les information dans le csv
        fputcsv($file, $etudiant, ";");
      } else {
        /*Si l'utilisateur est inscrit à partir de la page responsable admission*/
        $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
        //Met toute les infomation du formulaire dans une liste
        $resp = array($id, $_POST['nom'], $_POST['prenom'], $_POST['genre'],
                       $_POST['dateNaissance'], $_POST['codePostal'], $_POST['telephone'],
                       'NULL', 'NULL', 'NULL', $_POST['login'], $pass, $_POST['status']);
        //Insert les information dans le csv
        fputcsv($file, $resp, ";");

      }
      echo "Inscription réussit";
      fclose($file);
      /*Ajoute un log*/
      addLog("Inscription: Nouveau utilisateur: login: " . $_POST['login'] . " Status: " . $_POST['status']);
  } else {
      fclose($file);
      if ($_POST['status'] == "etudiant") header('Location: inscriptionEtudiant.php?erreur=alreadyExist');
      else header('Location: inscriptionRA.php?erreur=alreadyExist');
      exit();
  }
 ?>

<!DOCTYPE html>
<html lang="fr" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Confirmation</title>
  </head>
  <body>
      <a href="../portail.php">Retour au portail</a>
  </body>
</html>
