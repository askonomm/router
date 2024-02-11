<?php

use Asko\Router\Router;
use PHPUnit\Framework\TestCase;

class TestNoActionMiddleware {
    public function handle(): null
    {
        return null;
    }
}

class TestActionMiddleware {
    public function handle(): string
    {
        return "Hello, John!";
    }
}

class TestParamsMiddleware {
    public function handle(string $who): string
    {
        return "Hello, $who!";
    }
}

class RouterMiddlewareTest extends TestCase
{
    public function testNoActionMiddleware()
    {
        $_SERVER["REQUEST_URI"] = "/test";
        $_SERVER["REQUEST_METHOD"] = "GET";

        $router = new Router();
        $router->get("/test", function () {
            return "Hello, World!";
        })->middleware(TestNoActionMiddleware::class);

        $this->assertSame("Hello, World!", $router->dispatch());
    }

    public function testActionMiddleware()
    {
        $_SERVER["REQUEST_URI"] = "/test";
        $_SERVER["REQUEST_METHOD"] = "GET";

        $router = new Router();
        $router->get("/test", function () {
            return "Hello, World!";
        })->middleware(TestActionMiddleware::class);

        $this->assertSame("Hello, John!", $router->dispatch());
    }

    public function testMiddlewareWithParams()
    {
        $_SERVER["REQUEST_URI"] = "/hello/John";
        $_SERVER["REQUEST_METHOD"] = "GET";

        $router = new Router();
        $router->get("/hello/{who}", function () {
            return "Hello, World!";
        })->middleware(TestParamsMiddleware::class);

        $this->assertSame("Hello, John!", $router->dispatch());
    }
}
