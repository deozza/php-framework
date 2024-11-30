<?php

namespace App\Controllers;

use App\Entities\Contact;
use App\Http\Request;
use App\Http\Response;
use App\Manager\ContactManager;

class PostContactController extends AbstractController {

    public function process(Request $request): Response {
        $contact = new Contact();
        $contactManager = new ContactManager;
        $body = $request->getBody();
        $allowedBody = ['email','subject','message'];

        foreach ($body as $b) {
            if (!in_array($b ,$allowedBody)){
                return new Response(
                    json_encode(["erreur" => "Les prioprietes a part (email,subject,message) ne sont pas autorise."]),
                    400,
                    ['Content-Type' => 'application/json']
                );
            }
        }
        
        $contact->setEmail($body['email']);
        $contact->setSubject($body['subject']);
        $contact->setMessage($body['message']);
        $contact->setDateOfCreation(time());
        $contact->setDateOfUpdate(time());
        
        
        $response = $contactManager->generateJson($contact);

        return $response;
    }

}