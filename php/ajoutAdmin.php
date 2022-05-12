<?php
    /*Récupération de toutes les ligne du fichier*/
    if ($file = fopen('../membres/data/utilisateur.csv', "a+")) {
      while (!feof($file)){
        $line[] = fgetcsv($file, 1024, ';');
      }
    } else {
      echo "Erreur: Un problème est survenue avec le fichier csv !";
      exit(0);
    }

    /*Vérifie si le login existe déjà*/
    for ($i=1; $i < count($line) - 1; $i++) {
      /*$line[$i][10] represente la colonne avec tout les login*/
      if ($line[$i][10] == 'admin') {
        echo "L'administrateur existe déjà !";
        exit(0);
      }
    }
    /*Création du l'admin*/
    $id = count($line) - 2; //Creer un identifiant unique pour le nouvel utilisateur
    $pass = password_hash('admin', PASSWORD_DEFAULT);

    $resp = array($id, 'Suprème', 'Leader', 'NULL','NULL', 'NULL',
                  'NULL', 'NULL', 'NULL', 'NULL', 'admin', $pass, 'admin');

    fputcsv($file, $resp, ";"); //Insert les information dans le csv

    echo "Succès: L'administrateur a été ajouté !";
    fclose($file);
 ?>
