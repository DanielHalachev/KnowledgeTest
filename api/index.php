<?php

declare(strict_types=1);

spl_autoload_register(function ($class) {
  require_once "../libs/$class.php";
});

set_error_handler("ErrorHandler::handleError");
set_exception_handler("ErrorHandler::handleException");

header("Content-type: application/json; charset=UTF-8");

$parts = explode("/", $_SERVER["REQUEST_URI"]);
$id = $parts[4] ?? null;

$databaseConnection = new DatabaseConnection();

$userGateway = new UserGateway($databaseConnection);
$answerGateway = new AnswerGateway($databaseConnection);
$questionGateway = new QuestionGateway($databaseConnection);
$testGateway = new TestGateway($databaseConnection);
$feedbackGateway = new FeedbackGateway($databaseConnection);
$topicGateway = new TopicGateway($databaseConnection);
$authorGateway = new AuthorGateway($databaseConnection);
$sessionGateway = new SessionGateway($databaseConnection);

session_start();
switch (explode("?", $parts[3])[0]) {
  case "users":
    $controller = new UserController($userGateway);
    $controller->processRequest($_SERVER["REQUEST_METHOD"], $_GET, $id);
    break;
  case "answers" :
    $controller = new AnswerController($answerGateway);
    $controller->processRequest($_SERVER["REQUEST_METHOD"], $_GET, $id);
    break;
  case "questions":
    $controller = new QuestionController($questionGateway);
    $controller->processRequest($_SERVER["REQUEST_METHOD"], $_GET, $id);
    break;
  case "tests":
    $controller = new TestController($testGateway);
    $controller->processRequest($_SERVER["REQUEST_METHOD"], $_GET, $id);
    break;
  case "feedback":
    $controller = new FeedbackController($feedbackGateway);
    $controller->processRequest($_SERVER["REQUEST_METHOD"], $_GET, $id);
    break;
  case "session":
    $controller = new SessionController($sessionGateway);
    $controller->processRequest(
      $_SERVER["REQUEST_METHOD"],
      (array) json_decode(file_get_contents("php://input"), true)
    );
    break;
  default:
    http_response_code(404);
    exit;
    break;
}
