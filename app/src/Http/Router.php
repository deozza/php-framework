<?php

namespace App\Http;


class Router {

    const string CONTROLLER_NAMESPACE_PREFIX = "App\\Controllers\\";
    const string ROUTE_CONFIG_PATH = __DIR__ . '/../../config/routes.json';

    public static function route(Request $request): string {
        $config = self::getConfig();

        foreach($config as $route) {
            if(self::checkMethod($request, $route) === false || self::checkUri($request, $route) === false) {
                continue;
            }

            return $route['controller'];
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
        return $request->getUri() === $route['path'];
    }

}
