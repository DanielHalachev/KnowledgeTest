<?php
include_once "./../libs/Settings.php";
?>
<header>
  <nav>
    <h1><a href="./index.php"><?php echo SITE_NAME?> </a></h1>
    <ul>
      <a href="./index.php"><li><span class="fa fa-house"></span>Начало</li></a>
      <a href="./login.php"><li><span class="fa fa-right-to-bracket"></span>Вписване</li></a>
      <a href="./register.php"><li><span class="fa fa-user-plus"></span>Регистрация</li></a>
      <button onclick="toggleTheme()"><span class="fa fa-circle-half-stroke"></span></button>
    </ul>
  </nav>
</header>
