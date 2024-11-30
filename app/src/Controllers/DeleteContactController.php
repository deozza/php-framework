<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;

class DeleteContactController extends AbstractController {
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

        // Supprime le fichier
        if (!unlink($filePath)) {
            return new Response(
                json_encode(['error' => 'Failed to delete contact form.']),
                500,
                ['Content-Type' => 'application/json']
            );
        }

        // Retourne une réponse vide avec un code 204
        return new Response('', 204);
    }
}
