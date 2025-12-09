<?php

namespace App\Controllers;

use App\Lib\Controllers\AbstractController;
use App\Lib\Http\Request;
use App\Lib\Http\Response;

class GetController extends AbstractController {
    public function process(Request $request): Response {
       
        // for catch if its not Get methode

         if ($request->getMethod() !== 'GET') {
            return new Response(
                    json_encode(['error' => 'Method not allowed']), // content
                    405,                                            // status
                     ['Content-Type' => 'application/json']          // headers
            );
        }

        $contactDir = __DIR__ . '/../../var/contacts';
        $contacts = [];

        if(!is_dir($contactDir)){
            return new Response(
                json_encode($contacts, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
                200,
                ['Content-Type' => 'application/json']
            );
 }

        $files = scandir($contactDir);

        foreach($files as $file){

            if($file === '.' || $file === '..'){
                continue;
            }
            
            $filePath = $contactDir . DIRECTORY_SEPARATOR . $file;

            if(!is_file($filePath)){
                continue;
            }

            if(pathinfo($filePath, PATHINFO_EXTENSION) !== "json"){
                continue;
            }

            $jsonContent = file_get_contents($filePath);

            if($jsonContent === false) {
                continue;
            }

            $data = json_decode($jsonContent , true);

            if(!is_array($data)){
                continue;
            }

            $contact =[
                "email" => $data['email']                       ?? null, 
                "subject" => $data['subject']                   ?? null,
                "message" => $data['message']                   ?? null,
                "dateOfCreation" => $data['dateOfCreation']     ?? null,
                "dateOfLastUpdate" => $data['dateOfLastUpdate'] ?? null,
            ];

            $contacts[] = $contact;
        }

                return new Response(
            json_encode($contacts, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
            200,
            ['Content-Type' => 'application/json']
        );

    }
}


