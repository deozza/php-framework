<?php

namespace App\Http;

use App\Http\Request;

class Router
{
    public function route(Request $request): ?Response {
        $route = self::getRouteFromRequest($request);

        if(empty($route)) {
            return new Response('Not found', 404, ['Content-Type' => 'text/plain']);
        }

        if($this->checkMethod($request, $route) === false) {
            return new Response('Method not allowed', 405, ['Content-Type' => 'text/plain']);
        }

        $controller = 'App\\Controllers\\' . $route->controller;
        $controller = new $controller();
        return $controller->process($request);
    }

    private function getRouteFromRequest(Request $request): ?object{
        $routes = self::getConfig();
        foreach ($routes as $route) {
            if (self::urlMatches($request, $route)) {
                return $route;
            }
        }
        return null;
    }

    private static function urlMatches(Request $request, object $route): bool {
        $requestUriParts = self::getUrlParts($request->getPath());
        $routePathParts = self::getUrlParts($route->path);

        if(self::checkUrlPartsNumberMatches($requestUriParts, $routePathParts) === false) {
            return false;
        }

        foreach($routePathParts as $key => $part) {
            if(self::isUrlPartSlug($part) === false) {
                if($part !== $requestUriParts[$key]) {
                    return false;
                }
            }else{
                $request->addSlug(substr($part, 1), $requestUriParts[$key]);
            }
        }

        return true;
    }

    private static function getUrlParts(string $url): array {
        return explode('/', trim($url, '/'));
    }

    private static function checkUrlPartsNumberMatches(array $requestUriParts, array $routePathParts): bool {
        return count($requestUriParts) === count($routePathParts);
    }

    private static function isUrlPartSlug(string $part): bool {
        return strpos($part, ':') === 0;
    }

    private static function getConfig(): array {
        $config = json_decode(file_get_contents(__DIR__ . '/../../config/routes.json'));
        return $config;
    }

    private static function checkMethod(Request $request, object $route): bool {
        return in_array($request->getMethod(), $route->methods);
    }
}