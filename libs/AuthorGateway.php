<?php
class AuthorGateway {
  
  private $connection;
  
  public function __construct(DatabaseConnection $databaseConnection) {
    $this->connection = $databaseConnection->getConnection();
  }
  
  public function get(int $id): array | false {
    $sql = "SELECT * FROM `authors` WHERE id = ?";
    $statement = $this->connection->prepare($sql);
    $statement->bindParam(1, $id, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetch();
  }
  
  public function getAll(): array {
    $sql = "SELECT * FROM `authors`";
    $statement = $this->connection->prepare($sql);
    $statement->execute();
    return $statement->fetchAll();
  }

  public function create(array $data): int {
    $sql = "INSERT INTO `authors` (facultyNumber, firstName, lastName) VALUES (:facultyNumber, :firstName, :lastName)";
    $statement = $this->connection->prepare($sql);
    $statement->bindValue(":facultyNumber", $data["facultyNumber"], PDO::PARAM_STR);
    $statement->bindValue(":firstName", $data["firstName"] ?? null, PDO::PARAM_STR);
    $statement->bindValue(":lastName", $data["lastName"] ?? null, PDO::PARAM_STR);
    $statement->execute();
    return $this->connection->lastInsertId();
  }

  public function update(array $current, array $new):int {
    $sql = "UPDATE `authors` SET facultyNumber = :facultyNumber, firstName = :firstName, lastName = :lastName WHERE id = :id";
    $statement = $this->connection->prepare($sql);
    $statement->bindValue(":facultyNumber", $new["facultyNumber"] ?? $current["facultyNumber"], PDO::PARAM_STR);
    $statement->bindValue(":firstName", $new["firstName"] ?? $current["firstName"], PDO::PARAM_STR);
    $statement->bindValue(":lastName", $new["lastName"] ?? $current["lastName"], PDO::PARAM_STR);
    $statement->bindValue(":id", $current["id"], PDO::PARAM_INT);
    $statement->execute();
    return $statement->rowCount();
  }
  
  public function delete(int $id): int {
    $sql = "DELETE FROM `authors` WHERE id = :id";
    $statement = $this->connection->prepare($sql);
    $statement->bindValue(":id", $id, PDO::PARAM_INT);
    $statement->execute();
    return $statement->rowCount();
  }
}
?>
