<?php
declare(strict_types=1);

/**
 * Aquí concentro las operaciones generales de seguridad.
 */
final class Security
{
    /**
     * Aquí guardo eventos importantes en una bitácora.
     */
    public static function audit(
        string $action,
        array $context = []
    ): void {
        $entry = [
            'fecha' => date('c'),
            'accion' => $action,
            'usuario' => auth_user()['id_usuario'] ?? null,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'desconocida',
            'contexto' => $context,
        ];

        $line = json_encode(
            $entry,
            JSON_UNESCAPED_UNICODE
            | JSON_UNESCAPED_SLASHES
        );

        @file_put_contents(
            ROOT_PATH . '/storage/audit.log',
            $line . PHP_EOL,
            FILE_APPEND | LOCK_EX
        );
    }

    /**
     * Aquí genero el nombre del archivo de intentos.
     * Utilizo un hash para no guardar directamente el correo o la IP.
     */
    private static function limiterPath(
        string $identity
    ): string {
        return ROOT_PATH
            . '/storage/rate_limits/'
            . hash('sha256', $identity)
            . '.json';
    }

    /**
     * Aquí obtengo solamente los intentos que todavía son válidos.
     */
    private static function attempts(
        string $identity
    ): array {
        $path = self::limiterPath($identity);

        if (!is_file($path)) {
            return [];
        }

        $data = json_decode(
            (string) @file_get_contents($path),
            true
        );

        $minimumTime = time()
            - (int) config('login_lock_seconds', 900);

        return array_values(
            array_filter(
                is_array($data) ? $data : [],
                static fn(mixed $timestamp): bool =>
                    is_int($timestamp)
                    && $timestamp > $minimumTime
            )
        );
    }

    /**
     * Aquí compruebo si ya se alcanzó el límite.
     */
    public static function limited(
        string $identity
    ): bool {
        return count(self::attempts($identity))
            >= (int) config('login_max_attempts', 5);
    }

    /**
     * Aquí registro un intento incorrecto.
     */
    public static function failed(
        string $identity
    ): void {
        $attempts = self::attempts($identity);
        $attempts[] = time();

        @file_put_contents(
            self::limiterPath($identity),
            json_encode($attempts),
            LOCK_EX
        );
    }

    /**
     * Aquí elimino los intentos después de un acceso correcto.
     */
    public static function clear(
        string $identity
    ): void {
        $path = self::limiterPath($identity);

        if (is_file($path)) {
            @unlink($path);
        }
    }
}