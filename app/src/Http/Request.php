<?php

namespace App\Http;

class Request {
    private string $uri;
    private string $method;
    private array $headers;


    public function __construct() {
        $this->uri = $_SERVER['REQUEST_URI'];
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->headers = getallheaders();
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

    public function getContent(): array {
        $content = file_get_contents('php://input');
        $decodedContent = json_decode($content, true);
        return $decodedContent ?? [];
    }

}