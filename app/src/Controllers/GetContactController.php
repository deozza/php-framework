<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;

class GetContactController extends AbstractController
{
  public function process(Request $request): Response
  {
    // Get all contacts
    $directory = __DIR__ . '/../../var/contacts/';
    $contacts = [];

    if (is_dir($directory)) {
      $files = scandir($directory);
      foreach ($files as $file) {

        $filePath = $directory . $file;

        if (is_file($filePath)) {
          $content_file = file_get_contents($filePath);
          $contacts[] = json_decode($content_file, true);
        }
      }
    }
    return new Response(json_encode($contacts), 200, ['Content-Type' => 'application/json']);
  }
}
