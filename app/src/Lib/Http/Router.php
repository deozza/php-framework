<?php

namespace App\Lib\Http;

use App\Lib\Controllers\AbstractController;

class Router {
    const string CONTROLLER_NAMESPACE_PREFIX = "App\\Controllers\\";
    const string ROUTE_CONFIG_PATH = __DIR__ . '/../../../config/routes.json';

    public static function route(Request $request): Response {
        $config = self::getConfig();

        foreach($config as $route) {
            if(self::checkMethod($request, $route) === false || self::checkUri($request, $route) === false) {
                continue;
            }

            $controller = self::getControllerInstance($route['controller']);
            return $controller->process($request);
        }

        throw new \Exception('Route not found', 404);
    }
    
    private static function getConfig(): array {
        $routesConfigContent = file_get_contents(self::ROUTE_CONFIG_PATH);
        $routesConfig = json_decode($routesConfigContent, true);
        return $routesConfig;
    }

    private static function checkMethod(Request $request, array $route): bool {
        return $request->getMethod() === $route['method'];
    }

    private static function checkUri(Request $request, array $route): bool {
        $requestPath = $request->getPath();

        if ($requestPath === $route['path']) {
            return true;
        }

        if (strpos($route['path'], '{') !== false) {
            $pattern = preg_replace_callback('#\{([^}]+)\}#', function($m){
                return '(?P<' . $m[1] . '>.+)';
            }, $route['path']);

            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $requestPath, $matches)) {
                if (method_exists($request, 'setRouteParams')) {
                    $request->setRouteParams($matches);
                }
                return true;
            }
        }

        return false;
    }
    
    private static function getControllerInstance(string $controller): AbstractController {
        $controllerClass = self::CONTROLLER_NAMESPACE_PREFIX . $controller;

        if(class_exists($controllerClass) === false) {
            throw new \Exception('Route not found', 404);
        }

        $controllerInstance = new $controllerClass();

        if(is_subclass_of($controllerInstance, AbstractController::class) === false){
            throw new \Exception('Route not found', 404);
        }
        
        return $controllerInstance;
    }
}
