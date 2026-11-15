<?php
declare(strict_types=1);

final class UsuarioController extends Controller
{
    public function index(): void
    {
        $search = trim(
            (string) ($_GET['buscar'] ?? '')
        );

        $roleFilter = max(
            0,
            (int) ($_GET['rol'] ?? 0)
        );

        $statusFilter = (string) (
            $_GET['estado'] ?? ''
        );

        if (
            !in_array(
                $statusFilter,
                ['', 'activo', 'inactivo', 'bloqueado'],
                true
            )
        ) {
            $statusFilter = '';
        }

        $model = new Usuario();

        $perPage = 10;

        $total = $model->countFiltered(
            $search,
            $roleFilter,
            $statusFilter
        );

        $totalPages = max(
            1,
            (int) ceil($total / $perPage)
        );

        $page = max(
            1,
            (int) ($_GET['pagina'] ?? 1)
        );

        $page = min($page, $totalPages);

        $offset = ($page - 1) * $perPage;

        $users = $model->filtered(
            $search,
            $roleFilter,
            $statusFilter,
            $perPage,
            $offset
        );

        $roles = (new Rol())->active();

        $this->view(
            'users/index',
            [
                'pageTitle' => 'Gestión de usuarios',
                'users' => $users,
                'roles' => $roles,
                'search' => $search,
                'roleFilter' => $roleFilter,
                'statusFilter' => $statusFilter,
                'page' => $page,
                'totalPages' => $totalPages,
                'total' => $total,
            ],
            'app'
        );
    }

    public function create(): void
{
    $this->view(
        'users/form',
        [
            'pageTitle' => 'Registrar usuario',
            'roles' => (new Rol())->active(),
            'centers' => (
                new PuntoAtencion()
            )->activeForAssignment(),
            'editing' => false,
            'record' => [],
        ],
        'app'
    );
}

    public function store(): void
    {
        $data = $this->validated(false);

        $model = new Usuario();

        if ($model->emailExists($data['correo'])) {
            $this->failure(
                'Ya existe una cuenta con ese correo.',
                'usuarios/crear'
            );
        }

        $data['password_hash'] = password_hash(
            (string) $_POST['password'],
            PASSWORD_DEFAULT
        );

        $userId = $model->create($data);

        Security::audit(
        'usuario_creado',
        [
            'id_usuario_creado' => $userId,
            'id_rol' => $data['id_rol'],
            'id_punto_asignado' =>
                $data['id_punto_asignado'],
        ]
    );
    


      

/*
 | Obtengo la información completa de la cuenta
 | para personalizar el correo de bienvenida.
 */
$createdUser = $model->findWithRole(
    $userId
);

$emailSent = false;

if ($createdUser !== null) {
    try {
        $centerName = trim(
            (string) (
                $createdUser[
                    'nombre_punto_asignado'
                ] ?? ''
            )
        );

        /*
         | Envío la bienvenida al correo registrado.
         | No envío la contraseña ni su hash.
         */
        (new Correo())->sendTransactional(
            (string) $createdUser['correo'],
            (string) $createdUser[
                'nombre_completo'
            ],
            'Bienvenido a EcoGestión Ambiental',
            'Tu cuenta fue creada',
            'Tu cuenta quedó registrada correctamente '
                . 'en EcoGestión Ambiental.',
            [
                'Nombre' =>
                    $createdUser[
                        'nombre_completo'
                    ],

                'Correo' =>
                    $createdUser['correo'],

                'Rol' =>
                    $createdUser['nombre_rol'],

                'Centro asignado' =>
                    $centerName !== ''
                        ? $centerName
                        : 'No aplica',
            ],
            absolute_url('login'),
            'Iniciar sesión'
        );

        $emailSent = true;
    } catch (Throwable) {
        /*
         | Mantengo activa la cuenta aunque el
         | proveedor de correo no responda.
         */
        Security::audit(
            'correo_no_enviado',
            [
                'origen' =>
                    'bienvenida_usuario',

                'id_usuario' => $userId,
            ]
        );
    }


    $this->success(
    'La cuenta se registró correctamente.',
    'usuarios'
);
}

/*
 | También guardo la bienvenida dentro del sistema
 | para que el usuario pueda verla en la campana.
 */
try {
    (new Notificacion())->notifyUser(
        $userId,
        [
            'tipo_notificacion' => 'sistema',

            'titulo' =>
                'Bienvenido a EcoGestión Ambiental',

            'mensaje' =>
                'Tu cuenta fue creada correctamente. '
                . 'Ya puedes consultar los servicios '
                . 'disponibles para tu rol.',

            'medio' => $emailSent
                ? 'interna_correo'
                : 'interna',
        ]
    );
} catch (Throwable) {
    /*
     | La cuenta permanece creada aunque no pueda
     | registrarse la notificación interna.
     */
    Security::audit(
        'notificacion_no_generada',
        [
            'origen' => 'usuario_creado',
            'id_usuario' => $userId,
        ]
    );
}




    }

    public function edit(): void
    {
        $id = max(
            0,
            (int) ($_GET['id'] ?? 0)
        );

        $record = (new Usuario())->findWithRole($id);

        if ($record === null) {
            $this->failure(
                'No se encontró el usuario solicitado.',
                'usuarios'
            );
        }

        // El hash nunca se envía a la vista.
        unset($record['password_hash']);

       $this->view(
    'users/form',
    [
        'pageTitle' => 'Editar usuario',
        'roles' => (new Rol())->active(),
        'centers' => (
            new PuntoAtencion()
        )->activeForAssignment(),
        'editing' => true,
        'record' => $record,
    ],
    'app'
);
    }

    public function update(): void
    {
        $id = max(
            0,
            (int) ($_POST['id_usuario'] ?? 0)
        );

        $model = new Usuario();

        $current = $model->findWithRole($id);

        if ($current === null) {
            $this->failure(
                'No se encontró el usuario solicitado.',
                'usuarios'
            );
        }

        $data = $this->validated(true, $id);

        if (
            $model->emailExists(
                $data['correo'],
                $id
            )
        ) {
            $this->failure(
                'El correo pertenece a otra cuenta.',
                'usuarios/editar',
                ['id' => $id]
            );
        }

        $authenticatedId = (int) (
            auth_user()['id_usuario'] ?? 0
        );

        $authenticatedRoleId = (int) (
            auth_user()['id_rol'] ?? 0
        );

        /*
         | Un administrador puede editar sus datos,
         | pero no quitarse sus propios permisos.
         */
        if (
            $id === $authenticatedId
            && (
                $data['estado'] !== 'activo'
                || $data['id_rol'] !== $authenticatedRoleId
            )
        ) {
            $this->failure(
                'No puedes desactivar tu cuenta ni cambiar tu propio rol.',
                'usuarios/editar',
                ['id' => $id]
            );
        }

        $selectedRole = (new Rol())->find(
            (int) $data['id_rol']
        );

        $wasActiveAdministrator =
            $current['nombre_rol'] === 'Administrador'
            && $current['estado'] === 'activo';

        $willRemainActiveAdministrator =
            $selectedRole !== null
            && $selectedRole['nombre_rol'] === 'Administrador'
            && $data['estado'] === 'activo';

        /*
         | Impido que el sistema se quede sin
         | ningún administrador activo.
         */
        if (
            $wasActiveAdministrator
            && !$willRemainActiveAdministrator
            && $model->countActiveAdministrators() <= 1
        ) {
            $this->failure(
                'Debe permanecer al menos un administrador activo.',
                'usuarios/editar',
                ['id' => $id]
            );
        }

        $passwordChanged = false;

        if (
            (string) ($_POST['password'] ?? '') !== ''
        ) {
            $data['password_hash'] = password_hash(
                (string) $_POST['password'],
                PASSWORD_DEFAULT
            );

            $passwordChanged = true;
        }

        $model->update($id, $data);

        /*
         | Si modifico mis propios datos, actualizo
         | también la información visible de la sesión.
         */
        if ($id === $authenticatedId) {
            $_SESSION['usuario']['nombre_completo']
                = $data['nombre_completo'];

            $_SESSION['usuario']['correo']
                = $data['correo'];

            $_SESSION['usuario']['telefono']
                = $data['telefono'];
        }
        Security::audit(
            'usuario_actualizado',
            [
                'id_usuario_modificado' => $id,
                'id_punto_asignado' =>
                    $data['id_punto_asignado'],
                'password_actualizada' =>
                    $passwordChanged,
            ]
        );

        $this->success(
            'La información se actualizó correctamente.',
            'usuarios'
        );
    }

    public function changeStatus(): void
    {
        $id = max(
            0,
            (int) ($_POST['id_usuario'] ?? 0)
        );

        $newStatus = (string) (
            $_POST['estado'] ?? ''
        );

        if (
            !in_array(
                $newStatus,
                ['activo', 'inactivo'],
                true
            )
        ) {
            $this->failure(
                'El estado solicitado no es válido.',
                'usuarios'
            );
        }

        $authenticatedId = (int) (
            auth_user()['id_usuario'] ?? 0
        );

        if ($id === $authenticatedId) {
            $this->failure(
                'No puedes cambiar el estado de tu propia cuenta.',
                'usuarios'
            );
        }

        $model = new Usuario();

        $record = $model->findWithRole($id);

        if ($record === null) {
            $this->failure(
                'No se encontró el usuario solicitado.',
                'usuarios'
            );
        }

        if (
            $record['nombre_rol'] === 'Administrador'
            && $record['estado'] === 'activo'
            && $newStatus !== 'activo'
            && $model->countActiveAdministrators() <= 1
        ) {
            $this->failure(
                'Debe permanecer al menos un administrador activo.',
                'usuarios'
            );
        }

        $model->update(
            $id,
            ['estado' => $newStatus]
        );

        /*
         | Al desactivar una cuenta también cierro
         | sus sesiones registradas como activas.
         */
        if ($newStatus === 'inactivo') {
            $model->query(
                'UPDATE sesiones_usuario
                 SET
                    fecha_cierre = COALESCE(
                        fecha_cierre,
                        CURRENT_TIMESTAMP
                    ),
                    motivo_cierre = ?,
                    estado = ?
                 WHERE id_usuario = ?
                   AND estado = ?',
                [
                    'seguridad',
                    'cerrada',
                    $id,
                    'activa',
                ]
            );
        }

        Security::audit(
            'usuario_estado_actualizado',
            [
                'id_usuario_modificado' => $id,
                'nuevo_estado' => $newStatus,
            ]
        );

        $message = $newStatus === 'activo'
            ? 'La cuenta se activó correctamente.'
            : 'La cuenta se desactivó correctamente.';

        $this->success($message, 'usuarios');
    }

    private function validated(
        bool $editing,
        int $id = 0
    ): array {
        $returnRoute = $editing
            ? 'usuarios/editar'
            : 'usuarios/crear';

        $parameters = $editing
            ? ['id' => $id]
            : [];

        $name = trim(
            (string) ($_POST['nombre_completo'] ?? '')
        );

        $email = strtolower(
            trim((string) ($_POST['correo'] ?? ''))
        );

        $phone = trim(
            (string) ($_POST['telefono'] ?? '')
        );

        $roleId = max(
            0,
            (int) ($_POST['id_rol'] ?? 0)
        );

        $pointId = max(
        0,
        (int) ($_POST['id_punto_asignado'] ?? 0)
        );

        $status = (string) (
            $_POST['estado'] ?? 'activo'
        );

        $password = (string) (
            $_POST['password'] ?? ''
        );

        $confirmation = (string) (
            $_POST['password_confirmacion'] ?? ''
        );

        $nameLength = function_exists('mb_strlen')
            ? mb_strlen($name)
            : strlen($name);

        if (
            $nameLength < 3
            || $nameLength > 120
        ) {
            $this->failure(
                'El nombre debe contener entre 3 y 120 caracteres.',
                $returnRoute,
                $parameters
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
                $returnRoute,
                $parameters
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
                'Escribe un teléfono válido de 7 a 20 caracteres.',
                $returnRoute,
                $parameters
            );
        }

        $role = (new Rol())->find($roleId);

        if (
            $role === null
            || $role['estado'] !== 'activo'
        ) {
            $this->failure(
                'Selecciona un rol activo.',
                $returnRoute,
                $parameters
            );
        }
       
    $isCoordinator = strtolower(
        (string) $role['nombre_rol']
    ) === 'coordinador';

    if ($isCoordinator) {
        if ($pointId <= 0) {
            $this->failure(
                'Selecciona el centro que administrará el coordinador.',
                $returnRoute,
                $parameters
            );
        }

        $point = (new PuntoAtencion())->find(
            $pointId
        );

        if (
            $point === null
            || $point['estado'] !== 'activo'
        ) {
            $this->failure(
                'El centro seleccionado no existe o no está activo.',
                $returnRoute,
                $parameters
            );
        }
        } else {
            /*
            | Administradores y ciudadanos no tienen
            | un centro asignado.
            */
            $pointId = 0;
        }


        if (
            !in_array(
                $status,
                ['activo', 'inactivo', 'bloqueado'],
                true
            )
        ) {
            $this->failure(
                'Selecciona un estado válido.',
                $returnRoute,
                $parameters
            );
        }

        $passwordRequired = !$editing;

        $passwordWasWritten =
            $password !== ''
            || $confirmation !== '';

        if (
            $passwordRequired
            || $passwordWasWritten
        ) {
            if ($password !== $confirmation) {
                $this->failure(
                    'Las contraseñas no coinciden.',
                    $returnRoute,
                    $parameters
                );
            }

            if (!$this->validPassword($password)) {
                $this->failure(
                    'La contraseña debe tener de 8 a 72 caracteres, mayúscula, minúscula y número.',
                    $returnRoute,
                    $parameters
                );
            }
        }

      return [
        'id_rol' => $roleId,

        'id_punto_asignado' => $isCoordinator
            ? $pointId
            : null,

        'nombre_completo' => $name,

        'correo' => $email,

        'telefono' => $phone !== ''
            ? $phone
            : null,

        'estado' => $status,
    ];
    }

    private function validPassword(
        string $password
    ): bool {
        $length = strlen($password);

        return $length >= 8
            && $length <= 72
            && preg_match('/[A-Z]/', $password) === 1
            && preg_match('/[a-z]/', $password) === 1
            && preg_match('/[0-9]/', $password) === 1;
    }
}