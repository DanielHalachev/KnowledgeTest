<?php
class QuestionTypeGateway {
  
  private $connection;
  private array $validFields = ['id', 'description'];
  
  public function __construct(DatabaseConnection $databaseConnection) {
    $this->connection = $databaseConnection->getConnection();
  }
  
  public function get(int $id): array | false {
    $sql = "SELECT * FROM `questionTypes` WHERE id = ?";
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

    $sql = "SELECT * FROM `questionTypes`";

    if (!empty($filters)) {
      $sql .= " WHERE";
      $conditions = [];

      foreach ($filters as $key => $value) {
        $condition = "$key = :$key";
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
    $sql = "INSERT INTO `questionTypes` (id, description) VALUES (:id, :description)";
    $statement = $this->connection->prepare($sql);
    $statement->bindValue(":id", $data["id"], PDO::PARAM_INT);
    $statement->bindValue(":description", $data["description"], PDO::PARAM_STR);
    $statement->execute();
    return $this->connection->lastInsertId();
  }

  public function update(array $current, array $new): int {
    $sql = "UPDATE `questionTypes` SET id = :id, description = :description WHERE id = :currentId";
    $statement = $this->connection->prepare($sql);
    $statement->bindValue(":id", $new["id"] ?? $current["id"], PDO::PARAM_INT);
    $statement->bindValue(":description", $new["description"] ?? $current["description"], PDO::PARAM_STR);
    $statement->bindValue(":currentId", $current["id"], PDO::PARAM_INT);
    $statement->execute();
    return $statement->rowCount();
  }
  
  public function delete(int $id): int {
    $sql = "DELETE FROM `questionTypes` WHERE id = :id";
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

        if ($fieldName && in_array($fieldName, $this->validFields) && $direction && ($direction === 'ASC' || $direction === 'DESC')) {
          $sortCriteria[$fieldName] = $direction;
        }
      }
    }

    return $sortCriteria;
  }
}
?>

