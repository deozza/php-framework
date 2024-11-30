<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Entities\Contact;

class PatchContactController extends AbstractController
{
    public function process(Request $request): Response
    {
        // Utilise basename pour obtenir le nom du fichier à partir de l'URI
        $filename = basename($request->getUri());
        return $this->patchMethod($filename, $request);
    }

    private function patchMethod($filename, $request): Response
    {
        // Choisit le dossier où chercher le fichier correspondant
        $directory = __DIR__ . '/../../var/contacts/';
        $filePath = $directory . $filename . '.json';

        // Vérification si aucun fichier n'a été trouvé
        if (!$filePath) {
            return new Response(json_encode(['error' => 'Contact not found']), http_response_code(404), ['Content-Type' => 'application/json']);
        }

        // Vérification des champs supplémentaires non autorisés
        $allowed = ['email', 'subject', 'message'];
        $body = json_decode($request->getPayload(), true) ?? [];

        // Vérification de la présence des champs requis et renvoie une erreur 400 si un champ non autorisé est présent
        $extraFields = array_diff(array_keys($body), $allowed);
        if (!empty($extraFields)) {
            return new Response("Field not allowed: " . implode(', ', $extraFields), http_response_code(400));
        }

        // Lecture des données actuelles du fichier
        $currentData = json_decode(file_get_contents($filePath), true);
        $contact = new Contact(
            $currentData['email'],
            $currentData['subject'],
            $currentData['message'],
            $currentData['dateOfCreation'] ?? time(),
            $currentData['dateOfLastUpdate'] ?? time()
        );

        // Mise à jour des données du contact avec les nouvelles données
        foreach ($body as $key => $value) {
            $setter = 'set' . ucfirst($key);
            if (method_exists($contact, $setter)) {
                $contact->$setter($value);
            }
        }
        // foreach ($body as $key => $value) {
        //     switch ($key) {
        //     case 'email':
        //         $contact->setEmail($value);
        //         break;
        //     case 'subject':
        //         $contact->setSubject($value);
        //         break;
        //     case 'message':
        //         $contact->setMessage($value);
        //         break;
        //     }
        // }

        // Mise à jour du timestamp de la dernière modification
        $contact->setDateOfLastUpdate(time());

        // Convertir l'objet contact en tableau associatif
        $contactData = [
            'email' => $contact->getEmail(),
            'subject' => $contact->getSubject(),
            'message' => $contact->getMessage(),
            'dateOfCreation' => $contact->getDateOfCreation(),
            'dateOfLastUpdate' => $contact->getDateOfLastUpdate(),
        ];

        $urlEmail = $request->getEmail();

        // Vérification si l'email a été modifié
        if (isset($body['email']) && $body['email'] !== $urlEmail) {
            // Génération du nouveau nom de fichier basé sur le nouveau timestamp et le nouvel email
            $newFilename = sprintf('%s_%s.json', $contact->getDateOfLastUpdate(), $body['email']);
            $newFilePath = $directory . $newFilename;

            // Renommage du fichier
            rename($filePath, $newFilePath);
            file_put_contents($newFilePath, json_encode($contactData));
        } else {
            // Écriture des données mises à jour dans le fichier existant
            file_put_contents($filePath, json_encode($contactData));
        }

        // Retourne une réponse avec les données mises à jour en format JSON
        return new Response(json_encode($contactData), http_response_code(200), ['Content-Type' => 'application/json']);
    }
}
