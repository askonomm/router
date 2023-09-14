<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Asko\Router\Router;

class RouterTestDIDI
{
    public function test()
    {
        return "Hello from DI with DI";
    }
}

class RouterTestDIWithDI
{
    public function __construct(public RouterTestDIDI $di)
    {
    }
    public function test()
    {
        return $this->di->test();
    }
}

class RouterTestDI
{
    public function test()
    {
        return "Hello from DI";
    }
}

class RouterTestController
{
    private ?RouterTestDI $di = null;

    function __construct(RouterTestDI $di)
    {
        $this->di = $di;
    }

    public function index()
    {
        return "Hello, World!";
    }

    public function test_parameter(string $id)
    {
        return $id;
    }

    public function test_multiple_parameters(string $id, string $id2)
    {
        return $id . "." . $id2;
    }

    public function test_multiple_parameters2(string $id2, string $id)
    {
        return $id . "." . $id2;
    }

    public function test_di(RouterTestDI $di)
    {
        return $di->test();
    }

    public function test_di_with_parameter(RouterTestDI $di, int $id)
    {
        return $di->test() . ", " . $id;
    }

    public function test_di_from_constructor()
    {
        return $this->di->test();
    }

    public function test_di_di_from_constructor(RouterTestDIWithDI $di)
    {
        return $di->test();
    }
}

final class RouterTest extends TestCase
{
    public function testSimpleRoute(): void
    {
        $_SERVER["REQUEST_URI"] = "/";
        $_SERVER["REQUEST_METHOD"] = "GET";

        $router = new Router();
        $router->get("/", RouterTestController::class, "index");

        $this->assertSame("Hello, World!", $router->dispatch());
    }

    public function testSimpleRouteWithTrailingSlash(): void
    {
        $_SERVER["REQUEST_URI"] = "/test/";
        $_SERVER["REQUEST_METHOD"] = "GET";

        $router = new Router();
        $router->get("/test", RouterTestController::class, "index");

        $this->assertSame("Hello, World!", $router->dispatch());
    }

    public function testRouteWithParameters(): void
    {
        $_SERVER["REQUEST_URI"] = "/test/123";
        $_SERVER["REQUEST_METHOD"] = "GET";

        $router = new Router();
        $router->get("/test/{id}", RouterTestController::class, "test_parameter");

        $this->assertSame("123", $router->dispatch());
    }

    public function testRouteWithMultipleParameters(): void
    {
        $_SERVER["REQUEST_URI"] = "/test/123/and/456";
        $_SERVER["REQUEST_METHOD"] = "GET";

        $router = new Router();
        $router->get("/test/{id}/and/{id2}", RouterTestController::class, "test_multiple_parameters");

        $this->assertSame("123.456", $router->dispatch());
    }

    public function testRouteWithMultipleParametersDifferentOrder(): void
    {
        $_SERVER["REQUEST_URI"] = "/test/123/and/456";
        $_SERVER["REQUEST_METHOD"] = "GET";

        $router = new Router();
        $router->get("/test/{id}/and/{id2}", RouterTestController::class, "test_multiple_parameters2");

        $this->assertSame("123.456", $router->dispatch());
    }

    public function testRouteWithDI(): void
    {
        $_SERVER["REQUEST_URI"] = "/test";
        $_SERVER["REQUEST_METHOD"] = "GET";

        $router = new Router();
        $router->get("/test", RouterTestController::class, "test_di");

        $this->assertSame("Hello from DI", $router->dispatch());
    }

    public function testRouteWithDIAndParameters(): void
    {
        $_SERVER["REQUEST_URI"] = "/test/123";
        $_SERVER["REQUEST_METHOD"] = "GET";

        $router = new Router();
        $router->get("/test/{id}", RouterTestController::class, "test_di_with_parameter");

        $this->assertSame("Hello from DI, 123", $router->dispatch());
    }

    public function testRouteWithConstructorDI(): void
    {
        $_SERVER["REQUEST_URI"] = "/test";
        $_SERVER["REQUEST_METHOD"] = "GET";

        $router = new Router();
        $router->get("/test", RouterTestController::class, "test_di_from_constructor");

        $this->assertSame("Hello from DI", $router->dispatch());
    }

    public function testRouteWithConstructorDIDI(): void
    {
        $_SERVER["REQUEST_URI"] = "/test";
        $_SERVER["REQUEST_METHOD"] = "GET";

        $router = new Router();
        $router->get("/test", RouterTestController::class, "test_di_di_from_constructor");

        $this->assertSame("Hello from DI with DI", $router->dispatch());
    }

    public function testAnyRoute(): void
    {
        $_SERVER["REQUEST_URI"] = "/test";
        $_SERVER["REQUEST_METHOD"] = "POST";

        $router = new Router();
        $router->any("/test", RouterTestController::class, "index");

        $this->assertSame("Hello, World!", $router->dispatch());

        $_SERVER["REQUEST_URI"] = "/test";
        $_SERVER["REQUEST_METHOD"] = "GET";

        $router = new Router();
        $router->any("/test", RouterTestController::class, "index");

        $this->assertSame("Hello, World!", $router->dispatch());
    }
}
