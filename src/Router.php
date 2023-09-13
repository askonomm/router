<?php

declare(strict_types=1);

namespace Asko\Router;

class Router
{
    private string $path;
    private string $method;
    private array $routes;
    private array $non_injectables = ["string", "int", "float"];

    public function __construct()
    {
        $this->path = $this->normalize_path($_SERVER["REQUEST_URI"]);
        $this->method = $_SERVER["REQUEST_METHOD"];
    }

    /**
     * Normalizes the path by removing leading and trailing slashes.
     * 
     * @param string $path
     * @return string
     */
    private function normalize_path(string $path): string
    {
        return trim($path, "/");
    }

    /**
     * @param string $path
     * @return boolean
     */
    private function match_path(string $path): bool
    {
        $path = $this->normalize_path($path);
        $split_input_path = explode("/", $this->path);
        $split_route_path = explode("/", $path);

        if (count($split_input_path) !== count($split_route_path)) {
            return false;
        }

        $matched_parts_count = 0;

        for ($i = 0; $i < count($split_input_path); $i++) {
            $input_path_part = $split_input_path[$i];
            $route_path_part = $split_route_path[$i];

            // Required parameter check
            if (
                str_starts_with($route_path_part, "{") &&
                str_ends_with($route_path_part, "}") &&
                strlen($input_path_part) > 0
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

        return $matched_parts_count === count($split_input_path);
    }

    /**
     * @return Route|null
     */
    private function match(): ?Route
    {
        foreach ($this->routes as $route) {
            $match_method = $route->method === $this->method || $route->method === "*";

            if ($this->match_path($route->path) && $match_method) {
                return $route;
            }

            continue;
        }

        return null;
    }

    /**
     * @param Route $route
     * @param string $name
     * @return string|null
     */
    private function get_path_param(Route $route, string $name): ?string
    {
        $path = $this->normalize_path($route->path);
        $split_input_path = explode("/", $this->path);
        $split_route_path = explode("/", $path);
        $index = null;

        for ($i = 0; $i < count($split_route_path); $i++) {
            $route_path_part = $split_route_path[$i];

            if (
                str_starts_with($route_path_part, "{") &&
                str_ends_with($route_path_part, "}") &&
                $name === trim($route_path_part, "{}")
            ) {
                $index = $i;
                break;
            }
        }

        return $split_input_path[$index] ?? null;
    }

    /**
     * @param Route $route
     * @return array
     */
    private function get_method_params(Route $route): array
    {
        $reflection = new \ReflectionClass($route->controller);

        try {
            return $reflection->getMethod($route->action)->getParameters();
        } catch (\ReflectionException) {
            return [];
        }
    }

    /**
     * @param array $method_params
     * @return array
     */
    private function compose_injectables(array $method_params): array
    {
        $injectables = [];

        foreach ($method_params as $param) {
            $param_type = $param->getType();

            if (!in_array($param_type->getName(), $this->non_injectables)) {
                $param_class_name = $param_type->getName();

                // Thought: recursive constructor DI is technically possible, 
                // should we do it?
                $injectables[] = new $param_class_name;
            }
        }

        return $injectables;
    }

    /**
     * @param Route $route
     * @param array $method_params
     * @return array
     */
    private function compose_parameters(Route $route, array $method_params): array
    {
        $parameters = [];

        foreach ($method_params as $method_param) {
            $param_type = $method_param->getType();

            if (in_array($param_type->getName(), $this->non_injectables)) {
                $parameters[] = $this->get_path_param($route, $method_param->getName());
            }
        }

        return $parameters;
    }

    /**
     * @param string $path
     * @param string $controller
     * @param string $action
     * @return void
     */
    public function get(string $path, string $controller, string $action): void
    {
        $this->routes[] = new Route($path, $controller, $action, "GET");
    }

    /**
     * @param string $path
     * @param string $controller
     * @param string $action
     * @return void
     */
    public function head(string $path, string $controller, string $action): void
    {
        $this->routes[] = new Route($path, $controller, $action, "HEAD");
    }

    /**
     * @param string $path
     * @param string $controller
     * @param string $action
     * @return void
     */
    public function post(string $path, string $controller, string $action): void
    {
        $this->routes[] = new Route($path, $controller, $action, "POST");
    }

    /**
     * @param string $path
     * @param string $controller
     * @param string $action
     * @return void
     */
    public function put(string $path, string $controller, string $action): void
    {
        $this->routes[] = new Route($path, $controller, $action, "PUT");
    }

    /**
     * @param string $path
     * @param string $controller
     * @param string $action
     * @return void
     */
    public function delete(string $path, string $controller, string $action): void
    {
        $this->routes[] = new Route($path, $controller, $action, "DELETE");
    }

    /**
     * @param string $path
     * @param string $controller
     * @param string $action
     * @return void
     */
    public function patch(string $path, string $controller, string $action): void
    {
        $this->routes[] = new Route($path, $controller, $action, "PATCH");
    }

    /**
     * @param string $path
     * @param string $controller
     * @param string $action
     * @return void
     */
    public function options(string $path, string $controller, string $action): void
    {
        $this->routes[] = new Route($path, $controller, $action, "OPTIONS");
    }

    /**
     * @param string $path
     * @param string $controller
     * @param string $action
     * @return void
     */
    public function trace(string $path, string $controller, string $action): void
    {
        $this->routes[] = new Route($path, $controller, $action, "TRACE");
    }

    /**
     * @param string $path
     * @param string $controller
     * @param string $action
     * @return void
     */
    public function any(string $path, string $controller, string $action): void
    {
        $this->routes[] = new Route($path, $controller, $action, "*");
    }

    /**
     * @return void
     */
    public function dispatch(): mixed
    {
        // There are no routes
        if (empty($this->routes)) {
            return null;
        }

        $route = $this->match();

        // Route not found
        if (!$route) {
            return null;
        }

        // Controller not found
        if (!class_exists($route->controller)) {
            return null;
        }

        $method_params = $this->get_method_params($route);
        $injectables = $this->compose_injectables($method_params);
        $parameters = $this->compose_parameters($route, $method_params);
        $controller_instance = new $route->controller;

        // Method not found
        if (!is_callable([$controller_instance, $route->action])) {
            return null;
        }

        return call_user_func_array(
            [$controller_instance, $route->action],
            [...$injectables, ...$parameters]
        );
    }
}
