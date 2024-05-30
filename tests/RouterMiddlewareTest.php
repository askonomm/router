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
    /**
     * @throws Exception
     */
    public function testNoActionMiddleware()
    {
        $_SERVER["REQUEST_URI"] = "/test";
        $_SERVER["REQUEST_METHOD"] = "GET";

        $router = new Router();

        $router->get(
            path: "/test",
            callable: fn () => "Hello, World!",
            middlewares: [TestNoActionMiddleware::class]
        );

        $this->assertSame("Hello, World!", $router->dispatch());
    }

    /**
     * @throws Exception
     */
    public function testActionMiddleware()
    {
        $_SERVER["REQUEST_URI"] = "/test";
        $_SERVER["REQUEST_METHOD"] = "GET";

        $router = new Router();

        $router->get(
            path: "/test",
            callable: fn () => "Hello, World!",
            middlewares: [TestActionMiddleware::class]
        );

        $this->assertSame("Hello, John!", $router->dispatch());
    }

    /**
     * @throws Exception
     */
    public function testMiddlewareWithParams()
    {
        $_SERVER["REQUEST_URI"] = "/hello/John";
        $_SERVER["REQUEST_METHOD"] = "GET";

        $router = new Router();

        $router->get(
            path: "/hello/{who}",
            callable: fn () => "Hello, World!",
            middlewares: [TestParamsMiddleware::class]
        );

        $this->assertSame("Hello, John!", $router->dispatch());
    }
}
