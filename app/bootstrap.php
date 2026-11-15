<?php
declare(strict_types=1);

/* Bootstrap de la aplicación: prepara todo antes de atender una ruta. */
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
}

require_once ROOT_PATH . '/app/Core/helpers.php';

/* Autoload: carga una clase cuando PHP encuentra su nombre por primera vez. */
spl_autoload_register(static function (string $class): void {
    foreach (['Core', 'Models', 'Controllers'] as $directory) {
        $file = ROOT_PATH . '/app/' . $directory . '/' . $class . '.php';
        if (is_file($file)) {
            require_once $file;
            return;
        }
    }
});

date_default_timezone_set((string) config('timezone', 'America/Mexico_City'));

if (session_status() !== PHP_SESSION_ACTIVE) {
    $https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $https,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Referrer-Policy: strict-origin-when-cross-origin');
header(
    "Content-Security-Policy: "
    . "default-src 'self'; "
    . "script-src 'self' https://cdn.jsdelivr.net; "
    . "style-src 'self' 'unsafe-inline' "
    . "https://cdn.jsdelivr.net "
    . "https://fonts.googleapis.com; "
    . "font-src 'self' "
    . "https://fonts.gstatic.com "
    . "https://cdn.jsdelivr.net data:; "
    . "img-src 'self' data: "
    . "https://*.tile.openstreetmap.org; "
    . "frame-ancestors 'self'; "
    . "base-uri 'self'; "
    . "form-action 'self'"
);

/*
 | Aquí evito que el navegador almacene pantallas privadas
 | después de iniciar sesión.
 */
if (auth_user() !== null) {
    header(
        'Cache-Control: no-store, no-cache, '
        . 'must-revalidate, max-age=0'
    );
}