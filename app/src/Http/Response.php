<?php

namespace App\Http;

class Response {
    private string $content;
    private int $status;
    private array $headers;

    public function __construct(string $content = '', int $status = 200, array $headers = []) {
        $this->content = $content;
        $this->status = $status;
        //now returns application/json by default (je viens de me rendre compte que c'est pas la meilleure des idées de faire ça mais bon)
        $this->headers = array_merge(['Content-Type' => 'application/json'], $headers);
    }

    public function setStatus(int $status): self {
        $this->status = $status;
        return $this;
    }

    public function getStatus(): int {
        return $this->status;
    }

    public function setHeaders(array $headers): self {
        $this->headers = array_merge(['Content-Type' => 'application/json'], $headers);
        return $this;
    }

    public function addHeader(string $header): self {
        $this->headers[] = $header;
        return $this;
    }

    public function getHeaders(): array {
        return $this->headers;
    }

    public function setContent(string $content): self {
        $this->content = $content;
        return $this;
    }

    public function getContent(): string {
        return $this->content;
    }

    //function to return the status code (jvais pleurer)
    public function send(): void {
        http_response_code($this->status);
        foreach ($this->headers as $header => $value) {
            header("{$header}: {$value}");
        }
        echo $this->content;
    }
}