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
$router->get("/admin/login", [ExampleController::class, "hello"]);
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
