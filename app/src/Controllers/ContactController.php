<?php

namespace App\Controllers;

use App\Lib\Controllers\AbstractController;
use App\Lib\Http\Request;
use App\Lib\Http\Response;

class ContactController extends AbstractController
{
    public function process(Request $request): Response
    {
        $headers = $request->getHeaders();
        $contentType = $headers['Content-Type'] ?? ($headers['content-type'] ?? null);

        if ($contentType === null || stripos($contentType, 'application/json') !== 0) {
            return new Response(
                json_encode(['error' => 'Only application/json is accepted']),
                400, 
                ['Content-Type' => 'application/json']
            );
        }

        $rawBody = file_get_contents('php://input');
        $data = json_decode($rawBody, true);

        if (!is_array($data)) {
            return new Response(
                json_encode(['error' => 'Invalid JSON body']),
                400,
                ['Content-Type' => 'application/json']
            );
        }

        $allowedKeys = ['email', 'subject', 'message'];

        foreach ($data as $key => $value) {
            if (!in_array($key, $allowedKeys, true)) {
                return new Response(
                    json_encode(['error' => "Unexpected property: $key"]),
                    400,
                    ['Content-Type' => 'application/json']
                );
            }
        }
        foreach ($allowedKeys as $requiredKey) {
            if (!array_key_exists($requiredKey, $data)) {
                return new Response(
                    json_encode(['error' => "Missing property: $requiredKey"]),
                    400,
                    ['Content-Type' => 'application/json']
                );
            }
        }
        $timestamp = time();
        $email = $data['email'];
        $fileName = $timestamp . '_' . $email . '.json';
        $contactArray = [
            'email'            => $data['email'],
            'subject'          => $data['subject'],
            'message'          => $data['message'],
            'dateOfCreation'   => $timestamp,
            'dateOfLastUpdate' => $timestamp,
        ];
        $dir = __DIR__ . '/../../var/contacts/';

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        file_put_contents(
            $dir . $fileName,
            json_encode(
                $contactArray,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            )
        );

        $prettyName = date('Y-m-d_H-i-s', $timestamp) . '_' . $email . '.json';

        return new Response(
            json_encode(['file' => $prettyName]),
            201,
            ['Content-Type' => 'application/json']
        );
    }
}
