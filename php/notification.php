<?php
  /*DEBUG A TESTER*/

  function addNotification($id, $message){
    /*Vérification du message entrée*/
    if (empty($id) OR empty($message)) return;

    /*Récupération/Création du fichier contenant les voeux*/
    if ($file = fopen('../membres/data/notification.csv', "a+")) {
    } else {
      echo "Erreur : Un problème est survenue avec le fichier csv !";
      return;
    }

    $data = array($id, $message);
    fputcsv($file, $data, ";");
    fclose($file);
  }
 ?>
