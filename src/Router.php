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

    /**
     * Create route group
     *
     * @param array<string> $attributes
     * @param array<Route> $routes
     * @return void
     */
    public function group(array $attributes, array $routes): void
    {
        foreach ($routes as $route) {
            if (array_key_exists("prefix", $attributes)) {
                $route->path = (!str_starts_with($attributes["prefix"], "/") ? "/" . $attributes["prefix"]:$attributes["prefix"]) . $route->path;
            }
            if (array_key_exists("middleware", $attributes)) {
                $route->middleware[] = $attributes["middleware"];
            }
            $this->addRoute($route);
        }
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
     * Add HEAD Request to router
     *
     * @param string $path
     * @param callable|string $callback
     * @param array<string> $middleware
     * @return void
     */
    public function head(string $path, callable|string $callback, array $middleware = []): void
    {
        $this->addRoute(new Route($path, 'HEAD', $callback, $middleware));
    }

    /**
     * Add PUT Request to router
     *
     * @param string $path
     * @param callable|string $callback
     * @param array<string> $middleware
     * @return void
     */
    public function put(string $path, callable|string $callback, array $middleware = []): void
    {
        $this->addRoute(new Route($path, 'PUT', $callback, $middleware));
    }

    /**
     * Add DELETE Request to router
     *
     * @param string $path
     * @param callable|string $callback
     * @param array<string> $middleware
     * @return void
     */
    public function delete(string $path, callable|string $callback, array $middleware = []): void
    {
        $this->addRoute(new Route($path, 'DELETE', $callback, $middleware));
    }

    /**
     * Add OPTIONS Request to router
     *
     * @param string $path
     * @param callable|string $callback
     * @param array<string> $middleware
     * @return void
     */
    public function options(string $path, callable|string $callback, array $middleware = []): void
    {
        $this->addRoute(new Route($path, 'OPTIONS', $callback, $middleware));
    }

    /**
     * Add PATCH Request to router
     *
     * @param string $path
     * @param callable|string $callback
     * @param array<string> $middleware
     * @return void
     */
    public function patch(string $path, callable|string $callback, array $middleware = []): void
    {
        $this->addRoute(new Route($path, 'PATCH', $callback, $middleware));
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
