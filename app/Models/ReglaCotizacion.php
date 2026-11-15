<?php
declare(strict_types=1);

final class ReglaCotizacion extends Model
{
    protected string $table = 'reglas_cotizacion';

    protected string $primaryKey = 'id_regla';

    protected array $fillable = [
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
     * Obtiene las tarifas aplicando los filtros indicados.
     */
    public function search(
        int $categoryId,
        string $status
    ): array {
        $conditions = [];
        $parameters = [];

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
            ? ' WHERE ' . implode(' AND ', $conditions)
            : '';

        return $this->rows(
            'SELECT
                r.*,
                c.nombre AS categoria,
                c.color_hex,
                c.estado AS estado_categoria,
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
                ON c.id_categoria = r.id_categoria'
            . $where
            . ' ORDER BY
                    c.nombre,
                    r.vigencia_inicio DESC,
                    r.id_regla DESC',
            $parameters
        );
    }

    /**
     * Obtiene las categorías que pueden cotizarse.
     */
    public function availableCategories(): array
    {
        return $this->rows(
            'SELECT
                id_categoria,
                nombre,
                color_hex
             FROM categorias_residuos
             WHERE estado = ?
               AND disponible_cotizacion = 1
             ORDER BY nombre',
            ['activo']
        );
    }

    /**
     * Comprueba si una categoría está disponible
     * para utilizarse en las cotizaciones.
     */
    public function categoryAvailable(
        int $categoryId
    ): bool {
        return (int) $this->scalar(
            'SELECT COUNT(*)
             FROM categorias_residuos
             WHERE id_categoria = ?
               AND estado = ?
               AND disponible_cotizacion = 1',
            [
                $categoryId,
                'activo',
            ]
        ) > 0;
    }

    /**
     * Comprueba si una tarifa activa se cruza
     * con el periodo de otra tarifa activa.
     */
    public function hasOverlappingRate(
        int $categoryId,
        string $startDate,
        ?string $endDate,
        int $exceptId = 0
    ): bool {
        $sql = "
            SELECT COUNT(*)
            FROM reglas_cotizacion AS r
            WHERE r.id_categoria = ?
              AND r.estado = 'activa'
              AND r.vigencia_inicio
                  <= COALESCE(?, '9999-12-31')
              AND COALESCE(
                    r.vigencia_fin,
                    '9999-12-31'
                  ) >= ?
        ";

        $parameters = [
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
 * Obtiene todas las tarifas que se encuentran
 * vigentes en la fecha actual.
 */
public function currentRates(): array
{
    return $this->rows(
        'SELECT
            r.id_regla,
            r.id_categoria,
            r.precio_por_kg,
            r.precio_por_pieza,
            r.minimo_kg,
            r.maximo_kg,
            r.vigencia_inicio,
            r.vigencia_fin,
            c.nombre AS categoria,
            c.color_hex
         FROM reglas_cotizacion AS r
         INNER JOIN categorias_residuos AS c
            ON c.id_categoria = r.id_categoria
         WHERE r.estado = ?
           AND c.estado = ?
           AND c.disponible_cotizacion = 1
           AND r.vigencia_inicio <= CURDATE()
           AND (
                r.vigencia_fin IS NULL
                OR r.vigencia_fin >= CURDATE()
           )
           AND r.id_regla = (
                SELECT r2.id_regla
                FROM reglas_cotizacion AS r2
                WHERE r2.id_categoria = r.id_categoria
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
         ORDER BY c.nombre',
        [
            'activa',
            'activo',
            'activa',
        ]
    );
}

/**
 * Obtiene la tarifa vigente de una categoría.
 * Este método será utilizado para calcular
 * nuevamente la cotización en el servidor.
 */
public function currentRate(
    int $categoryId
): ?array {
    return $this->row(
        'SELECT
            r.id_regla,
            r.id_categoria,
            r.precio_por_kg,
            r.precio_por_pieza,
            r.minimo_kg,
            r.maximo_kg,
            r.vigencia_inicio,
            r.vigencia_fin,
            c.nombre AS categoria,
            c.color_hex
         FROM reglas_cotizacion AS r
         INNER JOIN categorias_residuos AS c
            ON c.id_categoria = r.id_categoria
         WHERE r.id_categoria = ?
           AND r.estado = ?
           AND c.estado = ?
           AND c.disponible_cotizacion = 1
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
            $categoryId,
            'activa',
            'activo',
        ]
    );
}
}