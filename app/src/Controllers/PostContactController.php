<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Entities\Contact;

class PostContactController extends AbstractController {

    public function process(Request $request): Response {
        // check if the request content type is application/json
        $headers = $request->getHeaders();
        if ($headers['Content-Type'] !== 'application/json') {
            return new Response(json_encode(['error' => 'Invalid Content-Type']), 400);
        }

        // set the data to check in the request body
        $data = json_decode(file_get_contents('php://input'), true);

        // check request body
        if (!isset($data['email'], $data['subject'], $data['message']) || count($data) !== 3) {
            return new Response(json_encode(['error' => 'Invalid request body']), 400);
        }

        // create new contact
        $contact = new Contact($data['email'], $data['subject'], $data['message']);

        // set the directory to save the contact file
        $directory = __DIR__ . "/../../var/contacts";

        // save the contact to dedicated file
        file_put_contents("{$directory}/{$contact->getFilename()}", json_encode($contact->toArray()));

        // send the response
        return new Response(json_encode(['file' => $contact->getFilename()]), 201);
    }
}