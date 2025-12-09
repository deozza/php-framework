<?php

namespace App\Controllers;

use App\Lib\Controllers\AbstractController;
use App\Lib\Http\Request;
use App\Lib\Http\Response;

class GetContactController extends AbstractController
{
    public function process(Request $request): Response
    {
        if ($request->getMethod() !== 'GET') {
            return new Response(
                json_encode(['error' => 'Only GET method is accepted']),
                405,
                ['Content-Type' => 'application/json']
            );
        }

        $directory = __DIR__ . '/../../var/contacts/';

        if (!is_dir($directory)) {
            return new Response(
                json_encode(['error' => 'Contacts directory not found']),
                500,
                ['Content-Type' => 'application/json']
            );
        }
        $files = glob($directory . '*.json');

        $contacts = [];

        foreach ($files as $filePath) {
            $content = file_get_contents($filePath);
            $data = json_decode($content, true);

            if (is_array($data)) {
                $contacts[] = $data;
            }
        }
        return new Response(
            json_encode($contacts, JSON_PRETTY_PRINT),
            200,
            ['Content-Type' => 'application/json']
        );
    }
}
