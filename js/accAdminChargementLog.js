const logDiv = document.getElementById('affichage-log');

$.ajax({
  url: "../php/getLog.php",
  type: "POST",
  success: function(result){
      console.log(result);
      if (result === "") return; //Evite le warning: Uncaught SyntaxError: JSON.parse: unexpected end of data

      const log = JSON.parse(result);

      /*Crée div et append a divLog*/
    }
  });
