<?php

namespace App\Controllers;

use App\Lib\Controllers\AbstractController;
use App\Lib\Http\Request;
use App\Lib\Http\Response;
use App\Managers\ArtistManager;

class PatchArtistController extends AbstractController {

    public function process(Request $request): Response {
        $artistManager = new ArtistManager();
        $artist = $artistManager->find($request->getSlug('id'));

        if($artist === null) {
            return new Response(json_encode(['error' => 'not found']), 404, ['Content-Type' => 'application/json']);
        }

        $artist->setName("Updated artist");
        $artistManager->update($artist);

        return new Response(json_encode($artist), 200, ['Content-Type' => 'application/json']);
    }
}