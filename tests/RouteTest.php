<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Asko\Router\Route;

class RouteTestController
{
    public function index()
    {
    }
}

final class RouteTest extends TestCase
{
    public function testGetRoute(): void
    {
        $route = new Route(
            path: "/",
            callable: [RouteTestController::class, "index"],
            method: "GET",
            middlewares: []
        );

        $this->assertSame("/", $route->path);
        $this->assertSame("GET", $route->method);
        $this->assertSame(RouteTestController::class, $route->callable[0]);
        $this->assertSame("index", $route->callable[1]);
        $this->assertSame([], $route->middlewares);
    }

    public function testHeadRoute(): void
    {
        $route = new Route(
            path: "/",
            callable: [RouteTestController::class, "index"],
            method: "HEAD",
            middlewares: []
        );

        $this->assertSame("/", $route->path);
        $this->assertSame("HEAD", $route->method);
        $this->assertSame(RouteTestController::class, $route->callable[0]);
        $this->assertSame("index", $route->callable[1]);
    }

    public function testPostRoute(): void
    {
        $route = new Route(
            path: "/",
            callable: [RouteTestController::class, "index"],
            method: "POST",
            middlewares: []
        );

        $this->assertSame("/", $route->path);
        $this->assertSame("POST", $route->method);
        $this->assertSame(RouteTestController::class, $route->callable[0]);
        $this->assertSame("index", $route->callable[1]);
    }

    public function testPutRoute(): void
    {
        $route = new Route(
            path: "/",
            callable: [RouteTestController::class, "index"],
            method: "PUT",
            middlewares: []
        );

        $this->assertSame("/", $route->path);
        $this->assertSame("PUT", $route->method);
        $this->assertSame(RouteTestController::class, $route->callable[0]);
        $this->assertSame("index", $route->callable[1]);
    }

    public function testDeleteRoute(): void
    {
        $route = new Route(
            path: "/",
            callable: [RouteTestController::class, "index"],
            method: "DELETE",
            middlewares: []
        );

        $this->assertSame("/", $route->path);
        $this->assertSame("DELETE", $route->method);
        $this->assertSame(RouteTestController::class, $route->callable[0]);
        $this->assertSame("index", $route->callable[1]);
    }

    public function testPatchRoute(): void
    {
        $route = new Route(
            path: "/",
            callable: [RouteTestController::class, "index"],
            method: "PATCH",
            middlewares: []
        );

        $this->assertSame("/", $route->path);
        $this->assertSame("PATCH", $route->method);
        $this->assertSame(RouteTestController::class, $route->callable[0]);
        $this->assertSame("index", $route->callable[1]);
    }

    public function testOptionsRoute(): void
    {
        $route = new Route(
            path: "/",
            callable: [RouteTestController::class, "index"],
            method: "OPTIONS",
            middlewares: []
        );

        $this->assertSame("/", $route->path);
        $this->assertSame("OPTIONS", $route->method);
        $this->assertSame(RouteTestController::class, $route->callable[0]);
        $this->assertSame("index", $route->callable[1]);
    }

    public function testConnectRoute(): void
    {
        $route = new Route(
            path: "/",
            callable: [RouteTestController::class, "index"],
            method: "CONNECT",
            middlewares: []
        );

        $this->assertSame("/", $route->path);
        $this->assertSame("CONNECT", $route->method);
        $this->assertSame(RouteTestController::class, $route->callable[0]);
        $this->assertSame("index", $route->callable[1]);
    }

    public function testTraceRoute(): void
    {
        $route = new Route(
            path: "/",
            callable: [RouteTestController::class, "index"],
            method: "TRACE",
            middlewares: []
        );

        $this->assertSame("/", $route->path);
        $this->assertSame("TRACE", $route->method);
        $this->assertSame(RouteTestController::class, $route->callable[0]);
        $this->assertSame("index", $route->callable[1]);
    }
}
