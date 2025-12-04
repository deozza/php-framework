<?php

namespace App\Controllers;

use App\Lib\Controllers\AbstractController;
use App\Lib\Http\Request;
use App\Lib\Http\Response;
use App\Entities\Contact;

class PostContactController extends AbstractController
{

    public function process(Request $request): Response
    {
        $body = json_decode($request->getBody(), true);
        $allowedFields = ['email', 'subject', 'message'];


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

        $timestamp = time();
        $contact = new Contact(
            $body['email'],
            $body['subject'],
            $body['message'],
        );

        $filename = "{$timestamp}_{$body['email']}.json";
        $filepath = __DIR__ . '/../../var/contacts/' . $filename;

        $contact->SaveToFile($filepath);
        return new Response(json_encode(["file" => date('Y-m-d_H-i-s', $timestamp) . "_{$body['email']}.json"]), 201, ['Content-Type' => 'application/json']);
    }
}
