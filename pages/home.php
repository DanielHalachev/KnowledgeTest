<?php
require_once "../libs/JWT.php";
if (!isset($_COOKIE['token'])) {
  header('Location: login.php');
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
    <title>Добре дошли</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="./../css/common.css" rel="stylesheet" type="text/css">
    <link href="./../css/home.css" rel="stylesheet" type="text/css">
    <script src="../js/theme.js"></script>
    <script src="https://kit.fontawesome.com/5c9473fc67.js" crossorigin="anonymous"></script>
    <script src="../js/logout.js" defer></script>
    <script src="../js/toggleNav.js"></script>
    <script src="../js/accountActions.js"></script>
    <script>
    function openImageUploadDialog() {
      document.getElementById('imageUpload').click();
    }
    function getToken() {
      return document.cookie
        .split("; ")
        .find((row) => row.startsWith("token="))
        .split("=")[1];
    }
    </script>
    <script>
    function handleImageUpload(event) {
      fetch("../api/users", {
        headers: {
          Authorization: `Bearer ${getToken()}`
        }
      })
        .then(response => response.json())
        .then(data => {
          const file = event.target.files[0];
          const userId = data[0].id;
          console.log(file);
          if (file && userId) {
            const fileExtension = file.name.split('.').pop();
            const newFileName = `${userId}.${fileExtension}`;
            const formData = new FormData();
            formData.append('profilePicture', file, newFileName);

            var xhr = new XMLHttpRequest();
            xhr.open("POST", "../img/uploadImage.php", true);
            xhr.onreadystatechange = function() {
              if (xhr.readyState === 4 && xhr.status === 200) {
                console.log(xhr.responseText);
              }
            };
            xhr.send(formData);

            fetch("../api/users/"+userId, {
              method: "PATCH",
              headers: {
                Authorization: `Bearer ${getToken()}`
              }, 
              body: JSON.stringify({profilePicture: newFileName})
            })
              .then(response => {
                if (response.ok) {
                  console.log('Image uploaded');
                  location.reload();
                } else {
                  console.error('Error uploading image');
                }
              })
              .catch(error => {
                console.error('Error uploading image:', error);
              });
          }
          const imgSrc = data[0].profilePicture
            ? `../img/${data[0].profilePicture}`
            : "../img/profile.png";
          document.getElementById("profilePicture").src = imgSrc;
        })
        .catch(error => {
          console.error("Error fetching user data:", error);
        });
    }
    </script>
    <script>
    document.addEventListener("DOMContentLoaded", () => {
      const token = getToken();
      fetch("../api/users", {
        headers: {
          Authorization: `Bearer ${token}`
        }
      })
        .then(response => response.json())
        .then(data => {
          const {email, firstName, lastName, profilePicture } = data[0];

          document.getElementById("firstName").textContent = firstName;
          document.getElementById("lastName").textContent = lastName;
          document.getElementById("email").textContent = email;

          const imgSrc = profilePicture ? `../img/${profilePicture}` : "../img/profile.png";
          document.getElementById("profilePicture").src = imgSrc;
        })
        .catch(error => {
          console.error("Error fetching user data:", error);
        });

      fetch("../api/tests?sort=id desc", {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      })
        .then((response) => response.json())
        .then((tests) => {
          const testsSection = document.querySelector("#latest-tests");

          if (tests.length === 0) {
            testsSection.innerHTML += "<p>Нямате създадени тестове.<p>";
          } else {
            tests.forEach((test) => {
              fetch(`../api/questions?testId=${test.id}&size=-1`, {
                headers: {
                  Authorization: `Bearer ${token}`,
                },
              })
                .then((response) => response.json())
                .then((questions) => {
                  const testSection = document.createElement("section");
                  testSection.classList.add("item");

                  const testLink = document.createElement("a");
                  testLink.href = `provideTestFeedback.php?testId=${test.id}`;

                  const testTitle = document.createElement("h3");
                  testTitle.textContent = test.topic ?? "Без име";

                  const testAuthor = document.createElement("p");
                  testAuthor.textContent = `Автор: ${test.author ?? "Неизвестен автор"}`;

                  const testQuestions = document.createElement("p");
                  testQuestions.textContent = `Брой въпроси: ${questions.length}`;

                  testLink.appendChild(testTitle);
                  testSection.appendChild(testLink);
                  testSection.appendChild(testAuthor);
                  testSection.appendChild(testQuestions);
                  testsSection.appendChild(testSection);
                })
                .catch((error) =>
                  console.error("Error fetching questions:", error)
                );
            });
          }
        })
        .catch((error) => console.error("Error fetching tests:", error));

      fetch("../api/questions?sort=id desc", {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      })
        .then((response) => response.json())
        .then((questions) => {
          const questionsSection = document.querySelector("#latest-questions");

          if (questions.length === 0) {
            questionsSection.innerHTML += "<p>Нямате създадени въпроси.</p>";
          } else {
            questions.forEach((question) => {
              fetch(`../api/tests/${question.testId}`, {
                headers: {
                  Authorization: `Bearer ${token}`,
                },
              })
                .then((response) => response.json())
                .then((test) => {
                  const questionSection = document.createElement("section");
                  questionSection.classList.add("item");

                  const questionLink = document.createElement("a");
                  questionLink.href = `./viewFeedback.php?questionId=${question.id}`;

                  const questionLabel = document.createElement("h3");
                  questionLabel.textContent = question.label;

                  const questionTest = document.createElement("p");
                  questionTest.textContent = `Тест: ${test.topic ?? "Тест без име"}`;

                  const questionAim = document.createElement("p");
                  questionAim.textContent = `Цел: ${question.aim ?? ""}`;

                  const questionIsMultipleChoice = document.createElement("p");
                  questionIsMultipleChoice.textContent = `С множествен избор: ${
question.isMultipleChoice ? "Да" : "Не"
}`;

                  questionLink.appendChild(questionLabel);
                  questionSection.appendChild(questionLink);
                  questionSection.appendChild(questionTest);
                  questionSection.appendChild(questionAim);
                  questionSection.appendChild(questionIsMultipleChoice);
                  questionsSection.appendChild(questionSection);
                })
                .catch((error) =>
                  console.error("Error fetching test information:", error)
                );
            });
          }
        })
        .catch((error) => console.error("Error fetching questions:", error));
    });
    </script>
  </head>
  <body>
<?php
include "../includes/loggedInUserHeader.php";
?>
    <aside>
      <section class="activity">
        <h2>Профил</h2>
        <img id="profilePicture" src="./../img/profile.png" alt="profile picture" onclick="openImageUploadDialog()" class="blur-image"/>
        <!-- <span class="image-overlay">Промяна</span> -->
        <p id="firstName">FirstName</p>
        <p id="lastName">LastName</p>
        <p id="email">Email</p>
        <input type="file" name="profilePicture" id="imageUpload" accept="image/*" style="display: none;" onchange="handleImageUpload(event)">
        <button type="button" onclick="logout()"><span class="fa fa-power-off"></span>Изход</button>
        <button type="button" onclick="document.getElementById('changePasswordDialog').show()"><span class="fa fa-key"></span>Смяна на паролата</button>
        <dialog id="changePasswordDialog">
          <form>
            <input type="password" id="newPassword" placeholder="Въведете новата парола">
            <input type="password" id="newPasswordRepeated" placeholder="Повторете новата парола">
            <p></p>
            <button type="submit" formmethod="dialog">Отказ</button>
            <button type="submit" formmethod="dialog" onclick="changePassword()">Смяна</button>
          </form> 
        </dialog>
        <button type="button" onclick="document.getElementById('deleteAccountDialog').show()"><span class="fa fa-user-slash"></span>Изтриване на профила</button>
        <dialog id="deleteAccountDialog">
          <form>
            <p>Сигурни ли сте, че искате да изтриете акаунта си и всички данни в него?</p>
            <button type="submit" formmethod="dialog">Отказ</button>
            <button type="submit" formmethod="dialog" onclick="deleteAccount()">Изтриване</button>
          </form> 
        </dialog>
      </section>
    </aside>
    <main>
      <section class="activity" id="latest-tests">
        <h2>Последни тестове</h2>
      </section>
      <section class="activity" id="latest-questions">
        <h2>Последни въпроси</h2>
      </section>
    </main>
    <?php
    include "../includes/footer.php";
    ?>
  </body>
</html>

