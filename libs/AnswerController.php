<?php

class AnswerController extends ControllerBase {
  private $token;
  private $payload;
  public function __construct(private AnswerGateway $gateway) {
    $this->token = $this->getAuthToken();
  }

  public function processRequest(string $method, array $filters, ?string $id): void {
    if ($method != "GET") {
      if (!$this->token) {
        $this->sendUnauthorizedResponse();
        return;
      }
      $this->payload = JWT::decode($this->token, getenv("SECRET_KEY"));
    }
    if ($id) {
      $this->processResourceRequest($method, $id);
    } else {
      $this->processCollectionRequest($method, $filters);
    }
  }

  public function processResourceRequest(string $method, string $id): void {
    $answer = $this->gateway->get($id);
    if (!$answer) {
      http_response_code(404);
      echo json_encode(["message" => "Answer not found"]);
      return;
    }

    switch ($method) {
      case "GET":
        echo json_encode($answer);
        break;
      case "PATCH":
        if(!$this->verifyPayload($this->payload, $answer["uploaderId"])) {
          return;
        }
        $data = (array) json_decode(file_get_contents("php://input"), true);
        $errors = $this->getValidationErrors($data, false);
        if (!empty($errors)) {
          http_response_code(422);
          echo json_encode(["errors" => $errors]);
          break;
        }
        $data["uploaderId"] = $answer["uploaderId"];
        $rows = $this->gateway->update($answer, $data);
        echo json_encode([
          "message" => "Answer $id updated",
          "rows" => $rows]);
        break;
      case "DELETE":
        if(!$this->verifyPayload($this->payload, $answer["uploaderId"])) {
          return;
        }
        $rows = $this->gateway->delete($id);
        echo json_encode([
          "message" => "Answer $id deleted",
          "rows" => $rows]);
        break;
      default:
        http_response_code(405);
        header("Allow: GET, PATCH, DELETE");
    }
  }

  public function processCollectionRequest(string $method, $filters): void {
    $sort = $filters['sort'] ?? null;
    unset($filters['sort']);
    switch ($method) {
      case "GET":
        if ($this->token) {
          $this->payload = JWT::decode($this->token, getenv("SECRET_KEY"));
          if(!$this->verifyPayload($this->payload)) {
            return;
          }
          $filters["uploaderId"] = $this->payload["userId"]; 
        }
        echo json_encode($this->gateway->getAll($filters, $sort));
        break;
      case "POST":
        $data = (array) json_decode(file_get_contents("php://input"), true);
        $data["uploaderId"] = $this->payload["userId"];
        $errors = $this->getValidationErrors($data);
        if (!empty($errors)) {
          http_response_code(422);
          echo json_encode(["errors" => $errors]);
          break;
        }
        if(!$this->verifyPayload($this->payload, $data["uploaderId"])) {
          return;
        }
        $id = $this->gateway->create($data);
        http_response_code(201);
        echo json_encode(
          ["message" => "Answer created",
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
      if (empty($data["questionId"])) {
        $errors[] = "QuestionId is required";
      }
      if (empty($data["label"])) {
        $errors[] = "Label is required";
      }
      if (!isset($data["isCorrect"])) {
        $errors[] = "IsCorrect field is required";
      }
    }
    if(array_key_exists("questionId", $data)) {
      if (filter_var($data["questionId"], FILTER_VALIDATE_INT) === false) {
        $errors[] = "Invalid questionId";
      }
    }
    if(array_key_exists("uploaderId", $data)) {
      if (filter_var($data["uploaderId"], FILTER_VALIDATE_INT) === false) {
        $errors[] = "Invalid questionId";
      }
    }
    if(array_key_exists("isCorrect", $data)) {
      if (filter_var($data["isCorrect"], FILTER_VALIDATE_BOOL) === null) {
        $errors[] = "Invalid isCorrect value";
      }
    }
    return $errors;
  }
}
?>
