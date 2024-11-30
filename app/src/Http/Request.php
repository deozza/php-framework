<?php

namespace App\Http;

class Request
{
    private string $uri;
    private string $method;
    private array $headers;

    public function __construct()
    {
        $this->uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->headers = getallheaders();
    }

    public function getUri(): string
    {
        // Récupérer l'URI et nettoyer les caractères parasites
        $uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
        $uri = str_replace(["\n", "\r", "%0A", "%0D"], '', $uri);
        $cleanedUri = rtrim($uri, "/");

        // Débogage pour vérifier ce qui est reçu
        var_dump("Raw URI: " . $_SERVER['REQUEST_URI']);
        var_dump("Cleaned URI: " . $cleanedUri);

        return $cleanedUri;
    }



    public function getMethod(): string
    {
        return $this->method;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getBody(): string
    {
        $body = file_get_contents('php://input');
        json_decode($body);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Invalid JSON body: ' . json_last_error_msg());
        }

        return $body;
    }
}
