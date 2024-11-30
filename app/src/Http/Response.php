<?php

namespace App\Http;

class Response
{
    private string $content;
    private int $status;

    public function __construct(string $content = '', int $status = 200)
    {
        $this->content = $content;
        $this->status = $status;
        $this->send();
    }

    public function send()
    {
        http_response_code($this->status);
        header("Content-Type: application/json");
        echo $this->content;
        exit();
    }

    public static function jsonResponse(array $data, int $status = 200): self
    {
        return new self(json_encode($data), $status);
    }
}
