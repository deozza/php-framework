<?php

use App\Http\Request;
use App\Http\Router;

require_once __DIR__ . '/../vendor/autoload.php';

try {
    
    $request = new Request();
    echo Router::route($request);

} catch(\Exception $e) {
    echo $e->getMessage();
}
