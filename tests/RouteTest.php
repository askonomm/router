<?php 

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Asko\Router\Route;

class RouteTestController
{
    public function index() {}
}

final class RouteTest extends TestCase
{
    public function testGetRoute(): void
    {
        $route = new Route("/", RouteTestController::class, "index", "GET");

        $this->assertSame("/", $route->path);
        $this->assertSame("GET", $route->method);
        $this->assertSame(RouteTestController::class, $route->controller);
        $this->assertSame("index", $route->action);
    }

    public function testHeadRoute(): void
    {
        $route = new Route("/", RouteTestController::class, "index", "HEAD");

        $this->assertSame("/", $route->path);
        $this->assertSame("HEAD", $route->method);
        $this->assertSame(RouteTestController::class, $route->controller);
        $this->assertSame("index", $route->action);
    }

    public function testPostRoute(): void
    {
        $route = new Route("/", RouteTestController::class, "index", "POST");

        $this->assertSame("/", $route->path);
        $this->assertSame("POST", $route->method);
        $this->assertSame(RouteTestController::class, $route->controller);
        $this->assertSame("index", $route->action);
    }

    public function testPutRoute(): void
    {
        $route = new Route("/", RouteTestController::class, "index", "PUT");

        $this->assertSame("/", $route->path);
        $this->assertSame("PUT", $route->method);
        $this->assertSame(RouteTestController::class, $route->controller);
        $this->assertSame("index", $route->action);
    }

    public function testDeleteRoute(): void
    {
        $route = new Route("/", RouteTestController::class, "index", "DELETE");

        $this->assertSame("/", $route->path);
        $this->assertSame("DELETE", $route->method);
        $this->assertSame(RouteTestController::class, $route->controller);
        $this->assertSame("index", $route->action);
    }

    public function testPatchRoute(): void
    {
        $route = new Route("/", RouteTestController::class, "index", "PATCH");

        $this->assertSame("/", $route->path);
        $this->assertSame("PATCH", $route->method);
        $this->assertSame(RouteTestController::class, $route->controller);
        $this->assertSame("index", $route->action);
    }

    public function testOptionsRoute(): void
    {
        $route = new Route("/", RouteTestController::class, "index", "OPTIONS");

        $this->assertSame("/", $route->path);
        $this->assertSame("OPTIONS", $route->method);
        $this->assertSame(RouteTestController::class, $route->controller);
        $this->assertSame("index", $route->action);
    }

    public function testConnectRoute(): void
    {
        $route = new Route("/", RouteTestController::class, "index", "CONNECT");

        $this->assertSame("/", $route->path);
        $this->assertSame("CONNECT", $route->method);
        $this->assertSame(RouteTestController::class, $route->controller);
        $this->assertSame("index", $route->action);
    }

    public function testTraceRoute(): void
    {
        $route = new Route("/", RouteTestController::class, "index", "TRACE");

        $this->assertSame("/", $route->path);
        $this->assertSame("TRACE", $route->method);
        $this->assertSame(RouteTestController::class, $route->controller);
        $this->assertSame("index", $route->action);
    }
}

