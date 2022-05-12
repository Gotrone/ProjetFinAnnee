<?php
    session_start();
    if (!isset($_POST['choix'])){
        header('Location: ../portail.php');
        exit();
    }

    /*Récupération/Création du fichier contenant les voeux*/
    if ($file = fopen('../membres/data/voeux.csv', "a+")) {
      while (!feof($file)){
        $line[] = fgetcsv($file, 1024, ';');
      }
    } else {
      echo "Erreur : Un problème est survenue avec le fichier csv !";
    }

    /*Formate les choix (si < 8 alors NULL)*/
    $choix = $_POST['choix'];
    $choix = explode(',', $choix);
    $choix_fin = array_fill(count($choix), 8 - count($choix), 'NULL');
    $choix = array_merge($choix, $choix_fin);

    /*Vérifie si l'utilisateur à déjà mis des voeux*/
    $exist = false;
    for ($i=1; $i < count($line) - 1; $i++) {
      /*$line[$i][0] represente la colonne avec tout les ID*/
      if ($line[$i][0] == $_SESSION['id']) {
        $exist = true;
      }
    }

    /*Si l'utilisateur n'a pas déjà mis des voeux on les ajoutent*/
    if (!$exist) {
      /*Met toute les infomation du formulaire dans une liste*/
      $item = array($_SESSION['id'], $choix[0], $choix[1], $choix[2],
                     $choix[3], $choix[4], $choix[5], $choix[6], $choix[7]);

      fputcsv($file, $item, ";"); //Insert les information dans le csv
      fclose($file);

      echo "Voeux ajoutés !";

    } else {
      /*Sinon on les modifient*/

      /*Creation d'un fichier temporaire csv*/
      if(!$tmpFile = fopen('../membres/data/tmpVoeux.csv', 'w+')){
        echo "Erreur: Un problème est survenue avec le fichier temporaire !";
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

      /*Récuperation des informations sur les voeux de l'utilisateur*/
      $newUser[0] = $line[$id_line][0];
      $newUser[1] = $choix[0];
      $newUser[2] = $choix[1];
      $newUser[3] = $choix[2];
      $newUser[4] = $choix[3];
      $newUser[5] = $choix[4];
      $newUser[6] = $choix[5];
      $newUser[7] = $choix[6];
      $newUser[8] = $choix[7];


      fputcsv($tmpFile, $newUser, ";"); //Ajoute l'utilisateur dans le fichier temporaire
      fclose($tmpFile);
      fclose($file);

      unlink('../membres/data/voeux.csv'); //Supprime l'ancien fichier de voeux
      rename('../membres/data/tmpVoeux.csv', '../membres/data/voeux.csv');

      echo "Voeux mis à jours !";
    }
?>
