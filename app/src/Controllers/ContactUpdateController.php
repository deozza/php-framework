<?php   
namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Entities\Contact;

class ContactUpdateController extends AbstractController {

    public function process(Request $request): Response {
        $attributes = $request->getAttributes();
        if (empty($attributes) || !isset($attributes['email'])) {
            return new Response(json_encode(["error" => "Email not specified in URI"]), 400);
        }

        $email = $attributes['email'];
        error_log("ContactUpdateController: Email fourni : $email");

        $body = json_decode($request->getBody(), true);
        error_log("ContactUpdateController: Corps de la requête : " . print_r($body, true));

        $allowedKeys = ['email', 'subject', 'message'];
        if (array_diff(array_keys($body), $allowedKeys)) {
            return new Response(json_encode(["error" => "Invalid keys in request body"]), 400);
        }

        $contactFiles = glob(__DIR__ . "/../../var/contacts/*{$email}*"); 
        if (empty($contactFiles)) {
            return new Response(json_encode(["error" => "Contact not found"]), 404);
        }

        $contactFile = $contactFiles[0]; 
        error_log("ContactUpdateController: Chemin du fichier contact : $contactFile");

        $contactData = json_decode(file_get_contents($contactFile), true);

        foreach ($allowedKeys as $key) {
            if (isset($body[$key])) {
                $contactData[$key] = $body[$key];
            }
        }

        $contactData['dateOfLastUpdate'] = time();

        file_put_contents($contactFile, json_encode($contactData));
        
        return new Response(json_encode($contactData), 200);
    }
}
