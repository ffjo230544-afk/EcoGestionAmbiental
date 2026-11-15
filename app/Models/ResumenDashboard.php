<?php
declare(strict_types=1);

/**
 * Aquí concentro los indicadores utilizados
 * en los paneles de los diferentes roles.
 */
final class ResumenDashboard extends Model
{
    protected string $table = 'usuarios';

    protected string $primaryKey = 'id_usuario';

    protected array $fillable = [];

    /**
     * Obtengo las cifras generales del administrador.
     */
    public function administrator(): array
    {
        $summary = $this->row(
            'SELECT
                (
                    SELECT COUNT(*)
                    FROM usuarios
                    WHERE estado = "activo"
                ) AS usuarios_activos,

                (
                    SELECT COUNT(*)
                    FROM puntos_atencion
                ) AS centros_registrados,

                (
                    SELECT COUNT(*)
                    FROM cotizaciones
                    WHERE estado = "pendiente"
                ) AS cotizaciones_pendientes,

                (
                    SELECT COUNT(*)
                    FROM citas
                    WHERE fecha_cita >= CURDATE()
                      AND estado IN (
                          "pendiente",
                          "confirmada"
                      )
                ) AS citas_programadas'
        );

        return [
            'usuarios_activos' => (int) (
                $summary['usuarios_activos'] ?? 0
            ),

            'centros_registrados' => (int) (
                $summary['centros_registrados'] ?? 0
            ),

            'cotizaciones_pendientes' => (int) (
                $summary['cotizaciones_pendientes'] ?? 0
            ),

            'citas_programadas' => (int) (
                $summary['citas_programadas'] ?? 0
            ),
        ];
    }

    /**
     * Obtengo únicamente la información relacionada
     * con el centro asignado al coordinador.
     */
    public function coordinator(int $pointId): array
    {
        $summary = $this->row(
            'SELECT
                (
                    SELECT COUNT(*)
                    FROM puntos_atencion
                    WHERE id_punto = ?
                      AND estado = "activo"
                ) AS centros_activos,

                (
                    SELECT COUNT(*)
                    FROM cotizaciones
                    WHERE id_punto = ?
                      AND estado = "pendiente"
                ) AS cotizaciones_pendientes,

                (
                    SELECT COUNT(*)
                    FROM citas
                    WHERE id_punto = ?
                      AND fecha_cita = CURDATE()
                      AND estado IN (
                          "pendiente",
                          "confirmada"
                      )
                ) AS citas_del_dia,

                (
                    SELECT COUNT(*)
                    FROM encuestas
                    WHERE estado = "publicada"

                      AND (
                          fecha_inicio IS NULL
                          OR fecha_inicio <= CURDATE()
                      )

                      AND (
                          fecha_cierre IS NULL
                          OR fecha_cierre >= CURDATE()
                      )
                ) AS encuestas_publicadas',
            [
                $pointId,
                $pointId,
                $pointId,
            ]
        );

        return [
            'centros_activos' => (int) (
                $summary['centros_activos'] ?? 0
            ),

            'cotizaciones_pendientes' => (int) (
                $summary['cotizaciones_pendientes'] ?? 0
            ),

            'citas_del_dia' => (int) (
                $summary['citas_del_dia'] ?? 0
            ),

            'encuestas_publicadas' => (int) (
                $summary['encuestas_publicadas'] ?? 0
            ),
        ];
    }

    /**
     * Obtengo la información personal del ciudadano.
     */
    public function citizen(int $userId): array
    {
        $summary = $this->row(
            'SELECT
                (
                    SELECT COUNT(*)
                    FROM puntos_atencion
                    WHERE estado = "activo"
                ) AS centros_disponibles,

                (
                    SELECT COUNT(*)
                    FROM cotizaciones
                    WHERE id_usuario = ?
                ) AS cotizaciones_usuario,

                (
                    SELECT COUNT(*)
                    FROM citas
                    WHERE id_usuario = ?
                      AND fecha_cita >= CURDATE()
                      AND estado IN (
                          "pendiente",
                          "confirmada"
                      )
                ) AS citas_usuario,

                (
                    SELECT COALESCE(
                        SUM(puntaje_obtenido),
                        0
                    )
                    FROM resultados_gamificacion
                    WHERE id_usuario = ?
                ) AS puntos_ambientales',
            [
                $userId,
                $userId,
                $userId,
            ]
        );

        return [
            'centros_disponibles' => (int) (
                $summary['centros_disponibles'] ?? 0
            ),

            'cotizaciones_usuario' => (int) (
                $summary['cotizaciones_usuario'] ?? 0
            ),

            'citas_usuario' => (int) (
                $summary['citas_usuario'] ?? 0
            ),

            'puntos_ambientales' => (int) (
                $summary['puntos_ambientales'] ?? 0
            ),
        ];
    }




    /**
     * Obtengo los centros activos disponibles
     * para los filtros de los dashboards.
     */
    public function activeCenters(): array
    {
        return $this->rows(
            'SELECT
                id_punto,
                nombre,
                municipio
             FROM puntos_atencion
             WHERE estado = ?
             ORDER BY nombre',
            ['activo']
        );
    }

    /**
     * Obtengo los resultados filtrados
     * que consultará el administrador.
     */
    public function administratorPeriod(
        string $dateFrom,
        string $dateTo,
        int $pointId
    ): array {
        return [
            'cotizaciones_periodo' =>
                $this->quotationCount(
                    $dateFrom,
                    $dateTo,
                    $pointId
                ),

            'cotizaciones_pendientes_periodo' =>
                $this->quotationCount(
                    $dateFrom,
                    $dateTo,
                    $pointId,
                    0,
                    ['pendiente']
                ),

            'citas_periodo' =>
                $this->appointmentCount(
                    $dateFrom,
                    $dateTo,
                    $pointId
                ),

            'citas_activas_periodo' =>
                $this->appointmentCount(
                    $dateFrom,
                    $dateTo,
                    $pointId,
                    0,
                    [
                        'pendiente',
                        'confirmada',
                    ]
                ),
        ];
    }

    /**
     * Obtengo los resultados del periodo
     * para el centro del coordinador.
     */
    public function coordinatorPeriod(
        int $pointId,
        string $dateFrom,
        string $dateTo
    ): array {
        return [
            'cotizaciones_periodo' =>
                $this->quotationCount(
                    $dateFrom,
                    $dateTo,
                    $pointId
                ),

            'cotizaciones_pendientes_periodo' =>
                $this->quotationCount(
                    $dateFrom,
                    $dateTo,
                    $pointId,
                    0,
                    ['pendiente']
                ),

            'citas_periodo' =>
                $this->appointmentCount(
                    $dateFrom,
                    $dateTo,
                    $pointId
                ),

            'citas_activas_periodo' =>
                $this->appointmentCount(
                    $dateFrom,
                    $dateTo,
                    $pointId,
                    0,
                    [
                        'pendiente',
                        'confirmada',
                    ]
                ),
        ];
    }

    /**
     * Obtengo la actividad del ciudadano
     * durante el periodo seleccionado.
     */
    public function citizenPeriod(
        int $userId,
        string $dateFrom,
        string $dateTo,
        int $pointId
    ): array {
        return [
            'cotizaciones_periodo' =>
                $this->quotationCount(
                    $dateFrom,
                    $dateTo,
                    $pointId,
                    $userId
                ),

            'cotizaciones_pendientes_periodo' =>
                $this->quotationCount(
                    $dateFrom,
                    $dateTo,
                    $pointId,
                    $userId,
                    ['pendiente']
                ),

            'citas_periodo' =>
                $this->appointmentCount(
                    $dateFrom,
                    $dateTo,
                    $pointId,
                    $userId
                ),

            /*
             | Los juegos no pertenecen a un centro.
             | Por eso sus puntos solamente se filtran
             | por usuario y rango de fechas.
             */
            'puntos_periodo' =>
                $this->environmentalPoints(
                    $userId,
                    $dateFrom,
                    $dateTo
                ),
        ];
    }

    /**
     * Cuento cotizaciones aplicando únicamente
     * los filtros que hayan sido seleccionados.
     */
    private function quotationCount(
        string $dateFrom,
        string $dateTo,
        int $pointId = 0,
        int $userId = 0,
        array $statuses = []
    ): int {
        $conditions = ['1 = 1'];
        $parameters = [];

        if ($dateFrom !== '') {
            $conditions[] =
                'fecha_cotizacion >= ?';

            $parameters[] =
                $dateFrom . ' 00:00:00';
        }

        if ($dateTo !== '') {
            $conditions[] =
                'fecha_cotizacion <= ?';

            $parameters[] =
                $dateTo . ' 23:59:59';
        }

        if ($pointId > 0) {
            $conditions[] = 'id_punto = ?';
            $parameters[] = $pointId;
        }

        if ($userId > 0) {
            $conditions[] = 'id_usuario = ?';
            $parameters[] = $userId;
        }

        if ($statuses !== []) {
            $markers = implode(
                ', ',
                array_fill(
                    0,
                    count($statuses),
                    '?'
                )
            );

            $conditions[] =
                'estado IN (' . $markers . ')';

            foreach ($statuses as $status) {
                $parameters[] = $status;
            }
        }

        return (int) $this->scalar(
            'SELECT COUNT(*)
             FROM cotizaciones
             WHERE '
             . implode(
                 ' AND ',
                 $conditions
             ),
            $parameters
        );
    }

    /**
     * Cuento citas aplicando el periodo,
     * el centro, el usuario y el estado.
     */
    private function appointmentCount(
        string $dateFrom,
        string $dateTo,
        int $pointId = 0,
        int $userId = 0,
        array $statuses = []
    ): int {
        $conditions = ['1 = 1'];
        $parameters = [];

        if ($dateFrom !== '') {
            $conditions[] = 'fecha_cita >= ?';
            $parameters[] = $dateFrom;
        }

        if ($dateTo !== '') {
            $conditions[] = 'fecha_cita <= ?';
            $parameters[] = $dateTo;
        }

        if ($pointId > 0) {
            $conditions[] = 'id_punto = ?';
            $parameters[] = $pointId;
        }

        if ($userId > 0) {
            $conditions[] = 'id_usuario = ?';
            $parameters[] = $userId;
        }

        if ($statuses !== []) {
            $markers = implode(
                ', ',
                array_fill(
                    0,
                    count($statuses),
                    '?'
                )
            );

            $conditions[] =
                'estado IN (' . $markers . ')';

            foreach ($statuses as $status) {
                $parameters[] = $status;
            }
        }

        return (int) $this->scalar(
            'SELECT COUNT(*)
             FROM citas
             WHERE '
             . implode(
                 ' AND ',
                 $conditions
             ),
            $parameters
        );
    }

    /**
     * Sumo los puntos obtenidos por el ciudadano
     * dentro del periodo solicitado.
     */
    private function environmentalPoints(
        int $userId,
        string $dateFrom,
        string $dateTo
    ): int {
        $conditions = ['id_usuario = ?'];
        $parameters = [$userId];

        if ($dateFrom !== '') {
            $conditions[] =
                'fecha_resultado >= ?';

            $parameters[] =
                $dateFrom . ' 00:00:00';
        }

        if ($dateTo !== '') {
            $conditions[] =
                'fecha_resultado <= ?';

            $parameters[] =
                $dateTo . ' 23:59:59';
        }

        return (int) $this->scalar(
            'SELECT COALESCE(
                SUM(puntaje_obtenido),
                0
             )
             FROM resultados_gamificacion
             WHERE '
             . implode(
                 ' AND ',
                 $conditions
             ),
            $parameters
        );
    }






}