<?php
class TopicGateway {
  
  private $connection;
  
  public function __construct(DatabaseConnection $databaseConnection) {
    $this->connection = $databaseConnection->getConnection();
  }
  
  public function get(int $id): array | false {
    $sql = "SELECT * FROM `topics` WHERE id = ?";
    $statement = $this->connection->prepare($sql);
    $statement->bindParam(1, $id, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetch();
  }
  
  public function getAll(): array {
    $sql = "SELECT * FROM `topics`";
    $statement = $this->connection->prepare($sql);
    $statement->execute();
    return $statement->fetchAll();
  }

  public function create(array $data): int {
    $sql = "INSERT INTO `topics` (name) VALUES (:name)";
    $statement = $this->connection->prepare($sql);
    $statement->bindValue(":name", $data["name"], PDO::PARAM_STR);
    $statement->execute();
    return $this->connection->lastInsertId();
  }

  public function update(array $current, array $new):int {
    $sql = "UPDATE `topics` SET name = :name WHERE id = :id";
    $statement = $this->connection->prepare($sql);
    $statement->bindValue(":name", $new["name"] ?? $current["name"], PDO::PARAM_STR);
    $statement->bindValue(":id", $current["id"], PDO::PARAM_INT);
    $statement->execute();
    return $statement->rowCount();
  }
  
  public function delete(int $id): int {
    $sql = "DELETE FROM `topics` WHERE id = :id";
    $statement = $this->connection->prepare($sql);
    $statement->bindValue(":id", $id, PDO::PARAM_INT);
    $statement->execute();
    return $statement->rowCount();
  }
}
?>
