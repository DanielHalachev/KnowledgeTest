<?php

class SessionGateway
{
    private $connection;

    public function __construct(DatabaseConnection $databaseConnection)
    {
        $this->connection = $databaseConnection->getConnection();
    }

    public static function checkLoginStatus(): array
    {
        $isLogged = isset($_SESSION['user_id']);

        return [
            'logged' => $isLogged,
            'userData' => $isLogged ? [
                'id' => $_SESSION['user_id'],
                'email' => $_SESSION['email'],
            ] : null
        ];
    }

    public function login(array $credentials) : array {
        if (self::checkLoginStatus()['logged']) {
            http_response_code(405);
            return ["errors" => "User is already logged"];
        }

        $selectStatement = $this->connection->prepare("SELECT * FROM `users` WHERE `email` = ?");
        $selectStatement->execute([$credentials['email']]);
        $user = $selectStatement->fetch();

        if ($user && password_verify($credentials['password'], $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];

        } else {
            return ['logged' => false];
        }

        return ['logged' => true];
    }

    public static function logout() : array {
        session_destroy();
        return ['logged out' => true];
    }
}