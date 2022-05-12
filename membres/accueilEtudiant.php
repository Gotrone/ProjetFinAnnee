<?php
  session_start();
  if (!isset($_SESSION["id"])){
      header('Location: ../portail.php');
      exit();
  }

  /*Récupération de toutes les ligne du fichier*/
  if ($file = fopen('data/utilisateur.csv', "r")) {
    while (!feof($file)){
      $line[] = fgetcsv($file, 1024, ';');
    }
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
        $dateNaissance = $line[$id_line][4];
        $parcours = $line[$id_line][7];
        $ects = $line[$id_line][8];
        $moyenne = $line[$id_line][9];
  } else {
    header('Location: ../portail.php?erreur=idNotFound');
    exit();
  }
?>

<!DOCTYPE html>
<html lang="fr" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Espace Etudiant</title>
  </head>
  <body>
    <div class="block-nav">
      <nav>
        <a>Home</a>
        <a href="../php/messagerie.php">Messagerie</a>
      </nav>
    </div>
      <div class="main-accueilEtudiant">
        <div class="profil" style="float:left;">
          <img id="profil-avatar" src="avatars/<?= $_SESSION['id']?>"
               onerror="this.onerror=null; this.src='avatars/default.jpg'" width="100" height="100">
          <section id="info-etudiant">
            <p>Nom: <?= $nom ?></p>
            <p>Prenom: <?= $prenom ?></p>
            <p>Date de naissance: <?= $dateNaissance ?></p>
            <p>Parcours: <?= $parcours ?></p>
            <p>Moyenne: <?= $moyenne ?></p>
            <p>Point ECTS: <?= $ects ?></p>
          </section>
          <form method="POST" action="../php/editionProfil.php">
            <input type="submit" name="edition" value="Editer le profil"/>
          </form>
        </div>
        <div class="main-center" style="float:left;">
          <h2>Espace étudiant</h2>
          <div class="main-choix">
            <span id="message-choix"></span>
            <?php require_once('../php/selectOptions.php')?>
            <script type="text/javascript" src="../js/etudiantSelectOption.js"></script>
            <input type="hidden" id="parcours" name="parcours" value="<?= $parcours?>">
            <button type="button" name="button" onclick="resolve()">Mettre à jours</button>
          </div>
          <script type="text/javascript" src="../js/accEtudiantVoeux.js"></script>
        </div>
        <div class="footer" style="float:left;">
          <form method="POST" action="../php/deconnexion.php">
            <input type="submit" name="out" value="Déconnexion"/>
          </form>
        </div>
      </div>
  </body>
</html>
