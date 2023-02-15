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

$router->run();
```

### Accepting different HTTP Methods

The router exposes a function for each accepted HTTP method. The list of available methods are:
* GET
* POST
* PUT
* DELETE
* OPTIONS
* HEAD
* PATCH

And they can be called like so:
```php
$router->get('/', MyController::class . '::index')
$router->post('/', MyController::class . '::index')
```

### Adding middleware

The router supports middleware than runs after the connection is accepted but before the request
is forwarded to the callback function.

For now, only direct callback functions are supported and can be added to the router like so:

```php
$router->addMiddleware("log", function ($request) {
    print_r($request);
});
```

Once the middleware is added to the router you can attach it to your route like so:
```php
$router->get('/', function () {
    echo 'Hello World';
}, ['log']);
```

### Passing callbacks from classes to the router

The router allows you to pass references to functions inside classes instead of inlining the callback functions
You can do this like so:

```php
class MyController {
    public function index($request) {
        echo 'Hello World';
    }
}

$router->get('/', MyController::class . '::index');
```