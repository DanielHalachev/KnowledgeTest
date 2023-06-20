<!DOCTYPE html>
<html lang="bg">
  <head>
    <title>Въпроси</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="./../css/common.css" rel="stylesheet" type="text/css">
    <link href="./../css/home.css" rel="stylesheet" type="text/css">
    <link href="./../css/questions.css" rel="stylesheet" type="text/css">
    <script src="../js/theme.js"></script>
    <script src="https://kit.fontawesome.com/5c9473fc67.js" crossorigin="anonymous"></script>
    <script src="../js/logout.js" defer></script>
    <script src="../js/toggleNav.js"></script>
    <script src="../js/questionTable.js"></script>
    <script src="../js/exportToMoodle.js"></script>
    <script>
    function getToken() {
      return document.cookie
        .split("; ")
        .find((row) => row.startsWith("token="))
        .split("=")[1];
    }
    </script>
    <script>
    function getCheckedIds(){
      var checkboxes = document.querySelectorAll('.questions-table input[type="checkbox"].single-select');
      var checkedQuestionIds = [];
      checkboxes.forEach((element) => {
        if(element.checked) {
          checkedQuestionIds.push(element.dataset.questionid);
        }
      });
      return checkedQuestionIds;
    }
    function moveQuestions(idList) {
      var selectValue = document.querySelector("#actions .tSelect").value;
      if (selectValue !== "") {
        idList.forEach(function(id) {
          var url = '../api/questions/' + parseInt(id);
          fetch(url, {
            method: 'PATCH',
            headers: {
              Authorization: `Bearer ${getToken()}`
            }, 
            body: JSON.stringify({ testId: selectValue })
          })
            .then(function(response) {
              if (response.ok) {
                location.reload();
              } else {
                console.error('Failed to move question ' + id + '.');
              }
            })
            .catch(function(error) {
              console.error('An error occurred while moving question ' + id + ':', error);
            });
        });
      }
    }

    function changeQuestionTypes (idList) {
      var selectValue = document.querySelector("#actions .qtSelect").value;
      if (selectValue !== "") {
        idList.forEach(function(id) {
          var url = '../api/questions/' + parseInt(id);
          fetch(url, {
            method: 'PATCH',
            headers: {
              Authorization: `Bearer ${getToken()}`
            }, 
            body: JSON.stringify({ questionType: selectValue })
          })
            .then(function(response) {
              if (response.ok) {
                location.reload();
                console.log('Question ' + id + ' moved successfully.');
              } else {
                console.error('Failed to change question type' + id + '.');
              }
            })
            .catch(function(error) {
              console.error('An error occurred while changing question type' + id + ':', error);
            });
        });
      }
    }

    function deleteQuestions (idList) {
      idList.forEach(function(id) {
        var url = '../api/questions/' + parseInt(id);
        fetch(url, {
          method: 'DELETE',
          headers: {
            Authorization: `Bearer ${getToken()}`
          } 
        })
          .then(function(response) {
            if (response.ok) {
              location.reload();
            } else {
              console.error('Failed to delete question ' + id + '.');
            }
          })
          .catch(function(error) {
            console.error('An error occurred while deleting question ' + id + ':', error);
          });
      });
    }
    </script>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
      fetchAndPopulateTests(getToken());
      fetchAndPopulateQuestionTypes();
      populateIsMultipleChoiceSelect();
      applyFilters(getToken());
    });
    </script>
    <script>
    const url = `../api/tests`;
    function createTest(){
      fetch(url, {
        method: "POST",
        headers: {
          Authorization: `Bearer ${getToken()}`
        },
        body: JSON.stringify({author: document.getElementById("authorName").value.trim() || null, topic: document.getElementById("topicName").value.trim() || "Неименуван тест"})
      })
        .then((response) => response.json())
        .then(() => {
          applyTestFilters();      
          fetchAndPopulateTests(getToken());
        })
        .catch((error) =>console.error("Error creating test:", error));
    }
    </script>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
      applyTestFilters(getToken());
    });
    </script>
    <script>
    function copyToClipboard(text){
      navigator.clipboard.writeText(text)
        .catch((error) => console.error("Couldn't write to clipboard: ", error));
    }
    </script>
    <script>
    function deleteTest(id) {
      const parent = event.target.parentNode.parentNode;
      dialog = parent.querySelector("dialog");
      if (!dialog) {
        dialog = document.createElement("dialog");
        const form = document.createElement("form");
        const text = document.createElement("p");
        text.innerHTML = "Сигурни ли сте, че искате да изтриете този тест и всички въпроси в него?";
        const cancelButton = document.createElement("button");
        cancelButton.innerHTML = "Отказ";
        cancelButton.type = "submit";
        cancelButton.setAttribute("formmethod", "dialog");
        const confirmButton = document.createElement("button");
        confirmButton.innerHTML = "Изтриване";
        confirmButton.type = "submit";
        confirmButton.setAttribute("formmethod", "dialog");
        confirmButton.addEventListener("click", function() {
          const token = getToken(); // Get the authorization token
          fetch(`../api/questions?testId=${id}`, {
            method: "GET",
            headers: {
              Authorization: `Bearer ${token}`
            }
          })
            .then(response => response.json())
            .then(data => {
              const deletePromises = data.map(question => {
                return fetch(`../api/questions/${question.id}`, {
                  method: "DELETE",
                  headers: {
                    Authorization: `Bearer ${token}`
                  }
                });
              });

              Promise.all(deletePromises)
                .then(() => {
                  fetch(`../api/tests/${id}`, {
                    method: "DELETE",
                    headers: {
                      Authorization: `Bearer ${token}`
                    }
                  })
                    .then(() => {
                      console.log("Test deleted successfully");
                      location.reload();
                    })
                    .catch(error => {
                      console.log("Error deleting the test:", error);
                    });
                })
                .catch(error => {
                  console.log("Error deleting questions:", error);
                });
            })
            .catch(error => {
              console.log("Error fetching questions:", error);
            });
        });
        form.appendChild(text);
        form.appendChild(cancelButton);
        form.appendChild(confirmButton);
        dialog.appendChild(form);
        parent.appendChild(dialog);
      }
      dialog.showModal();
    }
    </script>
    <script>
    function applyTestFilters() {
      const sidebarTestSearch = document.querySelector("#sidebarTestSearch");
      const testsSection = document.querySelector("#tests");
      testsSection.innerHTML = "";

      const searchFilter = sidebarTestSearch.value.trim();
      const queryParams = new URLSearchParams();
      if (searchFilter !== "") queryParams.set("topic", searchFilter);
      queryParams.set("size", -1);

      fetch("../api/tests?"+queryParams.toString(), {
        headers: {
          Authorization: `Bearer ${getToken()}`
        },
      })
        .then((response) => response.json())
        .then((tests) => {

          if (tests.length === 0) {
            testsSection.innerHTML += "<p>Нямате създадени тестове.<p>";
          } else {
            tests.forEach((test) => {
              // Fetch questions for each test
              fetch(`../api/questions?testId=${test.id}`, {
                headers: {
                  Authorization: `Bearer ${getToken()}`,
                },
              })
                .then((response) => response.json())
                .then((questions) => {
                  const testSection = document.createElement("section");
                  testSection.classList.add("item");
                  testSection.setAttribute('data-id', test.id);

                  const testLink = document.createElement("a");
                  testLink.href = `./provideTestFeedback.php?testId=${test.id}`;
                  testLink.target = "_blank";

                  const testTitle = document.createElement("h3");
                  testTitle.innerHTML = '<span class="fa fa-share-from-square"></span> ' + test.topic;

                  const testAuthor = document.createElement("p");
                  testAuthor.textContent = `Автор: ${test.author ?? "Неизвестен"}`;

                  const testQuestions = document.createElement("p");
                  testQuestions.textContent = `Въпроси: ${questions.length}`;

                  const actions = document.createElement("p");
                  actions.classList.add("actions");
                  actions.innerHTML = `
<button onclick="copyToClipboard('${test.id}')">
<span class="fa fa-clipboard"></span> Код</button>
<button onclick="exportToMoodleXML('${test.id}', '${test.topic}')">
<span class="fa fa-file-export"></span>Експорт</button>
<button onclick="deleteTest('${test.id}')"><span class="fa fa-trash"></span>Изтриване</button>`;

                  testLink.appendChild(testTitle);
                  testSection.appendChild(testLink);
                  testSection.appendChild(testAuthor);
                  testSection.appendChild(testQuestions);
                  testSection.appendChild(actions);
                  testsSection.appendChild(testSection);
                  testSection.addEventListener("click", function(){
                    if (event.target.tagName.toLowerCase() === "button" ||
                      event.target.tagName.toLowerCase() == "h3" ||
                      event.target.tagName.toLowerCase() == "a"){
                      return;
                    } else {
                      document.getElementById("testSelect").value=testSection.dataset.id;
                      applyFilters(getToken());
                    }
                  })
                })
                .catch((error) =>
                  console.error("Error fetching questions:", error)
                );
            });
          }
        })
        .catch((error) => console.error("Error fetching tests:", error));
    }
    </script>
    <script>
    function readCSV() {
      var fileInput = document.getElementById('csvFile');
      var file = fileInput.files[0];
      var formData = new FormData();
      formData.append('csvFile', file);
      var authToken = getToken();
      fetch('./../libs/readCSV.html', {
        method: 'POST',
        headers: {
          'Authorization': 'Bearer ' + authToken
        },
        body: formData
      })
        .then(response => response.text())
        .then(result => {
          location.reload();
        })
        .catch(error => {
          console.error("Error reading from csv file:", error);
        });
    }
    </script>
  </head>
  <body>
<?php
include "../includes/loggedInUserHeader.php";
?>
    <aside>
      <section class="activity">
        <h2>Тестове</h2>
        <button type="button" onclick="document.getElementById('uploadCSVDialog').showModal()"><span class="fa fa-upload"></span>Качване</button>
        <dialog id="uploadCSVDialog">
          <form id="uploadForm">
            <input type="file" name="topic" id="csvFile" accept=".csv">
            <button type="submit" formmethod="dialog">Отказ</button>
            <button type="submit" id="importButton" onclick="readCSV()" formmethod="dialog">Импортиране</button>
          </form>
        </dialog>
        <button type="button" onclick="document.getElementById('createTestDialog').showModal()"><span class="fa fa-plus"></span>Нов тест</button>
        <dialog id="createTestDialog">
          <form>
            <input type="text" name="topic" placeholder="Въведете тема" id="topicName">
            <input type="text" name="author" placeholder="Въведете автор" id="authorName">
            <button type="submit" formmethod="dialog">Отказ</button>
            <button type="submit" onclick="createTest()" formmethod="dialog">Създаване</button>
          </form>
        </dialog>
        <input type="search" name="testSearch" id="sidebarTestSearch" placeholder="Потърсете тест"/>
        <button type="button" onclick="applyTestFilters()"><span class="fa fa-search"></span></button>
        <div id="tests">

        </div>
      </section>
    </aside>
    <main>
      <section class="activity" id="questions">
        <div id="tableSetup">
          <div id="actions">
            <span>Преместване: 
              <select class="tSelect" onchange="moveQuestions(getCheckedIds())"></select>
            </span>
            <span>Промяна на тип:
              <select class="qtSelect" onchange="changeQuestionTypes(getCheckedIds())"></select>
            </span>
            <button onclick="deleteQuestions(getCheckedIds())"><span class="fa fa-trash"></span></button>
          </div>
          <span class="pageSetup">
            <span>Страница </span>
            <input type="number" name="page" value="1" min="1" id="pageField" onchange="applyFilters(getToken())">
            <span>. </span>
          </span>
          <span class="pageSetup">
            <span>Покажи </span>
            <select id="sizeSelect" onchange="applyFilters(getToken())">
              <option value="1">1</option>
              <option value="5">5</option>
              <option value="10" selected>10</option>
              <option value="20">20</option>
              <option value="50">50</option>
              <option value="100">100</option>
              <option value="-1">Всички</option>
            </select>
          </span>
        </div>
        <table class="questions-table">
          <thead>
            <tr>
              <th>
                <input type="checkbox" onchange="selectAll()">
              </th>
              <th data-sort="label">Въпрос<span class="fa fa-sort sort-icon"></span></th>
              <th data-sort="testId">Тест<span class="fa fa-sort sort-icon"></span></th>
              <th data-sort="aim">Цел<span class="fa fa-sort sort-icon"></span></th>
              <th data-sort="questionType">Тип<span class="fa fa-sort sort-icon"></span></th>
              <!-- <th data-sort="isMultipleChoice">С множествен отговор<span class="fa fa-sort sort-icon"></span></th> -->
              <th>Обратна връзка</th>
            </tr>
            <tr>
              <th>
                <button onclick="applyFilters(getToken())"><span class="fa fa-search"></span></button>
              </th>
              <th>
                <input type="search" id="labelSearchField"> 
              </th>
              <th>
                <select id="testSelect" class="tSelect" onchange="applyFilters(getToken())"></select>
              </th>
              <th>
                <input type="search" id="aimSearchField">
              </th>
              <th>
                <select id="questionTypeSelect" class="qtSelect" onchange="applyFilters(getToken())"></select>
              </th>
              <th>
              <select id="isMultipleChoiceSelect" onchange="applyFilters(getToken())"></select>
            </th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </section>
    </main>
    <?php
    include "../includes/footer.php";
    ?>
  </body>
</html>

