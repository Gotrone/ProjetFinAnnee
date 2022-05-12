<?php
  session_start();
  if (!isset($_SESSION["id"])) exit();

  $currentUser = $_SESSION['id'];
  $newContact = $_POST['newContact'];

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

  /*Vérification/Récuperation de l'id de l'utilisateur entré*/
  $exist = false;
  for ($i=0; $i < count($userLine) - 1; $i++) {
    if ($userLine[$i][10] == $newContact) {
        $exist = true;
        $newContactId = $userLine[$i][0];
    }
  }
  /*Si le login entré n'a pas été trouvé dans le fichier utilisateur*/
  if (!$exist) {
    echo "Erreur: Le login entré n'existe pas !";
    exit();
  }
  if ($currentUser == $newContactId) {
    echo "Erreur: Le login entré est le votre !";
    exit();
  }

  /*Récupération de toutes les ligne du fichier tchat*/
  if ($file = fopen('../membres/data/tchat.csv', "r+")) {
    while (!feof($file)){
      $line[] = fgetcsv($file, 1024, ';');
    }
  } else {
    echo "Erreur: Le fichier tchat n'a pas pu être ouvert !";
    exit();
  }

  for ($i=0; $i < count($line) - 1; $i++) {
    if ($line[$i][0] == $currentUser AND $line[$i][1] == $newContactId) {
        echo "Vous avez déjà ajouté ce contact.";
        exit();
    }
  }

  $data = array($currentUser, $newContactId);
  fputcsv($file, $data, ';');
  fclose($file);
 ?>
