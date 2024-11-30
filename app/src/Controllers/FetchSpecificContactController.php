<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;

class FetchSpecificContactController extends AbstractController {
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

        // Construit le chemin du fichier
        $filePath = $directory . '/' . $fileName;

        // Vérifie si le fichier existe
        if (!file_exists($filePath)) {
            return new Response(
                json_encode(['error' => 'Contact form not found.']),
                404,
                ['Content-Type' => 'application/json']
            );
        }

        // Lit le contenu du fichier JSON
        $jsonData = file_get_contents($filePath);
        $data = json_decode($jsonData, true);

        // Vérifie si le contenu est valide
        if (!$data) {
            return new Response(
                json_encode(['error' => 'Invalid contact form data.']),
                500,
                ['Content-Type' => 'application/json']
            );
        }

        // Retourne le contenu du fichier
        return new Response(
            json_encode($data, JSON_PRETTY_PRINT),
            200,
            ['Content-Type' => 'application/json']
        );
    }
}
