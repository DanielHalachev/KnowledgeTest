<!DOCTYPE html>
<html lang="bg">
  <head>
    <title>Регистрация</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="./../css/common.css" rel="stylesheet" type="text/css">
    <link href="./../css/login.css" rel="stylesheet" type="text/css">
    <script src="https://kit.fontawesome.com/5c9473fc67.js" crossorigin="anonymous"></script>
    <script src="../js/theme.js"></script>
  </head>
  <body>
<?php
include "../includes/guestUserHeader.php";
?>
    <main>
      <section class="activity">
        <h2>Регистрация</h2>
        <form method="post" action="register.php">
          <input type="text" name="firstName" placeholder="Въведете име *"/>
          <input type="text" name="lastName" placeholder="Въведете фамилия *"/>
          <input type="email" name="email" placeholder="Въведете имейл *"/>
          <input type="password" name="password" placeholder="Въведете парола *"/>
          <input type="password" name="repeatedPassword" placeholder="Повторете паролата *"/>
          <button type="submit"><span class="fa fa-user-plus"></span>Регистрация</button>
        </form>
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

        if ($email == null || $email == '' || $firstName == null || $firstName == '' || $lastName == null || $lastName ==''
        || $password == null || $password == '' || $repeatedPassword == null || $repeatedPassword == '') {
            return;
        }

        if ($password != $repeatedPassword) {
          echo "<p>Паролите не съвпадат. Моля повторете избраната от Вас парола</p>";
        } else {
          $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

          if (DatabaseHandler::getUser($email) == NULL) {
            $result = DatabaseHandler::createUser($email, $hashedPassword, $firstName, $lastName, NULL, NULL);
            if ($result) {
              echo "<p>Успешна регистрация.</p>";
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
    <?php
    include "../includes/footer.php";
    ?>
  </body>
</html>
