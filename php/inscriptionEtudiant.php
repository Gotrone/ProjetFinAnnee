<!DOCTYPE html>
<html lang="fr" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Inscription Eleves</title>
    <link rel="stylesheet" href="style.css">
  </head>
  <body>
    <div class="main-inscription-etudiant">
      <h1>Inscription étudiant:</h1>
      <form class="inscription" action="enregistrement.php" method="post">
        <section class="form-info-perso">
          <h2>Information personnelle</h2>
          <label>Nom</label><input type="text" name="nom" required>
          <label>Prenom</label><input type="text" name="prenom" required>
          <fieldset>
            <legend>Genre</legend>
            <label for="homme">Homme</label><input type="radio" id="homme" name="genre" value="Homme" required>
            <label for="femme">Femme</label><input type="radio" id="femme" name="genre" value="Femme" required>
          </fieldset>
          <label>Date de naissance</label><input type="date" name="dateNaissance" required>
          <label>Code postal</label><input type="number" name="codePostal" required>
          <label>Telephone</label><input type="text" name="telephone" required>
        </section>
        <section class="form-info-parcours">
          <h2>Information de parcours</h2>
            <label>Parcours</label>
            <select id="parcours" name="parcours" required>
              <option value="gsi">GSI</option>
              <option value="mi">MI</option>
              <option value="mf">MF</option>
            </select>
            <label>Points ECTS</label><input type="number" name="ects" min="0" step="1" required>
            <label>Moyenne</label><input type="number" name="moyenne" min="0" max="20" required>
        </section>
        <section class="form-info-connexion">
          <h2>Information de connexion</h2>
          <?php
            if (isset($_GET['erreur'])) {
                if ($_GET['erreur'] == 'alreadyExist') {
                    echo "<p style="."color:red;"."> Erreur: Cette utilisateur existe déjà ! </p>" ;
                }
            }
          ?>
          <label>Login</label><input type="text" name="login" required>
          <label>Mot de passe</label><input type="password" name="password" required>
        </section>
        <input type="hidden" name="status" value="etudiant">
        <input type="submit" name="submit" value="Valider">
      </form>
    </div>
    <a href="../portail.php">Retour au portail</a>
  </body>
</html>
