<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;

class ContactController extends AbstractController {


    public function process(Request $request): Response {

        $body = $request->getBody();

        // Validate required fields
        if (empty($body['email']) || empty($body['subject']) || empty($body['message'])) {
            return new Response(json_encode(['error' => 'Email, subject, and message are required.']), 400, ['Content-Type' => 'application/json']);
        }
        if (count($body) > 3) {
            return new Response(json_encode(['error' => 'Extra var detected. only email, subject and message are allowed.']), 400, ['Content-Type' => 'application/json']);
        }
        $email = $body['email'];
        $subject = $body['subject'];
        $message = $body['message'];
        $timestamp = time();
        $fileName = $timestamp . '_' . $email . '.json';

        $directory = '/var/www/html/var/contacts';
        $filePath = '/var/www/html/var/contacts' . '/' . $fileName;

        if (!file_exists($directory)) {
            mkdir($directory, 0755, true); // Create directory if it doesn't exist
        }

        $jsonData = json_encode(
            [
                'email' => $email,
                'subject' => $subject,
                'message' => $message,
                'dateOfCreation' => $timestamp,
                'dateOfLastUpdate' => $timestamp
            ],
            JSON_PRETTY_PRINT
        );
        if (file_put_contents($directory . '/' . $fileName, $jsonData)) {
            // Réponse avec status 201 et le nom du fichier
            return new Response(
                json_encode(['file' => $fileName]),
                201,
                ['Content-Type' => 'application/json']
            );
        } else {
            // En cas d'erreur lors de la sauvegarde
            return new Response(
                json_encode(['error' => 'Failed to save contact form.']),
                500,
                ['Content-Type' => 'application/json']
            );
            }
    }
}