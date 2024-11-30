<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Entities\Contact;
use App\Controllers\AbstractController;

class GetContactController extends AbstractController
{
  public function process(Request $request): Response
  {
    $uri = $request->getUri();
    $filename = basename($uri);

    return $this->getContact($filename);
  }

  private function getContact(string $filename): Response
  {
    $directory = __DIR__ . "/../../var/contacts/";

    $filename = $this->addJsonExtensionIfMissing($filename);

    $filePath = $directory . $filename;

    if (!file_exists($filePath)) {
      return $this->jsonResponse(['error' => 'Contact not found'], 404);
    }

    $contact = Contact::jsonRequestReader($filePath);

    return $this->jsonResponse($contact->bodyArray(), 200);
  }
}
