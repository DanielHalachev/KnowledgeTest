<?php
class QuestionGateway {
  
  private $connection;
  private array $validFields = [
    'id',
    'uploaderId', 
    'testId', 
    'aim', 
    'questionType', 
    'isMultipleChoice', 
    'label', 
    'correctFeedback', 
    'incorrectFeedback'];
  
  public function __construct(DatabaseConnection $databaseConnection) {
    $this->connection = $databaseConnection->getConnection();
  }
  
  public function get(int $id): array | false {
    $sql = "SELECT * FROM `questions` WHERE id = ?";
    $statement = $this->connection->prepare($sql);
    $statement->bindParam(1, $id, PDO::PARAM_INT);
    $statement->execute();
    $data = $statement->fetch();
    $data["isMultipleChoice"] = (bool) $data["isMultipleChoice"];
    return $data;
  }
  
  public function getAll(array $filters = [], string $sort = null): array {
    $filters = array_filter(
      $filters, 
      function ($value, $key) {
        return isset($value) && $value !== '' && in_array($key, $this->validFields);
      }, 
      ARRAY_FILTER_USE_BOTH
    );

    $sql = "SELECT * FROM `questions`";

    if (!empty($filters)) {
      $sql .= " WHERE";
      $conditions = [];

      foreach ($filters as $key => $value) {
        $condition = '';

        switch (true) {
          case $key === 'id':
            $condition = "$key = :$key";
            $value = (int) $value;
            break;
          case $key === 'uploaderId':
            $condition = "$key = :$key";
            $value = (int) $value;
            break;
          case $key === 'testId':
            $condition = "$key = :$key";
            $value = (int) $value;
            break;
          case $key === 'questionType':
            $condition = "$key = :$key";
            $value = (int) $value;
            break;
          case $key === 'isMultipleChoice':
            $condition = "$key = :$key";
            $value = ($value === "true") ? true : false;
            break;
          default:
            $condition = "$key LIKE CONCAT('%', :$key, '%')";
            break;
        }

        $conditions[] = $condition;
        $filters[$key] = $value;
      }

      $sql .= " " . implode(" AND ", $conditions);
    }

    if ($sort) {
      $sortCriteria = $this->parseSortCriteria($sort);
    }

    if (!empty($sortCriteria)) {
      $orderBy = [];
      foreach ($sortCriteria as $field => $direction) {
        $orderBy[] = "$field $direction";
      }
      $sql .= " ORDER BY " . implode(", ", $orderBy);
    }

    $statement = $this->connection->prepare($sql);

    foreach ($filters as $key => $value) {
      $statement->bindValue(":$key", $value, $this->getPDOType($value));
    }

    $statement->execute();

    $data = [];

    while ($row = $statement->fetch()) {
      $row["isMultipleChoice"] = (bool) $row["isMultipleChoice"];
      $data[] = $row;
    }

    return $data;
  }

  private function getPDOType($value): int {
    if (is_int($value)) {
      return PDO::PARAM_INT;
    } elseif (is_bool($value)) {
      return PDO::PARAM_BOOL;
    } else {
      return PDO::PARAM_STR;
    }
  }

  public function create(array $data): int {
    $sql = "INSERT INTO `questions` (uploaderId, testId, aim, questionType, isMultipleChoice, label, correctFeedback, incorrectFeedback) VALUES (:uploaderId, :testId, :aim, :questionType, :isMultipleChoice, :label, :correctFeedback, :incorrectFeedback)";
    $statement = $this->connection->prepare($sql);
    $statement->bindValue(":uploaderId", $data["uploaderId"], PDO::PARAM_INT);
    $statement->bindValue(":testId", $data["testId"] ?? null, PDO::PARAM_INT);
    $statement->bindValue(":aim", $data["aim"] ?? null, PDO::PARAM_STR);
    $statement->bindValue(":questionType", $data["questionType"], PDO::PARAM_INT);
    $statement->bindValue(":isMultipleChoice", $data["isMultipleChoice"], PDO::PARAM_BOOL);
    $statement->bindValue(":label", $data["label"], PDO::PARAM_STR);
    $statement->bindValue(":correctFeedback", $data["correctFeedback"] ?? null, PDO::PARAM_STR);
    $statement->bindValue(":incorrectFeedback", $data["incorrectFeedback"] ?? null, PDO::PARAM_STR);
    $statement->execute();
    return $this->connection->lastInsertId();
  }

  public function update(array $current, array $new):int {
    $sql = "UPDATE `questions` SET uploaderId = :uploaderId, testId = :testId, aim = :aim, questionType = :questionType, isMultipleChoice = :isMultipleChoice, label = :label, correctFeedback = :correctFeedback, incorrectFeedback = :incorrectFeedback WHERE id = :id";
    $statement = $this->connection->prepare($sql);
    $statement->bindValue(":uploaderId", $new["uploaderId"] ?? $current["uploaderId"], PDO::PARAM_INT);
    $statement->bindValue(":testId", $new["testId"] ?? $current["testId"], PDO::PARAM_INT);
    $statement->bindValue(":aim", $new["aim"] ?? $current["aim"], PDO::PARAM_STR);
    $statement->bindValue(":questionType", $new["questionType"] ?? $current["questionType"], PDO::PARAM_INT);
    $statement->bindValue(":isMultipleChoice", $new["isMultipleChoice"] ?? $current["isMultipleChoice"], PDO::PARAM_BOOL);
    $statement->bindValue(":label", $new["label"] ?? $current["label"], PDO::PARAM_STR);
    $statement->bindValue(":correctFeedback", $new["correctFeedback"] ?? $current["correctFeedback"], PDO::PARAM_STR);
    $statement->bindValue(":incorrectFeedback", $new["incorrectFeedback"] ?? $current["incorrectFeedback"], PDO::PARAM_STR);
    $statement->bindValue(":id", $current["id"], PDO::PARAM_INT);
    $statement->execute();
    return $statement->rowCount();
  }
  
  public function delete(int $id): int {
    $sql = "DELETE FROM `questions` WHERE id = :id";
    $statement = $this->connection->prepare($sql);
    $statement->bindValue(":id", $id, PDO::PARAM_INT);
    $statement->execute();
    return $statement->rowCount();
  }

  private function parseSortCriteria(?string $sort): array {
    $sortCriteria = [];

    if ($sort) {
      $fields = explode(',', $sort);

      foreach ($fields as $field) {
        $parts = explode(' ', trim($field));
        $fieldName = $parts[0] ?? '';
        $direction = strtoupper($parts[1] ?? '');

        if (
          $fieldName 
          && in_array($fieldName, $this->validFields) 
          && $direction && ($direction === 'ASC' || $direction === 'DESC')) {
            $sortCriteria[$fieldName] = $direction;
        }
      }
    }
    return $sortCriteria; 
  }
}
?>
