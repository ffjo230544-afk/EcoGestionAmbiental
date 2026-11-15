<?php
declare(strict_types=1);

final class CitaController extends Controller
{
    private const STATUS_LABELS = [
        'pendiente' => 'Pendiente',
        'confirmada' => 'Confirmada',
        'atendida' => 'Atendida',
        'cancelada' => 'Cancelada',
    ];

    private const MOTIVE_LABELS = [
        'entrega_reciclables' =>
            'Entrega de reciclables',

        'entrega_electronicos' =>
            'Entrega de electrónicos',

        'consulta_residuos_toxicos' =>
            'Consulta de residuos tóxicos',

        'informacion_servicios' =>
            'Información de servicios',

        'otro' => 'Otro motivo',
    ];

    /**
     * Aquí defino los cambios permitidos
     * para el administrador y coordinador.
     */
    private const TRANSITIONS = [
        'pendiente' => [
            'confirmada',
            'cancelada',
        ],

        'confirmada' => [
            'atendida',
            'cancelada',
        ],

        'atendida' => [],
        'cancelada' => [],
    ];

    /**
     * Relaciono el número del día de PHP
     * con el valor almacenado en MySQL.
     */
    private const DAY_CODES = [
        1 => 'lunes',
        2 => 'martes',
        3 => 'miercoles',
        4 => 'jueves',
        5 => 'viernes',
        6 => 'sabado',
        7 => 'domingo',
    ];

    /**
     * Muestra las citas correspondientes
     * al usuario autenticado.
     */
    public function index(): void
    {
        $search = substr(
            trim((string) ($_GET['buscar'] ?? '')),
            0,
            80
        );

        $status = (string) (
            $_GET['estado'] ?? ''
        );

        if (
            !array_key_exists(
                $status,
                self::STATUS_LABELS
            )
        ) {
            $status = '';
        }

        $appointmentDate = trim(
            (string) ($_GET['fecha'] ?? '')
        );

        if (
            $appointmentDate !== ''
            && !$this->validDate($appointmentDate)
        ) {
            $appointmentDate = '';
        }

        $model = new Cita();

        $centers = $model->activeCenters();

        $validCenterIds = array_map(
            'intval',
            array_column(
                $centers,
                'id_punto'
            )
        );

        $requestedPointId = max(
            0,
            (int) ($_GET['centro'] ?? 0)
        );

        $canManage = has_role(
            'admin',
            'coordinador'
        );

        /*
         | El ciudadano solamente obtiene
         | los registros que le pertenecen.
         */
        $userId = has_role('ciudadano')
            ? (int) auth_user()['id_usuario']
            : null;

        /*
         | El coordinador queda restringido
         | al centro guardado en su sesión.
         */
        if (has_role('coordinador')) {
            $pointId = auth_center_id();

            if ($pointId === null) {
                deny_access();
            }
        } elseif (
            has_role('admin')
            && $requestedPointId > 0
            && in_array(
                $requestedPointId,
                $validCenterIds,
                true
            )
        ) {
            /*
             | El administrador puede aplicar
             | un filtro opcional por centro.
             */
            $pointId = $requestedPointId;
        } else {
            $pointId = null;
        }

        $appointments = $model->search(
            $search,
            $status,
            $appointmentDate,
            $userId,
            $pointId
        );

        $this->view(
            'appointments/index',
            [
                'pageTitle' => $canManage
                    ? 'Gestión de citas'
                    : 'Mis citas',

                'appointments' => $appointments,
                'centers' => $centers,

                'search' => $search,
                'status' => $status,
                'appointmentDate' =>
                    $appointmentDate,

                'selectedPointId' =>
                    $pointId ?? 0,

                'statusLabels' =>
                    self::STATUS_LABELS,

                'motiveLabels' =>
                    self::MOTIVE_LABELS,

                'transitions' =>
                    self::TRANSITIONS,

                'canManage' => $canManage,
            ],
            'app'
        );
    }

    /**
     * Muestra el formulario para solicitar una cita.
     */
    public function create(): void
    {
        $model = new Cita();

        $centers = $model->activeCenters();

        /*
         | Organizo los horarios utilizando
         | el centro y el día como llaves.
         */
        $scheduleData = [];

        foreach (
            $model->schedulesForActiveCenters()
            as $schedule
        ) {
            $pointId = (int) $schedule['id_punto'];

            $scheduleData[
                $pointId
            ][
                $schedule['dia_semana']
            ] = [
                'open' => $schedule['hora_apertura']
                    !== null
                    ? substr(
                        (string) $schedule[
                            'hora_apertura'
                        ],
                        0,
                        5
                    )
                    : null,

                'close' => $schedule['hora_cierre']
                    !== null
                    ? substr(
                        (string) $schedule[
                            'hora_cierre'
                        ],
                        0,
                        5
                    )
                    : null,

                'closed' =>
                    (int) $schedule['cerrado'] === 1,

                'observations' =>
                    $schedule['observaciones'],
            ];
        }

        /*
         | Organizo los espacios ya registrados
         | para ocultarlos del selector.
         */
        $occupiedData = [];

        foreach (
            $model->occupiedSlots()
            as $occupied
        ) {
            $pointId = (int) $occupied['id_punto'];
            $date = (string) $occupied['fecha_cita'];

            $occupiedData[
                $pointId
            ][
                $date
            ][] = substr(
                (string) $occupied['hora_cita'],
                0,
                5
            );
        }

        $this->view(
            'appointments/form',
            [
                'pageTitle' => 'Solicitar cita',
                'centers' => $centers,
                'scheduleData' => $scheduleData,
                'occupiedData' => $occupiedData,
                'motiveLabels' =>
                    self::MOTIVE_LABELS,
                'useAppointments' => true,
            ],
            'app'
        );
    }

    /**
     * Valida y registra una cita nueva.
     */
    public function store(): void
    {
        $pointId = max(
            0,
            (int) ($_POST['id_punto'] ?? 0)
        );

        $appointmentDate = trim(
            (string) ($_POST['fecha_cita'] ?? '')
        );

        $appointmentTime = trim(
            (string) ($_POST['hora_cita'] ?? '')
        );

        $motive = (string) (
            $_POST['motivo'] ?? ''
        );

        $observations = trim(
            (string) ($_POST['observaciones'] ?? '')
        );

        if ($pointId <= 0) {
            $this->failure(
                'Selecciona un centro de atención.',
                'citas/crear'
            );
        }

        if (!$this->validDate($appointmentDate)) {
            $this->failure(
                'Selecciona una fecha válida.',
                'citas/crear'
            );
        }

        $dateObject =
            DateTimeImmutable::createFromFormat(
                '!Y-m-d',
                $appointmentDate
            );

        if ($dateObject === false) {
            $this->failure(
                'La fecha seleccionada no es válida.',
                'citas/crear'
            );
        }

        $today = new DateTimeImmutable('today');

        $maximumDate = $today->modify(
            '+90 days'
        );

        if ($dateObject < $today) {
            $this->failure(
                'No puedes solicitar una cita '
                . 'en una fecha anterior.',
                'citas/crear'
            );
        }

        if ($dateObject > $maximumDate) {
            $this->failure(
                'Las citas solamente pueden programarse '
                . 'con hasta 90 días de anticipación.',
                'citas/crear'
            );
        }

        if (
            !preg_match(
                '/^(?:[01]\d|2[0-3]):[0-5]\d$/',
                $appointmentTime
            )
        ) {
            $this->failure(
                'Selecciona un horario válido.',
                'citas/crear'
            );
        }

        if (
            !array_key_exists(
                $motive,
                self::MOTIVE_LABELS
            )
        ) {
            $this->failure(
                'Selecciona un motivo válido.',
                'citas/crear'
            );
        }

        if (strlen($observations) > 300) {
            $this->failure(
                'Las observaciones no pueden superar '
                . 'los 300 caracteres.',
                'citas/crear'
            );
        }

        $dayNumber = (int) $dateObject->format(
            'N'
        );

        $dayCode = self::DAY_CODES[
            $dayNumber
        ] ?? '';

        $model = new Cita();

        /*
         | Busco el horario real del centro.
         | Si no aparece, el centro está cerrado
         | o ya no se encuentra activo.
         */
        $schedule = $model->scheduleForDay(
            $pointId,
            $dayCode
        );

        if ($schedule === null) {
            $this->failure(
                'El centro no brinda atención '
                . 'en el día seleccionado.',
                'citas/crear'
            );
        }

        $openingTime = substr(
            (string) $schedule['hora_apertura'],
            0,
            5
        );

        $closingTime = substr(
            (string) $schedule['hora_cierre'],
            0,
            5
        );

        $openingMinutes = $this->timeToMinutes(
            $openingTime
        );

        $closingMinutes = $this->timeToMinutes(
            $closingTime
        );

        $appointmentMinutes =
            $this->timeToMinutes(
                $appointmentTime
            );

        /*
         | Cada cita utiliza un espacio de 30 minutos.
         */
        if (
            $appointmentMinutes < $openingMinutes
            || $appointmentMinutes + 30
                > $closingMinutes
        ) {
            $this->failure(
                'El horario seleccionado está fuera '
                . 'del horario de atención.',
                'citas/crear'
            );
        }

        /*
         | Confirmo que el horario corresponda
         | con alguno de los espacios de 30 minutos
         | generados desde la hora de apertura.
         */
        if (
            (
                $appointmentMinutes
                - $openingMinutes
            ) % 30 !== 0
        ) {
            $this->failure(
                'El horario seleccionado no corresponde '
                . 'con un espacio disponible.',
                'citas/crear'
            );
        }

        $appointmentAt = new DateTimeImmutable(
            $appointmentDate
            . ' '
            . $appointmentTime
            . ':00'
        );

        if (
            $appointmentAt
            <= new DateTimeImmutable()
        ) {
            $this->failure(
                'Selecciona una fecha y hora futura.',
                'citas/crear'
            );
        }

        $databaseTime =
            $appointmentTime . ':00';

        if (
            $model->slotExists(
                $pointId,
                $appointmentDate,
                $databaseTime
            )
        ) {
            $this->failure(
                'Ese horario ya no se encuentra '
                . 'disponible. Selecciona otro.',
                'citas/crear'
            );
        }

        try {
            $appointmentId = $model->create([
                'id_usuario' =>
                    (int) auth_user()['id_usuario'],

                'id_punto' => $pointId,

                'fecha_cita' =>
                    $appointmentDate,

                'hora_cita' =>
                    $databaseTime,

                'motivo' => $motive,

                'observaciones' =>
                    $observations !== ''
                        ? $observations
                        : null,

                'estado' => 'pendiente',
            ]);
        } catch (PDOException $exception) {
            /*
             | La llave única de MySQL funciona
             | como protección adicional si dos
             | usuarios intentan reservar al mismo tiempo.
             */
            if (
                (string) $exception->getCode()
                === '23000'
            ) {
                $this->failure(
                    'Ese horario acaba de ser reservado. '
                    . 'Selecciona otro.',
                    'citas/crear'
                );
            }

            throw $exception;
        }

        Security::audit(
            'cita_creada',
            [
                'id_cita' => $appointmentId,
                'id_punto' => $pointId,
                'fecha_cita' =>
                    $appointmentDate,
                'hora_cita' =>
                    $databaseTime,
            ]
        );





/*
 | Consulto los datos relacionados con la cita
 | para mostrarlos en el correo de confirmación.
 */
$appointmentDetails = (
    new Cita()
)->findDetailed(
    $appointmentId
);

$emailSent = false;

if ($appointmentDetails !== null) {
    try {
        /*
         | Confirmo por correo que la solicitud
         | fue recibida correctamente.
         */
        (new Correo())->sendTransactional(
            (string) $appointmentDetails[
                'correo_ciudadano'
            ],
            (string) $appointmentDetails[
                'ciudadano'
            ],
            'Solicitud de cita #'
                . $appointmentId,
            'Recibimos tu solicitud de cita',
            'La cita quedó registrada y se encuentra '
                . 'pendiente de confirmación.',
            [
                'Folio' =>
                    '#' . $appointmentId,

                'Centro' =>
                    $appointmentDetails[
                        'centro'
                    ],

                'Fecha' => date_mx(
                    $appointmentDetails[
                        'fecha_cita'
                    ]
                ),

                'Hora' => substr(
                    (string) $appointmentDetails[
                        'hora_cita'
                    ],
                    0,
                    5
                ),

                'Motivo' =>
                    $appointmentDetails[
                        'motivo'
                    ],

                'Estado' =>
                    'Pendiente de confirmación',
            ],
            absolute_url('citas'),
            'Consultar mis citas'
        );

        $emailSent = true;
    } catch (Throwable) {
        /*
         | La cita permanece registrada aunque
         | no pueda enviarse el correo.
         */
        Security::audit(
            'correo_no_enviado',
            [
                'origen' => 'cita_creada',
                'id_cita' => $appointmentId,
            ]
        );
    }
}

try {
    $notificationModel = new Notificacion();

    /*
     | Confirmo al ciudadano dentro del sistema
     | que la solicitud quedó registrada.
     */
    $notificationModel->notifyUser(
        (int) auth_user()['id_usuario'],
        [
            'id_cita' => $appointmentId,

            'tipo_notificacion' =>
                'cambio_cita',

            'titulo' =>
                'Solicitud de cita recibida',

            'mensaje' =>
                'La cita #'
                . $appointmentId
                . ' fue registrada y está '
                . 'pendiente de confirmación.',

            'medio' => $emailSent
                ? 'interna_correo'
                : 'interna',
        ]
    );

    /*
     | Notifico al administrador y al coordinador
     | responsable del centro seleccionado.
     */
    $notificationModel->notifyManagers(
        $pointId,
        [
            'id_cita' => $appointmentId,

            'tipo_notificacion' =>
                'cambio_cita',

            'titulo' =>
                'Nueva solicitud de cita',

            'mensaje' =>
                'Se registró la cita #'
                . $appointmentId
                . ' para el centro correspondiente.',

            'medio' => 'interna',
        ]
    );
} catch (Throwable) {
    /*
     | La cita permanece registrada aunque no pueda
     | generarse alguna notificación interna.
     */
    Security::audit(
        'notificacion_no_generada',
        [
            'origen' => 'cita_creada',
            'id_cita' => $appointmentId,
        ]
    );
}


   


        $this->success(
            'La cita se registró correctamente '
            . 'y quedó pendiente de confirmación.',
            'citas'
        );
    }

    /**
     * El administrador o coordinador cambian
     * el estado de una cita.
     */
    public function changeStatus(): void
    {
        $appointmentId = max(
            0,
            (int) ($_POST['id_cita'] ?? 0)
        );

        $newStatus = (string) (
            $_POST['estado'] ?? ''
        );

        $model = new Cita();
        $appointment = $model->find(
            $appointmentId
        );

        if ($appointment === null) {
            $this->failure(
                'No se encontró la cita solicitada.',
                'citas'
            );
        }

        /*
         | El coordinador solamente puede cambiar
         | las citas de su centro asignado.
         */
        if (has_role('coordinador')) {
            $assignedPointId =
                auth_center_id();

            if (
                $assignedPointId === null
                || (int) $appointment['id_punto']
                    !== $assignedPointId
            ) {
                Security::audit(
                    'cita_centro_rechazada',
                    [
                        'id_cita' =>
                            $appointmentId,

                        'id_punto_cita' =>
                            $appointment['id_punto'],

                        'id_punto_asignado' =>
                            $assignedPointId,
                    ]
                );

                deny_access();
            }
        }

        $currentStatus = (string) (
            $appointment['estado']
        );

        $allowed = self::TRANSITIONS[
            $currentStatus
        ] ?? [];

        if (
            !in_array(
                $newStatus,
                $allowed,
                true
            )
        ) {
            $this->failure(
                'El cambio de estado solicitado '
                . 'no está permitido.',
                'citas'
            );
        }

        /*
         | Una cita futura no puede marcarse
         | anticipadamente como atendida.
         */
        if ($newStatus === 'atendida') {
            $appointmentAt =
                new DateTimeImmutable(
                    $appointment['fecha_cita']
                    . ' '
                    . $appointment['hora_cita']
                );

            if (
                $appointmentAt
                > new DateTimeImmutable()
            ) {
                $this->failure(
                    'Una cita futura todavía no puede '
                    . 'marcarse como atendida.',
                    'citas'
                );
            }
        }

        $model->update(
            $appointmentId,
            ['estado' => $newStatus]
        );

        Security::audit(
            'cita_actualizada',
            [
                'id_cita' => $appointmentId,
                'id_punto' =>
                    $appointment['id_punto'],
                'estado_anterior' =>
                    $currentStatus,
                'estado_nuevo' =>
                    $newStatus,
            ]
        );

       





$notificationType = match ($newStatus) {
    'confirmada' =>
        'confirmacion_cita',

    'cancelada' =>
        'cancelacion_cita',

    default =>
        'cambio_cita',
};

$statusLabel = self::STATUS_LABELS[
    $newStatus
] ?? ucfirst($newStatus);

/*
 | Vuelvo a consultar la cita después del cambio
 | para obtener sus datos relacionados.
 */
$appointmentDetails =
    $model->findDetailed(
        $appointmentId
    );

$emailSent = false;

if ($appointmentDetails !== null) {
    try {
        /*
         | Informo al ciudadano sobre el nuevo
         | estado de su cita.
         */
        (new Correo())->sendTransactional(
            (string) $appointmentDetails[
                'correo_ciudadano'
            ],
            (string) $appointmentDetails[
                'ciudadano'
            ],
            'Actualización de la cita #'
                . $appointmentId,
            'Tu cita cambió de estado',
            'Se actualizó el estado de tu cita '
                . 'en EcoGestión Ambiental.',
            [
                'Folio' =>
                    '#' . $appointmentId,

                'Centro' =>
                    $appointmentDetails[
                        'centro'
                    ],

                'Fecha' => date_mx(
                    $appointmentDetails[
                        'fecha_cita'
                    ]
                ),

                'Hora' => substr(
                    (string) $appointmentDetails[
                        'hora_cita'
                    ],
                    0,
                    5
                ),

                'Estado actual' =>
                    $statusLabel,
            ],
            absolute_url('citas'),
            'Consultar mi cita'
        );

        $emailSent = true;
    } catch (Throwable) {
        /*
         | El cambio de estado permanece guardado
         | aunque el correo no pueda enviarse.
         */
        Security::audit(
            'correo_no_enviado',
            [
                'origen' =>
                    'cita_actualizada',

                'id_cita' =>
                    $appointmentId,
            ]
        );
    }
}

try {
    /*
     | También registro el cambio dentro
     | de la campana de notificaciones.
     */
    (new Notificacion())->notifyUser(
        (int) $appointment['id_usuario'],
        [
            'id_cita' => $appointmentId,

            'tipo_notificacion' =>
                $notificationType,

            'titulo' =>
                'Tu cita cambió de estado',

            'mensaje' =>
                'La cita #'
                . $appointmentId
                . ' ahora se encuentra en estado: '
                . $statusLabel
                . '.',

            'medio' => $emailSent
                ? 'interna_correo'
                : 'interna',
        ]
    );
} catch (Throwable) {
    /*
     | El estado de la cita permanece actualizado
     | aunque falle la notificación interna.
     */
    Security::audit(
        'notificacion_no_generada',
        [
            'origen' =>
                'cita_actualizada',

            'id_cita' =>
                $appointmentId,
        ]
    );
}





        


        $this->success(
            'El estado de la cita se actualizó '
            . 'correctamente.',
            'citas'
        );
    }

    /**
     * El ciudadano cancela una cita propia.
     */
    public function cancel(): void
    {
        $appointmentId = max(
            0,
            (int) ($_POST['id_cita'] ?? 0)
        );

        $userId = (int) (
            auth_user()['id_usuario']
        );

        $model = new Cita();

        $appointment = $model->findOwned(
            $appointmentId,
            $userId
        );

        if ($appointment === null) {
            $this->failure(
                'No se encontró la cita solicitada.',
                'citas'
            );
        }

        if (
            !in_array(
                $appointment['estado'],
                [
                    'pendiente',
                    'confirmada',
                ],
                true
            )
        ) {
            $this->failure(
                'Esta cita ya no puede cancelarse.',
                'citas'
            );
        }

        $model->update(
            $appointmentId,
            ['estado' => 'cancelada']
        );

        Security::audit(
            'cita_cancelada',
            [
                'id_cita' => $appointmentId,
                'id_punto' =>
                    $appointment['id_punto'],
            ]
        );




/*
 | Obtengo la información completa para confirmar
 | al ciudadano la cancelación de su cita.
 */
$appointmentDetails =
    $model->findDetailed(
        $appointmentId
    );

$emailSent = false;

if ($appointmentDetails !== null) {
    try {
        (new Correo())->sendTransactional(
            (string) $appointmentDetails[
                'correo_ciudadano'
            ],
            (string) $appointmentDetails[
                'ciudadano'
            ],
            'Cancelación de la cita #'
                . $appointmentId,
            'Tu cita fue cancelada',
            'La cancelación de tu cita se registró '
                . 'correctamente.',
            [
                'Folio' =>
                    '#' . $appointmentId,

                'Centro' =>
                    $appointmentDetails[
                        'centro'
                    ],

                'Fecha' => date_mx(
                    $appointmentDetails[
                        'fecha_cita'
                    ]
                ),

                'Hora' => substr(
                    (string) $appointmentDetails[
                        'hora_cita'
                    ],
                    0,
                    5
                ),

                'Estado' => 'Cancelada',
            ],
            absolute_url('citas'),
            'Consultar mis citas'
        );

        $emailSent = true;
    } catch (Throwable) {
        /*
         | La cancelación permanece guardada aunque
         | no pueda enviarse el correo.
         */
        Security::audit(
            'correo_no_enviado',
            [
                'origen' =>
                    'cita_cancelada',

                'id_cita' =>
                    $appointmentId,
            ]
        );
    }
}

try {
    $notificationModel = new Notificacion();

    /*
     | Registro la cancelación en las
     | notificaciones del ciudadano.
     */
    $notificationModel->notifyUser(
        $userId,
        [
            'id_cita' => $appointmentId,

            'tipo_notificacion' =>
                'cancelacion_cita',

            'titulo' =>
                'Cita cancelada',

            'mensaje' =>
                'La cita #'
                . $appointmentId
                . ' fue cancelada correctamente.',

            'medio' => $emailSent
                ? 'interna_correo'
                : 'interna',
        ]
    );

    /*
     | Informo al administrador y al coordinador
     | del centro sobre la cancelación.
     */
    $notificationModel->notifyManagers(
        (int) $appointment['id_punto'],
        [
            'id_cita' => $appointmentId,

            'tipo_notificacion' =>
                'cancelacion_cita',

            'titulo' =>
                'Cita cancelada por el ciudadano',

            'mensaje' =>
                'El ciudadano canceló la cita #'
                . $appointmentId
                . '.',

            'medio' => 'interna',
        ]
    );
} catch (Throwable) {
    /*
     | La cancelación permanece guardada aunque
     | falle alguna notificación interna.
     */
    Security::audit(
        'notificacion_no_generada',
        [
            'origen' => 'cita_cancelada',
            'id_cita' => $appointmentId,
        ]
    );
}




        $this->success(
            'La cita se canceló correctamente.',
            'citas'
        );
    }

    /**
     * Comprueba una fecha con formato Y-m-d.
     */
    private function validDate(string $date): bool
    {
        if ($date === '') {
            return false;
        }

        $dateObject =
            DateTimeImmutable::createFromFormat(
                '!Y-m-d',
                $date
            );

        return $dateObject !== false
            && $dateObject->format('Y-m-d')
                === $date;
    }

    /**
     * Convierte una hora a minutos.
     */
    private function timeToMinutes(
        string $time
    ): int {
        [$hours, $minutes] = array_map(
            'intval',
            explode(':', $time)
        );

        return ($hours * 60) + $minutes;
    }
}