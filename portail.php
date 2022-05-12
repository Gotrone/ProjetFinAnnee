<!DOCTYPE html>
<html lang="fr" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Portail de connexion</title>
  </head>
  <body>
      <div class="main-portail">
        <h1>Portail de Connexion</h1>
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
            <label>Login</label><input type="text" name="login" required>
            <label>Mot de passe</label><input type="password" name="password" required>
            <input type="submit" name="submit" value="Valider">
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
