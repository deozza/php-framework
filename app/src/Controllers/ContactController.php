<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use DirectoryIterator;

class ContactController extends AbstractController {

    public function process(Request $request): Response {
        if ($request->getMethod() === 'POST') {
            return $this->handlePost($request);
        }

        if ($request->getMethod() === 'GET') {
            return $this->handleGet();
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

        return new Response(json_encode(["file" => $filename]), 201);
    }

    private function handleGet(): Response {
        $contacts = [];
        $directory = __DIR__ . '/../../var/contacts';

        foreach (new DirectoryIterator($directory) as $fileInfo) {
            if (!$fileInfo->isDot() && $fileInfo->isFile()) {
                $contactData = json_decode(file_get_contents($fileInfo->getPathname()), true);
                $contacts[] = $contactData;
            }
        }

        return new Response(json_encode($contacts), 200);
    }
}