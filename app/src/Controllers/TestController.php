<?php

namespace App\Controllers;

use App\Lib\Controllers\AbstractController;
use App\Lib\Http\Request;
use App\Lib\Http\Response;

class TestController extends AbstractController {
    public function process(Request $request): Response
    {
        return $this->render('test', [
            'title' => 'Salut',
            'items' => [
                'foo',
                'bar'
            ]
        ]);
    }
    
}

?>
