<?php

use App\Http\Request;
use App\Http\Router;

require_once __DIR__ . '/../../vendor/autoload.php';

$request = new Request();
$router = new Router();
$response = $router->route($request);

http_response_code($response->getStatus());
foreach ($response->getHeaders() as $header => $value) {
    header("$header: $value");
}
echo $response->getContent();
