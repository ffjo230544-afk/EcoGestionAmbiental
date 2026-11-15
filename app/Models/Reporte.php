<?php
declare(strict_types=1);

final class Reporte extends Model
{
    /*
     | El modelo utiliza distintas tablas mediante
     | consultas personalizadas. Defino una principal
     | para cumplir la estructura general de Model.
     */
    protected string $table = 'puntos_atencion';

    protected string $primaryKey = 'id_punto';

    protected array $fillable = [];

    /**
     * Obtengo los roles disponibles para filtrar
     * el reporte de usuarios.
     */
    public function roles(): array
    {
        return $this->rows(
            'SELECT
                id_rol,
                nombre_rol
             FROM roles
             WHERE estado = ?
             ORDER BY id_rol',
            ['activo']
        );
    }

    /**
     * Obtengo los municipios disponibles.
     *
     * Cuando existe un centro restringido, solamente
     * regreso el municipio de ese centro.
     */
    public function municipalities(
        ?int $restrictedCenterId = null
    ): array {
        $conditions = [
            'municipio IS NOT NULL',
            'municipio <> ""',
        ];

        $parameters = [];

        if (
            $restrictedCenterId !== null
            && $restrictedCenterId > 0
        ) {
            $conditions[] = 'id_punto = ?';
            $parameters[] = $restrictedCenterId;
        }

        return $this->rows(
            'SELECT DISTINCT municipio
             FROM puntos_atencion
             WHERE '
             . implode(' AND ', $conditions)
             . '
             ORDER BY municipio',
            $parameters
        );
    }

    /**
     * Genero los datos del reporte de usuarios.
     * Este reporte será exclusivo del administrador.
     */
    public function users(
        int $roleId,
        string $status
    ): array {
        $conditions = [];
        $parameters = [];

        if ($roleId > 0) {
            $conditions[] = 'u.id_rol = ?';
            $parameters[] = $roleId;
        }

        if (
            in_array(
                $status,
                [
                    'activo',
                    'inactivo',
                    'bloqueado',
                ],
                true
            )
        ) {
            $conditions[] = 'u.estado = ?';
            $parameters[] = $status;
        }

        $where = $conditions !== []
            ? ' WHERE '
                . implode(' AND ', $conditions)
            : '';

        return $this->rows(
            'SELECT
                u.id_usuario,
                u.nombre_completo,
                u.correo,
                u.telefono,
                u.estado,
                u.fecha_registro,
                r.nombre_rol,
                p.nombre AS centro_asignado

             FROM usuarios AS u

             INNER JOIN roles AS r
                ON r.id_rol = u.id_rol

             LEFT JOIN puntos_atencion AS p
                ON p.id_punto =
                   u.id_punto_asignado'
            . $where
            . '
             ORDER BY
                r.nombre_rol,
                u.nombre_completo',
            $parameters
        );
    }

    /**
     * Genero los datos del reporte de centros.
     *
     * El identificador restringido impide que
     * el coordinador consulte otro centro.
     */
    public function centers(
        string $municipality,
        string $status,
        string $type,
        ?int $restrictedCenterId = null
    ): array {
        $conditions = [];
        $parameters = [];

        if (
            $restrictedCenterId !== null
            && $restrictedCenterId > 0
        ) {
            $conditions[] = 'p.id_punto = ?';
            $parameters[] = $restrictedCenterId;
        }

        if ($municipality !== '') {
            $conditions[] = 'p.municipio = ?';
            $parameters[] = $municipality;
        }

        if (
            in_array(
                $status,
                ['activo', 'inactivo'],
                true
            )
        ) {
            $conditions[] = 'p.estado = ?';
            $parameters[] = $status;
        }

        if (
            in_array(
                $type,
                [
                    'empresa',
                    'centro_acopio',
                    'punto_servicio',
                ],
                true
            )
        ) {
            $conditions[] = 'p.tipo_punto = ?';
            $parameters[] = $type;
        }

        $where = $conditions !== []
            ? ' WHERE '
                . implode(' AND ', $conditions)
            : '';

        return $this->rows(
            'SELECT
                p.*,

                (
                    SELECT COUNT(*)
                    FROM punto_categoria AS pc
                    WHERE pc.id_punto =
                          p.id_punto
                ) AS total_materiales,

                (
                    SELECT GROUP_CONCAT(
                        u.nombre_completo
                        ORDER BY u.nombre_completo
                        SEPARATOR ", "
                    )

                    FROM usuarios AS u

                    INNER JOIN roles AS r
                        ON r.id_rol = u.id_rol

                    WHERE u.id_punto_asignado =
                          p.id_punto

                      AND LOWER(
                          r.nombre_rol
                      ) = "coordinador"

                      AND u.estado = "activo"
                ) AS coordinadores

             FROM puntos_atencion AS p'
            . $where
            . '
             ORDER BY
                p.municipio,
                p.nombre',
            $parameters
        );
    }

/**
 * Aquí obtengo los centros disponibles para
 * utilizarlos en los filtros de los reportes.
 *
 * Cuando se recibe un centro restringido, solamente
 * se devuelve ese centro. Esto se utiliza para
 * los coordinadores.
 */
public function centerOptions(
    ?int $restrictedCenterId
): array {
    $conditions = [];
    $parameters = [];

    if ($restrictedCenterId !== null) {
        $conditions[] = 'id_punto = ?';
        $parameters[] = $restrictedCenterId;
    }

    $where = $conditions !== []
        ? ' WHERE ' . implode(
            ' AND ',
            $conditions
        )
        : '';

    return $this->rows(
        'SELECT
            id_punto,
            nombre,
            municipio,
            estado

         FROM puntos_atencion'
        . $where
        . '

         ORDER BY nombre',
        $parameters
    );
}

/**
 * Aquí obtengo las categorías para utilizarlas
 * en los filtros de tarifas y cotizaciones.
 *
 * También incluyo las categorías inactivas porque
 * pueden aparecer en registros históricos.
 */
public function categoryOptions(): array
{
    return $this->rows(
        'SELECT
            id_categoria,
            nombre,
            estado

         FROM categorias_residuos

         ORDER BY nombre'
    );
}

/**
 * Aquí preparo el reporte de materiales relacionados
 * con cada centro de atención.
 */
public function materials(
    int $pointId,
    string $categoryStatus
): array {
    $conditions = [];
    $parameters = [];

    if ($pointId > 0) {
        $conditions[] = 'p.id_punto = ?';
        $parameters[] = $pointId;
    }

    if (
        in_array(
            $categoryStatus,
            ['activo', 'inactivo'],
            true
        )
    ) {
        $conditions[] = 'c.estado = ?';
        $parameters[] = $categoryStatus;
    }

    $where = $conditions !== []
        ? ' WHERE ' . implode(
            ' AND ',
            $conditions
        )
        : '';

    return $this->rows(
        'SELECT
            p.id_punto,
            p.nombre AS centro,
            p.municipio,
            p.estado AS estado_centro,

            c.id_categoria,
            c.nombre AS categoria,
            c.tipo_residuo,
            c.estado AS estado_categoria,

            pc.acepta_compra,
            pc.acepta_recepcion,
            pc.observaciones

         FROM punto_categoria AS pc

         INNER JOIN puntos_atencion AS p
            ON p.id_punto = pc.id_punto

         INNER JOIN categorias_residuos AS c
            ON c.id_categoria = pc.id_categoria'
        . $where
        . '

         ORDER BY
            p.nombre,
            c.nombre',
        $parameters
    );
}

/**
 * Aquí obtengo las tarifas registradas por centro,
 * categoría y estado.
 */
public function rates(
    int $pointId,
    int $categoryId,
    string $rateStatus
): array {
    $conditions = [];
    $parameters = [];

    if ($pointId > 0) {
        $conditions[] = 'r.id_punto = ?';
        $parameters[] = $pointId;
    }

    if ($categoryId > 0) {
        $conditions[] = 'r.id_categoria = ?';
        $parameters[] = $categoryId;
    }

    if (
        in_array(
            $rateStatus,
            ['activa', 'inactiva'],
            true
        )
    ) {
        $conditions[] = 'r.estado = ?';
        $parameters[] = $rateStatus;
    }

    $where = $conditions !== []
        ? ' WHERE ' . implode(
            ' AND ',
            $conditions
        )
        : '';

    return $this->rows(
        'SELECT
            r.id_regla,
            r.precio_por_kg,
            r.precio_por_pieza,
            r.minimo_kg,
            r.maximo_kg,
            r.vigencia_inicio,
            r.vigencia_fin,
            r.estado,

            p.id_punto,
            p.nombre AS centro,
            p.municipio,

            c.id_categoria,
            c.nombre AS categoria,

            CASE
                WHEN r.estado = "activa"
                 AND r.vigencia_inicio <= CURDATE()
                 AND (
                    r.vigencia_fin IS NULL
                    OR r.vigencia_fin >= CURDATE()
                 )
                THEN 1
                ELSE 0
            END AS vigente_hoy

         FROM reglas_cotizacion AS r

         INNER JOIN puntos_atencion AS p
            ON p.id_punto = r.id_punto

         INNER JOIN categorias_residuos AS c
            ON c.id_categoria = r.id_categoria'
        . $where
        . '

         ORDER BY
            p.nombre,
            c.nombre,
            r.vigencia_inicio DESC,
            r.id_regla DESC',
        $parameters
    );
}

/**
 * Aquí obtengo las cotizaciones que cumplen con
 * el centro, categoría, estado y periodo elegidos.
 */
public function quotes(
    string $dateFrom,
    string $dateTo,
    int $pointId,
    int $categoryId,
    string $quoteStatus
): array {
    $conditions = [];
    $parameters = [];

    if ($dateFrom !== '') {
        $conditions[] = 'DATE(q.fecha_cotizacion) >= ?';
        $parameters[] = $dateFrom;
    }

    if ($dateTo !== '') {
        $conditions[] = 'DATE(q.fecha_cotizacion) <= ?';
        $parameters[] = $dateTo;
    }

    if ($pointId > 0) {
        $conditions[] = 'q.id_punto = ?';
        $parameters[] = $pointId;
    }

    if ($categoryId > 0) {
        $conditions[] = 'q.id_categoria = ?';
        $parameters[] = $categoryId;
    }

    if (
        in_array(
            $quoteStatus,
            [
                'pendiente',
                'revisada',
                'aprobada',
                'rechazada',
                'cancelada',
            ],
            true
        )
    ) {
        $conditions[] = 'q.estado = ?';
        $parameters[] = $quoteStatus;
    }

    $where = $conditions !== []
        ? ' WHERE ' . implode(
            ' AND ',
            $conditions
        )
        : '';

    return $this->rows(
        'SELECT
            q.id_cotizacion,
            q.peso_kg,
            q.cantidad_piezas,
            q.precio_unitario,
            q.monto_estimado,
            q.estado,
            q.fecha_cotizacion,

            u.nombre_completo AS ciudadano,
            u.correo AS correo_ciudadano,

            c.nombre AS categoria,

            p.nombre AS centro,
            p.municipio

         FROM cotizaciones AS q

         INNER JOIN usuarios AS u
            ON u.id_usuario = q.id_usuario

         INNER JOIN categorias_residuos AS c
            ON c.id_categoria = q.id_categoria

         LEFT JOIN puntos_atencion AS p
            ON p.id_punto = q.id_punto'
        . $where
        . '

         ORDER BY
            q.fecha_cotizacion DESC,
            q.id_cotizacion DESC',
        $parameters
    );
}

/**
 * Aquí obtengo las citas que cumplen con
 * el centro, estado y periodo seleccionados.
 */
public function appointments(
    string $dateFrom,
    string $dateTo,
    int $pointId,
    string $appointmentStatus
): array {
    $conditions = [];
    $parameters = [];

    if ($dateFrom !== '') {
        $conditions[] = 'ci.fecha_cita >= ?';
        $parameters[] = $dateFrom;
    }

    if ($dateTo !== '') {
        $conditions[] = 'ci.fecha_cita <= ?';
        $parameters[] = $dateTo;
    }

    if ($pointId > 0) {
        $conditions[] = 'ci.id_punto = ?';
        $parameters[] = $pointId;
    }

    if (
        in_array(
            $appointmentStatus,
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
        $parameters[] = $appointmentStatus;
    }

    $where = $conditions !== []
        ? ' WHERE ' . implode(
            ' AND ',
            $conditions
        )
        : '';

    return $this->rows(
        'SELECT
            ci.id_cita,
            ci.fecha_cita,
            ci.hora_cita,
            ci.motivo,
            ci.estado,
            ci.observaciones,

            u.nombre_completo AS ciudadano,
            u.correo AS correo_ciudadano,
            u.telefono AS telefono_ciudadano,

            p.nombre AS centro,
            p.municipio

         FROM citas AS ci

         INNER JOIN usuarios AS u
            ON u.id_usuario = ci.id_usuario

         INNER JOIN puntos_atencion AS p
            ON p.id_punto = ci.id_punto'
        . $where
        . '

         ORDER BY
            ci.fecha_cita ASC,
            ci.hora_cita ASC,
            ci.id_cita ASC',
        $parameters
    );
}





}