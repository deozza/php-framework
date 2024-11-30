<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;

class DeleteContactController extends AbstractController
{
    public function process(Request $request): Response
    {
        // Utilise basename pour obtenir le nom du fichier à partir de l'URI
        $filename = basename($request->getUri());
        return $this->deleteMethod($filename);
    }

    public function deleteMethod($filename): Response
    {
        // Choisit le dossier où chercher le fichier correspondant
        $directory = __DIR__ . '/../../var/contacts/';
        $filePath = $directory . $filename . '.json';

        // Vérification si le fichier n'a pas été trouvé
        if (!$filePath) {
            return new Response(json_encode(['error' => 'Contact not found']), 404, ['Content-Type' => 'application/json']);
        }

        // Suppression du fichier trouvé
        unlink($filePath);

        // Retourne une réponse avec un statut 204 No Content
        return new Response('', 204, ['Content-Type' => 'application/json']);
    }
}
