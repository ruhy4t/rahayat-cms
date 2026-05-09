<?php
/**
 * ============================================
 * Router Class - Simple MVC Router
 * ============================================
 */

declare(strict_types=1);

class Router
{
    private array $routes = [];
    private array $params = [];
    private string $controller = DEFAULT_CONTROLLER;
    private string $action = DEFAULT_ACTION;

    /**
     * Add a route
     */
    public function add(string $route, array $params = []): void
    {
        // Convert route to regex pattern
        $route = preg_replace('/\//', '\\/', $route);
        $route = preg_replace('/\{([a-z]+)\}/', '(?P<\1>[a-z0-9-]+)', $route);
        $route = preg_replace('/\{([a-z]+):([^\}]+)\}/', '(?P<\1>\2)', $route);
        $route = '/^' . $route . '$/i';

        $this->routes[$route] = $params;
    }

    /**
     * Match URL to route
     */
    public function match(string $url): bool
    {
        foreach ($this->routes as $route => $params) {
            if (preg_match($route, $url, $matches)) {
                foreach ($matches as $key => $match) {
                    if (is_string($key)) {
                        $params[$key] = $match;
                    }
                }
                $this->params = $params;
                return true;
            }
        }
        return false;
    }

    /**
     * Parse URL from request
     */
    public function parseUrl(): string
    {
        $url = $_GET['url'] ?? '';
        return rtrim($url, '/');
    }

    /**
     * Parse controller and action from URL
     */
    public function parseRoute(string $url): void
    {
        $parts = $url ? explode('/', $url) : [];

        // Get controller
        if (!empty($parts[0])) {
            $this->controller = ucfirst(strtolower($parts[0]));
        }

        // Get action
        if (!empty($parts[1])) {
            $this->action = strtolower($parts[1]);
        }

        // Get additional params
        if (count($parts) > 2) {
            $this->params = array_slice($parts, 2);
        }
    }

    /**
     * Dispatch the request to controller
     */
    public function dispatch(): void
    {
        $url = $this->parseUrl();

        // Check predefined routes first
        if ($this->match($url)) {
            if (isset($this->params['controller'])) {
                $this->controller = ucfirst($this->params['controller']);
            }
            if (isset($this->params['action'])) {
                $this->action = $this->params['action'];
            }
        } else {
            // Parse URL for controller/action
            $this->parseRoute($url);
        }

        $controllerClass = $this->controller . 'Controller';
        $controllerFile = APP_PATH . '/Controllers/' . $controllerClass . '.php';

        if (!file_exists($controllerFile)) {
            $this->error404('Controller not found: ' . $controllerClass);
            return;
        }

        require_once $controllerFile;

        if (!class_exists($controllerClass)) {
            $this->error404('Controller class not found: ' . $controllerClass);
            return;
        }

        $controller = new $controllerClass();

        // Check if action method exists
        $actionMethod = $this->action;
        if (!method_exists($controller, $actionMethod)) {
            $this->error404('Action not found: ' . $actionMethod);
            return;
        }

        // Check for middleware (auth check)
        if (method_exists($controller, 'before')) {
            if ($controller->before($this->action) === false) {
                return;
            }
        }

        // Filter out controller and action from params, keep only route parameters
        $actionParams = array_filter($this->params, function ($key) {
            return !in_array($key, ['controller', 'action']);
        }, ARRAY_FILTER_USE_KEY);

        // Re-index to ensure it's a sequential array for call_user_func_array
        $actionParams = array_values($actionParams);

        // Call the action with params
        call_user_func_array([$controller, $actionMethod], $actionParams);

        // After hook
        if (method_exists($controller, 'after')) {
            $controller->after($this->action);
        }
    }

    /**
     * Show 404 error
     */
    private function error404(string $message = 'Page not found'): void
    {
        http_response_code(404);

        if (defined('APP_DEBUG') && APP_DEBUG) {
            echo '<h1>404 - Not Found</h1>';
            echo '<p>' . htmlspecialchars($message) . '</p>';
        } else {
            // Load 404 view if exists
            $errorView = VIEW_PATH . '/errors/404.php';
            if (file_exists($errorView)) {
                require $errorView;
            } else {
                echo '<h1>404 - Page Not Found</h1>';
            }
        }
    }

    /**
     * Get current controller
     */
    public function getController(): string
    {
        return $this->controller;
    }

    /**
     * Get current action
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * Get route params
     */
    public function getParams(): array
    {
        return $this->params;
    }
}
