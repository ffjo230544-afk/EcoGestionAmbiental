<?php
declare(strict_types=1);

final class HorarioAtencion extends Model
{
    protected string $table = 'horarios_atencion';

    protected string $primaryKey = 'id_horario';

    protected array $fillable = [
        'id_punto',
        'dia_semana',
        'hora_apertura',
        'hora_cierre',
        'cerrado',
        'observaciones',
    ];

    public function forCenter(int $pointId): array
    {
        return $this->rows(
            "SELECT
                id_horario,
                id_punto,
                dia_semana,
                hora_apertura,
                hora_cierre,
                cerrado,
                observaciones
             FROM horarios_atencion
             WHERE id_punto = ?
             ORDER BY FIELD(
                dia_semana,
                'lunes',
                'martes',
                'miercoles',
                'jueves',
                'viernes',
                'sabado',
                'domingo'
             )",
            [$pointId]
        );
    }

    public function allGrouped(
    bool $onlyActiveCenters,
    ?int $restrictedPointId = null
): array {
    $conditions = [];
    $parameters = [];

    if ($onlyActiveCenters) {
        $conditions[] = 'p.estado = ?';
        $parameters[] = 'activo';
    }

    if (
        $restrictedPointId !== null
        && $restrictedPointId > 0
    ) {
        $conditions[] = 'p.id_punto = ?';
        $parameters[] = $restrictedPointId;
    }

    $where = $conditions !== []
        ? ' WHERE ' . implode(' AND ', $conditions)
        : '';

    $rows = $this->rows(
        "SELECT
            h.*,
            p.nombre AS nombre_centro,
            p.estado AS estado_centro
         FROM horarios_atencion AS h
         INNER JOIN puntos_atencion AS p
            ON p.id_punto = h.id_punto"
        . $where
        . " ORDER BY
            p.nombre,
            FIELD(
                h.dia_semana,
                'lunes',
                'martes',
                'miercoles',
                'jueves',
                'viernes',
                'sabado',
                'domingo'
            )",
        $parameters
    );

    $grouped = [];

    foreach ($rows as $row) {
        $grouped[
            (int) $row['id_punto']
        ][] = $row;
    }

    return $grouped;
}

    /**
     * Guarda los siete días como una sola operación.
     */
    public function saveWeek(
        int $pointId,
        array $week
    ): void {
        $database = $this->db();

        $statement = $database->prepare(
            'INSERT INTO horarios_atencion (
                id_punto,
                dia_semana,
                hora_apertura,
                hora_cierre,
                cerrado,
                observaciones
             ) VALUES (?, ?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE
                hora_apertura = VALUES(hora_apertura),
                hora_cierre = VALUES(hora_cierre),
                cerrado = VALUES(cerrado),
                observaciones = VALUES(observaciones)'
        );

        $database->beginTransaction();

        try {
            foreach ($week as $day => $schedule) {
                $statement->execute([
                    $pointId,
                    $day,
                    $schedule['hora_apertura'],
                    $schedule['hora_cierre'],
                    $schedule['cerrado'],
                    $schedule['observaciones'],
                ]);
            }

            $database->commit();
        } catch (Throwable $exception) {
            if ($database->inTransaction()) {
                $database->rollBack();
            }

            throw $exception;
        }
    }
}