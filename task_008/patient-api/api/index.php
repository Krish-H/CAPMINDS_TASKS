<?php
require_once __DIR__ . '/middlewares/JsonMiddleware.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/controllers/PatientController.php';

// Apply JSON Middleware
JsonMiddleware::handle();

// Setup Database Connection
$database = new Database();
$db = $database->getConnection();

// Parse request localhost/api/index.php?request=patients/5
$request = isset($_GET['request']) ? $_GET['request'] : '';
$requestParts = explode('/', trim($request, '/'));

$resource = isset($requestParts[0]) && $requestParts[0] !== '' ? $requestParts[0] : null;
$id = isset($requestParts[1]) && $requestParts[1] !== '' ? intval($requestParts[1]) : null;

$method = $_SERVER['REQUEST_METHOD'];

// Route request
if ($resource === 'patients') {
    $controller = new PatientController($db);
    $controller->processRequest($method, $id);
} else {
    require_once __DIR__ . '/helpers/Response.php';
    Response::send(404, false, "Endpoint not found");
}
?>
