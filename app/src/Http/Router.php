<?php

namespace App\Http;

use App\Controllers\AbstractController;
use App\Http\Request;
use Exception;

class Router {

    public function route(Request $request): Response {
        $config = self::getConfig();

        foreach ($config as $route) {
            if (self::checkUri($request, $route) === false) {
                continue;
            }
            if (self::checkMethod($request, $route) === false) {
                continue;
            }
            try {
                $controller = self::getController($route);
                return $controller->process($request);
            } catch (Exception $e) {
                return new Response(json_encode(["error" => $e->getMessage()]), 500);
            }
        }

        return new Response(json_encode(['error' => 'Not found']), 404);
    }

    private static function getConfig(): array {
        $routeFilePath = __DIR__ . '/../../config/routes.json';

        if (!file_exists($routeFilePath)) {
            throw new Exception("Routes configuration file not found.");
        }

        $config = json_decode(file_get_contents($routeFilePath));

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Error parsing routes configuration.");
        }

        return $config;
    }

    private static function checkMethod(Request $request, object $route): bool {
        return in_array($request->getMethod(), $route->methods);
    }

    private static function checkUri(Request $request, object $route): bool {
        $pattern = preg_replace('/\{(\w+)\}/', '([^/]+)', $route->path); 
        $pattern = '#^' . $pattern . '$#'; 

        if (preg_match($pattern, $request->getUri(), $matches)) {
            if (count($matches) > 1) {
                $request->setAttributes(['email' => $matches[1]]);
            }
            return true;
        }

        return false;
    }

    private static function getController(object $route): AbstractController {
        $controllerNamespace = "App\\Controllers\\" . $route->controller;


        if (!class_exists($controllerNamespace)) {
            throw new Exception("Controller class not found: " . $controllerNamespace);
        }

        $controller = new $controllerNamespace();

        if (!($controller instanceof AbstractController)) {
            throw new Exception("Invalid controller instance.");
        }

        return $controller;
    }
}
