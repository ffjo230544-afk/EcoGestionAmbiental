<?php
declare(strict_types=1);

/*
 | Este archivo documenta las opciones necesarias
 | para configurar el servidor SMTP.
 |
 | No contiene credenciales reales.
 */
return [
    'mail_enabled' => true,

    'mail_host' => 'smtp.gmail.com',
    'mail_port' => 587,
    'mail_encryption' => 'tls',

    'mail_username' =>
        'correo@gmail.com',

    'mail_password' =>
        'clave-de-aplicacion',

    'mail_from_address' =>
        'correo@gmail.com',

    'mail_from_name' =>
        'EcoGestión Ambiental',
];