<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;

class ContactController extends AbstractController {

    public function process(Request $request): Response {
        if ($request->getMethod() === 'POST') {
            return $this->handlePost($request);
        }

        return new Response('Contact Controller');
    }

    private function handlePost(Request $request): Response {
        $contentType = $request->getHeader('Content-Type');

        $body = json_decode($request->getBody(), true);

        $timestamp = time();
        $filename = "{$timestamp}_{$body['email']}.json";
        $filepath = __DIR__ . "/../../var/contacts/" . $filename;


        $data = [
            "email" => $body['email'],
            "subject" => $body['subject'],
            "message" => $body['message'],
            "dateOfCreation" => $timestamp,
            "dateOfLastUpdate" => $timestamp
        ];

        file_put_contents($filepath, json_encode($data));

        return new Response(json_encode(["file" => $filename]), 201);
    }
}
