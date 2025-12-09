<?php

namespace App\Controllers;

use App\Lib\Controllers\AbstractController;
use App\Lib\Http\Request;
use App\Lib\Http\Response;

class DeleteContactController extends AbstractController
{
    public function process(Request $request): Response
    { 
        if ($request->getMethod() !== 'DELETE') {
            return new Response(
                json_encode(['error' => 'Only DELETE method is accepted']),
                405,
                ['Content-Type' => 'application/json']
            );
        }
        $uri = trim($request->getUri(), '/');            
        $parts = explode('/', $uri);

        if (!isset($parts[1])) {
            return new Response(
                json_encode(['error' => 'Missing contact ID']),
                400,
                ['Content-Type' => 'application/json']
            );
        }

       $id = $parts[1];
       $id = trim($id);
       $id = rtrim($id, "\n\r");
       $id = str_replace("%0A", "", $id);


         $filePath = __DIR__ . '/../../var/contacts/' . $id . '.json';
 
        if (!file_exists($filePath)) {
            return new Response(
                json_encode(['error' => 'Contact not found']),
                404,
                ['Content-Type' => 'application/json']
            );
        }

        // Supprimer le fichier
        unlink($filePath);

     
        return new Response(
            '',     
            204,
            ['Content-Type' => 'application/json']
        );
    }
}
