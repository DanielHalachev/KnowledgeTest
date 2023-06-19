<?php
class FeedbackGateway {
  
  private $connection;
  private array $validFields = [
    'id',
    'questionId',
    'complexity',
    'feedback'
  ];
  
  public function __construct(DatabaseConnection $databaseConnection) {
    $this->connection = $databaseConnection->getConnection();
  }
  
  public function get(int $id): array | false {
    $sql = "SELECT * FROM `feedbacks` WHERE id = ?";
    $statement = $this->connection->prepare($sql);
    $statement->bindParam(1, $id, PDO::PARAM_INT);
    $statement->execute();
    $data = $statement->fetch();
    return $data;
  }
  
  public function getAll(array $filters = [], string $sort = null, $offset = 0, $limit = DEFAULT_QUERY_SIZE): array {
    $filters = array_filter(
      $filters, 
      function ($value, $key) {
        return isset($value) && $value !== '' && in_array($key, $this->validFields);
      }, 
      ARRAY_FILTER_USE_BOTH
    );

    $sql = "SELECT * FROM `feedbacks`";

    if (!empty($filters)) {
      $sql .= " WHERE";
      $conditions = [];

      foreach ($filters as $key => $value) {
        $condition = '';

        switch ($key) {
          case 'id':
          case 'questionId':
          case 'complexity':
            $condition = "$key = :$key";
            $value = (int) $value;
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

    if ($limit !== -1){
      $sql .= " LIMIT :limit";
    }
    $sql .= " OFFSET :offset";

    $statement = $this->connection->prepare($sql);

    foreach ($filters as $key => $value) {
      $statement->bindValue(":$key", $value, $this->getPDOType($value));
    }

    if ($limit !== -1){
      $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
    }
    $statement->bindValue(':offset', $offset, PDO::PARAM_INT);

    $statement->execute();

    $data = [];

    while ($row = $statement->fetch()) {
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
    $sql = "INSERT INTO `feedbacks` (questionId, complexity, feedback) VALUES (:questionId, :complexity, :feedback)";
    $statement = $this->connection->prepare($sql);
    $statement->bindValue(":questionId", $data["questionId"], PDO::PARAM_INT);
    $statement->bindValue(":complexity", $data["complexity"] ?? null, PDO::PARAM_INT);
    $statement->bindValue(":feedback", $data["feedback"] ?? null, PDO::PARAM_STR);
    $statement->execute();
    return $this->connection->lastInsertId();
  }

  public function update(array $current, array $new): int {
    $sql = "UPDATE `feedbacks` SET questionId = :questionId, complexity = :complexity, feedback = :feedback WHERE id = :id";
    $statement = $this->connection->prepare($sql);
    $statement->bindValue(":questionId", $new["questionId"] ?? $current["questionId"], PDO::PARAM_INT);
    $statement->bindValue(":complexity", $new["complexity"] ?? $current["complexity"], PDO::PARAM_INT);
    $statement->bindValue(":feedback", $new["feedback"] ?? $current["feedback"], PDO::PARAM_STR);
    $statement->bindValue(":id", $current["id"], PDO::PARAM_INT);
    $statement->execute();
    return $statement->rowCount();
  }
  
  public function delete(int $id): int {
    $sql = "DELETE FROM `feedbacks` WHERE id = :id";
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
