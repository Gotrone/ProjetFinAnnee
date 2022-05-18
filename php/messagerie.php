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
    <title>Messagerie</title>
    <script type="text/javascript" src="../js/jquery-3.6.0.js"></script>
    <link rel="stylesheet" href="../css/autocomplete.css">
  </head>
  <body>
    <div class="block-communication">
      <div class="block-message" >
        <span id="nom-contact"></span>
      </div>
      <div class="envoie-message">
          <input type="text" id="messageUtilisateur">
          <button type="button" name="envoieMessageUtilisateur" onclick="sendMessage()">Envoyer</button>
          <script type="text/javascript" src="../js/messagerieSendMessage.js"></script>
      </div>
    </div>
    <div class="block-contact">
      <div class="head-contact">
        <div class="autocomplete" style="width:300px;">
          <input class="input-autocomplete" type="text" id="ajoutContact" placeholder="Entrer un login..." onkeyup="autocompleteLogin(this, 0)">
        </div>
        <ul class="login-suggestion"></ul>
        <button type="button" name="buttonAjoutContact" onclick="addContact()">Ajouter</button>
        <script type="text/javascript" src="../js/autocomplete.js"></script>
      </div>
      <div class="body-contact" id="body-contact"></div>
      <span id="message-debug"></span>
    </div>
    <script type="text/javascript" src="../js/messagerieContact.js" onload="getContact()"></script>
    <div class="footer">
      <form method="POST" action="../php/deconnexion.php">
        <input type="submit" name="OUT" value="deconnexion"/>
      </form>
    </div>
  </body>
</html>
<script type="text/javascript" src="../js/messagerieGetMessage.js"></script>
