<?php

class FeedbackController extends ControllerBase {
  private $token;
  private $payload;

  public function __construct(private FeedbackGateway $gateway) {
    $this->token = $this->getAuthToken();
  }

  public function processRequest(string $method, array $filters, ?string $id): void {
    if ($this->token) {
      $this->payload = JWT::decode($this->token, getenv("SECRET_KEY"));
    }
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

    switch ($method) {
      case "GET":
        echo json_encode($feedback);
        break;
      // case "PATCH":
      //   $data = (array) json_decode(file_get_contents("php://input"), true);
      //   $rows = $this->gateway->update($feedback, $data);
      //   echo json_encode([
      //     "message" => "Feedback $id updated",
      //     "rows" => $rows]);
      //   break;
      // case "DELETE":
      //   if (!$this->verifyPayload($this->payload, $feedback["questionId"])) {
      //     return;
      //   }
      //   $rows = $this->gateway->delete($id);
      //   echo json_encode([
      //     "message" => "Feedback $id deleted",
      //     "rows" => $rows]);
      //   break;
      default:
        http_response_code(405);
        header("Allow: GET, PATCH, DELETE");
    }
  }

  public function processCollectionRequest(string $method, $filters): void {
    switch ($method) {
      case "GET":
        echo json_encode($this->gateway->getAll($filters));
        break;
      case "POST":
        // if (!$this->verifyPayload($this->payload, $filters["questionId"])) {
        //   return;
        // }
        $data = (array) json_decode(file_get_contents("php://input"), true);
        $id = $this->gateway->create($data);
        http_response_code(201);
        echo json_encode([
          "message" => "Feedback created",
          "id" => $id]);
        break;
      default:
        http_response_code(405);
        header("Allow: GET, POST");
    }
  }
}
?>
