<?php

namespace App\Http;

use App\Controllers\AbstractController;
use App\Http\Request;

class Router {
    public function route(Request $request): Response {
        foreach(self::getConfig() as $route) {
            if(self::checkUri($request, $route) === false){
                continue;
            }
        
            if(self::checkMethod($request, $route) === false){
                continue;
            }

            $controller = self::getController($route);
            return $controller->process($request);
        }

        return new Response('Not found', 404);
    }

    private static function getConfig(): array {
        $config = json_decode(file_get_contents(__DIR__ . '/../../config/routes.json'));
        return $config;
    }

    private static function checkMethod(Request $request, object $route): bool {
        return in_array($request->getMethod(), $route->methods);
    }

    private static function checkUri(Request $request, object $route): bool {
        $pattern = preg_replace('#<(\w+)>#', '(?P<$1>[^/]+)', $route->path);
        $pattern = "#^" . $pattern . "$#";
    
        if (preg_match($pattern, $request->getUri(), $matches)) {
            // Supprime les captures globales et garde seulement les groupes nommés
            $args = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
            $request->setArgs($args);
            return true;
        }
    
        return false;
    }
    

    private static function getController(object $route): AbstractController {
        $controllerNamespace = "App\\Controllers\\" . $route->controller;

        if(self::checkClassExists($controllerNamespace) === false){
            throw new \Exception("Controller not found");
        }

        $controller = new $controllerNamespace();

        if(self::checkControllerInstance($controller) === false){
            throw new \Exception("Controller not found");
        }

        return new $controllerNamespace();
    }

    private static function checkClassExists(string $controllerNamespace): bool {
        return class_exists($controllerNamespace);
    }

    private static function checkControllerInstance(AbstractController $controller): bool {
        return $controller instanceof AbstractController;
    }
}