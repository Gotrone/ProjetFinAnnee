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
  } else {
    header('Location: ../portail.php?erreur=idNotFound');
    exit();
  }
?>
<!DOCTYPE html>
<html lang="fr" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Espace Responsable Admission</title>
    <script type="text/javascript" src="../js/jquery-3.6.0.js"></script>
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
        </section>
        <form method="POST" action="../php/editionProfil.php">
          <input type="submit" name="edition" value="Editer le profil"/>
        </form>
      </div>
    <div class="block-affectation">
      <span id="message-affectation"></span>
      <form action="../php/affectation.php" method="post" enctype="multipart/form-data">
        <label>Fichier des places d'options:</label><input type="file" id="placeFile" name="placeFile">
        <button type="button" name="button" onclick="envoie()">Lancer</button>
      </form>
      <script type="text/javascript" src="../js/accResponsableAffectation.js"></script>
    </div>
    <div class="block-statistique">
      <div id="block-main-statistique">
        <div class="block-statistique-select">
            <select id="select-option" name="options" onchange="affichageStat(this.value)">
              <option name='choix' value='default'>Choisir</option>
              <optgroup label="GSI">
                <option name='options' value='HPDAGSI'>HPDA</option>
                <option name='options' value='BIGSI'>BI</option>
                <option name='options' value='CSGSI'>CS</option>
                <option name='options' value='IACGSI'>IAC</option>
                <option name='options' value='IAPGSI'>IAP</option>
                <option name='options' value='ICCGSI'>ICC</option>
                <option name='options' value='INEMGSI'>INEM</option>
                <option name='options' value='VISUAGSI'>VISUA</option>
              </optgroup>
              <optgroup label="MI">
                <option name='choix' value='HPDAMI'>HPDA</option>
                <option name='choix' value='BIMI'>BI</option>
                <option name='choix' value='DSMI'>DS</option>
                <option name='choix' value='FTMI'>FT</option>
                <option name='choix' value='IACMI'>IAC</option>
                <option name='choix' value='IAPMI'>IAP</option>
              </optgroup>
              <optgroup label="MF">
                <option name='choix' value='ACTUMF'>Actu</option>
                <option name='choix' value='MMFMF'>MMF</option>
              </optgroup>
            </select>
        </div>
        <span id="message-debug"></span>
      </div>
      <script type="text/javascript" src="../js/accResStat.js"></script>
    </div>
    <div class="footer">
      <form method="POST" action="../php/deconnexion.php">
        <input type="submit" name="OUT" value="deconnexion"/>
      </form>
    </div>
  </body>
</html>
