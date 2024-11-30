<?php

namespace App\Http;

use App\Controllers\ContactController;

class Router
{
    public function route(Request $request): Response
    {
        $uri = $request->getUri();
        $method = $request->getMethod();

        if ($uri === '/contact' && $method === 'POST') {
            $controller = new ContactController($request);
            return $controller->storeContact();
        }

        if ($uri === '/contact' && $method === 'GET') {
            $controller = new ContactController($request);
            return $controller->getAllContacts();
        }

        return Response::jsonResponse(['error' => 'Not found'], 404);
    }

}
