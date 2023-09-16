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
        $route = new Route("/", [RouteTestController::class, "index"], "GET");

        $this->assertSame("/", $route->path);
        $this->assertSame("GET", $route->method);
        $this->assertSame(RouteTestController::class, $route->callable[0]);
        $this->assertSame("index", $route->callable[1]);
    }

    public function testHeadRoute(): void
    {
        $route = new Route("/", [RouteTestController::class, "index"], "HEAD");

        $this->assertSame("/", $route->path);
        $this->assertSame("HEAD", $route->method);
        $this->assertSame(RouteTestController::class, $route->callable[0]);
        $this->assertSame("index", $route->callable[1]);
    }

    public function testPostRoute(): void
    {
        $route = new Route("/", [RouteTestController::class, "index"], "POST");

        $this->assertSame("/", $route->path);
        $this->assertSame("POST", $route->method);
        $this->assertSame(RouteTestController::class, $route->callable[0]);
        $this->assertSame("index", $route->callable[1]);
    }

    public function testPutRoute(): void
    {
        $route = new Route("/", [RouteTestController::class, "index"], "PUT");

        $this->assertSame("/", $route->path);
        $this->assertSame("PUT", $route->method);
        $this->assertSame(RouteTestController::class, $route->callable[0]);
        $this->assertSame("index", $route->callable[1]);
    }

    public function testDeleteRoute(): void
    {
        $route = new Route("/", [RouteTestController::class, "index"], "DELETE");

        $this->assertSame("/", $route->path);
        $this->assertSame("DELETE", $route->method);
        $this->assertSame(RouteTestController::class, $route->callable[0]);
        $this->assertSame("index", $route->callable[1]);
    }

    public function testPatchRoute(): void
    {
        $route = new Route("/", [RouteTestController::class, "index"], "PATCH");

        $this->assertSame("/", $route->path);
        $this->assertSame("PATCH", $route->method);
        $this->assertSame(RouteTestController::class, $route->callable[0]);
        $this->assertSame("index", $route->callable[1]);
    }

    public function testOptionsRoute(): void
    {
        $route = new Route("/", [RouteTestController::class, "index"], "OPTIONS");

        $this->assertSame("/", $route->path);
        $this->assertSame("OPTIONS", $route->method);
        $this->assertSame(RouteTestController::class, $route->callable[0]);
        $this->assertSame("index", $route->callable[1]);
    }

    public function testConnectRoute(): void
    {
        $route = new Route("/", [RouteTestController::class, "index"], "CONNECT");

        $this->assertSame("/", $route->path);
        $this->assertSame("CONNECT", $route->method);
        $this->assertSame(RouteTestController::class, $route->callable[0]);
        $this->assertSame("index", $route->callable[1]);
    }

    public function testTraceRoute(): void
    {
        $route = new Route("/", [RouteTestController::class, "index"], "TRACE");

        $this->assertSame("/", $route->path);
        $this->assertSame("TRACE", $route->method);
        $this->assertSame(RouteTestController::class, $route->callable[0]);
        $this->assertSame("index", $route->callable[1]);
    }
}
