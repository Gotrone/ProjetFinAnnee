<?php
  session_start();
  if (!isset($_SESSION["id"])) exit();
  $id = $_SESSION["id"];

  /*Récupération/Création du fichier contenant les voeux*/
  if(!file_exists('../membres/data/notification.csv')) return;
  if ($file = fopen('../membres/data/notification.csv', "r")) {
    while (!feof($file)){
      $line[] = fgetcsv($file, 1024, ';');
    }
  } else {
    echo "Erreur : Un problème est survenue avec le fichier csv !";
    return;
  }
  fclose($file);

  /*Récuperation des messages echanger entre les deux utilisateurs*/
  for ($i=0; $i < count($line) - 1; $i++) {
    if ($id == $line[$i][0]){
        $data[] = array($line[$i][0], $line[$i][1]);
    }
  }
  if (isset($data)) echo json_encode($data);

 ?>
