<?php
declare(strict_types=1);

final class PuntoAtencion extends Model
{
    protected string $table = 'puntos_atencion';

    protected string $primaryKey = 'id_punto';

    protected array $fillable = [
        'nombre',
        'tipo_punto',
        'direccion',
        'municipio',
        'estado_region',
        'codigo_postal',
        'telefono',
        'correo_contacto',
        'tipo_servicio',
        'latitud',
        'longitud',
        'descripcion',
        'estado',
    ];

    public function search(
        string $search,
        string $type,
        string $status,
        bool $onlyActive
    ): array {
        $conditions = [];
        $parameters = [];

        if ($onlyActive) {
            $conditions[] = 'p.estado = ?';
            $parameters[] = 'activo';
        } elseif (
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

        if ($search !== '') {
            $conditions[] = '(
                p.nombre LIKE ?
                OR p.direccion LIKE ?
                OR p.municipio LIKE ?
                OR p.codigo_postal LIKE ?
            )';

            $term = '%' . $search . '%';

            $parameters[] = $term;
            $parameters[] = $term;
            $parameters[] = $term;
            $parameters[] = $term;
        }

        $where = $conditions !== []
            ? ' WHERE ' . implode(' AND ', $conditions)
            : '';

        return $this->rows(
            'SELECT
                p.*,
                (
                    SELECT COUNT(*)
                    FROM punto_categoria AS pc
                    WHERE pc.id_punto = p.id_punto
                ) AS total_categorias
             FROM puntos_atencion AS p'
            . $where
            . ' ORDER BY p.nombre',
            $parameters
        );
    }

    public function categories(
    int $pointId,
    bool $onlyActive = false
): array {
    $statusCondition = $onlyActive
        ? ' AND c.estado = ?'
        : '';

    $parameters = [$pointId];

    if ($onlyActive) {
        $parameters[] = 'activo';
    }

    return $this->rows(
        'SELECT
            c.id_categoria,
            c.nombre,
            c.tipo_residuo,
            c.color_hex,
            c.estado,
            pc.acepta_compra,
            pc.acepta_recepcion,
            pc.observaciones
         FROM categorias_residuos AS c
         INNER JOIN punto_categoria AS pc
            ON pc.id_categoria = c.id_categoria
         WHERE pc.id_punto = ?'
        . $statusCondition
        . ' ORDER BY c.nombre',
        $parameters
    );
}

    public function schedules(int $pointId): array
    {
        return $this->rows(
            "SELECT
                id_horario,
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

/**
 * Obtiene los puntos que pueden colocarse en el mapa.
 */
public function forMap(
    string $search,
    string $type,
    string $service,
    string $status,
    int $categoryId,
    bool $onlyActive
): array {
    /*
     | Un punto solamente puede aparecer en el mapa
     | cuando tiene latitud y longitud.
     */
    $conditions = [
        'p.latitud IS NOT NULL',
        'p.longitud IS NOT NULL',
    ];

    $parameters = [];

    /*
     | El ciudadano solamente puede consultar
     | puntos de atención activos.
     */
    if ($onlyActive) {
        $conditions[] = 'p.estado = ?';
        $parameters[] = 'activo';
    } elseif (
        in_array(
            $status,
            ['activo', 'inactivo'],
            true
        )
    ) {
        $conditions[] = 'p.estado = ?';
        $parameters[] = $status;
    }

    if ($type !== '') {
        $conditions[] = 'p.tipo_punto = ?';
        $parameters[] = $type;
    }

    if ($service !== '') {
        $conditions[] = 'p.tipo_servicio = ?';
        $parameters[] = $service;
    }

    /*
     | Compruebo que el centro tenga relacionada
     | la categoría seleccionada.
     */
    if ($categoryId > 0) {
        $conditions[] = 'EXISTS (
            SELECT 1
            FROM punto_categoria AS filter_pc

            INNER JOIN categorias_residuos AS filter_c
                ON filter_c.id_categoria =
                   filter_pc.id_categoria

            WHERE filter_pc.id_punto =
                  p.id_punto

              AND filter_pc.id_categoria = ?
              AND filter_c.estado = "activo"
        )';

        $parameters[] = $categoryId;
    }

    if ($search !== '') {
        $conditions[] = '(
            p.nombre LIKE ?
            OR p.direccion LIKE ?
            OR p.municipio LIKE ?
            OR p.codigo_postal LIKE ?
        )';

        $term = '%' . $search . '%';

        $parameters[] = $term;
        $parameters[] = $term;
        $parameters[] = $term;
        $parameters[] = $term;
    }

    return $this->rows(
        'SELECT
            p.*,

            /*
             | Obtengo los nombres de los materiales
             | relacionados con cada centro.
             */
            (
                SELECT GROUP_CONCAT(
                    c.nombre
                    ORDER BY c.nombre
                    SEPARATOR ", "
                )

                FROM punto_categoria AS pc

                INNER JOIN categorias_residuos AS c
                    ON c.id_categoria =
                       pc.id_categoria

                WHERE pc.id_punto = p.id_punto
                  AND c.estado = "activo"
            ) AS materiales,

            (
                SELECT COUNT(*)

                FROM punto_categoria AS pc_count

                INNER JOIN categorias_residuos AS c_count
                    ON c_count.id_categoria =
                       pc_count.id_categoria

                WHERE pc_count.id_punto =
                      p.id_punto

                  AND c_count.estado = "activo"
            ) AS total_materiales

         FROM puntos_atencion AS p

         WHERE '
        . implode(' AND ', $conditions)
        . '

         ORDER BY
            p.estado DESC,
            p.nombre',
        $parameters
    );
}

/**
 * Obtiene las categorías activas
 * para el filtro del mapa.
 */
public function mapCategories(): array
{
    return $this->rows(
        'SELECT
            id_categoria,
            nombre

         FROM categorias_residuos

         WHERE estado = "activo"

         ORDER BY nombre'
    );
}

/**
 * Cuenta los puntos que todavía no pueden mostrarse
 * porque no tienen sus dos coordenadas.
 */
public function countWithoutCoordinates(
    bool $onlyActive
): int {
    $statusCondition = $onlyActive
        ? ' AND estado = "activo"'
        : '';

    return (int) $this->scalar(
        'SELECT COUNT(*)

         FROM puntos_atencion

         WHERE (
            latitud IS NULL
            OR longitud IS NULL
         )'
        . $statusCondition
    );
}



public function duplicateExists(
    string $name,
    string $address,
    int $exceptId = 0
): bool {
    $sql = 'SELECT COUNT(*)
            FROM puntos_atencion
            WHERE nombre = ?
              AND direccion = ?';

    $parameters = [
        $name,
        $address,
    ];

    if ($exceptId > 0) {
        $sql .= ' AND id_punto <> ?';
        $parameters[] = $exceptId;
    }

    return (int) $this->scalar(
        $sql,
        $parameters
    ) > 0;
}

/**
 * Obtiene los centros activos que compran
 * alguna categoría de residuo.
 */
public function quotationCenters(): array
{
    return $this->rows(
        'SELECT
            p.id_punto,
            p.nombre,
            p.municipio,
            GROUP_CONCAT(
                DISTINCT pc.id_categoria
                ORDER BY pc.id_categoria
                SEPARATOR ","
            ) AS categorias_compra
         FROM puntos_atencion AS p
         INNER JOIN punto_categoria AS pc
            ON pc.id_punto = p.id_punto
         INNER JOIN categorias_residuos AS c
            ON c.id_categoria = pc.id_categoria
         WHERE p.estado = ?
           AND c.estado = ?
           AND pc.acepta_compra = 1
         GROUP BY
            p.id_punto,
            p.nombre,
            p.municipio
         ORDER BY p.nombre',
        [
            'activo',
            'activo',
        ]
    );
}

/**
 * Comprueba en el servidor que el centro
 * realmente compre la categoría seleccionada.
 */
public function acceptsPurchase(
    int $pointId,
    int $categoryId
): bool {
    return (int) $this->scalar(
        'SELECT COUNT(*)
         FROM puntos_atencion AS p
         INNER JOIN punto_categoria AS pc
            ON pc.id_punto = p.id_punto
         INNER JOIN categorias_residuos AS c
            ON c.id_categoria = pc.id_categoria
         WHERE p.id_punto = ?
           AND pc.id_categoria = ?
           AND p.estado = ?
           AND c.estado = ?
           AND pc.acepta_compra = 1',
        [
            $pointId,
            $categoryId,
            'activo',
            'activo',
        ]
    ) > 0;
}
    
}



