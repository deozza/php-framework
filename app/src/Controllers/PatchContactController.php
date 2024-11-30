<?php

namespace App\Controllers;

use App\Entities\Contact;
use App\Http\Response;
use App\Http\Request;
use App\Manager\ContactManager;

class PatchContactController extends AbstractController {
    public function process(Request $request): Response {
        
        $contactManager = new ContactManager;
        $contact = $contactManager->findOne($request->getArgs('filename'));
        $content = $request->getBody();
        if (!isset($content["email"]) && !isset($content["subject"]) && !isset($content["message"])) {
            return new Response(
                json_encode(["error" => "Le JSON est invalide, aucun champ est defini"]),
                500,
                ['Content-Type' => 'application/json']
            );
        }
        if (isset($content["email"])) {
            $contact->setEmail($content["email"]);
        }
        if (isset($content["subject"])) {
            $contact->setSubject($content["subject"]);
        } 
        if (isset($content["message"])) {
            $contact->setMessage($content["message"]);
        }
        
        $contact->setDateOfUpdate(time());
        $response = $contactManager->save($request->getArgs('filename'),$contact);
        return $response;
    }
}