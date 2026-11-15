<?php
declare(strict_types=1);

/*
 | Aquí concentro la configuración general del sistema.
 | Evito escribir estos datos directamente en controladores o modelos.
 */
return [
    'app_name' => 'EcoGestión Ambiental',
    'app_url' => '',
    'timezone' => 'America/Mexico_City',
    'debug' => false,

    /*
     | Aquí configuro la duración de las sesiones.
     | Los 1800 segundos representan 30 minutos.
     */
    'session_timeout' => 1800,

    /*
     | Aquí indico si mostraré las cuentas demostrativas.
     | Antes de publicar cambiaré este valor a false.
     */
    'show_demo_credentials' => true,

    /*
     | Aquí configuro los datos de conexión con MySQL.
     */
    'db_host' => '127.0.0.1',
    'db_port' => 3306,
    'db_name' => 'ecogestion_ambiental',
    'db_user' => 'root',
    'db_password' => '',
    'db_charset' => 'utf8mb4',

    /*
     | Aquí limito los intentos incorrectos de inicio de sesión.
     | Después de cinco errores se bloqueará durante 15 minutos.
     */
    'login_max_attempts' => 5,
    'login_lock_seconds' => 900,
];