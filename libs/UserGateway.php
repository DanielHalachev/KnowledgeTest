<?php
class UserGateway {

  private $connection;
  private array $validFields = [
    'id',
    'googleId', 
    'email', 
    'password', 
    'firstName', 
    'lastName', 
    'profilePicture'];
  
  public function __construct(DatabaseConnection $databaseConnection) {
    $this->connection = $databaseConnection->getConnection();
  }

  public function get(string $id): array | false {
    $sql = "SELECT * FROM `users` WHERE id = ?";
    $connection = (new DatabaseConnection())->getConnection();
    $statement = $connection->prepare($sql);
    $statement->bindParam(1, $id);
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

    $sql = "SELECT * FROM `users`";
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

  public function create(array $data):string {
    $sql = "INSERT INTO `users` (googleId, email, password, firstName, lastName, profilePicture) VALUES (?, ?, ?, ?, ?, ?)";
    $statement = $this->connection->prepare($sql);
    $statement->bindParam(1, $data["googleId"], PDO::PARAM_STR);
    $statement->bindParam(2, $data["email"], PDO::PARAM_STR);
    $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
    $statement->bindParam(3, $hashedPassword, PDO::PARAM_STR);
    $statement->bindParam(4, $data["firstName"], PDO::PARAM_STR);
    $statement->bindParam(5, $data["lastName"], PDO::PARAM_STR);
    $statement->bindParam(6, $data["profilePicture"], PDO::PARAM_STR);

    $statement->execute();
    return $this->connection->lastInsertId();
  }

  public function update(array $current, array $new):int {
    $sql = "UPDATE `users` SET password = :password, firstName = :firstName, lastName = :lastName, profilePicture = :profilePicture WHERE id = :id";
    $statement = $this->connection->prepare($sql);
    $hashedNewPassword = isset($new['password']) ? password_hash($new['password'], PASSWORD_DEFAULT) : null;
    $statement->bindValue(":password", $hashedNewPassword ?? $current["password"], PDO::PARAM_STR);
    $statement->bindValue(":firstName", $new["firstName"] ?? $current["firstName"], PDO::PARAM_STR);
    $statement->bindValue(":lastName", $new["lastName"] ?? $current["lastName"], PDO::PARAM_STR);
    $statement->bindValue(":profilePicture", $new["profilePicture"] ?? $current["profilePicture"], PDO::PARAM_STR);
    $statement->bindValue(":id", $current["id"], PDO::PARAM_INT);

    $statement->execute();
    return $statement->rowCount();
  }

  public function delete(string $id): int {
    $sql = "DELETE FROM `users` WHERE id=:id";
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
