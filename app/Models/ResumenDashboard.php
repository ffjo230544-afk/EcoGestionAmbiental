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
}