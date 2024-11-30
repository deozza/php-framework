<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Manager\ContactManager;

class GetContactsController extends AbstractController{

    public function process(Request $request): Response {
        $contactManager = new ContactManager;
        $contacts = $contactManager->findAll();
        return new Response(
            json_encode($contacts),
            200,
            ['Content-Type' => 'application/json']
        );
    }
}