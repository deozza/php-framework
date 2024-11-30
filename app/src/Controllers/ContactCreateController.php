<?php
namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Entities\Contact;

class ContactCreateController extends AbstractController {
    public function process(Request $request): Response {
        $body = json_decode($request->getBody(), true);

        $allowedKeys = ['email', 'subject', 'message'];
        if (array_diff(array_keys($body), $allowedKeys)) {
            return new Response(json_encode(["error" => "Invalid keys in request body"]), 400);
        }
        
        $timestamp = time();
        $filename = "{$timestamp}_{$body['email']}.json";
        $filePath = __DIR__ . "/../../var/contacts/{$filename}";

        $contact = new Contact(
            $body['email'],
            $body['subject'],
            $body['message'],
            $timestamp,
            $timestamp
        );

        $contact->saveToFile($filePath);

        return new Response(json_encode(["file" => date('Y-m-d_H-i-s', $timestamp) . "_{$body['email']}.json"]), 201);
    }
}
