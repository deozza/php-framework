<?php

namespace App\Controllers;

use App\Lib\Controllers\AbstractController;
use App\Lib\Http\Request;
use App\Lib\Http\Response;
use App\Entities\Contact;
use App\Lib\Managers\ContactManager;

class PostContactController extends AbstractController
{

    public function process(Request $request): Response
    {

        if ($request->checkMethod('POST') === false) {
            return new Response(json_encode(['error' => 'Method Not Allowed']), 405, ['Content-Type' => 'application/json']);
        }
        if (empty($request->getBody())) {
            return new Response(json_encode(['error' => 'Empty request body']), 400, ['Content-Type' => 'application/json']);
        }
        if (!Request::isJson($request->getBody())) {
            return new Response(json_encode(['error' => 'Invalid JSON format']), 400, ['Content-Type' => 'application/json']);
        }

        $body = json_decode($request->getBody(), true);
        $allowedFields = ['email', 'subject', 'message'];

        if (array_diff(array_keys($body), $allowedFields)) {
            return new Response(json_encode(['error' => 'Invalid fields in request body']), 400, ['Content-Type' => 'application/json']);
        }

        foreach ($allowedFields as $field) {
            if (!array_key_exists($field, $body)) {
                return new Response(json_encode(['error' => "Missing field: $field"]), 400, ['Content-Type' => 'application/json']);
            }
        }

        $contact = new Contact(
            $body['email'],
            $body['subject'],
            $body['message'],
        );

        $contactManager = new ContactManager();
        $filename = $contactManager->saveContact($contact);
        return new Response(json_encode(["file" => $filename]), 201, ['Content-Type' => 'application/json']);
    }
}
