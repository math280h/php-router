# php-router

Simple PHP Router, it supports features such as middlewares and views using aura/view


## Installation

```
composer require math280h/php-router
```

## Usage

For simple usage you can inline callback functions directly in the routes as shown below.


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

However, you can also pass a callback from another class like shown here:
```php
class MyController {
    public function index($request) {
        echo 'Hello World';
    }
}

$router->get('/', MyController::class . '::index');
```
