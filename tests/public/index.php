<?php

require '../../vendor/autoload.php';

use Math280h\PhpRouter\Route;
use Math280h\PhpRouter\Router;

session_start();

$router = new Router();
$router->get('/', function () {
    echo 'Hello World';
});

$router->get('/yeet', function () {
    echo 'Hello World';
});


$router->group(["prefix" => "test"], [
    Route::get('/1', function () {
        echo 'Hello World';
    }),
    Route::get('/2', function () {
        echo 'Hello World';
    }),
]);

$router->addMiddleware("log", function ($request) {
    print_r($request);
});

$router->run();
