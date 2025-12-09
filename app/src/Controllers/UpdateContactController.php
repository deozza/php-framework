<?php

namespace App\Controllers;

use App\Lib\Controllers\AbstractController;
use App\Lib\Http\Request;
use App\Lib\Http\Response;

class UpdateContactController extends AbstractController
{
    public function process(Request $request): Response
    {
        if ($request->getMethod() !== 'PATCH') {
            return new Response(
                json_encode(['error' => 'Only PATCH method is accepted']),
                405,
                ['Content-Type' => 'application/json']
            );
        }

        // Extraire l'ID depuis l'URL
        // Exemple : /contact/1765231656_leia@alderaan.com
        $uri = trim($request->getUri(), '/');
        $parts = explode('/', $uri);

        if (!isset($parts[1])) {
            return new Response(
                json_encode(['error' => 'Missing contact ID']),
                400,
                ['Content-Type' => 'application/json']
            );
        }

        $id = $parts[1];

        // Construire le chemin du fichier
        $filePath = __DIR__ . '/../../var/contacts/' . $id . '.json';

        // Vérifier existence
        if (!file_exists($filePath)) {
            return new Response(
                json_encode(['error' => 'Contact not found']),
                404,
                ['Content-Type' => 'application/json']
            );
        }

        // Lire le fichier existant
        $content = file_get_contents($filePath);
        $contact = json_decode($content, true);

        // Lire le corps de la requête
        $rawBody = file_get_contents('php://input');
        $updates = json_decode($rawBody, true);

        if (!is_array($updates)) {
            return new Response(
                json_encode(['error' => 'Invalid JSON body']),
                400,
                ['Content-Type' => 'application/json']
            );
        }

        // Champs autorisés
        $allowed = ['email', 'subject', 'message'];

        // Vérifier qu'il n'y a pas de champs interdits
        foreach ($updates as $key => $value) {
            if (!in_array($key, $allowed)) {
                return new Response(
                    json_encode(['error' => 'Invalid field: ' . $key]),
                    400,
                    ['Content-Type' => 'application/json']
                );
            }
        }

        // Appliquer les mises à jour
        foreach ($updates as $key => $value) {
            $contact[$key] = $value;
        }

        // Mise à jour de la date de dernière modification
        $contact['dateOfLastUpdate'] = time();

        // Réécriture du fichier
        file_put_contents($filePath, json_encode($contact, JSON_PRETTY_PRINT));

        // Retourner le contact mis à jour
        return new Response(
            json_encode($contact, JSON_PRETTY_PRINT),
            200,
            ['Content-Type' => 'application/json']
        );
    }
}
