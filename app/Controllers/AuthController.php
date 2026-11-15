<?php
declare(strict_types=1);

/**
 * Aquí controlo el inicio y cierre de sesión.
 */
final class AuthController extends Controller
{
    /**
     * Aquí muestro el formulario de inicio de sesión.
     */
    public function login(): void
    {
        // Si ya existe una sesión, regreso al panel correspondiente.
        if (auth_user() !== null) {
            redirect(role_home());
        }

        $this->view(
            'auth/login',
            ['pageTitle' => 'Iniciar sesión'],
            'auth'
        );
    }

    /**
     * Aquí valido las credenciales enviadas desde el formulario.
     */
    public function authenticate(): void
    {
        $email = strtolower(
            trim((string) ($_POST['correo'] ?? ''))
        );

        $password = (string) (
            $_POST['password'] ?? ''
        );

        $ip = $_SERVER['REMOTE_ADDR'] ?? 'desconocida';

        /*
         | Combino correo e IP para identificar
         | los intentos realizados desde un equipo.
         */
        $identity = $email . '|' . $ip;

        if (
            !filter_var($email, FILTER_VALIDATE_EMAIL)
            || $password === ''
        ) {
            $this->failure(
                'Escribe un correo válido y tu contraseña.',
                'login'
            );
        }

        if (Security::limited($identity)) {
            Security::audit(
                'acceso_bloqueado',
                ['correo' => $email]
            );

            $this->failure(
                'Demasiados intentos fallidos. Espera 15 minutos.',
                'login'
            );
        }

        $usuarioModel = new Usuario();

        $user = $usuarioModel->findByEmailWithRole(
            $email
        );

        /*
         | Si el usuario no existe, genero un hash falso.
         | Así el tiempo de respuesta será parecido y no revelo
         | qué correos están registrados.
         */
        $hash = $user !== null
            ? (string) $user['password_hash']
            : password_hash(
                'cuenta-inexistente-ecogestion',
                PASSWORD_DEFAULT
            );

        if (
            $user === null
            || !password_verify($password, $hash)
        ) {
            Security::failed($identity);

            Security::audit(
                'acceso_rechazado',
                ['correo' => $email]
            );

            $this->failure(
                'El correo o la contraseña no son correctos.',
                'login'
            );
        }

        if (
            $user['estado'] !== 'activo'
            || $user['estado_rol'] !== 'activo'
        ) {
            Security::audit(
                'cuenta_inactiva',
                ['id_usuario' => $user['id_usuario']]
            );

            $this->failure(
                'Esta cuenta no se encuentra activa.',
                'login'
            );
        }

        /*
         | Aquí relaciono el nombre guardado en MySQL
         | con el código interno utilizado por las rutas.
         */
        $roleCodes = [
            'administrador' => 'admin',
            'coordinador' => 'coordinador',
            'ciudadano' => 'ciudadano',
        ];

        $roleName = strtolower(
            (string) $user['nombre_rol']
        );

        if (!isset($roleCodes[$roleName])) {
            $this->failure(
                'El rol de la cuenta no está autorizado.',
                'login'
            );
        }

        $roleCode = $roleCodes[$roleName];

        // Elimino los intentos fallidos después de un acceso correcto.
        Security::clear($identity);

        /*
         | Cambio el identificador de sesión para prevenir
         | ataques de fijación de sesión.
         */
        session_regenerate_id(true);

        /*
         | Aquí guardo solamente la información necesaria.
         | Nunca guardo la contraseña dentro de la sesión.
         */
        $_SESSION['usuario'] = [
            'id_usuario' => (int) $user['id_usuario'],
            'id_rol' => (int) $user['id_rol'],
            'nombre_completo' => $user['nombre_completo'],
            'correo' => $user['correo'],
            'telefono' => $user['telefono'],
            'nombre_rol' => $user['nombre_rol'],
            'rol_codigo' => $roleCode,
        ];

        $_SESSION['ultima_actividad'] = time();
        $_SESSION['sesion_actualizada'] = time();

        // Genero un nuevo token CSRF después de iniciar sesión.
        $_SESSION['_csrf_token'] = bin2hex(
            random_bytes(32)
        );

        /*
         | Aquí registro la sesión en MySQL.
         | El acceso principal continuará aunque falle el historial.
         */
        try {
            $sessionModel = new SesionUsuario();

            $sessionId = $sessionModel->create([
                'id_usuario' => $user['id_usuario'],

                'token_sesion' => hash(
                    'sha256',
                    session_id()
                    . '|'
                    . bin2hex(random_bytes(16))
                ),

                'ip_acceso' => $ip,

                'navegador' => substr(
                    (string) (
                        $_SERVER['HTTP_USER_AGENT'] ?? ''
                    ),
                    0,
                    180
                ),

                'estado' => 'activa',
            ]);

            $_SESSION['registro_sesion'] = $sessionId;
        } catch (Throwable) {
            // El historial no debe impedir el inicio de sesión.
        }

        Security::audit(
            'inicio_sesion',
            ['rol' => $roleCode]
        );

        $this->success(
            'Bienvenido a EcoGestión Ambiental, '
            . $user['nombre_completo']
            . '.',
            role_home($roleCode)
        );
    }

    /**
     * Aquí cierro la sesión actual.
     */
    public function logout(): void
    {
        $sessionRecord = (int) (
            $_SESSION['registro_sesion'] ?? 0
        );

        if ($sessionRecord > 0) {
            try {
                (new SesionUsuario())->update(
                    $sessionRecord,
                    [
                        'fecha_cierre' => date('Y-m-d H:i:s'),
                        'motivo_cierre' => 'manual',
                        'estado' => 'cerrada',
                    ]
                );
            } catch (Throwable) {
                // Completo el cierre local aunque falle el historial.
            }
        }

        Security::audit('cierre_sesion');

        // Elimino toda la información de la sesión anterior.
        $_SESSION = [];

        // Genero un identificador nuevo para la sesión anónima.
        session_regenerate_id(true);

        $this->success(
            'Tu sesión se cerró correctamente.',
            'login'
        );
    }
}