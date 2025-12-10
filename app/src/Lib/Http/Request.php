<?php

namespace App\Lib\Http;

class Request {
    private string $uri;
    private string $method;
    private array $headers;
    private array $params = [];

    public function __construct() {
        $this->uri = $_SERVER['REQUEST_URI'];
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->headers = getallheaders();
        foreach ($this->headers as $k => $v) {
            if (strtolower($k) === 'x-http-method-override' && !empty($v)) {
                $this->method = strtoupper($v);
                break;
            }
        }
        if (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']) && !empty($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
            $this->method = strtoupper($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']);
        }
        if (isset($_REQUEST['_method']) && !empty($_REQUEST['_method'])) {
            $this->method = strtoupper($_REQUEST['_method']);
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

    public function setParams(array $params): void {
        $this->params = $params;
    }

    public function getParams(): array {
        return $this->params;
    }
}
