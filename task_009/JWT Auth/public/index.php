<?php

// CORS Headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../config/config.php';

// Simple Autoloader
spl_autoload_register(function ($class) {
    $directories = [
        BASE_PATH . '/app/core/',
        BASE_PATH . '/app/helpers/',
        BASE_PATH . '/app/middleware/',
        BASE_PATH . '/app/models/',
        BASE_PATH . '/app/controllers/'
    ];

    foreach ($directories as $directory) {
        $file = $directory . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Run Global Middleware manually since we don't have a global middleware stack in our simple Router
$jsonMiddleware = new JsonMiddleware();
$jsonMiddleware->handle();

// Setup Router
$router = new Router();

// Auth Routes
$router->add('POST', '/api/register', 'AuthController@register');
$router->add('POST', '/api/login', 'AuthController@login');

// Protected Patient Routes
$protected = ['AuthMiddleware'];
$router->add('GET', '/api/patients', 'PatientController@index', $protected);
$router->add('POST', '/api/patients', 'PatientController@store', $protected);
$router->add('PUT', '/api/patients/{id}', 'PatientController@update', $protected);
$router->add('DELETE', '/api/patients/{id}', 'PatientController@destroy', $protected);

// Dispatch request
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = urldecode($uri);

// Calculate base paths to strip from URI
$scriptName = $_SERVER['SCRIPT_NAME'];
$scriptDir = str_replace('\\', '/', dirname($scriptName)); // e.g., /JWT Auth/public

if (strpos($uri, $scriptName) === 0) {
    $uri = substr($uri, strlen($scriptName));
} elseif ($scriptDir !== '/' && strpos($uri, $scriptDir) === 0) {
    $uri = substr($uri, strlen($scriptDir));
} else {
    $parentDir = str_replace('\\', '/', dirname($scriptDir)); // e.g., /JWT Auth
    if ($parentDir !== '/' && strpos($uri, $parentDir) === 0) {
        $uri = substr($uri, strlen($parentDir));
    }
}

if ($uri === '' || $uri[0] !== '/') {
    $uri = '/' . ltrim($uri, '/');
}

$method = $_SERVER['REQUEST_METHOD'];
$router->dispatch($uri, $method);
