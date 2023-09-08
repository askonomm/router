<?php

declare(strict_types=1);

namespace Asko\Router;

class Router
{
    private string $path;
    private string $method;
    private array $routes;

    public function __construct()
    {
        $this->path = $this->normalize_path($_SERVER["REQUEST_URI"]);
        $this->method = $_SERVER["REQUEST_METHOD"];
    }

    /**
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
            if ($this->match_path($route->path) &&
                $this->method === $route->method) {
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
    private function get_path_param(Route $route, string $name): ?string
    {
        $path = $this->normalize_path($route->path);
        $split_input_path = explode("/", $this->path);
        $split_route_path = explode("/", $path);
        $index = null;

        for($i = 0; $i < count($split_route_path); $i++) {
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
     * Undocumented function
     *
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
     * Undocumented function
     *
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
     * Undocumented function
     *
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
     * Undocumented function
     *
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
     * Undocumented function
     *
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
     * Undocumented function
     *
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
     * Undocumented function
     *
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
     * Undocumented function
     *
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
     * Undocumented function
     *
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
     * Undocumented function
     *
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

        // Compose injectables, which are classes typehinted in the method, 
        // and parameters, which are the actual values from the URL path.
        $non_injectables = ["string", "int", "float"];
        $injectables = [];
        $parameters = [];

        foreach($method_params as $method_param) {
            $param_type = $method_param->getType();

            if (!in_array($param_type->getName(), $non_injectables)) {
                $param_class_name = $param_type->getName();
                $injectables[] = new $param_class_name;
            } else {
                $parameters[] = $this->get_path_param($route, $method_param->getName());
            }
        }
        
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