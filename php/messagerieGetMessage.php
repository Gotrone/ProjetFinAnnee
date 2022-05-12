<?php
  session_start();
  if (!isset($_SESSION["id"])) exit();

  $currentUser = $_SESSION['id'];
  $contactLogin = $_POST['contactLogin'];

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

  /*Vérification/Récuperation de l'id du contact*/
  $exist = false;
  for ($i=0; $i < count($userLine) - 1; $i++) {
    if ($userLine[$i][10] == $contactLogin) {
        $exist = true;
        $contactId = $userLine[$i][0];
    }
  }
  /*Si le login entré n'a pas été trouvé dans le fichier utilisateur*/
  if (!$exist) {
    echo "Erreur: Impossible de récuperer les messages le login n'existe pas !";
    exit();
  }

  /*Récupération de toutes les ligne du fichier tchat*/
  if ($file = fopen('../membres/data/tchat.csv', "r")) {
    while (!feof($file)){
      $line[] = fgetcsv($file, 1024, ';');
    }
  } else {
    echo "Erreur: Le fichier tchat n'a pas pu être ouvert !";
    exit();
  }
  fclose($file);

  /*Récuperation des messages echanger entre les deux utilisateurs*/
  for ($i=0; $i < count($line) - 1; $i++) {
    if (($line[$i][0] == $currentUser AND $line[$i][1] == $contactId) OR
        ($line[$i][0] == $contactId AND $line[$i][1] == $currentUser)) {
      if (isset($line[$i][2]) AND isset($line[$i][3]) AND !empty($line[$i][3])) {
        $data[] = array($line[$i][1], $line[$i][2], $line[$i][3]);
      }
    }
  }

  if (isset($data)) echo json_encode($data);
 ?>
