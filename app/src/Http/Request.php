<?php

namespace App\Http;

class Request {
    private string $uri;
    private string $method;
    private array $headers;
    private ?string $body = null; 
    private array $attributes = []; 

    public function __construct() {
        $this->uri = $_SERVER['REQUEST_URI'];
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->headers = getallheaders();
        
        if ($this->method === 'POST' || $this->method === 'PATCH') {
            $this->body = file_get_contents('php://input');
        }
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

    public function getBody(): ?string {
        return $this->body;
    }

    public function getAttributes(): array {
        return $this->attributes;
    }

    public function setAttributes(array $attributes): void {
        $this->attributes = $attributes;
    }
}
