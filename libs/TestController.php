<?php

class TestController extends ControllerBase {
  private $token;
  private $payload;

  public function __construct(private TestGateway $gateway) {
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
    $test = $this->gateway->get($id);
    if (!$test) {
      http_response_code(404);
      echo json_encode(["message" => "Test not found"]);
      return;
    }

    switch ($method) {
      case "GET":
        echo json_encode($test);
        break;
      case "PATCH":
        if (!$this->verifyPayload($this->payload, $test["uploaderId"])) {
          return;
        }
        $data = (array) json_decode(file_get_contents("php://input"), true);
        $errors = $this->getValidationErrors($data, false);
        if (!empty($errors)) {
          http_response_code(422);
          echo json_encode(["errors" => $errors]);
          break;
        }
        $data["uploaderId"] = $test["uploaderId"];
        $rows = $this->gateway->update($test, $data);
        echo json_encode([
          "message" => "Test $id updated",
          "rows" => $rows]);
        break;
      case "DELETE":
        if (!$this->verifyPayload($this->payload, $test["uploaderId"])) {
          return;
        }
        $rows = $this->gateway->delete($id);
        echo json_encode([
          "message" => "Test $id deleted",
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
        if ($this->token) {
          $this->payload = JWT::decode($this->token, getenv("SECRET_KEY"));
          if (!$this->verifyPayload($this->payload)) {
            return;
          }
          $filters["uploaderId"] = $this->payload["userId"];
        }
        $errors = $this->getValidationErrors($filters, false);
        if (!empty($errors)) {
          http_response_code(422);
          echo json_encode(["errors" => $errors]);
          break;
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
        if (!$this->verifyPayload($this->payload)) {
          return;
        }
        $id = $this->gateway->create($data);
        http_response_code(201);
        echo json_encode(
          ["message" => "Test created",
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
      if (empty($data["uploaderId"])) {
        $errors[] = "UploaderId is required";
      }
    }

    if (array_key_exists("id", $data)) {
      if (filter_var($data["id"], FILTER_VALIDATE_INT) === false) {
        $errors[] = "Invalid id";
      }
    }
    if (array_key_exists("uploaderId", $data)) {
      if (filter_var($data["uploaderId"], FILTER_VALIDATE_INT) === false) {
        $errors[] = "Invalid uploaderId";
      }
    }
    // if (array_key_exists("authorId", $data)) {
    //   if (filter_var($data["authorId"], FILTER_VALIDATE_INT) === false) {
    //     $errors[] = "Invalid authorId";
    //   }
    // }
    // if (array_key_exists("topicId", $data)) {
    //   if (filter_var($data["topicId"], FILTER_VALIDATE_INT) === false) {
    //     $errors[] = "Invalid topicId";
    //   }
    // }
    return $errors;
  }
}
?>
