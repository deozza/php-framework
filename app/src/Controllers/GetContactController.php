<?php

namespace App\Controllers;

use App\Lib\Controllers\AbstractController;
use App\Lib\Http\Request;
use App\Lib\Http\Response;

class GetContactController extends AbstractController
{
  public function process(Request $request): Response
{
    $headers = $request->getHeaders();
    $headersLower = array_change_key_case($headers, CASE_LOWER);
    $contentType = $headersLower['content-type'] ?? null;

    if ($contentType === null || stripos($contentType, 'application/json') !== 0) {
        return new Response(
            json_encode(['error' => 'Only application/json is accepted']),
            400,
            ['Content-Type' => 'application/json']
        );
    }
    $rawBody = file_get_contents('php://input');
    $data = json_decode($rawBody, true);

    if (!is_array($data) || !isset($data['file'])) {
        return new Response(
            json_encode(['error' => 'Missing file field']),
            400,
            ['Content-Type' => 'application/json']
        );
    }
    $fileName = basename($data['file']);
    $filePath = __DIR__ . '/../../var/contacts/' . $fileName;

    if (!file_exists($filePath)) {
        return new Response(
            json_encode(['error' => 'Contact not found']),
            404,
            ['Content-Type' => 'application/json']
        );
    }

    $content = file_get_contents($filePath);

    return new Response(
        $content,
        200,
        ['Content-Type' => 'application/json']
    );
}

}

