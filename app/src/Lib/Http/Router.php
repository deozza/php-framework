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
        $currentMethod = $request->getMethod();
        if ($currentMethod === $route['method']) {
            return true;
        }

        if ($currentMethod === 'POST') {
            foreach ($request->getHeaders() as $k => $v) {
                if (strtolower($k) === 'x-http-method-override' && strtoupper($v) === $route['method']) {
                    return true;
                }
            }

            if (isset($_REQUEST['_method']) && strtoupper($_REQUEST['_method']) === $route['method']) {
                return true;
            }
            if (isset($_GET['_method']) && strtoupper($_GET['_method']) === $route['method']) {
                return true;
            }
        }

        return false;
    }

    private static function checkUri(Request $request, array $route): bool {
        $requestUri = $request->getUri();
        $requestPath = parse_url($requestUri, PHP_URL_PATH);
        $routePath = $route['path'];

        if ($requestPath === $routePath) {
            return true;
        }
        if (strpos($routePath, '{') !== false) {
            $pattern = preg_replace('#\{([a-zA-Z0-9_]+)\}#', '(?P<$1>[^/]+)', $routePath);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $requestPath, $matches)) {
                $params = [];
                foreach ($matches as $key => $value) {
                    if (!is_int($key)) {
                        $params[$key] = $value;
                    }
                }

                $request->setParams($params);
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

        if(is_subclass_of($controllerInstance, AbstractController::class)=== false){
            throw new \Exception('Route not found', 404);
        }
        
        return $controllerInstance;
    }

}
