<?php

namespace App\Controllers;

use App\Http\Response;
use App\Http\Request;
use App\Manager\ContactManager;

class DeleteContactController extends AbstractController {
    public function process(Request $request): Response {
        $contactManager = new ContactManager;
        $response = $contactManager->delete($request->getArgs('filename'));
        return $response;
    }

}