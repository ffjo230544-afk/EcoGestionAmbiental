<?php
declare(strict_types=1);

/**
 * Aquí administro la conexión general con la base de datos.
 * Utilizo PDO para trabajar con consultas preparadas y excepciones.
 */
final class Database
{
    /*
     | Aquí conservaré una sola conexión durante cada petición.
     | Esto evita conectarme varias veces innecesariamente.
     */
    private static ?PDO $instance = null;

    /**
     * Aquí obtengo una conexión configurada con MySQL.
     *
     * El parámetro permite conectarme con o sin seleccionar la base.
     * para el instalador
     */
    public static function connection(
        bool $includeDatabase = true
    ): PDO {
        // Si ya existe una conexión, reutilizo la misma.
        if (
            $includeDatabase
            && self::$instance instanceof PDO
        ) {
            return self::$instance;
        }

        /*
         | Aquí construyo el DSN.
         | El DSN indica servidor, puerto, base y codificación.
         */
        $dsn = sprintf(
            'mysql:host=%s;port=%d;%scharset=%s',
            config('db_host'),
            (int) config('db_port'),
            $includeDatabase
                ? 'dbname=' . config('db_name') . ';'
                : '',
            config('db_charset', 'utf8mb4')
        );

        /*
         | Aquí creo la conexión PDO.
         | Las opciones indican cómo se manejarán consultas y errores.
         */
        $connection = new PDO(
            $dsn,
            (string) config('db_user'),
            (string) config('db_password'),
            [
                // Convierto los errores de MySQL en excepciones.
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,

                // Obtengo cada registro como un arreglo asociativo.
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,

                // Utilizo consultas preparadas reales de MySQL.
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );

        // Guardo la conexión para reutilizarla.
        if ($includeDatabase) {
            self::$instance = $connection;
        }

        return $connection;
    }
}