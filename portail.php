<!DOCTYPE html>
<html lang="fr" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Portail de connexion</title>
    <link rel="stylesheet" type="text/css" href="portail.css"/>
    <script src="portail.js"></script>
  </head>
  <body>

      <div class="nav-bar">

        <div id="deco-menu-1"></div>
        <div id="deco-menu-2"></div>
        <div id="deco-menu-3"></div>

        <div class="button" id="home-button" 
          onmouseover="SelectedColor('home-button')"
          onmouseout="DefaultColor('home-button')">
          Acceuil
        </div>
        <div class="button" id="connexion-button"
          onmouseover="SelectedColor('connexion-button')"
          onmouseout="DefaultColor('connexion-button')">
          <a href="portail.php" style="text-decoration: none; color: inherit;">
          Connexion
        </div>
        <div class="button" id="inscrit-e-button"
          onmouseover="SelectedColor('inscrit-e-button')"
          onmouseout="DefaultColor('inscrit-e-button')">
          <a href="php/inscriptionEtudiant.php" style="text-decoration: none; color: inherit;">
          Inscription Etudiant
        </div>
        <div class="button" id="inscrit-r-button"
          onmouseover="SelectedColor('inscrit-r-button')"
          onmouseout="DefaultColor('inscrit-r-button')">
          <a href="php/inscriptionRA.php" style="text-decoration: none; color: inherit;">
          Inscription Responsable
        </div>
        <div class="button" id="a-propos">
          A propos
        </div>
      </div>

      <div class="main-portail">
        <h1 id="title">Portail de Connexion</h1>
        <div class="portail-connexion">
          <?php
            if (isset($_GET['erreur'])) {
                if ($_GET['erreur'] == 'mdp') {
                    echo "<p style="."color:red;"."> Erreur: Identifiant ou mot de passe incorrect ! </p>" ;
                } else if ($_GET['erreur'] == 'bd'){
                    echo "<p style="."color:red;"."> Erreur: Connexion à la base de donnée impossible ! </p>" ;
                }
            }
          ?>
          <form action="php/verificationConnexion.php" method="post">
            <div class="button-connect" id="inscrit-r-button">
              Inscription Responsable
            </div>
            <input id="login-input" type="text" value="login" name="login" required>
            <div class="button-connect" id="inscrit-r-button">
              Inscription Responsable
            </div>
            <input id="pswd-input" type="password" name="password" required>
            <input id="confirm" type="submit" name="submit" value="Valider">
          </form>
        </div>
        <div class="portail-inscription">
            <p>Inscription étudiant =></p><a href="php/inscriptionEtudiant.php">ICI</a> <br>
            <p>Inscription responsable admission =></p><a href="php/inscriptionRA.php">ICI</a> <br>
            <span id="message-admin"></span>
            <p>Ajout administrateur =></p><button type="button" name="button" onclick="ajoutAdmin()">Ajouter</button><br>
            <span id="message-reinitialiser"></span>
            <p>Réinitialisation CSV =></p><button type="button" name="button" onclick="reinitialiser()">Réinitialiser</button><br>
        </div>
      </div>
  </body>
</html>

<script type="text/javascript">
    function ajoutAdmin(){
        const message = document.getElementById("message-admin");

        xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function(){
          if (this.readyState == 4 && this.status == 200) {
              message.innerHTML = this.responseText;
          }
        };

        xhttp.open("POST", "php/ajoutAdmin.php", true);
        xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhttp.send();
    }

    function reinitialiser(){
        const message = document.getElementById("message-reinitialiser");

        xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function(){
          if (this.readyState == 4 && this.status == 200) {
              message.innerHTML = this.responseText;
          }
        };

        xhttp.open("POST", "php/reinitialiser.php", true);
        xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhttp.send();
    }
</script>
