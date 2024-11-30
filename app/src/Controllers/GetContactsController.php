<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;

class GetContactsController extends AbstractController {

    public function process(Request $request): Response {
        // check if the request content type is application/json
        $headers = $request->getHeaders();
        if ($headers['Content-Type'] !== 'application/json') {
            return new Response(json_encode(['error' => 'Invalid Content-Type']), 400);
        }

        // set the directory to read the contact files
        $directory = __DIR__ . "/../../var/contacts";

        // read all contact files
        $contacts = [];
        foreach (glob("{$directory}/*.json") as $filename) {
            $contacts[] = json_decode(file_get_contents($filename), true);
        }

        // send the response
        return new Response(json_encode($contacts), 200);
    }
}