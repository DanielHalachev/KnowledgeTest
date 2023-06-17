<?php

class FeedbackController extends ControllerBase {
  private $token;
  private $payload;

  public function __construct(private FeedbackGateway $gateway) {
    $this->token = $this->getAuthToken();
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

    $question = $this->getQuestion($feedback["questionId"]);
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
      case "PATCH":
        $data = (array) json_decode(file_get_contents("php://input"), true);
        $errors = $this->getValidationErrors($data, false);
        if (!empty($errors)) {
          http_response_code(422);
          echo json_encode(["errors" => $errors]);
          break;
        }
        $rows = $this->gateway->update($feedback, $data);
        echo json_encode([
          "message" => "Feedback $id updated",
          "rows" => $rows]);
        break;
      case "DELETE":
        $rows = $this->gateway->delete($id);
        echo json_encode([
          "message" => "Feedback $id deleted",
          "rows" => $rows]);
        break;
      default:
        http_response_code(405);
        header("Allow: GET, PATCH, DELETE");
    }
  }

  public function processCollectionRequest(string $method, $filters): void {
    if ($method !== "POST") {
      http_response_code(405);
      header("Allow: POST");
      return;
    }

    $data = (array) json_decode(file_get_contents("php://input"), true);
    $errors = $this->getValidationErrors($data, true);
    if (!empty($errors)) {
      http_response_code(422);
      echo json_encode(["errors" => $errors]);
      return;
    }

    $id = $this->gateway->create($data);
    http_response_code(201);
    echo json_encode([
      "message" => "Feedback created",
      "id" => $id]);
  }

  private function getValidationErrors(array $data, $isNew = true): array {
    $errors = [];
    if ($isNew) {
      if (empty($data["questionId"])) {
        $errors[] = "QuestionId is required";
      }
    }

    if (array_key_exists("questionId", $data)) {
      if (filter_var($data["questionId"], FILTER_VALIDATE_INT) === false) {
        $errors[] = "Invalid questionId";
      }
    }

    if (array_key_exists("complexity", $data)) {
      if (filter_var($data["complexity"], FILTER_VALIDATE_INT) === false) {
        $errors[] = "Invalid complexity";
      }
    }

    return $errors;
  }

  private function getQuestion(int $questionId): ?array {
    $url = "http://localhost/KnowledgeTestImproved/api/questions/$questionId";
    $headers = [
      "Authorization: Bearer $this->token",
      "Content-Type: application/json",
    ];

    $context = stream_context_create([
      "http" => [
        "method" => "GET",
        "header" => implode("\r\n", $headers),
      ],
    ]);

    $response = file_get_contents($url, false, $context);
    if ($response === false) {
      return null;
    }

    return json_decode($response, true);
  }
}
?>
