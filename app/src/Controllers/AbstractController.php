<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;

abstract class AbstractController {
    public abstract function process(Request $request): Response;
}
