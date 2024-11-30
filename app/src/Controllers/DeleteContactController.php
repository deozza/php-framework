<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Entities\Contact;
use App\Controllers\AbstractController;

class DeleteContactController extends AbstractController
{
  public function process(Request $request): Response
  {
    $uri = $request->getUri();
    $filename = basename($uri);;
    return $this->deleteContact($filename);
  }

  private function deleteContact(string $filename): Response
  {
    $directory = __DIR__ . "/../../var/contacts/";

    $filename = $this->addJsonExtensionIfMissing($filename);

    $filePath = $directory . $filename;

    if (!file_exists($filePath)) {
      return $this->jsonResponse(['error' => 'Contact not found'], 404);
    }

    unlink($filePath);

    return new Response('', 204);
  }
}