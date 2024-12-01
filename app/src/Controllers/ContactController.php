<?php

namespace App\Controllers;

use App\Contact;
use App\Http\Request;
use App\Http\Response;

class ContactController
{
    public function PostSaveContact(Request $request): Response
    {
        if ($request->getHeader('Content-Type') !== 'application/json') {
            return new Response(json_encode(['error' => 'Invalid content type']), 400, ['Content-Type' => 'application/json']);
        }

        $data = json_decode($request->getBody(), true);

        $requiredFields = ['email', 'subject', 'message'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                return new Response(json_encode(['error' => "Missing field: $field"]), 400, ['Content-Type' => 'application/json']);
            }
        }

        $contact = new Contact($data['email'], $data['subject'], $data['message']);
        $filename = $contact->save();

        return new Response(json_encode(['file' => $filename]), 201, ['Content-Type' => 'application/json']);
    }

    public function getAllContacts(Request $request): Response
    {
        if ($request->getHeader('Content-Type') !== 'application/json') {
            return new Response(json_encode(['error' => 'Invalid content type']), 400, ['Content-Type' => 'application/json']);
        }

        $directory = __DIR__ . '/../../var/contacts';
        $files = array_filter(scandir($directory), fn($file) => $file !== '.' && $file !== '..');
        $contacts = [];

        foreach ($files as $file) {
            $content = json_decode(file_get_contents($directory . '/' . $file), true);
            if ($content) {
                $contacts[] = $content;
            }
        }

        return new Response(json_encode($contacts), 200, ['Content-Type' => 'application/json']);
    }

    public function getContact(Request $request, string $filename): Response
    {
        if ($request->getHeader('Content-Type') !== 'application/json') {
            return new Response(json_encode(['error' => 'Invalid content type']), 400, ['Content-Type' => 'application/json']);
        }

        $filePath = __DIR__ . '/../../var/contacts/' . $filename;

        if (!file_exists($filePath)) {
            return new Response(json_encode(['error' => 'Contact not found']), 404, ['Content-Type' => 'application/json']);
        }

        $content = json_decode(file_get_contents($filePath), true);
        return new Response(json_encode($content), 200, ['Content-Type' => 'application/json']);
    }

    public function updateContact(Request $request, string $filename): Response
    {
        if ($request->getHeader('Content-Type') !== 'application/json') {
            return new Response(json_encode(['error' => 'Invalid content type']), 400, ['Content-Type' => 'application/json']);
        }

        $filePath = __DIR__ . '/../../var/contacts/' . $filename;

        if (!file_exists($filePath)) {
            return new Response(json_encode(['error' => 'Contact not found']), 404, ['Content-Type' => 'application/json']);
        }

        $contact = json_decode(file_get_contents($filePath), true);
        $data = json_decode($request->getBody(), true);

        $validFields = ['email', 'subject', 'message'];
        foreach ($data as $key => $value) {
            if (in_array($key, $validFields)) {
                $contact[$key] = $value;
            }
        }

        $contact['lastUpdatedAt'] = time();
        file_put_contents($filePath, json_encode($contact));

        return new Response(json_encode($contact), 200, ['Content-Type' => 'application/json']);
    }

    public function deleteContact(Request $request, string $filename): Response
    {
        if ($request->getHeader('Content-Type') !== 'application/json') {
            return new Response(json_encode(['error' => 'Invalid content type']), 400, ['Content-Type' => 'application/json']);
        }

        $filePath = __DIR__ . '/../../var/contacts/' . $filename;

        if (!file_exists($filePath)) {
            return new Response(json_encode(['error' => 'Contact not found']), 404, ['Content-Type' => 'application/json']);
        }

        unlink($filePath);
        return new Response(null, 204);
    }
}
