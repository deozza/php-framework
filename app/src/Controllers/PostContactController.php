<?php

namespace App\Controllers;

use App\Controllers\AbstractController;
use App\Http\Request;
use App\Http\Response;
use App\Entities\Contact;

class PostContactController extends AbstractController
{
    public function process(Request $request): Response
    {
        return $this->postMethod($request);
    }

    // Methode POST pour récupérer les informations de tous les fichiers contacts
    private function postMethod(Request $request): Response
    {
        // Récupération des données de la requête
        $data = json_decode($request->getPayload(), true) ?? [];

        // Définition des champs requis
        $required = ['email', 'subject', 'message'];

        // Vérification de la présence des champs requis
        foreach ($required as $field) {
            if (!array_key_exists($field, $data) || empty($data[$field])) {
                return new Response("Missing field: {$field}", http_response_code(400));
            }
        }

        // Vérification des champs supplémentaires non autorisés
        $extraFields = array_diff(array_keys($data), $required);
        if (!empty($extraFields)) {
            return new Response("Field not allowed: " . implode(', ', $extraFields), http_response_code(400));
        }

        // Valider la requête du body
        if (!isset($data['email'], $data['subject'], $data['message'])) {
            return new Response(json_encode(['error' => 'Invalid request body']), http_response_code(400), ['Content-Type' => 'application/json']);
        }

        $contact = new Contact($data['email'], $data['subject'], $data['message']);
        $timestamp = time();
        $filename = "{$timestamp}_{$contact->getEmail()}.json";
        $filePath = __DIR__ . '/../../var/contacts/' . $filename;

        // Convertir l'objet contact en tableau associatif
        $contactData = [
            'email' => $contact->getEmail(),
            'subject' => $contact->getSubject(),
            'message' => $contact->getMessage(),
            'dateOfCreation' => $contact->getDateOfCreation(),
            'dateOfLastUpdate' => $contact->getDateOfLastUpdate(),
        ];

        file_put_contents($filePath, json_encode($contactData));

        // Créer un nom de fichier avec une date lisible pour la réponse
        $responseTimestamp = date('Y-m-d_h-i-s', $timestamp);
        $responseFilename = "{$responseTimestamp}_{$contact->getEmail()}.json";

        return new Response(json_encode(['file' => $responseFilename]), http_response_code(201), ['Content-Type' => 'application/json']);
    }
}
