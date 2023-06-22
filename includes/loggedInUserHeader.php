<?php
include_once "./../libs/Settings.php";
?>
<header>
  <nav>
    <span class="fa fa-bars" id="menu" onclick="toggleNav()"></span>
    <h1><a href="./index.php"><?php echo SITE_NAME?> </a></h1>
    <ul>
      <a href="./home.php"><li><span class="fa fa-user"></span>Потребител</li></a>
      <a href="./questions.php"><li><span class="fa fa-question"></span>Въпроси</li></a>
      <a onclick="logout()"><li><span class="fa fa-power-off"></span>Изход</li></a>
      <button onclick="toggleTheme()"><span class="fa fa-circle-half-stroke"></span></button>
    </ul>
  </nav>
</header>

