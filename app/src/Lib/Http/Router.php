<?php

namespace App\Lib\Http;

use App\Lib\Controllers\AbstractController;


class Router
{

    const string CONTROLLER_NAMESPACE_PREFIX = "App\\Controllers\\";
    const string ROUTE_CONFIG_PATH = __DIR__ . '/../../../config/routes.json';


    public static function route(Request $request): Response
    {
        $config = self::getConfig();

        foreach ($config as $route) {
            if (self::checkMethod($request, $route) === false) {
                continue;
            }

            $params = self::checkUriAndExtractParams($request, $route);
            if ($params === false) {
                continue;
            }

            $request->setParams($params);

            $controller = self::getControllerInstance($route['controller']);
            return $controller->process($request);
        }

        throw new \Exception('Route not found', 404);
    }

    private static function getConfig(): array
    {
        $routesConfigContent = file_get_contents(self::ROUTE_CONFIG_PATH);
        $routesConfig = json_decode($routesConfigContent, true);

        return $routesConfig;
    }


    private static function checkMethod(Request $request, array $route): bool
    {
        return $request->getMethod() === $route['method'];
    }

    private static function checkUri(Request $request, array $route): bool
    {
        return $request->getUri() === $route['path'];
    }

    private static function checkUriAndExtractParams(Request $request, array $route): array|false
    {
        $requestUri = $request->getUri();
        $routePath = $route['path'];

        if (strpos($routePath, '{') === false) {
            return $requestUri === $routePath ? [] : false;
        }

        $pattern = preg_replace('#\{[a-zA-Z0-9_]+\}#', '([^/]+)', $routePath);
        $pattern = '#^' . $pattern . '$#';

        if (!preg_match($pattern, $requestUri, $matches)) {
            return false;
        }

        preg_match_all('#\{([a-zA-Z0-9_]+)\}#', $routePath, $paramNames);

        $params = [];
        foreach ($paramNames[1] as $index => $paramName) {
            $params[$paramName] = $matches[$index + 1];
        }

        return $params;
    }

    private static function getControllerInstance(string $controller): AbstractController
    {
        $controllerClass = self::CONTROLLER_NAMESPACE_PREFIX . $controller;

        if (class_exists($controllerClass) === false) {
            throw new \Exception('Route not found', 404);
        }

        $controllerInstance = new $controllerClass();

        if (is_subclass_of($controllerInstance, AbstractController::class) === false) {
            throw new \Exception('Route not found', 404);
        }

        return $controllerInstance;
    }
}
