<?php

class AuthMiddleware
{
    public function handle()
    {
        $headers = null;

        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER['Authorization']);
        } elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) { // Nginx or fast CGI
            $headers = trim($_SERVER['HTTP_AUTHORIZATION']);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }

        if (empty($headers)) {
            Response::json(['error' => 'Authorization header missing'], 401);
        }

        if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
            $token = $matches[1];
            $decoded = JWT::decode($token);

            if (!$decoded) {
                Response::json(['error' => 'Invalid or expired token'], 401);
            }

            if (!isset($decoded['jti'])) {
                Response::json(['error' => 'Invalid token structure'], 401);
            }

            // Verify the token exists in the database using jti
            $tokenModel = new Token();
            if (!$tokenModel->isValid($decoded['user_id'], $decoded['jti'])) {
                Response::json(['error' => 'Token has been revoked or is invalid for this session'], 401);
            }

            // Verify user still exists
            $userModel = new User();
            $user = $userModel->findById($decoded['user_id']);
            if (!$user) {
                Response::json(['error' => 'User no longer exists'], 401);
            }

            // Attach user data to request
            $_REQUEST['user'] = $decoded;
            return true;
        }

        Response::json(['error' => 'Invalid authorization format'], 401);
    }
}
