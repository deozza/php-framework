<?php

namespace App\Lib\Http;

class Request
{
    private string $uri;
    private string $method;
    private array $headers;
    private ?string $body = null;

    public function __construct()
    {
        $this->uri = $_SERVER['REQUEST_URI'];
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->headers = getallheaders();

        if ($this->method === 'POST' || $this->method === 'PUT' || $this->method === 'PATCH') {
            $this->body = file_get_contents('php://input');
        }
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getBody(): ?string
    {

        return $this->body;
    }

    public static function isJson(?string $string): bool
    {
        if (is_null($string)) {
            return false;
        }
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    public function checkMethod(string $method): bool
    {
        return $this->method === $method;
    }

    public function getPath(): string
    {
        return parse_url($this->uri, PHP_URL_PATH) ?? '/';
    }

    public function getPathSegments(): array
    {
        return explode('/', trim($this->getPath(), '/'));
    }
}
