<?php
declare(strict_types=1);

final class Cita extends Model
{
    protected string $table = 'citas';

    protected string $primaryKey = 'id_cita';

    protected array $fillable = [
        'id_usuario',
        'id_punto',
        'fecha_cita',
        'hora_cita',
        'motivo',
        'observaciones',
        'estado',
    ];

    /**
     * Busca las citas aplicando las restricciones
     * correspondientes al usuario autenticado.
     */
    public function search(
        string $search,
        string $status,
        string $appointmentDate,
        ?int $userId,
        ?int $pointId
    ): array {
        $conditions = [];
        $parameters = [];

        /*
         | El ciudadano solamente puede consultar
         | las citas que le pertenecen.
         */
        if ($userId !== null) {
            $conditions[] = 'ci.id_usuario = ?';
            $parameters[] = $userId;
        }

        /*
         | El coordinador solamente puede consultar
         | las citas de su centro asignado.
         */
        if ($pointId !== null) {
            $conditions[] = 'ci.id_punto = ?';
            $parameters[] = $pointId;
        }

        if (
            in_array(
                $status,
                [
                    'pendiente',
                    'confirmada',
                    'atendida',
                    'cancelada',
                ],
                true
            )
        ) {
            $conditions[] = 'ci.estado = ?';
            $parameters[] = $status;
        }

        if ($appointmentDate !== '') {
            $conditions[] = 'ci.fecha_cita = ?';
            $parameters[] = $appointmentDate;
        }

        if ($search !== '') {
            $conditions[] = '(
                CAST(ci.id_cita AS CHAR) LIKE ?
                OR u.nombre_completo LIKE ?
                OR u.correo LIKE ?
                OR p.nombre LIKE ?
                OR ci.motivo LIKE ?
            )';

            $term = '%' . $search . '%';

            $parameters[] = $term;
            $parameters[] = $term;
            $parameters[] = $term;
            $parameters[] = $term;
            $parameters[] = $term;
        }

        $where = $conditions !== []
            ? ' WHERE ' . implode(
                ' AND ',
                $conditions
            )
            : '';

        return $this->rows(
            'SELECT
                ci.*,

                u.nombre_completo AS ciudadano,
                u.correo AS correo_ciudadano,
                u.telefono AS telefono_ciudadano,

                p.nombre AS centro,
                p.direccion AS direccion_centro,
                p.municipio AS municipio_centro

             FROM citas AS ci

             INNER JOIN usuarios AS u
                ON u.id_usuario = ci.id_usuario

             INNER JOIN puntos_atencion AS p
                ON p.id_punto = ci.id_punto'

            . $where

            . ' ORDER BY
                CASE
                    WHEN TIMESTAMP(
                        ci.fecha_cita,
                        ci.hora_cita
                    ) >= NOW()
                    THEN 0
                    ELSE 1
                END,
                ci.fecha_cita ASC,
                ci.hora_cita ASC,
                ci.id_cita DESC',
            $parameters
        );
    }

    /**
     * Obtiene los centros activos que tienen
     * por lo menos un día de atención abierto.
     */
    public function activeCenters(): array
    {
        return $this->rows(
            'SELECT
                p.id_punto,
                p.nombre,
                p.direccion,
                p.municipio

             FROM puntos_atencion AS p

             WHERE p.estado = ?

               AND EXISTS (
                    SELECT 1

                    FROM horarios_atencion AS h

                    WHERE h.id_punto = p.id_punto
                      AND h.cerrado = 0
                      AND h.hora_apertura IS NOT NULL
                      AND h.hora_cierre IS NOT NULL
               )

             ORDER BY p.nombre',
            ['activo']
        );
    }

    /**
     * Obtiene los horarios de todos los centros
     * activos que pueden recibir citas.
     */
    public function schedulesForActiveCenters(): array
    {
        return $this->rows(
            'SELECT
                h.id_punto,
                h.dia_semana,
                h.hora_apertura,
                h.hora_cierre,
                h.cerrado,
                h.observaciones

             FROM horarios_atencion AS h

             INNER JOIN puntos_atencion AS p
                ON p.id_punto = h.id_punto

             WHERE p.estado = ?

             ORDER BY
                h.id_punto,
                FIELD(
                    h.dia_semana,
                    "lunes",
                    "martes",
                    "miercoles",
                    "jueves",
                    "viernes",
                    "sabado",
                    "domingo"
                )',
            ['activo']
        );
    }

    /**
     * Obtiene los espacios que ya fueron registrados.
     *
     * Debido a que la base tiene una llave única
     * por centro, fecha y hora, también considero
     * los registros cancelados como ocupados.
     */
    public function occupiedSlots(): array
    {
        return $this->rows(
            'SELECT
                id_punto,
                fecha_cita,
                hora_cita

             FROM citas

             WHERE fecha_cita >= CURDATE()

             ORDER BY
                id_punto,
                fecha_cita,
                hora_cita'
        );
    }

    /**
     * Busca el horario de un día específico
     * dentro de un centro activo.
     */
    public function scheduleForDay(
        int $pointId,
        string $day
    ): ?array {
        return $this->row(
            'SELECT
                h.id_horario,
                h.id_punto,
                h.dia_semana,
                h.hora_apertura,
                h.hora_cierre,
                h.cerrado,
                h.observaciones

             FROM horarios_atencion AS h

             INNER JOIN puntos_atencion AS p
                ON p.id_punto = h.id_punto

             WHERE h.id_punto = ?
               AND h.dia_semana = ?
               AND p.estado = ?
               AND h.cerrado = 0
               AND h.hora_apertura IS NOT NULL
               AND h.hora_cierre IS NOT NULL

             LIMIT 1',
            [
                $pointId,
                $day,
                'activo',
            ]
        );
    }

    /**
     * Comprueba que el horario todavía
     * no esté registrado en la base.
     */
    public function slotExists(
        int $pointId,
        string $date,
        string $time
    ): bool {
        return (int) $this->scalar(
            'SELECT COUNT(*)

             FROM citas

             WHERE id_punto = ?
               AND fecha_cita = ?
               AND hora_cita = ?',
            [
                $pointId,
                $date,
                $time,
            ]
        ) > 0;
    }

    /**
     * Busca una cita comprobando que pertenezca
     * al ciudadano indicado.
     */
    public function findOwned(
        int $appointmentId,
        int $userId
    ): ?array {
        return $this->row(
            'SELECT *

             FROM citas

             WHERE id_cita = ?
               AND id_usuario = ?

             LIMIT 1',
            [
                $appointmentId,
                $userId,
            ]
        );
    }
    /**
 * Obtiene las citas que serán mostradas
 * dentro del calendario.
 *
 * El rango corresponde al primer y último
 * día del mes seleccionado.
 */
public function calendarEvents(
    string $startDate,
    string $endDate,
    string $status,
    ?int $userId,
    ?int $pointId
): array {
    $conditions = [
        'ci.fecha_cita BETWEEN ? AND ?',
    ];

    $parameters = [
        $startDate,
        $endDate,
    ];

    /*
     | Cuando recibo un usuario, solamente
     | obtengo sus citas personales.
     */
    if ($userId !== null) {
        $conditions[] = 'ci.id_usuario = ?';
        $parameters[] = $userId;
    }

    /*
     | Cuando recibo un centro, solamente
     | obtengo las citas dirigidas a ese centro.
     */
    if ($pointId !== null) {
        $conditions[] = 'ci.id_punto = ?';
        $parameters[] = $pointId;
    }

    if (
        in_array(
            $status,
            [
                'pendiente',
                'confirmada',
                'atendida',
                'cancelada',
            ],
            true
        )
    ) {
        $conditions[] = 'ci.estado = ?';
        $parameters[] = $status;
    }

    return $this->rows(
        'SELECT
            ci.id_cita,
            ci.id_usuario,
            ci.id_punto,
            ci.fecha_cita,
            ci.hora_cita,
            ci.motivo,
            ci.observaciones,
            ci.estado,

            u.nombre_completo AS ciudadano,
            u.correo AS correo_ciudadano,
            u.telefono AS telefono_ciudadano,

            p.nombre AS centro,
            p.direccion AS direccion_centro,
            p.municipio AS municipio_centro

         FROM citas AS ci

         INNER JOIN usuarios AS u
            ON u.id_usuario = ci.id_usuario

         INNER JOIN puntos_atencion AS p
            ON p.id_punto = ci.id_punto

         WHERE '
        . implode(' AND ', $conditions)

        . ' ORDER BY
                ci.fecha_cita,
                ci.hora_cita,
                p.nombre,
                ci.id_cita',
        $parameters
    );
}
}