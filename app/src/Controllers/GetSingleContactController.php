<?php

namespace App\Controllers;

use App\Lib\Controllers\AbstractController;
use App\Lib\Http\Request;
use App\Lib\Http\Response;

class GetSingleContactController extends AbstractController
{
    public function process(Request $request): Response
    {
    
        if ($request->getMethod() !== 'GET') {
            return new Response(
                json_encode(["error" => "Only GET method is accepted"]),
                404,
                ['Content-Type' => 'application/json']
            );
        }

        // Exemple : /contact/1765231656_leia@alderaan.com
        $uri = trim($request->getUri(), '/');  
        $parts = explode('/', $uri);

        if (!isset($parts[1])) {
            return new Response(
                json_encode(["error" => "Missing contact ID"]),
                404,
                ['Content-Type' => 'application/json']
            );
        }

        $id = $parts[1];
        $filePath = __DIR__ . '/../../var/contacts/' . $id . '.json';

        if (!file_exists($filePath)) {
            return new Response(
                json_encode(["error" => "Contact not found"]),
                404,
                ['Content-Type' => 'application/json']
            );
        }
        $content = file_get_contents($filePath);

        return new Response(
            $content,
            200,
            ['Content-Type' => 'application/json']
        );
    }
}
