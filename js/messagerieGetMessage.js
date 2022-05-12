$(document).ready(function () {
  var delay = 5000;
  setTimeout(getMessage, delay);

  function getMessage() {
    const contact = document.getElementById('nom-contact').innerHTML;
    
    if (contact !== ''){
      $.ajax({
        type: 'POST',
        url: 'messagerieGetMessage.php',
        data: {"contactLogin": contact},
        success: function (result) {
          console.log(result);
        }
      });
    }
    setTimeout(getMessage, delay);
  }
});
