# php-router

Simple PHP Router, it supports features such as middlewares and views using aura/view


## Installation

```
composer require math280h/php-router
```

## Usage

For simple usage you can inline callback functions directly in the routes as shown below.

```php
use Math280h\PhpRouter\Router;
use Math280h\PhpRouter\Request;

session_start();

$router = new Router("./public");
$router->get('/', function () {
    echo 'Hello World';
});

$router->post('/', function (Request $request) {
    echo 'Hello World';
});

$router->run();
```
***Note**: The router always passes the Request object to callback functions*

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

The router will always pass the request object to the callback function.

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

### Returning views

The router is built with support for aura/view. This allows callback functions to pass back a view
and the router will automatically render it.

This can achieved like so:
```php
class MyController {
    public function index($request) {
        $view_factory = new \Aura\View\ViewFactory;
        $view = $view_factory->newInstance();
        $view_registry = $view->getViewRegistry();
        $layout_registry = $view->getLayoutRegistry();
        $layout_registry->set('default', dirname(__DIR__) . '/views/layouts/default.php');
        $view_registry->set('page', dirname(__DIR__) . '/views/my-view.php');
        $view->setView('page');
        $view->setLayout($layout);
        return $view;
    }
}

$router->get('/', MyController::class . '::index');
```

It's recommended to implement a helper function for spinning up new views so you don't have to duplicate the factory creation in your code

#### View helper function

A view helper function can look something like this:

```php
/**
 * Returns a view Object
 *
 * @param string $path
 * @param array $data
 * @param string $layout
 * @return View
 */
function view(string $path, array $data = [], string $layout = 'default'): View
{
    $view_factory = new \Aura\View\ViewFactory;
    $view = $view_factory->newInstance();
    $view_registry = $view->getViewRegistry();
    $layout_registry = $view->getLayoutRegistry();
    $layout_registry->set('default', dirname(__DIR__) . '/views/layouts/default.php');
    $view_registry->set('page', dirname(__DIR__) . '/views/' . $path . '.php');
    $view->setView('page');
    $view->setLayout($layout);
    $view->addData($data);
    return $view;
}
```
