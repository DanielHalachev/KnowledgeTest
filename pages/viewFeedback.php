<?php
require_once "../libs/JWT.php";
if (!isset($_COOKIE['token'])) {
  header('Location: question.php');
  exit;
}

$token = $_COOKIE['token'];

if (!JWT::isValid($token)) {
  header('Location: index.php');
  exit;
}
?>

<!DOCTYPE html>
<html lang="bg">
  <head>
    <title>Преглед на обратната връзка</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="./../css/common.css" rel="stylesheet" type="text/css">
    <link href="./../css/home.css" rel="stylesheet" type="text/css">
    <link href="./../css/viewFeedback.css" rel="stylesheet" type="text/css">
    <script src="https://kit.fontawesome.com/5c9473fc67.js" crossorigin="anonymous"></script>
    <script src="../js/logout.js" defer></script>
    <script src="../js/toggleNav.js"></script>
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
      const questionId = urlParams.get('questionId');
      fetch(`../api/questions/${questionId}`, {
        headers: {
          Authorization: `Bearer ${getToken()}`,
        },
      })
        .then((response) => response.json())
        .then((question) => {
          const questionSection = document.getElementById("questionSection");
          // questionSection.classList.add("item");

          const questionLabel = document.createElement("h3");
          questionLabel.innerHTML = question.label;
          questionSection.appendChild(questionLabel);

          const fieldset = document.createElement("form");

          fetch("../api/answers?questionId="+question.id)
            .then((response) => response.json())
            .then((answers) => {
              answers.forEach((answer) => {
                const label = document.createElement("label");
                if (question.isMultipleChoice) {
                  label.innerHTML = `<input type="checkbox" disabled ${ answer.isCorrect ? "checked" : "" }>${ answer.label }`;
                } else {
                  label.innerHTML = `<input type="radio" disabled ${ answer.isCorrect ? "checked" : ""}>${ answer.label }`;
                }
                fieldset.appendChild(label);
              }) 
            })
            .catch((error) => console.error("Couldn't fetch answers for question", error));

          questionSection.appendChild(fieldset);

          fetch(`../api/tests/` + question.testId)
            .then((response) => response.json())
            .then((test) => {
              const questionTest = document.createElement("p");
              questionTest.innerHTML = `<b>Тест: </b>${test.topic ?? "Неизвестен"}`;
              questionSection.appendChild(questionTest);
              const questionAuthor = document.createElement("p");
              questionAuthor.innerHTML = `<b>Автор на теста: </b>${test.author ?? "Неизвестен"}`;
              questionSection.appendChild(questionAuthor);
            })
            .catch((error) => console.log("Couldn't fetch name of test", error));

          const questionAim = document.createElement("p");
          questionAim.innerHTML = `<b>Цел: </b>${question.aim}`;
          questionSection.appendChild(questionAim);

          const questionIsMultipleChoice = document.createElement("p");
          questionIsMultipleChoice.innerHTML = `<b>С множествен избор: </b>${question.isMultipleChoice ? "Да" : "Не"}`;
          questionSection.appendChild(questionIsMultipleChoice);
        })
        .catch((error) =>
          console.error("Error fetching test information:", error)
        );

      fetch("../api/feedback?questionId="+questionId, {
        headers: {
          Authorization: `Bearer ${getToken()}`,
        },
      })
        .then((response) => response.json())
        .then((feedbacks) => {
          const feedbackSection = document.querySelector("#feedbacks");

          if (feedbacks.length === 0) {
            feedbackSection.innerHTML += "<p>Нямате получена обратна връзка.</p>";
          } else {
            feedbacks.forEach((feedback) => {
              const container = document.createElement("article");
              container.classList.add("feedback");

              const text = document.createElement("p");
              text.disabled = true;
              text.textContent = feedback.feedback;
              container.appendChild(text);

              const difficulty = document.createElement("span");
              difficulty.innerHTML = "Сложност: " + feedback.complexity + "/ 10";
              container.appendChild(difficulty);

              const slider = document.createElement("input");
              slider.disabled = true;
              slider.setAttribute("type", "range");
              slider.setAttribute("min", "1");
              slider.setAttribute("max", "10");
              slider.value = feedback.complexity;
              container.appendChild(slider);

              const button = document.createElement("button");
              button.innerHTML = '<span class="fa fa-trash">Изтриване</span>';
              button.setAttribute("type", "button");
              button.setAttribute("onclick", `deleteFeedback(${feedback.id})`);
              container.appendChild(button);

              feedbackSection.appendChild(container);
            });
          }
        })
        .catch((error) => console.error("Error fetching feedback:", error));
    });
    </script>
    <script>
    function deleteFeedback(id) {
      fetch("../api/feedback/"+id, {
        method: "DELETE",
        headers: {
          Authorization: `Bearer ${getToken()}`
        }
      })
        .then((response) => {
          if(response.ok) {
            location.reload();
          }
        })
        .catch((error) => console.error("Couldn't delete a feedback:", error));
    }
    </script> 
  </head>
  <body>
<?php
include "../includes/loggedInUserHeader.php";
?>
    <aside>
      <section class="activity" id="questionSection">
        <h2>Въпрос</h2>
      </section>
    </aside>
    <main>
      <section class="activity" id="feedbacks">
        <h2>Обратна връзка</h2>
      </section>
    </main>
    <?php
    include "../includes/footer.php";
    ?>
  </body>
</html>
