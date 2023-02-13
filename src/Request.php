<?php declare(strict_types=1);

namespace Math280h\PhpRouter;

use Error;
use InvalidArgumentException;

class Request
{
    /**
     * HTTP method
     * @var string
     */
    public string $method;

    /**
     * HTTP Path
     * @var string
     */
    public string $path;

    /**
     * HTTP URI
     * @var array{scheme?: string, host?: string, port?: int<0, 65535>, user?: string, pass?:string, path?: string, query?: string, fragment?: string}
     */
    public array $uri;

    /**
     * HTTP Query
     * @var array<int|string, array<string|int>|string>
     */
    public array $query;

    /**
     * Request data
     * @var array<object>
     */
    public array $data;

    public function __construct()
    {
        if (
            $_SERVER['REQUEST_URI'] === null ||
            $_SERVER['REQUEST_METHOD'] === null
        ) {
            throw new InvalidArgumentException();
        }

        $uri = parse_url($_SERVER['REQUEST_URI']);
        if (!is_array($uri)) {
            throw new InvalidArgumentException();
        }

        $this->uri = $uri;

        if (!array_key_exists('path', $this->uri)) {
            throw new InvalidArgumentException();
        }

        $this->path = rtrim($this->uri["path"], "/");
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->data = array_merge($_GET, $_POST);

        if (array_key_exists("query", $this->uri)) {
            parse_str($this->uri["query"], $query);
        } else {
            $query = [];
        }

        $this->query = $query;
    }

    /**
     * Attach Errors to request
     *
     * @param string $message
     * @param int $level
     * @return Request
     */
    public function withErrors(string $message, int $level): Request
    {
        $_SESSION["errors"][] = new Error($message, $level);
        return $this;
    }

    /**
     * Redirect request
     *
     * @param string $location
     * @return void
     */
    public function redirect(string $location): void
    {
        header("Location: $location");
    }
}