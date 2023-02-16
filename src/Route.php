<?php declare(strict_types=1);

namespace Math280h\PhpRouter;

use Error;
use Exception;

class Route
{
    /**
     * Request path
     * @var string
     */
    public string $path;

    /**
     * HTTP Method
     * @var string
     */
    public string $method;

    /**
     * Callable action.
     * @var callable|array{object, string|null}|string
     */
    public $callback;

    /**
     * Attached middleware
     * @var array<string>
     */
    public array $middleware;

    /**
     * @param string $path
     * @param string $method
     * @param callable|string $callback
     * @param array<string> $middleware
     */
    public function __construct(
        string $path,
        string $method,
        callable|string $callback,
        array $middleware
    )
    {
        $this->path = rtrim($path, "/");
        $this->method = $method;
        $this->callback = $callback;
        $this->middleware = $middleware;
        $this->makeCallable();
    }

    /**
     * Check if route matches request
     *
     * @param Request $request
     * @return bool
     */
    public function matches(Request $request): bool
    {
        return $this->method === $request->method && $this->path === $request->path;
    }

    /**
     * If callback is string try to convert it to callable
     * This is used to call controller functions
     *
     * @return void
     */
    private function makeCallable(): void
    {
        if (is_string($this->callback)) {
            $parts = explode('::', $this->callback);
            try {
                $class_name = array_shift($parts);
                $actor = new $class_name;

                $actor_method = array_shift($parts);
                $this->callback = [$actor, $actor_method];
                return;
            } catch (Exception) {
                throw new Error("Invalid route callback");
            }
        }
    }

    /**
     * Route construction
     */

    /**
     * @param string $path
     * @param callable|string $callback
     * @param array<string> $middleware
     * @return Route
     */
    public static function get(
        string $path,
        callable|string $callback,
        array $middleware = []
    ): Route
    {
        return new Route($path, "GET", $callback, $middleware);
    }

    /**
     * @param string $path
     * @param callable|string $callback
     * @param array<string> $middleware
     * @return Route
     */
    public static function post(
        string $path,
        callable|string $callback,
        array $middleware = []
    ): Route
    {
        return new Route($path, "POST", $callback, $middleware);
    }

    /**
     * @param string $path
     * @param callable|string $callback
     * @param array<string> $middleware
     * @return Route
     */
    public static function put(
        string $path,
        callable|string $callback,
        array $middleware = []
    ): Route
    {
        return new Route($path, "PUT", $callback, $middleware);
    }

    /**
     * @param string $path
     * @param callable|string $callback
     * @param array<string> $middleware
     * @return Route
     */
    public static function delete(
        string $path,
        callable|string $callback,
        array $middleware = []
    ): Route
    {
        return new Route($path, "DELETE", $callback, $middleware);
    }

    /**
     * @param string $path
     * @param callable|string $callback
     * @param array<string> $middleware
     * @return Route
     */
    public static function options(
        string $path,
        callable|string $callback,
        array $middleware = []
    ): Route
    {
        return new Route($path, "OPTIONS", $callback, $middleware);
    }

    /**
     * @param string $path
     * @param callable|string $callback
     * @param array<string> $middleware
     * @return Route
     */
    public static function head(
        string $path,
        callable|string $callback,
        array $middleware = []
    ): Route
    {
        return new Route($path, "HEAD", $callback, $middleware);
    }

    /**
     * @param string $path
     * @param callable|string $callback
     * @param array<string> $middleware
     * @return Route
     */
    public static function patch(
        string $path,
        callable|string $callback,
        array $middleware = []
    ): Route
    {
        return new Route($path, "PATCH", $callback, $middleware);
    }
}
