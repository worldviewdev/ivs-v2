<?php
session_start();

require_once __DIR__ . '/core/Response.php';
require_once __DIR__ . '/core/Database.php';

// Autoload controller & repo
spl_autoload_register(function ($class) {
    foreach (['controllers', 'repositories', 'core'] as $dir) {
        $file = __DIR__ . "/$dir/$class.php";
        if (file_exists($file)) require_once $file;
    }
});

$routes = require __DIR__ . '/routes.php';

$method = $_SERVER['REQUEST_METHOD'];
$path = strtok($_SERVER['REQUEST_URI'], '?');
$path = str_replace('/v1/api', '', $path); // kalau API ada di /v1/api/

// Cek route exact match dulu
if (isset($routes[$method][$path])) {
    [$controller, $action] = $routes[$method][$path];
    $instance = new $controller();
    $instance->$action();
} else {
    // Cek route dengan parameter dinamis
    $matched = false;
    foreach ($routes[$method] ?? [] as $route => $handler) {
        if (strpos($route, '{') !== false) {
            // Convert route pattern to regex
            $pattern = str_replace('{id}', '(\d+)', $route);
            $pattern = '#^' . $pattern . '$#';
            
            if (preg_match($pattern, $path)) {
                [$controller, $action] = $handler;
                $instance = new $controller();
                $instance->$action();
                $matched = true;
                break;
            }
        }
    }
    
    if (!$matched) {
        Response::json(['error' => 'Not Found'], 404);
    }
}
