<?php

class QuestionTypeController extends ControllerBase {
  private $token;
  private $payload;

  public function __construct(private QuestionTypeGateway $gateway) {
  }

  public function processRequest(string $method, array $filters, ?string $id): void {
    if ($method !== "GET" && $method !== "POST") {
      http_response_code(405);
      header("Allow: GET, POST");
      return;
    }

    if ($id) {
      $this->processResourceRequest($method, $id);
    } else {
      $this->processCollectionRequest($method, $filters);
    }
  }

  public function processResourceRequest(string $method, string $id): void {
    if ($method !== "GET") {
      http_response_code(405);
      header("Allow: GET");
      return;
    }

    $questionType = $this->gateway->get($id);
    if (!$questionType) {
      http_response_code(404);
      echo json_encode(["message" => "Question type not found"]);
      return;
    }

    echo json_encode($questionType);
  }

  public function processCollectionRequest(string $method, array $filters): void {
    $sort = $filters['sort'] ?? null;
    unset($filters['sort']);
    if ($method !== "GET" && $method !== "POST") {
      http_response_code(405);
      header("Allow: GET, POST");
      return;
    }

    switch ($method) {
      case "GET":
        $errors = $this->getValidationErrors($filters, false);
        if (!empty($errors)) {
          http_response_code(422);
          echo json_encode(["errors" => $errors]);
          break;
        }
        echo json_encode($this->gateway->getAll($filters, $sort));
        break;
      case "POST":
        $data = (array) json_decode(file_get_contents("php://input"), true);
        $errors = $this->getValidationErrors($data, true);
        if (!empty($errors)) {
          http_response_code(422);
          echo json_encode(["errors" => $errors]);
          break;
        }

        $id = $this->gateway->create($data);
        http_response_code(201);
        echo json_encode(["message" => "Question type created", "id" => $id]);
        break;
    }
  }

  private function getValidationErrors(array $data, bool $isNew = true): array {
    $errors = [];
    if ($isNew){
      if (empty($data["description"])) {
        $errors[] = "Description is required";
      }
    }
    if (array_key_exists("id", $data)) {
      if (filter_var($data["id"], FILTER_VALIDATE_INT) === false) {
        $errors[] = "Invalid id";
      }
    }

    return $errors;
  }
}

?>

