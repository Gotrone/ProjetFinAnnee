<?php
  function addLog($message){
    /*Vérification du message entrée*/
    if ($message == "") return;

    /*Récupération/Création du fichier contenant les voeux*/
    if ($file = fopen('../membres/data/log.csv', "a+")) {
      while (!feof($file)){
        $line[] = fgetcsv($file, 1024, ';');
      }
    } else {
      echo "Erreur : Un problème est survenue avec le fichier csv !";
      return;
    }

    $data = array(date('h:i'), $message);
    fputcsv($file, $data, ";");
    fclose($file);
  }
 ?>
