<?php
    session_start();

    function floatvalue($val){
        $val = str_replace(",",".",$val);
        $val = preg_replace('/\.(?=.*\.)/', '', $val);
        return floatval($val);
    }

    /*Vérification que la page a été appellée avec ajax*/
    if(empty($_SERVER['HTTP_X_REQUESTED_WITH'])){
        header('Location: ../portail.php');
        exit();
    }

    /*-------DEBUT-TRAITEMENT-NOMBRE-DE-PLACE--------*/

    /*Verification du fichier uploader*/
    if(!isset($_FILES['file']) OR empty($_FILES['file']['name'])) {
      echo "Erreur: Un problème est survenue, le fichier n'a pas pu être ouvert.";
      exit();
    }

    /*Verification de la taille*/
    $sizeMax = 52428800; //50Mo en octets
    if($_FILES["file"]["size"] <= 0 OR $_FILES['file']['size'] > $sizeMax) {
      echo "Erreur: Le fichier est trop volumineux, votre fichier doit être de taille inferieur à 50Mo";
      exit();
    }

    /*Vérification de la validité de l'extension*/
    $extensionUpload = strtolower(substr(strrchr($_FILES['file']['name'], '.'), 1)); //Récupération de l'extension
    if($extensionUpload != "csv") {
      echo "Erreur: Un problème est survenue, le fichier n'est pas au format CSV";
      exit();
    }

    /*Récupération de toutes les lignes du fichier entrée*/
    if ($inputFile = fopen($_FILES['file']['tmp_name'], "r")) {
      while (!feof($inputFile)){
        $inputLine[] = fgetcsv($inputFile, 1024, ';');
      }
      fclose($inputFile);
    } else {
      echo "Erreur: Un problème est survenue, le fichier n'a pas pu être lu";
      exit();
    }

    /*Cherche la colonne des differents parcours*/
    for ($i=0; $i < count($inputLine[0]); $i++) {
      switch (strtolower($inputLine[0][$i])) {
        case 'option':
          $lo = $i;
          break;
        case 'gsi':
          $lgsi = $i;
          break;
        case 'mi':
          $lmi = $i;
          break;
        case 'mf':
          $lmf = $i;
          break;
        default:
          break;
      }
    }

    /*Vérifie que les colonnes de parcours et option sont presents*/
    if (!isset($lo) OR !isset($lgsi) OR !isset($lmi) OR !isset($lmf)) {
      echo "Erreur: Les données ne sont pas au bon format !";
      exit();
    }

    /*Récupération des place de chaque options par parcours*/
    for ($i=1; $i < count($inputLine) - 1; $i++) {
      switch (strtolower($inputLine[$i][$lo])) {
        case 'actu':
          $placeMF['actu'] = $inputLine[$i][$lmf];
          break;
        case 'hpda':
          $placeGSI['hpda'] = $inputLine[$i][$lgsi];
          $placeMI['hpda'] = $inputLine[$i][$lmi];
          break;
        case 'bi':
          $placeGSI['bi'] = $inputLine[$i][$lgsi];
          $placeMI['bi'] = $inputLine[$i][$lmi];
          break;
        case 'cs':
          $placeGSI['cs'] = $inputLine[$i][$lgsi];
          break;
        case 'ds':
          $placeMI['ds'] = $inputLine[$i][$lmi];
          break;
        case 'ft':
          $placeMI['ft'] = $inputLine[$i][$lmi];
          break;
        case 'iac':
          $placeGSI['iac'] = $inputLine[$i][$lgsi];
          $placeMI['iac'] = $inputLine[$i][$lmi];
          break;
        case 'iap':
          $placeGSI['iap'] = $inputLine[$i][$lgsi];
          $placeMI['iap'] = $inputLine[$i][$lmi];
          break;
        case 'icc':
          $placeGSI['icc'] = $inputLine[$i][$lgsi];
          break;
        case 'inem':
          $placeGSI['inem'] = $inputLine[$i][$lgsi];
          break;
        case 'mmf':
          $placeMF['mmf'] = $inputLine[$i][$lmf];
          break;
        case 'visua':
          $placeGSI['visua'] = $inputLine[$i][$lgsi];
          break;
        default:
          break;
      }
    }

    /*-------FIN-TRAITEMENT-NOMBRE-DE-PLACE--------*/

    /*-------DEBUT-TRAITEMENT-ETUDIANT-------------*/

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

    /*Création des etudiants*/
    for ($i=1; $i < count($userLine) - 1; $i++) {
      /*Vérification que la ligne utilisateur n'est pas vide et qu'elle correspond à un etudiant*/
      if ($userLine[$i][0] != '' AND $userLine[$i][12] == 'etudiant') {
        /*Cherche dans les voeux la ligne correspondante a l'ID en cours de traitement*/
        for ($j=1; $j < count($userLine) - 1; $j++) {
          if ($voeuxLine[$j][0] == $userLine[$i][0]) {
            $currentLine = $j;
            break;
          }
        }
        //             match     ID             parcours             ECTS             Moyenne
        $etudiant[] = [false, $userLine[$i][0], $userLine[$i][7], $userLine[$i][8], $userLine[$i][9],
                       $voeuxLine[$currentLine][1], $voeuxLine[$currentLine][2], $voeuxLine[$currentLine][3],
                       $voeuxLine[$currentLine][4], $voeuxLine[$currentLine][5], $voeuxLine[$currentLine][6],
                       $voeuxLine[$currentLine][7], $voeuxLine[$currentLine][8]];
      }
    }
    /*-----------------------------FIN-TRAITEMENT-ETUDIANT---------------------------------------*/

    /*-------------------------------------------------------------------------------------------*/
    /*---------------------------------DEBUT-AFFECTATION-----------------------------------------*/
    /*-------------------------------------------------------------------------------------------*/

    $countGSI = 0;
    $countMI = 0;
    $countMF = 0;
    $allMatch = false;
    while (!$allMatch) {
      for ($i=0; $i < count($etudiant); $i++) {
        /*Si l'étudiant n'a pas de match*/
        if (!$etudiant[$i][0]) {
          /*Selection du parcours*/
          switch (trim(strtolower($etudiant[$i][2]))) {
            /*--------------------------------GSI----------------------------------*/
            case 'gsi':
              switch (trim(strtolower($etudiant[$i][$countGSI + 5]))) {
                /*--------------------------------HPDA----------------------------------*/
                case 'hpda':
                  if (!isset($placeGSI['hpda'])  OR empty($placeGSI['hpda'])) {
                    break;
                  }
                  /*Si il y a de la place dans cette option*/
                  if ($placeGSI['hpda'] > 0) {
                    $placeGSI['hpda']--; //Une place en moins dans cette option
                    /*Ajoute aux etudiants pris en hpdaGSI cette étudiant*/
                    $etudiantPrisHPDAGSI[] = [$etudiant[$i][1], $etudiant[$i][4]];
                    $etudiant[$i][0] = true;
                  } else {
                    /*Si il n'y a pas de place on regarde les moyennne des etudiants déjà pris*/
                    $moyenneMin = $etudiant[$i][4];
                    for ($h=0; $h < count($etudiantPrisHPDAGSI); $h++) {
                      /*Cherche l'id de l'étudiant ayant la pire moyenne parmis les etudiant pris*/
                      if (floatvalue($etudiantPrisHPDAGSI[$h][1]) < floatvalue($moyenneMin)) {
                        $moyenneMin = $etudiantPrisHPDAGSI[$h][1];
                        $idToGo = $etudiantPrisHPDAGSI[$h][0];
                      }
                    }

                    /*Si un etudiant pris a une moyenne inferieur on le remplace par l'étudiant actuel*/
                    if (isset($idToGo)) {
                      for ($h=0; $h < count($etudiantPrisHPDAGSI); $h++) {
                        if ($etudiantPrisHPDAGSI[$h][0] == $idToGo) {
                          $etudiantPrisHPDAGSI[$h][0] = $etudiant[$i][1];
                          $etudiantPrisHPDAGSI[$h][1] = $etudiant[$i][4];
                          $etudiant[$i][0] = true;
                          break;
                        }
                      }

                      /*Cherche l'étudiant remplacer et lui retire son match*/
                      for ($h=0; $h < count($etudiant); $h++) {
                        if ($etudiant[$i][1] == $idToGo) {
                          $etudiant[$h][0] = false;
                          break;
                        }
                      }
                    }
                  }
                break;
              /*--------------------------------FIN-HPDA----------------------------------*/
              /*--------------------------------BI----------------------------------*/
              case 'bi':
                /*Si il n'y a pas de place dans cette option*/
                if (!isset($placeGSI['bi'])  OR empty($placeGSI['bi'])) {
                  break;
                }
                /*Si il y a de la place dans cette option*/
                if ($placeGSI['bi'] > 0) {
                  $placeGSI['bi']--; //Une place en moins dans cette option
                  /*Ajoute aux etudiants pris en hpdaGSI cette étudiant*/
                  $etudiantPrisBIGSI[] = [$etudiant[$i][1], $etudiant[$i][4]];
                  $etudiant[$i][0] = true;
                } else {
                  /*Si il n'y a pas de place on regarde les moyennne des etudiants déjà pris*/
                  $moyenneMin = $etudiant[$i][4];
                  for ($h=0; $h < count($etudiantPrisBIGSI); $h++) {
                    /*Cherche l'id de l'étudiant ayant la pire moyenne parmis les etudiant pris*/
                    if (floatvalue($etudiantPrisBIGSI[$h][1]) < floatvalue($moyenneMin)) {
                      $moyenneMin = $etudiantPrisBIGSI[$h][1];
                      $idToGo = $etudiantPrisBIGSI[$h][0];
                    }
                  }

                  /*Si un etudiant pris a une moyenne inferieur on le remplace par l'étudiant actuel*/
                  if (isset($idToGo)) {
                    for ($h=0; $h < count($etudiantPrisBIGSI); $h++) {
                      if ($etudiantPrisBIGSI[$h][0] == $idToGo) {
                        $etudiantPrisBIGSI[$h][0] = $etudiant[$i][1];
                        $etudiantPrisBIGSI[$h][1] = $etudiant[$i][4];
                        $etudiant[$i][0] = true;
                        break;
                      }
                    }

                    /*Cherche l'étudiant remplacer et lui retire son match*/
                    for ($h=0; $h < count($etudiant); $h++) {
                      if ($etudiant[$i][1] == $idToGo) {
                        $etudiant[$h][0] = false;
                        break;
                      }
                    }
                  }
                }
                break;
              /*--------------------------------FIN-BI----------------------------------*/
              /*-----------------------------------CS-----------------------------------*/
              case 'cs':
                /*Si il n'y a pas de place dans cette option*/
                if (!isset($placeGSI['cs']) OR empty($placeGSI['cs'])) {
                  break;
                }
                /*Si il y a de la place dans cette option*/
                if ($placeGSI['cs'] > 0) {
                  $placeGSI['cs']--; //Une place en moins dans cette option
                  /*Ajoute aux etudiants pris en hpdaGSI cette étudiant*/
                  $etudiantPrisCSGSI[] = [$etudiant[$i][1], $etudiant[$i][4]];
                  $etudiant[$i][0] = true;
                } else {
                  /*Si il n'y a pas de place on regarde les moyennne des etudiants déjà pris*/
                  $moyenneMin = $etudiant[$i][4];
                  for ($h=0; $h < count($etudiantPrisCSGSI); $h++) {
                    /*Cherche l'id de l'étudiant ayant la pire moyenne parmis les etudiant pris*/
                    if (floatvalue($etudiantPrisCSGSI[$h][1]) < floatvalue($moyenneMin)) {
                      $moyenneMin = $etudiantPrisCSGSI[$h][1];
                      $idToGo = $etudiantPrisCSGSI[$h][0];
                    }
                  }

                  /*Si un etudiant pris a une moyenne inferieur on le remplace par l'étudiant actuel*/
                  if (isset($idToGo)) {
                    for ($h=0; $h < count($etudiantPrisCSGSI); $h++) {
                      if ($etudiantPrisCSGSI[$h][0] == $idToGo) {
                        $etudiantPrisCSGSI[$h][0] = $etudiant[$i][1];
                        $etudiantPrisCSGSI[$h][1] = $etudiant[$i][4];
                        $etudiant[$i][0] = true;
                        break;
                      }
                    }

                    /*Cherche l'étudiant remplacer et lui retire son match*/
                    for ($h=0; $h < count($etudiant); $h++) {
                      if ($etudiant[$i][1] == $idToGo) {
                        $etudiant[$h][0] = false;
                        break;
                      }
                    }
                  }
                }
                break;
              /*-----------------------------------FIN-CS-----------------------------------*/
              /*-----------------------------------IAC--------------------------------------*/
              case 'iac':
                /*Si il n'y a pas de place dans cette option*/
                if (!isset($placeGSI['iac'])  OR empty($placeGSI['iac'])) {
                  break;
                }
                /*Si il y a de la place dans cette option*/
                if ($placeGSI['iac'] > 0) {
                  $placeGSI['iac']--; //Une place en moins dans cette option
                  /*Ajoute aux etudiants pris en hpdaGSI cette étudiant*/
                  $etudiantPrisIACGSI[] = [$etudiant[$i][1], $etudiant[$i][4]];
                  $etudiant[$i][0] = true;
                } else {
                  /*Si il n'y a pas de place on regarde les moyennne des etudiants déjà pris*/
                  $moyenneMin = $etudiant[$i][4];
                  for ($h=0; $h < count($etudiantPrisIACGSI); $h++) {
                    /*Cherche l'id de l'étudiant ayant la pire moyenne parmis les etudiant pris*/
                    if (floatvalue($etudiantPrisIACGSI[$h][1]) < floatvalue($moyenneMin)) {
                      $moyenneMin = $etudiantPrisIACGSI[$h][1];
                      $idToGo = $etudiantPrisIACGSI[$h][0];
                    }
                  }

                  /*Si un etudiant pris a une moyenne inferieur on le remplace par l'étudiant actuel*/
                  if (isset($idToGo)) {
                    for ($h=0; $h < count($etudiantPrisIACGSI); $h++) {
                      if ($etudiantPrisIACGSI[$h][0] == $idToGo) {
                        $etudiantPrisIACGSI[$h][0] = $etudiant[$i][1];
                        $etudiantPrisIACGSI[$h][1] = $etudiant[$i][4];
                        $etudiant[$i][0] = true;
                        break;
                      }
                    }

                    /*Cherche l'étudiant remplacer et lui retire son match*/
                    for ($h=0; $h < count($etudiant); $h++) {
                      if ($etudiant[$i][1] == $idToGo) {
                        $etudiant[$h][0] = false;
                        break;
                      }
                    }
                  }
                }
                break;
                /*-----------------------------------FIN-IAC-----------------------------------*/
              /*-----------------------------------IAP--------------------------------------*/
              case 'iap':
                /*Si il n'y a pas de place dans cette option*/
                if (!isset($placeGSI['iap'])  OR empty($placeGSI['iap'])) {
                  break;
                }
                /*Si il y a de la place dans cette option*/
                if ($placeGSI['iap'] > 0) {
                  $placeGSI['iap']--; //Une place en moins dans cette option
                  /*Ajoute aux etudiants pris en hpdaGSI cette étudiant*/
                  $etudiantPrisIAPGSI[] = [$etudiant[$i][1], $etudiant[$i][4]];
                  $etudiant[$i][0] = true;
                } else {
                  /*Si il n'y a pas de place on regarde les moyennne des etudiants déjà pris*/
                  $moyenneMin = $etudiant[$i][4];
                  for ($h=0; $h < count($etudiantPrisIAPGSI); $h++) {
                    /*Cherche l'id de l'étudiant ayant la pire moyenne parmis les etudiant pris*/
                    if (floatvalue($etudiantPrisIAPGSI[$h][1]) < floatvalue($moyenneMin)) {
                      $moyenneMin = $etudiantPrisIAPGSI[$h][1];
                      $idToGo = $etudiantPrisIAPGSI[$h][0];
                    }
                  }

                  /*Si un etudiant pris a une moyenne inferieur on le remplace par l'étudiant actuel*/
                  if (isset($idToGo)) {
                    for ($h=0; $h < count($etudiantPrisIAPGSI); $h++) {
                      if ($etudiantPrisIAPGSI[$h][0] == $idToGo) {
                        $etudiantPrisIAPGSI[$h][0] = $etudiant[$i][1];
                        $etudiantPrisIAPGSI[$h][1] = $etudiant[$i][4];
                        $etudiant[$i][0] = true;
                        break;
                      }
                    }

                    /*Cherche l'étudiant remplacer et lui retire son match*/
                    for ($h=0; $h < count($etudiant); $h++) {
                      if ($etudiant[$i][1] == $idToGo) {
                        $etudiant[$h][0] = false;
                        break;
                      }
                    }
                  }
                }
                break;
                /*-----------------------------------FIN-IAP-----------------------------------*/
              /*-----------------------------------ICC--------------------------------------*/
              case 'icc':
                /*Si il n'y a pas de place dans cette option*/
                if (!isset($placeGSI['icc'])  OR empty($placeGSI['icc'])) {
                  break;
                }
                /*Si il y a de la place dans cette option*/
                if ($placeGSI['icc'] > 0) {
                  $placeGSI['icc']--; //Une place en moins dans cette option
                  /*Ajoute aux etudiants pris en hpdaGSI cette étudiant*/
                  $etudiantPrisICCGSI[] = [$etudiant[$i][1], $etudiant[$i][4]];
                  $etudiant[$i][0] = true;
                } else {
                  /*Si il n'y a pas de place on regarde les moyennne des etudiants déjà pris*/
                  $moyenneMin = $etudiant[$i][4];
                  for ($h=0; $h < count($etudiantPrisICCGSI); $h++) {
                    /*Cherche l'id de l'étudiant ayant la pire moyenne parmis les etudiant pris*/
                    if (floatvalue($etudiantPrisICCGSI[$h][1]) < floatvalue($moyenneMin)) {
                      $moyenneMin = $etudiantPrisICCGSI[$h][1];
                      $idToGo = $etudiantPrisICCGSI[$h][0];
                    }
                  }

                  /*Si un etudiant pris a une moyenne inferieur on le remplace par l'étudiant actuel*/
                  if (isset($idToGo)) {
                    for ($h=0; $h < count($etudiantPrisICCGSI); $h++) {
                      if ($etudiantPrisICCGSI[$h][0] == $idToGo) {
                        $etudiantPrisICCGSI[$h][0] = $etudiant[$i][1];
                        $etudiantPrisICCGSI[$h][1] = $etudiant[$i][4];
                        $etudiant[$i][0] = true;
                        break;
                      }
                    }

                    /*Cherche l'étudiant remplacer et lui retire son match*/
                    for ($h=0; $h < count($etudiant); $h++) {
                      if ($etudiant[$i][1] == $idToGo) {
                        $etudiant[$h][0] = false;
                        break;
                      }
                    }
                  }
                }
                break;
                /*-----------------------------------FIN-ICC-----------------------------------*/
              /*-----------------------------------INEM--------------------------------------*/
              case 'inem':
                /*Si il n'y a pas de place dans cette option*/
                if (!isset($placeGSI['inem'])  OR empty($placeGSI['inem'])) {
                  break;
                }
                /*Si il y a de la place dans cette option*/
                if ($placeGSI['inem'] > 0) {
                  $placeGSI['inem']--; //Une place en moins dans cette option
                  /*Ajoute aux etudiants pris en hpdaGSI cette étudiant*/
                  $etudiantPrisINEMGSI[] = [$etudiant[$i][1], $etudiant[$i][4]];
                  $etudiant[$i][0] = true;
                } else {
                  /*Si il n'y a pas de place on regarde les moyennne des etudiants déjà pris*/
                  $moyenneMin = $etudiant[$i][4];
                  for ($h=0; $h < count($etudiantPrisINEMGSI); $h++) {
                    /*Cherche l'id de l'étudiant ayant la pire moyenne parmis les etudiant pris*/
                    if (floatvalue($etudiantPrisINEMGSI[$h][1]) < floatvalue($moyenneMin)) {
                      $moyenneMin = $etudiantPrisINEMGSI[$h][1];
                      $idToGo = $etudiantPrisINEMGSI[$h][0];
                    }
                  }

                  /*Si un etudiant pris a une moyenne inferieur on le remplace par l'étudiant actuel*/
                  if (isset($idToGo)) {
                    for ($h=0; $h < count($etudiantPrisINEMGSI); $h++) {
                      if ($etudiantPrisINEMGSI[$h][0] == $idToGo) {
                        $etudiantPrisINEMGSI[$h][0] = $etudiant[$i][1];
                        $etudiantPrisINEMGSI[$h][1] = $etudiant[$i][4];
                        $etudiant[$i][0] = true;
                        break;
                      }
                    }

                    /*Cherche l'étudiant remplacer et lui retire son match*/
                    for ($h=0; $h < count($etudiant); $h++) {
                      if ($etudiant[$i][1] == $idToGo) {
                        $etudiant[$h][0] = false;
                        break;
                      }
                    }
                  }
                }
                break;
              /*-----------------------------------FIN-INEM-----------------------------------*/
              /*-----------------------------------VISUA--------------------------------------*/
              case 'visua':
                /*Si il n'y a pas de place dans cette option*/
                if (!isset($placeGSI['visua']) OR empty($placeGSI['visua'])) {
                  break;
                }
                /*Si il y a de la place dans cette option*/
                if ($placeGSI['visua'] > 0) {
                  $placeGSI['visua']--; //Une place en moins dans cette option
                  /*Ajoute aux etudiants pris en hpdaGSI cette étudiant*/
                  $etudiantPrisVISUAGSI[] = [$etudiant[$i][1], $etudiant[$i][4]];
                  $etudiant[$i][0] = true;
                } else {
                  /*Si il n'y a pas de place on regarde les moyennne des etudiants déjà pris*/
                  $moyenneMin = $etudiant[$i][4];
                  for ($h=0; $h < count($etudiantPrisVISUAGSI); $h++) {
                    /*Cherche l'id de l'étudiant ayant la pire moyenne parmis les etudiant pris*/
                    if (floatvalue($etudiantPrisVISUAGSI[$h][1]) < floatvalue($moyenneMin)) {
                      $moyenneMin = $etudiantPrisVISUAGSI[$h][1];
                      $idToGo = $etudiantPrisVISUAGSI[$h][0];
                    }
                  }

                  /*Si un etudiant pris a une moyenne inferieur on le remplace par l'étudiant actuel*/
                  if (isset($idToGo)) {
                    for ($h=0; $h < count($etudiantPrisINEMGSI); $h++) {
                      if ($etudiantPrisVISUAGSI[$h][0] == $idToGo) {
                        $etudiantPrisVISUAGSI[$h][0] = $etudiant[$i][1];
                        $etudiantPrisVISUAGSI[$h][1] = $etudiant[$i][4];
                        $etudiant[$i][0] = true;
                        break;
                      }
                    }

                    /*Cherche l'étudiant remplacer et lui retire son match*/
                    for ($h=0; $h < count($etudiant); $h++) {
                      if ($etudiant[$i][1] == $idToGo) {
                        $etudiant[$h][0] = false;
                        break;
                      }
                    }
                  }
                }
                break;
                /*-----------------------------------FIN-VISUA-----------------------------------*/
              default:
                break;
              }
            break;
          /*--------------------------------FIN-GSI----------------------------------*/

          /*----------------------------------MI-------------------------------------*/
          case 'mi':
            switch (trim(strtolower($etudiant[$i][$countMI + 5]))) {
              /*--------------------------------HPDA----------------------------------*/
              case 'hpda':
                if (!isset($placeMI['hpda'])  OR empty($placeMI['hpda'])) {
                  break;
                }
                /*Si il y a de la place dans cette option*/
                if ($placeMI['hpda'] > 0) {
                  $placeMI['hpda']--; //Une place en moins dans cette option
                  /*Ajoute aux etudiants pris en hpdaGSI cette étudiant*/
                  $etudiantPrisHPDAMI[] = [$etudiant[$i][1], $etudiant[$i][4]];
                  $etudiant[$i][0] = true;
                } else {
                  /*Si il n'y a pas de place on regarde les moyennne des etudiants déjà pris*/
                  $moyenneMin = $etudiant[$i][4];
                  for ($h=0; $h < count($etudiantPrisHPDAMI); $h++) {
                    /*Cherche l'id de l'étudiant ayant la pire moyenne parmis les etudiant pris*/
                    if (floatvalue($etudiantPrisHPDAGSI[$h][1]) < floatvalue($moyenneMin)) {
                      $moyenneMin = $etudiantPrisHPDAMI[$h][1];
                      $idToGo = $etudiantPrisHPDAMI[$h][0];
                    }
                  }

                  /*Si un etudiant pris a une moyenne inferieur on le remplace par l'étudiant actuel*/
                  if (isset($idToGo)) {
                    for ($h=0; $h < count($etudiantPrisHPDAMI); $h++) {
                      if ($etudiantPrisHPDAMI[$h][0] == $idToGo) {
                        $etudiantPrisHPDAMI[$h][0] = $etudiant[$i][1];
                        $etudiantPrisHPDAMI[$h][1] = $etudiant[$i][4];
                        $etudiant[$i][0] = true;
                        break;
                      }
                    }

                    /*Cherche l'étudiant remplacer et lui retire son match*/
                    for ($h=0; $h < count($etudiant); $h++) {
                      if ($etudiant[$i][1] == $idToGo) {
                        $etudiant[$h][0] = false;
                        break;
                      }
                    }
                  }
                }
              break;
            /*--------------------------------FIN-HPDA----------------------------------*/
            /*--------------------------------BI----------------------------------*/
            case 'bi':
              /*Si il n'y a pas de place dans cette option*/
              if (!isset($placeMI['bi'])  OR empty($placeMI['bi'])) {
                break;
              }
              /*Si il y a de la place dans cette option*/
              if ($placeMI['bi'] > 0) {
                $placeMI['bi']--; //Une place en moins dans cette option
                /*Ajoute aux etudiants pris en hpdaGSI cette étudiant*/
                $etudiantPrisBIMI[] = [$etudiant[$i][1], $etudiant[$i][4]];
                $etudiant[$i][0] = true;
              } else {
                /*Si il n'y a pas de place on regarde les moyennne des etudiants déjà pris*/
                $moyenneMin = $etudiant[$i][4];
                for ($h=0; $h < count($etudiantPrisBIMI); $h++) {
                  /*Cherche l'id de l'étudiant ayant la pire moyenne parmis les etudiant pris*/
                  if (floatvalue($etudiantPrisBIMI[$h][1]) < floatvalue($moyenneMin)) {
                    $moyenneMin = $etudiantPrisBIMI[$h][1];
                    $idToGo = $etudiantPrisBIMI[$h][0];
                  }
                }

                /*Si un etudiant pris a une moyenne inferieur on le remplace par l'étudiant actuel*/
                if (isset($idToGo)) {
                  for ($h=0; $h < count($etudiantPrisBIMI); $h++) {
                    if ($etudiantPrisBIMI[$h][0] == $idToGo) {
                      $etudiantPrisBIMI[$h][0] = $etudiant[$i][1];
                      $etudiantPrisBIMI[$h][1] = $etudiant[$i][4];
                      $etudiant[$i][0] = true;
                      break;
                    }
                  }

                  /*Cherche l'étudiant remplacer et lui retire son match*/
                  for ($h=0; $h < count($etudiant); $h++) {
                    if ($etudiant[$i][1] == $idToGo) {
                      $etudiant[$h][0] = false;
                      break;
                    }
                  }
                }
              }
              break;
            /*--------------------------------FIN-BI--------------------------------------*/

            /*-----------------------------------DS---------------------------------------*/
            case 'ds':
              /*Si il n'y a pas de place dans cette option*/
              if (!isset($placeMI['ds'])  OR empty($placeMI['ds'])) {
                break;
              }
              /*Si il y a de la place dans cette option*/
              if ($placeMI['ds'] > 0) {
                $placeMI['ds']--; //Une place en moins dans cette option
                /*Ajoute aux etudiants pris en hpdaGSI cette étudiant*/
                $etudiantPrisDSMI[] = [$etudiant[$i][1], $etudiant[$i][4]];
                $etudiant[$i][0] = true;
              } else {
                /*Si il n'y a pas de place on regarde les moyennne des etudiants déjà pris*/
                $moyenneMin = $etudiant[$i][4];
                for ($h=0; $h < count($etudiantPrisDSMI); $h++) {
                  /*Cherche l'id de l'étudiant ayant la pire moyenne parmis les etudiant pris*/
                  if (floatvalue($etudiantPrisDSMI[$h][1]) < floatvalue($moyenneMin)) {
                    $moyenneMin = $etudiantPrisDSMI[$h][1];
                    $idToGo = $etudiantPrisDSMI[$h][0];
                  }
                }

                /*Si un etudiant pris a une moyenne inferieur on le remplace par l'étudiant actuel*/
                if (isset($idToGo)) {
                  for ($h=0; $h < count($etudiantPrisDSMI); $h++) {
                    if ($etudiantPrisDSMI[$h][0] == $idToGo) {
                      $etudiantPrisDSMI[$h][0] = $etudiant[$i][1];
                      $etudiantPrisDSMI[$h][1] = $etudiant[$i][4];
                      $etudiant[$i][0] = true;
                      break;
                    }
                  }

                  /*Cherche l'étudiant remplacer et lui retire son match*/
                  for ($h=0; $h < count($etudiant); $h++) {
                    if ($etudiant[$i][1] == $idToGo) {
                      $etudiant[$h][0] = false;
                      break;
                    }
                  }
                }
              }
              break;
            /*-----------------------------------FIN-DS------------------------------------*/

            /*-----------------------------------FT----------------------------------------*/
            case 'ft':
              /*Si il n'y a pas de place dans cette option*/
              if (!isset($placeMI['ft']) OR empty($placeMI['ft'])) {
                break;
              }
              /*Si il y a de la place dans cette option*/
              if ($placeMI['ft'] > 0) {
                $placeMI['ft']--; //Une place en moins dans cette option
                /*Ajoute aux etudiants pris en hpdaGSI cette étudiant*/
                $etudiantPrisFTMI[] = [$etudiant[$i][1], $etudiant[$i][4]];
                $etudiant[$i][0] = true;
              } else {
                /*Si il n'y a pas de place on regarde les moyennne des etudiants déjà pris*/
                $moyenneMin = $etudiant[$i][4];
                for ($h=0; $h < count($etudiantPrisFTMI); $h++) {
                  /*Cherche l'id de l'étudiant ayant la pire moyenne parmis les etudiant pris*/
                  if (floatvalue($etudiantPrisFTMI[$h][1]) < floatvalue($moyenneMin)) {
                    $moyenneMin = $etudiantPrisFTMI[$h][1];
                    $idToGo = $etudiantPrisFTMI[$h][0];
                  }
                }

                /*Si un etudiant pris a une moyenne inferieur on le remplace par l'étudiant actuel*/
                if (isset($idToGo)) {
                  for ($h=0; $h < count($etudiantPrisFTMI); $h++) {
                    if ($etudiantPrisFTMI[$h][0] == $idToGo) {
                      $etudiantPrisFTMI[$h][0] = $etudiant[$i][1];
                      $etudiantPrisFTMI[$h][1] = $etudiant[$i][4];
                      $etudiant[$i][0] = true;
                      break;
                    }
                  }

                  /*Cherche l'étudiant remplacer et lui retire son match*/
                  for ($h=0; $h < count($etudiant); $h++) {
                    if ($etudiant[$i][1] == $idToGo) {
                      $etudiant[$h][0] = false;
                      break;
                    }
                  }
                }
              }
              break;
            /*-----------------------------------FIN-FT-----------------------------------*/

            /*-----------------------------------IAC--------------------------------------*/
            case 'iac':
              /*Si il n'y a pas de place dans cette option*/
              if (!isset($placeMI['iac'])  OR empty($placeMI['iac'])) {
                break;
              }
              /*Si il y a de la place dans cette option*/
              if ($placeMI['iac'] > 0) {
                $placeMI['iac']--; //Une place en moins dans cette option
                /*Ajoute aux etudiants pris en hpdaGSI cette étudiant*/
                $etudiantPrisIACMI[] = [$etudiant[$i][1], $etudiant[$i][4]];
                $etudiant[$i][0] = true;
              } else {
                /*Si il n'y a pas de place on regarde les moyennne des etudiants déjà pris*/
                $moyenneMin = $etudiant[$i][4];
                for ($h=0; $h < count($etudiantPrisIACMI); $h++) {
                  /*Cherche l'id de l'étudiant ayant la pire moyenne parmis les etudiant pris*/
                  if (floatvalue($etudiantPrisIACMI[$h][1]) < floatvalue($moyenneMin)) {
                    $moyenneMin = $etudiantPrisIACMI[$h][1];
                    $idToGo = $etudiantPrisIACMI[$h][0];
                  }
                }

                /*Si un etudiant pris a une moyenne inferieur on le remplace par l'étudiant actuel*/
                if (isset($idToGo)) {
                  for ($h=0; $h < count($etudiantPrisIACMI); $h++) {
                    if ($etudiantPrisIACMI[$h][0] == $idToGo) {
                      $etudiantPrisIACMI[$h][0] = $etudiant[$i][1];
                      $etudiantPrisIACMI[$h][1] = $etudiant[$i][4];
                      $etudiant[$i][0] = true;
                      break;
                    }
                  }

                  /*Cherche l'étudiant remplacer et lui retire son match*/
                  for ($h=0; $h < count($etudiant); $h++) {
                    if ($etudiant[$i][1] == $idToGo) {
                      $etudiant[$h][0] = false;
                      break;
                    }
                  }
                }
              }
              break;
              /*-----------------------------------FIN-IAC-----------------------------------*/
            /*-----------------------------------IAP--------------------------------------*/
            case 'iap':
              /*Si il n'y a pas de place dans cette option*/
              if (!isset($placeMI['iap'])  OR empty($placeMI['iap'])) {
                break;
              }
              /*Si il y a de la place dans cette option*/
              if ($placeMI['iap'] > 0) {
                $placeMI['iap']--; //Une place en moins dans cette option
                /*Ajoute aux etudiants pris en hpdaGSI cette étudiant*/
                $etudiantPrisIAPMI[] = [$etudiant[$i][1], $etudiant[$i][4]];
                $etudiant[$i][0] = true;
              } else {
                /*Si il n'y a pas de place on regarde les moyennne des etudiants déjà pris*/
                $moyenneMin = $etudiant[$i][4];
                for ($h=0; $h < count($etudiantPrisIAPMI); $h++) {
                  /*Cherche l'id de l'étudiant ayant la pire moyenne parmis les etudiant pris*/
                  if (floatvalue($etudiantPrisIAPMI[$h][1]) < floatvalue($moyenneMin)) {
                    $moyenneMin = $etudiantPrisIAPMI[$h][1];
                    $idToGo = $etudiantPrisIAPMI[$h][0];
                  }
                }

                /*Si un etudiant pris a une moyenne inferieur on le remplace par l'étudiant actuel*/
                if (isset($idToGo)) {
                  for ($h=0; $h < count($etudiantPrisIAPMI); $h++) {
                    if ($etudiantPrisIAPMI[$h][0] == $idToGo) {
                      $etudiantPrisIAPMI[$h][0] = $etudiant[$i][1];
                      $etudiantPrisIAPMI[$h][1] = $etudiant[$i][4];
                      $etudiant[$i][0] = true;
                      break;
                    }
                  }

                  /*Cherche l'étudiant remplacer et lui retire son match*/
                  for ($h=0; $h < count($etudiant); $h++) {
                    if ($etudiant[$i][1] == $idToGo) {
                      $etudiant[$h][0] = false;
                      break;
                    }
                  }
                }
              }
              break;
              /*-----------------------------------FIN-IAC-----------------------------------*/
              default:
                break;
              }
            break;
          /*----------------------------------FIN-MI-------------------------------------*/

          /*------------------------------------MF---------------------------------------*/
          case 'mf':
            switch (trim(strtolower($etudiant[$i][$countMF + 5]))) {
              /*--------------------------------ACTU----------------------------------*/
              case 'actu':
                if (!isset($placeMF['actu'])  OR empty($placeMF['actu'])) {
                  break;
                }
                /*Si il y a de la place dans cette option*/
                if ($placeMF['actu'] > 0) {
                  $placeMF['actu']--; //Une place en moins dans cette option
                  /*Ajoute aux etudiants pris en hpdaGSI cette étudiant*/
                  $etudiantPrisACTUMF[] = [$etudiant[$i][1], $etudiant[$i][4]];
                  $etudiant[$i][0] = true;
                } else {
                  /*Si il n'y a pas de place on regarde les moyennne des etudiants déjà pris*/
                  $moyenneMin = $etudiant[$i][4];
                  for ($h=0; $h < count($etudiantPrisACTUMF); $h++) {
                    /*Cherche l'id de l'étudiant ayant la pire moyenne parmis les etudiant pris*/
                    if (floatvalue($etudiantPrisACTUMF[$h][1]) < floatvalue($moyenneMin)) {
                      $moyenneMin = $etudiantPrisACTUMF[$h][1];
                      $idToGo = $etudiantPrisACTUMF[$h][0];
                    }
                  }

                  /*Si un etudiant pris a une moyenne inferieur on le remplace par l'étudiant actuel*/
                  if (isset($idToGo)) {
                    for ($h=0; $h < count($etudiantPrisACTUMF); $h++) {
                      if ($etudiantPrisACTUMF[$h][0] == $idToGo) {
                        $etudiantPrisACTUMF[$h][0] = $etudiant[$i][1];
                        $etudiantPrisACTUMF[$h][1] = $etudiant[$i][4];
                        $etudiant[$i][0] = true;
                        break;
                      }
                    }

                    /*Cherche l'étudiant remplacer et lui retire son match*/
                    for ($h=0; $h < count($etudiant); $h++) {
                      if ($etudiant[$i][1] == $idToGo) {
                        $etudiant[$h][0] = false;
                        break;
                      }
                    }
                  }
                }
              break;
            /*--------------------------------FIN-ACTU----------------------------------*/
            /*-----------------------------------MMF--------------------------------------*/
            case 'mmf':
              /*Si il n'y a pas de place dans cette option*/
              if (!isset($placeMF['mmf'])  OR empty($placeMF['mmf'])) {
                break;
              }
              /*Si il y a de la place dans cette option*/
              if ($placeMF['mmf'] > 0) {
                $placeMF['mmf']--; //Une place en moins dans cette option
                /*Ajoute aux etudiants pris en hpdaGSI cette étudiant*/
                $etudiantPrisMMFMF[] = [$etudiant[$i][1], $etudiant[$i][4]];
                $etudiant[$i][0] = true;
              } else {
                /*Si il n'y a pas de place on regarde les moyennne des etudiants déjà pris*/
                $moyenneMin = $etudiant[$i][4];
                for ($h=0; $h < count($etudiantPrisMMFMF); $h++) {
                  /*Cherche l'id de l'étudiant ayant la pire moyenne parmis les etudiant pris*/
                  if (floatvalue($etudiantPrisMMFMF[$h][1]) < floatvalue($moyenneMin)) {
                    $moyenneMin = $etudiantPrisMMFMF[$h][1];
                    $idToGo = $etudiantPrisMMFMF[$h][0];
                  }
                }

                /*Si un etudiant pris a une moyenne inferieur on le remplace par l'étudiant actuel*/
                if (isset($idToGo)) {
                  for ($h=0; $h < count($etudiantPrisMMFMF); $h++) {
                    if ($etudiantPrisMMFMF[$h][0] == $idToGo) {
                      $etudiantPrisMMFMF[$h][0] = $etudiant[$i][1];
                      $etudiantPrisMMFMF[$h][1] = $etudiant[$i][4];
                      $etudiant[$i][0] = true;
                      break;
                    }
                  }

                  /*Cherche l'étudiant remplacer et lui retire son match*/
                  for ($h=0; $h < count($etudiant); $h++) {
                    if ($etudiant[$i][1] == $idToGo) {
                      $etudiant[$h][0] = false;
                      break;
                    }
                  }
                }
              }
              break;
              /*-----------------------------------FIN-MMF-----------------------------------*/
              default:
                break;
              }
            break;
            /*----------------------------------FIN-MF---------------------------------*/
          default:
            break;
        }
      }
    }
    /*Vérification que tout les étudiants sont matcher*/
    $allMatch = true;
    for ($a=0; $a < count($etudiant); $a++) {
      if ($etudiant[$a][0] == false) {
        $allMatch = false;
        /*On passe au choix d'option suivant*/
        /*A COMPLETER SI PLUS DE PLACE MESSAGE ERREUR*/
        if ($countGSI < 8) {
           $countGSI++;
        } else {
           echo "Erreur, il n'y a plus de place pour les options";
           break 2;
        }
        if ($countMI < 6) {
          $countMI++;
        }
        if ($countMF < 2) {
          $countMF++;
        }

        break; //PLus break mais verification du parcours de l'etudiant pas match
      }
    }
  }


    /*---------------------------FIN-AFFECTATION------------------------------*/

    /*----------------------------TRAITEMENT-SORTIE-FICHIER-------------------*/
    $chemin =  '../membres/data/tmpAffectation.csv';
    if(file_exists($chemin)){unlink($chemin);}
    if ($fichier = fopen($chemin, 'w+')) {
      $idPrisHPDAGSI[] = 'hpdagsi';
      $idPrisBIGSI[] = 'bigsi';
      $idPrisCSGSI[] = 'csgsi';
      $idPrisIACGSI[] = 'iacgsi';
      $idPrisIAPGSI[] = 'iapgsi';
      $idPrisICCGSI[] = 'iccgsi';
      $idPrisINEMGSI[] = 'inemgsi';
      $idPrisVISUAGSI[] = 'visuagsi';
      $idPrisHPDAMI[] = 'hpdami';
      $idPrisBIMI[] = 'bimi';
      $idPrisDSMI[] = 'dsmi';
      $idPrisFTMI[] = 'ftmi';
      $idPrisIACMI[] = 'iacmi';
      $idPrisIAPMI[] = 'iapmi';
      $idPrisACTUMF[] = 'actumf';
      $idPrisMMFMF[] = 'mmfmf';

      /*Récupération des id des étudiants pris en HPDA depuis le parcours GSI*/
      if (isset($etudiantPrisHPDAGSI)) {
        for ($i=0; $i < count($etudiantPrisHPDAGSI); $i++) {
          $idPrisHPDAGSI[] = $etudiantPrisHPDAGSI[$i][0];
        }
      }
      /*Récupération des id des étudiants pris en BI depuis le parcours GSI*/
      if (isset($etudiantPrisBIGSI)) {
        for ($i=0; $i < count($etudiantPrisBIGSI); $i++) {
          $idPrisBIGSI[] = $etudiantPrisBIGSI[$i][0];
        }
      }
      /*Récupération des id des étudiants pris en CS depuis le parcours GSI*/
      if (isset($etudiantPrisCSGSI)) {
        for ($i=0; $i < count($etudiantPrisCSGSI); $i++) {
          $idPrisCSGSI[] = $etudiantPrisCSGSI[$i][0];
        }
      }
      /*Récupération des id des étudiants pris en IAC depuis le parcours GSI*/
      if (isset($etudiantPrisIACGSI)) {
        for ($i=0; $i < count($etudiantPrisIACGSI); $i++) {
          $idPrisIACGSI[] = $etudiantPrisIACGSI[$i][0];
        }
      }
      /*Récupération des id des étudiants pris en IAP depuis le parcours GSI*/
      if (isset($etudiantPrisIAPGSI)) {
        for ($i=0; $i < count($etudiantPrisIAPGSI); $i++) {
          $idPrisIAPGSI[] = $etudiantPrisIAPGSI[$i][0];
        }
      }
      /*Récupération des id des étudiants pris en ICC depuis le parcours GSI*/
      if (isset($etudiantPrisICCGSI)) {
        for ($i=0; $i < count($etudiantPrisICCGSI); $i++) {
          $idPrisICCGSI[] = $etudiantPrisICCGSI[$i][0];
        }
      }
      /*Récupération des id des étudiants pris en INEM depuis le parcours GSI*/
      if (isset($etudiantPrisINEMGSI)) {
        for ($i=0; $i < count($etudiantPrisINEMGSI); $i++) {
          $idPrisINEMGSI[] = $etudiantPrisINEMGSI[$i][0];
        }
      }
      /*Récupération des id des étudiants pris en VISUA depuis le parcours GSI*/
      if (isset($etudiantPrisVISUAGSI)) {
        for ($i=0; $i < count($etudiantPrisVISUAGSI); $i++) {
          $idPrisVISUAGSI[] = $etudiantPrisVISUAGSI[$i][0];
        }
      }
      /*Récupération des id des étudiants pris en HDPA depuis le parcours MI*/
      if (isset($etudiantPrisHPDAMI)) {
        for ($i=0; $i < count($etudiantPrisHPDAMI); $i++) {
          $idPrisHPDAMI[] = $etudiantPrisHPDAMI[$i][0];
        }
      }
      /*Récupération des id des étudiants pris en BI depuis le parcours MI*/
      if (isset($etudiantPrisBIMI)) {
        for ($i=0; $i < count($etudiantPrisBIMI); $i++) {
          $idPrisBIMI[] = $etudiantPrisBIMI[$i][0];
        }
      }
      /*Récupération des id des étudiants pris en DS depuis le parcours MI*/
      if (isset($etudiantPrisDSMI)) {
        for ($i=0; $i < count($etudiantPrisDSMI); $i++) {
          $idPrisDSMI[] = $etudiantPrisDSMI[$i][0];
        }
      }
      /*Récupération des id des étudiants pris en FT depuis le parcours MI*/
      if (isset($etudiantPrisFTMI)) {
        for ($i=0; $i < count($etudiantPrisFTMI); $i++) {
          $idPrisFTMI[] = $etudiantPrisFTMI[$i][0];
        }
      }
      /*Récupération des id des étudiants pris en IAC depuis le parcours MI*/
      if (isset($etudiantPrisIACMI)) {
        for ($i=0; $i < count($etudiantPrisIACMI); $i++) {
          $idPrisIACMI[] = $etudiantPrisIACMI[$i][0];
        }
      }
      /*Récupération des id des étudiants pris en IAP depuis le parcours MI*/
      if (isset($etudiantPrisIAPMI)) {
        for ($i=0; $i < count($etudiantPrisIAPMI); $i++) {
          $idPrisIAPMI[] = $etudiantPrisIAPMI[$i][0];
        }
      }
      /*Récupération des id des étudiants pris en ACTU depuis le parcours MF*/
      if (isset($etudiantPrisACTUMF)) {
        for ($i=0; $i < count($etudiantPrisACTUMF); $i++) {
          $idPrisACTUMF[] = $etudiantPrisACTUMF[$i][0];
        }
      }
      /*Récupération des id des étudiants pris en MMF depuis le parcours MF*/
      if (isset($etudiantPrisMMFMF)) {
        for ($i=0; $i < count($etudiantPrisMMFMF); $i++) {
          $idPrisMMFMF[] = $etudiantPrisMMFMF[$i][0];
        }
      }

      /*Insert les information dans le csv*/
      fputcsv($fichier, $idPrisHPDAGSI, ";");
      fputcsv($fichier, $idPrisBIGSI, ";");
      fputcsv($fichier, $idPrisCSGSI, ";");
      fputcsv($fichier, $idPrisIACGSI, ";");
      fputcsv($fichier, $idPrisIAPGSI, ";");
      fputcsv($fichier, $idPrisICCGSI, ";");
      fputcsv($fichier, $idPrisINEMGSI, ";");
      fputcsv($fichier, $idPrisVISUAGSI, ";");
      fputcsv($fichier, $idPrisHPDAMI, ";");
      fputcsv($fichier, $idPrisBIMI, ";");
      fputcsv($fichier, $idPrisDSMI, ";");
      fputcsv($fichier, $idPrisFTMI, ";");
      fputcsv($fichier, $idPrisIACMI, ";");
      fputcsv($fichier, $idPrisIAPMI, ";");
      fputcsv($fichier, $idPrisACTUMF, ";");
      fputcsv($fichier, $idPrisMMFMF, ";");

      include '../php/logFunction.php';
      addLog("Affectation: L'algorithme a affecté les étudiants");
    }

?>
