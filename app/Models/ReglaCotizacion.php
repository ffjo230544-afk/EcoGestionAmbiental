<?php
declare(strict_types=1);

final class ReglaCotizacion extends Model
{
    protected string $table = 'reglas_cotizacion';

    protected string $primaryKey = 'id_regla';

    protected array $fillable = [
        'id_punto',
        'id_categoria',
        'precio_por_kg',
        'precio_por_pieza',
        'minimo_kg',
        'maximo_kg',
        'vigencia_inicio',
        'vigencia_fin',
        'estado',
    ];

    /**
 * Aquí obtengo las tarifas utilizando los filtros
 * seleccionados por el usuario.
 *
 * Cuando recibo un centro, la consulta solamente
 * devuelve las tarifas que le pertenecen.
 */
public function search(
    int $pointId,
    int $categoryId,
    string $status
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
            $status,
            ['activa', 'inactiva'],
            true
        )
    ) {
        $conditions[] = 'r.estado = ?';
        $parameters[] = $status;
    }

    $where = $conditions !== []
        ? ' WHERE ' . implode(
            ' AND ',
            $conditions
        )
        : '';

    return $this->rows(
        'SELECT
            r.*,
            c.nombre AS categoria,
            c.color_hex,
            c.estado AS estado_categoria,
            p.nombre AS centro,
            p.municipio AS municipio_centro,
            p.estado AS estado_centro,

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

         INNER JOIN categorias_residuos AS c
            ON c.id_categoria = r.id_categoria

         INNER JOIN puntos_atencion AS p
            ON p.id_punto = r.id_punto'
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
 * Aquí obtengo las categorías disponibles para cotizar.
 *
 * Si recibo el identificador de un centro, solamente
 * regreso las categorías que ese centro compra.
 */
public function availableCategories(
    int $pointId = 0
): array {
    if ($pointId > 0) {
        return $this->rows(
            'SELECT
                c.id_categoria,
                c.nombre,
                c.color_hex

             FROM categorias_residuos AS c

             INNER JOIN punto_categoria AS pc
                ON pc.id_categoria = c.id_categoria

             INNER JOIN puntos_atencion AS p
                ON p.id_punto = pc.id_punto

             WHERE pc.id_punto = ?
               AND pc.acepta_compra = 1
               AND c.estado = ?
               AND c.disponible_cotizacion = 1
               AND p.estado = ?

             ORDER BY c.nombre',
            [
                $pointId,
                'activo',
                'activo',
            ]
        );
    }

    /*
     | Cuando el administrador no selecciona un centro,
     | obtengo todas las categorías compradas por algún
     | centro activo.
     */
    return $this->rows(
        'SELECT DISTINCT
            c.id_categoria,
            c.nombre,
            c.color_hex

         FROM categorias_residuos AS c

         INNER JOIN punto_categoria AS pc
            ON pc.id_categoria = c.id_categoria

         INNER JOIN puntos_atencion AS p
            ON p.id_punto = pc.id_punto

         WHERE pc.acepta_compra = 1
           AND c.estado = ?
           AND c.disponible_cotizacion = 1
           AND p.estado = ?

         ORDER BY c.nombre',
        [
            'activo',
            'activo',
        ]
    );
}



/**
 * Aquí obtengo todos los centros para el filtro
 * del administrador.
 *
 * Incluyo los inactivos porque puede ser necesario
 * consultar su historial de tarifas.
 */
public function centersForFilter(): array
{
    return $this->rows(
        'SELECT
            id_punto,
            nombre,
            municipio,
            estado

         FROM puntos_atencion

         ORDER BY nombre'
    );
}

/**
 * Aquí obtengo únicamente los centros activos
 * que compran por lo menos una categoría.
 */
public function activePurchasingCenters(): array
{
    return $this->rows(
        'SELECT DISTINCT
            p.id_punto,
            p.nombre,
            p.municipio

         FROM puntos_atencion AS p

         INNER JOIN punto_categoria AS pc
            ON pc.id_punto = p.id_punto

         INNER JOIN categorias_residuos AS c
            ON c.id_categoria = pc.id_categoria

         WHERE p.estado = ?
           AND c.estado = ?
           AND c.disponible_cotizacion = 1
           AND pc.acepta_compra = 1

         ORDER BY p.nombre',
        [
            'activo',
            'activo',
        ]
    );
}

/**
 * Para el formulario del administrador necesito saber
 * qué centros compran cada categoría.
 *
 * El resultado se utilizará para ocultar opciones
 * que no correspondan al centro seleccionado.
 */
public function categoriesWithCenters(): array
{
    return $this->rows(
        'SELECT
            c.id_categoria,
            c.nombre,
            c.color_hex,

            GROUP_CONCAT(
                DISTINCT pc.id_punto
                ORDER BY pc.id_punto
                SEPARATOR ","
            ) AS centros_compra

         FROM categorias_residuos AS c

         INNER JOIN punto_categoria AS pc
            ON pc.id_categoria = c.id_categoria

         INNER JOIN puntos_atencion AS p
            ON p.id_punto = pc.id_punto

         WHERE pc.acepta_compra = 1
           AND c.estado = ?
           AND c.disponible_cotizacion = 1
           AND p.estado = ?

         GROUP BY
            c.id_categoria,
            c.nombre,
            c.color_hex

         ORDER BY c.nombre',
        [
            'activo',
            'activo',
        ]
    );
}

/**
 * Aquí obtengo una tarifa acompañada de la información
 * de su centro y su categoría.
 */
public function findWithDetails(int $id): ?array
{
    return $this->row(
        'SELECT
            r.*,
            c.nombre AS categoria,
            p.nombre AS centro,
            p.municipio AS municipio_centro,
            p.estado AS estado_centro

         FROM reglas_cotizacion AS r

         INNER JOIN categorias_residuos AS c
            ON c.id_categoria = r.id_categoria

         INNER JOIN puntos_atencion AS p
            ON p.id_punto = r.id_punto

         WHERE r.id_regla = ?

         LIMIT 1',
        [$id]
    );
}

/**
 * Aquí compruebo que el centro esté activo,
 * que compre la categoría y que la categoría
 * esté disponible para cotización.
 */
public function categoryAvailableForCenter(
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
           AND c.id_categoria = ?
           AND p.estado = ?
           AND c.estado = ?
           AND c.disponible_cotizacion = 1
           AND pc.acepta_compra = 1',
        [
            $pointId,
            $categoryId,
            'activo',
            'activo',
        ]
    ) > 0;
}



/**
 * Aquí compruebo que no existan dos tarifas activas
 * para el mismo centro y categoría durante fechas
 * que se crucen.
 */
public function hasOverlappingRate(
    int $pointId,
    int $categoryId,
    string $startDate,
    ?string $endDate,
    int $exceptId = 0
): bool {
    $sql = "
        SELECT COUNT(*)

        FROM reglas_cotizacion AS r

        WHERE r.id_punto = ?
          AND r.id_categoria = ?
          AND r.estado = 'activa'

          AND r.vigencia_inicio
              <= COALESCE(?, '9999-12-31')

          AND COALESCE(
                r.vigencia_fin,
                '9999-12-31'
              ) >= ?
    ";

    $parameters = [
        $pointId,
        $categoryId,
        $endDate,
        $startDate,
    ];

    if ($exceptId > 0) {
        $sql .= ' AND r.id_regla <> ?';
        $parameters[] = $exceptId;
    }

    return (int) $this->scalar(
        $sql,
        $parameters
    ) > 0;
}

/**
 * Obtiene las tarifas que se encuentran vigentes.
 *
 * Aquí considero el centro y la categoría porque
 * cada centro puede manejar precios diferentes.
 */
public function currentRates(): array
{
    return $this->rows(
        'SELECT
            r.id_regla,
            r.id_punto,
            r.id_categoria,
            r.precio_por_kg,
            r.precio_por_pieza,
            r.minimo_kg,
            r.maximo_kg,
            r.vigencia_inicio,
            r.vigencia_fin,

            p.nombre AS centro,
            p.municipio,

            c.nombre AS categoria,
            c.color_hex

         FROM reglas_cotizacion AS r

         INNER JOIN puntos_atencion AS p
            ON p.id_punto = r.id_punto

         INNER JOIN categorias_residuos AS c
            ON c.id_categoria = r.id_categoria

         INNER JOIN punto_categoria AS pc
            ON pc.id_punto = r.id_punto
           AND pc.id_categoria = r.id_categoria

         WHERE r.estado = ?
           AND p.estado = ?
           AND c.estado = ?
           AND c.disponible_cotizacion = 1
           AND pc.acepta_compra = 1
           AND r.vigencia_inicio <= CURDATE()

           AND (
                r.vigencia_fin IS NULL
                OR r.vigencia_fin >= CURDATE()
           )

           /*
            | Si existieran varias tarifas vigentes,
            | tomo la más reciente de cada combinación
            | de centro y categoría.
            */
           AND r.id_regla = (
                SELECT r2.id_regla

                FROM reglas_cotizacion AS r2

                WHERE r2.id_punto = r.id_punto
                  AND r2.id_categoria = r.id_categoria
                  AND r2.estado = ?
                  AND r2.vigencia_inicio <= CURDATE()

                  AND (
                       r2.vigencia_fin IS NULL
                       OR r2.vigencia_fin >= CURDATE()
                  )

                ORDER BY
                    r2.vigencia_inicio DESC,
                    r2.id_regla DESC

                LIMIT 1
           )

         ORDER BY
            p.nombre,
            c.nombre',
        [
            'activa',
            'activo',
            'activo',
            'activa',
        ]
    );
}

/**
 * Obtiene la tarifa vigente de una categoría
 * dentro de un centro específico.
 *
 * Este método se usa para calcular nuevamente
 * la cotización en el servidor.
 */
public function currentRate(
    int $pointId,
    int $categoryId
): ?array {
    return $this->row(
        'SELECT
            r.id_regla,
            r.id_punto,
            r.id_categoria,
            r.precio_por_kg,
            r.precio_por_pieza,
            r.minimo_kg,
            r.maximo_kg,
            r.vigencia_inicio,
            r.vigencia_fin,

            p.nombre AS centro,
            p.municipio,

            c.nombre AS categoria,
            c.color_hex

         FROM reglas_cotizacion AS r

         INNER JOIN puntos_atencion AS p
            ON p.id_punto = r.id_punto

         INNER JOIN categorias_residuos AS c
            ON c.id_categoria = r.id_categoria

         INNER JOIN punto_categoria AS pc
            ON pc.id_punto = r.id_punto
           AND pc.id_categoria = r.id_categoria

         WHERE r.id_punto = ?
           AND r.id_categoria = ?
           AND r.estado = ?
           AND p.estado = ?
           AND c.estado = ?
           AND c.disponible_cotizacion = 1
           AND pc.acepta_compra = 1
           AND r.vigencia_inicio <= CURDATE()

           AND (
                r.vigencia_fin IS NULL
                OR r.vigencia_fin >= CURDATE()
           )

         ORDER BY
            r.vigencia_inicio DESC,
            r.id_regla DESC

         LIMIT 1',
        [
            $pointId,
            $categoryId,
            'activa',
            'activo',
            'activo',
        ]
    );
}






}