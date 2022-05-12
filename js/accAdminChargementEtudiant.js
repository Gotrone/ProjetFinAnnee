function envoie(){
    const message = document.getElementById('message-ajout-etudiant');

    var fichier = document.getElementById("etudiantFile").files[0];

    let formData = new FormData();
    formData.append("file", fichier);

    message.innerHTML = "Chargement...";

    $.ajax({
      url: "../php/ajoutEtudiant.php",
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
