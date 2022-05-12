<?php
  session_start();
  if (!isset($_SESSION['id'])){
      header('Location: ../portail.php');
      exit();
  }

  /*Récupération de toutes les ligne du fichier*/
  if ($file = fopen('../membres/data/utilisateur.csv', "r")) {
    while (!feof($file)){
      $line[] = fgetcsv($file, 1024, ';');
    }
  } else {
    header('Location: ../portail.php?erreur=fileNotFound');
    exit();
  }
  fclose($file);

  /*Récuperation de la ligne correspondante à l'id de l'utilisateur*/
  for ($i=1; $i < count($line) - 1; $i++) {
      if ($line[$i][0] == $_SESSION['id']) {
          $id_line = $i;
      }
  }
  /*Récuperation des informations de l'utilisateur*/
  if (isset($line[$id_line])) {
    $nom = $line[$id_line][1];
    $prenom = $line[$id_line][2];
    $genre = $line[$id_line][3];
    $dateNaissance = $line[$id_line][4];
    $codePostal = $line[$id_line][5];
    $telephone = $line[$id_line][6];
    //$parcours = $line[$id_line][7];
    $login = $line[$id_line][10];
    $status = $line[$id_line][12];
  } else {
    header('Location: ../portail.php?erreur=idNotFound');
    exit();
  }
?>
<!DOCTYPE html>
<html lang="fr" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Editer</title>
  </head>
  <body>
      <div class="main-edition">
        <div class="information">
          <h1>Editer le profil</h1>
          <section class="information-personnelle">
              <h2>Information</h2>
              <form method="POST" action="enregistrementEdition.php" enctype="multipart/form-data">
                <section class="readonly">
                  <label>Status</label><input type="text" name="status" value="<?= $status?>" readonly>
                  <label>Nom</label><input type="text" name="nom" value="<?= $nom?>" readonly>
                  <label>Prenom</label><input type="text" name="prenom" value="<?= $prenom?>" readonly>
                  <label>Genre</label><input type="text" name="genre" value="<?= $genre?>" readonly>
                  <label>Date de naissance</label><input type="date" name="dateNaissance" value="<?= $dateNaissance?>" readonly>
                </section>
                <section>
                  <?php
                    if (isset($_GET['erreur'])) {
                        if ($_GET['erreur'] == 'emptyPostal') {
                            echo "<p style="."color:red;".">Erreur: Votre code postal ne peut pas être vide !</p>" ;
                        }
                    } ?>
                  <label>Code postal</label><input type="number" name="codePostal" value="<?= $codePostal?>">
                  <?php
                    if (isset($_GET['erreur'])) {
                        if ($_GET['erreur'] == 'emptyPhone') {
                            echo "<p style="."color:red;".">Erreur: Votre numéro de téléphone ne peut pas être vide !</p>" ;
                        }
                    } ?>
                  <label>Telephone</label><input type="text" name="telephone" value="<?= $telephone?>">
                </section>
                <section>
                  <?php
                    if (isset($_GET['erreur'])) {
                        if ($_GET['erreur'] == 'alreadyExist') {
                            echo "<p style="."color:red;".">Erreur: Cette utilisateur existe déjà !</p>" ;
                        } else if ($_GET['erreur'] == 'emptyLogin') {
                            echo "<p style="."color:red;".">Erreur: Votre login ne peut pas être vide !</p>" ;
                        }
                    } ?>
                  <label>Login</label><input type="text" name="login" value="<?= $login?>">
                  <?php
                    if (isset($_GET['erreur'])) {
                        if ($_GET['erreur'] == 'wrongMdp') {
                            echo "<p style="."color:red;".">Erreur: Les mots de passes ne sont pas identiques !</p>" ;
                        }
                    } ?>
                  <label>Mot de passe</label><input type="password" name="mdp">
                  <label>Confirmation mot de passe</label><input type="password" name="mdp2">
                </section>
                <?php
                  if (isset($_GET['erreur'])) {
                      if ($_GET['erreur'] == 'upload') {
                          echo "<p style="."color:red;".">Erreur: Impossible d'importer votre photo de profil !</p>" ;
                      } else if ($_GET['erreur'] == 'type') {
                          echo "<p style="."color:red;".">Erreur: Votre photo de profil doit être au format jpg, jpeg ou png !</p>" ;
                      } else if ($_GET['erreur'] == 'size') {
                          echo "<p style="."color:red;".">Votre photo de profil ne doit pas dépasser 2Mo !</p>" ;
                      }
                  } ?>
                <label>Photo de profil</label><input type="file" name="avatar">
                <input type="submit" name="submit" value="Valider">
              </form>
              <?php
                if (isset($_GET['success'])) {
                  echo "<p>Modification effectuée !</p>";
                } ?>
          </section>
        </div>
        <div class="deco">
          <form method="POST" action="<?php if ($status == "etudiant") echo "../membres/accueilEtudiant.php";
                                            else echo "../membres/accueilResponsable.php";?>">
            <input type="submit" name="retour" value="Retour"/>
          </form>
        </div>
      </div>
  </body>
</html>
