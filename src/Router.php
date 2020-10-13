<?php

namespace PK;

use PK\Exceptions\Http\NotFound;
use PK\Http\Request;
use PK\Http\Response;

class Router
{
    private $map;

    public function handle(Request $req): Response
    {
        if (empty($this->map[$req->getMethod()])) {
            throw new \RuntimeException();
        }

        if (!isset($this->map[$req->getMethod()][$req->getPath()])) {
            return (new Response([], 404))->setException(new NotFound());
        }

        return call_user_func($this->map[$req->getMethod()][$req->getPath()], $req);
    }

    public function addRoute(string $method, string $path, callable $callback): void
    {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException();
        }

        $this->map[$method][$path] = $callback;
    }
}
