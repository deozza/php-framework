<?php

namespace App\Controllers;

use App\Lib\Controllers\AbstractController;
use App\Lib\Http\Request;
use App\Lib\Http\Response;

class PatchController extends AbstractController
{
    public function process(Request $request): Response
    {
        if ($request->getMethod() !== 'PATCH') {
            return new Response(
                json_encode(["error" => "Method not allowed"]),
                405,
                ["Content-Type" => "application/json"]
            );
        }

        $uri = $request->getUri(); 

        $path = parse_url($uri, PHP_URL_PATH) ?? '/';
        
        $parts = explode('/contact/', $path);

        if (!isset($parts[1]) || $parts[1] === '') {
            return new Response(
                json_encode(["error" => "No filename provided"]),
                400,
                ["Content-Type" => "application/json"]
            );
        }

        $filename = $parts[1]; 

        $safeFilename = basename($filename);

        if (!str_ends_with($safeFilename, '.json')) {
            $safeFilename .= '.json';
        }

        $path = __DIR__ . "/../../var/contacts/" . $safeFilename;

        if (!file_exists($path)) {
            return new Response(
                json_encode(["error" => "Contact not found"]),
                404,
                ["Content-Type" => "application/json"]
            );
        }

        $headers = method_exists($request, 'getHeaders') ? $request->getHeaders() : [];
        $contentType = $headers['Content-Type'] ?? $headers['content-type'] ?? '';

        if (stripos($contentType, 'application/json') === false) {
            return new Response(
                json_encode(["error" => "Invalid Content-Type, expected application/json"]),
                400,
                ["Content-Type" => "application/json"]
            );
        }

       
        $rawBody = $request->getBody();
        $body = json_decode($rawBody, true);

        if (!is_array($body)) {
            return new Response(
                json_encode(["error" => "Invalid JSON body"]),
                400,
                ["Content-Type" => "application/json"]
            );
        }

        $allowedFields = ['email', 'subject', 'message'];

        if (empty($body)) {
            return new Response(
                json_encode(["error" => "No fields to update"]),
                400,
                ["Content-Type" => "application/json"]
            );
        }

        $invalidFields = array_diff(array_keys($body), $allowedFields);
        if (!empty($invalidFields)) {
            return new Response(
                json_encode(["error" => "Invalid fields: " . implode(', ', $invalidFields)]),
                400,
                ["Content-Type" => "application/json"]
            );
        }

        $content = file_get_contents($path);
        $current = json_decode($content, true);

        if (!is_array($current)) {
            return new Response(
                json_encode(["error" => "Invalid contact file"]),
                500,
                ["Content-Type" => "application/json"]
            );
        }

        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $body)) {
                $current[$field] = $body[$field];
            }
        }

        $current['dateOfLastUpdate'] = time();

        
        file_put_contents($path, json_encode($current, JSON_PRETTY_PRINT));

       
        return new Response(
            json_encode($current),
            200,
            ["Content-Type" => "application/json"]
        );
    }
}
