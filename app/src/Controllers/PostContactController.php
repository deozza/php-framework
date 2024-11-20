<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;

class PostContactController extends AbstractController
{
  public function process(Request $request): Response
  {

    $data = json_decode($request->getPayload(), true);

    // Validate request body
    if (!isset($data['email'], $data['subject'], $data['message'])) {
      return new Response(json_encode(['error' => 'Invalid Form, please fill all fields']), 400, ['Content-Type' => 'application/json']);
    }

    $directory = __DIR__ . '/../../var/contacts';

    // Hadge Case : si répertoire pas déjà créé, retourne une erreur, donc création si inexistant
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
}
