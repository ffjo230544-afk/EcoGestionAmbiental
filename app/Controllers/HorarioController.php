<?php
declare(strict_types=1);

final class HorarioController extends Controller
{
    private const DAYS = [
        'lunes' => 'Lunes',
        'martes' => 'Martes',
        'miercoles' => 'Miércoles',
        'jueves' => 'Jueves',
        'viernes' => 'Viernes',
        'sabado' => 'Sábado',
        'domingo' => 'Domingo',
    ];

    public function index(): void
{
    $search = trim(
        (string) ($_GET['buscar'] ?? '')
    );

    $onlyActive = has_role('ciudadano');

    $restrictedPointId = has_role('coordinador')
        ? auth_center_id()
        : null;

    if (
        has_role('coordinador')
        && $restrictedPointId === null
    ) {
        deny_access();
    }

    $conditions = [];
    $parameters = [];

    if ($onlyActive) {
        $conditions[] = 'p.estado = ?';
        $parameters[] = 'activo';
    }

    /*
     | El coordinador solamente obtiene
     | el centro asignado.
     */
    if ($restrictedPointId !== null) {
        $conditions[] = 'p.id_punto = ?';
        $parameters[] = $restrictedPointId;
    }

    if ($search !== '') {
        $conditions[] = '(
            p.nombre LIKE ?
            OR p.municipio LIKE ?
            OR p.direccion LIKE ?
        )';

        $term = '%' . $search . '%';

        $parameters[] = $term;
        $parameters[] = $term;
        $parameters[] = $term;
    }

    $where = $conditions !== []
        ? ' WHERE ' . implode(' AND ', $conditions)
        : '';

    $centers = (new PuntoAtencion())->rows(
        'SELECT
            p.id_punto,
            p.nombre,
            p.direccion,
            p.municipio,
            p.estado
         FROM puntos_atencion AS p'
        . $where
        . ' ORDER BY p.nombre',
        $parameters
    );

    $grouped = (new HorarioAtencion())->allGrouped(
        $onlyActive,
        $restrictedPointId
    );

    $this->view(
        'schedules/index',
        [
            'pageTitle' => 'Horarios de atención',
            'centers' => $centers,
            'grouped' => $grouped,
            'days' => self::DAYS,
            'search' => $search,
            'canManage' => has_role(
                'admin',
                'coordinador'
            ),
        ],
        'app'
    );
}

    public function edit(): void
    {
        $pointId = max(
            0,
            (int) ($_GET['id'] ?? 0)
        );

        $center = (new PuntoAtencion())->find($pointId);

        if ($center === null) {
            $this->failure(
                'No se encontró el centro solicitado.',
                'horarios'
            );
        }

        require_center_management($pointId);

        $stored = (new HorarioAtencion())->forCenter(
            $pointId
        );

        $storedByDay = [];

        foreach ($stored as $schedule) {
            $storedByDay[$schedule['dia_semana']]
                = $schedule;
        }

        $week = [];

        foreach (self::DAYS as $day => $label) {
            $week[$day] = $storedByDay[$day]
                ?? $this->defaultSchedule($day);
        }

        $this->view(
            'schedules/form',
            [
                'pageTitle' => 'Configurar horario',
                'center' => $center,
                'week' => $week,
                'days' => self::DAYS,
            ],
            'app'
        );
    }

    public function update(): void
    {
        $pointId = max(
            0,
            (int) ($_POST['id_punto'] ?? 0)
        );

        $center = (new PuntoAtencion())->find($pointId);

        if ($center === null) {
            $this->failure(
                'No se encontró el centro solicitado.',
                'horarios'
            );
        }

        require_center_management($pointId);

        $submitted = $_POST['horarios'] ?? [];

        if (!is_array($submitted)) {
            $this->failure(
                'La información de horarios no es válida.',
                'horarios/editar',
                ['id' => $pointId]
            );
        }

        $week = [];

        foreach (self::DAYS as $day => $label) {
            $schedule = $submitted[$day] ?? [];

            if (!is_array($schedule)) {
                $this->failure(
                    'El horario de ' . $label
                    . ' no es válido.',
                    'horarios/editar',
                    ['id' => $pointId]
                );
            }

            $closed = (
                (string) (
                    $schedule['cerrado'] ?? '0'
                )
            ) === '1';

            $opening = trim(
                (string) (
                    $schedule['hora_apertura'] ?? ''
                )
            );

            $closing = trim(
                (string) (
                    $schedule['hora_cierre'] ?? ''
                )
            );

            $observations = trim(
                (string) (
                    $schedule['observaciones'] ?? ''
                )
            );

            if (
                $this->length($observations) > 150
            ) {
                $this->failure(
                    'Las observaciones de '
                    . $label
                    . ' no pueden superar 150 caracteres.',
                    'horarios/editar',
                    ['id' => $pointId]
                );
            }

            if (!$closed) {
                if (
                    !$this->validTime($opening)
                    || !$this->validTime($closing)
                ) {
                    $this->failure(
                        'Escribe un horario válido para '
                        . $label
                        . '.',
                        'horarios/editar',
                        ['id' => $pointId]
                    );
                }

                /*
                 | El formato HH:MM permite comparar
                 | las horas directamente.
                 */
                if ($opening >= $closing) {
                    $this->failure(
                        'La apertura de '
                        . $label
                        . ' debe ser anterior al cierre.',
                        'horarios/editar',
                        ['id' => $pointId]
                    );
                }
            }

            $week[$day] = [
                'hora_apertura' => $closed
                    ? null
                    : $opening . ':00',
                'hora_cierre' => $closed
                    ? null
                    : $closing . ':00',
                'cerrado' => $closed ? 1 : 0,
                'observaciones' =>
                    $observations !== ''
                        ? $observations
                        : null,
            ];
        }

        (new HorarioAtencion())->saveWeek(
            $pointId,
            $week
        );

        Security::audit(
            'horario_semanal_actualizado',
            ['id_punto' => $pointId]
        );

        $this->success(
            'El horario semanal se guardó correctamente.',
            'centros/detalle',
            ['id' => $pointId]
        );
    }

    private function defaultSchedule(
        string $day
    ): array {
        if ($day === 'domingo') {
            return [
                'hora_apertura' => null,
                'hora_cierre' => null,
                'cerrado' => 1,
                'observaciones' => null,
            ];
        }

        if ($day === 'sabado') {
            return [
                'hora_apertura' => '09:00:00',
                'hora_cierre' => '14:00:00',
                'cerrado' => 0,
                'observaciones' => null,
            ];
        }

        return [
            'hora_apertura' => '09:00:00',
            'hora_cierre' => '17:00:00',
            'cerrado' => 0,
            'observaciones' => null,
        ];
    }

    private function validTime(string $time): bool
    {
        return preg_match(
            '/^(?:[01][0-9]|2[0-3]):[0-5][0-9]$/',
            $time
        ) === 1;
    }

    private function length(string $value): int
    {
        return function_exists('mb_strlen')
            ? mb_strlen($value)
            : strlen($value);
    }
}