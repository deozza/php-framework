<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Entities\Contact;

class ContactController extends AbstractController {
    public function process(Request $request): Response {
        $directory = __DIR__ . '/../../var/contacts/';
        
        $files = glob($directory . '*.json');
        $contacts = [];

        foreach ($files as $file) {
            $contact = Contact::fromJsonFile($file);
            $contacts[] = $contact->toArray(); 
        }
        return new Response(json_encode($contacts), 200);
    }
}
