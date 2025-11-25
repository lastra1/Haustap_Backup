<?php
namespace Core;

final class Router {
    private array $routes = ['GET' => [], 'POST' => []];

    public function get(string $path, callable $handler): void { $this->routes['GET'][$path] = $handler; }
    public function post(string $path, callable $handler): void { $this->routes['POST'][$path] = $handler; }

    public function dispatch(string $uri, string $method): bool {
        // Normalize path: strip query and trailing slash (except for root)
        $raw = parse_url($uri, PHP_URL_PATH) ?: '/';
        $path = ($raw !== '/' && str_ends_with($raw, '/')) ? rtrim($raw, '/') : $raw;

        // 1) Exact match first
        if (isset($this->routes[$method][$path])) {
            ($this->routes[$method][$path])();
            return true;
        }

        // 2) Wildcard prefix match: pattern ending with "/*" matches any subpath
        foreach ($this->routes[$method] as $pattern => $handler) {
            if (!is_string($pattern)) { continue; }
            if ($pattern === $path) { continue; } // already handled exact above
            if (str_ends_with($pattern, '/*')) {
                $prefix = substr($pattern, 0, -2); // remove /*
                if ($prefix === '') { continue; }
                // Allow both exact prefix and deeper paths
                if ($path === $prefix || str_starts_with($path, $prefix . '/')) {
                    ($handler)();
                    return true;
                }
            }
        }

        return false;
    }
}
