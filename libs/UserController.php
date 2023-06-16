<?php

class UserController extends ControllerBase {
  private $token;
  private $payload;

  public function __construct(private UserGateway $gateway) {
    $this->token = $this->getAuthToken();
  }

  public function processRequest(string $method, array $filters, ?string $id): void {
    if (!$this->token) {
      $this->sendUnauthorizedResponse();
      return;
    }
    $this->payload = JWT::decode($this->token, getenv("SECRET_KEY"));
    if ($id) {
      $this->processResourceRequest($method, $id);
    } else {
      $this->processCollectionRequest($method, $filters);
    }
  }

  public function processResourceRequest(string $method, string $id): void {
    $user = $this->gateway->get($id);
    if (!$this->verifyPayload($this->payload, $id)) {
      return;
    }
    if (!$user) {
      http_response_code(404);
      echo json_encode(["message" => "User not found"]);
      return;
    }

    switch ($method) {
      case "GET":
        echo json_encode($user);
        break;
      case "PATCH":
        $data = (array) json_decode(file_get_contents("php://input"), true);
        $errors = $this->getValidationErrors($data, false);
        if (!empty($errors)) {
          http_response_code(422);
          echo json_encode(["errors" => $errors]);
          break;
        }
        $data["id"] = $user["id"];
        $rows = $this->gateway->update($user, $data);
        echo json_encode([
          "message" => "User $id updated",
          "rows" => $rows]);
        break;
      case "DELETE":
        if (!$this->verifyPayload($this->payload, $user["id"])) {
          return;
        }
        $rows = $this->gateway->delete($id);
        echo json_encode([
          "message" => "User $id deleted",
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
    if (!$this->verifyPayload($this->payload)) {
      return;
    }
    switch ($method) {
      case "GET":
        $filters["id"] = $this->payload["userId"]; 
        echo json_encode($this->gateway->getAll($filters, $sort));
        break;
      case "POST":
        $data = (array) json_decode(file_get_contents("php://input"), true);
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
          ["message" => "User created",
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
      if (empty($data["email"])) {
        $errors[] = "Email is required";
      } elseif (!filter_var($data["email"], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
      }

      if (empty($data["password"])) {
        $errors[] = "Password is required";
      }

      if (empty($data["firstName"])) {
        $errors[] = "First name is required";
      }

      if (empty($data["lastName"])) {
        $errors[] = "Last name is required";
      }
    }

    return $errors;
  }
}
?>
