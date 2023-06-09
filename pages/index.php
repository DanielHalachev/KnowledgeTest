<!DOCTYPE html>
<html lang="bg">
  <head>
    <title>Начало</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="./../css/common.css" rel="stylesheet" type="text/css">
    <link href="./../css/index.css" rel="stylesheet" type="text/css">
    <link href="./../css/questions.css" rel="stylesheet" type="text/css">
    <script src="../js/theme.js"></script>
    <script src="../js/logout.js"></script>
    <script src="./../js/checkTestExistence.js" defer></script>
    <script src="./../js/closeDialogOnOutsideClick.js" defer></script>
    <script src="./../js/questionTable.js" defer></script>
    <script src="https://kit.fontawesome.com/5c9473fc67.js" crossorigin="anonymous"></script>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
      fetchAndPopulateTests();
      fetchAndPopulateQuestionTypes();
      populateIsMultipleChoiceSelect();
      applyFilters();
    });
    </script>
  </head>
  <body>
    <?php
    require_once "../libs/JWT.php";
    if (isset($_COOKIE['token']) && JWT::isValid($_COOKIE['token'])) {
      include "../includes/loggedInUserHeader.php";
    } else {
      include "../includes/guestUserHeader.php";
    }
    ?>
    <main>
      <section class="activity">
        <h2>Преглед на тест</h2>
        <form id="test-by-code">
          <input id="code-search" type="search" name="code-search" placeholder="Въведете код на тест">
          <button type="submit" onclick="redirectToPreview(event)"><span class="fa fa-comment-dots"></span>Ревю</button>
          <button type="submit" onclick="redirectToTest(event)"><span class="fa fa-list-check"></span>Тест</button>
        </form>
        <dialog id="error-message">
          <form>
            <p>Не намерихме тест по въведения от Вас код. Моля проверете изписването на кода. </p>
            <button type="submit" formmethod="dialog">OK</button>
          </form>
        </dialog>
        <p>или</p>
        <button type="button" onclick="window.location = '#questions'"><span class="fa fa-list"></span>Преглед на въпрос</button>
      </section>
      <section class="activity">
        <h2>Създаване на тест</h2>
        <p>Впишете се, за да създадете тест</p>
        <form id="sign-in" action="login.php" method="post">
          <input type="email" name="email" value="" placeholder="Въведете имейл"/>
          <input type="password" name="password" value="" placeholder="Въведете парола"/>
          <button type="submit"><span class="fa fa-right-to-bracket"></span>Вписване</button>
        </form>
      </section>
      <section class="activity questions" id="questions">
        <div>
          <span>
            <span>Страница </span>
            <input type="number" name="page" value="1" min="1" id="pageField" onchange="applyFilters()">
            <span>. </span>
          </span>
          <span>
            <span>Покажи </span>
            <select id="sizeSelect" onchange="applyFilters()">
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
                <button onclick="applyFilters()"><span class="fa fa-search"></span></button>
              </th>
              <th>
                <input type="search" id="labelSearchField" onchange="applyFilters()">
              </th>
              <th>
                <select id="testSelect" class="tSelect" onchange="applyFilters()"></select>
              </th>
              <th>
                <input type="search" id="aimSearchField" onchange="applyFilters()">
              </th>
              <th>
                <select id="questionTypeSelect" class="qtSelect" onchange="applyFilters()"></select>
              </th>
              <th>
              <select id="isMultipleChoiceSelect" onchange="applyFilters()"></select>
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
