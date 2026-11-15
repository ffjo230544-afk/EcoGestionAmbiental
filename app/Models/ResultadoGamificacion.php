<?php
declare(strict_types=1);

final class ResultadoGamificacion extends Model
{
    protected string $table = 'resultados_gamificacion';

    protected string $primaryKey = 'id_resultado';

    protected array $fillable = [
        'id_usuario',
        'id_actividad',
        'puntaje_obtenido',
        'aciertos',
        'errores',
        'porcentaje_acierto',
    ];

    public function bestByActivity(int $userId): array
    {
        $records = $this->rows(
            'SELECT
                id_actividad,
                MAX(puntaje_obtenido) AS mejor_puntaje,
                COUNT(*) AS total_partidas
             FROM resultados_gamificacion
             WHERE id_usuario = ?
             GROUP BY id_actividad',
            [$userId]
        );

        $result = [];

        foreach ($records as $record) {
            $result[(int) $record['id_actividad']] = $record;
        }

        return $result;
    }

    public function summaryForUser(int $userId): array
    {
        $summary = $this->row(
            'SELECT
                COUNT(*) AS total_partidas,
                COALESCE(MAX(puntaje_obtenido), 0) AS mejor_puntaje,
                COALESCE(AVG(porcentaje_acierto), 0) AS promedio
             FROM resultados_gamificacion
             WHERE id_usuario = ?',
            [$userId]
        );

        return $summary ?? [
            'total_partidas' => 0,
            'mejor_puntaje' => 0,
            'promedio' => 0,
        ];
    }

    public function bestForGame(
        int $userId,
        int $activityId
    ): int {
        return (int) $this->scalar(
            'SELECT COALESCE(MAX(puntaje_obtenido), 0)
             FROM resultados_gamificacion
             WHERE id_usuario = ?
               AND id_actividad = ?',
            [$userId, $activityId]
        );
    }
}