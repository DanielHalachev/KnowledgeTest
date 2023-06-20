<!DOCTYPE html>
<html lang="bg">
  <head>
    <title>Добре дошли</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="./../css/common.css" rel="stylesheet" type="text/css">
    <link href="./../css/provideTestFeedback.css" rel="stylesheet" type="text/css">
    <script src="../js/theme.js"></script>
    <script src="https://kit.fontawesome.com/5c9473fc67.js" crossorigin="anonymous"></script>
    <script>
    function getToken() {
      return document.cookie
        .split("; ")
        .find((row) => row.startsWith("token="))
        .split("=")[1];
    }
    </script>
    <script>
    document.addEventListener("DOMContentLoaded", () => {
      const urlParams = new URLSearchParams(window.location.search);
      const testId = urlParams.get('testId');

      const mainSection = document.querySelector("main section");

      fetch(`../api/tests/${testId}`)
        .then((response) => response.json())
        .then((test) => {
          title = document.querySelector("main section h2");
          title.innerHTML = test.topic ?? "Тест без име";
        })
        .catch((error) => console.error("Couldn't fetch test by Id: ", error));

      const form = document.querySelector("form");

      var questionCounter = 0;
      fetch(`../api/questions?testId=${testId}`)
        .then((response) => response.json())
        .then((questions) => {
          questions.forEach((question) => {
            questionCounter++;
            const questionSection = document.createElement("article");
            questionSection.classList.add("question");

            const questionLabel = document.createElement("h3");
            questionLabel.innerHTML = questionCounter + ". " + question.label;
            questionSection.appendChild(questionLabel);

            fetch("../api/answers?questionId=" + question.id)
              .then((response) => response.json())
              .then((answers) => {
                if(answers.length == 0) {
                  return;
                }
                answers.forEach((answer) => {
                  const label = document.createElement("label");
                  if (question.isMultipleChoice) {
                    label.innerHTML = `<input type="checkbox" disabled ${ answer.isCorrect ? "checked" : "" }>${ answer.label }`;
                  } else {
                    label.innerHTML = `<input type="radio" disabled ${ answer.isCorrect ? "checked" : ""}>${ answer.label }`;
                  }
                  questionSection.appendChild(label);
                }) 
              })
              .catch((error) => console.error("Couldn't fetch answers for question", error));
            form.appendChild(questionSection);

            const feedbackSection = document.createElement("article");
            feedbackSection.classList.add("feedback");
            feedbackSection.setAttribute("data-question", `${question.id}`);
            const text = document.createElement("textarea");
            feedbackSection.appendChild(text);
            const complexity = document.createElement("p");
            complexity.innerHTML = "Сложност от 1 до 10:";
            feedbackSection.appendChild(complexity);
            const slider = document.createElement("input");
            slider.setAttribute("type", "range");
            slider.setAttribute("min", "1");
            slider.setAttribute("max", "10");
            slider.setAttribute("step", "1");
            slider.setAttribute("value", "5");
            feedbackSection.appendChild(slider);
            form.appendChild(feedbackSection);
          });
        })
        .catch((error) => console.error("Couldn't fetch a question:", error));
      submitButton = document.createElement("button");
      submitButton.setAttribute("type", "submit");
      submitButton.innerHTML = "Изпращане";
      submitButton.setAttribute("onclick", "submitFeedback()");
      mainSection.appendChild(submitButton);
    });
    </script>
    <script>
    function submitFeedback(){
      feedbacks = document.querySelectorAll("form article.feedback");
      feedbacks.forEach((feedbackSection) => {
        const questionId = feedbackSection.dataset.question;
        console.log("questionId: ", questionId);
        fetch("../api/feedback", {
          method: "POST",
          body: JSON.stringify({
            feedback: feedbackSection.querySelector("textarea").value, 
            complexity: feedbackSection.querySelector('input[type="range"]').value,
            questionId: questionId})
        })
          .then((response) => {
            const resultDialog = document.querySelector("#resultDialog");
            const resultMessage = resultDialog.querySelector("form p");
            if(response.ok) {
              resultMessage.innerHTML = "Изпращането е успешно!";
            }
            else {
              resultMessage.innerHTML = "Изпращането не беше успешно. Опитайте отново."
            }
            resultDialog.showModal();
          })
          .catch((error) => console.error(`Could post feedback for question ${questionId}: `, error));
      })
    }
    </script>
  </head>
  <body>
<?php
include "../includes/guestUserHeader.php";
?>
    <main>
      <section class="activity">
        <h2></h2>
        <form>
        </form>
      </section>
      <dialog id="resultDialog">
        <form>
          <p></p>
          <button type="submit" formmethod="dialog">Затваряне</button>
          <button type="submit" formmethod="dialog" onclick='document.location = "./index.html"'>Към началната страница</button>
        </form>
      </dialog>
    </main>
    <?php
    include "../includes/footer.php";
    ?>
  </body>
</html>
