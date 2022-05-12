<?php
  session_start();
  if (!isset($_SESSION["id"])) exit();

  /*Récupération/Création du fichier contenant les voeux*/
  if ($file = fopen('../membres/data/log.csv', "r")) {
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
    if (isset($line[$i][0]) AND !empty($line[$i][0]) AND isset($line[$i][1]) AND !empty($line[$i][1])){
        $data[] = array($line[$i][0], $line[$i][1]);
    }
  }
  if (isset($data)) echo json_encode($data);
 ?>
