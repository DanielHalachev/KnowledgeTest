<?php
class FeedbackGateway {
  
  private $connection;
  
  public function __construct(DatabaseConnection $databaseConnection) {
    $this->connection = $databaseConnection->getConnection();
  }
  
  public function get(int $id): array | false {
    $sql = "SELECT * FROM `feedbacks` WHERE id = ?";
    $statement = $this->connection->prepare($sql);
    $statement->bindParam(1, $id, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetch();
  }
  
  public function getAll(): array {
    $sql = "SELECT * FROM `feedbacks`";
    $statement = $this->connection->prepare($sql);
    $statement->execute();
    return $statement->fetchAll();
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

  public function update(array $current, array $new):int {
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
}
?>
