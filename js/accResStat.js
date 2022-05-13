function affichageStat(value){
  const message = document.getElementById('message-debug');

  $.ajax({
    url: "../php/affectationStat.php",
    type: "POST",
    data: {"option": value},
    success: function(result){
        message.innerHTML = result;
      }
    });
}
