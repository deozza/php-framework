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
    public function getBody(): array {
        // Check if the Content-Type header is JSON
        if (isset($this->headers['Content-Type']) && strpos($this->headers['Content-Type'], 'application/json') !== false) {
            // Decode JSON from the raw input
            $body = json_decode(file_get_contents('php://input'), true);

            // Return the decoded body or an empty array if decoding fails
            return is_array($body) ? $body : [];
        }

        // For non-JSON bodies (e.g., form data), return $_POST
        return $_POST;
    }
}