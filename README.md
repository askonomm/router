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

$router = new Router();

$router->get("/hello/{who}", function(string $who) {
    echo "Hello, {$who}";
});

$router->dispatch();
```

All HTTP methods are supported: `get`, `head`, `post`, `put`, `delete`, `patch`, `options`, `trace`. To catch any HTTP method, use `any`.

To set a 404 handler, use the `not_found` method, which just takes a callable as its only argument.

### Callables

You can pass 3 different types of callables to the router.

#### Controllers

Controllers are classes which are mostly used for grouping routes that are similar together, so let's say you have Admin page routes, well, it would make sense to create a `AdminController` class for that, where each method in it represents a single route. To use controller classes with Router you need to pass an array as the callable, where the first item is the class constant and the second the name of the method, like so:

```php
use Asko\Router\Router;

class AdminController
{
    public function login()
    {
        echo "Login page goes here.";
    }
}

$router = new Router();
$router->get("/admin/login", [AdminController::class, "login"]);
$router->dispatch();
```

#### Functions

Functions are regular PHP functions where you pass the name of the function as the callable. This is useful if you want to do more functional style programming, like so:

```php
use Asko\Router\Router;

function hello_world() {
    echo "Hello, World!";
}

$router = new Router();
$router->get("/hello-world", "hello_world");
$router->dispatch();
```

#### Closures

You can also entirely forgo having named functions and instead use anonymous functions in the form of Closures, like so:

```php
use Asko\Router\Router;

$router = new Router();

$router->get("/hello-world", function() {
    echo "Hello, World!";
});

$router->dispatch();
```

### Parameters

Parameters in Router are named, and then used in the function (or method) declaration as arguments with those same names. So let's say you have this Route:

```php
$router->get("/hello/{who}", ...);
```

Then the argument name you need to refer to is also `$who`, like so:

```php
$router->get("/hello/{who}", function(string $who) {
    echo "Hello, {$who}!";
});
```

You can have as many parameters as you wish, and the order of which you have them in the function declaration does not matter. The only thing that matters is that the name matches the Route parameter.

**Note:** your parameters must be type hinted as either `string`, `int` or `float`. Leaving parameters untyped will assume the parameter is `string`.

### Dependency injection

Router has configuration-free dependency injection in the form of type hinting classes in the function (or method) declaration. Dependency injections must occur before Route parameters. An example injection looks like this:

```php
use Asko\Router\Router;

class SomeDependency {}

$router = new Router();

$router->get("/hello/{who}", function(SomeDependency $dep, string $who) {
    echo "Hello, {$who}";
});

$router->dispatch();
```

The above examples instantiates the `SomeDependency` class and injects it into the callable. All callable types are supported: Controller methods (and constructor methods!), functions and Closures.

All injections are also recursive in nature, which means that the injected classes can also benefit from configuration-free dependency injection by type hinting injections in their respective constructor methods.

### Middlewares

Middlewares are a way to run code before the actual route is dispatched. You can use middlewares to check if a user is authenticated, or if a user has the right permissions to access a route, etc. Middlewares are added to the router by using the `middleware` method, like so:

```php
use Asko\Router\Router;

class SomeMiddleware
{
    public function handle(string $who): string
    {
        return "intercepted, {$who}!";
    }
}

$router = new Router();

$router->get('/hello/{who}', function(string $who) {
   return "Hello, {$who}!";
})->middleware(SomeMiddleware::class);
```

When the above route is dispatched, the `SomeMiddleware` class will be instantiated and the `handle` method will be called. If the `handle` method returns anything other than `null`, the route will not be dispatched and the return value of the `handle` method will be returned instead.

The `handle` method of a middleware also fully supports dependency injection, and can make use of the same parameters as in the route itself. 

Additionally, you can pass multiple middleware classes by passing an array of classes to the `middleware` method, like so:

```php
$router->get('/', function() {
   return "Hello, World";
})->middleware([SomeMiddleware::class, AnotherMiddleware::class]);
```