<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;

class TestController extends AbstractController
{


    public function process(Request $request): Response
    {
        $uriParts = explode('/', trim($request->getUri(), '/'));
        $email = end($uriParts);
        $uri = $request->getUri();
        $filename = basename($request->getUri());
        return new Response('Test Controller  ' . $filename);
    }
}
