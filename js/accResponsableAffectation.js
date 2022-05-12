function envoie(){
    const message = document.getElementById('message-affectation');

    var fichier = document.getElementById("placeFile").files[0];

    let formData = new FormData();
    formData.append("file", fichier);

    message.innerHTML = "Chargement...";

    $.ajax({
      url: "../php/affectation.php",
      type: "POST",
      data:  formData,
      contentType: false,
      cache: false,
      processData: false,
      enctype: 'multipart/form-data',
      success: function(result){
          message.innerHTML = result;
        }
      });
  }
