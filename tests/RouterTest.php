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

    public function not_found()
    {
        return "Not found.";
    }
}

final class RouterTest extends TestCase
{
    public function testSimpleGetRoute(): void
    {
        $_SERVER["REQUEST_URI"] = "/";
        $_SERVER["REQUEST_METHOD"] = "GET";

        $router = new Router();
        $router->get("/", [RouterTestController::class, "index"]);

        $this->assertSame("Hello, World!", $router->dispatch());
    }

    public function testSimpleHeadRoute(): void
    {
        $_SERVER["REQUEST_URI"] = "/";
        $_SERVER["REQUEST_METHOD"] = "HEAD";

        $router = new Router();
        $router->head("/", [RouterTestController::class, "index"]);

        $this->assertSame("Hello, World!", $router->dispatch());
    }

    public function testSimplePostRoute(): void
    {
        $_SERVER["REQUEST_URI"] = "/";
        $_SERVER["REQUEST_METHOD"] = "POST";

        $router = new Router();
        $router->post("/", [RouterTestController::class, "index"]);

        $this->assertSame("Hello, World!", $router->dispatch());
    }

    public function testSimplePutRoute(): void
    {
        $_SERVER["REQUEST_URI"] = "/";
        $_SERVER["REQUEST_METHOD"] = "PUT";

        $router = new Router();
        $router->put("/", [RouterTestController::class, "index"]);

        $this->assertSame("Hello, World!", $router->dispatch());
    }

    public function testSimpleDeleteRoute(): void
    {
        $_SERVER["REQUEST_URI"] = "/";
        $_SERVER["REQUEST_METHOD"] = "DELETE";

        $router = new Router();
        $router->delete("/", [RouterTestController::class, "index"]);

        $this->assertSame("Hello, World!", $router->dispatch());
    }

    public function testSimplePatchRoute(): void
    {
        $_SERVER["REQUEST_URI"] = "/";
        $_SERVER["REQUEST_METHOD"] = "PATCH";

        $router = new Router();
        $router->patch("/", [RouterTestController::class, "index"]);

        $this->assertSame("Hello, World!", $router->dispatch());
    }

    public function testSimpleOptionsRoute(): void
    {
        $_SERVER["REQUEST_URI"] = "/";
        $_SERVER["REQUEST_METHOD"] = "OPTIONS";

        $router = new Router();
        $router->options("/", [RouterTestController::class, "index"]);

        $this->assertSame("Hello, World!", $router->dispatch());
    }

    public function testSimpleTraceRoute(): void
    {
        $_SERVER["REQUEST_URI"] = "/";
        $_SERVER["REQUEST_METHOD"] = "TRACE";

        $router = new Router();
        $router->trace("/", [RouterTestController::class, "index"]);

        $this->assertSame("Hello, World!", $router->dispatch());
    }

    public function testSimpleRouteWithTrailingSlash(): void
    {
        $_SERVER["REQUEST_URI"] = "/test/";
        $_SERVER["REQUEST_METHOD"] = "GET";

        $router = new Router();
        $router->get("/test", [RouterTestController::class, "index"]);

        $this->assertSame("Hello, World!", $router->dispatch());
    }

    public function testRouteWithParameters(): void
    {
        $_SERVER["REQUEST_URI"] = "/test/123";
        $_SERVER["REQUEST_METHOD"] = "GET";

        $router = new Router();
        $router->get("/test/{id}", [RouterTestController::class, "test_parameter"]);

        $this->assertSame("123", $router->dispatch());
    }

    public function testRouteWithMultipleParameters(): void
    {
        $_SERVER["REQUEST_URI"] = "/test/123/and/456";
        $_SERVER["REQUEST_METHOD"] = "GET";

        $router = new Router();
        $router->get("/test/{id}/and/{id2}", [RouterTestController::class, "test_multiple_parameters"]);

        $this->assertSame("123.456", $router->dispatch());
    }

    public function testRouteWithMultipleParametersDifferentOrder(): void
    {
        $_SERVER["REQUEST_URI"] = "/test/123/and/456";
        $_SERVER["REQUEST_METHOD"] = "GET";

        $router = new Router();
        $router->get("/test/{id}/and/{id2}", [RouterTestController::class, "test_multiple_parameters2"]);

        $this->assertSame("123.456", $router->dispatch());
    }

    public function testRouteWithDI(): void
    {
        $_SERVER["REQUEST_URI"] = "/test";
        $_SERVER["REQUEST_METHOD"] = "GET";

        $router = new Router();
        $router->get("/test", [RouterTestController::class, "test_di"]);

        $this->assertSame("Hello from DI", $router->dispatch());
    }

    public function testRouteWithDIAndParameters(): void
    {
        $_SERVER["REQUEST_URI"] = "/test/123";
        $_SERVER["REQUEST_METHOD"] = "GET";

        $router = new Router();
        $router->get("/test/{id}", [RouterTestController::class, "test_di_with_parameter"]);

        $this->assertSame("Hello from DI, 123", $router->dispatch());
    }

    public function testRouteWithConstructorDI(): void
    {
        $_SERVER["REQUEST_URI"] = "/test";
        $_SERVER["REQUEST_METHOD"] = "GET";

        $router = new Router();
        $router->get("/test", [RouterTestController::class, "test_di_from_constructor"]);

        $this->assertSame("Hello from DI", $router->dispatch());
    }

    public function testRouteWithConstructorDIDI(): void
    {
        $_SERVER["REQUEST_URI"] = "/test";
        $_SERVER["REQUEST_METHOD"] = "GET";

        $router = new Router();
        $router->get("/test", [RouterTestController::class, "test_di_di_from_constructor"]);

        $this->assertSame("Hello from DI with DI", $router->dispatch());
    }

    public function testAnyRoute(): void
    {
        $_SERVER["REQUEST_URI"] = "/test";
        $_SERVER["REQUEST_METHOD"] = "POST";

        $router = new Router();
        $router->any("/test", [RouterTestController::class, "index"]);

        $this->assertSame("Hello, World!", $router->dispatch());

        $_SERVER["REQUEST_URI"] = "/test";
        $_SERVER["REQUEST_METHOD"] = "GET";

        $router = new Router();
        $router->any("/test", [RouterTestController::class, "index"]);

        $this->assertSame("Hello, World!", $router->dispatch());
    }

    public function testNotFoundRoute(): void
    {
        $_SERVER["REQUEST_URI"] = "/test";
        $_SERVER["REQUEST_METHOD"] = "GET";

        $router = new Router();
        $router->notFound([RouterTestController::class, "not_found"]);

        $this->assertSame("Not found.", $router->dispatch());
    }

    public function testClosureRoute(): void
    {
        $_SERVER["REQUEST_URI"] = "/test";
        $_SERVER["REQUEST_METHOD"] = "GET";

        $router = new Router();
        $router->get("/test", function () {
            return "Hello, World!";
        });

        $this->assertSame("Hello, World!", $router->dispatch());
    }

    public function testClosureRouteWithParameter(): void
    {
        $_SERVER["REQUEST_URI"] = "/hello/world";
        $_SERVER["REQUEST_METHOD"] = "GET";

        $router = new Router();
        $router->get("/hello/{who}", function (string $who) {
            return "Hello, {$who}!";
        });

        $this->assertSame("Hello, world!", $router->dispatch());
    }

    public function testClosureRouteWithDI(): void
    {
        $_SERVER["REQUEST_URI"] = "/test";
        $_SERVER["REQUEST_METHOD"] = "GET";

        $router = new Router();
        $router->get("/test", function (RouterTestDI $di) {
            return $di->test();
        });

        $this->assertSame("Hello from DI", $router->dispatch());
    }

    public function testClosureRouteWithDIAndParameter(): void
    {
        $_SERVER["REQUEST_URI"] = "/test/123";
        $_SERVER["REQUEST_METHOD"] = "GET";

        $router = new Router();
        $router->get("/test/{id}", function (RouterTestDI $di, int $id) {
            return $di->test() . ", " . $id;
        });

        $this->assertSame("Hello from DI, 123", $router->dispatch());
    }

    public function testFunctionRoute(): void
    {
        $_SERVER["REQUEST_URI"] = "/test";
        $_SERVER["REQUEST_METHOD"] = "GET";

        function test_function()
        {
            return "Hello, World!";
        }

        $router = new Router();
        $router->get("/test", "test_function");

        $this->assertSame("Hello, World!", $router->dispatch());
    }

    public function testNoControllerFound(): void
    {
        $_SERVER["REQUEST_URI"] = "/test";
        $_SERVER["REQUEST_METHOD"] = "GET";

        $router = new Router();

        $this->expectExceptionMessage("Class /SomeControllerGoesHere not found.");
        $router->get("/test", ["/SomeControllerGoesHere", "not_found"]);
        $router->dispatch();
    }

    public function testNoControllerMethodFound(): void
    {
        $_SERVER["REQUEST_URI"] = "/test";
        $_SERVER["REQUEST_METHOD"] = "GET";

        $router = new Router();

        $this->expectExceptionMessage("Method RouteTestController::not_found not found.");
        $router->get("/test", [RouteTestController::class, "not_found"]);
        $router->dispatch();
    }

    public function testNoFunctionFound(): void
    {
        $_SERVER["REQUEST_URI"] = "/test";
        $_SERVER["REQUEST_METHOD"] = "GET";

        $router = new Router();

        $this->expectExceptionMessage("Function test_fn not found.");
        $router->get("/test", "test_fn");
        $router->dispatch();
    }

    public function testNoRoutesFound(): void
    {
        $_SERVER["REQUEST_URI"] = "/test";
        $_SERVER["REQUEST_METHOD"] = "GET";

        $router = new Router();

        $this->assertSame(null, $router->dispatch());
    }

    public function testNoRouteFound(): void
    {
        $_SERVER["REQUEST_URI"] = "/test";
        $_SERVER["REQUEST_METHOD"] = "GET";

        $router = new Router();
        $router->get("/", function () {
            return "Hello, World!";
        });

        $this->assertSame(null, $router->dispatch());
    }

    public function testNoTypeHintedParameter(): void
    {
        $_SERVER["REQUEST_URI"] = "/test/123";
        $_SERVER["REQUEST_METHOD"] = "GET";

        $router = new Router();
        $router->get("/test/{id}", function ($id) {
            return $id;
        });

        $this->assertSame("123", $router->dispatch());
    }
}
