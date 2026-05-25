<?php

class JsonMiddleware
{
    public function handle()
    {
        $method = $_SERVER['REQUEST_METHOD'];

        if (in_array($method, ['POST', 'PUT', 'PATCH'])) {
            $contentType = isset($_SERVER['CONTENT_TYPE']) ? trim($_SERVER['CONTENT_TYPE']) : '';

            if (strpos($contentType, 'application/json') === false) {
                Response::json(['error' => 'Content-Type must be application/json'], 400);
            }

            $content = file_get_contents('php://input');
            
            if (empty($content)) {
                Response::json(['error' => 'Empty request body'], 400);
            }

            $decoded = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Response::json(['error' => 'Invalid JSON payload'], 400);
            }

            // Attach parsed body to global $_REQUEST
            $_REQUEST['body'] = $decoded;
        }

        return true;
    }
}
