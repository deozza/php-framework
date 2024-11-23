<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;

class ContactController extends AbstractController {

    public function process(Request $request): Response {
        $uriFile = explode('/', trim($request->getUri(), '/'));
    
        
        if ($uriFile[0] === 'contact') {
            if ($request->getMethod() === 'POST') {
                return $this->PostContact($request);
            }
    
            if ($request->getMethod() === 'GET') {
                if (isset($uriFile[1])) {  
                    return $this->getContactByFilename($request, $uriFile[1]);
                }
                return $this->getContacts($request);
            }
            if ($request->getMethod() === 'PATCH') {
                return $this->updateContact($request);
            }
            if ($request->getMethod() === 'DELETE') {
                return $this->deleteContact($request);
            }
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

    public function getContactByFilename(Request $request, string $filename): Response {
        $ContactPath = __DIR__ . '/../../var/contacts/' . $filename . '.json';

        if (!file_exists($ContactPath)) {
            return new Response('File not found', 404);
        }

        $contactData = json_decode(file_get_contents($ContactPath), true);
        return new Response(json_encode($contactData, JSON_PRETTY_PRINT), 200, ['Content-Type' => 'application/json']);
    }

    public function updateContact(Request $request): Response {
       
        $uriFile = explode('/', trim($request->getUri(), '/'));
        $filename = $uriFile[1];

        if (!$filename){
            return new Response('File not found',404);
        }

        $filepath = __DIR__ . '/../../var/contacts/' . $filename . '.json';

        if (!file_exists($filepath)) {
            return new Response('File not found', 404);
        }

        $body = json_decode(file_get_contents('php://input'), true);

        $requiredBody = ['email', 'subject', 'message'];
        $invalidBody = array_diff($requiredBody, array_keys($body));

        if ($invalidBody){
            return new Response(json_encode(['error' => 'Invalid request body'. implode(', ', $invalidBody)]), 400, ['Content-Type' => 'application/json']);
        }

        $contactData = json_decode(file_get_contents($filepath), true);

        foreach ($body as $key => $value) {
            $contactData[$key] = $value;
        }

        $contactData['dateOfLastUpdate'] = time();

        file_put_contents($filepath, json_encode($contactData, JSON_PRETTY_PRINT));

        return new Response(json_encode($contactData, JSON_PRETTY_PRINT), 200, ['Content-Type' => 'application/json']);
    }

    public function deleteContact(Request $request): Response {
        $uriFile = explode('/', trim($request->getUri(),'/'));
        $filename = $uriFile[1] ?? null;

        if (!$filename){
            return new Response('File not found',404);
        }

        $filepath = __DIR__ . '/../../var/contacts/' . $filename . '.json';

        if (!file_exists($filepath)) {
            return new Response('File not found', 404);
        }

        if (!unlink($filepath)) {
            return new Response('Failed to delete file', 500);
        }

        return new Response('File deleted successfully', 200);
    }
        
    
}