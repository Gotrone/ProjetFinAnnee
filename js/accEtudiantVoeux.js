function resolve(){
    //Récuperation du parcours de l'étudiant
    var parcours = document.getElementById('parcours').value;

    /*Vérification que le nombre de choix est cohérant avec le parcours effectuer*/
    var message = document.getElementById("message-choix");
    switch (parcours) {
      case 'gsi':
        /*Récuperation des choix entrées dans le tableau*/
        var choix = [];
        for (var i = 0; i < 8; i++) {
          choix[i] = document.getElementById('c' + i).value;
        }

        /*Filtre la liste de choix pour ne garder que les elements non nul*/
        choix = choix.filter(element => {
          return element !== 'default';
        });

        /*Vérification des choix entrés*/
        if (choix.length != 8) {
            message.innerHTML = "Erreur: Vous devez entrer 8 choix !";
            return;
        }
        break;
      case 'mi':
        /*Récuperation des choix entrées dans le tableau*/
        var choix = [];
        for (var i = 0; i < 6; i++) {
          choix[i] = document.getElementById('c' + i).value;
        }

        /*Filtre la liste de choix pour ne garder que les elements non nul*/
        choix = choix.filter(element => {
          return element !== 'default';
        });

        /*Vérification des choix entrés*/
        if (choix.length != 6) {
            message.innerHTML = "Erreur: Vous devez entrer 6 choix !";
            return;
        }
        break;
      case 'mf':
        /*Récuperation des choix entrées dans le tableau*/
        var choix = [];
        for (var i = 0; i < 2; i++) {
          choix[i] = document.getElementById('c' + i).value;
        }

        /*Filtre la liste de choix pour ne garder que les elements non nul*/
        choix = choix.filter(element => {
          return element !== 'default';
        });

        /*Vérification des choix entrés*/
        if (choix.length != 2) {
            message.innerHTML = "Erreur: Vous devez entrer 2 choix !";
            return;
        }
        break;
      default:
        message = "Erreur: Parcours non reconnu !";
        return;
    }

    xhttp = new XMLHttpRequest();

    xhttp.onreadystatechange = function(){
      if (this.readyState == 4 && this.status == 200) {
          message.innerHTML = this.responseText;
      }
    };

    xhttp.open("POST", "../php/enregistrementChoix.php", true);
    xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhttp.send('choix='+choix);
}
