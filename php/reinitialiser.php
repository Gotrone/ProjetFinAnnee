<?php
    /**/
    if(file_exists('../membres/data/utilisateur.csv')) unlink('../membres/data/utilisateur.csv');
    /*Création du fichier utilisateur*/
    if ($file = fopen('../membres/data/utilisateur.csv', "w+")) {
      /*Ecriture des legendes*/
      $legendes = array('id', 'nom','prenom', 'genre','dateNaissance',
                        'codePostal', 'telephone','parcours', 'ects',
                        'moyenne', 'login', 'password', 'status');

      fputcsv($file, $legendes, ";"); //Insert les informations dans le csv
      fclose($file);
      echo "Succès: Fichier utilisateur réinitialiser <br>";
    } else {
      echo "Erreur: Impossible de recrée le fichier";
    }

    /**/
    if(file_exists('../membres/data/voeux.csv')) unlink('../membres/data/voeux.csv');
    /*Création du fichier utilisateur*/
    if ($file = fopen('../membres/data/voeux.csv', "w+")) {
      /*Ecriture des legendes*/
      $legendes = array('id', 'choix 1', 'choix 2', 'choix 3','choix 4',
                        'choix 5', 'choix 6', 'choix 7', 'choix 8');

      fputcsv($file, $legendes, ";"); //Insert les informations dans le csv
      fclose($file);
      echo "Succès: Fichier voeux réinitialiser";
    } else {
      echo "Erreur: Impossible de recrée le fichier";
    }

    /**/
    if(file_exists('../membres/data/tmpAffectation.csv')) unlink('../membres/data/tmpAffectation.csv');
 ?>
