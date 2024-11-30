<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Entities\Contact;

class PostContactController extends AbstractController {

    public function process(Request $request): Response {
        $body = json_decode($request->getBody(), true);
        $timestamp = date('Y-m-d H:i:s');

        $contact = new Contact(
            $body['email'],
            $body['subject'],
            $body['message'],
            $timestamp,
            $timestamp
        );

        $directory = __DIR__ . "/../../var/contacts";
        file_put_contents("{$directory}/{$contact->getFilename()}", json_encode($contact->toArray()));

        return new Response(json_encode(['file' => $contact->getFilename()]), 201, ['Content-Type' => 'application/json']);
    }
}