<?php

namespace App\Http;

class Request
{
    private string $uri;
    private string $method;
    private array $headers;
    // Ajout de payload 
    private string $payload;
    private string $email;

    public function __construct()
    {
        $this->uri = $_SERVER['REQUEST_URI'];
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->headers = getallheaders();
        $this->payload = file_get_contents('php://input');
        $uriParts = explode('/', trim($this->uri, '/'));
        $this->email = end($uriParts);
    }

    // Création de la fonction getEmail() pour récupérer l'email rentré dans l'url
    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPayload(): string
    {
        return $this->payload;
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
}
