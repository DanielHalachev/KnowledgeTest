<?php
if (empty($_POST["email"])) {
  die("Email is required");
}

if (! filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
  die("Valid email is required");
}

if (empty($_POST["firstName"])) {
  die("First name is required");
}

if (empty($_POST["lastName"])) {
  die("Last name is required");
}

if (empty($_POST["password"])) {
  die("Password is required");
}

if (strlen($_POST["password"]) < 6) {
  die("Password must be at least 6 characters");
}

if (! preg_match("/[0-9]/", $_POST["password"])) {
  die("Password must contain at least one number");
}

if ($_POST["password"] !== $_POST["repeatedPassword"]) {
  die("Passwords must match");
}

$password_hash = password_hash($_POST["password"], PASSWORD_DEFAULT);


session_start();

  $user = UserGateway;
  if ($user) {
    if(password_verify($hashedPassword, $user->getPassword())) {
      $_SESSION['user'] = $user;
      header("Location: home.php");
      exit();
?>
