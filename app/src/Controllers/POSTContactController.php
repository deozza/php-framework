<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;

class POSTContactController extends AbstractController {


    public function process(Request $request): Response {    
        // Check si valide
        return $request->checkValidity();
    }
}