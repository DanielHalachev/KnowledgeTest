function redirectToPreview(event) {
  event.preventDefault();
  var input = document.getElementById('code-search').value;
  if(validateInput(input)){
    var url = "./provideTestFeedback.php?testId=" + input;
    fetchTest(url, input);
  }
}

function redirectToTest(event) {
  event.preventDefault();
  var input = document.getElementById('code-search').value;
  if(validateInput(input)){
    var url = "./makeTest.php?testId=" + input;
    fetchTest(url, input);
  }
}

function validateInput(input) {
  if(input === "") {
    const errorMessage = document.getElementById("error-message");
    errorMessage.showModal();
    return false;
  }
  return true;
}

function fetchTest(url, input){
  fetch("../api/tests/" + input)
    .then((response) => {
      if(response.ok) {
        window.location.href = url;
      }
      else {
        const errorMessage = document.getElementById("error-message");
        errorMessage.showModal();
      }
    })
    .catch((error) => {
      const errorMessage = document.getElementById("error-message").innerHTML = "Изглежда има проблем. Не можем да ви покажем теста в момента";
      errorMessage.showModal();
      console.error("Couldn't fetch test:", error);
    });
}
