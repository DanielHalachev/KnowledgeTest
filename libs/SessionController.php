<?php

class SessionController {
    public function __construct(private SessionGateway $gateway) {
    }

    public function processRequest(string $method, ?array $credentials): void
    {
        switch ($method) {
            case "GET" :
            {
                echo json_encode($this->gateway->checkLoginStatus());
                break;
            }
            case "POST" :
            {
                $errors = $this->getValidationErrors($credentials);
                if (!empty($errors)) {
                    http_response_code(422);
                    echo json_encode(["errors" => $errors]);
                    break;
                }
                echo json_encode($this->gateway->login($credentials));
                break;
            }
            case "DELETE" :
            {
                echo json_encode($this->gateway->logout());
                break;
            }
            default:
                http_response_code(405);
                header("Allow: GET, POST, DELETE");
        }
    }

    private function getValidationErrors(array $data): array {
        $errors = [];

        if (empty($data["email"])) {
            $errors[] = "Email is required";
        }
        if (empty($data["password"])) {
            $errors[] = "Password is required";
        }

        return $errors;
    }
}
?>
