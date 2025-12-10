<?php

namespace App\Controllers;

use App\Lib\Controllers\AbstractController;
use App\Lib\Http\Request;
use App\Lib\Http\Response;
use App\Lib\Managers\ContactManager;


class GetContactController extends AbstractController
{

    public function process(Request $request): Response
    {
        if ($request->checkMethod('GET') === false) {
            return new Response(json_encode(['error' => 'Method Not Allowed']), 405, ['Content-Type' => 'application/json']);
        }

        $segments = $request->getPathSegments();
        $filename = $segments[1] ?? null;

        $contactManager = new ContactManager();
        $contact = $contactManager->getContactByFilename($filename);

        if (!$contact) {
            return new Response(json_encode(['error' => 'Contact Not Found']), 404, ['Content-Type' => 'application/json']);
        }

        return new Response(json_encode($contact), 200, ['Content-Type' => 'application/json']);
    }
}
