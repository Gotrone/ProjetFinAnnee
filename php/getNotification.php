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

  /*Creation d'un fichier temporaire csv*/
  if(!$tmpFile = fopen('../membres/data/tmp.csv', 'w+')) exit();

  /*Récuperation des messages echanger entre les deux utilisateurs*/
  for ($i=0; $i < count($line) - 1; $i++) {
    if ($id == $line[$i][0]){
        /*Récupération des notifications de l'utilisateur*/
        $data[] = array($line[$i][0], $line[$i][1]);
    } else {
        /*Insert dans le fichier temporaire les notification qui n'appartiennent
          pas à l'utilisateur*/
        fputcsv($tmpFile, $line[$i], ";");
    }
  }

  fclose($tmpFile);

  unlink('../membres/data/notification.csv');
  rename('../membres/data/tmp.csv', '../membres/data/notification.csv');

  if (isset($data)) echo json_encode($data);

 ?>
