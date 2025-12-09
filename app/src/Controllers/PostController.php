<?php

namespace App\Controllers;

use App\Lib\Controllers\AbstractController;
use App\Lib\Http\Request;
use App\Lib\Http\Response;

class PostController extends AbstractController {
    public function process(Request $request): Response {


        // for catch if its not POST methode

         if ($request->getMethod() !== 'POST') {
            return new Response(
                    json_encode(['error' => 'Method not allowed']), // content
                    405,                                            // status
                     ['Content-Type' => 'application/json']          // headers
            );
        }




        // for verified the header are in json 

       $headers = $request -> getHeaders();
       $contentType = $headers['Content-Type'] ?? $headers['content-type'] ?? null;

       if($contentType === null || !str_starts_with($contentType, 'application/json')){
            return new Response(
                    json_encode(['error' => 'Format not allowed']), // content
                    400,                                            // status
                     ['Content-Type' => 'application/json']          // headers
            );
       }


        // for verified the json data are not null 
       $rawBody = file_get_contents('php://input');
       $data = json_decode($rawBody,true);

       if($data === null && json_last_error() !== JSON_ERROR_NONE){
                    return new Response(
                    json_encode(['error' => 'Value not good']), // content
                    400,                                            // status
                     ['Content-Type' => 'application/json']          // headers
            );
       }

       
       $allowFields = ['email', "subject", 'message'];
       $receivedFields = array_keys($data);
       $extractFields = array_diff($receivedFields, $allowFields);
        //    for value not autorised 
       if (!empty($extractFields)){
            return new Response(
                    json_encode(['error' => 'Value not autorized']), // content
                    400,                                            // status
                     ['Content-Type' => 'application/json']          // headers
            );
       }
    //    for review with obligate value 
       foreach($allowFields as $field){
            if (!array_key_exists($field, $data)) {
                return new Response(
                    json_encode(['error' => 'Value present not good']), // content
                    400,                                            // status
                     ['Content-Type' => 'application/json']          // headers
            );
       }
       }

    //  for add timestamp and valid process 
       $timestamp = time();
       $email = $data['email'];

       $storedFilename = $timestamp . '_' . $email . '.json';
       $formattedDate = date('Y-m-d_H-i-s', $timestamp);
       $responseFilename = $formattedDate . '_' . $email . '.json';


       $contactToSave = [
            'email' => $data['email'],
            'subject' => $data['subject'],
            'message' => $data['message'],
            'dateOfCreation' => $timestamp,
            'dateOfLastUpdate' => $timestamp,
        ];

        $jsonToStore = json_encode($contactToSave, JSON_PRETTY_PRINT);

        $dir = __DIR__ . '/../../var/contacts';

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $filePath = $dir . '/' . $storedFilename;

        if (file_put_contents($filePath, $jsonToStore) === false) {
            return new Response(
                json_encode(['error' => 'Failed to save contact']),
                500,
                ['Content-Type' => 'application/json']
            );
                }

                $responseBody = json_encode(['file' => $responseFilename]);

            return new Response(
                $responseBody,
                201,
                ['Content-Type' => 'application/json']
            );

    }

}