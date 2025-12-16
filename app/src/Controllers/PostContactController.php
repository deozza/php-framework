<?php

namespace App\Controllers;

use App\Forms\ContactForm;
use App\Lib\Http\Request;
use App\Lib\Http\Response;
use App\Lib\Controllers\AbstractController;
use App\Services\ContactService;

class PostContactController extends AbstractController {
    
    
    public function process(Request $request): Response
    {
        if($request->headerHasValue('Content-Type', 'application/json') === false) {
            $response = new Response('Wrong content-type', 400, ['Content-type' => 'application/json']);
            return $response; 
        }

        $payload = json_decode($request->getPayload(), true);

        $contactForm = new ContactForm();

        if($contactForm->checkPayloadIsValid($payload) === false) {
            $response = new Response('Wrong payload', 400, ['Content-type' => 'application/json']);
            return $response;
        }

        $contactService = new ContactService();
        $contact = $contactService->buildContact($payload);

        $contactService->saveContact($contact);
        
        $response = new Response(json_encode(['file' => $contact->getId()]), 201, ['Content-type' => 'application/json']);
        return $response;
    }
    
}

?>
