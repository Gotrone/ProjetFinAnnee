<?php
  session_start();
  if (!isset($_SESSION["id"])){
      header('Location: ../portail.php');
  }
?>
<!DOCTYPE html>
<html lang="fr" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Espace Administrateur</title>
    <script type="text/javascript" src="../js/jquery-3.6.0.js"></script>
  </head>
  <body>
    <div class="block-nav">
      <nav>
        <a>Home</a>
        <a href="../php/messagerie.php">Messagerie</a>
      </nav>
    </div>
    <div class="ajout-etudiant">
      <span id="message-ajout-etudiant"></span>
      <form action="../php/ajoutEtudiant.php" method="post" enctype="multipart/form-data">
        <label>Fichier étudiant:</label><input type="file" id="etudiantFile" name="etudiantFile">
        <button type="button" name="button" onclick="envoie()">Valider</button>
      </form>
    </div>
    <div class="block-log">
      <h2>Dernier log</h2>
      <div id="affichage-log"></div>
    </div>
    <div class="footer">
      <form method="POST" action="../php/deconnexion.php">
        <input type="submit" name="OUT" value="deconnexion"/>
      </form>
    </div>
  </body>
  <script type="text/javascript" src="../js/accAdminChargementEtudiant.js"></script>
  <script type="text/javascript" src="../js/accAdminChargementLog.js"></script>
</html>
