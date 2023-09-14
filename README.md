# Router

[![codecov](https://codecov.io/gh/askonomm/router/graph/badge.svg?token=7UTJN1TK6S)](https://codecov.io/gh/askonomm/router)

A PHP router with built-in configuration-free dependency injection.

## Install

```
composer require asko/router
```

## Usage

The most basic usage example looks like this:

```php
use Asko\Router\Router;

class ExampleController
{
    public function hello(string $who)
    {
        echo "Hello, {$who}";
    }
}

$router = new Router();
$router->get("/hello/{who}", ExampleController::class, "hello");
$router->dispatch();
```

All HTTP methods are supported: `get`, `head`, `post`, `put`, `delete`, `patch`, `options`, `trace`. To catch any HTTP method, use `any`.

### Dependency injection

To inject dependencies into your controller methods (including constructor methods!), simply prepend them to the method and typehint them, for example:

```php
use Asko\Router\Router;

class SomeDependency {}

class ExampleController
{
    public function hello(SomeDependency $dep, string $who)
    {
        echo "Hello, {$who}";
    }
}

$router = new Router();
$router->get("/hello/{who}", ExampleController::class, "hello");
$router->dispatch();
```

And then the `SomeDependency` class will be automatically injected. Injections have to always be before the route parameters, but route parameters themselves have no importance of order. They can be in any order, as long as the names of the parameters match the name defined in the route such that `{who}` is `$who` in the method definition.

The dependencies you inject can also have dependencies of their own that inject in the same way - dependency injection with Router is recursive, so it goes as deep as you want.
