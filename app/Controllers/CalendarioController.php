<?php
declare(strict_types=1);

final class CalendarioController extends Controller
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
     * Muestra el calendario correspondiente
     * al usuario autenticado.
     */
    public function index(): void
    {
        /*
         | Recibo el mes con formato Y-m.
         | Si no es válido utilizo el mes actual.
         */
        $month = trim(
            (string) (
                $_GET['mes']
                ?? date('Y-m')
            )
        );

        $monthDate =
            DateTimeImmutable::createFromFormat(
                '!Y-m',
                $month
            );

        if (
            $monthDate === false
            || $monthDate->format('Y-m')
                !== $month
        ) {
            $monthDate =
                new DateTimeImmutable(
                    'first day of this month'
                );

            $month = $monthDate->format(
                'Y-m'
            );
        }

        $startDate = $monthDate->format(
            'Y-m-01'
        );

        $endDate = $monthDate
            ->modify('last day of this month')
            ->format('Y-m-d');

        $previousMonth = $monthDate
            ->modify('-1 month')
            ->format('Y-m');

        $nextMonth = $monthDate
            ->modify('+1 month')
            ->format('Y-m');

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

        $isAdmin = has_role('admin');

        $isCoordinator =
            has_role('coordinador');

        $isCitizen = has_role('ciudadano');

        /*
         | El ciudadano solamente recibe
         | sus propias citas.
         */
        $userId = $isCitizen
            ? (int) auth_user()['id_usuario']
            : null;

        /*
         | El coordinador queda limitado
         | al centro guardado en su sesión.
         */
        if ($isCoordinator) {
            $pointId = auth_center_id();

            if ($pointId === null) {
                deny_access();
            }
        } elseif (
            $isAdmin
            && $requestedPointId > 0
            && in_array(
                $requestedPointId,
                $validCenterIds,
                true
            )
        ) {
            /*
             | El administrador puede seleccionar
             | un centro específico.
             */
            $pointId = $requestedPointId;
        } else {
            /*
             | El administrador sin filtro obtiene
             | el resumen de todos los centros.
             |
             | El ciudadano ya está limitado
             | mediante su identificador.
             */
            $pointId = null;
        }

        $events = $model->calendarEvents(
            $startDate,
            $endDate,
            $status,
            $userId,
            $pointId
        );

        /*
         | El modo resumen solamente se utiliza
         | cuando el administrador consulta todos
         | los centros al mismo tiempo.
         */
        $summaryMode =
            $isAdmin
            && $pointId === null;

        $selectedCenterName = '';

        if ($isCoordinator) {
            $selectedCenterName = (string) (
                auth_user()[
                    'nombre_punto_asignado'
                ]
                ?? 'Centro asignado'
            );
        } elseif ($pointId !== null) {
            foreach ($centers as $center) {
                if (
                    (int) $center['id_punto']
                    === $pointId
                ) {
                    $selectedCenterName =
                        (string) $center['nombre'];

                    break;
                }
            }
        }

        $this->view(
            'calendar/index',
            [
                'pageTitle' =>
                    'Calendario de citas',

                'events' => $events,
                'centers' => $centers,

                'month' => $month,
                'monthDate' => $monthDate,
                'previousMonth' =>
                    $previousMonth,
                'nextMonth' => $nextMonth,

                'status' => $status,

                'selectedPointId' =>
                    $pointId ?? 0,

                'selectedCenterName' =>
                    $selectedCenterName,

                'statusLabels' =>
                    self::STATUS_LABELS,

                'motiveLabels' =>
                    self::MOTIVE_LABELS,

                'isAdmin' => $isAdmin,
                'isCoordinator' =>
                    $isCoordinator,
                'isCitizen' => $isCitizen,

                'summaryMode' =>
                    $summaryMode,

                'canManage' => has_role(
                    'admin',
                    'coordinador'
                ),

                'useCalendar' => true,
            ],
            'app'
        );
    }
}