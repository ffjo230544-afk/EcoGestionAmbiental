<?php
declare(strict_types=1);

/**
 * Aquí relaciono las rutas con controladores, métodos y roles.
 */
final class Router
{
    private array $routes = [];

    /**
     * Aquí registro una ruta que responde mediante GET.
     */
    public function get(
        string $path,
        string $controller,
        string $action,
        array $roles = []
    ): void {
        $this->add(
            'GET',
            $path,
            $controller,
            $action,
            $roles
        );
    }

    /**
     * Aquí registro una ruta que responde mediante POST.
     */
    public function post(
        string $path,
        string $controller,
        string $action,
        array $roles = []
    ): void {
        $this->add(
            'POST',
            $path,
            $controller,
            $action,
            $roles
        );
    }

    /**
     * Aquí guardo la información completa de cada ruta.
     */
    private function add(
        string $method,
        string $path,
        string $controller,
        string $action,
        array $roles
    ): void {
        $key = $method . ':' . trim($path, '/');

        $this->routes[$key] = [
            'controller' => $controller,
            'action' => $action,
            'roles' => $roles,
        ];
    }

    /**
     * Aquí ejecuto la ruta solicitada.
     */
    public function dispatch(
        string $method,
        string $path
    ): void {
        $key = strtoupper($method)
            . ':'
            . trim($path, '/');

        $route = $this->routes[$key] ?? null;

        if ($route === null) {
            http_response_code(404);

            render(
                'errors/404',
                ['pageTitle' => 'Página no encontrada'],
                'public'
            );

            return;
        }

        /*
         | Si la ruta contiene roles, primero valido
         | la sesión y los permisos del usuario.
         */
        if ($route['roles'] !== []) {
            require_roles($route['roles']);
        }

        /*
         | Todos los formularios POST deben contener
         | un token CSRF válido.
         */
        if (strtoupper($method) === 'POST') {
            verify_csrf();
        }

        $controllerClass = $route['controller'];
        $controller = new $controllerClass();

        $controller->{$route['action']}();
    }
}
