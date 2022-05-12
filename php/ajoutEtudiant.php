<?php
    session_start();

    /*Vérification que la page a été appellée avec ajax*/
    if(empty($_SERVER['HTTP_X_REQUESTED_WITH'])){
        header('Location: ../portail.php');
        exit();
    }

    /*Verification du fichier uploader*/
    if(!isset($_FILES['file']) OR empty($_FILES['file']['name'])) {
      echo "Erreur: Un problème est survenue, le fichier est vide";
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
    }

    /*Cherche la ligne où ce situe tel ou tel information*/
    for ($i=0; $i < count($inputLine[0]); $i++) {
       switch ($inputLine[0][$i]) {
          case "nom":
              $lineNom = $i;
              break;
          case "prenom":
              $linePrenom = $i;
              break;
          case "login":
              $lineLogin = $i;
              break;
          case "ECTS acquis":
              $lineECTS = $i;
              break;
          case "Moyenne":
              $lineMoyenne = $i;
              break;
          case "Choix 1":
              $lineC1 = $i;
              break;
          case "Choix 2":
              $lineC2 = $i;
              break;
          case "Choix 3":
              $lineC3 = $i;
              break;
          case "Choix 4":
              $lineC4 = $i;
              break;
          case "Choix 5":
              $lineC5 = $i;
              break;
          case "Choix 6":
              $lineC6 = $i;
              break;
          case "Choix 7":
              $lineC7 = $i;
              break;
          case "Choix 8":
              $lineC8 = $i;
              break;
          default:
              echo "Erreur: Un problème est survenue, les champs du fichier ne sont pas au bon format";
              exit();
              break;
        }
     }

     /*Si des informations importantes ne sont pas presentes*/
     if (!isset($lineNom) OR !isset($linePrenom) OR !isset($lineECTS) OR !isset($lineMoyenne)
         OR !isset($lineC1) OR !isset($lineC2)) {
       echo "Erreur: Un problème est survenue, le fichier ne contient pas tout les champs essentiel";
       exit();
     }

     /*Détermine le parcours en fonction des voeux entrer*/
     if (isset($lineC3) AND isset($lineC4) AND isset($lineC5) AND isset($lineC6)) {
       if (isset($lineC7) AND isset($lineC8)) {
         $parcours = 'gsi';
       } else {
         $parcours = 'mi';
       }
     } else {
       $parcours = 'mf';
     }


     if ($outFile = fopen('../membres/data/utilisateur.csv', "a+")) {
       while (!feof($outFile)){
         $outLine[] = fgetcsv($outFile, 1024, ';');
       }
     }

     /*Si il n'y a pas de login dans le fichier entré on les crées: prenom.nom*/
     if (!isset($lineLogin)){
        for ($i=1; $i < count($inputLine) - 1; $i++) {
          $login[$i]  = $inputLine[$i][$linePrenom] . "." . $inputLine[$i][$lineNom];
        }
     } else {
       for ($i=1; $i < count($inputLine) - 1; $i++) {
         $login[$i] = $inputLine[$i][$lineLogin];
       }
     }

     /*Ouverture du fichier de voeux*/
     $outFileVoeux = fopen('../membres/data/voeux.csv', "a+");

     /*Pour toutes les lignes du fichier entré*/
     for ($i=1; $i < count($inputLine) - 1; $i++) {
       /*Vérifie si le login existe déjà*/
       $exist = false;
       for ($j=1; $j < count($outLine) - 1; $j++) {
         if ($login[$i] == $outLine[$j][10]) {
           $exist = true;
         }
       }
       /*Si le login n'existe pas crée un nouvel utilisateur*/
       if (!$exist) {
         $id = count($outLine) + $i - 3;
         $pass = password_hash('defaut', PASSWORD_DEFAULT); //A changer ?

         $etudiant = array($id, $inputLine[$i][$lineNom], $inputLine[$i][$linePrenom], 'NULL',
                        'NULL', 'NULL', 'NULL', $parcours, $inputLine[$i][$lineECTS],
                        $inputLine[$i][$lineMoyenne], $login[$i], $pass, 'etudiant');

         fputcsv($outFile, $etudiant, ";"); //Insert les informations dans utilisateur.csv

        /*On ajoute les choix SSI ils sont définies (2/6/8)*/
        if (isset($lineC3) AND isset($lineC4) AND isset($lineC5) AND isset($lineC6)) {
          if (isset($lineC7) AND isset($lineC8)) {
            /*Si il y a 8 choix*/
            $voeux = array($id, $inputLine[$i][$lineC1], $inputLine[$i][$lineC2],
                           $inputLine[$i][$lineC3], $inputLine[$i][$lineC4], $inputLine[$i][$lineC5],
                           $inputLine[$i][$lineC6], $inputLine[$i][$lineC7], $inputLine[$i][$lineC8]);
          } else {
            /*Si il n'y a que 6 choix*/
            $voeux = array($id, $inputLine[$i][$lineC1], $inputLine[$i][$lineC2],
                           $inputLine[$i][$lineC3], $inputLine[$i][$lineC4], $inputLine[$i][$lineC5],
                           $inputLine[$i][$lineC6], 'NULL', 'NULL');
          }
        } else {
          /*Si il n'y a que 2 choix*/
          $voeux = array($id, $inputLine[$i][$lineC1], $inputLine[$i][$lineC2],
                         'NULL', 'NULL', 'NULL', 'NULL', 'NULL', 'NULL');
        }

         fputcsv($outFileVoeux, $voeux, ";"); //Insert les informations dans le csv
       }
     }

     fclose($outFileVoeux);
     fclose($outFile);
     echo "Success";

     include '../php/logFunction.php';
     addLog("Admin: Fichier étudiant charger.");
     exit();
?>
