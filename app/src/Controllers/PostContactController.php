<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Entities\Contact;
use App\Controllers\AbstractController;

class PostContactController extends AbstractController
{
  public function process(Request $request): Response
  {
    if ($this->jsonValidation($request) === false) {
      return new Response('Invalid Content-Type', 400);
    }

    $body = json_decode($request->getBody(), true);

    $contact = new Contact($body['email'], $body['subject'], $body['message']);

    $contact->save();

    $fileName = $contact->fileName();

    return $this->jsonResponse(['fileName' => $fileName], 201);
  }
}
