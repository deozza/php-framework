<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Managers\ArtistManager;

class GetArtistsController extends AbstractController {


    public function process(Request $request): Response {
        $artistManager = new ArtistManager();
        $artists = $artistManager->findAll();
        return new Response(json_encode($artists), 200, ['Content-Type' => 'application/json']);
    }
}