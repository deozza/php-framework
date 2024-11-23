<?php

namespace App\Controllers;

use App\Controllers\AbstractController;
use App\Http\Request;
use App\Http\Response;

class ContactController extends AbstractController
{
    public function process(Request $request): Response
    {
        $uri = $request->getUri();

        // Utilisation des methodes pour chaque cas d'utilisation (GET, POST, PAST, DELETE)
        switch ($request->getMethod()) {
            case 'POST':
                return $this->postMethod($request);

            case 'GET':
                if (preg_match('#^/contact/(.+)$#', $uri, $matches)) {
                    return $this->getSpecificMethod($matches[1]);
                }
                // Si la method GET est utilisée sans paramètre, retourne les informations de tous les fichiers contacts
                return $this->getAllMethod();

            case 'PATCH':
                if (preg_match('#^/contact/(.+)$#', $uri, $matches)) {
                    return $this->patchMethod($matches[1], $request);
                }
                break;

            case 'DELETE':
                if (preg_match('#^/contact/(.+)$#', $uri, $matches)) {
                    return $this->deleteMethod($matches[1]);
                }
                break;
        }

        // Retourne une réponse avec le statut 401 si la méthode n'est pas autorisée
        return new Response('Method Not Allowed', 401);
    }

    // Methode POST pour créer un fichier contact
    public function postMethod(Request $request): Response
    {
        // Récupération des données de la requête
        $data = json_decode($request->getPayload(), true) ?? [];

        // Définition des champs requis
        $required = ['email', 'subject', 'message'];

        // Vérification de la présence des champs requis
        foreach ($required as $field) {
            if (!array_key_exists($field, $data) || empty($data[$field])) {
                return new Response("Missing field: {$field}", 400);
            }
        }

        // Vérification des champs supplémentaires non autorisés
        $extraFields = array_diff(array_keys($data), $required);
        if (!empty($extraFields)) {
            return new Response("Field not allowed: " . implode(', ', $extraFields), 400);
        }

        // Génération de timestamp pour le retour de la réponse (Je sais pas si c'est comme ça qu'il fallait le faire je me suis peut-être compliqué la vie pour rien :/ )
        $timestampPost = date("Y-m-d_h-i-s", time());

        // Génération du nom de fichier basé sur le timestamp et l'email
        $filename = sprintf('%s_%s.json', time(), $data['email']);
        $filenamePost = sprintf('%s_%s.json', $timestampPost, $data['email']);

        // Création du contenu du fichier
        $content = [
            'email' => $data['email'],
            'subject' => $data['subject'],
            'message' => $data['message'],
            'dateOfCreation' => time(),
            'dateOfLastUpdate' => time(),
        ];

        // Définition du chemin du fichier et écriture du contenu dans le fichier
        $filePath = __DIR__ . '/../../var/contacts/' . $filename;
        file_put_contents($filePath, json_encode($content));

        // Retourne une réponse avec le nom du fichier créé avec un timestamp plus lisible
        return new Response(json_encode(['file' => $filenamePost]), 201, ['Content-Type' => 'application/json']);
    }

    // Methode GET pour récupérer les informations de tous les fichiers contacts
    private function getAllMethod(): Response
    {
        // Choisit le dossier où récupérer lire et décoder les fichiers contacts
        $directory = __DIR__ . '/../../var/contacts/';
        $files = glob($directory . '*.json');
        $contacts = array_map(fn($file) => json_decode(file_get_contents($file), true), $files);

        // Retourne une réponse avec les données des contacts en format JSON
        return new Response(json_encode($contacts), 200, ['Content-Type' => 'application/json']);
    }

    // Methode GET pour récupérer les informations d'un ou plusieurs fichiers contacts via le mail correspondant au/x fichier/s
    public function getSpecificMethod(string $email): Response
    {
        // Choisit le dossier où chercher le fichier correspondant
        $directory = __DIR__ . '/../../var/contacts/';
        $pattern = $directory . '*_' . $email . '.json';
        $files = glob($pattern);

        // Vérification si aucun fichier n'a été trouvé
        if (!$files) {
            return new Response(json_encode(['error' => 'Contact not found']), 404, ['Content-Type' => 'application/json']);
        }

        // Lecture du contenu du fichier trouvé
        $data = file_get_contents($files[0]);

        // Retourne une réponse avec les données du contact en format JSON
        return new Response($data, 200, ['Content-Type' => 'application/json']);
    }

    // Methode PATCH pour modifier un ou plusieurs fichiers contacts via le mail correspondant au(x) fichier(x)
    private function patchMethod(string $email, Request $request): Response
    {
        // Choisit le dossier où chercher le fichier correspondant
        $directory = __DIR__ . '/../../var/contacts/';
        $files = glob($directory . "*_{$email}.json");

        // Vérification si aucun fichier n'a été trouvé
        if (!$files) {
            return new Response(json_encode(['error' => 'Contact not found']), 404, ['Content-Type' => 'application/json']);
        }

        // Vérification des champs supplémentaires non autorisés
        $allowed = ['email', 'subject', 'message'];
        $body = json_decode($request->getPayload(), true) ?? [];

        // Vérification de la présence des champs requis
        $extraFields = array_diff(array_keys($body), $allowed);
        if (!empty($extraFields)) {
            return new Response("Field not allowed: " . implode(', ', $extraFields), 400);
        }

        // Récupération des données de la requête
        $data = json_decode($request->getPayload(), true);

        // Lecture des données actuelles du fichier
        $currentData = json_decode(file_get_contents($files[0]), true);

        // Mise à jour des données du fichier avec les nouvelles données
        foreach ($data as $key => $value) {
            $currentData[$key] = $value;
        }

        // Mise à jour du timestamp de la dernière modification
        $currentData['dateOfLastUpdate'] = time();

        // Vérification si l'email a été modifié (c'était pas dans l'énoncé je me sui peut-être compliqué la vie pour rien encore une fois ? :'( )
        if (isset($data['email']) && $data['email'] !== $email) {
            // Génération du nouveau nom de fichier basé sur le nouveau timestamp et le nouvel email
            $newFilename = sprintf('%s_%s.json', $currentData['dateOfLastUpdate'], $data['email']);
            $newFilePath = $directory . $newFilename;

            // Renommage du fichier
            rename($files[0], $newFilePath);
        } else {
            // Écriture des données mises à jour dans le fichier existant
            file_put_contents($files[0], json_encode($currentData));
        }

        // Retourne une réponse avec les données mises à jour en format JSON
        return new Response(json_encode($currentData), 200, ['Content-Type' => 'application/json']);
    }

    // Method DELETE pour supprimer un ou plusieurs fichiers contacts via le mail correspondant au(x) fichier(x)
    private function deleteMethod(string $email): Response
    {
        // Choisit le dossier où chercher le fichier correspondant
        $directory = __DIR__ . '/../../var/contacts/';
        $files = glob($directory . "*_{$email}.json");

        // Vérification si aucun fichier n'a été trouvé
        if (!$files) {
            return new Response(json_encode(['error' => 'Contact not found']), 404, ['Content-Type' => 'application/json']);
        }

        // Suppression de chaque fichier trouvé
        foreach ($files as $file) {
            unlink($file);
        }

        // Retourne une réponse vide avec le statut 204
        return new Response('', 204);
    }

    // Fin ! :D 
}
