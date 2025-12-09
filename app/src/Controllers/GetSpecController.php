<?php

namespace App\Controllers;

use App\Lib\Controllers\AbstractController;
use App\Lib\Http\Request;
use App\Lib\Http\Response;

class GetController extends AbstractController {
    public function process(Request $request): Response {

        if ($request->getMethod() !== 'GET') {
            return new Response(
                json_encode(['error' => 'Method not allowed']),
                405,
                ['Content-Type' => 'application/json']
            );
        }

        $path = $request->getUri(); 

        $prefix = `/contact/`;

        if ($path === '/contact' || $path === `/contact/`) {
            return new Response(
                json_encode(['error' => 'Contact identifier missing in URL']),
                400,
                ['Content-Type' => 'application/json']
            );
        }

        if (strpos($path, $prefix) !== 0) {
            return new Response(
                json_encode(['error' => 'Invalid contact path']),
                400,
                ['Content-Type' => 'application/json']
            );
        }

        $filename = substr($path, strlen($prefix));

        $safeFilename = basename($filename);

        $contactsDir = dirname(__DIR__, 2) . '/var/contacts';

        if (!str_ends_with($safeFilename, '.json')) {
            $safeFilename .= '.json';
        }

        $filePath = $contactsDir . '/' . $safeFilename;

        if (!is_file($filePath)) {
            return new Response(
                json_encode(['error' => 'Contact not found']),
                404,
                ['Content-Type' => 'application/json']
            );
        }

        $json = file_get_contents($filePath);
        $data = json_decode($json, true);

        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            return new Response(
                json_encode(['error' => 'Invalid contact JSON file']),
                500,
                ['Content-Type' => 'application/json']
            );
        }

        return new Response(
            json_encode($data),
            200,
            ['Content-Type' => 'application/json']
        );
    }
}
