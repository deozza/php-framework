<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;

class GETContactController extends AbstractController {


    public function process(Request $request): Response {    
        
        $request->fetchAll();

        return new Response("GETController Up !");
    }
}