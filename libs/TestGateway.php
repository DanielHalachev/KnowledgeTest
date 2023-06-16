<?php
class TestGateway {
  
  private $connection;
  private array $validFields = [
    'id',
    'uploaderId',
    'authorId',
    'topicId'
  ];
  
  public function __construct(DatabaseConnection $databaseConnection) {
    $this->connection = $databaseConnection->getConnection();
  }
  
  public function get(int $id): array | false {
    $sql = "SELECT * FROM `tests` WHERE id = ?";
    $statement = $this->connection->prepare($sql);
    $statement->bindParam(1, $id, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetch();
  }
  
  public function getAll(array $filters = [], string $sort = null): array {
    $filters = array_filter(
      $filters, 
      function ($value, $key) {
        return isset($value) && $value !== '' && in_array($key, $this->validFields);
      }, 
      ARRAY_FILTER_USE_BOTH
    );

    $sql = "SELECT * FROM `tests`";

    if (!empty($filters)) {
      $sql .= " WHERE";
      $conditions = [];

      foreach ($filters as $key => $value) {
        $conditions[] = "$key = :$key";
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

    return $statement->fetchAll();
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
    $sql = "INSERT INTO `tests` (uploaderId, authorId, topicId) VALUES (:uploaderId, :authorId, :topicId)";
    $statement = $this->connection->prepare($sql);
    $statement->bindValue(":uploaderId", $data["uploaderId"], PDO::PARAM_INT);
    $statement->bindValue(":authorId", $data["authorId"] ?? null, PDO::PARAM_INT);
    $statement->bindValue(":topicId", $data["topicId"] ?? null, PDO::PARAM_INT);
    $statement->execute();
    return $this->connection->lastInsertId();
  }
  
  public function update(array $current, array $new): int {
    $sql = "UPDATE `tests` SET uploaderId = :uploaderId, authorId = :authorId, topicId = :topicId WHERE id = :id";
    $statement = $this->connection->prepare($sql);
    $statement->bindValue(":uploaderId", $new["uploaderId"] ?? $current["uploaderId"], PDO::PARAM_INT);
    $statement->bindValue(":authorId", $new["authorId"] ?? $current["authorId"], PDO::PARAM_INT);
    $statement->bindValue(":topicId", $new["topicId"] ?? $current["topicId"], PDO::PARAM_INT);
    $statement->bindValue(":id", $current["id"], PDO::PARAM_INT);
    $statement->execute();
    return $statement->rowCount();
  }
  
  public function delete(int $id): int {
    $sql = "DELETE FROM `tests` WHERE id = :id";
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
