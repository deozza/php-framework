<?php

namespace App\Controllers;

use App\Lib\Controllers\AbstractController;
use App\Lib\Http\Request;
use App\Lib\Http\Response;

class ContactController extends AbstractController
{
    private const FIELDS = ['email', 'subject', 'message'];
    private const STORAGE_PATH = __DIR__ . '/../../var/contacts';

    public function process(Request $request): Response
    {
        if ($request->getMethod() === 'POST' && !$this->isJsonContent($request)) {
            return new Response(
                '{"error": "Content-Type must be application/json"}',
                400,
                ['Content-Type' => 'application/json']
            );
        }

        $body = file_get_contents('php://input');
        $data = json_decode($body, true);

        if ($data === null) {
            return new Response(
                '{"error": "Invalid JSON"}',
                400,
                ['Content-Type' => 'application/json']
            );
        }

        $fields = array_keys($data);
        $invalid = array_diff($fields, self::FIELDS);

        if (!empty($invalid)) {
            return new Response(
                '{"error": "Invalid fields"}',
                400,
                ['Content-Type' => 'application/json']
            );
        }

        $missing = array_diff(self::FIELDS, $fields);
        if (!empty($missing)) {
            return new Response(
                '{"error": "Missing fields"}',
                400,
                ['Content-Type' => 'application/json']
            );
        }

        $timestamp = time();
        $email = $data['email'];
        $filename = $timestamp . '_' . $email . '.json';
        $filepath = self::STORAGE_PATH . '/' . $filename;

        $contact = [
            'email' => $data['email'],
            'subject' => $data['subject'],
            'message' => $data['message'],
            'dateOfCreation' => $timestamp,
            'dateOfLastUpdate' => $timestamp
        ];

        file_put_contents($filepath, json_encode($contact, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        $dateFormatted = date('Y-m-d_H-i-s', $timestamp);
        $responseFile = $dateFormatted . '_' . $email . '.json';

        return new Response(
            json_encode(['file' => $responseFile], JSON_UNESCAPED_SLASHES),
            201,
            ['Content-Type' => 'application/json']
        );
    }

    private function isJsonContent(Request $request): bool
    {
        $headers = $request->getHeaders();
        $contentType = $headers['Content-Type'] ?? '';
        return strpos($contentType, 'application/json') !== false;
    }

    public function getAllContacts(Request $request): Response
    {
        $list = [];
        foreach (glob(self::STORAGE_PATH . '/*.json') as $file) {
            $data = json_decode(file_get_contents($file), true);
            if (is_array($data)) {
                $list[] = $data;
            }
        }
        return new Response(
            json_encode($list, JSON_UNESCAPED_SLASHES),
            200,
            ['Content-Type' => 'application/json']
        );
    }

    public function getContact(Request $request, $params): Response
    {
        $filepath = self::STORAGE_PATH . '/' . $params;
        if (!file_exists($filepath)) {
            return new Response(
                '{"error": "Contact not found"}',
                404,
                ['Content-Type' => 'application/json']
            );
        }
        $data = json_decode(file_get_contents($filepath), true);
        return new Response(
            json_encode($data, JSON_UNESCAPED_SLASHES),
            200,
            ['Content-Type' => 'application/json']
        );
    }

    public function update(Request $request, $params): Response
    {
        $filepath = self::STORAGE_PATH . '/' . $params;
        
        if (!file_exists($filepath)) {
            return new Response(
                '{"error": "Contact not found"}',
                404,
                ['Content-Type' => 'application/json']
            );
        }

        if (!$this->isJsonContent($request)) {
            return new Response(
                '{"error": "Content-Type must be application/json"}',
                400,
                ['Content-Type' => 'application/json']
            );
        }

        $body = file_get_contents('php://input');
        $data = json_decode($body, true);

        if ($data === null || empty($data)) {
            return new Response(
                '{"error": "Invalid JSON"}',
                400,
                ['Content-Type' => 'application/json']
            );
        }

        $fields = array_keys($data);
        $invalid = array_diff($fields, self::FIELDS);
        
        if (!empty($invalid)) {
            return new Response(
                '{"error": "Invalid fields"}',
                400,
                ['Content-Type' => 'application/json']
            );
        }

        $contact = json_decode(file_get_contents($filepath), true);
        
        foreach ($data as $key => $value) {
            $contact[$key] = $value;
        }
        
        $contact['dateOfLastUpdate'] = time();

        file_put_contents($filepath, json_encode($contact, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        return new Response(
            json_encode($contact, JSON_UNESCAPED_SLASHES),
            200,
            ['Content-Type' => 'application/json']
        );
    }

    public function delete(Request $request, $params): Response
    {
        $filepath = self::STORAGE_PATH . '/' . $params;
        
        if (!file_exists($filepath)) {
            return new Response(
                '{"error": "Contact not found"}',
                404,
                ['Content-Type' => 'application/json']
            );
        }

        unlink($filepath);

        return new Response(
            '',
            204,
            ['Content-Type' => 'application/json']
        );
    }
}