<?php

class AuthController
{
    private $userModel;
    private $refreshTokenModel;

    public function __construct()
    {
        $this->userModel = new User();
        $this->refreshTokenModel = new RefreshToken();
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
            $accessExpirySecs = isset($_ENV['JWT_ACCESS_EXPIRY']) ? (int)$_ENV['JWT_ACCESS_EXPIRY'] : 900;
            $accessExpiry = time() + $accessExpirySecs;
            
            $payload = [
                'user_id' => $user['id'],
                'email' => $user['email'],
                'iat' => time(),
                'exp' => $accessExpiry
            ];

            $accessToken = JWT::encode($payload);

            // Generate refresh token
            $refreshToken = bin2hex(random_bytes(40));
            $refreshExpirySecs = isset($_ENV['JWT_REFRESH_EXPIRY']) ? (int)$_ENV['JWT_REFRESH_EXPIRY'] : 604800;
            $refreshExpiryTime = time() + $refreshExpirySecs;
            $expiresAt = date('Y-m-d H:i:s', $refreshExpiryTime);

            // Enforce single active session by deleting previous refresh tokens
            $this->refreshTokenModel->deleteByUserId($user['id']);

            // Store refresh token in DB
            $this->refreshTokenModel->create($user['id'], $refreshToken, $expiresAt);

            // Set HttpOnly cookie
            setcookie(
                'refresh_token',
                $refreshToken,
                [
                    'expires' => $refreshExpiryTime,
                    'path' => '/',
                    'domain' => '', // Default to current domain
                    'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
                    'httponly' => true,
                    'samesite' => 'Strict'
                ]
            );

            // Generate CSRF token
            $csrfToken = bin2hex(random_bytes(32));
            $_SESSION['csrf_token'] = $csrfToken;
            session_regenerate_id(true);

            Response::json([
                'message' => 'Login successful',
                'access_token' => $accessToken,
                'expires_in' => $accessExpiry,
                'csrf_token' => $csrfToken
            ], 200);
        } else {
            Response::json(['error' => 'Invalid credentials'], 401);
        }
    }

    public function refresh()
    {
        if (!isset($_COOKIE['refresh_token'])) {
            Response::json(['error' => 'Refresh token missing'], 401);
        }

        $tokenStr = $_COOKIE['refresh_token'];
        $tokenData = $this->refreshTokenModel->findByToken($tokenStr);

        if (!$tokenData) {
            // Token not found in DB
            Response::json(['error' => 'Invalid refresh token'], 401);
        }

        if (strtotime($tokenData['expires_at']) < time()) {
            // Token expired
            $this->refreshTokenModel->delete($tokenStr); // Cleanup
            Response::json(['error' => 'Refresh token expired'], 401);
        }

        $user = $this->userModel->findById($tokenData['user_id']);
        if (!$user) {
            Response::json(['error' => 'User no longer exists'], 401);
        }

        // Token is valid. Rotate it.
        $this->refreshTokenModel->delete($tokenStr);

        $newRefreshToken = bin2hex(random_bytes(40));
        $refreshExpirySecs = isset($_ENV['JWT_REFRESH_EXPIRY']) ? (int)$_ENV['JWT_REFRESH_EXPIRY'] : 604800;
        $refreshExpiryTime = time() + $refreshExpirySecs;
        $expiresAt = date('Y-m-d H:i:s', $refreshExpiryTime);

        $this->refreshTokenModel->create($user['id'], $newRefreshToken, $expiresAt);

        setcookie(
            'refresh_token',
            $newRefreshToken,
            [
                'expires' => $refreshExpiryTime,
                'path' => '/',
                'domain' => '', 
                'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
                'httponly' => true,
                'samesite' => 'Strict'
            ]
        );

        // Generate new Access Token
        $accessExpirySecs = isset($_ENV['JWT_ACCESS_EXPIRY']) ? (int)$_ENV['JWT_ACCESS_EXPIRY'] : 900;
        $accessExpiry = time() + $accessExpirySecs;
        $payload = [
            'user_id' => $user['id'],
            'email' => $user['email'],
            'iat' => time(),
            'exp' => $accessExpiry
        ];

        $accessToken = JWT::encode($payload);

        Response::json([
            'message' => 'Token refreshed successfully',
            'access_token' => $accessToken,
            'expires_in' => $accessExpiry
        ], 200);
    }
}
