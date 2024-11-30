<?php

namespace App\Controllers;

use App\Entities\Contact;
use App\Http\Request;
use App\Http\Response;
use App\Manager\ContactManager;

class GetContactController extends AbstractController {
    
    public function process(Request $request): Response {
        $contactManager = new ContactManager;
        $contact = $contactManager->findOne($request->getArgs('filename'));
        
        return new Response(
            json_encode($contact),
            200,
            ['Content-Type' => 'application/json']
        );
    }
}