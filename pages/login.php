<!DOCTYPE html>
<html lang="bg">
  <head>
    <title>Вписване</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="./../css/common.css" rel="stylesheet" type="text/css">
    <link href="./../css/login.css" rel="stylesheet" type="text/css">
    <script src="../js/theme.js"></script>
    <script src="https://kit.fontawesome.com/5c9473fc67.js" crossorigin="anonymous"></script>
  </head>
  <body>
<?php
include "../includes/guestUserHeader.php";
?>
    <main>
      <section class="activity">
        <h2>Вписване</h2>
        <form method="post" action="login.php">
          <input type="email" name="email" placeholder="Въведете имейл *"/>
          <input type="password" name="password" placeholder="Въведете парола *"/>
          <button type="submit"><span class="fa fa-right-to-bracket"></span>Вписване</button>
        </form>
      </section>
      <dialog id="error-message">
          <form>
          <?php
          session_start();
          require_once "./../libs/DatabaseConnection.php";
          require_once "./../libs/JWT.php";
          if (isset($_POST['email'])) {
            $email = $_POST['email'];
            $password = $_POST['password'];

            $sql = "SELECT * FROM `users` WHERE email = ?";
            $connection = (new DatabaseConnection())->getConnection();
            $statement = $connection->prepare($sql);
            $statement->bindParam(1, $email);
            $statement->execute();
            $user = $statement->fetch();
            if ($user) {
              if(password_verify($password, $user["password"])) {
                $payload = [
                    'userId' => $user["id"],
                    'email' => $email,
                    'expiration' => strtotime('+3 days')
                ];

                $secret = getenv("SECRET_KEY");

                $token = JWT::encode($payload, $secret);

                $_SESSION['token'] = $token;

                setcookie('token', $token, time() + (3 * 24 * 60 * 60), '/');
                header("Authorization: $token");
                header("Location: home.php");
                exit;
              }
              else {
                echo "<p>Грешно потребителско име или парола.</p>";
              }
            } else {
              echo "<p>Няма потребител с такъв имейл.</p>";
            }
            echo "<script>document.getElementById('error-message').showModal();</script>";
          }          
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
