<?php
  session_start();
  if (!isset($_SESSION["id"])) exit();

  /*Récuperation des donnée envoyer*/
  $currentUser = $_SESSION['id'];
  $contactLogin = $_POST['contactLogin'];
  $message = $_POST['message'];

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
    if ($currentUser == $userLine[$i][0]) {
      $currentUserLogin = $userLine[$i][10];
    }
  }
  /*Si le login entré n'a pas été trouvé dans le fichier utilisateur*/
  if (!$exist) {
    echo "Erreur: Le login n'existe pas !";
    exit();
  }

  /*Ouverture du fichier tchat*/
  if ($file = fopen('../membres/data/tchat.csv', "a+")){
    /*Censures des insultes*/
    $count = 0;
    $message = preg_replace(
        '.\b(?:' . implode('|', array_map('preg_quote', file('../membres/data/censure.txt', FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES))) . ')\b.i',
        '[Censuré]',
        $message, -1, $count
    );

    include '../php/logFunction.php';
    if ($count != 0) addLog("Signalement: L'utilisateur ". $currentUserLogin . " a envoyé un message qui a était censuré.");

    $data = array($currentUser, $contactId, date('h:i'), $message);
    fputcsv($file, $data, ';');
    fclose($file);
    echo "Message envoyer";
  } else {
    echo "Erreur: Le fichier tchat n'a pas pu être ouvert !";
    exit();
  }

 ?>
