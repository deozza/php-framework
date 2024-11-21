<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Managers\ArtistManager;

class GetArtistController extends AbstractController {

    public function process(Request $request): Response {
        $artistManager = new ArtistManager();
        $artist = $artistManager->find($request->getSlug('id'));

        if($artist === null) {
            return new Response(json_encode(['error' => 'not found']), 404, ['Content-Type' => 'application/json']);
        }

        return new Response(json_encode($artist), 200, ['Content-Type' => 'application/json']);
    }
}