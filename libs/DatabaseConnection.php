<?php
include_once "Settings.php";
class DatabaseConnection {
  private $connection;

  public function __construct() {
    $dbhost = DB_HOST;
    $dbName = DB_NAME;
    $userName = DB_USER;
    $userPassword = DB_PASSWORD;

    $this->connection = new PDO("mysql:host=$dbhost;dbname=$dbName", $userName, $userPassword,
      [
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_STRINGIFY_FETCHES => false,
      ]);
  }

  public function getConnection() {
    return $this->connection;
  }
}
?>
