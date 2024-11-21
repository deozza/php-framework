<?php

namespace App\Http;

class Request {
    private string $uri;
    private string $method;
    private array $headers;
    private string $body;

    public function __construct() {
        // URI et méthode HTTP
        $this->uri = $_SERVER['REQUEST_URI'];
        $this->method = $_SERVER['REQUEST_METHOD'];

        // Récupère les en-têtes HTTP
        $this->headers = getallheaders();

        // Récupère le corps brut de la requête (utile pour POST/PUT)
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
        // Recherche d'un en-tête insensible à la casse
        $name = strtolower($name);
        foreach ($this->headers as $key => $value) {
            if (strtolower($key) === $name) {
                return $value;
            }
        }
        return null; // Retourne null si l'en-tête n'existe pas
    }

    public function getBody(): string {
        return $this->body;
    }
}
