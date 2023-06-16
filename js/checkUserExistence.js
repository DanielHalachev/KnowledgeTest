document.getElementById("sign-in").addEventListener("submit", function(event) {
  event.preventDefault(); 

  var email = document.getElementById("email").value;
  var password = document.getElementById("pasword").value;
  var errorMessage = document.getElementById("error-message");

  var httpRequest = new XMLHttpRequest();
  httpRequest.open("GET", "./../libs/checkTestExistence.php?code=" + encodeURIComponent(code), true);
  httpRequest.onload = function() {
    if (httpRequest.status === 200) {
      var exists = JSON.parse(httpRequest.responseText).exists;

      if (exists) {
        window.location.href = "./../pages/testPreview.php?code=" + encodeURIComponent(code);
      } else {
        errorMessage.showModal();
      }
    }
  };
  httpRequest.send();
});
