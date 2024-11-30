<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;

class UpdateContactController extends AbstractController {
    public function process(Request $request): Response {
        $directory = '/var/www/html/var/contacts';

        // Vérifie si le répertoire existe
        if (!is_dir($directory)) {
            return new Response(
                json_encode(['error' => 'Contacts directory not found.']),
                404,
                ['Content-Type' => 'application/json']
            );
        }

        // Récupère le nom du fichier à partir des headers ou des paramètres de requête
        $fileName = $request->getHeaders()['X-Filename'] ?? $request->getBody()['filename'] ?? null;

        if (!$fileName) {
            return new Response(
                json_encode(['error' => 'Filename not provided.']),
                400,
                ['Content-Type' => 'application/json']
            );
        }

        $filePath = $directory . '/' . $fileName;

        // Vérifie si le fichier existe
        if (!file_exists($filePath)) {
            return new Response(
                json_encode(['error' => 'Contact form not found.']),
                404,
                ['Content-Type' => 'application/json']
            );
        }

        // Récupère le contenu actuel du fichier JSON
        $jsonData = file_get_contents($filePath);
        $data = json_decode($jsonData, true);

        if (!$data) {
            return new Response(
                json_encode(['error' => 'Invalid contact form data.']),
                500,
                ['Content-Type' => 'application/json']
            );
        }

        // Récupère les données à mettre à jour
        $updates = $request->getBody();

        // Liste des clés autorisées pour la mise à jour
        $allowedKeys = ['email', 'subject', 'message'];

        // Vérifie que seules les clés autorisées sont présentes
        foreach ($updates as $key => $value) {
            if (!in_array($key, $allowedKeys)) {
                return new Response(
                    json_encode(['error' => 'Invalid key: ' . $key]),
                    400,
                    ['Content-Type' => 'application/json']
                );
            }
        }

        // Met à jour les valeurs autorisées
        foreach ($updates as $key => $value) {
            $data[$key] = $value;
        }

        // Met à jour la date de dernière modification
        $data['dateOfLastUpdate'] = date('Y-m-d H:i:s');

        // Réécrit le fichier avec les nouvelles données
        file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT));

        // Retourne les nouvelles données
        return new Response(
            json_encode($data, JSON_PRETTY_PRINT),
            200,
            ['Content-Type' => 'application/json']
        );
    }
}
