<?php

namespace App\Http;

class Request {
    private string $uri;
    private string $method;
    private array $headers;
    private string $body;

    public function __construct() {
        $this->uri = $_SERVER['REQUEST_URI'];
        $this->method = $_SERVER['REQUEST_METHOD'];

        $this->headers = getallheaders();

        $this->body = file_get_contents('php://input');
    }

    public function getUri(): string {
        return $this->uri;
    }

    public function getMethod(): string {
        return $this->method;
    }

    public function getHeaders(): array {
        return $this->headers;
    }

    public function getHeader(string $name): ?string {
        $name = strtolower($name);
        foreach ($this->headers as $key => $value) {
            if (strtolower($key) === $name) {
                return $value;
            }
        }
        return null;
    }

    public function getBody(): string {
        return $this->body;
    }
}
