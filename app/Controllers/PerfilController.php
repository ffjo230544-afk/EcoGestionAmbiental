<?php
declare(strict_types=1);

final class PerfilController extends Controller
{
    /**
     * Aquí muestro la información del usuario
     * que tiene iniciada la sesión.
     */
    public function index(): void
    {
        $userId = (int) (
            auth_user()['id_usuario'] ?? 0
        );

        $record = (new Usuario())->findWithRole(
            $userId
        );

        if ($record === null) {
            Security::audit(
                'perfil_no_disponible',
                ['id_usuario' => $userId]
            );

            flash(
                'error',
                'No fue posible consultar tu cuenta.'
            );

            redirect(role_home());
        }

        /*
         | Nunca envío el hash de la contraseña
         | a una vista HTML.
         */
        unset($record['password_hash']);

        $this->view(
            'profile/index',
            [
                'pageTitle' => 'Mi perfil',
                'record' => $record,
            ],
            'app'
        );
    }

    /**
     * Aquí actualizo solamente los datos personales.
     *
     * El rol, estado y centro asignado no pueden
     * modificarse desde el perfil.
     */
    public function update(): void
    {
        $userId = (int) (
            auth_user()['id_usuario'] ?? 0
        );

        $userModel = new Usuario();

        $current = $userModel->findWithRole(
            $userId
        );

        if ($current === null) {
            $this->profileUnavailable();
        }

        $name = trim(
            (string) (
                $_POST['nombre_completo']
                ?? ''
            )
        );

        $email = strtolower(
            trim(
                (string) (
                    $_POST['correo']
                    ?? ''
                )
            )
        );

        $phone = trim(
            (string) (
                $_POST['telefono']
                ?? ''
            )
        );

        $emailConfirmationPassword = (string) (
            $_POST['password_confirmar_correo']
            ?? ''
        );

        $nameLength = function_exists('mb_strlen')
            ? mb_strlen($name)
            : strlen($name);

        if (
            $nameLength < 3
            || $nameLength > 120
        ) {
            $this->failure(
                'El nombre debe contener entre '
                . '3 y 120 caracteres.',
                'perfil'
            );
        }

        if (
            !filter_var(
                $email,
                FILTER_VALIDATE_EMAIL
            )
            || strlen($email) > 120
        ) {
            $this->failure(
                'Escribe un correo electrónico válido.',
                'perfil'
            );
        }

        if (
            $phone !== ''
            && !preg_match(
                '/^[0-9+\s()\-]{7,20}$/',
                $phone
            )
        ) {
            $this->failure(
                'Escribe un teléfono válido de '
                . '7 a 20 caracteres.',
                'perfil'
            );
        }

        if (
            $userModel->emailExists(
                $email,
                $userId
            )
        ) {
            $this->failure(
                'El correo electrónico ya pertenece '
                . 'a otra cuenta.',
                'perfil'
            );
        }

        /*
         | El correo se utiliza para iniciar sesión.
         | Por eso solicito la contraseña actual
         | solamente cuando el correo será cambiado.
         */
        $emailChanged = strcasecmp(
            $email,
            (string) $current['correo']
        ) !== 0;

        if (
            $emailChanged
            && $emailConfirmationPassword === ''
        ) {
            $this->failure(
                'Para cambiar el correo debes escribir '
                . 'tu contraseña actual.',
                'perfil'
            );
        }

        if (
            $emailChanged
            && !password_verify(
                $emailConfirmationPassword,
                (string) $current['password_hash']
            )
        ) {
            Security::audit(
                'cambio_correo_rechazado',
                ['id_usuario' => $userId]
            );

            $this->failure(
                'La contraseña actual no es correcta.',
                'perfil'
            );
        }

        $nameChanged = $name
            !== (string) $current[
                'nombre_completo'
            ];

        $phoneChanged = $phone
            !== (string) (
                $current['telefono'] ?? ''
            );

        $userModel->update(
            $userId,
            [
                'nombre_completo' => $name,
                'correo' => $email,

                'telefono' => $phone !== ''
                    ? $phone
                    : null,
            ]
        );

        /*
         | Actualizo la información guardada en sesión.
         | Así el menú y la barra superior muestran
         | los datos nuevos inmediatamente.
         */
        $_SESSION['usuario']['nombre_completo']
            = $name;

        $_SESSION['usuario']['correo']
            = $email;

        $_SESSION['usuario']['telefono']
            = $phone !== ''
                ? $phone
                : null;

        Security::audit(
            'perfil_actualizado',
            [
                'nombre_actualizado' =>
                    $nameChanged,

                'correo_actualizado' =>
                    $emailChanged,

                'telefono_actualizado' =>
                    $phoneChanged,
            ]
        );

        $this->success(
            'Tu información se actualizó '
            . 'correctamente.',
            'perfil'
        );
    }

    /**
     * Aquí cambio la contraseña de la cuenta
     * que tiene iniciada la sesión.
     */
    public function changePassword(): void
    {
        $userId = (int) (
            auth_user()['id_usuario'] ?? 0
        );

        $currentPassword = (string) (
            $_POST['password_actual'] ?? ''
        );

        $newPassword = (string) (
            $_POST['password_nueva'] ?? ''
        );

        $confirmation = (string) (
            $_POST['password_nueva_confirmacion']
            ?? ''
        );

        if (
            $currentPassword === ''
            || $newPassword === ''
            || $confirmation === ''
        ) {
            $this->failure(
                'Completa todos los campos para '
                . 'cambiar la contraseña.',
                'perfil'
            );
        }

        $userModel = new Usuario();

        $current = $userModel->findWithRole(
            $userId
        );

        if ($current === null) {
            $this->profileUnavailable();
        }

        if (
            !password_verify(
                $currentPassword,
                (string) $current['password_hash']
            )
        ) {
            Security::audit(
                'cambio_password_rechazado',
                [
                    'id_usuario' => $userId,
                    'motivo' =>
                        'password_actual_incorrecta',
                ]
            );

            $this->failure(
                'La contraseña actual no es correcta.',
                'perfil'
            );
        }

        if ($newPassword !== $confirmation) {
            $this->failure(
                'La contraseña nueva y su '
                . 'confirmación no coinciden.',
                'perfil'
            );
        }

        if (!$this->validPassword($newPassword)) {
            $this->failure(
                'La nueva contraseña debe tener '
                . 'entre 8 y 72 caracteres, '
                . 'una mayúscula, una minúscula '
                . 'y un número.',
                'perfil'
            );
        }

        /*
         | No permito volver a guardar exactamente
         | la misma contraseña.
         */
        if (
            password_verify(
                $newPassword,
                (string) $current['password_hash']
            )
        ) {
            $this->failure(
                'La contraseña nueva debe ser '
                . 'diferente a la actual.',
                'perfil'
            );
        }

        $passwordHash = password_hash(
            $newPassword,
            PASSWORD_DEFAULT
        );

        $currentSessionId = (int) (
            $_SESSION['registro_sesion'] ?? 0
        );

        $database = Database::connection();

        $closedSessions = 0;

        /*
         | La contraseña y el cierre de las demás
         | sesiones forman una sola operación.
         |
         | Si alguna acción falla, deshago ambas.
         */
        $database->beginTransaction();

        try {
            $userModel->update(
                $userId,
                [
                    'password_hash' =>
                        $passwordHash,
                ]
            );

            $closedSessions = (
                new SesionUsuario()
            )->closeOtherActiveSessions(
                $userId,
                $currentSessionId
            );

            $database->commit();
        } catch (Throwable $exception) {
            if ($database->inTransaction()) {
                $database->rollBack();
            }

            Security::audit(
                'cambio_password_error',
                ['id_usuario' => $userId]
            );

            $this->failure(
                'No fue posible cambiar la '
                . 'contraseña. Inténtalo nuevamente.',
                'perfil'
            );
        }

        /*
         | Después de una operación sensible
         | genero un nuevo identificador de sesión.
         */
        session_regenerate_id(true);

        /*
         | También cambio el token CSRF para que los
         | formularios anteriores dejen de ser válidos.
         */
        $_SESSION['_csrf_token'] = bin2hex(
            random_bytes(32)
        );

        $_SESSION['ultima_actividad'] = time();
        $_SESSION['sesion_actualizada'] = time();

        Security::audit(
            'password_actualizada',
            [
                'id_usuario' => $userId,

                'otras_sesiones_cerradas' =>
                    $closedSessions,
            ]
        );

        /*
         | Creo una notificación interna de seguridad.
         | El cambio principal no falla si la
         | notificación no puede registrarse.
         */
        try {
            (new Notificacion())->notifyUser(
                $userId,
                [
                    'tipo_notificacion' =>
                        'sistema',

                    'titulo' =>
                        'Contraseña actualizada',

                    'mensaje' =>
                        'La contraseña de tu cuenta '
                        . 'se cambió correctamente. '
                        . 'Las demás sesiones activas '
                        . 'fueron cerradas.',

                    'medio' => 'interna',
                ]
            );
        } catch (Throwable) {
            Security::audit(
                'notificacion_no_generada',
                [
                    'origen' =>
                        'password_actualizada',

                    'id_usuario' => $userId,
                ]
            );
        }

        $this->success(
            'Tu contraseña se cambió correctamente. '
            . 'Las demás sesiones fueron cerradas.',
            'perfil'
        );
    }

    /**
     * Aquí valido la complejidad mínima
     * de la contraseña nueva.
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

    /**
     * Aquí regreso al panel si la cuenta
     * ya no puede consultarse.
     */
    private function profileUnavailable(): never
    {
        Security::audit(
            'perfil_no_disponible'
        );

        flash(
            'error',
            'No fue posible consultar tu cuenta.'
        );

        redirect(role_home());
    }
}