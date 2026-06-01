<?php

class CsrfMiddleware
{
    public function handle()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        
        // Only validate CSRF on state-changing methods
        if (in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $csrfToken = null;
            
            if (isset($_SERVER['HTTP_X_CSRF_TOKEN'])) {
                $csrfToken = trim($_SERVER['HTTP_X_CSRF_TOKEN']);
            } elseif (function_exists('apache_request_headers')) {
                $requestHeaders = apache_request_headers();
                $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
                if (isset($requestHeaders['X-Csrf-Token'])) {
                    $csrfToken = trim($requestHeaders['X-Csrf-Token']);
                }
            }

            if (empty($csrfToken)) {
                Response::json(['error' => 'CSRF token missing'], 403);
            }

            if (!isset($_SESSION['csrf_token'])) {
                Response::json(['error' => 'CSRF session not found'], 403);
            }

            if (!hash_equals($_SESSION['csrf_token'], $csrfToken)) {
                Response::json(['error' => 'Invalid CSRF token'], 403);
            }
        }
        
        return true;
    }
}
