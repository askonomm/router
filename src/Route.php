<?php

declare(strict_types=1);

namespace Asko\Router;

/**
 * A Route encapsulation class for the Router.
 */
readonly class Route
{
    public function __construct(
        public string $path,
        public string $controller,
        public string $action,
        public string $method,
    ) {}
}