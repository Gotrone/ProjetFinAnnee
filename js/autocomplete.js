/*
  Type: 0: Tout les logins
        1: Tout les logins sauf status admin
        2: Tout les logins status etudiant
*/
function autocompleteLogin(input, type){
  xhttp = new XMLHttpRequest();

  xhttp.onreadystatechange = function(){
    if (this.readyState == 4 && this.status == 200) {
        if (this.responseText == "") return; //Evite le warning: Uncaught SyntaxError: JSON.parse: unexpected end of data
        const logins = JSON.parse(this.responseText); //Récupération des logins

        removeElements(); // Supprimme les anciennes suggestions

        for (var i = 0; i < logins.length; i++) {
            /*Si le champ entrée correspond au debut d'un login*/
            if (logins[i].toLowerCase().startsWith(input.value.toLowerCase()) && input.value != "") {
              //Créé une liste qui contiendra les logins suggerés et ajoute des propriété
              let listItem = document.createElement("li");
              listItem.classList.add("list-items");
              listItem.style.cursor = "pointer";
              listItem.setAttribute("onclick", "displayNames('" + logins[i] + "')");

              /*Affiche en gras la partie en commun du mot entrée et des logins*/
              let suggestion = "<b>" + logins[i].substr(0, input.value.length) + "</b>";
              suggestion += logins[i].substr(input.value.length);

              /*Ajoute les logins suggerés à la list*/
              listItem.innerHTML = suggestion;
              document.querySelector(".login-suggestion").appendChild(listItem);
            }
        }
    }
  };

  xhttp.open("POST", "../php/getLogin.php", true);
  xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xhttp.send("type="+type);
}


function displayNames(value) {
  const input = document.getElementsByClassName('input-autocomplete');
  input[0].value = value;
  removeElements();
}

function removeElements() {
  let items = document.querySelectorAll(".list-items");
  items.forEach((item) => {
    item.remove();
  });
}
