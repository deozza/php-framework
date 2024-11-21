<?php

namespace App\Controllers;

use App\Lib\Http\Request;
use App\Lib\Http\Response;
use App\Lib\Controllers\AbstractController;

class TestController extends AbstractController {


    public function process(Request $request): Response {
        return $this->render('test', [
            'title' => 'Pouet',
            'items' => ['foo', 'bar', 'baz'],
        ]);;
    }
}