<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use App\Http\Request;
use App\Http\Router;

require_once __DIR__ . '/../../vendor/autoload.php';

if (!class_exists(Request::class)) {
    die('Class App\Http\Request not found. Check autoloading or file path.');
} else {
    echo "Class App\Http\Request loaded successfully.\n";
}

try {
    $request = new Request();
    $router = new Router();

    $response = $router->route($request);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
