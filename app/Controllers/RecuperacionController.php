<?php
declare(strict_types=1);

final class RecuperacionController extends Controller
{
    /**
     * Aquí muestro el formulario para solicitar
     * un enlace de recuperación.
     */
    public function form(): void
    {
        if (auth_user() !== null) {
            redirect('perfil');
        }

        $this->view(
            'auth/forgot',
            [
                'pageTitle' =>
                    'Recuperar contraseña',
            ],
            'auth'
        );
    }

    /**
     * Aquí proceso la solicitud sin revelar
     * si el correo existe o no.
     */
    public function send(): void
    {
        $email = strtolower(
            trim(
                (string) (
                    $_POST['correo'] ?? ''
                )
            )
        );

        if (
            !filter_var(
                $email,
                FILTER_VALIDATE_EMAIL
            )
            || strlen($email) > 120
        ) {
            $this->failure(
                'Escribe un correo electrónico válido.',
                'recuperar-password'
            );
        }

        $ip = (string) (
            $_SERVER['REMOTE_ADDR']
            ?? 'desconocida'
        );

        /*
         | Reutilizo el limitador de seguridad.
         | La palabra recovery evita mezclar estos
         | intentos con los intentos del login.
         */
        $identity =
            'recovery|'
            . $email
            . '|'
            . $ip;

        if (Security::limited($identity)) {
            Security::audit(
                'recuperacion_bloqueada',
                [
                    'correo_hash' =>
                        hash('sha256', $email),
                ]
            );

            $this->failure(
                'Demasiadas solicitudes. '
                . 'Espera 15 minutos.',
                'recuperar-password'
            );
        }

        /*
         | Registro todas las solicitudes,
         | exista o no exista el correo.
         */
        Security::failed($identity);

        $user = (
            new Usuario()
        )->findByEmailWithRole($email);

        $accountIsAvailable =
            $user !== null
            && $user['estado'] === 'activo'
            && $user['estado_rol'] === 'activo';

        if ($accountIsAvailable) {
            $minutes = (int) config(
                'recovery_token_minutes',
                30
            );

            try {
                $plainToken = (
                    new TokenRecuperacion()
                )->issue(
                    (int) $user['id_usuario'],
                    $minutes
                );

                $recoveryUrl = absolute_url(
                    'restablecer-password',
                    [
                        'token' => $plainToken,
                    ]
                );

                (new Correo())
                    ->sendPasswordRecovery(
                        (string) $user['correo'],
                        (string) $user[
                            'nombre_completo'
                        ],
                        $recoveryUrl,
                        $minutes
                    );

                Security::audit(
                    'recuperacion_solicitada',
                    [
                        'id_usuario' =>
                            $user['id_usuario'],
                    ]
                );
            } catch (Throwable) {
                /*
                 | Nunca muestro el error técnico ni confirmo
                 | que la cuenta existe.
                 */
                Security::audit(
                    'recuperacion_no_enviada',
                    [
                        'id_usuario' =>
                            $user['id_usuario'],
                    ]
                );
            }
        } else {
            Security::audit(
                'recuperacion_correo_no_disponible',
                [
                    'correo_hash' =>
                        hash('sha256', $email),
                ]
            );
        }

        /*
         | El mensaje siempre será el mismo,
         | exista o no exista el correo.
         */
        flash(
            'success',
            'Si el correo está registrado y la '
            . 'cuenta se encuentra activa, recibirás '
            . 'las instrucciones de recuperación.'
        );

        redirect('recuperar-password');
    }

    /**
     * Aquí muestro el formulario para establecer
     * una contraseña nueva.
     */
    public function reset(): void
    {
        if (auth_user() !== null) {
            redirect('perfil');
        }

        $plainToken = trim(
            (string) ($_GET['token'] ?? '')
        );

        $record = (
            new TokenRecuperacion()
        )->findValid($plainToken);

        if ($record === null) {
            flash(
                'error',
                'El enlace no es válido, ya fue '
                . 'utilizado o ha expirado.'
            );

            redirect('recuperar-password');
        }

        $this->view(
            'auth/reset',
            [
                'pageTitle' =>
                    'Restablecer contraseña',

                'token' => $plainToken,
            ],
            'auth'
        );
    }

    /**
     * Aquí guardo la contraseña nueva.
     */
    public function update(): void
    {
        $plainToken = trim(
            (string) (
                $_POST['token_recuperacion']
                ?? ''
            )
        );

        $password = (string) (
            $_POST['password'] ?? ''
        );

        $confirmation = (string) (
            $_POST['password_confirmacion']
            ?? ''
        );

        if (
            !preg_match(
                '/^[a-f0-9]{64}$/i',
                $plainToken
            )
        ) {
            flash(
                'error',
                'El enlace de recuperación '
                . 'no es válido.'
            );

            redirect('recuperar-password');
        }

        if (
            $password === ''
            || $confirmation === ''
        ) {
            $this->failure(
                'Escribe y confirma tu '
                . 'contraseña nueva.',
                'restablecer-password',
                ['token' => $plainToken]
            );
        }

        if ($password !== $confirmation) {
            $this->failure(
                'Las contraseñas no coinciden.',
                'restablecer-password',
                ['token' => $plainToken]
            );
        }

        if (!$this->validPassword($password)) {
            $this->failure(
                'La contraseña debe tener entre '
                . '8 y 72 caracteres, una mayúscula, '
                . 'una minúscula y un número.',
                'restablecer-password',
                ['token' => $plainToken]
            );
        }

        $database = Database::connection();

        $database->beginTransaction();

        try {
            /*
             | Vuelvo a comprobar el token dentro de
             | la transacción y bloqueo el registro.
             */
            $tokenModel =
                new TokenRecuperacion();

            $tokenRecord =
                $tokenModel->findValid(
                    $plainToken,
                    true
                );

            if ($tokenRecord === null) {
                throw new RuntimeException(
                    'El token ya no es válido.'
                );
            }

            $userId = (int) (
                $tokenRecord['id_usuario']
            );

            (new Usuario())->update(
                $userId,
                [
                    'password_hash' =>
                        password_hash(
                            $password,
                            PASSWORD_DEFAULT
                        ),
                ]
            );

            $tokenModel->markAsUsed(
                (int) $tokenRecord['id_token']
            );

            $tokenModel->invalidateOthers(
                $userId,
                (int) $tokenRecord['id_token']
            );

            /*
             | Una recuperación de contraseña
             | cierra todas las sesiones anteriores.
             */
            (new SesionUsuario())
                ->closeAllActiveSessions(
                    $userId
                );

            $database->commit();
        } catch (Throwable) {
            if ($database->inTransaction()) {
                $database->rollBack();
            }

            flash(
                'error',
                'El enlace no es válido, ya fue '
                . 'utilizado o ha expirado.'
            );

            redirect('recuperar-password');
        }

        Security::audit(
            'password_recuperada',
            ['id_usuario' => $userId]
        );

        /*
         | La notificación y el correo no forman
         | parte de la transacción principal.
         */
        try {
            (new Notificacion())->notifyUser(
                $userId,
                [
                    'tipo_notificacion' =>
                        'recuperacion_contrasena',

                    'titulo' =>
                        'Contraseña restablecida',

                    'mensaje' =>
                        'La contraseña de tu cuenta '
                        . 'fue restablecida mediante '
                        . 'el proceso de recuperación.',

                    'medio' => 'interna',
                ]
            );
        } catch (Throwable) {
            Security::audit(
                'notificacion_no_generada',
                [
                    'origen' =>
                        'password_recuperada',

                    'id_usuario' => $userId,
                ]
            );
        }

        try {
            (new Correo())->sendPasswordChanged(
                (string) $tokenRecord['correo'],
                (string) $tokenRecord[
                    'nombre_completo'
                ]
            );
        } catch (Throwable) {
            Security::audit(
                'correo_no_enviado',
                [
                    'origen' =>
                        'password_recuperada',

                    'id_usuario' => $userId,
                ]
            );
        }

        /*
         | Si por alguna razón había una sesión
         | local, elimino todos sus datos.
         */
        $_SESSION = [];

        session_regenerate_id(true);

        flash(
            'success',
            'Tu contraseña se restableció '
            . 'correctamente. Ya puedes iniciar sesión.'
        );

        redirect('login');
    }

    /**
     * Aquí aplico la misma política utilizada
     * al crear y editar cuentas.
     */
    private function validPassword(
        string $password
    ): bool {
        $length = strlen($password);

        return $length >= 8
            && $length <= 72
            && preg_match(
                '/[A-Z]/',
                $password
            ) === 1
            && preg_match(
                '/[a-z]/',
                $password
            ) === 1
            && preg_match(
                '/[0-9]/',
                $password
            ) === 1;
    }
}