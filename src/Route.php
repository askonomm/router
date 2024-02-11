<?php

declare(strict_types=1);

namespace Asko\Router;

use Closure;

/**
 * A Route encapsulation class for the Router.
 */
class Route
{
    public function __construct(
        public string $path,
        public string|array|Closure $callable,
        public string $method,
        public array $middlewares,
    ) {
    }
}
