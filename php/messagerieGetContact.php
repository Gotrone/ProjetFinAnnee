<?php
  session_start();
  if (!isset($_SESSION["id"])) exit();

  $currentUser = $_SESSION['id'];

  /*Vérifie que le fichier de tchat existe*/
  if (!file_exists('../membres/data/tchat.csv')) exit();

  /*Récupération de toutes les ligne du fichier tchat*/
  if ($file = fopen('../membres/data/tchat.csv', "r")) {
    while (!feof($file)){
      $line[] = fgetcsv($file, 1024, ';');
    }
  } else {
    echo "Erreur: Le fichier utilisateur n'a pas pu être ouvert !";
    exit();
  }
  fclose($file);

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

  /*Récuperation de l'ensemble des message/contact de l'utilisateur*/
  for ($i=0; $i < count($line) - 1; $i++) {
    if (isset($line[$i])) {
      if ($line[$i][0] == $currentUser) {
        $contactLine[] = $line[$i][1];
      } else if ($line[$i][1] == $currentUser) {
        $contactLine[] = $line[$i][0];
      }
    }
  }
  /*Si l'utilisateur n'a pas de contact*/
  if (!isset($contactLine)) exit();


  /*Supprimme les doublons*/
  $contactLine = array_values(array_unique($contactLine));

  /*Récuperation des login a partir des ID récuperés*/
  for ($i=0; $i < count($contactLine); $i++) {
    for ($j=1; $j < count($userLine) - 1; $j++) {
      if ($contactLine[$i] == $userLine[$j][0]) {
        $contactLogin[] = $userLine[$j][10];
      }
    }
  }

  if (isset($contactLogin)) echo json_encode($contactLogin);
 ?>
