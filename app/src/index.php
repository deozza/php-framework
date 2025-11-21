<?php

use App\Http\Request;

require_once __DIR__ . '/../vendor/autoload.php';

$request = new Request();

$routesConfigPath = __DIR__ . '/../config/routes.json';

$routes = json_decode(file_get_contents($routesConfigPath), true);

foreach($routes as $route) {
    if($route['path'] !== $request->getUri()) {
        continue;
    }

    
    if($route['method'] !== $request->getMethod()) {
        continue;
    }

    echo $route['controller'];
}

