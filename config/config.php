<?php
declare(strict_types=1);

/*
 | Aquí concentro la configuración general del sistema.
 | Evito escribir estos datos directamente en controladores o modelos.
 */
return [
    'app_name' => 'EcoGestión Ambiental',
    'app_url' => '',   //cuando publique, aquí pondré la URL real del sistema
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
    'show_demo_credentials' => false,

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
 | Aquí indico las herramientas utilizadas para
 | generar y restaurar respaldos de MySQL.
 */
'mysql_dump_binary' =>
    'C:\\xampp\\mysql\\bin\\mysqldump.exe',

'mysql_client_binary' =>
    'C:\\xampp\\mysql\\bin\\mysql.exe',

/*
 | Limito los archivos SQL subidos a 50 MB.
 */
'backup_max_upload_mb' => 50,

    /*
     | Aquí limito los intentos incorrectos de inicio de sesión.
     | Después de cinco errores se bloqueará durante 15 minutos.
     */
    'login_max_attempts' => 5,
    'login_lock_seconds' => 900,


    /*
 | Los enlaces de recuperación tendrán una
 | duración máxima de 30 minutos.
 */
'recovery_token_minutes' => 30,

/*
 | Mientras el correo esté desactivado,
 | los mensajes se guardarán en storage/mail.log.
 |
 | En el siguiente módulo configuraré el envío
 | real mediante SMTP.
 */
'mail_enabled' => false,

'mail_host' => 'smtp.gmail.com',
'mail_port' => 587,
'mail_encryption' => 'tls',

'mail_username' => '',
'mail_password' => '',

'mail_from_address' => '',
'mail_from_name' => 'EcoGestión Ambiental',




];
