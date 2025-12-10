<?php

namespace App\Controllers;

use App\Lib\Controllers\AbstractController;
use App\Lib\Http\Request;
use App\Lib\Http\Response;
use App\Lib\Managers\ContactManager;

class UpdateContactController extends AbstractController
{

    public function process(Request $request): Response
    {

        if ($request->checkMethod('PATCH') === false) {
            return new Response(json_encode(['error' => 'Method Not Allowed']), 405, ['Content-Type' => 'application/json']);
        }

        $path = parse_url($request->getUri(), PHP_URL_PATH);
        $uriCut = explode('/', trim($path, '/'));
        $filename = $uriCut[1];

        if ($filename === null || $filename === '') {
            return new Response(json_encode(['error' => 'Filename is missing']), 400, ['Content-Type' => 'application/json']);
        }


        if (empty($request->getBody())) {
            return new Response(json_encode(['error' => 'Empty request body']), 400, ['Content-Type' => 'application/json']);
        }

        if (!Request::isJson($request->getBody())) {
            return new Response(json_encode(['error' => 'Invalid JSON format']), 400, ['Content-Type' => 'application/json']);
        }


        $body = json_decode($request->getBody(), true);
        $allowedFields = ['email', 'subject', 'message'];
        $fieldsToUpdate = array_intersect(array_keys($body), $allowedFields);

        if (array_diff(array_keys($body), $allowedFields)) {
            return new Response(json_encode(['error' => 'Invalid fields in request body']), 400, ['Content-Type' => 'application/json']);
        }

        if (empty($fieldsToUpdate)) {
            return new Response(json_encode(['error' => 'No valid fields to update']), 400, ['Content-Type' => 'application/json']);
        }

        $contactManager = new ContactManager();
        $getContact = $contactManager->getContactByFilename($filename);

        if (!$getContact) {
            return new Response(json_encode(['error' => 'Contact Not Found']), 404, ['Content-Type' => 'application/json']);
        }

        $updatedContact = $contactManager->updateContact($filename, $body);

        if (!$updatedContact) {
            return new Response(json_encode(['error' => 'Failed to update contact']), 500, ['Content-Type' => 'application/json']);
        }

        return new Response(json_encode($updatedContact, JSON_PRETTY_PRINT), 200, ['Content-Type' => 'application/json']);
    }
}
