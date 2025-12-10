<?php

namespace App\Controllers;

use App\Lib\Controllers\AbstractController;
use App\Lib\Http\Request;
use App\Lib\Http\Response;
use App\Lib\Managers\ContactManager;

class DeleteContactController extends AbstractController
{

    public function process(Request $request): Response
    {

        if ($request->checkMethod('DELETE') === false) {
            return new Response(json_encode(['error' => 'Method Not Allowed']), 405, ['Content-Type' => 'application/json']);
        }

        $segments = $request->getPathSegments();
        $filename = $segments[1] ?? null;

        $contactManager = new ContactManager();
        $getContact = $contactManager->getContactByFilename($filename);

        if (!$getContact) {
            return new Response(json_encode(['error' => 'Contact Not Found']), 404, ['Content-Type' => 'application/json']);
        }

        $deleted = $contactManager->deleteContact($filename);
        if (!$deleted) {
            return new Response(json_encode(['error' => 'Failed to delete contact']), 500, ['Content-Type' => 'application/json']);
        }
        return new Response('', 204, []);
    }
}
