<?php
namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Entities\Contact;

class ContactViewController extends AbstractController {
    public function process(Request $request): Response {
        if (preg_match('#^/contact/(.+)$#', $request->getUri(), $matches)) {
            $email = $matches[1];
            return $this->getContactByEmail($email);
        }

        return new Response(json_encode(["error" => "Contact not found"]), 404);
    }

    private function getContactByEmail(string $email): Response {
        $directory = __DIR__ . '/../../var/contacts/';

        $files = glob($directory . "*_{$email}.json");
        if (empty($files)) {
            return new Response(json_encode(["error" => "Contact not found"]), 404);
        }

        $contact = Contact::fromJsonFile($files[0]);
        return new Response(json_encode($contact->toArray()), 200);
    }
}
