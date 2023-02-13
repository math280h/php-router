<?php declare(strict_types=1);

namespace Math280h\PhpRouter;

use Closure;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveTreeIterator;
use Aura\View\View;
use RuntimeException;

class Router
{
    /**
     * Collection of routes
     * @var Route[]
     */
    private array $routes;

    /**
     * Collection of middleware
     * @var array<string, Closure>
     */
    private array $middlewares;

    public function __construct(string $public_directory = "")
    {
        $this->loadPublic($public_directory);
    }

    /**
     * Add Route to Router
     *
     * @param Route $route
     * @return void
     */
    private function addRoute(Route $route): void
    {
        $this->routes[] = $route;
    }

    /**
     * Add Middleware to Router
     *
     * @param string $name
     * @param Closure $middleware
     * @return void
     */
    public function addMiddleware(string $name, Closure $middleware): void
    {
        $this->middlewares[$name] = $middleware;
    }

    /**
     * Add GET Request to router
     *
     * @param string $path
     * @param callable|string $callback
     * @param array<string> $middleware
     * @return void
     */
    public function get(string $path, callable|string $callback, array $middleware = []): void
    {
        $this->addRoute(new Route($path, 'GET', $callback, $middleware));
    }

    /**
     * Add POST Request to router
     *
     * @param string $path
     * @param callable|string $callback
     * @param array<string> $middleware
     * @return void
     */
    public function post(string $path, callable|string $callback, array $middleware = []): void
    {
        $this->addRoute(new Route($path, 'POST', $callback, $middleware));
    }

    /**
     * Load resource in public folder
     *
     * @param string $public_directory
     * @return void
     */
    private function loadPublic(string $public_directory = ""): void
    {
        if ($public_directory === "") {
            $public_directory = dirname(__DIR__, 2) . '/public';
        }

        $public_resources = new RecursiveTreeIterator(
            new RecursiveDirectoryIterator(
                $public_directory,
                FilesystemIterator::SKIP_DOTS));

        foreach ($public_resources as $public_resource) {
            $path = explode('public/', $public_resource)[1];
            if (!str_ends_with($path, '.php')) {
                $this->get($path, function() use ($public_resource) {
                    echo file_get_contents($public_resource);
                });
            }
        }
    }

    /**
     * Run Router
     *
     * @return bool
     */
    public function run(): bool
    {
        $request = new Request();

        foreach ($this->routes as $route) {
            if ($route->matches($request)) {
                foreach ($route->middleware as $middleware) {
                    if (array_key_exists($middleware, $this->middlewares)) {
                        $middleware_result = $this->middlewares[$middleware]($request);
                        if ($middleware_result === false) {
                            return false;
                        }
                    }
                }

                # Check if callback is callable
                if (!is_callable($route->callback)) {
                    throw new RuntimeException('Route callback is not callable');
                }
                $callback = call_user_func($route->callback, $request);

                if ($callback instanceof View) {
                    echo $callback();
                }
                return true;
            }
        }

        /**
         * If this is reached, no route was found
         */
        http_response_code(404);
        echo "This page is not found";
        return false;
    }
}
