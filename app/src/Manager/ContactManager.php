<?php

namespace App\Manager;

use App\Entities\Contact;


use App\Http\Response;

class ContactManager {

    public function generateJson(Contact $contact) {
        $content = [
            'email' => $contact->getEmail(),
            'subject' => $contact->getSubject(),
            'message' => $contact->getMessage(),
            'dateOfCreation' => $contact->getDateOfCreation(),
            'dateOfLastUpdate' => $contact->getDateOfUpdate(),
        ];
        
        $jsonData = json_encode($content, JSON_PRETTY_PRINT);

        
        $filename = sprintf('%d_%s.json', $contact->getDateOfCreation(), $contact->getEmail());
        $filePath = __DIR__ . '/../../var/contacts/' . $filename;
        
        if (file_put_contents($filePath, $jsonData)) {
            return new Response(
                json_encode(["file" => $filename]),
                201,
                ['Content-Type' => 'application/json']
            );
        }

        return new Response(
            json_encode(["error" => "Le json est invalide"]),
            500,
            ['Content-Type' => 'application/json']
        );
    }

    public function findAll() {
        $directory = __DIR__ . '/../../var/contacts/';
        $files = glob($directory . '*.json');
        
        $contacts = [];
        foreach ($files as $file) {
            $contacts[] = json_decode(file_get_contents($file), true);
        }

        return $contacts;
    }

    public function findOne(string $filename) {
        $file = __DIR__ . '/../../var/contacts/' . $filename . '.json';
        if (!file_exists($file)) {
            return new Response(
                json_encode(["error" => "Le contact existe pas"]),
                404,
                ['Content-Type' => 'application/json']
            );
        }
        
        $data = json_decode(file_get_contents($file), true);
        $contact = new Contact($data["email"], $data["subject"], $data["message"]);
        return $contact;
    }

    public function save(string $filename, Contact $contact) {
        $file = __DIR__ . '/../../var/contacts/' . $filename . '.json';

        if (!file_exists($file)) {
            return new Response(
                json_encode(["error" => "Le contact existe pas"]),
                404,
                ['Content-Type' => 'application/json']
            );
        }
        $content = json_decode(file_get_contents($file), true);
        $content['email'] = $contact->getEmail();
        $content['subject'] = $contact->getSubject();
        $content['message'] = $contact->getMessage();
        $content['dateOfLastUpdate'] = $contact->getDateOfUpdate();

        file_put_contents($file, json_encode($content, JSON_PRETTY_PRINT));

        return new Response(
            json_encode($content),
            200,
            ['Content-Type' => 'application/json']
        ); 
    }

    public function delete(string $filename){
        $file = __DIR__ . '/../../var/contacts/' . $filename . '.json';

        if (!file_exists($file)) {
            return new Response(
                json_encode(["error" => "Le contact existe pas"]),
                404,
                ['Content-Type' => 'application/json']
            );
        }
        unlink($file);
        return new Response(
            json_encode(""),
            204,
            ['Content-Type' => 'application/json']
        ); 
    }

}