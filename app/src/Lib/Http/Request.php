<?php

namespace App\Lib\Http;

class Request {
    private string $uri;
    private string $method;
    private array $headers;
    private string $payload;

    public function __construct() {
        $this->uri = $_SERVER['REQUEST_URI'];
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->headers = getallheaders();
        $this->payload = file_get_contents('php://input');
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

    public function getPayload(): string {
        return $this->payload;
    }

    public function getHeader(string $key): string {
        if($this->hasHeader($key) === false) {
            throw new \Exception("header $key not found");
        }

        return $this->headers[$key];
    }

    public function hasHeader(string $key): bool {
        return array_key_exists($key, $this->headers);
    }

    public function headerHasValue(string $key, string $value): bool {
        $header = $this->getHeader($key);

        return $header === $value;
    }
}
