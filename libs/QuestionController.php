<?php

class QuestionController extends ControllerBase {
  private $token;
  private $payload;

  public function __construct(private QuestionGateway $gateway) {
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
    $question = $this->gateway->get($id);
    if (!$question) {
      http_response_code(404);
      echo json_encode(["message" => "Question not found"]);
      return;
    }

    switch ($method) {
      case "GET":
        echo json_encode($question);
        break;
      case "PATCH":
        if(!$this->verifyPayload($this->payload, $question["uploaderId"])) {
          return;
        }
        $data = (array) json_decode(file_get_contents("php://input"), true);
        $errors = $this->getValidationErrors($data, false);
        if (!empty($errors)) {
          http_response_code(422);
          echo json_encode(["errors" => $errors]);
          break;
        }
        $data["uploaderId"] = $question["uploaderId"];
        $rows = $this->gateway->update($question, $data);
        echo json_encode([
          "message" => "Question $id updated",
          "rows" => $rows]);
        break;
      case "DELETE":
        if(!$this->verifyPayload($this->payload, $question["uploaderId"])) {
          return;
        }
        $rows = $this->gateway->delete($id);
        echo json_encode([
          "message" => "Question $id deleted",
          "rows" => $rows]);
        break;
      default:
        http_response_code(405);
        header("Allow: GET, PATCH, DELETE");
    }
  }

  public function processCollectionRequest(string $method, $filters): void {
    $page = $filters["page"] ?? 1;
    $size = $filters["size"] ?? DEFAULT_QUERY_SIZE;
    unset($filters["page"], $filters["size"]);
    $offset = ($page - 1) * $size;

    $sort = $filters['sort'] ?? null;
    unset($filters['sort']);
    switch ($method) {
      case "GET":
        $errors = $this->getValidationErrors($filters, false);
        if (!empty($errors)) {
          http_response_code(422);
          echo json_encode(["errors" => $errors]);
          break;
        }
        if ($this->token) {
          $this->payload = JWT::decode($this->token, getenv("SECRET_KEY"));
          if(!$this->verifyPayload($this->payload)) {
            return;
          }
          $filters["uploaderId"] = $this->payload["userId"]; 
        }
        echo json_encode($this->gateway->getAll($filters, $sort, $offset, $size));
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
        if(!$this->verifyPayload($this->payload)) {
          return;
        }
        $id = $this->gateway->create($data);
        http_response_code(201);
        echo json_encode(
          ["message" => "Question created",
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
      if (empty($data["testId"])) {
        $errors[] = "TestId is required";
      }
      if (empty($data["questionType"])) {
        $errors[] = "QuestionType is required";
      }
      if (!isset($data["isMultipleChoice"])) {
        $errors[] = "IsMultipleChoice field is required";
      }
      if (empty($data["label"])) {
        $errors[] = "Label is required";
      }
    }

    if (array_key_exists("id", $data)) {
      if (filter_var($data["id"], FILTER_VALIDATE_INT) === false) {
        $errors[] = "Invalid id";
      }
    }
    if (array_key_exists("testId", $data)) {
      if (filter_var($data["testId"], FILTER_VALIDATE_INT) === false) {
        $errors[] = "Invalid testId";
      }
    }
    if (array_key_exists("questionType", $data)) {
      if (filter_var($data["questionType"], FILTER_VALIDATE_INT) === false) {
        $errors[] = "Invalid questionType";
      }
    }
    if (array_key_exists("isMultipleChoice", $data)) {
      if (filter_var($data["isMultipleChoice"], FILTER_VALIDATE_BOOL) === null) {
        $errors[] = "Invalid isMultipleChoice value";
      }
    }
    if (array_key_exists("page", $data)) {
      if (filter_var($data["page"], FILTER_VALIDATE_INT) === false) {
        $errors[] = "Invalid page value";
      }
    }
    if (array_key_exists("size", $data)) {
      if (filter_var($data["size"], FILTER_VALIDATE_INT) === false) {
        $errors[] = "Invalid size value";
      }
    }

    return $errors;
  }
}
?>
