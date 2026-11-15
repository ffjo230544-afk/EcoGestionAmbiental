<?php
declare(strict_types=1);

final class CategoriaResiduo extends Model
{
    protected string $table = 'categorias_residuos';

    protected string $primaryKey = 'id_categoria';

    protected array $fillable = [
        'nombre',
        'descripcion',
        'tipo_residuo',
        'icono',
        'color_hex',
        'disponible_cotizacion',
        'visible_base_conocimiento',
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
            $conditions[] = 'c.estado = ?';
            $parameters[] = 'activo';
        } elseif (
            in_array(
                $status,
                ['activo', 'inactivo'],
                true
            )
        ) {
            $conditions[] = 'c.estado = ?';
            $parameters[] = $status;
        }

        if (
            in_array(
                $type,
                [
                    'reciclable',
                    'reutilizable',
                    'peligroso',
                    'especial',
                    'organico',
                    'otro',
                ],
                true
            )
        ) {
            $conditions[] = 'c.tipo_residuo = ?';
            $parameters[] = $type;
        }

        if ($search !== '') {
            $conditions[] = '(
                c.nombre LIKE ?
                OR c.descripcion LIKE ?
            )';

            $term = '%' . $search . '%';

            $parameters[] = $term;
            $parameters[] = $term;
        }

        $where = $conditions !== []
            ? ' WHERE ' . implode(' AND ', $conditions)
            : '';

        return $this->rows(
            'SELECT
                c.*,
                (
                    SELECT COUNT(*)
                    FROM punto_categoria AS pc
                    WHERE pc.id_categoria = c.id_categoria
                ) AS total_centros
             FROM categorias_residuos AS c'
            . $where
            . ' ORDER BY c.nombre',
            $parameters
        );
    }

    public function nameExists(
        string $name,
        int $exceptId = 0
    ): bool {
        $sql = 'SELECT COUNT(*)
                FROM categorias_residuos
                WHERE nombre = ?';

        $parameters = [$name];

        if ($exceptId > 0) {
            $sql .= ' AND id_categoria <> ?';
            $parameters[] = $exceptId;
        }

        return (int) $this->scalar(
            $sql,
            $parameters
        ) > 0;
    }
}