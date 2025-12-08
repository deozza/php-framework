<?php

namespace App\Lib\Http;

use App\Lib\Controllers\AbstractController;
use App\Controllers\ContactController;


class Router {
    const CONTROLLER_NAMESPACE_PREFIX = "App\\Controllers\\";
    const ROUTE_CONFIG_PATH = __DIR__ . '/../../../config/routes.json';
    

    public static function route(Request $request): Response {
        $config = self::getConfig();

        foreach($config as $route) {
            if(self::checkMethod($request, $route) === false) {
                continue;
            }

            $uriCheck = self::checkUri($request, $route);
            if($uriCheck === false) {
                continue;
            }

            $controller = self::getControllerInstance($route['controller']);

            if ($route['controller'] === 'ContactController' && $request->getUri() === '/contact') {
                if ($request->getMethod() === 'GET') {
                    return $controller->getAll($request);
                } elseif ($request->getMethod() === 'POST') {
                    return $controller->process($request);
                }
            }

            if ($route['controller'] === 'ContactController' && $route['path'] === '/contact/{params}' && $request->getMethod() === 'GET') {
                return $controller->getContact($request, $uriCheck);
            }

            if ($route['controller'] === 'ContactController' && $route['path'] === '/contact/{params}' && $request->getMethod() === 'PATCH') {
                return $controller->update($request, $uriCheck);
            }

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

    private static function checkUri(Request $request, array $route) {
        $routePath = $route['path'];
        $uri = $request->getUri();

        if (strpos($routePath, '{params}') !== false) {
            $pattern = str_replace('{params}', '([^/]+)', $routePath);
            if (preg_match('#^' . $pattern . '$#', $uri, $matches)) {
                return $matches[1]; // valeur capturée
            }
            return false;
        }
        return $uri === $routePath ? true : false;
    }
    private static function getControllerInstance(string $controller) {
        $controllerClass = self::CONTROLLER_NAMESPACE_PREFIX . $controller;

        if(class_exists($controllerClass) === false) {
            throw new \Exception('Route not found', 404);
        }

        $controllerInstance = new $controllerClass();

        if(is_subclass_of($controllerInstance, AbstractController::class)=== false){
            throw new \Exception('Route not found', 404);
        }
        
        return $controllerInstance;
    }

}
