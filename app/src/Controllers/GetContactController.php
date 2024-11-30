<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;

class GetContactController extends AbstractController {

    public function process(Request $request): Response {
        $directory = __DIR__ . "/../../var/contacts";
        $files = glob("{$directory}/*.json");
        $contacts = [];

        foreach ($files as $file) {
            $content = file_get_contents($file);
            $contacts[] = json_decode($content, true);
        }

        return new Response(json_encode($contacts), 200, ['Content-Type' => 'application/json']);
    }
}