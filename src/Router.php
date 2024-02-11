<?php

declare(strict_types=1);

namespace Asko\Router;

use Closure;
use ReflectionException;

class Router
{
    private string $path;
    private string $method;
    private array $routes = [];
    private ?Route $notFoundRoute = null;
    private array $nonInjectables = ["string", "int", "float", "array"];

    public function __construct()
    {
        $this->path = $this->normalizePath($_SERVER["REQUEST_URI"]);
        $this->method = $_SERVER["REQUEST_METHOD"];
    }

    /**
     * Normalizes the path by removing leading and trailing slashes.
     * 
     * @param string $path
     * @return string
     */
    private function normalizePath(string $path): string
    {
        return trim($path, "/");
    }

    /**
     * @param string $path
     * @return boolean
     */
    private function matchPath(string $path): bool
    {
        $path = $this->normalizePath($path);
        $split_input_path = \explode("/", $this->path);
        $split_route_path = \explode("/", $path);

        if (\count($split_input_path) !== \count($split_route_path)) {
            return false;
        }

        $matched_parts_count = 0;

        for ($i = 0; $i < \count($split_input_path); $i++) {
            $input_path_part = $split_input_path[$i];
            $route_path_part = $split_route_path[$i];

            // Required parameter check
            if (
                \str_starts_with($route_path_part, "{") &&
                \str_ends_with($route_path_part, "}") &&
                \strlen($input_path_part) > 0
            ) {
                $matched_parts_count++;
                continue;
            }

            // 1:1 match
            else {
                if ($input_path_part === $route_path_part) {
                    $matched_parts_count++;
                    continue;
                }
            }
        }

        return $matched_parts_count === \count($split_input_path);
    }

    /**
     * @return Route|null
     */
    private function match(): ?Route
    {
        foreach ($this->routes as $route) {
            $match_method = $route->method === $this->method || $route->method === "*";

            if ($this->matchPath($route->path) && $match_method) {
                return $route;
            }
        }

        return null;
    }

    /**
     * @param Route $route
     * @param string $name
     * @return string|null
     */
    private function getPathParam(Route $route, string $name): ?string
    {
        $path = $this->normalizePath($route->path);
        $split_input_path = \explode("/", $this->path);
        $split_route_path = \explode("/", $path);
        $index = null;

        for ($i = 0; $i < \count($split_route_path); $i++) {
            $route_path_part = $split_route_path[$i];

            if (
                \str_starts_with($route_path_part, "{") &&
                \str_ends_with($route_path_part, "}") &&
                $name === \trim($route_path_part, "{}")
            ) {
                $index = $i;
                break;
            }
        }

        return $split_input_path[$index] ?? null;
    }

    /**
     * @param string $class
     * @return array
     * @throws ReflectionException
     */
    private function getConstructorParams(string $class): array
    {
        $reflection = new \ReflectionClass($class);

        if ($reflection->getConstructor()) {
            return $reflection->getConstructor()->getParameters();
        }

        return [];
    }

    /**
     * @param string $class
     * @param string $method
     * @return array
     * @throws ReflectionException
     */
    private function getMethodParams(string $class, string $method): array
    {
        $reflection = new \ReflectionClass($class);

        return $reflection->getMethod($method)->getParameters();
    }

    /**
     * @param string|Closure $fn
     * @return array
     * @throws ReflectionException
     */
    private function getFnParams(string|Closure $fn): array
    {
        $reflection = new \ReflectionFunction($fn);

        return $reflection->getParameters();
    }

    /**
     * @param array $method_params
     * @return array
     * @throws ReflectionException
     */
    private function composeInjectables(array $method_params): array
    {
        $injectables = [];

        foreach ($method_params as $param) {
            $param_type = $param->getType();

            if ($param_type && !\in_array($param_type->getName(), $this->nonInjectables)) {
                $injectables[] = $this->initClass($param_type->getName());
            }
        }

        return $injectables;
    }

    /**
     * @param Route $route
     * @param array $method_params
     * @return array
     */
    private function composeParameters(Route $route, array $method_params): array
    {
        $parameters = [];

        foreach ($method_params as $method_param) {
            $param_type = $method_param->getType();

            if (!$param_type || \in_array($param_type->getName(), $this->nonInjectables)) {
                $parameters[] = $this->getPathParam($route, $method_param->getName());
            }
        }

        return $parameters;
    }

    /**
     * @param string $class
     * @return object
     * @throws ReflectionException
     */
    private function initClass(string $class): object
    {
        $constructor_params = $this->getConstructorParams($class);
        $injectables = $this->composeInjectables($constructor_params);

        if (empty($injectables)) {
            return new $class;
        }

        $controller_class = new \ReflectionClass($class);

        return $controller_class->newInstanceArgs($injectables);
    }

    /**
     * @param string $path
     * @param string|array|Closure $callable
     * @return void
     */
    public function get(string $path, string|array|Closure $callable): void
    {
        $this->routes[] = new Route(
            path: $path,
            callable: $callable,
            method: "GET"
        );
    }

    /**
     * @param string $path
     * @param string|array|Closure $callable
     * @return void
     */
    public function head(string $path, string|array|Closure $callable): void
    {
        $this->routes[] = new Route(
            path: $path,
            callable: $callable,
            method: "HEAD"
        );
    }

    /**
     * @param string $path
     * @param string|array|Closure $callable
     * @return void
     */
    public function post(string $path, string|array|Closure $callable): void
    {
        $this->routes[] = new Route(
            path: $path,
            callable: $callable,
            method: "POST"
        );
    }

    /**
     * @param string $path
     * @param string|array|Closure $callable
     * @return void
     */
    public function put(string $path, string|array|Closure $callable): void
    {
        $this->routes[] = new Route(
            path: $path,
            callable: $callable,
            method: "PUT"
        );
    }

    /**
     * @param string $path
     * @param string|array|Closure $callable
     * @return void
     */
    public function delete(string $path, string|array|Closure $callable): void
    {
        $this->routes[] = new Route(
            path: $path,
            callable: $callable,
            method: "DELETE"
        );
    }

    /**
     * @param string $path
     * @param string|array|Closure $callable
     * @return void
     */
    public function patch(string $path, string|array|Closure $callable): void
    {
        $this->routes[] = new Route(
            path: $path,
            callable: $callable,
            method: "PATCH"
        );
    }

    /**
     * @param string $path
     * @param string|array|Closure $callable
     * @return void
     */
    public function options(string $path, string|array|Closure $callable): void
    {
        $this->routes[] = new Route(
            path: $path,
            callable: $callable,
            method: "OPTIONS"
        );
    }

    /**
     * @param string $path
     * @param string|array|Closure $callable
     * @return void
     */
    public function trace(string $path, string|array|Closure $callable): void
    {
        $this->routes[] = new Route(
            path: $path,
            callable: $callable,
            method: "TRACE"
        );
    }

    /**
     * @param string $path
     * @param string|array|Closure $callable
     * @return void
     */
    public function any(string $path, string|array|Closure $callable): void
    {
        $this->routes[] = new Route(
            path: $path,
            callable: $callable,
            method: "*"
        );
    }

    /**
     * @param string|array|Closure $callable
     * @return void
     */
    public function notFound(string|array|Closure $callable): void
    {
        $this->notFoundRoute = new Route(
            path: "",
            callable: $callable,
            method: "*"
        );
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function dispatch(): mixed
    {
        // There are no routes
        if (empty($this->routes) && !$this->notFoundRoute) {
            return null;
        }

        $route = $this->match();

        // Route not found
        if (!$route) {
            if ($this->notFoundRoute) {
                $route = $this->notFoundRoute;
            } else {
                return null;
            }
        }

        // If we have a controller, check if it exists
        if (is_array($route->callable) && !class_exists($route->callable[0])) {
            throw new \Exception("Class {$route->callable[0]} not found.");
        }

        // if we have a controller, check if its method exists
        if (is_array($route->callable) && !method_exists($route->callable[0], $route->callable[1])) {
            throw new \Exception("Method {$route->callable[0]}::{$route->callable[1]} not found.");
        }

        // And if both exist, we proceed with controller and its method
        if (is_array($route->callable)) {
            [$controller, $action] = $route->callable;
            $controller_instance = $this->initClass($controller);
            $method_params = $this->getMethodParams($controller, $action);

            return call_user_func_array(
                [$controller_instance, $action],
                [
                    ...$this->composeInjectables($method_params),
                    ...$this->composeParameters($route, $method_params)
                ]
            );
        }

        // If we have a function name, check if it exists
        if (is_string($route->callable) && !function_exists($route->callable)) {
            throw new \Exception("Function {$route->callable} not found.");
        }

        // And if it exists, we proceed with the function
        if (is_string($route->callable)) {
            $fn_params = $this->getFnParams($route->callable);

            return call_user_func_array(
                $route->callable,
                [
                    ...$this->composeInjectables($fn_params),
                    ...$this->composeParameters($route, $fn_params)
                ]
            );
        }

        // If we have a closure, we proceed with the closure
        if ($route->callable instanceof \Closure) {
            $fn_params = $this->getFnParams($route->callable);

            return call_user_func_array(
                $route->callable,
                [
                    ...$this->composeInjectables($fn_params),
                    ...$this->composeParameters($route, $fn_params)
                ]
            );
        }
    }
}
