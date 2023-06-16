<?php
class AnswerGateway {
  
  private $connection;
  private array $validFields = ['id', 'uploaderId', 'questionId', 'label', 'isCorrect'];
  
  public function __construct(DatabaseConnection $databaseConnection) {
    $this->connection = $databaseConnection->getConnection();
  }
  
  public function get(int $id): array | false {
    $sql = "SELECT * FROM `answers` WHERE id = ?";
    $statement = $this->connection->prepare($sql);
    $statement->bindParam(1, $id, PDO::PARAM_INT);
    $statement->execute();
    $data = $statement->fetch();
    if ($data) {
      $data["isCorrect"] = (bool) $data["isCorrect"];
    }
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

    $sql = "SELECT * FROM `answers`";

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
          case $key === 'questionId':
            $condition = "$key = :$key";
            $value = (int) $value;
            break;
          case $key === 'isCorrect':
            $condition = "$key = :$key";
            $value = (bool) $value;
            break;
          default:
            $condition = "$key = :$key";
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
      $row["isCorrect"] = (bool) $row["isCorrect"];
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
    $sql = "INSERT INTO `answers` (uploaderID, questionId, label, isCorrect) VALUES (:uploaderId, :questionId, :label, :isCorrect)";
    $statement = $this->connection->prepare($sql);
    $statement->bindParam(":uploaderId", $data["uploaderId"], PDO::PARAM_INT);
    $statement->bindParam(":questionId", $data["questionId"], PDO::PARAM_INT);
    $statement->bindParam(":label", $data["label"], PDO::PARAM_STR);
    $statement->bindParam(":isCorrect", $data["isCorrect"], PDO::PARAM_BOOL);
    $statement->execute();
    return $this->connection->lastInsertId();
  }
  
  public function update(array $current, array $new): int {
    $sql = "UPDATE `answers` SET uploaderId = :uploaderId, questionId = :questionId, label = :label, isCorrect = :isCorrect WHERE id = :id";
    $statement = $this->connection->prepare($sql);
    $statement->bindValue(":uploaderId", $new["uploaderId"] ?? $current["uploaderId"], PDO::PARAM_INT);
    $statement->bindValue(":questionId", $new["questionId"] ?? $current["questionId"], PDO::PARAM_INT);
    $statement->bindValue(":label", $new["label"] ?? $current["label"], PDO::PARAM_STR);
    $statement->bindValue(":isCorrect", $new["isCorrect"] ?? $current["isCorrect"], PDO::PARAM_BOOL);
    $statement->bindValue(":id", $current["id"], PDO::PARAM_INT);
    $statement->execute();
    return $statement->rowCount();
  }
  
  public function delete(int $id): int {
    $sql = "DELETE FROM `answers` WHERE id = :id";
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
