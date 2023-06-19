<?php

class FeedbackController extends ControllerBase {
  private $token;
  private $payload;
  private $questionGateway;

  public function __construct(private FeedbackGateway $gateway) {
    $this->token = $this->getAuthToken();
    $this->questionGateway = new QuestionGateway(new DatabaseConnection());
  }

  public function processRequest(string $method, array $filters, ?string $id): void {
    if ($id) {
      $this->processResourceRequest($method, $id);
    } else {
      $this->processCollectionRequest($method, $filters);
    }
  }

  public function processResourceRequest(string $method, string $id): void {
    $feedback = $this->gateway->get($id);
    if (!$feedback) {
      http_response_code(404);
      echo json_encode(["message" => "Feedback not found"]);
      return;
    }

    $question = $this->questionGateway->get($feedback["questionId"]);
    if (!$question) {
      http_response_code(404);
      echo json_encode(["message" => "Question not found"]);
      return;
    }

    if (!$this->token) {
      $this->sendUnauthorizedResponse();
      return;
    }
    
    $this->payload = JWT::decode($this->token, getenv("SECRET_KEY"));

    if (!$this->verifyPayload($this->payload, $question["uploaderId"])) {
      return;
    }

    switch ($method) {
      case "GET":
        echo json_encode($feedback);
        break;
      // case "PATCH":
      //   $data = (array) json_decode(file_get_contents("php://input"), true);
      //   $errors = $this->getValidationErrors($data, false);
      //   if (!empty($errors)) {
      //     http_response_code(422);
      //     echo json_encode(["errors" => $errors]);
      //     break;
      //   }
      //   $rows = $this->gateway->update($feedback, $data);
      //   echo json_encode([
      //     "message" => "Feedback $id updated",
      //     "rows" => $rows]);
      //   break;
      case "DELETE":
        $rows = $this->gateway->delete($id);
        echo json_encode([
          "message" => "Feedback $id deleted",
          "rows" => $rows]);
        break;
      default:
        http_response_code(405);
        header("Allow: GET, DELETE");
    }
  }

  public function processCollectionRequest(string $method, array $filters): void {
    $page = $filters["page"] ?? 1;
    $size = $filters["size"] ?? DEFAULT_QUERY_SIZE;
    unset($filters["page"], $filters["size"]);
    $offset = ($page - 1) * $size;

    $sort = $filters['sort'] ?? null;
    unset($filters['sort']);
    
    switch ($method) {
      case "GET":
        if (!$this->token) {
          $this->sendUnauthorizedResponse();
          return;
        }
        $this->payload = JWT::decode($this->token, getenv("SECRET_KEY"));
        $data = (array) json_decode(file_get_contents("php://input"), true);
        $errors = $this->getValidationErrors($data);

        $questionId = (int) $filters['questionId'] ?? null;

        if (!$questionId) {
          http_response_code(400);
          echo json_encode(["message" => "Question ID is required"]);
          return;
        }

        $question = $this->questionGateway->get($questionId);
        if (!$question) {
          http_response_code(404);
          echo json_encode(["message" => "Question not found"]);
          return;
        }
        if (!$this->verifyPayload($this->payload, $question["uploaderId"])) {
          return;
        }
        echo json_encode($this->gateway->getAll($filters, $sort, $offset, $size));
        break;
      case "POST":
        $data = (array) json_decode(file_get_contents("php://input"), true);
        $errors = $this->getValidationErrors($data);
        if (!empty($errors)) {
          http_response_code(422);
          echo json_encode(["errors" => $errors]);
          break;
        }
        $id = $this->gateway->create($data);
        http_response_code(201);
        echo json_encode(
          ["message" => "Feedback created",
            "id" => $id]);
        break;
      default:
        http_response_code(405);
        header("Allow: GET, POST");
    }
  }

  private function getValidationErrors(array $data, bool $isNew = true): array {
    $errors = [];

    if ($isNew) {
      if (empty($data["complexity"])) {
        $errors[] = "Complexity is required";
      }
      if (empty($data["feedback"])) {
        $errors[] = "Feedback is required";
      }
    }

    if (empty($data["questionId"])) {
      $errors[] = "QuestionId is required";
    }

    if (array_key_exists("questionId", $data)) {
      if (filter_var($data["questionId"], FILTER_VALIDATE_INT) === false) {
        $errors[] = "Invalid questionId";
      }
    }

    if (array_key_exists("id", $data)) {
      if (filter_var($data["id"], FILTER_VALIDATE_INT) === false) {
        $errors[] = "Invalid id";
      }
    }
    if (array_key_exists("complexity", $data)) {
      if (filter_var($data["complexity"], FILTER_VALIDATE_INT) === false) {
        $errors[] = "Invalid complexity";
      }
    }

    return $errors;
  }
}
?>
