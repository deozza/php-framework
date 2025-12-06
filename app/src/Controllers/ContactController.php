<?php

namespace App\Controllers;

use App\Lib\Http\Request;
use App\Lib\Http\Response;
use App\Lib\Controllers\AbstractController;

class ContactController extends AbstractController
{
    public function process(Request $request): Response
    {
        if ($request->getMethod() === 'POST') {
            return $this->createContact($request);
        } elseif ($request->getMethod() === 'GET') {
            $id = $request->getParam('id');
            if ($id !== null) {
                return $this->getContactById($request, $id);
            }
            return $this->getAllContacts($request);
        } elseif ($request->getMethod() === 'PATCH') {
            $id = $request->getParam('id');
            if ($id !== null) {
                return $this->updateContact($request, $id);
            }

            return new Response(
                json_encode(['error' => 'ID required for PATCH']),
                400,
                ['Content-Type' => 'application/json']
            );
        } elseif ($request->getMethod() === 'DELETE') {
            $id = $request->getParam('id');
            if ($id !== null) {
                return $this->deleteContact($request, $id);
            }

            return new Response(
                json_encode(['error' => 'ID required for DELETE']),
                400,
                ['Content-Type' => 'application/json']
            );
        }

        return new Response(
            json_encode(['error' => 'Method not allowed']),
            405,
            ['Content-Type' => 'application/json']
        );
    }


    private function createContact(Request $request): Response
    {
        $contentType = $request->getHeader('Content-Type');
        if ($contentType !== 'application/json') {
            return new Response(
                json_encode(['error' => 'Content-Type must be application/json']),
                400,
                ['Content-Type' => 'application/json']
            );
        }

        $body = $request->getBody();
        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return new Response(
                json_encode(['error' => 'Invalid JSON']),
                400,
                ['Content-Type' => 'application/json']
            );
        }

        $allowedKeys = ['email', 'subject', 'message'];
        $dataKeys = array_keys($data);

        $extraKeys = array_diff($dataKeys, $allowedKeys);
        if (!empty($extraKeys)) {
            return new Response(
                json_encode(['error' => 'Invalid properties: ' . implode(', ', $extraKeys)]),
                400,
                ['Content-Type' => 'application/json']
            );
        }

        $missingKeys = array_diff($allowedKeys, $dataKeys);
        if (!empty($missingKeys)) {
            return new Response(
                json_encode(['error' => 'Missing properties: ' . implode(', ', $missingKeys)]),
                400,
                ['Content-Type' => 'application/json']
            );
        }

        $contactsDir = __DIR__ . '/../../var/contacts';
        if (!is_dir($contactsDir)) {
            mkdir($contactsDir, 0777, true);
        }

        $timestamp = time();
        $email = $data['email'];
        $fileName = $timestamp . '_' . $email . '.json';
        $filePath = $contactsDir . '/' . $fileName;

        $fileContent = [
            'email' => $data['email'],
            'subject' => $data['subject'],
            'message' => $data['message'],
            'dateOfCreation' => $timestamp,
            'dateOfLastUpdate' => $timestamp
        ];

        file_put_contents($filePath, json_encode($fileContent, JSON_PRETTY_PRINT));

        return new Response(
            json_encode(['file' => $fileName]),
            201,
            ['Content-Type' => 'application/json']
        );
    }

    private function getAllContacts(Request $request): Response
    {
        $contactsDir = __DIR__ . '/../../var/contacts';
        if (!is_dir($contactsDir)) {
            return new Response(
                json_encode([]),
                200,
                ['Content-Type' => 'application/json']
            );
        }

        $files = scandir($contactsDir);
        $contacts = [];

        foreach ($files as $file) {
            if ($file === '.' || $file === '..' || !str_ends_with($file, '.json')) {
                continue;
            }

            $filePath = $contactsDir . '/' . $file;
            $fileContent = file_get_contents($filePath);
            $contactData = json_decode($fileContent, true);

            if ($contactData !== null) {
                $contacts[] = $contactData;
            }
        }

        return new Response(
            json_encode($contacts),
            200,
            ['Content-Type' => 'application/json']
        );
    }

    private function getContactById(Request $request, string $id): Response
    {

        $contactsDir = __DIR__ . '/../../var/contacts';
        $filePath = null;

        if (str_ends_with($id, '.json')) {
            $filePath = $contactsDir . '/' . $id;
        } else {
            $files = scandir($contactsDir);
            foreach ($files as $file) {
                if ($file === '.' || $file === '..') {
                    continue;
                }

                if (strpos($file, $id) !== false) {
                    $filePath = $contactsDir . '/' . $file;
                    break;
                }
            }
        }

        if ($filePath === null || !file_exists($filePath)) {
            return new Response(
                json_encode(['error' => 'Contact not found']),
                404,
                ['Content-Type' => 'application/json']
            );
        }

        $fileContent = file_get_contents($filePath);
        $contactData = json_decode($fileContent, true);

        if ($contactData === null) {
            return new Response(
                json_encode(['error' => 'Error reading contact data']),
                500,
                ['Content-Type' => 'application/json']
            );
        }

        return new Response(
            json_encode($contactData),
            200,
            ['Content-Type' => 'application/json']
        );
    }

    private function updateContact(Request $request, string $id): Response
    {
        $contentType = $request->getHeader('Content-Type');
        if ($contentType !== 'application/json') {
            return new Response(
                json_encode(['error' => 'Content-Type must be application/json']),
                400,
                ['Content-Type' => 'application/json']
            );
        }

        $body = $request->getBody();
        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return new Response(
                json_encode(['error' => 'Invalid JSON']),
                400,
                ['Content-Type' => 'application/json']
            );
        }

        if (empty($data)) {
            return new Response(
                json_encode(['error' => 'No data provided for update']),
                400,
                ['Content-Type' => 'application/json']
            );
        }

        $allowedKeys = ['email', 'subject', 'message'];
        $dataKeys = array_keys($data);

        $extraKeys = array_diff($dataKeys, $allowedKeys);
        if (!empty($extraKeys)) {
            return new Response(
                json_encode(['error' => 'Invalid fields: ' . implode(', ', $extraKeys)]),
                400,
                ['Content-Type' => 'application/json']
            );
        }

        $contactsDir = __DIR__ . '/../../var/contacts';
        $filePath = null;
        $fileName = null;

        if (str_ends_with($id, '.json')) {
            $fileName = $id;
            $filePath = $contactsDir . '/' . $fileName;
        } else {
            $files = scandir($contactsDir);
            foreach ($files as $file) {
                if ($file === '.' || $file === '..') {
                    continue;
                }

                if (strpos($file, $id) !== false) {
                    $fileName = $file;
                    $filePath = $contactsDir . '/' . $fileName;
                    break;
                }
            }
        }

        if ($filePath === null || !file_exists($filePath)) {
            return new Response(
                json_encode(['error' => 'Contact not found']),
                404,
                ['Content-Type' => 'application/json']
            );
        }

        $fileContent = file_get_contents($filePath);
        $contactData = json_decode($fileContent, true);

        if ($contactData === null) {
            return new Response(
                json_encode(['error' => 'Error reading contact data']),
                500,
                ['Content-Type' => 'application/json']
            );
        }

        foreach ($data as $key => $value) {
            $contactData[$key] = $value;
        }

        $contactData['dateOfLastUpdate'] = time();
        file_put_contents($filePath, json_encode($contactData, JSON_PRETTY_PRINT));

        return new Response(
            json_encode($contactData, JSON_PRETTY_PRINT),
            200,
            ['Content-Type' => 'application/json']
        );
    }

    private function deleteContact(Request $request, string $id): Response
    {
        $contactsDir = __DIR__ . '/../../var/contacts';
        $filePath = null;
        $fileName = null;

        if (str_ends_with($id, '.json')) {
            $fileName = $id;
            $filePath = $contactsDir . '/' . $fileName;
        } else {
            $files = scandir($contactsDir);
            foreach ($files as $file) {
                if ($file === '.' || $file === '..') {
                    continue;
                }

                if (strpos($file, $id) !== false) {
                    $filePath = $contactsDir . '/' . $file;
                    break;
                }
            }
        }

        if ($filePath === null || !file_exists($filePath)) {
            return new Response(
                json_encode(['error' => 'Contact not found']),
                404,
                ['Content-Type' => 'application/json']
            );
        }

        unlink($filePath);

        return new Response(
            json_encode(['message' => 'Contact deleted successfully']),
            204,
            []
        );
    }
}
