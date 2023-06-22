<?php
include_once "DatabaseConnection.php";
include_once "Settings.php";
include_once "User.php";
include_once "Test.php";

class DatabaseHandler {
  public static function getUser(string $email): ?User {
    $sql = "SELECT * FROM `users` WHERE email = ?";
    $connection = (new DatabaseConnection())->getConnection();
    $statement = $connection->prepare($sql);
    $statement->bindParam(1, $email);
    $statement->execute();
    $result = $statement->fetch();
    if ($statement->rowCount() == 0) {
      return NULL;
    }

    return (new User(
      $result['id'], 
      $result['googleId'], 
      $result['email'], 
      $result['password'], 
      $result['firstName'], 
      $result['lastName'], 
      $result['lastName']));
  }

  public static function createUser(string $email, string $hashedPassword, string $firstName, string $lastName, string $googleId = NULL, string $profilePicture = NULL): bool {
    $sql = "INSERT INTO `users` (googleId, email, password, firstName, lastName, profilePicture) VALUES (?, ?, ?, ?, ?, ?)";
    $connection = (new DatabaseConnection())->getConnection();
    $statement = $connection->prepare($sql);
    $statement->bindParam(1, $googleId);
    $statement->bindParam(2, $email);
    $statement->bindParam(3, $hashedPassword);
    $statement->bindParam(4, $firstName);
    $statement->bindParam(5, $lastName);
    $statement->bindParam(6, $profilePicture, PDO::PARAM_LOB);
    $statement->execute();

    return ($statement->rowCount() > 0);
  }

  public static function testExists(int $id): bool {
    $sql = "SELECT * FROM `tests` WHERE id = ?";
    $connection = (new DatabaseConnection())->getConnection();
    $statement = $connection->prepare($sql);
    $statement->bindParam(1, $id);
    $statement->execute();
    return ($statement->rowCount() > 0);
  }

  public static function createQuestion(int $testId, int $uploaderId, string $aim, int $questionTypeId, bool $isMultipleChoice,
                                        string $text, string $correctFeedback, string $incorrectFeedback) : int {
    $sql = "INSERT INTO `questions` (testId, uploaderId ,aim, questionType, isMultipleChoice, label, correctFeedback, 
                         incorrectFeedback) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $connection = (new DatabaseConnection())->getConnection();
    $statement = $connection->prepare($sql);
    $statement->bindParam(1, $testId);
    $statement->bindParam(2, $uploaderId);
    $statement->bindParam(3, $aim);
    $statement->bindParam(4, $questionTypeId);
    $statement->bindParam(5, $isMultipleChoice);
    $statement->bindParam(6, $text);
    $statement->bindParam(7, $correctFeedback);
    $statement->bindParam(8, $incorrectFeedback);

    $statement->execute();
    return (int) $connection->lastInsertId();
  }

  public static function createAnswer(int $questionId, int $uploaderId, string $label, bool $isCorrect) : int {
    $sql = "INSERT INTO `answers` (questionId, uploaderId, label, isCorrect) VALUES (?, ?, ?, ?)";
    $connection = (new DatabaseConnection())->getConnection();
    $statement = $connection->prepare($sql);
    $statement->bindParam(1, $questionId);
    $statement->bindParam(2, $uploaderId);
    $statement->bindParam(3, $label);
    $statement->bindParam(4, $isCorrect);

    $statement->execute();
    return (int) $connection->lastInsertId();
  }

  public static function createFeedback(int $questionId, int $complexity, string $feedback = NULL) : int {
    $sql = "INSERT INTO `feedbacks` (questionId, complexity, feedback) VALUES (?, ?, ?)";
    $connection = (new DatabaseConnection())->getConnection();
    $statement = $connection->prepare($sql);
    $statement->bindParam(1, $questionId);
    $statement->bindParam(2, $complexity);
    $statement->bindParam(3, $feedback);

    $statement->execute();
    return (int) $connection->lastInsertId();
  }

  public static function createQuestionType(int $questionTypeId, string $description = NULL) : int {
    $sql = "INSERT INTO `questiontypes` (id, description) VALUES (?, ?)";
    $connection = (new DatabaseConnection())->getConnection();
    $statement = $connection->prepare($sql);
    $statement->bindParam(1, $questionTypeId);
    $statement->bindParam(2, $description);

    $statement->execute();
    return $questionTypeId;
  }

  public static function createTest(int $uploaderId, string $author, string $topic = NULL) : int {
    $sql = "INSERT INTO `tests` (uploaderId, author, topic) VALUES (?, ?, ?)";
    $connection = (new DatabaseConnection())->getConnection();
    $statement = $connection->prepare($sql);
    var_dump($uploaderId);
    $statement->bindParam(1, $uploaderId);
    $statement->bindParam(2, $author);
    $statement->bindParam(3, $topic);

    $statement->execute();

    return (int) $connection->lastInsertId();
  }

  public static function getQuestionTypeId(string $questiontypeId) : int {
    $sql = "SELECT id FROM `questiontypes` WHERE id = ?";
    $connection = (new DatabaseConnection())->getConnection();
    $statement = $connection->prepare($sql);
    $statement->bindParam(1, $questiontypeId);
    $resultOfQuery = $statement->execute();

    if ($resultOfQuery && $statement->rowCount() > 0) {
      return (int) $statement->fetchColumn();
    }

    return self::createQuestionType($questiontypeId);
  }

  public static function getTestId(int $uploaderId, string $facultyNumber, string $topicName = NULL) : int {
    if ($topicName !== null) {
      $sql = "SELECT id FROM `tests` WHERE uploaderId = ? AND author = ? AND topic = ?";
      $connection = (new DatabaseConnection())->getConnection();
      $statement = $connection->prepare($sql);
      $statement->bindParam(1, $uploaderId);
      $statement->bindParam(2, $facultyNumber);
      $statement->bindParam(3, $topicName);
      $resultOfQuery = $statement->execute();

      if ($resultOfQuery && $statement->rowCount() > 0) {
        return (int)$statement->fetchColumn();
      }

      return self::createTest($uploaderId, $facultyNumber, $topicName);
    } else {
      $sql = "SELECT id FROM `tests` WHERE uploaderId = ? AND author = ?";

      $connection = (new DatabaseConnection())->getConnection();
      $statement = $connection->prepare($sql);
      $statement->bindParam(1, $uploaderId);
      $statement->bindParam(2, $facultyNumber);
      $resultOfQuery = $statement->execute();

      if ($resultOfQuery && $statement->rowCount() > 0) {
        return (int) $statement->fetchColumn();
      }

      return self::createTest($uploaderId, $facultyNumber, date('Y-m-d').'-'.$facultyNumber);
    }
  }
}
?>
