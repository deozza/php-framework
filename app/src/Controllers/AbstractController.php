<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;

abstract class AbstractController{
    abstract public function process(Request $request): Response;

    protected function jsonResponse(array $data, int $status = 200): Response
    {
        return new Response(json_encode($data), $status, ['Content-Type' => 'application/json']);
    }

    protected function jsonValidation(Request $request): bool
    {
        return $request->getHeaders()['Content-Type'] === 'application/json';
    }
}