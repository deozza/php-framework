<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;

class FetchContactController extends AbstractController {


    public function process(Request $request): Response {
        $directory = '/var/www/html/var/contacts';
        if (!is_dir($directory)) {
            return new Response(
                json_encode(['error' => 'Contacts directory not found.']),
                404,
                ['Content-Type' => 'application/json']
            );
        }
        $allData = [];
        $files = scandir($directory);
        $jsonFiles = array_filter($files, function ($file) {
                return pathinfo($file, PATHINFO_EXTENSION) === 'json';
            });
        foreach ($jsonFiles as $file) {
            $filePath = $directory . '/' . $file;

            // Lire le contenu du fichier JSON
            $jsonData = file_get_contents($filePath);

            // Décoder le JSON en tableau associatif
            $data = json_decode($jsonData, true);

            // Si les données sont valides, les ajouter au tableau final
            if ($data) {
                $allData[] = $data;
            }
        }
        return new Response(json_encode($allData, JSON_PRETTY_PRINT), 200, ['Content-Type' => 'application/json']);
    }
}