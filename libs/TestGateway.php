<?php
class TestGateway {
  
  private $connection;
  private array $validFields = [
    'id',
    'uploaderId',
    'author',
    'topic'
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
  
  public function getAll(array $filters = [], string $sort = null, $offset = 0, $limit = DEFAULT_QUERY_SIZE): array {
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
          case $key === 'author':
            $condition = "$key LIKE CONCAT('%', :$key, '%')";
            break;
          case $key === 'topic':
            $condition = "$key LIKE CONCAT('%', :$key, '%')";
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

    if ($limit !== -1){
      $sql .= " LIMIT :limit";
    }

    if ($offset !== 0) {
      $sql .= " OFFSET :offset";
    }

    $statement = $this->connection->prepare($sql);

    foreach ($filters as $key => $value) {
      $statement->bindValue(":$key", $value, $this->getPDOType($value));
    }

    if ($limit !== -1){
      $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
    }

    if ($offset !== 0) {
      $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
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
    $sql = "INSERT INTO `tests` (uploaderId, author, topic) VALUES (:uploaderId, :author, :topic)";
    $statement = $this->connection->prepare($sql);
    $statement->bindValue(":uploaderId", $data["uploaderId"], PDO::PARAM_INT);
    $statement->bindValue(":author", $data["author"] ?? null, PDO::PARAM_STR);
    $statement->bindValue(":topic", $data["topic"] ?? null, PDO::PARAM_STR);
    $statement->execute();
    return $this->connection->lastInsertId();
  }
  
  public function update(array $current, array $new): int {
    $sql = "UPDATE `tests` SET uploaderId = :uploaderId, author = :author, topic = :topic WHERE id = :id";
    $statement = $this->connection->prepare($sql);
    $statement->bindValue(":uploaderId", $new["uploaderId"] ?? $current["uploaderId"], PDO::PARAM_INT);
    $statement->bindValue(":author", $new["author"] ?? $current["author"], PDO::PARAM_STR);
    $statement->bindValue(":topic", $new["topic"] ?? $current["topic"], PDO::PARAM_STR);
    $statement->bindValue(":id", $current["id"], PDO::PARAM_INT);
    $statement->execute();
    return $statement->rowCount();
  }
  
  public function delete(int $id): int {
    $questionGateway = new QuestionGateway(new DatabaseConnection());
    $filters = [];
    $filters["testId"] = $id;
    $questions = $questionGateway->getAll($filters, null, 0, -1);
    foreach ($questions as $question) {
      $questionGateway->delete($question["id"]);
    }
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
