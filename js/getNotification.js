

$(document).ready(function () {
  $.ajax({
    type: 'POST',
    url: '../php/getNotification.php',
    success: function (result) {
        console.log(result);
      }
    });
});
