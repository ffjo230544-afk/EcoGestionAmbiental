<?php
declare(strict_types=1);

use PHPMailer\PHPMailer\PHPMailer;

/**
 * Aquí concentro el envío de correos
 * de EcoGestión Ambiental.
 */
final class Correo
{
    /**
     * Envío el enlace temporal utilizado
     * para recuperar una contraseña.
     */
    public function sendPasswordRecovery(
        string $recipientEmail,
        string $recipientName,
        string $recoveryUrl,
        int $expirationMinutes
    ): void {
        $subject =
            'Recuperación de contraseña';

        $message =
            'Recibí una solicitud para cambiar '
            . 'la contraseña de tu cuenta.';

        $details = [
            'Duración del enlace' =>
                $expirationMinutes
                . ' minutos',
        ];

        $this->sendTransactional(
            $recipientEmail,
            $recipientName,
            $subject,
            'Recuperación de contraseña',
            $message,
            $details,
            $recoveryUrl,
            'Restablecer contraseña'
        );
    }

    /**
     * Informo que la contraseña de una cuenta
     * fue modificada correctamente.
     */
    public function sendPasswordChanged(
        string $recipientEmail,
        string $recipientName
    ): void {
        $this->sendTransactional(
            $recipientEmail,
            $recipientName,
            'Tu contraseña fue actualizada',
            'Contraseña actualizada',
            'La contraseña de tu cuenta fue '
            . 'actualizada correctamente.',
            [],
            absolute_url('login'),
            'Iniciar sesión'
        );
    }

    /**
     * Envío un mensaje para comprobar
     * la configuración SMTP.
     */
    public function sendTest(
        string $recipientEmail
    ): void {
        $this->sendTransactional(
            $recipientEmail,
            'Usuario de prueba',
            'Prueba de correo de EcoGestión',
            'Configuración correcta',
            'El servidor SMTP de EcoGestión '
            . 'Ambiental envió correctamente '
            . 'este mensaje.',
            [
                'Servidor' => $this->mailSetting(
                    'mail_host',
                    'No disponible'
                ),

                'Fecha' => date(
                    'd/m/Y H:i'
                ),
            ],
            absolute_url(),
            'Abrir EcoGestión'
        );
    }

    /**
     * Construyo un correo transaccional con
     * el diseño general de la aplicación.
     */
    public function sendTransactional(
        string $recipientEmail,
        string $recipientName,
        string $subject,
        string $heading,
        string $message,
        array $details = [],
        ?string $actionUrl = null,
        string $actionLabel = 'Abrir EcoGestión'
    ): void {
        if (
            !filter_var(
                $recipientEmail,
                FILTER_VALIDATE_EMAIL
            )
        ) {
            throw new InvalidArgumentException(
                'El correo destinatario no es válido.'
            );
        }

        $safeName = htmlspecialchars(
            $recipientName,
            ENT_QUOTES | ENT_SUBSTITUTE,
            'UTF-8'
        );

        $safeHeading = htmlspecialchars(
            $heading,
            ENT_QUOTES | ENT_SUBSTITUTE,
            'UTF-8'
        );

        $safeMessage = nl2br(
            htmlspecialchars(
                $message,
                ENT_QUOTES | ENT_SUBSTITUTE,
                'UTF-8'
            )
        );

        $detailRows = '';
        $plainDetails = '';

        foreach ($details as $label => $value) {
            $safeLabel = htmlspecialchars(
                (string) $label,
                ENT_QUOTES | ENT_SUBSTITUTE,
                'UTF-8'
            );

            $safeValue = htmlspecialchars(
                (string) $value,
                ENT_QUOTES | ENT_SUBSTITUTE,
                'UTF-8'
            );

            $detailRows .= '
                <tr>
                    <td style="
                        padding:10px 12px;
                        color:#607168;
                        border-bottom:1px solid #dcebe0;
                    ">
                        ' . $safeLabel . '
                    </td>

                    <td style="
                        padding:10px 12px;
                        color:#173f27;
                        font-weight:600;
                        border-bottom:1px solid #dcebe0;
                    ">
                        ' . $safeValue . '
                    </td>
                </tr>
            ';

            $plainDetails .=
                (string) $label
                . ': '
                . (string) $value
                . PHP_EOL;
        }

        $actionHtml = '';
        $plainAction = '';

        if (
            $actionUrl !== null
            && trim($actionUrl) !== ''
        ) {
            $safeUrl = htmlspecialchars(
                $actionUrl,
                ENT_QUOTES | ENT_SUBSTITUTE,
                'UTF-8'
            );

            $safeActionLabel = htmlspecialchars(
                $actionLabel,
                ENT_QUOTES | ENT_SUBSTITUTE,
                'UTF-8'
            );

            $actionHtml = '
                <p style="
                    margin:30px 0;
                    text-align:center;
                ">
                    <a
                        href="' . $safeUrl . '"
                        style="
                            display:inline-block;
                            padding:14px 24px;
                            color:#ffffff;
                            background:#16803c;
                            text-decoration:none;
                            border-radius:10px;
                            font-weight:bold;
                        "
                    >
                        ' . $safeActionLabel . '
                    </a>
                </p>
            ';

            $plainAction =
                PHP_EOL
                . $actionLabel
                . ': '
                . $actionUrl
                . PHP_EOL;
        }

        $detailsHtml = $detailRows !== ''
            ? '
                <table style="
                    width:100%;
                    margin-top:22px;
                    border-collapse:collapse;
                    background:#f7faf8;
                    border:1px solid #dcebe0;
                    border-radius:12px;
                ">
                    ' . $detailRows . '
                </table>
            '
            : '';

        $html = '
            <!doctype html>
            <html lang="es">
            <head>
                <meta charset="UTF-8">
            </head>

            <body style="
                margin:0;
                padding:24px;
                background:#f2f7f4;
                font-family:Arial,Helvetica,sans-serif;
            ">
                <div style="
                    max-width:620px;
                    margin:0 auto;
                    overflow:hidden;
                    background:#ffffff;
                    border:1px solid #dcebe0;
                    border-radius:18px;
                ">
                    <div style="
                        padding:24px 30px;
                        color:#ffffff;
                        background:#16803c;
                    ">
                        <div style="
                            font-size:22px;
                            font-weight:bold;
                        ">
                            EcoGestión Ambiental
                        </div>

                        <div style="
                            margin-top:4px;
                            opacity:.85;
                        ">
                            UPEMOR
                        </div>
                    </div>

                    <div style="
                        padding:30px;
                        color:#173f27;
                        line-height:1.6;
                    ">
                        <h1 style="
                            margin:0 0 18px;
                            font-size:25px;
                            color:#173f27;
                        ">
                            ' . $safeHeading . '
                        </h1>

                        <p>
                            Hola,
                            <strong>'
                            . $safeName
                            . '</strong>.
                        </p>

                        <p>
                            ' . $safeMessage . '
                        </p>

                        ' . $detailsHtml . '

                        ' . $actionHtml . '

                        <p style="
                            margin-top:28px;
                            color:#607168;
                            font-size:13px;
                        ">
                            Este mensaje fue generado
                            automáticamente por
                            EcoGestión Ambiental.
                        </p>
                    </div>
                </div>
            </body>
            </html>
        ';

        $text =
            'EcoGestión Ambiental'
            . PHP_EOL . PHP_EOL
            . 'Hola, '
            . $recipientName
            . '.'
            . PHP_EOL . PHP_EOL
            . $heading
            . PHP_EOL
            . $message
            . PHP_EOL . PHP_EOL
            . $plainDetails
            . $plainAction
            . PHP_EOL
            . 'Este mensaje fue generado '
            . 'automáticamente.';

        $this->send(
            $recipientEmail,
            $recipientName,
            $subject,
            $html,
            $text
        );
    }

    /**
     * Envío el mensaje mediante SMTP.
     *
     * Cuando SMTP está desactivado, registro el
     * contenido en el archivo local protegido.
     */
    private function send(
        string $recipientEmail,
        string $recipientName,
        string $subject,
        string $html,
        string $text
    ): void {
        if (
            !(bool) $this->mailSetting(
                'mail_enabled',
                false
            )
        ) {
            $this->writeLocalMessage(
                $recipientEmail,
                $recipientName,
                $subject,
                $text
            );

            return;
        }

        if (!class_exists(PHPMailer::class)) {
            throw new RuntimeException(
                'PHPMailer no está instalado.'
            );
        }

        $username = trim(
            (string) $this->mailSetting(
                'mail_username',
                ''
            )
        );

        $password = (string) (
            $this->mailSetting(
                'mail_password',
                ''
            )
        );

        $fromAddress = trim(
            (string) $this->mailSetting(
                'mail_from_address',
                $username
            )
        );

        if (
            !filter_var(
                $username,
                FILTER_VALIDATE_EMAIL
            )
            || !filter_var(
                $fromAddress,
                FILTER_VALIDATE_EMAIL
            )
            || $password === ''
        ) {
            throw new RuntimeException(
                'La configuración SMTP '
                . 'está incompleta.'
            );
        }

        $mail = new PHPMailer(true);

        /*
         | Utilizo SMTP autenticado con cifrado
         | para proteger las credenciales y mensajes.
         */
        $mail->isSMTP();



/*
 | Limito el tiempo de conexión con el servidor SMTP
 | para evitar que una petición permanezca bloqueada.
 */
$mail->Timeout = 15;
$mail->Timelimit = 15;

/*
 | Cierro la conexión después de cada mensaje
 | para no conservar conexiones SMTP incompletas.
 */
$mail->SMTPKeepAlive = false;

/*
 | Mantengo desactivada la salida técnica de SMTP
 | para que no se mezcle con las vistas del sistema.
 */
$mail->SMTPDebug = 0;



        



        $mail->Host = (string) (
            $this->mailSetting(
                'mail_host',
                'smtp.gmail.com'
            )
        );

        $mail->Port = (int) (
            $this->mailSetting(
                'mail_port',
                587
            )
        );

        $mail->SMTPAuth = true;
        $mail->Username = $username;
        $mail->Password = $password;
        $mail->Timeout = 20;
        $mail->CharSet = 'UTF-8';

        $encryption = strtolower(
            (string) $this->mailSetting(
                'mail_encryption',
                'tls'
            )
        );

        if ($encryption === 'ssl') {
            $mail->SMTPSecure =
                PHPMailer::ENCRYPTION_SMTPS;
        } elseif ($encryption === 'tls') {
            $mail->SMTPSecure =
                PHPMailer::ENCRYPTION_STARTTLS;
        } else {
            $mail->SMTPAutoTLS = false;
        }

        $mail->setFrom(
            $fromAddress,
            (string) $this->mailSetting(
                'mail_from_name',
                'EcoGestión Ambiental'
            )
        );

        $mail->addAddress(
            $recipientEmail,
            $recipientName
        );

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $html;
        $mail->AltBody = $text;

        $mail->send();
    }

    /**
     * Obtengo las opciones privadas de correo
     * sin mezclarlas con el código principal.
     */
    private function mailSetting(
        string $key,
        mixed $default = null
    ): mixed {
        static $settings = null;

        if ($settings === null) {
            $settings = [];

            $file = ROOT_PATH
                . '/config/mail.local.php';

            if (is_file($file)) {
                $loaded = require $file;

                if (is_array($loaded)) {
                    $settings = $loaded;
                }
            }
        }

        return $settings[$key]
            ?? config($key, $default);
    }

    /**
     * Registro los correos localmente cuando
     * esta instalación no utiliza SMTP.
     */
    private function writeLocalMessage(
        string $recipientEmail,
        string $recipientName,
        string $subject,
        string $text
    ): void {
        $entry =
            str_repeat('=', 70)
            . PHP_EOL
            . 'Fecha: '
            . date('c')
            . PHP_EOL
            . 'Destinatario: '
            . $recipientName
            . ' <'
            . $recipientEmail
            . '>'
            . PHP_EOL
            . 'Asunto: '
            . $subject
            . PHP_EOL . PHP_EOL
            . $text
            . PHP_EOL;

        $result = @file_put_contents(
            ROOT_PATH . '/storage/mail.log',
            $entry,
            FILE_APPEND | LOCK_EX
        );

        if ($result === false) {
            throw new RuntimeException(
                'No fue posible registrar '
                . 'el correo local.'
            );
        }
    }
}