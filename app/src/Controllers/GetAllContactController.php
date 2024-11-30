<?php

namespace App\Controllers;

use App\Controllers\AbstractController;
use App\Http\Request;
use App\Http\Response;

class GetAllContactController extends AbstractController
{
    public function process(Request $request): Response
    {
        return $this->getAllMethod();
    }

    // Methode GET pour récupérer les informations de tous les fichiers contacts
    private function getAllMethod(): Response
    {
        // Choisit le dossier où récupérer lire et décoder les fichiers contacts
        $directory = __DIR__ . '/../../var/contacts/';
        $files = glob($directory . '*.json');
        $contacts = array_map(fn($file) => json_decode(file_get_contents($file), true), $files);

        // Retourne une réponse avec les données des contacts en format JSON
        return new Response(json_encode($contacts), http_response_code(200), ['Content-Type' => 'application/json']);
    }
}
