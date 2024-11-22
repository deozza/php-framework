<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;

class ContactController extends AbstractController {

    public function process(Request $request): Response{
        if ($request->getMethod() === 'POST') {
            return $this->PostContact($request);
        }
        if ($request->getMethod() === 'GET') {
            return $this->getContacts($request);
        }
        return new Response('Method not allowed', 405);
    }
    public function PostContact(Request $request): Response
    {
        if ($request-> getMethod() !== 'POST') {
            return new Response('Method not allowed', 405);
        }
    

        $body = json_decode(file_get_contents('php://input'),true);
    
        $requiredBody = ['email', 'subject', 'message'];

        if (!$body || array_diff($requiredBody, array_keys($body)) || count($body) !== count($requiredBody)) {
            return new Response(
            json_encode(['error' => 'Invalid request body']),
            400,
            ['Content-Type' => 'application/json']
            );
        }

        $timestamp = time();
        $filename = sprintf('%s_%s.json', $timestamp, $body['email']);
        $storagePath = realpath(__DIR__ . '/../../var/contacts') ?: __DIR__ . '/../../var/contacts';
        $filePath = sprintf('%s/%s', $storagePath, $filename);

        $data = [
            'email' => $body['email'],
            'subject' => $body['subject'],
            'message' => $body['message'],
            'dateOfCreation' => $timestamp,
            'dateOfLastUpdate' => $timestamp,
        ];

        if (!is_dir($storagePath)) {
            mkdir($storagePath, 0777, true);
        }

        file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT));

        return new Response(
        json_encode(['file' => $filename]),
        201,
        ['Content-Type' => 'application/json']
        );
    }
public function getContacts(Request $request): Response {
    if ($request->getMethod() !== 'GET') {
        return new Response('Method not allowed', 405);
    }

    $ContactPath = __DIR__ . '/../../var/contacts';
    $contacts = [];

    foreach (scandir($ContactPath) as $file) {
        if (pathinfo($file, PATHINFO_EXTENSION) === 'json') {
            $filePath = $ContactPath . '/' . $file;
            $contact = json_decode(file_get_contents($filePath), true);
            if ($contact) {
                $contacts[] = $contact;
         }
        }
    }
    return new Response(json_encode($contacts, JSON_PRETTY_PRINT), 200, ['Content-Type' => 'application/json']);
}
}