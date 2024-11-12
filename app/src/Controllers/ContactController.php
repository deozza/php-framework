<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;

class ContactController extends AbstractController {

    public function process(Request $request): Response {
        if ($request->getMethod() === 'POST') {
            return $this->create($request);
        }
        if ($request->getMethod() === 'GET') {
            return $this->fetchAll();
        }
        return new Response('Method Not Allowed', 405);
    }

    public function create(Request $request): Response {

        $allow_body = ['email', 'subject', 'message'];
        $body = $request->getContent();
        
        if (!is_array($body) || !isset($body['email'], $body['subject'], $body['message'])) {
            return new Response(
                json_encode(["error" => "Invalid JSON"]),
                400,
                ['Content-Type' => 'application/json']
            );
        }

        foreach($body as $fields => $value) {
            if (!in_array($fields, $allow_body)){
                return new Response(
                    json_encode(["error" => "Invalid JSON"]),
                    400,
                    ['Content-Type' => 'application/json']
                );
            }
        }


        $timestamp = time();
        $email = $body['email'];
        $emailWithoutExtension = preg_replace('/\.[a-z]+$/i', '', $email);
        
        $content = [
            'email' => $body['email'],
            'subject' => $body['subject'],
            'message' => $body['message'],
            'dateOfCreation' => $timestamp,
            'dateOfLastUpdate' => $timestamp,
        ];
        
        $jsonData = json_encode($content, JSON_PRETTY_PRINT);

        
        $filename = sprintf('%d_%s.json', $timestamp, $emailWithoutExtension);
        $filePath = __DIR__ . '/../../var/contacts/' . $filename;
        
        if (file_put_contents($filePath, $jsonData) !== false) {
            return new Response(
                json_encode(["file" => $filename]),
                201,
                ['Content-Type' => 'application/json']
            );
        } else {
            return new Response(
                json_encode(["error" => "Invalid JSON."]),
                500,
                ['Content-Type' => 'application/json']
            );
        }
    }

    public function fetchAll(): Response {
        $directory = __DIR__ . '/../../var/contacts/';
        $files = glob($directory . '*.json');
        
        $contacts = [];
        foreach ($files as $file) {
            $contacts[] = json_decode(file_get_contents($file), true);
        }

        return new Response(
            json_encode($contacts),
            200,
            ['Content-Type' => 'application/json']
        );
    }

}