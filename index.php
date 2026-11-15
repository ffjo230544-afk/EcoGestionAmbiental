<?php
declare(strict_types=1);

/* Front Controller: todas las solicitudes de la aplicación pasan por aquí. */
require_once __DIR__ . '/app/bootstrap.php';

try {
    $router = new Router();
    require ROOT_PATH . '/routes/web.php';
    $router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', current_route());
} catch (Throwable $exception) {
    http_response_code(500);
    $title = 'Ocurrió un problema en EcoGestión Ambiental';
    $message = 'Revisa la estructura del proyecto e inténtalo nuevamente.';
    $technical = config('debug') ? $exception->getMessage() : '';
    require ROOT_PATH . '/app/Views/errors/simple.php';
}
