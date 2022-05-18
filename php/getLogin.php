<?php
  session_start();
  if (!isset($_SESSION["id"]) OR !isset($_POST['type'])) exit();

  $type = $_POST['type'];

  /*Récupération de toutes les ligne du fichier utilisateur*/
  if ($userFile = fopen('../membres/data/utilisateur.csv', "r")) {
    while (!feof($userFile)){
      $userLine[] = fgetcsv($userFile, 1024, ';');
    }
  } else {
    echo "Erreur: Le fichier utilisateur n'a pas pu être ouvert !";
    exit();
  }
  fclose($userFile);

  /*Récuperation de tout les logins en function du type*/
  if ($type == 0) {
    for ($i=1; $i < count($userLine) - 1; $i++) {
      if (isset($userLine[$i][10]) AND !empty($userLine[$i][10])) {
        $data[] = $userLine[$i][10];
      }
    }
  } else if ($type == 1) {
    for ($i=1; $i < count($userLine) - 1; $i++) {
      if (isset($userLine[$i][10]) AND !empty($userLine[$i][10]) AND trim($userLine[$i][12]) != "admin") {
        $data[] = $userLine[$i][10];
      }
    }
  } else if ($type == 2) {
    for ($i=1; $i < count($userLine) - 1; $i++) {
      if (isset($userLine[$i][10]) AND !empty($userLine[$i][10]) AND trim($userLine[$i][12]) == "etudiant") {
        $data[] = $userLine[$i][10];
      }
    }
  }


  if (isset($data)) echo json_encode($data);
 ?>
