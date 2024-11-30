<?php

namespace App\Http;

class Request {
    private string $uri;
    private string $method;
    private array $headers;
    protected array $args;

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

    public function getBody(): array {
        return json_decode(file_get_contents('php://input'),true);
    }

    public function getArgs(string $args): string
    {
        if(isset($this->args[$args])) {
            return $this->args[$args];
        }
        return '';
    }
    public function setArgs(array $args): void {
        $this->args = $args;
    }
}