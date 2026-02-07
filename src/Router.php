<?php

namespace App;

class Router
{
    private array $routes = [];

    public function get(string $path, array $handler): void
    {
        $this->routes['GET'][$path] = $handler;
    }

    public function post(string $path, array $handler): void
    {
        $this->routes['POST'][$path] = $handler;
    }

    public function dispatch(string $method, string $uri): void
    {
        // Strip query string
        $path = parse_url($uri, PHP_URL_PATH);

        // Remove trailing slash (except for root)
        if ($path !== '/' && substr($path, -1) === '/') {
            $path = rtrim($path, '/');
        }

        // Remove base path prefix if app is in a subdirectory
        $basePath = $this->getBasePath();
        if ($basePath && strpos($path, $basePath) === 0) {
            $path = substr($path, strlen($basePath)) ?: '/';
        }

        // Look up route
        if (isset($this->routes[$method][$path])) {
            $handler = $this->routes[$method][$path];
            [$class, $methodName] = $handler;
            $class::$methodName();
            return;
        }

        // 404
        http_response_code(404);
        if (strpos($path, '/api/') === 0) {
            json_response(['error' => 'Not found'], 404);
        } else {
            echo '<h1>404 - Seite nicht gefunden</h1>';
        }
    }

    private function getBasePath(): string
    {
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $dir = dirname($scriptName);
        return ($dir === '/' || $dir === '\\') ? '' : $dir;
    }
}
