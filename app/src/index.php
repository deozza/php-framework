<?php

$uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];
$headers = getallheaders();

var_dump($uri);
var_dump($method);
var_dump($headers);
