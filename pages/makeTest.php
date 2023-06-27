<!DOCTYPE html>
<html lang="bg">
  <head>
    <title>Добре дошли</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="./../css/common.css" rel="stylesheet" type="text/css">
    <link href="./../css/provideTestFeedback.css" rel="stylesheet" type="text/css">
    <script src="https://kit.fontawesome.com/5c9473fc67.js" crossorigin="anonymous"></script>
    <script src="../js/theme.js"></script>
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
          title.textContent = test.topic ?? "Тест без име";
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
            questionSection.setAttribute("data-question", `${question.id}`);
            if(question.isMultipleChoice === true) {
              questionSection.classList.add("multiple-choice");
            }

            const questionLabel = document.createElement("h3");
            questionLabel.textContent = questionCounter + ". " + question.label;
            questionSection.appendChild(questionLabel);

            fetch("../api/answers?questionId=" + question.id)
              .then((response) => response.json())
              .then((answers) => {
                if (answers.length == 0) {
                  return;
                }
                answers.forEach((answer) => {
                  const label = document.createElement("label");
                  const input = document.createElement("input");
                  input.name = question.id;
                  input.value = answer.id;
                  if (question.isMultipleChoice) {
                    input.type = "checkbox";
                  } else {
                    input.type = "radio";
                  }
                  label.appendChild(input);
                  const text = document.createTextNode(answer.label);
                  label.appendChild(text);
                  questionSection.appendChild(label);
                })
              })
              .catch((error) => console.error("Couldn't fetch answers for question", error));
            form.appendChild(questionSection);
          });
        })
        .catch((error) => console.error("Couldn't fetch a question:", error));
      submitButton = document.createElement("button");
      submitButton.setAttribute("type", "submit");
      submitButton.textContent = "Изпращане";
      submitButton.setAttribute("onclick", "submitAnswers()");
      mainSection.appendChild(submitButton);
    });
    </script>
    <script>
    function submitAnswers() {
      const questions = document.querySelectorAll("form article.question");
      const numberOfQuestions = questions.length;
      let result = 0;
      const resultDialog = document.querySelector("#resultDialog");
      const resultMessage = resultDialog.querySelector("form p");

      const fetchPromises = Array.from(questions).map((questionSection) => {
        const questionId = questionSection.dataset.question;
        const selectedAnswers = Array.from(questionSection.querySelectorAll("input:checked"));
        return fetch("../api/answers?questionId=" + questionId)
          .then((response) => response.json())
          .then((answers) => {
            const correctAnswers = answers.filter((answer) => answer.isCorrect).map((answer) => answer.id);
            const incorrectAnswers = answers.filter((answer) => !answer.isCorrect).map((answer) => answer.id);
            let score = 0;

            // if (questionSection.classList.contains("multiple-choice")) {
              const correctSelectedAnswers = selectedAnswers.filter((selectedAnswer) => {
                return correctAnswers.some((correctAnswer) => correctAnswer == selectedAnswer.value);
              });
              correctSelectedAnswers.forEach((input) => {
                input.parentNode.classList.add("correct");
                input.parentNode.innerHTML += ' <span class="fa fa-check"></span>'
              })
              const incorrectSelectedAnswers = selectedAnswers.filter((selectedAnswer) => {
                return incorrectAnswers.some((incorrectAnswer) => incorrectAnswer == selectedAnswer.value);
              });
              incorrectSelectedAnswers.forEach((input) => {
                input.parentNode.classList.add("incorrect");
                input.parentNode.innerHTML += ' <span class="fa fa-xmark"></span>'
              })
              const correctRatio = correctSelectedAnswers.length / correctAnswers.length;
              const incorrectRatio = incorrectSelectedAnswers.length / answers.length;

              score = Math.max(correctRatio - incorrectRatio, 0);
            // } else {
            //   const selectedAnswer = selectedAnswers[0];
            //   const isCorrect = correctAnswers.some((correctAnswer) => correctAnswer === selectedAnswer.value);
            //   score = isCorrect ? 1 : 0;
            // }
            return score;
          })
          .catch((error) => console.error(`Could post answers for question ${questionId}: `, error));
      });
      Promise.all(fetchPromises)
        .then((scores) => {
          result = scores.reduce((total, score) => total + score, 0);
          const totalScore = (result / numberOfQuestions) * 100;
          resultMessage.textContent = `Вашият резултат е ${totalScore.toFixed(2)}%`;
          resultDialog.showModal();
        })
        .catch((error) => console.error("Couldn't fetch answers: ", error));
    }
    </script>
  </head>
  <body>
<?php
include "../includes/guestUserHeader.php";
?>
    <main>
      <section class="activity test">
        <h2></h2>
        <form>
        </form>
      </section>
      <dialog id="resultDialog">
        <form>
          <p></p>
          <button type="submit" formmethod="dialog">Затваряне</button>
          <button type="submit" formmethod="dialog" onclick='document.location = "./index.php"'>Към началната страница</button>
        </form>
      </dialog>
    </main>
    <?php
    include "../includes/footer.php";
    ?>
  </body>
</html>

