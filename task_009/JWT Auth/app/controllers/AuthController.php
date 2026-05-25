<?php

class AuthController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function register()
    {
        $body = $_REQUEST['body'] ?? [];
        $name = trim($body['name'] ?? '');
        $email = trim($body['email'] ?? '');
        $password = $body['password'] ?? '';

        if (empty($name) || empty($email) || empty($password)) {
            Response::json(['error' => 'Name, email, and password are required'], 400);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Response::json(['error' => 'Invalid email format'], 400);
        }

        $userId = $this->userModel->create($name, $email, $password);

        if ($userId === -1) {
            Response::json(['error' => 'Email already exists'], 409);
        }

        if ($userId) {
            Response::json(['message' => 'User registered successfully', 'user_id' => $userId], 201);
        } else {
            Response::json(['error' => 'Failed to register user'], 500);
        }
    }

    public function login()
    {
        $body = $_REQUEST['body'] ?? [];
        $email = trim($body['email'] ?? '');
        $password = $body['password'] ?? '';

        if (empty($email) || empty($password)) {
            Response::json(['error' => 'Email and password are required'], 400);
        }

        $user = $this->userModel->findByEmail($email);

        if ($user && password_verify($password, $user['password'])) {
            $expiry = time() + ($_ENV['JWT_EXPIRY'] ?? 3600);
            
            $payload = [
                'user_id' => $user['id'],
                'email' => $user['email'],
                'iat' => time(),
                'exp' => $expiry
            ];

            $token = JWT::encode($payload);

            Response::json([
                'message' => 'Login successful',
                'token' => $token,
                'expires_in' => $expiry
            ], 200);
        } else {
            Response::json(['error' => 'Invalid credentials'], 401);
        }
    }
}
