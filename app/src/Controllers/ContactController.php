<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;

class ContactController extends AbstractController
{
  public function process(Request $request): Response
  {
    switch ($request->getMethod()) {
      case 'GET':
        if ($request->getSlug('email')) {
          return $this->getSpecificContact($request);
        }
        return $this->getContacts($request);
      case 'GET':
        return $this->getSpecificContact($request);
      case 'POST':
        return $this->postContact($request);
      case 'PATCH':
        return $this->updateContact($request);
      case 'DELETE':
        return $this->deleteContact($request);
      default:
        return new Response('Method not allowed', 405, ['Content-Type' => 'text/plain']);
    }
  }

  private function postContact(Request $request): Response
  {
    // Post contact form
    $data = json_decode($request->getPayload(), true);

    // Validate request body
    if (!isset($data['email'], $data['subject'], $data['message'])) {
      return new Response(json_encode(['error' => 'Invalid Form, please fill all fields']), 400, ['Content-Type' => 'application/json']);
    }

    $directory = __DIR__ . '/../../var/contacts';

    // Edge Case : si répertoire pas déjà créé, retourne une erreur, donc création si inexistant
    if (!is_dir($directory)) {
      mkdir($directory, 0777, true);
    }

    $timestamp = time();

    $formContact = [
      'email' => $data['email'],
      'subject' => $data['subject'],
      'message' => $data['message'],
      'dateOfCreation' => $timestamp,
      'dateOfLastUpdate' => $timestamp,
    ];

    $filename = $timestamp . '_' . $data['email'] . '.json';
    $filepath = $directory . '/' . $filename;
    file_put_contents($filepath, json_encode($formContact));

    return new Response(json_encode(['file' => $filename]), 201, ['Content-Type' => 'application/json']);
  }
  private function getContacts(Request $request): Response
  {
    // Get all contacts form
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

  private function getSpecificContact(Request $request): Response
  {
    $email = $request->getSlug('email');
    $directory = __DIR__ . '/../../var/contacts/';
    $filePath = $directory . '*' . $email . '.json';

    $files = glob($filePath);

    if (empty($files)) {
      return new Response(json_encode(['error' => 'Contact form not found']), 404, ['Content-Type' => 'application/json']);
    }

    $content_file = file_get_contents($files[0]);
    $contact = json_decode($content_file, true);

    return new Response(json_encode($contact), 200, ['Content-Type' => 'application/json']);
  }

  private function updateContact(Request $request): Response
  {
    $email = $request->getSlug('email');
    $directory = __DIR__ . '/../../var/contacts/';
    $filePath = $directory . '*' . $email . '.json';

    $files = glob($filePath);

    if (empty($files)) {
      return new Response(json_encode(['error' => 'Contact form not found']), 404, ['Content-Type' => 'application/json']);
    }

    $content_file = file_get_contents($files[0]);
    $contact = json_decode($content_file, true);

    $body = json_decode($request->getPayload(), true);

    $bodyFieldAllowed = ['email', 'subject', 'message'];

    foreach ($body as $field => $value) {
      if (!in_array($field, $bodyFieldAllowed)) {
        return new Response(json_encode(['error' => 'Invalid body field']), 400, ['Content-Type' => 'application/json']);
      }
    }

    $contact['email'] = $body['email'] ?? $contact['email'];
    $contact['subject'] = $body['subject'] ?? $contact['subject'];
    $contact['message'] = $body['message'] ?? $contact['message'];
    $contact['dateOfLastUpdate'] = time();

    file_put_contents($files[0], json_encode($contact));

    return new Response(json_encode($contact), 200, ['Content-Type' => 'application/json']);
  }

  private function deleteContact(Request $request): Response
  {
    $email = $request->getSlug('email');
    $directory = __DIR__ . '/../../var/contacts/';
    $filePath = $directory . '*' . $email . '.json';

    $files = glob($filePath);

    if (empty($files)) {
      return new Response(json_encode(['error' => 'Contact form not found']), 404, ['Content-Type' => 'application/json']);
    }

    unlink($files[0]);

    return new Response('', 204);
  }
}
