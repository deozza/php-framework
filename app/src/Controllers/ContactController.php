<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;

class ContactController extends AbstractController {

    public function process(Request $request): Response {
        // Vérifie la méthode HTTP
        if ($request->getMethod() === 'POST') {
            return $this->handlePost($request);
        }

        // Par défaut pour les autres méthodes
        return new Response('Contact Controller');
    }

    private function handlePost(Request $request): Response {
        // Vérifie que le contenu est bien en JSON
        $contentType = $request->getHeader('Content-Type');
        if ($contentType !== 'application/json') {
            return new Response(json_encode(["error" => "Content-Type must be application/json"]), 400);
        }

        // Récupère le corps de la requête
        $body = json_decode($request->getBody(), true);

        // Valide la présence des champs requis
        if (
            !isset($body['email'], $body['subject'], $body['message']) ||
            count($body) !== 3
        ) {
            return new Response(json_encode(["error" => "Invalid request body. Only 'email', 'subject', and 'message' are allowed."]), 400);
        }

        // Validation basique de l'email
        if (!filter_var($body['email'], FILTER_VALIDATE_EMAIL)) {
            return new Response(json_encode(["error" => "Invalid email format."]), 400);
        }

        // Prépare les données pour le fichier
        $timestamp = time();
        $filename = "{$timestamp}_{$body['email']}.json";
        $filepath = __DIR__ . "/../../var/contacts/" . $filename;


        $data = [
            "email" => $body['email'],
            "subject" => $body['subject'],
            "message" => $body['message'],
            "dateOfCreation" => $timestamp,
            "dateOfLastUpdate" => $timestamp
        ];

        // Assure que le dossier existe
        if (!is_dir(__DIR__ . "/../../var/contacts/")) {
            mkdir(__DIR__ . "/../../var/contacts/", 0777, true);
        }

        // Sauvegarde dans un fichier
        if (file_put_contents($filepath, json_encode($data)) === false) {
            return new Response(json_encode(["error" => "Failed to save contact."]), 500);
        }

        // Retourne une réponse 201 avec le nom du fichier
        return new Response(json_encode(["file" => $filename]), 201);
    }
}
