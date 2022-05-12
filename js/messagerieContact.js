function getContact(){
  const message = document.getElementById('message-debug');
  const contactDiv = document.getElementById('body-contact');

  $.ajax({
    url: "../php/messagerieGetContact.php",
    type: "POST",
    success: function(result){
        console.log(result);
        if (result === "") return; //Evite le warning: Uncaught SyntaxError: JSON.parse: unexpected end of data

        const contact = JSON.parse(result);

        for (var i = 0; i < contact.length; i++) {
          let exist = false;
          for (var j = 0; j < contactDiv.childElementCount; j++) {
              if (contact[i] == contactDiv.childNodes[j].firstChild.innerHTML) {
                exist = true;
                break;
              }
          }

          if (!exist) {
            const newParagraphe = document.createElement('p');
            newParagraphe.innerHTML = contact[i];

            const newDiv = document.createElement('div');
            newDiv.setAttribute('class', 'contact-element');
            newDiv.onclick = setContact;
            newDiv.setAttribute('value', contact[i]);
            newDiv.style.width = "100px";
            newDiv.style.height = "100px";
            newDiv.style.border = "solid 1px black" ;
            newDiv.appendChild(newParagraphe);

            contactDiv.appendChild(newDiv);
          }
        }
      }
    });
}

function addContact(){
  const message = document.getElementById('message-debug');
  const newContact = document.getElementById('ajoutContact').value;

  document.getElementById('ajoutContact').value = "";

  $.ajax({
    url: "../php/messagerieAjoutContact.php",
    type: "POST",
    data: {"newContact": newContact},
    success: function(result){
        message.innerHTML = result;
        getContact();
      }
  });
}

function setContact(e){
    let element;
    if (e.target.className !== "contact-element") {
      element = e.target.parentNode;
    } else {
      element = e.target;
    }

    const contactSpan = document.getElementById('nom-contact');
    contactSpan.innerHTML = element.getAttribute('value');
}
