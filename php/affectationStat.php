<?php
  function floatvalue($val){
          $val = str_replace(",",".",$val);
          $val = preg_replace('/\.(?=.*\.)/', '', $val);
          return floatval($val);
  }

  /*Vérifie que le fichier d'affectation existe*/
  if (!file_exists('../membres/data/tmpAffectation.csv')) {
    echo "Erreur: Le fichier tmpAffectation n'a pas été trouvé";
    exit();
  }


  /*Récupération de toutes les ligne du fichier utilisateur*/
  if ($file = fopen('../membres/data/utilisateur.csv', "r")) {
    while (!feof($file)){
      $userLine[] = fgetcsv($file, 1024, ';');
    }
  } else {
    echo "Erreur: Le fichier utilisateur n'a pas été trouvé";
    exit();
  }
  fclose($file);

  /*Récupération de toutes les ligne du fichier voeux*/
  if ($file = fopen('../membres/data/voeux.csv', "r")) {
    while (!feof($file)){
      $voeuxLine[] = fgetcsv($file, 1024, ';');
    }
  } else {
    echo "Erreur: Le fichier de voeux n'a pas été trouvé";
    exit();
  }
  fclose($file);

  /*Récupération de toutes les ligne du fichier voeux*/
  if ($file = fopen('../membres/data/tmpAffectation.csv', "r")) {
    while (!feof($file)){
      $affectationLine[] = fgetcsv($file, 1024, ';');
    }
  } else {
    echo "Erreur: Le fichier de voeux n'a pas été trouvé";
    exit();
  }
  fclose($file);

  if (!isset($_POST['option'])) {
    echo "Erreur: Le serveur n'a pas reçu l'option !";
    exit();
  }

  /*Récupération de tout les id de l'option demandée*/
  for ($i=0; $i < count($affectationLine) - 1; $i++) {
    if (strtolower($_POST['option']) == $affectationLine[$i][0]) {
      for ($j=1; $j < count($affectationLine[$i]); $j++) {
           $optionChoisie['id'][] = $affectationLine[$i][$j];
      }
    }
  }

  if (!isset($optionChoisie)) {
    echo "NULL";
    exit();
  }

  /*Initialisation*/
  $optionChoisie['ectsMoyenne'] = 0;
  $optionChoisie['moyenneTotal'] = 0;
  $optionChoisie['moyenneMin'] = 20;
  $optionChoisie['total'] = count($optionChoisie['id']);
  for ($i=0; $i < 8; $i++) {
    $optionChoisie['repartition'][$i] = 0;
  }

  for ($i=0; $i < count($optionChoisie['id']); $i++) {
    /*Récuperation des informations depuis le fichier utilisateur*/
    for ($j=1; $j < count($userLine) - 1; $j++) {
      if ($optionChoisie['id'][$i] == $userLine[$j][0]) {
        $optionChoisie['nom'][$i] = $userLine[$j][1];
        $optionChoisie['prenom'][$i] = $userLine[$j][2];
        $optionChoisie['ectsMoyenne'] += floatvalue($userLine[$j][8]);
        $optionChoisie['moyenneTotal'] += floatvalue($userLine[$j][9]);
        if ($userLine[$j][9] < $optionChoisie['moyenneMin']) {
          $optionChoisie['moyenneMin'] = $userLine[$j][9];
        }
      }
    }

    /*Correspondance entre numéro de choix et affectation*/
    for ($j=1; $j < count($voeuxLine) - 1; $j++) {
      if ($optionChoisie['id'][$i] == $voeuxLine[$j][0]) {
          for ($h=1; $h < 9; $h++) {
            $pattern = "/\b".trim($voeuxLine[$j][$h]).".*/i";
            if (preg_match($pattern, $_POST['option'])) {
              $optionChoisie['repartition'][$h-1]++;
            }
          }
      }
    }
  }
  $optionChoisie['moyenneTotal'] = $optionChoisie['moyenneTotal'] / $optionChoisie['total'];
  $optionChoisie['ectsMoyenne'] = $optionChoisie['ectsMoyenne'] / $optionChoisie['total'];

  echo json_encode($optionChoisie, JSON_PRETTY_PRINT|JSON_PRESERVE_ZERO_FRACTION);
 ?>
