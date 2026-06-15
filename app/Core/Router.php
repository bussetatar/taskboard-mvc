<?php

declare(strict_types=1);

namespace App\Core;

use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;

final class Router
{
    private array $routes = [];

    public function get(string $path, array $handler, array $middleware = []): void
    {
        $this->add('GET', $path, $handler, $middleware);
    }

    public function post(string $path, array $handler, array $middleware = []): void
    {
        $this->add('POST', $path, $handler, $middleware);
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        $path = $path !== '/' ? rtrim($path, '/') : '/';

        if ($method === 'POST' && !Csrf::validate($_POST['_token'] ?? null)) {
            http_response_code(419);
            (new Controller())->render('errors/419', ['title' => 'Session expired']);
            return;
        }

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $parameters = $this->match($route['path'], $path);

            if ($parameters === null) {
                continue;
            }

            $this->runMiddleware($route['middleware']);

            [$controllerClass, $action] = $route['handler'];
            $controller = new $controllerClass();
            $controller->{$action}(...array_values($parameters));
            return;
        }

        http_response_code(404);
        (new Controller())->render('errors/404', ['title' => 'Page not found']);
    }

    private function add(string $method, string $path, array $handler, array $middleware): void
    {
        $this->routes[] = compact('method', 'path', 'handler', 'middleware');
    }

    private function match(string $routePath, string $requestPath): ?array
    {
        $pattern = preg_replace('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', '(?P<$1>[0-9]+)', $routePath);
        $pattern = '#^' . $pattern . '$#';

        if (!preg_match($pattern, $requestPath, $matches)) {
            return null;
        }

        return array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
    }

    private function runMiddleware(array $middleware): void
    {
        $map = [
            'auth' => AuthMiddleware::class,
            'guest' => GuestMiddleware::class,
        ];

        foreach ($middleware as $name) {
            if (!isset($map[$name])) {
                throw new \RuntimeException('Unknown middleware: ' . $name);
            }

            (new $map[$name]())->handle();
        }
    }
}
