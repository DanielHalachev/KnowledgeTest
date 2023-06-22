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


  public static function createAuthor(string $facultyNumber, string $firstName = NULL, string $lastName = NULL) : int {
    $sql = "INSERT INTO `authors` (facultyNumber, firstName, lastName) VALUES (?, ?, ?)";
    $connection = (new DatabaseConnection())->getConnection();
    $statement = $connection->prepare($sql);
    $statement->bindParam(1, $facultyNumber);
    $statement->bindParam(2, $firstName);
    $statement->bindParam(3, $lastName);

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

  public static function createTopic(string $name = NULL) : int {
    $sql = "INSERT INTO `topics` (name) VALUES (?)";
    $connection = (new DatabaseConnection())->getConnection();
    $statement = $connection->prepare($sql);
    $statement->bindParam(1, $name);

    $statement->execute();
    return (int) $connection->lastInsertId();
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

  public static function getAuthorId(?string $facultyNumber, bool $toCreate = true) : ?int {

    if ($facultyNumber === null) {
      return null;
    }

    $sql = "SELECT id FROM `authors` WHERE facultyNumber = ?";
    $connection = (new DatabaseConnection())->getConnection();
    $statement = $connection->prepare($sql);
    $statement->bindParam(1, $facultyNumber);
    $resultOfQuery = $statement->execute();

    if ($resultOfQuery && $statement->rowCount() > 0) {
      return (int) $statement->fetchColumn();
    }

    if ($toCreate) {
      return self::createAuthor($facultyNumber);
    }
    return -1;
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

  public static function getTopicId(?string $name, bool $toCreate = true) : ?int {

    if ($name === null) {
      return null;
    }

    $sql = "SELECT id FROM `topics` WHERE name = ?";
    $connection = (new DatabaseConnection())->getConnection();
    $statement = $connection->prepare($sql);
    $statement->bindParam(1, $name);
    $resultOfQuery = $statement->execute();

    if ($resultOfQuery && $statement->rowCount() > 0) {
      return (int) $statement->fetchColumn();
    }

    if ($toCreate) {
      return self::createTopic($name);
    }
    return -1;
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

  public static function getAllTestsByUploaderId(
      ?int $topicId = null,
      ?int $authorId = null,
      ?int $uploaderId = null
  ): array {
    $sql = "SELECT * FROM `tests` WHERE ";
    $conditions = [];

    if ($topicId !== null) {
      $conditions[] = "topicId = ?";
    }
    if ($authorId !== null) {
      $conditions[] = "authorId = ?";
    }
    if ($uploaderId !== null) {
      $conditions[] = "uploaderId = ?";
    }

    $sql .= implode(" AND ", $conditions);

    $connection = (new DatabaseConnection())->getConnection();
    $statement = $connection->prepare($sql);

    $parameterNumber = 1;

    if ($topicId !== null) {
      $statement->bindParam($parameterNumber++, $topicId);
    }
    if ($authorId !== null) {
      $statement->bindParam($parameterNumber++, $authorId);
    }
    if ($uploaderId !== null) {
      $statement->bindParam($parameterNumber++, $uploaderId);
    }

    if ($parameterNumber === 1) {
      $sql = "SELECT * FROM `tests`";
      $statement = $connection->prepare($sql);
    }

    $statement->execute();
    $tests = [];

    while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
      $test = new Test($row['id'], $row['uploaderId'], $row['authorId'], $row['topicId']);
      $tests[] = $test;
    }

    return $tests;
  }

  public static function getTopicNameByTopicId(?int $topicId) : ?string {
    $sql = "SELECT name FROM `topics` WHERE id = ?";
    $connection = (new DatabaseConnection())->getConnection();
    $statement = $connection->prepare($sql);
    $statement->bindParam(1, $topicId);
    $statement->execute();

    return $statement->fetchColumn();
  }

  public static function getFacultyNumberByAuthorId(?int $authorId) : ?string {
    $sql = "SELECT facultyNumber FROM `authors` WHERE id = ?";
    $connection = (new DatabaseConnection())->getConnection();
    $statement = $connection->prepare($sql);
    $statement->bindParam(1, $authorId);
    $statement->execute();

    return $statement->fetchColumn();
  }

  public static function getQuestionsCountByTestId(int $testId) : ?int {
    $sql = "SELECT COUNT(id ) FROM `questions` WHERE testId = ?";
    $connection = (new DatabaseConnection())->getConnection();
    $statement = $connection->prepare($sql);
    $statement->bindParam(1, $testId);
    $statement->execute();

    return $statement->fetchColumn();
  }

  public static function getSummaryForAllTests(?string $topic = NULL,
                                               ?string $facultyNumber = NULL,
                                               ?int $uploaderId = NULL) : ?array {
    $result = [];
    $tests = self::getAllTestsByUploaderId(self::getTopicId($topic, false),
                                           self::getAuthorId($facultyNumber, false),
                                           $uploaderId);

    foreach ($tests as $test) {
      $summary = [
          'topicName' => self::getTopicNameByTopicId($test->getTopicId()),
          'facultyNumber' => self::getFacultyNumberByAuthorId($test->getAuthorId()),
          'questionsCount' => self::getQuestionsCountByTestId($test->getId())
      ];

      $result[] = $summary;
    }

    return $result;
}
}
?>
