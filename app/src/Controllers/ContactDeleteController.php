<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use Exception;

class ContactDeleteController extends AbstractController {

    public function process(Request $request): Response {
        $attributes = $request->getAttributes();
        if (empty($attributes) || !isset($attributes['email'])) {
            return new Response(json_encode(["error" => "Email not specified in URI"]), 400);
        }

        $email = $attributes['email'];

        $contactDir = __DIR__ . "/../../var/contacts/";

        $files = scandir($contactDir);
        $contactFile = null;

        foreach ($files as $file) {
            if (strpos($file, $email) !== false) {
                $contactFile = $contactDir . $file;
                break;
            }
        }
        if (!$contactFile || !file_exists($contactFile)) {
            return new Response(json_encode(["error" => "Contact not found"]), 404);
        }
        unlink($contactFile);
        return new Response('', 204);
    }
}
