<!DOCTYPE html>
<html lang="bg">
  <head>
    <title>Регистрация</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="./../css/common.css" rel="stylesheet" type="text/css">
    <link href="./../css/login.css" rel="stylesheet" type="text/css">
    <script src="https://kit.fontawesome.com/5c9473fc67.js" crossorigin="anonymous"></script>
  </head>
  <body>
    <header>
      <nav>
        <h1><a href="./index.html">KnowledgeTest</a></h1>
        <ul>
          <a href="./index.html"><li>Правене на тест</li></a>
          <a href=""><li>Създаване на тест</li></a>
          <a href="./login.php"><li>Вписване</li></a>
          <a href="./register.php"><li>Регистрация</li></a>
        </ul>
      </nav>
    </header>
    <main>
      <section class="activity">
        <h2>Регистрация</h2>
        <form method="post" action="register.php">
          <input type="text" name="firstName" placeholder="Въведете име"/>
          <input type="text" name="lastName" placeholder="Въведете фамилия"/>
          <input type="email" name="email" placeholder="Въведете имейл"/>
          <input type="password" name="password" placeholder="Въведете парола"/>
          <input type="password" name="repeatedPassword" placeholder="Повторете паролата"/>
          <button type="submit"><span class="fa fa-user-plus"></span>Регистрация</button>
        </form>
        <p>или</p>
        <button type="button"><span class="fa fa-google"></span>Вписване с Google</button>
      </section>
      <dialog id="error-message">
        <form>
        <?php
        include_once "./../libs/DatabaseHandler.php";
        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $repeatedPassword = $_POST['repeatedPassword'];

        if ($password != $repeatedPassword) {
          echo "<p>Паролите не съвпадат. Моля повторете избраната от Вас парола</p>";
        } else {
          $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

          if (DatabaseHandler::getUser($email) == NULL) {
            $result = DatabaseHandler::createUser($email, $hashedPassword, $firstName, $lastName, NULL, NULL);
            if ($result) {
              echo "<p>Моля потвърдете регистрацията чрез имейл.</p>";
            } else {
              echo "<p>Нещо се обърка. Моля опитайте отново.</p>";
            }
          } else {
            echo "<p>Вече съществува потребител с този имейл. Моля използвайте различен имейл или възстановете паролата си.</p>";
          }
        }
        echo "<script>document.getElementById('error-message').showModal();</script>";
        ?>
        <button type="submit" formmethod="dialog">OK</button>
        </form>
      </dialog>
    </main>
    <footer>
      <p>KnowledgeTest 2023&copy;</p>
      <p>
        <span>Даниел Халачев, </span>
        <span>Стефан Велев</span>
      </p>
    </footer>
  </body>
</html>
