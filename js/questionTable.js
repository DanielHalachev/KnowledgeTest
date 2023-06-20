// Helper function to create an answer row
function createAnswerRow(answer, isMultipleChoice) {
  const answerRow = document.createElement("tr");

  const checkboxCell = document.createElement("td");
  const checkbox = document.createElement("input");
  if(isMultipleChoice){
    checkbox.type = "checkbox";
  } else {
    checkbox.type="radio";
  }
  checkbox.checked = answer.isCorrect;
  checkbox.disabled = true;
  checkboxCell.appendChild(checkbox);

  const labelCell = document.createElement("td");
  labelCell.textContent = answer.label;

  answerRow.appendChild(checkboxCell);
  answerRow.appendChild(labelCell);

  return answerRow;
}

// Helper function to create the accordion row for answers
function createAnswersAccordionRow(questionId) {
  const answersRow = document.createElement("tr");
  answersRow.id = `answers-row-${questionId}`;
  answersRow.classList.add("answers-row");
  answersRow.style.display = "none";

  const answersCell = document.createElement("td");
  answersCell.colSpan = 6;

  const answersTable = document.createElement("table");
  answersTable.classList.add("answers-table");

  const answersHeaderRow = document.createElement("tr");

  const checkboxHeaderCell = document.createElement("th");
  checkboxHeaderCell.textContent = "Correct";
  answersHeaderRow.appendChild(checkboxHeaderCell);

  const labelHeaderCell = document.createElement("th");
  labelHeaderCell.textContent = "Label";
  answersHeaderRow.appendChild(labelHeaderCell);

  answersTable.appendChild(answersHeaderRow);
  answersCell.appendChild(answersTable);
  answersRow.appendChild(answersCell);

  return answersRow;
}

// Helper function to fetch and populate answers for a specific question
function fetchAndPopulateAnswers(questionId, isMultipleChoice, token) {
  const headers = token ? { Authorization: `Bearer ${token}` } : {};
  return fetch(`../api/answers?questionId=${questionId}`, { headers })
    .then((response) => response.json())
    .then((answers) => {
      const answersTable = document.querySelector(`#answers-row-${questionId} .answers-table`);
      answersTable.innerHTML = ""; // Clear existing rows

      answers.forEach((answer) => {
        const answerRow = createAnswerRow(answer, isMultipleChoice);
        answersTable.appendChild(answerRow);
      });
    })
    .catch((error) => console.error("Error fetching answers:", error));
}
// Helper function to fetch test topic by testId
function fetchTestTopic(testId, token) {
  const headers = token ? { Authorization: `Bearer ${token}` } : {};
  return fetch(`../api/tests/${testId}`, { headers })
    .then((response) => response.json())
    .then((test) => test.topic)
    .catch((error) => {
      console.error("Error fetching test topic:", error);
      return "";
    });
}

function applyFilters(token) {
  const headers = token ? { Authorization: `Bearer ${token}` } : {};

  const labelSearchField = document.querySelector("#labelSearchField");
  const testSelect = document.querySelector("#testSelect");
  const aimSearchField = document.querySelector("#aimSearchField");
  const questionTypeSelect = document.querySelector("#questionTypeSelect");
  const isMultipleChoiceSelect = document.querySelector("#isMultipleChoiceSelect");
  const pageField = document.querySelector("#pageField");
  const sizeSelect = document.querySelector("#sizeSelect");
  const questionsTable = document.querySelector("#questions .questions-table tbody");
  questionsTable.innerHTML = "";

  const labelFilter = labelSearchField.value.trim();
  const testFilter = testSelect.value;
  const aimFilter = aimSearchField.value.trim();
  const questionTypeFilter = questionTypeSelect.value;
  const isMultipleChoiceFilter = isMultipleChoiceSelect.value;
  const page = pageField.value;
  const size = sizeSelect.value;

  const tableHeaders = document.querySelectorAll("th[data-sort]");
  const sortParams = [];

  tableHeaders.forEach((th) => {
    const sortParam = th.dataset.sort;
    const sortDirection = th.dataset.sorted;

    if (sortDirection) {
      sortParams.push(`${sortParam} ${sortDirection}`);
    }
  });

  const sortQuery = sortParams.join(",");

  const queryParams = new URLSearchParams();
  if (labelFilter !== "") queryParams.set("label", labelFilter);
  if (testFilter !== "") queryParams.set("testId", testFilter);
  if (aimFilter !== "") queryParams.set("aim", aimFilter);
  if (questionTypeFilter !== "") queryParams.set("questionType", questionTypeFilter);
  if (isMultipleChoiceFilter !== "") queryParams.set("isMultipleChoice", isMultipleChoiceFilter);
  queryParams.set("page", page);
  queryParams.set("size", size);
  if (sortQuery !== "") queryParams.set("sort", sortQuery);




  fetch("../api/questions?"+queryParams.toString(), { headers })
    .then((response) => response.json())
    .then((questions) => {

      // if (questions.length === 0) {
      //   questionsSection.innerHTML += "<p>Нямате създадени въпроси.</p>";
      // } else
    {
        questions.forEach((question) => {
          const questionRow = document.createElement("tr");
          questionRow.classList.add("question-row");

          // Create table cells for question data
          const selectCell = document.createElement("td");
          const selectCheckbox = document.createElement("input");
          selectCheckbox.type = "checkbox";
          selectCheckbox.classList.add("single-select");
          selectCheckbox.setAttribute("data-questionId", question.id);
          selectCell.appendChild(selectCheckbox);
          questionRow.appendChild(selectCell);

          const labelCell = document.createElement("td");
          labelCell.textContent = question.label;
          questionRow.appendChild(labelCell);

          const testCell = document.createElement("td");
          testCell.textContent = ""; // Will be populated later
          questionRow.appendChild(testCell);

          const aimCell = document.createElement("td");
          aimCell.textContent = question.aim;
          questionRow.appendChild(aimCell);

          const questionTypeCell = document.createElement("td");
          questionTypeCell.textContent = ""; // Will be populated later
          questionRow.appendChild(questionTypeCell);

          // const isMultipleChoiceCell = document.createElement("td");
          // isMultipleChoiceCell.textContent = question.isMultipleChoice ? "Да" : "Не";
          // questionRow.appendChild(isMultipleChoiceCell);

          const feedbackCell = document.createElement("td");
          const feedbackButton = document.createElement("button");
          if (token) {
            feedbackButton.setAttribute('onclick', 'location.href="./viewFeedback.html?questionId='+question.id+'"');
            feedbackButton.innerHTML = '<span class="fa fa-comments"></span>';
          } else {
            feedbackButton.setAttribute('onclick', 'location.href="./provideQuestionFeedback.html?questionId='+question.id+'"');
            feedbackButton.innerHTML = '<span class="fa fa-comment-dots"></span>';
          }
          feedbackCell.appendChild(feedbackButton);
          questionRow.appendChild(feedbackCell);

          // Attach click event listener to show/hide answers
          questionRow.addEventListener("click", () => {
            const targetElement = event.target;

            // Exclude checkbox from toggling visibility
            if (
              targetElement.tagName.toLowerCase() == "button" || 
                (targetElement.tagName.toLowerCase() === "input" && targetElement.type === "checkbox")
            ) {
              return;
            }
            const answersRow = document.querySelector(`#answers-row-${question.id}`);
            answersRow.style.display = answersRow.style.display === "none" ? "table-row" : "none";

            // Fetch answers only when opening the row for the first time
            if (answersRow.style.display === "table-row" && !answersRow.dataset.fetched) {
              fetchAndPopulateAnswers(question.id, question.isMultipleChoice, token)
                .then(() => {
                  answersRow.dataset.fetched = true;
                })
                .catch((error) => console.error("Error fetching answers for question:", error));
            }
          });

          fetchTestTopic(question.testId, token).then((topic) => {
            testCell.textContent = topic;
          });

          fetch(`../api/types/${question.questionType}`)
            .then((response) => response.json())
            .then((questionType) => {
              questionTypeCell.textContent = questionType.description;
            })
            .catch((error) => {
              console.error("Error fetching question type:", error);
            });

          questionsTable.appendChild(questionRow);
          questionsTable.appendChild(createAnswersAccordionRow(question.id));
        });

      }
    })
    .catch((error) => console.error("Error fetching questions:", error));
}

// Helper function to toggle the sort direction icon
function toggleSortIcon(element) {
  if (element.classList.contains("fa-sort")) {
    element.classList.replace("fa-sort", "fa-sort-up");
  } else if (element.classList.contains("fa-sort-up")) {
    element.classList.replace("fa-sort-up", "fa-sort-down");
  } else {
    element.classList.replace("fa-sort-down", "fa-sort");
  }
}

// Function to handle sorting when a table header is clicked
function handleSort(event) {
  const th = event.target;
  if (th.nodeName === "SPAN") {
    th = th.parentNode;
  }
  const sortParam = th.dataset.sort;
  const sortIcon = th.querySelector(".sort-icon");

  if (!sortParam) return;

  if (th.dataset.sorted === "asc") {
    th.dataset.sorted = "desc";
    toggleSortIcon(sortIcon);
  } else if (th.dataset.sorted === "desc") {
    th.dataset.sorted = "";
    toggleSortIcon(sortIcon);
  } else {
    th.dataset.sorted = "asc";
    toggleSortIcon(sortIcon);
  }

  // Perform the sorting operation and update the table
  applyFilters();
}

// Function to fetch tests and populate the test select field
function fetchAndPopulateTests(token) {
  const headers = token ? { Authorization: `Bearer ${token}` } : {};
  const testSelects = document.querySelectorAll(".tSelect");
  testSelects.forEach((element) => {
    element.innerHTML = "";
  });

  fetch("../api/tests", { headers })
    .then((response) => response.json())
    .then((tests) => {
      testSelects.forEach((element) => {
        const blankOption = document.createElement("option");
        blankOption.value = "";
        if(element.id === "testSelect"){
          blankOption.textContent = "Всички";
        } else {
          blankOption.textContent = "Изберете тест";
        }
        element.appendChild(blankOption);
      });

      tests.forEach((test) => {
        testSelects.forEach((element) => {
          const option = document.createElement("option");
          option.value = test.id;
          option.textContent = test.topic;
          element.appendChild(option);
        });
      });
    })
    .catch((error) => console.error("Error fetching tests:", error));
}

// Function to fetch question types and populate the question type select field
function fetchAndPopulateQuestionTypes() {
  const questionTypeSelects = document.querySelectorAll(".qtSelect");
  questionTypeSelects.forEach((element) => {
    element.innerHTML="";
  });

  fetch("../api/types")
    .then((response) => response.json())
    .then((types) => {
      questionTypeSelects.forEach((element) => {
        const blankOption = document.createElement("option");
        blankOption.value = "";
        if (element.id === "questionTypeSelect") {
          blankOption.textContent = "Всички";
        } else {
          blankOption.textContent = "Изберете тип";
        }
        element.appendChild(blankOption);
      });

      types.forEach((type) => {
        questionTypeSelects.forEach(element => {
          const option = document.createElement("option");
          option.value = type.id;
          option.textContent = type.description;
          element.appendChild(option);
        });
      });
    })
    .catch((error) => console.error("Error fetching question types:", error));
}

// Function to populate the isMultipleChoice select field
function populateIsMultipleChoiceSelect() {
  const isMultipleChoiceSelect = document.getElementById("isMultipleChoiceSelect");
  isMultipleChoiceSelect.innerHTML="";

  // Add blank option
  const blankOption = document.createElement("option");
  blankOption.value = "";
  blankOption.textContent = "Всички";
  isMultipleChoiceSelect.appendChild(blankOption);

  // Add Да option
  const yesOption = document.createElement("option");
  yesOption.value = "true";
  yesOption.textContent = "Да";
  isMultipleChoiceSelect.appendChild(yesOption);

  // Add Не option
  const noOption = document.createElement("option");
  noOption.value = "false";
  noOption.textContent = "Не";
  isMultipleChoiceSelect.appendChild(noOption);
}

function selectAll() {
  const checkboxes = document.querySelectorAll("#questions tbody input[type='checkbox']");
  const isSelected = document.querySelector("#questions thead input[type='checkbox']").checked;
  checkboxes.forEach((checkbox) =>{
    checkbox.checked = isSelected;
  });
}

document.addEventListener("DOMContentLoaded", function() {
  const tableHeaders = document.querySelectorAll("th[data-sort]");
  tableHeaders.forEach((th) => {
    th.addEventListener("click", handleSort);
  });
});


