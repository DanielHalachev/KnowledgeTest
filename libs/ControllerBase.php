<?php
class ControllerBase {

  protected function sendUnauthorizedResponse(): void {
    http_response_code(401);
    echo json_encode(["message" => "Unauthorized access"]);
  } 
  protected function sendForbiddenResponse(): void {
    http_response_code(403);
    echo json_encode(["message" => "Forbidden resource"]);
  }

  protected function getAuthToken(): ?string {

    $headers = getAllHeaders();
    if (!array_key_exists('Authorization', $headers)) {
      return null;
    }
    $authHeader = $headers['Authorization'] ?? null;

    if ($authHeader && preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
      return $matches[1];
    }
    return null;
  }
  protected function verifyPayload(?array $payload, ?int $resourceOwnerId = null): bool {
    if (!$payload) {
      $this->sendUnauthorizedResponse();
      return false;
    }
    if (!$payload["expiration"] || strtotime($payload["expiration"]) > time()) {
      $this->sendUnauthorizedResponse();
      return false;
    }
    if ($resourceOwnerId && $payload["userId"] && $payload["userId"] !== $resourceOwnerId) {
      $this->sendForbiddenResponse();
      return false;      
    }
    return true;
  }
}

?>
