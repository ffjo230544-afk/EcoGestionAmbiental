<?php
declare(strict_types=1);

final class Cotizacion extends Model
{
    protected string $table = 'cotizaciones';

    protected string $primaryKey = 'id_cotizacion';

    protected array $fillable = [
        'id_usuario',
        'id_categoria',
        'id_punto',
        'peso_kg',
        'cantidad_piezas',
        'precio_unitario',
        'monto_estimado',
        'observaciones',
        'estado',
    ];

    /**
     * Busca las cotizaciones aplicando los filtros
     * correspondientes al usuario autenticado.
     *
     * Cuando recibo un usuario, solamente devuelvo
     * las cotizaciones que le pertenecen.
     *
     * Cuando recibo un centro, solamente devuelvo
     * las cotizaciones dirigidas a ese centro.
     */
    public function search(
        string $search,
        string $status,
        ?int $userId,
        ?int $pointId
    ): array {
        $conditions = [];
        $parameters = [];

        /*
         | Este filtro se utiliza para el ciudadano.
         */
        if ($userId !== null) {
            $conditions[] = 'q.id_usuario = ?';
            $parameters[] = $userId;
        }

        /*
         | Este filtro se utiliza para el coordinador.
         */
        if ($pointId !== null) {
            $conditions[] = 'q.id_punto = ?';
            $parameters[] = $pointId;
        }

        if (
            in_array(
                $status,
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
            $parameters[] = $status;
        }

        if ($search !== '') {
            $conditions[] = '(
                CAST(q.id_cotizacion AS CHAR) LIKE ?
                OR u.nombre_completo LIKE ?
                OR u.correo LIKE ?
                OR c.nombre LIKE ?
                OR p.nombre LIKE ?
            )';

            $term = '%' . $search . '%';

            $parameters[] = $term;
            $parameters[] = $term;
            $parameters[] = $term;
            $parameters[] = $term;
            $parameters[] = $term;
        }

        $where = $conditions !== []
            ? ' WHERE '
                . implode(' AND ', $conditions)
            : '';

        return $this->rows(
            'SELECT
                q.*,

                u.nombre_completo AS ciudadano,
                u.correo AS correo_ciudadano,

                c.nombre AS categoria,
                c.color_hex,

                p.nombre AS centro,
                p.municipio AS municipio_centro

             FROM cotizaciones AS q

             INNER JOIN usuarios AS u
                ON u.id_usuario = q.id_usuario

             INNER JOIN categorias_residuos AS c
                ON c.id_categoria = q.id_categoria

             LEFT JOIN puntos_atencion AS p
                ON p.id_punto = q.id_punto'

            . $where

            . ' ORDER BY
                    q.fecha_cotizacion DESC,
                    q.id_cotizacion DESC',
            $parameters
        );
    }

    /**
     * Busca una cotización comprobando que
     * pertenezca al ciudadano indicado.
     */
    public function findOwned(
        int $quoteId,
        int $userId
    ): ?array {
        return $this->row(
            'SELECT *
             FROM cotizaciones
             WHERE id_cotizacion = ?
               AND id_usuario = ?
             LIMIT 1',
            [
                $quoteId,
                $userId,
            ]
        );
    }
}