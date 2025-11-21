<?php

namespace App\Controllers;

use App\Http\Request;

class TestController extends AbstractController {
    public function process(Request $request) {
        echo 'hello world';
    }
}
