<?php

namespace App\Controllers;

use App\Lib\Controllers\AbstractController;
use App\Lib\Managers\ContactManager;
use App\Lib\Http\Request;
use App\Lib\Http\Response;

class GetAllContactController extends AbstractController
{

    public function process(Request $request): Response
    {
        if ($request->checkMethod('GET') === false) {
            return new Response(json_encode(['error' => 'Method Not Allowed']), 405, ['Content-Type' => 'application/json']);
        }
        $contactManager = new ContactManager();
        $contacts = $contactManager->getAllContacts();
        if (empty($contacts)) {
            return new Response(json_encode(['message' => 'No contacts found']), 404, ['Content-Type' => 'application/json']);
        }
        return new Response(json_encode($contacts), 200, ['Content-Type' => 'application/json']);
    }
}
