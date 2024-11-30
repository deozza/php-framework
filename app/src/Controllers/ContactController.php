<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;

class ContactController
{
    private const CONTACT_DIRECTORY = __DIR__ . '/../../var/contacts/';

    public function __construct(private Request $request)
    {
    }

    // Exercice 1: Création d'un contact
    public function storeContact(): Response
    {
        // Debugging request body
        $body = $this->request->getBody();
        var_dump("Raw Body: ", $body);

        $data = json_decode($body, true);
        var_dump("Parsed Data: ", $data);

        if (!isset($data['email'], $data['subject'], $data['message'])) {
            return Response::jsonResponse(['error' => 'Invalid request body'], 400);
        }

        $filename = time() . '_' . $data['email'] . '.json';
        $filePath = self::CONTACT_DIRECTORY . $filename;

        if (!is_dir(self::CONTACT_DIRECTORY)) {
            mkdir(self::CONTACT_DIRECTORY, 0777, true);
        }

        if (file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT)) === false) {
            return Response::jsonResponse(['error' => 'Failed to save contact'], 500);
        }

        return Response::jsonResponse(['file' => $filename], 201);
    }

    // Exercice 2: Récupération de tous les contacts
    public function getAllContacts(): Response
    {
        if (!is_dir(self::CONTACT_DIRECTORY)) {
            return Response::jsonResponse([], 200);
        }

        $files = array_diff(scandir(self::CONTACT_DIRECTORY), ['.', '..']);
        $contacts = [];

        foreach ($files as $file) {
            $filePath = self::CONTACT_DIRECTORY . $file;
            $data = json_decode(file_get_contents($filePath), true);

            if ($data !== null) {
                $contacts[] = $data;
            }
        }

        return Response::jsonResponse($contacts, 200);
    }
}