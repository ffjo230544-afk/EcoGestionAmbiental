<?php
declare(strict_types=1);

abstract class Controller
{
    protected function view(
        string $view,
        array $data = [],
        string $layout = 'app'
    ): void {
        render($view, $data, $layout);
    }

    protected function failure(
        string $message,
        string $route,
        array $parameters = []
    ): never {
        remember_input($_POST);
        flash('error', $message);
        redirect($route, $parameters);
    }

    protected function success(
        string $message,
        string $route,
        array $parameters = []
    ): never {
        flash('success', $message);
        redirect($route, $parameters);
    }
}