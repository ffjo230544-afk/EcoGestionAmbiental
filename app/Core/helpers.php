<?php
declare(strict_types=1);

/** Obtiene un valor del archivo config/config.php. */
function config(?string $key = null, mixed $default = null): mixed
{
    static $settings = null;
    $settings ??= require ROOT_PATH . '/config/config.php';
    return $key === null ? $settings : ($settings[$key] ?? $default);
}

/** Escapa texto antes de imprimirlo en HTML para prevenir XSS. */
function e(mixed $value): string
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Detecta la carpeta donde está instalado el proyecto dentro de htdocs. */
function base_url(): string
{
    $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/index.php');
    $base = rtrim(str_replace('\\', '/', dirname($script)), '/');
    return $base === '.' ? '' : $base;
}

/** Construye enlaces internos, por ejemplo: index.php?ruta=login. */
function url(string $route = '', array $parameters = []): string
{
    $query = $route !== '' ? ['ruta' => trim($route, '/')] : [];
    $query = array_merge($query, $parameters);
    return base_url() . '/index.php' . ($query ? '?' . http_build_query($query) : '');
}

/** Construye la dirección de un CSS, JavaScript o imagen pública. */
function asset(string $path): string
{
    return base_url() . '/public/' . ltrim($path, '/');
}

/** Redirige a otra ruta interna y termina la petición actual. */
function redirect(string $route = '', array $parameters = []): never
{
    header('Location: ' . url($route, $parameters));
    exit;
}

/** Genera un token contra envíos falsificados de formularios (CSRF). */
function csrf_token(): string
{
    if (empty($_SESSION['_csrf_token'])) {
        $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
    }
    return (string) $_SESSION['_csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

function verify_csrf(): void
{
    $received = (string) ($_POST['_csrf'] ?? '');
    if ($received === '' || !hash_equals(csrf_token(), $received)) {
        http_response_code(419);
        $title = 'Formulario expirado';
        $message = 'Actualiza la página e inténtalo nuevamente.';
        require ROOT_PATH . '/app/Views/errors/simple.php';
        exit;
    }
}

/** Mensajes que sobreviven una redirección y se muestran una sola vez. */
function flash(string $key, mixed $value = null): mixed
{
    if (func_num_args() === 2) {
        $_SESSION['_flash'][$key] = $value;
        return null;
    }
    $stored = $_SESSION['_flash'][$key] ?? null;
    unset($_SESSION['_flash'][$key]);
    return $stored;
}

/**
 * Conserva datos anteriores del formulario,
 * pero nunca contraseñas ni tokens.
 */
function remember_input(array $input): void
{
    unset(
        $input['password'],
        $input['password_confirmacion'],
        $input['_csrf']
    );

    $_SESSION['_old_input'] = $input;
}

/**
 * Recupera un valor anterior de manera segura.
 */
function old(
    string $key,
    mixed $default = ''
): mixed {
    $input = $_SESSION['_old_input'] ?? [];

    if (!is_array($input)) {
        unset($_SESSION['_old_input']);
        return $default;
    }

    $segments = explode('.', $key);
    $value = $input;

    foreach ($segments as $segment) {
        if (
            !is_array($value)
            || !array_key_exists($segment, $value)
        ) {
            return $default;
        }

        $value = $value[$segment];
    }

    if (
        is_array($value)
        || is_object($value)
    ) {
        return $default;
    }

    return $value;
}

/**
 * Elimina los valores anteriores después
 * de renderizar la página.
 */
function forget_old_input(): void
{
    unset($_SESSION['_old_input']);
}


/**
 * Aquí obtengo el usuario que inició sesión.
 */
function auth_user(): ?array
{
    $user = $_SESSION['usuario'] ?? null;

    return is_array($user)
        ? $user
        : null;
}

/**
 * Aquí obtengo el código del rol actual.
 */
function auth_role(): ?string
{
    return auth_user()['rol_codigo'] ?? null;
}

/**
 * Aquí compruebo si el usuario tiene uno de los roles indicados.
 */
function has_role(string ...$roles): bool
{
    return in_array(
        auth_role(),
        $roles,
        true
    );
}

/**
 * Aquí determino la pantalla principal correspondiente a cada rol.
 */
function role_home(?string $role = null): string
{
    $role ??= auth_role();

    return match ($role) {
        'admin' => 'administrador',
        'coordinador' => 'coordinador',
        'ciudadano' => 'ciudadano',
        default => 'login',
    };
}

/**
 * Aquí exijo que exista una sesión válida.
 */
function require_auth(): array
{
    $user = auth_user();

    // Si no existe sesión, envío al usuario al login.
    if ($user === null) {
        flash(
            'error',
            'Inicia sesión para acceder a esta sección.'
        );

        redirect('login');
    }

    /*
     | Aquí calculo el tiempo transcurrido desde
     | la última actividad del usuario.
     */
    $lastActivity = (int) (
        $_SESSION['ultima_actividad'] ?? time()
    );

    $elapsed = time() - $lastActivity;

    if (
        $elapsed
        > (int) config('session_timeout', 1800)
    ) {
        $sessionRecord = (int) (
            $_SESSION['registro_sesion'] ?? 0
        );

        /*
         | Intento marcar la sesión como cerrada.
         | Si falla el historial, cierro la sesión local de todos modos.
         */
        if ($sessionRecord > 0) {
            try {
                (new SesionUsuario())->update(
                    $sessionRecord,
                    [
                        'fecha_cierre' => date('Y-m-d H:i:s'),
                        'motivo_cierre' => 'inactividad',
                        'estado' => 'cerrada',
                    ]
                );
            } catch (Throwable) {
                // Mantengo el cierre local aunque falle el historial.
            }
        }

        Security::audit(
            'sesion_expirada',
            ['id_usuario' => $user['id_usuario']]
        );

        $_SESSION = [];

        session_regenerate_id(true);

        flash(
            'error',
            'La sesión se cerró por inactividad.'
        );

        redirect('login');
    }

    /*
     | Aquí confirmo que la cuenta y su rol sigan activos.
     | Esto permite retirar el acceso sin esperar a que cierre sesión.
     */
    $account = (new Usuario())->row(
        'SELECT
            u.id_rol,
            u.estado,
            r.nombre_rol,
            r.estado AS estado_rol
         FROM usuarios AS u
         INNER JOIN roles AS r
            ON r.id_rol = u.id_rol
         WHERE u.id_usuario = ?
         LIMIT 1',
        [$user['id_usuario']]
    );

    if (
        $account === null
        || $account['estado'] !== 'activo'
        || $account['estado_rol'] !== 'activo'
        || (int) $account['id_rol']
            !== (int) $user['id_rol']
    ) {
        Security::audit(
            'sesion_revocada',
            ['id_usuario' => $user['id_usuario']]
        );

        $_SESSION = [];

        session_regenerate_id(true);

        flash(
            'error',
            'La cuenta ya no está activa o sus permisos cambiaron.'
        );

        redirect('login');
    }

    /*
     | Actualizo la actividad en MySQL como máximo una vez por minuto.
     | Así evito realizar una consulta en cada pequeño movimiento.
     */
    $lastDatabaseUpdate = (int) (
        $_SESSION['sesion_actualizada'] ?? 0
    );

    if (
        time() - $lastDatabaseUpdate >= 60
        && !empty($_SESSION['registro_sesion'])
    ) {
        try {
            (new SesionUsuario())->update(
                (int) $_SESSION['registro_sesion'],
                [
                    'fecha_ultima_actividad' =>
                        date('Y-m-d H:i:s'),
                ]
            );

            $_SESSION['sesion_actualizada'] = time();
        } catch (Throwable) {
            // No detengo el sistema por un error del historial.
        }
    }

    $_SESSION['ultima_actividad'] = time();

    return $user;
}

/**
 * Aquí muestro un error cuando el rol no está autorizado.
 */
function deny_access(): never
{
    http_response_code(403);

    render(
        'errors/403',
        ['pageTitle' => 'Acceso restringido'],
        'public'
    );

    exit;
}

/**
 * Aquí exijo que el usuario tenga uno de los roles permitidos.
 */
function require_roles(array $roles): array
{
    $user = require_auth();

    $currentRole = (string) (
        $user['rol_codigo'] ?? ''
    );

    if (!in_array($currentRole, $roles, true)) {
        deny_access();
    }

    return $user;
}



/** Combina una vista específica con una plantilla o layout general. */
function render(string $view, array $data = [], string $layout = 'public'): void
{
    $viewFile = ROOT_PATH . '/app/Views/' . $view . '.php';
    $layoutFile = ROOT_PATH . '/app/Views/layouts/' . $layout . '.php';
    if (!is_file($viewFile) || !is_file($layoutFile)) {
        throw new RuntimeException('No se encontró la vista o el layout solicitado.');
    }
    extract($data, EXTR_SKIP);
    ob_start();
    require $viewFile;
    $content = (string) ob_get_clean();
    require $layoutFile;
    forget_old_input();
}

function current_route(): string
{
    return trim((string) ($_GET['ruta'] ?? ''), '/');
}



/**
 * Obtiene exclusivamente las opciones permitidas
 * para el rol indicado.
 */
function menu_items(?string $role = null): array
{
    $role ??= auth_role();

    $menu = require ROOT_PATH . '/config/menu.php';

    return array_values(
        array_filter(
            $menu,
            static fn(array $item): bool =>
                in_array($role, $item['roles'], true)
        )
    );
}

/**
 * Construye el enlace de una opción.
 *
 * Si el módulo ya tiene ruta, abre esa ruta.
 * Si todavía no existe, regresa al panel
 * y localiza su tarjeta mediante el target.
 */
function menu_url(array $item): string
{
    if (!empty($item['route'])) {
        return url((string) $item['route']);
    }

    $target = (string) ($item['target'] ?? '');

    return url(role_home())
        . ($target !== '' ? '#' . $target : '');
}

/**
 * Determina si una opción corresponde
 * con la ruta que está abierta actualmente.
 */
function menu_item_active(array $item): bool
{
    if (empty($item['route'])) {
        return false;
    }

    $routes = $item['match']
        ?? [(string) $item['route']];

    return in_array(
        current_route(),
        $routes,
        true
    );
}

/**
 * Obtiene las primeras letras del nombre
 * para mostrarlas dentro del avatar.
 */
function initials(string $name): string
{
    $words = preg_split(
        '/\s+/u',
        trim($name)
    ) ?: [];

    $result = '';

    foreach (array_slice($words, 0, 2) as $word) {
        $result .= function_exists('mb_substr')
            ? mb_substr($word, 0, 1)
            : substr($word, 0, 1);
    }

    return function_exists('mb_strtoupper')
        ? mb_strtoupper($result)
        : strtoupper($result);
}