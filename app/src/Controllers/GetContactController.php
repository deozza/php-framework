<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Entities\Contact;
use App\Http\Router;

class GetContactController extends AbstractController {

    public function process(Request $request): Response {
        // check if the request content type is application/json
        $headers = $request->getHeaders();
        if ($headers['Content-Type'] !== 'application/json') {
            return new Response(json_encode(['error' => 'Invalid Content-Type']), 400);
        }

        // get the contact id from the uri
        $params = Router::extractParams($request->getUri(), '/contact/:id');
        if (empty($params)) {
            return new Response(json_encode(['error' => 'Invalid URI']), 400);
        }
        $id = $params[0];

        // see if the id is an email or a filename
        if (is_numeric($id[0])) {
            //si id is a filename
            $contactFile = Contact::findByFilename($id);
        } else {
            // si id is an email
            $contactFile = Contact::findByEmail($id);
        }

        if (!$contactFile) {
            return new Response(json_encode(['error' => 'Contact not found']), 404);
        }

        // read the contact file
        $contact = json_decode(file_get_contents($contactFile), true);

        // send the response
        return new Response(json_encode($contact), 200);
    }
}