<?php
    session_start();
    if (!isset($_POST["submit"])){
        header('Location: ../portail.php');
    }

    /*Récupération de toutes les ligne du fichier*/
    if ($file = fopen('../membres/data/utilisateur.csv', "r")) {
      while (!feof($file)){
        $line[] = fgetcsv($file, 1024, ';');
      }
    }
    fclose($file);

    /*Si on ne trouve pas l'ID dans utilisateur.csv*/
    if (!isset($line[$_SESSION['id']])) {
      header('Location: ../portail.php?erreur=idNotFound');
      exit();
    }


    /*Creation d'un fichier temporaire csv*/
    if(!$tmpFile = fopen('../membres/data/tmp.csv', 'w+')){
      header('Location: editionProfil.php?erreur=tmpCsv');
      exit();
    }

    /*Récuperation de la ligne correspondante à l'id de l'utilisateur*/
    for ($i=1; $i < count($line) - 1; $i++) {
        if ($line[$i][0] == $_SESSION['id']) $id_line = $i;
    }

    /*Insert dans le fichier temporaire les informations de utilisateur.csv
      sans la ligne de l'utilisateur que l'on édite*/
    for ($i=0; $i < count($line) - 1; $i++) {
        if ($i != $id_line) fputcsv($tmpFile, $line[$i], ";");
    }

    /*Récuperation des informations de l'utilisateur*/
    $newUser[0] = $line[$id_line][0];
    $newUser[1] = $line[$id_line][1];
    $newUser[2] = $line[$id_line][2];
    $newUser[3] = $line[$id_line][3];
    $newUser[4] = $line[$id_line][4];
    $newUser[5] = $line[$id_line][5];
    $newUser[6] = $line[$id_line][6];
    $newUser[7] = $line[$id_line][7];
    $newUser[8] = $line[$id_line][8];
    $newUser[9] = $line[$id_line][9];
    $newUser[10] = $line[$id_line][10];
    $newUser[11] = $line[$id_line][11];
    $newUser[12] = $line[$id_line][12];

    /*Si on change de login :*/
    if ($_POST['login'] != $_SESSION['login']) {
      /*Vérifie si le nouveau login n'existe pas déjà dans le fichier*/
      for ($i=1; $i < count($line) - 1; $i++) {
        if ($line[$i][10] == $_POST['login']) {
          header('Location: editionProfil.php?erreur=alreadyExist');
          exit();
        }
      }
      /*Verification que le nouveau login n'est pas vide*/
      if (!empty($_POST['login'])) {
        $_SESSION['login'] = $_POST['login']; //Met à jours la varible de SESSION
        $newUser[10] =  $_POST['login'];
      } else {
        header('Location: editionProfil.php?erreur=emptyLogin');
          exit();
      }
    }


    /*Verification que le nouveau codePostal n'est pas vide*/
    if (isset($_POST['codePostal']) AND !empty($_POST['codePostal'])) {
        $newUser[5] =  $_POST['codePostal'];
    } else {
        header('Location: editionProfil.php?erreur=emptyPostal');
        exit();
    }

    /*Verification que le nouveau telephone n'est pas vide*/
    if (isset($_POST['telephone']) AND !empty($_POST['telephone'])) {
        $newUser[6] =  $_POST['telephone'];
    } else {
        header('Location: editionProfil.php?erreur=emptyPhone');
        exit();
    }

    /*Verification que le nouveau mdp n'est pas vide et correspond au 2eme*/
    if (isset($_POST['mdp']) AND isset($_POST['mdp2']) AND !empty($_POST['mdp']) AND !empty($_POST['mdp2'])){
      if ($_POST['mdp'] == $_POST['mdp2']) {
        $newUser[11] =  password_hash($_POST['mdp'], PASSWORD_DEFAULT);
      } else {
        header('Location: editionProfil.php?erreur=wrongMdp');
        exit();
      }
    }

    /*------------------------------------------------*/
    /*Verification de l'image uploader*/
    if(isset($_FILES['avatar']) AND !empty($_FILES['avatar']['name'])) {
        /*Verification de la taille*/
        $sizeMax = 2097152; //2Mo en octets
        if($_FILES["avatar"]["size"] > 0 AND $_FILES['avatar']['size'] <= $sizeMax) {
            /*Vérification de la validiter de l'extension*/
            $extensionTypes = array('jpg', 'jpeg', 'png'); //Extensions autorisées
            //Récupération de l'extension de l'image
            $extensionUpload = strtolower(substr(strrchr($_FILES['avatar']['name'], '.'), 1));
            if(in_array($extensionUpload, $extensionTypes)) {
                /*Vérification qu'il n'y est pas déjà un fichier de ce nom dans le dossier*/
                $chemin = "../membres/avatars/".$_SESSION['id'].".".$extensionUpload;
                if (!is_file($chemin)) {
                  $resultat = move_uploaded_file($_FILES['avatar']['tmp_name'], $chemin);
                  if (!$resultat) {
                    header('Location: editionProfil.php?erreur=upload?is_file');
                    exit();
                  }
                } else {
                  /*Essaye de supprimmer l'ancienne photo*/
                  if (unlink($chemin)) {
                    $resultat = move_uploaded_file($_FILES['avatar']['tmp_name'], $chemin);
                    if (!$resultat) {
                      header('Location: editionProfil.php?erreur=upload?unlinkUpload');
                      exit();
                    }
                  } else {
                    header('Location: editionProfil.php?erreur=upload?unlink');
                    exit();
                  }
               }
            } else {
              header('Location: editionProfil.php?erreur=type');
              exit();
            }
          } else {
            header('Location: editionProfil.php?erreur=size');
            exit();
        }
    }


    fputcsv($tmpFile, $newUser, ";"); //Ajoute l'utilisateur dans le fichier temporaire
    fclose($tmpFile);

    unlink('../membres/data/utilisateur.csv');
    rename('../membres/data/tmp.csv', '../membres/data/utilisateur.csv');
    header('Location: editionProfil.php?success');

    include '../php/logFunction.php';
    addLog("Modification: Edition du profil: Login:" . $_POST['login'] . " Status:" . $newUser[12]);
    exit();
?>
