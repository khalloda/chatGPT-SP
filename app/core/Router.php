<?php
declare(strict_types=1);

namespace App\Core;

/**
 * Purpose: Minimal router with middleware pipeline and 404 handler.
 * Outputs: Dispatch to controllers or closures
 */
final class Router
{
    private array $routes = [];   // method => [ [pattern, handler, middleware[]], ... ]
    private array $named = [];
    private array $globalMiddleware = [];

    public function middleware(array $mw): self { $this->globalMiddleware = $mw; return $this; }

    public function get(string $path, callable|array $handler): self { return $this->map('GET', $path, $handler); }
    public function post(string $path, callable|array $handler): self { return $this->map('POST', $path, $handler); }

    private function map(string $method, string $path, callable|array $handler): self
    {
        $path = '/' . trim($path, '/'); // normalize
        if ($path !== '/') $path = rtrim($path, '/');

        $this->routes[$method][] = ['pattern' => $this->compile($path), 'handler' => $handler, 'mw' => []];
        $last = array_key_last($this->routes[$method]);
        // allow chaining ->middleware([...])
        return new class($this, $method, $last) {
            private Router $r; private string $m; private int $i;
            public function __construct($r,$m,$i){$this->r=$r;$this->m=$m;$this->i=$i;}
            public function middleware(array $mw): Router {
                $this->r->routes[$this->m][$this->i]['mw'] = $mw; return $this->r;
            }
        };
    }

    private function compile(string $path): array
    {
        $regex = preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', '(?P<\1>[^/]+)', $path);
        return ['path' => $path, 'regex' => '#^' . $regex . '$#'];
    }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        $uri = '/' . trim($uri, '/');
        if ($uri !== '/') $uri = rtrim($uri, '/');

        foreach ($this->routes[$method] ?? [] as $route) {
            if (preg_match($route['pattern']['regex'], $uri, $m)) {
                $params = array_filter($m, 'is_string', ARRAY_FILTER_USE_KEY);
                $mw = array_merge($this->globalMiddleware, $route['mw']);
                $this->runMiddleware($mw);
                return $this->invoke($route['handler'], $params);
            }
        }
        http_response_code(404);
        echo '404 Not Found';
    }

    private function runMiddleware(array $mw): void
    {
        foreach ($mw as $name) {
            $class = '\\App\\Middleware\\' . ucfirst($name) . 'Middleware';
            if (class_exists($class)) {
                (new $class())->handle();
            }
        }
    }

    private function invoke(callable|array $handler, array $params): void
    {
        if (is_array($handler)) {
            [$class, $method] = $handler;
            $controller = new $class();
            $controller->$method($params);
        } else {
            $handler();
        }
    }
}

