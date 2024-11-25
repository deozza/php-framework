<?php

namespace App\Controllers;

use App\Lib\Controllers\AbstractController;
use App\Lib\Http\Request;
use App\Lib\Http\Response;
use App\Managers\ArtistManager;

class DeleteArtistController extends AbstractController {

    public function process(Request $request): Response {
        $artistManager = new ArtistManager();
        $artist = $artistManager->find($request->getSlug('id'));

        if($artist === null) {
            return new Response(json_encode(['error' => 'not found']), 404, ['Content-Type' => 'application/json']);
        }

        $artistManager->remove($artist);

        return new Response("", 204, ['Content-Type' => 'application/json']);
    }
}