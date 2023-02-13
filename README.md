# php-router
Simple PHP Router implementation

## Installation

```
composer require math280h/php-router
```

## Usage

```php
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

$router->get('/', function () {
    echo 'Hello World';
}, ['log']);

$router->run();

```
