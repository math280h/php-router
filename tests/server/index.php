<?php

require '../../vendor/autoload.php';

use Math280h\PhpRouter\Router;

session_start();

$router = new Router("./public");
$router->get('/', function () {
    echo 'Hello World';
});

$router->addMiddleware("log", function ($request) {
    print_r($request);
});

$router->run();
