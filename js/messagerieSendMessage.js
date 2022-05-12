function sendMessage(){
    const contactSpan = document.getElementById('nom-contact');
    const contact = contactSpan.innerHTML;
    if (contact == "") return;

    const messageToSend = document.getElementById('messageUtilisateur').value;
    document.getElementById('messageUtilisateur').value = "";

    $.ajax({
      url: "../php/messagerieSendMessage.php",
      type: "POST",
      data: {"contactLogin": contact, "message": messageToSend},
      success: function(result){
          console.log(result);
        }
    });
}
