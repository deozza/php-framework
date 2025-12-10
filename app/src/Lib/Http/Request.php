<?php

namespace App\Lib\Http;

class Request {
    private string $uri;
    private string $method;
    private array $headers;
    private string $body;
    private string $path;
    private array $routeParams = [];

    public function __construct() {
        $this->uri = $_SERVER['REQUEST_URI'];
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->headers = getallheaders();
        $this->body = file_get_contents('php://input') ?: '';
        $this->path = parse_url($this->uri, PHP_URL_PATH) ?? $this->uri;
    }

    public function getUri(): string {
        return $this->uri;
    }

    public function getPath(): string {
        return $this->path;
    }

    public function getBody(): string {
        return $this->body;
    }

    public function setRouteParams(array $params): void {
        $filtered = [];
        foreach ($params as $k => $v) {
            if (!is_int($k)) {
                $filtered[$k] = $v;
            }
        }
        $this->routeParams = $filtered;
    }

    public function getRouteParams(): array {
        return $this->routeParams;
    }

    public function getMethod(): string {
        return $this->method;
    }

    public function getHeaders(): array {
        return $this->headers;
    }
}
